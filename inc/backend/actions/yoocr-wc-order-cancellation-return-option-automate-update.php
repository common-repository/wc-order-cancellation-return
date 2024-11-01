<?php

if (!defined('ABSPATH')) {
    exit;
}

class WC_Order_Cancellation_Return_Option_Update {

    public function __construct() {
        add_action('update_option_order_cancel_cancellation_approval', [$this, 'update_cancel_request_settings'], 10, 2);
    }

    public function update_cancel_request_settings($old_value, $new_value) {
        // Get the current settings
        $settings = get_option('woocommerce_order_cancel_request_settings');
        
        // Check if the settings are serialized
        if (is_string($settings)) {
            $settings = maybe_unserialize($settings);
        }

        // Update the 'enabled' value based on the new value of 'order_cancel_cancellation_approval'
        if ($new_value === 'yes') {
            $settings['enabled'] = 'yes';
        } else {
            $settings['enabled'] = 'no';
        }

        // Update the option with the new settings
        update_option('woocommerce_order_cancel_request_settings', maybe_serialize($settings));
    }
}

new WC_Order_Cancellation_Return_Option_Update();
