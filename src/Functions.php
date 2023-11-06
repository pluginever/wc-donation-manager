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
 * @param int $product_id The campaign product ID.
 *
 * @since 1.0.0
 * @return Campaign[]|int The campaigns.
 */

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

if ( ! function_exists( 'wc_get_email_order_items' ) ) {
	/**
	 * Get HTML for the order items to be shown in emails.
	 *
	 * @param WC_Order $order Order object.
	 * @param array    $args Arguments.
	 *
	 * @since 3.0.0
	 * @return string
	 */
	function wc_get_email_order_items( $order, $args = array() ) {
		ob_start();

		$defaults = array(
			'show_sku'      => false,
			'show_image'    => false,
			'image_size'    => array( 32, 32 ),
			'plain_text'    => false,
			'sent_to_admin' => false,
		);

		$args     = wp_parse_args( $args, $defaults );
		$template = $args['plain_text'] ? 'emails/plain/email-order-items.php' : 'emails/email-order-items.php';

		$order_object = array();
		foreach ( $order->get_items() as $item ) {
			$product = $item->get_product();
			if ( ! $product->is_type( 'donation' ) ) {
				$order_object[] = $item;
			}
		}

		wc_get_template(
			$template,
			apply_filters(
				'woocommerce_email_order_items_args',
				array(
					'order'               => $order,
					'items'               => $order_object,
					'show_download_links' => $order->is_download_permitted() && ! $args['sent_to_admin'],
					'show_sku'            => $args['show_sku'],
					'show_purchase_note'  => $order->is_paid() && ! $args['sent_to_admin'],
					'show_image'          => $args['show_image'],
					'image_size'          => $args['image_size'],
					'plain_text'          => $args['plain_text'],
					'sent_to_admin'       => $args['sent_to_admin'],
				)
			)
		);

		return apply_filters( 'woocommerce_email_order_items_table', ob_get_clean(), $order );
	}
}
