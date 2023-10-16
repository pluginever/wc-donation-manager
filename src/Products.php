<?php

namespace WooCommerceDonationManager;

defined( 'ABSPATH' ) || exit;

/**
 * Class Products.
 *
 * @package WooCommerceDonationManager
 * @since 1.0.0
 */
class Products {
	/**
	 * Products constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// Disable AJAX add to cart for Donation products
		add_filter('woocommerce_loop_add_to_cart_link', array( __CLASS__,'add_to_cart_link' ), 10, 2);
	}

	public static function add_to_cart_link( $linkHtml, $product) {
//		var_dump( $product->get_type() );
		return ( $product->get_type() == 'donation' ? str_replace('ajax_add_to_cart', '', $linkHtml ) : $linkHtml );
	}
}
