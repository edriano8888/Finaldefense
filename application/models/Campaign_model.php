<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Campaign_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->_ensure_campaigns_columns();
        $this->_ensure_influencer_tasks_table();
    }

    private function _ensure_campaigns_columns() {
        if (!$this->db->field_exists('budget', 'campaigns')) {
            $this->db->query("ALTER TABLE `campaigns` ADD COLUMN `budget` DECIMAL(15,2) DEFAULT 0.00 AFTER `description`");
        }
    }

    // --- Campaign Operations ---

    public function get_all_campaigns() {
        $this->_ensure_influencer_tasks_table();
        $brand_id = $this->session->userdata('brand_id');
        if ($brand_id) {
            $this->db->where('brand_id', $brand_id);
        }
        $campaigns = $this->db->order_by('created_at', 'DESC')->get('campaigns')->result();
        
        foreach ($campaigns as &$campaign) {
            $campaign->influencers = $this->get_campaign_influencers($campaign->id);
            $campaign->influencer_count = count($campaign->influencers);
            $campaign->assigned_influencer_ids = array_map(function($inf) {
                return (int)$inf->influencer_id;
            }, $campaign->influencers);

            $actual_spent = 0.0;
            foreach ($campaign->influencers as $inf_row) {
                $actual_spent += (float)($inf_row->product_cost ?? 0.0);
            }
            $campaign->actual_spent = $actual_spent;

            $camp_tasks = $this->db->select('t.*, u.name AS assignee_name, a.username AS influencer_username')
                                   ->from('influencer_tasks t')
                                   ->join('users u', 't.assigned_to = u.id', 'left')
                                   ->join('collaborations col', 't.influencer_id = col.id', 'left')
                                   ->join('influencer_accounts a', 'col.account_id = a.id', 'left')
                                   ->where('t.campaign_id', (int)$campaign->id)
                                   ->order_by('t.created_at', 'DESC')
                                   ->get()
                                   ->result();
            $campaign->tasks = $camp_tasks;
            $campaign->total_tasks = count($camp_tasks);
            $done_count = 0;
            foreach ($camp_tasks as $ct) {
                if (($ct->status ?? '') === 'Done') {
                    $done_count++;
                }
            }
            $campaign->done_tasks = $done_count;
            $campaign->pending_tasks = $campaign->total_tasks - $done_count;
            $campaign->task_progress_pct = $campaign->total_tasks > 0 ? round(($done_count / $campaign->total_tasks) * 100) : 0;
        }
        
        return $campaigns;
    }

    public function count_all_campaigns() {
        $brand_id = $this->session->userdata('brand_id');
        if ($brand_id) {
            $this->db->where('brand_id', $brand_id);
        }
        return $this->db->count_all_results('campaigns');
    }

    public function get_campaign_by_id($id) {
        $brand_id = $this->session->userdata('brand_id');
        if ($brand_id) {
            $this->db->where('brand_id', $brand_id);
        }
        return $this->db->where('id', $id)->get('campaigns')->row();
    }

    public function create_campaign($data) {
        $data['brand_id'] = $this->session->userdata('brand_id');
        $this->db->insert('campaigns', $data);
        return $this->db->insert_id();
    }

    public function update_campaign($id, $data) {
        return $this->db->where('id', $id)->update('campaigns', $data);
    }

    public function delete_campaign($id) {
        return $this->db->where('id', $id)->delete('campaigns');
    }

    // --- Campaign Influencer Operations ---

    public function assign_influencers($campaign_id, $influencer_ids) {
        // First remove existing assignments to avoid duplicates if re-assigning
        $this->db->where('campaign_id', $campaign_id)->delete('campaign_influencers');
        
        $data = [];
        foreach ($influencer_ids as $influencer_id) {
            $data[] = [
                'campaign_id' => $campaign_id,
                'influencer_id' => $influencer_id
            ];
        }
        
        if (!empty($data)) {
            return $this->db->insert_batch('campaign_influencers', $data);
        }
        return false;
    }

    public function add_influencer_to_campaign($campaign_id, $influencer_id) {
        $exists = $this->db->where('campaign_id', (int)$campaign_id)
                           ->where('influencer_id', (int)$influencer_id)
                           ->count_all_results('campaign_influencers');
        if ($exists === 0) {
            return $this->db->insert('campaign_influencers', [
                'campaign_id' => (int)$campaign_id,
                'influencer_id' => (int)$influencer_id,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
        return true;
    }

    public function get_campaign_influencers($campaign_id) {
        $this->db->select("ci.*, COALESCE(NULLIF(a.platform, ''), 'Instagram') AS platform, a.username, a.profile_image, p.full_name, p.email, col.product_cost, col.pipeline_status, col.campaign_type", false);
        $this->db->from('campaign_influencers ci');
        $this->db->join('collaborations col', 'ci.influencer_id = col.id', 'left');
        $this->db->join('influencer_accounts a', 'col.account_id = a.id', 'left');
        $this->db->join('influencer_profiles p', 'col.profile_id = p.id', 'left');
        $this->db->where('ci.campaign_id', (int)$campaign_id);
        return $this->db->get()->result();
    }

    // --- Task Operations ---

    public function get_tasks($campaign_id, $influencer_id) {
        $this->_ensure_influencer_tasks_table();
        return $this->db->select('t.*, u.name AS assignee_name')
                        ->from('influencer_tasks t')
                        ->join('users u', 't.assigned_to = u.id', 'left')
                        ->where([
                            't.campaign_id' => (int)$campaign_id,
                            't.influencer_id' => (int)$influencer_id
                        ])
                        ->order_by('t.created_at', 'ASC')
                        ->get()
                        ->result();
    }

    public function add_task($data) {
        $this->_ensure_influencer_tasks_table();
        return $this->db->insert('influencer_tasks', $data);
    }

    public function update_task_status($task_id, $status) {
        $this->_ensure_influencer_tasks_table();
        return $this->db->where('id', $task_id)->update('influencer_tasks', ['status' => $status, 'updated_at' => date('Y-m-d H:i:s')]);
    }

    public function delete_task($task_id) {
        $this->_ensure_influencer_tasks_table();
        return $this->db->where('id', $task_id)->delete('influencer_tasks');
    }

    public function get_user_tasks($user_id, $status = null, $limit = 200, $user_role = null) {
        $this->_ensure_influencer_tasks_table();

        $user_id = (int)$user_id;
        if ($user_role === null && isset($this->session)) {
            $user_role = (string)$this->session->userdata('role');
        }
        $user_name = isset($this->session) ? trim((string)$this->session->userdata('name')) : '';
        $is_manager = in_array(strtolower((string)$user_role), ['admin', 'manager'], true);

        $this->db->select("t.*, c.title AS campaign_title, a.username AS influencer_username, COALESCE(NULLIF(a.platform, ''), 'Instagram') AS influencer_platform, p.full_name AS influencer_full_name, a.profile_image AS influencer_profile_image, u.name AS assignee_name, u.email AS assignee_email", false);
        $this->db->from('influencer_tasks t');
        $this->db->join('campaigns c', 't.campaign_id = c.id', 'left');
        $this->db->join('collaborations col', 't.influencer_id = col.id', 'left');
        $this->db->join('influencer_accounts a', 'col.account_id = a.id', 'left');
        $this->db->join('influencer_profiles p', 'col.profile_id = p.id', 'left');
        $this->db->join('users u', 't.assigned_to = u.id', 'left');

        if (!$is_manager) {
            $this->db->group_start();
            $this->db->where('t.assigned_to', $user_id);
            if ($user_name !== '') {
                $this->db->or_where('LOWER(u.name)', strtolower($user_name));
            }
            $this->db->group_end();
        }

        if ($status !== null && $status !== '') {
            $this->db->where('t.status', $status);
        }
        $this->db->order_by('t.due_date IS NULL', 'ASC', false);
        $this->db->order_by('t.due_date', 'ASC');
        $this->db->order_by('t.created_at', 'DESC');
        $this->db->limit((int)$limit);
        return $this->db->get()->result();
    }

    private function _ensure_influencer_tasks_table() {
        if ($this->db->table_exists('influencer_tasks')) {
            if (!$this->db->field_exists('assigned_to', 'influencer_tasks')) {
                $this->db->query("ALTER TABLE `influencer_tasks` ADD COLUMN `assigned_to` INT NULL AFTER `status`");
            }
            if (!$this->db->field_exists('due_date', 'influencer_tasks')) {
                $this->db->query("ALTER TABLE `influencer_tasks` ADD COLUMN `due_date` DATE NULL AFTER `assigned_to`");
            }
            if (!$this->db->field_exists('created_by', 'influencer_tasks')) {
                $this->db->query("ALTER TABLE `influencer_tasks` ADD COLUMN `created_by` INT NULL AFTER `due_date`");
            }
            if (!$this->db->field_exists('created_at', 'influencer_tasks')) {
                $this->db->query("ALTER TABLE `influencer_tasks` ADD COLUMN `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
            }
            if (!$this->db->field_exists('updated_at', 'influencer_tasks')) {
                $this->db->query("ALTER TABLE `influencer_tasks` ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT NULL AFTER `created_at`");
            }
            return;
        }

        $this->db->query("
            CREATE TABLE IF NOT EXISTS `influencer_tasks` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `campaign_id` INT NOT NULL,
                `influencer_id` INT NOT NULL,
                `task_name` VARCHAR(255) NOT NULL,
                `status` VARCHAR(50) NOT NULL DEFAULT 'Pending',
                `assigned_to` INT NULL,
                `due_date` DATE NULL,
                `created_by` INT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_campaign` (`campaign_id`),
                KEY `idx_influencer` (`influencer_id`),
                KEY `idx_assigned_to` (`assigned_to`),
                KEY `idx_status` (`status`),
                KEY `idx_due_date` (`due_date`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");
    }
}
