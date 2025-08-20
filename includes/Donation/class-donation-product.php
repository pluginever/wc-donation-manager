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
		 * Get the add to cart button success message for donation product.
		 *
		 * @since 1.0.6
		 * @return string
		 */
		public function add_to_cart_success_message() {
			$text = __( 'Your donation has been added to the cart. Proceed with the donation to help now.', 'wc-donation-manager' );

			if ( $this->is_purchasable() && $this->is_in_stock() ) {
				$text = sprintf(
				/* translators: %1$s: Product title, %2$s: View cart URL */
					__( 'Your donation to &ldquo;%1$s&rdquo; has been added to the cart. Proceed with the donation to help now. <a href="%2$s" class="button wc-forward">View Cart</a>', 'wc-donation-manager' ),
					$this->get_name(),
					esc_url( wc_get_cart_url() )
				);
			}

			/**
			 * Filter donation product add to cart success message.
			 *
			 * @since 1.0.6
			 * @param string $text The success message when a donation product is added to the cart.
			 * @param WCDM_Donation_Product $this Reference to the current WCDM_Donation_Product instance.
			 */
			return apply_filters( 'wcdm_donation_product_add_to_cart_success_message', $text, $this );
		}

		/**
		 * Get the already-in-cart error message for donation products.
		 *
		 * @since 1.0.6
		 * @return string
		 */
		public function already_in_cart_message() {
			$text = sprintf(
			/* translators: %s: Product title */
				__( 'You’ve already added “%s” to your cart. Complete your donation to help now!', 'wc-donation-manager' ),
				$this->get_name()
			);

			$view_cart_url = wc_get_cart_url();
			$text         .= sprintf(
				' <a href="%s" class="button wc-forward">%s</a>',
				esc_url( $view_cart_url ),
				__( 'View Cart', 'wc-donation-manager' )
			);

			/**
			 * Filter donation product already in cart message.
			 *
			 * @since 1.0.6
			 * @param string $text The message when a donation product is already in the cart.
			 * @param WCDM_Donation_Product $this Reference to the current WCDM_Donation_Product instance.
			 */
			return apply_filters( 'wcdm_donation_product_already_in_cart_message', $text, $this );
		}
	}
}
