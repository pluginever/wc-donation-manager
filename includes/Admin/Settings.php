<?php

namespace PluginEver\DonationManager\Admin;

use PluginEver\DonationManager\B8\SettingsUI;

defined( 'ABSPATH' ) || exit;

/**
 * Settings class.
 *
 * Renders the tabbed settings screen through the framework SettingsUI while
 * persisting/rendering the fields with WooCommerce so existing `wcdm_*` option
 * keys and custom field types keep working.
 *
 * @since 1.0.0
 * @package PluginEver\DonationManager\Admin
 */
class Settings extends SettingsUI {

	/**
	 * Capability required to manage the settings.
	 *
	 * @since 1.1.3
	 * @var string
	 */
	protected string $capability = 'manage_options';

	/**
	 * Register hooks.
	 *
	 * @since 1.1.3
	 * @return void
	 */
	public function register(): void {
		$this->app->on_filter( 'settings', array( $this, 'register_settings' ) );
	}

	/**
	 * Register the plugin settings.
	 *
	 * @since 1.1.3
	 * @param array<string, mixed> $settings Settings definition keyed by tab.
	 * @return array<string, mixed>
	 */
	public function register_settings( array $settings ): array {
		$settings['general'] = array(
			'title'  => __( 'General', 'wc-donation-manager' ),
			'fields' => array(
				array(
					'title' => __( 'General Settings', 'wc-donation-manager' ),
					'type'  => 'title',
					'desc'  => __( 'The following options are the plugin\'s general settings. These options affect how the plugin will work.', 'wc-donation-manager' ),
					'id'    => 'general_options',
				),
				array(
					'title'    => __( 'Skip to Cart', 'wc-donation-manager' ),
					'desc'     => __( 'Skip to Cart.', 'wc-donation-manager' ),
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
			),
		);

		$settings['advanced'] = array(
			'title'  => __( 'Advanced', 'wc-donation-manager' ),
			'fields' => array(
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
					'title'   => __( 'Donation behaviour after goal completion', 'wc-donation-manager' ),
					'desc'    => __( 'Define what should happen when a campaign reaches its goal.', 'wc-donation-manager' ),
					'id'      => 'wcdm_goal_behavior',
					'type'    => 'select',
					'default' => 'continue',
					'options' => array(
						'continue'   => __( 'Continue accepting donations', 'wc-donation-manager' ),
						'close'      => __( 'Close campaign automatically', 'wc-donation-manager' ),
						'soft_close' => __( 'Show as completed but still allow donations', 'wc-donation-manager' ),
					),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'advanced_options',
				),
			),
		);

		$settings['emails'] = array(
			'title'  => __( 'Emails', 'wc-donation-manager' ),
			'fields' => array(),
		);

		return $settings;
	}

	/**
	 * Output the settings fields through WooCommerce.
	 *
	 * @since 1.1.3
	 * @param array<int, array<string, mixed>> $fields Prepared field declarations.
	 * @return void
	 */
	protected function render_fields( array $fields ): void {
		if ( function_exists( 'woocommerce_admin_fields' ) ) {
			woocommerce_admin_fields( $fields );
			return;
		}

		parent::render_fields( $fields );
	}

	/**
	 * Persist the submitted settings fields through WooCommerce.
	 *
	 * @since 1.1.3
	 * @param array<int, array<string, mixed>> $fields Field declarations for the current tab.
	 * @param array<string, mixed>             $data   Unslashed request data.
	 * @return bool True when the fields were saved.
	 */
	protected function save_fields( array $fields, array $data ): bool {
		if ( ! function_exists( 'woocommerce_update_options' ) ) {
			return false;
		}

		woocommerce_update_options( $fields );

		return true;
	}
}
