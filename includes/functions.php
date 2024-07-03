<?php

use WooCommerceDonationManager\Plugin;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Get the plugin instance.
 *
 * @since 1.0.0
 * @return WooCommerceDonationManager\Plugin
 */
function WCDM() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	return Plugin::instance();
}
