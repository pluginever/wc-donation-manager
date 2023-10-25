<?php

namespace WooCommerceDonationManager\Emails;

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
		add_filter( 'woocommerce_email_classes', array( $this, 'add_donation_order_email') );
		add_filter( 'woocommerce_get_order_item_totals', array( $this, 'order_item_totals'), 10, 3 );
	}

	/**
	 *  Add a custom email to the list of emails WooCommerce should load.
	 *
	 * @param array $email_classes available email classes.
	 *
	 * @since 1.0.0
	 * @return array filtered available email.
	 */
	public function add_donation_order_email( $email_classes ) {
		require_once __DIR__ . '/../class-wc-donation-order-email.php';
		$email_classes['WC_Donation_Order_Email'] = new WC_Donation_Order_Email();

		return $email_classes;
	}

	/**
	 *  Add a custom email to the list of emails WooCommerce should load.
	 *
	 * @param array $email_classes available email classes.
	 * @return array filtered available email.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function order_item_totals( $total_rows, $order, $tax_display ){
		$donation_total = 0;
		foreach ( $order->get_items() as $item ) {
			$product = $item->get_product();
			if ( $product->is_type( 'donation' ) ) {
				$donation_total += $item->get_total();
			}
		}

		$total_rows['cart_subtotal']['value'] = floatval( $order->get_subtotal() - $donation_total );
		$total_rows['order_total']['value'] = floatval( $order->get_total() - $donation_total );

		return $total_rows;
	}
}
