<?php

namespace WooCommerceDonationManager;

defined( 'ABSPATH' ) || exit;

/**
 * Class ProductTypes.
 *
 * Extends WC product simple type for Donation
 *
 * @package WooCommerceDonationManager
 * @since 1.0.0
 */
class WC_Product_Donation extends \WC_Product_Simple {

	/**
	 * Initialize simple product.
	 *
	 * @param \WC_Product_Simple|int $product Product instance or ID.
	 */

	/**
	 * Return the product type
	 *
	 * @return string
	 */
	public function get_type() {
		return 'donation';
	}

	/**
	 * Returns the product's active price.
	 *
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string price
	 */
}
