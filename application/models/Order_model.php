<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_pending_orders() {
        $brand_id = $this->session->userdata('brand_id');
        if ($brand_id) {
            $this->db->where('brand_id', $brand_id);
        }
        $this->db->where('status', 'pending');
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get('orders')->result();
    }

    public function get_all_orders() {
        $brand_id = $this->session->userdata('brand_id');
        if ($brand_id) {
            $this->db->where('brand_id', $brand_id);
        }
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get('orders')->result();
    }

    public function get_orders_by_status($status) {
        $brand_id = $this->session->userdata('brand_id');
        if ($brand_id) {
            $this->db->where('brand_id', $brand_id);
        }
        if ($status && $status !== 'all') {
            $this->db->where('status', $status);
        }
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get('orders')->result();
    }

    public function update_status($id, $status) {
        $this->db->where('id', $id);
        return $this->db->update('orders', ['status' => $status]);
    }

    public function create_order($data) {
        $brand_id = $this->session->userdata('brand_id');
        $data['brand_id'] = $brand_id;
        $data['status'] = 'pending';
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert('orders', $data);
        return $this->db->insert_id();
    }

    public function get_order_by_id($id) {
        return $this->db->get_where('orders', ['id' => $id])->row();
    }

    public function count_by_status($status = null) {
        $brand_id = $this->session->userdata('brand_id');
        if ($brand_id) {
            $this->db->where('brand_id', $brand_id);
        }
        if ($status) {
            $this->db->where('status', $status);
        }
        return $this->db->count_all_results('orders');
    }
}
