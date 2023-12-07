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
		'name'        => '',
		'cause'       => '',
		'goal_amount' => 0.00,
		'end_date'    => '',
		'status'      => 'publish',
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
	 * Save data.
	 *
	 * @since 1.0.0
	 * @return $this|\WP_Error Post object (or WP_Error on failure).
	 */
	public function save() {
		if ( empty( $this->get_prop( 'name' ) ) ) {
			return new \WP_Error( 'missing_required', __( 'Missing required campaign name.', 'wc-donation-manager' ) );
		}

		if ( empty( $this->get_prop( 'goal_amount' ) ) ) {
			return new \WP_Error( 'missing_required', __( 'Missing required amount.', 'wc-donation-manager' ) );
		}

		if ( empty( $this->get_prop( 'status' ) ) ) {
			return new \WP_Error( 'missing_required', __( 'Missing required status.', 'wc-donation-manager' ) );
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
	public function get_name() {
		return $this->get_prop( 'name' );
	}

	/**
	 * Set campaign name.
	 *
	 * @param string $campaign Campaign name.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function set_name( $campaign ) {
		$this->set_prop( 'name', sanitize_text_field( $campaign ) );
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
		$this->set_prop( 'cause', sanitize_textarea_field( $cause ) );
	}

	/**
	 * Get goal amount.
	 *
	 * @since 1.0.0
	 * @return numeric
	 */
	public function get_goal_amount() {
		return $this->get_prop( 'goal_amount' );
	}

	/**
	 * Set goal amount.
	 *
	 * @param numeric $goal_amount Campaign goal amount.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function set_goal_amount( $goal_amount ) {
		$this->set_prop( 'goal_amount', floatval( $goal_amount ) );
	}

	/**
	 * Get campaign end date.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_end_date() {
		return $this->get_prop( 'end_date' );
	}

	/**
	 * Set campaign end date.
	 *
	 * @param string $end_date Campaign end date.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function set_end_date( $end_date ) {
		$this->set_prop( 'end_date', sanitize_text_field( $end_date ) );
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
		$all_status = array( 'Publish', 'Pending', 'Draft' );
		$status     = ucfirst( $status );
		if ( in_array( $status, $all_status, true ) ) {
			$this->set_prop( 'status', ucfirst( sanitize_key( $status ) ) );
		} else {
			$this->set_prop( 'status', ucfirst( sanitize_key( 'Draft' ) ) );
		}
	}
}
