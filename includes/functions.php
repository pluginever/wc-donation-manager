<?php
/**
 * Usefully functions.
 *
 * @package PluginEver\DonationManager
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.


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
 * Get goal behavior for a campaign.
 *
 * Behaviour is a per-campaign value set in the campaign form; campaigns
 * saved before a behaviour was chosen keep accepting donations.
 *
 * @param int $campaign_id Campaign ID.
 * @return string
 */
function wcdm_get_goal_behavior( $campaign_id ) {
	$behavior = get_post_meta( $campaign_id, 'wcdm_goal_behavior', true );

	return in_array( $behavior, array( 'continue', 'close', 'soft_close' ), true ) ? $behavior : 'continue';
}

/**
 * Get the step-ladder-aligned initial donation amount for an input.
 *
 * Native number inputs step along the ladder `min + n × step`: when the
 * rendered value sits off the ladder (an empty price renders 0.00, or a
 * price that is not a multiple of the step), the first click snaps to the
 * nearest rung instead of adding a full step. Seeding the value onto the
 * ladder makes every increment and decrement exactly `±step`, regardless
 * of the minimum.
 *
 * @since 2.0.0
 * @param mixed $price Product default price. Empty means the donor picks the amount.
 * @param mixed $min   Minimum accepted amount.
 * @param mixed $max   Maximum accepted amount; 0 or empty means unlimited.
 * @param mixed $step  Amount increment step.
 * @return float The initial value, on the ladder.
 */
function wcdm_get_initial_amount( $price, $min, $max, $step ) {
	$min  = (float) $min;
	$max  = (float) $max;
	$step = (float) $step;

	if ( $step <= 0 ) {
		$step = 1;
	}

	$base = $min;
	if ( '' !== $price && null !== $price ) {
		$base = max( $min, (float) $price );
	}

	$rungs = 0;
	if ( $base > $min ) {
		$rungs = (int) ceil( ( $base - $min ) / $step );
	}

	$value = $min + ( $rungs * $step );

	if ( $max > 0 && $value > $max ) {
		$value = $max;
	}

	return $value;
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
