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
	protected $post_type = 'product';

	/**
	 * All data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.0.0
	 * @var array All data.
	 */
	protected $data = array(
		'name'   => '',
		'price' => 0.00,
		'regular_price' => 0.00,
		'goal_amount'   => 0.00,
		'amount_increment_steps' => 0.01,
		'wcdm_min_amount' => 1,
		'wcdm_max_amount' => 100,
		'cause'  => '',
		'status' => 'publish',
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
		'cause'  => 'post_excerpt',
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

		if ( empty( $this->get_prop( 'price' ) ) ) {
			return new \WP_Error( 'missing_required', __( 'Missing required price.', 'wc-donation-manager' ) );
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
	 * Get price.
	 *
	 * @since 1.0.0
	 * @return numeric
	 */
	public function get_price() {
		return $this->get_prop( 'price' );
	}

	/**
	 * Set price.
	 *
	 * @param numeric $price Campaign price.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function set_price( $price ) {
		$this->set_prop( 'price', floatval( $price ) );
	}

	/**
	 * Get regular price.
	 *
	 * @since 1.0.0
	 * @return numeric
	 */
	public function get_regular_price() {
		return $this->get_prop( 'regular_price' );
	}

	/**
	 * Set regular price.
	 *
	 * @param numeric $regular_price Campaign regular price.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function set_regular_price( $regular_price ) {
		$this->set_prop( 'regular_price', floatval( $regular_price ) );
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
	 * Get amount increment steps.
	 *
	 * @since 1.0.0
	 * @return numeric
	 */
	public function get_amount_increment_steps() {
		return $this->get_prop( 'amount_increment_steps' );
	}

	/**
	 * Set amount increment steps.
	 *
	 * @param numeric $amount_increment_steps Campaign amount increment steps.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function set_amount_increment_steps( $amount_increment_steps ) {
		$this->set_prop( 'amount_increment_steps', is_numeric( $amount_increment_steps ) ? number_format( $amount_increment_steps, 2, '.', '' ) : 0.01 );
	}

	/**
	 * Get minimum amount.
	 *
	 * @since 1.0.0
	 * @return numeric
	 */
	public function get_wcdm_min_amount() {
		return $this->get_prop( 'wcdm_min_amount' );
	}

	/**
	 * Set minimum amount.
	 *
	 * @param numeric $wcdm_min_amount Campaign minimum amount.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function set_wcdm_min_amount( $wcdm_min_amount ) {
		$this->set_prop( 'wcdm_min_amount', floatval( $wcdm_min_amount ) );
	}

	/**
	 * Get maximum amount.
	 *
	 * @since 1.0.0
	 * @return numeric
	 */
	public function get_wcdm_max_amount() {
		return $this->get_prop( 'wcdm_max_amount' );
	}

	/**
	 * Set maximum amount.
	 *
	 * @param numeric $wcdm_max_amount Campaign maximum amount.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function set_wcdm_max_amount( $wcdm_max_amount ) {
		$this->set_prop( 'wcdm_max_amount', floatval( $wcdm_max_amount ) );
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
		$all_status = array( "Publish", "Pending", "Draft" );
		$status = ucfirst($status);
		if ( in_array( $status, $all_status ) ) {
			$this->set_prop( 'status', ucfirst( sanitize_key( $status ) ) );
		} else {
			$this->set_prop( 'status', ucfirst( sanitize_key( 'Draft' ) ) );
		}
	}
}
