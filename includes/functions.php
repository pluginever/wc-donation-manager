<?php
/**
 * Usefully functions.
 *
 * @package WooCommerceDonationManager
 * @since 1.0.0
 */

use WooCommerceDonationManager\Plugin;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Get the plugin instance.
 *
 * @since 1.0.0
 * @return WooCommerceDonationManager\Plugin
 */
function WCDM() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	return Plugin::instance();
}

/**
 * Get campaign.
 *
 * @param mixed $campaign Campaign object or ID.
 *
 * @version 1.0.0
 * @return WP_Post|false The campaign object, or false if not found.
 */
function wcdm_get_campaign( $campaign ) {

	if ( is_numeric( $campaign ) ) {
		$campaign = get_post( $campaign );
	}

	if ( $campaign instanceof WP_Post && 'wcdm_campaigns' === $campaign->post_type ) {
		return $campaign;
	}

	return false;
}

/**
 * Get campaigns.
 *
 * @param array $args The campaign args.
 * @param bool  $count Whether to return a count.
 *
 * @since 1.0.0
 * @return array|int The campaigns.
 */
function wcdm_get_campaigns( $args = array(), $count = false ) {
	$defaults = array(
		'post_type'      => 'wcdm_campaigns',
		'posts_per_page' => - 1,
		'orderby'        => 'date',
		'order'          => 'ASC',
	);
	$args     = wp_parse_args( $args, $defaults );
	$query    = new WP_Query( $args );

	if ( $count ) {
		return $query->found_posts;
	}

	return array_map( 'wcdm_get_campaign', $query->posts );
}

/**
 * Get campaign products.
 *
 * @param int $campaign_id The campaign ID.
 *
 * @since 1.0.0
 * @return void|array|WP_Post[] The campaigns.
 */
function wcdm_get_campaign_products( $campaign_id ) {
	if ( ! $campaign_id ) {
		return;
	}

	$args = array(
		'post_type'      => 'product',
		'posts_per_page' => - 1,
		'orderby'        => 'date',
		'meta_key'       => 'wcdm_campaign_id', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		'meta_value'     => $campaign_id, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		'order'          => 'ASC',
	);

	$query = new WP_Query( $args );

	return $query->posts;
}

/**
 * Get the post title.
 *
 * @param int $post_id Post id.
 *
 * @since 1.0.0
 * @return void|string The post title.
 */
function wcdm_get_the_title( $post_id ) {
	if ( ! $post_id ) {
		return;
	}

	return sprintf( '(#%1$s) %2$s', $post_id, get_the_title( $post_id ) );
}

/**
 * Modify the "Product has been added to your cart" message for product-data: donation.
 *
 * @param string $message The message HTML.
 * @param array $products The products that were added to the cart.
 * @return string
 */
function wcdm_add_to_cart_message_customize_for_donation_product(string $message, array $products ): string
{
	$product_id = key( $products );
	$product = wc_get_product( $product_id );

	if ( $product && $product->get_type() === 'donation' ) {
		$product_name = $product->get_name();
		$message = sprintf(
			'<div><strong>%s</strong> - %s</div>',
			$product_name,
			esc_html__( 'Proceed with the donations', 'wc-donation-manager' )
		);
	}

	return $message;
}
add_filter( 'wc_add_to_cart_message_html', 'wcdm_add_to_cart_message_customize_for_donation_product', 10, 2 );



