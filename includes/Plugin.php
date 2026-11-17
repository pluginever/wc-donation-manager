<?php

namespace PluginEver\DonationManager;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Main plugin class.
 *
 * @since   1.0.0
 * @package PluginEver\DonationManager
 *
 * @property Settings        $settings    Plugin settings.
 * @property Campaigns       $campaigns   Campaigns registry.
 *
 * @property-read string     $name         Plugin display name.
 * @property-read string     $settings_url Settings page URL.
 * @property-read string     $docs_url     Documentation URL.
 * @property-read string     $support_url  Support page URL.
 * @property-read string     $upgrade_url  Premium upgrade URL.
 * @property-read string     $review_url   Review URL.
 */
class Plugin extends B8\App {

	/**
	 * Components to boot.
	 *
	 * @since 2.0.0
	 * @var array<int, class-string>
	 */
	protected array $components = array(
		Installer::class,
		Campaigns::class,
		Donation\Donation::class,
		Frontend\Frontend::class,
		Frontend\Product::class,
		Frontend\Cart::class,
		Frontend\Orders::class,
		RestAPI\Routes::class,
		Admin\Admin::class,
		Admin\Feedback::class,
	);

	/**
	 * Register hooks.
	 *
	 * @since 1.1.3
	 * @return void
	 */
	public function bootstrap(): void {
		$upload_dir = wp_upload_dir( null, false );
		define( 'WCDM_PLUGIN_FILE', $this->file );
		define( 'WCDM_PLUGIN_VERSION', $this->version );
		define( 'WCDM_UPLOADS_DIR', $upload_dir['basedir'] . '/wcdm/' );
		define( 'WCDM_UPLOADS_URL', $upload_dir['baseurl'] . '/wcdm/' );

		$this->bind( 'settings', Settings::class );
		$this->alias( Campaigns::class, 'campaigns' );

		add_action( 'woocommerce_loaded', array( $this, 'woocommerce_loaded' ), 0 );
		add_filter( 'plugin_action_links_' . $this->basename(), array( $this, 'plugin_action_links' ) );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
	}

	/**
	 * Boot the components once WooCommerce is loaded.
	 *
	 * @since 1.1.3
	 * @return void
	 */
	public function woocommerce_loaded(): void {
		require_once __DIR__ . '/Donation/class-donation-product.php';

		$this->boot( $this->components );

		/**
		 * Fires when the plugin is initialized.
		 *
		 * @param Plugin $plugin The plugin instance.
		 *
		 * @since 1.0.0
		 */
		$this->do_action( 'loaded', $this );
	}

	/**
	 * Install the plugin on activation.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function activate(): void {
		$this->make( Installer::class )->install();
	}

	/**
	 * Clean the plugin's scheduled work on deactivation.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function deactivate(): void {
		$this->make( Installer::class )->deactivate();
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
		$settings = sprintf(
			'<a href="%s">%s</a>',
			esc_url( (string) $this->get( 'settings_url' ) ),
			esc_html__( 'Settings', 'wc-donation-manager' )
		);

		$links = array_merge( array( 'settings' => $settings ), $links );

		if ( ! $this->is_pro_active() ) {
			$links['go_pro'] = '<a href="' . esc_url( (string) $this->get( 'upgrade_url' ) ) . '" target="_blank" style="color: #39b54a; font-weight: bold;">' . esc_html__( 'Go Pro', 'wc-donation-manager' ) . '</a>';
		}

		return $links;
	}

	/**
	 * Add the plugin row meta links.
	 *
	 * @since 2.0.0
	 * @param array<int, string> $links Plugin row meta links.
	 * @param string             $file  Plugin file path relative to the plugins directory.
	 * @return array<int, string>
	 */
	public function plugin_row_meta( array $links, string $file ): array {
		if ( $file !== $this->basename() ) {
			return $links;
		}

		$links[] = sprintf(
			'<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>',
			esc_url( (string) $this->get( 'review_url' ) ),
			esc_html__( 'Review', 'wc-donation-manager' )
		);

		return $links;
	}

	/**
	 * Whether the Pro add-on is active.
	 *
	 * @since 1.1.3
	 * @return bool
	 */
	public function is_pro_active(): bool {
		return $this->addons->has( 'wc-donation-manager-pro' );
	}
}
