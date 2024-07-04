<?php

namespace WooCommerceDonationManager;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Installer Class.
 *
 * @since 1.0.0
 * @package WooCommerceDonationManager
 */
class Installer {

	/**
	 * Update callbacks.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $updates = array();

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'check_update' ), 5 );
		add_action( 'wcdm_run_update_callback', array( $this, 'run_update_callback' ), 10, 2 );
		add_action( 'wcdm_update_db_version', array( $this, 'update_db_version' ) );
	}

	/**
	 * Check the plugin version and run the updater if necessary.
	 *
	 * This check is done on all requests and runs if the versions do not match.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function check_update() {
		$db_version      = WCDM()->get_db_version();
		$current_version = WCDM()->get_version();
		$requires_update = version_compare( $db_version, $current_version, '<' );
		$can_install     = ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) && ! defined( 'IFRAME_REQUEST' );
		if ( $can_install && $requires_update && ! WC()->queue()->get_next( 'wcdm_run_update_callback' ) ) {
			static::install();
			$update_versions = array_keys( $this->updates );
			usort( $update_versions, 'version_compare' );
			if ( ! is_null( $db_version ) && version_compare( $db_version, end( $update_versions ), '<' ) ) {
				$this->update();
			} else {
				WCDM()->update_db_version( $current_version );
			}
		}
	}

	/**
	 * Update the plugin.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function update() {
		$db_version = WCDM()->get_db_version();
		$loop       = 0;
		foreach ( $this->updates as $version => $callbacks ) {
			$callbacks = (array) $callbacks;
			if ( version_compare( $db_version, $version, '<' ) ) {
				foreach ( $callbacks as $callback ) {
					WC()->queue()->schedule_single(
						time() + $loop,
						'wcdm_run_update_callback',
						array(
							'callback' => $callback,
							'version'  => $version,
						)
					);
					++$loop;
				}
			}
			++$loop;
		}

		if ( version_compare( WCDM()->get_db_version(), WCDM()->get_version(), '<' ) &&
			! WC()->queue()->get_next( 'wcdm_update_db_version' ) ) {
			WC()->queue()->schedule_single(
				time() + $loop,
				'wcdm_update_db_version',
				array(
					'version' => WCDM()->get_version(),
				)
			);
		}
	}

	/**
	 * Run the update callback.
	 *
	 * @param string $callback The callback to run.
	 * @param string $version The version of the callback.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function run_update_callback( $callback, $version ) {
		require_once __DIR__ . '/functions/updates.php';
		if ( is_callable( $callback ) ) {
			$result = (bool) call_user_func( $callback );
			if ( $result ) {
				WC()->queue()->add(
					'wcdm_run_update_callback',
					array(
						'callback' => $callback,
						'version'  => $version,
					)
				);
			}
		}
	}

	/**
	 * Update the plugin version.
	 *
	 * @param string $version The version to update to.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function update_db_version( $version ) {
		WCDM()->update_db_version( $version );
	}

	/**
	 * Install the plugin.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function install() {
		if ( ! is_blog_installed() ) {
			return;
		}
		self::save_default_settings();
		flush_rewrite_rules( true );
		add_option( 'wcdm_installed', time() );
		WCDM()->add_db_version();
	}

	/**
	 * Save default settings.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function save_default_settings() {
		Admin\Settings::instance()->save_defaults();
	}
}
