<?php

namespace PluginEver\DonationManager;

defined( 'ABSPATH' ) || exit;

/**
 * Plugin settings.
 *
 * Declares every option in one place; the REST API and the React
 * settings screen render straight from this declaration.
 *
 * @since   2.0.0
 * @package PluginEver\DonationManager
 *
 * @extends B8\Settings<Plugin>
 */
class Settings extends B8\Settings {

	/**
	 * Define the plugin settings.
	 *
	 * @since 2.0.0
	 * @return array<string, mixed> Settings definition keyed by group.
	 */
	protected function define_settings(): array {
		return array(
			'general'  => array(
				'title'       => __( 'General', 'wc-donation-manager' ),
				'description' => __( 'Cart, checkout and donation behaviour.', 'wc-donation-manager' ),
				'priority'    => 10,
				'fields'      => $this->get_general_fields(),
			),
			'advanced' => array(
				'title'       => __( 'Advanced', 'wc-donation-manager' ),
				'description' => __( 'Global amounts, goal behaviour and data removal.', 'wc-donation-manager' ),
				'priority'    => 200,
				'fields'      => $this->get_advanced_fields(),
			),
		);
	}

	/**
	 * Get the general fields.
	 *
	 * @since 2.0.0
	 * @return array<string, array<string, mixed>> Fields keyed by name.
	 */
	protected function get_general_fields(): array {
		return array(
			'checkout'              => array(
				'type'        => 'section',
				'title'       => __( 'Checkout', 'wc-donation-manager' ),
				'description' => __( 'How donation products move through the cart and checkout.', 'wc-donation-manager' ),
			),
			'skip_cart'             => array(
				'type'     => 'switch',
				'label'    => __( 'Skip to cart', 'wc-donation-manager' ),
				'help'     => __( 'Redirect donors to the cart page after a donation product is added to the cart.', 'wc-donation-manager' ),
				'default'  => 'yes',
				'sanitize' => 'yes_no',
				'section'  => 'checkout',
			),
			'fast_checkout'         => array(
				'type'     => 'switch',
				'label'    => __( 'Fast checkout', 'wc-donation-manager' ),
				'help'     => __( 'Redirect donors straight to the checkout page after a donation product is added to the cart.', 'wc-donation-manager' ),
				'default'  => 'no',
				'sanitize' => 'yes_no',
				'section'  => 'checkout',
			),
			'editable_cart_price'   => array(
				'type'     => 'switch',
				'label'    => __( 'Editable cart item price', 'wc-donation-manager' ),
				'help'     => __( 'Let donors edit the donation amount from the cart page. Applies to donation products only.', 'wc-donation-manager' ),
				'default'  => 'yes',
				'sanitize' => 'yes_no',
				'section'  => 'checkout',
			),
			'disabled_coupon_field' => array(
				'type'     => 'switch',
				'label'    => __( 'Disable coupon field', 'wc-donation-manager' ),
				'help'     => __( 'Hide the coupon field on the cart and checkout pages while the cart holds a donation product.', 'wc-donation-manager' ),
				'default'  => 'yes',
				'sanitize' => 'yes_no',
				'section'  => 'checkout',
			),
			'disabled_order_note'   => array(
				'type'     => 'switch',
				'label'    => __( 'Disable order note', 'wc-donation-manager' ),
				'help'     => __( 'Hide the order note field on the checkout page while the cart holds a donation product.', 'wc-donation-manager' ),
				'default'  => 'yes',
				'sanitize' => 'yes_no',
				'section'  => 'checkout',
			),
			'tax'                   => array(
				'type'  => 'section',
				'title' => __( 'Tax', 'wc-donation-manager' ),
			),
			'disabled_tax'          => array(
				'type'     => 'switch',
				'label'    => __( 'Disable tax', 'wc-donation-manager' ),
				'help'     => __( 'Hide the tax status and tax class fields on the product edit page while the product type is Donation.', 'wc-donation-manager' ),
				'default'  => 'yes',
				'sanitize' => 'yes_no',
				'section'  => 'tax',
			),
		);
	}

	/**
	 * Get the advanced fields.
	 *
	 * @since 2.0.0
	 * @return array<string, array<string, mixed>> Fields keyed by name.
	 */
	protected function get_advanced_fields(): array {
		return array(
			'amounts'        => array(
				'type'        => 'section',
				'title'       => __( 'Donation amounts', 'wc-donation-manager' ),
				'description' => __( 'Applied globally when a campaign or product sets no limits of its own.', 'wc-donation-manager' ),
			),
			'minimum_amount' => array(
				'type'     => 'number',
				'label'    => __( 'Minimum amount', 'wc-donation-manager' ),
				'help'     => __( 'Lowest donation amount accepted when a campaign sets no minimum of its own.', 'wc-donation-manager' ),
				'default'  => 1,
				'sanitize' => 'float',
				'section'  => 'amounts',
			),
			'maximum_amount' => array(
				'type'     => 'number',
				'label'    => __( 'Maximum amount', 'wc-donation-manager' ),
				'help'     => __( 'Highest donation amount accepted when a campaign sets no maximum of its own.', 'wc-donation-manager' ),
				'default'  => 100,
				'sanitize' => 'float',
				'section'  => 'amounts',
			),
			'campaigns'      => array(
				'type'  => 'section',
				'title' => __( 'Campaigns', 'wc-donation-manager' ),
			),
			'goal_behavior'  => array(
				'type'     => 'select',
				'label'    => __( 'After goal completion', 'wc-donation-manager' ),
				'help'     => __( 'What happens when a campaign reaches its goal.', 'wc-donation-manager' ),
				'default'  => 'continue',
				'options'  => array(
					array(
						'value' => 'continue',
						'label' => __( 'Continue accepting donations', 'wc-donation-manager' ),
					),
					array(
						'value' => 'close',
						'label' => __( 'Close the campaign automatically', 'wc-donation-manager' ),
					),
					array(
						'value' => 'soft_close',
						'label' => __( 'Show as completed but still allow donations', 'wc-donation-manager' ),
					),
				),
				'sanitize' => 'enum:continue,close,soft_close',
				'section'  => 'campaigns',
			),
			'data'           => array(
				'type'        => 'section',
				'title'       => __( 'Danger zone', 'wc-donation-manager' ),
				'description' => __( 'Removed data cannot be recovered.', 'wc-donation-manager' ),
				'intent'      => 'danger',
			),
			'delete_data'    => array(
				'type'       => 'switch',
				'label'      => __( 'Uninstall', 'wc-donation-manager' ),
				'help'       => __( 'Delete every campaign, setting and donation record when the plugin is uninstalled. Deactivating the plugin leaves everything in place.', 'wc-donation-manager' ),
				'default'    => 'no',
				'sanitize'   => 'yes_no',
				'section'    => 'data',
				'capability' => 'manage_options',
			),
		);
	}
}
