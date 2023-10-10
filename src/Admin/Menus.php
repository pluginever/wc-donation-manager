<?php

namespace WooCommerceDonationManager\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Menus class.
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
		add_action( 'wc_donation_manager_donors_content', array( $this, 'output_donors_content' ) );
		add_action( 'wc_donation_manager_tools_content', array( $this, 'output_tools_content' ) );
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

		add_submenu_page(
			'wc-donation-manager',
			esc_html__( 'Campaign', 'wc-donation-manager' ),
			esc_html__( 'Campaign', 'wc-donation-manager' ),
			'manage_options',
			'wc-donation-manager',
			array( $this, 'output_main_page' )
		);

		add_submenu_page(
			'wc-donation-manager',
			esc_html__( 'Donors', 'wc-donation-manager' ),
			esc_html__( 'Donors', 'wc-donation-manager' ),
			'manage_options',
			'wcdm-donors',
			array( $this, 'output_donors_page' )
		);

		add_submenu_page(
			'wc-donation-manager',
			esc_html__( 'Tools', 'wc-donation-manager' ),
			esc_html__( 'Tools', 'wc-donation-manager' ),
			'manage_options',
			'wcdm-tools',
			array( $this, 'output_tools_page' )
		);
/*
		$submenu_pages = array(
			'Campaign',
			'Donors',
			'Tools',
		);
		// Add submenu pages.
		foreach ( $submenu_pages as $submenu_page ) {
			add_submenu_page(
				'wc-donation-manager',
				esc_html__( ucwords( $submenu_page ), 'wc-donation-manager' ),
				esc_html__( ucwords( $submenu_page ), 'wc-donation-manager' ),
				'manage_options',
				'Campaign' === $submenu_page ? 'wc-donation-manager' : strtolower( 'wcdm-' . $submenu_page ),
				array( $this, 'output_'. strtolower( $submenu_page ) .'_page' )
			);
		}
*/
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
			'wcdm-settings',
			array( Settings::class, 'output' )
		);
	}

	/**
	 * Output main page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function output_main_page() {
		$page_hook = 'campaigns';
		include __DIR__ . '/views/admin-page.php';
	}

	/**
	 * Output donors page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function output_donors_page() {
		$page_hook = 'donors';
		include __DIR__ . '/views/admin-page.php';
	}

	/**
	 * Output tools page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function output_tools_page() {
		$page_hook = 'tools';
		include __DIR__ . '/views/admin-page.php';
	}

	/**
	 * Output campaigns content.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function output_campaigns_content() {
		$add_campaigns  = isset( $_GET['new'] ) ? true : false; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$edit_campaign = isset( $_GET['edit_campaign'] ) ? absint( wp_unslash( $_GET['edit_campaign'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( $edit_campaign && ! wcdm_get_campaign( $edit_campaign ) ) {
			wp_safe_redirect( admin_url( 'admin.php?page=wc-donation-manager' ) );
			exit();
		}

		if ( $add_campaigns ) {
			include __DIR__ . '/views/add-campaign.php';
		} elseif ( $edit_campaign ) {
			include __DIR__ . '/views/edit-campaign.php';
		} else {
			include __DIR__ . '/views/list-campaigns.php';
		}
	}

	/**
	 * Output donors content.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function output_donors_content() {

		$add_campaigns  = isset( $_GET['new'] ) ? true : false; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		$edit_campaign = isset( $_GET['edit_donor'] ) ? absint( wp_unslash( $_GET['edit_donor'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

//		if ( $edit_campaign && ! wcdm_get_campaign( $edit_campaign ) ) {
//			wp_safe_redirect( admin_url( 'admin.php?page=wc-donation-manager' ) );
//			exit();
//		}
//
//		wp_die( $add_campaigns );

		if ( $add_campaigns ) {
			include __DIR__ . '/views/add-donors.php';
		} elseif ( $edit_campaign ) {
			include __DIR__ . '/views/edit-donors.php';
		} else {
			include __DIR__ . '/views/list-donors.php';
		}
	}

	/**
	 * Output tools content.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function output_tools_content() {
		echo 'Tools Content';
	}
}
