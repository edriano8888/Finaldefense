<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property Campaign_model $Campaign_model
 * @property User_model $User_model
 * @property Brand_model $Brand_model
 * @property Influencer_model $Influencer_model
 * @property Order_model $Order_model
 */
class Manager extends Brand_Controller {
    
    public function __construct() {
        parent::__construct();
        if (!is_cli() && !$this->check_permission('brand_settings')) {
            $this->session->set_flashdata('error', 'You do not have permission to access the Manager Panel.');
            redirect('influencer');
        }
        $this->load->model(['Campaign_model', 'User_model', 'Influencer_model', 'Brand_model', 'Order_model', 'Activity_log_model']);
        $this->load->helper(['url', 'date']);
        $this->load->library('session');
    }

    public function index() {
        $data['user_name'] = $this->session->userdata('name');
        $data['user_role'] = $this->user_role;
        $data['page_title'] = 'Dashboard Overview';
        
        // Dashboard Stats (Scoped to current brand)
        $data['total_members_count'] = count($this->Brand_model->get_members($this->brand_id));
        $data['pending_orders_count'] = count($this->Order_model->get_pending_orders());
        $data['pending_influencers_count'] = $this->Influencer_model->count_influencers(['pipeline_status' => 'Sourced']); 
        
        // Detailed Analytics & Campaign Info
        $analytics = $this->Influencer_model->get_analytics_stats();
        $data['analytics'] = $analytics;
        
        $campaigns = $this->Campaign_model->get_all_campaigns();
        $data['campaigns'] = $campaigns;
        $data['active_campaigns_count'] = count($campaigns);

        // Fetch recent activities
        $data['recent_activities'] = $this->Activity_log_model->get_recent_logs(5);

        $data['content_view'] = 'manager/dashboard_overview';
        $this->load->view('manager/dashboard', $data);
    }

    public function team_management() {
        $data['user_name'] = $this->session->userdata('name');
        $data['user_role'] = $this->user_role;
        $data['active_section'] = 'team_management';
        $data['page_title'] = 'Team Management';
        $data['members'] = $this->Brand_model->get_member_performance($this->brand_id);
        $data['content_view'] = 'manager/team_management';
        $this->load->view('manager/dashboard', $data);
    }

    public function remove_team_member($user_id) {
        if ($this->Brand_model->remove_member($this->brand_id, $user_id)) {
            $this->session->set_flashdata('success', 'Team member removed successfully.');
        } else {
            $this->session->set_flashdata('error', 'Failed to remove team member.');
        }
        redirect('manager/team_management');
    }

    public function brands() {
        $data['user_name'] = $this->session->userdata('name');
        $data['user_role'] = $this->user_role;
        $data['active_section'] = 'brands';
        $data['page_title'] = 'Brand Profiles';
        $data['brands'] = $this->Brand_model->get_all_brands_with_summary();
        $data['content_view'] = 'manager/brands';
        $this->load->view('manager/dashboard', $data);
    }

    public function create_brand() {
        $name = $this->input->post('name');
        $industry = $this->input->post('industry');
        
        if ($name) {
            $brand_data = [
                'name' => $name,
                'industry' => $industry,
                'contact_person' => $this->input->post('contact_person'),
                'email' => $this->input->post('email'),
                'phone' => $this->input->post('phone'),
                'budget_range' => $this->input->post('budget_range'),
                'preferred_platforms' => $this->input->post('preferred_platforms'),
                'notes' => $this->input->post('notes'),
                'status' => 'active',
                'created_by' => $this->user_id
            ];
            $brand_id = $this->Brand_model->create_brand($brand_data, $this->user_id);
            
            if ($brand_id) {
                $this->session->set_flashdata('success', 'Brand Client Profile created successfully!');
            } else {
                $this->session->set_flashdata('error', 'Failed to create brand.');
            }
        }
        redirect('manager/brands');
    }

    public function update_brand() {
        $id = $this->input->post('id');
        $name = $this->input->post('name');

        if ($id && $name) {
            $data = [
                'name' => $name,
                'industry' => $this->input->post('industry'),
                'contact_person' => $this->input->post('contact_person'),
                'email' => $this->input->post('email'),
                'phone' => $this->input->post('phone'),
                'budget_range' => $this->input->post('budget_range'),
                'preferred_platforms' => $this->input->post('preferred_platforms'),
                'notes' => $this->input->post('notes'),
                'status' => $this->input->post('status')
            ];
            if ($this->Brand_model->update_brand($id, $data)) {
                $this->session->set_flashdata('success', 'Brand Client Profile updated successfully!');
            } else {
                $this->session->set_flashdata('error', 'Failed to update brand.');
            }
        }
        redirect('manager/brands');
    }

    public function delete_brand($id) {
        if ($id) {
            // Check if it's the current brand
            if ((int)$id === (int)$this->brand_id) {
                $this->session->set_flashdata('error', 'Cannot delete the active brand. Switch brands first.');
            } else {
                if ($this->Brand_model->delete_brand($id)) {
                    $this->session->set_flashdata('success', 'Brand deleted successfully.');
                } else {
                    $this->session->set_flashdata('error', 'Failed to delete brand.');
                }
            }
        }
        redirect('manager/brands');
    }

    public function invite_member() {
        $email = $this->input->post('email');
        $role = $this->input->post('role') ?: 'collaborator';

        if ($email) {
            if ($this->Brand_model->invite_member($this->brand_id, $email, $role)) {
                $this->session->set_flashdata('success', 'Team member added successfully!');
            } else {
                $this->session->set_flashdata('error', 'Failed to add member. User may not exist or is already a member.');
            }
        }
        redirect('manager/team_management');
    }

    public function update_member_role() {
        $user_id = $this->input->post('user_id');
        $role = $this->input->post('role');

        if ($user_id && $role) {
            if ($this->Brand_model->update_member_role($this->brand_id, $user_id, $role)) {
                $this->session->set_flashdata('success', 'Member role updated!');
            } else {
                $this->session->set_flashdata('error', 'Failed to update role.');
            }
        }
        redirect('manager/team_management');
    }

    public function campaign_planning() {
        $data['user_name'] = $this->session->userdata('name');
        $data['user_role'] = $this->user_role;
        $data['active_section'] = 'campaign_planning';
        $data['page_title'] = 'Campaign Planning';
        
        $data['campaigns'] = $this->Campaign_model->get_all_campaigns();
        $data['influencers'] = $this->Influencer_model->get_influencers(); // For assignment
        $data['members'] = $this->Brand_model->get_members($this->brand_id);

        $data['content_view'] = 'manager/campaign_planning';
        $this->load->view('manager/dashboard', $data);
    }

    public function create_campaign() {
        $data = [
            'brand_id' => $this->brand_id,
            'title' => $this->input->post('title'),
            'description' => $this->input->post('description'),
            'budget' => $this->input->post('budget') ?: 0,
            'start_date' => $this->input->post('start_date'),
            'end_date' => $this->input->post('end_date'),
            'status' => $this->input->post('status') ?: 'Planning'
        ];

        $campaign_id = $this->Campaign_model->create_campaign($data);
        
        if ($campaign_id) {
            $this->session->set_flashdata('success', 'Campaign created successfully!');
        } else {
            $this->session->set_flashdata('error', 'Failed to create campaign.');
        }
        
        redirect('manager/campaign_planning');
    }

    public function update_campaign() {
        $id = $this->input->post('id');
        $data = [
            'title' => $this->input->post('title'),
            'description' => $this->input->post('description'),
            'budget' => $this->input->post('budget') ?: 0,
            'start_date' => $this->input->post('start_date'),
            'end_date' => $this->input->post('end_date'),
            'status' => $this->input->post('status')
        ];
        
        if ($id && $this->Campaign_model->update_campaign($id, $data)) {
            $this->session->set_flashdata('success', 'Campaign updated successfully!');
        } else {
            $this->session->set_flashdata('error', 'Failed to update campaign.');
        }
        
        redirect('manager/campaign_planning');
    }

    public function update_campaign_status() {
        $id = $this->input->post('id');
        $status = $this->input->post('status');
        
        $valid_statuses = ['Planning', 'Active', 'Done', 'Pending', 'Ongoing', 'Completed', 'Failed'];
        
        if ($id && in_array($status, $valid_statuses)) {
            $this->Campaign_model->update_campaign($id, ['status' => $status]);
            
            if ($this->input->is_ajax_request() || $this->input->get('ajax') || $this->input->post('ajax')) {
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Campaign status updated to ' . $status,
                    'new_status' => $status,
                    'csrf_token' => $this->security->get_csrf_hash()
                ]);
                exit;
            }
            
            $this->session->set_flashdata('success', 'Campaign status updated to ' . $status);
        } else {
            if ($this->input->is_ajax_request() || $this->input->get('ajax') || $this->input->post('ajax')) {
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'error', 
                    'message' => 'Invalid status or campaign ID',
                    'csrf_token' => $this->security->get_csrf_hash()
                ]);
                exit;
            }
        }
        
        redirect('manager/campaign_planning');
    }

    public function delete_campaign($id = null) {
        $id = $id ?: $this->input->post('id');
        if ($id && $this->Campaign_model->delete_campaign($id)) {
            if ($this->input->is_ajax_request() || $this->input->get('ajax') || $this->input->post('ajax')) {
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'success', 
                    'message' => 'Campaign deleted successfully',
                    'csrf_token' => $this->security->get_csrf_hash()
                ]);
                exit;
            }
            $this->session->set_flashdata('success', 'Campaign deleted successfully!');
        } else {
            if ($this->input->is_ajax_request() || $this->input->get('ajax') || $this->input->post('ajax')) {
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'error', 
                    'message' => 'Failed to delete campaign',
                    'csrf_token' => $this->security->get_csrf_hash()
                ]);
                exit;
            }
            $this->session->set_flashdata('error', 'Failed to delete campaign.');
        }
        
        redirect('manager/campaign_planning');
    }

    public function assign_influencers() {
        $campaign_id = (int)$this->input->post('campaign_id');
        $influencer_ids = $this->input->post('influencer_ids'); // Array from select multiple
        $redirect_to = (string)$this->input->post('redirect_to');

        if ($campaign_id) {
            $this->Campaign_model->assign_influencers($campaign_id, is_array($influencer_ids) ? $influencer_ids : []);
            $this->session->set_flashdata('success', 'Creators assigned to campaign successfully!');
        }
        
        if ($redirect_to === 'campaign_details' && $campaign_id) {
            redirect('manager/campaign_details/' . $campaign_id);
        }
        redirect('manager/campaign_planning');
    }

    public function campaign_details($id) {
        $data['user_name'] = $this->session->userdata('name');
        $data['user_role'] = $this->user_role;
        $data['active_section'] = 'campaign_details';
        $data['page_title'] = 'Campaign Details';
        
        $data['campaign'] = $this->Campaign_model->get_campaign_by_id($id);
        if (!$data['campaign']) {
            show_404();
        }
        
        $data['assigned_influencers'] = $this->Campaign_model->get_campaign_influencers($id);
        $data['all_influencers'] = $this->Influencer_model->get_influencers();
        $data['members'] = $this->Brand_model->get_members($this->brand_id);
        
        // Fetch tasks for each assigned influencer
        foreach ($data['assigned_influencers'] as &$influencer) {
            $influencer->tasks = $this->Campaign_model->get_tasks($id, $influencer->influencer_id);
        }

        $data['content_view'] = 'manager/campaign_details';
        $this->load->view('manager/dashboard', $data);
    }

    public function add_task() {
        $campaign_id = (int)$this->input->post('campaign_id');
        $influencer_id_raw = $this->input->post('influencer_id');
        $apply_to_all = (string)$this->input->post('apply_to_all') === '1' || $influencer_id_raw === 'all';
        $influencer_id = (int)$influencer_id_raw;
        $task_name = trim((string)$this->input->post('task_name'));
        $assigned_to = $this->input->post('assigned_to');
        $due_date = $this->input->post('due_date');
        $redirect_to = (string)$this->input->post('redirect_to');

        $assigned_to_id = ($assigned_to !== null && $assigned_to !== '') ? (int)$assigned_to : null;
        $due_date_val = ($due_date !== null && $due_date !== '') ? (string)$due_date : null;

        if ($campaign_id && $task_name !== '') {
            $target_influencer_ids = [];
            if ($apply_to_all) {
                $camp_infs = $this->Campaign_model->get_campaign_influencers($campaign_id);
                if (!empty($camp_infs)) {
                    foreach ($camp_infs as $ci) {
                        $target_influencer_ids[] = (int)$ci->influencer_id;
                    }
                }
            } elseif ($influencer_id > 0) {
                $target_influencer_ids[] = $influencer_id;
            }

            $created_count = 0;
            foreach ($target_influencer_ids as $target_inf_id) {
                $task_data = [
                    'campaign_id' => $campaign_id,
                    'influencer_id' => $target_inf_id,
                    'task_name' => $task_name,
                    'status' => 'Pending',
                    'assigned_to' => $assigned_to_id,
                    'due_date' => $due_date_val,
                    'created_by' => $this->user_id
                ];
                if ($this->Campaign_model->add_task($task_data)) {
                    $created_count++;
                }
            }

            if ($created_count > 0) {
                $this->session->set_flashdata('success', $created_count > 1 ? "Task assigned across {$created_count} creators!" : 'Task assigned successfully!');
            } else {
                $this->session->set_flashdata('error', 'Please assign at least one creator to this campaign first.');
            }
        }
        
        if ($redirect_to === 'campaign_planning') {
            redirect('manager/campaign_planning');
        }
        redirect('manager/campaign_details/' . $campaign_id . ($influencer_id > 0 ? '?influencer_id=' . $influencer_id : ''));
    }

    public function update_task_status() {
        $task_id = (int)$this->input->post('task_id');
        $status = (string)$this->input->post('status');
        $campaign_id = (int)$this->input->post('campaign_id');
        $influencer_id = (int)$this->input->post('influencer_id');

        if ($task_id && in_array($status, ['Pending', 'Done'], true)) {
            $this->Campaign_model->update_task_status($task_id, $status);
            $this->session->set_flashdata('success', 'Task status updated!');
        }
        
        redirect('manager/campaign_details/' . $campaign_id . ($influencer_id ? '?influencer_id=' . $influencer_id : ''));
    }

    public function delete_task() {
        $task_id = (int)$this->input->post('task_id');
        $campaign_id = (int)$this->input->post('campaign_id');
        $influencer_id = (int)$this->input->post('influencer_id');

        if ($task_id) {
            $this->Campaign_model->delete_task($task_id);
            $this->session->set_flashdata('success', 'Task deleted.');
        }
        
        redirect('manager/campaign_details/' . $campaign_id . ($influencer_id ? '?influencer_id=' . $influencer_id : ''));
    }

    public function influencer_review() {
        $data['user_name'] = $this->session->userdata('name');
        $data['user_role'] = $this->user_role;
        $data['active_section'] = 'influencer_review';
        $data['page_title'] = 'Influencer Review';

        $filters = [];
        $search = $this->input->get('search');
        $platform = $this->input->get('platform') ?: 'all';
        
        $data['current_search'] = $search ?: '';
        $data['current_platform'] = $platform;

        if ($search) {
            // If searching, search GLOBAL to avoid "why can't I find them" issues
            $data['pending_influencers'] = $this->Influencer_model->search_global($search, $this->brand_id);
            // Apply platform filter on the result if not 'all'
            if ($platform !== 'all') {
                $data['pending_influencers'] = array_filter($data['pending_influencers'], function($inf) use ($platform) {
                    return isset($inf->platform) && $inf->platform === $platform;
                });
            }
        } else {
            // Default: Show only 'Sourced' influencers for review
            if ($platform !== 'all') {
                $filters['platform'] = $platform;
            }
            $data['pending_influencers'] = $this->Influencer_model->get_influencers_by_pipeline_status(['Sourced'], $filters);
        }

        $data['content_view'] = 'manager/influencer_review';
        $this->load->view('manager/dashboard', $data);
    }

    public function update_influencer_status() {
        $id = $this->input->post('id');
        $status = $this->input->post('status'); // 'Contacted' (Approved) or 'Rejected'

        if ($id && ($status == 'Contacted' || $status == 'Rejected')) {
            $this->Influencer_model->update_pipeline_status($id, $status);
            
            // Log activity
            $influencer = $this->Influencer_model->get_influencer_by_id($id);
            $name = $influencer ? ($influencer->username ?: $influencer->full_name) : "Influencer #$id";
            
            $log_action = ($status == 'Contacted') ? 'Approve Influencer' : 'Reject Influencer';
            $details = ($status == 'Contacted') 
                ? "Approved creator @$name for brand outreach." 
                : "Rejected creator @$name during qualification review.";
            $this->Activity_log_model->log_activity($log_action, $details);

            $msg = ($status == 'Contacted') ? 'Influencer approved for contact!' : 'Influencer rejected.';
            $this->session->set_flashdata('success', $msg);
        } else {
            $this->session->set_flashdata('error', 'Invalid request.');
        }

        redirect('manager/influencer_review');
    }

    public function bulk_update_influencers() {
        $ids = $this->input->post('ids'); // Array or JSON string of IDs
        $action = $this->input->post('action'); // 'approve' or 'reject'

        if (is_string($ids)) {
            $ids = json_decode($ids, true);
        }

        if (!empty($ids) && ($action === 'approve' || $action === 'reject')) {
            $status = ($action === 'approve') ? 'Contacted' : 'Rejected';
            $this->Influencer_model->bulk_update_status($ids, $status);

            // Log activity
            $log_action = ($action === 'approve') ? 'Bulk Approve Influencers' : 'Bulk Reject Influencers';
            $details = ($action === 'approve') 
                ? "Bulk approved " . count($ids) . " creators for brand outreach." 
                : "Bulk rejected " . count($ids) . " creators during review.";
            $this->Activity_log_model->log_activity($log_action, $details);

            $msg = ($action === 'approve') ? count($ids) . ' influencers approved!' : count($ids) . ' influencers rejected.';
            $this->session->set_flashdata('success', $msg);
        } else {
            $this->session->set_flashdata('error', 'Invalid request.');
        }

        redirect('manager/influencer_review');
    }

    public function orders_discounts() {
        $data['user_name'] = $this->session->userdata('name');
        $data['user_role'] = $this->user_role;
        $data['active_section'] = 'orders_discounts';
        $data['page_title'] = 'Orders & Discounts';

        $status_filter = $this->input->get('status') ?: 'all';
        $data['current_status'] = $status_filter;

        $data['pending_orders'] = $this->Order_model->get_orders_by_status($status_filter === 'pending' ? 'pending' : null);

        $data['stats'] = [
            'pending' => $this->Order_model->count_by_status('pending'),
            'approved' => $this->Order_model->count_by_status('approved'),
            'rejected' => $this->Order_model->count_by_status('rejected'),
            'total' => $this->Order_model->count_by_status()
        ];

        $data['content_view'] = 'manager/orders_discounts';
        $this->load->view('manager/dashboard', $data);
    }

    public function update_order_status() {
        $id = (int)$this->input->post('id');
        $status = strtolower((string)$this->input->post('status'));
        $valid_statuses = ['approved', 'rejected', 'shipped', 'delivered', 'pending'];

        if ($id && in_array($status, $valid_statuses)) {
            $order = $this->Order_model->get_order_by_id($id);
            $p_name = $order ? $order->product_name : "Order #$id";
            $cost = $order ? (float)$order->total_cost : 0.0;

            $this->Order_model->update_status($id, $status);

            // Log activity
            $log_action = 'Update Sample Order Status';
            $details = "Updated order #{$id} ('$p_name') status to '$status' (Cost: $" . number_format($cost, 2) . ").";
            $this->Activity_log_model->log_activity($log_action, $details);

            $this->session->set_flashdata('success', 'Order status updated to ' . ucfirst($status) . '!');
        } else {
            $this->session->set_flashdata('error', 'Invalid status update request.');
        }

        redirect('manager/orders_discounts');
    }

    public function create_sample_order() {
        $product_name = trim((string)$this->input->post('product_name'));
        $influencer_name = trim((string)$this->input->post('influencer_name'));
        $total_cost = (float)$this->input->post('total_cost');
        $coupon_code = trim((string)$this->input->post('coupon_code'));
        $tracking_number = trim((string)$this->input->post('tracking_number'));
        $notes = trim((string)$this->input->post('notes'));

        if (!empty($product_name)) {
            $data = [
                'brand_id' => $this->brand_id,
                'influencer_name' => $influencer_name,
                'product_name' => $product_name,
                'coupon_code' => $coupon_code,
                'tracking_number' => $tracking_number,
                'total_cost' => $total_cost,
                'notes' => $notes,
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s')
            ];
            $this->Order_model->create_order($data);
            $this->Activity_log_model->log_activity('Create Sample Order', "Created sample order '$product_name' for creator " . ($influencer_name ?: 'Unknown'));
            $this->session->set_flashdata('success', 'Sample order created successfully!');
        } else {
            $this->session->set_flashdata('error', 'Product name is required.');
        }
        redirect('manager/orders_discounts');
    }

    public function performance() {
        $data['user_name'] = $this->session->userdata('name');
        $data['user_role'] = $this->user_role;
        $data['active_section'] = 'performance';
        $data['page_title'] = 'Performance Analytics';
        
        // Handle period filtering
        $period = $this->input->get('period') ?: 'all';
        $data['current_period'] = $period;
        
        $days = null;
        if ($period == '30') $days = 30;
        elseif ($period == '90') $days = 90;
        elseif ($period == '365') $days = 365;
        
        // Get real analytics
        $data['analytics'] = $this->Influencer_model->get_analytics_stats($days);
        
        $data['content_view'] = 'manager/performance';
        $this->load->view('manager/dashboard', $data);
    }

    public function billing() {
        show_404();
    }

    public function reports() {
        $data['user_name'] = $this->session->userdata('name');
        $data['user_role'] = $this->user_role;
        $data['active_section'] = 'reports';
        $data['page_title'] = 'Reports & Export';
        
        // Fetch stats for the reports overview
        $data['analytics'] = $this->Influencer_model->get_analytics_stats();
        $data['total_campaigns'] = $this->Campaign_model->count_all_campaigns();
        $data['total_members'] = count($this->Brand_model->get_members($this->brand_id));
        $data['all_influencers'] = $this->Influencer_model->get_influencers();
        
        $data['content_view'] = 'manager/reports';
        $this->load->view('manager/dashboard', $data);
    }

    /**
     * Shared report export helpers.
     * Reports are exported as real Excel (.xlsx) files instead of CSV because
     * CSV cannot store column widths (Excel showed "#####" and cut-off headers).
     * Every report = title block + ONE header row + data rows, with columns
     * auto-sized to fit, the header frozen, and numbers stored as real numbers.
     */
    private $_report_filename = 'Instroom_Report';

    private function _report_start($filename) {
        $this->load->library('Xlsx_writer');
        $out = new Xlsx_writer();
        $this->_report_filename = $filename;

        // "Instroom_Campaign_Report_20261010.csv" -> "Campaign Report"
        $title = preg_replace('/\.(csv|xlsx?)$/i', '', $filename);
        $title = preg_replace('/^Instroom_|_\d{8}$/', '', $title);
        $title = trim(str_replace('_', ' ', $title));
        $out->set_sheet_name($title);

        $brand = (!empty($this->brand_name) && $this->brand_name !== 'Armful Media') ? $this->brand_name : 'JBL';
        if (!empty($this->brand_id) && isset($this->Brand_model) && method_exists($this->Brand_model, 'get_brand')) {
            $b = $this->Brand_model->get_brand($this->brand_id);
            if ($b && !empty($b->name) && $b->name !== 'Armful Media') { $brand = $b->name; }
        }

        $out->add_title('INSTROOM - ' . strtoupper($title));
        $out->add_meta('Brand:', $brand);
        $out->add_meta('Generated On:', date('F j, Y - g:i A'));
        $out->add_meta('Prepared By:', $this->session->userdata('name') ?: 'Manager');
        $out->add_blank();
        return $out;
    }

    private function _report_header($out, array $header) {
        $out->add_header($header);
        $out->freeze_header($out->row_count());
        $out->auto_filter($out->row_count());
    }

    private function _csv_row($out, array $row, $cols) {
        $row = array_values($row);
        $row = array_pad(array_slice($row, 0, $cols), $cols, '');
        $out->add_row($row);
    }

    private function _report_send($out) {
        $out->send($this->_report_filename);
    }

    private function _num($v, $dec = 2) {
        return round((float)$v, $dec);
    }

    private function _report_date($value) {
        if (empty($value) || strpos((string)$value, '0000-00-00') === 0) { return ''; }
        $ts = is_numeric($value) ? (int)$value : strtotime($value);
        return $ts ? date('M d, Y', $ts) : '';
    }

    /**
     * Export Performance Report to CSV (Platform breakdown + Grand Total row)
     */
    public function export_performance() {
        $a = $this->Influencer_model->get_analytics_stats();
        $out = $this->_report_start('Instroom_Performance_Report_' . date('Ymd') . '.xlsx');

        $header = [
            'Platform', 'Total Influencers', 'Responded', 'Won / Ambassadors', 'Posted',
            'Not Interested', 'Content Pending', 'Response Rate (%)', 'Conversion Rate (%)', 'Sales GMV (USD)'
        ];
        $cols = count($header);
        $this->_report_header($out, $header);

        $t = ['total' => 0, 'responded' => 0, 'won' => 0, 'posted' => 0, 'not_interested' => 0, 'content_pending' => 0, 'sales_amount' => 0];
        foreach ((array)$a['platform_details'] as $platform => $s) {
            $total = (int)($s['total'] ?? 0);
            $this->_csv_row($out, [
                $platform,
                $total,
                (int)($s['responded'] ?? 0),
                (int)($s['won'] ?? 0),
                (int)($s['posted'] ?? 0),
                (int)($s['not_interested'] ?? 0),
                (int)($s['content_pending'] ?? 0),
                $total > 0 ? $this->_num(($s['responded'] / $total) * 100) : 0,
                $total > 0 ? $this->_num(($s['won'] / $total) * 100) : 0,
                $this->_num($s['sales_amount'] ?? 0),
            ], $cols);
            foreach ($t as $k => $_) { $t[$k] += (float)($s[$k] ?? 0); }
        }

        $out->add_total([
            'GRAND TOTAL',
            (int)$t['total'],
            (int)$t['responded'],
            (int)$t['won'],
            (int)$t['posted'],
            (int)$t['not_interested'],
            (int)$t['content_pending'],
            $t['total'] > 0 ? $this->_num(($t['responded'] / $t['total']) * 100) : 0,
            $t['total'] > 0 ? $this->_num(($t['won'] / $t['total']) * 100) : 0,
            $this->_num($t['sales_amount']),
        ], $cols);

        $this->_report_send($out);
    }

    /**
     * Export Team Performance Report to CSV
     */
    public function export_team() {
        $members = $this->Brand_model->get_member_performance($this->brand_id);
        $out = $this->_report_start('Instroom_Team_Report_' . date('Ymd') . '.xlsx');

        $header = ['No.', 'Full Name', 'Email Address', 'Role', 'Total Sourced', 'Ambassadors (Won)', 'Conversion Rate (%)', 'ROI (x)'];
        $cols = count($header);
        $this->_report_header($out, $header);

        $i = 1;
        foreach ((array)$members as $m) {
            $this->_csv_row($out, [
                $i++,
                $m->name ?? '',
                $m->email ?? '',
                ucfirst($m->role ?? ''),
                (int)($m->total_sourced ?? 0),
                (int)($m->ambassadors ?? 0),
                $this->_num($m->conversion_rate ?? 0),
                $this->_num($m->roi ?? 0),
            ], $cols);
        }

        $this->_report_send($out);
    }

    /**
     * Export Campaign ROI Report to CSV
     */
    public function export_campaigns() {
        $campaigns = $this->Campaign_model->get_all_campaigns();
        $out = $this->_report_start('Instroom_Campaign_Report_' . date('Ymd') . '.xlsx');

        $header = ['No.', 'Campaign Title', 'Status', 'Start Date', 'End Date', 'Duration (Days)', 'Assigned Influencers', 'Description'];
        $cols = count($header);
        $this->_report_header($out, $header);

        $i = 1;
        foreach ((array)$campaigns as $c) {
            $start = !empty($c->start_date) ? strtotime($c->start_date) : null;
            $end   = !empty($c->end_date) ? strtotime($c->end_date) : null;
            $this->_csv_row($out, [
                $i++,
                $c->title ?? '',
                ucfirst($c->status ?? ''),
                $this->_report_date($start),
                $this->_report_date($end),
                ($start && $end) ? max(0, (int)round(($end - $start) / 86400)) : '',
                count((array)$this->Campaign_model->get_campaign_influencers($c->id)),
                $c->description ?? '',
            ], $cols);
        }

        $this->_report_send($out);
    }

    /**
     * Export full influencer roster to CSV/Excel with optional filters:
     * - influencer_id: specific ID or 'all'
     * - month: month number (1-12) or 'YYYY-MM'
     * - day: day of month (1-31) or full date 'YYYY-MM-DD'
     * - year: 4-digit year
     */
    public function export_influencers_csv() {
        $filters = [];
        $inf_id = $this->input->get('influencer_id');
        $month  = $this->input->get('month');
        $day    = $this->input->get('day');
        $date   = $this->input->get('date');
        $year   = $this->input->get('year');

        if (!empty($inf_id) && $inf_id !== 'all') {
            $filters['influencer_id'] = $inf_id;
        }
        if (!empty($date)) {
            $filters['date'] = $date;
        } elseif (!empty($day)) {
            $filters['day'] = $day;
        }
        if (!empty($month)) {
            $filters['month'] = $month;
        }
        if (!empty($year)) {
            $filters['year'] = $year;
        }

        $list = $this->Influencer_model->get_influencers($filters);

        // Build descriptive filename based on filters
        $title_parts = ['Instroom_Influencer_Report'];
        if (!empty($inf_id) && $inf_id !== 'all') {
            $selected_inf = $this->Influencer_model->get_influencer($inf_id);
            if ($selected_inf) {
                $raw_s = !empty($selected_inf['username']) ? $selected_inf['username'] : (!empty($selected_inf['full_name']) ? $selected_inf['full_name'] : 'creator');
                $slug = preg_replace('/[^A-Za-z0-9_]+/', '_', $raw_s);
                $title_parts[] = $slug;
            }
        }
        if (!empty($month)) {
            $title_parts[] = 'M' . str_replace('-', '_', $month);
        }
        if (!empty($day)) {
            $title_parts[] = 'D' . str_replace('-', '_', $day);
        }
        $filename = implode('_', $title_parts) . '_' . date('Ymd') . '.xlsx';

        $out = $this->_report_start($filename);

        // Add filter metadata in header
        if (!empty($inf_id) && $inf_id !== 'all') {
            $inf_name = isset($selected_inf) && $selected_inf ? ($selected_inf['full_name'] ?: $selected_inf['username']) : '#' . $inf_id;
            $out->add_meta('Filtered Creator:', $inf_name);
        } else {
            $out->add_meta('Filtered Creator:', 'All Influencers');
        }

        if (!empty($month)) {
            $out->add_meta('Filter Month:', $month);
        }
        if (!empty($day)) {
            $out->add_meta('Filter Day/Date:', $day);
        }
        if (!empty($year)) {
            $out->add_meta('Filter Year:', $year);
        }

        $header = ['ID', 'Full Name', 'Username', 'Platform', 'Niche', 'Location', 'Email Address',
                   'Followers', 'Engagement Rate (%)', 'Pipeline Status', 'Date Added'];
        $cols = count($header);
        $this->_report_header($out, $header);

        foreach ((array)$list as $inf) {
            $this->_csv_row($out, [
                $inf->id,
                $inf->full_name ?? '',
                $inf->username ?? '',
                Influencer_model::normalize_platform($inf->platform ?? ''),
                $inf->niche ?? '',
                $inf->location ?? '',
                $inf->email ?? '',
                (int)($inf->followers ?? 0),
                $this->_num($inf->eng_rate ?? 0),
                $inf->pipeline_status ?? '',
                $this->_report_date($inf->created_at ?? ''),
            ], $cols);
        }

        $this->_report_send($out);
    }

    /**
     * Export a specific influencer's report to CSV (one header row + one data row)
     */
    public function export_influencer_csv($id = null) {
        if (!$id) {
            $id = $this->input->get('influencer_id') ?: $this->input->post('influencer_id');
        }

        if (empty($id) || $id === 'all') {
            return $this->export_influencers_csv();
        }

        $inf = $this->Influencer_model->get_influencer($id);
        if (!$inf) {
            show_error('Influencer record not found.', 404);
            return;
        }
        $inf = (array)$inf;

        $fee   = (float)($inf['fee'] ?? 0);
        $prod  = (float)($inf['product_cost'] ?? 0);
        $gmv   = (float)($inf['gmv'] ?? 0);
        $views = (float)($inf['avg_video_views'] ?? 0);
        $invest = $fee + $prod;

        $header = ['Report ID', 'Full Name', 'Username', 'Platform', 'Niche', 'Location', 'Email Address',
                   'Followers', 'Engagement Rate (%)', 'Avg Video Views', 'Pipeline Status',
                   'Creator Fee (USD)', 'Product Cost (USD)', 'Total Investment (USD)', 'Sales GMV (USD)',
                   'ROI (x)', 'CPM (USD)', 'Report Date'];
        $cols = count($header);

        $slug = preg_replace('/[^A-Za-z0-9_]+/', '_', $inf['username'] ?? ('creator_' . $id));
        $out = $this->_report_start('Instroom_Influencer_' . $slug . '_' . date('Ymd') . '.xlsx');
        $this->_report_header($out, $header);
        $this->_csv_row($out, [
            'INF-RPT-' . date('Ymd-') . sprintf('%04d', $id),
            $inf['full_name'] ?? '',
            $inf['username'] ?? '',
            Influencer_model::normalize_platform($inf['platform'] ?? ''),
            $inf['niche'] ?? '',
            $inf['location'] ?? '',
            $inf['email'] ?? '',
            (int)($inf['followers'] ?? 0),
            $this->_num($inf['eng_rate'] ?? 0),
            (int)$views,
            $inf['pipeline_status'] ?? '',
            $this->_num($fee),
            $this->_num($prod),
            $this->_num($invest),
            $this->_num($gmv),
            $invest > 0 ? $this->_num($gmv / $invest) : 0,
            $views > 0 ? $this->_num(($invest / $views) * 1000) : 0,
            date('F j, Y'),
        ], $cols);

        $this->_report_send($out);
    }

    /**
     * Export Performance Report to PDF
     */
    public function export_performance_pdf() {
        $data['analytics'] = $this->Influencer_model->get_analytics_stats();
        $data['report_date'] = date('F d, Y');
        $data['user_name'] = $this->session->userdata('name') ?: 'Manager';
        $data['brand_name'] = (!empty($this->brand_name) && $this->brand_name !== 'Armful Media') ? $this->brand_name : 'JBL';
        $this->load->view('reports/performance_pdf', $data);
    }

    /**
     * Export Team Performance Report to PDF
     */
    public function export_team_pdf() {
        $data['members'] = $this->Brand_model->get_member_performance($this->brand_id);
        $data['report_date'] = date('F d, Y');
        $data['user_name'] = $this->session->userdata('name') ?: 'Manager';
        $data['brand_name'] = (!empty($this->brand_name) && $this->brand_name !== 'Armful Media') ? $this->brand_name : 'JBL';
        $this->load->view('reports/team_pdf', $data);
    }

    /**
     * Export Campaign ROI Report to PDF
     */
    public function export_campaigns_pdf() {
        $campaigns = $this->Campaign_model->get_all_campaigns();
        foreach ($campaigns as &$c) {
            $c->assigned_count = count($this->Campaign_model->get_campaign_influencers($c->id));
        }
        $data['campaigns'] = $campaigns;
        $data['report_date'] = date('F d, Y');
        $data['user_name'] = $this->session->userdata('name') ?: 'Manager';
        $data['brand_name'] = (!empty($this->brand_name) && $this->brand_name !== 'Armful Media') ? $this->brand_name : 'JBL';
        $this->load->view('reports/campaigns_pdf', $data);
    }

    /**
     * Export Influencer Report to PDF with Month, Day, and Influencer filters.
     * Supports both single creator dossier and multi-creator filtered roster.
     */
    public function export_influencer_pdf($id = null) {
        if (!$id) {
            $id = $this->input->get('influencer_id') ?: $this->input->post('influencer_id');
        }

        $month = $this->input->get('month');
        $day   = $this->input->get('day');
        $date  = $this->input->get('date');
        $year  = $this->input->get('year');

        $month_names = [
            '1' => 'January', '2' => 'February', '3' => 'March', '4' => 'April',
            '5' => 'May', '6' => 'June', '7' => 'July', '8' => 'August',
            '9' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
        ];
        $month_label = '';
        if (!empty($month)) {
            if (isset($month_names[(string)(int)$month])) {
                $month_label = $month_names[(string)(int)$month] . (!empty($year) ? ' ' . $year : '');
            } else {
                $month_label = $month;
            }
        }
        $day_label = !empty($date) ? date('M d, Y', strtotime($date)) : (!empty($day) ? 'Day ' . $day : '');

        // If specific influencer selected
        if (!empty($id) && $id !== 'all') {
            $influencer = $this->Influencer_model->get_influencer($id);
            if (!$influencer) {
                show_error('Influencer record not found.', 404);
                return;
            }

            $data['influencer'] = $influencer;
            $data['report_date'] = date('F d, Y');
            $data['report_id'] = 'INF-RPT-' . date('Ymd-') . sprintf('%04d', $id);
            $data['user_name'] = $this->session->userdata('name') ?: 'Manager';
            $data['brand_name'] = (!empty($this->brand_name) && $this->brand_name !== 'Armful Media') ? $this->brand_name : 'JBL';
            $data['filter_month_label'] = $month_label;
            $data['filter_day_label'] = $day_label;
            $this->load->view('reports/influencer_pdf', $data);
            return;
        }

        // If 'all' or empty, render filtered multi-influencer report
        $filters = [];
        if (!empty($date))  $filters['date']  = $date;
        elseif (!empty($day)) $filters['day'] = $day;
        if (!empty($month)) $filters['month'] = $month;
        if (!empty($year))  $filters['year']  = $year;

        $influencers = $this->Influencer_model->get_influencers($filters);

        $data['influencers'] = $influencers;
        $data['report_date'] = date('F d, Y');
        $data['report_id']   = 'ROSTER-' . date('Ymd-His');
        $data['user_name']   = $this->session->userdata('name') ?: 'Manager';
        $data['brand_name']  = (!empty($this->brand_name) && $this->brand_name !== 'Armful Media') ? $this->brand_name : 'JBL';
        $data['filter_influencer_name'] = 'All Influencers';
        $data['filter_month_label']     = $month_label;
        $data['filter_day_label']       = $day_label;

        $this->load->view('reports/influencer_roster_pdf', $data);
    }

    public function seed_test_influencer() {
        $platforms = ['Instagram', 'TikTok', 'YouTube', 'Facebook'];
        $statuses = ['Sourced', 'Contacted', 'Replied', 'Posted', 'Campaign Completed'];
        
        for ($i = 0; $i < 3; $i++) {
            $p_name = "Test Influencer " . rand(100, 999);
            $platform = $platforms[array_rand($platforms)];
            $status = $statuses[array_rand($statuses)];
            
            // 1. Create Profile
            $profile_data = [
                'full_name' => $p_name . ' (AI)',
                'email' => strtolower(str_replace(' ', '_', $p_name)) . '@example.com',
                'location' => 'New York, USA'
            ];
            $this->db->insert('influencer_profiles', $profile_data);
            $profile_id = $this->db->insert_id();

            // 2. Create Account
            $account_data = [
                'profile_id' => $profile_id,
                'platform' => $platform,
                'username' => 'user_' . rand(1000, 9999),
                'status' => 'Active'
            ];
            $this->db->insert('influencer_accounts', $account_data);
            $account_id = $this->db->insert_id();

            // 3. Create Stats
            $stats_data = [
                'account_id' => $account_id,
                'followers' => rand(10000, 500000),
                'eng_rate' => rand(1, 10),
                'avg_video_views' => rand(5000, 100000),
                'gmv' => rand(500, 5000)
            ];
            $this->db->insert('influencer_account_stats', $stats_data);

            // 4. Create Collaboration
            $collab_data = [
                'brand_id' => $this->brand_id,
                'profile_id' => $profile_id,
                'account_id' => $account_id,
                'pipeline_status' => $status,
                'product_cost' => rand(20, 100),
                'created_by' => $this->user_id,
                'created_at' => date('Y-m-d H:i:s')
            ];
            $this->db->insert('collaborations', $collab_data);
        }

        $this->session->set_flashdata('success', '3 test influencers created! Analytics updated.');
        redirect('manager/performance');
    }

    public function seed_test_order() {
        $order_data = [
            'brand_id' => $this->brand_id,
            'product_name' => 'Instroom Limited Hoodie (Test)',
            'total_cost' => rand(20, 150),
            'status' => 'pending',
            'notes' => 'AI Generated test order for testing approval workflow.',
            'created_at' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('orders', $order_data);
        $this->session->set_flashdata('success', 'Test order created! Go to Orders & Discounts to review.');
        redirect('manager/orders_discounts');
    }

    public function sitemap() {
        $this->output
            ->set_content_type('application/xml')
            ->set_output(file_get_contents(FCPATH . 'assets/sitemaps/sitemap.xml'));
    }

    public function test_review($id = 24) {
        $this->load->model('Influencer_model');
        $this->load->model('Activity_log_model');
        
        echo "Updating pipeline status to Sourced...\n";
        $this->Influencer_model->update_pipeline_status($id, 'Sourced');
        
        echo "Fetching influencer details by id...\n";
        $influencer = $this->Influencer_model->get_influencer_by_id($id);
        var_dump($influencer);
        
        echo "Logging activity...\n";
        $this->Activity_log_model->log_activity('Approve Influencer', 'Approved ' . ($influencer ? $influencer->username : 'Unknown'));
        echo "Done!\n";
    }

    public function activity_logs() {
        $this->load->model('Activity_log_model');
        $this->load->library('pagination');

        $data['user_name'] = $this->session->userdata('name');
        $data['user_role'] = $this->session->userdata('role') ?? 'manager';
        $data['active_section'] = 'activity_logs';
        $data['page_title'] = 'Activity Logs';

        $config['base_url'] = site_url('manager/activity_logs');
        $config['total_rows'] = $this->db->count_all('activity_logs');
        $config['per_page'] = 20;

        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'per_page';

        // Styling for pagination
        $config['full_tag_open'] = '<div class="flex space-x-2">';
        $config['full_tag_close'] = '</div>';
        $config['attributes'] = array('class' => 'px-3 py-1 rounded-lg border border-gray-200 bg-white text-xs font-medium text-gray-600 hover:bg-gray-50 transition-colors');
        $config['cur_tag_open'] = '<span class="px-3 py-1 rounded-lg border border-brand-green bg-green-50 text-xs font-bold text-brand-green">';
        $config['cur_tag_close'] = '</span>';

        $page = ($this->input->get('per_page')) ? (int)$this->input->get('per_page') : 0;
        $this->pagination->initialize($config);

        $raw_logs = $this->Activity_log_model->get_logs($config['per_page'], $page);

        $logs = [];
        foreach ($raw_logs as $l) {
            $created = isset($l->created_at) ? $l->created_at : date('Y-m-d H:i:s');
            $logs[] = [
                'action'  => $l->action ?? '',
                'details' => $l->details ?? '',
                'user'    => $l->user_name ?? 'System',
                'time'    => $this->time_elapsed_string($created),
                'date'    => date('M d, Y H:i', strtotime($created)),
                'ip'      => $l->ip_address ?? '127.0.0.1'
            ];
        }

        $data['logs'] = $logs;
        $data['pagination'] = $this->pagination->create_links();
        $data['pagination_links'] = $data['pagination'];
        $data['total_rows'] = $config['total_rows'];

        $this->load->view('influencer/activity_logs', $data);
    }

    private function time_elapsed_string($datetime, $full = false) {
        if (empty($datetime)) return 'just now';
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $weeks = floor($diff->d / 7);
        $days = $diff->d - ($weeks * 7);

        $parts = [];
        if ($diff->y) $parts['y'] = $diff->y . ' year' . ($diff->y > 1 ? 's' : '');
        if ($diff->m) $parts['m'] = $diff->m . ' month' . ($diff->m > 1 ? 's' : '');
        if ($weeks)   $parts['w'] = $weeks . ' week' . ($weeks > 1 ? 's' : '');
        if ($days)    $parts['d'] = $days . ' day' . ($days > 1 ? 's' : '');
        if ($diff->h) $parts['h'] = $diff->h . ' hour' . ($diff->h > 1 ? 's' : '');
        if ($diff->i) $parts['i'] = $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
        if ($diff->s) $parts['s'] = $diff->s . ' second' . ($diff->s > 1 ? 's' : '');

        if (!$full) $parts = array_slice($parts, 0, 1);
        return !empty($parts) ? implode(', ', $parts) . ' ago' : 'just now';
    }
}
