<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Track extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('Tracking_model');
    }

    public function index($code) {
        // Get the tracking link
        $tracking_link = $this->Tracking_model->get_tracking_link_by_code($code);
        
        if (!$tracking_link) {
            // If not found, redirect to homepage
            redirect(base_url());
            return;
        }

        // Log the view
        $this->Tracking_model->log_view($tracking_link->id);

        // Redirect to the destination URL
        redirect($tracking_link->destination_url);
    }
}
