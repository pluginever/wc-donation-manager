<?php

namespace WooCommerceDonationManager\Admin;

use WooCommerceDonationManager\Models\Campaign;

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
		add_action( 'wp_ajax_wcdm_search_products', array( __CLASS__, 'search_products' ) );
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
		if ( is_wp_error( $campaign ) ) {
			wc_donation_manager()->add_notice( $campaign->get_error_message(), 'error' );
		} else {
			self::handle_donation_product( $data );
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
			self::handle_donation_product( $data );
			wc_donation_manager()->add_notice( __( 'Campaign saved successfully.', 'wc-donation-manager' ), 'success' );
		}
		wp_safe_redirect( $referer );
		exit;
	}

	/**
	 * Update donation products depends on the campaign options.
	 *
	 * @param array $data Campaign meta data.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function handle_donation_product( $data ) {

		$product_ids = $data['donation_products'];

		if ( $product_ids && is_array( $product_ids ) ) {
			foreach ( $product_ids as $product_id ) {

				if ( $data['amount'] ) {
					update_post_meta( $product_id, '_price', floatval( $data['amount'] ) );
					update_post_meta( $product_id, '_regular_price', floatval( $data['amount'] ) );
				}

				if ( $data['goal_amount'] ) {
					update_post_meta( $product_id, '_goal_amount', floatval( $data['goal_amount'] ) );
				}

				if ( $data['cause'] ) {
					update_post_meta( $product_id, '_wcdm_campaign_cause', sanitize_text_field( $data['cause'] ) );
				}
			}
		}
	}

	/**
	 * Save donation product meta.
	 * The method only callable while adding/editing donation products type.
	 *
	 * @param int $product_id Donation product id.
	 *
	 * @version 1.0.0
	 * @return void
	 */
	public static function save_donation_meta( $product_id ) {
		wp_verify_nonce( '_wpnonce' );
		$price              = isset( $_POST['wcdm_amount'] ) ? floatval( wp_unslash( $_POST['wcdm_amount'] ) ) : '';
		$goal_amounts       = isset( $_POST['_goal_amount'] ) ? sanitize_text_field( wp_unslash( $_POST['_goal_amount'] ) ) : '';
		$predefined_amounts = ! empty( $_POST['_predefined_amounts'] ) ? explode( ',', preg_replace( '/\s*/m', '', sanitize_text_field( wp_unslash( $_POST['_predefined_amounts'] ) ) ) ) : array();
		$predefined_amounts = array_filter( array_unique( $predefined_amounts ) );

		update_post_meta( $product_id, '_price', $price );
		update_post_meta( $product_id, '_regular_price', $price );
		update_post_meta( $product_id, '_goal_amount', $goal_amounts );
		update_post_meta( $product_id, '_is_predefined_amounts', isset( $_POST['_is_predefined_amounts'] ) ? sanitize_text_field( wp_unslash( $_POST['_is_predefined_amounts'] ) ) : '' );
		update_post_meta( $product_id, '_predefined_amounts_title', ( ! empty( $_POST['_predefined_amounts_title'] ) ? sanitize_text_field( wp_unslash( $_POST['_predefined_amounts_title'] ) ) : __( 'Suggested amounts', 'wc-donation-manager' ) ) );
		update_post_meta( $product_id, '_predefined_amounts', $predefined_amounts );
		update_post_meta( $product_id, '_is_custom_amount', isset( $_POST['_is_custom_amount'] ) ? sanitize_text_field( wp_unslash( $_POST['_is_custom_amount'] ) ) : 'no' );
		update_post_meta( $product_id, '_amount_increment_steps', ( ! empty( $_POST['_amount_increment_steps'] ) && is_numeric( $_POST['_amount_increment_steps'] ) ? number_format( wp_unslash( $_POST['_amount_increment_steps'] ), 2, '.', '' ) : 0.01 ) );
		update_post_meta( $product_id, '_wcdm_min_amount', ( ! empty( $_POST['_wcdm_min_amount'] ) && is_numeric( $_POST['_wcdm_min_amount'] ) ? floatval( wp_unslash( $_POST['_wcdm_min_amount'] ) ) : get_option( 'wcdm_minimum_amount' ) ) );
		update_post_meta( $product_id, '_wcdm_max_amount', ( ! empty( $_POST['_wcdm_max_amount'] ) && is_numeric( $_POST['_wcdm_max_amount'] ) ? floatval( wp_unslash( $_POST['_wcdm_max_amount'] ) ) : get_option( 'wcdm_maximum_amount' ) ) );
		update_post_meta( $product_id, '_wcdm_campaign_id', ( ! empty( $_POST['_wcdm_campaign_id'] ) ? sanitize_text_field( wp_unslash( $_POST['_wcdm_campaign_id'] ) ) : '' ) );
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

	/**
	 * Search products.
	 *
	 * @since 1.1.4
	 */
	public static function search_products() {
		check_ajax_referer( 'wc_donation_manager', 'nonce' );

		$term = isset( $_POST['term'] ) ? sanitize_text_field( wp_unslash( $_POST['term'] ) ) : '';

		if ( empty( $term ) ) {
			wp_send_json_success( esc_html__( 'No, search term provided.', 'wc-donation-manager' ) );
			wp_die();
		}

		$data_store = \WC_Data_Store::load( 'product' );
		$ids        = $data_store->search_products( $term, '', true, true );
		$results    = array();

		if ( $ids ) {
			foreach ( $ids as $id ) {
				$product = wc_get_product( $id );
				if ( ! $product ) {
					continue;
				}
				$text = sprintf(
					'(#%1$s) %2$s',
					$product->get_id(),
					wp_strip_all_tags( $product->get_formatted_name() )
				);

				$results[] = array(
					'id'   => $product->get_id(),
					'text' => $text,
				);
			}
		}

		wp_send_json(
			array(
				'results'    => $results,
				'pagination' => array(
					'more' => false,
				),
			)
		);
		wp_die();
	}
}