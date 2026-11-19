<?php

namespace PluginEver\DonationManager;

defined( 'ABSPATH' ) || exit;

/**
 * Plugin settings.
 *
 * Declares every option in one place; the REST API and the React
 * settings screen render straight from this declaration. Fields marked
 * `gated` unlock with the Pro add-on; the free UI renders them disabled
 * behind the paywall.
 *
 * @since   2.0.0
 * @package PluginEver\DonationManager
 *
 * @extends B8\Settings<Plugin>
 */
class Settings extends B8\Settings {

	/**
	 * The WooCommerce email option the emails group reads and writes.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	const EMAIL_OPTION = 'woocommerce_customer_completed_donation_settings';

	/**
	 * App setting names to WooCommerce email option keys.
	 *
	 * @since 2.0.0
	 * @var array<string, string>
	 */
	const EMAIL_MAP = array(
		'email_enabled'            => 'enabled',
		'email_subject'            => 'subject',
		'email_heading'            => 'heading',
		'email_additional_content' => 'additional_content',
		'email_type'               => 'email_type',
	);

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
			'emails'   => array(
				'title'       => __( 'Emails', 'wc-donation-manager' ),
				'description' => __( 'The completed-donation email donors receive.', 'wc-donation-manager' ),
				'priority'    => 30,
				'fields'      => $this->get_emails_fields(),
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
			'customizable'          => array(
				'type'        => 'section',
				'title'       => __( 'Customize labels & buttons', 'wc-donation-manager' ),
				'description' => __( 'Text donors see on the donation product and campaign pages.', 'wc-donation-manager' ),
				'intent'      => 'promo',
			),
			'add_to_cart_btn_text'  => array(
				'type'     => 'text',
				'label'    => __( 'Add to cart button text', 'wc-donation-manager' ),
				'help'     => __( 'Overrides the add-to-cart button label on donation products and campaigns.', 'wc-donation-manager' ),
				'default'  => __( 'Donate Now', 'wc-donation-manager' ),
				'sanitize' => 'text',
				'section'  => 'customizable',
				'width'    => 'full',
				'gated'    => true,
			),
			'expired_text'          => array(
				'type'     => 'text',
				'label'    => __( 'Campaign expired text', 'wc-donation-manager' ),
				'help'     => __( 'Shown on donation products whose campaign end date has passed.', 'wc-donation-manager' ),
				'default'  => __( 'The campaign expired!', 'wc-donation-manager' ),
				'sanitize' => 'text',
				'section'  => 'customizable',
				'width'    => 'full',
				'gated'    => true,
			),
		);
	}

	/**
	 * Get the emails fields.
	 *
	 * The values live in WooCommerce's own email option, so the email
	 * keeps working everywhere WooCommerce manages emails.
	 *
	 * @since 2.0.0
	 * @return array<string, array<string, mixed>> Fields keyed by name.
	 */
	protected function get_emails_fields(): array {
		return array(
			'email_enabled'            => array(
				'type'     => 'switch',
				'label'    => __( 'Enable', 'wc-donation-manager' ),
				'help'     => __( 'Send donors this email when their donation order completes.', 'wc-donation-manager' ),
				'default'  => 'yes',
				'sanitize' => 'yes_no',
				'width'    => 'full',
				'gated'    => true,
			),
			'email_subject'            => array(
				'type'     => 'text',
				'label'    => __( 'Subject', 'wc-donation-manager' ),
				'help'     => __( 'Available placeholders: {site_title}, {site_address}, {order_date}, {order_number}.', 'wc-donation-manager' ),
				'default'  => __( 'Your {site_title} donation has been received!', 'wc-donation-manager' ),
				'sanitize' => 'text',
				'width'    => 'full',
				'gated'    => true,
			),
			'email_heading'            => array(
				'type'     => 'text',
				'label'    => __( 'Email heading', 'wc-donation-manager' ),
				'default'  => __( 'Thank you for your donation', 'wc-donation-manager' ),
				'sanitize' => 'text',
				'width'    => 'full',
				'gated'    => true,
			),
			'email_additional_content' => array(
				'type'     => 'textarea',
				'label'    => __( 'Additional content', 'wc-donation-manager' ),
				'help'     => __( 'Text to appear below the main email content. Available placeholders: {site_title}, {site_address}, {order_date}, {order_number}.', 'wc-donation-manager' ),
				'sanitize' => 'textarea',
				'width'    => 'full',
				'props'    => array( 'rows' => 4 ),
				'gated'    => true,
			),
			'email_type'               => array(
				'type'     => 'select',
				'label'    => __( 'Email type', 'wc-donation-manager' ),
				'default'  => 'html',
				'options'  => array(
					array(
						'value' => 'html',
						'label' => __( 'HTML', 'wc-donation-manager' ),
					),
					array(
						'value' => 'plain',
						'label' => __( 'Plain text', 'wc-donation-manager' ),
					),
					array(
						'value' => 'multipart',
						'label' => __( 'Multipart', 'wc-donation-manager' ),
					),
				),
				'sanitize' => 'enum:html,plain,multipart',
				'width'    => 'full',
				'gated'    => true,
			),
		);
	}

	/**
	 * Get the current values, typed.
	 *
	 * The emails group reads WooCommerce's own email option, so the
	 * values stay in one place everywhere WooCommerce manages emails.
	 *
	 * @since 2.0.0
	 * @return array<string, mixed> Values keyed by name, falling back to defaults.
	 */
	public function get_values(): array {
		$values = parent::get_values();

		$stored = get_option( self::EMAIL_OPTION, false );

		if ( is_array( $stored ) && $stored ) {
			foreach ( self::EMAIL_MAP as $name => $key ) {
				if ( isset( $stored[ $key ] ) && '' !== $stored[ $key ] ) {
					$values[ $name ] = $stored[ $key ];
				}
			}
		}

		return $values;
	}

	/**
	 * Save the submitted values.
	 *
	 * The emails group writes into WooCommerce's own email option;
	 * everything else goes through the regular option store.
	 *
	 * @since 2.0.0
	 * @param array<string, mixed>                $values Values keyed by name; a name not sent is left untouched.
	 * @param array<string, array<string, mixed>> $fields Optional. Fields to save. Default all of them.
	 * @return int Number of values written.
	 */
	public function save_values( array $values, array $fields = array() ): int {
		$email_values = array_intersect_key( $values, self::EMAIL_MAP );
		$saved        = 0;

		if ( $email_values ) {
			$stored = get_option( self::EMAIL_OPTION, array() );
			$stored = is_array( $stored ) ? $stored : array();

			foreach ( $email_values as $name => $value ) {
				$key = self::EMAIL_MAP[ $name ];

				switch ( $key ) {
					case 'enabled':
						$stored[ $key ] = 'yes' === $value ? 'yes' : 'no';
						break;
					case 'additional_content':
						$stored[ $key ] = sanitize_textarea_field( (string) $value );
						break;
					case 'email_type':
						$stored[ $key ] = in_array( $value, array( 'html', 'plain', 'multipart' ), true ) ? $value : 'html';
						break;
					default:
						$stored[ $key ] = sanitize_text_field( (string) $value );
						break;
				}

				unset( $values[ $name ] );
				++$saved;
			}

			update_option( self::EMAIL_OPTION, $stored );
		}

		return $saved + parent::save_values( $values, $fields );
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
				'default'  => 1000,
				'sanitize' => 'float',
				'section'  => 'amounts',
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
