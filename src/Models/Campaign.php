<?php

namespace WooCommerceDonationManager\Models;

use WooCommerceDonationManager\Lib\Data;

defined( 'ABSPATH' ) || exit;

/**
 * Campaign class
 *
 * @package WooCommerceDonationManager
 * @since   1.0.0
 */
class Campaign extends Data {
	/**
	 * Post type.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $post_type = 'wcdm_campaigns';

	/**
	 * All data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.0.0
	 * @var array All data.
	 */
	protected $data = array(
		'campaign' => '',
		'amount'   => '',
		'goal'     => '',
		'cause'    => '',
		'status'   => '',
	);

	/**
	 * Post data to property map.
	 *
	 * Post data key => property key.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $postdata_map = array(
		'campaign' => 'post_title',
		'cause'    => 'post_content',
		'status'   => 'post_status',
	);

	/**
	 * Populate data.
	 *
	 * @param int|\WP_Post $data Post ID or object.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function populate_data( $data ) {
		if ( is_string( $data ) && get_page_by_path( $data, OBJECT, $this->post_type ) ) {
			$data = get_page_by_path( $data, OBJECT, $this->post_type );
		}

		return parent::populate_data( $data );
	}

	/**
	 * Save data.
	 *
	 * @since 1.0.0
	 * @return $this|\WP_Error Post object (or WP_Error on failure).
	 */
	public function save() {
		if ( empty( $this->get_prop( 'name' ) ) ) {
			return new \WP_Error( 'missing_required', __( 'Missing required campaign name', 'wc-donation-manager' ) );
		}

		return parent::save();
	}

	/*
	|--------------------------------------------------------------------------
	| Getters and Setters.
	|--------------------------------------------------------------------------
	| Getters and setters for the data properties.
	*/

	/**
	 * Get ticket number.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_campaign() {
		return $this->get_prop( 'name' );
	}

	/**
	 * Set ticket number.
	 *
	 * @param string $campaign Campaign number.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function set_campaign( $campaign ) {
		$this->set_prop( 'name', $campaign );
	}
}
