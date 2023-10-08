<?php

namespace WooCommerceDonationManager\Admin;

use WooCommerceDonationManager\Models\Campaign;

defined( 'ABSPATH' ) || exit;

/**
 * Actions class.
 *
 * All actions related to the admin area
 * should be added here.
 *
 * @since 1.0.0
 * @package WooCommerceDonationManager
 */
class Actions {

	/**
	 * Actions constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_post_wcdm_add_campaign', array( __CLASS__, 'add_campaign' ) );
	}

	/**
	 * Add a campaign.
	 *
	 * @since 1.0.0
	 * @return null
	 */
	public static function add_campaign() {
		check_admin_referer( 'wcdm_add_campaign' );
		$referer  = wp_get_referer();
		$data     = wp_unslash( $_POST );
		$campaign = Campaign::insert( $data );
		if ( is_wp_error( $campaign ) ) {
			wc_donation_manager()->add_notice( $campaign->get_error_message(), 'error' );
		} else {
			wc_donation_manager()->add_notice( __( 'Campaign saved successfully.', 'wc-donation-manager' ), 'success' );
		}
		wp_safe_redirect( $referer );
		exit;
	}

}
