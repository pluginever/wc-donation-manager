<?php

namespace WooCommerceDonationManager\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Admin class.
 *
 * @since 1.0.0
 * @package WooCommerceDonationManager
 */
class Admin {

	/**
	 * Admin constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ), 1 );
		add_filter( 'woocommerce_screen_ids', array( $this, 'screen_ids' ) );
		add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), PHP_INT_MAX );
		add_filter( 'update_footer', array( $this, 'update_footer' ), PHP_INT_MAX );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
	}

	/**
	 * Init.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		// require_once __DIR__ . '/Functions.php';
		// wc_donation_manager()->services->add( Settings::instance() );
		wc_donation_manager()->services->add( Menus::class );
		// wc_donation_manager()->services->add( Orders::class );
		// wc_donation_manager()->services->add( Products::class );
		 wc_donation_manager()->services->add( Actions::class );
	}

	/**
	 * Add the plugin screens to the WooCommerce screens.
	 * This will load the WooCommerce admin styles and scripts.
	 *
	 * @param array $ids Screen ids.
	 *
	 * @return array
	 */
	public function screen_ids( $ids ) {
		return array_merge( $ids, self::get_screen_ids() );
	}

	/**
	 * Admin footer text.
	 *
	 * @param string $footer_text Footer text.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function admin_footer_text( $footer_text ) {
		if ( wc_donation_manager()->get_review_url() && in_array( get_current_screen()->id, self::get_screen_ids(), true ) ) {
			$footer_text = sprintf(
			/* translators: 1: Plugin name 2: WordPress */
				__( 'Thank you for using %1$s. If you like it, please leave us a %2$s rating. A huge thank you from PluginEver in advance!', 'wc-donation-manager' ),
				'<strong>' . esc_html( wc_donation_manager()->get_name() ) . '</strong>',
				'<a href="' . esc_url( wc_donation_manager()->get_review_url() ) . '" target="_blank" class="wc-donation-manager-rating-link" data-rated="' . esc_attr__( 'Thanks :)', 'wc-donation-manager' ) . '">&#9733;&#9733;&#9733;&#9733;&#9733;</a>'
			);
		}

		return $footer_text;
	}

	/**
	 * Update footer.
	 *
	 * @param string $footer_text Footer text.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function update_footer( $footer_text ) {
		if ( in_array( get_current_screen()->id, self::get_screen_ids(), true ) ) {
			/* translators: 1: Plugin version */
			$footer_text = sprintf( esc_html__( 'Version %s', 'wc-donation-manager' ), wc_donation_manager()->get_version() );
		}

		return $footer_text;
	}

	/**
	 * Get screen ids.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public static function get_screen_ids() {
		$screen_ids = [
			'toplevel_page_wc-donation-manager',
			'woocommerce_page_plugin-wc-donation-manager',
			'admin_page_plugin-wc-donation-manager',
		];

		return apply_filters( 'wc_donation_manager_screen_ids', $screen_ids );
	}

	/**
	 * Enqueue admin scripts.
	 *
	 * @param string $hook Hook name.
	 *
	 * @since 1.0.0
	 */
	public function admin_scripts( $hook ) {
		$screen_ids = self::get_screen_ids();
		wc_donation_manager()->register_style( 'wcdm-admin', 'css/wcdm-admin.css' );
		wc_donation_manager()->register_script( 'wcdm-admin', 'js/wcdm-admin.js' );

		if ( in_array( $hook, $screen_ids, true ) ) {
			wp_enqueue_style( 'wcdm-admin' );
			wp_enqueue_script( 'wcdm-admin' );
		}
	}

	/**
	 * Get list table class.
	 *
	 * @param string $list_table List table class name.
	 *
	 * @return AbstractListTable
	 */
	public static function get_list_table( $list_table ) {
		static $instances = array();
		switch ( $list_table ) {

			case 'campaigns':
				$class = 'WooCommerceDonationManager\Admin\ListTables\CampaignsListTable';
				break;
			case 'donors':
				$class = 'WooCommerceDonationManager\Admin\ListTables\DonorsListTable';
				break;
		}

		if ( $class && class_exists( $class ) && ! isset( $instances[ $class ] ) ) {
			$instances[ $class ] = new $class();
		}

		return $instances[ $class ];
	}
}
