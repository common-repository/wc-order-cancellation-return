<?php

if (!defined('ABSPATH')) {
	exit;
}

class WC_Order_Cancellation_Return_Cancel_Order_Popup_Form {

	public function __construct() {
		add_action('wp_footer', [$this, 'yoocr_wc_order_cancellation_return_cancel_order_form']);
	}

	public function yoocr_wc_order_cancellation_return_cancel_order_form() {
		if (is_wc_endpoint_url('order-received') || is_account_page()) {
			$reasons_string = get_option('order_cancel_reasons', '');
			$reasons = array_filter(array_map('trim', explode(PHP_EOL, $reasons_string)));
			?>
			<div id="cancelOrderPopup">
				<form id="cancelOrderForm">
					<p><?php esc_html_e('Please leave your reason for the order cancellation:', 'wc-order-cancellation-return'); ?></p>
					<?php foreach ($reasons as $reason): ?>
						<label>
							<input type="radio" name="cancel_reason" value="<?php echo esc_attr($reason); ?>"> 
							<?php echo esc_html($reason); ?>
						</label>
					<?php endforeach; ?>
					<label>
						<input type="radio" name="cancel_reason" value="Other" <?php echo empty($reasons) ? 'checked' : ''; ?>>
						<?php echo empty($reasons) ? esc_html__('I cancel the order because:', 'wc-order-cancellation-return') : esc_html__('Other reason', 'wc-order-cancellation-return'); ?>
					</label>
					<textarea id="cancelOtherReasonText" name="other_reason" placeholder="<?php esc_attr_e('Please type your reason', 'wc-order-cancellation-return'); ?>" <?php echo empty($reasons) ? '' : 'class="hidden"'; ?>></textarea>
					<input type="hidden" name="order_id" id="cancelOrderPopupOrderId">
					<button type="button" id="submitCancelReasonButton" class="cancel-order-confirm"><?php esc_html_e('Confirm cancellation', 'wc-order-cancellation-return'); ?></button>
					<button type="button" class="cancel-order"><?php esc_html_e('Close', 'wc-order-cancellation-return'); ?></button>
					<div id="cancelError" class="error-message hidden"><?php esc_html_e('Please select or type your reason.', 'wc-order-cancellation-return'); ?></div>
					<div id="cancelOtherReasonError" class="error-message hidden"><?php esc_html_e('Please type your reason.', 'wc-order-cancellation-return'); ?></div>
				</form>
			</div>
			<?php
		}
	}
}

new WC_Order_Cancellation_Return_Cancel_Order_Popup_Form();
