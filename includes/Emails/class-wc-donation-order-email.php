<?php
/**
 * Class WC_Donation_Order_Email file.
 *
 * @package WooCommerceDonationManager/Emails
 */

defined( 'ABSPATH' ) || exit;

/**
 * A Donation Order Email class
 *
 * @extends \WC_Email
 *
 * @since 0.1
 */
class WC_Donation_Order_Email extends \WC_Email {

	/**
	 * Donation amount.
	 *
	 * @var int
	 */
	protected $donation_amount;

	/**
	 * Set email defaults
	 *
	 * @since 0.1
	 */
	public function __construct() {
		$this->id             = 'customer_completed_donation';
		$this->customer_email = true;
		$this->title          = __( 'Completed donation', 'wc-donation-manager' );
		$this->description    = __( 'Donation order completed notification emails are sent when a customer places an donation order.', 'wc-donation-manager' );
		$this->template_html  = 'emails/customer-completed-donation.php';
		$this->template_plain = 'emails/plain/customer-completed-donation.php';
		$this->template_base  = WCDM_PATH . 'templates/';
		$this->placeholders   = array(
			'{order_date}'   => '',
			'{order_number}' => '',
		);

		// Trigger on order status completed.
		add_action( 'woocommerce_order_status_completed_notification', array( $this, 'trigger' ), 10, 2 );

		// Call parent constructor.
		parent::__construct();
	}

	/**
	 * Trigger the sending of this email.
	 *
	 * @param int            $order_id The order ID.
	 * @param WC_Order|false $order Order object.
	 *
	 * @since 0.1
	 * @return void
	 */
	public function trigger( $order_id, $order = false ) {
		$this->setup_locale();

		if ( $order_id && ! is_a( $order, 'WC_Order' ) ) {
			$order = wc_get_order( $order_id );
		}

		if ( is_a( $order, 'WC_Order' ) ) {
			$this->object                         = $order;
			$this->recipient                      = $this->object->get_billing_email();
			$this->placeholders['{order_date}']   = wc_format_datetime( $this->object->get_date_created() );
			$this->placeholders['{order_number}'] = $this->object->get_order_number();

			foreach ( $order->get_items() as $item ) {
				$product = $item->get_product();
				if ( $product->is_type( 'donation' ) ) {
					$this->donation_amount += (float) $item['subtotal'];
				}
			}
		}

		if ( $this->is_enabled() && $this->get_recipient() ) {
			$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
		}

		$this->restore_locale();
	}

	/**
	 * Get content html.
	 *
	 * @return string
	 */
	public function get_content_html() {
		return wc_get_template_html(
			$this->template_html,
			array(
				'order'              => $this->object,
				'email_heading'      => $this->get_heading(),
				'additional_content' => $this->get_additional_content(),
				'sent_to_admin'      => false,
				'plain_text'         => false,
				'email'              => $this,
				'donation_amount'    => $this->donation_amount,
			),
			'',
			$this->template_base,
		);
	}

	/**
	 * Get content plain.
	 *
	 * @return string
	 */
	public function get_content_plain() {
		return wc_get_template_html(
			$this->template_plain,
			array(
				'order'              => $this->object,
				'email_heading'      => $this->get_heading(),
				'additional_content' => $this->get_additional_content(),
				'sent_to_admin'      => false,
				'plain_text'         => true,
				'email'              => $this,
				'donation_amount'    => $this->donation_amount,
			),
			'',
			$this->template_base,
		);
	}

	/**
	 * Return content from the additional_content field.
	 *
	 * Displayed above the footer.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_additional_content() {
		/**
		 * Provides an opportunity to inspect and modify additional content for the email.
		 *
		 * @since 3.7.0
		 *
		 * @param string      $additional_content Additional content to be added to the email.
		 * @param object|bool $object             The object (ie, product or order) this email relates to, if any.
		 * @param WC_Email    $email              WC_Email instance managing the email.
		 */
		return apply_filters( 'wcdm_email_additional_content_' . $this->id, $this->format_string( $this->get_option( 'additional_content', $this->get_default_additional_content() ) ), $this->object, $this );
	}

	/**
	 * Initialize Settings Form Fields.
	 *
	 * @since 0.1
	 * @return void
	 */
	public function init_form_fields() {

		$this->form_fields = array(
			'enabled'            => array(
				'title'   => 'Enable/Disable',
				'type'    => 'checkbox',
				'label'   => 'Enable this email notification',
				'default' => 'yes',
			),
			'subject'            => array(
				'title'       => 'Subject',
				'type'        => 'text',
				'description' => sprintf( /* translators: 1: Email subject */ __( 'This controls the email subject line. Leave blank to use the default subject: %s.', 'wc-donation-manager' ), $this->get_default_subject() ),
				'desc_tip'    => true,
				'placeholder' => $this->get_default_subject(),
				'default'     => '',
			),
			'heading'            => array(
				'title'       => 'Email Heading',
				'type'        => 'text',
				'description' => sprintf( /* translators: 1: Email heading */ __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: %s.', 'wc-donation-manager' ), $this->get_default_heading() ),
				'desc_tip'    => true,
				'placeholder' => $this->get_default_heading(),
				'default'     => '',
			),
			'additional_content' => array(
				'title'       => __( 'Additional content', 'wc-donation-manager' ),
				'description' => __( 'Text to appear below the main email content. Available placeholders: {site_title}, {site_address}, {site_url}, {order_date}, {order_number}', 'wc-donation-manager' ),
				'desc_tip'    => true,
				'type'        => 'textarea',
				'css'         => 'width:400px;',
				'placeholder' => __( 'N/A', 'wc-donation-manager' ),
				'default'     => $this->get_default_additional_content(),
			),
			'email_type'         => array(
				'title'       => __( 'Email type', 'wc-donation-manager' ),
				'type'        => 'select',
				'description' => __( 'Choose which format of email to send.', 'wc-donation-manager' ),
				'desc_tip'    => true,
				'default'     => 'html',
				'class'       => 'email_type wc-enhanced-select',
				'options'     => $this->get_email_type_options(),
			),
		);
	}

	/**
	 * Get email subject.
	 *
	 * @since  1.0.0
	 * @return string
	 */
	public function get_default_subject() {
		return __( 'Your {site_title} donation has been received!', 'wc-donation-manager' );
	}

	/**
	 * Get email heading.
	 *
	 * @since  1.0.0
	 * @return string
	 */
	public function get_default_heading() {
		return __( 'Thank you for your donation', 'wc-donation-manager' );
	}

	/**
	 * Default email additional content
	 *
	 * @return string
	 */
	public function get_default_additional_content() {
		return __( 'Just to let you know — we\'ve received your donation order #{order_number}, and it is now being processed:', 'wc-donation-manager' );
	}
}
