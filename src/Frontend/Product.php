<?php

namespace WooCommerceDonationManager\Frontend;

defined( 'ABSPATH' ) || exit;

/**
 * Class Products.
 *
 * This class is responsible for all frontend functionality for products shop/archive page.
 *
 * @since 1.0.0
 * @package WooCommerceDonationManager\Frontend
 */
class Product {
	/**
	 * Products constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'woocommerce_loop_add_to_cart_link', array( __CLASS__, 'add_to_cart_link' ), 10, 2 );
		add_filter( 'woocommerce_get_price_html', array( __CLASS__, 'get_price_html' ), 10, 2 );
		add_action( 'woocommerce_before_add_to_cart_button', array( __CLASS__, 'before_add_to_cart_button' ) );
		add_action( 'woocommerce_donation_add_to_cart', array( __CLASS__, 'add_to_cart_template' ) );
		add_filter( 'woocommerce_add_to_cart_redirect', array( __CLASS__, 'add_to_cart_redirect' ), 10, 2 );
		add_filter( 'woocommerce_add_cart_item', array( __CLASS__, 'add_cart_item' ) );
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
			$currency_symbol       = get_woocommerce_currency_symbol();
			$is_predefined_amounts = get_post_meta( $product->get_id(), '_is_predefined_amounts', true );
			$is_custom_amount      = get_post_meta( $product->get_id(), '_is_custom_amount', true );
			$campaign_id           = get_post_meta( $product->get_id(), '_wcdm_campaign_id', true );
			?>
			<div class="wc-donation-manager">
				<div class="campaign-cause">
					<p><?php echo esc_textarea( get_post_meta( $product->get_id(), '_wcdm_campaign_cause', true ) ); ?></p>
				</div>
				<?php if ( $campaign_id ) : ?>
				<div class="campaign-progress">
					<div class="progress-label">
						<label for="campaign-progressbar"><?php echo sprintf( /* translators: 1: WC currency symbol 2: Raised amount */ __( '%1$s%2$.2f raised', 'wc-donation-manager' ), esc_html( $currency_symbol ), esc_html( get_post_meta( $campaign_id, '_raised_amount', true ) ) ); // phpcs:ignore ?></label>
						<label for="campaign-progressbar"><?php echo sprintf( /* translators: 1: WC currency symbol 2: Raised amount */ __( '%1$s%2$.2f goal', 'wc-donation-manager' ), esc_html( $currency_symbol ), esc_html( get_post_meta( $campaign_id, '_goal_amount', true ) ) ); // phpcs:ignore ?></label>
					</div>
					<progress id="campaign-progressbar" value="<?php echo esc_attr( get_post_meta( $campaign_id, '_raised_amount', true ) ); ?>" max="<?php echo esc_attr( get_post_meta( $campaign_id, '_goal_amount', true ) ); ?>"><?php echo sprintf( /* translators: 1: Raised amount */ __( '%1$s%%', 'wc-donation-manager' ), esc_html( get_post_meta( $campaign_id, '_raised_amount', true ) ) ); // phpcs:ignore ?></progress>
				</div>
				<?php endif; ?>
				<?php
				if ( $is_predefined_amounts ) {
					printf( '<h4>%s</h4>', esc_html( get_post_meta( $product->get_id(), '_predefined_amounts_title', true ) ) );
				}

				$predefined_amounts = esc_html( get_post_meta( $product->get_id(), '_predefined_amounts', true ) );
				?>
				<div class="suggested-amounts">
				<?php foreach ( explode( ',', $predefined_amounts ) as $predefined_amount ) : ?>
					<button class="suggested-amount" value="<?php echo esc_html( $predefined_amount ); ?>" type="button"><?php printf( '%s%.2f', esc_html( $currency_symbol ), floatval( $predefined_amount ) ); ?></button>
				<?php endforeach; ?>
				</div>
				<div class="campaign-amount <?php echo sanitize_html_class( $is_custom_amount ? '' : 'disabled' ); ?>">
					<label for="donation_amount" class="input-text"><?php echo sprintf( /* translators: 1: WC currency symbol */ __( 'Other Amount (%1$s) :', 'wc-donation-manager' ), esc_html( $currency_symbol ) ); // phpcs:ignore ?></label>
					<input type="number" name="donation_amount" id="donation_amount"
							min="<?php echo esc_html( get_post_meta( $product->get_id(), '_wcdm_min_amount', true ) ); ?>"
							max="<?php echo esc_html( get_post_meta( $product->get_id(), '_wcdm_max_amount', true ) ); ?>"
							step="<?php echo esc_html( get_post_meta( $product->get_id(), '_amount_increment_steps', true ) ); ?>"
							value="<?php echo esc_html( number_format( $product->get_price(), 2, '.', '' ) ); ?>"
							class="input-text text"/>
				</div>
			</div>
			<?php
		}
	}

	/**
	 * Use the Simple product type's add to cart button for donation products.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function add_to_cart_template() {
		$campaign_id       = get_post_meta( get_the_ID(), '_wcdm_campaign_id', true );
		$campaign_end_date = intval( str_replace( '-', '', get_post_meta( $campaign_id, '_end_date', true ) ) );

		if ( intval( ( gmdate( 'Ymd' ) ) > $campaign_end_date ) ) {
			printf( '%1$s%2$s%3$s', '<p class="expired">', esc_html__( 'The campaign expired!', 'wc-donation-manager' ), '</p>' );
			return;
		}

		do_action( 'woocommerce_simple_add_to_cart' );
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
		$product_id = (int) apply_filters( 'woocommerce_add_to_cart_product_id', ! empty( $_POST['add-to-cart'] ) ? sanitize_key( wp_unslash( $_POST['add-to-cart'] ) ) : '' );
		if ( $product_id ) {
			$product = wc_get_product( $product_id );
			if ( $product->is_type( 'donation' ) && 'yes' === get_option( 'wcdm_fast_checkout', 'no' ) ) {
				return wc_get_checkout_url();
			}
			if ( $product->is_type( 'donation' ) && 'yes' === get_option( 'wcdm_skip_cart', 'yes' ) ) {
				return wc_get_cart_url();
			}
		}

		return $url;
	}

	/**
	 * Process donation amount when a Donation product is added to the cart.
	 *
	 * @param array $item Item data.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public static function add_cart_item( $item ) {
		if ( 'donation' === $item['data']->get_type() ) {
			if ( isset( $_POST['donation_amount'] ) && is_numeric( $_POST['donation_amount'] ) && $_POST['donation_amount'] >= 0 ) {
				$item['donation_amount'] = floatval( wp_unslash( $_POST['donation_amount'] ) );
			}
			$item['data']->set_price( $item['donation_amount'] );
		}

		return $item;
	}
}
