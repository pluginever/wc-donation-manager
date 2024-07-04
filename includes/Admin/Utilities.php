<?php

namespace WooCommerceDonationManager\Admin;

use WooCommerceDonationManager\Admin\Menus;
use WooCommerceDonationManager\Admin\Settings;

defined( 'ABSPATH' ) || exit();

/**
 * Utilities class.
 *
 * @since 1.0.0
 * @package WooCommerceDonationManager\Admin
 */
class Utilities {

	/**
	 * Get admin menus.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public static function get_menus() {
		$menus = array(
			// Donors page.
			array(
				'page_title' => __( 'Donors', 'wc-donation-manager' ),
				'menu_title' => __( 'Donors', 'wc-donation-manager' ),
				'capability' => 'manage_options',
				'menu_slug'  => 'wcdm-donors',
				'page_id'    => 'donors',
				'callback'   => null,
			),
			// Settings page.
			array(
				'page_title' => __( 'Settings', 'wc-donation-manager' ),
				'menu_title' => __( 'Settings', 'wc-donation-manager' ),
				'capability' => 'manage_options',
				'menu_slug'  => 'wcdm-settings',
				'page_id'    => 'settings',
				'callback'   => array( Settings::class, 'output' ),
			),
		);

		return apply_filters( 'wc_donation_manager_admin_menus', $menus );
	}

	/**
	 * Get page ids.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public static function get_screen_ids() {
		$screen_ids = array(
			'toplevel_page_' . Menus::PARENT_SLUG,
			Menus::PARENT_SLUG . '_page_settings',
		);

		foreach ( self::get_menus() as $page ) {
			$screen_ids[] = Menus::PARENT_SLUG . '_page_' . $page['menu_slug'];
		}

		return $screen_ids;
	}

	/**
	 * Determine if current page is add screen.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public static function is_add_screen() {
		return filter_input( INPUT_GET, 'add' ) !== null;
	}

	/**
	 * Determine if current page is edit screen.
	 *
	 * @since 1.0.0
	 * @return false|int False if not edit screen, id if edit screen.
	 */
	public static function is_edit_screen() {
		return filter_input( INPUT_GET, 'edit', FILTER_VALIDATE_INT );
	}
}
