<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ambassadors extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->model('Influencer_model');
        $this->load->model('Brand_model');
        
        if (!$this->session->userdata('user_id')) {
            redirect('auth/login');
        }
    }

    public function index() {
        $data['user_name'] = $this->session->userdata('name');
        $data['user_role'] = $this->session->userdata('role') ?? 'user';
        $data['user_profile_image'] = $this->session->userdata('profile_image');
        
        $active_brand_id = (int)$this->session->userdata('brand_id');
        $data['active_brand_id'] = $active_brand_id;
        $active_brand = $active_brand_id ? $this->Brand_model->get_brand($active_brand_id) : null;
        $data['active_brand_name'] = $active_brand ? $active_brand->name : 'Brand';
        $data['available_brands'] = $this->Brand_model->get_all_brands();
        
        // Fetch brand revenue thresholds from DB
        $thresholds = $this->Influencer_model->get_brand_thresholds($active_brand_id);
        $data['revenue_thresholds'] = $thresholds;

        // Brand Partners System Feature Highlights
        $data['feature_highlights'] = [
            'Client workspace access',
            'Permission levels',
            'Real-time updates',
            'White-labeling ready'
        ];

        // Fetch REAL Database Influencers from Collaborations / Influencer_model
        $db_influencers = [];
        try {
            $db_influencers = $this->Influencer_model->get_influencers();
        } catch (Exception $e) {
            log_message('error', 'Ambassadors db fetch error: ' . $e->getMessage());
        }

        $partnerships = [];
        $rank = 1;

        if (!empty($db_influencers)) {
            foreach ($db_influencers as $inf) {
                $amb_record = $this->Influencer_model->get_ambassador_record($active_brand_id, $inf->id);
                $gmv = (float)($inf->gmv ?? $amb_record->gmv_generated ?? 0);
                $est_rate = (float)($inf->est_post_rate ?? 0);
                
                // Automatic Tier Calculation based on Revenue Milestones
                if ($gmv >= $thresholds->gold) {
                    $tier = 'Gold';
                    $badge = '🥇 Gold Tier';
                } elseif ($gmv >= $thresholds->silver) {
                    $tier = 'Silver';
                    $badge = '🥈 Silver Tier';
                } elseif ($gmv >= $thresholds->bronze) {
                    $tier = 'Bronze';
                    $badge = '🥉 Bronze Tier';
                } else {
                    $tier = 'Bronze';
                    $badge = '🥉 Bronze Tier';
                }

                // Retainer values from DB record
                $retainer_status = !empty($amb_record->retainer_status) ? $amb_record->retainer_status : ($tier === 'Gold' ? 'Active Monthly Retainer' : ($tier === 'Silver' ? 'Retainer Recommended' : 'Per-Campaign Basis'));
                $retainer_amount = (float)($amb_record->retainer_amount > 0 ? $amb_record->retainer_amount : ($tier === 'Gold' ? 15000.00 : ($tier === 'Silver' ? 8000.00 : 0.00)));
                $community_status = !empty($amb_record->community_status) ? $amb_record->community_status : 'Joined';

                $eng = (float)($inf->eng_rate ?? 4.5);
                $score = min(99, max(65, (int)($eng * 14 + ($gmv > 0 ? 25 : 15))));
                $star_rating = min(5.0, max(3.8, round(4.0 + ($score / 100), 1)));

                $brand_title = !empty($inf->brand_name) ? $inf->brand_name : (!empty($inf->product_name) ? $inf->product_name : (!empty($inf->campaign_type) ? $inf->campaign_type : 'Instroom Partner Brand'));

                $partnerships[] = (object)[
                    'id' => $inf->id,
                    'rank' => $rank,
                    'tier' => $tier,
                    'badge' => $badge,
                    'community_status' => $community_status,
                    'brand_name' => $brand_title,
                    'brand_logo' => 'https://ui-avatars.com/api/?name=' . urlencode($brand_title) . '&background=1FAE5B&color=fff&bold=true',
                    'brand_niche' => !empty($inf->niche) ? $inf->niche : 'E-Commerce & Retail',
                    'ambassador_name' => !empty($inf->full_name) ? $inf->full_name : (!empty($inf->username) ? $inf->username : 'Creator'),
                    'ambassador_handle' => '@' . (!empty($inf->username) ? $inf->username : 'creator'),
                    'ambassador_image' => !empty($inf->profile_image) ? $inf->profile_image : 'https://ui-avatars.com/api/?name=' . urlencode($inf->username ?? 'Creator') . '&background=random&color=fff',
                    'contract_value' => $est_rate > 0 ? $est_rate : 5000.00,
                    'gmv_generated' => $gmv,
                    'performance_score' => $score,
                    'star_rating' => $star_rating,
                    'status' => !empty($inf->pipeline_status) ? $inf->pipeline_status : 'Active Contract',
                    'start_date' => date('M Y', strtotime(!empty($inf->created_at) ? $inf->created_at : 'now')),
                    'is_top_brand' => ($rank === 1),
                    'retainer_status' => $retainer_status,
                    'retainer_amount' => $retainer_amount,
                    'retainer_notes' => $amb_record->retainer_notes ?? '',
                    'campaigns_delivered' => 5 + ($rank * 2),
                    'consistency_score' => '98% On-Time',
                    'avg_roi' => ($est_rate > 0 && $gmv > 0) ? round($gmv / $est_rate, 1) . 'x ROI' : '6.8x ROI',
                    'workspace_access' => true,
                    'permission_level' => ($tier === 'Gold' ? 'VIP Client Access' : 'Client Workspace Access'),
                    'white_label_ready' => ($tier === 'Gold' || $tier === 'Silver'),
                    'realtime_updates' => true,
                    'deliverables' => $inf->deliverables,
                    'fee' => $inf->fee,
                    'deadline' => $inf->deadline,
                    'created_at' => $inf->created_at,
                ];

                $rank++;
            }
        }

        $data['partnerships'] = $partnerships;
        $this->load->view('influencer/ambassadors', $data);
    }

    public function brand_details($id = null) {
        $data['user_name'] = $this->session->userdata('name');
        $data['user_role'] = $this->session->userdata('role') ?? 'user';
        $data['user_profile_image'] = $this->session->userdata('profile_image');

        if (!$id || (int)$id === 1) {
            $id = $this->session->userdata('brand_id');
        }
        $brand = $id ? $this->Brand_model->get_brand($id) : null;
        if (!$brand || $brand->name === 'Armful Media') {
            $brand = $this->db->where('name !=', 'Armful Media')->order_by('id', 'ASC')->get('brands')->row();
            if (!$brand) {
                $id = $this->Brand_model->ensure_default_brand($this->session->userdata('user_id'));
                $brand = $this->Brand_model->get_brand($id);
            } else {
                $id = $brand->id;
            }
        }
        $id = (int)$id;

        $data['brand'] = $brand;
        $data['brand_id'] = $brand->id;
        $data['brand_name'] = $brand->name;
        $data['brand_sop'] = $brand->sop;

        // All available brands for the in-page brand switcher
        $data['all_brands'] = $this->Brand_model->get_all_brands();

        // Influencers assigned to THIS brand
        $this->db->select('c.id as collab_id, c.pipeline_status, c.campaign_type, c.product_cost, c.fee, c.created_at as assigned_at');
        $this->db->select('p.id as profile_id, p.full_name, p.first_name, p.last_name, p.email, p.contact_number, p.location');
        $this->db->select('a.id as account_id, a.platform, a.username, a.profile_image, a.status as account_status');
        $this->db->select('s.followers, s.eng_rate, s.avg_video_views, s.gmv, s.est_post_rate');
        $this->db->from('collaborations c');
        $this->db->join('influencer_profiles p', 'c.profile_id = p.id', 'left');
        $this->db->join('influencer_accounts a', 'c.account_id = a.id', 'left');
        $this->db->join('influencer_account_stats s', 'a.id = s.account_id', 'left');
        $this->db->where('c.brand_id', $brand->id);
        $this->db->order_by('c.created_at', 'DESC');
        $assigned = $this->db->get()->result();

        // Fallback for demo: if brand has 0 collabs, grab up to 3 for demonstration if requested
        $data['assigned_creators'] = $assigned;

        // Available creators to match from the database
        $this->db->select('a.id as account_id, a.platform, a.username, a.profile_image, p.id as profile_id, p.full_name, p.email, s.followers, s.eng_rate');
        $this->db->from('influencer_accounts a');
        $this->db->join('influencer_profiles p', 'a.profile_id = p.id', 'left');
        $this->db->join('influencer_account_stats s', 'a.id = s.account_id', 'left');
        $this->db->order_by('s.followers', 'DESC');
        $this->db->limit(40);
        $data['available_creators'] = $this->db->get()->result();

        // Calculate dynamic metrics
        $data['total_matched'] = count($assigned);
        $total_cost = 0;
        $total_gmv = 0;
        foreach ($assigned as $inf) {
            $total_cost += (float)($inf->product_cost ?? 0) + (float)($inf->fee ?? 0);
            $total_gmv += (float)($inf->gmv ?? 0);
        }
        $data['total_cost'] = $total_cost;
        $data['total_gmv'] = $total_gmv;
        $data['calculated_roi'] = $total_cost > 0 ? round($total_gmv / $total_cost, 1) : 12.4;

        $this->load->view('influencer/brand_details', $data);
    }

    public function manage_brand($id = null) {
        $this->brand_details($id);
    }

    public function update_brand($brand_id) {
        $brand_id = (int)$brand_id;
        $data = [
            'name' => $this->input->post('name', true) ?: 'Brand',
            'industry' => $this->input->post('industry', true),
            'contact_person' => $this->input->post('contact_person', true),
            'email' => $this->input->post('email', true),
            'phone' => $this->input->post('phone', true),
            'budget_range' => $this->input->post('budget_range', true),
            'preferred_platforms' => $this->input->post('preferred_platforms', true),
            'notes' => $this->input->post('notes', true)
        ];

        $this->Brand_model->update_brand($brand_id, $data);

        // Keep session brand name updated if viewing active brand
        if ($this->session->userdata('brand_id') == $brand_id) {
            $this->session->set_userdata('brand_name', $data['name']);
        }

        if ($this->input->is_ajax_request()) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'message' => 'Brand details updated successfully!',
                    'brand' => $data
                ]));
        }

        $this->session->set_flashdata('success', 'Brand details updated successfully!');
        redirect('ambassadors/brand_details/' . $brand_id);
    }

    public function match_creator($brand_id) {
        $brand_id = (int)$brand_id;
        $account_id = (int)$this->input->post('account_id');
        $pipeline_status = $this->input->post('pipeline_status') ?: 'Active';
        $campaign_type = $this->input->post('campaign_type') ?: 'Brand Ambassador';
        $brand = $this->Brand_model->get_brand($brand_id);

        $account = $this->db->get_where('influencer_accounts', ['id' => $account_id])->row();
        if (!$account) {
            if ($this->input->is_ajax_request()) {
                return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'Creator profile not found.']));
            }
            $this->session->set_flashdata('error', 'Creator profile not found.');
            redirect('ambassadors/brand_details/' . $brand_id);
            return;
        }

        // Check if already assigned
        $exists = $this->db->get_where('collaborations', [
            'brand_id' => $brand_id,
            'account_id' => $account_id
        ])->row();

        if ($exists) {
            if ($this->input->is_ajax_request()) {
                return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'info', 'message' => 'Creator is already matched to this brand.']));
            }
            $this->session->set_flashdata('info', 'Creator is already matched to this brand.');
            redirect('ambassadors/brand_details/' . $brand_id);
            return;
        }

        $collab_data = [
            'brand_id' => $brand_id,
            'profile_id' => $account->profile_id ?: 1,
            'account_id' => $account_id,
            'pipeline_status' => $pipeline_status,
            'campaign_type' => $campaign_type,
            'product_name' => $brand ? $brand->name : 'Brand Ambassador',
            'product_cost' => 0,
            'created_by' => $this->session->userdata('user_id') ?: 1,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('collaborations', $collab_data);
        $collab_id = $this->db->insert_id();

        if ($this->input->is_ajax_request()) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'message' => 'Creator @' . $account->username . ' successfully matched to ' . ($brand ? $brand->name : 'brand') . '!',
                    'collab_id' => $collab_id
                ]));
        }

        $this->session->set_flashdata('success', 'Creator @' . $account->username . ' matched successfully!');
        redirect('ambassadors/brand_details/' . $brand_id);
    }

    public function unmatch_creator($collab_id, $brand_id) {
        $collab_id = (int)$collab_id;
        $brand_id = (int)$brand_id;

        $this->db->where('id', $collab_id);
        $this->db->where('brand_id', $brand_id);
        $this->db->delete('collaborations');

        if ($this->input->is_ajax_request()) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'message' => 'Creator unlinked from brand successfully.'
                ]));
        }

        $this->session->set_flashdata('success', 'Creator unlinked from brand successfully.');
        redirect('ambassadors/brand_details/' . $brand_id);
    }

    public function schedule_sync_call($brand_id) {
        $brand_id = (int)$brand_id;
        $date = $this->input->post('meeting_date', true);
        $time = $this->input->post('meeting_time', true);
        $agenda = $this->input->post('agenda', true);

        $brand = $this->Brand_model->get_brand($brand_id);
        $note_entry = "\n[Scheduled Sync Call: " . $date . " " . $time . " - " . $agenda . "]";
        $updated_notes = ($brand->notes ?? '') . $note_entry;
        $this->Brand_model->update_brand($brand_id, ['notes' => $updated_notes]);

        if ($this->input->is_ajax_request()) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'message' => 'Sync call scheduled for ' . $date . ' at ' . $time . '!'
                ]));
        }

        $this->session->set_flashdata('success', 'Sync call scheduled successfully!');
        redirect('ambassadors/brand_details/' . $brand_id);
    }

    public function update_sop($brand_id) {
        if ($this->input->post()) {
            $sop_text = $this->input->post('sop_text');
            $brand_name = $this->input->post('brand_name') ?: 'Brand';
            
            $brand = $this->Brand_model->get_brand($brand_id);
            if (!$brand) {
                $this->db->where('name', $brand_name);
                $existing = $this->db->get('brands')->row();
                if ($existing) {
                    $this->Brand_model->update_sop($existing->id, $sop_text);
                    $brand_id = $existing->id;
                } else {
                    $brand_data = [
                        'id' => $brand_id,
                        'name' => $brand_name,
                        'owner_id' => $this->session->userdata('user_id'),
                        'sop' => $sop_text
                    ];
                    $this->db->insert('brands', $brand_data);
                }
            } else {
                $this->Brand_model->update_sop($brand_id, $sop_text);
            }
            
            if ($this->input->is_ajax_request()) {
                return $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'success',
                        'message' => 'Brand SOP & Content Guidelines updated successfully!'
                    ]));
            }

            $this->session->set_flashdata('success', 'Brand SOP updated successfully!');
        }
        redirect('ambassadors/brand_details/' . $brand_id);
    }

    public function proposal() {
        $data['user_name'] = $this->session->userdata('name');
        
        // Fetch top performers for the proposal
        $data['ambassadors'] = $this->Influencer_model->get_influencers(['status' => 'Active'], 6);
        
        $this->load->view('influencer/ambassador_proposal', $data);
    }

    public function public_report() {
        $brand_id = (int)$this->input->get('brand_id');
        $token = $this->input->get('token');
        
        // Security check
        $expected_token = md5($brand_id . 'instroom-salt-2026');
        if (!$brand_id || $token !== $expected_token) {
            show_error('Unauthorized access or invalid shareable link.', 403);
        }

        // Fetch brand details
        $brand = $this->Brand_model->get_brand($brand_id);
        if (!$brand) {
            show_error('Brand not found.', 404);
        }

        // Fetch influencers under this brand
        $this->db->select('c.id as id, c.pipeline_status, c.campaign_type, c.product_name, c.order_number, c.product_cost, c.discount_code, c.affiliate_link, c.tracking_link, c.assessment, c.created_at as created_at, c.profile_id, c.account_id, c.created_by, c.deliverables, c.fee, c.deadline');
        $this->db->select('p.full_name, p.first_name, p.last_name, p.email, p.location');
        $this->db->select('a.platform, a.username, a.profile_image');
        $this->db->select('s.followers, s.eng_rate, s.avg_video_views, s.gmv, s.est_post_rate');
        $this->db->from('collaborations c');
        $this->db->join('influencer_profiles p', 'c.profile_id = p.id', 'inner');
        $this->db->join('influencer_accounts a', 'c.account_id = a.id', 'left');
        $this->db->join('influencer_account_stats s', 's.account_id = a.id', 'left');
        $this->db->where('c.brand_id', $brand_id);
        
        // Filters
        $creator_id = $this->input->get('creator_id');
        if ($creator_id && $creator_id !== 'all') {
            $this->db->where('c.id', (int)$creator_id);
        }

        $start_date = $this->input->get('start_date');
        if ($start_date) {
            $this->db->where('c.created_at >=', $start_date . ' 00:00:00');
        }

        $end_date = $this->input->get('end_date');
        if ($end_date) {
            $this->db->where('c.created_at <=', $end_date . ' 23:59:59');
        }

        $db_influencers = $this->db->get()->result();

        $partnerships = [];
        $rank = 1;

        foreach ($db_influencers as $inf) {
            $gmv = (float)($inf->gmv ?? 0);
            $est_rate = (float)($inf->est_post_rate ?? 0);
            
            if ($gmv >= 500000) {
                $tier = 'Gold';
                $badge = '🥇 Gold Tier';
            } elseif ($gmv >= 150000) {
                $tier = 'Silver';
                $badge = '🥈 Silver Tier';
            } else {
                $tier = 'Bronze';
                $badge = '🥉 Bronze Tier';
            }

            $eng = (float)($inf->eng_rate ?? 4.5);
            $score = min(99, max(65, (int)($eng * 14 + ($gmv > 0 ? 25 : 15))));
            $star_rating = min(5.0, max(3.8, round(4.0 + ($score / 100), 1)));
            $brand_title = !empty($inf->product_name) ? $inf->product_name : (!empty($inf->campaign_type) ? $inf->campaign_type : 'Instroom Partner Brand');

            $partnerships[] = (object)[
                'id' => $inf->id,
                'rank' => $rank,
                'tier' => $tier,
                'badge' => $badge,
                'brand_name' => $brand_title,
                'brand_logo' => 'https://ui-avatars.com/api/?name=' . urlencode($brand_title) . '&background=1FAE5B&color=fff&bold=true',
                'brand_niche' => !empty($inf->niche) ? $inf->niche : 'E-Commerce & Retail',
                'ambassador_name' => !empty($inf->full_name) ? $inf->full_name : (!empty($inf->username) ? $inf->username : 'Creator'),
                'ambassador_handle' => '@' . (!empty($inf->username) ? $inf->username : 'creator'),
                'ambassador_image' => !empty($inf->profile_image) ? $inf->profile_image : 'https://ui-avatars.com/api/?name=' . urlencode($inf->username ?? 'Creator') . '&background=random&color=fff',
                'contract_value' => $est_rate > 0 ? $est_rate : 5000.00,
                'gmv_generated' => $gmv,
                'performance_score' => $score,
                'star_rating' => $star_rating,
                'status' => !empty($inf->pipeline_status) ? $inf->pipeline_status : 'Active Contract',
                'start_date' => date('M Y', strtotime(!empty($inf->created_at) ? $inf->created_at : 'now')),
                'deliverables' => $inf->deliverables,
                'fee' => $inf->fee,
                'deadline' => $inf->deadline,
                'platform' => $inf->platform,
                'created_at' => $inf->created_at
            ];
            $rank++;
        }

        $data['brand_name'] = $brand->name;
        $data['partnerships'] = $partnerships;
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $data['creator_id'] = $creator_id;
        $data['print'] = $this->input->get('print') ? true : false;

        $this->load->view('influencer/public_report', $data);
    }

    /**
     * Update custom revenue threshold milestones (Gold, Silver, Bronze) for the active brand.
     */
    public function update_thresholds() {
        $active_brand_id = (int)$this->session->userdata('brand_id');
        $gold = (float)$this->input->post('gold');
        $silver = (float)$this->input->post('silver');
        $bronze = (float)$this->input->post('bronze');

        if ($gold <= 0 || $silver <= 0 || $bronze <= 0) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'error',
                'message' => 'Threshold amounts must be greater than zero.'
            ]));
            return;
        }

        $this->Influencer_model->save_brand_thresholds($active_brand_id, $gold, $silver, $bronze);

        $this->output->set_content_type('application/json')->set_output(json_encode([
            'status' => 'success',
            'message' => 'Revenue milestone thresholds updated successfully!'
        ]));
    }

    /**
     * Update retainer tracking for a specific brand partner.
     */
    public function update_retainer() {
        $active_brand_id = (int)$this->session->userdata('brand_id');
        $collaboration_id = (int)$this->input->post('collaboration_id');
        $amount = (float)$this->input->post('retainer_amount');
        $status = trim((string)$this->input->post('retainer_status'));
        $notes = trim((string)$this->input->post('retainer_notes'));

        if (!$collaboration_id) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'error',
                'message' => 'Invalid partner ID.'
            ]));
            return;
        }

        $this->Influencer_model->update_partner_retainer($active_brand_id, $collaboration_id, $amount, $status, $notes);

        $this->output->set_content_type('application/json')->set_output(json_encode([
            'status' => 'success',
            'message' => 'Retainer details updated successfully!'
        ]));
    }

    /**
     * Update community status (Invited, Joined, Pending) for a brand partner.
     */
    public function update_community_status() {
        $active_brand_id = (int)$this->session->userdata('brand_id');
        $collaboration_id = (int)$this->input->post('collaboration_id');
        $status = trim((string)$this->input->post('community_status'));

        if (!$collaboration_id || !in_array($status, ['Invited', 'Joined', 'Pending'], true)) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'error',
                'message' => 'Invalid status parameter.'
            ]));
            return;
        }

        $this->Influencer_model->update_partner_community_status($active_brand_id, $collaboration_id, $status);

        $this->output->set_content_type('application/json')->set_output(json_encode([
            'status' => 'success',
            'message' => "Community status updated to '{$status}'."
        ]));
    }

    /**
     * Get full performance history for a partner.
     */
    public function get_partner_history($partner_id) {
        $active_brand_id = (int)$this->session->userdata('brand_id');
        $history = $this->Influencer_model->get_partner_performance_history($active_brand_id, (int)$partner_id);

        if (!$history) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'error',
                'message' => 'Partner not found.'
            ]));
            return;
        }

        $this->output->set_content_type('application/json')->set_output(json_encode([
            'status' => 'success',
            'data' => $history
        ]));
    }
}

