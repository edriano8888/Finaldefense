<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ayrshare {

    private $api_key;
    private $base_url = 'https://api.ayrshare.com/api/';

    public function __construct() {
        $this->api_key = getenv('AYRSHARE_API_KEY') ?: '';
    }

    /**
     * Post to Social Media Platforms (Instagram, Facebook, TikTok, etc.)
     * 
     * @param string $postText
     * @param array $platforms e.g. ["instagramApi", "facebook", "tiktok"]
     * @param array $mediaUrls e.g. ["https://img.ayrshare.com/012/gb.jpg"]
     * @return array|false
     */
    public function post($postText, $platforms = ["instagram", "facebook", "tiktok"], $mediaUrls = []) {
        $normalized_platforms = array_map(function($p) {
            $p_lower = strtolower((string)$p);
            if ($p_lower === 'instagramapi' || $p_lower === 'instagram') return 'instagram';
            return $p_lower;
        }, (array)$platforms);

        $data = [
            "post" => $postText,
            "platforms" => array_values(array_unique($normalized_platforms))
        ];

        if (!empty($mediaUrls)) {
            $data["mediaUrls"] = (array)$mediaUrls;
        }

        return $this->call('POST', 'post', $data);
    }

    /**
     * Get Connected Social Profiles & Account Info
     * @return array|false
     */
    public function getUser() {
        return $this->call('GET', 'user');
    }

    /**
     * Get Post History
     * @return array|false
     */
    public function getHistory() {
        return $this->call('GET', 'history');
    }

    /**
     * Search Connected Accounts for Discovery Controller
     * 
     * @param string $platform (instagram, facebook, tiktok, all)
     * @param string $keyword
     * @return array
     */
    public function searchProfiles($platform, $keyword) {
        $user_data = $this->getUser();
        if (empty($user_data['displayNames'])) return [];

        $results = [];
        $clean_kw = strtolower(str_replace(['@', '#', ' '], '', trim((string)$keyword)));

        foreach ($user_data['displayNames'] as $item) {
            $item_platform = strtolower($item['platform'] ?? '');
            if ($platform !== 'all' && $item_platform !== strtolower($platform) && !($platform === 'instagram' && strpos($item_platform, 'instagram') !== false)) {
                continue;
            }

            $username = strtolower(ltrim($item['username'] ?? '', '@'));
            $display_name = $item['displayName'] ?? $username;

            if ($clean_kw !== '' && strpos($username, $clean_kw) === false && strpos(strtolower($display_name), $clean_kw) === false) {
                continue;
            }

            $results[] = [
                'platform' => $platform === 'all' ? ($item_platform ?: 'instagram') : $platform,
                'platform_id' => $item['id'] ?? '',
                'username' => $username ?: $clean_kw,
                'full_name' => $display_name,
                'profile_image' => $item['userImage'] ?? '',
                'followers' => 0,
                'following' => 0,
                'posts_count' => 0,
                'eng_rate' => 'N/A',
                'bio' => $item['description'] ?? 'Connected Social Account via Ayrshare API',
                'website' => '',
                'is_verified' => true,
                'recent_media' => [],
                'notes' => 'Connected via Ayrshare API',
                'profile_url' => $item['profileUrl'] ?? ('https://' . $platform . '.com/' . $username)
            ];
        }

        return $results;
    }

    /**
     * Make the API Call to Ayrshare
     */
    private function call($method, $endpoint, $data = []) {
        $url = $this->base_url . $endpoint;
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$this->api_key}",
            "Content-Type: application/json"
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        if ($method === 'POST') {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            log_message('error', 'Ayrshare API Error: ' . $error);
            return ['status' => 'error', 'message' => $error];
        }

        return json_decode($response, true);
    }
}
