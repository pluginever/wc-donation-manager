<?php

namespace WooCommerceDonationManager\Frontend;

use WooCommerceDonationManager\Plugin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Frontend.
 *
 * This class is responsible for all frontend functionality.
 *
 * @since   1.0.0
 * @package WooCommerceDonationManager\Frontend
 */
class Frontend {
	/**
	 * Plugin instance.
	 *
	 * @var Plugin
	 */
	protected Plugin $plugin;

	/**
	 * Frontend Class constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Plugin $plugin Plugin instance.
	 */
	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
		add_action( 'woocommerce_before_cart', array( __CLASS__, 'remove_coupon' ) );
		add_action( 'woocommerce_before_checkout_form', array( __CLASS__, 'remove_coupon' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Removing coupon fields from cart and checkout pages.
	 *
	 * This will remove coupon if the cart items has donation product
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function remove_coupon(): void {
		if ( ! WC()->cart->is_empty() && 'yes' === get_option( 'wcdm_disabled_coupon_field', 'yes' ) ) {
			foreach ( WC()->cart->get_cart() as $cart_item ) {
				if ( 'donation' === $cart_item['data']->get_type() ) {
					add_filter( 'woocommerce_coupons_enabled', '__return_false' );
					break;
				}
			}
		}
	}

	/**
	 * Enqueue frontend scripts.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function enqueue_scripts(): void {
		$this->plugin->scripts->enqueue_style( 'wcdm-frontend', 'css/frontend.css' );
		$this->plugin->scripts->enqueue_script( 'wcdm-frontend', 'js/frontend.js', array( 'jquery' ) );
	}
}
