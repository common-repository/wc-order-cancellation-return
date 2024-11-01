<?php

if (!defined('ABSPATH')) {
	exit;
}

class WC_Order_Cancellation_Return_Handle_Order_Cancellation {

	public function __construct() {
		add_action('wp_ajax_handle_order_cancellation', [$this, 'yoocr_wc_order_cancellation_return_handle_order_cancellation']);
		add_action('wp_ajax_nopriv_handle_order_cancellation', [$this, 'yoocr_wc_order_cancellation_return_handle_order_cancellation']);
	}

	public function yoocr_wc_order_cancellation_return_handle_order_cancellation() {
		if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'cancel_order_nonce')) {
			wp_send_json_error('Unauthorized: Invalid nonce');
			wp_die();
		}

		$order_id = intval($_POST['order_id']);
		$reason = sanitize_text_field($_POST['reason']);

		if (!$order_id || !$reason) {
			wp_send_json_error(__('Invalid data', 'wc-order-cancellation-return'));
			wp_die();
		}

		$order = wc_get_order($order_id);
		if (!$order) {
			wp_send_json_error(__('Order not found', 'wc-order-cancellation-return'));
			wp_die();
		}

		if ('yes' === get_option('order_cancel_cancellation_approval', 'yes')) {
			$note = __("Order is requested for cancellation by customer. Reason: ", "wc-order-cancellation-return") . $reason;
			$current_status = $order->get_status();
			$order->update_meta_data('status_before_cancellation_request', $current_status);
			$order->update_meta_data('request_cancellation_reason', $reason);
			$order->update_status('wc-request-cancel');
			$order->save();
			$order->add_order_note($note . ' || ' . __('Order status changed to Cancellation requested.', 'wc-order-cancellation-return'));

			// Trigger email manually if necessary
			WC()->mailer()->emails['YOOCR_WC_Order_Cancellation_Return_Order_Cancel_Request_Email']->trigger($order_id);

			wp_send_json_success(__('Order is waiting for review of request cancellation and noted.', 'wc-order-cancellation-return'));
		} else {
			$this->yoocr_wc_order_cancellation_return_handle_order_cancellation_approval($order, $reason);
			wp_send_json_success(__('Order cancellation request processed without approval.', 'wc-order-cancellation-return'));
		}
		wp_die();
	}

	private function yoocr_wc_order_cancellation_return_handle_order_cancellation_approval($order, $reason) {
		$current_status = $order->get_status();
		$order->update_meta_data('status_before_cancellation_request', $current_status);
		$order->update_meta_data('cancellation_reason', $reason);
		$order->update_status('cancelled');
		$order->save();
		$order->add_order_note(__('Order cancelled by customer. Reason: ', 'wc-order-cancellation-return') . $reason . ' || ' . __('Order status changed to Cancelled.', 'wc-order-cancellation-return'));

	}
}

new WC_Order_Cancellation_Return_Handle_Order_Cancellation();
