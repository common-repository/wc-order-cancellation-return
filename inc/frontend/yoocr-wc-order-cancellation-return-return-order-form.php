<?php

if (!defined('ABSPATH')) {
	exit;
}

class WC_Order_Cancellation_Return_Return_Order_Popup_Form {

	public function __construct() {
			add_action('wp_footer', [$this, 'yoocr_wc_order_cancellation_return_return_order_form']);
	}

	public function yoocr_wc_order_cancellation_return_return_order_form() {
		if (is_wc_endpoint_url('order-received') || is_account_page()) {
			$reasons_string = get_option('order_return_reasons', '');
			$reasons = array_filter(array_map('trim', explode(PHP_EOL, $reasons_string)));
			?>
			<div id="returnOrderPopup">
				<form id="returnOrderForm">
					<p><?php esc_html_e('Please leave your reason for the order return:', 'wc-order-cancellation-return'); ?></p>
					<?php foreach ($reasons as $reason): ?>
						<label>
							<input type="radio" name="return_reason" value="<?php echo esc_attr($reason); ?>"> 
							<?php echo esc_html($reason); ?>
						</label>
					<?php endforeach; ?>
					<label>
						<input type="radio" name="return_reason" value="Other" <?php echo empty($reasons) ? 'checked' : ''; ?>>
						<?php echo empty($reasons) ? esc_html__('I return the order because:', 'wc-order-cancellation-return') : esc_html__('Other reason', 'wc-order-cancellation-return'); ?>
					</label>
					<textarea id="returnOtherReasonText" name="other_reason" placeholder="<?php esc_attr_e('Please type your reason', 'wc-order-cancellation-return'); ?>" <?php echo empty($reasons) ? '' : 'class="hidden"'; ?>></textarea>
					<input type="hidden" name="order_id" id="returnOrderPopupOrderId">
					<button type="button" id="submitReturnReasonButton" class="return-order-confirm"><?php esc_html_e('Confirm request return', 'wc-order-cancellation-return'); ?></button>
					<button type="button" class="return-order"><?php esc_html_e('Close', 'wc-order-cancellation-return'); ?></button>
					<div id="returnError" class="error-message hidden"><?php esc_html_e('Please select or type your reason.', 'wc-order-cancellation-return'); ?></div>
					<div id="returnOtherReasonError" class="error-message hidden"><?php esc_html_e('Please type your reason.', 'wc-order-cancellation-return'); ?></div>
				</form>
			</div>
			<?php
		}
	}
}

new WC_Order_Cancellation_Return_Return_Order_Popup_Form();
