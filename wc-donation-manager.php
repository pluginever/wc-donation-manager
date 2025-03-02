<?php
/**
 * Plugin Name:          WC Donation Manager
 * Plugin URI:           https://pluginever.com/
 * Description:          A powerful and user-friendly WordPress plugin designed to seamlessly integrate donation functionality into the WooCommerce platform. This plugin is the ultimate solution for effortlessly managing and receiving donations for a charitable organization, a non-profit, or a business looking to support a cause.
 * Version:              1.0.3
 * Requires at least:    5.0
 * Requires PHP:         7.4
 * Author:               PluginEver
 * Author URI:           https://pluginever.com/
 * License:              GPL v2 or later
 * License URI:          https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:          wc-donation-manager
 * Domain Path:          /languages
 * Tested up to:         6.7
 * WC requires at least: 3.0.0
 * WC tested up to:      9.7
 * Requires Plugins:     woocommerce
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

// Don't call the file directly.
defined( 'ABSPATH' ) || exit();

// Require the autoloader.
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/vendor-prefixed/autoload.php';

// Instantiate the plugin.
WooCommerceDonationManager\Plugin::create(
	array(
		'file'         => __FILE__,
		'settings_url' => admin_url( 'admin.php?page=wcdm-settings' ),
	)
);
