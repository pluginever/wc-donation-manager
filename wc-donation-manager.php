<?php
/**
 * Plugin Name: WC Donation Manager
 * Description: A powerful and user-friendly WordPress plugin designed to seamlessly integrate donation functionality into the WooCommerce platform. This plugin is the ultimate solution for effortlessly managing and receiving donations for a charitable organization, a non-profit, or a business looking to support a cause.
 * Version: 1.0.0
 * Plugin URI: https://pluginever.com/plugins/wc-donation-manager/
 * Author: PluginEver
 * Author URI: https://pluginever.com/
 * Text Domain: wc-donation-manager
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

// don't call the file directly.
defined( 'ABSPATH' ) || exit();

/**
 * Plugin compatibility with WooCommerce HPOS.
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

// Welcome to the WooCommerce Donation Manager Plugin.
