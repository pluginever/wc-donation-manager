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
//		$is_order_page    = ( in_array( $hook, array( 'post.php', 'post-new.php' ), true ) && in_array( get_post_type(), array( 'shop_order' ), true ) ) || 'woocommerce_page_wc-orders' === $hook;
//		$is_product_page  = in_array( $hook, array( 'post.php', 'post-new.php' ), true ) && in_array( get_post_type(), array( 'product' ), true );
//		$is_settings_page = Menus::PARENT_SLUG . '_page_wcdm-settings' === $hook;
//		WCDM()->scripts->enqueue_style( 'wcdm-admin', 'css/admin.css', array( 'bytekit-layout', 'bytekit-components', 'woocommerce_admin_styles' ) );
//		WCDM()->scripts->register_script( 'wcdm-admin', 'js/admin.js' );
//
//		if ( ! in_array( $hook, Utilities::get_screen_ids(), true ) && ! $is_product_page && ! $is_order_page ) {
//			return;
//		}
//
//		$localize = array(
//			'ajaxurl'      => admin_url( 'admin-ajax.php' ),
//			'security'     => wp_create_nonce( 'wc_key_manager' ),
//			'i18n'         => array(
//				'search_products'  => esc_html__( 'Select products', 'wc-donation-manager' ),
//				'search_orders'    => esc_html__( 'Select orders', 'wc-donation-manager' ),
//				'search_customers' => esc_html__( 'Select customers', 'wc-donation-manager' ),
//			),
//			'key_settings' => array(
//				'pattern' => get_option( 'wcdm_key_pattern', '####-####-####-####' ),
//				'chars'   => get_option( 'wcdm_key_characters', '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ' ),
//			),
//		);
//
//		wp_enqueue_style( 'jquery-ui-style' );
//		wp_enqueue_script( 'jquery-ui-datepicker' );
//		wp_localize_script( 'wcdm-admin', 'wcdm_admin_vars', $localize );
//		wp_enqueue_script( 'wcdm-admin' );
//		wp_enqueue_style( 'wcdm-admin' );

		$screen_ids = Utilities::get_screen_ids();
		WCDM()->scripts->enqueue_style( 'wcdm-admin', 'css/wcdm-admin.css', array( 'bytekit-layout', 'bytekit-components', 'woocommerce_admin_styles' ) );
		WCDM()->scripts->register_script( 'wcdm-admin', 'js/wcdm-admin.js' );

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
				__( 'Thank you for using %s!', 'starter-plugin' ),
				'<strong>' . esc_html( WCDM()->get_name() ) . '</strong>',
			);
			if ( WCDM()->get_review_url() ) {
				$text .= sprintf(
				/* translators: %s: Plugin name */
					__( ' Share your appreciation with a five-star review %s.', 'starter-plugin' ),
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
			$text = sprintf( esc_html__( 'Version %s', 'starter-plugin' ), WCDM()->get_version() );
		}

		return $text;
	}
}
