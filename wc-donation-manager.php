<?php
/**
 * Plugin Name:  WC Donation Manager
 * Description:  TBD.
 * Version:      1.0.0
 * Plugin URI:   https://pluginever.com/plugins/woocommerce-donation-manager/
 * Author:       PluginEver
 * Author URI:   https://pluginever.com/
 * Text Domain:  wc-donation-manager
 * Domain Path: /languages/
 * Requires PHP: 5.6
 * WC requires at least: 3.0.0
 * WC tested up to: 7.1.0
 *
 * @package WooCommerceDonationManager
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

use WooCommerceDonationManager\Plugin;

// don't call the file directly.
defined( 'ABSPATH' ) || exit();

// Autoload function.
spl_autoload_register(
	function ( $class ) {
		$prefix = 'WooCommerceDonationManager\\';
		$len    = strlen( $prefix );

		// Bail out if the class name doesn't start with our prefix.
		if ( strncmp( $prefix, $class, $len ) !== 0 ) {
			return;
		}

		// Remove the prefix from the class name.
		$relative_class = substr( $class, $len );
		// Replace the namespace separator with the directory separator.
		$file = str_replace( '\\', DIRECTORY_SEPARATOR, $relative_class ) . '.php';

		// Look for the file in the src and lib directories.
		$file_paths = array(
			__DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $file,
			__DIR__ . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . $file,
		);

		foreach ( $file_paths as $file_path ) {
			if ( file_exists( $file_path ) ) {
				require_once $file_path;
				break;
			}
		}
	}
);

/**
 * Plugin compatibility with WooCommerce HPOS
 *
 * @since 1.0.0
 * @return void
 */
add_action(
	'before_woocommerce_init',
	function () {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
);


/**
 * Get the plugin instance.
 *
 * @since 1.0.0
 * @return Plugin
 */
function wc_donation_manager() { // phpcs:ignore
	$data = array(
		'file'             => __FILE__,
		'settings_url'     => admin_url( 'admin.php?page=wc-donation-manager' ),
		'support_url'      => 'https://pluginever.com/support/',
		'docs_url'         => 'https://pluginever.com/docs/wocommerce-starter-plugin/',
	);

	return Plugin::create( $data );
}

// Initialize the plugin.
wc_donation_manager();
