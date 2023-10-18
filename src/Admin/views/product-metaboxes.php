<?php
/**
 * View: Product Metabox Fields
 *
 * @since 1.0.0
 * @subpackage Admin/Views
 * @package WooCommerceDonationManager
 * @var $product \WC_Product Products object.
 */

defined( 'ABSPATH' ) || exit;
?>
<div id="wcdm_tab_data" class="panel woocommerce_options_panel wcdm_tab_data_options">
	<?php
	echo '<div class="options_group show_if_donation">';

	$store_timer = get_post_meta( $product->get_id(), 'wcdm_settings_option', true );
	woocommerce_wp_radio(
		array(
			'id'          => 'wcdm_settings_option',
			'label'       => __( 'Donation Manager Settings', 'wc-donation-manager' ),
			'description' => __( 'Customize store timer or use global setting.', 'wc-donation-manager' ),
			'desc_tip'    => true,
			'value'       => empty( $store_timer ) ? 'global' : $store_timer,
			'options'     => array(
				'global'    => __( 'Global Setting', 'wc-donation-manager' ),
				'customize' => __( 'Customize', 'wc-donation-manager' ),
			),
		)
	);

	woocommerce_wp_select(
		array(
			'id'          => 'wcdm_time_condition',
			'label'       => __( 'Time condition', 'wc-donation-manager' ),
			'description' => __( 'Select a time range to disable store purchase.', 'wc-donation-manager' ),
			'desc_tip'    => true,
			'options'     => array(
				'date'           => __( 'Date', 'wc-donation-manager' ),
				'weekly'         => __( 'Weekly on ever [Saturday - Sunday] ', 'wc-donation-manager' ),
				'monthly'        => __( 'Monthly on every [1-31]', 'wc-donation-manager' ),
				'month_of'       => __( 'On the month of [January - December]', 'wc-donation-manager' ),
				'public_holiday' => __( 'Public Holidays [US - Canada]', 'wc-donation-manager' ),
			),
			'value'       => get_post_meta( $product->get_id(), 'wcdm_time_condition', true ),
		)
	);

	woocommerce_wp_text_input(
		array(
			'id'          => 'wcdm_date_start',
			'label'       => __( 'Start date', 'wc-donation-manager' ),
			'description' => __( 'Date start for this product to show in store', 'wc-donation-manager' ),
			'type'        => 'date',
			'value'       => get_post_meta( $product->get_id(), 'wcdm_date_start', true ),
		)
	);

	woocommerce_wp_text_input(
		array(
			'id'          => 'wcdm_date_end',
			'label'       => __( 'End date', 'wc-donation-manager' ),
			'description' => __( 'Date end for this product to show in store', 'wc-donation-manager' ),
			'type'        => 'date',
			'value'       => get_post_meta( $product->get_id(), 'wcdm_date_end', true ),
		)
	);
	woocommerce_wp_select(
		array(
			'id'                => 'wcdm_days_purchase',
			'name'              => 'wcdm_days_purchase[]',
			'label'             => '',
			'description'       => __( 'Select day or multiple days for every weeks.', 'wc-donation-manager' ),
			'default'           => get_post_meta( $product->get_id(), 'wcdm_days_purchase', true ),
			'desc_tip'          => 'true',
			'class'             => 'wcdm_days_purchase',
			'options'           => array(
				'monday'    => 'Monday',
				'tuesday'   => 'Tuesday',
				'wednesday' => 'Wednesday',
				'thursday'  => 'Thursday',
				'friday'    => 'Friday',
				'saturday'  => 'Saturday',
				'sunday'    => 'Sunday',
			),
			'custom_attributes' => array(
				'multiple' => 'multiple',
			),
		)
	);

	woocommerce_wp_select(
		array(
			'id'                => 'wcdm_months_purchase',
			'name'              => 'wcdm_months_purchase[]',
			'label'             => '',
			'description'       => __( 'Select day or multiple days for every weeks.', 'wc-donation-manager' ),
			'default'           => get_post_meta( $product->get_id(), 'wcdm_months_purchase', true ),
			'desc_tip'          => 'true',
			'class'             => 'wcdm_months_purchase',
			'options'           => array(
				'january'   => __( 'January', 'wc-donation-manager' ),
				'february'  => __( 'February', 'wc-donation-manager' ),
			),
			'custom_attributes' => array(
				'multiple' => 'multiple',
			),
		)
	);

	woocommerce_wp_select(
		array(
			'id'                => 'wcdm_month_day_purchase',
			'name'              => 'wcdm_month_day_purchase[]',
			'label'             => '',
			'description'       => __( 'Select day or multiple days for every weeks.', 'wc-donation-manager' ),
			'default'           => get_post_meta( $product->get_id(), 'wcdm_month_day_purchase', true ),
			'desc_tip'          => 'true',
			'class'             => 'wcdm_month_day_purchase',
			'options'           => array(
				'1'  => __( '1', 'wc-donation-manager' ),
				'2'  => __( '2', 'wc-donation-manager' ),
				'3'  => __( '3', 'wc-donation-manager' ),
			),
			'custom_attributes' => array(
				'multiple' => 'multiple',
			),
		)
	);

	woocommerce_wp_textarea_input(
		array(
			'id'          => 'wcdm_notice_text',
			'label'       => __( 'Notice Text', 'wc-donation-manager' ),
			'description' => __( 'Prefix for the ticket number. Leave blank for no prefix.', 'wc-donation-manager' ),
			'desc_tip'    => true,
			'placeholder' => 'e.g. PREFIX',
			'type'        => 'text',
			'value'       => get_post_meta( $product->get_id(), 'wcdm_notice_text', true ),
			'class'       => 'short',
		)
	);

	echo '</div>';
	?>
</div>
