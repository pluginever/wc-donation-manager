<?php

namespace PluginEver\DonationManager\RestAPI\Controllers;

use WP_REST_Request;
use WP_REST_Response;

defined( 'ABSPATH' ) || exit;

/**
 * Settings REST controller.
 *
 * Serves the settings declaration; the React settings screen
 * renders straight from it.
 *
 * @since   2.0.0
 * @package PluginEver\DonationManager\RestAPI\Controllers
 */
class Settings extends Controller {

	/**
	 * Field keys the browser never sees.
	 *
	 * @since 2.0.0
	 * @var array<int, string>
	 */
	private const PRIVATE_KEYS = array( 'show_in_rest', 'capability' );

	/**
	 * Get the settings.
	 *
	 * @since 2.0.0
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response Groups, fields and values.
	 */
	public function get_items( $request ): WP_REST_Response {
		$settings = wc_donation_manager()->settings;
		$stored   = $settings->get_values();
		$fields   = array();
		$values   = array();

		foreach ( $this->get_settings() as $name => $field ) {
			$fields[ $name ] = array_diff_key( $field, array_flip( self::PRIVATE_KEYS ) );

			if ( array_key_exists( $name, $stored ) ) {
				$values[ $name ] = $stored[ $name ];
			}
		}

		return rest_ensure_response(
			array(
				'groups' => (object) $settings->get_groups(),
				'fields' => (object) $fields,
				'values' => (object) $values,
			)
		);
	}

	/**
	 * Save the settings.
	 *
	 * @since 2.0.0
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response Groups, fields and the values as they now stand.
	 */
	public function update_items( WP_REST_Request $request ): WP_REST_Response {
		/*
		 * Narrowed by value, never by field map: `get_fields()` strips `option`
		 * and `sanitize`, so handing it back as the map to save would clean every
		 * value as plain text and let a `section` row write an option.
		 */
		$values = array_intersect_key( (array) $request->get_json_params(), $this->get_settings() );

		wc_donation_manager()->settings->save_values( $values );

		return $this->get_items( $request );
	}

	/**
	 * Get the settings fields exposed to the current user.
	 *
	 * @since 2.0.0
	 * @return array<string, array<string, mixed>> Fields keyed by name.
	 */
	protected function get_settings(): array {
		return array_filter(
			wc_donation_manager()->settings->get_fields(),
			static function ( $field ) {
				if ( isset( $field['show_in_rest'] ) && false === $field['show_in_rest'] ) {
					return false;
				}

				return empty( $field['capability'] ) || current_user_can( $field['capability'] );
			}
		);
	}
}
