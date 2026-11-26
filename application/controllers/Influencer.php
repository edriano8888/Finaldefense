<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_Pagination $pagination
 * @property Influencer_model $Influencer_model
 * @property Activity_log_model $Activity_log_model
 * @property Campaign_model $Campaign_model
 * @property User_model $User_model
 * @property Brand_model $Brand_model
 * @property CI_DB_query_builder $db
 * @property CI_Session $session
 * @property CI_Input $input
 * @property CI_Upload $upload
 * @property CI_Loader $load
 * @property CI_Output $output
 * @property Shopify $shopify
 */
class Influencer extends Brand_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Influencer_model');
        $this->load->model('Activity_log_model');
        $this->load->model('Campaign_model');
        $this->load->model('Tracking_model');
    }

    public function brand_details($id = null) {
        redirect('ambassadors/brand_details/' . ($id ?: ''));
    }

    public function index() {
        $data['user_name'] = $this->session->userdata('name');
        $data['user_role'] = $this->user_role;
        $data['brand_id'] = $this->brand_id;
        $data['brand_name'] = $this->brand_name;
        $data['user_brands'] = $this->User_model->get_user_brands($this->user_id);

        $brand_id = $this->brand_id;

        // ── 1. KPI Totals ────────────────────────────────────────────────────
        $this->load->model('Campaign_model');
        $data['active_campaigns_count'] = $this->Campaign_model->count_all_campaigns();
        $data['total_influencers'] = $this->Influencer_model->count_all_influencers();

        // Real avg engagement & total GMV from influencer_account_stats joined via collaborations
        $this->db->select('COALESCE(AVG(s.eng_rate),0) as avg_eng, COALESCE(SUM(s.gmv),0) as total_gmv, COALESCE(SUM(c.product_cost),0) as total_spend');
        $this->db->from('collaborations c');
        $this->db->join('influencer_account_stats s', 'c.account_id = s.account_id', 'left');
        if ($brand_id) $this->db->where('c.brand_id', $brand_id);
        $kpi = $this->db->get()->row();
        $data['avg_engagement'] = round((float)($kpi->avg_eng ?? 0), 2);
        $data['total_gmv']      = (float)($kpi->total_gmv  ?? 0);
        $data['total_spend']    = (float)($kpi->total_spend ?? 0);

        // Influencers added this month vs last month (for growth delta)
        $this->db->where('brand_id', $brand_id);
        $this->db->where('created_at >=', date('Y-m-01'));
        $data['new_this_month'] = $this->db->count_all_results('collaborations');

        $this->db->where('brand_id', $brand_id);
        $this->db->where('created_at >=', date('Y-m-01', strtotime('first day of last month')));
        $this->db->where('created_at <',  date('Y-m-01'));
        $data['new_last_month'] = $this->db->count_all_results('collaborations');

        // ── 2. Pipeline breakdown ────────────────────────────────────────────
        $this->db->select('pipeline_status, COUNT(*) as cnt');
        $this->db->from('collaborations');
        if ($brand_id) $this->db->where('brand_id', $brand_id);
        $this->db->group_by('pipeline_status');
        $pipeline_rows = $this->db->get()->result();

        $pipeline_map = [];
        foreach ($pipeline_rows as $r) {
            $pipeline_map[$r->pipeline_status] = (int)$r->cnt;
        }

        $all_stages = [
            'For Outreach'      => ['color' => '#6366f1', 'icon' => 'fa-bullhorn'],
            'Contacted'         => ['color' => '#3b82f6', 'icon' => 'fa-envelope'],
            'Replied'           => ['color' => '#8b5cf6', 'icon' => 'fa-reply'],
            'In - Progress'     => ['color' => '#f59e0b', 'icon' => 'fa-spinner'],
            'Not Interested'    => ['color' => '#ef4444', 'icon' => 'fa-times-circle'],
            'For Order Creation'=> ['color' => '#10b981', 'icon' => 'fa-shopping-bag'],
            'In-Transit'        => ['color' => '#06b6d4', 'icon' => 'fa-truck'],
            'Delivered'         => ['color' => '#84cc16', 'icon' => 'fa-box-open'],
            'Posted'            => ['color' => '#f43f5e', 'icon' => 'fa-camera'],
            'Campaign Completed'=> ['color' => '#1fae5b', 'icon' => 'fa-check-double'],
        ];
        $pipeline_stages = [];
        $pipeline_total  = array_sum($pipeline_map);
        foreach ($all_stages as $stage => $meta) {
            $cnt = $pipeline_map[$stage] ?? 0;
            $pipeline_stages[] = [
                'name'    => $stage,
                'count'   => $cnt,
                'color'   => $meta['color'],
                'icon'    => $meta['icon'],
                'percent' => $pipeline_total > 0 ? round($cnt / $pipeline_total * 100, 1) : 0,
            ];
        }
        $data['pipeline_stages'] = $pipeline_stages;
        $data['pipeline_total']  = $pipeline_total;

        // ── 3. Platform breakdown ────────────────────────────────────────────
        $this->db->select('a.platform, COUNT(*) as cnt, COALESCE(AVG(s.eng_rate),0) as avg_eng');
        $this->db->from('collaborations c');
        $this->db->join('influencer_accounts a', 'c.account_id = a.id', 'left');
        $this->db->join('influencer_account_stats s', 'a.id = s.account_id', 'left');
        if ($brand_id) $this->db->where('c.brand_id', $brand_id);
        $this->db->group_by('a.platform');
        $this->db->order_by('cnt', 'DESC');
        $platform_rows = $this->db->get()->result();

        $platform_total = array_sum(array_column((array)$platform_rows, 'cnt'));
        $platform_meta  = [
            'Instagram' => ['color' => '#e1306c', 'icon' => 'fa-instagram', 'fab' => true],
            'TikTok'    => ['color' => '#111827', 'icon' => 'fa-tiktok',    'fab' => true],
            'YouTube'   => ['color' => '#ff0000', 'icon' => 'fa-youtube',   'fab' => true],
            'Facebook'  => ['color' => '#1877f2', 'icon' => 'fa-facebook',  'fab' => true],
        ];
        $platforms = [];
        foreach ($platform_rows as $r) {
            $name = $r->platform ?: 'Other';
            $meta = $platform_meta[$name] ?? ['color' => '#9ca3af', 'icon' => 'fa-globe', 'fab' => false];
            $platforms[] = [
                'name'    => $name,
                'count'   => (int)$r->cnt,
                'avg_eng' => round((float)$r->avg_eng, 2),
                'percent' => $platform_total > 0 ? round($r->cnt / $platform_total * 100, 1) : 0,
                'color'   => $meta['color'],
                'icon'    => $meta['icon'],
                'fab'     => $meta['fab'],
            ];
        }
        $data['platforms']       = $platforms;
        $data['platform_total']  = $platform_total;

        // ── 4. Top influencers by followers ──────────────────────────────────
        $data['top_influencers'] = $this->Influencer_model->get_influencers([], 8);

        // ── 5. Recently added ────────────────────────────────────────────────
        $data['recent_influencers'] = $this->Influencer_model->get_recent_influencers(5);

        // ── 6. Activity feed ─────────────────────────────────────────────────
        $icon_map = [
            'Add Influencer'       => ['icon' => 'fa-user-plus',    'color' => 'green'],
            'Add Instagram'        => ['icon' => 'fa-instagram',    'color' => 'pink'],
            'Add TikTok'           => ['icon' => 'fa-tiktok',       'color' => 'gray'],
            'Edit Influencer'      => ['icon' => 'fa-pen',          'color' => 'blue'],
            'Delete Influencer'    => ['icon' => 'fa-trash',        'color' => 'red'],
            'Bulk Delete'          => ['icon' => 'fa-trash-alt',    'color' => 'red'],
            'CSV Import'           => ['icon' => 'fa-file-csv',     'color' => 'teal'],
            'Qualify Influencer'   => ['icon' => 'fa-check-circle', 'color' => 'green'],
            'Update Assessment'    => ['icon' => 'fa-clipboard',    'color' => 'purple'],
            'Update Detail'        => ['icon' => 'fa-edit',         'color' => 'blue'],
            'Bulk Pipeline Update' => ['icon' => 'fa-layer-group',  'color' => 'indigo'],
            'Login'                => ['icon' => 'fa-sign-in-alt',  'color' => 'gray'],
        ];
        $raw_logs  = $this->Activity_log_model->get_recent_logs(10);
        $activities = [];
        foreach ($raw_logs as $log) {
            $action  = $log['action'];
            $matched = null;
            foreach ($icon_map as $key => $meta) {
                if (stripos($action, $key) !== false) { $matched = $meta; break; }
            }
            if (!$matched) $matched = ['icon' => 'fa-circle', 'color' => 'gray'];
            $activities[] = [
                'title'       => $action,
                'description' => $log['details'] ?: ('Action by ' . $log['user_name']),
                'user'        => $log['user_name'],
                'time'        => $this->time_elapsed_string($log['created_at']),
                'date'        => date('M j, g:ia', strtotime($log['created_at'])),
                'icon'        => $matched['icon'],
                'color'       => $matched['color'],
            ];
        }
        $data['activities'] = $activities;

        $this->load->view('influencer/dashboard', $data);
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

    public function dashboard() {
        $this->index();
    }
    
    public function manage() {
        $data['user_name'] = $this->session->userdata('name');
        $data['user_role'] = $this->session->userdata('role') ?? 'user';
        
        $filters = array(
            'search' => $this->input->get('search'),
            'platform' => $this->input->get('platform'),
            'niche' => $this->input->get('niche'),
            'status' => $this->input->get('status')
        );

        // Rows per page (default 100 like Excel view)
        $limit_param = (int)$this->input->get('limit');
        $per_page = ($limit_param && in_array($limit_param, [10, 25, 50, 100], true)) ? $limit_param : 100;

        // Pagination Config
        $this->load->library('pagination');
        $config['base_url'] = base_url('influencer/manage');
        $config['total_rows'] = $this->Influencer_model->count_influencers($filters);
        $config['per_page'] = $per_page;
        $config['reuse_query_string'] = TRUE;
        $config['page_query_string'] = TRUE;
        
        $config['full_tag_open'] = '<div class="flex items-center space-x-1.5">';
        $config['full_tag_close'] = '</div>';
        $config['attributes'] = array('class' => 'px-2.5 py-1 rounded-lg border border-gray-200 bg-white text-xs font-semibold text-gray-600 hover:bg-gray-50');
        $config['cur_tag_open'] = '<span class="px-2.5 py-1 rounded-lg border border-brand-green bg-emerald-50 text-xs font-bold text-brand-deep">';
        $config['cur_tag_close'] = '</span>';
        
        $this->pagination->initialize($config);

        $page = ($this->input->get('per_page')) ? (int)$this->input->get('per_page') : 0;
        
        $data['influencers'] = $this->Influencer_model->get_influencers($filters, $config['per_page'], $page);
        $data['pagination'] = $this->pagination->create_links();
        $data['total_rows'] = $config['total_rows'];
        $data['per_page_limit'] = $per_page;
        $data['current_page'] = ($config['per_page'] > 0) ? floor($page / $config['per_page']) + 1 : 1;
        $data['total_pages'] = ($config['per_page'] > 0 && $config['total_rows'] > 0) ? (int)ceil($config['total_rows'] / $config['per_page']) : 1;
        $data['active_count'] = $this->Influencer_model->count_influencers(['status' => 'Active']);
        $data['start_index'] = ($config['total_rows'] > 0) ? $page + 1 : 0;
        $data['end_index'] = min($page + $config['per_page'], $config['total_rows']);
        
        $this->load->model('Campaign_model');
        $data['campaigns'] = $this->Campaign_model->get_all_campaigns();
        
        $this->load->model('Email_model');
        $data['email_drips'] = $this->Email_model->get_email_drips($this->brand_id);

        // Custom columns for this brand
        $custom_columns = $this->db->where('brand_id', $this->brand_id)->order_by('id', 'ASC')->get('custom_columns')->result();
        $data['custom_columns'] = $custom_columns;
        
        $custom_values = [];
        if (!empty($custom_columns)) {
            $col_ids = array_map(function($c) { return (int)$c->id; }, $custom_columns);
            $vals = $this->db->where('brand_id', $this->brand_id)->where_in('column_id', $col_ids)->get('custom_column_values')->result();
            foreach ($vals as $v) {
                $custom_values[$v->column_id . '_' . $v->collaboration_id] = $v->value;
            }
        }
        $data['custom_values'] = $custom_values;
        
        $this->load->view('influencer/manage', $data);
    }

    public function pipeline() {
        $data['user_name'] = $this->session->userdata('name');
        $data['user_role'] = $this->session->userdata('role') ?? 'user';
        $this->load->view('influencer/pipeline', $data);
    }

    public function fetch_pipeline() {
        $pipeline_stages = ['For Outreach', 'Contacted', 'Replied', 'In - Progress', 'Not Interested'];
        $influencers = [];
        
        try {
            $influencers = $this->Influencer_model->get_influencers_by_pipeline_status($pipeline_stages);
        } catch (Exception $e) {
            log_message('error', 'Pipeline fetch error: ' . $e->getMessage());
        }
        
        $pipeline = [
            'For Outreach' => [],
            'Contacted' => [],
            'Replied' => [],
            'In - Progress' => [],
            'Not Interested' => []
        ];
        
        foreach ($influencers as $inf) {
            $status = isset($inf->pipeline_status) ? $inf->pipeline_status : '';
            
            if (array_key_exists($status, $pipeline)) {
                $pipeline[$status][] = $inf;
            }
        }
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($pipeline));
    }

    public function analytics() {
        $data['user_name'] = $this->session->userdata('name');
        $data['user_role'] = $this->session->userdata('role') ?? 'user';
        $data['stats'] = $this->Influencer_model->get_analytics_stats();
        $data['growth_trend'] = $this->Influencer_model->get_influencer_growth_trend(30);
        $data['platforms'] = ['Facebook', 'Instagram', 'TikTok', 'YouTube'];
        $data['all_influencers'] = $this->Influencer_model->get_influencers();
        $this->load->view('influencer/analytics', $data);
    }

    public function export_analytics_pdf() {
        $this->load->model('Brand_model');
        $data['user_name'] = $this->session->userdata('name') ?: 'Armful Manager';
        $data['user_role'] = $this->session->userdata('role') ?? 'user';
        $data['stats'] = $this->Influencer_model->get_analytics_stats();
        $data['growth_trend'] = $this->Influencer_model->get_influencer_growth_trend(30);
        $data['top_influencers'] = $this->Influencer_model->get_influencers(['limit' => 15]);
        
        $active_brand_id = (int)$this->session->userdata('brand_id');
        $active_brand = $active_brand_id ? $this->Brand_model->get_brand($active_brand_id) : null;
        $data['active_brand_name'] = $active_brand ? $active_brand->name : 'Client Brand';
        $data['report_date'] = date('F j, Y');
        $data['report_id'] = 'INS-RPT-' . date('Ymd-His');

        $this->load->view('reports/analytics_pdf', $data);
    }

    /**
     * Export Analytics to CSV — readable, sectioned report.
     * Layout (every row padded to the same column count so Excel columns stay aligned):
     *   Report header -> 1. Key Summary -> 2. Pipeline Funnel -> 3. Platform Performance
     *   -> 4. Top Influencers -> Notes / How metrics are computed
     */
    public function export_analytics_csv() {
        error_reporting(0);
        @ini_set('display_errors', '0');

        $this->load->model('Brand_model');
        $stats = $this->Influencer_model->get_analytics_stats();
        $top   = $this->Influencer_model->get_influencers(['limit' => 15]);

        $brand_id = (int)$this->session->userdata('brand_id');
        $brand    = $brand_id ? $this->Brand_model->get_brand($brand_id) : null;
        $brand_name  = $brand ? $brand->name : 'Client Brand';
        $prepared_by = $this->session->userdata('name') ?: 'Manager';

        // ---------- helpers ----------
        $money = function ($v) { return '$' . number_format((float)$v, 2); };
        $int   = function ($v) { return number_format((float)$v); };
        $pctv  = function ($part, $whole) { return $whole > 0 ? ($part / $whole) * 100 : 0; };
        $pct   = function ($v) { return number_format((float)$v, 1) . '%'; };

        // Exported as .xlsx (not CSV) so every column is auto-sized to fit its
        // header and values - CSV files cannot store column widths.
        $this->load->library('Xlsx_writer');
        $x = new Xlsx_writer(['sheet_name' => 'Analytics Report']);

        $w = function (array $row = []) use ($x) {
            empty($row) ? $x->add_blank() : $x->add_row($row);
        };
        $h = function (array $row) use ($x) { $x->add_header($row); };
        $section = function ($title) use ($x) {
            $x->add_blank();
            $x->add_section($title);
        };

        // ---------- totals ----------
        $total     = (float)($stats['total_influencers'] ?? 0);
        $gmv       = (float)($stats['sales_amount'] ?? 0);
        $spend     = (float)($stats['product_expenditure'] ?? 0);
        $views     = (float)($stats['total_views'] ?? 0);
        $roi       = $spend > 0 ? $gmv / $spend : 0;

        // ---------- REPORT HEADER ----------
        $x->add_title('INSTROOM - INFLUENCER ANALYTICS REPORT');
        $x->add_meta('Brand Name:', $brand_name);
        $x->add_meta('Generated On:', date('F j, Y - g:i A'));
        $x->add_meta('Prepared By:', $prepared_by);
        $x->add_meta('Report ID:', 'INS-ANL-' . date('Ymd-His'));

        // ---------- 1. KEY SUMMARY ----------
        $section('1. KEY PERFORMANCE SUMMARY');
        $h(['Metric Name', 'Metric Value', 'Description & Notes']);
        $w(['Total Influencers', $int($total), 'Number of creators currently in your roster']);
        $w(['Total Sales (GMV)', $money($gmv), 'Total revenue generated through influencer sales']);
        $w(['Total Product Spend', $money($spend), 'Cost of products sent to influencers']);
        $w(['Return on Spend (ROI)', number_format($roi, 2) . 'x', 'Sales earned for every $1 spent (higher is better)']);
        $w(['Total Video Views', $int($views), 'Combined average views across all creators']);
        $w(['Cost per 1,000 Views (CPM)', $money($stats['cpm'] ?? 0), 'Spend needed to reach 1,000 views (lower is better)']);
        $w(['Average Engagement Rate', $pct($stats['avg_eng_rate'] ?? 0), 'Average audience interaction (likes/comments vs followers)']);

        // ---------- 2. PIPELINE FUNNEL ----------
        $section('2. OUTREACH PIPELINE FUNNEL');
        $h(['Funnel Stage', 'Creator Count', '% of Total Pipeline', 'Stage Definition']);
        $funnel = [
            ['Total Outreach',          $total,                              'All influencers added to the pipeline'],
            ['Responded',               $stats['responded_count'] ?? 0,      'Influencers who replied to outreach'],
            ['Won / Ambassadors',       $stats['won_count'] ?? 0,            'Agreed to collaborate (order created or later)'],
            ['Content Pending',         $stats['content_pending_count'] ?? 0, 'Product sent; waiting for content to be posted'],
            ['Posted Content',          $stats['posted_count'] ?? 0,         'Already published content for the brand'],
            ['Not Interested',          $stats['not_interested_count'] ?? 0, 'Declined the collaboration'],
        ];
        foreach ($funnel as $f) {
            $w([$f[0], $int($f[1]), $pct($pctv($f[1], $total)), $f[2]]);
        }

        // ---------- 3. PLATFORM PERFORMANCE ----------
        $section('3. PERFORMANCE BY PLATFORM');
        $h(['Social Platform', 'Total Influencers', 'Responded', 'Won', 'Posted', 'Response Rate', 'Win Rate', 'Sales (GMV)', 'Share of Total Sales']);
        $t = ['total' => 0, 'responded' => 0, 'won' => 0, 'posted' => 0, 'sales_amount' => 0];
        foreach ((array)($stats['platform_details'] ?? []) as $d) {
            foreach ($t as $k => $_) { $t[$k] += (float)($d[$k] ?? 0); }
        }
        $prow = function ($label, $d, $kind = 'data') use ($x, $int, $pct, $pctv, $money, $t) {
            $n = (float)($d['total'] ?? 0);
            $x->add_row([
                $label,
                $int($n),
                $int($d['responded'] ?? 0),
                $int($d['won'] ?? 0),
                $int($d['posted'] ?? 0),
                $pct($pctv($d['responded'] ?? 0, $n)),
                $pct($pctv($d['won'] ?? 0, $n)),
                $money($d['sales_amount'] ?? 0),
                $pct($pctv($d['sales_amount'] ?? 0, $t['sales_amount'])),
            ], $kind);
        };
        foreach ((array)($stats['platform_details'] ?? []) as $platform => $d) {
            $prow($platform, $d);
        }
        $prow('TOTAL PLATFORMS', $t, 'total');

        // ---------- 4. TOP INFLUENCERS ----------
        $section('4. TOP INFLUENCERS (BY FOLLOWERS)');
        $h(['Rank', 'Full Name', 'Username', 'Platform', 'Followers', 'Engagement Rate', 'Niche / Category', 'Location', 'Pipeline Status']);
        $top = (array)$top;
        usort($top, function ($a, $b) { return (int)($b->followers ?? 0) <=> (int)($a->followers ?? 0); });
        if (empty($top)) {
            $w(['-', 'No influencers found']);
        } else {
            $rank = 1;
            foreach ($top as $inf) {
                $w([
                    $rank++,
                    !empty($inf->full_name) ? $inf->full_name : '-',
                    !empty($inf->username) ? '@' . ltrim($inf->username, '@') : '-',
                    Influencer_model::normalize_platform($inf->platform ?? '') ?: '-',
                    $int($inf->followers ?? 0),
                    $pct($inf->eng_rate ?? 0),
                    !empty($inf->niche) ? $inf->niche : '-',
                    !empty($inf->location) ? $inf->location : '-',
                    !empty($inf->pipeline_status) ? $inf->pipeline_status : '-',
                ]);
            }
        }

        // ---------- 5. METRIC DEFINITIONS ----------
        $section('5. METRIC DEFINITIONS & CALCULATION NOTES');
        $h(['Metric Title', 'Formula / Description']);
        $w(['Response Rate', 'Responded Creators / Total Influencers x 100']);
        $w(['Win Rate', 'Won Creators / Total Influencers x 100']);
        $w(['Share of Sales', 'Platform Sales GMV / Total Sales GMV x 100']);
        $w(['ROI (Return on Investment)', 'Total Sales GMV / Total Product & Creator Spend']);
        $w(['CPM (Cost per Mille)', 'Total Product Spend / Total Video Views x 1,000']);
        $w([]);
        $w(['Notice:', 'Data is generated live from the Instroom database. Confidential - Internal Use Only.']);

        $x->send('Instroom_Analytics_Report_' . date('Y-m-d'));
    }

    public function export_influencer_pdf($id = null) {
        if (!$id) {
            $id = $this->input->get('influencer_id') ?: $this->input->post('influencer_id');
        }

        if (!$id) {
            $all = $this->Influencer_model->get_influencers();
            if (!empty($all)) {
                $id = $all[0]->id;
            }
        }

        $influencer = $this->Influencer_model->get_influencer($id);
        if (!$influencer) {
            show_error('Influencer record not found.', 404);
            return;
        }

        $data['influencer'] = $influencer;
        $data['report_date'] = date('F d, Y');
        $data['report_id'] = 'INF-RPT-' . date('Ymd-') . sprintf('%04d', $id);
        $data['user_name'] = $this->session->userdata('name') ?: 'User';
        
        $this->load->view('reports/influencer_pdf', $data);
    }

    public function export_single_influencer_csv($id = null) {
        if (!$id) {
            $id = $this->input->get('influencer_id') ?: $this->input->post('influencer_id');
        }

        $influencer = $id ? $this->Influencer_model->get_influencer($id) : null;
        if (!$influencer) {
            show_error('Influencer record not found.', 404);
            return;
        }

        $inf = (array)$influencer;
        $slug = preg_replace('/[^A-Za-z0-9_]+/', '_', $inf['username'] ?? ('creator_' . $id));
        
        $this->load->library('Xlsx_writer');
        $x = new Xlsx_writer(['sheet_name' => 'Influencer Report']);

        $added = (!empty($inf['created_at']) && strpos($inf['created_at'], '0000-00-00') !== 0)
            ? date('F j, Y', strtotime($inf['created_at'])) : '-';

        $x->add_title('INSTROOM - INDIVIDUAL INFLUENCER PERFORMANCE REPORT');
        $x->add_meta('Report ID:', 'INF-RPT-' . date('Ymd-') . sprintf('%04d', $id));
        $x->add_meta('Generated On:', date('F j, Y - g:i A'));
        $x->add_blank();

        $x->add_header(['Field Name', 'Details & Performance Data']);
        $x->freeze_header($x->row_count());
        $x->add_row(['Full Name', $inf['full_name'] ?? '-']);
        $x->add_row(['Username', !empty($inf['username']) ? '@' . ltrim($inf['username'], '@') : '-']);
        $x->add_row(['Platform', Influencer_model::normalize_platform($inf['platform'] ?? '')]);
        $x->add_row(['Niche / Category', $inf['niche'] ?? '-']);
        $x->add_row(['Location', $inf['location'] ?? '-']);
        $x->add_row(['Email Address', $inf['email'] ?? '-']);
        $x->add_row(['Followers Count', (int)($inf['followers'] ?? 0)]);
        $x->add_row(['Engagement Rate', number_format((float)($inf['eng_rate'] ?? 0), 2) . '%']);
        $x->add_row(['Pipeline Status', $inf['pipeline_status'] ?? '-']);
        $x->add_row(['Date Added', $added]);

        $x->send('Instroom_Influencer_Report_' . $slug . '_' . date('Ymd'));
    }

    public function sitemap() {
        $this->output
            ->set_content_type('application/xml')
            ->set_output(file_get_contents(FCPATH . 'assets/sitemaps/sitemap.xml'));
    }

    public function closed_collaborations() {
        $data['user_name'] = $this->session->userdata('name');
        $data['user_role'] = $this->session->userdata('role') ?? 'user';
        
        // Shopify Integration Simulation
        // In a real scenario, this would use Shopify API Webhooks to listen for order updates.
        // For this prototype, we simulate checking an order status based on 'order_number'.
        $data['shopify_status'] = [
            'connected' => true,
            'store_url' => 'shop.instroom.com',
            'last_sync' => date('Y-m-d H:i:s')
        ];
        
        $this->load->view('influencer/closed_collaborations', $data);
    }

    public function fetch_closed_collaborations() {
        $closed_stages = ['For Order Creation', 'In-Transit', 'Delivered', 'Posted', 'Campaign Completed'];
        $influencers = [];
        
        try {
            $influencers = $this->Influencer_model->get_influencers_by_pipeline_status($closed_stages);
            
            // Load Shopify Library
            $this->load->library('shopify');
        } catch (Exception $e) {
            log_message('error', 'Closed collaborations fetch error: ' . $e->getMessage());
        }

        $pipeline = [
            'For Order Creation' => [],
            'In-Transit' => [],
            'Delivered' => [],
            'Posted' => [],
            'Campaign Completed' => []
        ];
        
        foreach ($influencers as $inf) {
            $status = isset($inf->pipeline_status) ? $inf->pipeline_status : '';
            
            // Check for real Shopify order status if an order number exists
            if (!empty($inf->order_number)) {
                try {
                    // Fetch order details from Shopify API
                    $order_data = $this->shopify->get_order($inf->order_number); // Assuming order_number is the ID, or search by name
                    
                    if ($order_data && isset($order_data['order'])) {
                        $inf->shipping_status = strtoupper($order_data['order']['fulfillment_status'] ?? 'UNFULFILLED');
                    } else {
                        $inf->shipping_status = 'NOT FOUND';
                    }
                } catch (Exception $e) {
                    $inf->shipping_status = 'API ERROR';
                    log_message('error', 'Shopify API error: ' . $e->getMessage());
                }
            }

            if (array_key_exists($status, $pipeline)) {
                $pipeline[$status][] = $inf;
            }
        }
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($pipeline));
    }

    public function activity_logs() {
        $data['user_name'] = $this->session->userdata('name');
        $data['user_role'] = $this->user_role;
        $data['active_section'] = 'activity_logs';
        
        // Pagination for logs
        $this->load->library('pagination');
        $config['base_url'] = base_url('influencer/activity_logs');
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
        
        $this->pagination->initialize($config);
        
        $page = ($this->input->get('per_page')) ? $this->input->get('per_page') : 0;
        $raw_logs = $this->Activity_log_model->get_logs($config['per_page'], $page);
        
        $logs = [];
        foreach($raw_logs as $log) {
            $logs[] = [
                'action' => $log->action,
                'details' => $log->details,
                'user' => $log->user_name,
                'time' => $this->time_elapsed_string($log->created_at),
                'date' => date('M d, Y H:i', strtotime($log->created_at)),
                'ip' => $log->ip_address
            ];
        }
        
        $data['logs'] = $logs;
        $data['pagination'] = $this->pagination->create_links();
        $data['total_rows'] = $config['total_rows'];
        
        $this->load->view('influencer/activity_logs', $data);
    }

    public function update_pipeline_status() {
        $this->update_pipeline_stage();
    }

    public function update_pipeline_stage() {
        $id = $this->input->post('id');
        $new_stage = $this->input->post('stage') ?: $this->input->post('status') ?: $this->input->post('pipeline_status');
        
        if ($id && $new_stage) {
            // Ensure the stage is valid
            $valid_stages = [
                'For Outreach', 'Contacted', 'Replied', 'In - Progress', 'Not Interested',
                'For Order Creation', 'In-Transit', 'Delivered', 'Posted', 'Campaign Completed'
            ];
            if (in_array($new_stage, $valid_stages)) {
                $update_data = ['pipeline_status' => $new_stage];
                
                // Sync status based on pipeline stage
                if ($new_stage == 'Not Interested') {
                    $update_data['status'] = 'Inactive';
                } else {
                    $update_data['status'] = 'Active';
                }

                if ($this->Influencer_model->update_influencer($id, $update_data)) {
                    $this->output
                        ->set_content_type('application/json')
                        ->set_output(json_encode(['status' => 'success']));
                } else {
                    $this->output
                        ->set_content_type('application/json')
                        ->set_output(json_encode(['status' => 'error', 'message' => 'Database update failed']));
                }
            } else {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid stage: ' . $new_stage]));
            }
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid data', 'debug' => $_POST]));
        }
    }

    public function qualify($id) {
        // Move to pipeline by setting pipeline_status
        $result = $this->Influencer_model->update_influencer($id, [
            'status' => 'Active',
            'pipeline_status' => 'For Outreach',
            'assessment' => 'Qualified'
        ]);
        
        if ($result) {
            $this->Activity_log_model->log_activity(
                'Qualify Influencer',
                "Qualified influencer ID: $id",
                $this->session->userdata('user_id'),
                $this->session->userdata('name')
            );
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'message' => 'Influencer qualified and moved to Pipeline!']));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Failed to qualify influencer']));
        }
    }

    public function delete($id) {
        $influencer = $this->Influencer_model->get_influencer_by_id($id);
        $username = $influencer ? $influencer->username : 'Unknown';
        
        $this->Influencer_model->delete_influencer($id);
        
        $this->Activity_log_model->log_activity(
            'Delete Influencer',
            "Deleted influencer: $username (ID: $id)",
            $this->session->userdata('user_id'),
            $this->session->userdata('name')
        );

        $this->session->set_flashdata('success', 'Influencer deleted successfully!');
        if ($this->input->is_ajax_request()) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success']));
            return;
        }
        redirect('influencer');
    }

    public function delete_selected() {
        $ids = $this->input->post('ids');
        if (!empty($ids)) {
            if (is_string($ids)) {
                $ids = explode(',', $ids);
            }
            $ok = $this->Influencer_model->delete_influencers_by_ids($ids);
            
            $count = count($ids);
            if ($ok) {
                $this->Activity_log_model->log_activity(
                    'Bulk Delete',
                    "Deleted $count influencers via bulk action",
                    $this->session->userdata('user_id'),
                    $this->session->userdata('name')
                );
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'success']));
                return;
            }

            $err = $this->db->error();
            $msg = isset($err['message']) && $err['message'] ? $err['message'] : 'Delete failed';
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => $msg]));
            return;
        }
        $this->output
            ->set_status_header(400)
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'error', 'message' => 'No items selected']));
    }

    public function bulk_approve() {
        $ids = $this->input->post('ids');
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }
        $clean_ids = [];
        if (is_array($ids)) {
            foreach ($ids as $id) {
                $iid = (int)$id;
                if ($iid > 0) $clean_ids[] = $iid;
            }
        }
        if (!empty($clean_ids)) {
            $today = date('Y-m-d');
            $this->db->where_in('id', $clean_ids);
            $this->db->update('collaborations', [
                'assessment' => 'Qualified',
                'pipeline_status' => 'For Outreach',
                'date_reviewed' => $today
            ]);
            $this->Activity_log_model->log_activity(
                'Bulk Approve',
                'Approved ' . count($clean_ids) . ' influencers via bulk action',
                $this->session->userdata('user_id'),
                $this->session->userdata('name')
            );
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'message' => count($clean_ids) . ' influencers approved successfully'
                ]));
            return;
        }
        $this->output
            ->set_status_header(400)
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'error', 'message' => 'No items selected']));
    }

    public function add_row_ajax() {
        $brand_id = (int)$this->brand_id ?: (int)$this->session->userdata('brand_id');
        if ($brand_id <= 0) {
            $first_b = $this->db->get('brands')->row();
            $brand_id = $first_b ? (int)$first_b->id : 1;
        }
        $user_id = (int)$this->session->userdata('user_id') ?: 1;
        $count = (int)$this->input->post('count');
        if ($count < 1) $count = 1;
        if ($count > 50) $count = 50;

        $created_ids = [];
        $this->db->trans_start();
        for ($i = 0; $i < $count; $i++) {
            $this->db->insert('influencer_profiles', [
                'full_name' => '',
                'first_name' => '',
                'last_name' => '',
                'location' => '',
                'niche' => 'General',
                'gender' => '',
                'contact_info' => '',
                'notes' => '',
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $profile_id = $this->db->insert_id();

            $this->db->insert('influencer_accounts', [
                'profile_id' => $profile_id,
                'platform' => 'Instagram',
                'username' => '',
                'status' => 'Active',
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $account_id = $this->db->insert_id();

            $this->db->insert('influencer_account_stats', [
                'account_id' => $account_id,
                'followers' => 0,
                'eng_rate' => 0.00
            ]);

            $this->db->insert('collaborations', [
                'brand_id' => $brand_id,
                'profile_id' => $profile_id,
                'account_id' => $account_id,
                'pipeline_status' => 'For Outreach',
                'assessment' => 'Pending',
                'created_by' => $user_id,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $created_ids[] = $this->db->insert_id();
        }
        $this->db->trans_complete();

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'count' => count($created_ids),
                'collab_ids' => $created_ids,
                'id' => !empty($created_ids) ? $created_ids[0] : null,
                'csrf_hash' => $this->security->get_csrf_hash(),
                'message' => count($created_ids) . ' row(s) added successfully'
            ]));
    }

    public function add_custom_column_ajax() {
        $brand_id = (int)$this->brand_id;
        $name = trim($this->input->post('column_name'));
        $desc = trim($this->input->post('description'));
        $field_type = trim($this->input->post('field_type') ?: 'Text');
        $section = trim($this->input->post('section') ?: 'influencer');

        if (empty($name)) {
            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Column name is required']));
            return;
        }

        $this->db->insert('custom_columns', [
            'brand_id' => $brand_id,
            'column_name' => $name,
            'description' => $desc,
            'field_type' => $field_type,
            'section' => $section,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        $col_id = $this->db->insert_id();

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'column' => [
                    'id' => $col_id,
                    'column_name' => $name,
                    'description' => $desc,
                    'field_type' => $field_type,
                    'section' => $section
                ]
            ]));
    }

    public function delete_custom_column_ajax() {
        $brand_id = (int)$this->brand_id;
        $col_id = (int)$this->input->post('column_id');

        if ($col_id > 0) {
            $this->db->where('id', $col_id)->where('brand_id', $brand_id)->delete('custom_columns');
            $this->db->where('column_id', $col_id)->where('brand_id', $brand_id)->delete('custom_column_values');
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success']));
            return;
        }

        $this->output
            ->set_status_header(400)
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid column ID']));
    }

    public function save_custom_value_ajax() {
        $brand_id = (int)$this->brand_id;
        $col_id = (int)$this->input->post('column_id');
        $collab_id = (int)$this->input->post('collaboration_id');
        $val = $this->input->post('value');

        if ($col_id > 0 && $collab_id > 0) {
            $existing = $this->db->where('column_id', $col_id)
                                ->where('collaboration_id', $collab_id)
                                ->get('custom_column_values')
                                ->row();
            if ($existing) {
                $this->db->where('id', $existing->id)->update('custom_column_values', [
                    'value' => $val,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            } else {
                $this->db->insert('custom_column_values', [
                    'brand_id' => $brand_id,
                    'column_id' => $col_id,
                    'collaboration_id' => $collab_id,
                    'value' => $val,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success']));
            return;
        }

        $this->output
            ->set_status_header(400)
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid parameters']));
    }

    public function bulk_add_pipeline() {
        $ids = $this->input->post('ids');
        $stage = $this->input->post('stage') ? $this->input->post('stage') : 'For Outreach';
        
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }

        $clean_ids = [];
        if (is_array($ids)) {
            foreach ($ids as $id) {
                $iid = (int)$id;
                if ($iid > 0) {
                    $clean_ids[] = $iid;
                }
            }
        }
        $clean_ids = array_values(array_unique($clean_ids));

        $allowed_stages = [
            'For Outreach', 'Contacted', 'Replied', 'In - Progress', 'Not Interested',
            'For Order Creation', 'In-Transit', 'Delivered', 'Posted', 'Campaign Completed'
        ];
        if (!in_array($stage, $allowed_stages, true)) {
            $stage = 'For Outreach';
        }

        if (empty($clean_ids)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'No items selected']));
            return;
        }

        $ok = $this->Influencer_model->update_influencers_by_ids($clean_ids, [
            'pipeline_status' => $stage,
            'assessment' => 'Qualified'
        ]);
        
        $count = count($clean_ids);
        $this->Activity_log_model->log_activity(
            'Bulk Pipeline Update',
            "Moved $count influencers to pipeline stage: $stage",
            $this->session->userdata('user_id'),
            $this->session->userdata('name')
        );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => $ok ? 'success' : 'error',
                'count' => $count,
                'stage' => $stage,
                'message' => $ok ? 'Influencers moved to pipeline.' : 'Failed to move influencers.'
            ]));
    }

    public function delete_all() {
        $this->Influencer_model->delete_all_influencers();
        
        $this->Activity_log_model->log_activity(
            'Delete All',
            "Deleted ALL influencers from the database",
            $this->session->userdata('user_id'),
            $this->session->userdata('name')
        );
        
        $this->session->set_flashdata('success', 'All influencers deleted successfully!');
        redirect('influencer');
    }

    public function get_data($id = null) {
        if (!$id) {
            $id = $this->input->get('id') ?: $this->input->post('id');
        }
        if (!$id) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(null));
            return;
        }
        $data = $this->Influencer_model->get_influencer_by_id($id);
        
        // Enhance data with default values if needed
        if ($data) {
            // Map existing fields to expected frontend names if they differ
            if (!isset($data->engagement_rate) && isset($data->eng_rate)) {
                $data->engagement_rate = $data->eng_rate;
            }
            if (!isset($data->date_added) && isset($data->created_at)) {
                $data->date_added = $data->created_at;
            }
            if (isset($data->notes) && preg_match('/^(Discovered via|Direct Lookup via|Verified TikTok Creator|Existing Influencer in Database)/i', trim((string)$data->notes))) {
                $data->notes = '';
            }
        }
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    public function update_notes_ajax() {
        $id = (int)$this->input->post('id');
        if ($id <= 0) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid influencer ID']));
            return;
        }

        $notes = trim((string)$this->input->post('notes'));
        if (preg_match('/^(Discovered via|Direct Lookup via|Verified TikTok Creator|Existing Influencer in Database)/i', $notes)) {
            $notes = '';
        }

        $result = $this->Influencer_model->update_influencer($id, ['notes' => $notes]);
        if ($result) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'notes' => $notes]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Failed to save note']));
        }
    }

    public function update_detail_ajax() {
        $id = $this->input->post('id');
        
        if (!$id) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid ID']));
            return;
        }

        $data = [];
        
        // Direct mapping fields
        $fields = [
            'username', 'followers', 'location', 'niche', 'gender', 'email', 'full_name',
            'avg_video_views', 'gmv', 'est_post_rate', 'first_name', 'last_name',
            'platform', 'ig_username', 'notes', 'contact_number', 'product_name',
            'order_number', 'product_cost', 'discount_code', 'affiliate_link',
            'shipping_address', 'tracking_link', 'assessment', 'date_reviewed', 'pipeline_status',
            'campaign_type', 'status', 'deliverables', 'fee', 'deadline'
        ];

        foreach ($fields as $field) {
            if ($this->input->post($field) !== null) {
                $data[$field] = $this->input->post($field);
            }
        }

        // Mapped fields
        if ($this->input->post('engagement_rate') !== null) {
            $data['eng_rate'] = $this->input->post('engagement_rate');
        }

        // Logic: If Assessment is Qualified / Approved, set Pipeline Status to For Outreach and record date
        if (in_array($this->input->post('assessment'), ['Qualified', 'Approved'], true)) {
            $data['pipeline_status'] = 'For Outreach';
            if (empty($data['date_reviewed'])) {
                $data['date_reviewed'] = date('Y-m-d');
            }
        } elseif (in_array($this->input->post('assessment'), ['Not Qualified', 'Declined', 'Rejected'], true)) {
            $data['pipeline_status'] = '';
            if (empty($data['date_reviewed'])) {
                $data['date_reviewed'] = date('Y-m-d');
            }
        }

        // Handle full name reconstruction if name parts are updated
        if (isset($data['first_name']) || isset($data['last_name'])) {
            $current = $this->Influencer_model->get_influencer_by_id($id);
            if ($current) {
                $f_name = isset($data['first_name']) ? $data['first_name'] : $current->first_name;
                $l_name = isset($data['last_name']) ? $data['last_name'] : $current->last_name;
                $data['full_name'] = trim($f_name . ' ' . $l_name);
            }
        }

        $result = $this->Influencer_model->update_influencer($id, $data);
        if ($result) {
            $this->Activity_log_model->log_activity(
                'Update Detail',
                "Updated details for influencer ID: $id",
                $this->session->userdata('user_id'),
                $this->session->userdata('name')
            );
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'message' => 'Influencer updated successfully']));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Database update failed']));
        }
    }

    public function add_pipeline_influencer_ajax() {
        if (!$this->session->userdata('user_id')) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
            return;
        }

        $platform = $this->input->post('platform') ?: 'Instagram';
        $username = trim((string)$this->input->post('username'));
        
        if (empty($username)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Username is required']));
            return;
        }

        // Clean leading @
        $username = ltrim($username, '@');

        $email = trim((string)$this->input->post('email'));
        $full_name = trim((string)$this->input->post('full_name'));
        if (empty($full_name)) {
            $full_name = $username;
        }

        $pipeline_status = $this->input->post('pipeline_status') ?: 'Contacted';
        $campaign_type = $this->input->post('campaign_type') ?: 'Gifting';
        $followers = (int)$this->input->post('followers');
        $eng_rate = (float)$this->input->post('eng_rate');
        $location = trim((string)$this->input->post('location'));
        $niche = trim((string)$this->input->post('niche'));
        $notes = trim((string)$this->input->post('notes'));
        $deliverables = trim((string)$this->input->post('deliverables'));
        $fee = (float)$this->input->post('fee');

        $data = [
            'platform' => $platform,
            'username' => $username,
            'full_name' => $full_name,
            'email' => $email,
            'contact_info' => $email,
            'location' => $location,
            'niche' => $niche,
            'followers' => $followers,
            'eng_rate' => $eng_rate,
            'pipeline_status' => $pipeline_status,
            'campaign_type' => $campaign_type,
            'notes' => $notes,
            'deliverables' => $deliverables,
            'fee' => $fee > 0 ? $fee : NULL,
            'status' => 'Active',
            'created_by' => $this->session->userdata('user_id'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $insert_id = $this->Influencer_model->insert_influencer($data);
        if ($insert_id) {
            $this->Activity_log_model->log_activity(
                'Add Influencer',
                "Added new influencer to pipeline ($pipeline_status): @$username ($platform)",
                $this->session->userdata('user_id'),
                $this->session->userdata('name')
            );

            $new_influencer = $this->Influencer_model->get_influencer_by_id($insert_id);

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'message' => 'Influencer added to pipeline successfully!',
                    'influencer' => $new_influencer
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Failed to add influencer to database'
                ]));
        }
    }

    public function update_assessment_ajax() {
        $id = $this->input->post('id');
        $assessment = $this->input->post('assessment');
        $notes = $this->input->post('notes');
        
        if ($id && $assessment) {
            $data = ['assessment' => $assessment];
            
            // Logic: If Assessment is Qualified, set Pipeline Status to For Outreach
            // If Assessment is Not Qualified, remove from pipeline (clear status)
            if ($assessment === 'Qualified') {
                $data['pipeline_status'] = 'For Outreach';
            } elseif ($assessment === 'Not Qualified') {
                $data['pipeline_status'] = '';
            }
            
            // If notes provided, update them
            if ($notes !== null) {
                $data['notes'] = $notes;
            }

            $result = $this->Influencer_model->update_influencer($id, $data);
            
            if ($result) {
                $this->Activity_log_model->log_activity(
                    'Update Assessment',
                    "Updated assessment to '$assessment' for influencer ID: $id",
                    $this->session->userdata('user_id'),
                    $this->session->userdata('name')
                );
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'success', 'message' => 'Assessment updated']));
            } else {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'error', 'message' => 'Database update failed']));
            }
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid data']));
        }
    }

    public function update() {
        $id = $this->input->post('id');
        $platform = $this->input->post('platform');
        
        $data = array(
            'username' => $this->input->post('username'),
            'followers' => $this->input->post('followers'),
            'eng_rate' => $this->input->post('engagement_rate'), // Mapped to eng_rate
            'location' => $this->input->post('location'),
            'niche' => $this->input->post('niche'),
            'status' => $this->input->post('status') ? $this->input->post('status') : 'Active'
        );

        // Add platform specific fields
        if ($platform == 'TikTok') {
             $data['avg_video_views'] = $this->input->post('avg_video_views');
             $data['gmv'] = $this->input->post('gmv');
             $data['est_post_rate'] = $this->input->post('est_post_rate');
        } 
        
        // Add common optional fields
        if ($this->input->post('full_name')) $data['full_name'] = $this->input->post('full_name');
        if ($this->input->post('email')) $data['email'] = $this->input->post('email');
        if ($this->input->post('contact_info')) $data['contact_info'] = $this->input->post('contact_info');
        if ($this->input->post('first_name')) $data['first_name'] = $this->input->post('first_name');
        if ($this->input->post('last_name')) $data['last_name'] = $this->input->post('last_name');
        if ($this->input->post('notes')) $data['notes'] = $this->input->post('notes');

        $this->Influencer_model->update_influencer($id, $data);
        
        $this->Activity_log_model->log_activity(
            'Update Influencer',
            "Updated influencer with ID: $id",
            $this->session->userdata('user_id'),
            $this->session->userdata('name')
        );

        $this->session->set_flashdata('success', 'Influencer updated successfully!');
        redirect('influencer');
    }


    public function save_manual() {
        $id = $this->input->post('id');
        $redirect_to = (string)$this->input->post('redirect');

        $email = trim((string)$this->input->post('email'));
        $contact_info = trim((string)$this->input->post('contact_info'));
        if (empty($email) && filter_var($contact_info, FILTER_VALIDATE_EMAIL)) {
            $email = $contact_info;
        }
        if (empty($contact_info) && !empty($email)) {
            $contact_info = $email;
        }

        $full_name = trim((string)$this->input->post('full_name'));
        $first_name = trim((string)$this->input->post('first_name'));
        $last_name = trim((string)$this->input->post('last_name'));
        if (empty($full_name) && ($first_name || $last_name)) {
            $full_name = trim($first_name . ' ' . $last_name);
        }

        $data = array(
            'platform' => $this->input->post('platform') ?: 'Instagram',
            'username' => trim((string)$this->input->post('username')),
            'full_name' => $full_name,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'contact_info' => $contact_info,
            'followers' => (int)$this->input->post('followers'),
            'eng_rate' => (float)$this->input->post('engagement_rate'), // Mapped to eng_rate
            'location' => trim((string)$this->input->post('location')),
            'niche' => trim((string)$this->input->post('niche')),
            'notes' => trim((string)$this->input->post('notes')),
            'assessment' => trim((string)$this->input->post('assessment')),
            'status' => 'Active', // Default status
        );

        // Logic: If Assessment is Qualified, set Pipeline Status to For Outreach
        if ($this->input->post('assessment') === 'Qualified') {
            $data['pipeline_status'] = 'For Outreach';
        }

        if ($data['platform'] === 'TikTok') {
            $data['avg_video_views'] = $this->input->post('avg_video_views');
            $data['gmv'] = $this->input->post('gmv');
            $data['est_post_rate'] = $this->input->post('est_post_rate');
        }

        $drip_id = (int)$this->input->post('drip_id');
        $campaign_id = (int)$this->input->post('campaign_id');
        $influencer_id = $id;

        if ($id) {
            $this->Influencer_model->update_influencer($id, $data);
            $this->Activity_log_model->log_activity(
                'Edit Influencer',
                "Edited influencer (Manual): " . $data['username'] . " (" . $data['platform'] . ")",
                $this->session->userdata('user_id'),
                $this->session->userdata('name')
            );
        } else {
            // For new records, ensure pipeline_status is empty if not set (i.e. not Qualified)
            if (!isset($data['pipeline_status'])) {
                $data['pipeline_status'] = '';
            }
            $data['created_at'] = date('Y-m-d H:i:s'); // Mapped to created_at
            $influencer_id = $this->Influencer_model->insert_influencer($data);

            $this->Activity_log_model->log_activity(
                'Add Influencer',
                "Added new influencer (Manual): " . $data['username'] . " (" . $data['platform'] . ")",
                $this->session->userdata('user_id'),
                $this->session->userdata('name')
            );
        }

        if ($drip_id > 0 && $influencer_id > 0) {
            $this->load->model('Email_model');
            $this->Email_model->add_creator_to_drip($drip_id, $influencer_id);
        }

        if ($campaign_id > 0 && $influencer_id > 0) {
            $this->load->model('Campaign_model');
            $this->Campaign_model->add_influencer_to_campaign($campaign_id, $influencer_id);
        }

        $this->session->set_flashdata('success', 'Influencer saved successfully!');
        if ($redirect_to !== '') {
            $parts = parse_url($redirect_to);
            $has_scheme = isset($parts['scheme']) && $parts['scheme'] !== '';
            if (!$has_scheme) {
                redirect(ltrim($redirect_to, '/'));
            }
        }
        redirect('influencer/manage');
    }

    public function save_instagram() {
        $id = $this->input->post('id');
        $redirect_to = (string)$this->input->post('redirect');

        $email = trim((string)$this->input->post('email'));
        $contact_info = trim((string)$this->input->post('contact_info'));
        if (empty($contact_info) && !empty($email)) {
            $contact_info = $email;
        } elseif (empty($email) && filter_var($contact_info, FILTER_VALIDATE_EMAIL)) {
            $email = $contact_info;
        }

        $data = array(
            'platform' => 'Instagram',
            'username' => trim((string)$this->input->post('username')),
            'email' => $email,
            'contact_info' => $contact_info,
            'full_name' => trim((string)$this->input->post('full_name')),
            'followers' => (int)$this->input->post('followers'),
            'eng_rate' => (float)$this->input->post('engagement_rate'), // Mapped to eng_rate
            'location' => trim((string)$this->input->post('location')),
            'niche' => trim((string)$this->input->post('niche')),
            'status' => 'Active',
        );

        $drip_id = (int)$this->input->post('drip_id');
        $campaign_id = (int)$this->input->post('campaign_id');
        $influencer_id = $id;

        if ($id) {
            $this->Influencer_model->update_influencer($id, $data);
            $this->Activity_log_model->log_activity(
                'Edit Instagram Influencer',
                "Edited Instagram influencer: " . $data['username'],
                $this->session->userdata('user_id'),
                $this->session->userdata('name')
            );
        } else {
            // New Instagram influencers start with no pipeline status
            $data['pipeline_status'] = '';
            $data['created_at'] = date('Y-m-d H:i:s'); // Mapped to created_at
            $influencer_id = $this->Influencer_model->insert_influencer($data);
            
            $this->Activity_log_model->log_activity(
                'Add Instagram Influencer',
                "Added new Instagram influencer: " . $data['username'],
                $this->session->userdata('user_id'),
                $this->session->userdata('name')
            );
        }

        if ($drip_id > 0 && $influencer_id > 0) {
            $this->load->model('Email_model');
            $this->Email_model->add_creator_to_drip($drip_id, $influencer_id);
        }

        if ($campaign_id > 0 && $influencer_id > 0) {
            $this->load->model('Campaign_model');
            $this->Campaign_model->add_influencer_to_campaign($campaign_id, $influencer_id);
        }

        $this->session->set_flashdata('success', 'Instagram influencer saved successfully!');
        if ($redirect_to !== '') {
            $parts = parse_url($redirect_to);
            $has_scheme = isset($parts['scheme']) && $parts['scheme'] !== '';
            if (!$has_scheme) {
                redirect(ltrim($redirect_to, '/'));
            }
        }
        redirect('influencer/manage');
    }

    public function save_tiktok() {
        $id = $this->input->post('id');
        $redirect_to = (string)$this->input->post('redirect');

        $email = trim((string)$this->input->post('email'));
        $contact_info = trim((string)$this->input->post('contact_info'));
        if (empty($contact_info) && !empty($email)) {
            $contact_info = $email;
        } elseif (empty($email) && filter_var($contact_info, FILTER_VALIDATE_EMAIL)) {
            $email = $contact_info;
        }

        $data = array(
            'platform' => 'TikTok',
            'username' => trim((string)$this->input->post('username')),
            'email' => $email,
            'contact_info' => $contact_info,
            'full_name' => trim((string)$this->input->post('full_name')),
            'followers' => (int)$this->input->post('followers'),
            'eng_rate' => (float)$this->input->post('engagement_rate'), // Mapped to eng_rate
            'avg_video_views' => $this->input->post('avg_video_views'),
            'gmv' => $this->input->post('gmv'),
            'est_post_rate' => $this->input->post('est_post_rate'),
            'location' => trim((string)$this->input->post('location')),
            'niche' => trim((string)$this->input->post('niche')),
            'status' => 'Active',
        );

        $drip_id = (int)$this->input->post('drip_id');
        $campaign_id = (int)$this->input->post('campaign_id');
        $influencer_id = $id;

        if ($id) {
            $this->Influencer_model->update_influencer($id, $data);
            $this->Activity_log_model->log_activity(
                'Edit TikTok Influencer',
                "Edited TikTok influencer: " . $data['username'],
                $this->session->userdata('user_id'),
                $this->session->userdata('name')
            );
        } else {
            // New TikTok influencers start with no pipeline status
            $data['pipeline_status'] = '';
            $data['created_at'] = date('Y-m-d H:i:s'); // Mapped to created_at
            $influencer_id = $this->Influencer_model->insert_influencer($data);
            
            $this->Activity_log_model->log_activity(
                'Add TikTok Influencer',
                "Added new TikTok influencer: " . $data['username'],
                $this->session->userdata('user_id'),
                $this->session->userdata('name')
            );
        }

        if ($drip_id > 0 && $influencer_id > 0) {
            $this->load->model('Email_model');
            $this->Email_model->add_creator_to_drip($drip_id, $influencer_id);
        }

        if ($campaign_id > 0 && $influencer_id > 0) {
            $this->load->model('Campaign_model');
            $this->Campaign_model->add_influencer_to_campaign($campaign_id, $influencer_id);
        }
        $this->session->set_flashdata('success', 'TikTok influencer saved successfully!');
        if ($redirect_to !== '') {
            $parts = parse_url($redirect_to);
            $has_scheme = isset($parts['scheme']) && $parts['scheme'] !== '';
            if (!$has_scheme) {
                redirect(ltrim($redirect_to, '/'));
            }
        }
        redirect('influencer/manage');
    }

    public function import() {
        $this->import_csv();
    }

    public function import_csv() {
        if (isset($_FILES['file']['name']) && !empty($_FILES['file']['name'])) {
            $path = './uploads/';
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }

            $config['upload_path'] = $path;
            $config['allowed_types'] = 'csv|txt';
            $config['max_size'] = 10000;

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('file')) {
                $file_data = $this->upload->data();
                $file_path = $path . $file_data['file_name'];

                $handle = fopen($file_path, 'r');
                if ($handle !== FALSE) {
                    // Pre-load existing handles and emails from active brand roster to prevent duplication
                    $brand_id = $this->brand_id ?: $this->session->userdata('brand_id');

                    $seen_handles = [];
                    $seen_emails = [];

                    $this->db->select("LOWER(REPLACE(a.username, '@', '')) as uname, LOWER(p.email) as uemail");
                    $this->db->from('collaborations c');
                    $this->db->join('influencer_accounts a', 'c.account_id = a.id', 'left');
                    $this->db->join('influencer_profiles p', 'c.profile_id = p.id', 'left');
                    if ($brand_id) {
                        $this->db->where('c.brand_id', $brand_id);
                    }
                    $existing_collabs = $this->db->get()->result();

                    foreach ($existing_collabs as $c_row) {
                        $u = trim((string)$c_row->uname);
                        $e = trim((string)$c_row->uemail);
                        if ($u !== '') {
                            $seen_handles[$u] = true;
                        }
                        if ($e !== '') {
                            $seen_emails[$e] = true;
                        }
                    }

                    // Read first line to detect headers
                    $first_row = fgetcsv($handle, 10000, ",");
                    $col_handle = null;
                    $col_fullname = null;
                    $col_email = null;
                    $col_platform = null;
                    $col_followers = null;
                    $col_eng_rate = null;
                    $col_niche = null;
                    $col_location = null;

                    $is_header = false;
                    if ($first_row !== FALSE) {
                        foreach ($first_row as $idx => $cell_value) {
                            $norm = strtolower(trim($cell_value));
                            $norm = preg_replace('/[\xEF\xBB\xBF]/', '', $norm); // Strip BOM
                            $norm_clean = preg_replace('/[^a-z0-9]/', '', $norm);

                            if (strpos($norm_clean, 'handle') !== false || strpos($norm_clean, 'username') !== false || $norm_clean === 'user') {
                                $col_handle = $idx;
                                $is_header = true;
                            } elseif (strpos($norm_clean, 'fullname') !== false || $norm_clean === 'name' || strpos($norm_clean, 'creator') !== false) {
                                $col_fullname = $idx;
                                $is_header = true;
                            } elseif (strpos($norm_clean, 'email') !== false || strpos($norm_clean, 'mail') !== false) {
                                $col_email = $idx;
                                $is_header = true;
                            } elseif (strpos($norm_clean, 'platform') !== false || strpos($norm_clean, 'social') !== false) {
                                $col_platform = $idx;
                                $is_header = true;
                            } elseif (strpos($norm_clean, 'follower') !== false) {
                                $col_followers = $idx;
                                $is_header = true;
                            } elseif (strpos($norm_clean, 'engagement') !== false || strpos($norm_clean, 'engrate') !== false || $norm_clean === 'er') {
                                $col_eng_rate = $idx;
                                $is_header = true;
                            } elseif (strpos($norm_clean, 'niche') !== false || strpos($norm_clean, 'category') !== false) {
                                $col_niche = $idx;
                                $is_header = true;
                            } elseif (strpos($norm_clean, 'location') !== false || strpos($norm_clean, 'city') !== false || strpos($norm_clean, 'country') !== false) {
                                $col_location = $idx;
                                $is_header = true;
                            }
                        }
                    }

                    // Default mappings if no standard header names matched
                    if (!$is_header) {
                        // Assume order: Column 0 = Handle, Column 1 = Fullname, Column 2 = Email
                        $col_handle = 0;
                        $col_fullname = 1;
                        $col_email = 2;
                    }

                    $insert_data = [];
                    $duplicate_count = 0;
                    $row_num = 0;

                    // If first row was data instead of headers, process it
                    if (!$is_header && $first_row !== FALSE) {
                        $this->_process_csv_row($first_row, $col_handle, $col_fullname, $col_email, $col_platform, $col_followers, $col_eng_rate, $col_niche, $col_location, $seen_handles, $seen_emails, $insert_data, $duplicate_count);
                    }

                    // Read remaining rows
                    while (($row = fgetcsv($handle, 10000, ",")) !== FALSE) {
                        $row_num++;
                        $this->_process_csv_row($row, $col_handle, $col_fullname, $col_email, $col_platform, $col_followers, $col_eng_rate, $col_niche, $col_location, $seen_handles, $seen_emails, $insert_data, $duplicate_count);
                    }
                    fclose($handle);
                    unlink($file_path);

                    if (!empty($insert_data)) {
                        $this->Influencer_model->insert_batch_influencers($insert_data);
                        $imported_count = count($insert_data);

                        $this->Activity_log_model->log_activity(
                            'CSV Import',
                            "Imported $imported_count influencers via CSV ($duplicate_count duplicate records skipped)",
                            $this->session->userdata('user_id'),
                            $this->session->userdata('name')
                        );

                        if ($duplicate_count > 0) {
                            $this->session->set_flashdata('success', "Import successful! Added $imported_count new influencer(s). ($duplicate_count duplicate records were skipped).");
                        } else {
                            $this->session->set_flashdata('success', "Import successful! Added $imported_count new influencer(s) with no duplicates.");
                        }
                    } else {
                        if ($duplicate_count > 0) {
                            $this->session->set_flashdata('error', "No new influencers imported. All $duplicate_count influencer(s) in the file already exist in your roster (duplicates skipped).");
                        } else {
                            $this->session->set_flashdata('error', 'No valid influencer records found in the CSV file.');
                        }
                    }
                } else {
                    $this->session->set_flashdata('error', 'Could not open uploaded CSV file.');
                }
            } else {
                $this->session->set_flashdata('error', 'File upload failed: ' . $this->upload->display_errors('', ''));
            }
        }
        redirect('influencer/manage');
    }

    private function _process_csv_row($row, $col_h, $col_fn, $col_em, $col_pl, $col_fol, $col_er, $col_ni, $col_loc, &$seen_handles, &$seen_emails, &$insert_data, &$duplicate_count) {
        if (!is_array($row) || empty($row)) return;

        $raw_handle   = ($col_h !== null && isset($row[$col_h])) ? trim((string)$row[$col_h]) : '';
        $raw_fullname = ($col_fn !== null && isset($row[$col_fn])) ? trim((string)$row[$col_fn]) : '';
        $raw_email    = ($col_em !== null && isset($row[$col_em])) ? trim((string)$row[$col_em]) : '';
        $raw_platform = ($col_pl !== null && isset($row[$col_pl])) ? trim((string)$row[$col_pl]) : '';
        $raw_followers = ($col_fol !== null && isset($row[$col_fol])) ? (int)preg_replace('/[^0-9]/', '', $row[$col_fol]) : 0;
        $raw_er       = ($col_er !== null && isset($row[$col_er])) ? (float)preg_replace('/[^0-9.]/', '', $row[$col_er]) : 0.0;
        $raw_niche    = ($col_ni !== null && isset($row[$col_ni])) ? trim((string)$row[$col_ni]) : '';
        $raw_location = ($col_loc !== null && isset($row[$col_loc])) ? trim((string)$row[$col_loc]) : '';

        // Clean handle: remove leading '@' and whitespace
        $clean_handle = ltrim($raw_handle, '@');
        $clean_handle = trim($clean_handle);

        // Clean email: trim and lowercase
        $clean_email = strtolower(trim($raw_email));

        // Skip completely blank rows
        if ($clean_handle === '' && $clean_email === '' && $raw_fullname === '') {
            return;
        }

        // Fullname fallback
        $clean_fullname = trim($raw_fullname);
        if ($clean_fullname === '') {
            if ($clean_handle !== '') {
                $clean_fullname = $clean_handle;
            } elseif ($clean_email !== '') {
                $parts = explode('@', $clean_email);
                $clean_fullname = $parts[0];
            } else {
                $clean_fullname = 'Influencer';
            }
        }

        // Auto platform default
        $platform = !empty($raw_platform) ? $raw_platform : 'Instagram';

        // Check for duplicates
        $handle_key = ($clean_handle !== '') ? strtolower($clean_handle) : '';
        $email_key = ($clean_email !== '') ? $clean_email : '';

        $is_dup = false;
        if ($handle_key !== '' && isset($seen_handles[$handle_key])) {
            $is_dup = true;
        } elseif ($email_key !== '' && isset($seen_emails[$email_key])) {
            $is_dup = true;
        }

        if ($is_dup) {
            $duplicate_count++;
            return;
        }

        // Mark as seen so subsequent duplicate rows in the same CSV file are also skipped
        if ($handle_key !== '') {
            $seen_handles[$handle_key] = true;
        }
        if ($email_key !== '') {
            $seen_emails[$email_key] = true;
        }

        $insert_data[] = array(
            'platform' => $platform,
            'username' => !empty($clean_handle) ? $clean_handle : '',
            'full_name' => $clean_fullname,
            'email' => $clean_email,
            'followers' => $raw_followers,
            'eng_rate' => $raw_er,
            'niche' => !empty($raw_niche) ? $raw_niche : 'General',
            'location' => $raw_location,
            'status' => 'Active',
            'pipeline_status' => '', // Imported influencers start with no pipeline status
            'created_at' => date('Y-m-d H:i:s')
        );
    }

    public function download_template() {
        $filename = 'influencer_import_template.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: text/csv; charset=utf-8");
        
        $file = fopen('php://output', 'w');
        fputs($file, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel compatibility
        
        $header = array("Handle", "Fullname", "Email");
        fputcsv($file, $header);
        fputcsv($file, array("@sample_creator", "Sample Creator", "creator@example.com"));
        fputcsv($file, array("@sample_tiktok", "TikTok Creator", "tiktok_creator@example.com"));
        
        fclose($file);
        exit;
    }

    public function export_csv() {
        $filename = 'influencer_export_' . date('Ymd') . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: text/csv; charset=utf-8");
        
        $file = fopen('php://output', 'w');
        fputs($file, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel compatibility
        
        $header = array("ID", "Platform", "Username", "Full Name", "Followers", "Engagement Rate (%)", "Niche", "Email Address", "Location", "Status", "Pipeline Status", "Date Added");
        fputcsv($file, $header);
        
        // Get filters from GET request
        $filters = array(
            'search' => $this->input->get('search'),
            'platform' => $this->input->get('platform'),
            'niche' => $this->input->get('niche'),
            'status' => $this->input->get('status')
        );

        // Get influencers based on filters (no limit)
        $influencers = $this->Influencer_model->get_influencers($filters);
        
        foreach ($influencers as $inf) {
            $line = array(
                $inf->id,
                Influencer_model::normalize_platform($inf->platform ?? ''),
                isset($inf->username) ? $inf->username : '',
                isset($inf->full_name) ? $inf->full_name : '',
                (int)($inf->followers ?? 0),
                round((float)($inf->eng_rate ?? 0), 2),
                isset($inf->niche) ? $inf->niche : '',
                isset($inf->email) ? $inf->email : '',
                isset($inf->location) ? $inf->location : '',
                ucfirst($inf->status ?? 'Active'),
                isset($inf->pipeline_status) ? $inf->pipeline_status : '',
                (!empty($inf->created_at) && $inf->created_at !== '0000-00-00 00:00:00') ? date('Y-m-d', strtotime($inf->created_at)) : ''
            );
            fputcsv($file, $line);
        }
        
        fclose($file);
        exit;
    }

    public function get_tasks_ajax($influencer_id = null) {
        if (!$influencer_id) {
            $influencer_id = $this->input->get('influencer_id') ?: $this->input->post('influencer_id');
        }

        if (!$influencer_id) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid ID']));
            return;
        }

        // We need to find which campaign(s) this influencer is assigned to
        $this->db->select('ci.campaign_id, c.title as campaign_title');
        $this->db->from('campaign_influencers ci');
        $this->db->join('campaigns c', 'ci.campaign_id = c.id');
        $this->db->where('ci.influencer_id', $influencer_id);
        $campaigns = $this->db->get()->result();

        $all_tasks = [];
        foreach ($campaigns as $camp) {
            $tasks = $this->Campaign_model->get_tasks($camp->campaign_id, $influencer_id);
            foreach ($tasks as $t) {
                $t->campaign_title = $camp->campaign_title;
                $all_tasks[] = $t;
            }
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'success', 'data' => $all_tasks]));
    }

    public function update_task_status_ajax() {
        $task_id = $this->input->post('task_id');
        $status = $this->input->post('status');

        if ($task_id && in_array($status, ['Pending', 'Done'])) {
            $result = $this->Campaign_model->update_task_status($task_id, $status);
            if ($result) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'success', 'message' => 'Task updated!']));
            } else {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'error', 'message' => 'Update failed']));
            }
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid data']));
        }
    }

    public function my_tasks() {
        $data['user_name'] = $this->session->userdata('name');
        $data['user_role'] = $this->session->userdata('role') ?? 'employee';

        $status = (string)$this->input->get('status');
        if ($status !== '' && !in_array($status, ['Pending', 'Done'], true)) {
            $status = '';
        }
        $data['filter_status'] = $status;
        $data['tasks'] = $this->Campaign_model->get_user_tasks((int)$this->user_id, $status !== '' ? $status : null, 200, (string)$this->user_role);

        $this->load->view('influencer/my_tasks', $data);
    }

    public function update_my_task_status() {
        $task_id = (int)$this->input->post('task_id');
        $status = (string)$this->input->post('status');
        $filter_status = (string)$this->input->post('filter_status');
        $redirect_url = 'influencer/my_tasks' . (in_array($filter_status, ['Pending', 'Done'], true) ? '?status=' . urlencode($filter_status) : '');

        if ($task_id <= 0 || !in_array($status, ['Pending', 'Done'], true)) {
            $this->session->set_flashdata('error', 'Invalid request.');
            redirect($redirect_url);
        }

        $row = $this->db
            ->select('t.assigned_to, t.task_name, c.brand_id, u.name AS assignee_name, ia.username AS influencer_username')
            ->from('influencer_tasks t')
            ->join('campaigns c', 't.campaign_id = c.id', 'left')
            ->join('users u', 't.assigned_to = u.id', 'left')
            ->join('collaborations col', 't.collaboration_id = col.id', 'left')
            ->join('influencer_accounts ia', 'col.account_id = ia.id', 'left')
            ->where('t.id', $task_id)
            ->get()
            ->row();

        if (!$row) {
            $this->session->set_flashdata('error', 'Task not found.');
            redirect($redirect_url);
        }

        $assigned_to = isset($row->assigned_to) ? (int)$row->assigned_to : 0;
        $assignee_name = isset($row->assignee_name) ? strtolower(trim((string)$row->assignee_name)) : '';
        $current_name = strtolower(trim((string)$this->session->userdata('name')));
        $is_manager = in_array(strtolower((string)$this->user_role), ['admin', 'manager'], true);
        $is_same_user = ($assigned_to === (int)$this->user_id) || ($current_name !== '' && $assignee_name === $current_name);

        if (!$is_manager && !$is_same_user) {
            $this->session->set_flashdata('error', 'Permission denied.');
            redirect($redirect_url);
        }

        $this->Campaign_model->update_task_status($task_id, $status);
        if ($status === 'Done') {
            $this->session->set_flashdata('success', 'Task marked as completed and moved to Completed Archive!');
            $this->session->set_flashdata('completed_task_meta', [
                'task_name' => (string)($row->task_name ?? 'Task'),
                'influencer_username' => (string)($row->influencer_username ?? '')
            ]);
            redirect('influencer/my_tasks?status=Done');
        } else {
            $this->session->set_flashdata('success', 'Task moved back to Pending.');
            redirect('influencer/my_tasks?status=Pending');
        }
    }

    public function export_selected_csv() {
        $ids = $this->input->post('ids');
        if (empty($ids)) {
            $this->session->set_flashdata('error', 'No items selected for export.');
            redirect('influencer');
        }

        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }

        $filename = 'influencer_export_selected_' . date('Ymd') . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: text/csv; charset=utf-8");
        
        $file = fopen('php://output', 'w');
        fputs($file, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel compatibility
        
        $header = array("ID", "Platform", "Username", "Full Name", "Followers", "Engagement Rate (%)", "Niche", "Email Address", "Location", "Status", "Pipeline Status", "Date Added");
        fputcsv($file, $header);
        
        // Fetch specific influencers using model
        $influencers = $this->Influencer_model->get_influencers_by_ids($ids);

        foreach ($influencers as $inf) {
            $line = array(
                $inf->id,
                Influencer_model::normalize_platform($inf->platform ?? ''),
                isset($inf->username) ? $inf->username : '',
                isset($inf->full_name) ? $inf->full_name : '',
                (int)($inf->followers ?? 0),
                round((float)($inf->eng_rate ?? 0), 2),
                isset($inf->niche) ? $inf->niche : '',
                isset($inf->email) ? $inf->email : '',
                isset($inf->location) ? $inf->location : '',
                ucfirst($inf->status ?? 'Active'),
                isset($inf->pipeline_status) ? $inf->pipeline_status : '',
                (!empty($inf->created_at) && $inf->created_at !== '0000-00-00 00:00:00') ? date('Y-m-d', strtotime($inf->created_at)) : ''
            );
            fputcsv($file, $line);
        }
        
        fclose($file);
        exit;
    }

    // Tracking link methods
    public function generate_tracking_link() {
        $influencer_id = $this->input->post('influencer_id');
        $campaign_id = $this->input->post('campaign_id') ? $this->input->post('campaign_id') : null;
        $platform = $this->input->post('platform');
        $destination_url = $this->input->post('destination_url');

        if (!$influencer_id || !$platform || !$destination_url) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'Missing required fields']));
            return;
        }

        $result = $this->Tracking_model->create_tracking_link($influencer_id, $campaign_id, $platform, $destination_url);
        $this->Activity_log_model->log_activity('Generate Tracking Link', "Generated tracking link (Code: {$result['code']}) for influencer ID: $influencer_id", $this->session->userdata('user_id'), $this->session->userdata('name'));

        $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'success', 'data' => $result]));
    }

    public function get_tracking_links($influencer_id = null, $campaign_id = null) {
        $links = $this->Tracking_model->get_tracking_links($influencer_id, $campaign_id);
        $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'success', 'data' => $links]));
    }

    public function get_influencer_tracking_stats($influencer_id) {
        $stats = $this->Tracking_model->get_influencer_stats($influencer_id);
        $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'success', 'data' => $stats]));
    }

    public function get_campaign_tracking_stats($campaign_id) {
        $stats = $this->Tracking_model->get_campaign_stats($campaign_id);
        $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'success', 'data' => $stats]));
    }
}
