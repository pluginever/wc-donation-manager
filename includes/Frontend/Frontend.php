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
		add_filter( 'woocommerce_checkout_fields', array( __CLASS__, 'remove_order_note_field' ), 20 );
		add_filter( 'woocommerce_enable_order_notes_field', array( __CLASS__, 'maybe_disable_order_notes_field' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Whether the cart holds a donation product.
	 *
	 * @since 2.0.0
	 * @return bool
	 */
	protected static function cart_has_donation(): bool {
		if ( ! ( WC()->cart instanceof \WC_Cart ) || WC()->cart->is_empty() ) {
			return false;
		}

		foreach ( WC()->cart->get_cart() as $cart_item ) {
			if ( ! empty( $cart_item['data'] ) && $cart_item['data']->is_type( 'donation' ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Remove the order notes field from the checkout while the cart holds
	 * a donation product.
	 *
	 * Classic checkout: the field is registered under the `order` fieldset,
	 * so dropping it there (and flipping the order-notes switch) removes
	 * the textarea and its validation everywhere the classic templates
	 * render it.
	 *
	 * @since 2.0.0
	 * @param array<string, mixed> $fields Checkout fields.
	 * @return array<string, mixed>
	 */
	public static function remove_order_note_field( array $fields ): array {
		if ( 'yes' !== get_option( 'wcdm_disabled_order_note', 'yes' ) || ! self::cart_has_donation() ) {
			return $fields;
		}

		unset( $fields['order']['order_comments'], $fields['shipping']['order_comments'] );

		return $fields;
	}

	/**
	 * Flip the classic order-notes switch off while the cart holds a
	 * donation product.
	 *
	 * @since 2.0.0
	 * @param bool $enabled Whether the order notes field is enabled.
	 * @return bool
	 */
	public static function maybe_disable_order_notes_field( bool $enabled ): bool {
		if ( 'yes' !== get_option( 'wcdm_disabled_order_note', 'yes' ) || ! self::cart_has_donation() ) {
			return $enabled;
		}

		return false;
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

		if ( 'yes' !== get_option( 'wcdm_disabled_coupon_field', 'yes' ) || ! self::cart_has_donation() ) {
			return;
		}

		add_filter( 'woocommerce_coupons_enabled', '__return_false' );
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

		wp_localize_script(
			'wcdm-frontend',
			'wcdm_front_vars',
			array(
				'fast_checkout' => get_option( 'wcdm_fast_checkout', 'no' ),
				'skip_cart'     => get_option( 'wcdm_skip_cart', 'yes' ),
				'checkout_url'  => wc_get_checkout_url(),
				'cart_url'      => wc_get_cart_url(),
			)
		);

		// The Checkout block renders its order-notes step from block markup;
		// hide it with CSS while the setting asks for it (classic checkout
		// loses the field through the checkout-fields filter instead).
		if ( 'yes' === get_option( 'wcdm_disabled_order_note', 'yes' ) && self::cart_has_donation() ) {
			wp_register_style( 'wcdm-checkout-note', false, array(), (string) $this->app->version );
			wp_enqueue_style( 'wcdm-checkout-note' );
			wp_add_inline_style(
				'wcdm-checkout-note',
				'.wc-block-checkout__order-notes{display:none!important}'
			);
		}
	}
}
