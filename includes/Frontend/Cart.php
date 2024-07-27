<?php

namespace WooCommerceDonationManager\Frontend;

defined( 'ABSPATH' ) || exit;

/**
 * Cart class.
 *
 * Handles cart functionality.
 *
 * @since 1.0.0
 * @package WooCommerceDonationManager\Frontend
 */
class Cart {

	/**
	 * Cart Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'woocommerce_get_cart_item_from_session', array( __CLASS__, 'get_cart_item_from_session' ) );
		add_filter( 'woocommerce_cart_item_price', array( __CLASS__, 'cart_item_price' ), 10, 3 );
		add_filter( 'woocommerce_update_cart_action_cart_updated', array( __CLASS__, 'update_cart' ) );
	}

	/**
	 * Set donation product price when loading the cart.
	 *
	 * @param array $session_data Session cart item data.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public static function get_cart_item_from_session( $session_data ) {
		if ( $session_data['data']->get_type() === 'donation' && isset( $session_data['donation_amount'] ) ) {
			$session_data['data']->set_price( $session_data['donation_amount'] );
		}

		return $session_data;
	}

	/**
	 * Add the donation amount field to the cart item.
	 *
	 * @param string $price item price html.
	 * @param array  $cart_item Cart item.
	 * @param string $cart_item_key Cart item key.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public static function cart_item_price( $price, $cart_item, $cart_item_key ) {
		if ( ! is_cart() ) {
			return $price;
		}

		if ( 'donation' === $cart_item['data']->get_type() && 'yes' === get_option( 'wcdm_editable_cart_price', 'yes' ) ) {
			return '<div class="donation-product-price"><label class="input-text" for="donation_amount">' . esc_html( get_woocommerce_currency_symbol() ) . '</label><input type="number" name="donation_amount_' . sanitize_key( $cart_item_key ) . '" id="donation_amount" min="' . esc_attr( get_post_meta( $cart_item['product_id'], '_wcdm_min_amount', true ) ) . '" max="' . esc_attr( get_post_meta( $cart_item['product_id'], '_wcdm_max_amount', true ) ) . '" step="' . floatval( get_post_meta( $cart_item['product_id'], '_amount_increment_steps', true ) ) . '" value="' . esc_attr( number_format( floatval( $cart_item['data']->get_price() ), 2, '.', '' ) ) . '" class="input-text text" /></div>';
		}

		return $price;
	}

	/**
	 * Process donation amount fields in cart updates.
	 *
	 * @param bool $cart_updated weather true of false.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public static function update_cart( $cart_updated ) {
		wp_verify_nonce( '_wpnonce' );

		if ( 'yes' !== get_option( 'wcdm_editable_cart_price', 'yes' ) ) {
			return $cart_updated;
		}

		global $woocommerce;
		foreach ( $woocommerce->cart->get_cart() as $key => $cart_item ) {
			if ( $cart_item['data']->get_type() === 'donation' && isset( $_POST[ 'donation_amount_' . $key ] ) && is_numeric( $_POST[ 'donation_amount_' . $key ] ) && $_POST[ 'donation_amount_' . $key ] >= 0 && $_POST[ 'donation_amount_' . $key ] !== $cart_item['data']->get_price() ) {
				$cart_item['donation_amount'] = floatval( wp_unslash( $_POST[ 'donation_amount_' . $key ] ) );
				$cart_item['data']->set_price( $cart_item['donation_amount'] );
				$woocommerce->cart->cart_contents[ $key ] = $cart_item;
				$cart_updated                             = true;
			}
		}

		return $cart_updated;
	}
}
