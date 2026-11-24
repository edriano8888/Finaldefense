<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Email Model - Embedded Email System for Instroom
 * Handles email accounts, threads, messages, templates, and follow-ups
 *
 * @property CI_DB_query_builder $db
 * @property CI_Session $session
 */
class Email_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->_ensure_email_drips_table();
        $this->_ensure_email_drip_steps_table();
        $this->_ensure_email_columns();
    }

    /**
     * Ensure email drips table exists for lightweight campaign tracking.
     */
    private function _ensure_email_drips_table() {
        if ($this->db->table_exists('email_drips')) {
            return;
        }

        $this->db->query("
            CREATE TABLE IF NOT EXISTS `email_drips` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `brand_id` INT NOT NULL,
                `user_id` INT NOT NULL,
                `name` VARCHAR(255) NOT NULL,
                `drip_type` VARCHAR(50) NOT NULL DEFAULT 'acquisition',
                `status` VARCHAR(50) NOT NULL DEFAULT 'drafted',
                `is_active` TINYINT(1) NOT NULL DEFAULT 0,
                `start_date` DATE NULL,
                `duration_days` INT NOT NULL DEFAULT 0,
                `creators_count` INT NOT NULL DEFAULT 0,
                `opens_rate` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
                `responses_rate` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
                `bounces_rate` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
                `notes` TEXT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_brand_id` (`brand_id`),
                KEY `idx_user_id` (`user_id`),
                KEY `idx_drip_type` (`drip_type`),
                KEY `idx_status` (`status`),
                KEY `idx_is_active` (`is_active`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");
    }

    /**
     * Ensure email tracking columns exist on the collaborations table.
     */
    private function _ensure_email_columns() {
        if (!$this->db->field_exists('email_count', 'collaborations')) {
            $this->db->query("ALTER TABLE collaborations ADD COLUMN email_count INT DEFAULT 0");
        }
        if (!$this->db->field_exists('last_email_at', 'collaborations')) {
            $this->db->query("ALTER TABLE collaborations ADD COLUMN last_email_at TIMESTAMP NULL");
        }
        if (!$this->db->field_exists('email_thread_id', 'collaborations')) {
            $this->db->query("ALTER TABLE collaborations ADD COLUMN email_thread_id VARCHAR(255) DEFAULT NULL");
        }
    }

    // ============================================
    // EMAIL ACCOUNTS
    // ============================================

    /**
     * Get email account by ID
     */
    public function get_account($account_id) {
        return $this->db->where('id', $account_id)
                       ->get('email_accounts')
                       ->row();
    }

    // ============================================
    // EMAIL DRIPS
    // ============================================

    /**
     * Get email drips for a brand.
     */
    public function get_email_drips($brand_id, $filters = [], $limit = 100) {
        $this->db->select('d.*, u.name AS owner_name');
        $this->db->from('email_drips d');
        $this->db->join('users u', 'u.id = d.user_id', 'left');
        $this->db->where('d.brand_id', (int) $brand_id);

        if (!empty($filters['drip_type'])) {
            $this->db->where('d.drip_type', $filters['drip_type']);
        }

        if (!empty($filters['status'])) {
            $this->db->where('d.status', $filters['status']);
        }

        $this->db->order_by('d.start_date IS NULL', 'ASC', false);
        $this->db->order_by('d.start_date', 'DESC');
        $this->db->order_by('d.created_at', 'DESC');
        $this->db->limit((int) $limit);

        return $this->db->get()->result();
    }

    /**
     * Get one email drip by id and brand.
     */
    public function get_email_drip($drip_id, $brand_id) {
        return $this->db->where('id', (int) $drip_id)
            ->where('brand_id', (int) $brand_id)
            ->get('email_drips')
            ->row();
    }

    /**
     * Create a new email drip record.
     */
    public function create_email_drip($data) {
        $this->db->insert('email_drips', $data);
        return $this->db->insert_id();
    }

    /**
     * Ensure email drip creators relationship table exists.
     */
    private function _ensure_email_drip_creators_table() {
        if ($this->db->table_exists('email_drip_creators')) {
            return;
        }

        $this->db->query("
            CREATE TABLE IF NOT EXISTS `email_drip_creators` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `drip_id` INT NOT NULL,
                `influencer_id` INT NOT NULL,
                `status` VARCHAR(50) NOT NULL DEFAULT 'Active',
                `current_step` INT NOT NULL DEFAULT 1,
                `last_sent_at` DATETIME NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `unique_drip_influencer` (`drip_id`, `influencer_id`),
                KEY `idx_drip_id` (`drip_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");
    }

    /**
     * Get creators linked to a drip campaign.
     */
    public function get_drip_creators($drip_id) {
        $this->_ensure_email_drip_creators_table();
        
        $this->db->select('dc.id as link_id, dc.status, dc.current_step, dc.last_sent_at, dc.created_at as linked_at');
        $this->db->select('c.id as collaboration_id, p.full_name, p.email, a.username, a.platform, a.profile_image');
        $this->db->from('email_drip_creators dc');
        $this->db->join('collaborations c', 'c.id = dc.influencer_id', 'inner');
        $this->db->join('influencer_profiles p', 'p.id = c.profile_id', 'left');
        $this->db->join('influencer_accounts a', 'a.id = c.account_id', 'left');
        $this->db->where('dc.drip_id', (int)$drip_id);
        $this->db->order_by('dc.created_at', 'DESC');
        
        $creators = $this->db->get()->result();

        // Fallback: If no explicit links yet, show saved influencers for this brand/campaign
        if (empty($creators)) {
            $brand_id = $this->session->userdata('brand_id');
            $this->db->select('c.id as collaboration_id, c.pipeline_status as status, 1 as current_step, c.created_at as last_sent_at');
            $this->db->select('p.full_name, p.email, a.username, a.platform, a.profile_image');
            $this->db->from('collaborations c');
            $this->db->join('influencer_profiles p', 'p.id = c.profile_id', 'left');
            $this->db->join('influencer_accounts a', 'a.id = c.account_id', 'left');
            if ($brand_id) {
                $this->db->where('c.brand_id', $brand_id);
            }
            $this->db->order_by('c.created_at', 'DESC');
            $this->db->limit(20);
            $creators = $this->db->get()->result();
        }

        return $creators;
    }

    /**
     * Add a creator to a drip campaign.
     */
    public function add_creator_to_drip($drip_id, $influencer_id) {
        $this->_ensure_email_drip_creators_table();
        
        $exists = $this->db->where('drip_id', (int)$drip_id)
                           ->where('influencer_id', (int)$influencer_id)
                           ->count_all_results('email_drip_creators');
        if ($exists === 0) {
            $this->db->insert('email_drip_creators', [
                'drip_id' => (int)$drip_id,
                'influencer_id' => (int)$influencer_id,
                'status' => 'Active',
                'current_step' => 1
            ]);
            
            // Recount creators for email_drips table
            $count = $this->db->where('drip_id', (int)$drip_id)->count_all_results('email_drip_creators');
            $this->db->where('id', (int)$drip_id)->update('email_drips', ['creators_count' => $count]);
        }
    }

    /**
     * Ensure email drip steps table exists.
     */
    private function _ensure_email_drip_steps_table() {
        if ($this->db->table_exists('email_drip_steps')) {
            return;
        }

        $this->db->query("
            CREATE TABLE IF NOT EXISTS `email_drip_steps` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `drip_id` INT NOT NULL,
                `step_number` INT NOT NULL DEFAULT 1,
                `thread_type` VARCHAR(50) NOT NULL DEFAULT 'NEW THREAD',
                `wait_days` INT NOT NULL DEFAULT 4,
                `subject` VARCHAR(255) NOT NULL DEFAULT '',
                `body_html` TEXT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `idx_drip_id` (`drip_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");
    }

    /**
     * Get email drip sequence steps for a campaign.
     */
    public function get_email_drip_steps($drip_id) {
        $this->_ensure_email_drip_steps_table();
        $steps = $this->db->where('drip_id', (int) $drip_id)
            ->order_by('step_number', 'ASC')
            ->get('email_drip_steps')
            ->result();

        if (!empty($steps)) {
            return $steps;
        }

        return [
            (object) [
                'id' => 1,
                'step_number' => 1,
                'thread_type' => 'NEW THREAD',
                'wait_days' => 4,
                'subject' => 'love your work!',
                'body_html' => "Hi {{first_name}},\n\nLove your content on your insta - {{user_name}}, amazing post about {{custom_line}}\n\nWould you be open to working with us to get the word out there about our product? Let me know and I'll send something over for you to taste 😍\n\n--\n{{owner_name}}"
            ],
            (object) [
                'id' => 2,
                'step_number' => 2,
                'thread_type' => 'SAME THREAD',
                'wait_days' => 7,
                'subject' => 'love your work!',
                'body_html' => "Hey {{first_name}},\n\nJust following up on my previous message in case it got buried! Would love to collaborate with you.\n\nBest,\n{{owner_name}}"
            ],
            (object) [
                'id' => 3,
                'step_number' => 3,
                'thread_type' => 'NEW THREAD',
                'wait_days' => 1,
                'subject' => 'giveaway with [brand]',
                'body_html' => "Hi {{first_name}},\n\nWe are hosting an exclusive giveaway for our top creators and would love for you to join!\n\nCheers,\n{{owner_name}}"
            ],
            (object) [
                'id' => 4,
                'step_number' => 4,
                'thread_type' => 'SAME THREAD',
                'wait_days' => 3,
                'subject' => 'giveaway with [brand]',
                'body_html' => "Hey {{first_name}},\n\nLast call for the giveaway spot! Let me know if you're interested.\n\nBest,\n{{owner_name}}"
            ]
        ];
    }

    /**
     * Save sequence steps for a drip campaign.
     */
    public function save_email_drip_steps($drip_id, $steps) {
        $this->_ensure_email_drip_steps_table();
        $this->db->where('drip_id', (int) $drip_id)->delete('email_drip_steps');

        if (!empty($steps) && is_array($steps)) {
            foreach ($steps as $index => $step) {
                $this->db->insert('email_drip_steps', [
                    'drip_id' => (int) $drip_id,
                    'step_number' => $index + 1,
                    'thread_type' => !empty($step['thread_type']) ? $step['thread_type'] : 'NEW THREAD',
                    'wait_days' => isset($step['wait_days']) ? (int) $step['wait_days'] : 4,
                    'subject' => !empty($step['subject']) ? $step['subject'] : '',
                    'body_html' => !empty($step['body_html']) ? $step['body_html'] : ''
                ]);
            }
        }
        return true;
    }

    /**
     * Delete an email drip campaign and its associated steps and creators.
     */
    public function delete_email_drip($drip_id, $brand_id) {
        $drip_id = (int) $drip_id;
        $brand_id = (int) $brand_id;
        $drip = $this->get_email_drip($drip_id, $brand_id);
        if (!$drip) return false;

        $this->db->where('drip_id', $drip_id)->delete('email_drip_steps');
        $this->db->where('drip_id', $drip_id)->delete('email_drip_creators');
        $this->db->where('id', $drip_id)->where('brand_id', $brand_id)->delete('email_drips');
        return true;
    }

    /**
     * Update an email drip by id and brand.
     */
    public function update_email_drip($drip_id, $brand_id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');

        return $this->db->where('id', (int) $drip_id)
            ->where('brand_id', (int) $brand_id)
            ->update('email_drips', $data);
    }

    /**
     * Get email drip dashboard stats.
     */
    public function get_email_drip_stats($brand_id, $drip_type = null) {
        $stats = [
            'total' => 0,
            'active' => 0,
            'drafted' => 0,
            'completed' => 0,
            'creators' => 0,
        ];

        $this->db->from('email_drips');
        $this->db->where('brand_id', (int) $brand_id);
        if ($drip_type) {
            $this->db->where('drip_type', $drip_type);
        }
        $stats['total'] = (int) $this->db->count_all_results();

        $this->db->from('email_drips');
        $this->db->where('brand_id', (int) $brand_id);
        $this->db->where('is_active', 1);
        if ($drip_type) {
            $this->db->where('drip_type', $drip_type);
        }
        $stats['active'] = (int) $this->db->count_all_results();

        $this->db->from('email_drips');
        $this->db->where('brand_id', (int) $brand_id);
        $this->db->where('status', 'drafted');
        if ($drip_type) {
            $this->db->where('drip_type', $drip_type);
        }
        $stats['drafted'] = (int) $this->db->count_all_results();

        $this->db->from('email_drips');
        $this->db->where('brand_id', (int) $brand_id);
        $this->db->where('status', 'completed');
        if ($drip_type) {
            $this->db->where('drip_type', $drip_type);
        }
        $stats['completed'] = (int) $this->db->count_all_results();

        $this->db->select_sum('creators_count');
        $this->db->from('email_drips');
        $this->db->where('brand_id', (int) $brand_id);
        if ($drip_type) {
            $this->db->where('drip_type', $drip_type);
        }
        $row = $this->db->get()->row();
        $stats['creators'] = isset($row->creators_count) ? (int) $row->creators_count : 0;

        return $stats;
    }

    /**
     * Get all active accounts for a user (with brand fallback for team workspace)
     */
    public function get_user_accounts($user_id, $include_inactive = false) {
        $this->db->where('user_id', $user_id);
        if (!$include_inactive) {
            $this->db->where('is_active', TRUE);
        }
        $accounts = $this->db->order_by('is_primary', 'DESC')
                             ->order_by('created_at', 'ASC')
                             ->get('email_accounts')
                             ->result();

        // If user has no personal email accounts, fallback to company/brand active accounts
        if (empty($accounts)) {
            $brand_id = $this->session->userdata('brand_id');
            if ($brand_id) {
                $this->db->where('brand_id', $brand_id);
            }
            if (!$include_inactive) {
                $this->db->where('is_active', TRUE);
            }
            $accounts = $this->db->order_by('is_primary', 'DESC')
                                 ->order_by('id', 'DESC')
                                 ->get('email_accounts')
                                 ->result();
        }

        return $accounts;
    }

    /**
     * Get primary account for user (with brand fallback)
     */
    public function get_primary_account($user_id) {
        $acc = $this->db->where('user_id', $user_id)
                        ->where('is_primary', TRUE)
                        ->where('is_active', TRUE)
                        ->get('email_accounts')
                        ->row();
        if (!$acc) {
            $acc = $this->db->where('user_id', $user_id)
                            ->where('is_active', TRUE)
                            ->get('email_accounts')
                            ->row();
        }
        if (!$acc) {
            $brand_id = $this->session->userdata('brand_id');
            if ($brand_id) {
                $this->db->where('brand_id', $brand_id);
            }
            $acc = $this->db->where('is_active', TRUE)
                            ->order_by('is_primary', 'DESC')
                            ->order_by('id', 'DESC')
                            ->get('email_accounts')
                            ->row();
        }
        return $acc;
    }

    /**
     * Add new email account
     */
    public function add_account($data) {
        $this->db->insert('email_accounts', $data);
        return $this->db->insert_id();
    }

    /**
     * Update email account
     */
    public function update_account($account_id, $data) {
        return $this->db->where('id', $account_id)
                       ->update('email_accounts', $data);
    }

    /**
     * Set account as primary
     */
    public function set_primary_account($account_id, $user_id) {
        // Remove primary from all user's accounts
        $this->db->where('user_id', $user_id)
                 ->update('email_accounts', ['is_primary' => FALSE]);
        
        // Set new primary
        return $this->db->where('id', $account_id)
                       ->update('email_accounts', ['is_primary' => TRUE]);
    }

    /**
     * Delete account
     */
    public function delete_account($account_id) {
        return $this->db->where('id', $account_id)
                       ->delete('email_accounts');
    }

    // ============================================
    // EMAIL THREADS
    // ============================================

    /**
     * Get thread by ID
     */
    public function get_thread($thread_id) {
        $this->db->select('t.*, 
                         CASE 
                            WHEN c.pipeline_status IS NOT NULL AND c.pipeline_status != "" THEN c.pipeline_status
                            WHEN (SELECT COUNT(*) FROM emails WHERE thread_id = t.thread_id AND direction = "incoming") > 0 AND (t.pipeline_stage IS NULL OR t.pipeline_stage = "Contacted" OR t.pipeline_stage = "For Outreach") THEN "Replied"
                            ELSE COALESCE(t.pipeline_stage, "Contacted")
                         END as pipeline_stage');
        $this->db->from('email_threads t');
        $this->db->join('collaborations c', 'c.id = t.influencer_id', 'left');
        $this->db->where('t.thread_id', $thread_id);
        return $this->db->get()->row();
    }

    /**
     * Get or create thread
     */
    public function get_or_create_thread($thread_id, $data) {
        $existing = $this->get_thread($thread_id);
        if ($existing) {
            return $existing->id;
        }
        
        $this->db->insert('email_threads', $data);
        return $this->db->insert_id();
    }

    /**
     * Get threads for account with filtering
     */
    public function get_threads($filters = [], $limit = 50, $offset = 0) {
        $this->db->select('t.*, 
                         CASE 
                            WHEN c.pipeline_status IS NOT NULL AND c.pipeline_status != "" THEN c.pipeline_status
                            WHEN (SELECT COUNT(*) FROM emails WHERE thread_id = t.thread_id AND direction = "incoming") > 0 AND (t.pipeline_stage IS NULL OR t.pipeline_stage = "Contacted" OR t.pipeline_stage = "For Outreach") THEN "Replied"
                            ELSE COALESCE(t.pipeline_stage, "Contacted")
                         END as pipeline_stage,
                         (SELECT COUNT(*) FROM emails WHERE thread_id = t.thread_id) as message_count,
                         (SELECT body_plain FROM emails WHERE thread_id = t.thread_id ORDER BY created_at DESC LIMIT 1) as last_preview,
                         (SELECT direction FROM emails WHERE thread_id = t.thread_id ORDER BY created_at DESC LIMIT 1) as last_direction');
        $this->db->from('email_threads t');
        $this->db->join('collaborations c', 'c.id = t.influencer_id', 'left');
        
        // Apply filters
        if (!empty($filters['account_id'])) {
            $this->db->where('t.account_id', $filters['account_id']);
        }
        if (!empty($filters['influencer_id'])) {
            $this->db->where('t.influencer_id', $filters['influencer_id']);
        }
        if (!empty($filters['campaign_id'])) {
            $this->db->where('t.campaign_id', $filters['campaign_id']);
        }
        if (!empty($filters['pipeline_stage'])) {
            $this->db->where('t.pipeline_stage', $filters['pipeline_stage']);
        }
        if (!empty($filters['is_read']) && $filters['is_read'] === 'false') {
            $this->db->where('t.is_read', FALSE);
        }
        if (!empty($filters['is_starred']) && $filters['is_starred'] === 'true') {
            $this->db->where('t.is_starred', TRUE);
        }
        if (!empty($filters['search'])) {
            $this->db->group_start()
                     ->like('t.subject', $filters['search'])
                     ->or_like('t.participants', $filters['search'])
                     ->group_end();
        }
        
        // Hide archived unless explicitly requested
        if (empty($filters['include_archived'])) {
            $this->db->where('t.is_archived', FALSE);
        }
        
        $this->db->order_by('t.last_message_at', 'DESC');
        $this->db->limit($limit, $offset);
        
        return $this->db->get()->result();
    }

    /**
     * Count threads for pagination
     */
    public function count_threads($filters = []) {
        $this->db->from('email_threads t');
        
        // Apply same filters as get_threads
        if (!empty($filters['account_id'])) {
            $this->db->where('t.account_id', $filters['account_id']);
        }
        if (!empty($filters['influencer_id'])) {
            $this->db->where('t.influencer_id', $filters['influencer_id']);
        }
        if (!empty($filters['campaign_id'])) {
            $this->db->where('t.campaign_id', $filters['campaign_id']);
        }
        if (!empty($filters['pipeline_stage'])) {
            $this->db->where('t.pipeline_stage', $filters['pipeline_stage']);
        }
        if (!empty($filters['is_read']) && $filters['is_read'] === 'false') {
            $this->db->where('t.is_read', FALSE);
        }
        if (!empty($filters['is_starred']) && $filters['is_starred'] === 'true') {
            $this->db->where('t.is_starred', TRUE);
        }
        if (empty($filters['include_archived'])) {
            $this->db->where('t.is_archived', FALSE);
        }
        
        return $this->db->count_all_results();
    }

    /**
     * Update thread
     */
    public function update_thread($thread_id, $data) {
        return $this->db->where('thread_id', $thread_id)
                       ->update('email_threads', $data);
    }

    /**
     * Link thread to influencer and campaign
     */
    public function link_thread($thread_id, $influencer_id = null, $campaign_id = null, $pipeline_stage = null) {
        $data = [];
        if ($influencer_id !== null) $data['influencer_id'] = $influencer_id;
        if ($campaign_id !== null) $data['campaign_id'] = $campaign_id;
        if ($pipeline_stage !== null) $data['pipeline_stage'] = $pipeline_stage;
        
        if (!empty($data)) {
            return $this->update_thread($thread_id, $data);
        }
        return true;
    }

    /**
     * Unify/Merge an old thread into a target thread
     */
    public function unify_thread($old_thread_id, $target_thread_id) {
        if (empty($old_thread_id) || empty($target_thread_id) || $old_thread_id === $target_thread_id) {
            return;
        }

        $old_thread = $this->get_thread($old_thread_id);
        $target_thread = $this->get_thread($target_thread_id);

        // Reassign all emails under old_thread_id to target_thread_id
        $this->db->where('thread_id', $old_thread_id)->update('emails', [
            'thread_id' => $target_thread_id
        ]);

        if ($old_thread && $target_thread) {
            $updates = [];
            if (empty($target_thread->influencer_id) && !empty($old_thread->influencer_id)) {
                $updates['influencer_id'] = $old_thread->influencer_id;
            }
            if (empty($target_thread->campaign_id) && !empty($old_thread->campaign_id)) {
                $updates['campaign_id'] = $old_thread->campaign_id;
            }
            if (!empty($updates)) {
                $this->db->where('id', $target_thread->id)->update('email_threads', $updates);
            }
            // Remove the duplicate thread
            $this->db->where('id', $old_thread->id)->delete('email_threads');
        } elseif ($old_thread && !$target_thread) {
            // Rename old thread's thread_id to the target_thread_id
            $this->db->where('id', $old_thread->id)->update('email_threads', [
                'thread_id' => $target_thread_id
            ]);
        }
    }

    /**
     * Find an existing thread by participant email and normalized subject
     */
    public function find_thread_by_subject_and_participant($account_id, $participant_email, $clean_subject) {
        if (empty($participant_email) || empty($clean_subject)) {
            return null;
        }

        $clean_subject = trim(preg_replace('/^(\s*(re|fwd|fw):\s*)+/i', '', $clean_subject));
        if (empty($clean_subject)) {
            return null;
        }

        $candidates = $this->db->where('account_id', (int)$account_id)
                                ->where('is_archived', 0)
                                ->like('participants', $participant_email)
                                ->order_by('last_message_at', 'DESC')
                                ->limit(20)
                                ->get('email_threads')
                                ->result();

        foreach ($candidates as $cand) {
            $cand_clean = trim(preg_replace('/^(\s*(re|fwd|fw):\s*)+/i', '', $cand->subject));
            if (strcasecmp($cand_clean, $clean_subject) === 0) {
                return $cand;
            }
        }

        return null;
    }

    // ============================================
    // EMAILS (Individual Messages)
    // ============================================

    /**
     * Get email by ID
     */
    public function get_email($email_id) {
        return $this->db->where('id', $email_id)
                       ->get('emails')
                       ->row();
    }

    /**
     * Get email by message ID (external provider ID)
     */
    public function get_email_by_message_id($message_id) {
        return $this->db->where('message_id', $message_id)
                       ->get('emails')
                       ->row();
    }

    /**
     * Get emails in thread
     */
    public function get_thread_emails($thread_id, $limit = 100) {
        return $this->db->where('thread_id', $thread_id)
                       ->order_by('created_at', 'ASC')
                       ->limit($limit)
                       ->get('emails')
                       ->result();
    }

    /**
     * Save incoming email
     */
    public function save_incoming_email($data) {
        $this->db->insert('emails', $data);
        $email_id = $this->db->insert_id();
        
        // Update thread's last message time & set pipeline_stage to Replied
        $this->db->where('thread_id', $data['thread_id'])
                 ->update('email_threads', [
                     'last_message_at' => $data['received_at'],
                     'is_read' => FALSE,
                     'pipeline_stage' => 'Replied'
                 ]);
        
        // Auto transition pipeline status
        $thread = $this->db->get_where('email_threads', ['thread_id' => $data['thread_id']])->row();
        $influencer_id = $thread ? $thread->influencer_id : ($data['influencer_id'] ?? null);
        if ($influencer_id) {
            $this->load->model('Influencer_model');
            $this->Influencer_model->auto_transition_pipeline_on_email($influencer_id, 'incoming');
        }

        // Create notification for incoming email
        $display_sender = $data['from_name'] ?? '';
        if ($influencer_id) {
            $this->db->select('p.full_name');
            $this->db->from('collaborations c');
            $this->db->join('influencer_profiles p', 'c.profile_id = p.id', 'inner');
            $this->db->where('c.id', $influencer_id);
            $collab_profile = $this->db->get()->row();
            if ($collab_profile && !empty($collab_profile->full_name)) {
                $display_sender = $collab_profile->full_name;
            }
        }
        if (empty($display_sender)) {
            $display_sender = $data['from_address'] ?? '';
        }

        // Fetch brand_id from account
        $brand_id = null;
        if (!empty($data['account_id'])) {
            $account = $this->db->get_where('email_accounts', ['id' => $data['account_id']])->row();
            if ($account) {
                $brand_id = $account->brand_id;
            }
        }

        $this->load->model('Notification_model');
        $this->Notification_model->create_creator_reply_notification(
            $brand_id,
            $display_sender,
            $data['from_address'] ?? '',
            $data['subject'] ?? '',
            $data['thread_id']
        );
        
        return $email_id;
    }

    /**
     * Save outgoing email
     */
    public function save_outgoing_email($data) {
        $this->db->insert('emails', $data);
        $email_id = $this->db->insert_id();
        
        // Update thread's last message time
        $this->db->where('thread_id', $data['thread_id'])
                 ->update('email_threads', [
                     'last_message_at' => $data['sent_at']
                 ]);
        
        // Auto transition pipeline status
        if (!empty($data['influencer_id'])) {
            $this->load->model('Influencer_model');
            $this->Influencer_model->auto_transition_pipeline_on_email($data['influencer_id'], 'outgoing');
        }
        
        return $email_id;
    }

    /**
     * Mark email as read
     */
    public function mark_as_read($email_id) {
        return $this->db->where('id', $email_id)
                       ->update('emails', ['is_read' => TRUE]);
    }

    /**
     * Mark thread as read
     */
    public function mark_thread_as_read($thread_id) {
        // Mark all emails in thread as read
        $this->db->where('thread_id', $thread_id)
                 ->update('emails', ['is_read' => TRUE]);
        
        // Mark thread as read
        return $this->db->where('thread_id', $thread_id)
                       ->update('email_threads', ['is_read' => TRUE]);
    }

    /**
     * Get unread count
     */
    public function get_unread_count($account_id = null, $user_id = null) {
        $this->db->from('email_threads');
        $this->db->where('is_read', FALSE);
        $this->db->where('is_archived', FALSE);
        
        if ($account_id) {
            $this->db->where('account_id', (int) $account_id);
        } elseif ($user_id) {
            $user_id = (int) $user_id;
            $this->db->where("account_id IN (SELECT id FROM email_accounts WHERE user_id = {$user_id} AND is_active = 1)", NULL, FALSE);
        }
        
        return $this->db->count_all_results();
    }

    /**
     * Mark all inbox threads and emails as read for a user or account.
     */
    public function mark_all_threads_as_read($account_id = null, $user_id = null) {
        if ($account_id) {
            $account_id = (int) $account_id;
            $this->db->where('account_id', $account_id);
            $this->db->update('email_threads', ['is_read' => TRUE]);
            $this->db->query("UPDATE emails SET is_read = 1 WHERE thread_id IN (SELECT thread_id FROM email_threads WHERE account_id = {$account_id})");
        } elseif ($user_id) {
            $user_id = (int) $user_id;
            $this->db->where("account_id IN (SELECT id FROM email_accounts WHERE user_id = {$user_id})", NULL, FALSE);
            $this->db->update('email_threads', ['is_read' => TRUE]);
            $this->db->query("UPDATE emails SET is_read = 1 WHERE thread_id IN (SELECT thread_id FROM email_threads WHERE account_id IN (SELECT id FROM email_accounts WHERE user_id = {$user_id}))");
        } else {
            $this->db->update('email_threads', ['is_read' => TRUE]);
            $this->db->update('emails', ['is_read' => TRUE]);
        }
        return true;
    }

    // ============================================
    // EMAIL TEMPLATES
    // ============================================

    /**
     * Get template by ID
     */
    public function get_template($template_id) {
        return $this->db->where('id', $template_id)
                       ->get('email_templates')
                       ->row();
    }

    /**
     * Get templates with filtering
     */
    public function get_templates($filters = [], $limit = 50, $offset = 0) {
        $this->db->from('email_templates');
        
        if (!empty($filters['user_id'])) {
            $this->db->where('user_id', $filters['user_id']);
        }
        if (!empty($filters['brand_id'])) {
            $this->db->where('brand_id', $filters['brand_id']);
        }
        if (!empty($filters['category'])) {
            $this->db->where('category', $filters['category']);
        }
        if (!empty($filters['is_shared'])) {
            $this->db->where('is_shared', $filters['is_shared']);
        }
        if (!empty($filters['is_favorite'])) {
            $this->db->where('is_favorite', TRUE);
        }
        if (!empty($filters['search'])) {
            $this->db->group_start()
                     ->like('name', $filters['search'])
                     ->or_like('subject', $filters['search'])
                     ->or_like('body_plain', $filters['search'])
                     ->group_end();
        }
        
        // Include shared templates from other users
        if (!empty($filters['user_id']) && empty($filters['own_only'])) {
            $this->db->or_where('is_shared', TRUE);
        }
        
        $this->db->order_by('is_favorite', 'DESC')
                 ->order_by('usage_count', 'DESC')
                 ->order_by('name', 'ASC')
                 ->limit($limit, $offset);
        
        return $this->db->get()->result();
    }

    /**
     * Save template
     */
    public function save_template($data) {
        if (!empty($data['id'])) {
            $this->db->where('id', $data['id'])->update('email_templates', $data);
            return $data['id'];
        }
        $this->db->insert('email_templates', $data);
        return $this->db->insert_id();
    }

    /**
     * Delete template
     */
    public function delete_template($template_id) {
        return $this->db->where('id', $template_id)
                       ->delete('email_templates');
    }

    /**
     * Increment template usage
     */
    public function increment_template_usage($template_id) {
        return $this->db->where('id', $template_id)
                       ->set('usage_count', 'usage_count + 1', FALSE)
                       ->set('last_used_at', date('Y-m-d H:i:s'))
                       ->update('email_templates');
    }

    /**
     * Process template variables with support for casing variants:
     * - UPPERCASE / Capslock: {{FIRST_NAME}}, {{first_name_upper}}, {{first_name:upper}}
     * - lowercase / small letters: {{first_name_lower}}, {{first_name:lower}}
     * - Title Case: {{first_name}}, {{first_name:title}}
     */
    public function process_template($template, $variables = []) {
        $subject = $template->subject;
        $body_plain = $template->body_plain;
        $body_html = $template->body_html;
        
        // Build casing variants for each variable
        $expanded = [];
        foreach ($variables as $key => $value) {
            $val_str = (string)$value;
            $expanded[$key] = $val_str;
            $expanded[strtoupper($key)] = strtoupper($val_str);
            $expanded[$key . '_upper'] = strtoupper($val_str);
            $expanded[$key . '_caps'] = strtoupper($val_str);
            $expanded[$key . '_lower'] = strtolower($val_str);
            $expanded[$key . '_small'] = strtolower($val_str);
            $expanded[$key . '_title'] = ucwords(strtolower($val_str));
        }

        // 1. Process tag modifiers: {{var:upper}}, {{var:lower}}, {{var:caps}}, {{var:title}}
        $modifier_pattern = '/\{\{\s*([a-zA-Z0-9_-]+)\s*[:|]\s*(upper|caps|lower|small|title)\s*\}\}/i';
        $replace_callback = function($matches) use ($variables) {
            $key = strtolower($matches[1]);
            $mod = strtolower($matches[2]);
            $raw_val = '';
            foreach ($variables as $k => $v) {
                if (strtolower($k) === $key) {
                    $raw_val = (string)$v;
                    break;
                }
            }
            if ($raw_val === '') return $matches[0];
            if ($mod === 'upper' || $mod === 'caps') return strtoupper($raw_val);
            if ($mod === 'lower' || $mod === 'small') return strtolower($raw_val);
            if ($mod === 'title') return ucwords(strtolower($raw_val));
            return $raw_val;
        };

        $subject = preg_replace_callback($modifier_pattern, $replace_callback, $subject);
        $body_plain = preg_replace_callback($modifier_pattern, $replace_callback, $body_plain);
        $body_html = preg_replace_callback($modifier_pattern, $replace_callback, $body_html);

        // 2. Sort expanded placeholders by length descending to avoid partial match collisions
        uksort($expanded, function($a, $b) {
            return strlen($b) - strlen($a);
        });

        foreach ($expanded as $key => $value) {
            $placeholder = '{{' . $key . '}}';
            $subject = str_replace($placeholder, $value, $subject);
            $body_plain = str_replace($placeholder, $value, $body_plain);
            $body_html = str_replace($placeholder, $value, $body_html);
        }
        
        return [
            'subject' => $subject,
            'body_plain' => $body_plain,
            'body_html' => $body_html
        ];
    }

    // ============================================
    // FOLLOW-UP REMINDERS
    // ============================================

    /**
     * Create follow-up reminder
     */
    public function create_follow_up($data) {
        $this->db->insert('email_follow_ups', $data);
        return $this->db->insert_id();
    }

    /**
     * Get pending follow-ups
     */
    public function get_pending_follow_ups($user_id = null, $limit = 50) {
        $this->db->from('email_follow_ups');
        $this->db->where('status', 'pending');
        $this->db->where('reminder_at <=', date('Y-m-d H:i:s'));
        
        if ($user_id) {
            $this->db->where('user_id', $user_id);
        }
        
        $this->db->order_by('reminder_at', 'ASC');
        $this->db->limit($limit);
        
        return $this->db->get()->result();
    }

    /**
     * Get user's follow-ups
     */
    public function get_user_follow_ups($user_id, $status = null, $limit = 50) {
        $this->db->from('email_follow_ups');
        $this->db->where('user_id', $user_id);
        
        if ($status) {
            $this->db->where('status', $status);
        }
        
        $this->db->order_by('reminder_at', 'DESC');
        $this->db->limit($limit);
        
        return $this->db->get()->result();
    }

    /**
     * Update follow-up status
     */
    public function update_follow_up($follow_up_id, $data) {
        return $this->db->where('id', $follow_up_id)
                       ->update('email_follow_ups', $data);
    }

    /**
     * Complete follow-up
     */
    public function complete_follow_up($follow_up_id) {
        return $this->update_follow_up($follow_up_id, [
            'status' => 'completed',
            'completed_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Snooze follow-up
     */
    public function snooze_follow_up($follow_up_id, $snooze_until) {
        return $this->update_follow_up($follow_up_id, [
            'status' => 'snoozed',
            'snoozed_until' => $snooze_until
        ]);
    }

    // ============================================
    // SYNC LOG
    // ============================================

    /**
     * Log sync operation
     */
    public function log_sync($data) {
        $this->db->insert('email_sync_logs', $data);
        return $this->db->insert_id();
    }

    /**
     * Update sync log
     */
    public function update_sync_log($log_id, $data) {
        return $this->db->where('id', $log_id)
                       ->update('email_sync_logs', $data);
    }

    /**
     * Get last sync for account
     */
    public function get_last_sync($account_id, $sync_type = null) {
        $this->db->where('account_id', $account_id);
        if ($sync_type) {
            $this->db->where('sync_type', $sync_type);
        }
        return $this->db->order_by('id', 'DESC')
                       ->limit(1)
                       ->get('email_sync_logs')
                       ->row();
    }

    // ============================================
    // STATS & ANALYTICS
    // ============================================

    /**
     * Get email stats for user/brand
     */
    public function get_stats($user_id = null, $brand_id = null, $start_date = null, $end_date = null) {
        $stats = [
            'total_sent' => 0,
            'total_received' => 0,
            'total_threads' => 0,
            'response_rate' => 0,
            'avg_response_time' => 0,
            'follow_ups_pending' => 0
        ];
        
        // Set date range
        if (!$start_date) {
            $start_date = date('Y-m-d', strtotime('-30 days'));
        }
        if (!$end_date) {
            $end_date = date('Y-m-d');
        }
        
        // Get account IDs
        $account_ids = [];
        if ($user_id) {
            $accounts = $this->get_user_accounts($user_id, true);
            $account_ids = array_column($accounts, 'id');
        }
        
        if (empty($account_ids)) {
            return $stats;
        }
        
        // Sent count
        $this->db->where_in('account_id', $account_ids);
        $this->db->where('direction', 'outgoing');
        $this->db->where('is_draft', FALSE);
        $this->db->where('DATE(sent_at) >=', $start_date);
        $this->db->where('DATE(sent_at) <=', $end_date);
        $stats['total_sent'] = $this->db->count_all_results('emails');
        
        // Received count
        $this->db->where_in('account_id', $account_ids);
        $this->db->where('direction', 'incoming');
        $this->db->where('DATE(received_at) >=', $start_date);
        $this->db->where('DATE(received_at) <=', $end_date);
        $stats['total_received'] = $this->db->count_all_results('emails');
        
        // Thread count
        $this->db->where_in('account_id', $account_ids);
        $this->db->where('DATE(created_at) >=', $start_date);
        $this->db->where('DATE(created_at) <=', $end_date);
        $stats['total_threads'] = $this->db->count_all_results('email_threads');
        
        // Pending follow-ups
        $this->db->where_in('account_id', $account_ids);
        $this->db->where('status', 'pending');
        $stats['follow_ups_pending'] = $this->db->count_all_results('email_follow_ups');
        
        return $stats;
    }

    /**
     * Search emails
     */
    public function search($query, $account_ids = [], $limit = 50) {
        $this->db->select('e.*, t.subject as thread_subject, t.influencer_id, t.campaign_id');
        $this->db->from('emails e');
        $this->db->join('email_threads t', 'e.thread_id = t.thread_id', 'left');
        
        if (!empty($account_ids)) {
            $this->db->where_in('e.account_id', $account_ids);
        }
        
        // Full-text search
        $this->db->group_start()
                 ->like('e.subject', $query)
                 ->or_like('e.body_plain', $query)
                 ->or_like('e.from_address', $query)
                 ->or_like('e.from_name', $query)
                 ->or_like('t.subject', $query)
                 ->group_end();
        
        $this->db->order_by('e.created_at', 'DESC');
        $this->db->limit($limit);
        
        return $this->db->get()->result();
    }

    /**
     * Seed realistic demo inbox threads for testing.
     */
    public function seed_demo_threads($user_id, $brand_id = null) {
        // Retrieve active user info to avoid hardcoded/mock user info
        $user = $this->db->where('id', (int) $user_id)->get('users')->row();
        $email_address = $user ? $user->email : 'user@example.com';
        $display_name = $user ? $user->name : 'System Manager';

        // Retrieve brand name
        $brand_name = 'Brand';
        if ($brand_id) {
            $brand = $this->db->where('id', (int) $brand_id)->get('brands')->row();
            if ($brand) {
                $brand_name = $brand->name;
            }
        }

        // Ensure active primary email account exists
        $account = $this->get_primary_account($user_id);
        if (!$account) {
            // Check if user already has an active account connected
            $user_accounts = $this->get_user_accounts($user_id);
            if (!empty($user_accounts)) {
                $account = $user_accounts[0];
                $account_id = $account->id;
                $email_address = $account->email_address;
                $display_name = $account->display_name ?: $display_name;
            } else {
                $account_id = $this->add_account([
                    'user_id' => (int) $user_id,
                    'brand_id' => (int) $brand_id,
                    'provider' => 'gmail',
                    'email_address' => $email_address,
                    'display_name' => $display_name,
                    'is_primary' => 1,
                    'is_active' => 1,
                    'sync_enabled' => 1,
                    'last_sync_at' => date('Y-m-d H:i:s')
                ]);
            }
        } else {
            $account_id = $account->id;
            $email_address = $account->email_address;
            $display_name = $account->display_name ?: $display_name;
        }

        // Retrieve real influencers/collaborations in database to link the demo threads to
        $this->db->select('c.id, p.full_name, p.email, c.pipeline_status');
        $this->db->from('collaborations c');
        $this->db->join('influencer_profiles p', 'c.profile_id = p.id', 'inner');
        if ($brand_id) {
            $this->db->where('c.brand_id', (int) $brand_id);
        }
        $this->db->limit(4);
        $real_influencers = $this->db->get()->result();

        $demo_creators = [];
        foreach ($real_influencers as $inf) {
            $demo_creators[] = [
                'influencer_id' => $inf->id,
                'from_name' => $inf->full_name,
                'from_email' => $inf->email,
                'stage' => $inf->pipeline_status ?: 'Negotiating'
            ];
        }

        // Fallback demo creators padding if the DB has fewer than 4 real influencers
        $fallbacks = [
            ['from_name' => 'Sarah Jenkins', 'from_email' => 'sarah.jenkins@creator.co', 'stage' => 'Negotiating'],
            ['from_name' => 'Marcus Vance', 'from_email' => 'marcus.vance@vlog.com', 'stage' => 'Contacted'],
            ['from_name' => 'Elena Rostova', 'from_email' => 'elena@fashionstyle.io', 'stage' => 'Confirmed'],
            ['from_name' => 'David Kim', 'from_email' => 'david.kim@techreviews.com', 'stage' => 'Content Review']
        ];

        $i = count($demo_creators);
        while ($i < 4) {
            $fallback = $fallbacks[$i];
            $demo_creators[] = [
                'influencer_id' => null,
                'from_name' => $fallback['from_name'],
                'from_email' => $fallback['from_email'],
                'stage' => $fallback['stage']
            ];
            $i++;
        }

        // Build dynamic demo threads referencing the actual active account and brand
        $demo_data = [
            [
                'thread_id' => 'thread_demo_1_' . time(),
                'subject' => "Re: Partnership Opportunity with {$brand_name}",
                'from_name' => $demo_creators[0]['from_name'],
                'from_email' => $demo_creators[0]['from_email'],
                'influencer_id' => $demo_creators[0]['influencer_id'],
                'stage' => $demo_creators[0]['stage'],
                'body' => "Hi {$display_name}!\n\nThanks for reaching out! I love your products and would be super excited to collaborate with {$brand_name}. My media kit rate for an Instagram Reel + Story set is $450. Let me know if that aligns with your budget!\n\nBest,\n" . explode(' ', $demo_creators[0]['from_name'])[0],
                'days_ago' => 0
            ],
            [
                'thread_id' => 'thread_demo_2_' . time(),
                'subject' => 'Rate sheet & collaboration details',
                'from_name' => $demo_creators[1]['from_name'],
                'from_email' => $demo_creators[1]['from_email'],
                'influencer_id' => $demo_creators[1]['influencer_id'],
                'stage' => $demo_creators[1]['stage'],
                'body' => "Hey team!\n\nGot your outreach email from the drip sequence. I'd love to review the sample kit before committing to a full video. Could you send over a package to my shipping address?\n\nCheers,\n" . explode(' ', $demo_creators[1]['from_name'])[0],
                'days_ago' => 1
            ],
            [
                'thread_id' => 'thread_demo_3_' . time(),
                'subject' => "Re: Exclusive Giveaway with {$brand_name}",
                'from_name' => $demo_creators[2]['from_name'],
                'from_email' => $demo_creators[2]['from_email'],
                'influencer_id' => $demo_creators[2]['influencer_id'],
                'stage' => $demo_creators[2]['stage'],
                'body' => "Hi! I just filled out the agreement form for the upcoming holiday giveaway. So excited for this partnership with {$brand_name}! Let me know when the tracking number for the product package is ready.\n\nBest regards,\n" . $demo_creators[2]['from_name'],
                'days_ago' => 2
            ],
            [
                'thread_id' => 'thread_demo_4_' . time(),
                'subject' => 'Product Review & Draft Video Preview',
                'from_name' => $demo_creators[3]['from_name'],
                'from_email' => $demo_creators[3]['from_email'],
                'influencer_id' => $demo_creators[3]['influencer_id'],
                'stage' => $demo_creators[3]['stage'],
                'body' => "Hey {$display_name},\n\nI just finished filming the unboxing and review video for the new campaign line! Attached is the unlisted preview link. Let me know if you need any edits before I post live on Friday.\n\nThanks,\n" . explode(' ', $demo_creators[3]['from_name'])[0],
                'days_ago' => 3
            ]
        ];

        foreach ($demo_data as $item) {
            $created = date('Y-m-d H:i:s', strtotime("-{$item['days_ago']} days"));
            
            $this->db->insert('email_threads', [
                'thread_id' => $item['thread_id'],
                'account_id' => $account_id,
                'subject' => $item['subject'],
                'participants' => json_encode([$item['from_email']]),
                'pipeline_stage' => $item['stage'],
                'influencer_id' => $item['influencer_id'],
                'is_read' => $item['days_ago'] > 0 ? 1 : 0,
                'is_starred' => $item['days_ago'] === 0 ? 1 : 0,
                'is_archived' => 0,
                'last_message_at' => $created
            ]);

            $this->db->insert('emails', [
                'message_id' => 'msg_' . md5($item['thread_id'] . $item['days_ago']),
                'thread_id' => $item['thread_id'],
                'account_id' => $account_id,
                'from_address' => $item['from_email'],
                'from_name' => $item['from_name'],
                'to_addresses' => json_encode([$email_address]),
                'subject' => $item['subject'],
                'body_plain' => $item['body'],
                'body_html' => nl2br(htmlspecialchars($item['body'])),
                'direction' => 'incoming',
                'is_read' => $item['days_ago'] > 0 ? 1 : 0,
                'created_at' => $created,
                'influencer_id' => $item['influencer_id']
            ]);

            // Create notification for demo incoming email
            $display_sender = $item['from_name'];
            if (!empty($item['influencer_id'])) {
                $this->db->select('p.full_name');
                $this->db->from('collaborations c');
                $this->db->join('influencer_profiles p', 'c.profile_id = p.id', 'inner');
                $this->db->where('c.id', $item['influencer_id']);
                $collab_profile = $this->db->get()->row();
                if ($collab_profile && !empty($collab_profile->full_name)) {
                    $display_sender = $collab_profile->full_name;
                }
            }

            $this->load->model('Notification_model');
            $this->Notification_model->create_creator_reply_notification(
                $brand_id,
                $display_sender,
                $item['from_email'],
                $item['subject'],
                $item['thread_id']
            );
        }

        return true;
    }

    /**
     * Disconnect/Remove an email account for a user.
     * Also cleans up local threads and messages associated with that account.
     */
    public function disconnect_account($account_id, $user_id) {
        $account = $this->db->get_where('email_accounts', [
            'id' => (int)$account_id,
            'user_id' => (int)$user_id
        ])->row();

        if (!$account) {
            return false;
        }

        // Get threads associated with this account to clean up messages
        $threads = $this->db->get_where('email_threads', ['account_id' => $account->id])->result();
        foreach ($threads as $t) {
            $this->db->where('thread_id', $t->thread_id)->delete('emails');
            $this->db->where('id', $t->id)->delete('email_threads');
        }

        // Also delete any remaining emails tied directly to account_id
        $this->db->where('account_id', $account->id)->delete('emails');

        // Delete the account
        $this->db->where('id', $account->id)->delete('email_accounts');
        return true;
    }

    /**
     * Ensure deleted email threads tracking table exists.
     */
    public function _ensure_deleted_threads_table() {
        if (!$this->db->table_exists('deleted_email_threads')) {
            $this->load->dbforge();
            $this->dbforge->add_field([
                'thread_id' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'null' => FALSE
                ],
                'deleted_at' => [
                    'type' => 'DATETIME',
                    'null' => FALSE
                ]
            ]);
            $this->dbforge->add_key('thread_id', TRUE);
            $this->dbforge->create_table('deleted_email_threads', TRUE);
        }
    }

    /**
     * Delete an email thread and its messages locally from the system.
     */
    public function delete_thread($thread_id, $user_id = null) {
        $this->_ensure_deleted_threads_table();

        $thread = $this->db->get_where('email_threads', ['thread_id' => $thread_id])->row();
        if (!$thread) {
            return false;
        }

        // Record in deleted_email_threads so we never pull it again from Gmail
        $this->db->replace('deleted_email_threads', [
            'thread_id' => $thread_id,
            'deleted_at' => date('Y-m-d H:i:s')
        ]);

        // Delete all messages in the thread
        $this->db->where('thread_id', $thread_id)->delete('emails');

        // Delete the thread record
        $this->db->where('thread_id', $thread_id)->delete('email_threads');

        return true;
    }
}

