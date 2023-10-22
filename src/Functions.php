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
		'post_type'      => 'product',
		'posts_per_page' => - 1,
		'orderby'        => 'date',
		'order'          => 'ASC',
		'tax_query'      => array( // phpcs:ignore
			array(
				'taxonomy' => 'product_type',
				'field'    => 'slug',
				'terms'    => 'donation',
			),
		),
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
 * Get donors.
 *
 * @param array $args The args.
 * @param bool  $count Whether to return a count.
 *
 * @since 1.0.0
 * @return Donor[]|int The donors.
 */
function wcdm_get_donors( $args = array(), $count = false ) {
	$defaults = array(
		'post_type'      => 'wcdm_donors',
		'posts_per_page' => - 1,
		'orderby'        => 'title',
		'order'          => 'ASC',
	);
	$args     = wp_parse_args( $args, $defaults );
	$query    = new WP_Query( $args );

	if ( $count ) {
		return $query->found_posts;
	}

	return array_map( 'wcdm_get_donor', $query->posts );
}

/**
 * Get donor.
 *
 * @param mixed $donor Donor object or ID.
 *
 * @version 1.0.0
 * @return Donor|null
 */
function wcdm_get_donor( $donor ) {
	$donor = new Donor( $donor );

	if ( $donor->get_id() ) {
		return $donor;
	}

	return null;
}
