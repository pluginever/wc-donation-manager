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
		add_filter( 'product_type_options', array( __CLASS__, 'product_type_option' ), 11 );
		add_filter( 'woocommerce_product_data_tabs', array( __CLASS__, 'product_data_tab' ) );

//		add_filter( 'woocommerce_product_data_tabs', array( __CLASS__, 'product_data_tab' ) );
//		add_action( 'woocommerce_product_data_panels', array( __CLASS__, 'product_write_panel' ) );
//		add_filter( 'woocommerce_process_product_meta', array( __CLASS__, 'product_save_data' ) );
//		add_action( 'woocommerce_product_after_variable_attributes', array( __CLASS__, 'variable_product_content' ), 10, 3 );
	}

	/**
	 * Add "Donation" as a product type option
	 * Only if this option is enabled one can donate to the product
	 *
	 * @param $product_type_options
	 *
	 * @version 1.0.0
	 * @return array
	 */
	public static function product_type_option( $product_type_options ): array {
		$product_type_options[ 'wcdm_donation' ] = [
			'id'            => 'wcdm_donation',
			'wrapper_class' => 'show_if_simple show_if_variable show_if_grouped',
			'label'         => __( 'Donation', 'wc-donation-manager' ),
			'description'   => __( 'This product will only be used for donation if activated', 'wc-donation-manager' ),
			'default'       => 'on',
		];

		return $product_type_options;
	}

	/**
	 * Add donation tab on product edit page.
	 *
	 * @param array $tabs Product data tabs.
	 *
	 * @version 1.0.0
	 * @return array
	 */
	public static function product_data_tab( array $tabs = array() ): array {
		$wcdm_tab =  array(
			'label'    => __( 'Donation Manager', 'wc-donation-manager' ),
			'target'   => 'wcdm_form_data',
			'class'    => 'show_if_donation_active hidden wcdm_donation hide_if_external',
			'priority' => 11,
		);
		$tabs[] = $wcdm_tab;
		return $tabs;
	}
}
