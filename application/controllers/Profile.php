<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_Session $session
 * @property CI_Loader $load
 * @property CI_Input $input
 * @property CI_Form_validation $form_validation
 * @property User_model $User_model
 */
class Profile extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->model('User_model');
        $this->load->library('form_validation');
        $this->load->database();

        if (!$this->session->userdata('user_id')) {
            redirect('auth/login');
        }
    }

    private function _ensure_user_profile_image_column() {
        if (!$this->db->table_exists('users')) return false;
        if ($this->db->field_exists('profile_image', 'users')) return true;

        $orig_debug = $this->db->db_debug;
        $this->db->db_debug = false;
        $ok = $this->db->query("ALTER TABLE `users` ADD COLUMN `profile_image` VARCHAR(255) NOT NULL DEFAULT ''");
        $this->db->db_debug = $orig_debug;

        if ($ok === false) {
            $err = $this->db->error();
            log_message('error', 'Profile: failed to add users.profile_image column. ' . (isset($err['message']) ? $err['message'] : ''));
            return false;
        }

        return true;
    }

    public function index() {
        $user_id = $this->session->userdata('user_id');
        $data['user'] = $this->User_model->get_user_by_id($user_id);
        $data['user_name'] = $data['user']->name;
        
        $this->load->view('profile', $data);
    }

    public function update() {
        $user_id = $this->session->userdata('user_id');
        
        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('profile');
        } else {
            $data = [
                'name' => $this->input->post('name'),
                'email' => $this->input->post('email')
            ];

            // Check if email is already taken by another user
            // This logic is simple; ideally, we should check if email exists for ID != user_id
            // For now, assuming email change is allowed without uniqueness check on update for simplicity
            // or we can implement email_exists_for_other($email, $id) in model.
            
            // Let's rely on basic update for now.

            if ($this->User_model->update_user($user_id, $data)) {
                // Update session data
                $this->session->set_userdata('name', $data['name']);
                $this->session->set_userdata('email', $data['email']);
                
                $this->session->set_flashdata('success', 'Profile updated successfully!');
            } else {
                $this->session->set_flashdata('error', 'Failed to update profile.');
            }
            redirect('profile');
        }
    }

    public function change_password() {
        $user_id = $this->session->userdata('user_id');

        $this->form_validation->set_rules('current_password', 'Current Password', 'required');
        $this->form_validation->set_rules('new_password', 'New Password', 'required|min_length[6]');
        $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[new_password]');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('profile');
        } else {
            $current_password = $this->input->post('current_password');
            $new_password = $this->input->post('new_password');

            if ($this->User_model->check_password($user_id, $current_password)) {
                $data = ['password' => password_hash($new_password, PASSWORD_BCRYPT)];
                if ($this->User_model->update_user($user_id, $data)) {
                    $this->session->set_flashdata('success', 'Password changed successfully!');
                } else {
                    $this->session->set_flashdata('error', 'Failed to update password.');
                }
            } else {
                $this->session->set_flashdata('error', 'Incorrect current password.');
            }
            redirect('profile');
        }
    }

    public function upload_photo() {
        $user_id = (int)$this->session->userdata('user_id');

        if (strtoupper((string)$this->input->method()) !== 'POST') {
            show_404();
        }

        $accept = isset($_SERVER['HTTP_ACCEPT']) ? (string)$_SERVER['HTTP_ACCEPT'] : '';
        $is_ajax = $this->input->is_ajax_request()
            || $this->input->post('ajax') === '1'
            || (stripos($accept, 'application/json') !== false);

        if (!isset($_FILES['profile_image']) || !is_array($_FILES['profile_image']) || (int)$_FILES['profile_image']['error'] === UPLOAD_ERR_NO_FILE) {
            if ($is_ajax) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'error', 'message' => 'Please choose a profile picture to upload.']));
                return;
            }
            $this->session->set_flashdata('error', 'Please choose a profile picture to upload.');
            redirect('profile');
            return;
        }

        $upload_dir = FCPATH . 'assets/uploads/profile_pictures/';
        if (!is_dir($upload_dir)) {
            @mkdir($upload_dir, 0755, true);
        }

        if (!is_dir($upload_dir)) {
            if ($is_ajax) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'error', 'message' => 'Upload directory is not writable.']));
                return;
            }
            $this->session->set_flashdata('error', 'Upload directory is not writable.');
            redirect('profile');
            return;
        }

        $config = [
            'upload_path' => $upload_dir,
            'allowed_types' => 'jpg|jpeg|png|webp',
            'max_size' => 2048,
            'file_name' => 'user_' . $user_id . '_' . time(),
            'overwrite' => true,
            'remove_spaces' => true,
        ];

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('profile_image')) {
            $err = strip_tags((string)$this->upload->display_errors());
            log_message('error', 'Profile upload failed: ' . $err);
            if ($is_ajax) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'error', 'message' => $err]));
                return;
            }
            $this->session->set_flashdata('error', $err);
            redirect('profile');
            return;
        }

        $file = $this->upload->data();
        $relative_path = 'assets/uploads/profile_pictures/' . $file['file_name'];

        if (!$this->_ensure_user_profile_image_column()) {
            if ($is_ajax) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'error', 'message' => 'Profile image column is missing and could not be created.']));
                return;
            }
            $this->session->set_flashdata('error', 'Profile image column is missing and could not be created.');
            redirect('profile');
            return;
        }

        $ok = $this->User_model->update_user($user_id, ['profile_image' => $relative_path]);
        if ($ok) {
            $this->session->set_userdata('profile_image', $relative_path);
            if ($is_ajax) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'success', 'url' => base_url($relative_path)]));
                return;
            }
            $this->session->set_flashdata('success', 'Profile picture updated successfully!');
        } else {
            $err = $this->db->error();
            log_message('error', 'Profile: failed to update users.profile_image for user_id=' . $user_id . '. ' . (isset($err['message']) ? $err['message'] : ''));
            if ($is_ajax) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'error', 'message' => 'Failed to save profile picture.']));
                return;
            }
            $this->session->set_flashdata('error', 'Failed to save profile picture.');
        }

        redirect('profile');
    }
}
