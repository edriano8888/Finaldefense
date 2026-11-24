<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Activity_log_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('user_agent');
        // Check schema only once (if we haven't checked it before in this session)
        if (!$this->session->userdata('activity_log_schema_checked')) {
            $this->check_schema();
            $this->session->set_userdata('activity_log_schema_checked', TRUE);
        }
    }

    public function check_schema() {
        if (!$this->db->field_exists('ip_address', 'activity_logs')) {
            $this->load->dbforge();
            $fields = array(
                'ip_address' => array(
                    'type'       => 'VARCHAR',
                    'constraint' => 45,
                    'null'       => TRUE,
                    'after'      => 'details'
                )
            );
            $this->dbforge->add_column('activity_logs', $fields);
        }
    }

    /**
     * Map an action string to a notification type, icon, and title.
     *
     * @param  string $action
     * @return array  ['type' => ..., 'icon' => ..., 'title' => ..., 'audience' => ...]
     */
    private function _action_to_notif_meta($action) {
        $action_lower = strtolower(trim($action));

        // Login events — only the performing user should see these
        if ($action_lower === 'login' || $action_lower === 'user login') {
            return [
                'type'     => 'login',
                'icon'     => 'fa-sign-in-alt',
                'title'    => 'New Login',
                'audience' => 'user',   // scoped by user_id in create()
            ];
        }

        // Influencer additions
        if (in_array($action_lower, [
            'add influencer', 'add instagram influencer',
            'add tiktok influencer', 'add youtube influencer',
        ], true)) {
            return [
                'type'     => 'influencer',
                'icon'     => 'fa-user-plus',
                'title'    => 'Influencer Added',
                'audience' => 'all',
            ];
        }

        // Influencer deletions
        if (in_array($action_lower, ['delete influencer', 'bulk delete'], true)) {
            return [
                'type'     => 'warning',
                'icon'     => 'fa-trash-alt',
                'title'    => 'Influencer Removed',
                'audience' => 'all',
            ];
        }

        // Qualification
        if ($action_lower === 'qualify influencer') {
            return [
                'type'     => 'success',
                'icon'     => 'fa-check-circle',
                'title'    => 'Influencer Qualified',
                'audience' => 'all',
            ];
        }

        // Pipeline updates
        if (in_array($action_lower, [
            'update pipeline', 'bulk pipeline update', 'pipeline update',
        ], true)) {
            return [
                'type'     => 'influencer',
                'icon'     => 'fa-columns',
                'title'    => 'Pipeline Updated',
                'audience' => 'all',
            ];
        }

        // CSV / bulk imports
        if (strpos($action_lower, 'csv') !== false || strpos($action_lower, 'import') !== false) {
            return [
                'type'     => 'info',
                'icon'     => 'fa-file-import',
                'title'    => 'Bulk Import',
                'audience' => 'all',
            ];
        }

        // User management (admin audience)
        if (in_array($action_lower, [
            'add user', 'update user', 'delete user',
            'create user', 'remove user',
        ], true)) {
            return [
                'type'     => 'info',
                'icon'     => 'fa-users-cog',
                'title'    => 'User Management',
                'audience' => 'admin',
            ];
        }

        // Campaign / collaboration events
        if (strpos($action_lower, 'campaign') !== false || strpos($action_lower, 'collaborat') !== false) {
            return [
                'type'     => 'influencer',
                'icon'     => 'fa-handshake',
                'title'    => 'Campaign Update',
                'audience' => 'all',
            ];
        }

        // Task updates
        if (strpos($action_lower, 'task') !== false) {
            return [
                'type'     => 'info',
                'icon'     => 'fa-tasks',
                'title'    => 'Task Update',
                'audience' => 'all',
            ];
        }

        // Default fallback
        return [
            'type'     => 'info',
            'icon'     => 'fa-bell',
            'title'    => ucfirst($action),
            'audience' => 'all',
        ];
    }

    /**
     * Log an activity and automatically seed a notification for it.
     *
     * @param string   $action    Short description (e.g. 'Login', 'Add Influencer')
     * @param string   $details   Additional details (optional)
     * @param int|null $user_id   Performing user's id (defaults to session)
     * @param string   $user_name Performing user's name (defaults to session)
     */
    public function log_activity($action, $details = null, $user_id = null, $user_name = null) {
        $uid  = $user_id   ? (int)$user_id   : (int)$this->session->userdata('user_id');
        $name = $user_name ? $user_name       : ($this->session->userdata('name') ?: 'System');

        $data = [
            'user_id'    => $uid ?: null,
            'user_name'  => $name,
            'action'     => $action,
            'details'    => $details,
            'ip_address' => $this->input->ip_address(),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $result = $this->db->insert('activity_logs', $data);

        // ── Auto-seed notification ──────────────────────────────────────────
        try {
            $meta     = $this->_action_to_notif_meta($action);
            $brand_id = $this->session->userdata('brand_id');

            $notif = [
                'brand_id' => $brand_id ? (int)$brand_id : null,
                'title'    => $meta['title'],
                'message'  => $details ? (string)$details : ucfirst($action) . ($name !== 'System' ? ' by ' . $name : ''),
                'type'     => $meta['type'],
                'icon'     => $meta['icon'],
                'audience' => $meta['audience'],
            ];

            // For login and user-specific actions, scope to the acting user only
            if ($meta['audience'] === 'user') {
                $notif['audience'] = 'all';
                $notif['user_id']  = $uid ?: null;
            } else {
                $notif['user_id'] = null; // broadcast to all within the brand
            }

            $this->load->model('Notification_model');
            $this->Notification_model->create($notif);
        } catch (Exception $e) {
            // Never let notification seeding crash the main flow
            log_message('error', 'Notification seeding failed for action "' . $action . '": ' . $e->getMessage());
        }
        // ───────────────────────────────────────────────────────────────────

        return $result;
    }

    /**
     * Get all logs with pagination.
     */
    public function get_logs($limit = 50, $offset = 0) {
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get('activity_logs', $limit, $offset)->result();
    }

    /**
     * Get recent logs (for dashboard widgets).
     */
    public function get_recent_logs($limit = 5) {
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get('activity_logs', $limit)->result_array();
    }
}
