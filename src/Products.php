<?php

namespace WooCommerceDonationManager;

use WC_Product_Donation;

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
		add_action('woocommerce_donation_add_to_cart', array( __CLASS__, 'add_to_cart_template' ) );

		// TODO: Redirect specific product type isn't completed. It's works for all products
		add_filter('woocommerce_add_to_cart_redirect', array( __CLASS__, 'add_to_cart_redirect' ), 10, 2 );

		add_filter('woocommerce_add_cart_item', array( __CLASS__, 'add_cart_item' ) );
		add_filter('woocommerce_get_cart_item_from_session', array( __CLASS__, 'get_cart_item_from_session' ) );
		add_filter('woocommerce_cart_item_price', array( __CLASS__, 'cart_item_price'), 10, 3 );
		add_filter('woocommerce_update_cart_action_cart_updated', array( __CLASS__, 'update_cart' ) );
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
			echo( '<div class="wc-donation-amount">
				<label for="donation_amount">'. esc_html__('Amount', 'wc-donation-manager' ) . ':</label>
				<input type="number" name="donation_amount" id="donation_amount" size="5" min="0" step="'. $product->get_amount_increment_steps().'" value="'. number_format( $product->get_price(), 2, '.', '' ) .'" class="input-text text" />
			</div>' );
		}
	}

	// Use the Simple product type's add to cart button for Donation products
	public static function add_to_cart_template() {
		do_action('woocommerce_simple_add_to_cart' );
	}

	// TODO: Redirect specific product type isn't completed. It's works for all products
	// Add to cart redirect to cart or checkout page
	public static function add_to_cart_redirect( $url ) {

		$product = new WC_Product_Donation();

		if ( 'donation' === $product->get_type() ) {
			$direct_checkout = 'yes';

			if ( 'yes' === $direct_checkout ) {
				return wc_get_checkout_url();
			}

			return wc_get_cart_url();
		}

		return $url;
	}

	// Process donation amount when a Donation product is added to the cart
	public static function add_cart_item( $item ) {
		if ($item['data']->get_type() == 'donation') {
			if ( isset( $_POST['donation_amount'] ) && is_numeric( $_POST['donation_amount'] ) && $_POST['donation_amount'] >= 0)
				$item['donation_amount'] = $_POST['donation_amount']*1;
			$item['data']->set_price( $item['donation_amount'] );
		}
		return $item;
	}

	// Set Donation product price when loading the cart
	public static function get_cart_item_from_session($session_data) {
		if ($session_data['data']->get_type() == 'donation' && isset($session_data['donation_amount']))
			$session_data['data']->set_price($session_data['donation_amount']);
		return $session_data;
	}

	// Add the donation amount field to the cart display
	public static function cart_item_price( $price, $cart_item, $cart_item_key) {

//		return ( ( $cart_item['data']->get_type() == 'donation' && !get_option('disable_cart_amount_field' ) ) ?
		return ( ( $cart_item['data']->get_type() == 'donation' ) ?
			'<input type="number" name="donation_amount_'.$cart_item_key.'" size="5" min="0" step="'. $cart_item['data']->get_amount_increment_steps() . '" value="'.$cart_item['data']->get_price().'" />' :
			$price );
	}

	// Process donation amount fields in cart updates
	public static function update_cart( $cart_updated ) {
//		if ( get_option('disable_cart_amount_field' ) ) {
//			return $cart_updated;
//		}
		global $woocommerce;
		foreach ($woocommerce->cart->get_cart() as $key => $cartItem) {
			if ($cartItem['data']->get_type() == 'donation' && isset($_POST['donation_amount_'.$key])
			    && is_numeric($_POST['donation_amount_'.$key]) && $_POST['donation_amount_'.$key] >= 0 && $_POST['donation_amount_'.$key] != $cartItem['data']->get_price()) {
				$cartItem['donation_amount'] = $_POST['donation_amount_'.$key]*1;
				$cartItem['data']->set_price($cartItem['donation_amount']);
				$woocommerce->cart->cart_contents[$key] = $cartItem;
				$cart_updated = true;
			}
		}
		return $cart_updated;
	}
}
