<?php

if (!defined('ABSPATH')) {
	exit;
}

if (!function_exists('is_plugin_active')) {
    include_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

class WC_Order_Cancellation_Return_Settings {

    public function __construct() {
        register_activation_hook(__FILE__, [$this, 'yoocr_wc_order_cancellation_return_set_default_settings']);
        if (!is_plugin_active('wc-order-splitter-premium/wc-order-splitter-premium.php') || get_option('wc_order_splitter_premium_license_status') !== 'activated') {
            add_filter('woocommerce_settings_tabs_array', [$this, 'yoocr_wc_order_cancellation_return_settings_add_orders_tab'], 30);
            add_action('woocommerce_settings_tabs_orders', [$this, 'yoocr_wc_order_cancellation_return_settings_orders_add_cancellation_section'], 1);
            add_action('woocommerce_update_options_orders', [$this, 'yoocr_wc_order_cancellation_return_settings_orders_cancellation_update']);
            add_action('woocommerce_settings_tabs_orders', [$this, 'yoocr_wc_order_cancellation_return_settings_orders_add_return_section'], 2);
            add_action('woocommerce_update_options_orders', [$this, 'yoocr_wc_order_cancellation_return_settings_orders_return_update']);
            add_action('woocommerce_admin_field_available_time', [$this, 'render_available_time_field']);
        }
    }

	public function yoocr_wc_order_cancellation_return_set_default_settings() {
		if (false === get_option('order_cancel_enable_cancellation')) {
			update_option('order_cancel_enable_cancellation', 'yes');
		}
		if (false === get_option('order_cancel_order_status')) {
			update_option('order_cancel_order_status', array('wc-processing'));
		}
		if (false === get_option('order_cancel_reasons')) {
			update_option('order_cancel_reasons', '');
		}
		if (false === get_option('order_cancel_cancellation_approval')) {
			update_option('order_cancel_cancellation_approval', 'yes');
		}
		if (false === get_option('order_return_enable_return')) {
			update_option('order_return_enable_return', 'no');
		}
		if (false === get_option('order_return_order_status')) {
			update_option('order_return_order_status', array('wc-completed'));
		}
		if (false === get_option('order_return_reasons')) {
			update_option('order_return_reasons', '');
		}
		if (false === get_option('order_return_approval')) {
			update_option('order_return_approval', 'yes');
		}
	}

	public function yoocr_wc_order_cancellation_return_settings_add_orders_tab($settings_tabs) {
		$settings_tabs['orders'] = __('Orders', 'wc-order-cancellation-return');
		return $settings_tabs;
	}

	public function yoocr_wc_order_cancellation_return_settings_orders_add_cancellation_section() {
		woocommerce_admin_fields($this->get_cancel_order_settings());
	}

	public function get_cancel_order_settings() {
		$settings = array(
			'section_title' => array(
				'name'     => __('Order cancellation', 'wc-order-cancellation-return'),
				'type'     => 'title',
				'desc'     => '<span class="yo-premium"><i class="dashicons dashicons-lock"></i> Upgrade to Premium version for more features such as Form customization, Additional notice, Cancel button at email, and more... <a href="https://yoohw.com/product/woocommerce-order-cancellation-return-premium/" target="_blank" class="premium-label">Upgrade</a></span>',
				'id'       => 'order_cancel_section_title'
			),
			'enable_cancellation' => array(
				'name'     => __('Enable cancellation', 'wc-order-cancellation-return'),
				'type'     => 'checkbox',
				'id'       => 'order_cancel_enable_cancellation',
				'default'  => 'yes',
				'desc'     => __('Allows customers to cancel their orders.', 'wc-order-cancellation-return'),
			),
			'order_status' => array(
				'name'     => __('Allowed status', 'wc-order-cancellation-return'),
				'type'     => 'multiselect',
				'desc_tip' => __('Select statuses for which orders can be cancelled.', 'wc-order-cancellation-return'),
				'id'       => 'order_cancel_order_status',
				'options'  => wc_get_order_statuses(),
				'default'  => array('wc-processing'),
				'custom_attributes' => array(
					'data-placeholder' => __('Select order statuses', 'wc-order-cancellation-return')
				),
				'class'    => 'wc-enhanced-select',
				'css'      => 'min-width:300px;',
			),
			'reasons' => array(
				'name' => __('Reasons', 'wc-order-cancellation-return'),
				'type' => 'textarea',
				'desc' => __('One reason per line:', 'wc-order-cancellation-return'),
				'desc_tip' => __('Enter the reasons for order cancellation, one per line. If you leave it empty then the customer has to type an optional reason.', 'wc-order-cancellation-return'),
				'id' => 'order_cancel_reasons',
				'css' => 'min-width:300px; height: 100px;',
				'custom_attributes' => array(
					'placeholder' => __('Type each reason on a new line', 'wc-order-cancellation-return')
				),
			),
			'available_time' => array(
                'name' => __('Available time', 'wc-order-cancellation-return-premium'),
                'type' => 'available_time',
                'desc' => __('After the order has created.', 'wc-order-cancellation-return-premium'),
                'desc_tip' => __('Set the time duration for cancellation. Enter the number and select the unit of time.', 'wc-order-cancellation-return-premium'),
                'id' => 'order_cancel_available_time',
            ),
			'cancellation_approval' => array(
				'name'     => __('Cancellation request', 'wc-order-cancellation-return'),
				'type'     => 'checkbox',
				'id'       => 'order_cancel_cancellation_approval',
				'default'  => 'yes',
				'desc'     => __('Enable request to cancel the orders.', 'wc-order-cancellation-return'),
			),
			'section_end' => array(
				'type' => 'sectionend',
				'id' => 'order_cancel_section_end'
			)
		);

		return apply_filters('order_cancel_settings', $settings);
	}

	public function yoocr_wc_order_cancellation_return_settings_orders_cancellation_update() {
		woocommerce_update_options($this->get_cancel_order_settings());
	}

	public function yoocr_wc_order_cancellation_return_settings_orders_add_return_section() {
		woocommerce_admin_fields($this->get_return_order_settings());
	}

	public function get_return_order_settings() {
		$settings = array(
			'section_title' => array(
				'name'     => __('Order return', 'wc-order-cancellation-return'),
				'type'     => 'title',
				'desc'     => '<span class="yo-premium"><i class="dashicons dashicons-lock"></i> Upgrade to Premium version for more features such as Form customization, Additional notice, Return button, Return attachments, and more... <a href="https://yoohw.com/product/woocommerce-order-cancellation-return-premium/" target="_blank" class="premium-label">Upgrade</a></span>',
				'id'       => 'order_return_section_title'
			),
			'enable_return' => array(
				'name'     => __('Enable return', 'wc-order-cancellation-return'),
				'type'     => 'checkbox',
				'id'       => 'order_return_enable_return',
				'default'  => 'no',
				'desc'     => __('Allows customers to return their orders.', 'wc-order-cancellation-return'),
			),
			'order_status' => array(
				'name'     => __('Allowed status', 'wc-order-cancellation-return'),
				'type'     => 'select',
				'desc_tip' => __('Select statuses for which orders can be returned.', 'wc-order-cancellation-return'),
				'id'       => 'order_return_order_status',
				'options'  => wc_get_order_statuses(),
				'custom_attributes' => array(
					'data-placeholder' => __('Select order statuses', 'wc-order-cancellation-return')
				),
				'class'    => 'wc-enhanced-select',
				'css'      => 'min-width:300px;',
			),
			'reasons' => array(
				'name' => __('Reasons', 'wc-order-cancellation-return'),
				'type' => 'textarea',
				'desc' => __('One reason per line:', 'wc-order-cancellation-return'),
				'desc_tip' => __('Enter the reasons for order return, one per line. If you leave it empty then the customer has to type an optional reason.', 'wc-order-cancellation-return'),
				'id' => 'order_return_reasons',
				'css' => 'min-width:300px; height: 100px;',
				'custom_attributes' => array(
					'placeholder' => __('Type each reason on a new line', 'wc-order-cancellation-return')
				),
			),
			'available_time' => array(
                'name' => __('Available time', 'wc-order-cancellation-return-premium'),
                'type' => 'available_time',
                'desc' => __('After the order status has changed to the status that you set above.', 'wc-order-cancellation-return-premium'),
                'desc_tip' => __('Set the time duration for return request. Enter the number and select the unit of time.', 'wc-order-cancellation-return-premium'),
                'id' => 'order_return_available_time',
            ),
			'return_approval' => array(
				'name'     => __('Return request', 'wc-order-cancellation-return'),
				'type'     => 'checkbox',
				'id'       => 'order_return_approval',
				'default'  => 'yes',
				'desc'     => __('Enable request to return the orders.', 'wc-order-cancellation-return'),
			),
			'section_end' => array(
				'type' => 'sectionend',
				'id' => 'order_return_section_end'
			)
		);

		return apply_filters('order_return_settings', $settings);
	}

	public function yoocr_wc_order_cancellation_return_settings_orders_return_update() {
		woocommerce_update_options($this->get_return_order_settings());
	}

    public function render_available_time_field($value) {
        $field_id = $value['id'];
        $option_value = get_option($field_id, array('value' => '', 'unit' => 'hours'));
        $time_value = isset($option_value['value']) ? $option_value['value'] : '';
        $time_unit = isset($option_value['unit']) ? $option_value['unit'] : 'hours';
        ?>
        <tr valign="top">
            <th scope="row" class="titledesc">
                <label for="<?php echo esc_attr($field_id); ?>"><?php echo esc_html($value['name']); ?></label>
                <?php if ( ! empty( $value['desc_tip'] ) ) : ?>
                    <span class="woocommerce-help-tip" data-tip="<?php echo esc_attr( $value['desc_tip'] ); ?>"></span>
                <?php endif; ?>
            </th>
            <td class="forminp forminp-<?php echo sanitize_title($value['type']); ?>">
                <input name="<?php echo esc_attr($field_id); ?>[value]" id="<?php echo esc_attr($field_id); ?>_value" type="number" style="width: 70px;" value="<?php echo esc_attr($time_value); ?>" placeholder="<?php echo esc_attr($value['placeholder']); ?>" />
                <select name="<?php echo esc_attr($field_id); ?>[unit]" id="<?php echo esc_attr($field_id); ?>_unit" style="width: auto;">
                    <option value="hours" <?php selected($time_unit, 'hours'); ?>><?php esc_html_e('Hour(s)', 'wc-order-cancellation-return-premium'); ?></option>
                    <option value="days" <?php selected($time_unit, 'days'); ?>><?php esc_html_e('Day(s)', 'wc-order-cancellation-return-premium'); ?></option>
                    <option value="weeks" <?php selected($time_unit, 'weeks'); ?>><?php esc_html_e('Week(s)', 'wc-order-cancellation-return-premium'); ?></option>
                </select>
                <p class="description"><?php echo wp_kses_post($value['desc']); ?></p>
            </td>
        </tr>
        <?php
    }

    public function save_available_time_field() {
        if (isset($_POST['order_cancel_available_time'])) {
            update_option('order_cancel_available_time', wc_clean($_POST['order_cancel_available_time']));
        }
        if (isset($_POST['order_return_available_time'])) {
            update_option('order_return_available_time', wc_clean($_POST['order_return_available_time']));
        }
    }
}

new WC_Order_Cancellation_Return_Settings();
