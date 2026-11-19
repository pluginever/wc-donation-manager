<?php

namespace PluginEver\DonationManager\Admin;

use PluginEver\DonationManager\B8\Component;

defined( 'ABSPATH' ) || exit;

/**
 * Class Metaboxes.
 *
 * @since   1.0.0
 * @package PluginEver\DonationManager\Admin
 */
class Metaboxes extends Component {
	/**
	 * Metaboxes constructor.
	 *
	 * @since 1.0.0
	 */
	public function register(): void {
		add_filter( 'woocommerce_product_data_tabs', array( __CLASS__, 'product_data_tab' ), 10, 1 );
		add_action( 'woocommerce_product_data_panels', array( __CLASS__, 'product_data' ) );
		add_action( 'woocommerce_admin_process_product_object', array( $this, 'save_donation_product_object' ) );
		add_action( 'woocommerce_process_product_meta_donation', array( $this, 'save_donation_meta' ) );
		add_action( 'admin_footer', array( __CLASS__, 'admin_custom_js' ) );
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
		// Donation products are managed entirely in the Donation Manager tab.
		foreach ( $tabs as $tab_id => $tab_data ) {
			$tabs[ $tab_id ]['class'][] = 'hide_if_donation';
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
	 * Add fields to the product data panel.
	 *
	 * Everything a donation product needs lives in the Donation Manager
	 * tab: the amount fields (moved out of General) and the limits and
	 * campaign assignment.
	 *
	 * @version 1.0.0
	 * @return void
	 */
	public static function product_data() {
		echo '<div id="wcdm_tab_data" class="panel woocommerce_options_panel wcdm_tab_data_options">';

		echo '<div class="options_group show_if_donation">';
		woocommerce_wp_text_input(
			array(
				'id'            => 'wcdm_amount',
				'label'         => __( 'Default amount', 'wc-donation-manager' ),
				'description'   => __( 'Enter the default amount for the campaign.', 'wc-donation-manager' ),
				'desc_tip'      => false,
				'value'         => get_post_meta( get_the_ID(), '_price', true ),
				'data_type'     => 'price',
				'wrapper_class' => 'options_group',
			)
		);

		woocommerce_wp_checkbox(
			array(
				'id'          => 'wcdm_is_predefined_amounts',
				'label'       => __( 'Allow predefined amounts', 'wc-donation-manager' ),
				'description' => __( 'When enabled donors will be able to donate by chosing an option from the predefined/suggested amounts.', 'wc-donation-manager' ),
				'value'       => get_post_meta( get_the_ID(), 'wcdm_is_predefined_amounts', true ),
				'desc_tip'    => true,
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'          => 'wcdm_predefined_amounts_title',
				'label'       => __( 'Predefined amounts title', 'wc-donation-manager' ),
				'description' => __( 'Enter the title text of predefined/suggested amounts for the campaign.', 'wc-donation-manager' ),
				'desc_tip'    => false,
				'value'       => get_post_meta( get_the_ID(), 'wcdm_predefined_amounts_title', true ),
				'data_type'   => 'text',
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'            => 'wcdm_predefined_amounts',
				'label'         => __( 'Predefined amounts', 'wc-donation-manager' ),
				'description'   => __( 'Enter the list of predefined/suggested amounts for the campaign. Each amount should be separated by comma.', 'wc-donation-manager' ),
				'desc_tip'      => false,
				'value'         => implode( ',', (array) get_post_meta( get_the_ID(), 'wcdm_predefined_amounts', true ) ?? array() ),
				'data_type'     => 'text',
				'wrapper_class' => 'options_group',
			)
		);

		woocommerce_wp_checkbox(
			array(
				'id'            => 'wcdm_is_custom_amount',
				'label'         => __( 'Allow custom amount', 'wc-donation-manager' ),
				'description'   => __( 'When enabled donors will be able to donate the custom amount.', 'wc-donation-manager' ),
				'value'         => empty( get_post_meta( get_the_ID(), 'wcdm_is_custom_amount', true ) ) ? 'yes' : get_post_meta( get_the_ID(), 'wcdm_is_custom_amount', true ),
				'wrapper_class' => 'options_group',
				'desc_tip'      => true,
			)
		);

		$amount_increment = get_post_meta( get_the_ID(), 'wcdm_amount_increment_steps', true );
		woocommerce_wp_text_input(
			array(
				'id'          => 'wcdm_amount_increment_steps',
				'label'       => __( 'Amount increment steps', 'wc-donation-manager' ),
				'description' => __( 'Enter the amount increment steps for the campaign amount field. This will applicable for increasing or decreasing amounts on the campaign page.', 'wc-donation-manager' ),
				'desc_tip'    => false,
				'value'       => ( empty( $amount_increment ) ? 0.01 : $amount_increment ),
				'data_type'   => 'decimal',
			)
		);
		echo '</div>';

		echo '<div class="options_group show_if_donation">';
		woocommerce_wp_text_input(
			array(
				'id'          => 'wcdm_min_amount',
				'label'       => __( 'Minimum amount', 'wc-donation-manager' ),
				'description' => __( 'Enter the minimum amount for the campaign. Default amount comes from the plugin global settings.', 'wc-donation-manager' ),
				'desc_tip'    => true,
				'value'       => ! empty( get_post_meta( get_the_ID(), 'wcdm_min_amount', true ) ) ? get_post_meta( get_the_ID(), 'wcdm_min_amount', true ) : get_option( 'wcdm_minimum_amount' ),
				'data_type'   => 'price',
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'          => 'wcdm_max_amount',
				'label'       => __( 'Maximum amount', 'wc-donation-manager' ),
				'description' => __( 'Enter the maximum amount for the campaign. Default amount comes from the plugin global settings.', 'wc-donation-manager' ),
				'desc_tip'    => true,
				'value'       => ! empty( get_post_meta( get_the_ID(), 'wcdm_max_amount', true ) ) ? get_post_meta( get_the_ID(), 'wcdm_max_amount', true ) : get_option( 'wcdm_maximum_amount' ),
				'data_type'   => 'price',
			)
		);

		$args = array(
			'post_status' => 'publish',
		);

		$wcdm_campaigns   = wcdm_get_campaigns( $args );
		$campaign_options = array(
			'0' => 'Select a campaign',
		);
		foreach ( $wcdm_campaigns as $wcdm_campaign ) {
			$campaign_options[ $wcdm_campaign->ID ] = sprintf( '%1$s (%2$s)', $wcdm_campaign->post_title, $wcdm_campaign->ID );
		}

		woocommerce_wp_select(
			array(
				'id'          => 'wcdm_campaign_id',
				'label'       => __( 'Select a campaign', 'wc-donation-manager' ),
				'description' => __( 'Select a campaign to assign this donation product. After selected a campaign, the campaign cause & the goal amount will be inherited.', 'wc-donation-manager' ),
				'desc_tip'    => true,
				'options'     => $campaign_options,
				'value'       => ! empty( get_post_meta( get_the_ID(), 'wcdm_campaign_id', true ) ) ? get_post_meta( get_the_ID(), 'wcdm_campaign_id', true ) : '0',
			)
		);

		/**
		 * Hook for adding more campaign data.
		 *
		 * This hook allows developers to add more fields or data related to the campaign on the product edit page.
		 *
		 * @param int $post_id The ID of the current product post.
		 *
		 * @since 1.0.1
		 */
		do_action( 'wc_donation_manager_after_product_campaign_data', get_the_ID() );

		echo '</div></div>';
	}

	/**
	 * Save donation product meta.
	 *
	 * Runs on the legacy per-type save hook; only reachable while
	 * adding or editing a donation product.
	 *
	 * @param int $product_id Donation product id.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function save_donation_meta( $product_id ) {
		$this->save_donation_product_data( $product_id );
	}

	/**
	 * Save donation product data on the WooCommerce product object.
	 *
	 * @param \WC_Product $product Product object.
	 *
	 * @since 1.1.3
	 * @return void
	 */
	public function save_donation_product_object( $product ) {
		if ( ! $product instanceof \WC_Product || ! $product->is_type( 'donation' ) ) {
			return;
		}

		$price = $this->get_posted_price();
		$product->set_regular_price( $price );
		$product->set_price( $price );
		$this->save_donation_product_data( $product->get_id(), $product );
	}

	/**
	 * Save donation product fields.
	 *
	 * @param int              $product_id Donation product id.
	 * @param \WC_Product|null $product    Product object; set, the values land on the product's meta data.
	 *
	 * @since 1.1.3
	 * @return void
	 */
	private function save_donation_product_data( $product_id, $product = null ) {
		$price              = $this->get_posted_price();
		$predefined_amounts = ! empty( $_POST['wcdm_predefined_amounts'] ) ? explode( ',', preg_replace( '/\s*/m', '', sanitize_text_field( wp_unslash( $_POST['wcdm_predefined_amounts'] ) ) ) ) : array(); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- WooCommerce verifies the product save nonce before this hook fires.
		$predefined_amounts = array_filter( array_unique( $predefined_amounts ) );
		$meta_data          = array(
			'wcdm_is_predefined_amounts'    => isset( $_POST['wcdm_is_predefined_amounts'] ) ? sanitize_text_field( wp_unslash( $_POST['wcdm_is_predefined_amounts'] ) ) : '', // phpcs:ignore WordPress.Security.NonceVerification.Missing -- WooCommerce verifies the product save nonce before this hook fires.
			'wcdm_predefined_amounts_title' => ( ! empty( $_POST['wcdm_predefined_amounts_title'] ) ? sanitize_text_field( wp_unslash( $_POST['wcdm_predefined_amounts_title'] ) ) : __( 'Suggested amounts', 'wc-donation-manager' ) ), // phpcs:ignore WordPress.Security.NonceVerification.Missing -- WooCommerce verifies the product save nonce before this hook fires.
			'wcdm_predefined_amounts'       => $predefined_amounts,
			'wcdm_is_custom_amount'         => isset( $_POST['wcdm_is_custom_amount'] ) ? sanitize_text_field( wp_unslash( $_POST['wcdm_is_custom_amount'] ) ) : 'no', // phpcs:ignore WordPress.Security.NonceVerification.Missing -- WooCommerce verifies the product save nonce before this hook fires.
			'wcdm_amount_increment_steps'   => ( ! empty( $_POST['wcdm_amount_increment_steps'] ) && is_numeric( $_POST['wcdm_amount_increment_steps'] ) ? number_format( (float) wp_unslash( $_POST['wcdm_amount_increment_steps'] ), 2, '.', '' ) : 0.01 ), // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- WooCommerce verifies the product save nonce before this hook fires.
			'wcdm_min_amount'               => ( ! empty( $_POST['wcdm_min_amount'] ) && is_numeric( $_POST['wcdm_min_amount'] ) ? floatval( wp_unslash( $_POST['wcdm_min_amount'] ) ) : get_option( 'wcdm_minimum_amount' ) ), // phpcs:ignore WordPress.Security.NonceVerification.Missing -- WooCommerce verifies the product save nonce before this hook fires.
			'wcdm_max_amount'               => ( ! empty( $_POST['wcdm_max_amount'] ) && is_numeric( $_POST['wcdm_max_amount'] ) ? floatval( wp_unslash( $_POST['wcdm_max_amount'] ) ) : get_option( 'wcdm_maximum_amount' ) ), // phpcs:ignore WordPress.Security.NonceVerification.Missing -- WooCommerce verifies the product save nonce before this hook fires.
			'wcdm_campaign_id'              => ( ! empty( $_POST['wcdm_campaign_id'] ) ? sanitize_text_field( wp_unslash( $_POST['wcdm_campaign_id'] ) ) : '' ), // phpcs:ignore WordPress.Security.NonceVerification.Missing -- WooCommerce verifies the product save nonce before this hook fires.
		);

		if ( $product instanceof \WC_Product ) {
			foreach ( $meta_data as $key => $value ) {
				$product->update_meta_data( $key, $value );
			}

			return;
		}

		update_post_meta( $product_id, '_price', $price );
		update_post_meta( $product_id, '_regular_price', $price );

		foreach ( $meta_data as $key => $value ) {
			update_post_meta( $product_id, $key, $value );
		}
	}

	/**
	 * Get the posted donation price.
	 *
	 * @since 1.1.3
	 * @return float|string
	 */
	private function get_posted_price() {
		return isset( $_POST['wcdm_amount'] ) ? wc_format_decimal( sanitize_text_field( wp_unslash( $_POST['wcdm_amount'] ) ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- WooCommerce verifies the product save nonce before this hook fires.
	}

	/**
	 * Add custom admin script on product add/edit page.
	 *
	 * @version 1.0.0
	 * @return void
	 */
	public static function admin_custom_js() {
		global $pagenow, $typenow;
		if ( isset( $pagenow ) && ( 'post.php' === $pagenow || 'post-new.php' === $pagenow ) && isset( $typenow ) && 'product' === $typenow ) {
			?>
			<script type='text/javascript'>
				jQuery(document).ready(function () {
					jQuery('#woocommerce-product-data .type_box label[for=_downloadable].tips').addClass('show_if_donation').show();

					jQuery('#wcdm_is_predefined_amounts').on( 'change', function() {
						if ( jQuery(this).is(":checked") ) {
							jQuery( '.wcdm_predefined_amounts_title_field').show();
							jQuery( '.wcdm_predefined_amounts_field').show();
						} else {
							jQuery( '.wcdm_predefined_amounts_title_field').hide();
							jQuery( '.wcdm_predefined_amounts_field').hide();
						}
					}).trigger('change');
				});

			</script>
			<?php
		}
	}
}
