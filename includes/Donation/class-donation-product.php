<?php
/**
 * Class WCDM_Donation_Product.
 *
 * @since 1.0.0
 * @package WooCommerceDonationManager\Donation
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WC_Product_Simple' ) ) {

	/**
	 * Class WC_Product_Donation
	 * Donation Product Type.
	 *
	 * @since 1.0.0
	 */
	class WCDM_Donation_Product extends \WC_Product_Simple {

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
		 * Set the default individual sold status as true.
		 *
		 * @since 1.0.0
		 * @return bool
		 */
		public function is_sold_individually() {
			return true;
		}

		/**
		 * Get the add to cart button text.
		 *
		 * @since 1.0.0
		 * @return string
		 */
		public function add_to_cart_text() {
			$text = $this->is_purchasable() && $this->is_in_stock() ? __( 'Donate Now', 'wc-donation-manager' ) : __( 'About donation', 'wc-donation-manager' );

			return apply_filters( 'wcdm_donation_product_add_to_cart_text', $text, $this );
		}

		/**
		 * Get the add to cart button url.
		 * Disabled add to cart feature from product shop or archive page
		 *
		 * @since 1.0.0
		 * @return string
		 */
		public function add_to_cart_url() {
			return get_permalink( $this->id );
		}

		/**
		 * Get the single add to cart button text.
		 *
		 * @since 1.0.0
		 * @return string
		 */
		public function single_add_to_cart_text() {
			$text = get_option( 'wcdm_add_to_cart_btn_text', __( 'Donate Now', 'wc-donation-manager' ) );

			return apply_filters( 'wcdm_donation_product_single_add_to_cart_text', $text, $this );
		}

		/**
		 * Get the add to cart button text description - used in aria tags.
		 *
		 * @since 1.0.0
		 * @return string
		 */
		public function add_to_cart_description() {
			/* translators: %s: Product title */
			$text = $this->is_purchasable() && $this->is_in_stock() ? __( 'Add &ldquo;%s&rdquo; to your cart', 'wc-donation-manager' ) : __( 'Read more about &ldquo;%s&rdquo;', 'wc-donation-manager' );

			return apply_filters( 'wcdm_donation_product_add_to_cart_description', sprintf( $text, $this->get_name() ), $this );
		}

		/**
		 * Set the default taxable status as false.
		 *
		 * @since 1.0.0
		 * @return bool
		 */
		public function is_taxable() {
			if ( 'yes' === get_option( 'wcdm_disabled_tax', 'yes' ) ) {
				return false;
			}

			return true;
		}

		/**
		 * Set the default shipping status as false.
		 *
		 * @since 1.0.0
		 * @return bool
		 */
		public function needs_shipping() {
			return false;
		}

		/**
		 * Set the default virtual product status as true.
		 *
		 * @since 1.0.0
		 * @return bool
		 */
		public function is_virtual() {
			return true;
		}

		/**
		 * Get the add to cart button success message for donation product - used to update the mini cart live region.
		 *
		 * @since 1.0.6
		 * @return string
		 */
		public function add_to_cart_success_message() {
			$text = '';

			if ( $this->is_purchasable() && $this->is_in_stock() ) {
				/* translators: %s: Product title */
				$text = __( 'Donation product &ldquo;%s&rdquo; has been added to your cart. Proceed with the donation.', 'wc-donation-manager' );
				$text = sprintf( $text, $this->get_name() );
			}

			/**
			 * Filter donation product add to cart success message.
			 *
			 * @since 9.2.0
			 * @param string $text The success message when a donation product is added to the cart.
			 * @param WCDM_Donation_Product $this Reference to the current WCDM_Donation_Product instance.
			 */
			return apply_filters( 'wcdm_donation_product_add_to_cart_success_message', $text, $this );
		}
	}
}
