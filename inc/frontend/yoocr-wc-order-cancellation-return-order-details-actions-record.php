<?php

if (!defined('ABSPATH')) {
	exit;
}

class WC_Order_Cancellation_Return_Order_Details_Actions_Record {

	public function __construct() {
		add_action('woocommerce_order_details_before_order_table', [$this, 'yoocr_wc_order_cancellation_return_order_details_actions_record']);
	}

	public function yoocr_wc_order_cancellation_return_order_details_actions_record($order) {
		$request_return_reason = $order->get_meta('request_return_reason', true);
		if (!empty($request_return_reason)) {
			echo '<p><strong>' . esc_html__('Return request:', 'wc-order-cancellation-return') . '</strong> ' . esc_html($request_return_reason) . '</p>';
		}

		$request_return_approved = $order->get_meta('request_return_approved', true);
		if (!empty($request_return_approved)) {
			echo '<p><strong>' . esc_html__('Order return:', 'wc-order-cancellation-return') . '</strong> ' . esc_html($request_return_approved) . '</p>';
		}

		$order_return_rejection_reason = $order->get_meta('order_return_rejection_reason', true);
		if (!empty($order_return_rejection_reason)) {
			echo '<p><strong>' . esc_html__('Return request rejected:', 'wc-order-cancellation-return') . '</strong> ' . esc_html($order_return_rejection_reason) . '</p>';
		}

		$request_cancellation_reason = $order->get_meta('request_cancellation_reason', true);
		if (!empty($request_cancellation_reason)) {
			echo '<p><strong>' . esc_html__('Cancellation request:', 'wc-order-cancellation-return') . '</strong> ' . esc_html($request_cancellation_reason) . '</p>';
		}

		$request_cancellation_approved = $order->get_meta('request_cancellation_approved', true);
		if (!empty($request_cancellation_approved)) {
			echo '<p><strong>' . esc_html__('Order cancellation:', 'wc-order-cancellation-return') . '</strong> ' . esc_html($request_cancellation_approved) . '</p>';
		}

		$order_cancellation_rejection_reason = $order->get_meta('order_cancellation_rejection_reason', true);
		if (!empty($order_cancellation_rejection_reason)) {
			echo '<p><strong>' . esc_html__('Cancellation request rejected:', 'wc-order-cancellation-return') . '</strong> ' . esc_html($order_cancellation_rejection_reason) . '</p>';
		}

		$order_cancellation_reason = $order->get_meta('cancellation_reason', true);
		if (!empty($order_cancellation_reason)) {
			echo '<p><strong>' . esc_html__('Cancellation reason:', 'wc-order-cancellation-return') . '</strong> ' . esc_html($order_cancellation_reason) . '</p>';
		}
	}
}

new WC_Order_Cancellation_Return_Order_Details_Actions_Record();
