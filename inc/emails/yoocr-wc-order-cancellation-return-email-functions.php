<?php

if (!defined('ABSPATH')) {
	exit;
}

class WC_Order_Cancellation_Return_Function_Return_Email {

	public function __construct() {
		add_action('yoocr_wc_order_cancellation_return_handle_email_order_return_request', [$this, 'yoocr_wc_order_cancellation_return_trigger_return_request_email']);
	}

	public function yoocr_wc_order_cancellation_return_trigger_return_request_email($order_id) {
		$mailer = WC()->mailer();
		$emails = $mailer->get_emails();
		$order = wc_get_order($order_id);
	
		if ($order) {
			$attachments = $order->get_meta('return_attachments', true);
	
			if (!empty($emails) && isset($emails['YOOCR_WC_Order_Cancellation_Return_Order_Return_Request_Email'])) {
				$email = $emails['YOOCR_WC_Order_Cancellation_Return_Order_Return_Request_Email'];
				$email->trigger($order_id, $attachments);
			}
		}
	}    
}

new WC_Order_Cancellation_Return_Function_Return_Email();
