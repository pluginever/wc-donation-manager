<?php

namespace WooCommerceDonationManager;

defined( 'ABSPATH' ) || exit;

/**
 * Orders class.
 *
 * Handles order functionality weather admin or customer.
 *
 * @since 1.0.0
 * @package WooCommerceDonationManager
 */
class Orders {
	/**
	 * Orders Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'woocommerce_order_status_completed', array( __CLASS__, 'order_status_completed' ), 20, 2 );
		// TODO: This bellow action added only for testing the above action.
		add_action( 'woocommerce_thankyou', array( $this, 'auto_complete_paid_order_thankyou' ), 20, 1 );
	}

	/**
	 * Forced updating order status as completed.
	 *
	 * @param int $order_id Order ID.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function auto_complete_paid_order_thankyou( $order_id ) {
		if ( ! $order_id ) {
			return;
		}
		// Get an instance of the WC_Product object.
		$order = wc_get_order( $order_id );
		$order->update_status( 'completed' );
	}

	/**
	 * Updating raised amount on order status changed to completed.
	 *
	 * This will increase the raised amount once even if admin changed the order status multiple times.
	 *
	 * @param int       $order_id Order ID.
	 * @param \WC_Order $order Order object.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function order_status_completed( $order_id, $order ) {
		foreach ( $order->get_items() as $item_id => $item ) {
			$product = $item->get_product();

			if ( $product->is_type( 'donation' ) ) {
				$orders_id = get_post_meta( $item['product_id'], 'wcdm_orders_id', true );

				if ( ! is_array( $orders_id ) ) {
					$orders_id = array( get_post_meta( $item['product_id'], 'wcdm_orders_id', true ) );
				}

				if ( ! in_array( $order_id, $orders_id, true ) ) {
					$orders_id[]    = $order_id;
					$raised_amount  = (float) get_post_meta( $item['product_id'], 'wcdm_raised_amount', true );
					$raised_amount += (float) $item['subtotal'];

					update_post_meta( $item['product_id'], 'wcdm_orders_id', $orders_id );
					update_post_meta( $item['product_id'], 'wcdm_raised_amount', $raised_amount );

					// TODO: Need to create "Donor" from here by getting help of Donor model.
				}
			}
		}
	}
}
