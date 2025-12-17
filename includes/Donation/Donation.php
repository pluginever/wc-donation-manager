<?php

namespace WooCommerceDonationManager\Donation;

defined( 'ABSPATH' ) || exit; // Exist if accessed directly.

/**
 * Class Donation.
 *
 * @since   1.0.0
 * @package WooCommerceDonationManager\Donation
 */
class Donation {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'product_type_selector', array( __CLASS__, 'add_product_type' ) );
		add_filter( 'woocommerce_product_class', array( __CLASS__, 'product_type_class' ), 10, 2 );
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
	 * Add "Donation" product class.
	 *
	 * @param string $class_name Class name.
	 * @param string $product_type Product type name.
	 *
	 * @version 1.0.0
	 * @return string Class name.
	 */
	public static function product_type_class( $class_name, $product_type ) {

		if ( 'donation' === $product_type ) {
			$class_name = 'WCDM_Donation_Product';
		}

		return $class_name;
	}
}
