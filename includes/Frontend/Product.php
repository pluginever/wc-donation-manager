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
		add_filter( 'wc_add_to_cart_message_html', array( __CLASS__, 'add_to_cart_message' ), 10, 2 );
		add_filter( 'woocommerce_add_to_cart_validation', array( __CLASS__, 'prevent_duplicate_add_to_cart' ), 10, 2 );
	}

	/**
	 * Remove ajax_add_to_cart from add to cart button.
	 *
	 * This will only be applied for the donation products.
	 *
	 * @param string      $link Link html.
	 * @param \WC_Product $product Product object.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public static function add_to_cart_link( string $link, \WC_Product $product ): string {
		return ( 'donation' === $product->get_type() ? str_replace( 'ajax_add_to_cart', '', $link ) : $link );
	}

	/**
	 * Disable price display in frontend for donation products.
	 *
	 * @param string      $price product price html.
	 * @param \WC_Product $product Product object.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public static function get_price_html( string $price, \WC_Product $product ): string {
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
	public static function before_add_to_cart_button(): void {
		global $product;

		if ( 'donation' === $product->get_type() ) {
			$currency_symbol       = get_woocommerce_currency_symbol();
			$is_predefined_amounts = get_post_meta( $product->get_id(), 'wcdm_is_predefined_amounts', true );
			$is_custom_amount      = get_post_meta( $product->get_id(), 'wcdm_is_custom_amount', true );
			$campaign_id           = get_post_meta( $product->get_id(), 'wcdm_campaign_id', true );
			$campaign_cause        = ! empty( get_post_meta( $product->get_id(), 'wcdm_campaign_cause', true ) ) ? '<p>' . get_post_meta( $product->get_id(), 'wcdm_campaign_cause', true ) . '</p>' : apply_filters( 'the_content', get_post_field( 'post_content', $campaign_id ) );
			?>
			<div class="wc-donation-manager">
				<?php if ( $campaign_cause ) : ?>
				<div class="campaign-cause">
					<?php echo wp_kses_post( $campaign_cause ); ?>
				</div>
				<?php endif; ?>

				<?php if ( $campaign_id ) : ?>
					<div
						class="wcdm-progress-bar"
						data-raised="<?php echo esc_attr( (float) get_post_meta( $campaign_id, '_raised_amount', true ) ); ?>"
						data-goal="<?php echo esc_attr( (float) get_post_meta( $campaign_id, 'wcdm_goal_amount', true ) ); ?>"
						data-currency="<?php echo esc_html( $currency_symbol ); ?>"
					>
						<div class="donation-header">
							<div class="donation-title">Donation Progress</div>
							<div class="donation-percent">0%</div>
						</div>

						<div class="progress-wrapper">
							<div class="progress-bar"></div>
						</div>

						<div class="donation-info">
							<div>
								Raised:
								<span class="amount raised-amount">$0</span>
							</div>

							<div>
								Goal:
								<span class="amount goal-amount">$0</span>
							</div>
						</div>
					</div>
				<?php endif; ?>
				<?php
				if ( $is_predefined_amounts ) :
					$predefined_amounts_title = get_post_meta( $product->get_id(), 'wcdm_predefined_amounts_title', true );
					$predefined_amounts       = get_post_meta( $product->get_id(), 'wcdm_predefined_amounts', true );

					if ( $predefined_amounts_title ) {
						printf( '<h4>%s</h4>', esc_html( $predefined_amounts_title ) );
					}
					if ( $predefined_amounts ) {
						?>
					<div class="suggested-amounts">
						<label class="suggested-amount selected"><?php printf( '%s%.2f', esc_html( $currency_symbol ), floatval( $product->get_price() ) ); ?>
							<input type="radio" name="suggested-amount[]" value="<?php echo esc_html( $product->get_price() ); ?>" checked="checked">
						</label>
						<?php foreach ( $predefined_amounts as $predefined_amount ) : ?>
							<?php if ( $predefined_amount && $product->get_price() !== $predefined_amount ) { ?>
								<label class="suggested-amount"><?php printf( '%s%.2f', esc_html( $currency_symbol ), floatval( $predefined_amount ) ); ?>
									<input type="radio" name="suggested-amount[]" value="<?php echo esc_html( $predefined_amount ); ?>">
								</label>
							<?php } ?>
						<?php endforeach; ?>
					<?php } ?>
					</div>
				<?php endif; ?>
				<div class="campaign-amount <?php echo sanitize_html_class( 'yes' === $is_custom_amount ? '' : 'disabled' ); ?>">
					<label for="donation_amount" class="input-text"><?php echo esc_html( $currency_symbol ); ?></label>
					<input type="<?php echo esc_attr( 'yes' === $is_custom_amount ? 'number' : 'hidden' ); ?>" name="donation_amount" id="donation_amount"
							min="<?php echo esc_attr( get_post_meta( $product->get_id(), 'wcdm_min_amount', true ) ); ?>"
							max="<?php echo esc_attr( get_post_meta( $product->get_id(), 'wcdm_max_amount', true ) ); ?>"
							step="<?php echo esc_attr( get_post_meta( $product->get_id(), 'wcdm_amount_increment_steps', true ) ); ?>"
							value="<?php echo esc_attr( number_format( floatval( $product->get_price() ), 2, '.', '' ) ); ?>"
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
	public static function add_to_cart_template(): void {
		$campaign_id           = get_post_meta( get_the_ID(), 'wcdm_campaign_id', true );
		$campaign_end_date     = intval( str_replace( '-', '', get_post_meta( $campaign_id, '_end_date', true ) ) );
		$campaign_expired_text = get_option( 'wcdm_expired_text', __( 'The campaign expired!', 'wc-donation-manager' ) );

		if ( $campaign_id && intval( ( gmdate( 'Ymd' ) ) > $campaign_end_date ) ) {
			printf( '%1$s%2$s%3$s', '<p class="expired">', esc_html( $campaign_expired_text ), '</p>' );
			return;
		}

		do_action( 'woocommerce_simple_add_to_cart' );
	}

	/**
	 * Redirecting whether cart or checkout page.
	 *
	 * This will only be applied for the donation products.
	 *
	 * @param string $url Add to cart button url.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public static function add_to_cart_redirect( string $url ) {
		wp_verify_nonce( '_wpnonce' );
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
		wp_verify_nonce( '_wpnonce' );

		if ( 'donation' === $item['data']->get_type() ) {
			if ( isset( $_POST['donation_amount'] ) && is_numeric( $_POST['donation_amount'] ) && $_POST['donation_amount'] >= 0 ) {
				$item['donation_amount'] = floatval( wp_unslash( $_POST['donation_amount'] ) );
			}
			$item['data']->set_price( $item['donation_amount'] );
		}

		return $item;
	}

	/**
	 * Add to cart message for donation products.
	 *
	 * @param string $message Message.
	 * @param int    $product_id Product ID.
	 *
	 * @since 1.0.6
	 * @return string
	 */
	public static function add_to_cart_message( $message, $product_id ) {
		$product = wc_get_product( $product_id );

		if ( $product instanceof \WCDM_Donation_Product ) {
			return $product->add_to_cart_success_message();
		}

		return $message;
	}

	/**
	 * Prevent adding donation product again.
	 *
	 * @param bool $passed Whether the product can be added to the cart.
	 * @param int  $product_id Product ID.
	 *
	 * @return bool
	 * @since 1.0.6
	 */
	public static function prevent_duplicate_add_to_cart( bool $passed, int $product_id ): bool {
		$product = wc_get_product( $product_id );

		// Only donation products.
		if ( $product instanceof \WCDM_Donation_Product ) {
			foreach ( WC()->cart->get_cart() as $cart_item ) {
				if ( $cart_item['product_id'] === $product_id ) {
					wc_add_notice( $product->already_in_cart_message(), 'error' );
					return false;
				}
			}
		}

		return $passed;
	}
}
