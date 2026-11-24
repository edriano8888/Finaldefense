<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Brand_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function ensure_default_brand($user_id) {
        $brand = $this->db->where('name !=', 'Armful Media')->order_by('id', 'ASC')->get('brands')->row();

        if (!$brand) {
            $brand_data = [
                'name' => 'JBL',
                'industry' => 'Audio & Electronics',
                'owner_id' => $user_id
            ];
            $this->db->insert('brands', $brand_data);
            $brand_id = $this->db->insert_id();
            $brand = (object)array_merge(['id' => $brand_id], $brand_data);
        }

        $brand_id = (int)$brand->id;

        return $brand_id;
    }

    public function ensure_membership($brand_id, $user_id, $user_role = null) {
        if ($this->is_member($brand_id, $user_id)) {
            return true;
        }

        $role = 'collaborator';
        if ($user_role === 'admin' || $user_role === 'manager') {
            $role = 'admin';
        } elseif ($user_role === 'viewer') {
            $role = 'viewer';
        }

        return $this->db->insert('brand_members', [
            'brand_id' => $brand_id,
            'user_id' => $user_id,
            'role' => $role
        ]);
    }

    /**
     * Create a new brand and assign owner
     */
    public function create_brand($data, $owner_id) {
        $this->db->trans_start();

        // 1. Insert Brand
        $brand_data = array(
            'name' => $data['name'],
            'industry' => isset($data['industry']) ? $data['industry'] : NULL,
            'contact_person' => isset($data['contact_person']) ? $data['contact_person'] : NULL,
            'email' => isset($data['email']) ? $data['email'] : NULL,
            'phone' => isset($data['phone']) ? $data['phone'] : NULL,
            'budget_range' => isset($data['budget_range']) ? $data['budget_range'] : NULL,
            'preferred_platforms' => isset($data['preferred_platforms']) ? $data['preferred_platforms'] : NULL,
            'notes' => isset($data['notes']) ? $data['notes'] : NULL,
            'owner_id' => $owner_id
        );
        $this->db->insert('brands', $brand_data);
        $brand_id = $this->db->insert_id();

        // 2. Add Owner to Brand Members
        $member_data = array(
            'brand_id' => $brand_id,
            'user_id' => $owner_id,
            'role' => 'owner'
        );
        $this->db->insert('brand_members', $member_data);

        $this->db->trans_complete();
        return $brand_id;
    }

    /**
     * Get brand by ID
     */
    public function get_brand($brand_id) {
        return $this->db->get_where('brands', ['id' => $brand_id])->row();
    }

    public function update_brand($brand_id, $data) {
        $this->db->where('id', $brand_id);
        return $this->db->update('brands', $data);
    }

    public function delete_brand($brand_id) {
        // Warning: This will leave orphaned data in collaborations, campaigns etc.
        // In a real system, we'd use soft-deletes.
        $this->db->where('id', $brand_id);
        return $this->db->delete('brands');
    }

    public function get_all_brands() {
        $user_id = 0;
        if (isset($this->session)) {
            $user_id = (int)$this->session->userdata('user_id');
        }
        
        $this->db->from('brands b');
        if ($user_id > 0) {
            $this->db->select('b.*, bm.role AS role');
            $this->db->join('brand_members bm', 'bm.brand_id = b.id AND bm.user_id = ' . (int)$user_id, 'left', false);
            $this->db->group_start();
            $this->db->where('b.owner_id', $user_id);
            $this->db->or_where('bm.user_id IS NOT NULL', null, false);
            $this->db->group_end();
        } else {
            $this->db->select('b.*, NULL AS role', false);
        }
        $this->db->where('b.name !=', 'Armful Media');
        $this->db->order_by('b.name', 'ASC');
        return $this->db->get()->result();
    }

    public function get_all_brands_with_summary() {
        $brands = $this->get_all_brands();

        foreach ($brands as &$brand) {
            // Active Campaigns
            $this->db->where('brand_id', $brand->id);
            $this->db->where('status', 'Ongoing');
            $brand->active_campaigns = $this->db->count_all_results('campaigns');

            // Total Campaigns
            $this->db->where('brand_id', $brand->id);
            $brand->total_campaigns = $this->db->count_all_results('campaigns');

            // Total Budget Spent (Product Cost)
            $this->db->select('SUM(product_cost) as total_spent');
            $this->db->where('brand_id', $brand->id);
            $spent_row = $this->db->get('collaborations')->row();
            $brand->total_budget_spent = $spent_row->total_spent ?? 0;

            // Top Influencers (based on count of collaborations)
            $this->db->select('p.full_name, COUNT(c.id) as usage_count');
            $this->db->from('collaborations c');
            $this->db->join('influencer_profiles p', 'c.profile_id = p.id');
            $this->db->where('c.brand_id', $brand->id);
            $this->db->group_by('c.profile_id');
            $this->db->order_by('usage_count', 'DESC');
            $this->db->limit(3);
            $brand->top_influencers = $this->db->get()->result();
        }

        return $brands;
    }

    /**
     * Get brand subscription details
     */
    public function get_subscription($brand_id) {
        return null;
    }

    /**
     * Get members of a brand
     */
    public function get_members($brand_id) {
        $this->db->select('bm.*, u.name, u.email, u.status AS user_status, u.last_login');
        $this->db->from('brand_members bm');
        $this->db->join('users u', 'bm.user_id = u.id');
        $this->db->where('bm.brand_id', $brand_id);
        $this->db->where("u.email NOT LIKE '%_old@%'", null, false);
        return $this->db->get()->result();
    }

    public function get_member_performance($brand_id) {
        $members = $this->get_members($brand_id);
        $won_stages = ['Won', 'Posted', 'Campaign Completed'];

        foreach ($members as &$member) {
            $user_id = (int)$member->user_id;

            // Total Sourced
            $this->db->where('brand_id', $brand_id);
            $this->db->where('created_by', $user_id);
            $member->total_sourced = $this->db->count_all_results('collaborations');

            // Ambassadors (Won Stages)
            $this->db->where('brand_id', $brand_id);
            $this->db->where('created_by', $user_id);
            $this->db->where_in('pipeline_status', $won_stages);
            $member->ambassadors = $this->db->count_all_results('collaborations');

            // Conversion Rate
            $member->conversion_rate = $member->total_sourced > 0 
                ? round(($member->ambassadors / $member->total_sourced) * 100, 1) 
                : 0;

            // ROI calculation: Sum(GMV) / Sum(Product Cost)
            $this->db->select('SUM(s.gmv) as total_gmv, SUM(c.product_cost) as total_cost');
            $this->db->from('collaborations c');
            // Join influencer_account_stats via account_id
            $this->db->join('influencer_account_stats s', 'c.account_id = s.account_id', 'left');
            $this->db->where('c.brand_id', $brand_id);
            $this->db->where('c.created_by', $user_id);
            $stats = $this->db->get()->row();

            $total_gmv = (float)($stats->total_gmv ?? 0);
            $total_cost = (float)($stats->total_cost ?? 0);

            $member->total_gmv = $total_gmv;
            $member->total_cost = $total_cost;
            // ROI = Revenue / Cost (Multiplier)
            $member->roi = $total_cost > 0 ? round($total_gmv / $total_cost, 2) : 0;
        }

        return $members;
    }

    /**
     * Check if user is member of brand
     */
    public function is_member($brand_id, $user_id) {
        $query = $this->db->get_where('brand_members', [
            'brand_id' => $brand_id,
            'user_id' => $user_id
        ]);
        return $query->num_rows() > 0;
    }

    /**
     * Seat Enforcement: Count non-owner members
     */
    public function get_seats_used($brand_id) {
        $this->db->where('brand_id', $brand_id);
        $this->db->where('role !=', 'owner');
        return $this->db->count_all_results('brand_members');
    }

    /**
     * Invitations
     */
    public function create_invitation($brand_id, $email, $role) {
        return false;
    }

    public function get_invitation($token) {
        return null;
    }

    /**
     * Get all pending brands (for Super Admin)
     */
    public function get_pending_brands() {
        $this->db->select('b.*, u.name as owner_name, u.email as owner_email');
        $this->db->from('brands b');
        $this->db->join('users u', 'b.owner_id = u.id');
        // If we had a status column in brands, we'd use it. For now, return all for management.
        return $this->db->get()->result();
    }

    /**
     * Update brand status (for Super Admin)
     */
    public function update_status($brand_id, $status) {
        $this->db->where('id', $brand_id);
        return $this->db->update('brands', ['status' => $status]);
    }

    public function remove_member($brand_id, $user_id) {
        $this->db->where('brand_id', $brand_id);
        $this->db->where('user_id', $user_id);
        $this->db->where('role !=', 'owner'); // Safety check: cannot remove owner
        return $this->db->delete('brand_members');
    }

    public function update_member_role($brand_id, $user_id, $role) {
        $valid_roles = ['owner', 'admin', 'manager', 'collaborator', 'viewer'];
        if (!in_array($role, $valid_roles)) return false;

        $this->db->where('brand_id', $brand_id);
        $this->db->where('user_id', $user_id);
        return $this->db->update('brand_members', ['role' => $role]);
    }

    public function get_user_by_email($email) {
        return $this->db->get_where('users', ['email' => $email])->row();
    }

    public function invite_member($brand_id, $email, $role) {
        // For simplicity in this implementation, we directly add if user exists
        // In a real system, this would create an invitation record/email
        $user = $this->get_user_by_email($email);
        if (!$user) return false;

        if ($this->is_member($brand_id, $user->id)) return false;

        return $this->db->insert('brand_members', [
            'brand_id' => $brand_id,
            'user_id' => $user->id,
            'role' => $role
        ]);
    }

    public function accept_invitation($token, $user_id) {
        return false;
    }

    /**
     * Update Brand SOP
     * @param int $brand_id
     * @param string $sop_text
     * @return bool
     */
    public function update_sop($brand_id, $sop_text) {
        $this->db->where('id', $brand_id);
        return $this->db->update('brands', ['sop' => $sop_text]);
    }
}
