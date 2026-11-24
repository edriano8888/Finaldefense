<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_DB_query_builder $db
 * @property CI_Loader $load
 */
class User_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->_ensure_reset_columns();
        $this->_create_default_admin();
    }

    private function _create_default_admin() {
        $this->db->where('email', 'admin@armfulmedia.com');
        $existing = $this->db->get('users')->row();
        
        if (!$existing) {
            $this->db->insert('users', [
                'name' => 'Admin User',
                'email' => 'admin@armfulmedia.com',
                'password' => password_hash('admin123', PASSWORD_BCRYPT),
                'role' => 'admin',
                'platform_role' => 'super_admin',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    private function _ensure_reset_columns() {
        if (!$this->db->field_exists('reset_token', 'users')) {
            $this->db->query("ALTER TABLE `users` ADD COLUMN `reset_token` VARCHAR(255) NULL DEFAULT NULL");
        }
        if (!$this->db->field_exists('reset_token_expires', 'users')) {
            $this->db->query("ALTER TABLE `users` ADD COLUMN `reset_token_expires` DATETIME NULL DEFAULT NULL");
        }
    }

    public function register($data) {
        return $this->db->insert('users', $data);
    }

    public function login($email, $password) {
        $this->db->where('email', $email);
        $query = $this->db->get('users');

        if ($query->num_rows() == 1) {
            $user = $query->row();
            
            // Check if account is active
            if (isset($user->status) && $user->status !== 'active') {
                return 'inactive';
            }

            if (password_verify($password, $user->password)) {
                // Update last login
                $this->db->where('id', $user->id);
                $this->db->update('users', ['last_login' => date('Y-m-d H:i:s')]);
                
                // Get User's Brands
                $user->brands = $this->get_user_brands($user->id);
                return $user;
            }
        }
        return false;
    }

    /**
     * Get all brands associated with a user
     */
    public function get_user_brands($user_id) {
        $this->db->select('b.*, bm.role');
        $this->db->from('brands b');
        $this->db->join('brand_members bm', 'b.id = bm.brand_id');
        $this->db->where('bm.user_id', $user_id);
        return $this->db->get()->result();
    }

    /**
     * Check if user is Super Admin
     */
    public function is_super_admin($user_id) {
        $user = $this->get_user_by_id($user_id);
        return ($user && $user->platform_role === 'super_admin');
    }
    
    public function email_exists($email) {
        $this->db->where('email', $email);
        $query = $this->db->get('users');
        return $query->num_rows() > 0;
    }

    /**
     * Get user by ID
     * @param int $id
     * @return stdClass|null
     */
    public function get_user_by_id($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('users');
        return $query->row();
    }

    public function update_user($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('users', $data);
    }

    public function check_password($id, $password) {
        $this->db->where('id', $id);
        $query = $this->db->get('users');
        if ($query->num_rows() == 1) {
            $user = $query->row();
            return password_verify($password, $user->password);
        }
        return false;
    }

    public function get_all_users($search = null) {
        if ($search) {
            $this->db->group_start();
            $this->db->like('name', $search);
            $this->db->or_like('email', $search);
            $this->db->group_end();
        }
        return $this->db->get('users')->result();
    }

    public function delete_user($id) {
        $this->db->where('id', $id);
        return $this->db->delete('users');
    }

    public function count_all_users() {
        return $this->db->count_all('users');
    }

    // Password Reset Methods
    public function set_password_reset_token($email) {
        $this->db->where('email', $email);
        $user = $this->db->get('users')->row();
        
        if (!$user) {
            return false;
        }

        $token = bin2hex(random_bytes(32));
        $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $this->db->where('id', $user->id);
        $this->db->update('users', [
            'reset_token' => $token,
            'reset_token_expires' => $expires_at
        ]);

        return $token;
    }

    public function validate_reset_token($token) {
        $this->db->where('reset_token', $token);
        $this->db->where('reset_token_expires >', date('Y-m-d H:i:s'));
        $query = $this->db->get('users');
        return $query->row();
    }

    public function reset_password($token, $password) {
        $user = $this->validate_reset_token($token);
        if (!$user) {
            return false;
        }

        $this->db->where('id', $user->id);
        $this->db->update('users', [
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'reset_token' => null,
            'reset_token_expires' => null
        ]);

        return true;
    }
}
