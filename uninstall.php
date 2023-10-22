<?php
/**
 * WC Donation Manager Uninstall
 *
 * Uninstalling WC Donation Manager deletes user roles, pages, tables, and options.
 *
 * @package     WooCommerceDonationManager
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

// remove all the options starting with wcdm_.
$delete_all_options = get_option( 'wcdm_delete_data' );
// if ( empty( $delete_all_options ) ) {
// return;
// }
// Delete all the options.
global $wpdb;
$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'wcdm_%';" );
