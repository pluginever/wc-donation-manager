<?php

namespace WooCommerceDonationManager;

defined( 'ABSPATH' ) || exit;

/**
 * Class Plugin.
 *
 * @since 1.0.0
 *
 * @package WooCommerceDonationManager
 */
class Plugin extends Lib\Plugin {

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
		$this->define( 'WCDM_VERSION', $this->get_version() );
		$this->define( 'WCDM_FILE', $this->get_file() );
		$this->define( 'WCDM_PATH', $this->get_dir_path() );
		$this->define( 'WCDM_URL', $this->get_dir_url() );
		$this->define( 'WCDM_ASSETS_URL', $this->get_assets_url() );
		$this->define( 'WCDM_ASSETS_PATH', $this->get_assets_path() );
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
	 * Hook into actions and filters.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function init_hooks() {
		register_activation_hook( $this->get_file(), array( Installer::class, 'install' ) );
		add_action( 'admin_notices', array( $this, 'dependencies_notices' ) );
		add_action( 'woocommerce_init', array( $this, 'init' ), 0 );
	}

	/**
	 * Missing dependencies notice.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function dependencies_notices() {
		if ( $this->is_plugin_active( 'woocommerce' ) ) {
			return;
		}
		$notice = sprintf(
		/* translators: 1: plugin name 2: WooCommerce */
			__( '%1$s requires %2$s to be installed and active.', 'wc-donation-manager' ),
			'<strong>' . esc_html( $this->get_name() ) . '</strong>',
			'<strong>' . esc_html__( 'WooCommerce', 'wc-donation-manager' ) . '</strong>'
		);

		echo '<div class="notice notice-error"><p>' . wp_kses_post( $notice ) . '</p></div>';
	}

	/**
	 * Init the plugin after plugins_loaded so environment variables are set.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function init() {
		// $this->services->add( Actions::class );
		// $this->services->add( Installer::class );
		$this->services->add( PostTypes::class );
		// $this->services->add( Cart::class );
		// $this->services->add( Checkout::class );
		// $this->services->add( Emails\Emails::class );
//		$this->services->add( Models\Campaign::class );
//		$this->services->add( Models\Donor::class );

		if ( self::is_request( 'admin' ) ) {
			$this->services->add( Admin\Admin::class );
		}

		// if ( self::is_request( 'frontend' ) ) {
		// $this->services->add( Frontend\Frontend::class );
		// }

		// Init action.
		do_action( 'wc_donation_manager_init' );
	}
}
