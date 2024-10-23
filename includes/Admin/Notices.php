<?php

namespace WooCommerceDonationManager\Admin;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Notices class.
 *
 * @since 1.0.0
 */
class Notices {

	/**
	 * Notices constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'admin_notices' ) );
	}

	/**
	 * Admin notices.
	 *
	 * @since 1.0.0
	 */
	public function admin_notices() {
		$installed_time = get_option( 'wcdm_installed' );
		$current_time   = wp_date( 'U' );

		// Halloween's promotion notice.
		$halloween_time = date_i18n( strtotime( '2024-11-11 00:00:00' ) );
		if ( $current_time < $halloween_time ) {
			WCDM()->notices->add(
				array(
					'message'     => __DIR__ . '/views/notices/halloween.php',
					'dismissible' => false,
					'notice_id'   => 'wcdm_halloween_promotion',
					'style'       => 'border-left-color: #8500ff;background-image: url("' . esc_url( WCDM()->get_assets_url( 'images/halloween-banner.svg' ) ) . '");',
					'class'       => 'notice-halloween',
				)
			);
		}

		if ( ! defined( 'WCDM_PRO_VERSION' ) ) {
			WCDM()->notices->add(
				array(
					'message'     => __DIR__ . '/views/notices/upgrade.php',
					'notice_id'   => 'wcdm_upgrade',
					'style'       => 'border-left-color: #0542fa;',
					'dismissible' => false,
				)
			);
		}

		// Show after 5 days.
		if ( $installed_time && $current_time > ( $installed_time + ( 5 * DAY_IN_SECONDS ) ) ) {
			WCDM()->notices->add(
				array(
					'message'     => __DIR__ . '/views/notices/review.php',
					'dismissible' => false,
					'notice_id'   => 'wcdm_review',
					'style'       => 'border-left-color: #0542fa;',
				)
			);
		}
	}
}
