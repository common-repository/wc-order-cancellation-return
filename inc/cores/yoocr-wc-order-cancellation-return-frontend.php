<?php

if (!defined('ABSPATH')) {
    exit;
}

class WC_Order_Cancellation_Return_Frontend {
    public function __construct() {
        $this->includes();
    }

    private function includes() {
        include_once plugin_dir_path(__FILE__) . '../frontend/yoocr-wc-order-cancellation-return-cancel-order-form.php';
        include_once plugin_dir_path(__FILE__) . '../frontend/yoocr-wc-order-cancellation-return-order-details-cancel-button.php';
        include_once plugin_dir_path(__FILE__) . '../frontend/yoocr-wc-order-cancellation-return-return-order-form.php';
        include_once plugin_dir_path(__FILE__) . '../frontend/yoocr-wc-order-cancellation-return-order-details-return-button.php';
        include_once plugin_dir_path(__FILE__) . '../frontend/yoocr-wc-order-cancellation-return-order-details-actions-record.php';
    }
}

new WC_Order_Cancellation_Return_Frontend();