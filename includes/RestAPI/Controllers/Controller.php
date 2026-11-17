<?php

namespace PluginEver\DonationManager\RestAPI\Controllers;

use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;

defined( 'ABSPATH' ) || exit;

/**
 * Base REST controller.
 *
 * @since   2.0.0
 * @package PluginEver\DonationManager\RestAPI\Controllers
 */
abstract class Controller extends WP_REST_Controller {

	/**
	 * Capability that guards every route of this plugin.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	const CAPABILITY = 'manage_woocommerce';

	/**
	 * Check whether the current user may read the list.
	 *
	 * @since 2.0.0
	 * @param WP_REST_Request $request Request object.
	 * @return bool Whether the current user may.
	 */
	public function get_items_permissions_check( $request ) {
		return current_user_can( self::CAPABILITY );
	}

	/**
	 * Check whether the current user may read a record.
	 *
	 * @since 2.0.0
	 * @param WP_REST_Request $request Request object.
	 * @return bool Whether the current user may.
	 */
	public function get_item_permissions_check( $request ) {
		return current_user_can( self::CAPABILITY );
	}

	/**
	 * Check whether the current user may read the list options.
	 *
	 * @since 2.0.0
	 * @param WP_REST_Request $request Request object.
	 * @return bool Whether the current user may.
	 */
	public function get_options_permissions_check( $request ) {
		return current_user_can( self::CAPABILITY );
	}

	/**
	 * Check whether the current user may create a record.
	 *
	 * @since 2.0.0
	 * @param WP_REST_Request $request Request object.
	 * @return bool Whether the current user may.
	 */
	public function create_item_permissions_check( $request ) {
		return current_user_can( self::CAPABILITY );
	}

	/**
	 * Check whether the current user may update a record.
	 *
	 * @since 2.0.0
	 * @param WP_REST_Request $request Request object.
	 * @return bool Whether the current user may.
	 */
	public function update_item_permissions_check( $request ) {
		return current_user_can( self::CAPABILITY );
	}

	/**
	 * Check whether the current user may delete a record.
	 *
	 * @since 2.0.0
	 * @param WP_REST_Request $request Request object.
	 * @return bool Whether the current user may.
	 */
	public function delete_item_permissions_check( $request ) {
		return current_user_can( self::CAPABILITY );
	}

	/**
	 * Wrap the prepared rows in a collection response with pagination headers.
	 *
	 * @since 2.0.0
	 * @param array<int, mixed> $rows     Prepared rows.
	 * @param int               $total    Total records matching the query.
	 * @param int               $per_page Records per page.
	 * @param WP_REST_Request   $request  Request object.
	 * @return WP_REST_Response The collection response.
	 */
	protected function prepare_collection_response( array $rows, int $total, int $per_page, WP_REST_Request $request ): WP_REST_Response {
		$pages    = $per_page > 0 ? (int) ceil( $total / $per_page ) : 1;
		$page     = max( 1, (int) $request->get_param( 'page' ) );
		$base     = add_query_arg( urlencode_deep( $request->get_query_params() ), rest_url( $request->get_route() ) );
		$response = rest_ensure_response( $rows );

		$response->header( 'X-WP-Total', (string) $total );
		$response->header( 'X-WP-TotalPages', (string) $pages );

		if ( $page > 1 ) {
			$response->link_header( 'prev', add_query_arg( 'page', min( $page - 1, $pages ), $base ) );
		}

		if ( $page < $pages ) {
			$response->link_header( 'next', add_query_arg( 'page', $page + 1, $base ) );
		}

		return $response;
	}

	/**
	 * Get the schema, respecting the context.
	 *
	 * @since 2.0.0
	 * @return array<string, mixed> Item schema.
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => $this->rest_base,
			'type'       => 'object',
			'properties' => $this->get_properties(),
		);

		return $this->add_additional_fields_schema( $schema );
	}

	/**
	 * Get the item properties.
	 *
	 * Override for controllers that serve records; a controller without an
	 * item schema, like the settings one, inherits the empty default.
	 *
	 * @since 2.0.0
	 * @return array<string, mixed> Properties keyed by name.
	 */
	protected function get_properties(): array {
		return array();
	}
}
