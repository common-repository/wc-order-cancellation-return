<?php

if (!defined('ABSPATH')) {
    exit;
}

class WC_Order_Cancellation_Return_Customer_Email_Refunded_Order {

    public function __construct() {
        add_action('woocommerce_email_before_order_table', [$this, 'yoocr_wc_order_cancellation_return_customer_email_refunded_order_return_approved'], 10, 4);
        add_filter('woocommerce_email_subject_customer_refunded_order', [$this, 'yoocr_wc_order_cancellation_return_customer_email_refunded_order_return_approved_subject'], 10, 2);
    }

    public function yoocr_wc_order_cancellation_return_customer_email_refunded_order_return_approved($order, $sent_to_admin, $plain_text, $email) {
        if ($email->id === 'customer_refunded_order') {
            $approval_reason = $order->get_meta('request_return_approved', true);

            if (!empty($approval_reason)) {
                if ($plain_text) {
                    echo esc_html(__('Return order:', 'wc-order-cancellation-return')) . ' ' . esc_html($approval_reason) . "\n";
                } else {
                    echo '<p>' . esc_html__('Return order:', 'wc-order-cancellation-return') . ' <strong>' . esc_html($approval_reason) . '</strong></p>';
                }
            }
        }
    }

    public function yoocr_wc_order_cancellation_return_customer_email_refunded_order_return_approved_subject($subject, $order) {
        $approval_reason = $order->get_meta('request_return_approved', true);
        if (!empty($approval_reason)) {
            $subject = __('Your request for order return is approved', 'wc-order-cancellation-return');
        }
        return $subject;
    }
}

new WC_Order_Cancellation_Return_Customer_Email_Refunded_Order();

?>