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
		add_filter( 'woocommerce_get_cart_item_from_session', array( __CLASS__, 'get_cart_item_from_session' ) );
		add_filter( 'woocommerce_cart_item_price', array( __CLASS__, 'cart_item_price'), 10, 3 );
		add_filter( 'woocommerce_update_cart_action_cart_updated', array( __CLASS__, 'update_cart' ) );
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

	// Set Donation product price when loading the cart
	public static function get_cart_item_from_session( $session_data ) {
		if ($session_data['data']->get_type() == 'donation' && isset($session_data['donation_amount']))
			$session_data['data']->set_price( $session_data['donation_amount']);
		return $session_data;
	}

	// Add the donation amount field to the cart display
	public static function cart_item_price( $price, $cart_item, $cart_item_key) {
		if ( $cart_item['data']->get_type() == 'donation' && 'yes' === get_option( 'wcdm_editable_cart_price', 'yes') ) {
			return '<label for="donation_amount">' . get_woocommerce_currency_symbol() .'</label><input type="number" name="donation_amount_'. $cart_item_key .'" id="donation_amount" min="' . get_post_meta( $cart_item['product_id'], 'wcdm_min_amount', true ) . '" max="'. get_post_meta( $cart_item['product_id'], 'wcdm_max_amount', true ) .'" step="'. get_post_meta( $cart_item['product_id'], 'wcdm_amount_increment_steps', true ) .'" value="'. number_format( $cart_item['data']->get_price(), 2, '.', '' ) .'" class="input-text text" />';
		}
		return $price;
	}

	// Process donation amount fields in cart updates
	public static function update_cart( $cart_updated ) {
		if ( 'yes' !== get_option( 'wcdm_editable_cart_price', 'yes' ) ) {
			return $cart_updated;
		}
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
