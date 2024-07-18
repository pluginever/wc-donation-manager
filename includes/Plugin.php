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
		require_once __DIR__ . '/Functions.php';
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
		// Include common classes.
		$this->set(
			array(
				Emails\Emails::class,
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
}
