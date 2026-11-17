<?php

namespace PluginEver\DonationManager\RestAPI\Controllers;

use WP_REST_Request;
use WP_REST_Response;

defined( 'ABSPATH' ) || exit;

/**
 * Products REST controller.
 *
 * Read-only search over WooCommerce products, used by the campaign
 * product picker and the donation product metabox.
 *
 * @since   2.0.0
 * @package PluginEver\DonationManager\RestAPI\Controllers
 */
class Products extends Controller {

	/**
	 * Route base.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	protected $rest_base = 'products';

	/**
	 * Check whether the current user may read the list.
	 *
	 * Products carry commercial data, so the same capability the
	 * WooCommerce product screens use guards them.
	 *
	 * @since 2.0.0
	 * @param WP_REST_Request $request Request object.
	 * @return bool Whether the current user may.
	 */
	public function get_items_permissions_check( $request ) {
		return current_user_can( self::CAPABILITY );
	}

	/**
	 * Search products.
	 *
	 * @since 2.0.0
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response The matching products.
	 */
	public function get_items( $request ) {
		$per_page = (int) $request->get_param( 'per_page' );
		$args     = array(
			'status'  => array( 'publish', 'private', 'draft' ),
			'limit'   => $per_page > 0 ? $per_page : 20,
			'page'    => max( 1, (int) $request->get_param( 'page' ) ),
			'search'  => (string) $request->get_param( 'search' ),
			'orderby' => 'title',
			'order'   => 'ASC',
		);

		$type = (string) $request->get_param( 'type' );

		if ( 'donation' === $type ) {
			$args['type'] = array( 'donation' );
		}

		if ( ! function_exists( 'wc_get_products' ) ) {
			return rest_ensure_response( array() );
		}

		$products = wc_get_products( $args );
		$items    = array_map( array( $this, 'prepare_item' ), $products );
		$total    = wc_get_products( array_merge( $args, array( 'return' => 'ids' ) ) );

		return $this->prepare_collection_response( $items, count( $total ), (int) $args['limit'], $request );
	}

	/**
	 * Get the collection params.
	 *
	 * @since 2.0.0
	 * @return array<string, mixed> Collection params.
	 */
	public function get_collection_params() {
		return array(
			'search'   => array(
				'description'       => __( 'Limit results to those matching a string.', 'wc-donation-manager' ),
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'type'     => array(
				'description' => __( 'Limit results to one product type.', 'wc-donation-manager' ),
				'type'        => 'string',
				'enum'        => array( 'all', 'donation' ),
				'default'     => 'all',
			),
			'page'     => array(
				'description' => __( 'Current page of the collection.', 'wc-donation-manager' ),
				'type'        => 'integer',
				'default'     => 1,
				'minimum'     => 1,
			),
			'per_page' => array(
				'description' => __( 'Maximum number of items to be returned in result set.', 'wc-donation-manager' ),
				'type'        => 'integer',
				'default'     => 20,
				'minimum'     => 1,
				'maximum'     => 100,
			),
		);
	}

	/**
	 * Prepare a product for the response.
	 *
	 * @since 2.0.0
	 * @param \WC_Product $product Product object.
	 * @return array<string, mixed> Prepared product.
	 */
	protected function prepare_item( $product ): array {
		return array(
			'id'       => $product->get_id(),
			'name'     => sprintf( '(#%1$d) %2$s', $product->get_id(), $product->get_name() ),
			'title'    => $product->get_name(),
			'price'    => (float) $product->get_price(),
			'type'     => $product->get_type(),
			'status'   => $product->get_status(),
			'campaign' => (int) $product->get_meta( 'wcdm_campaign_id' ),
		);
	}

	/**
	 * Get the item properties.
	 *
	 * @since 2.0.0
	 * @return array<string, mixed> Properties keyed by name.
	 */
	protected function get_properties(): array {
		return array(
			'id'       => array(
				'description' => __( 'Unique identifier for the product.', 'wc-donation-manager' ),
				'type'        => 'integer',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
			'name'     => array(
				'description' => __( 'Product name, prefixed by its id.', 'wc-donation-manager' ),
				'type'        => 'string',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
			'type'     => array(
				'description' => __( 'Product type.', 'wc-donation-manager' ),
				'type'        => 'string',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
			'price'    => array(
				'description' => __( 'Product price.', 'wc-donation-manager' ),
				'type'        => 'number',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
			'campaign' => array(
				'description' => __( 'Id of the campaign the product raises for, or 0 when it is unlinked.', 'wc-donation-manager' ),
				'type'        => 'integer',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
		);
	}
}
