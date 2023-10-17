<?php

defined( 'ABSPATH' ) || exit;

if ( class_exists('WC_Product_Simple' ) ) {
	/**
	 * Class WC_Product_Donation
	 * Donation Product Type.
	 *
	 * @since 1.0.0
	 */
	class WC_Product_Donation extends \WC_Product_Simple {

		private int $amount_increment;

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
		 * Return the product type donation.
		 *
		 * @since 1.0.0
		 * @return string
		 */
		public function get_type() {
			return 'donation';
		}

		/**
		 * Get the campaign amount increment.
		 *
		 * @since 1.0.0
		 * @return string
		 */
		public function get_campaign_amount_increment() {

			if ( ! isset( $this->amount_increment ) ) {
				$this->amount_increment = get_post_meta( $this->get_id(), 'wcdm_campaign_amount_increment', true );
			} else {
				$this->amount_increment = 0.01;
			}

			return $this->amount_increment;
		}

		/**
		 * Get the add to cart button text.
		 *
		 * @since 1.0.0
		 * @return string
		 */
		public function add_to_cart_text() {
			$text = $this->is_purchasable() && $this->is_in_stock() ? __( 'Donate Now', 'wc-donation-manager' ) : __( 'Read more (Donate)', 'wc-donation-manager' );

			return apply_filters( 'woocommerce_product_add_to_cart_text', $text, $this );
		}

		/**
		 * Get the add to cart button text description - used in aria tags.
		 *
		 * @since 1.0.0
		 * @return string
		 */
		public function add_to_cart_description() {
			/* translators: %s: Product title */
			$text = $this->is_purchasable() && $this->is_in_stock() ? __( 'Add &ldquo;%s&rdquo; to your cart (Donate)', 'wc-donation-manager' ) : __( 'Read more about &ldquo;%s&rdquo; (Donate)', 'wc-donation-manager' );

			return apply_filters( 'woocommerce_product_add_to_cart_description', sprintf( $text, $this->get_name() ), $this );
		}
	}
}
