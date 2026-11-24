<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Unified Admin User Management View
 * Renders the unified Admin Panel layout with the modern User Management workspace.
 */
$data = get_defined_vars();
$data['active_section'] = 'user_management';
if (!isset($data['users'])) {
    $CI =& get_instance();
    if (!isset($CI->User_model)) {
        $CI->load->model('User_model');
    }
    $data['users'] = $CI->User_model->get_all_users();
}
$this->load->view('admin/dashboard', $data);
