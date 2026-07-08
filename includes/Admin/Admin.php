<?php

namespace PluginEver\DonationManager\Admin;

use PluginEver\DonationManager\B8\Component;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Admin class.
 *
 * @since 1.0.0
 * @package PluginEver\DonationManager\Admin
 */
class Admin extends Component {

	/**
	 * Child components.
	 *
	 * @since 1.1.3
	 * @var array<int|string, class-string>
	 */
	public array $components = array(
		Menus::class,
		Settings::class,
		Actions::class,
		Metaboxes::class,
		Notices::class,
	);

	/**
	 * Whether to load.
	 *
	 * @since 1.1.3
	 * @return bool
	 */
	public function autoload(): bool {
		return is_admin();
	}

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function register(): void {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'woocommerce_screen_ids', array( $this, 'add_screen_ids' ) );
		add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), PHP_INT_MAX );
		add_filter( 'update_footer', array( $this, 'update_footer' ), PHP_INT_MAX );
	}

	/**
	 * Enqueue admin scripts.
	 *
	 * @param string $hook The current admin page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function enqueue_scripts( $hook ) {
		$screen_ids = Utilities::get_screen_ids();
		$this->app->scripts->enqueue_style( 'wcdm-admin', 'admin-common.css', array( 'b8-layout', 'b8-components', 'woocommerce_admin_styles' ) );
		$this->app->scripts->register_script( 'wcdm-admin', 'admin-common.js' );

		if ( in_array( $hook, $screen_ids, true ) ) {
			wp_enqueue_style( 'wcdm-admin' );
			wp_enqueue_script( 'wcdm-admin' );

			$localize = array(
				'ajaxurl'  => admin_url( 'admin-ajax.php' ),
				'security' => wp_create_nonce( 'wc_donation_manager' ),
				'i18n'     => array(
					'search_products' => esc_html__( 'Select products', 'wc-donation-manager' ),
				),
			);

			wp_localize_script( 'wcdm-admin', 'wcdm_admin_vars', $localize );
		}
	}

	/**
	 * Add the plugin screens to the WooCommerce screens.
	 * This will load the WooCommerce admin styles and scripts.
	 *
	 * @param array $ids Screen ids.
	 *
	 * @return array
	 */
	public function add_screen_ids( $ids ) {
		return array_merge( $ids, Utilities::get_screen_ids() );
	}

	/**
	 * Request review.
	 *
	 * @param string $text Footer text.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function admin_footer_text( $text ) {
		$screen = get_current_screen();
		if ( $screen && in_array( $screen->id, Utilities::get_screen_ids(), true ) ) {
			$text = sprintf(
			/* translators: %s: Plugin name */
				__( 'Thank you for using %s!', 'wc-donation-manager' ),
				'<strong>' . esc_html( (string) $this->app->get( 'name' ) ) . '</strong>',
			);
			if ( $this->app->review_url ) {
				$text .= sprintf(
				/* translators: %s: review link */
					__( ' Share your appreciation with a five-star review %s.', 'wc-donation-manager' ),
					'<a href="' . esc_url( (string) $this->app->review_url ) . '" target="_blank">here</a>'
				);
			}
		}

		return $text;
	}

	/**
	 * Update footer.
	 *
	 * @param string $text Footer text.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function update_footer( $text ) {
		$screen = get_current_screen();
		if ( $screen && in_array( $screen->id, Utilities::get_screen_ids(), true ) ) {
			/* translators: 1: Plugin version */
			$text = sprintf( esc_html__( 'Version %s', 'wc-donation-manager' ), $this->app->version );
		}

		return $text;
	}
}
