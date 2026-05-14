<?php

namespace WooCommerceDonationManager\Admin;

use WooCommerceDonationManager\Plugin;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Admin class.
 *
 * @since 1.0.0
 * @package WooCommerceDonationManager\Admin
 */
class Admin {
	/**
	 * Plugin instance.
	 *
	 * @var Plugin
	 */
	protected Plugin $plugin;

	/**
	 * Admin Class constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Plugin $plugin Plugin instance.
	 */
	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
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
		$this->plugin->scripts->enqueue_style( 'wcdm-admin', 'css/admin.css', array( 'woocommerce_admin_styles' ) );
		$this->plugin->scripts->register_script( 'wcdm-admin', 'js/admin.js' );

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
	public function add_screen_ids( $ids ): array {
		return array_merge( $ids, Utilities::get_screen_ids() );
	}

	/**
	 * Request review.
	 *
	 * @param string $text Footer text.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function admin_footer_text( string $text ): string {

		if ( in_array( get_current_screen()->id, Utilities::get_screen_ids(), true ) ) {
			$text = sprintf(
			/* translators: %s: Plugin name */
				__( 'Thank you for using %s!', 'wc-donation-manager' ),
				'<strong>' . esc_html( WCDM()->get_name() ) . '</strong>',
			);
			if ( $this->plugin->review_url ) {
				$text .= sprintf(
				/* translators: %s: Plugin name */
					__( ' Share your appreciation with a five-star review %s.', 'wc-donation-manager' ),
					'<a href="' . esc_url( $this->plugin->review_url ) . '" target="_blank">here</a>'
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
	 * @return string
	 * @since 1.0.0
	 */
	public function update_footer( string $text ): string {

		if ( in_array( get_current_screen()->id, Utilities::get_screen_ids(), true ) ) {
			/* translators: 1: Plugin version */
			$text = sprintf( esc_html__( 'Version %s', 'wc-donation-manager' ), $this->plugin->version );
		}

		return $text;
	}
}
