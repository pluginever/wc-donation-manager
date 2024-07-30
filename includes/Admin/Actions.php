<?php

namespace WooCommerceDonationManager\Admin;

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
	}

	/**
	 * Add a campaign.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function add_campaign() {
		check_admin_referer( 'wcdm_add_campaign' );
		$referer = wp_get_referer();

		$name        = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$cause       = isset( $_POST['cause'] ) ? sanitize_textarea_field( wp_unslash( $_POST['cause'] ) ) : '';
		$goal_amount = isset( $_POST['goal_amount'] ) ? floatval( wp_unslash( $_POST['goal_amount'] ) ) : floatval( '0' );
		$end_date    = isset( $_POST['end_date'] ) ? sanitize_text_field( wp_unslash( $_POST['end_date'] ) ) : '';
		$status      = isset( $_POST['status'] ) ? sanitize_key( wp_unslash( $_POST['status'] ) ) : 'pending';
		$id          = isset( $_POST['id'] ) ? intval( wp_unslash( $_POST['id'] ) ) : intval( '0' );

		$args = array(
			'ID'           => $id,
			'post_type'    => 'wcdm_campaigns',
			'post_title'   => wp_strip_all_tags( $name ),
			'post_content' => wp_kses_post( $cause ),
			'post_status'  => $status,
			'meta_input'   => array(
				'_goal_amount' => $goal_amount,
				'_end_date'    => $end_date,
			),
		);

		$campaign = wp_insert_post( $args );

		if ( is_wp_error( $campaign ) ) {
			WCDM()->flash->error( $campaign->get_error_message() );
		} else {
			WCDM()->flash->success( __( 'Campaign created successfully.', 'wc-donation-manager' ) );

			$referer = add_query_arg(
				array( 'edit' => absint( $campaign ) ),
				remove_query_arg( 'add', $referer )
			);
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
		$referer = wp_get_referer();

		$name        = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$cause       = isset( $_POST['cause'] ) ? sanitize_textarea_field( wp_unslash( $_POST['cause'] ) ) : '';
		$goal_amount = isset( $_POST['goal_amount'] ) ? floatval( wp_unslash( $_POST['goal_amount'] ) ) : floatval( '0' );
		$end_date    = isset( $_POST['end_date'] ) ? sanitize_text_field( wp_unslash( $_POST['end_date'] ) ) : '';
		$status      = isset( $_POST['status'] ) ? sanitize_key( wp_unslash( $_POST['status'] ) ) : 'pending';
		$id          = isset( $_POST['id'] ) ? intval( wp_unslash( $_POST['id'] ) ) : intval( '0' );

		$args = array(
			'ID'           => $id,
			'post_type'    => 'wcdm_campaigns',
			'post_title'   => wp_strip_all_tags( $name ),
			'post_content' => wp_kses_post( $cause ),
			'post_status'  => $status,
			'meta_input'   => array(
				'_goal_amount' => $goal_amount,
				'_end_date'    => $end_date,
			),
		);

		$campaign = wp_insert_post( $args );

		if ( is_wp_error( $campaign ) ) {
			WCDM()->flash->error( $campaign->get_error_message() );
		} else {
			WCDM()->flash->success( __( 'Campaign updated successfully.', 'wc-donation-manager' ) );
		}

		wp_safe_redirect( $referer );
		exit;
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
	}
}
