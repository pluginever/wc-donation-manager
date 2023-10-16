<?php

/**
 * Advanced Product Type
 */
class WC_Product_Donation extends WC_Product_Simple {
	/**
	 * Initialize simple product.
	 *
	 * @param WC_Product_Simple|int $product Product instance or ID.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $product = 0 ) {
		parent::__construct( $product );
	}

	/**
	 * Return the product type donation
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_type() {
		return 'donation';
	}



	/**
	 * Get the add to url used mainly in loops.
	 *
	 * @return string
	 */
//	public function add_to_cart_url() {
//		$url = $this->is_purchasable() && $this->is_in_stock() ? remove_query_arg(
//			'added-to-cart',
//			add_query_arg(
//				array(
//					'add-to-cart' => $this->get_id(),
//				),
//				( function_exists( 'is_feed' ) && is_feed() ) || ( function_exists( 'is_404' ) && is_404() ) ? $this->get_permalink() : ''
//			)
//		) : $this->get_permalink();
//		return apply_filters( 'woocommerce_product_add_to_cart_url', $url, $this );
//	}

	/**
	 * Get the add to cart button text.
	 *
	 * @return string
	 */
	public function add_to_cart_text() {
		$text = $this->is_purchasable() && $this->is_in_stock() ? __( 'Donate Now', 'woocommerce', 'wc-donation-manager' ) : __( 'Read more (Donate)', 'woocommerce', 'wc-donation-manager' );

		return apply_filters( 'woocommerce_product_add_to_cart_text', $text, $this );
	}

	/**
	 * Get the add to cart button text description - used in aria tags.
	 *
	 * @since 3.3.0
	 * @return string
	 */
	public function add_to_cart_description() {
		/* translators: %s: Product title */
		$text = $this->is_purchasable() && $this->is_in_stock() ? __( 'Add &ldquo;%s&rdquo; to your cart (Donate)', 'woocommerce', 'wc-donation-manager' ) : __( 'Read more about &ldquo;%s&rdquo; (Donate)', 'woocommerce', 'wc-donation-manager' );

		return apply_filters( 'woocommerce_product_add_to_cart_description (Donate)', sprintf( $text, $this->get_name() ), $this );
	}

}
