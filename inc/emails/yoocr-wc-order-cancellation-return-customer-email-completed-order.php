<?php

if (!defined('ABSPATH')) {
    exit;
}

class WC_Order_Cancellation_Return_Customer_Email_Completed_Order {

    public function __construct() {
        add_action('woocommerce_email_before_order_table', [$this, 'yoocr_wc_order_cancellation_return_customer_email_completed_order_rejection_reason'], 20, 4);
        add_filter('woocommerce_email_subject_customer_completed_order', [$this, 'yoocr_wc_order_cancellation_return_customer_email_completed_order_subject'], 10, 2);
    }

    public function yoocr_wc_order_cancellation_return_customer_email_completed_order_rejection_reason($order, $sent_to_admin, $plain_text, $email) {
        if ($email->id === 'customer_completed_order' && !$sent_to_admin) {
            $rejection_reason = $order->get_meta('order_return_rejection_reason', true);

            if (!empty($rejection_reason)) {
                if ($plain_text) {
                    echo "\n" . esc_html(__('Rejection reason:', 'wc-order-cancellation-return')) . ' ' . esc_html($rejection_reason);
                } else {
                    echo '<p>' . esc_html__('Rejection reason:', 'wc-order-cancellation-return') . ' <strong>' . esc_html($rejection_reason) . '</strong></p>';
                }
            }
        }
    }

    public function yoocr_wc_order_cancellation_return_customer_email_completed_order_subject($subject, $order) {
        $rejection_reason = $order->get_meta('order_return_rejection_reason', true);
        if (!empty($rejection_reason)) {
            $subject = __('Your request for order return is rejected', 'wc-order-cancellation-return');
        }
        return $subject;
    }
}

new WC_Order_Cancellation_Return_Customer_Email_Completed_Order();

?>