<?php

namespace PluginEver\DonationManager;

use PluginEver\DonationManager\B8\Component;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Campaigns registry.
 *
 * Registers the campaign post type and centralizes the campaign
 * queries shared by the admin UI and the REST API.
 *
 * @since   2.0.0
 * @package PluginEver\DonationManager
 *
 * @extends Component<Plugin>
 */
class Campaigns extends Component {

	/**
	 * Campaign post type.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	const POST_TYPE = 'wcdm_campaigns';

	/**
	 * Campaign meta keys.
	 *
	 * @since 2.0.0
	 * @var array<string, string>
	 */
	const META = array(
		'goal_amount'   => 'wcdm_goal_amount',
		'end_date'      => '_end_date',
		'goal_behavior' => 'wcdm_goal_behavior',
	);

	/**
	 * Register hooks.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function register(): void {
		add_action( 'init', array( $this, 'register_post_type' ) );
	}

	/**
	 * Register the campaign post type.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function register_post_type(): void {
		$labels = array(
			'name'               => _x( 'Campaign', 'post type general name', 'wc-donation-manager' ),
			'singular_name'      => _x( 'Campaign', 'post type singular name', 'wc-donation-manager' ),
			'menu_name'          => _x( 'Campaign', 'admin menu', 'wc-donation-manager' ),
			'name_admin_bar'     => _x( 'Campaign', 'add new on admin bar', 'wc-donation-manager' ),
			'add_new'            => _x( 'Add New', 'campaign', 'wc-donation-manager' ),
			'add_new_item'       => __( 'Add New Campaign', 'wc-donation-manager' ),
			'new_item'           => __( 'New Campaign', 'wc-donation-manager' ),
			'edit_item'          => __( 'Edit Campaign', 'wc-donation-manager' ),
			'view_item'          => __( 'View Campaign', 'wc-donation-manager' ),
			'all_items'          => __( 'All Campaign', 'wc-donation-manager' ),
			'search_items'       => __( 'Search Campaign', 'wc-donation-manager' ),
			'parent_item_colon'  => __( 'Parent Campaign:', 'wc-donation-manager' ),
			'not_found'          => __( 'No campaigns found.', 'wc-donation-manager' ),
			'not_found_in_trash' => __( 'No campaigns found in Trash.', 'wc-donation-manager' ),
		);

		$args = array(
			'labels'              => $this->app->apply_filters( 'campaigns_post_type_labels', $labels ),
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => false,
			'show_in_menu'        => false,
			'show_in_nav_menus'   => false,
			'query_var'           => false,
			'can_export'          => false,
			'rewrite'             => false,
			'capability_type'     => 'post',
			'has_archive'         => false,
			'hierarchical'        => false,
			'menu_position'       => null,
			'supports'            => array(),
		);

		register_post_type( self::POST_TYPE, $this->app->apply_filters( 'campaigns_post_type_args', $args ) );
	}

	/**
	 * Get a campaign.
	 *
	 * @since 2.0.0
	 * @param int|\WP_Post $campaign Campaign id or post object.
	 * @return \WP_Post|null Campaign post, or null when not found.
	 */
	public function get( $campaign ): ?\WP_Post {
		if ( $campaign instanceof \WP_Post ) {
			$post = $campaign;
		} else {
			$post = get_post( $campaign );
		}

		if ( $post instanceof \WP_Post && self::POST_TYPE === $post->post_type ) {
			return $post;
		}

		return null;
	}

	/**
	 * Query campaigns.
	 *
	 * @since 2.0.0
	 * @param array<string, mixed> $args WP_Query arguments; `search`, `orderby`, `order`, `per_page`, `paged` and `status` are convenience keys.
	 * @return array{items: \WP_Post[], total: int} Campaigns and the total found.
	 */
	public function query( array $args = array() ): array {
		$args = wp_parse_args(
			$args,
			array(
				'search'   => '',
				'status'   => 'any',
				'orderby'  => 'date',
				'order'    => 'DESC',
				'per_page' => 20,
				'paged'    => 1,
			)
		);

		$query_args = array(
			'post_type'              => self::POST_TYPE,
			'post_status'            => (array) $args['status'],
			's'                      => (string) $args['search'],
			'orderby'                => (string) $args['orderby'],
			'order'                  => (string) $args['order'],
			'posts_per_page'         => (int) $args['per_page'],
			'paged'                  => (int) $args['paged'],
			'update_post_term_cache' => false,
			'no_found_rows'          => false,
		);

		if ( ! empty( $args['meta_key'] ) ) {
			$query_args['meta_key'] = (string) $args['meta_key']; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- deliberate sort column chosen by the controller.
		}

		$query = new \WP_Query( $query_args );

		return array(
			'items' => $query->posts,
			'total' => (int) $query->found_posts,
		);
	}

	/**
	 * Create or update a campaign.
	 *
	 * @since 2.0.0
	 * @param array<string, mixed> $data Campaign data; `name`, `cause`, `status`, `goal_amount`, `end_date` and `goal_behavior`.
	 * @return \WP_Post|\WP_Error The campaign post, or why it could not be saved.
	 */
	public function save( array $data ) {
		$id      = isset( $data['id'] ) ? absint( $data['id'] ) : 0;
		$editing = $id > 0 && null !== $this->get( $id );
		$status  = isset( $data['status'] ) ? sanitize_key( (string) $data['status'] ) : '';

		if ( '' === $status ) {
			$status = $editing ? get_post_status( $id ) : 'pending';
		}

		$post_id = wp_insert_post(
			array(
				'ID'           => $id,
				'post_type'    => self::POST_TYPE,
				'post_title'   => sanitize_text_field( (string) ( $data['name'] ?? '' ) ),
				'post_content' => wp_kses_post( (string) ( $data['cause'] ?? '' ) ),
				'post_status'  => in_array( $status, get_post_statuses(), true ) ? $status : 'pending',
				'meta_input'   => array(
					self::META['goal_amount']   => isset( $data['goal_amount'] ) ? (float) $data['goal_amount'] : 0,
					self::META['end_date']      => isset( $data['end_date'] ) ? sanitize_text_field( (string) $data['end_date'] ) : '',
					self::META['goal_behavior'] => isset( $data['goal_behavior'] ) ? sanitize_key( (string) $data['goal_behavior'] ) : $this->app->options->get( 'goal_behavior', 'continue' ),
				),
			),
			true
		);

		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		return $this->get( $post_id );
	}

	/**
	 * Delete a campaign.
	 *
	 * @since 2.0.0
	 * @param int  $id   Campaign id.
	 * @param bool $force Whether to bypass trash.
	 * @return \WP_Post|false|null Deleted post, or false when it could not be deleted.
	 */
	public function delete( int $id, bool $force = true ) {
		if ( null === $this->get( $id ) ) {
			return false;
		}

		return wp_delete_post( $id, $force );
	}

	/**
	 * Get the campaign statuses.
	 *
	 * @since 2.0.0
	 * @return array<string, string> Statuses keyed by slug.
	 */
	public function get_statuses(): array {
		return array(
			'pending' => _x( 'Pending', 'campaign status', 'wc-donation-manager' ),
			'publish' => _x( 'Active', 'campaign status', 'wc-donation-manager' ),
			'draft'   => _x( 'Draft', 'campaign status', 'wc-donation-manager' ),
			'closed'  => _x( 'Closed', 'campaign status', 'wc-donation-manager' ),
		);
	}
}
