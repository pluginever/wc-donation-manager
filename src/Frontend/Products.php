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
class Products {
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
	 * @param string $link_Html
	 * @param $product \WC_Product object.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public static function add_to_cart_link( $link_Html, $product ) {
		return ( 'donation' == $product->get_type() ? str_replace( 'ajax_add_to_cart', '', $link_Html ) : $link_Html );
	}

	/**
	 * Disable price display in frontend for donation products.
	 *
	 * @param string $price product price html.
	 * @param $product \WC_Product object.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public static function get_price_html( $price, $product ) {
		if ( 'donation' == $product->get_type() ) {
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
		$currency_symbol = get_woocommerce_currency_symbol();
		$goal_amount     = '' !== get_post_meta( get_the_ID(), '_goal_amount', true ) ? get_post_meta( get_the_ID(), '_goal_amount', true ) : '0';
		$raised_amount   = '' !== get_post_meta( get_the_ID(), 'wcdm_raised_amount', true ) ? get_post_meta( get_the_ID(), 'wcdm_raised_amount', true ) : '0';
		if ( 'donation' == $product->get_type() ) {
			ob_start(); // TODO: have a question! Is it right way to use this if the contents displaying without help of ob_start().
			?>
			<div class="wc-donation-manager">
				<div class="campaign-cause">
					<p><?php echo get_post_meta( $product->get_id(), '_wcdm_campaign_cause', true ); ?></p>
				</div>
				<div class="campaign-progress">
					<div class="progress-label">
						<label for="campaign-progressbar"><?php echo $currency_symbol . $raised_amount; ?>
							raised</label>
						<label for="campaign-progressbar"><?php echo $currency_symbol . $goal_amount; ?> goal</label>
					</div>
					<progress id="campaign-progressbar" value="<?php echo $raised_amount ?>"
							  max="<?php echo $goal_amount ?>"><?php echo $raised_amount; ?>%
					</progress>
				</div>
				<h4>Suggested amounts:</h4>
				<div class="suggested-amounts">
					<button type="button">$25</button>
					<button type="button">$50</button>
					<button type="button">$75</button>
					<button type="button">$100</button>
				</div>
				<div class="campaign-amount">
					<label for="donation_amount"><?php esc_html_e( 'Other Amount', 'wc-donation-manager' );
						echo ' (' . $currency_symbol . ')'; ?>:</label>
					<input type="number" name="donation_amount" id="donation_amount"
						   min="<?php echo get_post_meta( $product->get_id(), '_wcdm_min_amount', true ); ?>"
						   max="<?php echo get_post_meta( $product->get_id(), '_wcdm_max_amount', true ); ?>"
						   step="<?php echo get_post_meta( $product->get_id(), '_amount_increment_steps', true ); ?>"
						   value="<?php echo number_format( $product->get_price(), 2, '.', '' ); ?>"
						   class="input-text text"/>
				</div>
			</div>

			<?php
			echo ob_get_clean();
		}
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
		$product_id = (int) apply_filters( 'woocommerce_add_to_cart_product_id', ! empty( $_POST['add-to-cart'] ) ? $_POST['add-to-cart'] : '' );

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
				$item['donation_amount'] = $_POST['donation_amount'] * 1;
			}
			$item['data']->set_price( $item['donation_amount'] );
		}

		return $item;
	}
}
