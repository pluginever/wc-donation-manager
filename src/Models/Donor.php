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
class Donor extends Data {
	/**
	 * Post type.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $post_type = 'wcdm_donors';

	/**
	 * All data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.0.0
	 * @var array All data.
	 */
	protected $data = array(
		'name'        => '',
		'description' => '',
		'age'         => '',
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
		'name'        => 'post_title',
		'description' => 'post_content',
	);

	/**
	 * Save data.
	 *
	 * @since 1.0.0
	 * @return $this|\WP_Error Post object (or WP_Error on failure).
	 */
	public function save() {
		if ( empty( $this->get_prop( 'age' ) ) ) {
			return new \WP_Error( 'missing_required', __( 'Missing required age.', 'wc-donation-manager' ) );
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
	public function get_age() {
		return $this->get_prop( 'age' );
	}

	/**
	 * Set age.
	 *
	 * @param string $age Campaign number.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function set_age( $age ) {
		$this->set_prop( 'age', absint( $age ) );
	}

}
