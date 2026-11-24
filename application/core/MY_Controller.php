<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Base Controller
 * @property CI_Session $session
 * @property CI_Loader $load
 * @property CI_Input $input
 * @property CI_Output $output
 */
class MY_Controller extends CI_Controller {
    public function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Manila');
        if (isset($this->db)) {
            $this->db->query("SET time_zone = '+08:00'");
        }
    }
}

/**
 * Base Controller for Company-wide operations (Armful Media)
 */
class Brand_Controller extends CI_Controller {

    protected $brand_id;
    protected $brand_name;
    protected $user_id;
    protected $user_role;
    protected $subscription;
    protected $user_profile_image;

    public function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Manila');
        if (isset($this->db)) {
            $this->db->query("SET time_zone = '+08:00'");
        }
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->model('User_model');
        $this->load->model('Brand_model');

        if (is_cli()) {
            if (!$this->session->userdata('user_id')) {
                $this->session->set_userdata([
                    'user_id' => 1,
                    'role' => 'employee',
                    'name' => 'CLI Test User',
                    'brand_id' => 1,
                    'logged_in' => TRUE
                ]);
            }
        }

        if (!is_cli() && !$this->session->userdata('user_id')) {
            redirect('auth/login');
        }

        $this->user_id = $this->session->userdata('user_id');
        $this->user_role = $this->session->userdata('role');

        $profile_image = (string)$this->session->userdata('profile_image');
        if ($profile_image === '') {
            $u = $this->User_model->get_user_by_id($this->user_id);
            if ($u && isset($u->profile_image) && (string)$u->profile_image !== '') {
                $profile_image = (string)$u->profile_image;
                $this->session->set_userdata('profile_image', $profile_image);
            }
        }
        $this->user_profile_image = $profile_image;
        $this->load->vars(['user_profile_image' => $profile_image]);

        // Allow Brand Switching while under the "Armful Media" umbrella
        $brand_id = $this->session->userdata('brand_id');
        $brand = $brand_id ? $this->Brand_model->get_brand($brand_id) : null;
        if (!$brand || $brand->name === 'Armful Media') {
            $brand_id = $this->Brand_model->ensure_default_brand($this->user_id);
            $this->session->set_userdata('brand_id', $brand_id);
            $brand = $this->Brand_model->get_brand($brand_id);
        }

        $this->brand_id = (int)$brand_id;
        $this->Brand_model->ensure_membership($this->brand_id, $this->user_id, $this->user_role);

        $this->brand_name = $brand ? $brand->name : 'JBL';
        $this->subscription = $this->Brand_model->get_subscription($this->brand_id);
    }

    /**
     * Check if user has permission for an action (One Company Mode)
     */
    protected function check_permission($permission) {
        $role = $this->user_role;

        $matrix = [
            'invite_members' => ['admin', 'manager'],
            'manage_influencers' => ['admin', 'manager', 'employee'],
            'view_pipeline' => ['admin', 'manager', 'employee', 'viewer'],
            'brand_settings' => ['admin', 'manager'],
            'manage_subscription' => ['admin']
        ];

        if (isset($matrix[$permission])) {
            return in_array($role, $matrix[$permission]);
        }

        return false;
    }

    /**
     * Plan limits are disabled in One Company Mode or set to unlimited
     */
    protected function check_limit($type, $current_count) {
        return true; // Always unlimited for Armful Media
    }
}

/**
 * Auth Controller - Ensures user is logged in
 */
class Auth_Controller extends MY_Controller {
    
    protected $user_id;
    protected $user_role;
    protected $user_name;

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');

        if (is_cli()) {
            if (!$this->session->userdata('user_id')) {
                $this->session->set_userdata([
                    'user_id' => 1,
                    'role' => 'employee',
                    'name' => 'CLI Test User',
                    'brand_id' => 1,
                    'logged_in' => TRUE
                ]);
            }
        }

        // Check if user is logged in
        if (!is_cli() && !$this->session->userdata('logged_in')) {
            // Save current URL to redirect back after login
            $this->session->set_userdata('redirect_url', current_url());
            redirect('auth/login');
        }

        $this->user_id = $this->session->userdata('user_id');
        $this->user_role = $this->session->userdata('role');
        $this->user_name = $this->session->userdata('name');
    }
}

/**
 * Admin Controller - Ensures user is Admin
 */
class Admin_Controller extends Auth_Controller {
    public function __construct() {
        parent::__construct();
        if ($this->user_role !== 'admin') {
            $this->session->set_flashdata('error', 'Access denied. Administrators only.');
            redirect('influencer');
        }
    }
}

/**
 * Manager Controller - Ensures user is Manager or Admin
 */
class Manager_Controller extends Auth_Controller {
    public function __construct() {
        parent::__construct();
        if (!in_array(strtolower($this->user_role), ['admin', 'manager'])) {
            $this->session->set_flashdata('error', 'You do not have permission to access the Manager Panel.');
            redirect('influencer');
        }
    }
}

/**
 * Employee Controller - Ensures user is Employee, Manager, or Admin
 */
class Employee_Controller extends Auth_Controller {
    public function __construct() {
        parent::__construct();
        if (!in_array(strtolower($this->user_role), ['admin', 'manager', 'employee'])) {
            $this->session->set_flashdata('error', 'Access denied.');
            redirect('auth/login');
        }
    }
}
