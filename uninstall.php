<?php
/**
 * Donation Manager Uninstall.
 *
 * Uninstalling Donation Manager deletes the plugin's options, campaign
 * posts and post meta when the user opted in to data removal.
 *
 * @package PluginEver\DonationManager
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

if ( 'yes' !== get_option( 'wcdm_delete_data', 'no' ) && ! defined( 'WCDM_REMOVE_ALL_DATA' ) ) {
	return;
}

global $wpdb;

/*
 * The options live under the plugin's option prefix; flush them by
 * pattern like the framework's options service does.
 */
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- one-time uninstall; nothing is cached after.
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'wcdm\\_%' OR option_name LIKE 'wcdonationmanager\\_%'" );

// Delete campaigns and the donation product data of the campaigns.
$wpdb->query( "DELETE meta FROM {$wpdb->postmeta} meta LEFT JOIN {$wpdb->posts} posts ON posts.ID = meta.post_id WHERE posts.ID IS NULL AND meta.meta_key LIKE 'wcdm\\_%'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- one-time uninstall; nothing is cached after.
$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type = 'wcdm_campaigns'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- one-time uninstall; nothing is cached after.

wp_cache_flush();
