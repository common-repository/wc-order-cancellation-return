<?php

if (!defined('ABSPATH')) {
	exit;
}

class WC_Order_Cancellation_Return_Handle_Order_Return {

	public function __construct() {
		add_action('wp_ajax_handle_order_return', [$this, 'yoocr_wc_order_cancellation_return_handle_order_return']);
		add_action('wp_ajax_nopriv_handle_order_return', [$this, 'yoocr_wc_order_cancellation_return_handle_order_return']);
	}

	public function yoocr_wc_order_cancellation_return_handle_order_return() {
		if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'return_order_nonce')) {
			wp_send_json_error('Invalid nonce');
			wp_die();
		}

		$order_id = intval($_POST['order_id']);
		$reason = sanitize_text_field($_POST['reason']);

		if ('yes' === get_option('order_return_enable_return', 'no')) {
			if ($order_id && $reason) {
				$order = wc_get_order($order_id);
				if (!$order) {
					wp_send_json_error(__('Order not found', 'wc-order-cancellation-return'));
					wp_die();
				}

				if ('yes' === get_option('order_return_approval', 'yes')) {
					$note = __("Order is requested for return by customer. Reason: ", "wc-order-cancellation-return") . $reason;
					$order->update_meta_data('request_return_reason', $reason);
					$order->update_status('request-return');
					$order->save();
					$order->add_order_note($note . ' || ' . __('Order status changed from Completed to Return requested.', 'wc-order-cancellation-return'));

					do_action('yoocr_wc_order_cancellation_return_handle_email_order_return_request', $order_id);
					wp_send_json_success(__('Order is waiting for review of request return and noted.', 'wc-order-cancellation-return'));
				} else {
					$this->yoocr_wc_order_cancellation_return_handle_order_return_approval($order, $reason);
					wp_send_json_success(__('Order return request processed without approval.', 'wc-order-cancellation-return'));
				}
			} else {
				wp_send_json_error(__('Invalid data', 'wc-order-cancellation-return'));
			}
		} else {
			wp_send_json_error(__('Returns are not enabled.', 'wc-order-cancellation-return'));
		}
		wp_die();
	}

	private function yoocr_wc_order_cancellation_return_handle_order_return_approval($order, $reason) {
		$order->update_meta_data('request_return_reason', $reason);
		$order->update_status('refunded', __('Return approved and order refunded.', 'wc-order-cancellation-return'));
		$order->save();
		$order->add_order_note(__('Return approved and order refunded.', 'wc-order-cancellation-return'));
	}
}

new WC_Order_Cancellation_Return_Handle_Order_Return();
