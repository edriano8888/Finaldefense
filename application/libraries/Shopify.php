<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shopify {

    private $access_token;
    private $store_url;
    private $api_version = '2024-01';

    public function __construct() {
        // Load from environment variables (using a helper or directly if available)
        // For CodeIgniter 3, we often need to parse the .env manually if not using a library
        // But here we'll assume the values are set or passed.
        
        $this->access_token = getenv('SHOPIFY_ACCESS_TOKEN') ?: '';
        $this->store_url = getenv('SHOPIFY_STORE_URL') ?: 'instroom-dev-store.myshopify.com'; 
    }

    /**
     * Get Shop Details
     * @return array|false
     */
    public function get_shop() {
        return $this->call('GET', 'shop.json');
    }

    /**
     * Get Order Details
     * @param string $order_id
     * @return array|false
     */
    public function get_order($order_id) {
        if (!$order_id) return false;
        return $this->call('GET', "orders/{$order_id}.json");
    }

    /**
     * Get Orders by Fulfillment Status
     * @param string $status (shipped, partial, unshipped, any)
     * @return array
     */
    public function get_orders($status = 'any') {
        return $this->call('GET', "orders.json?status=any&fulfillment_status={$status}&limit=10");
    }

    /**
     * Create a Draft Order for an Influencer
     * @param array $items
     * @param array $customer
     * @return array|false
     */
    public function create_draft_order($items, $customer) {
        $data = [
            'draft_order' => [
                'line_items' => $items,
                'customer' => $customer,
                'use_customer_default_address' => true,
                'tags' => 'Influencer, Instroom'
            ]
        ];
        return $this->call('POST', 'draft_orders.json', $data);
    }

    /**
     * Make the API Call
     */
    private function call($method, $endpoint, $data = []) {
        $url = "https://{$this->store_url}/admin/api/{$this->api_version}/{$endpoint}";
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "X-Shopify-Access-Token: {$this->access_token}",
            "Content-Type: application/json"
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // For local dev only

        if ($method === 'POST') {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            log_message('error', 'Shopify API Error: ' . $error);
            // Return detailed error for debugging
            return ['errors' => ['cURL Error' => $error]];
        }

        return json_decode($response, true);
    }
}
