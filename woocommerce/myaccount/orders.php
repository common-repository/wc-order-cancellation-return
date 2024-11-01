<?php
/**
 * Orders
 *
 * Shows orders on the account page.
 *
 * This template is copied to work on the plugin WooCommerce Order Cancellation / Return
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.5.0
 */

defined('ABSPATH') || exit;

do_action('woocommerce_before_account_orders', $has_orders);

if ($has_orders) : ?>

	<table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
		<thead>
			<tr>
				<?php foreach (wc_get_account_orders_columns() as $column_id => $column_name) : ?>
					<th class="woocommerce-orders-table__header woocommerce-orders-table__header-<?php echo esc_attr($column_id); ?>">
						<span class="nobr"><?php echo esc_html($column_name); ?></span>
					</th>
				<?php endforeach; ?>
			</tr>
		</thead>

		<tbody>
			<?php
			foreach ($customer_orders->orders as $customer_order) {
				$order = wc_get_order($customer_order); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				$item_count = $order->get_item_count() - $order->get_item_count_refunded();
				?>
				<tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-<?php echo esc_attr($order->get_status()); ?> order">
					<?php foreach (wc_get_account_orders_columns() as $column_id => $column_name) : ?>
						<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-<?php echo esc_attr($column_id); ?>" data-title="<?php echo esc_attr($column_name); ?>">
							<?php if (has_action('woocommerce_my_account_my_orders_column_' . $column_id)) : ?>
								<?php do_action('woocommerce_my_account_my_orders_column_' . $column_id, $order); ?>

							<?php elseif ('order-number' === $column_id) : ?>
								<a href="<?php echo esc_url($order->get_view_order_url()); ?>">
									<?php echo esc_html(_x('#', 'hash before order number', 'wc-order-cancellation-return') . $order->get_order_number()); ?>
								</a>

							<?php elseif ('order-date' === $column_id) : ?>
								<time datetime="<?php echo esc_attr($order->get_date_created()->date('c')); ?>"><?php echo esc_html(wc_format_datetime($order->get_date_created())); ?></time>

							<?php elseif ('order-status' === $column_id) : ?>
								<?php echo esc_html(wc_get_order_status_name($order->get_status())); ?>

							<?php elseif ('order-total' === $column_id) : ?>
								<?php
								/* translators: 1: formatted order total 2: total order items */
								echo wp_kses_post(sprintf(_n('%1$s for %2$s item', '%1$s for %2$s items', $item_count, 'wc-order-cancellation-return'), $order->get_formatted_order_total(), $item_count));
								?>

							<?php elseif ('order-actions' === $column_id) : ?>
								<?php
								$actions = wc_get_account_orders_actions($order);

								if (!empty($actions)) {

									$is_cancellation_enabled = get_option('order_cancel_enable_cancellation', 'no');
									$allowed_statuses_option = get_option('order_cancel_order_status', array());
									$allowed_statuses = array_map(function ($status) {
										return str_replace('wc-', '', $status);
									}, $allowed_statuses_option);

									foreach ($actions as $key => $action) {
										if ($key !== 'cancel') {
											echo '<a href="' . esc_url($action['url']) . '" class="woocommerce-button button ' . sanitize_html_class($key) . '">' . esc_html($action['name']) . '</a>';
										}
									}

									if ('yes' === $is_cancellation_enabled && in_array($order->get_status(), $allowed_statuses)) {
										$display_cancel_button = true;

										// Retrieve and parse the `order_cancel_available_time` option
										$cancel_available_time = get_option('order_cancel_available_time', '');
										if ($cancel_available_time) {
											$cancel_available_time = maybe_unserialize($cancel_available_time);
										}
										$cancel_time_value = isset($cancel_available_time['value']) ? intval($cancel_available_time['value']) : 0;
										$cancel_time_unit = isset($cancel_available_time['unit']) ? $cancel_available_time['unit'] : 'hours';

										// Convert the specified time duration into seconds
										switch ($cancel_time_unit) {
											case 'days':
												$time_difference_allowed = $cancel_time_value * 86400; // 1 day = 86400 seconds
												break;
											case 'weeks':
												$time_difference_allowed = $cancel_time_value * 604800; // 1 week = 604800 seconds
												break;
											case 'hours':
											default:
												$time_difference_allowed = $cancel_time_value * 3600; // 1 hour = 3600 seconds
												break;
										}

										$order_date = $order->get_date_created();
										$order_time_utc = $order_date->getTimestamp();

										// Convert order time to the site's local time
										$order_time_local = $order_time_utc + (get_option('gmt_offset') * 3600);

										$current_time = current_time('timestamp');
										$time_difference = $current_time - $order_time_local;

										// Only display the cancel button if within the allowed time or if the time value is 0
										if ($cancel_time_value !== 0 && $time_difference > $time_difference_allowed) {
											$display_cancel_button = false;
										}

										// Check if the order has a cancellation rejection reason
										$order_cancellation_rejection_reason = $order->get_meta('order_cancellation_rejection_reason', true);
										if (!empty($order_cancellation_rejection_reason)) {
											$display_cancel_button = false;
										}

										if ($display_cancel_button) {
											$cancel_url = wp_nonce_url(add_query_arg(array(
												'cancel_order' => 'true',
												'order' => $order->get_id(),
												'order_key' => $order->get_order_key(),
											), $order->get_cancel_order_url()), 'woocommerce-cancel_order');

											echo '<a href="' . esc_url($cancel_url) . '" class="woocommerce-button button cancel cancel_order_button" data-order-id="' . esc_attr($order->get_id()) . '">' . esc_html__('Cancel', 'wc-order-cancellation-return') . '</a>';
										}
									}

									$enable_return = get_option('order_return_enable_return', 'no');
									$allowed_status = get_option('order_return_order_status', ''); // Single select field now
									$return_rejected_reason = $order->get_meta('order_return_rejection_reason', true);

									if ('yes' === $enable_return && 'wc-' . $order->get_status() === $allowed_status && empty($return_rejected_reason)) {
										$display_return_button = true;

										// Retrieve and parse the `order_return_available_time` option
										$return_available_time = get_option('order_return_available_time', '');
										if ($return_available_time) {
											$return_available_time = maybe_unserialize($return_available_time);
										}
										$return_time_value = isset($return_available_time['value']) ? intval($return_available_time['value']) : 0;
										$return_time_unit = isset($return_available_time['unit']) ? $return_available_time['unit'] : 'hours';

										// Convert the specified time duration into seconds
										switch ($return_time_unit) {
											case 'days':
												$time_difference_allowed = $return_time_value * 86400; // 1 day = 86400 seconds
												break;
											case 'weeks':
												$time_difference_allowed = $return_time_value * 604800; // 1 week = 604800 seconds
												break;
											case 'hours':
											default:
												$time_difference_allowed = $return_time_value * 3600; // 1 hour = 3600 seconds
												break;
										}

										// Retrieve the date the order changed to the allowed status
										$order_status_change_date = $order->get_meta('order_status_change_date_' . $order->get_status(), true);
										if ($order_status_change_date) {
											$order_time_local = strtotime($order_status_change_date);
										} else {
											$order_date = $order->get_date_created();
											$order_time_utc = $order_date->getTimestamp();
											$order_time_local = $order_time_utc + (get_option('gmt_offset') * 3600);
										}

										$current_time = current_time('timestamp');
										$time_difference = $current_time - $order_time_local;

										// Only display the return button if within the allowed time or if the time value is 0
										if ($return_time_value !== 0 && $time_difference > $time_difference_allowed) {
											$display_return_button = false;
										}

										if ($display_return_button) {
											echo '<a href="#" class="button return_order_button" data-order-id="' . esc_attr($order->get_id()) . '" onclick="return false;">' . esc_html__('Return', 'wc-order-cancellation-return') . '</a>';
										}
									}

									if ($order->has_status('request-cancel')) {
										echo '<button class="button" disabled style="cursor: not-allowed; opacity: 0.5;">' . esc_html__('Cancel requested', 'wc-order-cancellation-return') . '</button>';
									}

									if ($order->has_status('request-return')) {
										echo '<button class="button" disabled style="cursor: not-allowed; opacity: 0.5;">' . esc_html__('Return requested', 'wc-order-cancellation-return') . '</button>';
									}
								}
								?>
							<?php endif; ?>
						</td>
					<?php endforeach; ?>
				</tr>
			<?php
			}
			?>
		</tbody>
	</table>

	<?php do_action('woocommerce_before_account_orders_pagination'); ?>

	<?php if (1 < $customer_orders->max_num_pages) : ?>
		<div class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination">
			<?php if (1 !== $current_page) : ?>
				<a class="woocommerce-button woocommerce-button--previous woocommerce-Button woocommerce-Button--previous button<?php echo esc_attr($wp_button_class); ?>" href="<?php echo esc_url(wc_get_endpoint_url('orders', $current_page - 1)); ?>"><?php esc_html_e('Previous', 'wc-order-cancellation-return'); ?></a>
			<?php endif; ?>

			<?php if (intval($customer_orders->max_num_pages) !== $current_page) : ?>
				<a class="woocommerce-button woocommerce-button--next woocommerce-Button woocommerce-Button--next button<?php echo esc_attr($wp_button_class); ?>" href="<?php echo esc_url(wc_get_endpoint_url('orders', $current_page + 1)); ?>"><?php esc_html_e('Next', 'wc-order-cancellation-return'); ?></a>
			<?php endif; ?>
		</div>
	<?php endif; ?>

<?php else : ?>

	<?php wc_print_notice(esc_html__('No order has been made yet.', 'wc-order-cancellation-return') . ' <a class="woocommerce-Button wc-forward button' . esc_attr($wp_button_class) . '" href="' . esc_url(apply_filters('woocommerce_return_to_shop_redirect', wc_get_page_permalink('shop'))) . '">' . esc_html__('Browse products', 'wc-order-cancellation-return') . '</a>', 'notice'); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment ?>

<?php endif; ?>

<?php do_action('woocommerce_after_account_orders', $has_orders); ?>
