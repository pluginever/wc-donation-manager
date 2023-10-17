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
		add_filter('woocommerce_product_options_general_product_data', array( __CLASS__, 'general_product_data' ) );
		add_action( 'woocommerce_process_product_meta_donation', array( __CLASS__, 'save_product_meta' ) );

		add_action('admin_footer', array( __CLASS__, 'admin_custom_js' ) );
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
				$tabs[$tab_ID]['class'][] = 'hide_if_donation';
			}
		}

		$tabs['wc_donation_manager'] =  array(
			'label'    => __( 'Donation Manager', 'wc-donation-manager' ),
			'target'   => 'wcdm_form_data',
			'class'    => array( 'show_if_donation', 'hidden', 'hide_if_external' ),
			'priority' => 12,
		);

		return $tabs;
	}

	/**
	 * Add fields to the General product data tab.
	 *
	 * @version 1.0.0
	 * @return void
	 */
	public static function general_product_data() {
		echo '<div class="options_group show_if_donation">';
		woocommerce_wp_text_input( array(
			'id' => 'donation_default_amount',
			'label' => esc_html__('Default amount',
				'donations-for-woocommerce', 'wc-donation-manager'),
			'value' => get_post_meta( get_the_ID(), '_price', true ),
			'data_type' => 'price'
		));
		$donationAmountIncrement = get_post_meta( get_the_ID(), 'wcdm_donation_amount_increment', true);
		woocommerce_wp_text_input( array(
			'id' => 'donation_amount_increment',
			'label' => esc_html__('Amount increment',
				'donations-for-woocommerce', 'wc-donation-manager'),
			'value' => ( empty( $donationAmountIncrement ) ? 0.01 : $donationAmountIncrement ),
			'data_type' => 'decimal'
		));
		echo '</div>';
	}

	/**
	 * Save donation product meta.
	 * the method only callable while adding donation type products.
	 *
	 * @param int $product_Id donation product id.
	 * @version 1.0.0
	 * @return void
	 */
	public static function save_product_meta( $product_ID ) {
		$price = ( $_POST['donation_default_amount'] === '' ) ? '' : wc_format_decimal( $_POST['donation_default_amount'] );
		update_post_meta( $product_ID, '_price', $price );
		update_post_meta( $product_ID, '_regular_price', $price );
		update_post_meta( $product_ID, '_donation_amount_increment', ( !empty( $_POST['donation_amount_increment'] ) && is_numeric( $_POST['donation_amount_increment'] ) ? number_format( $_POST['donation_amount_increment'], 2, '.', '' ) : 0.01 ) );
	}


	// Show taxes options

	public static function admin_custom_js() {

		global $pagenow, $typenow;
		if ( isset($pagenow) && $pagenow == 'post.php' && isset($typenow) && $typenow == 'product' )   {
			?>
			<script type='text/javascript'>

				jQuery(document).ready( function () {
					<?php //  if ( hm_wcdon_get_option('show_tax_donation_product' )) { ?>
					jQuery('#general_product_data ._tax_status_field').parent().addClass('show_if_donation').show();
					<?php // } ?>
					jQuery('#woocommerce-product-data .type_box label[for=_downloadable].tips').addClass('show_if_donation').show();
				})

			</script>
			<?php
		}
	}
}
