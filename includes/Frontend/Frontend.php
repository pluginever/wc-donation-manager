<?php

namespace PluginEver\DonationManager\Frontend;

use PluginEver\DonationManager\B8\Component;

defined( 'ABSPATH' ) || exit;

/**
 * Class Frontend.
 *
 * This class is responsible for all frontend functionality.
 *
 * @since   1.0.0
 * @package PluginEver\DonationManager\Frontend
 *
 * @extends Component<\PluginEver\DonationManager\Plugin>
 */
class Frontend extends Component {

	/**
	 * Frontend constructor.
	 *
	 * @since 1.0.0
	 */
	public function register(): void {
		// Early, so every consumer of `wc_coupons_enabled()` sees the flag:
		// the cart/checkout shortcodes, and the cart/checkout blocks, which
		// snapshot the value into their `couponsEnabled` JS setting.
		add_action( 'wp_loaded', array( __CLASS__, 'conditionally_disable_coupons' ), 20 );
		add_action( 'woocommerce_before_cart', array( __CLASS__, 'conditionally_disable_coupons' ) );
		add_action( 'woocommerce_before_checkout_form', array( __CLASS__, 'conditionally_disable_coupons' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Disable coupons while the cart holds a donation product.
	 *
	 * Hides the coupon field on the cart and checkout pages — shortcodes
	 * and blocks — and the Store API refuses coupon application too,
	 * since its cart routes check `wc_coupons_enabled()`.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function conditionally_disable_coupons(): void {
		if ( is_admin() && ! wp_doing_ajax() ) {
			return;
		}

		if ( ! ( WC()->cart instanceof \WC_Cart ) || WC()->cart->is_empty() || 'yes' !== get_option( 'wcdm_disabled_coupon_field', 'yes' ) ) {
			return;
		}

		foreach ( WC()->cart->get_cart() as $cart_item ) {
			if ( ! empty( $cart_item['data'] ) && $cart_item['data']->is_type( 'donation' ) ) {
				add_filter( 'woocommerce_coupons_enabled', '__return_false' );
				break;
			}
		}
	}

	/**
	 * Enqueue frontend scripts.
	 *
	 * The frontend bundle is a small jQuery asset built by esbuild, not part
	 * of the React build, so it is served from `assets/build` directly.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function enqueue_scripts() {
		$this->app->scripts->enqueue_style( 'wcdm-frontend', $this->app->plugin_path( 'assets/build/frontend-common.css' ) );
		$this->app->scripts->enqueue_script( 'wcdm-frontend', $this->app->plugin_path( 'assets/build/frontend-common.js' ), array( 'jquery' ) );
	}
}
