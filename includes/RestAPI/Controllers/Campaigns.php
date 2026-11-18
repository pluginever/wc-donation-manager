<?php

namespace PluginEver\DonationManager\RestAPI\Controllers;

use PluginEver\DonationManager\Campaigns as CampaignRegistry;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

defined( 'ABSPATH' ) || exit;

/**
 * Campaigns REST controller.
 *
 * Serves the campaign post type over the REST API; the React
 * campaigns screen consumes it.
 *
 * @since   2.0.0
 * @package PluginEver\DonationManager\RestAPI\Controllers
 */
class Campaigns extends Controller {

	/**
	 * Route base.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	protected $rest_base = 'campaigns';

	/**
	 * Get the campaigns.
	 *
	 * @since 2.0.0
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response The campaigns.
	 */
	public function get_items( $request ) {
		$orderby = (string) $request->get_param( 'orderby' );

		// The list sends column keys; the query needs WP_Query's own names.
		$orderby_map = array(
			'name'        => 'title',
			'goal_amount' => 'meta_value_num',
			'end_date'    => 'meta_value',
			'status'      => 'post_status',
		);

		$args = array(
			'search'   => (string) $request->get_param( 'search' ),
			'status'   => (array) $request->get_param( 'status' ),
			'orderby'  => $orderby_map[ $orderby ] ?? 'date',
			'order'    => (string) $request->get_param( 'order' ),
			'per_page' => (int) $request->get_param( 'per_page' ),
			'paged'    => (int) $request->get_param( 'page' ),
		);

		if ( 'meta_value_num' === $args['orderby'] ) {
			$args['meta_key'] = CampaignRegistry::META['goal_amount']; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- deliberate sort column requested by the list.
		} elseif ( 'meta_value' === $args['orderby'] ) {
			$args['meta_key'] = CampaignRegistry::META['end_date']; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- deliberate sort column requested by the list.
		}

		$query  = wc_donation_manager()->campaigns->query( $args );
		$items  = array();
		$fields = array_keys( $this->get_properties() );

		foreach ( $query['items'] as $post ) {
			$items[] = $this->prepare_item( $post, $request, $fields );
		}

		return $this->prepare_collection_response( $items, $query['total'], (int) $args['per_page'], $request );
	}

	/**
	 * Get a single campaign.
	 *
	 * @since 2.0.0
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response|WP_Error The campaign, or why it could not be read.
	 */
	public function get_item( $request ) {
		$post = wc_donation_manager()->campaigns->get( (int) $request['id'] );

		if ( null === $post ) {
			return new WP_Error( 'wcdm_rest_campaign_invalid', __( 'Invalid campaign id.', 'wc-donation-manager' ), array( 'status' => 404 ) );
		}

		return rest_ensure_response( $this->prepare_item( $post, $request ) );
	}

	/**
	 * Create a campaign.
	 *
	 * @since 2.0.0
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response|WP_Error The campaign, or why it could not be created.
	 */
	public function create_item( $request ) {
		$post = wc_donation_manager()->campaigns->save( $this->get_campaign_data( $request ) );

		if ( is_wp_error( $post ) ) {
			return $post;
		}

		$this->update_additional_fields_for_object( $post, $request );

		$request->set_param( 'context', 'edit' );

		return rest_ensure_response( $this->prepare_item( $post, $request ) );
	}

	/**
	 * Update a campaign.
	 *
	 * @since 2.0.0
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response|WP_Error The campaign, or why it could not be updated.
	 */
	public function update_item( $request ) {
		$post = wc_donation_manager()->campaigns->get( (int) $request['id'] );

		if ( null === $post ) {
			return new WP_Error( 'wcdm_rest_campaign_invalid', __( 'Invalid campaign id.', 'wc-donation-manager' ), array( 'status' => 404 ) );
		}

		$data   = $this->get_campaign_data( $request );
		$stored = array(
			'id'     => $post->ID,
			'name'   => $post->post_title,
			'cause'  => $post->post_content,
			'status' => $post->post_status,
		);

		$post = wc_donation_manager()->campaigns->save( array_merge( $stored, $data ) );

		if ( is_wp_error( $post ) ) {
			return $post;
		}

		$this->update_additional_fields_for_object( $post, $request );

		return rest_ensure_response( $this->prepare_item( $post, $request ) );
	}

	/**
	 * Delete a campaign.
	 *
	 * @since 2.0.0
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response|WP_Error Whether it was deleted, or why the request was refused.
	 */
	public function delete_item( $request ) {
		$post = wc_donation_manager()->campaigns->get( (int) $request['id'] );

		if ( null === $post ) {
			return new WP_Error( 'wcdm_rest_campaign_invalid', __( 'Invalid campaign id.', 'wc-donation-manager' ), array( 'status' => 404 ) );
		}

		$previous = $this->prepare_item( $post, $request );
		$deleted  = wc_donation_manager()->campaigns->delete( (int) $request['id'], 'true' === $request->get_param( 'force' ) );

		if ( ! $deleted ) {
			return new WP_Error( 'wcdm_rest_campaign_delete_failed', __( 'The campaign could not be deleted.', 'wc-donation-manager' ), array( 'status' => 500 ) );
		}

		$response = rest_ensure_response( $previous );
		$response->set_data(
			array(
				'deleted'  => true,
				'previous' => $previous,
			)
		);

		return $response;
	}

	/**
	 * Batch-delete campaigns.
	 *
	 * @since 2.0.0
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response The result of each delete.
	 */
	public function batch_items( $request ) {
		$results = array();

		foreach ( (array) $request->get_param( 'delete' ) as $id ) {
			$deleted = wc_donation_manager()->campaigns->delete( absint( $id ), true );

			$results['delete'][] = $deleted
				? array(
					'id'      => absint( $id ),
					'deleted' => true,
				)
				: array(
					'id'      => absint( $id ),
					'deleted' => false,
					'error'   => array( 'message' => __( 'The campaign could not be deleted.', 'wc-donation-manager' ) ),
				);
		}

		return rest_ensure_response( $results );
	}

	/**
	 * Get the list options the React screens render from.
	 *
	 * @since 2.0.0
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response The options.
	 */
	public function get_options( $request ) {
		$campaigns = wc_donation_manager()->campaigns;
		$statuses  = array();
		foreach ( $campaigns->get_statuses() as $value => $label ) {
			$statuses[] = array(
				'value' => $value,
				'label' => $label,
			);
		}

		$behaviors = array(
			array(
				'value' => 'continue',
				'label' => __( 'Continue accepting donations', 'wc-donation-manager' ),
			),
			array(
				'value' => 'close',
				'label' => __( 'Close the campaign automatically', 'wc-donation-manager' ),
			),
			array(
				'value' => 'soft_close',
				'label' => __( 'Show as completed but still allow donations', 'wc-donation-manager' ),
			),
		);

		/**
		 * Filters the campaigns list columns.
		 *
		 * @since 2.0.0
		 * @param array<string, array<string, mixed>> $table_columns Columns keyed by field.
		 */
		$options['table_columns'] = apply_filters(
			'wcdm_campaign_table_columns',
			array(
				'name'        => array(
					'title'     => __( 'Name', 'wc-donation-manager' ),
					'priority'  => 10,
					'rowHeader' => true,
					'sortable'  => true,
					'hideable'  => false,
					'minWidth'  => 220,
				),
				'cause'       => array(
					'title'    => __( 'Cause', 'wc-donation-manager' ),
					'priority' => 20,
					'minWidth' => 200,
					'truncate' => true,
				),
				'goal_amount' => array(
					'title'    => __( 'Goal', 'wc-donation-manager' ),
					'priority' => 30,
					'sortable' => true,
					'align'    => 'end',
					'minWidth' => 120,
				),
				'end_date'    => array(
					'title'    => __( 'End date', 'wc-donation-manager' ),
					'priority' => 40,
					'sortable' => true,
					'minWidth' => 120,
				),
				'status'      => array(
					'title'    => __( 'Status', 'wc-donation-manager' ),
					'priority' => 50,
					'sortable' => true,
					'minWidth' => 100,
				),
			)
		);

		/**
		 * Filters the campaigns list filters.
		 *
		 * @since 2.0.0
		 * @param array<string, array<string, mixed>> $query_filters Filters keyed by field.
		 */
		$options['query_filters'] = apply_filters(
			'wcdm_campaign_query_filters',
			array(
				'status' => array(
					'label'    => __( 'Status', 'wc-donation-manager' ),
					'type'     => 'select',
					'priority' => 10,
					'options'  => $statuses,
				),
			)
		);

		/**
		 * Filters the campaigns table actions.
		 *
		 * @since 2.0.0
		 * @param array<string, mixed> $table_actions Actions keyed by id.
		 */
		$options['table_actions'] = apply_filters(
			'wcdm_campaign_table_actions',
			array(
				'add' => array( 'title' => __( 'Add campaign', 'wc-donation-manager' ) ),
			)
		);

		/**
		 * Filters the campaign form fields.
		 *
		 * @since 2.0.0
		 * @param array<string, array<string, mixed>> $form_fields Fields keyed by name.
		 */
		$options['form_fields'] = apply_filters(
			'wcdm_campaign_form_fields',
			array(
				'name'          => array(
					'type'     => 'text',
					'label'    => __( 'Name', 'wc-donation-manager' ),
					'help'     => __( 'Shown to donors on the donation product page.', 'wc-donation-manager' ),
					'priority' => 10,
					'required' => true,
					'width'    => 'full',
				),
				'cause'         => array(
					'type'     => 'editor',
					'label'    => __( 'Cause', 'wc-donation-manager' ),
					'help'     => __( 'What the campaign raises money for.', 'wc-donation-manager' ),
					'priority' => 20,
					'width'    => 'full',
					'props'    => array(
						'rows'         => 6,
						'mediaButtons' => true,
					),
				),
				'goal_amount'   => array(
					'type'     => 'number',
					'label'    => __( 'Goal amount', 'wc-donation-manager' ),
					'help'     => __( 'Leave 0 for a campaign without a goal.', 'wc-donation-manager' ),
					'priority' => 30,
					'width'    => '1/2',
				),
				'end_date'      => array(
					'type'     => 'date',
					'label'    => __( 'End date', 'wc-donation-manager' ),
					'help'     => __( 'The day the campaign closes. Empty means it never closes.', 'wc-donation-manager' ),
					'priority' => 40,
					'width'    => '1/2',
				),
				'status'        => array(
					'type'     => 'select',
					'label'    => __( 'Status', 'wc-donation-manager' ),
					'priority' => 50,
					'width'    => '1/2',
					'required' => true,
					'default'  => 'publish',
					'options'  => $statuses,
				),
				'goal_behavior' => array(
					'type'     => 'select',
					'label'    => __( 'After goal completion', 'wc-donation-manager' ),
					'help'     => __( 'What happens when the campaign reaches its goal.', 'wc-donation-manager' ),
					'priority' => 60,
					'width'    => '1/2',
					'default'  => wc_donation_manager()->options->get( 'goal_behavior', 'continue' ),
					'options'  => $behaviors,
				),
			)
		);

		$options['statuses']  = $statuses;
		$options['behaviors'] = $behaviors;

		/**
		 * Filters the campaigns options.
		 *
		 * @since 2.0.0
		 * @param array<string, mixed> $options Options keyed by key.
		 */
		$options = apply_filters( 'wcdm_campaign_options', $options );

		return rest_ensure_response( $options );
	}

	/**
	 * Get the collection params.
	 *
	 * @since 2.0.0
	 * @return array<string, mixed> Collection params.
	 */
	public function get_collection_params() {
		return array(
			'context'  => $this->get_context_param( array( 'default' => 'view' ) ),
			'search'   => array(
				'description'       => __( 'Limit results to those matching a string.', 'wc-donation-manager' ),
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'status'   => array(
				'description'       => __( 'Limit results to campaigns with one or more statuses.', 'wc-donation-manager' ),
				'type'              => 'array',
				'items'             => array( 'type' => 'string' ),
				'default'           => array( 'any' ),
				'sanitize_callback' => 'wp_parse_list',
			),
			'orderby'  => array(
				'description' => __( 'Sort collection by object attribute.', 'wc-donation-manager' ),
				'type'        => 'string',
				'default'     => 'date',
				'enum'        => array( 'date', 'id', 'name', 'goal_amount', 'end_date', 'status', 'modified' ),
			),
			'order'    => array(
				'description'       => __( 'Order sort attribute ascending or descending.', 'wc-donation-manager' ),
				'type'              => 'string',
				'default'           => 'DESC',
				'enum'              => array( 'ASC', 'DESC' ),
				'sanitize_callback' => static function ( $value ) {
					return strtoupper( (string) $value );
				},
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
	 * Get the item properties.
	 *
	 * @since 2.0.0
	 * @return array<string, mixed> Properties keyed by name.
	 */
	protected function get_properties(): array {
		return array(
			'id'            => array(
				'description' => __( 'Unique identifier for the campaign.', 'wc-donation-manager' ),
				'type'        => 'integer',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
			'name'          => array(
				'description' => __( 'Campaign name.', 'wc-donation-manager' ),
				'type'        => 'string',
				'context'     => array( 'view', 'edit' ),
				'required'    => true,
			),
			'cause'         => array(
				'description' => __( 'What the campaign raises money for.', 'wc-donation-manager' ),
				'type'        => 'string',
				'context'     => array( 'view', 'edit' ),
			),
			'status'        => array(
				'description' => __( 'Campaign status.', 'wc-donation-manager' ),
				'type'        => 'string',
				'context'     => array( 'view', 'edit' ),
				'enum'        => array_keys( wc_donation_manager()->campaigns->get_statuses() ),
			),
			'goal_amount'   => array(
				'description' => __( 'Amount the campaign aims to raise.', 'wc-donation-manager' ),
				'type'        => 'number',
				'context'     => array( 'view', 'edit' ),
			),
			'raised_amount' => array(
				'description' => __( 'Amount raised so far.', 'wc-donation-manager' ),
				'type'        => 'number',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
			'end_date'      => array(
				'description' => __( 'Date the campaign closes, in site timezone. Empty means it never closes.', 'wc-donation-manager' ),
				'type'        => 'string',
				'context'     => array( 'view', 'edit' ),
			),
			'goal_behavior' => array(
				'description' => __( 'What happens when the goal is reached.', 'wc-donation-manager' ),
				'type'        => 'string',
				'context'     => array( 'view', 'edit' ),
				'enum'        => array( 'continue', 'close', 'soft_close' ),
			),
			'products'      => array(
				'description' => __( 'Donation products linked to the campaign.', 'wc-donation-manager' ),
				'type'        => 'array',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
				'items'       => array( 'type' => 'object' ),
			),
			'date_created'  => array(
				'description' => __( 'The date the campaign was created, as GMT.', 'wc-donation-manager' ),
				'type'        => 'string',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
			'date_modified' => array(
				'description' => __( 'The date the campaign was last modified, as GMT.', 'wc-donation-manager' ),
				'type'        => 'string',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
		);
	}

	/**
	 * Get the campaign data carried by the request.
	 *
	 * @since 2.0.0
	 * @param WP_REST_Request $request Request object.
	 * @return array<string, mixed> Campaign data.
	 */
	protected function get_campaign_data( WP_REST_Request $request ): array {
		$data = array();

		foreach ( array( 'name', 'cause', 'status', 'goal_amount', 'end_date', 'goal_behavior' ) as $key ) {
			if ( $request->offsetExists( $key ) ) {
				$data[ $key ] = $request[ $key ];
			}
		}

		if ( isset( $data['goal_amount'] ) ) {
			$data['goal_amount'] = (float) $data['goal_amount'];
		}

		return $data;
	}

	/**
	 * Prepare a campaign post for the response.
	 *
	 * @since 2.0.0
	 * @param \WP_Post                $post   Campaign post.
	 * @param WP_REST_Request         $request Request object.
	 * @param array<int, string>|null $fields Field names to include; null means all.
	 * @return array<string, mixed> Prepared campaign.
	 */
	protected function prepare_item( \WP_Post $post, WP_REST_Request $request, ?array $fields = null ): array {
		$campaigns = wc_donation_manager()->campaigns;
		$behavior  = (string) get_post_meta( $post->ID, $campaigns::META['goal_behavior'], true );
		if ( '' === $behavior ) {
			$behavior = (string) wc_donation_manager()->options->get( 'goal_behavior', 'continue' );
		}

		$items = array(
			'id'            => (int) $post->ID,
			'name'          => (string) $post->post_title,
			'cause'         => (string) $post->post_content,
			'status'        => (string) $post->post_status,
			'goal_amount'   => (float) get_post_meta( $post->ID, $campaigns::META['goal_amount'], true ),
			'raised_amount' => (float) get_post_meta( $post->ID, '_raised_amount', true ),
			'end_date'      => (string) get_post_meta( $post->ID, $campaigns::META['end_date'], true ),
			'goal_behavior' => $behavior,
			'products'      => $this->prepare_products( (int) $post->ID ),
			'date_created'  => mysql_to_rfc3339( $post->post_date_gmt ),
			'date_modified' => mysql_to_rfc3339( $post->post_modified_gmt ),
		);

		/**
		 * Filters the REST response for a campaign.
		 *
		 * @since 2.0.0
		 * @param array<string, mixed> $items   Prepared fields.
		 * @param \WP_Post             $post    Campaign post.
		 * @param WP_REST_Request      $request Request object.
		 */
		$items = apply_filters( 'wcdm_rest_campaign_response', $items, $post, $request );

		if ( null === $fields ) {
			return $items;
		}

		return array_intersect_key( $items, array_flip( $fields ) );
	}

	/**
	 * Prepare the donation products linked to a campaign.
	 *
	 * @since 2.0.0
	 * @param int $campaign_id Campaign id.
	 * @return array<int, array<string, mixed>> Products, or an empty list.
	 */
	protected function prepare_products( int $campaign_id ): array {
		if ( ! $campaign_id || ! function_exists( 'wc_get_product' ) ) {
			return array();
		}

		$prepared = array();

		foreach ( wcdm_get_campaign_products( $campaign_id ) as $product_post ) {
			$product = wc_get_product( $product_post->ID );

			if ( ! $product ) {
				continue;
			}

			$prepared[] = array(
				'id'    => $product->get_id(),
				'name'  => $product->get_name(),
				'price' => $product->get_price(),
			);
		}

		return $prepared;
	}
}
