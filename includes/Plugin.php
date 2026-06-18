<?php

namespace PluginEver\DonationManager;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Plugin Class.
 *
 * @since   1.0.0
 * @package PluginEver\DonationManager
 *
 * @property-read string $name         Plugin display name.
 * @property-read string $settings_url Settings page URL.
 * @property-read string $docs_url     Documentation URL.
 * @property-read string $support_url  Support page URL.
 * @property-read string $upgrade_url  Premium upgrade URL.
 * @property-read string $pro_basename Premium plugin basename.
 * @property-read string $review_url   Review URL.
 */
final class Plugin extends B8\App {

	/**
	 * Components to boot.
	 *
	 * @since 1.1.3
	 * @var array<int|string, class-string>
	 */
	protected array $components = array(
		Installer::class,
		Donation\Donation::class,
		Frontend\Frontend::class,
		Frontend\Product::class,
		Frontend\Cart::class,
		Frontend\Orders::class,
		Admin\Admin::class,
	);

	/**
	 * Bootstraps the plugin.
	 *
	 * @since 1.1.3
	 * @return void
	 */
	public function bootstrap(): void {
		$role = apply_filters( 'wc_donation_manager_role', 'manage_woocommerce' );
		define( 'WCDM_VERSION', $this->version );
		define( 'WCDM_FILE', $this->file );
		define( 'WCDM_PATH', $this->plugin_path() );
		define( 'WCDM_URL', $this->plugin_url() );
		define( 'WCDM_ASSETS_URL', $this->assets_url() );
		define( 'WCDM_ASSETS_PATH', $this->assets_path() );
		define( 'WCDM_MANAGER_ROLE', $role );

		// Donation product type class (non-namespaced WC_Product subclass).
		require_once __DIR__ . '/Donation/class-donation-product.php';

		add_action( 'init', array( $this, 'register_cpt_campaigns' ) );
		add_action( 'woocommerce_loaded', array( $this, 'woocommerce_loaded' ), 0 );
		add_filter( 'plugin_action_links_' . $this->basename(), array( $this, 'plugin_action_links' ) );
	}

	/**
	 * Boot the components once WooCommerce is loaded.
	 *
	 * @since 1.1.3
	 * @return void
	 */
	public function woocommerce_loaded(): void {
		$this->boot( $this->components );

		/**
		 * Fires when the plugin is initialized.
		 *
		 * @param Plugin $plugin The plugin instance.
		 *
		 * @since 1.0.0
		 */
		do_action( 'wc_donation_manager_init', $this );
	}

	/**
	 * Add plugin action links.
	 *
	 * @param array<string, string> $links The plugin action links.
	 *
	 * @since 1.0.0
	 * @return array<string, string>
	 */
	public function plugin_action_links( $links ) {
		if ( ! $this->is_pro_active() ) {
			$links['go_pro'] = '<a href="' . esc_url( (string) $this->get( 'upgrade_url' ) ) . '" target="_blank" style="color: #39b54a; font-weight: bold;">' . esc_html__( 'Go Pro', 'wc-donation-manager' ) . '</a>';
		}

		return $links;
	}

	/**
	 * Whether the Pro add-on is active.
	 *
	 * @since 1.1.3
	 * @return bool
	 */
	public function is_pro_active(): bool {
		return ! empty( $this->pro_basename ) && $this->plugin_active( (string) $this->pro_basename );
	}

	/**
	 * Register custom post type campaigns.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function register_cpt_campaigns() {
		$labels = array(
			'name'               => _x( 'Campaign', 'post type general name', 'wc-donation-manager' ),
			'singular_name'      => _x( 'Campaign', 'post type singular name', 'wc-donation-manager' ),
			'menu_name'          => _x( 'Campaign', 'admin menu', 'wc-donation-manager' ),
			'name_admin_bar'     => _x( 'Campaign', 'add new on admin bar', 'wc-donation-manager' ),
			'add_new'            => _x( 'Add New', 'campaign', 'wc-donation-manager' ),
			'add_new_item'       => __( 'Add New Campaign', 'wc-donation-manager' ),
			'new_item'           => __( 'New Campaign', 'wc-donation-manager' ),
			'edit_item'          => __( 'Edit Campaign', 'wc-donation-manager' ),
			'view_item'          => __( 'View Campaign', 'wc-donation-manager' ),
			'all_items'          => __( 'All Campaign', 'wc-donation-manager' ),
			'search_items'       => __( 'Search Campaign', 'wc-donation-manager' ),
			'parent_item_colon'  => __( 'Parent Campaign:', 'wc-donation-manager' ),
			'not_found'          => __( 'No campaigns found.', 'wc-donation-manager' ),
			'not_found_in_trash' => __( 'No campaigns found in Trash.', 'wc-donation-manager' ),
		);

		$args = array(
			'labels'              => apply_filters( 'wcdm_campaigns_post_type_labels', $labels ),
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => false,
			'show_in_menu'        => false,
			'show_in_nav_menus'   => false,
			'query_var'           => false,
			'can_export'          => false,
			'rewrite'             => false,
			'capability_type'     => 'post',
			'has_archive'         => false,
			'hierarchical'        => false,
			'menu_position'       => null,
			'supports'            => array(),
		);

		register_post_type( 'wcdm_campaigns', apply_filters( 'wcdm_campaigns_post_type_args', $args ) );
	}
}
