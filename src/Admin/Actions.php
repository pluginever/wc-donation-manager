<?php

namespace WooCommerceDonationManager\Admin;

use WooCommerceDonationManager\Models\Campaign;
use WooCommerceDonationManager\Models\Donor;

defined( 'ABSPATH' ) || exit;

/**
 * Actions class.
 *
 * All actions related to the admin area
 * should be added here.
 *
 * @since 1.0.0
 * @package WooCommerceDonationManager
 */
class Actions {
	/**
	 * Actions constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_post_wcdm_add_campaign', array( __CLASS__, 'add_campaign' ) );
		add_action( 'admin_post_wcdm_edit_campaign', array( __CLASS__, 'edit_campaign' ) );
		add_action( 'woocommerce_process_product_meta_donation', array( __CLASS__, 'save_donation_meta' ) );
		add_action( 'admin_post_wcdm_add_donor', array( __CLASS__, 'add_donor' ) );
		add_action( 'admin_post_wcdm_edit_donor', array( __CLASS__, 'edit_donor' ) );
	}

	/**
	 * Add a campaign.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function add_campaign() {
		check_admin_referer( 'wcdm_add_campaign' );
		$referer  = wp_get_referer();
		$data     = wp_unslash( $_POST );
		$campaign = Campaign::insert( $data );
		// Set the product type as donation.
		wp_set_object_terms( $campaign->get_id(), 'donation', 'product_type' );
		if ( is_wp_error( $campaign ) ) {
			wc_donation_manager()->add_notice( $campaign->get_error_message(), 'error' );
		} else {
			wc_donation_manager()->add_notice( __( 'Campaign saved successfully.', 'wc-donation-manager' ), 'success' );
		}
		wp_safe_redirect( $referer );
		exit;
	}

	/**
	 * Edit a campaign.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function edit_campaign() {
		check_admin_referer( 'wcdm_edit_campaign' );
		$referer  = wp_get_referer();
		$data     = wp_unslash( $_POST );
		$campaign = Campaign::insert( $data );
		if ( is_wp_error( $campaign ) ) {
			wc_donation_manager()->add_notice( $campaign->get_error_message(), 'error' );
		} else {
			wc_donation_manager()->add_notice( __( 'Campaign saved successfully.', 'wc-donation-manager' ), 'success' );
		}
		wp_safe_redirect( $referer );
		exit;
	}

	/**
	 * Save donation product meta.
	 * The method only callable while adding/editing donation products type.
	 *
	 * @param int $product_id donation product id.
	 *
	 * @version 1.0.0
	 * @return void
	 */
	public static function save_donation_meta( $product_id ) {
		$price       = isset( $_POST['wcdm_amount'] ) ? floatval( wp_unslash( $_POST['wcdm_amount'] ) ) : '';
		$goal_amount = isset( $_POST['_goal_amount'] ) ? floatval( wp_unslash( $_POST['_goal_amount'] ) ) : '';
		update_post_meta( $product_id, '_price', $price );
		update_post_meta( $product_id, '_regular_price', $price );
		update_post_meta( $product_id, '_goal_amount', $goal_amount );
		update_post_meta( $product_id, '_amount_increment_steps', ( ! empty( $_POST['_amount_increment_steps'] ) && is_numeric( $_POST['_amount_increment_steps'] ) ? number_format( wp_unslash( $_POST['_amount_increment_steps'] ), 2, '.', '' ) : 0.01 ) );
		update_post_meta( $product_id, '_wcdm_min_amount', ( ! empty( $_POST['_wcdm_min_amount'] ) && is_numeric( $_POST['_wcdm_min_amount'] ) ? floatval( wp_unslash( $_POST['_wcdm_min_amount'] ) ) : get_option( 'wcdm_minimum_amount' ) ) );
		update_post_meta( $product_id, '_wcdm_max_amount', ( ! empty( $_POST['_wcdm_max_amount'] ) && is_numeric( $_POST['_wcdm_max_amount'] ) ? floatval( wp_unslash( $_POST['_wcdm_max_amount'] ) ) : get_option( 'wcdm_maximum_amount' ) ) );
		update_post_meta( $product_id, '_wcdm_campaign_cause', ( ! empty( $_POST['_wcdm_campaign_cause'] ) ? sanitize_text_field( wp_unslash( $_POST['_wcdm_campaign_cause'] ) ) : '' ) );
	}

	/**
	 * Add a donor.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function add_donor() {
		check_admin_referer( 'wcdm_add_donor' );
		$referer = wp_get_referer();
		$data    = wp_unslash( $_POST );
		$donor   = Donor::insert( $data );
		if ( is_wp_error( $donor ) ) {
			wc_donation_manager()->add_notice( $donor->get_error_message(), 'error' );
		} else {
			wc_donation_manager()->add_notice( __( 'Donor saved successfully.', 'wc-donation-manager' ), 'success' );
		}
		wp_safe_redirect( $referer );
		exit;
	}

	/**
	 * Edit a donor.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function edit_donor() {
		check_admin_referer( 'wcdm_edit_donor' );
		$referer  = wp_get_referer();
		$data     = wp_unslash( $_POST );
		$campaign = Donor::insert( $data );
		if ( is_wp_error( $campaign ) ) {
			wc_donation_manager()->add_notice( $campaign->get_error_message(), 'error' );
		} else {
			wc_donation_manager()->add_notice( __( 'Donor saved successfully.', 'wc-donation-manager' ), 'success' );
		}
		wp_safe_redirect( $referer );
		exit;
	}
}
