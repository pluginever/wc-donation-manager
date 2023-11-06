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
