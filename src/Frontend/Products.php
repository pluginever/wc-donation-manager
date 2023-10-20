<?php

namespace WooCommerceDonationManager\Frontend;

defined( 'ABSPATH' ) || exit;

/**
 * Class Products.
 *
 * @package WooCommerceDonationManager\Frontend
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
		$currency_symbol = get_woocommerce_currency_symbol();
		$goal_amount = '' !== get_post_meta( get_the_ID(), 'wcdm_goal_amount', true ) ? get_post_meta( get_the_ID(), 'wcdm_goal_amount', true ) : '0';
		$raised_amount = '' !== get_post_meta( get_the_ID(), 'wcdm_raised_amount', true ) ? get_post_meta( get_the_ID(), 'wcdm_raised_amount', true ) : '0';
		if ( 'donation' == $product->get_type() ) {
			ob_start();
			?>
			<div class="wc-donation-manager">
				<div class="campaign-cause">
					<p><?php echo get_post_meta( $product->get_id(), 'wcdm_campaign_cause', true ); ?></p>
				</div>
				<div class="campaign-progress">
					<div class="progress-label">
						<label for="campaign-progressbar"><?php echo $currency_symbol . $raised_amount; ?> raised</label>
						<label for="campaign-progressbar"><?php echo $currency_symbol . $goal_amount; ?> goal</label>
					</div>
					<progress id="campaign-progressbar" value="<?php echo $raised_amount ?>>" max="100"><?php echo $raised_amount; ?>%</progress>
				</div>

				<div class="suggested-amounts">
					<h4>Suggested amounts:</h4>
					<button type="button">$25</button>
					<button type="button">$50</button>
					<button type="button">$75</button>
					<button type="button">$100</button>
				</div>

				<div class="campaign-amount">
					<label for="donation_amount"><?php esc_html_e('Other Amount', 'wc-donation-manager' ); echo ' (' . $currency_symbol . ')'; ?>:</label>
					<input type="number" name="donation_amount" id="donation_amount" min="<?php echo get_post_meta( $product->get_id(), 'wcdm_min_amount', true ); ?>" max="<?php echo get_post_meta( $product->get_id(), 'wcdm_max_amount', true ); ?>" step="<?php echo get_post_meta( $product->get_id(), 'wcdm_amount_increment_steps', true ); ?>" value="<?php echo number_format( $product->get_price(), 2, '.', '' ); ?>" class="input-text text" />
				</div>
			</div>

			<?php
			echo ob_get_clean();
		}
	}

	// Use the Simple product type's add to cart button for Donation products
	public static function add_to_cart_template() {
		do_action('woocommerce_simple_add_to_cart' );
	}

	// Redirecting weather cart or checkout page.
	public static function add_to_cart_redirect( $url ) {
		$product_id = (int) apply_filters( 'woocommerce_add_to_cart_product_id', ! empty( $_POST['add-to-cart'] ) ? $_POST['add-to-cart'] : '' );

		if( $product_id ){
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


	// Process donation amount when a Donation product is added to the cart
	public static function add_cart_item( $item ) {
		if ( 'donation' === $item['data']->get_type() ) {
			if ( isset( $_POST['donation_amount'] ) && is_numeric( $_POST['donation_amount'] ) && $_POST['donation_amount'] >= 0)
				$item['donation_amount'] = $_POST['donation_amount']*1;
			$item['data']->set_price( $item['donation_amount'] );
		}
		return $item;
	}

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
