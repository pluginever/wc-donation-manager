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
		'amount' => 0.00,
		'goal'   => 0.00,
		'cause'  => '',
		'status' => 'published',
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

		if ( empty( $this->get_prop( 'amount' ) ) ) {
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
		$this->set_prop( 'amount', floatval( $amount ) );
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
		$this->set_prop( 'goal', floatval( $goal ) );
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
		$all_status = array( "published", "private", "draft" );
		if ( in_array( $status, $all_status ) ) {
			$this->set_prop( 'status', sanitize_key( $status ) );
		} else {
			$this->set_prop( 'status', sanitize_key( 'draft' ) );
		}
	}
}
