<?php

if (!defined('ABSPATH')) {
	exit;
}

class WC_Order_Cancellation_Return_Order_Details_Return_Button {

	public function __construct() {
		if ($this->is_return_enabled()) {
			add_action('woocommerce_order_details_after_order_table', [$this, 'yoocr_wc_order_cancellation_return_order_details_return_button'], 20);
		}
	}

	private function is_return_enabled() {
		return get_option('order_return_enable_return', 'no') === 'yes';
	}

	public function yoocr_wc_order_cancellation_return_order_details_return_button($order) {
		$enable_return = get_option('order_return_enable_return', 'no');
		$allowed_status = get_option('order_return_order_status', '');

		if ($order->has_status('completed')) {
			echo '<a href="' . esc_url(wp_nonce_url(add_query_arg('order_again', $order->get_id(), wc_get_cart_url()), 'woocommerce-order_again')) . '" class="button" style="margin-right: 10px;">' . esc_html__('Order again', 'wc-order-cancellation-return') . '</a>';
		}

		$return_rejected_reason = $order->get_meta('order_return_rejection_reason', true);

		if ('yes' === $enable_return && 'wc-' . $order->get_status() === $allowed_status && empty($return_rejected_reason)) {
			$display_return_button = true;

			// Retrieve and parse the `order_return_available_time` option
			$return_available_time = get_option('order_return_available_time', '');
			if ($return_available_time) {
				$return_available_time = maybe_unserialize($return_available_time);
			}
			$return_time_value = isset($return_available_time['value']) ? intval($return_available_time['value']) : 0;
			$return_time_unit = isset($return_available_time['unit']) ? $return_available_time['unit'] : 'hours';

			// Convert the specified time duration into seconds
			switch ($return_time_unit) {
				case 'days':
					$time_difference_allowed = $return_time_value * 86400; // 1 day = 86400 seconds
					break;
				case 'weeks':
					$time_difference_allowed = $return_time_value * 604800; // 1 week = 604800 seconds
					break;
				case 'hours':
				default:
					$time_difference_allowed = $return_time_value * 3600; // 1 hour = 3600 seconds
					break;
			}

			// Retrieve the date the order changed to the allowed status
			$order_status_change_date = $order->get_meta('order_status_change_date_' . $order->get_status(), true);
			if ($order_status_change_date) {
				$order_time_local = strtotime($order_status_change_date);
			} else {
				$order_date = $order->get_date_created();
				$order_time_utc = $order_date->getTimestamp();
				$order_time_local = $order_time_utc + (get_option('gmt_offset') * 3600);
			}

			$current_time = current_time('timestamp');
			$time_difference = $current_time - $order_time_local;

			// Only display the return button if within the allowed time or if the time value is 0
			if ($return_time_value !== 0 && $time_difference > $time_difference_allowed) {
				$display_return_button = false;
			}

			if ($display_return_button) {
				echo '<a href="#" class="button return_order_button" data-order-id="' . esc_attr($order->get_id()) . '" onclick="return false;">' . esc_html__('Return order', 'wc-order-cancellation-return') . '</a>';
			}
		}

		if ($order->has_status('request-return')) {
			echo '<button class="button" disabled style="cursor: not-allowed; opacity: 0.5;">' . esc_html__('Waiting for approval', 'wc-order-cancellation-return') . '</button>';
		}
	}
}

new WC_Order_Cancellation_Return_Order_Details_Return_Button();
