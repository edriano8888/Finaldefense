<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tracking_model extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->_ensure_tracking_tables();
    }

    // --- Create tracking tables ---
    private function _ensure_tracking_tables() {
        // Table for tracking links (unique per influencer-platform-campaign)
        if (!$this->db->table_exists('tracking_links')) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `tracking_links` (
                    `id` INT NOT NULL AUTO_INCREMENT,
                    `code` VARCHAR(50) NOT NULL UNIQUE,
                    `influencer_id` INT NOT NULL,
                    `campaign_id` INT NULL,
                    `platform` VARCHAR(50) NOT NULL,
                    `destination_url` TEXT NOT NULL,
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    KEY `idx_code` (`code`),
                    KEY `idx_influencer` (`influencer_id`),
                    KEY `idx_campaign` (`campaign_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
            ");
        }

        // Table for tracking views/clicks
        if (!$this->db->table_exists('tracking_logs')) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `tracking_logs` (
                    `id` BIGINT NOT NULL AUTO_INCREMENT,
                    `tracking_link_id` INT NOT NULL,
                    `ip_address` VARCHAR(45) NULL,
                    `user_agent` TEXT NULL,
                    `referrer` TEXT NULL,
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    KEY `idx_tracking_link` (`tracking_link_id`),
                    KEY `idx_created_at` (`created_at`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
            ");
        }
    }

    // --- Generate a unique tracking code ---
    private function _generate_code() {
        $code = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
        $this->db->where('code', $code);
        $count = $this->db->count_all_results('tracking_links');
        
        return $count > 0 ? $this->_generate_code() : $code;
    }

    // --- Create a tracking link ---
    public function create_tracking_link($influencer_id, $campaign_id, $platform, $destination_url) {
        $code = $this->_generate_code();
        
        $data = [
            'code' => $code,
            'influencer_id' => $influencer_id,
            'campaign_id' => $campaign_id,
            'platform' => $platform,
            'destination_url' => $destination_url
        ];

        $this->db->insert('tracking_links', $data);
        return [
            'id' => $this->db->insert_id(),
            'code' => $code,
            'url' => base_url('track/' . $code)
        ];
    }

    // --- Get tracking link by code ---
    public function get_tracking_link_by_code($code) {
        return $this->db->where('code', $code)->get('tracking_links')->row();
    }

    // --- Log a view/click ---
    public function log_view($tracking_link_id) {
        $data = [
            'tracking_link_id' => $tracking_link_id,
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent(),
            'referrer' => $this->input->server('HTTP_REFERER')
        ];

        return $this->db->insert('tracking_logs', $data);
    }

    // --- Get stats for an influencer ---
    public function get_influencer_stats($influencer_id) {
        $this->db->select('tl.platform, COUNT(tl.id) as total_views');
        $this->db->from('tracking_links tl');
        $this->db->join('tracking_logs tlog', 'tl.id = tlog.tracking_link_id', 'left');
        $this->db->where('tl.influencer_id', $influencer_id);
        $this->db->group_by('tl.platform');
        return $this->db->get()->result();
    }

    // --- Get stats for a campaign ---
    public function get_campaign_stats($campaign_id) {
        $this->db->select('tl.platform, tl.influencer_id, COUNT(tlog.id) as total_views, a.username, p.full_name');
        $this->db->from('tracking_links tl');
        $this->db->join('tracking_logs tlog', 'tl.id = tlog.tracking_link_id', 'left');
        $this->db->join('campaign_influencers ci', 'tl.influencer_id = ci.influencer_id AND tl.campaign_id = ci.campaign_id', 'left');
        $this->db->join('collaborations col', 'tl.influencer_id = col.id', 'left');
        $this->db->join('influencer_accounts a', 'col.account_id = a.id', 'left');
        $this->db->join('influencer_profiles p', 'col.profile_id = p.id', 'left');
        $this->db->where('tl.campaign_id', $campaign_id);
        $this->db->group_by('tl.platform, tl.influencer_id');
        return $this->db->get()->result();
    }

    // --- Get all tracking links for an influencer and campaign ---
    public function get_tracking_links($influencer_id = null, $campaign_id = null) {
        $this->db->select('tl.*, COUNT(tlog.id) as total_views');
        $this->db->from('tracking_links tl');
        $this->db->join('tracking_logs tlog', 'tl.id = tlog.tracking_link_id', 'left');
        
        if ($influencer_id) {
            $this->db->where('tl.influencer_id', $influencer_id);
        }
        
        if ($campaign_id) {
            $this->db->where('tl.campaign_id', $campaign_id);
        }
        
        $this->db->group_by('tl.id');
        $this->db->order_by('tl.created_at', 'DESC');
        return $this->db->get()->result();
    }
}
