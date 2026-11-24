<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->library('session');
        $this->load->model('User_model');
        $this->load->model('Activity_log_model');
        
        if ($this->session->userdata('role') !== 'admin') {
            $this->session->set_flashdata('error', 'Access denied. Administrators only.');
            redirect('influencer');
        }

        $user_id = (int)$this->session->userdata('user_id');
        if ($user_id) {
            $u = $this->User_model->get_user_by_id($user_id);
            if ($u && isset($u->profile_image) && (string)$u->profile_image !== '') {
                $this->session->set_userdata('profile_image', (string)$u->profile_image);
            }
            $this->load->vars(['user_profile_image' => $this->session->userdata('profile_image')]);
        }
    }

    public function index()
    {
        redirect('admin/dashboard');
    }

    public function dashboard()
    {
        $data['active_section'] = 'dashboard';
        $data['total_users'] = $this->User_model->count_all_users();
        $data['active_users'] = $this->db->where('status', 'active')->count_all_results('users'); 
        $data['employee_count'] = $this->db->where('role', 'employee')->count_all_results('users');
        $data['manager_count'] = $this->db->where('role', 'manager')->count_all_results('users');
        $data['admin_count'] = $this->db->where('role', 'admin')->count_all_results('users');
        $data['system_health'] = '100% Operational';
        
        $data['total_influencers'] = $this->db->table_exists('collaborations') ? $this->db->count_all_results('collaborations') : 0;
        $data['total_campaigns'] = $this->db->table_exists('campaigns') ? $this->db->count_all_results('campaigns') : 0;
        $data['total_drips'] = $this->db->table_exists('email_drips') ? $this->db->count_all_results('email_drips') : 0;

        // Fetch real recent logs formatted for dashboard
        $raw_logs = $this->Activity_log_model->get_recent_logs(8);
        $formatted_logs = [];
        foreach($raw_logs as $log) {
            $formatted_logs[] = [
                'user' => isset($log['user_name']) ? $log['user_name'] : 'System',
                'action' => isset($log['action']) ? $log['action'] : 'Activity',
                'details' => isset($log['details']) ? $log['details'] : '',
                'time' => isset($log['created_at']) ? $this->time_elapsed_string($log['created_at']) : 'Recently'
            ];
        }
        $data['recent_logs'] = $formatted_logs;
        
        $this->load->view('admin/dashboard', $data);
    }

    public function users()
    {
        $data['active_section'] = 'user_management';
        $data['users'] = $this->User_model->get_all_users();
        $this->load->view('admin/dashboard', $data);
    }

    public function user_management()
    {
        $this->users();
    }

    public function save_user()
    {
        $this->load->library('form_validation');
        $user_id = $this->input->post('user_id');

        // Validate inputs
        $this->form_validation->set_rules('name', 'Name', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('role', 'Role', 'required');
        $this->form_validation->set_rules('status', 'Status', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => validation_errors()]));
            return;
        }

        $name = $this->input->post('name');
        $email = $this->input->post('email');
        $role = $this->input->post('role');
        $status = $this->input->post('status');
        $password = $this->input->post('password');

        if ($user_id) {
            // Edit user
            // Check if email is unique (excluding current user)
            $this->db->where('email', $email);
            $this->db->where('id !=', $user_id);
            $duplicate = $this->db->get('users')->row();

            if ($duplicate) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'error', 'message' => 'Email already exists!']));
                return;
            }

            $data = [
                'name' => $name,
                'email' => $email,
                'role' => $role,
                'status' => $status
            ];

            if ($password) {
                $data['password'] = password_hash($password, PASSWORD_BCRYPT);
            }

            if ($this->User_model->update_user($user_id, $data)) {
                $this->Activity_log_model->log_activity('Update User', "Updated user ID: $user_id ($name)");
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'success', 'message' => 'User updated successfully!']));
            } else {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'error', 'message' => 'Failed to update user.']));
            }
        } else {
            // Add user
            if ($this->User_model->email_exists($email)) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'error', 'message' => 'Email already exists!']));
                return;
            }

            if (!$password) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'error', 'message' => 'Password is required for new users.']));
                return;
            }

            $data = [
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_BCRYPT),
                'role' => $role,
                'status' => $status,
                'platform_role' => 'user'
            ];

            if ($this->User_model->register($data)) {
                $row = $this->db->query('SELECT LAST_INSERT_ID() AS id')->row();
                $new_user_id = $row ? (int)$row->id : 0;
                $this->load->model('Brand_model');
                $brand_id = $this->Brand_model->ensure_default_brand($this->session->userdata('user_id'));
                if ($new_user_id) {
                    $this->Brand_model->ensure_membership($brand_id, $new_user_id, $role);
                }
                $this->Activity_log_model->log_activity('Add User', "Added new user: $name ($role)");
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'success', 'message' => 'User added successfully!']));
            } else {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'error', 'message' => 'Failed to add user.']));
            }
        }
    }

    public function add_user()
    {
        $name = $this->input->post('name');
        $email = $this->input->post('email');
        $password = password_hash($this->input->post('password'), PASSWORD_BCRYPT);
        $role = $this->input->post('role');
        
        $data = [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => $role,
            'platform_role' => 'user',
            'status' => 'active'
        ];

        if ($this->User_model->email_exists($email)) {
            $this->session->set_flashdata('error', 'Email already exists!');
        } else {
            if ($this->User_model->register($data)) {
                $row = $this->db->query('SELECT LAST_INSERT_ID() AS id')->row();
                $user_id = $row ? (int)$row->id : 0;
                $this->load->model('Brand_model');
                $brand_id = $this->Brand_model->ensure_default_brand($this->session->userdata('user_id'));
                if ($user_id) {
                    $this->Brand_model->ensure_membership($brand_id, $user_id, $role);
                }
                $this->Activity_log_model->log_activity('Add User', "Added new user: $name ($role)");
                $this->session->set_flashdata('success', 'User added successfully!');
            } else {
                $this->session->set_flashdata('error', 'Failed to add user.');
            }
        }
        redirect('admin/user_management');
    }

    public function edit_user()
    {
        $id = $this->input->post('id');
        $name = $this->input->post('name');
        $email = $this->input->post('email');
        $role = $this->input->post('role');
        $status = $this->input->post('status');

        $data = [
            'name' => $name,
            'email' => $email,
            'role' => $role,
            'status' => $status
        ];

        // Only update password if provided
        if ($this->input->post('password')) {
            $data['password'] = password_hash($this->input->post('password'), PASSWORD_BCRYPT);
        }

        $result = $this->User_model->update_user($id, $data);
        if ($result && $this->db->affected_rows() > 0) {
            $this->Activity_log_model->log_activity('Update User', "Updated user ID: $id ($name)");
            $this->session->set_flashdata('success', 'User updated successfully!');
        } elseif ($result) {
            $this->session->set_flashdata('success', 'No changes to update.');
        } else {
            $this->session->set_flashdata('error', 'Failed to update user.');
        }
        redirect('admin/user_management');
    }

    public function delete_user($id)
    {
        $isAjax = $this->input->is_ajax_request() || 
                  (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) ||
                  $this->input->get('ajax') == 1;

        $result = $this->User_model->delete_user($id);
        if ($result && $this->db->affected_rows() > 0) {
            $this->Activity_log_model->log_activity('Delete User', "Deleted user ID: $id");
            if ($isAjax) {
                return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'success', 'message' => 'User deleted successfully!']));
            }
            $this->session->set_flashdata('success', 'User deleted successfully!');
        } else {
            if ($isAjax) {
                return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'Failed to delete user.']));
            }
            $this->session->set_flashdata('error', 'Failed to delete user.');
        }
        redirect('admin/user_management');
    }

    public function toggle_status($id, $status)
    {
        $new_status = ($status == 'active') ? 'inactive' : 'active';
        $result = $this->User_model->update_user($id, ['status' => $new_status]);
        if ($result && $this->db->affected_rows() > 0) {
            $this->Activity_log_model->log_activity('Update Status', "Changed status of user ID: $id to $new_status");
            $this->session->set_flashdata('success', 'User status updated!');
        } elseif ($result) {
            $this->session->set_flashdata('success', 'No changes to status.');
        } else {
            $this->session->set_flashdata('error', 'Failed to update status.');
        }
        redirect('admin/user_management');
    }
    
    // Helper function for time elapsed
    private function time_elapsed_string($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        // Calculate weeks manually instead of assigning dynamic property
        $weeks = floor($diff->d / 7);
        $days = $diff->d - ($weeks * 7);

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        
        foreach ($string as $k => &$v) {
            // Handle weeks separately since it's not a standard DateInterval property
            if ($k === 'w') {
                if ($weeks) {
                    $v = $weeks . ' ' . $v . ($weeks > 1 ? 's' : '');
                } else {
                    unset($string[$k]);
                }
            } elseif ($k === 'd') {
                if ($days) {
                    $v = $days . ' ' . $v . ($days > 1 ? 's' : '');
                } else {
                    unset($string[$k]);
                }
            } else {
                if ($diff->$k) {
                    $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
                } else {
                    unset($string[$k]);
                }
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    public function system_settings()
    {
        $data['active_section'] = 'system_settings';
        $data['settings'] = [
            'site_name' => 'Instroom System',
            'maintenance_mode' => false,
            'allow_registration' => true,
            'default_currency' => 'USD',
        ];
        $this->load->view('admin/dashboard', $data);
    }

    public function audit_logs()
    {
        $data['active_section'] = 'audit_logs';
        $data['logs'] = $this->Activity_log_model->get_logs(100);
        $this->load->view('admin/dashboard', $data);
    }

    public function activity_logs()
    {
        $this->audit_logs();
    }

    public function backups()
    {
        $data['active_section'] = 'backups';
        $data['backups'] = [
            (object)['id' => 1, 'filename' => 'db_backup_2023-10-25.sql.gz', 'size' => '2.5 MB', 'created_at' => '2023-10-25 00:00:00'],
            (object)['id' => 2, 'filename' => 'db_backup_2023-10-24.sql.gz', 'size' => '2.4 MB', 'created_at' => '2023-10-24 00:00:00'],
            (object)['id' => 3, 'filename' => 'db_backup_2023-10-23.sql.gz', 'size' => '2.4 MB', 'created_at' => '2023-10-23 00:00:00'],
        ];
        $this->load->view('admin/dashboard', $data);
    }

    public function announcements()
    {
        $data['active_section'] = 'announcements';
        $data['announcements'] = [
            (object)['id' => 1, 'title' => 'Scheduled Maintenance', 'message' => 'System will be down for 1 hour tonight.', 'audience' => 'All Users', 'status' => 'Active', 'created_at' => '2023-10-25 10:00:00'],
            (object)['id' => 2, 'title' => 'New Policy Update', 'message' => 'Please review the updated influencer guidelines.', 'audience' => 'Employees', 'status' => 'Active', 'created_at' => '2023-10-24 14:00:00'],
        ];
        $this->load->view('admin/dashboard', $data);
    }
}
