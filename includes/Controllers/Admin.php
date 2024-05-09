<?php

namespace PluginEver\WooCommerceDonationManager\Controllers;

defined( 'ABSPATH' ) || exit; // Exist if accessed directly.

/**
 * Admin class.
 *
 * @since 1.0.0
 */
class Admin {

	/**
	 * Admin Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'product_type_selector', array( __CLASS__, 'add_product_type' ) );
		add_filter( 'woocommerce_product_data_tabs', array( __CLASS__, 'product_data_tab' ), 10, 1 );
		add_filter( 'woocommerce_product_options_general_product_data', array( __CLASS__, 'general_product_data' ) );
		add_action( 'woocommerce_product_data_panels', array( __CLASS__, 'product_data' ) );
		add_action( 'woocommerce_process_product_meta_donation', array( __CLASS__, 'save_donation_meta' ) );
	}

	/**
	 * Add "Donation" as a product type selector.
	 * Only if this option is selected one can donate to the product.
	 *
	 * @param array $product_type array of product types.
	 *
	 * @version 1.0.0
	 * @return array array of product types.
	 */
	public static function add_product_type( $product_type ) {
		$product_type['donation'] = __( 'Donation', 'wc-donation-manager' );

		return $product_type;
	}

	/**
	 * Add donation tab on product edit page.
	 * Hide all except the General and Advanced product data tabs for Donation products.
	 *
	 * @param array $tabs Product data tabs.
	 *
	 * @version 1.0.0
	 * @return array array of product data tabs.
	 */
	public static function product_data_tab( array $tabs = array() ): array {
		foreach ( $tabs as $tab_id => $tab_data ) {
			if ( 'general' !== $tab_id && 'advanced' !== $tab_id ) {
				$tabs[ $tab_id ]['class'][] = 'hide_if_donation';
			}
		}

		$tabs['wc_donation_manager'] = array(
			'label'    => __( 'Donation Manager', 'wc-donation-manager' ),
			'target'   => 'wcdm_tab_data',
			'class'    => array( 'show_if_donation', 'hidden', 'hide_if_external' ),
			'priority' => 12,
		);

		return $tabs;
	}

	/**
	 * Add fields to the general product data.
	 *
	 * @version 1.0.0
	 * @return void
	 */
	public static function general_product_data() {
		echo '<div class="options_group show_if_donation">';
		$default_amount = get_post_meta( get_the_ID(), '_price', true );
		woocommerce_wp_text_input(
			array(
				'id'            => 'wcdm_amount',
				'label'         => __( 'Default amount', 'wc-donation-manager' ),
				'description'   => __( 'Enter the default amount for the campaign.', 'wc-donation-manager' ),
				'desc_tip'      => false,
				'value'         => ( empty( $default_amount ) ? floatval( 1 ) : floatval( $default_amount ) ),
				'data_type'     => 'price',
				'wrapper_class' => 'options_group',
			)
		);

		woocommerce_wp_checkbox(
			array(
				'id'            => '_is_custom_amount',
				'label'         => __( 'Allow custom amount', 'wc-donation-manager' ),
				'description'   => __( 'When enabled donors will be able to donate the custom amount.', 'wc-donation-manager' ),
				'value'         => empty( get_post_meta( get_the_ID(), '_is_custom_amount', true ) ) ? 'yes' : get_post_meta( get_the_ID(), '_is_custom_amount', true ),
				'wrapper_class' => 'options_group',
				'desc_tip'      => true,
			)
		);

		$amount_increment = get_post_meta( get_the_ID(), '_amount_increment_steps', true );
		woocommerce_wp_text_input(
			array(
				'id'          => '_amount_increment_steps',
				'label'       => __( 'Amount increment steps', 'wc-donation-manager' ),
				'description' => __( 'Enter the amount increment steps for the campaign amount field. This will applicable for increasing or decreasing amounts on the campaign page.', 'wc-donation-manager' ),
				'desc_tip'    => false,
				'value'       => ( empty( $amount_increment ) ? floatval( '0.01' ) : floatval( $amount_increment ) ),
				'data_type'   => 'decimal',
			)
		);
		echo '</div>';
	}

	/**
	 * Add fields to the product data panel.
	 *
	 * @version 1.0.0
	 * @return void
	 */
	public static function product_data() {
		echo '<div id="wcdm_tab_data" class="panel woocommerce_options_panel wcdm_tab_data_options"><div class="options_group show_if_donation">';
		woocommerce_wp_text_input(
			array(
				'id'          => '_wcdm_min_amount',
				'label'       => __( 'Minimum amount', 'wc-donation-manager' ),
				'description' => __( 'Enter the minimum amount for the campaign. Default amount comes from the plugin global settings.', 'wc-donation-manager' ),
				'desc_tip'    => true,
				'value'       => ! empty( get_post_meta( get_the_ID(), '_wcdm_min_amount', true ) ) ? get_post_meta( get_the_ID(), '_wcdm_min_amount', true ) : intval( '1' ),
				'data_type'   => 'price',
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'          => '_wcdm_max_amount',
				'label'       => __( 'Maximum amount', 'wc-donation-manager' ),
				'description' => __( 'Enter the maximum amount for the campaign. Default amount comes from the plugin global settings.', 'wc-donation-manager' ),
				'desc_tip'    => true,
				'value'       => ! empty( get_post_meta( get_the_ID(), '_wcdm_max_amount', true ) ) ? get_post_meta( get_the_ID(), '_wcdm_max_amount', true ) : intval( '1000' ),
				'data_type'   => 'price',
			)
		);

		woocommerce_wp_checkbox(
			array(
				'id'          => '_wcdm_is_fast_checkout',
				'label'       => __( 'Enable fast checkout', 'wc-donation-manager' ),
				'description' => __( 'This will redirect donors to the checkout page after adding a donation product to the cart item.', 'wc-donation-manager' ),
				'desc_tip'    => true,
				'value'       => empty( get_post_meta( get_the_ID(), '_wcdm_is_fast_checkout', true ) ) ? 'no' : get_post_meta( get_the_ID(), '_wcdm_is_fast_checkout', true ),
			)
		);

		woocommerce_wp_textarea_input(
			array(
				'id'          => '_wcdm_campaign_cause',
				'label'       => __( 'Campaign cause', 'wc-donation-manager' ),
				'description' => __( 'Enter the cause of the campaign. This will be override the assigned campaign cause text. Leave it empty to use the campaign default cause text.', 'wc-donation-manager' ),
				'desc_tip'    => true,
				'placeholder' => 'Enter the cause of the campaign...',
				'value'       => ! empty( get_post_meta( get_the_ID(), '_wcdm_campaign_cause', true ) ) ? get_post_meta( get_the_ID(), '_wcdm_campaign_cause', true ) : '',
			)
		);
		echo '</div></div>';
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
		$price = isset( $_POST['wcdm_amount'] ) ? floatval( wp_unslash( $_POST['wcdm_amount'] ) ) : '';

		update_post_meta( $product_id, '_price', $price );
		update_post_meta( $product_id, '_regular_price', $price );
		update_post_meta( $product_id, '_is_custom_amount', isset( $_POST['_is_custom_amount'] ) ? sanitize_text_field( wp_unslash( $_POST['_is_custom_amount'] ) ) : 'no' );
		update_post_meta( $product_id, '_wcdm_is_fast_checkout', isset( $_POST['_wcdm_is_fast_checkout'] ) ? sanitize_text_field( wp_unslash( $_POST['_wcdm_is_fast_checkout'] ) ) : 'no' );
		update_post_meta( $product_id, '_amount_increment_steps', ( ! empty( $_POST['_amount_increment_steps'] ) && is_numeric( $_POST['_amount_increment_steps'] ) ? number_format( wp_unslash( $_POST['_amount_increment_steps'] ), 2, '.', '' ) : 0.01 ) );
		update_post_meta( $product_id, '_wcdm_min_amount', ( ! empty( $_POST['_wcdm_min_amount'] ) && is_numeric( $_POST['_wcdm_min_amount'] ) ? floatval( wp_unslash( $_POST['_wcdm_min_amount'] ) ) : get_option( 'wcdm_minimum_amount' ) ) );
		update_post_meta( $product_id, '_wcdm_max_amount', ( ! empty( $_POST['_wcdm_max_amount'] ) && is_numeric( $_POST['_wcdm_max_amount'] ) ? floatval( wp_unslash( $_POST['_wcdm_max_amount'] ) ) : get_option( 'wcdm_maximum_amount' ) ) );
		update_post_meta( $product_id, '_wcdm_campaign_cause', ( ! empty( $_POST['_wcdm_campaign_cause'] ) ? sanitize_text_field( wp_unslash( $_POST['_wcdm_campaign_cause'] ) ) : '' ) );
	}
}
