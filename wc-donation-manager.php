<?php
/**
 * Plugin Name:          Donation Manager
 * Plugin URI:           https://pluginever.com/plugins/woocommerce-donation-manager-pro/
 * Description:          Easily manage and collect donations with WooCommerce. It provides a seamless solution for receiving donations for charitable organizations, non-profits, or businesses supporting a cause.
 * Version:              1.1.3
 * Requires at least:    5.2
 * Tested up to:         7.0
 * Requires PHP:         7.4
 * Author:               PluginEver
 * Author URI:           https://pluginever.com/
 * License:              GPL v2 or later
 * License URI:          https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:          wc-donation-manager
 * Domain Path:          /languages
 * WC requires at least: 3.0.0
 * WC tested up to:      10.8
 * Requires Plugins:     woocommerce
 *
 * @link                 https://pluginever.com
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 * @author              Sultan Nasir Uddin <manikdrmc@gmail.com>
 * @copyright           2026 ByteEver
 * @license             GPL-2.0+
 * @package             PluginEver\DonationManager
 */

use PluginEver\DonationManager\Installer;
use PluginEver\DonationManager\Plugin;

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/functions.php';

$data = array(
	'version'      => '1.1.3',
	'name'         => 'Donation Manager',
	'settings_url' => admin_url( 'admin.php?page=wcdm-settings' ),
	'support_url'  => 'https://pluginever.com/support/',
	'docs_url'     => 'https://pluginever.com/docs/wc-donation-manager/',
	'upgrade_url'  => 'https://pluginever.com/plugins/woocommerce-donation-manager-pro/',
	'pro_basename' => 'wc-donation-manager-pro/wc-donation-manager-pro.php',
	'review_url'   => 'https://wordpress.org/support/plugin/wc-donation-manager/reviews/#new-post',
);

// Instantiate the plugin.
Plugin::create( __FILE__, $data );

/**
 * Get the plugin instance.
 *
 * @since 1.0.0
 * @return Plugin
 */
function wc_donation_manager(): Plugin {
	return Plugin::instance();
}

// Register the plugin activation and deactivation hooks.
wc_donation_manager()->on_activation( array( Installer::class, 'install' ) );
wc_donation_manager()->on_deactivation( array( Installer::class, 'deactivate' ) );

// Declare WooCommerce feature compatibility.
add_action(
	'before_woocommerce_init',
	function () {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__, true );
		}
	}
);

wc_donation_manager()->bootstrap();
