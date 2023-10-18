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
		add_filter( 'woocommerce_product_options_general_product_data', array( __CLASS__, 'general_product_data' ) );
		add_action( 'woocommerce_product_data_panels', array( __CLASS__, 'product_data' ) );
		add_action( 'admin_footer', array( __CLASS__, 'admin_custom_js' ) );
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
		foreach ( $tabs as $tab_ID => $tab_data ) {
			if ( $tab_ID != 'general' && $tab_ID != 'advanced' ) {
				$tabs[ $tab_ID ]['class'][] = 'hide_if_donation';
			}
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
	 * Add fields to the general product data.
	 *
	 * @version 1.0.0
	 * @return void
	 */
	public static function general_product_data() {
		echo '<div class="options_group show_if_donation">';
		woocommerce_wp_text_input( array(
			'id'          => 'wcdm_amount',
			'label'       => __( 'Default amount', 'wc-donation-manager' ),
			'description' => __( 'Enter the default amount for the donation.', 'wc-donation-manager' ),
			'desc_tip'    => false,
			'value'       => get_post_meta( get_the_ID(), '_price', true ),
			'data_type'   => 'price',
		) );
		woocommerce_wp_text_input( array(
			'id'        => 'wcdm_goal_amount',
			'label'     => __( 'Goal amount', 'wc-donation-manager' ),
			'value'     => get_post_meta( get_the_ID(), 'wcdm_goal_amount', true ),
			'data_type' => 'price',
		) );
		$amount_increment = get_post_meta( get_the_ID(), 'wcdm_amount_increment_steps', true );
		woocommerce_wp_text_input( array(
			'id'        => 'wcdm_amount_increment_steps',
			'label'     => __( 'Amount increment steps', 'wc-donation-manager' ),
			'value'     => ( empty( $amount_increment ) ? 0.01 : $amount_increment ),
			'data_type' => 'decimal',
		) );
		echo '</div>';
	}

	/**
	 * Add fields to the product data panel.
	 *
	 * @version 1.0.0
	 * @return void
	 */
	public static function product_data() {
	echo '<div id="wcdm_tab_data" class="panel woocommerce_options_panel wcdm_tab_data_options"><div class="options_group show_if_donation">';
		// TODO: Need to add metabox fields.
	echo '</div></div>';


//		global $post;
//		$product = wc_get_product( $post->ID );
//		include __DIR__ . '/views/product-metaboxes.php';
	}

	/**
	 * Add custom admin script on product add/edit page.
	 *
	 * @version 1.0.0
	 * @return void
	 */
	public static function admin_custom_js() {
		global $pagenow, $typenow;
		if ( isset( $pagenow ) && $pagenow == 'post.php' && isset( $typenow ) && $typenow == 'product' ) {
			?>
			<script type='text/javascript'>
				jQuery(document).ready(function () {
					<?php if ( 'yes' != get_option( 'wcdm_disabled_tax', 'yes' ) ) { ?>
					jQuery('#general_product_data ._tax_status_field').parent().addClass('show_if_donation').show();
					<?php } ?>
					jQuery('#woocommerce-product-data .type_box label[for=_downloadable].tips').addClass('show_if_donation').show();
				})
			</script>
			<?php
		}
	}
}
