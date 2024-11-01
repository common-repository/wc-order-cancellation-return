<?php

if (!defined('ABSPATH')) {
	exit;
}

class WC_Order_Cancellation_Return_Emails {
	public function __construct() {
		$this->includes();
	}

	private function includes() {
		include_once plugin_dir_path(__FILE__) . '../emails/yoocr-wc-order-cancellation-return-admin-email-cancelled-order.php';
		include_once plugin_dir_path(__FILE__) . '../emails/yoocr-wc-order-cancellation-return-admin-email-order-cancel-request.php';
		include_once plugin_dir_path(__FILE__) . '../emails/yoocr-wc-order-cancellation-return-admin-email-order-return-request.php';
		include_once plugin_dir_path(__FILE__) . '../emails/yoocr-wc-order-cancellation-return-email-functions.php';
		include_once plugin_dir_path(__FILE__) . '../emails/yoocr-wc-order-cancellation-return-customer-email-refunded-order.php';
		include_once plugin_dir_path(__FILE__) . '../emails/yoocr-wc-order-cancellation-return-customer-email-completed-order.php';
	}
}

new WC_Order_Cancellation_Return_Emails();
