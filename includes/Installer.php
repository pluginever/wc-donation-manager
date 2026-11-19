<?php

namespace PluginEver\DonationManager;

use PluginEver\DonationManager\B8\Component;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Installer Class.
 *
 * @since 1.0.0
 * @package PluginEver\DonationManager
 *
 * @extends Component<Plugin>
 */
class Installer extends Component {

	/**
	 * Update hook name.
	 *
	 * @since 1.1.3
	 * @var string
	 */
	const UPDATE_HOOK = 'wcdm_run_update';

	/**
	 * Option storing the install timestamp.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	const INSTALLED_OPTION = 'installed_on';

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

		$this->migrate_legacy_options();
		$this->backfill_campaign_behaviors();
		$this->create_defaults();
		$this->app->options->update_db_version( $this->app->version, true );
		add_option( $this->app->options->key( self::INSTALLED_OPTION ), time() );

		flush_rewrite_rules();
	}

	/**
	 * Seed default option values from the registered settings fields.
	 *
	 * @since 1.1.3
	 * @return void
	 */
	protected function create_defaults(): void {
		foreach ( $this->app->settings->get_fields() as $name => $field ) {
			if ( ! isset( $field['default'] ) || false !== get_option( $this->app->options->key( $name ), false ) ) {
				continue;
			}

			$this->app->options->update( $name, $field['default'] );
		}
	}

	/**
	 * Fold the options written before the `wcdm` prefix existed into it.
	 *
	 * Every pre-2.0 option the framework wrote carried a prefix derived from
	 * the plugin slug (`wcdonationmanager_...`); the plugin now declares its
	 * own `wcdm` prefix.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	protected function migrate_legacy_options(): void {
		global $wpdb;

		$legacy_prefix = 'wcdonationmanager_';
		$pattern       = $wpdb->esc_like( $legacy_prefix ) . '%';

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- one-off rename of legacy-prefixed options; no core API.
		$names = $wpdb->get_col( $wpdb->prepare( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s", $pattern ) );

		if ( empty( $names ) ) {
			return;
		}

		foreach ( $names as $name ) {
			$value = get_option( $name );
			delete_option( $name );
			update_option( $this->app->options->key( substr( (string) $name, strlen( $legacy_prefix ) ) ), $value );
		}

		wp_cache_flush();
	}

	/**
	 * Copy the pre-2.0 global goal behaviour onto every campaign that has
	 * none, then retire the global option.
	 *
	 * Goal behaviour is per-campaign since 2.0.0; the old global setting
	 * still describes the intent of campaigns saved before it moved.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	protected function backfill_campaign_behaviors(): void {
		$legacy = (string) $this->app->options->get( 'goal_behavior', '' );

		if ( '' === $legacy ) {
			return;
		}

		foreach ( wcdm_get_campaigns( array( 'post_status' => 'any' ) ) as $campaign ) {
			$behavior = get_post_meta( $campaign->ID, Campaigns::META['goal_behavior'], true );

			if ( '' === $behavior || null === $behavior ) {
				update_post_meta( $campaign->ID, Campaigns::META['goal_behavior'], $legacy );
			}
		}

		$this->app->options->delete( 'goal_behavior' );
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
