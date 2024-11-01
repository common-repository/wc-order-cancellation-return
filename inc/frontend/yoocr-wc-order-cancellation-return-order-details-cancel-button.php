<?php

if (!defined('ABSPATH')) {
	exit;
}

class WC_Order_Cancellation_Return_Order_Details_Cancel_Button {

	public function __construct() {
		if ($this->is_cancellation_enabled()) {
			add_action('woocommerce_order_details_after_order_table', [$this, 'yoocr_wc_order_cancellation_return_order_details_cancel_button'], 10, 1);
		}
	}

	private function is_cancellation_enabled() {
		return get_option('order_cancel_enable_cancellation', 'no') === 'yes';
	}

	public function yoocr_wc_order_cancellation_return_order_details_cancel_button($order) {
		$is_cancellation_enabled = get_option('order_cancel_enable_cancellation', 'no');
		$allowed_statuses = get_option('order_cancel_order_status', array());

		$allowed_statuses = array_map(function ($status) {
			return str_replace('wc-', '', $status);
		}, $allowed_statuses);

		$display_cancel_button = true;

		$cancel_available_time = get_option('order_cancel_available_time', '');
		if ($cancel_available_time) {
			$cancel_available_time = maybe_unserialize($cancel_available_time);
		}
		$cancel_time_value = isset($cancel_available_time['value']) ? intval($cancel_available_time['value']) : 0;
		$cancel_time_unit = isset($cancel_available_time['unit']) ? $cancel_available_time['unit'] : 'hours';

		switch ($cancel_time_unit) {
			case 'days':
				$time_difference_allowed = $cancel_time_value * 86400;
				break;
			case 'weeks':
				$time_difference_allowed = $cancel_time_value * 604800;
				break;
			case 'hours':
			default:
				$time_difference_allowed = $cancel_time_value * 3600;
				break;
		}

		$order_date = $order->get_date_created();
		$order_time_utc = $order_date->getTimestamp();
		
		// Convert order time to the site's local time
		$order_time_local = $order_time_utc + (get_option('gmt_offset') * 3600);

		$current_time = current_time('timestamp');
		$time_difference = $current_time - $order_time_local;

		if ($cancel_time_value !== 0 && $time_difference > $time_difference_allowed) {
			$display_cancel_button = false;
		}

		$order_cancellation_rejection_reason = $order->get_meta('order_cancellation_rejection_reason', true);
		if (!empty($order_cancellation_rejection_reason)) {
			$display_cancel_button = false;
		}

		if ('yes' === $is_cancellation_enabled && in_array($order->get_status(), $allowed_statuses) && $display_cancel_button) {
			$cancel_url = wp_nonce_url(add_query_arg(array(
				'cancel_order' => 'true',
				'order' => $order->get_id(),
				'order_key' => $order->get_order_key(),
			), $order->get_cancel_order_url()), 'woocommerce-cancel_order');

			echo '<a href="' . esc_url($cancel_url) . '" data-order-id="' . esc_attr($order->get_id()) . '" class="button cancel_order_button">' . esc_html__('Cancel order', 'wc-order-cancellation-return') . '</a>';
		}

		if ($order->has_status('request-cancel')) {
			echo '<button class="button" disabled style="cursor: not-allowed; opacity: 0.5;">' . esc_html__('Waiting for approval', 'wc-order-cancellation-return') . '</button>';
		}
	}        
}

new WC_Order_Cancellation_Return_Order_Details_Cancel_Button();
