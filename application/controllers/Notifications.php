<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notifications extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Notification_model');
    }

    private function require_login() {
        if (!$this->session->userdata('logged_in') || !$this->session->userdata('user_id')) {
            $this->output
                ->set_status_header(401)
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => 'Unauthorized']));
            return false;
        }
        return true;
    }

    public function summary() {
        if (!$this->require_login()) return;

        $user_id  = (int)$this->session->userdata('user_id');
        $role     = (string)$this->session->userdata('role');
        $brand_id = $this->session->userdata('brand_id');

        $count = $this->Notification_model->count_unread($user_id, $role, $brand_id);
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['unread' => $count]));
    }

    public function list() {
        if (!$this->require_login()) return;

        $limit = (int)$this->input->get('limit');
        if ($limit <= 0 || $limit > 50) {
            $limit = 15;
        }

        $user_id  = (int)$this->session->userdata('user_id');
        $role     = (string)$this->session->userdata('role');
        $brand_id = $this->session->userdata('brand_id');

        $items = $this->Notification_model->list_for_user($user_id, $role, $brand_id, $limit);
        $out   = [];
        foreach ($items as $n) {
            $out[] = [
                'id'         => (int)$n->id,
                'title'      => (string)$n->title,
                'message'    => $n->message !== null ? (string)$n->message : '',
                'url'        => $n->url    !== null ? (string)$n->url      : '',
                'type'       => (string)$n->type,
                'icon'       => (string)$n->icon,
                'is_read'    => (int)$n->is_read === 1,
                'created_at' => (string)$n->created_at,
            ];
        }

        $unread = $this->Notification_model->count_unread($user_id, $role, $brand_id);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['unread' => $unread, 'items' => $out]));
    }

    public function mark_read() {
        if (!$this->require_login()) return;

        $id  = $this->input->post('id');
        $all = (int)$this->input->post('all') === 1;

        $user_id  = (int)$this->session->userdata('user_id');
        $role     = (string)$this->session->userdata('role');
        $brand_id = $this->session->userdata('brand_id');

        if ($all) {
            $this->Notification_model->mark_read($user_id, $role, $brand_id, null);
        } else {
            $nid = (int)$id;
            if ($nid > 0) {
                $this->Notification_model->mark_read($user_id, $role, $brand_id, $nid);
            }
        }

        $unread = $this->Notification_model->count_unread($user_id, $role, $brand_id);
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['ok' => true, 'unread' => $unread]));
    }

    public function delete() {
        if (!$this->require_login()) return;

        $id = (int)$this->input->post('id');
        if ($id <= 0) {
            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => 'Invalid id']));
            return;
        }

        $user_id  = (int)$this->session->userdata('user_id');
        $role     = (string)$this->session->userdata('role');
        $brand_id = $this->session->userdata('brand_id');

        $this->Notification_model->delete_notification($user_id, $role, $brand_id, $id);

        $unread = $this->Notification_model->count_unread($user_id, $role, $brand_id);
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['ok' => true, 'unread' => $unread]));
    }
}
