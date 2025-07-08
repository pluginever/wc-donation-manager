<?php

namespace WooCommerceDonationManager\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Settings class.
 *
 * @since 1.0.0
 * @package WooCommerceDonationManager\Admin
 */
class Settings extends \WooCommerceDonationManager\ByteKit\Admin\Settings {

	/**
	 * Get settings tabs.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_tabs() {
		return apply_filters(
			'wc_donation_manager_settings_tabs',
			array(
				'general'  => __( 'General', 'wc-donation-manager' ),
				'advanced' => __( 'Advanced', 'wc-donation-manager' ),
				'emails'   => __( 'Emails', 'wc-donation-manager' ),
			)
		);
	}

	/**
	 * Get settings.
	 *
	 * @param string $tab Tab name.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_settings( $tab ) {
		$settings = array();
		switch ( $tab ) {
			case 'general':
				$settings = array(
					array(
						'title' => __( 'General Settings', 'wc-donation-manager' ),
						'type'  => 'title',
						'desc'  => __( 'The following options are the plugin\'s general settings. These options affect how the plugin will work.', 'wc-donation-manager' ),
						'id'    => 'general_options',
					),
					array(
						'title'    => __( 'Skip cart', 'wc-donation-manager' ),
						'desc'     => __( 'Skip cart.', 'wc-donation-manager' ),
						'desc_tip' => __( 'This will redirect donors to the cart page after adding a donation product to the cart item.', 'wc-donation-manager' ),
						'id'       => 'wcdm_skip_cart',
						'default'  => 'yes',
						'type'     => 'checkbox',
					),
					array(
						'title'    => __( 'Enable fast checkout', 'wc-donation-manager' ),
						'desc'     => __( 'Enable fast checkout.', 'wc-donation-manager' ),
						'desc_tip' => __( 'This will redirect donors to the checkout page after adding a donation product to the cart item.', 'wc-donation-manager' ),
						'id'       => 'wcdm_fast_checkout',
						'default'  => 'no',
						'type'     => 'checkbox',
					),
					array(
						'title'    => __( 'Editable cart item price', 'wc-donation-manager' ),
						'desc'     => __( 'Editable cart item price.', 'wc-donation-manager' ),
						'desc_tip' => __( 'This will make the cart item price editable for the donation products only.', 'wc-donation-manager' ),
						'id'       => 'wcdm_editable_cart_price',
						'default'  => 'yes',
						'type'     => 'checkbox',
					),
					array(
						'title'    => __( 'Disable coupon field', 'wc-donation-manager' ),
						'desc'     => __( 'Disable coupon field.', 'wc-donation-manager' ),
						'desc_tip' => __( 'This will disabled coupon fields from cart and checkout page if cart has at least a donation product.', 'wc-donation-manager' ),
						'id'       => 'wcdm_disabled_coupon_field',
						'default'  => 'yes',
						'type'     => 'checkbox',
					),
					array(
						'title'   => __( 'Disable order note', 'wc-donation-manager' ),
						'desc'    => __( 'Disable order note.', 'wc-donation-manager' ),
						'id'      => 'wcdm_disabled_order_note',
						'default' => 'yes',
						'type'    => 'checkbox',
					),
					array(
						'title'    => __( 'Disable tax', 'wc-donation-manager' ),
						'desc'     => __( 'Disable tax for donation.', 'wc-donation-manager' ),
						'desc_tip' => __( 'This will hide the tax status and tax class fields from the product edit page when the product type is set to <i>Donation</i>.', 'wc-donation-manager' ),
						'id'       => 'wcdm_disabled_tax',
						'default'  => 'yes',
						'type'     => 'checkbox',
					),
					array(
						'type' => 'sectionend',
						'id'   => 'general_options',
					),
					array(
						'title' => __( 'Customize Labels & Buttons', 'wc-donation-manager' ),
						'type'  => 'title',
						'id'    => 'section_customizable_options',
						'desc'  => __( 'The following options are the premium plugin general settings. ', 'wc-donation-manager' ),
					),
					array(
						'title'    => __( 'Add to cart button text', 'wc-donation-manager' ),
						'type'     => 'wcdm_add_to_cart_btn_text',
						'id'       => 'wcdm_add_to_cart_btn_text',
						'desc'     => __( 'Enter the add to cart button text. This will be applicable only for campaigns or donation product types.', 'wc-donation-manager' ),
						'desc_tip' => __( 'Enter the add to cart button text. This will be applicable only for campaigns or donation product types.', 'wc-donation-manager' ),
						'default'  => __( 'Donate Now', 'wc-donation-manager' ),
					),
					array(
						'title'    => __( 'Campaign expired text', 'wc-donation-manager' ),
						'id'       => 'wcdm_expired_text',
						'type'     => 'wcdm_expired_text',
						'desc'     => __( 'Enter the campaign expired text. This will be visible to the donation products if the campaign end date is exceeded.', 'wc-donation-manager' ),
						'desc_tip' => __( 'Enter the campaign expired text. This will be visible to the donation products if the campaign end date is exceeded.', 'wc-donation-manager' ),
						'default'  => 'The campaign expired!',
					),
					array(
						'type' => 'sectionend',
						'id'   => 'section_customizable_options',
					),
				);
				break;

			case 'advanced':
				$settings = array(
					array(
						'title' => __( 'Advanced Settings', 'wc-donation-manager' ),
						'type'  => 'title',
						'desc'  => __( 'The following options are the plugin advanced settings.', 'wc-donation-manager' ),
						'id'    => 'advanced_options',
					),
					array(
						'title'    => __( 'Minimum amount', 'wc-donation-manager' ),
						'desc'     => __( 'Enter the minimum amount. This will be apply globally if didn\'t set the minimum amount while creating campaigns.', 'wc-donation-manager' ),
						'desc_tip' => __( 'Enter the minimum amount. This will be apply globally if didn\'t set the minimum amount while creating campaigns.', 'wc-donation-manager' ),
						'id'       => 'wcdm_minimum_amount',
						'type'     => 'text',
						'default'  => '1',
					),
					array(
						'title'    => __( 'Maximum amount', 'wc-donation-manager' ),
						'desc'     => __( 'Enter the maximum amount. This will be apply globally if didn\'t set the maximum amount while creating campaigns.', 'wc-donation-manager' ),
						'desc_tip' => __( 'Enter the maximum amount. This will be apply globally if didn\'t set the maximum amount while creating campaigns.', 'wc-donation-manager' ),
						'id'       => 'wcdm_maximum_amount',
						'type'     => 'text',
						'default'  => '100',
					),
					array(
						'title'    => __( 'Delete plugin data', 'wc-donation-manager' ),
						'desc'     => __( 'Delete plugin data.', 'wc-donation-manager' ),
						'desc_tip' => __( 'Enabling this will delete all the data while uninstalling the plugin.', 'wc-donation-manager' ),
						'id'       => 'wcdm_delete_data',
						'default'  => 'no',
						'type'     => 'checkbox',
					),
					array(
						'type' => 'sectionend',
						'id'   => 'advanced_options',
					),
				);
				break;

			default:
				break;
		}

		/**
		 * Filter the settings for the plugin.
		 *
		 * @param array $settings The settings.
		 *
		 * @deprecated 1.0.0
		 */
		$settings = apply_filters( 'wc_donation_manager_' . $tab . '_settings', $settings );

		/**
		 * Filter the settings for the plugin.
		 *
		 * @param array  $settings The settings.
		 * @param string $tab The current tab.
		 *
		 * @since 1.0.0
		 */
		return apply_filters( 'wc_donation_manager_settings', $settings, $tab );
	}

	/**
	 * Output settings form.
	 *
	 * @param array $settings Settings.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function output_form( $settings ) {
		$current_tab = $this->get_current_tab();
		$hook        = 'wc_donation_manager_settings_' . $current_tab . '_content';
		if ( has_action( $hook ) ) {
			/**
			 * Action hook to output settings form.
			 *
			 * @since 1.0.0
			 */
			do_action( $hook );

			return;
		}
		parent::output_form( $settings );
	}
}
