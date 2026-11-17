<?php

namespace PluginEver\DonationManager\RestAPI;

use PluginEver\DonationManager\B8\Component;
use PluginEver\DonationManager\Plugin;
use PluginEver\DonationManager\RestAPI\Controllers\Campaigns;
use PluginEver\DonationManager\RestAPI\Controllers\Products;
use PluginEver\DonationManager\RestAPI\Controllers\Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Handles the REST routes.
 *
 * @since   2.0.0
 * @package PluginEver\DonationManager\RestAPI
 *
 * @extends Component<Plugin>
 */
class Routes extends Component {

	/**
	 * Register hooks.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function register(): void {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
		add_filter( 'wcdm_resource_routes', array( $this, 'resource_routes' ), 5 );
	}

	/**
	 * Register the REST routes.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function register_routes(): void {
		$this->app->router->group(
			array( 'permission' => 'manage_woocommerce' ),
			static function ( $router ): void {
				$router->get( 'settings', array( Settings::class, 'get_items' ) );
				$router->post( 'settings', array( Settings::class, 'update_items' ) );

				$router->resource(
					'campaigns',
					Campaigns::class,
					array(
						'get_views'   => false,
						'batch_items' => array( 'POST', 'batch' ),
					)
				);
			}
		);

		// Public to any admin user; products carry no campaign secrets but the controller checks `manage_woocommerce` itself.
		$this->app->router->group(
			array(),
			static function ( $router ): void {
				$router->resource(
					'products',
					Products::class,
					array(
						'create_item' => false,
						'update_item' => false,
						'delete_item' => false,
						'get_views'   => false,
					)
				);
			}
		);
	}

	/**
	 * Add the options route to every resource.
	 *
	 * @since 2.0.0
	 * @param array<string, array{0: string|string[], 1: string}|false> $routes Routes keyed by controller method.
	 * @return array<string, array{0: string|string[], 1: string}|false>
	 */
	public function resource_routes( array $routes ): array {
		$routes['get_options'] = array( 'GET', 'options' );

		return $routes;
	}
}
