<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property Influencer_model $Influencer_model
 * @property CI_Session $session
 * @property CI_Input $input
 * @property CI_Output $output
 * @property CI_DB_query_builder $db
 */
class Api extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Influencer_model');
        $this->load->database();
        $this->load->library('session');
        
        // Allow CORS for local development (adjust origin in production)
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type");
        header("Access-Control-Allow-Private-Network: true");
        
        if ($this->input->method() === 'options') {
            exit(0);
        }
    }

    public function add_influencer() {
        // Read JSON input
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!$data) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid JSON input']));
            return;
        }

        // Basic Validation
        if (empty($data['username']) || empty($data['platform'])) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Username and Platform are required']));
            return;
        }

        // Hardcode user_id for extension sample (In real app, use API Token or Session)
        // For this local sample, we'll assume the main user (Admin) is adding it, or pass user_id if available.
        // We'll try to use session if cookies are shared, otherwise default to 1 (Admin)
        $user_id = $this->session->userdata('user_id') ? $this->session->userdata('user_id') : 1;

        $insert_data = [
            'platform' => strtolower($data['platform']),
            'username' => $data['username'],
            'full_name' => isset($data['full_name']) ? $data['full_name'] : $data['username'],
            'profile_image' => isset($data['profile_image']) ? $data['profile_image'] : "https://ui-avatars.com/api/?name=" . urlencode($data['username']) . "&background=random&color=fff",
            'followers' => isset($data['followers']) ? (int)$data['followers'] : 0,
            'niche' => isset($data['niche']) ? $data['niche'] : '',
            'eng_rate' => isset($data['eng_rate']) ? $data['eng_rate'] : 'N/A',
            'location' => isset($data['location']) ? $data['location'] : '',
            'notes' => isset($data['bio']) ? $data['bio'] : 'Added via Chrome Extension',
            'contact_info' => isset($data['profile_url']) ? $data['profile_url'] : '',
            'status' => 'Active',
            'pipeline_status' => '',
            'created_by' => $user_id,
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Check Duplicates
        $this->db->where('username', $insert_data['username']);
        $this->db->where('platform', $insert_data['platform']);
        $account_query = $this->db->get('influencer_accounts');

        if ($account_query->num_rows() > 0) {
            $account = $account_query->row();
            $this->db->where('account_id', $account->id);
            $collab_exists = $this->db->count_all_results('collaborations');
            
            if ($collab_exists > 0) {
                echo json_encode(['status' => 'error', 'message' => 'Influencer already in your list!']);
                return;
            }
        }

        // Insert
        $insert_id = $this->Influencer_model->insert_influencer($insert_data);

        if ($insert_id) {
            echo json_encode(['status' => 'success', 'message' => 'Influencer added successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to add influencer.']);
        }
    }
}
