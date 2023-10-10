<?php
/**
 * Usefully functions.
 *
 * @since 1.0.0
 * @package WooCommerceDonationManager
 */

use WooCommerceDonationManager\Models\Campaign;

defined( 'ABSPATH' ) || exit;

/**
 * Get campaigns.
 *
 * @param array $args The args.
 * @param bool  $count Whether to return a count.
 *
 * @return Campaign[]|int The campaigns.
 * @since 1.0.0
 */
function wcdm_get_campaigns( $args = [], $count = false ) {
	$defaults = array(
		'post_type'      => 'wcdm_campaigns',
		'posts_per_page' => - 1,
		'orderby'        => 'title',
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
 * @return Campaign|null
 * @version 1.0.0
 */
function wcdm_get_campaign( $campaign ) {
	$campaign = new Campaign( $campaign );

	if ( $campaign->get_id() ) {
		return $campaign;
	}

	return null;
}
