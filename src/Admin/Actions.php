<?php

namespace WooCommerceDonationManager\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Actions class.
 *
 * All actions related to the admin area
 * should be added here.
 *
 * @since 1.0.0
 * @package WooCommerceDonationManager
 */
class Actions {

	/**
	 * Actions constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_post_wcsp_add_thing', array( __CLASS__, 'add_thing' ) );
	}

	/**
	 * Add a thing.
	 *
	 * @since 1.0.0
	 */
	public static function add_thing() {
		check_admin_referer( 'wcsp_add_thing' );
		$referer     = wp_get_referer();
		$status      = isset( $_POST['thing_status'] ) ? sanitize_text_field( wp_unslash( $_POST['thing_status'] ) ) : 'new';
		$customer_id = isset( $_POST['thing_customer'] ) ? absint( $_POST['thing_customer'] ) : 0;
		$order_id    = isset( $_POST['thing_order'] ) ? absint( $_POST['thing_order'] ) : 0;
		$data        = array(
			'name'       => isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '',
			'product_id' => isset( $_POST['thing_product'] ) ? absint( $_POST['thing_product'] ) : 0,
			'status'     => 'new',
		);

		if ( empty( $data['name'] ) ) {
			wc_donation_manager()->add_notice( __( 'Please enter a name for the thing.', 'wc-donation-manager' ), 'error' );
			wp_safe_redirect( $referer );
			exit;
		}
		if ( empty( $data['product_id'] ) ) {
			wc_donation_manager()->add_notice( __( 'Please select a product for the thing.', 'wc-donation-manager' ), 'error' );
			wp_safe_redirect( $referer );
			exit;
		}
		$data['status'] = 'publish';
		$thing          = wcsp_create_thing( $data );
		if ( is_wp_error( $thing ) ) {
			wc_donation_manager()->add_notice( $thing->get_error_message(), 'error' );
			wp_safe_redirect( $referer );
			exit;
		}

		$product = $thing->get_product();
		$order   = wc_get_order( $order_id );
		if ( 'create_order' === $status && $product ) {
			$order_id = $thing->create_order( $customer_id );
			if ( is_wp_error( $order_id ) ) {
				wc_donation_manager()->add_notice( $order_id->get_error_message(), 'error' );
				wp_safe_redirect( $referer );
				exit;
			}
			$notice = sprintf(
				/* translators: %1$s: order edit link, %2$s: order id */
				__( 'Thing has been created and created an associated order. <a href="%1$s">Edit order #%2$s</a>', 'wc-donation-manager' ),
				esc_url( admin_url( 'post.php?post=' . $order->get_id() . '&action=edit' ) ),
				esc_html( $order->get_id() )
			);
		} elseif ( 'existing_order' === $status && $product && $order ) {
			$order_item_id = $thing->add_to_order( $order_id );
			if ( is_wp_error( $order_item_id ) ) {
				wc_donation_manager()->add_notice( $order_item_id->get_error_message(), 'error' );
				wp_safe_redirect( $referer );
				exit;
			}
			$notice = sprintf(
				/* translators: %1$s: order edit link, %2$s: order id */
				__( 'Thing has been created and updated the associated order. <a href="%1$s">Edit order #%2$s</a>', 'wc-donation-manager' ),
				esc_url( admin_url( 'post.php?post=' . $order->get_id() . '&action=edit' ) ),
				esc_html( $order->get_id() )
			);
		} else {
			$notice = sprintf(
				/* translators: %1$s: thing edit link, %2$s: thing id */
				__( 'Thing has been created. <a href="%1$s">Edit thing #%2$s</a>', 'wc-donation-manager' ),
				esc_url( admin_url( 'admin.php?page=wc-donation-manager&edit_thing=' . $thing->get_id() ) ),
				esc_html( $thing->get_id() )
			);
		}

		wc_donation_manager()->add_notice( $notice );
		wp_safe_redirect( $referer );
		exit;
	}

}
