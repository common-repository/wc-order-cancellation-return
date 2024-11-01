<?php

if (!defined('ABSPATH')) {
	exit;
}

class WC_Order_Cancellation_Return_Backend {
	public function __construct() {
		$this->includes();
	}

	private function includes() {
		include_once plugin_dir_path(__FILE__) . '../backend/yoocr-wc-order-cancellation-return-settings.php';
		include_once plugin_dir_path(__FILE__) . '../backend/yoocr-wc-order-cancellation-return-edit-order-cancellation-approval.php';
		include_once plugin_dir_path(__FILE__) . '../backend/yoocr-wc-order-cancellation-return-edit-order-return-approval.php';
		include_once plugin_dir_path(__FILE__) . '../backend/actions/yoocr-wc-order-cancellation-return-handle-order-cancellation.php';
		include_once plugin_dir_path(__FILE__) . '../backend/actions/yoocr-wc-order-cancellation-return-handle-order-return.php';
		include_once plugin_dir_path(__FILE__) . '../backend/actions/yoocr-wc-order-cancellation-return-handle-cancellation-approval.php';
		include_once plugin_dir_path(__FILE__) . '../backend/actions/yoocr-wc-order-cancellation-return-handle-return-approval.php';
		include_once plugin_dir_path(__FILE__) . '../backend/actions/yoocr-wc-order-cancellation-return-order-status.php';
		include_once plugin_dir_path(__FILE__) . '../backend/actions/yoocr-wc-order-cancellation-return-store-order-status-change-date.php';
	}
}

new WC_Order_Cancellation_Return_Backend();
