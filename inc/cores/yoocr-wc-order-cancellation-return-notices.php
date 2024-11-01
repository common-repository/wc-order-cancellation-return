<?php

if (!defined('ABSPATH')) {
    exit;
}

class WC_Order_Cancellation_Return_Notices {

    public function __construct() {
        add_action('admin_notices', [$this, 'wc_order_cancellation_return_check_woocommerce']);
        add_action('admin_notices', [$this, 'wc_order_cancellation_return_admin_notice']);
        add_action('wp_ajax_dismiss_wc_order_cancellation_return_notice', [$this, 'wc_order_cancellation_return_dismiss_notice']);
        add_action('wp_ajax_never_show_wc_order_cancellation_return_notice', [$this, 'wc_order_cancellation_return_never_show_notice']);
    }

    public function wc_order_cancellation_return_check_woocommerce() {
        if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            ?>
            <div class="notice notice-warning is-dismissible">
                <p><?php esc_html_e('WooCommerce Order Cancellation / Return requires WooCommerce to be installed and activated.', 'wc-order-cancellation-return'); ?></p>
            </div>
            <?php
        }
    }

    public function wc_order_cancellation_return_admin_notice() {
        $user_id = get_current_user_id();
        $activation_time = get_user_meta($user_id, 'wc_order_cancellation_return_activation_time', true);
        $current_time = current_time('timestamp');

        if (get_user_meta($user_id, 'wc_order_cancellation_return_never_show_again', true) === 'yes') {
            return;
        }

        if (!$activation_time) {
            update_user_meta($user_id, 'wc_order_cancellation_return_activation_time', $current_time);
            return;
        }

        $time_since_activation = $current_time - $activation_time;
        $days_since_activation = floor($time_since_activation / DAY_IN_SECONDS);

        if ($days_since_activation >= 7 && (($days_since_activation - 7) % 90 === 0)) {
            if (get_user_meta($user_id, 'wc_order_cancellation_return_notice_dismissed', true) !== 'yes') {
                ?>
                <div class="notice notice-info is-dismissible">
                    <p><?php echo wp_kses(
                        __('Thank you for using WooCommerce Order Cancellation / Return! Please consider <a href="https://wordpress.org/plugins/wc-order-cancellation-return/#reviews" target="_blank">leaving a review</a> <span style="color: #e26f56;">&#9733;&#9733;&#9733;&#9733;&#9733;</span> to help us improve and grow.', 'wc-order-cancellation-return'), 
                        [
                            'a' => ['href' => [], 'target' => []],
                            'span' => ['style' => []]
                        ]
                    ); ?></p>
                    <p><a href="#" onclick="WC_Order_Cancellation_Return_Admin_Notice.dismissForever();"><?php esc_html_e('Never show this again', 'wc-order-cancellation-return'); ?></a></p>
                </div>
                <?php
                add_action('admin_footer', [$this, 'wc_order_cancellation_return_admin_footer_scripts']);
            }
        }
    }

    public function wc_order_cancellation_return_admin_footer_scripts() {
        ?>
        <script type="text/javascript">
            var WC_Order_Cancellation_Return_Admin_Notice = {
                dismissForever: function() {
                    jQuery.ajax({
                        url: ajaxurl,
                        type: "POST",
                        data: {
                            action: "never_show_wc_order_cancellation_return_notice",
                        },
                        success: function(response) {
                            jQuery(".notice.notice-info").hide();
                        }
                    });
                }
            };
            jQuery(document).on("click", ".notice.is-dismissible", function(){
                jQuery.ajax({
                    url: ajaxurl,
                    type: "POST",
                    data: {
                        action: "dismiss_wc_order_cancellation_return_notice",
                    }
                });
            });
        </script>
        <?php
    }

    public function wc_order_cancellation_return_dismiss_notice() {
        $user_id = get_current_user_id();
        update_user_meta($user_id, 'wc_order_cancellation_return_notice_dismissed', 'yes');
    }

    public function wc_order_cancellation_return_never_show_notice() {
        $user_id = get_current_user_id();
        update_user_meta($user_id, 'wc_order_cancellation_return_never_show_again', 'yes');
    }
}

new WC_Order_Cancellation_Return_Notices();

?>