<?php

if (!defined('ABSPATH')) {
    exit;
}

class WC_Order_Cancellation_Return_Order_Status_Creating {

    public function __construct() {
        add_action('init', [$this, 'yoocr_wc_order_cancellation_return_order_status_wc_request_return_created']);
        add_action('init', [$this, 'yoocr_wc_order_cancellation_return_order_status_wc_request_cancellation_created']);
        add_filter('wc_order_statuses', [$this, 'yoocr_wc_order_cancellation_return_add_request_return_to_order_statuses']);
        add_filter('wc_order_statuses', [$this, 'yoocr_wc_order_cancellation_return_add_request_cancellation_to_order_statuses']);
        add_filter('woocommerce_can_order_status_be_changed', [$this, 'yoocr_wc_order_cancellation_return_order_status_wc_request_return_can_be_changed'], 10, 3);
        add_filter('woocommerce_can_order_status_be_changed', [$this, 'yoocr_wc_order_cancellation_return_order_status_wc_request_cancellation_can_be_changed'], 10, 3);
    }

    public function yoocr_wc_order_cancellation_return_order_status_wc_request_return_created() {
        $enable_return = get_option('order_return_enable_return', 'no');
        
        if ('yes' === $enable_return) {
            register_post_status('wc-request-return', array(
                'label'                     => _x('Return requested', 'Order status', 'wc-order-cancellation-return'),
                'public'                    => true,
                'exclude_from_search'       => false,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
                'label_count'               => _n_noop('Request return <span class="count">(%s)</span>', 'Request returns <span class="count">(%s)</span>', 'wc-order-cancellation-return')
            ));
        }
    }

    public function yoocr_wc_order_cancellation_return_order_status_wc_request_cancellation_created() {
        $enable_cancellation = get_option('order_cancel_enable_cancellation', 'no');
        
        if ('yes' === $enable_cancellation) {
            register_post_status('wc-request-cancel', array(
                'label'                     => _x('Cancel requested', 'Order status', 'wc-order-cancellation-return'),
                'public'                    => true,
                'exclude_from_search'       => false,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
                'label_count'               => _n_noop('Request cancellation <span class="count">(%s)</span>', 'Request cancellations <span class="count">(%s)</span>', 'wc-order-cancellation-return')
            ));
        }
    }

    public function yoocr_wc_order_cancellation_return_add_request_return_to_order_statuses($order_statuses) {
        $new_order_statuses = array();
        foreach ($order_statuses as $key => $status) {
            $new_order_statuses[$key] = $status;
            if ('wc-completed' === $key) {
                $new_order_statuses['wc-request-return'] = _x('Return requested', 'Order status', 'wc-order-cancellation-return');
            }
        }

        return $new_order_statuses;
    }

    public function yoocr_wc_order_cancellation_return_add_request_cancellation_to_order_statuses($order_statuses) {
        $new_order_statuses = array();
        foreach ($order_statuses as $key => $status) {
            $new_order_statuses[$key] = $status;
            if ('wc-completed' === $key) {
                $new_order_statuses['wc-request-cancel'] = _x('Cancel requested', 'Order status', 'wc-order-cancellation-return');
            }
        }

        return $new_order_statuses;
    }

    public function yoocr_wc_order_cancellation_return_order_status_wc_request_return_can_be_changed($can_change, $order, $new_status) {
        $current_status = $order->get_status();

        if ('wc-request-return' === $current_status) {
            $can_change = true;
        }

        return $can_change;
    }

    public function yoocr_wc_order_cancellation_return_order_status_wc_request_cancellation_can_be_changed($can_change, $order, $new_status) {
        $current_status = $order->get_status();

        if ('wc-request-cancel' === $current_status) {
            $can_change = true;
        }

        return $can_change;
    }
}

new WC_Order_Cancellation_Return_Order_Status_Creating();
