<?php

namespace WooCommerceDonationManager\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Menus class.
 *
 * @since 1.0.0
 * @package WooCommerceDonationManager
 */
class Menus {

	/**
	 * Menus constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'main_menu' ) );
		add_action( 'admin_menu', array( $this, 'settings_menu' ), 100 );
		add_action( 'wc_donation_manager_campaigns_content', array( $this, 'render_campaigns_content' ) );
		add_action( 'wc_donation_manager_donors_content', array( $this, 'render_donors_content' ) );
		add_action( 'wc_donation_manager_settings_emails', array( $this, 'render_emails_content' ) );
	}

	/**
	 * Main menu.
	 *
	 * @since 1.0.0
	 */
	public function main_menu() {
		add_menu_page(
			esc_html__( 'Donations', 'wc-donation-manager' ),
			esc_html__( 'Donations', 'wc-donation-manager' ),
			'manage_options',
			'wc-donation-manager',
			null,
			'dashicons-money-alt',
			'55.5'
		);

		add_submenu_page(
			'wc-donation-manager',
			esc_html__( 'Campaigns', 'wc-donation-manager' ),
			esc_html__( 'Campaigns', 'wc-donation-manager' ),
			'manage_options',
			'wc-donation-manager',
			array( $this, 'output_main_page' )
		);

		add_submenu_page(
			'wc-donation-manager',
			esc_html__( 'Donors', 'wc-donation-manager' ),
			esc_html__( 'Donors', 'wc-donation-manager' ),
			'manage_options',
			'wcdm-donors',
			array( $this, 'output_donors_page' )
		);
	}

	/**
	 * Settings menu.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function settings_menu() {
		add_submenu_page(
			'wc-donation-manager',
			__( 'Settings', 'wc-donation-manager' ),
			__( 'Settings', 'wc-donation-manager' ),
			'manage_options',
			'wcdm-settings',
			array( Settings::class, 'output' )
		);
	}

	/**
	 * Output main page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function output_main_page() {
		$page_hook = 'campaigns';
		include __DIR__ . '/views/admin-page.php';
	}

	/**
	 * Output donors page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function output_donors_page() {
		$page_hook = 'donors';
		include __DIR__ . '/views/admin-page.php';
	}

	/**
	 * Render campaigns content.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function render_campaigns_content() {
		$add_campaign  = isset( $_GET['new'] ) ? true : false; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$edit_campaign = isset( $_GET['edit_campaign'] ) ? absint( wp_unslash( $_GET['edit_campaign'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$view_campaign = isset( $_GET['view_campaign'] ) ? absint( wp_unslash( $_GET['view_campaign'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( $edit_campaign && ! wcdm_get_campaign( $edit_campaign ) ) {
			wp_safe_redirect( admin_url( 'admin.php?page=wc-donation-manager' ) );
			exit();
		}

		if ( $view_campaign && ! wcdm_get_campaign( $view_campaign ) ) {
			wp_safe_redirect( admin_url( 'admin.php?page=wc-donation-manager' ) );
			exit();
		}

		if ( $add_campaign ) {
			include __DIR__ . '/views/add-campaign.php';
		} elseif ( $edit_campaign ) {
			include __DIR__ . '/views/edit-campaign.php';
		} elseif ( $view_campaign ) {
			include __DIR__ . '/views/view-campaign.php';
		} else {
			include __DIR__ . '/views/list-campaigns.php';
		}
	}

	/**
	 * Render donors content.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function render_donors_content() {
		include __DIR__ . '/views/list-donors.php';
	}

	/**
	 * Render settings emails tab content.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function render_emails_content() {
		$mailer          = WC()->mailer();
		$email_templates = $mailer->get_emails();

		$donation_emails = array_filter(
			$email_templates,
			function ( $email ) {
				// Check if class name contains 'WC_Donation_Order_Email'.
				return str_contains( get_class( $email ), 'WC_Donation_Order_Email' );
			}
		);

		?>
		<tr valign="top">
			<td class="wc_emails_wrapper" colspan="2">
				<table class="wc_emails widefat" cellspacing="0">
					<thead>
					<tr>
						<?php
						$columns = apply_filters(
							'woocommerce_email_setting_columns',
							array(
								'status'     => '',
								'name'       => __( 'Email', 'wc-donation-manager' ),
								'email_type' => __( 'Content type', 'wc-donation-manager' ),
								'recipient'  => __( 'Recipient(s)', 'wc-donation-manager' ),
								'actions'    => '',
							)
						);
						foreach ( $columns as $key => $column ) {
							echo '<th class="wc-email-settings-table-' . esc_attr( $key ) . '">' . esc_html( $column ) . '</th>';
						}
						?>
					</tr>
					<tbody>
					<?php

					foreach ( $donation_emails as $email_key => $email ) {
						echo '<tr>';

						$manage_url = add_query_arg(
							array(
								'section' => strtolower( $email_key ),
							),
							admin_url( 'admin.php?page=wc-settings&tab=email' )
						);

						foreach ( $columns as $key => $column ) {

							switch ( $key ) {
								case 'name':
									echo '<td class="wc-email-settings-table-' . esc_attr( $key ) . '">
										<a href="' . esc_url( $manage_url ) . '">' . esc_html( $email->get_title() ) . '</a>
										' . wc_help_tip( $email->get_description() ) . '</td>';  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									break;
								case 'recipient':
									echo '<td class="wc-email-settings-table-' . esc_attr( $key ) . '">
										' . esc_html( $email->is_customer_email() ? __( 'Customer', 'wc-donation-manager' ) : $email->get_recipient() ) . '</td>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									break;
								case 'status':
									echo '<td class="wc-email-settings-table-' . esc_attr( $key ) . '">';

									if ( $email->is_manual() ) {
										echo '<span class="status-manual tips" data-tip="' . esc_attr__( 'Manually sent', 'wc-donation-manager' ) . '">' . esc_html__( 'Manual', 'wc-donation-manager' ) . '</span>';
									} elseif ( $email->is_enabled() ) {
										echo '<span class="status-enabled tips" data-tip="' . esc_attr__( 'Enabled', 'wc-donation-manager' ) . '">' . esc_html__( 'Yes', 'wc-donation-manager' ) . '</span>';
									} else {
										echo '<span class="status-disabled tips" data-tip="' . esc_attr__( 'Disabled', 'wc-donation-manager' ) . '">-</span>';
									}

									echo '</td>';
									break;
								case 'email_type':
									echo '<td class="wc-email-settings-table-' . esc_attr( $key ) . '">
											' . esc_html( $email->get_content_type() ) . '
										</td>';
									break;
								case 'actions':
									echo '<td class="wc-email-settings-table-' . esc_attr( $key ) . '">
											<a class="button alignright" href="' . esc_url( $manage_url ) . '">' . esc_html__( 'Manage', 'wc-donation-manager' ) . '</a>
										</td>';
									break;
								default:
									do_action( 'woocommerce_email_setting_column_' . $key, $email );
									break;
							}
						}

						echo '</tr>';
					}
					?>
					</tbody>
				</table>
			</td>
		</tr>
		<?php
	}
}
