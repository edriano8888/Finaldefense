<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Prototype extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
    }

    public function index() {
        $this->admin();
    }

    public function admin() {
        $data['section'] = $this->input->get('section') ?? 'dashboard';
        $this->load->view('prototype/admin', $data);
    }

    public function employee() {
        $data['section'] = $this->input->get('section') ?? 'dashboard';
        $this->load->view('prototype/employee', $data);
    }

    public function manager() {
        $data['section'] = $this->input->get('section') ?? 'dashboard';
        $this->load->view('prototype/manager', $data);
    }
}
