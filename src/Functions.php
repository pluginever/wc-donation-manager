<?php
/**
 * Usefully functions.
 *
 * @since 1.0.0
 * @package WooCommerceDonationManager
 */

use WooCommerceDonationManager\Models\Thing;

defined( 'ABSPATH' ) || exit;

//require_once __DIR__ . '/Functions/updates.php';

/**
 * Get thing.
 *
 * @param mixed $data The data.
 *
 * @since 1.0.0
 * @return Campaigns|false The campaigns, or false if not found.
 */
function wcdm_get_campaigns_edited( $data ) {

	if ( $data instanceof Campaigns ) {
		return $data;
	}

	if ( is_numeric( $data ) ) {
		$data = get_post( $data );
	}

	if ( $data instanceof WP_Post && 'wcsp_campaigns' === $data->post_type ) {
		return new Campaigns( $data );
	}
//	wp_die('hello');
	return false;
}

/**
 * Insert thing.
 *
 * @param array $data The data.
 * @param bool  $wp_error Optional. Whether to return a WP_Error object on failure. Default false.
 *
 * @since 1.0.0
 * @return Thing|WP_Error|false The thing object on success, WP_Error on failure. False if $wp_error is set to false.
 */
function wcsp_create_thing( $data, $wp_error = true ) {
	$defaults = array(
		'ID' => 0,
	);
	$data     = wp_parse_args( $data, $defaults );
	$thing    = new Thing( $data['ID'] );
	$thing->set_data( $data );
	$saved = $thing->save();

	if ( is_wp_error( $saved ) ) {
		return $wp_error ? $saved : false;
	}

	return $thing;
}

/**
 * Get things.
 *
 * @param array $args The args.
 * @param bool  $count Whether to return a count.
 *
 * @since 1.0.0
 * @return Campaigns[]|int The things.
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
 * Get ticket.
 *
 * @param mixed $campaign Ticket object or ID.
 *
 * @return Campaigns|null
 * @version 1.0.0
 */
function wcdm_get_campaign( $campaign ) {

//	wp_die();
	$campaign = new Campaigns( $campaign );

	if ( $campaign->get_id() ) {
		return $campaign;
	}

	return null;
}
