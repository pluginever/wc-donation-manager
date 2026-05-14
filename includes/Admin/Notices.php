<?php

namespace WooCommerceDonationManager\Admin;

use WooCommerceDonationManager\Plugin;

defined( 'ABSPATH' ) || exit;

/**
 * Notices class.
 *
 * @since 1.0.0
 */
class Notices {
	/**
	 * Plugin instance.
	 *
	 * @var Plugin
	 */
	protected Plugin $plugin;

	/**
	 * Notices constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
		add_action( 'admin_init', array( $this, 'admin_notices' ) );
	}

	/**
	 * Admin notices.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function admin_notices() {
		$installed_time = absint( get_option( 'wcdm_installed' ) );
		$current_time   = absint( wp_date( 'U' ) );

		if ( ! defined( 'WCDM_PRO_VERSION' ) ) {
			$this->plugin->notices->add(
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
			$this->plugin->notices->add(
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
