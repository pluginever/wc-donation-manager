<?php

namespace PluginEver\DonationManager\Admin;

use PluginEver\DonationManager\B8\Component;
use PluginEver\DonationManager\Plugin;

defined( 'ABSPATH' ) || exit;

/**
 * Handles the admin menu.
 *
 * One parent page and one mount node; every screen is a hash
 * route rendered by the React app.
 *
 * @since   2.0.0
 * @package PluginEver\DonationManager\Admin
 *
 * @extends Component<Plugin>
 */
class Menu extends Component {

	/**
	 * Parent menu slug.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	const PARENT = 'wc-donation-manager';

	/**
	 * Registered screen ids.
	 *
	 * @since 2.0.0
	 * @var array<int, string>
	 */
	protected array $screen_ids = array();

	/**
	 * Register hooks.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function register(): void {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'style_upgrade' ) );
	}

	/**
	 * Register the admin menu.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function register_menu(): void {
		global $admin_page_hooks;

		$this->screen_ids[] = add_menu_page(
			__( 'Donations', 'wc-donation-manager' ),
			__( 'Donations', 'wc-donation-manager' ),
			'manage_woocommerce',
			self::PARENT,
			array( $this, 'render' ),
			'dashicons-heart',
			'55.9'
		);

		// Keeps the screen ids stable when the menu title is translated.
		$admin_page_hooks[ self::PARENT ] = self::PARENT;

		/**
		 * Filters the admin pages configuration.
		 *
		 * @since 2.0.0
		 * @param array<int, array<string, mixed>> $pages Admin page configurations.
		 */
		$pages = $this->app->apply_filters(
			'admin_pages',
			array(
				array(
					'title'      => __( 'Campaigns', 'wc-donation-manager' ),
					'path'       => '/campaigns',
					'capability' => 'manage_woocommerce',
					'position'   => 10,
				),
				array(
					'title'      => __( 'Donors', 'wc-donation-manager' ),
					'path'       => '/donors',
					'capability' => 'manage_woocommerce',
					'position'   => 20,
				),
				array(
					'title'      => __( 'Settings', 'wc-donation-manager' ),
					'path'       => '/settings',
					'capability' => 'manage_woocommerce',
					'position'   => 30,
				),
			)
		);

		usort( $pages, static fn( $a, $b ) => ( $a['position'] ?? 10 ) <=> ( $b['position'] ?? 10 ) );

		foreach ( $pages as $page ) {
			$page = wp_parse_args(
				$page,
				array(
					'title'      => '',
					'slug'       => '',
					'path'       => '',
					'capability' => 'manage_woocommerce',
					'position'   => 10,
					'handle'     => null,
					'callback'   => array( $this, 'render' ),
				)
			);

			// The leading slash is part of the route path the JS router matches.
			if ( ! empty( $page['path'] ) ) {
				$page['slug'] = self::PARENT . '#/' . ltrim( $page['path'], '/' );
			}

			if ( ! $page['title'] || ! $page['slug'] ) {
				continue;
			}

			$hook = add_submenu_page(
				self::PARENT,
				$page['title'],
				$page['title'],
				$page['capability'],
				$page['slug'],
				$this->app->callback( $page['callback'] ),
				$page['position']
			);

			if ( ! $hook ) {
				continue;
			}

			$this->screen_ids[] = $hook;

			if ( is_callable( $page['handle'] ) ) {
				add_action( "load-{$hook}", $this->app->callback( $page['handle'] ) );
			}
		}

		if ( ! $this->app->is_pro_active() ) {
			add_submenu_page(
				self::PARENT,
				__( 'Upgrade to Pro', 'wc-donation-manager' ),
				__( 'Upgrade to Pro', 'wc-donation-manager' ),
				'manage_woocommerce',
				add_query_arg(
					array(
						'utm_source' => 'plugin',
						'utm_medium' => 'admin-menu',
					),
					(string) $this->app->upgrade_url
				),
				'',
				9000
			);
		}

		// Parent is a container; drop the auto-created duplicate submenu.
		remove_submenu_page( self::PARENT, self::PARENT );
	}

	/**
	 * Paint the Upgrade to Pro menu item in the Pro green the app uses.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function style_upgrade(): void {
		if ( $this->app->is_pro_active() ) {
			return;
		}

		$item = '#adminmenu #toplevel_page_' . self::PARENT . ' .wp-submenu a[href*="utm_medium=admin-menu"]';

		wp_add_inline_style(
			'admin-menu',
			"{$item} { margin: 4px 8px; background: #39b54a; color: #ffffff; font-weight: 600; }
			{$item}:hover, {$item}:focus { background: #2f9a3e; color: #ffffff; box-shadow: none; }"
		);
	}

	/**
	 * Render the mount node.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function render(): void {
		?>
		<style>
			body { background-color: var(--b8-color-bg-page); }
			#wpcontent { padding-inline-start: 0; }
			#wpbody-content { padding-bottom: 0; }
			#wpbody-content > div:not(#wcdm-admin):not(#screen-meta) { display: none; }
			#wpfooter { display: none; }
			.a11y-speak-region { inset-inline-start: -1px; top: -1px; }
			#wcdm-admin { min-height: calc(100vh - var(--wp-admin--admin-bar--height, 32px)); }
		</style>
		<div id="wcdm-admin"></div>
		<?php
	}

	/**
	 * Get the screen ids.
	 *
	 * @since 2.0.0
	 * @return array<int, string> Screen IDs.
	 */
	public function get_screen_ids(): array {
		return $this->screen_ids;
	}
}
