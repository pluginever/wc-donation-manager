<?php
/**
 * Usefully functions.
 *
 * @since 1.0.0
 * @package WooCommerceDonationManager
 */

use WooCommerceDonationManager\Models\Campaign;
use WooCommerceDonationManager\Models\Donor;

defined( 'ABSPATH' ) || exit;

/**
 * Get campaigns.
 *
 * @param array $args The args.
 * @param bool  $count Whether to return a count.
 *
 * @since 1.0.0
 * @return Campaign[]|int The campaigns.
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
 * Get campaign.
 *
 * @param mixed $campaign Campaign object or ID.
 *
 * @version 1.0.0
 * @return Campaign|null
 */
function wcdm_get_campaign( $campaign ) {
	$campaign = new Campaign( $campaign );

	if ( $campaign->get_id() ) {
		return $campaign;
	}

	return null;
}

/**
 * Get campaign products.
 *
 * @param int $campaign_id The campaign ID.
 *
 * @since 1.0.0
 * @return void|WP_Post[] The campaigns.
 */
function wcdm_get_campaign_products( $campaign_id ) {
	if ( ! $campaign_id ) {
		return;
	}

	$args  = array(
		'post_type'      => 'product',
		'posts_per_page' => - 1,
		'orderby'        => 'date',
		'meta_key'       => '_wcdm_campaign_id', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
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
 * Get donors from the woocommerce order list.
 *
 * @param array $args The args.
 * @param bool  $count Whether to return a count.
 *
 * @since 1.0.0
 * @return array|int
 */
function wcdm_get_donors( $args = array(), $count = false ) {
	$defaults = array(
		'limit'    => -1,
		'paged'    => 1,
		'paginate' => true,
		'order'    => 'DESC',
		'key'      => '_has_product_type',
		'value'    => 'donation',
	);

	$args   = wp_parse_args( $args, $defaults );
	$orders = wc_get_orders( $args );

	$filtered_orders = array();
	foreach ( $orders->orders as $order ) {
		foreach ( $order->get_items() as $item ) {
			$product = $item->get_product();
			if ( $product->is_type( 'donation' ) ) {
				$filtered_orders[] = $order;
				break;
			}
		}
	}

	if ( $count ) {
		return count( $filtered_orders );
	}

	return $filtered_orders;
}
