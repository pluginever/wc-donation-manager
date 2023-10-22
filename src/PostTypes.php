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
		add_action( 'init', array( $this, 'register_cpt_donors' ) );
	}

	/**
	 * Register custom post type donors.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function register_cpt_donors() {
		$labels = array(
			'name'               => _x( 'Donor', 'post type general name', 'wc-donation-manager' ),
			'singular_name'      => _x( 'Donor', 'post type singular name', 'wc-donation-manager' ),
			'menu_name'          => _x( 'Donor', 'admin menu', 'wc-donation-manager' ),
			'name_admin_bar'     => _x( 'Donor', 'add new on admin bar', 'wc-donation-manager' ),
			'add_new'            => _x( 'Add New', 'donor', 'wc-donation-manager' ),
			'add_new_item'       => __( 'Add New Donor', 'wc-donation-manager' ),
			'new_item'           => __( 'New Donor', 'wc-donation-manager' ),
			'edit_item'          => __( 'Edit Donor', 'wc-donation-manager' ),
			'view_item'          => __( 'View Donor', 'wc-donation-manager' ),
			'all_items'          => __( 'All Donor', 'wc-donation-manager' ),
			'search_items'       => __( 'Search Donor', 'wc-donation-manager' ),
			'parent_item_colon'  => __( 'Parent Donor:', 'wc-donation-manager' ),
			'not_found'          => __( 'No donors found.', 'wc-donation-manager' ),
			'not_found_in_trash' => __( 'No donors found in Trash.', 'wc-donation-manager' ),
		);

		$args = array(
			'labels'              => apply_filters( 'wcdm_donors_post_type_labels', $labels ),
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

		register_post_type( 'wcdm_donors', apply_filters( 'wcdm_donors_post_type_args', $args ) );
	}
}
