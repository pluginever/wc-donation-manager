<?php

namespace PluginEver\DonationManager\Admin;

use PluginEver\DonationManager\B8\Component;

defined( 'ABSPATH' ) || exit;

/**
 * Notices class.
 *
 * @since 1.0.0
 */
class Notices extends Component {

	/**
	 * Notices constructor.
	 *
	 * @since 1.0.0
	 */
	public function register(): void {
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

		if ( ! $this->app->is_pro_active() ) {
			$this->app->notices->add(
				array(
					'message'     => $this->app->templates_path( 'admin/notices/upgrade.php' ),
					'notice_id'   => 'wcdm_upgrade',
					'style'       => 'border-left-color: #0542fa;',
					'dismissible' => false,
				)
			);
		}

		// Show after 5 days.
		if ( $installed_time && $current_time > ( $installed_time + ( 5 * DAY_IN_SECONDS ) ) ) {
			$this->app->notices->add(
				array(
					'message'     => $this->app->templates_path( 'admin/notices/review.php' ),
					'dismissible' => false,
					'notice_id'   => 'wcdm_review',
					'style'       => 'border-left-color: #0542fa;',
				)
			);
		}
	}
}
