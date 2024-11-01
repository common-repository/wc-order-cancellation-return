<?php

if (!defined('ABSPATH')) {
	exit;
}

class WC_Order_Cancellation_Return_Admin_Email_Order_Return_Request {

	public function __construct() {
		add_action('woocommerce_email_before_order_table', [$this, 'yoocr_wc_order_cancellation_return_admin_email_order_return_request'], 10, 4);
	}

	public function yoocr_wc_order_cancellation_return_admin_email_order_return_request($order, $sent_to_admin, $plain_text, $email) {
		if ($email->id === 'order_return_request') {
			$return_request_reason = $order->get_meta('request_return_reason', true);

			if (!empty($return_request_reason)) {
				if ($plain_text) {
					echo esc_html(__('Return reason:', 'wc-order-cancellation-return')) . ' ' . esc_html($return_request_reason) . "\n";
				} else {
					echo '<p>' . esc_html__('Return reason:', 'wc-order-cancellation-return') . ' <strong>' . esc_html($return_request_reason) . '</strong></p>';
				}
			}
		}
	}
}

new WC_Order_Cancellation_Return_Admin_Email_Order_Return_Request();
