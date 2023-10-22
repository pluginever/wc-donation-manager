<?php

namespace WooCommerceDonationManager\Models;

use WooCommerceDonationManager\Lib\Data;

defined( 'ABSPATH' ) || exit;

/**
 * Donor class
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
		'donation_no' => 0,
		'order'       => '',
		'order_id'    => 0,
		'amount'      => 0,
		'type'        => 'recurring',
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
		'name'  => 'post_title',
		'order' => 'post_content',
		'type'  => 'post_status',
	);

	/**
	 * Save data.
	 *
	 * @since 1.0.0
	 * @return $this|\WP_Error Post object (or WP_Error on failure).
	 */
	public function save() {
		if ( empty( $this->get_prop( 'name' ) ) ) {
			return new \WP_Error( 'missing_required', __( 'Missing required donor name.', 'wc-donation-manager' ) );
		}

		if ( empty( $this->get_prop( 'donation_no' ) ) ) {
			return new \WP_Error( 'missing_required', __( 'Missing required donation no.', 'wc-donation-manager' ) );
		}

		if ( empty( $this->get_prop( 'order_id' ) ) ) {
			return new \WP_Error( 'missing_required', __( 'Missing required order ID.', 'wc-donation-manager' ) );
		}

		if ( empty( $this->get_prop( 'amount' ) ) ) {
			return new \WP_Error( 'missing_required', __( 'Missing required amount.', 'wc-donation-manager' ) );
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
	 * Get donor name.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_name() {
		return $this->get_prop( 'name' );
	}

	/**
	 * Set donor name.
	 *
	 * @param string $name Donor name.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function set_name( $name ) {
		$this->set_prop( 'name', sanitize_text_field( $name ) );
	}

	/**
	 * Get donation no.
	 *
	 * @since 1.0.0
	 * @return numeric
	 */
	public function get_donation_no() {
		return $this->get_prop( 'donation_no' );
	}

	/**
	 * Set donation no.
	 *
	 * @param numeric $donation_no Donation no.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function set_donation_no( $donation_no ) {
		$this->set_prop( 'donation_no', absint( $donation_no ) );
	}

	/**
	 * Get order name.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_order() {
		return $this->get_prop( 'order' );
	}

	/**
	 * Set order name.
	 *
	 * @param string $order Order name.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function set_order( $order ) {
		$this->set_prop( 'order', sanitize_textarea_field( $order ) );
	}

	/**
	 * Get order ID.
	 *
	 * @since 1.0.0
	 * @return numeric
	 */
	public function get_order_id() {
		return $this->get_prop( 'order_id' );
	}

	/**
	 * Set donation no.
	 *
	 * @param numeric $order_id Order ID.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function set_order_id( $order_id ) {
		$this->set_prop( 'order_id', absint( $order_id ) );
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
	 * @param numeric $amount Donor amount.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function set_amount( $amount ) {
		$this->set_prop( 'amount', floatval( $amount ) );
	}

	/**
	 * Get type.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_type() {
		return $this->get_prop( 'type' );
	}

	/**
	 * Set type.
	 *
	 * @param string $type Donor type.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function set_type( $type ) {
		$types = array( 'recurring', 'onetime' );
		if ( in_array( $type, $types ) ) { // phpcs:ignore
			$this->set_prop( 'type', sanitize_key( $type ) );
		} else {
			$this->set_prop( 'type', sanitize_key( 'recurring' ) );
		}
	}
}
