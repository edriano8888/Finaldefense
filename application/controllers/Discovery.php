<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property Influencer_model $Influencer_model
 * @property CI_Session $session
 * @property CI_Input $input
 * @property CI_DB_query_builder $db
 * @property CI_Cache $cache
 */
class Discovery extends Brand_Controller {

    private $youtube_api_key;
    private $fb_access_token;
    private $ig_business_id;
    private $google_api_key;
    private $google_search_engine_id;
    private $tiktok_rapidapi_key;
    private $instagram_rapidapi_key;
    private $zocialmine_api_key;
    private $apify_api_token;
    private $user_name;
    private $user_email;
    private $api_mode; // 'live' or 'mock'
    private $last_ig_error;

    public function __construct() {
        parent::__construct();
        $this->load->model('Influencer_model');
        $this->load->model('Email_model');
        $this->load->library('form_validation');
        $this->load->database();
        $this->load->driver('cache', array('adapter' => 'file'));



        $this->user_name = $this->session->userdata('name');
        $this->user_email = $this->session->userdata('email');
        
        // Try to get key from environment first (more secure), then config fallback
        $this->youtube_api_key = getenv('YOUTUBE_API_KEY') ?: config_item('youtube_api_key');
        
        // Facebook/Instagram Graph API
        $this->fb_access_token = getenv('FACEBOOK_ACCESS_TOKEN');
        $this->ig_business_id = getenv('IG_BUSINESS_ACCOUNT_ID');
        
        // Google Custom Search
        $this->google_api_key = getenv('GOOGLE_API_KEY') ?: $this->youtube_api_key;
        $this->google_search_engine_id = getenv('GOOGLE_SEARCH_ENGINE_ID');

        // Ayrshare API
        $this->ayrshare_api_key = getenv('AYRSHARE_API_KEY');

        // RapidAPI
        $this->instagram_rapidapi_key = getenv('RAPIDAPI_KEY');

        // Zocialmine API (TikTok)
        $this->zocialmine_api_key = getenv('ZOCIALMINE_API_KEY') ?: '';

        // Apify API
        $this->apify_api_token = getenv('APIFY_API_TOKEN') ?: '';

        // Determine mode based on available keys
        $this->api_mode = ($this->youtube_api_key || $this->fb_access_token || $this->tiktok_rapidapi_key || $this->instagram_rapidapi_key || $this->ayrshare_api_key || $this->zocialmine_api_key || $this->apify_api_token || ($this->google_api_key && $this->google_search_engine_id)) ? 'live' : 'mock';
        $this->last_ig_error = null;
    }

    public function index() {
        $data['user_name'] = $this->user_name;
        $data['user_role'] = $this->user_role;
        $data['user_email'] = $this->user_email;
        $data['brand_id'] = $this->brand_id;
        $data['brand_name'] = $this->brand_name;
        $data['user_brands'] = $this->User_model->get_user_brands($this->user_id);
        $data['api_mode'] = $this->api_mode;
        $data['results'] = [];
        $data['searched'] = false;
        
        // Removed search_history usage for a cleaner system
        $data['recent_searches'] = [];

        // Trending/Recommended Searches (Static for now to keep DB clean)
        $data['recommended_searches'] = [
            ['keyword' => '#MinimalistStyle'],
            ['keyword' => '#WellnessJourney'],
            ['keyword' => '#HandmadeWithLove'],
            ['keyword' => '#TravelVlog'],
            ['keyword' => '#FoodieLife']
        ];

        $this->load->view('discovery/index', $data);
    }

    public function api_status() {
        if (!$this->session->userdata('logged_in') || !$this->session->userdata('user_id')) {
            show_404();
        }

        $app_id = getenv('FACEBOOK_APP_ID');
        $app_secret = getenv('FACEBOOK_APP_SECRET');

        $status = [
            'instagram_graph' => [
                'configured' => (bool)$this->fb_access_token && (bool)$this->ig_business_id,
                'access_token' => $this->_mask_secret((string)$this->fb_access_token),
                'ig_business_id' => (string)$this->ig_business_id,
                'app_id' => $this->_mask_secret((string)$app_id),
                'app_secret' => $this->_mask_secret((string)$app_secret),
                'debug_token' => null,
                'business_discovery' => null,
            ],
        ];

        if ($this->fb_access_token && $app_id && $app_secret) {
            $app_token = $app_id . '|' . $app_secret;
            $url = 'https://graph.facebook.com/debug_token?input_token=' . rawurlencode((string)$this->fb_access_token) . '&access_token=' . rawurlencode($app_token);
            $resp = $this->_make_api_request($url);
            $decoded = $resp !== FALSE ? json_decode((string)$resp, true) : null;
            if (is_array($decoded)) {
                $status['instagram_graph']['debug_token'] = [
                    'ok' => !isset($decoded['error']) && isset($decoded['data']),
                    'error' => isset($decoded['error']) ? $decoded['error'] : null,
                    'data' => isset($decoded['data']) ? [
                        'is_valid' => isset($decoded['data']['is_valid']) ? (bool)$decoded['data']['is_valid'] : null,
                        'expires_at' => isset($decoded['data']['expires_at']) ? (int)$decoded['data']['expires_at'] : null,
                        'scopes' => isset($decoded['data']['scopes']) ? $decoded['data']['scopes'] : null,
                        'type' => isset($decoded['data']['type']) ? (string)$decoded['data']['type'] : null,
                    ] : null,
                ];
            } else {
                $status['instagram_graph']['debug_token'] = [
                    'ok' => false,
                    'error' => ['message' => 'debug_token request failed'],
                    'data' => null,
                ];
            }
        }

        $test_username = (string)$this->input->get('username');
        if ($test_username === '') {
            $test_username = 'instagram';
        }
        $test_username = ltrim($test_username, '@');

        if ($this->fb_access_token && $this->ig_business_id) {
            $u = $this->_instagram_username_lookup($test_username, (string)$this->ig_business_id, (string)$this->fb_access_token);
            $status['instagram_graph']['business_discovery'] = [
                'ok' => is_array($u),
                'username' => $test_username,
                'result' => is_array($u) ? [
                    'username' => isset($u['username']) ? (string)$u['username'] : null,
                    'followers' => isset($u['followers']) ? (int)$u['followers'] : null,
                    'posts_count' => isset($u['posts_count']) ? (int)$u['posts_count'] : null,
                    'recent_media_count' => isset($u['recent_media']) && is_array($u['recent_media']) ? count($u['recent_media']) : null,
                ] : null
            ];
        }

        $test_hashtag = (string)$this->input->get('hashtag');
        $test_hashtag = ltrim(trim($test_hashtag), '#');
        if ($test_hashtag !== '' && $this->fb_access_token && $this->ig_business_id) {
            $hashtag_status = [
                'ok' => false,
                'hashtag' => $test_hashtag,
                'hashtag_id' => null,
                'top_media_count' => 0,
                'username_count' => 0,
                'sample_usernames' => [],
                'error' => null
            ];

            $url = "https://graph.facebook.com/v21.0/ig_hashtag_search?user_id={$this->ig_business_id}&q=" . urlencode($test_hashtag) . "&access_token=" . $this->fb_access_token;
            $resp = $this->_make_api_request($url);
            $decoded = $resp !== FALSE ? json_decode((string)$resp, true) : null;
            if (is_array($decoded) && isset($decoded['data'][0]['id'])) {
                $hashtag_id = (string)$decoded['data'][0]['id'];
                $hashtag_status['hashtag_id'] = $hashtag_id;
                $fields = 'id,username,caption,permalink,media_type';
                $url2 = "https://graph.facebook.com/v21.0/{$hashtag_id}/top_media?user_id={$this->ig_business_id}&fields={$fields}&access_token=" . $this->fb_access_token . "&limit=30";
                $resp2 = $this->_make_api_request($url2);
                $decoded2 = $resp2 !== FALSE ? json_decode((string)$resp2, true) : null;
                if (is_array($decoded2) && isset($decoded2['data']) && is_array($decoded2['data'])) {
                    $hashtag_status['top_media_count'] = count($decoded2['data']);
                    $names = [];
                    foreach ($decoded2['data'] as $m) {
                        if (isset($m['username'])) {
                            $n = strtolower(trim((string)$m['username']));
                            $n = ltrim($n, '@');
                            if ($n !== '') $names[] = $n;
                        }
                    }
                    $names = array_values(array_unique($names));
                    $hashtag_status['username_count'] = count($names);
                    $hashtag_status['sample_usernames'] = array_slice($names, 0, 10);
                    $hashtag_status['ok'] = $hashtag_status['top_media_count'] > 0;
                } elseif (is_array($decoded2) && isset($decoded2['error'])) {
                    $hashtag_status['error'] = $decoded2['error'];
                }
            } elseif (is_array($decoded) && isset($decoded['error'])) {
                $hashtag_status['error'] = $decoded['error'];
            }

            $status['instagram_graph']['hashtag_test'] = $hashtag_status;
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($status));
    }

    public function image_proxy() {
        $url = trim((string)$this->input->get('url'));
        if ($url === '' || !filter_var($url, FILTER_VALIDATE_URL)) {
            show_404();
            return;
        }
        $scheme = strtolower((string)parse_url($url, PHP_URL_SCHEME));
        if (!in_array($scheme, ['http', 'https'], true)) {
            show_404();
            return;
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 12,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
            CURLOPT_HTTPHEADER => [
                'Accept: image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
                'Referer: https://www.instagram.com/'
            ]
        ]);
        $img = curl_exec($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $ct = (string)curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);

        if ($code >= 200 && $code < 300 && !empty($img)) {
            header('Access-Control-Allow-Origin: *');
            header('Cross-Origin-Resource-Policy: cross-origin');
            header('Cache-Control: public, max-age=86400');
            header('Content-Type: ' . ($ct !== '' ? $ct : 'image/jpeg'));
            echo $img;
            exit;
        }

        show_404();
    }

    public function search() {
        @set_time_limit(180);
        $platform = strtolower(trim((string)($this->input->post('platform') ?: $this->input->get('platform'))));
        if ($platform === '') $platform = 'instagram';

        $raw_keyword = trim((string)($this->input->post('keyword') ?: $this->input->get('keyword')));
        $is_filter_only = ($raw_keyword === '');
        $keyword = $is_filter_only ? 'lifestyle' : $raw_keyword;
        $is_direct_handle = (strpos($raw_keyword, '@') === 0);

        $min_followers = $this->input->post('min_followers') !== null ? trim((string)$this->input->post('min_followers')) : trim((string)$this->input->get('min_followers'));
        $max_followers = $this->input->post('max_followers') !== null ? trim((string)$this->input->post('max_followers')) : trim((string)$this->input->get('max_followers'));
        $location = trim((string)($this->input->post('location') ?: $this->input->get('location')));
        $min_engagement = $this->input->post('min_engagement') !== null ? trim((string)$this->input->post('min_engagement')) : trim((string)$this->input->get('min_engagement'));
        $has_email = $this->input->post('has_email') !== null ? trim((string)$this->input->post('has_email')) : trim((string)$this->input->get('has_email'));
        $gender = strtolower(trim((string)($this->input->post('gender') ?: $this->input->get('gender'))));
        $exclude_saved = $this->input->post('exclude_saved') ?: $this->input->get('exclude_saved');

        // Prepare Saved List for Exclusion
        $saved_map = [];
        if ($exclude_saved === 'true' || $exclude_saved === '1') {
             $query = $this->db->query("
                SELECT ia.username, ia.platform 
                FROM influencer_accounts ia 
                JOIN collaborations c ON c.account_id = ia.id
                WHERE c.brand_id = ?
                  AND c.account_id IS NOT NULL
             ", [$this->brand_id]);
             if ($query) {
                 foreach($query->result() as $row) {
                     $clean_u = strtolower(ltrim(trim((string)$row->username), '@'));
                     $saved_map[strtolower((string)$row->platform) . ':' . $clean_u] = true;
                 }
             }
        }

        $results = [];
        $data['warning'] = ''; // Initialize warning

        // Helper closure to merge creator arrays without duplicate usernames
        $merge_creators = function(array &$target, array $incoming) {
            $existing_usernames = array_map(function($r) {
                return strtolower(ltrim(trim((string)($r['username'] ?? '')), '@'));
            }, $target);
            foreach ($incoming as $row) {
                $uname_lower = strtolower(ltrim(trim((string)($row['username'] ?? '')), '@'));
                if ($uname_lower === '') continue;
                $found_index = array_search($uname_lower, $existing_usernames, true);
                if ($found_index !== false) {
                    foreach ($row as $k => $v) {
                        if ($v !== '' && $v !== null && $v !== 0 && $v !== '0%' && $v !== 'N/A') {
                            $target[$found_index][$k] = $v;
                        } elseif (!isset($target[$found_index][$k])) {
                            $target[$found_index][$k] = $v;
                        }
                    }
                } else {
                    $target[] = $row;
                    $existing_usernames[] = $uname_lower;
                }
            }
        };

        $allow_mock = (strtolower((string)getenv('DISCOVERY_ALLOW_MOCK')) === 'true');

        // LIVE API DISCOVERY ONLY (No pre-dumped database rows or mock data)
        if ($platform === 'youtube') {
            if ($this->youtube_api_key) {
                $api_results = $this->_search_youtube($keyword, $location);
                if (!empty($api_results)) {
                    $merge_creators($results, $api_results);
                }
            }
            if (empty($results) && $allow_mock && !$is_direct_handle) {
                $merge_creators($results, $this->_get_mock_data('youtube', (string)$keyword));
            }
        } elseif ($platform === 'instagram') {
            if ($this->apify_api_token || $this->fb_access_token || $this->instagram_rapidapi_key) {
                $api_results = $this->_search_instagram($keyword, $location);
                if (!empty($api_results)) {
                    $merge_creators($results, $api_results);
                }
            }

            if (empty($results) && $this->google_search_engine_id && $this->google_api_key && $this->google_search_engine_id !== 'your_search_engine_id') {
                $cse_results = $this->_search_google_custom($keyword, 'instagram');
                if (!empty($cse_results)) {
                    $merge_creators($results, $cse_results);
                    $data['warning'] = '';
                }
            }

            if (empty($results) && $allow_mock && !$is_direct_handle) {
                $merge_creators($results, $this->_get_mock_data('instagram', (string)$keyword));
            }
        } elseif ($platform === 'tiktok') {
            $api_results = [];
            $clean_tt_kw = strtolower(ltrim(trim((string)$keyword), '@'));
            $niche_words = ['fitness','fashion','beauty','lifestyle','travel','food','foodie','tech','technology','gaming','gamer','skincare','makeup','wellness','workout','gym','vlog','music','dance','comedy','ootd','minimaliststyle','wellnessjourney','handmadewithlove','travelvlog','foodielife','style','cooking','health','art','design','gadgets'];
            $is_likely_tt_username = $is_direct_handle || (strpos($raw_keyword, '#') !== 0 && strpos($clean_tt_kw, ' ') === false && !in_array($clean_tt_kw, $niche_words, true));

            // Fast 1-second direct TikTok profile SSR lookup for @handles and specific usernames
            if ($is_likely_tt_username && $clean_tt_kw !== '') {
                $direct_tt = $this->_scrape_tiktok_profile_direct($clean_tt_kw);
                if (!empty($direct_tt)) {
                    $api_results[] = $direct_tt;
                }
            }

            // Run Apify TikTok Scraper for niche/hashtag searches or if direct handle lookup found nothing
            if (empty($api_results) && $this->apify_api_token && !$is_direct_handle) {
                $apify_tt = $this->_search_tiktok_apify($keyword, $location);
                if (!empty($apify_tt)) {
                    $api_results = array_merge($api_results, $apify_tt);
                }
            }
            if (empty($api_results)) {
                $api_results = $this->_search_tiktok($keyword);
            }
            if (empty($api_results) && $this->google_search_engine_id && $this->google_api_key && $this->google_search_engine_id !== 'your_search_engine_id') {
                $api_results = $this->_search_google_custom($keyword, 'tiktok');
            }
            if (!empty($api_results)) {
                $merge_creators($results, $api_results);
            }

            if (empty($results) && $allow_mock && !$is_direct_handle) {
                $merge_creators($results, $this->_get_mock_data('tiktok', (string)$keyword));
            }
        }

        // Normalize all live result items before filtering so every filter field is populated from real data
        foreach ($results as &$item) {
            $item['username'] = ltrim(trim((string)($item['username'] ?? '')), '@');
            $media_captions = '';
            if (!empty($item['recent_media']) && is_array($item['recent_media'])) {
                foreach ($item['recent_media'] as $rm) {
                    $media_captions .= ' ' . (string)($rm['caption'] ?? '');
                }
            }
            $profile_bio_only = (string)($item['bio'] ?? '') . ' ' . (string)($item['website'] ?? '');
            $bio_combined = (string)($item['notes'] ?? '') . ' ' . $profile_bio_only . ' ' . $media_captions;
            if (empty($item['email'])) {
                $item['email'] = $this->_extract_email_from_text($bio_combined);
            }
            $item['has_email'] = !empty($item['email']);
            if (empty($item['location'])) {
                $item['location'] = $this->_extract_location_from_text($profile_bio_only . ' ' . (string)($item['full_name'] ?? ''));
            }
            if (empty($item['gender'])) {
                $item['gender'] = $this->_detect_creator_gender($item);
            }
        }
        unset($item);

        // POST-SEARCH FILTERING
        if (($min_followers !== '' && $min_followers !== null) || 
            ($max_followers !== '' && $max_followers !== null) || 
            $location !== '' || 
            ($min_engagement !== '' && $min_engagement !== null && (float)$min_engagement > 0) || 
            ($has_email !== '' && $has_email !== null) || 
            ($exclude_saved === 'true' || $exclude_saved === '1') || 
            ($gender !== '' && $gender !== 'any')) {

            $results = array_filter($results, function($item) use ($min_followers, $max_followers, $location, $min_engagement, $has_email, $saved_map, $exclude_saved, $gender) {
                // 0. Exclude Saved
                if ($exclude_saved === 'true' || $exclude_saved === '1') {
                    $clean_u = strtolower(ltrim(trim((string)($item['username'] ?? '')), '@'));
                    $key = strtolower((string)($item['platform'] ?? '')) . ':' . $clean_u;
                    if (isset($saved_map[$key])) return false;
                }

                // 1. Followers Filter
                $followers = (int)($item['followers'] ?? 0);
                if ($min_followers !== '' && $min_followers !== null && is_numeric($min_followers) && (int)$min_followers > 0 && $followers < (int)$min_followers) return false;
                if ($max_followers !== '' && $max_followers !== null && is_numeric($max_followers) && (int)$max_followers > 0 && $followers > (int)$max_followers) return false;

                // 2. Location Filter (supports Country Names, ISO Codes, States & Major Cities)
                if ($location !== '') {
                    if (!$this->_matches_location_filter($item, $location)) {
                        return false;
                    }
                }

                // 3. Engagement Rate Filter
                if ($min_engagement !== '' && $min_engagement !== null && (float)$min_engagement > 0) {
                    $rate_str = (string)($item['eng_rate'] ?? '0');
                    $rate = floatval(preg_replace('/[^0-9.]/', '', $rate_str));
                    if ($rate < floatval($min_engagement)) return false;
                }

                // 4. Email Filter (checks email field, has_email flag, and bio/notes regex)
                if ($has_email !== '' && $has_email !== null) {
                    $item_email = trim((string)($item['email'] ?? ''));
                    $bio = (string)($item['notes'] ?? '') . ' ' . (string)($item['bio'] ?? '') . ' ' . (string)($item['website'] ?? '');
                    $has_email_found = ($item_email !== '') || !empty($item['has_email']) || (bool)preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $bio);
                    
                    if ((string)$has_email === '1' && !$has_email_found) return false;
                    if ((string)$has_email === '0' && $has_email_found) return false;
                }

                // 5. Gender Filter
                if ($gender !== '' && $gender !== 'any') {
                    $detected_gender = !empty($item['gender']) ? strtolower((string)$item['gender']) : $this->_detect_creator_gender($item);
                    if ($gender === 'male' && $detected_gender !== 'male') return false;
                    if ($gender === 'female' && $detected_gender !== 'female') return false;
                }

                return true;
            });
            $results = array_values($results);
        }

        $data['user_name'] = $this->user_name;
        $data['user_role'] = $this->user_role;
        $data['user_email'] = $this->user_email;
        $data['results'] = $results;
        $data['searched'] = true;
        $data['search_params'] = [
            'platform' => $platform,
            'keyword' => $raw_keyword !== '' ? $raw_keyword : $keyword,
            'min_followers' => $min_followers,
            'max_followers' => $max_followers,
            'location' => $location,
            'min_engagement' => $min_engagement,
            'has_email' => $has_email,
            'gender' => $gender,
            'exclude_saved' => $exclude_saved
        ];
        $data['api_mode'] = $this->api_mode;
        $data['campaigns'] = $this->Email_model->get_email_drips($this->brand_id);

        $this->load->view('discovery/results', $data);
    }

    public function add() {
        if (!$this->session->userdata('logged_in') || !$this->session->userdata('user_id')) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Unauthorized. Please login again.']));
            return;
        }

        if (strtoupper((string)$this->input->method()) !== 'POST') {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid request method.']));
            return;
        }

        try {
            $platform = strtolower(trim((string)$this->input->post('platform')));
            $username = trim((string)$this->input->post('username'));

            if ($platform === '' || $username === '') {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'error', 'message' => 'Missing platform or username.']));
                return;
            }

            $followers_raw = $this->input->post('followers');
            $followers = is_numeric($followers_raw) ? (int)$followers_raw : (int)preg_replace('/[^\d]/', '', (string)$followers_raw);

            $avg_views_raw = $this->input->post('avg_video_views');
            $avg_video_views = is_numeric($avg_views_raw) ? (int)$avg_views_raw : (int)preg_replace('/[^\d]/', '', (string)$avg_views_raw);

            $eng_rate_raw = trim((string)$this->input->post('eng_rate'));
            $eng_rate = 0.0;
            if ($eng_rate_raw !== '' && strtoupper($eng_rate_raw) !== 'N/A') {
                $eng_rate_clean = str_replace('%', '', $eng_rate_raw);
                $eng_rate_clean = trim($eng_rate_clean);
                if (is_numeric($eng_rate_clean)) {
                    $eng_rate = (float)$eng_rate_clean;
                }
            }

            $raw_bio = trim((string)$this->input->post('bio'));
            $raw_notes = trim((string)$this->input->post('notes'));
            if (preg_match('/^(Discovered via|Direct Lookup via|Verified TikTok Creator|Existing Influencer in Database)/i', $raw_notes) || ($raw_bio !== '' && $raw_notes === $raw_bio)) {
                $raw_notes = '';
            }
            $bio_search_text = $raw_bio . ' ' . $raw_notes . ' ' . $this->input->post('full_name');
            $email = $this->input->post('email') ?: $this->_extract_email_from_text($bio_search_text);

            $data = [
                'platform' => $platform,
                'username' => $username,
                'full_name' => $this->input->post('full_name'),
                'profile_image' => $this->input->post('profile_image'),
                'followers' => $followers,
                'niche' => $this->input->post('niche'),
                'avg_video_views' => $avg_video_views,
                'eng_rate' => $eng_rate,
                'location' => $this->input->post('location'),
                'bio' => $raw_bio,
                'notes' => $raw_notes,
                'contact_info' => $this->input->post('profile_url'),
                'email' => $email,
                'status' => 'Active',
                'pipeline_status' => '',
                'created_by' => $this->user_id,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->db->where('username', $username);
            $this->db->where('platform', $platform);
            $account_query = $this->db->get('influencer_accounts');

            if ($account_query->num_rows() > 0) {
                $account = $account_query->row();
                $this->db->where('account_id', $account->id);
                $this->db->where('brand_id', $this->brand_id);
                $collab_exists = $this->db->count_all_results('collaborations');
                if ($collab_exists > 0) {
                    $this->output
                        ->set_content_type('application/json')
                        ->set_output(json_encode(['status' => 'error', 'message' => 'Influencer already in your brand\'s list!']));
                    return;
                }
            }

            $insert_id = $this->Influencer_model->insert_influencer($data);

            if ($insert_id) {
                $campaign_id = (int) $this->input->post('campaign_id');
                if ($campaign_id > 0) {
                    $this->load->model('Email_model');
                    $this->Email_model->add_creator_to_drip($campaign_id, $insert_id);
                }

                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'success', 
                        'message' => 'Influencer added successfully!',
                        'campaign_id' => $campaign_id
                    ]));
                return;
            }

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Failed to add influencer.']));
        } catch (Throwable $e) {
            log_message('error', 'Discovery add error: ' . $e->getMessage());
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()]));
        }
    }

    public function profile() {
        if (!$this->session->userdata('logged_in') || !$this->session->userdata('user_id')) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Unauthorized. Please login again.']));
            return;
        }

        if (strtoupper((string)$this->input->method()) !== 'POST') {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid request method.']));
            return;
        }

        try {
            $platform = strtolower(trim((string)$this->input->post('platform')));
            $username = trim((string)$this->input->post('username'));
            $username = ltrim($username, '@');

            if ($platform === '' || $username === '') {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'error', 'message' => 'Missing platform or username.']));
                return;
            }

            $clean_u = ltrim($username, '@');
            $u = null;

            // Always fetch real live profile data from API
            if ($platform === 'tiktok') {
                if ($this->apify_api_token) {
                    $tt_res = $this->_search_tiktok_apify($clean_u);
                    if (!empty($tt_res[0])) {
                        $u = $tt_res[0];
                    }
                }
                if (!$u) {
                    $u = $this->_tiktok_username_lookup(strtolower($clean_u));
                }
            } elseif ($platform === 'instagram') {
                if ($this->apify_api_token || $this->instagram_rapidapi_key || ($this->fb_access_token && $this->ig_business_id)) {
                    $u = $this->_instagram_username_lookup(strtolower($clean_u), (string)$this->ig_business_id, (string)$this->fb_access_token);
                }
            }

            if (!$u) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'error', 'message' => 'No live profile data found from API.']));
                return;
            }

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'data' => $u]));
            return;
        } catch (Throwable $e) {
            log_message('error', 'Discovery profile error: ' . $e->getMessage());
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()]));
        }
    }

    private function _search_instagram($keyword, $location = '') {
        $access_token = $this->fb_access_token;
        $ig_business_id = $this->ig_business_id;

        $keyword = trim((string)$keyword);
        $starts_with_at = (strpos($keyword, '@') === 0);
        $starts_with_hash = (strpos($keyword, '#') === 0);
        $clean_keyword = str_replace(['@', '#', ' '], '', $keyword);
        $clean_keyword_lower = strtolower($clean_keyword);

        $results = [];

        // STRATEGY 1: Direct Username Lookup if handle explicitly starts with '@'
        if ($starts_with_at) {
            $user_result = $this->_instagram_username_lookup($clean_keyword_lower, $ig_business_id, $access_token);
            if (!empty($user_result)) {
                $results[] = $user_result;
            }
            return $results;
        }

        // STRATEGY 2: Live Apify Instagram Hashtag + Profile Scraper (100% Real Data)
        if ($this->apify_api_token) {
            $apify_results = $this->_search_instagram_apify($keyword, $location);
            if (!empty($apify_results)) {
                $results = array_merge($results, $apify_results);
            }
        }

        // STRATEGY 3: RapidAPI Search enriched with real profile lookup
        if (empty($results) && $this->instagram_rapidapi_key) {
            $rapid_search_results = $this->_instagram_search_rapidapi($keyword);
            if (!empty($rapid_search_results)) {
                $results = array_merge($results, $rapid_search_results);
            }
        }

        // STRATEGY 4: Niche / Hashtag Search via Meta Instagram Graph API
        if (empty($results) && $access_token && $ig_business_id && $ig_business_id !== 'YOUR_IG_BUSINESS_ID') {
            $hashtag_results = $this->_instagram_hashtag_search($clean_keyword, $ig_business_id, $access_token);
            if (!empty($hashtag_results)) {
                $results = array_merge($results, $hashtag_results);
            }
        }

        // STRATEGY 5: Direct Username Lookup if not a hashtag and no results found yet
        if (!$starts_with_hash && empty($results)) {
            $user_result = $this->_instagram_username_lookup($clean_keyword_lower, $ig_business_id, $access_token);
            if (!empty($user_result)) {
                $results[] = $user_result;
            }
        }

        return $results;
    }

    private function _instagram_search_rapidapi($keyword) {
        if (empty($this->instagram_rapidapi_key)) return [];

        $host = 'instagram-scraper-stable-api.p.rapidapi.com';
        $search_url = "https://$host/search_ig.php";
        $clean_q = ltrim(trim((string)$keyword), '#@');
        if ($clean_q === '') return [];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $search_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "x-rapidapi-host: $host",
            "x-rapidapi-key: {$this->instagram_rapidapi_key}",
            "Content-Type: application/x-www-form-urlencoded"
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['search_query' => $clean_q]));
        $resp = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code !== 200 || empty($resp)) return [];

        $data = json_decode($resp, true);
        if (empty($data['users']) || !is_array($data['users'])) return [];

        $usernames = [];
        foreach (array_slice($data['users'], 0, 8) as $entry) {
            $u = $entry['user'] ?? $entry;
            $uname = trim((string)($u['username'] ?? ''));
            if ($uname !== '' && !in_array($uname, $usernames, true)) {
                $usernames[] = $uname;
            }
        }

        if (!empty($usernames) && !empty($this->apify_api_token)) {
            $enriched = $this->_instagram_batch_lookup_apify($usernames, $clean_q);
            if (!empty($enriched)) {
                return $enriched;
            }
        }

        return [];
    }

    private function _instagram_username_lookup($username, $ig_business_id, $access_token) {
        $clean_u = ltrim(trim((string)$username), '@');
        if ($clean_u === '') return null;

        if ($this->apify_api_token) {
            $apify_res = $this->_instagram_username_lookup_apify($clean_u);
            if (!empty($apify_res)) {
                return $apify_res;
            }
        }

        if ($this->instagram_rapidapi_key) {
            $rapid_res = $this->_instagram_username_lookup_rapidapi($username);
            if (!empty($rapid_res)) {
                return $rapid_res;
            }
        }

        if ($this->zocialmine_api_key) {
            $zocial_res = $this->_instagram_username_lookup_zocialmine($username);
            if (!empty($zocial_res)) {
                return $zocial_res;
            }
        }

        // Endpoint: Business Discovery
        // Added media fields to fetch recent posts for the Peek view
        $fields = "business_discovery.username($username){username,website,name,ig_id,id,profile_picture_url,biography,follows_count,followers_count,media_count,media{caption,media_url,media_type,like_count,comments_count,timestamp,permalink,thumbnail_url}}";
        $url = "https://graph.facebook.com/v21.0/{$ig_business_id}?fields=" . urlencode($fields) . "&access_token=" . $access_token;

        $response = $this->_make_api_request($url);

        if ($response === FALSE) return null;

        $data = json_decode($response, true);
        
        if (isset($data['error']) || !isset($data['business_discovery'])) {
            if (isset($data['error'])) {
                log_message('error', 'IG Business Discovery API Error: ' . json_encode($data['error']));
                $msg = isset($data['error']['message']) ? (string)$data['error']['message'] : 'Unknown error';
                $code = isset($data['error']['code']) ? (string)$data['error']['code'] : '';
                $type = isset($data['error']['type']) ? (string)$data['error']['type'] : '';
                $this->last_ig_error = trim(($type !== '' ? $type . ' ' : '') . ($code !== '' ? '(' . $code . ') ' : '') . $msg);
            }
            return null;
        }

        $u = $data['business_discovery'];
        
        // Process Media Data if available
        $recent_media = [];
        if (isset($u['media']['data'])) {
            foreach ($u['media']['data'] as $m) {
                // Determine image URL: thumbnail_url for VIDEO, media_url for IMAGE/CAROUSEL
                $image_url = isset($m['thumbnail_url']) ? $m['thumbnail_url'] : (isset($m['media_url']) ? $m['media_url'] : '');
                
                $recent_media[] = [
                    'caption' => isset($m['caption']) ? $m['caption'] : '',
                    'media_url' => $this->_wrap_ig_image_proxy($image_url), // Used for display
                    'media_type' => isset($m['media_type']) ? $m['media_type'] : 'IMAGE',
                    'likes' => isset($m['like_count']) ? $m['like_count'] : 0,
                    'comments' => isset($m['comments_count']) ? $m['comments_count'] : 0,
                    'timestamp' => isset($m['timestamp']) ? $m['timestamp'] : '',
                    'permalink' => isset($m['permalink']) ? $m['permalink'] : ''
                ];
            }
        }
        $this->_cache_recent_media_batch($recent_media);

        $posts_count = isset($u['media_count']) ? (int)$u['media_count'] : 0;
        if ($posts_count === 0 && !empty($recent_media)) {
            $posts_count = count($recent_media);
        }

        $bio_text = isset($u['biography']) ? $u['biography'] : '';
        $extracted_email = $this->_extract_email_from_text($bio_text . ' ' . (isset($u['name']) ? $u['name'] : ''));

        $res = [
            'platform' => 'instagram',
            'platform_id' => isset($u['id']) ? $u['id'] : '',
            'username' => isset($u['username']) ? $u['username'] : $username,
            'full_name' => isset($u['name']) ? $u['name'] : '',
            'profile_image' => isset($u['profile_picture_url']) ? $u['profile_picture_url'] : '',
            'followers' => isset($u['followers_count']) ? (int)$u['followers_count'] : 0,
            'following' => isset($u['follows_count']) ? (int)$u['follows_count'] : 0,
            'posts_count' => $posts_count,
            'eng_rate' => $this->_calculate_engagement($u), // Estimate
            'bio' => $bio_text,
            'website' => isset($u['website']) ? $u['website'] : '',
            'is_verified' => false,
            'is_private' => false,
            'recent_media' => $recent_media, // Pass media data
            'notes' => '',
            'email' => $extracted_email,
            'has_email' => !empty($extracted_email),
            'profile_url' => "https://instagram.com/" . (isset($u['username']) ? $u['username'] : $username)
        ];

        $this->_sync_local_account($res);

        return $res;
    }

    private function _instagram_username_lookup_rapidapi($username) {
        $rapidapi_key = $this->instagram_rapidapi_key;
        $host = 'instagram-scraper-stable-api.p.rapidapi.com';

        // 1. Fetch Profile Info
        $profile_url = "https://$host/ig_get_fb_profile_v3.php";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $profile_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "x-rapidapi-host: $host",
            "x-rapidapi-key: $rapidapi_key",
            "Content-Type: application/x-www-form-urlencoded"
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['username_or_url' => $username]));
        
        $profile_resp = curl_exec($ch);
        $profile_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($profile_code !== 200 || empty($profile_resp)) {
            log_message('error', "RapidAPI Instagram profile lookup failed for $username. HTTP Code: $profile_code");
            $this->last_ig_error = "RapidAPI profile lookup failed (HTTP $profile_code)";
            return null;
        }

        $profile_data = json_decode($profile_resp, true);
        if (!$profile_data || isset($profile_data['error'])) {
            log_message('error', "RapidAPI Instagram profile error for $username: " . json_encode($profile_data));
            $this->last_ig_error = isset($profile_data['error']) ? $profile_data['error'] : "Invalid profile response";
            return null;
        }

        // 2. Fetch Posts (Limit 12)
        $posts_url = "https://$host/get_ig_user_posts.php";
        $ch2 = curl_init();
        curl_setopt($ch2, CURLOPT_URL, $posts_url);
        curl_setopt($ch2, CURLOPT_POST, true);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch2, CURLOPT_CONNECTTIMEOUT, 8);
        curl_setopt($ch2, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch2, CURLOPT_HTTPHEADER, [
            "x-rapidapi-host: $host",
            "x-rapidapi-key: $rapidapi_key",
            "Content-Type: application/x-www-form-urlencoded"
        ]);
        curl_setopt($ch2, CURLOPT_POSTFIELDS, http_build_query(['username_or_url' => $username, 'amount' => 12]));
        
        $posts_resp = curl_exec($ch2);
        $posts_code = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
        curl_close($ch2);

        $posts_data = [];
        if ($posts_code === 200 && !empty($posts_resp)) {
            $posts_data = json_decode($posts_resp, true);
        }

        // Process Media Data
        $recent_media = [];
        if (isset($posts_data['posts']) && is_array($posts_data['posts'])) {
            foreach ($posts_data['posts'] as $p) {
                if (isset($p['node'])) {
                    $node = $p['node'];
                    $caption_text = isset($node['caption']['text']) ? $node['caption']['text'] : '';
                    $image_url = isset($node['image_versions2']['candidates'][0]['url']) ? $node['image_versions2']['candidates'][0]['url'] : '';
                    $media_type = (isset($node['media_type']) && $node['media_type'] == 2) ? 'VIDEO' : 'IMAGE';
                    
                    $recent_media[] = [
                        'caption' => $caption_text,
                        'media_url' => $this->_wrap_ig_image_proxy($image_url),
                        'media_type' => $media_type,
                        'likes' => isset($node['like_count']) ? (int)$node['like_count'] : 0,
                        'comments' => isset($node['comment_count']) ? (int)$node['comment_count'] : 0,
                        'timestamp' => isset($node['taken_at']) ? date('c', $node['taken_at']) : '',
                        'permalink' => isset($node['code']) ? "https://instagram.com/p/" . $node['code'] : ''
                    ];
                }
            }
        }
        $this->_cache_recent_media_batch($recent_media);

        $followers_count = isset($profile_data['follower_count']) ? (int)$profile_data['follower_count'] : 0;
        $following_count = isset($profile_data['following_count']) ? (int)$profile_data['following_count'] : 0;
        $media_count = isset($profile_data['media_count']) ? (int)$profile_data['media_count'] : 0;
        if ($media_count === 0 && !empty($recent_media)) {
            $media_count = count($recent_media);
        }

        $bio_text = isset($profile_data['biography']) ? $profile_data['biography'] : '';
        $extracted_email = $this->_extract_email_from_text($bio_text . ' ' . (isset($profile_data['full_name']) ? $profile_data['full_name'] : ''));

        // Calculate engagement
        $u_compat = [
            'followers_count' => $followers_count,
            'media' => [
                'data' => []
            ]
        ];
        foreach ($recent_media as $m) {
            $u_compat['media']['data'][] = [
                'like_count' => $m['likes'],
                'comments_count' => $m['comments']
            ];
        }
        $eng_rate = $this->_calculate_engagement($u_compat);

        $res = [
            'platform' => 'instagram',
            'platform_id' => isset($profile_data['id']) ? $profile_data['id'] : '',
            'username' => isset($profile_data['username']) ? $profile_data['username'] : $username,
            'full_name' => isset($profile_data['full_name']) ? $profile_data['full_name'] : '',
            'profile_image' => isset($profile_data['profile_pic_url']) ? $profile_data['profile_pic_url'] : '',
            'followers' => $followers_count,
            'following' => $following_count,
            'posts_count' => $media_count,
            'eng_rate' => $eng_rate,
            'bio' => $bio_text,
            'website' => isset($profile_data['external_url']) ? $profile_data['external_url'] : '',
            'is_verified' => !empty($profile_data['is_verified']),
            'is_private' => !empty($profile_data['is_private']),
            'recent_media' => $recent_media,
            'notes' => '',
            'email' => $extracted_email,
            'has_email' => !empty($extracted_email),
            'profile_url' => "https://instagram.com/" . (isset($profile_data['username']) ? $profile_data['username'] : $username)
        ];

        $this->_sync_local_account($res);

        return $res;
    }

    private function _instagram_username_lookup_zocialmine($username) {
        if (empty($this->zocialmine_api_key)) return null;

        $url = "https://api.zocialmine.com/v1/instagram/profile";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "x-api-key: {$this->zocialmine_api_key}",
            "Content-Type: application/json"
        ]);
        
        $clean_username = ltrim(trim($username), '@');
        $post_data = ['url' => "https://www.instagram.com/$clean_username"];
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        
        $resp = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code !== 200 || empty($resp)) {
            return null;
        }

        $decoded = json_decode($resp, true);
        if (!isset($decoded['status']) || $decoded['status'] !== 'success' || empty($decoded['data'])) {
            return null;
        }

        $d = $decoded['data'];
        $user = $d['user'] ?? [];
        $stats = $d['stats'] ?? [];

        $bio = $user['biography'] ?? '';
        $extracted_email = $this->_extract_email_from_text($bio . ' ' . ($user['fullName'] ?? ''));

        $full_name = !empty($user['fullName']) ? $user['fullName'] : $clean_username;
        $profile_image = $this->_extract_profile_image($user, $full_name);

        // Format recent media if available
        $recent_media = [];
        if (isset($d['recent_media']) && is_array($d['recent_media'])) {
            foreach ($d['recent_media'] as $m) {
                $recent_media[] = [
                    'caption' => $m['caption'] ?? '',
                    'media_url' => $m['displayUrl'] ?? '',
                    'media_type' => isset($m['isVideo']) && $m['isVideo'] ? 'VIDEO' : 'IMAGE',
                    'likes' => (int)($m['likeCount'] ?? 0),
                    'comments' => (int)($m['commentCount'] ?? 0),
                    'timestamp' => isset($m['takenAt']) ? date('c', $m['takenAt']) : date('c'),
                    'permalink' => isset($m['shortcode']) ? "https://instagram.com/p/" . $m['shortcode'] : ''
                ];
            }
        }

        // Calculate engagement
        $followers = (int)($stats['followerCount'] ?? 0);
        $eng_rate = '0%';
        if ($followers > 0 && !empty($recent_media)) {
            $total_eng = 0;
            foreach ($recent_media as $rm) {
                $total_eng += ($rm['likes'] + $rm['comments']);
            }
            $avg_eng = $total_eng / count($recent_media);
            $eng_rate = number_format(($avg_eng / $followers) * 100, 2) . '%';
        }

        $res = [
            'platform' => 'instagram',
            'platform_id' => $user['id'] ?? $clean_username,
            'username' => $user['username'] ?? $clean_username,
            'full_name' => $full_name,
            'profile_image' => $profile_image,
            'followers' => $followers,
            'following' => (int)($stats['followingCount'] ?? 0),
            'posts_count' => (int)($stats['mediaCount'] ?? 0),
            'eng_rate' => $eng_rate,
            'bio' => $bio,
            'website' => $user['externalUrl'] ?? '',
            'is_verified' => !empty($user['isVerified']),
            'is_private' => !empty($user['isPrivate']) || !empty($user['privateAccount']),
            'recent_media' => $recent_media,
            'notes' => 'Discovered via Zocialmine Instagram Profile API',
            'profile_url' => "https://instagram.com/$clean_username",
            'email' => $extracted_email,
            'has_email' => !empty($extracted_email)
        ];

        $this->_sync_local_account($res);

        return $res;
    }

    private function _sync_local_account($data) {
        if (empty($data['username']) || empty($data['platform'])) return;
        
        $account_q = $this->db->where('username', $data['username'])
                             ->where('platform', $data['platform'])
                             ->get('influencer_accounts');
        if ($account_q->num_rows() > 0) {
            $account = $account_q->row();
            
            if (!empty($data['email']) && !empty($account->profile_id)) {
                $this->db->where('id', $account->profile_id)->update('influencer_profiles', ['email' => $data['email']]);
            }

            if (!empty($data['profile_image'])) {
                $this->db->where('id', $account->id)->update('influencer_accounts', ['profile_image' => $data['profile_image']]);
            }

            if (!empty($data['full_name']) && !empty($account->profile_id)) {
                $this->db->where('id', $account->profile_id)->update('influencer_profiles', ['full_name' => $data['full_name']]);
            }

            if (isset($data['followers'])) {
                $stats_q = $this->db->where('account_id', $account->id)->get('influencer_account_stats');
                if ($stats_q->num_rows() > 0) {
                    $this->db->where('account_id', $account->id)->update('influencer_account_stats', [
                        'followers' => (int)$data['followers'],
                        'eng_rate' => $data['eng_rate']
                    ]);
                } else {
                    $this->db->insert('influencer_account_stats', [
                        'account_id' => $account->id,
                        'followers' => (int)$data['followers'],
                        'eng_rate' => $data['eng_rate']
                    ]);
                }
            }
        }
    }

    private function _instagram_hashtag_search($hashtag, $ig_business_id, $access_token) {
        // Step 1: Get Hashtag ID
        $url = "https://graph.facebook.com/v21.0/ig_hashtag_search?user_id={$ig_business_id}&q=" . urlencode($hashtag) . "&access_token=" . $access_token;
        $response = $this->_make_api_request($url);
        
        if ($response === FALSE) {
            log_message('error', 'IG Hashtag Search Request Failed (cURL/network error)');
            $this->last_ig_error = 'Hashtag search request failed (network error)';
            return [];
        }

        $data = json_decode($response, true);

        if (isset($data['error'])) {
            $msg = isset($data['error']['message']) ? (string)$data['error']['message'] : 'Unknown error';
            $code = isset($data['error']['code']) ? (string)$data['error']['code'] : '';
            $type = isset($data['error']['type']) ? (string)$data['error']['type'] : '';
            $full_error = trim(($type !== '' ? $type . ' ' : '') . ($code !== '' ? '(' . $code . ') ' : '') . $msg);
            log_message('error', 'IG Hashtag Search API Error: ' . $full_error . ' | Raw: ' . json_encode($data['error']));
            $this->last_ig_error = $full_error;
            return [];
        }

        if (!isset($data['data'][0]['id'])) return [];
        
        $hashtag_id = $data['data'][0]['id'];

        // Step 2: Get Recent Media for Hashtag (top_media is restricted in Graph API v21+)
        $fields = 'id,caption';
        $url = "https://graph.facebook.com/v21.0/{$hashtag_id}/recent_media?user_id={$ig_business_id}&fields={$fields}&access_token=" . $access_token . "&limit=25";
        
        $response = $this->_make_api_request($url);
        if ($response === FALSE) return [];
        
        $data = json_decode($response, true);
        
        if (!isset($data['data'])) return [];

        // Step 3: Extract usernames from media and mentions in captions
        $potential_users = [];
        
        foreach ($data['data'] as $media) {
            if (isset($media['username'])) {
                $u = strtolower(trim((string)$media['username']));
                $u = ltrim($u, '@');
                if (strlen($u) > 1 && !in_array($u, $potential_users, true)) {
                    $potential_users[] = $u;
                }
            }
            if (isset($media['caption'])) {
                // Regex to find @mentions
                preg_match_all('/@([a-zA-Z0-9_.]+)/', $media['caption'], $matches);
                if (!empty($matches[1])) {
                    foreach ($matches[1] as $username) {
                        $username = strtolower(trim($username));
                        // Filter out self-referencing if possible (not possible without knowing owner)
                        // Filter out common spam words if needed
                        if (strlen($username) > 1 && !in_array($username, $potential_users, true)) {
                            $potential_users[] = $username;
                        }
                    }
                }
            }
        }

        if (strlen($hashtag) > 1) {
            array_unshift($potential_users, strtolower($hashtag));
        }

        $potential_users = array_values(array_unique($potential_users));
        $potential_users = array_slice($potential_users, 0, 4);
        
        $results = [];
        foreach ($potential_users as $username) {
            $user_data = $this->_instagram_username_lookup($username, $ig_business_id, $access_token);
            if ($user_data) {
                $user_data['notes'] = "Discovered via #$hashtag";
                $results[] = $user_data;
            }
        }
        
        return $results;
    }
    
    private function _calculate_engagement($user_data) {
        $followers = isset($user_data['followers_count']) ? $user_data['followers_count'] : 0;
        if ($followers == 0) return '0%';
        
        $total_engagement = 0;
        $count = 0;

        if (isset($user_data['media']['data']) && is_array($user_data['media']['data'])) {
            foreach ($user_data['media']['data'] as $media) {
                $likes = isset($media['like_count']) ? $media['like_count'] : 0;
                $comments = isset($media['comments_count']) ? $media['comments_count'] : 0;
                $total_engagement += ($likes + $comments);
                $count++;
            }
        }

        if ($count == 0) return '0%';

        $avg_engagement = $total_engagement / $count;
        $rate = ($avg_engagement / $followers) * 100;
        
        return number_format($rate, 2) . '%';
    }

    private function _lookup_instagram_username($username) {
        // Reuse search logic as Business Discovery is essentially a lookup
        $results = $this->_search_instagram($username);
        return !empty($results) ? $results[0] : null;
    }

    private function _search_youtube($keyword, $location = '') {
        if (!isset($this->cache)) {
            $this->load->driver('cache', array('adapter' => 'file'));
        }

        $clean_kw = trim((string)$keyword);
        $clean_loc = trim((string)$location);
        $cache_key = 'youtube_search_v3_' . md5(strtolower($clean_kw . '|' . $clean_loc));

        if ($cached_results = $this->cache->get($cache_key)) {
            return $cached_results;
        }

        $queries = [$clean_kw];
        if ($clean_loc !== '' && stripos($clean_kw, $clean_loc) === false) {
            $queries[] = $clean_kw . ' ' . $clean_loc;
        }

        $channel_ids = [];
        $search_snippets = [];

        foreach ($queries as $idx => $q_str) {
            $max_res = ($idx === 0) ? 15 : 10;
            $url = "https://www.googleapis.com/youtube/v3/search?part=snippet&type=channel&q=" . urlencode($q_str) . "&key=" . $this->youtube_api_key . "&maxResults=" . $max_res;
            $response = $this->_make_api_request($url);
            if ($response === FALSE) continue;

            $data = json_decode($response, true);
            if (isset($data['error']) || empty($data['items']) || !is_array($data['items'])) continue;

            foreach ($data['items'] as $item) {
                $cid = $item['snippet']['channelId'] ?? ($item['id']['channelId'] ?? '');
                if ($cid && !isset($search_snippets[$cid])) {
                    $channel_ids[] = $cid;
                    $search_snippets[$cid] = $item['snippet'] ?? [];
                }
            }
        }

        if (empty($channel_ids)) {
            return [];
        }

        // Batch fetch full statistics + snippet for all discovered channels in one API call
        $batch_ids = implode(',', array_slice($channel_ids, 0, 25));
        $stats_url = "https://www.googleapis.com/youtube/v3/channels?part=statistics,snippet&id=" . urlencode($batch_ids) . "&key=" . $this->youtube_api_key;
        $stats_resp = $this->_make_api_request($stats_url);
        $channels_map = [];
        if ($stats_resp !== FALSE) {
            $stats_decoded = json_decode($stats_resp, true);
            if (!empty($stats_decoded['items']) && is_array($stats_decoded['items'])) {
                foreach ($stats_decoded['items'] as $ch_item) {
                    if (!empty($ch_item['id'])) {
                        $channels_map[$ch_item['id']] = $ch_item;
                    }
                }
            }
        }

        $iso_country_map = [
            'US' => 'United States',
            'GB' => 'United Kingdom',
            'UK' => 'United Kingdom',
            'CA' => 'Canada',
            'AU' => 'Australia',
            'PH' => 'Philippines',
            'SG' => 'Singapore',
            'AE' => 'United Arab Emirates',
            'IN' => 'India',
            'DE' => 'Germany',
            'FR' => 'France',
            'JP' => 'Japan',
            'KR' => 'South Korea',
            'ID' => 'Indonesia',
            'MY' => 'Malaysia',
            'NZ' => 'New Zealand'
        ];

        $results = [];
        foreach ($channel_ids as $channelId) {
            $ch_full = $channels_map[$channelId] ?? null;
            $snip = $ch_full['snippet'] ?? ($search_snippets[$channelId] ?? []);
            $stat = $ch_full['statistics'] ?? [];

            $subs = (int)($stat['subscriberCount'] ?? 0);
            $viewCount = (int)($stat['viewCount'] ?? 0);
            $videoCount = (int)($stat['videoCount'] ?? 0);
            $avg_views = ($videoCount > 0) ? (int)round($viewCount / $videoCount) : 0;

            $raw_country = strtoupper(trim((string)($snip['country'] ?? '')));
            $full_desc = (string)($snip['description'] ?? '');
            $country_label = isset($iso_country_map[$raw_country]) ? $iso_country_map[$raw_country] : $raw_country;
            if ($country_label === '') {
                $country_label = $this->_extract_location_from_text($full_desc . ' ' . ($snip['title'] ?? ''));
            }

            // Calculate real engagement rate from channel views, video count, and subscribers
            $yt_eng = 0.0;
            if ($subs > 0 && $avg_views > 0) {
                $yt_eng = min(25.0, max(0.5, round(($avg_views / $subs) * 15, 2)));
            }
            $extracted_email = $this->_extract_email_from_text($full_desc);

            $custom_url = ltrim(trim((string)($snip['customUrl'] ?? '')), '@');
            $title = trim((string)($snip['title'] ?? ($snip['channelTitle'] ?? 'Unknown')));
            $username = $custom_url !== '' ? $custom_url : $title;
            $thumb = $snip['thumbnails']['high']['url'] ?? ($snip['thumbnails']['medium']['url'] ?? ($snip['thumbnails']['default']['url'] ?? ''));

            $results[] = [
                'platform' => 'youtube',
                'platform_id' => $channelId,
                'username' => $username,
                'full_name' => $title,
                'profile_image' => $thumb,
                'followers' => $subs,
                'posts_count' => $videoCount,
                'eng_rate' => $yt_eng > 0 ? (number_format($yt_eng, 2) . '%') : '0%',
                'avg_video_views' => $avg_views,
                'location' => $country_label,
                'niche' => ltrim($clean_kw, '#@'),
                'notes' => $full_desc !== '' ? substr($full_desc, 0, 300) : 'Discovered via YouTube Data API v3',
                'bio' => $full_desc,
                'email' => $extracted_email,
                'has_email' => !empty($extracted_email),
                'profile_url' => $custom_url !== '' ? ("https://www.youtube.com/@" . $custom_url) : ("https://www.youtube.com/channel/" . $channelId)
            ];
        }

        if (!empty($results)) {
            $this->cache->save($cache_key, $results, 3600);
        }

        return $results;
    }

    private function _get_youtube_channel_stats($channelId) {
        if (!isset($this->cache)) {
             $this->load->driver('cache', array('adapter' => 'file'));
        }

        $cache_key = 'youtube_stats_full_v2_' . $channelId;
        if ($cached_stats = $this->cache->get($cache_key)) {
            return $cached_stats;
        }

        $url = "https://www.googleapis.com/youtube/v3/channels?part=statistics,snippet&id=" . $channelId . "&key=" . $this->youtube_api_key;
        $response = $this->_make_api_request($url);
        
        $stats = [
            'subscribers' => 0,
            'avg_views' => 0,
            'country' => '',
            'description' => ''
        ];

        if ($response) {
            $data = json_decode($response, true);
            if (isset($data['items'][0])) {
                $item = $data['items'][0];
                $viewCount = isset($item['statistics']['viewCount']) ? $item['statistics']['viewCount'] : 0;
                $videoCount = isset($item['statistics']['videoCount']) ? $item['statistics']['videoCount'] : 0;
                $stats['subscribers'] = isset($item['statistics']['subscriberCount']) ? $item['statistics']['subscriberCount'] : 0;
                if ($videoCount > 0) {
                    $stats['avg_views'] = round($viewCount / $videoCount);
                }
                $stats['country'] = isset($item['snippet']['country']) ? $item['snippet']['country'] : '';
                $stats['description'] = isset($item['snippet']['description']) ? (string)$item['snippet']['description'] : '';
                $this->cache->save($cache_key, $stats, 86400);
                return $stats;
            }
        }
        return $stats;
    }

    private function _search_google_custom($keyword, $platform = 'instagram') {
        $key = $this->google_api_key;
        $cx = $this->google_search_engine_id;
        
        if (!$key || !$cx) return [];

        $q = urlencode($keyword);
        $url = "https://www.googleapis.com/customsearch/v1?key={$key}&cx={$cx}&q={$q}";

        $response = $this->_make_api_request($url);
        if ($response === FALSE) return [];

        $data = json_decode($response, true);
        if (!isset($data['items'])) return [];

        $results = [];
        foreach ($data['items'] as $item) {
            $snippet = isset($item['snippet']) ? $item['snippet'] : '';
            $title = isset($item['title']) ? $item['title'] : '';
            $link = isset($item['link']) ? $item['link'] : '';
            
            // Basic Parsing for Instagram
            // Title format often: "Name (@username) • Instagram photos and videos"
            
            // Extract Username
            $username = '';
            if (preg_match('/@([a-zA-Z0-9_.]+)/', $title, $matches)) {
                $username = $matches[1];
            } elseif (preg_match('/instagram\.com\/([a-zA-Z0-9_.]+)/', $link, $matches)) {
                $username = $matches[1];
            }
            
            if (empty($username) || in_array($username, ['p', 'reel', 'stories', 'explore', 'tags'])) continue; // Skip non-profile links

            // Extract Full Name
            $full_name = '';
            $title_parts = explode('(@', $title);
            if (count($title_parts) > 1) {
                $full_name = trim($title_parts[0]);
            } else {
                $full_name = $username;
            }

            // Extract Followers
            $followers = 0;
            // Look for "X Followers" in snippet
            if (preg_match('/([0-9.,KkMm]+)\s+Followers/', $snippet, $matches)) {
                $followers = $this->_parse_count_string($matches[1]);
            }

            // Image
            $image = '';
            if (isset($item['pagemap']['cse_image'][0]['src'])) {
                $image = $item['pagemap']['cse_image'][0]['src'];
            } else {
                $image = "https://ui-avatars.com/api/?name=" . urlencode($full_name) . "&background=random&color=fff";
            }

            $results[] = [
                'platform' => $platform,
                'platform_id' => '', // Unknown via search
                'username' => $username,
                'full_name' => $full_name,
                'profile_image' => $image,
                'followers' => $followers,
                'eng_rate' => 'N/A', // Not available via search
                'avg_video_views' => 0,
                'location' => '', 
                'notes' => 'Discovered via Google Search: ' . substr($snippet, 0, 100) . '...',
                'profile_url' => $link,
                'is_verified' => false,
                'recent_media' => [] // No media data available
            ];
        }

        return $results;
    }



    private function _parse_count_string($str) {
        $str = str_replace(',', '', strtolower(trim($str)));
        if (strpos($str, 'k') !== false) {
            return floatval($str) * 1000;
        } elseif (strpos($str, 'm') !== false) {
            return floatval($str) * 1000000;
        }
        return floatval($str);
    }

    private function _make_api_request($url, $headers = []) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For local dev without proper cert bundle
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $output = curl_exec($ch);
        $http_code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_errno($ch);
        
        curl_close($ch);

        // Return FALSE only on network/cURL errors (e.g. DNS failure, timeout)
        // Do NOT return FALSE on 4xx/5xx HTTP codes — the Facebook Graph API returns
        // structured JSON error bodies (with error.message, error.code) even on 400/401/403.
        // Callers are responsible for checking isset($data['error']) in the decoded response.
        if ($curl_error) {
            log_message('error', 'cURL error (' . $curl_error . ') for URL: ' . $url);
            return FALSE;
        }

        // For non-Facebook APIs (e.g. YouTube, TikTok) that don't return error JSON on 5xx,
        // treat server errors as failures. 4xx are still returned so callers can inspect them.
        if ($http_code >= 500) {
            log_message('error', 'HTTP ' . $http_code . ' server error for URL: ' . $url);
            return FALSE;
        }
        
        return $output;
    }

    private function _search_local($platform, $keyword) {
        $this->db->select('a.*, p.full_name, p.email, p.location, p.notes as bio, s.followers, s.eng_rate, s.avg_video_views, pd.bio as platform_bio, pd.external_url, pd.verified_status, GROUP_CONCAT(DISTINCT n.niche_name SEPARATOR ", ") as niche_list', false);
        $this->db->from('influencer_accounts a');
        $this->db->join('influencer_profiles p', 'a.profile_id = p.id', 'left');
        $this->db->join('influencer_account_stats s', 'a.id = s.account_id', 'left');
        $this->db->join('influencer_account_platform_details pd', 'a.id = pd.account_id', 'left');
        $this->db->join('influencer_niches n', 'a.id = n.account_id', 'left');
        
        if ($platform && $platform !== 'all') {
            $this->db->where('LOWER(a.platform)', strtolower($platform));
        }

        $trimmed_kw = trim((string)$keyword);
        if ($trimmed_kw !== '') {
            $search_terms = $this->_expand_niche_keywords($trimmed_kw);
            $this->db->group_start();
            foreach ($search_terms as $idx => $term) {
                if ($idx === 0) {
                    $this->db->like('a.username', $term);
                } else {
                    $this->db->or_like('a.username', $term);
                }
                $this->db->or_like('p.full_name', $term);
                $this->db->or_like('p.notes', $term);
                $this->db->or_like('pd.bio', $term);
                $this->db->or_like('p.location', $term);
                $this->db->or_like('n.niche_name', $term);
            }
            $this->db->group_end();
        }

        $this->db->group_by('a.id');
        $this->db->order_by('s.followers', 'DESC');
        $this->db->limit(50);
        $query = $this->db->get();
        $rows = $query ? $query->result() : [];

        $results = [];
        foreach ($rows as $row) {
            $clean_username = ltrim(trim((string)$row->username), '@');
            $full_name = isset($row->full_name) && (string)$row->full_name !== '' ? (string)$row->full_name : $clean_username;
            $profile_image = isset($row->profile_image) ? (string)$row->profile_image : '';
            if ($profile_image === '') {
                $profile_image = "https://ui-avatars.com/api/?name=" . urlencode($full_name) . "&background=random&color=fff";
            }

            $eng_rate_raw = isset($row->eng_rate) ? $row->eng_rate : '';
            $eng_rate_str = '0%';
            if ($eng_rate_raw !== null && $eng_rate_raw !== '') {
                $eng_rate_str = (string)$eng_rate_raw;
                if (strpos($eng_rate_str, '%') === false) {
                    $eng_rate_str = rtrim($eng_rate_str) . '%';
                }
            }

            $bio = isset($row->bio) && (string)$row->bio !== '' ? (string)$row->bio : (isset($row->platform_bio) ? (string)$row->platform_bio : '');
            $loc = isset($row->location) ? trim((string)$row->location) : '';
            if (strtoupper($loc) === 'NA' || strtoupper($loc) === 'N/A') {
                $loc = '';
            } elseif ($loc === 'PH') {
                $loc = 'Philippines';
            } elseif ($loc === 'US') {
                $loc = 'United States';
            } elseif ($loc === 'UK' || $loc === 'GB') {
                $loc = 'United Kingdom';
            } elseif ($loc === 'CA') {
                $loc = 'Canada';
            } elseif ($loc === 'AU') {
                $loc = 'Australia';
            }

            $email = isset($row->email) && trim((string)$row->email) !== '' ? trim((string)$row->email) : $this->_extract_email_from_text($bio);
            $niche_str = isset($row->niche_list) && trim((string)$row->niche_list) !== '' ? trim((string)$row->niche_list) : '';

            $results[] = [
                'platform' => strtolower((string)$row->platform),
                'platform_id' => $row->id,
                'username' => $clean_username,
                'full_name' => $full_name,
                'profile_image' => $profile_image,
                'followers' => (int)$row->followers,
                'eng_rate' => $eng_rate_str,
                'avg_video_views' => (int)$row->avg_video_views,
                'location' => $loc,
                'niche' => $niche_str,
                'bio' => $bio,
                'website' => isset($row->external_url) ? (string)$row->external_url : '',
                'email' => $email,
                'has_email' => !empty($email),
                'recent_media' => [],
                'following' => 0,
                'posts_count' => 0,
                'notes' => ($bio !== '' ? $bio : 'Existing Influencer in Database') . ($niche_str !== '' ? ' | Niche: ' . $niche_str : ''),
                'profile_url' => $this->_get_profile_url($row->platform, $clean_username),
                'is_verified' => (bool)$row->verified_status,
                'is_local' => true
            ];
        }

        return $results;
    }

    private function _expand_niche_keywords($keyword) {
        $raw = trim((string)$keyword);
        $clean = ltrim($raw, '#@');
        $terms = [$raw, $clean];

        // Split camelCase (e.g. MinimalistStyle -> Minimalist, Style)
        $spaced = preg_replace('/([a-z])([A-Z])/', '$1 $2', $clean);
        if ($spaced !== $clean) {
            $terms[] = $spaced;
            foreach (explode(' ', $spaced) as $part) {
                $part = trim($part);
                if (strlen($part) >= 3) {
                    $terms[] = $part;
                }
            }
        }

        // Split by spaces, hyphens, underscores
        foreach (preg_split('/[\s\-_]+/', $clean) as $w) {
            $w = trim($w);
            if (strlen($w) >= 3) {
                $terms[] = $w;
            }
        }

        // Map common hashtag/niche synonyms so niche & hashtag searches match related creators
        $lower = strtolower($clean);
        $synonym_map = [
            'minimaliststyle' => ['fashion', 'style', 'ootd', 'lifestyle', 'minimalist'],
            'minimalist'      => ['fashion', 'style', 'lifestyle', 'ootd'],
            'ootd'            => ['fashion', 'style', 'outfit', 'ootd'],
            'fashion'         => ['fashion', 'ootd', 'style', 'luxury'],
            'style'           => ['fashion', 'style', 'ootd'],
            'wellnessjourney' => ['fitness', 'wellness', 'health', 'workout', 'lifestyle'],
            'wellness'        => ['fitness', 'wellness', 'health', 'workout'],
            'fitness'         => ['fitness', 'workout', 'gym', 'training', 'health'],
            'workout'         => ['fitness', 'workout', 'gym', 'bodybuilding'],
            'gym'             => ['fitness', 'workout', 'bodybuilding'],
            'handmadewithlove'=> ['lifestyle', 'art', 'design', 'fashion', 'craft', 'creator'],
            'handmade'        => ['lifestyle', 'art', 'design', 'craft'],
            'travelvlog'      => ['travel', 'vlog', 'wanderlust', 'explore'],
            'travel'          => ['travel', 'wanderlust', 'travelgram', 'followmeto'],
            'wanderlust'      => ['travel', 'wanderlust', 'travelgram'],
            'foodielife'      => ['food', 'foodie', 'cooking', 'chef', 'culinary', 'coffee'],
            'foodie'          => ['food', 'foodie', 'cooking', 'chef', 'culinary'],
            'food'            => ['food', 'foodie', 'cooking', 'chef', 'culinary'],
            'cooking'         => ['food', 'foodie', 'cooking', 'chef', 'culinary'],
            'beauty'          => ['beauty', 'skincare', 'makeup', 'cosmetics', 'glow'],
            'skincare'        => ['beauty', 'skincare', 'makeup', 'glowyskin'],
            'makeup'          => ['beauty', 'makeup', 'skincare', 'cosmetics'],
            'tech'            => ['tech', 'gadgets', 'smartphones', 'technology', 'gaming'],
            'technology'      => ['tech', 'gadgets', 'smartphones'],
            'gadgets'         => ['tech', 'gadgets', 'smartphones'],
            'gaming'          => ['gaming', 'gamer', 'streamer', 'twitch', 'mlbb'],
            'lifestyle'       => ['lifestyle', 'vlog', 'daily', 'fashion', 'beauty']
        ];

        foreach ($synonym_map as $key => $syns) {
            if ($lower === $key || strpos($lower, $key) !== false) {
                foreach ($syns as $s) {
                    $terms[] = $s;
                }
            }
        }

        return array_values(array_unique(array_filter($terms, function($t) {
            return trim((string)$t) !== '';
        })));
    }

    private function _matches_location_filter(array $item, $location) {
        $search_loc = strtolower(trim((string)$location));
        if ($search_loc === '') return true;

        $haystack = strtolower(
            ' ' . (string)($item['location'] ?? '') .
            ' | ' . (string)($item['notes'] ?? '') .
            ' | ' . (string)($item['bio'] ?? '') .
            ' | ' . (string)($item['full_name'] ?? '') . ' '
        );

        if (strpos($haystack, $search_loc) !== false) {
            return true;
        }

        $location_aliases = [
            'united states' => ['united states', 'usa', 'u.s.', 'america', 'los angeles', 'new york', 'miami', 'california', 'texas', 'chicago', 'seattle', 'boston', 'atlanta', ', ca', ', ny', ', fl', ', tx'],
            'usa'           => ['united states', 'usa', 'u.s.', 'america', 'los angeles', 'new york', 'miami', 'california', ', ca', ', ny', ', fl'],
            'us'            => ['united states', 'usa', 'los angeles', 'new york', 'miami', ', ca', ', ny', ', fl'],
            'united kingdom'=> ['united kingdom', 'uk', 'u.k.', 'great britain', 'england', 'london', 'manchester', 'birmingham', 'scotland'],
            'uk'            => ['united kingdom', 'uk', 'u.k.', 'great britain', 'england', 'london', 'manchester'],
            'london'        => ['london', 'united kingdom', 'uk'],
            'canada'        => ['canada', 'toronto', 'vancouver', 'montreal', 'ottawa', 'calgary', 'ontario'],
            'australia'     => ['australia', 'sydney', 'melbourne', 'adelaide', 'brisbane', 'perth'],
            'philippines'   => ['philippines', 'manila', 'cebu', 'calapan', 'taguig', 'bgc', 'makati', 'quezon', 'davao', 'mindoro', 'pasig', 'alabang', 'filipino', 'pinoy', '_ph'],
            'manila'        => ['manila', 'bgc', 'taguig', 'makati', 'quezon', 'pasig', 'philippines']
        ];

        $raw_loc_upper = strtoupper(trim((string)($item['location'] ?? '')));
        if (($search_loc === 'united states' || $search_loc === 'usa' || $search_loc === 'us') && $raw_loc_upper === 'US') return true;
        if (($search_loc === 'united kingdom' || $search_loc === 'uk' || $search_loc === 'london') && ($raw_loc_upper === 'GB' || $raw_loc_upper === 'UK')) return true;
        if (($search_loc === 'canada' || $search_loc === 'ca') && $raw_loc_upper === 'CA') return true;
        if (($search_loc === 'australia' || $search_loc === 'au') && $raw_loc_upper === 'AU') return true;
        if (($search_loc === 'philippines' || $search_loc === 'ph' || $search_loc === 'manila') && $raw_loc_upper === 'PH') return true;

        if (isset($location_aliases[$search_loc])) {
            foreach ($location_aliases[$search_loc] as $alias) {
                if (strpos($haystack, $alias) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    private function _detect_creator_gender(array $item) {
        if (!empty($item['gender']) && in_array(strtolower((string)$item['gender']), ['male', 'female'], true)) {
            return strtolower((string)$item['gender']);
        }

        $full_name = strtolower(trim((string)($item['full_name'] ?? '')));
        $username = strtolower(ltrim(trim((string)($item['username'] ?? '')), '@'));
        $bio = strtolower((string)($item['notes'] ?? '') . ' ' . (string)($item['bio'] ?? '') . ' ' . $full_name);

        $first_word = preg_replace('/[^a-z]/', '', explode(' ', $full_name)[0] ?? '');

        $female_names = [
            'sarah','jessica','emily','emma','olivia','sophia','ava','isabella','mia','charlotte','amelia','harper',
            'evelyn','abigail','ella','scarlett','grace','chloe','victoria','riley','aria','lily','aubrey','zoey',
            'penelope','lillian','addison','layla','natalie','camila','hannah','brooklyn','zoe','nora','leah',
            'savannah','audrey','claire','eleanor','skylar','ellie','samantha','stella','paisley','violet','mila',
            'allison','alexa','anna','hazel','aleyna','heart','chiara','camille','nikkie','nikkietutorials',
            'stephanie','glamzilla','kryz','kryzzzie','kayla','pamela','imane','pokimane','alix','angela','bea',
            'nicole','viy','alex','danica','xyriel','drea','kassandra','eya','marlyn','maryln','crystal','nelifer',
            'sierra','maya','elena','lucia','naomi','ruby','alice','madison','kennedy','sadie','peyton','serenity',
            'kylie','kendall','bella','gigi','selena','ariana','taylor','hailey','charli','dixie','addison','sabrina'
        ];

        $male_names = [
            'john','mike','michael','david','chris','christopher','daniel','james','robert','william','joseph',
            'thomas','charles','matthew','anthony','mark','donald','steven','paul','andrew','joshua','kenneth',
            'kevin','brian','george','timothy','ronald','edward','jason','jeffrey','ryan','jacob','gary','nicholas',
            'eric','jonathan','stephen','larry','justin','scott','brandon','benjamin','samuel','gregory','alexander',
            'patrick','frank','raymond','jack','dennis','jerry','tyler','aaron','jose','adam','nathan','henry',
            'zachary','douglas','peter','kyle','noah','ethan','jeremy','walter','christian','keith','roger','terry',
            'austin','sean','gerald','carl','harold','dylan','arthur','lawrence','jordan','jesse','bryan','billy',
            'bruce','gabriel','joe','logan','albert','willie','alan','juan','wayne','elijah','randy','roy','vincent',
            'ralph','eugene','russell','bobby','mason','philip','louis','marcus','simeon','gordon','erwan','murad',
            'marques','mkbhd','cong','marlon','yow','dudut','liam','edrian','jasper','andrei','yvoh','lucas','leo',
            'liam','oliver','elijah','mateo','levi','sebastian','ezra','owen','asher','hudson','caleb','isaiah'
        ];

        if (in_array($first_word, $female_names, true) || in_array($username, $female_names, true)) {
            return 'female';
        }
        if (in_array($first_word, $male_names, true) || in_array($username, $male_names, true)) {
            return 'male';
        }

        // Check bio & name indicators
        if (preg_match('/\b(she\/her|her|mom|mother|wife|girl|woman|women|queen|actress|sister|gal|lady|miss|mrs|beauty|makeup|grwm)\b/i', $bio)) {
            return 'female';
        }
        if (preg_match('/\b(he\/him|him|dad|father|husband|boy|man|men|king|actor|brother|guy|mr|bro|bodybuilding)\b/i', $bio)) {
            return 'male';
        }

        // Deterministic fallback based on username hash so every profile can be filtered cleanly
        return (abs(crc32($username ?: $full_name)) % 2 === 0) ? 'female' : 'male';
    }

    private function _get_profile_url($platform, $username) {
        switch (strtolower((string)$platform)) {
            case 'youtube': return "https://youtube.com/@" . $username;
            case 'instagram': return "https://instagram.com/" . $username;
            case 'tiktok': return "https://tiktok.com/@" . $username;
            default: return "#";
        }
    }

    private function _get_mock_data($platform, $keyword) {
        $mock_results = [];
        $raw_kw = trim((string)$keyword);
        $clean_key = preg_replace('/[^a-zA-Z0-9]/', '', ltrim($raw_kw, '#@'));
        if ($clean_key === '') $clean_key = 'Lifestyle';
        $slug = strtolower(substr($clean_key, 0, 14));
        $niche_title = ucfirst(strtolower(preg_replace('/([a-z])([A-Z])/', '$1 $2', $clean_key)));
        $hashtag_label = '#' . $clean_key;

        // Comprehensive Multi-Tier Creator Pool covering:
        // - All 5 Follower Tiers: Nano (1k-10k), Micro (10k-50k), Macro (50k-500k), Mega (500k-1M), Whale (1M+)
        // - All Major Filter Locations: United States, United Kingdom, Canada, Australia, Philippines
        // - Both Genders: Male & Female
        // - Both Email States: Exists (with business email) & Doesn't Exist (no email)
        // - Diverse Engagement Rates (2.1% to 11.4%)
        $real_creator_pools = [
            // NANO TIER (1k - 10k)
            ['username' => $slug . '_maya_us', 'name' => 'Maya Lin', 'gender' => 'female', 'followers' => 4800, 'eng' => '8.6%', 'loc' => 'Los Angeles, United States', 'has_email' => true, 'bio' => "$niche_title micro-creator & daily content curator $hashtag_label | LA, United States"],
            ['username' => 'liam_' . $slug . '_uk', 'name' => 'Liam Carter', 'gender' => 'male', 'followers' => 7200, 'eng' => '6.4%', 'loc' => 'London, United Kingdom', 'has_email' => false, 'bio' => "Sharing $niche_title inspiration & honest reviews $hashtag_label based in London, UK"],
            ['username' => $slug . '_chloe_ca', 'name' => 'Chloe Tremblay', 'gender' => 'female', 'followers' => 3400, 'eng' => '9.2%', 'loc' => 'Toronto, Canada', 'has_email' => true, 'bio' => "Canadian $niche_title enthusiast & aesthetic creator $hashtag_label"],
            ['username' => 'noah_' . $slug . '_au', 'name' => 'Noah Mitchell', 'gender' => 'male', 'followers' => 8900, 'eng' => '5.8%', 'loc' => 'Sydney, Australia', 'has_email' => true, 'bio' => "$niche_title & lifestyle vlogger in Sydney, Australia $hashtag_label"],
            ['username' => $slug . '_bea_mnl', 'name' => 'Bea Villanueva', 'gender' => 'female', 'followers' => 6100, 'eng' => '10.8%', 'loc' => 'Manila, Philippines', 'has_email' => false, 'bio' => "Pinay $niche_title creator & UGC specialist $hashtag_label | Manila, Philippines"],

            // MICRO TIER (10k - 50k)
            ['username' => 'sophia_' . $slug . 'daily', 'name' => 'Sophia Brooks', 'gender' => 'female', 'followers' => 28500, 'eng' => '7.8%', 'loc' => 'New York, United States', 'has_email' => true, 'bio' => "NYC $niche_title blogger & brand partner $hashtag_label | United States"],
            ['username' => 'ethan_' . $slug . 'hq', 'name' => 'Ethan Wright', 'gender' => 'male', 'followers' => 41200, 'eng' => '5.5%', 'loc' => 'Austin, United States', 'has_email' => false, 'bio' => "Full-time $niche_title creator & reviewer $hashtag_label | United States"],
            ['username' => 'amelia_' . $slug . '_ldn', 'name' => 'Amelia Clarke', 'gender' => 'female', 'followers' => 19800, 'eng' => '8.1%', 'loc' => 'Manchester, United Kingdom', 'has_email' => true, 'bio' => "UK $niche_title & lifestyle tips every week $hashtag_label | United Kingdom"],
            ['username' => 'lucas_' . $slug . '_ca', 'name' => 'Lucas Roy', 'gender' => 'male', 'followers' => 34600, 'eng' => '6.9%', 'loc' => 'Vancouver, Canada', 'has_email' => true, 'bio' => "$niche_title storyteller & visual creator in Vancouver, Canada $hashtag_label"],
            ['username' => 'ruby_' . $slug . '_au', 'name' => 'Ruby Henderson', 'gender' => 'female', 'followers' => 24900, 'eng' => '7.3%', 'loc' => 'Melbourne, Australia', 'has_email' => false, 'bio' => "Aussie $niche_title community & daily routines $hashtag_label | Australia"],
            ['username' => 'miguel_' . $slug . '_ph', 'name' => 'Miguel Santos', 'gender' => 'male', 'followers' => 45800, 'eng' => '9.4%', 'loc' => 'BGC Taguig, Philippines', 'has_email' => true, 'bio' => "$niche_title content creator & community lead $hashtag_label | Manila, Philippines"],

            // MACRO TIER (50k - 500k)
            ['username' => 'olivia_' . $slug . '_co', 'name' => 'Olivia Bennett', 'gender' => 'female', 'followers' => 185000, 'eng' => '6.5%', 'loc' => 'Miami, United States', 'has_email' => true, 'bio' => "$niche_title & modern lifestyle creator $hashtag_label | Miami, United States"],
            ['username' => 'daniel_' . $slug . 'pro', 'name' => 'Daniel Foster', 'gender' => 'male', 'followers' => 310000, 'eng' => '5.4%', 'loc' => 'Los Angeles, United States', 'has_email' => true, 'bio' => "Creative director & $niche_title authority $hashtag_label | United States"],
            ['username' => 'charlotte_' . $slug . 'uk', 'name' => 'Charlotte Davies', 'gender' => 'female', 'followers' => 142000, 'eng' => '7.2%', 'loc' => 'London, United Kingdom', 'has_email' => false, 'bio' => "London-based $niche_title curator & host $hashtag_label | United Kingdom"],
            ['username' => 'mason_' . $slug . '_to', 'name' => 'Mason Campbell', 'gender' => 'male', 'followers' => 225000, 'eng' => '6.1%', 'loc' => 'Toronto, Canada', 'has_email' => true, 'bio' => "$niche_title videos & deep dives $hashtag_label | Toronto, Canada"],
            ['username' => 'sienna_' . $slug . '_oz', 'name' => 'Sienna Collins', 'gender' => 'female', 'followers' => 168000, 'eng' => '8.0%', 'loc' => 'Sydney, Australia', 'has_email' => true, 'bio' => "Australian $niche_title ambassador & digital creator $hashtag_label"],
            ['username' => $slug . '_creator_ph', 'name' => 'Angela Cruz', 'gender' => 'female', 'followers' => 485000, 'eng' => '5.2%', 'loc' => 'Manila, Philippines', 'has_email' => true, 'bio' => "Official $niche_title creator & brand ambassador $hashtag_label | Manila, Philippines"],

            // MEGA TIER (500k - 1M)
            ['username' => 'hannah_' . $slug . 'world', 'name' => 'Hannah Reynolds', 'gender' => 'female', 'followers' => 680000, 'eng' => '6.3%', 'loc' => 'New York, United States', 'has_email' => true, 'bio' => "Award-winning $niche_title creator & podcast host $hashtag_label | United States"],
            ['username' => 'james_' . $slug . '_uk', 'name' => 'James Sterling', 'gender' => 'male', 'followers' => 740000, 'eng' => '5.7%', 'loc' => 'London, United Kingdom', 'has_email' => true, 'bio' => "UK $niche_title & culture channel $hashtag_label | London, United Kingdom"],
            ['username' => 'ava_' . $slug . '_canada', 'name' => 'Ava Morrison', 'gender' => 'female', 'followers' => 590000, 'eng' => '7.5%', 'loc' => 'Montreal, Canada', 'has_email' => false, 'bio' => "$niche_title lifestyle & creative studio $hashtag_label | Canada"],
            ['username' => 'jack_' . $slug . '_au', 'name' => 'Jack Thompson', 'gender' => 'male', 'followers' => 820000, 'eng' => '5.1%', 'loc' => 'Brisbane, Australia', 'has_email' => true, 'bio' => "$niche_title adventures & reviews $hashtag_label | Australia"],
            ['username' => $slug . '_studio', 'name' => 'Marcus Tan', 'gender' => 'male', 'followers' => 890000, 'eng' => '6.1%', 'loc' => 'Manila, Philippines', 'has_email' => true, 'bio' => "Creative Director & $niche_title specialist $hashtag_label | Manila, Philippines"],
            ['username' => $slug . '_insider', 'name' => 'David Guison', 'gender' => 'male', 'followers' => 640000, 'eng' => '5.9%', 'loc' => 'BGC Taguig, Philippines', 'has_email' => true, 'bio' => "Content Creator & $niche_title curator $hashtag_label | Philippines"],

            // WHALE TIER (1M+)
            ['username' => 'real_' . $slug . '_daily', 'name' => 'Nicole Anders', 'gender' => 'female', 'followers' => 1750000, 'eng' => '6.4%', 'loc' => 'Los Angeles, United States', 'has_email' => true, 'bio' => "$niche_title Enthusiast & Vlogger $hashtag_label | Los Angeles, United States"],
            ['username' => 'alex_' . $slug . '_official', 'name' => 'Alexander Reed', 'gender' => 'male', 'followers' => 2350000, 'eng' => '5.6%', 'loc' => 'London, United Kingdom', 'has_email' => true, 'bio' => "Global $niche_title creator & entrepreneur $hashtag_label | London, United Kingdom"],
            ['username' => 'elena_' . $slug . '_ca', 'name' => 'Elena Vance', 'gender' => 'female', 'followers' => 1420000, 'eng' => '7.1%', 'loc' => 'Toronto, Canada', 'has_email' => true, 'bio' => "$niche_title & lifestyle icon $hashtag_label | Toronto, Canada"],
            ['username' => 'harper_' . $slug . '_au', 'name' => 'Harper Walsh', 'gender' => 'female', 'followers' => 1650000, 'eng' => '6.8%', 'loc' => 'Sydney, Australia', 'has_email' => false, 'bio' => "Australian $niche_title & wellness creator $hashtag_label | Sydney, Australia"],
            ['username' => 'the_' . $slug . '_vlog', 'name' => 'Samantha Reyes', 'gender' => 'female', 'followers' => 1250000, 'eng' => '4.8%', 'loc' => 'Cebu, Philippines', 'has_email' => true, 'bio' => "Digital creator sharing $niche_title tips & product reviews $hashtag_label | Philippines"],
            ['username' => 'styleby_' . $slug, 'name' => 'Bea Alonzo', 'gender' => 'female', 'followers' => 3100000, 'eng' => '7.4%', 'loc' => 'Manila, Philippines', 'has_email' => true, 'bio' => "Lifestyle & $niche_title ambassador $hashtag_label | Manila, Philippines"],
        ];

        $colors = ['0F6B3E', '1FAE5B', '2563EB', '7C3AED', 'DB2777', 'D97706'];
        foreach ($real_creator_pools as $idx => $c) {
            $base_url = ($platform === 'youtube') ? "https://youtube.com/@" : (($platform === 'tiktok') ? "https://tiktok.com/@" : "https://instagram.com/");
            $email = $c['has_email'] ? (strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $c['name'])) . "@creatorcollab.com") : '';
            $bg = $colors[$idx % count($colors)];

            $mock_results[] = [
                'platform' => $platform ?: 'instagram',
                'username' => $c['username'],
                'full_name' => $c['name'],
                'gender' => $c['gender'],
                'niche' => $niche_title,
                'profile_image' => "https://ui-avatars.com/api/?name=" . urlencode($c['name']) . "&background=$bg&color=fff",
                'followers' => $c['followers'],
                'eng_rate' => $c['eng'],
                'growth_rate' => '+3.4%',
                'avg_video_views' => round($c['followers'] * 0.22),
                'location' => $c['loc'],
                'notes' => $c['bio'] . ($email ? " | Contact: " . $email : ''),
                'bio' => $c['bio'],
                'email' => $email,
                'has_email' => $c['has_email'],
                'profile_url' => $base_url . $c['username'],
                'is_verified' => ($c['followers'] >= 100000 || ($idx % 3 === 0))
            ];
        }

        return $mock_results;
    }

    private function _load_env() {
        $env_path = FCPATH . '.env';
        if (file_exists($env_path)) {
            $lines = file($env_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) continue;
                $parts = explode('=', $line, 2);
                if (count($parts) === 2) {
                    $key = trim($parts[0]);
                    $value = trim($parts[1]);
                    if ($value !== '' && (($value[0] === '"' && substr($value, -1) === '"') || ($value[0] === "'" && substr($value, -1) === "'"))) {
                        $value = substr($value, 1, -1);
                    }

                    $matches = [];
                    preg_match_all('/([A-Z][A-Z0-9_]{2,})=/', $value, $matches, PREG_OFFSET_CAPTURE);
                    if (!empty($matches[1])) {
                        $first_offset = (int)$matches[1][0][1];
                        if ($first_offset > 0) {
                            $main_value = substr($value, 0, $first_offset);
                            $main_value = trim($main_value);
                            if ($main_value !== '' && (($main_value[0] === '"' && substr($main_value, -1) === '"') || ($main_value[0] === "'" && substr($main_value, -1) === "'"))) {
                                $main_value = substr($main_value, 1, -1);
                            }
                            putenv($key . '=' . $main_value);
                            $_ENV[$key] = $main_value;

                            for ($i = 0; $i < count($matches[1]); $i++) {
                                $k2 = (string)$matches[1][$i][0];
                                $start = (int)$matches[1][$i][1];
                                $val_start = $start + strlen($k2) + 1;
                                $end = ($i + 1 < count($matches[1])) ? (int)$matches[1][$i + 1][1] : strlen($value);
                                $v2 = substr($value, $val_start, $end - $val_start);
                                $v2 = trim($v2);
                                if ($v2 !== '' && (($v2[0] === '"' && substr($v2, -1) === '"') || ($v2[0] === "'" && substr($v2, -1) === "'"))) {
                                    $v2 = substr($v2, 1, -1);
                                }
                                putenv($k2 . '=' . $v2);
                                $_ENV[$k2] = $v2;
                            }
                            continue;
                        }
                    }

                    putenv($key . '=' . $value);
                    $_ENV[$key] = $value;
                }
            }
        }
    }

    private function _search_ayrshare($platform, $keyword) {
        $this->load->library('ayrshare');
        return $this->ayrshare->searchProfiles($platform, $keyword);
    }

    private function _scrape_tiktok_profile_direct($username) {
        $clean_u = strtolower(ltrim(trim((string)$username), '@#'));
        if ($clean_u === '' || preg_match('/[^a-zA-Z0-9_.-]/', $clean_u)) {
            return null;
        }

        if (!isset($this->cache)) {
            $this->load->driver('cache', array('adapter' => 'file'));
        }

        $cache_key = 'tt_direct_prof_v2_' . md5($clean_u);
        if ($cached = $this->cache->get($cache_key)) {
            return $cached;
        }

        $url = "https://www.tiktok.com/@" . rawurlencode($clean_u);
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_COOKIEFILE => '',
            CURLOPT_USERAGENT => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1',
            CURLOPT_HTTPHEADER => [
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language: en-US,en;q=0.9'
            ]
        ]);
        $html = curl_exec($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code !== 200 || empty($html)) {
            return null;
        }

        if (!preg_match('/<script id="__UNIVERSAL_DATA_FOR_REHYDRATION__"[^>]*>(.*?)<\/script>/s', (string)$html, $m)) {
            return null;
        }

        $decoded = json_decode($m[1], true);
        $uinfo = $decoded['__DEFAULT_SCOPE__']['webapp.user-detail']['userInfo'] ?? null;
        if (empty($uinfo) || empty($uinfo['user'])) {
            return null;
        }

        $u = $uinfo['user'];
        $stats = $uinfo['stats'] ?? [];
        $unique_id = trim((string)($u['uniqueId'] ?? $clean_u));
        if ($unique_id === '') return null;

        $full_name = trim((string)($u['nickname'] ?? $unique_id));
        $bio = (string)($u['signature'] ?? '');
        $bio_link = (string)($u['bioLink']['link'] ?? '');
        $followers = (int)($stats['followerCount'] ?? 0);
        $following = (int)($stats['followingCount'] ?? 0);
        $posts_count = (int)($stats['videoCount'] ?? 0);
        $hearts = (int)($stats['heartCount'] ?? ($stats['heart'] ?? 0));

        // Calculate accurate TikTok engagement rate
        $eng_rate = '0%';
        $avg_likes_per_vid = ($posts_count > 0 && $hearts > 0) ? ($hearts / $posts_count) : 0;
        if ($followers > 0 && $avg_likes_per_vid > 0) {
            if ($avg_likes_per_vid <= ($followers * 0.35)) {
                $rate_val = ($avg_likes_per_vid / $followers) * 100.0;
            } else {
                $est_reach = max($followers * 4.0, $avg_likes_per_vid * 9.5);
                $rate_val = ($avg_likes_per_vid / $est_reach) * 100.0;
            }
            $eng_rate = number_format(min(35.0, max(0.1, $rate_val)), 2) . '%';
        }

        $raw_avatar = $u['avatarMedium'] ?? ($u['avatarLarger'] ?? ($u['avatarThumb'] ?? ''));
        $avatar = $raw_avatar !== '' ? $this->_cache_avatar_locally($raw_avatar, 'tt_' . $unique_id) : '';
        if ($avatar === '') {
            $avatar = "https://ui-avatars.com/api/?name=" . urlencode($full_name) . "&background=0F6B3E&color=fff";
        }

        $extracted_email = $this->_extract_email_from_text($bio . ' ' . $full_name . ' ' . $bio_link);
        $loc_detected = $this->_extract_location_from_text($bio . ' ' . $full_name . ' ' . ($u['region'] ?? ''));

        $res = [
            'platform' => 'tiktok',
            'platform_id' => (string)($u['id'] ?? $unique_id),
            'username' => $unique_id,
            'full_name' => $full_name,
            'profile_image' => $avatar,
            'followers' => $followers,
            'following' => $following,
            'posts_count' => $posts_count,
            'eng_rate' => $eng_rate,
            'avg_video_views' => (int)round($avg_likes_per_vid * 8.5),
            'location' => $loc_detected,
            'niche' => $unique_id,
            'bio' => $bio,
            'website' => $bio_link,
            'is_verified' => !empty($u['verified']),
            'is_private' => !empty($u['privateAccount']) || !empty($u['secret']),
            'recent_media' => [
                [
                    'caption' => $bio !== '' ? $bio : ('TikTok Creator Profile (@' . $unique_id . ')'),
                    'media_url' => $avatar,
                    'media_type' => 'VIDEO',
                    'likes' => (int)round($avg_likes_per_vid),
                    'comments' => 0,
                    'timestamp' => date('c'),
                    'permalink' => "https://www.tiktok.com/@" . $unique_id
                ]
            ],
            'notes' => '',
            'profile_url' => "https://www.tiktok.com/@" . $unique_id,
            'email' => $extracted_email,
            'has_email' => !empty($extracted_email)
        ];

        $this->_sync_local_account($res);
        $this->cache->save($cache_key, $res, 3600);
        return $res;
    }

    private function _search_tiktok($keyword) {
        $clean_kw = strtolower(str_replace(['@', '#', ' '], '', trim((string)$keyword)));
        if ($clean_kw === '') return [];

        $results = [];

        // 1. Direct real-time TikTok profile SSR scrape (gets real followers, avatar, hearts, videos, bio)
        $direct_item = $this->_scrape_tiktok_profile_direct($clean_kw);
        if ($direct_item) {
            $results[] = $direct_item;
            return $results;
        }

        // 2. Check Zocialmine API for TikTok
        if (!empty($this->zocialmine_api_key)) {
            $zocial_item = $this->_search_tiktok_zocialmine($clean_kw);
            if ($zocial_item) {
                $results[] = $zocial_item;
                return $results;
            }
        }

        // 3. Check Ayrshare API connected TikTok accounts
        if (!empty($this->ayrshare_api_key)) {
            $ayr_results = $this->_search_ayrshare('tiktok', $keyword);
            if (!empty($ayr_results)) {
                $results = array_merge($results, $ayr_results);
            }
        }

        return $results;
    }

    private function _search_tiktok_zocialmine($username) {
        if (empty($this->zocialmine_api_key)) return null;

        $url = "https://api.zocialmine.com/v1/tiktok/profile";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "x-api-key: {$this->zocialmine_api_key}",
            "Content-Type: application/json"
        ]);
        
        $clean_username = ltrim(trim($username), '@');
        $post_data = ['url' => "https://www.tiktok.com/@$clean_username"];
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        
        $resp = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code !== 200 || empty($resp)) {
            return null;
        }

        $decoded = json_decode($resp, true);
        if (!isset($decoded['status']) || $decoded['status'] !== 'success' || empty($decoded['data'])) {
            return null;
        }

        $d = $decoded['data'];
        $user = $d['user'] ?? [];
        $stats = $d['stats'] ?? [];

        $bio = $user['signature'] ?? '';
        $extracted_email = $this->_extract_email_from_text($bio . ' ' . ($user['nickname'] ?? ''));

        $full_name = !empty($user['nickname']) ? $user['nickname'] : $clean_username;
        $profile_image = $user['avatarMedium'] ?? ($user['avatarLarger'] ?? "https://ui-avatars.com/api/?name=" . urlencode($full_name) . "&background=0F6B3E&color=fff");

        return [
            'platform' => 'tiktok',
            'platform_id' => $user['id'] ?? $clean_username,
            'username' => $user['uniqueId'] ?? $clean_username,
            'full_name' => $full_name,
            'profile_image' => $profile_image,
            'followers' => (int)($stats['followerCount'] ?? 0),
            'following' => (int)($stats['followingCount'] ?? 0),
            'posts_count' => (int)($stats['videoCount'] ?? 0),
            'eng_rate' => $this->_calculate_tiktok_engagement($stats),
            'bio' => $bio,
            'website' => $user['bioLink']['link'] ?? '',
            'is_verified' => !empty($user['verified']),
            'is_private' => !empty($user['privateAccount']) || !empty($user['secret']),
            'recent_media' => [
                [
                    'caption' => $bio !== '' ? $bio : 'TikTok Creator Profile (@' . $clean_username . ')',
                    'media_url' => $profile_image,
                    'media_type' => 'VIDEO',
                    'likes' => (int)($stats['heart'] ?? 0),
                    'comments' => 0,
                    'timestamp' => date('Y-m-d'),
                    'permalink' => "https://www.tiktok.com/@" . ($user['uniqueId'] ?? $clean_username)
                ]
            ],
            'notes' => '',
            'profile_url' => "https://www.tiktok.com/@" . ($user['uniqueId'] ?? $clean_username),
            'email' => $extracted_email,
            'has_email' => !empty($extracted_email)
        ];
    }

    private function _calculate_tiktok_engagement($stats) {
        $followers = (int)($stats['followerCount'] ?? 0);
        if ($followers === 0) return '0%';
        $heart = (int)($stats['heart'] ?? 0);
        $videoCount = (int)($stats['videoCount'] ?? 0);
        if ($videoCount > 0) {
            $avg_likes = $heart / $videoCount;
            $rate = ($avg_likes / $followers) * 100;
        } else {
            $rate = ($heart / $followers) * 100;
        }
        return number_format(min(100.0, $rate), 2) . '%';
    }

    private function _tiktok_username_lookup($username) {
        $username = strtolower(ltrim(trim((string)$username), '@'));
        if ($username === '') return null;

        // 1. Direct real-time TikTok profile SSR scrape (fast, accurate followers, avatar, engagement)
        $direct_item = $this->_scrape_tiktok_profile_direct($username);
        if ($direct_item) {
            return $direct_item;
        }

        // 2. Try Zocialmine fallback
        if (!empty($this->zocialmine_api_key)) {
            $zocial_item = $this->_search_tiktok_zocialmine($username);
            if ($zocial_item) {
                return $zocial_item;
            }
        }

        // 3. Try Ayrshare fallback
        if (!empty($this->ayrshare_api_key)) {
            $ayr = $this->_search_ayrshare('tiktok', $username);
            if (!empty($ayr[0])) {
                return $ayr[0];
            }
        }

        return null;
    }

    private function _extract_email_from_text($text) {
        if (empty($text)) return '';
        // Normalize obfuscated email patterns like " [at] ", " (at) ", " AT ", " [dot] ", " (dot) "
        $text = preg_replace('/\s*[\(\[\{]?\s*at\s*[\)\]\}]?\s*/i', '@', (string)$text);
        $text = preg_replace('/\s*[\(\[\{]?\s*dot\s*[\)\]\}]?\s*/i', '.', (string)$text);

        if (preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', (string)$text, $matches)) {
            $email = strtolower(trim($matches[0]));
            if (strpos($email, 'example.com') === false && strpos($email, 'placeholder') === false && strpos($email, 'domain.com') === false) {
                return $email;
            }
        }
        return '';
    }

    private function _extract_location_from_text($text) {
        $raw = trim((string)$text);
        if ($raw === '') return '';

        // 1. Check explicit pin emoji "📍 Location"
        if (preg_match('/📍\s*([A-Za-z0-9\s,\.\-\/]{2,35})/u', $raw, $m)) {
            $pinned = trim(preg_split('/[\r\n\|•✨🤍💪🔥]/u', $m[1])[0] ?? '');
            $pinned = trim($pinned, " \t\n\r\0\x0B,.-/");
            if (strlen($pinned) >= 2) {
                return $pinned;
            }
        }

        $lower = strtolower($raw);

        // 2. Check flag emojis or domain TLD indicators
        if (strpos($raw, '🇺🇸') !== false) return 'United States';
        if (strpos($raw, '🇬🇧') !== false) return 'United Kingdom';
        if (strpos($raw, '🇨🇦') !== false) return 'Canada';
        if (strpos($raw, '🇦🇺') !== false || strpos($lower, '.com.au') !== false) return 'Australia';
        if (strpos($raw, '🇵🇭') !== false || strpos($lower, '.com.ph') !== false) return 'Philippines';

        // 3. Check explicit city/state/country mentions in real bio/caption
        $patterns = [
            'Los Angeles, United States' => '/\b(los angeles|l\.a\.|hollywood|california|socal)\b/i',
            'New York, United States'    => '/\b(new york|nyc|brooklyn|manhattan)\b/i',
            'Miami, United States'       => '/\b(miami|florida)\b/i',
            'United States'              => '/\b(united states|u\.s\.a\.|usa|texas|austin|chicago|seattle|boston|atlanta)\b/i',
            'London, United Kingdom'     => '/\b(london)\b/i',
            'United Kingdom'             => '/\b(united kingdom|u\.k\.|england|manchester|birmingham|scotland|britain)\b/i',
            'Toronto, Canada'            => '/\b(toronto|ontario)\b/i',
            'Vancouver, Canada'          => '/\b(vancouver)\b/i',
            'Canada'                     => '/\b(canada|montreal|calgary|ottawa)\b/i',
            'Sydney, Australia'          => '/\b(sydney)\b/i',
            'Melbourne, Australia'       => '/\b(melbourne)\b/i',
            'Australia'                  => '/\b(australia|brisbane|perth|adelaide|aussie)\b/i',
            'Manila, Philippines'        => '/\b(manila|makati|taguig|bgc|quezon city|pasig)\b/i',
            'Cebu, Philippines'          => '/\b(cebu)\b/i',
            'Philippines'                => '/\b(philippines|davao|calapan|mindoro|pinoy|pinay|filipino)\b/i',
        ];

        foreach ($patterns as $label => $regex) {
            if (preg_match($regex, $raw)) {
                return $label;
            }
        }

        return '';
    }

    private function _wrap_ig_image_proxy($url) {
        $u = trim((string)$url);
        if ($u === '' || !filter_var($u, FILTER_VALIDATE_URL)) return '';
        $cache_file = FCPATH . 'assets/cache/ig_media/' . md5($u) . '.jpg';
        if (file_exists($cache_file) && filesize($cache_file) > 100) {
            return '/assets/cache/ig_media/' . md5($u) . '.jpg';
        }
        if (strpos($u, 'cdninstagram.com') !== false || strpos($u, 'fbcdn.net') !== false || strpos($u, 'tiktokcdn.com') !== false) {
            return '/home/image_proxy?url=' . rawurlencode($u);
        }
        return $u;
    }

    private function _cache_recent_media_batch(array &$recent_media) {
        if (empty($recent_media)) return;

        $cache_dir = FCPATH . 'assets/cache/ig_media/';
        if (!is_dir($cache_dir)) {
            @mkdir($cache_dir, 0777, true);
        }

        $mh = curl_multi_init();
        $handles = [];

        foreach ($recent_media as $idx => &$m) {
            $m_url = trim((string)($m['media_url'] ?? ''));
            $raw_u = $m_url;
            if (strpos($m_url, '/home/image_proxy?url=') === 0 || strpos($m_url, '/discovery/image_proxy?url=') === 0) {
                $parts = explode('?url=', $m_url, 2);
                $raw_u = isset($parts[1]) ? rawurldecode($parts[1]) : '';
            }
            if ($raw_u === '' || !filter_var($raw_u, FILTER_VALIDATE_URL)) {
                continue;
            }

            $hash_name = md5($raw_u) . '.jpg';
            $disk_path = $cache_dir . $hash_name;
            if (file_exists($disk_path) && filesize($disk_path) > 100) {
                $m['media_url'] = '/assets/cache/ig_media/' . $hash_name;
                continue;
            }

            // Pre-cache up to 6 recent posts per creator in parallel
            if (count($handles) < 6) {
                $ref = (strpos($raw_u, 'tiktokcdn.com') !== false) ? 'https://www.tiktok.com/' : 'https://www.instagram.com/';
                $ch = curl_init($raw_u);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_CONNECTTIMEOUT => 4,
                    CURLOPT_TIMEOUT => 7,
                    CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
                    CURLOPT_HTTPHEADER => [
                        'Accept: image/jpeg,image/png,image/webp,image/*,*/*;q=0.8',
                        'Referer: ' . $ref
                    ]
                ]);
                curl_multi_add_handle($mh, $ch);
                $handles[$idx] = [
                    'ch' => $ch,
                    'disk_path' => $disk_path,
                    'local_url' => '/assets/cache/ig_media/' . $hash_name,
                    'raw_u' => $raw_u
                ];
            } else {
                $m['media_url'] = $this->_wrap_ig_image_proxy($raw_u);
            }
        }
        unset($m);

        if (!empty($handles)) {
            $running = null;
            do {
                $status = curl_multi_exec($mh, $running);
                if ($running) {
                    curl_multi_select($mh, 0.2);
                }
            } while ($running && $status == CURLM_OK);

            foreach ($handles as $idx => $h) {
                $img = curl_multi_getcontent($h['ch']);
                $code = (int)curl_getinfo($h['ch'], CURLINFO_HTTP_CODE);
                curl_multi_remove_handle($mh, $h['ch']);
                curl_close($h['ch']);

                if ($code >= 200 && $code < 300 && !empty($img) && strlen($img) > 100) {
                    if (@file_put_contents($h['disk_path'], $img) !== false) {
                        $recent_media[$idx]['media_url'] = $h['local_url'];
                        continue;
                    }
                }
                $recent_media[$idx]['media_url'] = $this->_wrap_ig_image_proxy($h['raw_u']);
            }
        }

        curl_multi_close($mh);
    }

    private function _cache_avatar_locally($url, $username = '') {
        $u = trim((string)$url);
        if ($u === '' || !filter_var($u, FILTER_VALIDATE_URL)) return '';

        $cache_dir = FCPATH . 'assets/cache/avatars/';
        if (!is_dir($cache_dir)) {
            @mkdir($cache_dir, 0777, true);
        }

        $safe_user = preg_replace('/[^a-zA-Z0-9_.-]/', '', strtolower((string)$username));
        $file_name = ($safe_user !== '' ? ('av_' . $safe_user . '_' . substr(md5($u), 0, 8)) : ('av_' . md5($u))) . '.jpg';
        $file_path = $cache_dir . $file_name;

        if (file_exists($file_path) && filesize($file_path) > 200) {
            return '/assets/cache/avatars/' . $file_name;
        }

        $ref = (strpos($u, 'tiktokcdn.com') !== false) ? 'https://www.tiktok.com/' : 'https://www.instagram.com/';
        $ch = curl_init($u);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_CONNECTTIMEOUT => 4,
            CURLOPT_TIMEOUT => 8,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
            CURLOPT_HTTPHEADER => [
                'Accept: image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
                'Referer: ' . $ref
            ]
        ]);
        $img = curl_exec($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code >= 200 && $code < 300 && !empty($img) && strlen($img) > 200) {
            if (@file_put_contents($file_path, $img) !== false) {
                return '/assets/cache/avatars/' . $file_name;
            }
        }

        return $this->_wrap_ig_image_proxy($u);
    }

    private function _extract_profile_image($item, $full_name = '') {
        if (!is_array($item)) {
            return "https://ui-avatars.com/api/?name=" . urlencode($full_name ?: 'User') . "&background=1FAE5B&color=fff";
        }

        // Prefer standard 150x150 profilePicUrl first for fast local avatar caching, fallback to HD
        $pic = $item['profilePicUrl']
            ?? ($item['profilePicUrlHD']
            ?? ($item['profilePicUrlHd'] 
            ?? ($item['profile_pic_url'] 
            ?? ($item['profile_pic_url_hd'] 
            ?? ($item['profilePictureUrl'] 
            ?? ($item['hdProfilePicUrlInfo']['url'] 
            ?? ($item['hd_profile_pic_url_info']['url'] 
            ?? ($item['profile_image'] 
            ?? ''))))))));

        if (!empty($pic) && is_string($pic)) {
            $pic = trim(stripslashes(str_replace('\/', '/', $pic)));
            if (filter_var($pic, FILTER_VALIDATE_URL)) {
                $uname = (string)($item['username'] ?? ($item['ownerUsername'] ?? ''));
                $cached_local = $this->_cache_avatar_locally($pic, $uname);
                if ($cached_local !== '') {
                    return $cached_local;
                }
            }
        }

        return "https://ui-avatars.com/api/?name=" . urlencode($full_name ?: 'User') . "&background=1FAE5B&color=fff";
    }

    private function _calculate_accurate_engagement_rate($followers, array $recent_media) {
        $followers = (int)$followers;
        if ($followers <= 0 || empty($recent_media)) {
            return ['eng_rate' => '0%', 'avg_eng' => 0];
        }

        $eng_values = [];
        foreach ($recent_media as $rm) {
            $likes_val = max(0, (int)($rm['likes'] ?? 0));
            $comm_val = max(0, (int)($rm['comments'] ?? 0));
            $post_eng = $likes_val + $comm_val;
            if ($post_eng > 0) {
                $eng_values[] = $post_eng;
            }
        }

        if (empty($eng_values)) {
            return ['eng_rate' => '0%', 'avg_eng' => 0];
        }

        sort($eng_values, SORT_NUMERIC);

        // Separate regular feed/reel posts from extreme viral/collab outliers (> 50% of follower count)
        $organic_posts = array_values(array_filter($eng_values, function($val) use ($followers) {
            return $val <= ($followers * 0.50);
        }));

        if (!empty($organic_posts)) {
            $n = count($organic_posts);
            if ($n >= 4) {
                // Interquartile Mean (middle 50% of organic posts) for rock-solid accuracy
                $trim = (int)floor($n * 0.25);
                $slice = array_slice($organic_posts, $trim, max(1, $n - ($trim * 2)));
                $rep_eng = array_sum($slice) / count($slice);
            } else {
                // Median of organic posts
                $mid = (int)floor(($n - 1) / 2);
                $rep_eng = ($n % 2 === 1) ? $organic_posts[$mid] : (($organic_posts[$mid] + $organic_posts[$mid + 1]) / 2.0);
            }
            $calc_rate = ($rep_eng / $followers) * 100.0;
        } else {
            // Every post is a high-reach viral Reel exceeding 50% of followers; normalize by estimated Reel reach
            $n = count($eng_values);
            $mid = (int)floor(($n - 1) / 2);
            $rep_eng = ($n % 2 === 1) ? $eng_values[$mid] : (($eng_values[$mid] + $eng_values[$mid + 1]) / 2.0);
            $est_reach = max($followers * 5.0, $rep_eng * 8.5);
            $calc_rate = ($rep_eng / $est_reach) * 100.0;
        }

        $calc_rate = min(35.0, max(0.05, $calc_rate));
        return [
            'eng_rate' => number_format($calc_rate, 2) . '%',
            'avg_eng' => (int)round($rep_eng)
        ];
    }

    private function _format_apify_ig_profile(array $item, $niche_label = '', array $fallback_post = []) {
        $username = trim((string)($item['username'] ?? ($item['ownerUsername'] ?? '')));
        if ($username === '') return null;

        $full_name = trim((string)($item['fullName'] ?? ($item['ownerFullName'] ?? $username)));
        if ($full_name === '') $full_name = $username;

        $bio = (string)($item['biography'] ?? '');
        $ext_url = (string)($item['externalUrl'] ?? '');
        if ($ext_url === '' && !empty($item['externalUrls'][0]['url'])) {
            $ext_url = (string)$item['externalUrls'][0]['url'];
        }

        $direct_email = $item['email'] ?? ($item['businessEmail'] ?? ($item['publicEmail'] ?? ''));
        $extracted_email = !empty($direct_email) ? strtolower(trim((string)$direct_email)) : $this->_extract_email_from_text($bio . ' ' . $full_name . ' ' . $ext_url);

        $followers = (int)($item['followersCount'] ?? ($item['followers'] ?? 0));
        $following = (int)($item['followsCount'] ?? ($item['following'] ?? 0));
        $posts_count = (int)($item['postsCount'] ?? 0);

        $recent_media = [];
        $captions_concat = '';
        if (isset($item['latestPosts']) && is_array($item['latestPosts'])) {
            foreach ($item['latestPosts'] as $p) {
                $cap = (string)($p['caption'] ?? '');
                $captions_concat .= ' ' . $cap;
                $raw_m_url = $p['displayUrl'] ?? ($p['thumbnailUrl'] ?? ($p['images'][0] ?? ($p['childPosts'][0]['displayUrl'] ?? '')));
                $recent_media[] = [
                    'caption' => $cap,
                    'media_url' => $this->_wrap_ig_image_proxy($raw_m_url),
                    'media_type' => (!empty($p['isVideo']) || (isset($p['type']) && strtolower((string)$p['type']) === 'video')) ? 'VIDEO' : 'IMAGE',
                    'likes' => (int)($p['likesCount'] ?? 0),
                    'comments' => (int)($p['commentsCount'] ?? 0),
                    'timestamp' => isset($p['timestamp']) ? date('c', strtotime((string)$p['timestamp'])) : date('c'),
                    'permalink' => isset($p['url']) ? (string)$p['url'] : ''
                ];
            }
        } elseif (!empty($fallback_post)) {
            $cap = (string)($fallback_post['caption'] ?? '');
            $captions_concat .= ' ' . $cap;
            $raw_m_url = $fallback_post['displayUrl'] ?? ($fallback_post['thumbnailUrl'] ?? ($fallback_post['images'][0] ?? ''));
            $recent_media[] = [
                'caption' => $cap,
                'media_url' => $this->_wrap_ig_image_proxy($raw_m_url),
                'media_type' => (isset($fallback_post['type']) && strtolower((string)$fallback_post['type']) === 'video') ? 'VIDEO' : 'IMAGE',
                'likes' => (int)($fallback_post['likesCount'] ?? 0),
                'comments' => (int)($fallback_post['commentsCount'] ?? 0),
                'timestamp' => isset($fallback_post['timestamp']) ? date('c', strtotime((string)$fallback_post['timestamp'])) : date('c'),
                'permalink' => isset($fallback_post['url']) ? (string)$fallback_post['url'] : ''
            ];
        }

        $this->_cache_recent_media_batch($recent_media);

        if ($extracted_email === '') {
            $extracted_email = $this->_extract_email_from_text($captions_concat);
        }

        $eng_metrics = $this->_calculate_accurate_engagement_rate($followers, $recent_media);
        $eng_rate = $eng_metrics['eng_rate'];
        $avg_video_views = (int)round($eng_metrics['avg_eng'] * 3.5);

        // Extract location strictly from the creator's profile bio/name/website (not random photo post captions)
        $location_str = $this->_extract_location_from_text($bio . ' ' . $full_name . ' ' . $ext_url);
        $category_name = (!empty($item['businessCategoryName']) && $item['businessCategoryName'] !== 'None') ? (string)$item['businessCategoryName'] : '';
        $final_niche = $niche_label !== '' ? ltrim($niche_label, '#@') : $category_name;

        return [
            'platform' => 'instagram',
            'platform_id' => (string)($item['id'] ?? ($item['ownerId'] ?? $username)),
            'username' => $username,
            'full_name' => $full_name,
            'profile_image' => $this->_extract_profile_image($item, $full_name),
            'followers' => $followers,
            'following' => $following,
            'posts_count' => $posts_count,
            'eng_rate' => $eng_rate,
            'avg_video_views' => $avg_video_views,
            'location' => $location_str,
            'niche' => $final_niche,
            'bio' => $bio !== '' ? $bio : trim(substr($captions_concat, 0, 240)),
            'website' => $ext_url,
            'is_verified' => !empty($item['verified']) || !empty($item['isVerified']),
            'is_private' => !empty($item['private']) || !empty($item['isPrivate']),
            'recent_media' => $recent_media,
            'notes' => '',
            'profile_url' => "https://instagram.com/$username",
            'email' => $extracted_email,
            'has_email' => !empty($extracted_email)
        ];
    }

    private function _instagram_batch_lookup_apify(array $usernames, $niche_label = '', array $fallback_posts_by_user = []) {
        if (empty($this->apify_api_token) || empty($usernames)) return [];

        $clean_list = [];
        foreach ($usernames as $u) {
            $u = ltrim(trim((string)$u), '@');
            if ($u !== '' && !in_array($u, ['p', 'reel', 'stories', 'explore', 'tags'], true) && !in_array($u, $clean_list, true)) {
                $clean_list[] = $u;
            }
            if (count($clean_list) >= 8) break;
        }
        if (empty($clean_list)) return [];

        $url = "https://api.apify.com/v2/acts/apify~instagram-profile-scraper/run-sync-get-dataset-items?token=" . $this->apify_api_token . "&timeout=35";
        $post_data = ['usernames' => $clean_list];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode($post_data),
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 40
        ]);

        $resp = curl_exec($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (($code !== 200 && $code !== 201) || empty($resp)) {
            return [];
        }

        $items = json_decode($resp, true);
        if (!is_array($items)) return [];

        $results = [];
        foreach ($items as $item) {
            if (!is_array($item) || isset($item['error'])) continue;
            $uname_key = strtolower((string)($item['username'] ?? ''));
            $fb_post = ($uname_key !== '' && isset($fallback_posts_by_user[$uname_key])) ? $fallback_posts_by_user[$uname_key] : [];
            $formatted = $this->_format_apify_ig_profile($item, $niche_label, $fb_post);
            if ($formatted) {
                $results[] = $formatted;
            }
        }

        // Sort by followers descending
        usort($results, function($a, $b) {
            return ((int)($b['followers'] ?? 0)) <=> ((int)($a['followers'] ?? 0));
        });

        return $results;
    }

    private function _search_instagram_apify($keyword, $location = '') {
        if (empty($this->apify_api_token)) return [];

        if (!isset($this->cache)) {
            $this->load->driver('cache', array('adapter' => 'file'));
        }

        $keyword = trim((string)$keyword);
        $starts_with_at = (strpos($keyword, '@') === 0);
        if ($starts_with_at) {
            $user_result = $this->_instagram_username_lookup_apify($keyword);
            return !empty($user_result) ? [$user_result] : [];
        }

        $clean_tag = strtolower((string)preg_replace('/[^a-zA-Z0-9_]/', '', ltrim($keyword, '#@')));
        if ($clean_tag === '') $clean_tag = 'lifestyle';

        $clean_loc = strtolower(trim((string)$location));
        $cache_key = 'ig_live_v4_' . md5($clean_tag . '|' . $clean_loc);
        if ($cached = $this->cache->get($cache_key)) {
            return $cached;
        }

        $hashtags = [$clean_tag];
        if ($clean_loc !== '') {
            if (strpos($clean_loc, 'united kingdom') !== false || $clean_loc === 'uk' || strpos($clean_loc, 'london') !== false) {
                $hashtags[] = $clean_tag . 'uk';
            } elseif (strpos($clean_loc, 'australia') !== false || strpos($clean_loc, 'sydney') !== false) {
                $hashtags[] = $clean_tag . 'australia';
            } elseif (strpos($clean_loc, 'canada') !== false || strpos($clean_loc, 'toronto') !== false) {
                $hashtags[] = $clean_tag . 'canada';
            } elseif (strpos($clean_loc, 'philippines') !== false || strpos($clean_loc, 'manila') !== false) {
                $hashtags[] = $clean_tag . 'ph';
            } elseif (strpos($clean_loc, 'united states') !== false || $clean_loc === 'usa' || $clean_loc === 'us') {
                $hashtags[] = $clean_tag . 'usa';
            }
        }

        // STEP 1: Discover real Instagram creators posting in this niche/hashtag via apify~instagram-hashtag-scraper
        $url1 = "https://api.apify.com/v2/acts/apify~instagram-hashtag-scraper/run-sync-get-dataset-items?token=" . $this->apify_api_token . "&timeout=25";
        $post_data1 = [
            'hashtags' => array_values(array_unique($hashtags)),
            'resultsLimit' => 24,
            'resultsType' => 'posts'
        ];

        $ch1 = curl_init($url1);
        curl_setopt_array($ch1, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode($post_data1),
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 30
        ]);
        $resp1 = curl_exec($ch1);
        $code1 = (int)curl_getinfo($ch1, CURLINFO_HTTP_CODE);
        curl_close($ch1);

        $posts = (($code1 === 200 || $code1 === 201) && !empty($resp1)) ? json_decode($resp1, true) : [];
        if (!is_array($posts)) $posts = [];

        // Sort posts by engagement (likes + comments) descending so we pick the top real creators
        usort($posts, function($a, $b) {
            $eng_a = max(0, (int)($a['likesCount'] ?? 0)) + max(0, (int)($a['commentsCount'] ?? 0));
            $eng_b = max(0, (int)($b['likesCount'] ?? 0)) + max(0, (int)($b['commentsCount'] ?? 0));
            return $eng_b <=> $eng_a;
        });

        $usernames = [];
        $fallback_posts_by_user = [];
        foreach ($posts as $p) {
            $u = trim((string)($p['ownerUsername'] ?? ''));
            if ($u === '' || in_array($u, ['p', 'reel', 'stories', 'explore', 'tags'], true)) continue;
            $u_lower = strtolower($u);
            if (!isset($fallback_posts_by_user[$u_lower])) {
                $fallback_posts_by_user[$u_lower] = $p;
                $usernames[] = $u;
            }
            if (count($usernames) >= 8) break;
        }

        // If keyword itself looks like a handle and we have room, include it
        if (strpos($keyword, '#') !== 0 && strpos($keyword, ' ') === false && count($usernames) < 8 && !in_array($clean_tag, array_map('strtolower', $usernames), true)) {
            $usernames[] = $clean_tag;
        }

        // STEP 2: Fetch full real Instagram profiles (followers, bio, email, recent posts) in batch
        $results = $this->_instagram_batch_lookup_apify($usernames, $keyword, $fallback_posts_by_user);

        if (!empty($results)) {
            $this->cache->save($cache_key, $results, 3600);
        }

        return $results;
    }

    private function _instagram_username_lookup_apify($username) {
        if (empty($this->apify_api_token)) return null;

        $clean_u = ltrim(trim((string)$username), '@');
        if ($clean_u === '') return null;

        $batch = $this->_instagram_batch_lookup_apify([$clean_u], $clean_u);
        if (!empty($batch[0])) {
            $this->_sync_local_account($batch[0]);
            return $batch[0];
        }

        return null;
    }

    private function _search_tiktok_apify($keyword, $location = '') {
        if (empty($this->apify_api_token)) return [];

        if (!isset($this->cache)) {
            $this->load->driver('cache', array('adapter' => 'file'));
        }

        $raw_kw = trim((string)$keyword);
        $clean_kw = ltrim($raw_kw, '#@');
        if ($clean_kw === '') $clean_kw = 'lifestyle';
        $clean_loc = trim((string)$location);

        $cache_key = 'tiktok_live_v4_' . md5(strtolower($clean_kw . '|' . $clean_loc));
        if ($cached = $this->cache->get($cache_key)) {
            return $cached;
        }

        $search_queries = [$clean_kw];
        if ($clean_loc !== '' && stripos($clean_kw, $clean_loc) === false) {
            array_unshift($search_queries, $clean_kw . ' ' . $clean_loc);
        }

        $url = "https://api.apify.com/v2/acts/clockworks~tiktok-scraper/run-sync-get-dataset-items?token=" . $this->apify_api_token . "&timeout=30";
        
        $post_data = [
            'searchQueries' => $search_queries,
            'search' => $search_queries,
            'resultsPerPage' => 16,
            'shouldDownloadVideos' => false,
            'shouldDownloadCovers' => false,
            'shouldDownloadSlideshowImages' => false,
            'shouldDownloadAvatars' => false
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode($post_data),
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 35
        ]);

        $resp = curl_exec($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (($code !== 200 && $code !== 201) || empty($resp)) {
            log_message('error', "Apify TikTok search failed for '$keyword'. HTTP Code: $code.");
            return [];
        }

        $items = json_decode($resp, true);
        if (!is_array($items)) return [];

        $results = [];
        $seen = [];
        foreach ($items as $item) {
            if (!is_array($item)) continue;
            $author = $item['author'] ?? [];
            $am = $item['authorMeta'] ?? [];
            $username = trim((string)($author['uniqueId'] ?? ($am['name'] ?? '')));
            if ($username === '' || isset($seen[strtolower($username)])) continue;
            $seen[strtolower($username)] = true;

            $full_name = trim((string)($author['nickname'] ?? ($am['nickName'] ?? $username)));
            $bio = (string)($author['signature'] ?? ($am['signature'] ?? ''));
            $video_text = (string)($item['text'] ?? '');
            $bio_link = (string)($author['bioLink']['link'] ?? ($am['bioLink'] ?? ''));
            $extracted_email = $this->_extract_email_from_text($bio . ' ' . $full_name . ' ' . $video_text . ' ' . $bio_link);

            $followers = (int)($author['stats']['followerCount'] ?? ($am['fans'] ?? 0));
            $following = (int)($author['stats']['followingCount'] ?? ($am['following'] ?? 0));
            $posts_count = (int)($author['stats']['videoCount'] ?? ($am['video'] ?? 0));
            $hearts = (int)($author['stats']['heartCount'] ?? ($author['stats']['heart'] ?? ($am['heart'] ?? 0)));

            $digg_count = (int)($item['diggCount'] ?? 0);
            $comm_count = (int)($item['commentCount'] ?? 0);
            $share_count = (int)($item['shareCount'] ?? 0);
            $play_count = (int)($item['playCount'] ?? 0);

            // Calculate accurate TikTok engagement rate (accounting for For You Page video reach vs follower count)
            $eng_rate = '0%';
            $vid_interactions = $digg_count + $comm_count + $share_count;
            if ($play_count > 0 && $vid_interactions > 0) {
                $reach_base = max($followers, $play_count);
                $rate_val = ($vid_interactions / $reach_base) * 100.0;
                $eng_rate = number_format(min(35.0, max(0.1, $rate_val)), 2) . '%';
            } elseif ($followers > 0 && $posts_count > 0 && $hearts > 0) {
                $avg_likes_per_vid = $hearts / $posts_count;
                if ($avg_likes_per_vid <= ($followers * 0.35)) {
                    $rate_val = ($avg_likes_per_vid / $followers) * 100.0;
                } else {
                    $est_reach = max($followers * 4.0, $avg_likes_per_vid * 9.0);
                    $rate_val = ($avg_likes_per_vid / $est_reach) * 100.0;
                }
                $eng_rate = number_format(min(35.0, max(0.1, $rate_val)), 2) . '%';
            }

            $region_raw = trim((string)($am['region'] ?? ($item['region'] ?? '')));
            $loc_detected = $this->_extract_location_from_text($bio . ' ' . $full_name . ' ' . $region_raw);
            if ($loc_detected === '' && $clean_loc !== '' && stripos((string)($item['searchQuery'] ?? ''), $clean_loc) !== false) {
                $loc_detected = $clean_loc;
            }

            $raw_avatar = $author['avatarMedium'] ?? ($author['avatarThumb'] ?? ($am['avatar'] ?? ($am['originalAvatarUrl'] ?? '')));
            $avatar = $raw_avatar !== '' ? $this->_cache_avatar_locally($raw_avatar, 'tt_' . $username) : '';
            if ($avatar === '') {
                $avatar = "https://ui-avatars.com/api/?name=" . urlencode($full_name) . "&background=0F6B3E&color=fff";
            }

            $raw_cover = $item['videoMeta']['coverUrl'] ?? ($item['video']['cover'] ?? '');
            $cover_url = $raw_cover !== '' ? $this->_wrap_ig_image_proxy($raw_cover) : $avatar;
            $tt_recent_media = [
                [
                    'caption' => $video_text !== '' ? $video_text : ($bio !== '' ? $bio : 'TikTok Creator Profile (@' . $username . ')'),
                    'media_url' => $cover_url,
                    'media_type' => 'VIDEO',
                    'likes' => $digg_count,
                    'comments' => $comm_count,
                    'timestamp' => isset($item['createTime']) ? date('c', (int)$item['createTime']) : date('c'),
                    'permalink' => $item['webVideoUrl'] ?? ("https://www.tiktok.com/@" . $username)
                ]
            ];
            $this->_cache_recent_media_batch($tt_recent_media);

            $results[] = [
                'platform' => 'tiktok',
                'platform_id' => (string)($author['id'] ?? ($am['id'] ?? $username)),
                'username' => $username,
                'full_name' => $full_name,
                'profile_image' => $avatar,
                'followers' => $followers,
                'following' => $following,
                'posts_count' => $posts_count,
                'eng_rate' => $eng_rate,
                'avg_video_views' => $play_count,
                'location' => $loc_detected,
                'niche' => $clean_kw,
                'bio' => $bio !== '' ? $bio : $video_text,
                'website' => $bio_link,
                'is_verified' => !empty($author['verified']) || !empty($am['verified']),
                'is_private' => !empty($author['privateAccount']) || !empty($am['privateAccount']),
                'recent_media' => $tt_recent_media,
                'notes' => '',
                'profile_url' => "https://www.tiktok.com/@" . $username,
                'email' => $extracted_email,
                'has_email' => !empty($extracted_email)
            ];
        }

        if (!empty($results)) {
            $this->cache->save($cache_key, $results, 3600);
        }

        return $results;
    }

    private function _mask_secret($value) {
        $v = (string)$value;
        if ($v === '') return '';
        $len = strlen($v);
        if ($len <= 8) return str_repeat('*', $len);
        return substr($v, 0, 4) . str_repeat('*', max(0, $len - 8)) . substr($v, -4);
    }
}
