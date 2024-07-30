<?php

namespace WooCommerceDonationManager;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Plugin Class.
 *
 * @since   1.0.0
 * @package WooCommerceDonationManager
 */
final class Plugin extends ByteKit\Plugin {

	/**
	 * Plugin constructor.
	 *
	 * @param array $data The plugin data.
	 *
	 * @since 1.0.0
	 */
	protected function __construct( $data ) {
		parent::__construct( $data );
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Define constants.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function define_constants() {
		$role = apply_filters( 'wc_donation_manager_role', 'manage_woocommerce' );
		$this->define( 'WCDM_VERSION', $this->get_version() );
		$this->define( 'WCDM_FILE', $this->get_file() );
		$this->define( 'WCDM_PATH', $this->get_dir_path() );
		$this->define( 'WCDM_URL', $this->get_dir_url() );
		$this->define( 'WCDM_ASSETS_URL', $this->get_assets_url() );
		$this->define( 'WCDM_ASSETS_PATH', $this->get_assets_path() );
		$this->define( 'WCDM_MANAGER_ROLE', $role );
	}

	/**
	 * Include required files.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function includes() {
		require_once __DIR__ . '/functions.php';
	}

	/**
	 * Initialize the plugin hooks.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function init_hooks() {
		register_activation_hook( $this->get_file(), array( Installer::class, 'install' ) );
		add_action( 'before_woocommerce_init', array( $this, 'on_before_woocommerce_init' ) );
		add_action( 'woocommerce_init', array( $this, 'on_init' ), 0 );
	}

	/**
	 * Run on before WooCommerce init.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function on_before_woocommerce_init() {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', $this->get_file(), true );
		}
	}

	/**
	 * Run on init.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function on_init() {
		// Register post types.
		add_action( 'init', array( $this, 'register_cpt_campaigns' ) );

		// Including donation product type class.
		require_once __DIR__ . '/Donation/class-donation-product.php';

		// Include common classes.
		$this->set(
			array(
				Donation\Donation::class,
				Frontend\Frontend::class,
				Frontend\Product::class,
				Frontend\Cart::class,
				Frontend\Orders::class,
			)
		);

		// Include Admin classes.
		if ( is_admin() ) {
			$this->set(
				array(
					Admin\Admin::class,
					Admin\Menus::class,
					Admin\Settings::instance(),
					Admin\Actions::class,
					Admin\Metaboxes::class,
				)
			);
		}

		/**
		 * Fires when the plugin is initialized.
		 *
		 * @param Plugin $this The plugin instance.
		 *
		 * @since 1.0.0
		 */
		do_action( 'wc_donation_manager_init', $this );
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
