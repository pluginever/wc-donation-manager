<?php

namespace WooCommerceDonationManager\Emails;

defined( 'ABSPATH' ) || exit;

/**
 * Class Emails.
 *
 * Handles emails.
 *
 * @since 1.0.0
 * @package WooCommerceDonationManager\Emails
 */
class Emails {
	/**
	 * Emails constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'woocommerce_order_status_completed', array( __CLASS__, 'send_email_on_change_order_status' ), 20, 2 );
		add_action( 'woocommerce_email_before_order_table', array( __CLASS__, 'add_content_specific_email' ), 20, 4 );
	}

	/**
	 * Sending custom and formatted email on change the order status.
	 *
	 * @param int $order_id Order ID.
	 * @param \WC_Order $order Order object
	 *
	 * @version 1.0.0
	 * @return void
	 */
	public static function send_email_on_change_order_status( $order_id, $order ) {

		$heading = $subject = 'Order Refused';

		// Get WooCommerce email objects
		$mailer = WC()->mailer()->get_emails();

		// Use one of the active emails e.g. "Customer_Completed_Order"
		// Won't work if you choose an object that is not active
		// Assign heading & subject to chosen object
		$mailer['WC_Email_Customer_Completed_Order']->heading = $heading;
		$mailer['WC_Email_Customer_Completed_Order']->subject = $subject;

		// Send the email with custom heading & subject
		$mailer['WC_Email_Customer_Completed_Order']->trigger( $order_id );

		// You have to use the email ID chosen above and also that $order->get_status() == "refused"

	}

	/**
	 * @snippet       Add Text to Customer Processing Order Email
	 * @how-to        Get CustomizeWoo.com FREE
	 * @author        Rodolfo Melogli
	 * @testedwith    Woo 4.6
	 * @donate $9     https://businessbloomer.com/bloomer-armada/
	 */

	/**
	 * Add custom text to the Customer Processing Order Email
	 *
	 * @param \WC_Order $order Order object
	 * @param bool $sent_to_admin Send email to admin.
	 * @param string $plain_text Email plain text.
	 * @param \WC_Email $email Email object.
	 *
	 * @version 1.0.0
	 * @return void
	 */
	public static function add_content_specific_email( $order, $sent_to_admin, $plain_text, $email ) {

//      Possible conditions for sending the emails.
//		if ( $email->id == 'cancelled_order' ) {}
//		if ( $email->id == 'customer_completed_order' ) {}
//		if ( $email->id == 'customer_invoice' ) {}
//		if ( $email->id == 'customer_new_account' ) {}
//		if ( $email->id == 'customer_note' ) {}
//		if ( $email->id == 'customer_on_hold_order' ) {}
//		if ( $email->id == 'customer_refunded_order' ) {}
//		if ( $email->id == 'customer_reset_password' ) {}
//		if ( $email->id == 'failed_order' ) {}
//		if ( $email->id == 'new_order' ) {}

		if ( $email->id == 'customer_processing_order' ) {
			echo '<h2 class="email-upsell-title">Get 30% off</h2><p class="email-upsell-p">Thank you for making this purchase! Come back and use the code "<strong>SAVE30</strong>" to receive a 30% discount on your next purchase! Click here to continue shopping.</p>';
		}
	}
}
