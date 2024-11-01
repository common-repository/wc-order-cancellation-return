<?php

if (!defined('ABSPATH')) {
	exit;
}

class WC_Order_Cancellation_Return_Scripts_Locate_Template_Classes {

	public function __construct() {
		add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
		add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
		add_filter('woocommerce_locate_template', [$this, 'locate_template'], 10, 3);
		add_filter('woocommerce_email_classes', [$this, 'add_email_classes']);
	}

	public function enqueue_scripts() {
		if (is_account_page() || is_order_received_page()) {
			wp_enqueue_script('wcocr-cancel-order', plugins_url('../../js/yoocr-wc-order-cancellation-return-cancel-button-form.js', __FILE__), array('jquery'), '1.0.0', true);
			wp_localize_script('wcocr-cancel-order', 'wcocr_cancel_order_vars', array(
				'ajax_url' => admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce('cancel_order_nonce'),
				'sending_text' => __('Sending...', 'wc-order-cancellation-return'),
			));

			wp_enqueue_script('wcocr-return-order', plugins_url('../../js/yoocr-wc-order-cancellation-return-return-button-form.js', __FILE__), array('jquery'), '1.0.0', true);
			wp_localize_script('wcocr-return-order', 'wcocr_return_order_vars', array(
				'ajax_url' => admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce('return_order_nonce'),
				'sending_text' => __('Sending...', 'wc-order-cancellation-return'),
			));

			wp_enqueue_style('wcocr-cancel-order-style', plugins_url('../../css/yoocr-wc-order-cancellation-return-style.css', __FILE__), '1.0.0', true);
		}
	}

	public function admin_enqueue_scripts() {
		wp_enqueue_style('wcocr-admin-style', plugins_url('../../css/yoocr-wc-order-cancellation-return-admin-style.css', __FILE__), '1.0.1', true);
	}

	public function locate_template($template, $template_name, $template_path) {
		$basename = basename($template);
		if ($basename == 'orders.php') {
			$template = trailingslashit(plugin_dir_path(__FILE__)) . '../../woocommerce/myaccount/orders.php';
		}
		return $template;
	}

	public function add_email_classes($email_classes) {
		$enable_return = get_option('order_return_enable_return', 'no');
		if ('yes' === $enable_return) {
			include_once plugin_dir_path(__FILE__) . '../emails/yoocr-wc-order-cancellation-return-class-wc-order-return-request-email.php';
			$email_classes['YOOCR_WC_Order_Cancellation_Return_Order_Return_Request_Email'] = new YOOCR_WC_Order_Cancellation_Return_Order_Return_Request_Email();
		}

		$enable_cancel = get_option('order_cancel_enable_cancellation', 'no');
		if ('yes' === $enable_cancel) {
			include_once plugin_dir_path(__FILE__) . '../emails/yoocr-wc-order-cancellation-return-class-wc-order-cancel-request-email.php';
			$email_classes['YOOCR_WC_Order_Cancellation_Return_Order_Cancel_Request_Email'] = new YOOCR_WC_Order_Cancellation_Return_Order_Cancel_Request_Email();
		}

		return $email_classes;
	}
}

new WC_Order_Cancellation_Return_Scripts_Locate_Template_Classes();
