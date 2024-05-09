<?php

namespace PluginEver\WooCommerceDonationManager;

defined( 'ABSPATH' ) || exit;

/**
 * The main plugin class.
 *
 * @since 1.0.0
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
	 * Initialize the plugin.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function init() {
		/**
		 * Including donation product type class.
		 */
		require_once __DIR__ . '/Donation/class-wc-product-donation.php';

		new Controllers\Admin();
		new Controllers\Product();
		new Emails\Emails();
	}
}
