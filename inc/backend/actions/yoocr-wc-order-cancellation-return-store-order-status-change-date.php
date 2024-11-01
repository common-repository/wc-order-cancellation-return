<?php

if (!defined('ABSPATH')) {
	exit;
}

class WC_Order_Cancellation_Return_Status_Change_Date_Storage {

	public function __construct() {
		add_action('woocommerce_order_status_changed', [$this, 'yoocr_wc_order_cancellation_return_store_order_status_change_date'], 10, 3);
	}

	public function yoocr_wc_order_cancellation_return_store_order_status_change_date($order_id, $old_status, $new_status) {
		$allowed_status = get_option('order_return_order_status', '');
		if ('wc-' . $new_status === $allowed_status) {
			$order = wc_get_order($order_id);
			$order->update_meta_data('order_status_change_date_' . $new_status, current_time('mysql'));
			$order->save();
		}
	}
}

// Instantiate the class to ensure the action is hooked
new WC_Order_Cancellation_Return_Status_Change_Date_Storage();
