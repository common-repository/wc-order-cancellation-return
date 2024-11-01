<?php

if (!defined('ABSPATH')) {
	exit;
}

class WC_Order_Cancellation_Return_Admin_Email_Cancelled_Order {

	public function __construct() {
		add_action('woocommerce_email_before_order_table', [$this, 'yoocr_wc_order_cancellation_return_admin_email_cancelled_order'], 10, 4);
	}

	public function yoocr_wc_order_cancellation_return_admin_email_cancelled_order($order, $sent_to_admin, $plain_text, $email) {
		if ($email->id === 'cancelled_order') {
			$reason = $order->get_meta('cancellation_reason', true);

			if (empty($reason)) {
				$reason = esc_html__('Cancelled by shop manager.', 'wc-order-cancellation-return');
			}

			if ($plain_text) {
				echo esc_html(__('Reason for cancellation:', 'wc-order-cancellation-return')) . ' ' . esc_html($reason) . "\n";
			} else {
				echo '<p>' . esc_html__('Reason for cancellation:', 'wc-order-cancellation-return') . ' <strong>' . esc_html($reason) . '</strong></p>';
			}
		}
	}
}

new WC_Order_Cancellation_Return_Admin_Email_Cancelled_Order();
