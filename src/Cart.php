<?php

namespace WooCommerceDonationManager;

defined( 'ABSPATH' ) || exit;

/**
 * Cart class.
 *
 * Handles cart functionality.
 *
 * @since 1.0.0
 * @package WooCommerceDonationManager
 */
class Cart {
	/**
	 * Cart Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'woocommerce_get_item_data', array( __CLASS__, 'get_item_data' ), 10, 2 );
	}

	/**
	 * Get item data.
	 *
	 * This information shows up in the cart and checkout page
	 * under the product name.
	 * key: This is used internally by WooCommerce for html classes.
	 * value: This is the value that is saved to the order item meta.
	 * display: This is the value that is displayed to the user.
	 *
	 * @since 1.0.0
	 *
	 * @param array $item_data Item data.
	 * @param array $cart_item Cart item.
	 * @return array
	 */
	public static function get_item_data( $item_data, $cart_item ) {
		// Add example item data.
		$item_data[] = array(
			'key'     => __( 'This is the key', 'wc-donation-manager' ),
			'value'   => __( 'This is the value', 'wc-donation-manager' ),
			'display' => __( 'This is the display value', 'wc-donation-manager' ),
		);
		return $item_data;
	}
}
