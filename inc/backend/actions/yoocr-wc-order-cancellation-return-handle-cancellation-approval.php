<?php

if (!defined('ABSPATH')) {
	exit;
}

class WC_Order_Cancellation_Return_Handle_Cancellation_Approval {

	public function __construct() {
		add_action('admin_init', [$this, 'yoocr_wc_order_cancellation_return_handle_order_cancellation_approval']);
		add_action('wp_ajax_yoocr_wc_order_cancellation_return_handle_order_cancellation_rejection', [$this, 'yoocr_wc_order_cancellation_return_handle_order_cancellation_rejection']);
	}

	public function yoocr_wc_order_cancellation_return_handle_order_cancellation_approval() {
		if (isset($_POST['approve_cancellation'], $_POST['order_id'], $_POST['approve_cancellation_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['approve_cancellation_nonce'])), 'approve_cancellation_action')) {
			$order_id = intval($_POST['order_id']);
			$order = wc_get_order($order_id);

			if (!$order) {
				return;
			}

			if ($order->has_status('request-cancel')) {
				$order->update_meta_data('request_cancellation_approved', __('Your cancellation request has been approved.', 'wc-order-cancellation-return'));
				$order->update_status('cancelled', __('Cancellation approved and order cancelled.', 'wc-order-cancellation-return'));
				$order->save();

				add_action('admin_notices', function() {
					echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Cancellation has been approved and the order status updated to cancelled.', 'wc-order-cancellation-return') . '</p></div>';            
				});
				
				wp_redirect($_SERVER['REQUEST_URI']);
				exit;
			} else {
				add_action('admin_notices', function() {
					echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__('Invalid order or status cannot be changed.', 'wc-order-cancellation-return') . '</p></div>';
				});
			}
		}
	}

	public function yoocr_wc_order_cancellation_return_handle_order_cancellation_rejection() {
		if (!isset($_POST['rejection_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['rejection_nonce'])), 'handle_rejection_action')) {
			wp_send_json_error(['message' => __('Invalid nonce', 'wc-order-cancellation-return')]);
			wp_die();
		}

		if (!current_user_can('manage_woocommerce')) {
			wp_send_json_error(['message' => __('Unauthorized', 'wc-order-cancellation-return')]);
			return;
		}

		$order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
		$reason = isset($_POST['reason']) ? sanitize_text_field($_POST['reason']) : '';

		if (!$order_id || !$reason) {
			wp_send_json_error(['message' => __('Invalid order ID or reason', 'wc-order-cancellation-return')]);
			return;
		}

		$order = wc_get_order($order_id);
		if (!$order) {
			wp_send_json_error(['message' => __('Order not found', 'wc-order-cancellation-return')]);
			return;
		}

		if ($order->has_status('request-cancel')) {
			$previous_status = $order->get_meta('status_before_cancellation_request');
			$note = sprintf(
				__('Admin rejected cancellation request. Reason: %s || Order status changed from %s to %s.', 'wc-order-cancellation-return'),
				$reason,
				$order->get_status(),
				$previous_status
			);

			$order->update_meta_data('order_cancellation_rejection_reason', $reason);
			$order->set_status($previous_status);
			$order->save();
			$order->add_order_note($note);
			
			wp_send_json_success(['message' => __('Order rejection processed and reason saved', 'wc-order-cancellation-return')]);
		} else {
			wp_send_json_error(['message' => __('Order is not in Request cancellation status', 'wc-order-cancellation-return')]);
		}
		wp_die();
	}
}

new WC_Order_Cancellation_Return_Handle_Cancellation_Approval();
