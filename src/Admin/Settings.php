<?php

namespace WooCommerceDonationManager\Admin;

use WooCommerceDonationManager\Lib;

defined( 'ABSPATH' ) || exit;

/**
 * Class Settings.
 *
 * @since   1.0.0
 * @package WooCommerceDonationManager\Admin
 */
class Settings extends Lib\Settings {

	/**
	 * Get settings tabs.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_tabs() {
		$tabs = array(
			'general'       => __( 'General', 'wc-donation-manager' ),
			'emails'        => __( 'Emails', 'wc-donation-manager' ),
			'fee_recurring' => __( 'Fee Recurring', 'wc-donation-manager' ),
			'advanced'      => __( 'Advanced', 'wc-donation-manager' ),
		);

		return apply_filters( 'wc_donation_manager_settings_tabs', $tabs );
	}

	/**
	 * Get settings.
	 *
	 * @param string $tab Current tab.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_settings( $tab ) {
		$settings = array();

		$pages        = get_pages();
		$page_options = array(
			'0' => 'Select a page',
		);

		foreach ( $pages as $page ) {
			$page_options[ $page->ID ] = $page->post_title;
		}

		switch ( $tab ) {
			case 'general':
				$settings = array(
					array(
						'title' => __( 'General Settings', 'wc-donation-manager' ),
						'type'  => 'title',
						'desc'  => __( 'The following options are the plugin general settings. Theses options affect how the plugin will work.', 'wc-donation-manager' ),
						'id'    => 'general_options',
					),
					array(
						'title'    => __( 'Success page', 'wc-donation-manager' ),
						'desc'     => __( 'Select a success page. This page will display after completed the donation process. Leave blank for default Woocommerce success page.', 'wc-donation-manager' ),
						'desc_tip' => __( 'Select a success page. This page will display after completed the donation process. Leave blank for default Woocommerce success page.', 'wc-donation-manager' ),
						'id'       => 'wcdm_success_page',
						'type'     => 'select',
						'options'  => $page_options,
						'default'  => '0',
					),
					array(
						'title'    => __( 'Donor wall page', 'wc-donation-manager' ),
						'desc'     => __( 'Select a donor wall page. This page will display donor wall details.', 'wc-donation-manager' ),
						'desc_tip' => __( 'Select a donor wall page. This page will display donor wall details.', 'wc-donation-manager' ),
						'id'       => 'wcdm_donor_wall_page',
						'type'     => 'select',
						'options'  => $page_options,
						'default'  => '0',
					),
					array(
						'type' => 'sectionend',
						'id'   => 'general_options',
					),
					array(
						'title' => __( 'Default Donation Settings', 'wc-donation-manager' ),
						'type'  => 'title',
						'desc'  => __( 'The following options are the plugin default donation settings. Theses options are the global settings for all the campaigns.', 'wc-donation-manager' ),
						'id'    => 'general_donation_options',
					),
					array(
						'title'    => __( 'Add to cart button text', 'wc-donation-manager' ),
						'id'       => 'wcdm_add_to_cart_btn_text',
						'desc'     => __( 'Enter the add to cart button text. This will be applicable only for campaigns or donation product types.', 'wc-donation-manager' ),
						'desc_tip' => __( 'Enter the add to cart button text. This will be applicable only for campaigns or donation product types.', 'wc-donation-manager' ),
						'type'     => 'text',
						'default'  => 'Donate Now',
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
						'title'    => __( 'Disabled coupon field', 'wc-donation-manager' ),
						'desc'     => __( 'Disabled coupon field.', 'wc-donation-manager' ),
						'desc_tip' => __( 'This will disabled coupon fields from cart and checkout page if cart has at least a donation product.', 'wc-donation-manager' ),
						'id'       => 'wcdm_disabled_coupon_field',
						'default'  => 'yes',
						'type'     => 'checkbox',
					),
					array(
						'title'   => __( 'Disabled order note', 'wc-donation-manager' ),
						'desc'    => __( 'Disabled order note.', 'wc-donation-manager' ),
						'id'      => 'wcdm_disabled_order_note',
						'default' => 'yes',
						'type'    => 'checkbox',
					),
					array(
						'title'    => __( 'Disabled tax', 'wc-donation-manager' ),
						'desc'     => __( 'Disabled tax for donation.', 'wc-donation-manager' ),
						'desc_tip' => __( 'Disabled the tax for donation product. This will hide tax status and tax class from product edit page as well if product type selected as donation.', 'wc-donation-manager' ),
						'id'       => 'wcdm_disabled_tax',
						'default'  => 'yes',
						'type'     => 'checkbox',
					),
					array(
						'title'    => __( 'Campaign expired text', 'wc-donation-manager' ),
						'desc'     => __( 'Enter the campaign expired text. This will be visible to the donation products if the campaign end date is exceeded.', 'wc-donation-manager' ),
						'desc_tip' => __( 'Enter the campaign expired text. This will be visible to the donation products if the campaign end date is exceeded.', 'wc-donation-manager' ),
						'id'       => 'wcdm_expired_text',
						'default'  => 'The campaign expired!',
						'type'     => 'text',
					),
					array(
						'type' => 'sectionend',
						'id'   => 'general_donation_options',
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
						'title'    => __( 'Delete plugin data', 'wc-email-attachments' ),
						'desc'     => __( 'Delete plugin data.', 'wc-email-attachments' ),
						'desc_tip' => __( 'Enabling this will delete all the data while uninstalling the plugin.', 'wc-email-attachments' ),
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
			case 'fee_recurring':
				$settings = array(
					array(
						'title' => __( 'Fee Recurring Settings', 'wc-donation-manager' ),
						'type'  => 'title',
						'desc'  => __( 'The following options are the fee recurring settings.', 'wc-donation-manager' ),
						'id'    => 'fee_recurring_options',
					),
					array(
						'title'   => __( 'Enable fee recurring', 'wc-donation-manager' ),
						'desc'    => __( 'Enable the donation fee recurring.', 'wc-donation-manager' ),
						'id'      => 'wcdm_enable_fee_recurring',
						'default' => 'yes',
						'type'    => 'checkbox',
					),
					array(
						'title'             => __( 'Default fee recurring', 'wc-donation-manager' ),
						'desc'              => __( 'Select the default donation fee recurring option.', 'wc-donation-manager' ),
						'desc_tip'          => __( 'Select the default donation fee recurring option.', 'wc-donation-manager' ),
						'id'                => 'wcdm_default_recurring',
						'type'              => 'select',
						'options'           => array(
							'onetime' => __( 'Onetime', 'wc-donation-manager' ),
						),
						'default'           => 'onetime',
						'custom_attributes' => array(
							'data-cond-id'    => 'wcdm_enable_fee_recurring',
							'data-cond-value' => 'yes',
						),
					),
					array(
						'type' => 'sectionend',
						'id'   => 'fee_recurring_options',
					),
				);
				break;
		}

		/**
		 * Filter the settings for the plugin.
		 *
		 * @param array $settings The settings.
		 * @param string $tab The current tab.
		 *
		 * @since 1.0.0
		 */
		return apply_filters( 'wc_donation_manager_get_settings_' . $tab, $settings );
	}

	/**
	 * Output settings form.
	 *
	 * @param array $settings Settings.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	protected function output_form( $settings ) {
		$current_tab = $this->get_current_tab();
		/**
		 * Action hook to output settings form.
		 *
		 * @since 1.0.0
		 */
		do_action( 'wc_donation_manager_settings_' . $current_tab );
		parent::output_form( $settings );
	}

	/**
	 * Output premium widget.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function output_premium_widget() {
		if ( wc_donation_manager()->is_premium_active() ) {
			return;
		}
		$features = array(
			__( 'Feature 1', 'wc-donation-manager' ),
			__( 'Feature 2', 'wc-donation-manager' ),
			__( 'Feature 3', 'wc-donation-manager' ),
			__( 'Many more ...', 'wc-donation-manager' ),
		);
		?>
		<div class="pev-panel promo-panel">
			<h3><?php esc_html_e( 'Want More?', 'wc-donation-manager' ); ?></h3>
			<p><?php esc_attr_e( 'This plugin offers a premium version which comes with the following features:', 'wc-donation-manager' ); ?></p>
			<ul>
				<?php foreach ( $features as $feature ) : ?>
					<li>- <?php echo esc_html( $feature ); ?></li>
				<?php endforeach; ?>
			</ul>
			<a href="https://pluginever.com/plugins/wc-donation-manager/?utm_source=plugin-settings&utm_medium=banner&utm_campaign=upgrade&utm_id=wc-donation-manager"
				class="button" target="_blank">
				<?php esc_html_e( 'Upgrade to PRO', 'wc-donation-manager' ); ?>
			</a>
		</div>
		<?php
	}

	/**
	 * Output tabs.
	 *
	 * @param array $tabs Tabs.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function output_tabs( $tabs ) {
		parent::output_tabs( $tabs );
		if ( wc_donation_manager()->get_docs_url() ) {
			printf( '<a href="%s" class="nav-tab" target="_blank">%s</a>', esc_url( wc_donation_manager()->get_docs_url() ), esc_html__( 'Documentation', 'wc-donation-manager' ) );
		}
	}
}
