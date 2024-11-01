<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class YOOCR_WC_Order_Cancellation_Return_Order_Cancel_Request_Email
 */
class YOOCR_WC_Order_Cancellation_Return_Order_Cancel_Request_Email extends WC_Email {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id             = 'order_cancel_request';
		$this->title          = __( 'Order cancellation request', 'wc-order-cancellation-return' );
		$this->description    = __( 'Notification emails sent to admin when a cancellation is requested.', 'wc-order-cancellation-return' );

		$this->heading        = __( 'New cancellation request', 'wc-order-cancellation-return' );
		
		// translators: %s is the order number
		$this->subject        = sprintf( __( 'Cancellation requested for order #%s', 'wc-order-cancellation-return' ), '{order_number}' );

		$this->template_base  = plugin_dir_path( __FILE__ ) . '../../woocommerce/';
		$this->template_html  = 'emails/admin-order-cancel-request.php';
		$this->template_plain = 'emails/plain/admin-order-cancel-request.php';

		// Initialize email settings
		$this->recipient = $this->get_option( 'recipient', get_option( 'admin_email' ) );

		// Call parent constructor to load any other defaults not explicity defined here
		parent::__construct();   

		// This must be last, to give other properties like `template_base` a chance to get set by subclassing constructors.
		$this->init_form_fields();
	}

	/**
	 * Initialize settings form fields.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title'   => __( 'Enable/Disable', 'wc-order-cancellation-return' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable this email notification', 'wc-order-cancellation-return' ),
				'default' => 'yes',
			),
			'recipient' => array(
				'title'       => __( 'Recipient(s)', 'wc-order-cancellation-return' ),
				'type'        => 'text',
				'description' => __( 'Enter recipients (comma separated) for this email.', 'wc-order-cancellation-return' ),
				'default'     => get_option( 'admin_email' ),
				'desc_tip'    => true,
			),
			'subject' => array(
				'title'       => __( 'Subject', 'wc-order-cancellation-return' ),
				'type'        => 'text',
				'desc_tip'    => true,
				'description' => __( 'This controls the email subject line.', 'wc-order-cancellation-return' ),
				'placeholder' => $this->get_default_subject(),
				'default'     => '',
			),
			'heading' => array(
				'title'       => __( 'Email heading', 'wc-order-cancellation-return' ),
				'type'        => 'text',
				'desc_tip'    => true,
				'description' => __( 'This controls the main heading contained within the email notification.', 'wc-order-cancellation-return' ),
				'placeholder' => $this->get_default_heading(),
				'default'     => '',
			),
			'additional_content' => array(
				'title'       => __( 'Additional content', 'wc-order-cancellation-return' ),
				'type'        => 'textarea',
				'description' => __( 'This content will be inserted after the main email content.', 'wc-order-cancellation-return' ),
				'desc_tip'    => true,
				'default'     => '',
			),
			'email_type' => array(
				'title'       => __( 'Email type', 'wc-order-cancellation-return' ),
				'type'        => 'select',
				'description' => __( 'Choose which format of email to send.', 'wc-order-cancellation-return' ),
				'default'     => 'html',
				'class'       => 'email_type wc-enhanced-select',
				'options'     => $this->get_email_type_options(),
				'desc_tip'    => true,
			),
		);
	}

	/**
	 * Trigger the sending of this email.
	 */
	public function trigger($order_id, $attachments = []) {
		$this->setup_locale();

		if ($order_id) {
			$this->object = wc_get_order($order_id);
			$this->recipient = $this->get_recipient();

			$this->subject = str_replace('{order_number}', $this->object->get_order_number(), $this->subject);

			if ($this->is_enabled() && $this->recipient) {
				$sent = $this->send($this->recipient, $this->get_subject(), $this->get_content(), $this->get_headers(), $attachments);
				if ($sent) {
					$this->delete_attachments($attachments);
				}
			}
		}

		$this->restore_locale();
	}

	private function delete_attachments($attachments) {
		if (empty($attachments)) {
			return;
		}
	
		if (!is_array($attachments)) {
			$attachments = [$attachments]; // Ensure attachments is an array
		}
	
		foreach ($attachments as $file_path) {
			if (file_exists($file_path)) {
				unlink($file_path);
			}
		}
	}

	/**
	 * Get content html.
	 */
	public function get_content_html() {
		ob_start();
		wc_get_template( $this->template_html, array(
			'order'              => $this->object,
			'email_heading'      => $this->get_heading(),
			'additional_content' => $this->get_option( 'additional_content', '' ),
			'sent_to_admin'      => true,
			'plain_text'         => false,
			'email'              => $this,
		), '', $this->template_base );
		return ob_get_clean();
	}

	/**
	 * Get content plain.
	 */
	public function get_content_plain() {
		ob_start();
		wc_get_template( $this->template_plain, array(
			'order'              => $this->object,
			'email_heading'      => $this->get_heading(),
			'additional_content' => $this->get_option( 'additional_content', '' ),
			'sent_to_admin'      => true,
			'plain_text'         => true,
			'email'              => $this,
		), '', $this->template_base );
		return ob_get_clean();
	}
}
