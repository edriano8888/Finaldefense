<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_DB_query_builder|CI_DB_mysqli_driver $db
 * @property CI_Loader $load
 */
class Influencer_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // Helper to join tables
    private function _get_base_query() {
        $brand_id = $this->session->userdata('brand_id');
        
        $this->db->select('c.id as id, c.pipeline_status, c.campaign_type, c.product_name, c.order_number, c.product_cost, c.discount_code, c.affiliate_link, c.tracking_link, c.assessment, c.date_reviewed, c.created_at as created_at, c.profile_id, c.account_id, c.created_by, c.deliverables, c.fee, c.deadline'); // Collaboration fields
        $this->db->select("p.full_name, p.first_name, p.last_name, p.email, p.contact_number, p.contact_info, p.location, p.gender, COALESCE(NULLIF(p.niche, ''), 'General') as niche, p.shipping_address, p.notes"); // Profile fields
        $this->db->select('a.platform, a.username, a.profile_image, a.status'); // Core Account fields
        $this->db->select('s.followers, s.eng_rate, s.avg_video_views, s.gmv, s.est_post_rate'); // Account Stats fields
        $this->db->select('pd.bio, pd.external_url, pd.account_type, pd.verified_status'); // Account Platform Details
        $this->db->select('u.name as sourced_by_name'); // Employee who sourced this influencer
        $this->db->select('b.name as brand_name'); // Real brand name
        
        $this->db->from('collaborations c');
        $this->db->join('influencer_profiles p', 'c.profile_id = p.id', 'left');
        $this->db->join('influencer_accounts a', 'c.account_id = a.id', 'left');
        $this->db->join('influencer_account_stats s', 'a.id = s.account_id', 'left');
        $this->db->join('influencer_account_platform_details pd', 'a.id = pd.account_id', 'left');
        $this->db->join('users u', 'c.created_by = u.id', 'left');
        $this->db->join('brands b', 'c.brand_id = b.id', 'left');

        if ($brand_id) {
            $this->db->where('c.brand_id', $brand_id);
        }
    }

    public function get_influencers($filters = array(), $limit = null, $offset = null) {
        $this->_get_base_query();
        $this->_apply_filters($filters);

        if ($limit !== null && $offset !== null) {
            $this->db->limit($limit, $offset);
        } elseif ($limit !== null) {
            $this->db->limit($limit);
        }
        
        // Default spreadsheet sort (Row 1, 2, 3... sequential order)
        if (isset($filters['sort']) && strtolower($filters['sort']) === 'desc') {
            $this->db->order_by('c.id', 'DESC');
        } else {
            $this->db->order_by('c.id', 'ASC');
        }

        $query = $this->db->get();
        return $query->result();
    }

    public function count_influencers($filters = array()) {
        $brand_id = $this->session->userdata('brand_id');
        $this->db->from('collaborations c');
        $this->db->join('influencer_profiles p', 'c.profile_id = p.id', 'left');
        $this->db->join('influencer_accounts a', 'c.account_id = a.id', 'left');
        if ($brand_id) {
            $this->db->where('c.brand_id', $brand_id);
        }
        $this->_apply_filters($filters);
        return $this->db->count_all_results();
    }
    
    public function count_all_influencers() {
        $brand_id = $this->session->userdata('brand_id');
        if ($brand_id) {
            $this->db->where('brand_id', $brand_id);
        }
        return $this->db->count_all_results('collaborations');
    }

    private function _apply_filters($filters) {
        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('a.username', $filters['search']);
            $this->db->or_like('p.full_name', $filters['search']);
            $this->db->or_like('a.platform', $filters['search']);
            $this->db->or_like('p.notes', $filters['search']);
            $this->db->group_end();
        }

        if (!empty($filters['platform']) && $filters['platform'] != 'Platform') {
            $this->db->where('a.platform', $filters['platform']);
        }

        if (!empty($filters['niche']) && $filters['niche'] != 'Niche') {
            $this->db->join('influencer_niches n', 'a.id = n.account_id', 'left');
            $this->db->where('n.niche_name', $filters['niche']);
        }

        if (!empty($filters['status']) && $filters['status'] != 'Status') {
            $this->db->where('a.status', $filters['status']);
        }

        if (!empty($filters['pipeline_status'])) {
            $this->db->where('c.pipeline_status', $filters['pipeline_status']);
        }

        // Specific influencer filter
        if (!empty($filters['influencer_id']) && $filters['influencer_id'] !== 'all') {
            $this->db->where('c.id', (int)$filters['influencer_id']);
        }

        // Specific day / date filter
        if (!empty($filters['day'])) {
            $day_val = trim($filters['day']);
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $day_val)) {
                $this->db->where("DATE(c.created_at) =", $day_val);
            } elseif (is_numeric($day_val)) {
                $this->db->where("DAY(c.created_at) =", (int)$day_val);
            }
        }

        if (!empty($filters['date'])) {
            $this->db->where("DATE(c.created_at) =", trim($filters['date']));
        }

        // Specific month filter (supports 'YYYY-MM' e.g. '2026-10', or month number 1-12)
        if (!empty($filters['month'])) {
            $month_val = trim($filters['month']);
            if (strpos($month_val, '-') !== false) {
                list($yr, $mo) = explode('-', $month_val);
                $this->db->where("YEAR(c.created_at) =", (int)$yr);
                $this->db->where("MONTH(c.created_at) =", (int)$mo);
            } else {
                $this->db->where("MONTH(c.created_at) =", (int)$month_val);
                if (!empty($filters['year'])) {
                    $this->db->where("YEAR(c.created_at) =", (int)$filters['year']);
                }
            }
        }

        // Specific year filter
        if (!empty($filters['year']) && empty($filters['month'])) {
            $this->db->where("YEAR(c.created_at) =", (int)$filters['year']);
        }

        // Date range filter
        if (!empty($filters['start_date'])) {
            $this->db->where("DATE(c.created_at) >=", $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $this->db->where("DATE(c.created_at) <=", $filters['end_date']);
        }
    }

    public function get_influencers_by_ids($ids) {
        $this->_get_base_query();
        $this->db->where_in('c.id', $ids);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_influencer($id) {
        $this->_get_base_query();
        $this->db->where('c.id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_influencer_by_email($email) {
        $this->_get_base_query();
        $this->db->where('p.email', $email);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_influencers_by_pipeline_status($statuses, $filters = []) {
        $this->_get_base_query();
        
        if (!empty($statuses)) {
            $this->db->where_in('c.pipeline_status', $statuses);
        }

        // Apply search filter
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $this->db->group_start();
            $this->db->like('p.full_name', $search);
            $this->db->or_like('a.username', $search);
            $this->db->or_like('p.email', $search);
            $this->db->group_end();
        }

        // Apply platform filter
        if (isset($filters['platform']) && !empty($filters['platform'])) {
            $this->db->where('a.platform', $filters['platform']);
        }

        $this->db->order_by('c.created_at', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Search influencers across ALL pipeline statuses
     */
    public function search_global($search, $brand_id = null) {
        $this->_get_base_query();
        if ($brand_id) {
            $this->db->where('c.brand_id', $brand_id);
        }
        
        $this->db->group_start();
        $this->db->like('p.full_name', $search);
        $this->db->or_like('a.username', $search);
        $this->db->or_like('p.email', $search);
        $this->db->group_end();
        
        return $this->db->get()->result();
    }
    
    public function get_influencer_growth_trend($days = 30) {
        $this->db->select('DATE(created_at) as date, COUNT(*) as count');
        $this->db->from('collaborations');
        $this->db->where('created_at >=', date('Y-m-d', strtotime("-$days days")));
        $this->db->group_by('DATE(created_at)');
        $this->db->order_by('date', 'ASC');
        $query = $this->db->get();
        
        $result = [];
        // Fill in missing dates with 0
        $period = new DatePeriod(
            new DateTime("-$days days"),
            new DateInterval('P1D'),
            new DateTime('+1 day')
        );
        
        $data_map = [];
        foreach ($query->result() as $row) {
            $data_map[$row->date] = $row->count;
        }

        foreach ($period as $key => $value) {
            $date = $value->format('Y-m-d');
            $result[$date] = isset($data_map[$date]) ? $data_map[$date] : 0;
        }
        
        return $result;
    }

    public static function normalize_platform($platform) {
        if (empty($platform)) return 'Other';
        $p = strtolower(trim($platform));
        if ($p === 'instagram' || $p === 'ig') return 'Instagram';
        if ($p === 'tiktok' || $p === 'tt') return 'TikTok';
        if ($p === 'youtube' || $p === 'yt') return 'YouTube';
        if ($p === 'facebook' || $p === 'fb') return 'Facebook';
        if ($p === 'linkedin') return 'LinkedIn';
        if ($p === 'twitter' || $p === 'x') return 'Twitter';
        return ucfirst($p);
    }

    public function get_analytics_stats($days = null) {
        $brand_id = $this->session->userdata('brand_id');

        $stats = [
            'total_influencers' => 0,
            'platform_counts' => [
                'Instagram' => 0,
                'TikTok' => 0,
                'YouTube' => 0,
                'Facebook' => 0
            ],
            'total_likes' => 0,
            'total_comments' => 0,
            'total_views' => 0,
            'total_video_views' => 0,
            'cpm' => 0,
            'sales_quantity' => 0,
            'sales_amount' => 0,
            'product_expenditure' => 0,
            'web_clicks' => 0,
            'won_count' => 0,
            'posted_count' => 0,
            'outreach_count' => 0,
            'responded_count' => 0,
            'not_interested_count' => 0,
            'content_pending_count' => 0,
            'inactive_count' => 0,
            'content_saved_count' => 0,
            'pipeline_breakdown' => [],
            'location_breakdown' => [],
            'platform_details' => []
        ];

        // 1. Basic Aggregates
        $this->db->select('
            COUNT(*) as total_influencers,
            COALESCE(SUM(s.gmv), 0) as sales_amount,
            COALESCE(SUM(c.product_cost), 0) as product_expenditure,
            COALESCE(SUM(s.avg_video_views), 0) as total_views,
            COALESCE(SUM(COALESCE(s.followers,0) * (COALESCE(s.eng_rate,0) / 100)), 0) as total_engagements,
            COALESCE(AVG(s.eng_rate), 0) as avg_eng_rate
        ');
        $this->db->from('collaborations c');
        $this->db->join('influencer_accounts a', 'c.account_id = a.id', 'left');
        $this->db->join('influencer_account_stats s', 'a.id = s.account_id', 'left');
        if ($brand_id) {
            $this->db->where('c.brand_id', $brand_id);
        }
        if ($days) {
            $this->db->where('c.created_at >=', date('Y-m-d H:i:s', strtotime("-$days days")));
        }
        $row = $this->db->get()->row();
        
        $stats['total_influencers'] = $row->total_influencers;
        $stats['sales_amount'] = $row->sales_amount;
        $stats['product_expenditure'] = $row->product_expenditure;
        $stats['total_views'] = $row->total_views;
        $stats['avg_eng_rate'] = $row->avg_eng_rate;
        
        // Estimates based on Engagement Rate
        $total_engagements = $row->total_engagements;
        $stats['total_likes'] = $total_engagements * 0.95; // Assume 95% of engagement is likes
        $stats['total_comments'] = $total_engagements * 0.05; // Assume 5% of engagement is comments
        
        // Calculate CPM (Cost Per Mille) = (Cost / Views) * 1000
        if ($stats['total_views'] > 0) {
            $stats['cpm'] = ($stats['product_expenditure'] / $stats['total_views']) * 1000;
        }

        $stats['outreach_count'] = $row->total_influencers; // Assuming all are outreach

        // 2. Pipeline Breakdown
        $this->db->select('pipeline_status, COUNT(*) as count');
        if ($brand_id) {
            $this->db->where('brand_id', $brand_id);
        }
        if ($days) {
            $this->db->where('created_at >=', date('Y-m-d H:i:s', strtotime("-$days days")));
        }
        $this->db->group_by('pipeline_status');
        $query = $this->db->get('collaborations');
        foreach($query->result() as $row) {
            $stats['pipeline_breakdown'][$row->pipeline_status] = $row->count;
            
            // Map to specific counts
            $status = $row->pipeline_status;
            $won_stages = ['For Order Creation', 'In-Transit', 'Delivered', 'Posted', 'Campaign Completed'];
            $responded_stages = ['Replied', 'In - Progress', 'For Order Creation', 'In-Transit', 'Delivered', 'Posted', 'Campaign Completed', 'Not Interested'];

            if (in_array($status, $won_stages)) $stats['won_count'] += $row->count;
            if (in_array($status, $responded_stages)) $stats['responded_count'] += $row->count;
            if ($status == 'Posted' || $status == 'Campaign Completed') $stats['posted_count'] += $row->count;
            if ($status == 'Not Interested') $stats['not_interested_count'] += $row->count;
            if ($status == 'Content Pending' || $status == 'In-Transit' || $status == 'Delivered') $stats['content_pending_count'] += $row->count;
        }

        // 3. Platform Breakdown
        $this->db->select('a.platform, COUNT(*) as count');
        $this->db->from('collaborations c');
        $this->db->join('influencer_accounts a', 'c.account_id = a.id', 'left');
        if ($brand_id) {
            $this->db->where('c.brand_id', $brand_id);
        }
        if ($days) {
            $this->db->where('c.created_at >=', date('Y-m-d H:i:s', strtotime("-$days days")));
        }
        $this->db->group_by('a.platform');
        $query = $this->db->get();
        foreach($query->result() as $row) {
            $norm_p = self::normalize_platform($row->platform);
            if (!isset($stats['platform_counts'][$norm_p])) {
                $stats['platform_counts'][$norm_p] = 0;
            }
            $stats['platform_counts'][$norm_p] += (int)$row->count;
        }

        // 4. Location Breakdown
        $this->db->select('p.location, COUNT(*) as count');
        $this->db->from('collaborations c');
        $this->db->join('influencer_profiles p', 'c.profile_id = p.id', 'left');
        if ($brand_id) {
            $this->db->where('c.brand_id', $brand_id);
        }
        if ($days) {
            $this->db->where('c.created_at >=', date('Y-m-d H:i:s', strtotime("-$days days")));
        }
        $this->db->group_by('p.location');
        $query = $this->db->get();
        foreach($query->result() as $row) {
            $stats['location_breakdown'][$row->location] = $row->count;
        }

        // 5. Detailed Platform Stats
        $this->db->select('a.platform, c.pipeline_status, COUNT(*) as count, COALESCE(SUM(s.gmv), 0) as sales_amount');
        $this->db->from('collaborations c');
        $this->db->join('influencer_accounts a', 'c.account_id = a.id', 'left');
        $this->db->join('influencer_account_stats s', 'a.id = s.account_id', 'left');
        if ($brand_id) {
            $this->db->where('c.brand_id', $brand_id);
        }
        if ($days) {
            $this->db->where('c.created_at >=', date('Y-m-d H:i:s', strtotime("-$days days")));
        }
        $this->db->group_by(['a.platform', 'c.pipeline_status']);
        $query = $this->db->get();
        
        $platforms = ['Facebook', 'Instagram', 'TikTok', 'YouTube'];
        foreach ($platforms as $p) {
            $stats['platform_details'][$p] = [
                'total' => 0, 'outreach' => 0, 'responded' => 0, 'not_interested' => 0, 
                'content_pending' => 0, 'posted' => 0, 'web_clicks' => 0,
                'sales_quantity' => 0, 'sales_amount' => 0, 'cvr' => 0, 'won' => 0
            ];
        }

        foreach($query->result() as $row) {
            $p = self::normalize_platform($row->platform);
            $s = $row->pipeline_status;
            $c = (int)$row->count;
            $gmv = (float)$row->sales_amount;

            if (!isset($stats['platform_details'][$p])) {
                $stats['platform_details'][$p] = [
                    'total' => 0, 'outreach' => 0, 'responded' => 0, 'not_interested' => 0, 
                    'content_pending' => 0, 'posted' => 0, 'web_clicks' => 0,
                    'sales_quantity' => 0, 'sales_amount' => 0, 'cvr' => 0, 'won' => 0
                ];
            }

            $stats['platform_details'][$p]['total'] += $c;
            $stats['platform_details'][$p]['outreach'] += $c;
            $stats['platform_details'][$p]['sales_amount'] += $gmv;
            
            $won_stages = ['For Order Creation', 'In-Transit', 'Delivered', 'Posted', 'Campaign Completed'];
            $responded_stages = ['Replied', 'In - Progress', 'For Order Creation', 'In-Transit', 'Delivered', 'Posted', 'Campaign Completed', 'Not Interested'];

            if (in_array($s, $won_stages)) $stats['platform_details'][$p]['won'] += $c;
            if (in_array($s, $responded_stages)) $stats['platform_details'][$p]['responded'] += $c;
            if ($s == 'Posted' || $s == 'Campaign Completed') $stats['platform_details'][$p]['posted'] += $c;
            if ($s == 'Not Interested') $stats['platform_details'][$p]['not_interested'] += $c;
            if ($s == 'Content Pending' || $s == 'In-Transit' || $s == 'Delivered') $stats['platform_details'][$p]['content_pending'] += $c;
        }

        return $stats;
    }

    public function get_recent_influencers($limit = 5) {
        $this->_get_base_query();
        $this->db->order_by('c.created_at', 'DESC');
        $this->db->limit($limit);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_all_influencers() {
        return $this->get_influencers();
    }

    public function search_influencers($search) {
        return $this->get_influencers(['search' => $search]);
    }

    public function get_influencer_by_id($id) {
        $this->_get_base_query();
        $this->db->where('c.id', $id);
        $query = $this->db->get();
        return $query->row();
    }

    public function update_pipeline_status($id, $status) {
        $this->db->where('id', $id);
        return $this->db->update('collaborations', ['pipeline_status' => $status]);
    }

    public function bulk_update_status($ids, $status) {
        if (empty($ids)) return false;
        $this->db->where_in('id', $ids);
        return $this->db->update('collaborations', ['pipeline_status' => $status]);
    }

    public function insert_influencer($data) {
        $this->db->trans_start();

        // 1. Profile Data
        $full_name = isset($data['full_name']) ? trim((string)$data['full_name']) : '';
        $email = isset($data['email']) ? trim((string)$data['email']) : '';
        $contact_info = isset($data['contact_info']) ? trim((string)$data['contact_info']) : '';

        if (empty($email) && filter_var($contact_info, FILTER_VALIDATE_EMAIL)) {
            $email = $contact_info;
        }
        if (empty($contact_info) && !empty($email)) {
            $contact_info = $email;
        }
        if (empty($full_name)) {
            $username = isset($data['username']) ? trim((string)$data['username']) : '';
            $full_name = !empty($username) ? $username : (!empty($email) ? explode('@', $email)[0] : 'Influencer');
        }

        $clean_notes = isset($data['notes']) ? trim((string)$data['notes']) : '';
        if (preg_match('/^(Discovered via|Direct Lookup via|Verified TikTok Creator|Existing Influencer in Database)/i', $clean_notes)) {
            $clean_notes = '';
        }

        $profile_data = array(
            'full_name' => $full_name,
            'first_name' => isset($data['first_name']) ? $data['first_name'] : '',
            'last_name' => isset($data['last_name']) ? $data['last_name'] : '',
            'email' => $email,
            'contact_number' => isset($data['contact_number']) ? $data['contact_number'] : '',
            'contact_info' => $contact_info,
            'location' => isset($data['location']) ? $data['location'] : '',
            'shipping_address' => isset($data['shipping_address']) ? $data['shipping_address'] : '',
            'notes' => $clean_notes,
        );

        // Check if account already exists for (platform, username) or email to reuse profile_id
        $profile_id = 0;
        if (!empty($data['username']) && !empty($data['platform'])) {
            $this->db->where('platform', $data['platform']);
            $this->db->where('username', $data['username']);
            $qa = $this->db->get('influencer_accounts');
            if ($qa->num_rows() > 0 && !empty($qa->row()->profile_id)) {
                $profile_id = (int)$qa->row()->profile_id;
                $this->db->where('id', $profile_id);
                $this->db->update('influencer_profiles', array_filter($profile_data));
            }
        }

        if ($profile_id == 0 && !empty($email)) {
            $this->db->where('email', $email);
            $q = $this->db->get('influencer_profiles');
            if ($q->num_rows() > 0) {
                $profile_id = (int)$q->row()->id;
                $this->db->where('id', $profile_id);
                $this->db->update('influencer_profiles', array_filter($profile_data));
            }
        }
        
        if ($profile_id == 0) {
            if (!$this->db->insert('influencer_profiles', $profile_data)) {
                log_message('error', 'Insert Profile Failed: ' . json_encode($this->db->error()));
            }
            $profile_id = $this->db->insert_id();
        }

        // 2. Account Data
        $account_id = 0;
        if (isset($data['username']) || isset($data['platform'])) {
            $account_core = array(
                'profile_id' => $profile_id,
                'platform' => isset($data['platform']) ? $data['platform'] : '',
                'username' => isset($data['username']) ? $data['username'] : '',
                'profile_image' => isset($data['profile_image']) ? $data['profile_image'] : '',
                'status' => isset($data['status']) ? $data['status'] : 'Active',
            );

            $account_stats = array(
                'followers' => isset($data['followers']) ? $data['followers'] : 0,
                'eng_rate' => isset($data['eng_rate']) ? $data['eng_rate'] : 0,
                'avg_video_views' => isset($data['avg_video_views']) ? $data['avg_video_views'] : 0,
                'gmv' => isset($data['gmv']) ? $data['gmv'] : 0,
                'est_post_rate' => isset($data['est_post_rate']) ? $data['est_post_rate'] : 0,
            );

            $account_details = array(
                'bio' => isset($data['bio']) ? $data['bio'] : NULL,
                'external_url' => isset($data['external_url']) ? $data['external_url'] : NULL,
                'account_type' => isset($data['account_type']) ? $data['account_type'] : 'personal',
                'verified_status' => isset($data['verified_status']) ? $data['verified_status'] : 0,
            );

            // Check if account exists for this profile
            if (!empty($account_core['username'])) {
                $this->db->where('profile_id', $profile_id);
                $this->db->where('platform', $account_core['platform']);
                $this->db->where('username', $account_core['username']);
                $q = $this->db->get('influencer_accounts');
                if ($q->num_rows() > 0) {
                    $account_id = $q->row()->id;
                    $this->db->where('id', $account_id);
                    $this->db->update('influencer_accounts', $account_core);
                } else {
                    // Try to find by username/platform globally (handle existing account with different profile)
                    $this->db->where('platform', $account_core['platform']);
                    $this->db->where('username', $account_core['username']);
                    $q2 = $this->db->get('influencer_accounts');
                    
                    if ($q2->num_rows() > 0) {
                        $account_id = $q2->row()->id;
                        // Update account with new data AND new profile_id
                        $this->db->where('id', $account_id);
                        $this->db->update('influencer_accounts', $account_core);
                    } else {
                        if (!$this->db->insert('influencer_accounts', $account_core)) {
                            log_message('error', 'Insert Account Failed: ' . json_encode($this->db->error()));
                        }
                        $account_id = $this->db->insert_id();
                    }
                }
            } else {
                 // Insert even without username if platform provided? 
                 if (!empty($account_core['platform'])) {
                    $this->db->insert('influencer_accounts', $account_core);
                    $account_id = $this->db->insert_id();
                 }
            }

            // Update Breakdown Tables
            if ($account_id > 0) {
                // Update Stats
                $this->db->where('account_id', $account_id);
                $q_stats = $this->db->get('influencer_account_stats');
                if ($q_stats->num_rows() > 0) {
                    $this->db->where('account_id', $account_id);
                    $this->db->update('influencer_account_stats', $account_stats);
                } else {
                    $account_stats['account_id'] = $account_id;
                    $this->db->insert('influencer_account_stats', $account_stats);
                }

                // Update Platform Details
                $this->db->where('account_id', $account_id);
                $q_pd = $this->db->get('influencer_account_platform_details');
                if ($q_pd->num_rows() > 0) {
                    $this->db->where('account_id', $account_id);
                    $this->db->update('influencer_account_platform_details', $account_details);
                } else {
                    $account_details['account_id'] = $account_id;
                    $this->db->insert('influencer_account_platform_details', $account_details);
                }

                // --- NEW: Add initial metrics snapshot ---
                $this->add_metrics_snapshot($account_id, $account_stats);
                
                // --- NEW: Sync niches if present ---
                if (!empty($data['niche'])) {
                    $niches = is_array($data['niche']) ? $data['niche'] : explode(',', $data['niche']);
                    $this->update_niches($account_id, $niches);
                }
            }
        }

        // 3. Collaboration Data
        $collab_data = array(
            'brand_id' => $this->session->userdata('brand_id'),
            'profile_id' => $profile_id,
            'account_id' => ($account_id > 0) ? $account_id : NULL,
            'pipeline_status' => isset($data['pipeline_status']) ? $data['pipeline_status'] : 'For Outreach',
            'campaign_type' => isset($data['campaign_type']) ? $data['campaign_type'] : '',
            'product_name' => isset($data['product_name']) ? $data['product_name'] : '',
            'order_number' => isset($data['order_number']) ? $data['order_number'] : '',
            'product_cost' => isset($data['product_cost']) ? $data['product_cost'] : NULL,
            'discount_code' => isset($data['discount_code']) ? $data['discount_code'] : '',
            'affiliate_link' => isset($data['affiliate_link']) ? $data['affiliate_link'] : '',
            'tracking_link' => isset($data['tracking_link']) ? $data['tracking_link'] : '',
            'assessment' => isset($data['assessment']) ? $data['assessment'] : '',
            'created_by' => isset($data['created_by']) ? $data['created_by'] : NULL,
            'created_at' => isset($data['created_at']) ? $data['created_at'] : date('Y-m-d H:i:s')
        );

        if (!$this->db->insert('collaborations', $collab_data)) {
             log_message('error', 'Insert Collaboration Failed: ' . json_encode($this->db->error()));
             return false;
        }
        $insert_id = $this->db->insert_id();

        $this->db->trans_complete();
        return $insert_id;
    }

    public function insert_batch_influencers($data) {
        // Batch insert is tricky with 3 tables. Better to loop insert_influencer
        $this->db->trans_start();
        foreach ($data as $row) {
            $this->insert_influencer($row);
        }
        $this->db->trans_complete();
        return TRUE;
    }

    public function update_influencer($id, $data) {
        // $id is collaboration id
        $collab = $this->get_influencer_by_id($id);
        if (!$collab) return false;

        $this->db->trans_start();

        // Split data
        $profile_cols = ['full_name', 'first_name', 'last_name', 'email', 'contact_number', 'contact_info', 'location', 'shipping_address', 'notes', 'gender', 'niche'];
        $account_core_cols = ['platform', 'username', 'profile_image', 'status'];
        $account_stats_cols = ['followers', 'eng_rate', 'avg_video_views', 'gmv', 'est_post_rate'];
        $account_details_cols = ['bio', 'external_url', 'account_type', 'verified_status'];
        $collab_cols = ['pipeline_status', 'campaign_type', 'product_name', 'order_number', 'product_cost', 'discount_code', 'affiliate_link', 'tracking_link', 'assessment', 'date_reviewed', 'deliverables', 'fee', 'deadline'];

        $profile_data = [];
        $account_core_data = [];
        $account_stats_data = [];
        $account_details_data = [];
        $collab_data = [];

        foreach ($data as $key => $value) {
            if (in_array($key, $profile_cols)) $profile_data[$key] = $value;
            if (in_array($key, $account_core_cols)) $account_core_data[$key] = $value;
            if (in_array($key, $account_stats_cols)) $account_stats_data[$key] = $value;
            if (in_array($key, $account_details_cols)) $account_details_data[$key] = $value;
            if (in_array($key, $collab_cols)) $collab_data[$key] = $value;
        }

        if (!empty($profile_data)) {
            if ($collab->profile_id) {
                $this->db->where('id', $collab->profile_id);
                $this->db->update('influencer_profiles', $profile_data);
            } else {
                $this->db->insert('influencer_profiles', $profile_data);
                $new_profile_id = $this->db->insert_id();
                $this->db->where('id', $collab->id)->update('collaborations', ['profile_id' => $new_profile_id]);
                if ($collab->account_id) {
                    $this->db->where('id', $collab->account_id)->update('influencer_accounts', ['profile_id' => $new_profile_id]);
                }
            }
        }

        if ($collab->account_id) {
            // Update Core Account
            if (!empty($account_core_data)) {
                $this->db->where('id', $collab->account_id);
                $this->db->update('influencer_accounts', $account_core_data);
            }

            // Update Stats
            if (!empty($account_stats_data)) {
                // Check for snapshot
                $current_stats = $this->db->get_where('influencer_account_stats', ['account_id' => $collab->account_id])->row();
                $metrics_changed = false;
                foreach ($account_stats_cols as $key) {
                    if (isset($account_stats_data[$key]) && (!$current_stats || $account_stats_data[$key] != $current_stats->$key)) {
                        $metrics_changed = true;
                        break;
                    }
                }

                $this->db->where('account_id', $collab->account_id);
                if ($this->db->get('influencer_account_stats')->num_rows() > 0) {
                    $this->db->where('account_id', $collab->account_id);
                    $this->db->update('influencer_account_stats', $account_stats_data);
                } else {
                    $account_stats_data['account_id'] = $collab->account_id;
                    $this->db->insert('influencer_account_stats', $account_stats_data);
                }

                if ($metrics_changed) {
                    $this->add_metrics_snapshot($collab->account_id, $account_stats_data);
                }
            }

            // Update Details
            if (!empty($account_details_data)) {
                $this->db->where('account_id', $collab->account_id);
                if ($this->db->get('influencer_account_platform_details')->num_rows() > 0) {
                    $this->db->where('account_id', $collab->account_id);
                    $this->db->update('influencer_account_platform_details', $account_details_data);
                } else {
                    $account_details_data['account_id'] = $collab->account_id;
                    $this->db->insert('influencer_account_platform_details', $account_details_data);
                }
            }

            // Update niches
            if (isset($data['niche'])) {
                $niches = is_array($data['niche']) ? $data['niche'] : explode(',', $data['niche']);
                $this->update_niches($collab->account_id, $niches);
            }
        } else if (!empty($account_core_data) && !empty($account_core_data['username']) && $collab->profile_id) {
             // Create new account if it didn't exist
             $account_core_data['profile_id'] = $collab->profile_id;
             $this->db->insert('influencer_accounts', $account_core_data);
             $new_acc_id = $this->db->insert_id();
             
             if ($new_acc_id) {
                $account_stats_data['account_id'] = $new_acc_id;
                $this->db->insert('influencer_account_stats', $account_stats_data);
                
                $account_details_data['account_id'] = $new_acc_id;
                $this->db->insert('influencer_account_platform_details', $account_details_data);

                $this->db->where('id', $id);
                $this->db->update('collaborations', ['account_id' => $new_acc_id]);
             }
        }

        if (!empty($collab_data)) {
            $this->db->where('id', $id);
            $this->db->update('collaborations', $collab_data);
        }

        $this->db->trans_complete();
        return TRUE;
    }

    public function delete_influencer($id) {
        $id = (int)$id;
        if ($id <= 0) {
            return false;
        }

        $this->db->trans_start();

        if ($this->db->table_exists('influencer_deliverables') && $this->db->field_exists('collaboration_id', 'influencer_deliverables')) {
            $this->db->where('collaboration_id', $id)->delete('influencer_deliverables');
        }

        if ($this->db->table_exists('influencer_tasks') && $this->db->field_exists('influencer_id', 'influencer_tasks')) {
            $this->db->where('influencer_id', $id)->delete('influencer_tasks');
        }

        if ($this->db->table_exists('campaign_influencers') && $this->db->field_exists('influencer_id', 'campaign_influencers')) {
            $this->db->where('influencer_id', $id)->delete('campaign_influencers');
        }

        if ($this->db->table_exists('email_conversations') && $this->db->field_exists('influencer_id', 'email_conversations')) {
            $this->db->where('influencer_id', $id)->delete('email_conversations');
        }

        $this->db->where('id', $id)->delete('collaborations');
        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    public function delete_influencers_by_ids($ids) {
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }

        $clean_ids = [];
        if (is_array($ids)) {
            foreach ($ids as $id) {
                $iid = (int)$id;
                if ($iid > 0) {
                    $clean_ids[] = $iid;
                }
            }
        }
        $clean_ids = array_values(array_unique($clean_ids));
        if (empty($clean_ids)) {
            return false;
        }

        $this->db->trans_start();

        if ($this->db->table_exists('influencer_deliverables') && $this->db->field_exists('collaboration_id', 'influencer_deliverables')) {
            $this->db->where_in('collaboration_id', $clean_ids)->delete('influencer_deliverables');
        }

        if ($this->db->table_exists('influencer_tasks') && $this->db->field_exists('influencer_id', 'influencer_tasks')) {
            $this->db->where_in('influencer_id', $clean_ids)->delete('influencer_tasks');
        }

        if ($this->db->table_exists('campaign_influencers') && $this->db->field_exists('influencer_id', 'campaign_influencers')) {
            $this->db->where_in('influencer_id', $clean_ids)->delete('campaign_influencers');
        }

        if ($this->db->table_exists('email_conversations') && $this->db->field_exists('influencer_id', 'email_conversations')) {
            $this->db->where_in('influencer_id', $clean_ids)->delete('email_conversations');
        }

        $this->db->where_in('id', $clean_ids)->delete('collaborations');

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function update_influencers_by_ids($ids, $data) {
        // Only updates collaboration table safely for batch
        // If updating profile/account fields in batch, it's risky as IDs refer to collabs
        // Assuming batch updates are mostly for pipeline_status (collab field)
        $this->db->where_in('id', $ids);
        return $this->db->update('collaborations', $data);
    }

    public function delete_all_influencers() {
        return $this->db->empty_table('collaborations');
    }

    // --- NEW BREAKDOWN METHODS ---

    /**
     * Log a metric snapshot for historical tracking
     */
    public function add_metrics_snapshot($account_id, $data) {
        $snapshot = [
            'account_id' => $account_id,
            'followers' => isset($data['followers']) ? $data['followers'] : 0,
            'eng_rate' => isset($data['eng_rate']) ? $data['eng_rate'] : 0,
            'avg_video_views' => isset($data['avg_video_views']) ? $data['avg_video_views'] : 0,
            'gmv' => isset($data['gmv']) ? $data['gmv'] : 0.00
        ];
        return $this->db->insert('influencer_metrics_history', $snapshot);
    }

    /**
     * Get historical metrics for an account
     */
    public function get_metrics_history($account_id, $limit = 10) {
        $this->db->where('account_id', $account_id);
        $this->db->order_by('recorded_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get('influencer_metrics_history')->result();
    }

    /**
     * Add a chronological note for a profile
     */
    public function add_note($profile_id, $content, $category = 'general', $user_id = null) {
        $note = [
            'profile_id' => $profile_id,
            'user_id' => $user_id ? $user_id : $this->session->userdata('user_id'),
            'content' => $content,
            'category' => $category
        ];
        return $this->db->insert('influencer_notes', $note);
    }

    /**
     * Get all notes for a profile
     */
    public function get_notes($profile_id) {
        $this->db->select('n.*, u.name as author_name');
        $this->db->from('influencer_notes n');
        $this->db->join('users u', 'n.user_id = u.id', 'left');
        $this->db->where('n.profile_id', $profile_id);
        $this->db->order_by('n.created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Track a new deliverable (post) for a collaboration
     */
    public function add_deliverable($collaboration_id, $data) {
        $deliverable = [
            'collaboration_id' => $collaboration_id,
            'post_url' => $data['post_url'],
            'post_type' => isset($data['post_type']) ? $data['post_type'] : 'image',
            'status' => 'pending',
            'metrics_at_post' => isset($data['metrics']) ? json_encode($data['metrics']) : NULL,
            'posted_at' => isset($data['posted_at']) ? $data['posted_at'] : date('Y-m-d H:i:s')
        ];
        return $this->db->insert('influencer_deliverables', $deliverable);
    }

    /**
     * Get deliverables for a specific collaboration
     */
    public function get_deliverables($collaboration_id) {
        $this->db->where('collaboration_id', $collaboration_id);
        $this->db->order_by('posted_at', 'DESC');
        return $this->db->get('influencer_deliverables')->result();
    }

    /**
     * Sync normalized niches for an account
     */
    public function update_niches($account_id, $niches_array) {
        // Remove existing
        $this->db->where('account_id', $account_id);
        $this->db->delete('influencer_niches');

        // Add new
        $data = [];
        foreach ($niches_array as $niche) {
            if (empty(trim($niche))) continue;
            $data[] = [
                'account_id' => $account_id,
                'niche_name' => trim($niche)
            ];
        }

        if (!empty($data)) {
            return $this->db->insert_batch('influencer_niches', $data);
        }
        return true;
    }

    /**
     * Get normalized niches for an account
     */
    public function get_niches($account_id) {
        $this->db->where('account_id', $account_id);
        $res = $this->db->get('influencer_niches')->result();
        $niches = [];
        foreach ($res as $row) {
            $niches[] = $row->niche_name;
        }
        return $niches;
    }

    /**
     * Automatically transition pipeline status based on email interactions.
     * outgoing = Sourced/For Outreach -> Contacted
     * incoming = Contacted -> Replied
     */
    public function auto_transition_pipeline_on_email($influencer_id, $direction) {
        if (!$influencer_id) return false;
        
        $collab = $this->db->get_where('collaborations', ['id' => $influencer_id])->row();
        if (!$collab) return false;

        $current_stage = $collab->pipeline_status;
        $new_stage = null;
        $details = "";

        if ($direction === 'outgoing' && ($current_stage === 'For Outreach' || $current_stage === 'Sourced')) {
            $new_stage = 'Contacted';
            $details = "Auto-moved creator to 'Contacted' stage after sending outreach email.";
        } elseif ($direction === 'incoming' && $current_stage === 'Contacted') {
            $new_stage = 'Replied';
            $details = "Auto-moved creator to 'Replied' stage after receiving incoming email.";
        }

        if ($new_stage) {
            $this->db->where('id', $influencer_id)->update('collaborations', ['pipeline_status' => $new_stage]);
            $this->db->where('influencer_id', $influencer_id)->update('email_threads', ['pipeline_stage' => $new_stage]);
            
            // Log transition
            $this->load->model('Activity_log_model');
            $this->Activity_log_model->log_activity(
                'Update Pipeline Stage',
                $details,
                $collab->created_by ?: $this->session->userdata('user_id'),
                'System'
            );
            return true;
        }

        return false;
    }

    // ========================================================
    // BRAND AMBASSADORS / PARTNERS SYSTEM
    // ========================================================

    /**
     * Ensure database tables exist for Ambassadors and Brand Settings.
     */
    public function _ensure_ambassadors_tables() {
        if (!$this->db->table_exists('ambassadors')) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `ambassadors` (
                    `id` INT NOT NULL AUTO_INCREMENT,
                    `brand_id` INT NOT NULL,
                    `collaboration_id` INT NOT NULL,
                    `community_status` VARCHAR(50) NOT NULL DEFAULT 'Joined',
                    `gmv_generated` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                    `retainer_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                    `retainer_status` VARCHAR(100) NOT NULL DEFAULT 'Per-Campaign Basis',
                    `retainer_notes` TEXT NULL,
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    KEY `idx_brand_collab` (`brand_id`, `collaboration_id`),
                    KEY `idx_community_status` (`community_status`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
            ");
        }

        if (!$this->db->table_exists('brand_settings')) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `brand_settings` (
                    `id` INT NOT NULL AUTO_INCREMENT,
                    `brand_id` INT NOT NULL,
                    `gold_threshold` DECIMAL(12,2) NOT NULL DEFAULT 500000.00,
                    `silver_threshold` DECIMAL(12,2) NOT NULL DEFAULT 150000.00,
                    `bronze_threshold` DECIMAL(12,2) NOT NULL DEFAULT 50000.00,
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `uk_brand_id` (`brand_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
            ");
        }
    }

    /**
     * Get revenue milestone thresholds for a brand.
     */
    public function get_brand_thresholds($brand_id) {
        $this->_ensure_ambassadors_tables();
        $row = $this->db->get_where('brand_settings', ['brand_id' => (int)$brand_id])->row();
        if ($row) {
            return (object)[
                'gold' => (float)$row->gold_threshold,
                'silver' => (float)$row->silver_threshold,
                'bronze' => (float)$row->bronze_threshold
            ];
        }
        return (object)[
            'gold' => 500000.00,
            'silver' => 150000.00,
            'bronze' => 50000.00
        ];
    }

    /**
     * Save custom revenue thresholds for a brand.
     */
    public function save_brand_thresholds($brand_id, $gold, $silver, $bronze) {
        $this->_ensure_ambassadors_tables();
        $existing = $this->db->get_where('brand_settings', ['brand_id' => (int)$brand_id])->row();
        if ($existing) {
            return $this->db->where('brand_id', (int)$brand_id)->update('brand_settings', [
                'gold_threshold' => max(0, (float)$gold),
                'silver_threshold' => max(0, (float)$silver),
                'bronze_threshold' => max(0, (float)$bronze)
            ]);
        } else {
            return $this->db->insert('brand_settings', [
                'brand_id' => (int)$brand_id,
                'gold_threshold' => max(0, (float)$gold),
                'silver_threshold' => max(0, (float)$silver),
                'bronze_threshold' => max(0, (float)$bronze)
            ]);
        }
    }

    /**
     * Get or initialize ambassador record for a collaboration.
     */
    public function get_ambassador_record($brand_id, $collaboration_id) {
        $this->_ensure_ambassadors_tables();
        $record = $this->db->get_where('ambassadors', [
            'brand_id' => (int)$brand_id,
            'collaboration_id' => (int)$collaboration_id
        ])->row();

        if (!$record) {
            // Check collaboration & stats to set initial values
            $collab = $this->db->get_where('collaborations', ['id' => $collaboration_id])->row();
            $gmv = 0.00;
            if ($collab && $collab->account_id) {
                $stat = $this->db->get_where('influencer_account_stats', ['account_id' => $collab->account_id])->row();
                if ($stat) {
                    $gmv = (float)($stat->gmv ?? 0.00);
                }
            }

            $initial = [
                'brand_id' => (int)$brand_id,
                'collaboration_id' => (int)$collaboration_id,
                'community_status' => 'Joined',
                'gmv_generated' => $gmv,
                'retainer_amount' => 0.00,
                'retainer_status' => 'Per-Campaign Basis',
                'retainer_notes' => ''
            ];
            $this->db->insert('ambassadors', $initial);
            $record = $this->db->get_where('ambassadors', ['id' => $this->db->insert_id()])->row();
        }

        return $record;
    }

    /**
     * Update retainer tracking for an ambassador.
     */
    public function update_partner_retainer($brand_id, $collaboration_id, $retainer_amount, $retainer_status, $retainer_notes = '') {
        $this->_ensure_ambassadors_tables();
        $this->get_ambassador_record($brand_id, $collaboration_id);

        return $this->db->where([
            'brand_id' => (int)$brand_id,
            'collaboration_id' => (int)$collaboration_id
        ])->update('ambassadors', [
            'retainer_amount' => max(0, (float)$retainer_amount),
            'retainer_status' => trim((string)$retainer_status),
            'retainer_notes' => trim((string)$retainer_notes)
        ]);
    }

    /**
     * Update community status for an ambassador (Invited, Joined, Pending).
     */
    public function update_partner_community_status($brand_id, $collaboration_id, $community_status) {
        $this->_ensure_ambassadors_tables();
        $this->get_ambassador_record($brand_id, $collaboration_id);

        return $this->db->where([
            'brand_id' => (int)$brand_id,
            'collaboration_id' => (int)$collaboration_id
        ])->update('ambassadors', [
            'community_status' => trim((string)$community_status)
        ]);
    }

    /**
     * Get detailed performance history for an ambassador.
     */
    public function get_partner_performance_history($brand_id, $collaboration_id) {
        $collab = $this->db->get_where('collaborations', ['id' => $collaboration_id])->row();
        if (!$collab) return null;

        $profile = $this->db->get_where('influencer_profiles', ['id' => $collab->profile_id])->row();
        $account = $collab->account_id ? $this->db->get_where('influencer_accounts', ['id' => $collab->account_id])->row() : null;
        $stats = $collab->account_id ? $this->db->get_where('influencer_account_stats', ['account_id' => $collab->account_id])->row() : null;
        $amb = $this->get_ambassador_record($brand_id, $collaboration_id);

        $gmv = (float)($stats->gmv ?? $amb->gmv_generated ?? 0.00);
        $fee = (float)($collab->fee ?? 0.00);
        $contract_val = $fee > 0 ? $fee : (float)($stats->est_post_rate ?? 5000.00);

        return (object)[
            'collaboration_id' => $collab->id,
            'creator_name' => $profile ? $profile->full_name : 'Creator',
            'handle' => $account ? '@' . $account->username : '@creator',
            'profile_image' => $account ? $account->profile_image : '',
            'platform' => $account ? $account->platform : 'Instagram',
            'product_name' => $collab->product_name ?: 'Brand Campaign',
            'deliverables' => $collab->deliverables ?: '1x Reel, 2x Stories',
            'contract_value' => $contract_val,
            'gmv_generated' => $gmv,
            'retainer_amount' => (float)$amb->retainer_amount,
            'retainer_status' => $amb->retainer_status,
            'retainer_notes' => $amb->retainer_notes,
            'community_status' => $amb->community_status,
            'roi_multiplier' => $contract_val > 0 ? round($gmv / $contract_val, 1) : 0,
            'created_at' => $collab->created_at
        ];
    }
}

