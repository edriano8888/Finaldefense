<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    public function index() {
        $this->load->view('home');
    }

    /**
     * Server-side TikTok Video Extractor API with multi-provider fallback & short-link resolver
     */
    public function tiktok_api() {
        header('Content-Type: application/json; charset=utf-8');

        $raw_url = $this->input->post('url') ?: $this->input->get('url');
        if (empty($raw_url)) {
            echo json_encode(['status' => false, 'message' => 'Please enter a valid TikTok video URL.']);
            return;
        }

        $url = trim($raw_url);

        // 1. Resolve short links (vt.tiktok.com / vm.tiktok.com / tiktok.com/t/)
        $resolved_url = $this->resolve_tiktok_shortlink($url);

        // 2. Provider 1: TikWM
        $data = $this->fetch_from_tikwm($resolved_url);

        // 3. Provider 2 Fallback: Delirius API
        if (!$data || !$data['status']) {
            $data = $this->fetch_from_delirius($resolved_url);
        }

        // 4. Provider 3 Fallback: TiklyDown
        if (!$data || !$data['status']) {
            $data = $this->fetch_from_tiklydown($resolved_url);
        }

        if ($data && $data['status']) {
            echo json_encode($data);
        } else {
            echo json_encode([
                'status' => false,
                'message' => 'Could not extract video. Please ensure the TikTok video link is valid, public, and active.'
            ]);
        }
    }

    /**
     * Resolve short links (vt.tiktok.com, vm.tiktok.com) to target full video URL
     */
    private function resolve_tiktok_shortlink($url) {
        if (strpos($url, 'vt.tiktok.com') !== false || strpos($url, 'vm.tiktok.com') !== false || strpos($url, '/t/') !== false || strpos($url, 'm.tiktok.com') !== false) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
            curl_exec($ch);
            $effective_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
            curl_close($ch);
            if (!empty($effective_url)) {
                return $effective_url;
            }
        }
        return $url;
    }

    /**
     * Provider 1: TikWM API
     */
    private function fetch_from_tikwm($url) {
        $ch = curl_init('https://www.tikwm.com/api/');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['url' => $url, 'hd' => 1]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 12);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response) {
            $json = json_decode($response, true);
            if (isset($json['code']) && $json['code'] === 0 && !empty($json['data'])) {
                $v = $json['data'];
                // Prioritize 'play' (H.264 / avc1 no-watermark) over 'hdplay' (HEVC / hvc1)
                // because hvc1 causes a black screen with audio-only in Windows browsers & players
                $h264_url = !empty($v['play']) ? $v['play'] : (!empty($v['hdplay']) ? $v['hdplay'] : '');
                return [
                    'status' => true,
                    'provider' => 'TikWM',
                    'title' => !empty($v['title']) ? $v['title'] : 'TikTok Video',
                    'nickname' => !empty($v['author']['nickname']) ? $v['author']['nickname'] : 'Creator',
                    'username' => !empty($v['author']['unique_id']) ? '@' . $v['author']['unique_id'] : '@username',
                    'avatar' => !empty($v['author']['avatar']) ? $v['author']['avatar'] : 'https://ui-avatars.com/api/?name=TikTok&background=000&color=fff',
                    'cover' => !empty($v['cover']) ? $v['cover'] : '',
                    'video_hd' => $h264_url,
                    'video_sd' => $h264_url,
                    'video_hevc' => !empty($v['hdplay']) ? $v['hdplay'] : $h264_url,
                    'music' => !empty($v['music']) ? $v['music'] : '',
                    'views' => !empty($v['play_count']) ? number_format($v['play_count']) : '0',
                    'likes' => !empty($v['digg_count']) ? number_format($v['digg_count']) : '0',
                    'shares' => !empty($v['share_count']) ? number_format($v['share_count']) : '0',
                    'comments' => !empty($v['comment_count']) ? number_format($v['comment_count']) : '0',
                ];
            }
        }
        return false;
    }

    /**
     * Provider 2: Delirius API
     */
    private function fetch_from_delirius($url) {
        $ch = curl_init('https://deliriussapi-oficial.vercel.app/download/tiktok?url=' . urlencode($url));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 12);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response) {
            $json = json_decode($response, true);
            if (isset($json['status']) && $json['status'] && !empty($json['data'])) {
                $d = $json['data'];
                // Prioritize 'play' (H.264 / avc1) over 'hdplay' (HEVC / hvc1) to prevent black screen
                $video_url = !empty($d['play']) ? $d['play'] : (!empty($d['hdplay']) ? $d['hdplay'] : '');
                if ($video_url) {
                    return [
                        'status' => true,
                        'provider' => 'DeliriusAPI',
                        'title' => !empty($d['title']) ? $d['title'] : 'TikTok Video',
                        'nickname' => !empty($d['author']['nickname']) ? $d['author']['nickname'] : 'Creator',
                        'username' => !empty($d['author']['username']) ? '@' . $d['author']['username'] : '@username',
                        'avatar' => !empty($d['author']['avatar']) ? $d['author']['avatar'] : 'https://ui-avatars.com/api/?name=TikTok&background=000&color=fff',
                        'cover' => !empty($d['cover']) ? $d['cover'] : '',
                        'video_hd' => $video_url,
                        'video_sd' => $video_url,
                        'music' => !empty($d['music']) ? $d['music'] : '',
                        'views' => !empty($d['views']) ? number_format($d['views']) : '0',
                        'likes' => !empty($d['likes']) ? number_format($d['likes']) : '0',
                        'shares' => '0',
                        'comments' => !empty($d['comments']) ? number_format($d['comments']) : '0',
                    ];
                }
            }
        }
        return false;
    }

    /**
     * Provider 3 Fallback: TiklyDown API
     */
    private function fetch_from_tiklydown($url) {
        $ch = curl_init('https://api.tiklydown.eu.org/api/download?url=' . urlencode($url));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 12);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response) {
            $json = json_decode($response, true);
            if (!empty($json['video']['noWatermark']) || !empty($json['video']['watermark'])) {
                $video_url = !empty($json['video']['noWatermark']) ? $json['video']['noWatermark'] : $json['video']['watermark'];
                return [
                    'status' => true,
                    'provider' => 'TiklyDown',
                    'title' => !empty($json['title']) ? $json['title'] : 'TikTok Video',
                    'nickname' => !empty($json['author']['name']) ? $json['author']['name'] : 'Creator',
                    'username' => !empty($json['author']['unique_id']) ? '@' . $json['author']['unique_id'] : '@username',
                    'avatar' => !empty($json['author']['avatar']) ? $json['author']['avatar'] : 'https://ui-avatars.com/api/?name=TikTok&background=000&color=fff',
                    'cover' => !empty($json['video']['cover']) ? $json['video']['cover'] : '',
                    'video_hd' => $video_url,
                    'video_sd' => $video_url,
                    'music' => !empty($json['music']['play_url']) ? $json['music']['play_url'] : '',
                    'views' => '0',
                    'likes' => '0',
                    'shares' => '0',
                    'comments' => '0',
                ];
            }
        }
        return false;
    }

    /**
     * Secure Proxy endpoint to stream & force download of TikTok video/audio assets bypassing CORS.
     */
    public function download_proxy() {
        $url = $this->input->get('url');
        $type = $this->input->get('type') ?: 'video';
        if (empty($url)) {
            show_404();
        }

        // Validate HTTP/HTTPS protocol
        if (!preg_match('/^https?:\/\//i', $url)) {
            show_error('Invalid URL scheme.', 400);
        }

        $disposition = $this->input->get('disposition') ?: 'attachment';

        ini_set('default_socket_timeout', 30);

        if (ob_get_level()) {
            ob_end_clean();
        }

        if ($type === 'audio') {
            header('Content-Type: audio/mpeg');
            if ($disposition === 'attachment') {
                header('Content-Disposition: attachment; filename="tiktok_audio_' . time() . '.mp3"');
            } else {
                header('Content-Disposition: inline');
            }
        } else {
            header('Content-Type: video/mp4');
            if ($disposition === 'attachment') {
                header('Content-Disposition: attachment; filename="tiktok_hd_video_' . time() . '.mp4"');
            } else {
                header('Content-Disposition: inline');
            }
        }

        header('Content-Description: File Transfer');
        header('Accept-Ranges: bytes');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        // Stream remote resource securely via cURL (forwarding Range headers for smooth HTML5 video playback)
        $ch = curl_init($url);
        $req_headers = ['Referer: https://www.tiktok.com/'];
        if (!empty($_SERVER['HTTP_RANGE'])) {
            $req_headers[] = 'Range: ' . $_SERVER['HTTP_RANGE'];
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $req_headers);
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, function($curl, $header_line) {
            if (preg_match('/^HTTP\/[0-9.]+\s+206\b/i', $header_line)) {
                http_response_code(206);
            } elseif (preg_match('/^(Content-Length|Content-Range):\s*(.+)$/i', trim($header_line), $m)) {
                header($m[1] . ': ' . $m[2]);
            }
            return strlen($header_line);
        });
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
        curl_exec($ch);
        curl_close($ch);
        exit;
    }

    public function image_proxy() {
        @session_write_close();
        while (ob_get_level() > 0) {
            @ob_end_clean();
        }

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

        $cache_dir = FCPATH . 'assets/cache/ig_media/';
        if (!is_dir($cache_dir)) {
            @mkdir($cache_dir, 0777, true);
        }
        $cache_file = $cache_dir . md5($url) . '.jpg';
        if (file_exists($cache_file) && filesize($cache_file) > 100) {
            $head = (string)@file_get_contents($cache_file, false, null, 0, 12);
            $mime = 'image/jpeg';
            if (substr($head, 0, 4) === 'RIFF' && substr($head, 8, 4) === 'WEBP') {
                $mime = 'image/webp';
            } elseif (substr($head, 0, 4) === "\x89PNG") {
                $mime = 'image/png';
            }
            header('Access-Control-Allow-Origin: *');
            header('Cross-Origin-Resource-Policy: cross-origin');
            header('Cache-Control: public, max-age=86400');
            header('Content-Type: ' . $mime);
            header('Content-Length: ' . filesize($cache_file));
            readfile($cache_file);
            exit;
        }

        $ref = (strpos($url, 'tiktokcdn.com') !== false) ? 'https://www.tiktok.com/' : 'https://www.instagram.com/';
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 12,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
            CURLOPT_HTTPHEADER => [
                'Accept: image/jpeg,image/png,image/webp,image/*,*/*;q=0.8',
                'Referer: ' . $ref
            ]
        ]);
        $img = curl_exec($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $ct = (string)curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);

        if ($code >= 200 && $code < 300 && !empty($img)) {
            @file_put_contents($cache_file, $img);
            header('Access-Control-Allow-Origin: *');
            header('Cross-Origin-Resource-Policy: cross-origin');
            header('Cache-Control: public, max-age=86400');
            header('Content-Type: ' . ($ct !== '' ? $ct : 'image/jpeg'));
            header('Content-Length: ' . strlen($img));
            echo $img;
            exit;
        }

        show_404();
    }
}
