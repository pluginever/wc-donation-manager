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

		// Pro tabs.
		if ( ! WCDM()->is_plugin_active( 'wc-donation-manager-pro' ) ) {
			add_action( 'wc_donation_manager_donors_content', array( $this, 'render_donors_content' ) );
			add_action( 'wc_donation_manager_settings_emails_content', array( $this, 'render_emails_settings' ) );
			add_action( 'woocommerce_admin_field_wcdm_add_to_cart_btn_text', array( $this, 'render_customizable_settings' ) );
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
			'dashicons-heart',
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
			'label'   => __( 'Per page', 'wc-donation-manager' ),
			'default' => 20,
		);
		$page = preg_replace( '/^.*?wcdm-/', 'wcdm-', $screen->id );
		$tab  = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : '';
		$page = ! empty( $tab ) ? $page . '-' . $tab : $page;

		switch ( $page ) {
			case 'toplevel_page_wc-donation-manager':
			case 'toplevel_page_wc-donation-manager-campaign':
				$this->list_table = new ListTables\CampaignsListTable();
				$this->list_table->prepare_items();
				$args['option'] = 'wcdm_campaigns_per_page';
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
		$edit     = Utilities::is_edit_screen();
		$campaign = ! empty( $edit ) ? get_post( $edit ) : '';

		if ( ! empty( $edit ) && empty( $campaign ) ) {
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
		?>
		<h1><?php esc_html_e( 'Donors', 'wc-donation-manager' ); ?></h1>
		<p><?php esc_html_e( 'The list of donors and their donation information(s)', 'wc-donation-manager' ); ?></p>
		<hr class="wp-header-end">
		<div class="wcdm-feature-promo-banner">
			<div class="wcdm-feature-promo-banner__content">
				<h3><?php esc_html_e( 'Available in Pro Version', 'wc-donation-manager' ); ?></h3>
				<a href="https://pluginever.com/plugins/woocommerce-donation-manager-pro/?utm_source=import-tab&utm_medium=link&utm_campaign=upgrade&utm_id=wc-donation-manager" target="_blank" class="button-primary"><?php esc_html_e( 'Upgrade to Pro Now', 'wc-donation-manager' ); ?></a>
			</div>
			<img src="<?php echo esc_url( WCDM()->get_dir_url() . 'assets/images/donors.png' ); ?>" alt="<?php esc_attr_e( 'Donors list table', 'wc-donation-manager' ); ?>"/>
		</div>
		<?php
	}

	/**
	 * Render settings emails tab content.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function render_emails_settings() {
		?>
		<div class="wcdm-feature-promo-banner">
			<div class="wcdm-feature-promo-banner__content">
				<h3><?php esc_html_e( 'Available in Pro Version', 'wc-donation-manager' ); ?></h3>
				<a href="https://pluginever.com/plugins/woocommerce-donation-manager-pro/?utm_source=import-tab&utm_medium=link&utm_campaign=upgrade&utm_id=wc-donation-manager" target="_blank" class="button-primary"><?php esc_html_e( 'Upgrade to Pro Now', 'wc-donation-manager' ); ?></a>
			</div>
			<img src="<?php echo esc_url( WCDM()->get_dir_url() . 'assets/images/emails.png' ); ?>" alt="<?php esc_attr_e( 'Customer completed emails', 'wc-donation-manager' ); ?>"/>
		</div>
		<?php
	}

	/**
	 * Render customizable settings.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function render_customizable_settings() {
		?>
		<div class="wcdm-feature-promo-banner">
			<div class="wcdm-feature-promo-banner__content">
				<h3><?php esc_html_e( 'Available in Pro Version', 'wc-donation-manager' ); ?></h3>
				<a href="https://pluginever.com/plugins/woocommerce-donation-manager-pro/?utm_source=import-tab&utm_medium=link&utm_campaign=upgrade&utm_id=wc-donation-manager" target="_blank" class="button-primary"><?php esc_html_e( 'Upgrade to Pro Now', 'wc-donation-manager' ); ?></a>
			</div>
			<img src="<?php echo esc_url( WCDM()->get_dir_url() . 'assets/images/customizable-options.png' ); ?>" alt="<?php esc_attr_e( 'Add customizable settings', 'wc-donation-manager' ); ?>"/>
		</div>
		<?php
	}
}
