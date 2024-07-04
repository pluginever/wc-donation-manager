<?php

namespace WooCommerceDonationManager;

defined( 'ABSPATH' ) || exit;

/**
 * Helpers class.
 *
 * @since 1.0.0
 * @package WooCommerceDonationManager
 */
class Helpers {
	/**
	 * Get product title.
	 *
	 * @param \WC_Product| int $product Product title.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function wcdm_get_product_title( $product ) {
		$product = wc_get_product( $product );
		if ( $product && ! empty( $product->get_id() ) ) {
			return sprintf(
				'(#%1$s) %2$s',
				$product->get_id(),
				html_entity_decode( $product->get_formatted_name() )
			);
		}
		return '';
	}
}
