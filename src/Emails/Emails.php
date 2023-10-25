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
		add_action( 'woocommerce_order_status_completed', array( __CLASS__, 'send_email' ), 20, 2 );
//		add_action( 'woocommerce_before_email_order', array( __CLASS__, 'add_order_instruction_email' ), 10, 2 );
//		add_filter( 'woocommerce_email_heading_customer_processing_order', array( __CLASS__, 'send_donation_email'), 10, 5 );
//		add_action( 'woocommerce_email_before_order_table', array( __CLASS__, 'add_content_before_order_table' ), 20, 4 );
	}

	/**
	 * Sending custom and formatted email on change the order status as completed.
	 *
	 * @param int       $order_id Order ID.
	 * @param \WC_Order $order Order object.
	 *
	 * @version 1.0.0
	 * @return void
	 */
	public static function send_email( $order_id, $order ) {



		$heading = 'Heading: Order Completed';

		$subject = 'Subject: Order Completed';

		// Get WooCommerce email objects
		$mailer = WC()->mailer()->get_emails();

		var_dump($mailer);
		wp_die();

		// Possible mailer objects
		// WC_Email_Customer_New_Account
		// WC_Email_Customer_Reset_Password
		// WC_Email_Customer_Note
		// WC_Email_Customer_Invoice
		// WC_Email_Customer_Refunded_Order
		// WC_Email_Customer_Completed_Order
		// WC_Email_Customer_Processing_Order
		// WC_Email_Customer_On_Hold_Order
		// WC_Email_Failed_Order
		// WC_Email_Cancelled_Order
		// WC_Email_New_Order


		// Use one of the active emails e.g. "Customer_Completed_Order"
		// Won't work if you choose an object that is not active
		// Assign heading & subject to chosen object
		$mailer['WC_Email_Customer_Completed_Order']->heading = $heading;
		$mailer['WC_Email_Customer_Completed_Order']->subject = $subject;

		// Send the email with custom heading & subject
		$mailer['WC_Email_Customer_Completed_Order']->trigger( $order_id );

		// You have to use the email ID chosen above and also that $order->get_status() == "completed"
	}

	public static function add_order_instruction_email( $order, $sent_to_admin ) {

		if ( ! $sent_to_admin ) {

			if ( 'cod' == $order->payment_method ) {
				// cash on delivery method
				echo '<p><strong>Instructions:</strong> Full payment is due immediately upon delivery: <em>cash only, no exceptions</em>.</p>';
			} else {
				// other methods (ie credit card)
				echo '<p><strong>Instructions:</strong> Please look for "Madrigal Electromotive GmbH" on your next credit card statement.</p>';
			}
		}
	}

	public static function send_donation_email( $email_heading, $order ) {
		global $woocommerce;
		$items = $order->get_items();
		foreach ( $items as $item ) {
			$product_id = $item['product_id'];
			if ( $product_id == 61 ) {
				$email_heading = 'WooCommerce email notification OR how to test WooCommerce emails';
			}
			return $email_heading;
		}
	}

	/**
	 * Add custom text to the Customer Processing Order Email.
	 *
	 * @param \WC_Order $order Order object.
	 * @param bool      $sent_to_admin Send email to admin.
	 * @param string    $plain_text Email plain text.
	 * @param \WC_Email $email Email object.
	 *
	 * @version 1.0.0
	 * @return void
	 */
	public static function add_content_before_order_table( $order, $sent_to_admin, $plain_text, $email ) {

		// Possible conditions for sending the emails.
		// if ( $email->id == 'cancelled_order' ) {}
		// if ( $email->id == 'customer_completed_order' ) {}
		// if ( $email->id == 'customer_invoice' ) {}
		// if ( $email->id == 'customer_new_account' ) {}
		// if ( $email->id == 'customer_note' ) {}
		// if ( $email->id == 'customer_on_hold_order' ) {}
		// if ( $email->id == 'customer_refunded_order' ) {}
		// if ( $email->id == 'customer_reset_password' ) {}
		// if ( $email->id == 'failed_order' ) {}
		// if ( $email->id == 'new_order' ) {}

		if ( $email->id == 'customer_processing_order' ) {
			echo '<h2 class="email-upsell-title">Get 30% off</h2><p class="email-upsell-p">Thank you for making this purchase! Come back and use the code "<strong>SAVE30</strong>" to receive a 30% discount on your next purchase! Click here to continue shopping.</p>';
		}
	}
}
