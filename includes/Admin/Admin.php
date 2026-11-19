<?php

namespace PluginEver\DonationManager\Admin;

use PluginEver\DonationManager\B8\Component;
use PluginEver\DonationManager\Plugin;

defined( 'ABSPATH' ) || exit;

/**
 * Admin class.
 *
 * Registers the React admin bundle and loads it on the plugin screens.
 *
 * @since   1.0.0
 * @package PluginEver\DonationManager\Admin
 *
 * @extends Component<Plugin>
 */
class Admin extends Component {

	/**
	 * Child components.
	 *
	 * @since 2.0.0
	 * @var array<int, class-string>
	 */
	public array $components = array(
		Menu::class,
		Metaboxes::class,
		Notices::class,
	);

	/**
	 * Whether to load.
	 *
	 * @since 1.1.3
	 * @return bool
	 */
	public function autoload(): bool {
		return is_admin();
	}

	/**
	 * Register hooks.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function register(): void {
		add_action( 'admin_enqueue_scripts', array( $this, 'register_assets' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_filter( 'woocommerce_screen_ids', array( $this, 'add_screen_ids' ) );
		add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), PHP_INT_MAX );
		add_filter( 'update_footer', array( $this, 'update_footer' ), PHP_INT_MAX );
	}

	/**
	 * Register the admin assets.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function register_assets(): void {
		$this->app->scripts->register_script( 'wcdm-admin', 'admin.js' );
		$this->app->scripts->register_style( 'wcdm-admin', 'style-admin.css' );

		wp_localize_script( 'wcdm-admin', 'wcdm_admin_vars', $this->get_admin_vars() );

		// The Cause field's editor mounts on demand inside a drawer and is
		// driven by the classic editor API. The default machinery can bail
		// (enqueue gates, the rich-edit preference), so the handles and the
		// settings bootstrap are loaded explicitly.
		wp_register_script(
			'wcdm-editor-bootstrap',
			'',
			array( 'editor', 'quicktags', 'wp-tinymce' ),
			(string) $this->app->version,
			false
		);
		wp_add_inline_script(
			'wcdm-editor-bootstrap',
			'window.wp = window.wp || {}; window.wp.editor = window.wp.editor || {}; window.wp.editor.getDefaultSettings = window.wp.editor.getDefaultSettings || function () { return { tinymce: { wpautop: true }, quicktags: { buttons: "strong,em,link,block,del,ins,img,ul,ol,li,code,more,close" } }; };',
			'after'
		);
	}

	/**
	 * Enqueue the admin assets on the plugin screens.
	 *
	 * @since 2.0.0
	 * @param string $hook Current admin page hook suffix.
	 * @return void
	 */
	public function enqueue_assets( string $hook ): void {
		$screen = get_current_screen();

		/**
		 * Filters the screens the admin bundle loads on.
		 *
		 * @since 2.0.0
		 * @param array<int, string> $screens Screen ids and hook suffixes.
		 */
		$screens = $this->app->apply_filters( 'admin_screens_ids', $this->app->get( Menu::class )->get_screen_ids() );

		if ( ! in_array( $hook, $screens, true ) && ! in_array( $screen ? $screen->id : '', $screens, true ) ) {
			return;
		}

		$namespace = implode( '/', array_filter( array( $this->app->rest_prefix, $this->app->rest_version ) ) );

		/**
		 * Filters the REST paths preloaded for the admin screens.
		 *
		 * @since 2.0.0
		 * @param array<int, string> $preload_paths REST paths.
		 */
		$preload_paths = $this->app->apply_filters( 'rest_api_preload_paths', array( "/{$namespace}/settings", "/{$namespace}/campaigns/options" ) );
		$preload_data  = array_reduce( $preload_paths, 'rest_preload_api_request', array() );

		wp_enqueue_media();
		wp_enqueue_editor();
		// Guarantee the classic editor API for the drawer's Cause editor.
		wp_enqueue_script( 'editor' );
		wp_enqueue_script( 'quicktags' );
		wp_enqueue_script( 'wp-tinymce' );
		wp_enqueue_script( 'wcdm-editor-bootstrap' );
		wp_enqueue_script( 'wcdm-admin' );
		wp_enqueue_style( 'wcdm-admin' );
		wp_add_inline_script(
			'wp-api-fetch',
			sprintf( 'wp.apiFetch.use( wp.apiFetch.createPreloadingMiddleware( %s ) );', wp_json_encode( $preload_data, JSON_HEX_TAG | JSON_UNESCAPED_SLASHES ) ),
			'after'
		);
	}

	/**
	 * Add the plugin screens to the WooCommerce screens.
	 *
	 * This will load the WooCommerce admin styles and scripts.
	 *
	 * @since 1.0.0
	 * @param array<int, string> $ids Screen ids.
	 * @return array<int, string>
	 */
	public function add_screen_ids( $ids ) {
		return array_merge( (array) $ids, $this->app->get( Menu::class )->get_screen_ids() );
	}

	/**
	 * Filter the admin footer text.
	 *
	 * @since 1.0.0
	 * @param string $text Footer text.
	 * @return string
	 */
	public function admin_footer_text( $text ) {
		$screen = get_current_screen();

		if ( $screen && in_array( $screen->id, $this->app->get( Menu::class )->get_screen_ids(), true ) ) {
			$text = sprintf(
				/* translators: %s: Plugin name */
				__( 'Thank you for using %s!', 'wc-donation-manager' ),
				'<strong>' . esc_html( (string) $this->app->get( 'name' ) ) . '</strong>'
			);

			if ( $this->app->review_url ) {
				$text .= sprintf(
					/* translators: %s: review link */
					__( ' Share your appreciation with a five-star review %s.', 'wc-donation-manager' ),
					'<a href="' . esc_url( (string) $this->app->review_url ) . '" target="_blank">here</a>'
				);
			}
		}

		return $text;
	}

	/**
	 * Filter the admin footer version text.
	 *
	 * @since 1.0.0
	 * @param string $text Footer text.
	 * @return string
	 */
	public function update_footer( $text ) {
		$screen = get_current_screen();

		if ( $screen && in_array( $screen->id, $this->app->get( Menu::class )->get_screen_ids(), true ) ) {
			/* translators: %s: Plugin version */
			$text = sprintf( esc_html__( 'Version %s', 'wc-donation-manager' ), $this->app->version );
		}

		return $text;
	}

	/**
	 * Get the localized admin data.
	 *
	 * @since 2.0.0
	 * @return array<string, mixed> Admin data.
	 */
	protected function get_admin_vars(): array {
		$user      = wp_get_current_user();
		$namespace = implode( '/', array_filter( array( $this->app->rest_prefix, $this->app->rest_version ) ) );

		$data = array(
			'slug'           => Menu::PARENT,
			'version'        => $this->app->version,
			'rest'           => array(
				'url'       => esc_url_raw( rest_url() ),
				'nonce'     => wp_create_nonce( 'wp_rest' ),
				'namespace' => $namespace,
			),
			'adminUrl'       => admin_url(),
			'assetsUrl'      => $this->app->assets_url(),
			'isPro'          => $this->app->is_pro_active(),
			'upgradeUrl'     => (string) $this->app->upgrade_url,
			'docsUrl'        => (string) $this->app->docs_url,
			'user'           => array(
				'id'           => $user->ID,
				'name'         => $user->display_name,
				'email'        => $user->user_email,
				'capabilities' => array(
					'manage_woocommerce' => current_user_can( 'manage_woocommerce' ),
				),
			),
			'locale'         => get_locale(),
			'currency'       => function_exists( 'get_woocommerce_currency' ) ? get_woocommerce_currency() : '',
			'currencySymbol' => function_exists( 'get_woocommerce_currency_symbol' ) ? html_entity_decode( get_woocommerce_currency_symbol() ) : '',
		);

		/**
		 * Filters the localized admin data.
		 *
		 * @since 2.0.0
		 * @param array<string, mixed> $data Admin data.
		 */
		return $this->app->apply_filters( 'admin_vars', $data );
	}
}
