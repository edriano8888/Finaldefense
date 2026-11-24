<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notification_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->ensure_table();
    }

    private function ensure_table() {
        $this->db->query(
            "CREATE TABLE IF NOT EXISTS `notifications` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `brand_id` INT(11) NULL,
                `user_id` INT(11) NULL,
                `audience` VARCHAR(20) NOT NULL DEFAULT 'all',
                `title` VARCHAR(255) NOT NULL DEFAULT '',
                `message` TEXT NULL,
                `url` VARCHAR(500) NULL,
                `type` VARCHAR(30) NOT NULL DEFAULT 'info',
                `icon` VARCHAR(80) NOT NULL DEFAULT 'fa-bell',
                `is_read` TINYINT(1) NOT NULL DEFAULT 0,
                `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `idx_notifications_brand` (`brand_id`),
                KEY `idx_notifications_user` (`user_id`),
                KEY `idx_notifications_read` (`is_read`),
                KEY `idx_notifications_created` (`created_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
        );

        // Add `type` column if it does not exist yet (for existing installations)
        if (!$this->db->field_exists('type', 'notifications')) {
            $this->db->query("ALTER TABLE `notifications` ADD COLUMN `type` VARCHAR(30) NOT NULL DEFAULT 'info' AFTER `url`");
        }
        // Add `icon` column if it does not exist yet
        if (!$this->db->field_exists('icon', 'notifications')) {
            $this->db->query("ALTER TABLE `notifications` ADD COLUMN `icon` VARCHAR(80) NOT NULL DEFAULT 'fa-bell' AFTER `type`");
        }
    }

    /**
     * Create a notification.
     *
     * @param array $data  Keys: brand_id, user_id, audience, title, message, url, type, icon
     * @return int  Inserted row id
     */
    public function create($data) {
        $insert = [
            'brand_id' => isset($data['brand_id']) ? (int)$data['brand_id'] : null,
            'user_id'  => isset($data['user_id'])  ? (int)$data['user_id']  : null,
            'audience' => isset($data['audience']) ? (string)$data['audience'] : 'all',
            'title'    => isset($data['title'])    ? (string)$data['title']    : '',
            'message'  => isset($data['message'])  ? (string)$data['message']  : null,
            'url'      => isset($data['url'])       ? (string)$data['url']      : null,
            'type'     => isset($data['type'])      ? (string)$data['type']     : 'info',
            'icon'     => isset($data['icon'])      ? (string)$data['icon']     : 'fa-bell',
            'is_read'  => 0,
        ];
        $this->db->insert('notifications', $insert);
        return $this->db->insert_id();
    }

    /**
     * Delete a single notification scoped to the requesting user/brand.
     */
    public function delete_notification($user_id, $role, $brand_id, $id) {
        $user_id  = (int)$user_id;
        $brand_id = $brand_id ? (int)$brand_id : null;
        $role     = strtolower((string)$role);

        if ($brand_id) {
            $this->db->where('brand_id', $brand_id);
        }
        $this->db->group_start();
            $this->db->where('user_id', $user_id);
            $this->db->or_where('user_id IS NULL', null, false);
        $this->db->group_end();
        $this->db->group_start();
            $this->db->where('audience', 'all');
            $this->db->or_where('audience', $role);
        $this->db->group_end();
        $this->db->where('id', (int)$id);

        return $this->db->delete('notifications');
    }

    private function apply_scope($user_id, $role, $brand_id) {
        $user_id  = (int)$user_id;
        $brand_id = $brand_id ? (int)$brand_id : null;
        $role     = strtolower((string)$role);

        $this->db->from('notifications n');
        if ($brand_id) {
            $this->db->where('n.brand_id', $brand_id);
        }
        $this->db->group_start();
            $this->db->where('n.user_id', $user_id);
            $this->db->or_where('n.user_id IS NULL', null, false);
        $this->db->group_end();
        $this->db->group_start();
            $this->db->where('n.audience', 'all');
            $this->db->or_where('n.audience', $role);
        $this->db->group_end();
    }

    public function list_for_user($user_id, $role, $brand_id, $limit = 10) {
        $this->apply_scope($user_id, $role, $brand_id);
        $this->db->order_by('n.created_at', 'DESC');
        $this->db->limit((int)$limit);
        return $this->db->get()->result();
    }

    public function count_unread($user_id, $role, $brand_id) {
        $this->apply_scope($user_id, $role, $brand_id);
        $this->db->where('n.is_read', 0);
        return (int)$this->db->count_all_results();
    }

    public function mark_read($user_id, $role, $brand_id, $id = null) {
        $user_id  = (int)$user_id;
        $brand_id = $brand_id ? (int)$brand_id : null;
        $role     = strtolower((string)$role);

        if ($brand_id) {
            $this->db->where('brand_id', $brand_id);
        }
        $this->db->group_start();
            $this->db->where('user_id', $user_id);
            $this->db->or_where('user_id IS NULL', null, false);
        $this->db->group_end();
        $this->db->group_start();
            $this->db->where('audience', 'all');
            $this->db->or_where('audience', $role);
        $this->db->group_end();

        if ($id !== null) {
            $this->db->where('id', (int)$id);
        }

        return $this->db->update('notifications', ['is_read' => 1]);
    }

    /**
     * Create a system notification for an incoming creator reply.
     *
     * @param int|null $brand_id
     * @param string $from_name
     * @param string $from_address
     * @param string $subject
     * @param string $thread_id
     * @return int Inserted notification ID
     */
    public function create_creator_reply_notification($brand_id, $from_name, $from_address, $subject, $thread_id) {
        $sender_display = !empty($from_name) ? $from_name : $from_address;
        return $this->create([
            'brand_id' => $brand_id ? (int)$brand_id : null,
            'user_id'  => null,
            'audience' => 'all',
            'title'    => 'New Reply from ' . $sender_display,
            'message'  => 'Subject: ' . (!empty($subject) ? $subject : '(No Subject)'),
            'url'      => 'email/thread/' . $thread_id,
            'type'     => 'influencer',
            'icon'     => 'fa-reply'
        ]);
    }
}

