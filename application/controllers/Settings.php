<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_Session $session
 * @property CI_Loader $load
 * @property CI_Input $input
 * @property CI_Form_validation $form_validation
 * @property User_model $User_model
 * @property Activity_log_model $Activity_log_model
 */
class Settings extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->model('User_model');
        $this->load->model('Activity_log_model');
        $this->load->library('form_validation');
        $this->load->database();
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }
    }

    public function index() {
        $user_id = (int)$this->session->userdata('user_id');
        $user    = $this->User_model->get_user_by_id($user_id);

        $data['user_name']         = $this->session->userdata('name');
        $data['user_email']        = $this->session->userdata('email');
        $data['user_role']         = $this->session->userdata('role');
        $data['user']              = $user;
        $data['user_profile_image']= $this->session->userdata('profile_image');

        // Flash messages
        $data['success'] = $this->session->flashdata('success');
        $data['error']   = $this->session->flashdata('error');

        $this->load->view('settings', $data);
    }

    // ── Update account name / email ──────────────────────────────────────────
    public function update_account() {
        $user_id = (int)$this->session->userdata('user_id');

        $this->form_validation->set_rules('name',  'Name',  'required|trim|max_length[100]');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim|max_length[150]');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', strip_tags(validation_errors()));
            redirect('settings');
        }

        $data = [
            'name'  => $this->input->post('name'),
            'email' => $this->input->post('email'),
        ];

        if ($this->User_model->update_user($user_id, $data)) {
            $this->session->set_userdata('name',  $data['name']);
            $this->session->set_userdata('email', $data['email']);
            $this->Activity_log_model->log_activity(
                'Update Account Settings',
                'User updated their account name/email.',
                $user_id,
                $data['name']
            );
            $this->session->set_flashdata('success', 'Account updated successfully.');
        } else {
            $this->session->set_flashdata('error', 'Failed to update account.');
        }
        redirect('settings');
    }

    // ── Change password ──────────────────────────────────────────────────────
    public function change_password() {
        $user_id = (int)$this->session->userdata('user_id');

        $this->form_validation->set_rules('current_password', 'Current Password', 'required');
        $this->form_validation->set_rules('new_password',     'New Password',     'required|min_length[8]');
        $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[new_password]');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', strip_tags(validation_errors()));
            redirect('settings');
        }

        $current = $this->input->post('current_password');
        $new     = $this->input->post('new_password');

        if ($this->User_model->check_password($user_id, $current)) {
            $ok = $this->User_model->update_user($user_id, [
                'password' => password_hash($new, PASSWORD_BCRYPT)
            ]);
            if ($ok) {
                $this->Activity_log_model->log_activity(
                    'Change Password',
                    'User changed their password.',
                    $user_id,
                    $this->session->userdata('name')
                );
                $this->session->set_flashdata('success', 'Password changed successfully.');
            } else {
                $this->session->set_flashdata('error', 'Failed to update password.');
            }
        } else {
            $this->session->set_flashdata('error', 'Current password is incorrect.');
        }
        redirect('settings');
    }

    // ── Save preferences (AJAX) ──────────────────────────────────────────────
    // Persists notification & appearance prefs in the session so they survive
    // page reloads. No DB schema changes needed.
    public function save_preferences() {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $allowed = ['email_notifications', 'browser_alerts', 'dark_mode', 'compact_view'];
        $prefs   = $this->session->userdata('user_prefs') ?: [];

        foreach ($allowed as $key) {
            $val = $this->input->post($key);
            if ($val !== null) {
                $prefs[$key] = ($val === '1' || $val === 'true' || $val === true);
            }
        }
        $this->session->set_userdata('user_prefs', $prefs);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'ok', 'prefs' => $prefs]));
    }
}
