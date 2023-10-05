<?php

namespace WooCommerceDonationManager\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Admin class.
 *
 * @since 1.0.0
 * @package WooCommerceDonationManager
 */
class Menus {

	/**
	 * Menus constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'main_menu' ) );
		add_action( 'admin_menu', array( $this, 'settings_menu' ), 100 );
		add_action( 'wc_donation_manager_campaigns_content', array( $this, 'output_campaigns_content' ) );
	}

	/**
	 * Main menu.
	 *
	 * @since 1.0.0
	 */
	public function main_menu() {
		add_menu_page(
			esc_html__( 'Donation Manager', 'wc-donation-manager' ),
			esc_html__( 'Donation Manager', 'wc-donation-manager' ),
			'manage_woocommerce',
			'wc-donation-manager',
			null,
			'dashicons-money-alt',
			'55.5'
		);

		$submenu_pages = array(
			'Campaigns',
			'Donors',
			'Tools',
		);
		// Add submenu pages.
		foreach( $submenu_pages as $submenu_page ) {
			add_submenu_page(
				'wc-donation-manager',
				esc_html__( $submenu_page, 'wc-donation-manager' ),
				esc_html__( $submenu_page, 'wc-donation-manager' ),
				'manage_options',
				'Campaigns' === $submenu_page ? 'wc-donation-manager' : strtolower( 'wcdm-' . $submenu_page ),
				array( $this, 'output_main_page' )
			);
		}
	}

	/**
	 * Settings menu.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function settings_menu() {
		add_submenu_page(
			'wc-donation-manager',
			__( 'Settings', 'wc-donation-manager' ),
			__( 'Settings', 'wc-donation-manager' ),
			'manage_woocommerce',
			'wcsp-settings',
			array( Settings::class, 'output' )
		);
	}

	/**
	 * Output main page.
	 *
	 * @since 1.0.0
	 */
	public function output_main_page() {
		$page_hook = 'campaigns';
		include __DIR__ . '/views/admin-page.php';
	}

	/**
	 * Output things content.
	 *
	 * @since 1.0.0
	 */
	public function output_campaigns_content() {

		$add_campaigns  = isset( $_GET['new'] ) ? true : false; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$edit_campaigns = isset( $_GET['edit_campaigns'] ) ? absint( wp_unslash( $_GET['edit_campaigns'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
//		if ( $edit_campaigns && ! wcdm_get_campaigns( $edit_campaigns ) ) {
		if ( $edit_campaigns ) {
			wp_safe_redirect( admin_url( 'admin.php?page=wc-donation-manager' ) );
			exit();
		}
		if ( $add_campaigns ) {
			include __DIR__ . '/views/add-thing.php';
		} elseif ( $edit_campaigns ) {
			include __DIR__ . '/views/edit-thing.php';
		} else {
			include __DIR__ . '/views/list-campaigns.php';
		}
	}
}
