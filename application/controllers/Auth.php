<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property User_model $User_model
 * @property Activity_log_model $Activity_log_model
 * @property CI_Form_validation $form_validation
 * @property CI_Session $session
 * @property CI_Loader $load
 * @property CI_Input $input
 */
class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->model('Activity_log_model'); // Load Activity Logger
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->helper('form'); // Load form helper for CSRF and form functions!
    }

    public function index() {
        redirect('auth/login');
    }

    public function login() {
        if ($this->session->userdata('user_id')) {
            $role = $this->session->userdata('role');
            if ($role === 'admin') {
                redirect('admin');
            } elseif ($role === 'manager') {
                redirect('manager');
            } else {
                redirect('influencer');
            }
        }

        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('auth/login');
        } else {
            $email = $this->input->post('email');
            $password = $this->input->post('password');

            $user = $this->User_model->login($email, $password);

            if ($user === 'inactive') {
                $this->session->set_flashdata('error', 'Your account is inactive. Please contact administrator.');
                redirect('auth/login');
            } elseif ($user) {
                $this->load->model('Brand_model');
                $brand_id = $this->Brand_model->ensure_default_brand($user->id);
                $this->Brand_model->ensure_membership($brand_id, $user->id, isset($user->role) ? $user->role : 'employee');

                $session_data = array(
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => isset($user->role) ? $user->role : 'employee',
                    'platform_role' => isset($user->platform_role) ? $user->platform_role : 'user',
                    'brand_id' => $brand_id,
                    'logged_in' => TRUE
                );
                $this->session->set_userdata($session_data);
                
                // Log Activity
                $this->Activity_log_model->log_activity(
                    'Login',
                    'User logged in successfully.',
                    $user->id,
                    $user->name
                );

                // Redirect based on role
                if (isset($user->role) && $user->role === 'admin') {
                    redirect('admin');
                } elseif (isset($user->role) && $user->role === 'manager') {
                    redirect('manager');
                } else {
                    redirect('influencer');
                }
            } else {
                $this->session->set_flashdata('error', 'Invalid email or password');
                redirect('auth/login');
            }
        }
    }

    public function register() {
        if ($this->session->userdata('user_id')) {
            redirect('influencer');
        }

        $this->form_validation->set_rules('name', 'Name', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[users.email]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]');
        $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[password]');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('auth/register');
        } else {
            $data = array(
                'name' => $this->input->post('name'),
                'email' => $this->input->post('email'),
                'password' => password_hash($this->input->post('password'), PASSWORD_BCRYPT),
                'role' => 'employee',
                'platform_role' => 'user',
                'status' => 'active'
            );

            if ($this->User_model->register($data)) {
                $user = $this->User_model->login($data['email'], $this->input->post('password'));
                if ($user) {
                    $this->load->model('Brand_model');
                    $brand_id = $this->Brand_model->ensure_default_brand($user->id);
                    $this->Brand_model->ensure_membership($brand_id, $user->id, 'employee');

                    $session_data = array(
                        'user_id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => 'employee',
                        'platform_role' => 'user',
                        'brand_id' => $brand_id,
                        'logged_in' => TRUE
                    );
                    $this->session->set_userdata($session_data);
                    
                    redirect('influencer');
                }
                redirect('auth/login');
            } else {
                $this->session->set_flashdata('error', 'Something went wrong. Please try again.');
                redirect('auth/register');
            }
        }
    }

    public function onboarding() {
        redirect('influencer');
    }

    public function choose_plan() {
        redirect('influencer');
    }

    public function accept_invite($token) {
        show_404();
    }

    public function switch_brand($brand_id) {
        if (!$this->session->userdata('user_id')) {
            redirect('auth/login');
        }

        $this->load->model('Brand_model');
        $brand_id = (int)$brand_id;
        $brand = $this->Brand_model->get_brand($brand_id);
        if (!$brand) {
            show_404();
        }

        $this->session->set_userdata('brand_id', $brand_id);
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_userdata('logged_in', TRUE);
        }

        $redirect_to = (string)$this->input->get('redirect');
        if ($redirect_to !== '') {
            $redirect_parts = parse_url($redirect_to);
            $has_scheme = isset($redirect_parts['scheme']) && $redirect_parts['scheme'] !== '';

            if (!$has_scheme) {
                $redirect_to = ltrim($redirect_to, '/');
                redirect($redirect_to);
            }

            $ref_host = isset($redirect_parts['host']) ? strtolower((string)$redirect_parts['host']) : '';
            $ref_port = isset($redirect_parts['port']) ? (int)$redirect_parts['port'] : 0;

            $base_parts = parse_url(base_url());
            $base_host = isset($base_parts['host']) ? strtolower((string)$base_parts['host']) : '';
            $base_port = isset($base_parts['port']) ? (int)$base_parts['port'] : 0;

            $same_host = $ref_host !== '' && $base_host !== '' && $ref_host === $base_host;
            $local_hosts = ['localhost', '127.0.0.1', '::1'];
            $both_local = in_array($ref_host, $local_hosts, true) && in_array($base_host, $local_hosts, true);
            $same_port = $ref_port === $base_port || ($ref_port === 0 && $base_port === 0);

            if (($same_host || $both_local) && $same_port) {
                redirect($redirect_to);
            }
        }

        $ref = $this->input->server('HTTP_REFERER');
        if ($ref) {
            $ref_parts = parse_url($ref);
            $base_parts = parse_url(base_url());
            $ref_host = isset($ref_parts['host']) ? strtolower((string)$ref_parts['host']) : '';
            $base_host = isset($base_parts['host']) ? strtolower((string)$base_parts['host']) : '';
            $ref_port = isset($ref_parts['port']) ? (int)$ref_parts['port'] : 0;
            $base_port = isset($base_parts['port']) ? (int)$base_parts['port'] : 0;

            $same_host = $ref_host !== '' && $base_host !== '' && $ref_host === $base_host;
            $local_hosts = ['localhost', '127.0.0.1', '::1'];
            $both_local = in_array($ref_host, $local_hosts, true) && in_array($base_host, $local_hosts, true);
            $same_port = $ref_port === $base_port || ($ref_port === 0 && $base_port === 0);

            if (($same_host || $both_local) && $same_port) {
                redirect($ref);
            }
        }

        $role = (string)$this->session->userdata('role');
        if ($role === 'admin') {
            redirect('admin');
        } elseif ($role === 'manager') {
            redirect('manager');
        }
        redirect('influencer/dashboard');
    }

    public function switch_brand_ajax() {
        if (!$this->session->userdata('user_id')) {
            return $this->output
                ->set_status_header(401)
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Not authenticated']));
        }

        $this->load->model('Brand_model');

        $brand_id = (int)$this->input->post('brand_id');
        if ($brand_id <= 0) {
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid brand']));
        }

        $brand = $this->Brand_model->get_brand($brand_id);
        if (!$brand) {
            return $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Brand not found']));
        }

        $this->session->set_userdata('brand_id', $brand_id);
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_userdata('logged_in', TRUE);
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'success', 'brand_id' => $brand_id]));
    }

    public function logout() {
        if ($this->session->userdata('user_id')) {
            $this->Activity_log_model->log_activity(
                'Logout',
                'User logged out.',
                $this->session->userdata('user_id'),
                $this->session->userdata('name')
            );
        }
        $this->session->sess_destroy();
        redirect('auth/login');
    }

    // Forgot Password Methods via PHPMailer
    public function forgot_password() {
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('auth/forgot_password');
        } else {
            $email = $this->input->post('email');
            $user = $this->db->where('email', $email)->get('users')->row();

            if ($user) {
                $token = $this->User_model->set_password_reset_token($email);

                if ($token) {
                    $reset_link = base_url('auth/reset_password/' . $token);
                    
                    // Send Email via PHPMailer / Gmail SMTP
                    $this->load->library('phpmailer_lib');
                    $sent = $this->phpmailer_lib->send_password_reset($email, $user->name, $reset_link);

                    $msg = 'A password reset email has been sent to <strong>' . htmlspecialchars($email) . '</strong> via PHPMailer. Please check your Gmail inbox to reset your password.';
                    $this->session->set_flashdata('success', $msg);
                } else {
                    $this->session->set_flashdata('error', 'Unable to generate reset token. Please try again.');
                }
            } else {
                $this->session->set_flashdata('error', 'No registered account found with that email address.');
            }

            redirect('auth/forgot_password');
        }
    }

    public function reset_password($token) {
        $user = $this->User_model->validate_reset_token($token);

        if (!$user) {
            $this->session->set_flashdata('error', 'Invalid or expired reset token');
            redirect('auth/forgot_password');
        }

        $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]');
        $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[password]');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('auth/reset_password', ['token' => $token]);
        } else {
            $password = $this->input->post('password');
            if ($this->User_model->reset_password($token, $password)) {
                $this->session->set_flashdata('success', 'Password reset successfully! You can now login');
                redirect('auth/login');
            } else {
                $this->session->set_flashdata('error', 'Something went wrong. Please try again');
                redirect('auth/reset_password/' . $token);
            }
        }
    }

    public function google_login() {
        $credential = $this->input->post('credential');
        if (empty($credential)) {
            $this->session->set_flashdata('error', 'Google Authentication failed. Credential not supplied.');
            redirect('auth/login');
        }

        $parts = explode('.', $credential);
        if (count($parts) < 3) {
            $this->session->set_flashdata('error', 'Google Authentication failed. Invalid token format.');
            redirect('auth/login');
        }

        $payload_raw = base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1]));
        $payload = json_decode($payload_raw, true);

        if (empty($payload['email'])) {
            $this->session->set_flashdata('error', 'Google Authentication failed. Email not returned by Google.');
            redirect('auth/login');
        }

        $email = strtolower(trim($payload['email']));
        $name = !empty($payload['name']) ? $payload['name'] : 'Google User';

        $this->db->where('email', $email);
        $user = $this->db->get('users')->row();

        if ($user) {
            if (isset($user->status) && $user->status !== 'active') {
                $this->session->set_flashdata('error', 'Your account is inactive. Please contact administrator.');
                redirect('auth/login');
            }
        } else {
            // Register new user automatically
            $new_user_data = [
                'name' => $name,
                'email' => $email,
                'password' => password_hash(bin2hex(random_bytes(16)), PASSWORD_BCRYPT),
                'role' => 'employee',
                'platform_role' => 'user',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s')
            ];
            $this->User_model->register($new_user_data);
            
            $this->db->where('email', $email);
            $user = $this->db->get('users')->row();
        }

        if ($user) {
            $this->load->model('Brand_model');
            $brand_id = $this->Brand_model->ensure_default_brand($user->id);
            $this->Brand_model->ensure_membership($brand_id, $user->id, isset($user->role) ? $user->role : 'employee');

            $session_data = array(
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => isset($user->role) ? $user->role : 'employee',
                'platform_role' => isset($user->platform_role) ? $user->platform_role : 'user',
                'brand_id' => $brand_id,
                'logged_in' => TRUE
            );
            $this->session->set_userdata($session_data);
            
            // Log Activity
            $this->Activity_log_model->log_activity(
                'Login via Google',
                'User logged in successfully via Google Sign-In.',
                $user->id,
                $user->name
            );

            // Redirect based on role
            if (isset($user->role) && $user->role === 'admin') {
                redirect('admin');
            } elseif (isset($user->role) && $user->role === 'manager') {
                redirect('manager');
            } else {
                redirect('influencer');
            }
        } else {
            $this->session->set_flashdata('error', 'Failed to authenticate user.');
            redirect('auth/login');
        }
    }
}
