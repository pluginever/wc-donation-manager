<?php

defined( 'ABSPATH' ) || exit;

/**
 * Get list table class.
 *
 * @param string $type Type of list table to get.
 *
 * @since 1.0.0
 * @return object
 */
function wcsp_get_list_table( $type ) {
	switch ( $type ) {
		case 'campaigns':
		default:
			$list_table = new \WooCommerceDonationManager\Admin\ListTables\CampaignsListTable();
			break;
	}

	return $list_table;
}
