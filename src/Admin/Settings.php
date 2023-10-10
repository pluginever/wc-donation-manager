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
			'general'  => __( 'General', 'wc-donation-manager' ),
			'advanced' => __( 'Advanced', 'wc-donation-manager' ),
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

		switch ( $tab ) {
			case 'general':
				$settings = array(
					[
						'title' => __( 'General settings', 'wc-donation-manager' ),
						'type'  => 'title',
						'desc'  => __( 'The following options affect how the plugin will work.', 'wc-donation-manager' ),
						'id'    => 'general_options',
					],
					[
						'title'       => __( 'Example field', 'wc-donation-manager' ),
						'id'          => 'wcdm_example_field',
						'desc'        => __( 'This is an example field.', 'wc-donation-manager' ),
						'desc_tip'    => __( 'This is an example field.', 'wc-donation-manager' ),
						'type'        => 'text',
						'default'     => 'I am a default value',
						'placeholder' => 'I am a placeholder',
					],
					[
						'type' => 'sectionend',
						'id'   => 'general_options',
					],
				);
				break;
			case 'advanced':
				$settings = array(
					[
						'title' => __( 'Advanced settings', 'wc-donation-manager' ),
						'type'  => 'title',
						'desc'  => __( 'The following options affect how the plugin will work.', 'wc-donation-manager' ),
						'id'    => 'advanced_options',
					],
					[
						'title'       => __( 'Example field', 'wc-donation-manager' ),
						'id'          => 'wcdm_example_field',
						'desc'        => __( 'This is an example field.', 'wc-donation-manager' ),
						'desc_tip'    => __( 'This is an example field.', 'wc-donation-manager' ),
						'type'        => 'text',
						'default'     => 'I am a default value',
						'placeholder' => 'I am a placeholder',
					],
					[
						'type' => 'sectionend',
						'id'   => 'advanced_options',
					],
				);
				break;
		}

		return apply_filters( 'wc_donation_manager_get_settings_' . $tab, $settings );
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
			<a href="https://pluginever.com/plugins/wc-donation-manager/?utm_source=plugin-settings&utm_medium=banner&utm_campaign=upgrade&utm_id=wc-donation-manager" class="button" target="_blank">
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
			echo sprintf( '<a href="%s" class="nav-tab" target="_blank">%s</a>', esc_url( wc_donation_manager()->get_docs_url() ), esc_html__( 'Documentation', 'wc-donation-manager' ) );
		}
	}
}
