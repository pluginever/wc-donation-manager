<?php

namespace PluginEver\DonationManager;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Installer Class.
 *
 * @since 1.0.0
 * @package PluginEver\DonationManager
 */
class Installer extends B8\Component {

	/**
	 * Update hook name.
	 *
	 * @since 1.1.3
	 * @var string
	 */
	const UPDATE_HOOK = 'wc_donation_manager_run_update';

	/**
	 * Upgrade routines keyed by the target version.
	 *
	 * @since 1.0.0
	 * @var array<string, callable>
	 */
	protected array $updates = array();

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function register(): void {
		add_action( 'init', array( $this, 'maybe_update' ) );
		add_action( self::UPDATE_HOOK, array( $this, 'run_update' ) );
	}

	/**
	 * Run the installer when the stored version is behind the plugin version.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function maybe_update(): void {
		$can_install = ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) && ! defined( 'IFRAME_REQUEST' );

		if ( $can_install && version_compare( $this->app->version, $this->app->options->get_db_version(), '>' ) ) {
			$this->install();

			if ( ! empty( $this->updates ) ) {
				$this->app->queue->add( self::UPDATE_HOOK );
			}
		}
	}

	/**
	 * Run the pending upgrade routines.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function run_update(): void {
		$installed = $this->app->options->get_db_version();

		uksort( $this->updates, 'version_compare' );

		foreach ( $this->updates as $version => $callback ) {
			if ( version_compare( $installed, $version, '<' ) ) {
				call_user_func( $callback );
				$this->app->options->update_db_version( $version, true );
			}
		}

		$this->app->options->update_db_version( $this->app->version, true );
	}

	/**
	 * Install the plugin.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function install(): void {
		if ( ! is_blog_installed() ) {
			return;
		}

		$this->create_defaults();
		$this->app->options->update_db_version( $this->app->version, true );
		add_option( 'wcdm_installed', time() );

		flush_rewrite_rules();
	}

	/**
	 * Seed default option values from the registered settings fields.
	 *
	 * @since 1.1.3
	 * @return void
	 */
	protected function create_defaults(): void {
		foreach ( $this->app->settings->get_settings() as $group ) {
			foreach ( $group['fields'] as $field ) {
				if ( empty( $field['id'] ) || ! isset( $field['default'] ) || false !== get_option( $field['id'], false ) ) {
					continue;
				}
				add_option( $field['id'], $field['default'] );
			}
		}
	}

	/**
	 * Clean up the plugin's runtime state on deactivation.
	 *
	 * @since 1.1.3
	 * @return void
	 */
	public function deactivate(): void {
		$this->app->queue->clear();

		flush_rewrite_rules();
	}
}
