<?php

defined( 'ABSPATH' ) || exit;

/**
 * A custom Donation Order Email class
 *
 * @extends \WC_Email
 *
 * @since 0.1
 */
class WC_Donation_Order_Email extends WC_Email {

	/**
	 * Set email defaults
	 *
	 * @since 0.1
	 */
	public function __construct() {

		// set ID, this simply needs to be a unique name
		$this->id = 'wc_donation_order';

		// this is the title in WooCommerce Email settings
		$this->title = 'Donation Order';

		// this is the description in WooCommerce email settings
		$this->description = 'Donation Order Notification emails are sent when a customer places an order with 3-day or next day shipping';

		// these are the default heading and subject lines that can be overridden using the settings
		$this->heading = __( 'Thank you for your donation', 'wc-donation-manager' );
		$this->subject = sprintf( __( 'Your %1$s donation has been received!', 'wc-donation-manager' ), get_bloginfo( 'name' ) );

		// these define the locations of the templates that this email should use, we'll just use the new order template since this email is similar
		$this->template_html  = 'emails/admin-new-order.php';
		$this->template_plain = 'emails/plain/admin-new-order.php';

		// Trigger on new paid orders
		add_action( 'woocommerce_order_status_pending_to_processing_notification', array( $this, 'trigger' ) );
		add_action( 'woocommerce_order_status_failed_to_processing_notification', array( $this, 'trigger' ) );

		// Call parent constructor to load any other defaults not explicit defined here
		parent::__construct();

		// this sets the recipient to the settings defined below in init_form_fields()
		$this->recipient = $this->get_option( 'recipient' );

		// if none was entered, just use the WP admin email as a fallback
		if ( ! $this->recipient ) {
			$this->recipient = get_option( 'admin_email' );
		}
	}

	/**
	 * Determine if the email should actually be sent and setup email merge variables.
	 *
	 * @since 0.1
	 * @param int $order_id Order id.
	 * @return void
	 */
	public function trigger( $order_id ) {

		// bail if no order ID is present
		if ( ! $order_id ) {
			return;
		}

		// setup order object
		$this->object = new WC_Order( $order_id );

		// bail if shipping method is not expedited
		// if ( ! in_array( $this->object->get_shipping_method(), array( 'Three Day Shipping', 'Next Day Shipping' ) ) )
		// return;

		// replace variables in the subject/headings
		$this->find[]    = '{order_date}';
		$this->replace[] = date_i18n( wc_date_format(), strtotime( $this->object->order_date ) );

		$this->find[]    = '{order_number}';
		$this->replace[] = $this->object->get_order_number();

		// if ( ! $this->is_enabled() || ! $this->get_recipient() )
		// return;

		// woohoo, send the email!
		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
		// $this->send( $this->get_recipient(), 'The custom subject', 'this is email contents.', $this->get_headers(), $this->get_attachments() );

		var_dump( $order_id );
		wp_die();
	}

	/**
	 * get_content_html function.
	 *
	 * @since 0.1
	 * @return string
	 */
	public function get_content_html() {
		ob_start();
		wc_get_template(
			$this->template_html,
			array(
				'order'         => $this->object,
				'email_heading' => $this->get_heading(),
			)
		);
		return ob_get_clean();
	}


	/**
	 * get_content_plain function.
	 *
	 * @since 0.1
	 * @return string
	 */
	public function get_content_plain() {
		ob_start();
		wc_get_template(
			$this->template_plain,
			array(
				'order'         => $this->object,
				'email_heading' => $this->get_heading(),
			)
		);
		return ob_get_clean();
	}

	/**
	 * Initialize Settings Form Fields
	 *
	 * @since 0.1
	 */
	public function init_form_fields() {

		$this->form_fields = array(
			'enabled'    => array(
				'title'   => 'Enable/Disable',
				'type'    => 'checkbox',
				'label'   => 'Enable this email notification',
				'default' => 'yes',
			),
			'recipient'  => array(
				'title'       => 'Recipient(s)',
				'type'        => 'text',
				'description' => sprintf( 'Enter recipients (comma separated) for this email. Defaults to <code>%s</code>.', esc_attr( get_option( 'admin_email' ) ) ),
				'placeholder' => '',
				'default'     => '',
			),
			'subject'    => array(
				'title'       => 'Subject',
				'type'        => 'text',
				'description' => sprintf( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', $this->subject ),
				'placeholder' => '',
				'default'     => '',
			),
			'heading'    => array(
				'title'       => 'Email Heading',
				'type'        => 'text',
				'description' => sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.' ), $this->heading ),
				'placeholder' => '',
				'default'     => '',
			),
			'email_type' => array(
				'title'       => 'Email type',
				'type'        => 'select',
				'description' => 'Choose which format of email to send.',
				'default'     => 'html',
				'class'       => 'email_type',
				'options'     => array(
					'plain'     => 'Plain text',
					'html'      => 'HTML',
					'woocommerce',
					'multipart' => 'Multipart',
					'woocommerce',
				),
			),
		);
	}
}