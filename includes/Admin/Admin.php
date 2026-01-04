<?php

namespace WooCommerceDonationManager\Admin;

defined( 'ABSPATH' ) || exit(); // Exit if accessed directly.

/**
 * Admin class.
 *
 * @since 1.0.0
 * @package WooCommerceDonationManager\Admin
 */
class Admin {

	/**
	 * Admin constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
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
		WCDM()->scripts->enqueue_style( 'wcdm-admin', 'css/admin.css', array( 'bytekit-layout', 'bytekit-components', 'woocommerce_admin_styles' ) );
		WCDM()->scripts->register_script( 'wcdm-admin', 'js/admin.js' );

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

		if ( in_array( get_current_screen()->id, Utilities::get_screen_ids(), true ) ) {
			$text = sprintf(
			/* translators: %s: Plugin name */
				__( 'Thank you for using %s!', 'wc-donation-manager' ),
				'<strong>' . esc_html( WCDM()->get_name() ) . '</strong>',
			);
			if ( WCDM()->get_review_url() ) {
				$text .= sprintf(
				/* translators: %s: Plugin name */
					__( ' Share your appreciation with a five-star review %s.', 'wc-donation-manager' ),
					'<a href="' . esc_url( WCDM()->get_review_url() ) . '" target="_blank">here</a>'
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

		if ( in_array( get_current_screen()->id, Utilities::get_screen_ids(), true ) ) {
			/* translators: 1: Plugin version */
			$text = sprintf( esc_html__( 'Version %s', 'wc-donation-manager' ), WCDM()->get_version() );
		}

		return $text;
	}
}
