<?php

namespace PluginEver\WooCommerceDonationManager\Controllers;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Class Product.
 *
 * This class is responsible for all frontend functionality for products shop/archive page.
 *
 * @since 1.0.0
 * @package PluginEver\WooCommerceDonationManager\Controllers
 */
class Product {
	/**
	 * Product constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'woocommerce_loop_add_to_cart_link', array( __CLASS__, 'add_to_cart_link' ), 10, 2 );
		add_filter( 'woocommerce_get_price_html', array( __CLASS__, 'get_price_html' ), 10, 2 );
		add_action( 'woocommerce_donation_add_to_cart', array( __CLASS__, 'add_to_cart_template' ) );
		add_action( 'woocommerce_before_add_to_cart_button', array( __CLASS__, 'before_add_to_cart_button' ) );
		add_filter( 'woocommerce_add_to_cart_redirect', array( __CLASS__, 'add_to_cart_redirect' ), 10, 2 );
		add_filter( 'woocommerce_get_cart_item_from_session', array( __CLASS__, 'get_cart_item_from_session' ) );
		add_filter( 'woocommerce_add_cart_item', array( __CLASS__, 'set_cart_item' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Remove ajax_add_to_cart from add to cart button.
	 *
	 * This will only be applied for the donation products.
	 *
	 * @param string      $link Link html.
	 * @param \WC_Product $product Product object.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public static function add_to_cart_link( $link, $product ) {
		return ( 'donation' === $product->get_type() ? str_replace( 'ajax_add_to_cart', '', $link ) : $link );
	}

	/**
	 * Disable price display in frontend for donation products.
	 *
	 * @param string      $price product price html.
	 * @param \WC_Product $product Product object.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public static function get_price_html( $price, $product ) {
		if ( 'donation' === $product->get_type() ) {
			return ( is_admin() ? 'Variable' : '' );
		}

		return $price;
	}

	/**
	 * Use the Simple product type's add to cart button for donation products.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function add_to_cart_template() {
		do_action( 'woocommerce_simple_add_to_cart' );
	}

	/**
	 * Add donation information and amount input field before the add to cart button.
	 *
	 * This will only be applied for the donation products.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function before_add_to_cart_button() {
		global $product;
		if ( 'donation' === $product->get_type() ) {
			$currency_symbol  = get_woocommerce_currency_symbol();
			$is_custom_amount = get_post_meta( $product->get_id(), '_is_custom_amount', true );
			$campaign_cause   = get_post_meta( $product->get_id(), '_wcdm_campaign_cause', true );
			?>
			<div class="wc-donation-manager">
				<?php if ( $campaign_cause ) : ?>
					<div class="campaign-cause">
						<p>
							<?php echo wp_kses_post( $campaign_cause ); ?>
						</p>
					</div>
				<?php endif; ?>

				<div class="campaign-amount <?php echo sanitize_html_class( 'yes' === $is_custom_amount ? '' : 'disabled' ); ?>">
					<label for="donation_amount" class="input-text"><?php echo sprintf( /* translators: 1: WC currency symbol */ __( '%1$s', 'wc-donation-manager' ), esc_html( $currency_symbol ) ); // phpcs:ignore ?></label>
					<input type="<?php echo esc_attr( 'yes' === $is_custom_amount ? 'number' : 'hidden' ); ?>" name="donation_amount" id="donation_amount" min="<?php echo esc_attr( get_post_meta( $product->get_id(), '_wcdm_min_amount', true ) ); ?>" max="<?php echo esc_attr( get_post_meta( $product->get_id(), '_wcdm_max_amount', true ) ); ?>" step="<?php echo esc_attr( get_post_meta( $product->get_id(), '_amount_increment_steps', true ) ); ?>" value="<?php echo esc_attr( number_format( floatval( $product->get_price() ), 2, '.', '' ) ); ?>" class="input-text text"/>
				</div>
			</div>
			<?php
		}
	}

	/**
	 * Redirecting weather cart or checkout page.
	 *
	 * This will only be applied for the donation products.
	 *
	 * @param string $url Add to cart button url.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public static function add_to_cart_redirect( $url ) {
		wp_verify_nonce( '_wpnonce' );

		$product_id = (int) apply_filters( 'woocommerce_add_to_cart_product_id', ! empty( $_POST['add-to-cart'] ) ? sanitize_key( wp_unslash( $_POST['add-to-cart'] ) ) : '' );
		if ( $product_id ) {
			$product = wc_get_product( $product_id );
			if ( $product->is_type( 'donation' ) && 'yes' === get_post_meta( $product_id, '_wcdm_is_fast_checkout', true ) ) {
				return wc_get_checkout_url();
			}
		}

		return $url;
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

		if ( 'donation' === $session_data['data']->get_type() && isset( $session_data['donation_amount'] ) ) {
			$session_data['data']->set_price( $session_data['donation_amount'] );
		}

		return $session_data;
	}

	/**
	 * Process donation amount when a Donation product is added to the cart.
	 *
	 * @param array $item Cart item data.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public static function set_cart_item( $item ) {
		wp_verify_nonce( '_wpnonce' );

		if ( 'donation' === $item['data']->get_type() ) {

			if ( isset( $_POST['donation_amount'] ) && is_numeric( $_POST['donation_amount'] ) && $_POST['donation_amount'] >= 0 ) {
				$item['donation_amount'] = floatval( wp_unslash( $_POST['donation_amount'] ) );
			}

			$item['data']->set_price( $item['donation_amount'] );
			$item['data']->set_regular_price( $item['donation_amount'] );
			$item['data']->set_sale_price( $item['donation_amount'] );
		}

		return $item;
	}

	/**
	 * Enqueue frontend scripts.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function enqueue_scripts() {
		// Enqueue css files.
		$asset_file = WCDM_ASSETS_URL . 'dist/css/wcdm-frontend.asset.php';
		$asset      = file_exists( $asset_file ) ? require_once $asset_file : array(
			'dependencies' => array(),
			'version'      => WCDM_VERSION,
		);
		wp_enqueue_style( 'wcdm-frontend', WCDM_ASSETS_URL . 'dist/css/wcdm-frontend.css', $asset['dependencies'], $asset['version'] );
	}
}
