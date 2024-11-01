<?php

if (!defined('ABSPATH')) {
	exit;
}

class WC_Order_Cancellation_Return_Edit_Order_Return_Approval {

	public function __construct() {
		add_action('woocommerce_admin_order_data_after_shipping_address', [$this, 'yoocr_wc_order_cancellation_return_edit_order_return_approval'], 10, 1);
		add_action('admin_footer', [$this, 'yoocr_wc_order_cancellation_return_admin_footer_scripts']);
	}

	public function yoocr_wc_order_cancellation_return_edit_order_return_approval($order) {
		if ($order->has_status('request-return')) {
			$return_reason = $order->get_meta('request_return_reason', true);

			echo '<p><strong>' . esc_html__('Return requested', 'wc-order-cancellation-return') . '</strong><br>' . esc_html__('Reason:', 'wc-order-cancellation-return') . ' ' . esc_html($return_reason) . '</p>';
			echo '<form method="post">';
			wp_nonce_field('approve_return_action', 'approve_return_nonce');
			echo '<input type="hidden" name="order_id" value="' . esc_attr($order->get_id()) . '">';
			echo '<button type="submit" name="approve_return" class="button-secondary icon-button" title="' . esc_attr__('Approved', 'wc-order-cancellation-return') . '" style="margin-right: 5px;" onclick="return confirm(\'' . esc_js(__('Are you sure to approve this return?', 'wc-order-cancellation-return')) . '\')"><span class="dashicons dashicons-yes"></span></button>';
			echo '</form>';
			
			echo '<input type="hidden" id="rejectionNonce" value="' . esc_attr(wp_create_nonce('handle_rejection_action')) . '">';
			echo '<button type="button" id="return-rejected" class="button-secondary icon-button" title="' . esc_attr__('Rejected', 'wc-order-cancellation-return') . '" onclick="WC_Order_Cancellation_Return_Admin.openRejectionModal();"><span class="dashicons dashicons-no"></span></button>';

			echo '<div id="rejectionModal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; overflow:auto; background-color:rgb(0,0,0); background-color:rgba(0,0,0,0.4);">
				<div style="background-color:#fefefe; margin:15% auto; padding:20px; border:1px solid #888; width:80%; max-width:500px;">
					<h3>' . esc_html__('Enter rejection reason', 'wc-order-cancellation-return') . '</h3>
					<textarea id="rejectionReason" rows="4" style="width:100%;"></textarea>
					<button type="button" onclick="WC_Order_Cancellation_Return_Admin.submitRejectionReason(' . esc_js($order->get_id()) . ');" class="button-primary">' . esc_html__('Submit', 'wc-order-cancellation-return') . '</button>
					<button type="button" onclick="WC_Order_Cancellation_Return_Admin.closeRejectionModal();" class="button-secondary">' . esc_html__('Cancel', 'wc-order-cancellation-return') . '</button>
				</div>
			</div>';

			// Pass the order status to the footer scripts function
			$this->yoocr_wc_order_cancellation_return_admin_footer_scripts(true);
		} else {
			$this->yoocr_wc_order_cancellation_return_admin_footer_scripts(false);
		}
	}

	public function yoocr_wc_order_cancellation_return_admin_footer_scripts($is_request_return = false) {
		if ($is_request_return) {
			?>
			<script type="text/javascript">
			var WC_Order_Cancellation_Return_Admin = {
				openRejectionModal: function() {
					document.getElementById("rejectionModal").style.display = "block";
				},
				closeRejectionModal: function() {
					document.getElementById("rejectionModal").style.display = "none";
				},
				submitRejectionReason: function(orderId) {
					var reason = document.getElementById("rejectionReason").value;
					var nonce = document.getElementById("rejectionNonce").value; // Ensure nonce is sent with request
					if (reason === "") {
						alert('<?php echo esc_js(__('Please enter a reason for rejection.', 'wc-order-cancellation-return')); ?>');
						return;
					}
					// Send rejection reason to server via Ajax
					jQuery.post(ajaxurl, {
						action: "yoocr_wc_order_cancellation_return_handle_order_rejection",
						order_id: orderId,
						reason: reason,
						rejection_nonce: nonce  // Sending nonce for verification
					}, function(response) {
						if(response.success) {
							alert('<?php echo esc_js(__('Rejection submitted.', 'wc-order-cancellation-return')); ?>');
							WC_Order_Cancellation_Return_Admin.closeRejectionModal();
							location.reload();
						} else {
							alert('<?php echo esc_js(__('Failed to submit rejection.', 'wc-order-cancellation-return')); ?>');
						}
					});
				}
			};
			</script>
			<?php
		}
	}
}

new WC_Order_Cancellation_Return_Edit_Order_Return_Approval();
