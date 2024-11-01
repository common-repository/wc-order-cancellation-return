<?php
/**
 * Plugin Name: WooCommerce Cancel / Return Orders
 * Plugin URI: https://wordpress.org/plugins/wc-order-cancellation-return
 * Description: Allows the customers cancel and return their orders on WooCommerce site.
 * Version: 1.0.6
 * Author: YoOhw.com
 * Author URI: https://yoohw.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Requires at least: 5.2
 * Requires PHP: 7.0
 * Text Domain: wc-order-cancellation-return
 * Requires Plugins: woocommerce
 */

if (!defined('ABSPATH')) {
	exit;
}

class WC_Order_Cancellation_Return {

	public function __construct() {
		add_action('plugins_loaded', [$this, 'load_textdomain']);
		add_filter('plugin_action_links_' . plugin_basename(__FILE__), [$this, 'add_action_links']);

		$this->includes();
	}

	public function load_textdomain() {
		load_plugin_textdomain('wc-order-cancellation-return', false, basename(dirname(__FILE__)) . '/languages/');
	}

	public function add_action_links($links) {
		$settings_link = '<a href="admin.php?page=wc-settings&tab=orders">Settings</a>';
		array_unshift($links, $settings_link);
		return $links;
	}

	private function includes() {
		include_once plugin_dir_path(__FILE__) . 'inc/cores/yoocr-wc-order-cancellation-return-notices.php';
		include_once plugin_dir_path(__FILE__) . 'inc/cores/yoocr-wc-order-cancellation-return-frontend.php';
		include_once plugin_dir_path(__FILE__) . 'inc/cores/yoocr-wc-order-cancellation-return-backend.php';
		include_once plugin_dir_path(__FILE__) . 'inc/cores/yoocr-wc-order-cancellation-return-emails.php';
		include_once plugin_dir_path(__FILE__) . 'inc/cores/yoocr-wc-order-cancellation-return-scripts-locate-template-classes.php';
	}
}

new WC_Order_Cancellation_Return();
