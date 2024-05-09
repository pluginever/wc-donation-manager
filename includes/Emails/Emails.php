<?php

namespace PluginEver\WooCommerceDonationManager\Emails;

use WC_Donation_Order_Email;

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
		add_filter( 'woocommerce_email_classes', array( $this, 'add_donation_order_email' ) );
	}

	/**
	 *  Add a donation email to the list of emails WooCommerce should load.
	 *
	 * @param array $email_classes available email classes.
	 *
	 * @since 1.0.0
	 * @return array filtered available email.
	 */
	public function add_donation_order_email( $email_classes ) {
		require_once __DIR__ . '/class-wc-donation-order-email.php';

		$email_classes['WC_Donation_Order_Email'] = new WC_Donation_Order_Email();

		return $email_classes;
	}
}
