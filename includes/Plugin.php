<?php

namespace WooCommerceDonationManager;

defined( 'ABSPATH' ) || exit;

/**
 * The main plugin class.
 *
 * @since 1.0.0
 * @package WooCommerceDonationManager
 */
class Plugin {

	/**
	 * Plugin file path.
	 *
	 * @var string
	 */
	protected $file;

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	protected $version = '1.0.0';

	/**
	 * The single instance of the class.
	 *
	 * @since 1.0.0
	 * @var self
	 */
	public static $instance;

	/**
	 * Gets the single instance of the class.
	 * This method is used to create a new instance of the class.
	 *
	 * @param string $file The plugin file path.
	 * @param string $version The plugin version.
	 *
	 * @since 1.0.0
	 * @return static
	 */
	final public static function create( $file, $version = '1.0.0' ) {
		if ( null === self::$instance ) {
			self::$instance = new static( $file, $version );
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 *
	 * @param string $file The plugin file path.
	 * @param string $version The plugin version.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $file, $version ) {
		$this->file    = $file;
		$this->version = $version;
		$this->define_constants();
		$this->init_hooks();
	}

	/**
	 * Define plugin constants.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function define_constants() {
		define( 'WCDM_VERSION', $this->version );
		define( 'WCDM_FILE', $this->file );
		define( 'WCDM_PATH', plugin_dir_path( $this->file ) );
		define( 'WCDM_URL', plugin_dir_url( $this->file ) );
		define( 'WCDM_ASSETS_URL', WCDM_URL . 'assets/' );
	}

	/**
	 * Initialize hooks.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function init_hooks() {
		register_activation_hook( WCDM_FILE, array( $this, 'activate' ) );
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'admin_notices', array( $this, 'dependencies_notices' ) );
		add_action( 'woocommerce_init', array( $this, 'init' ), 0 );
	}

	/**
	 * Plugin activation hook.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function activate() {
		update_option( 'wcdm_version', WCDM_VERSION );
	}

	/**
	 * Load plugin textdomain.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'wc-donation-manager', false, dirname( plugin_basename( WCDM_FILE ) ) . '/languages/' );
	}

	/**
	 * Check if the plugin is active.
	 *
	 * @param string $plugin The plugin slug or basename.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_plugin_active( $plugin ) {
		// Check if the $plugin is a basename or a slug. If it's a slug, convert it to a basename.
		if ( false === strpos( $plugin, '/' ) ) {
			$plugin = $plugin . '/' . $plugin . '.php';
		}

		$active_plugins = (array) get_option( 'active_plugins', array() );
		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
		}

		return in_array( $plugin, $active_plugins, true ) || array_key_exists( $plugin, $active_plugins );
	}

	/**
	 * Missing dependencies notice.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function dependencies_notices() {
		if ( self::is_plugin_active( 'woocommerce' ) || ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		$plugin            = 'woocommerce/woocommerce.php';
		$installed_plugins = get_plugins();
		if ( isset( $installed_plugins[ $plugin ] ) ) {
			$notice = sprintf(
			/* translators: 1: plugin name 2: WooCommerce */
				__( '%1$s requires %2$s to be activated. %3$s', 'wc-donation-manager' ),
				'<strong>' . esc_html__( 'Donation Manager for WooCommerce', 'wc-donation-manager' ) . '</strong>',
				'<strong>' . esc_html__( 'WooCommerce', 'wc-donation-manager' ) . '</strong>',
				sprintf(
					'<a href="%s">%s</a>',
					esc_url( wp_nonce_url( self_admin_url( 'plugins.php?action=activate&plugin=' . $plugin ), 'activate-plugin_' . $plugin ) ),
					esc_html__( 'Activate WooCommerce', 'wc-donation-manager' )
				)
			);
		} else {
			$notice = sprintf(
			/* translators: 1: plugin name 2: WooCommerce */
				__( '%1$s requires %2$s to be installed and activated. %3$s', 'wc-donation-manager' ),
				'<strong>' . esc_html__( 'Donation Manager for WooCommerce', 'wc-donation-manager' ) . '</strong>',
				'<strong>' . esc_html__( 'WooCommerce', 'wc-donation-manager' ) . '</strong>',
				sprintf(
					'<a href="%s">%s</a>',
					esc_url( wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=woocommerce' ), 'install-plugin_woocommerce' ) ),
					esc_html__( 'Install WooCommerce', 'wc-donation-manager' )
				)
			);
		}
		echo '<div class="error"><p>' . wp_kses_post( $notice ) . '</p></div>';
	}

	/**
	 * Initialize the plugin.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function init() {
		// Including donation product type class.
		require_once __DIR__ . '/Donation/class-donation-product.php';

		new Controllers\Admin();
		new Controllers\Product();
		new Emails\Emails();
	}
}
