<?php

namespace WooCommerceDonationManager\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Metaboxes.
 *
 * @since   1.0.0
 * @package WooCommerceDonationManager\Admin
 */
class Metaboxes {
	/**
	 * Metaboxes constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'product_type_selector', array( __CLASS__, 'add_type' ) );
		add_filter( 'woocommerce_product_data_tabs', array( __CLASS__, 'product_data_tab' ), 10, 1 );
		add_filter('woocommerce_product_options_general_product_data', array( __CLASS__, 'general_product_data' ) );
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
	public static function add_type( $product_type ) {
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
		foreach ($tabs as $tabId => $tabData) {
			if ($tabId != 'general' && $tabId != 'advanced') {
				$tabs[$tabId]['class'][] = 'hide_if_donation';
			}
		}

		$tabs[] =  array(
			'label'    => __( 'Donation Manager', 'wc-donation-manager' ),
			'target'   => 'wcdm_form_data',
			'class'    => 'show_if_donation hidden hide_if_external',
			'priority' => 11,
		);
		return $tabs;
	}

	/**
	 * Add fields to the General product data tab.
	 *
	 * @version 1.0.0
	 * @return void
	 */
	public static function general_product_data() {
		echo '<div class="options_group show_if_donation">';
		woocommerce_wp_text_input( array(
			'id' => 'donation_default_amount',
			'label' => esc_html__('Default amount',
				'donations-for-woocommerce', 'wc-donation-manager'),
			'value' => get_post_meta( get_the_ID(), '_price', true ),
			'data_type' => 'price'
		));
		$donationAmountIncrement = get_post_meta( get_the_ID(), '_donation_amount_increment', true);
		woocommerce_wp_text_input( array(
			'id' => 'donation_amount_increment',
			'label' => esc_html__('Amount increment',
				'donations-for-woocommerce', 'wc-donation-manager'),
			'value' => ( empty( $donationAmountIncrement ) ? 0.01 : $donationAmountIncrement ),
			'data_type' => 'decimal'
		));
		echo '</div>';
	}
}
