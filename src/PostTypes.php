<?php

namespace WooCommerceDonationManager;

defined( 'ABSPATH' ) || exit;

/**
 * Class PostTypes.
 *
 * Responsible for registering custom post types.
 *
 * @package WooCommerceDonationManager
 * @since 1.0.0
 */
class PostTypes {

	/**
	 * CPT constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'register_cpt' ] );
	}

	/**
	 * Register custom post types.
	 *
	 * @since 1.0.0
	 */
	public function register_cpt() {
		$labels = array(
			'name'               => _x( 'Campaigns', 'post type general name', 'wc-donation-manager' ),
			'singular_name'      => _x( 'Campaign', 'post type singular name', 'wc-donation-manager' ),
			'menu_name'          => _x( 'Campaigns', 'admin menu', 'wc-donation-manager' ),
			'name_admin_bar'     => _x( 'Campaign', 'add new on admin bar', 'wc-donation-manager' ),
			'add_new'            => _x( 'Add New', 'ticket', 'wc-donation-manager' ),
			'add_new_item'       => __( 'Add New Campaign', 'wc-donation-manager' ),
			'new_item'           => __( 'New Campaign', 'wc-donation-manager' ),
			'edit_item'          => __( 'Edit Campaign', 'wc-donation-manager' ),
			'view_item'          => __( 'View Campaign', 'wc-donation-manager' ),
			'all_items'          => __( 'All Campaigns', 'wc-donation-manager' ),
			'search_items'       => __( 'Search Campaigns', 'wc-donation-manager' ),
			'parent_item_colon'  => __( 'Parent Campaigns:', 'wc-donation-manager' ),
			'not_found'          => __( 'No tickets found.', 'wc-donation-manager' ),
			'not_found_in_trash' => __( 'No tickets found in Trash.', 'wc-donation-manager' ),
		);

		$args = array(
			'labels'              => apply_filters( 'wcdm_campaigns_post_type_labels', $labels ),
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
			'supports'            => [],
		);

		register_post_type( 'wcdm_campaigns', apply_filters( 'wcdm_campaigns_post_type_args', $args ) );
	}

}
