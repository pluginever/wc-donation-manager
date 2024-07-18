<?php

namespace WooCommerceDonationManager\Admin;

defined( 'ABSPATH' ) || exit();

/**
 * Menus class.
 *
 * @since 1.0.0
 * @package WooCommerceDonationManager\Admin
 */
class Menus {

	/**
	 * Main menu slug.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const PARENT_SLUG = 'wc-donation-manager';

	/**
	 * List tables.
	 *
	 * @var \WP_List_Table
	 */
	private $list_table;

	/**
	 * Menus constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_filter( 'set-screen-option', array( $this, 'screen_option' ), 10, 3 );
		add_action( 'current_screen', array( $this, 'setup_list_table' ) );

		add_action( 'wc_donation_manager_campaigns_content', array( $this, 'render_campaigns_content' ) );

		// Settings tabs.
		add_action( 'wc_donation_manager_settings_tutorial_content', array( $this, 'render_tutorial_content' ) );

		// Pro tabs.
		if ( ! WCDM()->is_plugin_active( 'wc-donation-manager-pro.php' ) ) {
			add_action( 'wc_donation_manager_donors_content', array( $this, 'render_donors_content' ) );
			add_action( 'wc_donation_manager_settings_emails_content', array( $this, 'render_emails_settings' ) );
		}
	}

	/**
	 * Register admin menu.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function admin_menu() {
		global $admin_page_hooks;
		add_menu_page(
			__( 'Donations', 'wc-donation-manager' ),
			__( 'Donations', 'wc-donation-manager' ),
			'manage_options',
			self::PARENT_SLUG,
			null,
			'dashicons-lock',
			'55.9'
		);
		$admin_page_hooks['wc-donation-manager'] = 'wc-donation-manager';

		$submenus = Utilities::get_menus();
		usort(
			$submenus,
			function ( $a, $b ) {
				$a = isset( $a['position'] ) ? $a['position'] : PHP_INT_MAX;
				$b = isset( $b['position'] ) ? $b['position'] : PHP_INT_MAX;

				return $a - $b;
			}
		);

		foreach ( $submenus as $submenu ) {
			$submenu = wp_parse_args(
				$submenu,
				array(
					'page_title' => '',
					'menu_title' => '',
					'capability' => 'manage_options',
					'menu_slug'  => '',
					'callback'   => null,
					'position'   => '10',
					'page_id'    => null,
					'tabs'       => array(),
					'load_hook'  => null,
				)
			);
			if ( ! is_callable( $submenu['callback'] ) && ! empty( $submenu['page_id'] ) ) {
				$submenu['callback'] = function () use ( $submenu ) {
					$page_id = $submenu['page_id'];
					$tabs    = $submenu['tabs'];
					include_once __DIR__ . '/views/admin-page.php';
				};
			}
			$load = add_submenu_page(
				self::PARENT_SLUG,
				$submenu['page_title'],
				$submenu['menu_title'],
				$submenu['capability'],
				$submenu['menu_slug'],
				$submenu['callback'],
				$submenu['position']
			);
			if ( ! empty( $submenu['load_hook'] ) && is_callable( $submenu['load_hook'] ) ) {
				add_action( 'load-' . $load, $submenu['load_hook'] );
			}
		}
	}

	/**
	 * Set screen option.
	 *
	 * @param mixed  $status Screen option value. Default false.
	 * @param string $option Option name.
	 * @param mixed  $value New option value.
	 *
	 * @since 1.0.0
	 * @return mixed
	 */
	public function screen_option( $status, $option, $value ) {
		$options = apply_filters(
			'wc_donation_manager_screen_options',
			array(
				'wcdm_campaigns_per_page',
				'wcdm_donors_per_page',
			)
		);
		if ( in_array( $option, $options, true ) ) {
			return $value;
		}

		return $status;
	}

	/**
	 * Current screen.
	 *
	 * @since 1.0.0
	 */
	public function setup_list_table() {
		wp_verify_nonce( '_wpnonce' );
		$screen = get_current_screen();
		if ( Utilities::is_add_screen() || Utilities::is_edit_screen() || ! in_array( $screen->id, Utilities::get_screen_ids(), true ) ) {
			return;
		}
		$args = array(
			'label'   => __( 'Per page', 'wp-ever-accounting' ),
			'default' => 20,
		);
		$page = preg_replace( '/^.*?wcdm-/', 'wcdm-', $screen->id );
		$tab  = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : '';
		$page = ! empty( $tab ) ? $page . '-' . $tab : $page;

		switch ( $page ) {
			case 'toplevel_page_wc-donation-manager':
				$this->list_table = new ListTables\CampaignsListTable();
				$this->list_table->prepare_items();
				$args['option'] = 'wcdm_campaigns_per_page';
				add_screen_option( 'per_page', $args );
				break;
			case 'wcdm-donors':
				$this->list_table = new ListTables\DonorsListTable();
				$this->list_table->prepare_items();
				$args['option'] = 'wcdm_donors_per_page';
				add_screen_option( 'per_page', $args );
				break;
		}
	}

	/**
	 * Render campaigns content.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function render_campaigns_content() {
		$edit = Utilities::is_edit_screen();
		$api  = ! empty( $edit ) ? get_post( $edit ) : '';

		if ( ! empty( $edit ) && empty( $api ) ) {
			wp_safe_redirect( remove_query_arg( 'edit' ) );
			exit();
		}

		if ( Utilities::is_add_screen() ) {
			include __DIR__ . '/views/campaigns/add.php';
		} elseif ( $edit ) {
			include __DIR__ . '/views/campaigns/edit.php';
		} else {
			include __DIR__ . '/views/campaigns/campaigns.php';
		}
	}

	/**
	 * Render donors content.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function render_donors_content() {
		include __DIR__ . '/views/donors/donors.php';
	}

	/**
	 * Render settings emails tab content.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function render_emails_settings() {
		$mailer          = WC()->mailer();
		$email_templates = $mailer->get_emails();

		$donation_emails = array_filter(
			$email_templates,
			function ( $email ) {
				// Check if class name contains 'WC_Donation_Order_Email'.
				return str_contains( get_class( $email ), 'WC_Donation_Order_Email' );
			}
		);

		if ( ! empty( $donation_emails ) ) {
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
		} else {
			esc_html_e( 'No email templates found.', 'wc-donation-manager' );
		}
	}

	/**
	 * Render settings - tutorial tab content.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function render_tutorial_content() {
		// TODO: Tutorials content will be appear here.
		esc_html_e( 'Lorem lipsum is a dollar site. Update this text first then use it.', 'wc-donation-manager' );
	}
}
