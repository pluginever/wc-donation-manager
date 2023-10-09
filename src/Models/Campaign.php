<?php

namespace WooCommerceDonationManager\Models;

use WooCommerceDonationManager\Lib\Data;

defined( 'ABSPATH' ) || exit;

/**
 * Campaign class
 *
 * @since 1.0.0
 * @package WooCommerceDonationManager
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
		'name'   => '',
		'amount' => '',
		'goal'   => '',
		'cause'  => '',
		'status' => '',
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
		'name'   => 'post_title',
		'cause'  => 'post_content',
		'status' => 'post_status',
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
	 * Get campaign name.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_campaign() {
		return $this->get_prop( 'name' );
	}

	/**
	 * Set campaign name.
	 *
	 * @param string $campaign Campaign number.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function set_campaign( $campaign ) {
		$this->set_prop( 'name', $campaign );
	}

	/**
	 * Get amount.
	 *
	 * @since 1.0.0
	 * @return numeric
	 */
	public function get_amount() {
		return $this->get_prop( 'amount' );
	}

	/**
	 * Set amount.
	 *
	 * @param numeric $amount Campaign amount.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function set_amount( $amount ) {
		$this->set_prop( 'amount', $amount );
	}

	/**
	 * Get goal.
	 *
	 * @since 1.0.0
	 * @return numeric
	 */
	public function get_goal() {
		return $this->get_prop( 'goal' );
	}

	/**
	 * Set goal.
	 *
	 * @param numeric $goal Campaign goal.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function set_goal( $goal ) {
		$this->set_prop( 'goal', $goal );
	}

	/**
	 * Get cause.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_cause() {
		return $this->get_prop( 'cause' );
	}

	/**
	 * Set cause.
	 *
	 * @param string $cause Campaign cause.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function set_cause( $cause ) {
		$this->set_prop( 'cause', $cause );
	}

	/**
	 * Get status.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_status() {
		return $this->get_prop( 'status' );
	}

	/**
	 * Set status.
	 *
	 * @param string $status Campaign status.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function set_status( $status ) {
		$this->set_prop( 'status', $status );
	}
}
