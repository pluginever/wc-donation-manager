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
		add_filter( 'woocommerce_loop_add_to_cart_link', array( __CLASS__, 'add_to_cart_link' ), 10, 2 );
		add_filter( 'woocommerce_get_price_html', array( __CLASS__, 'get_price_html' ), 10, 2 );
		add_action('woocommerce_before_add_to_cart_button', array( __CLASS__, 'before_add_to_cart_button' ) );
	}


	//Remove ajax_add_to_cart from add to cart button
	public static function add_to_cart_link( $linkHtml, $product ) {
//		var_dump( $product->get_type() );
		return ( 'donation' == $product->get_type() ? str_replace( 'ajax_add_to_cart', '', $linkHtml ) : $linkHtml );
	}

	//	 Disable price display in frontend for Donation products
	public static function get_price_html( $price, $product ) {

		if ( 'donation' == $product->get_type() ) {
			return ( is_admin() ? 'Variable' : '' );
		} else {
			return $price;
		}
	}


	// Add amount field before add to cart button
	public static function before_add_to_cart_button() {
		global $product;
		if ( 'donation' == $product->get_type() ) {
			echo('<div class="wc-donation-amount">
				<label for="donation_amount_field">'.esc_html__('Amount', 'donations-for-woocommerce').':</label>
				<input type="number" name="donation_amount" id="donation_amount_field" size="5" min="0" step="'.$product->get_donation_amount_increment().'" value="'.number_format($product->get_price(), 2, '.', '').'" class="input-text text" />
			</div>');
		}
	}
}
