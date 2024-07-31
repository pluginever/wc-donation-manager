<?php

namespace WooCommerceDonationManager\Admin\ListTables;

defined( 'ABSPATH' ) || exit();

// Load WP_List_Table if not loaded.
if ( ! class_exists( '\WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * List table class.
 *
 * @since 1.0.0
 * @package WooCommerceDonationManager
 */
class ListTable extends \WP_List_Table {

	/**
	 * Current page URL.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $base_url;

	/**
	 * Return the sortable column specified for this request to order the results by, if any.
	 *
	 * @param string $fallback Default order.
	 *
	 * @return string
	 */
	protected function get_request_orderby( $fallback = 'id' ) {
		wp_verify_nonce( '_wpnonce' );
		$orderby = isset( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : '';
		return in_array( $orderby, $this->get_sortable_columns(), true ) ? $orderby : $fallback;
	}


	/**
	 * Return the order specified for this request, if any.
	 *
	 * @param string $fallback Default order.
	 *
	 * @return string
	 */
	protected function get_request_order( $fallback = 'DESC' ) {
		wp_verify_nonce( '_wpnonce' );
		$order = isset( $_GET['order'] ) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : '';
		return in_array( strtoupper( $order ), array( 'ASC', 'DESC' ), true ) ? strtoupper( $order ) : $fallback;
	}

	/**
	 * Return the status filter for this request, if any.
	 *
	 * @param string $fallback Default status.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	protected function get_request_status( $fallback = null ) {
		wp_verify_nonce( '_wpnonce' );
		$status = ( ! empty( $_GET['status'] ) ) ? sanitize_text_field( wp_unslash( $_GET['status'] ) ) : '';

		return empty( $status ) ? $fallback : $status;
	}

	/**
	 * Return the search filter for this request, if any.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_request_search() {
		wp_verify_nonce( '_wpnonce' );
		return ! empty( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
	}

	/**
	 * Checks if the current request has a bulk action. If that is the case it will validate and will
	 * execute the bulk method handler. Regardless if the action is valid or not it will redirect to
	 * the previous page removing the current arguments that makes this request a bulk action.
	 */
	protected function process_actions() {
		$this->_column_headers = array( $this->get_columns(), get_hidden_columns( $this->screen ), $this->get_sortable_columns() );

		// Detect when a bulk action is being triggered.
		$action = $this->current_action();
		if ( ! $action ) {
			return;
		}

		check_admin_referer( 'bulk-' . $this->_args['plural'] );

		$ids    = isset( $_GET['id'] ) ? map_deep( wp_unslash( $_GET['id'] ), 'intval' ) : array();
		$ids    = wp_parse_id_list( $ids );
		$method = 'bulk_' . $action;
		if ( array_key_exists( $action, $this->get_bulk_actions() ) && method_exists( $this, $method ) && ! empty( $ids ) ) {
			$this->$method( $ids );
		}

		if ( isset( $_SERVER['REQUEST_URI'] ) || isset( $_REQUEST['_wp_http_referer'] ) ) {
			wp_safe_redirect(
				remove_query_arg(
					array( '_wp_http_referer', '_wpnonce', 'id', 'action', 'action2' ),
					esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) )
				)
			);
			exit;
		}
	}

	/**
	 * This function renders most of the columns in the list table.
	 *
	 * @param Object|array $item The current item.
	 * @param string       $column_name The name of the column.
	 *
	 * @since 1.0.0
	 * @return string The column value.
	 */
	public function column_default( $item, $column_name ) {

		if ( is_object( $item ) && method_exists( $item, "get_$column_name" ) ) {
			$getter = "get_$column_name";

			return empty( $item->$getter( 'view' ) ) ? '&mdash;' : esc_html( $item->$getter( 'view' ) );
		} elseif ( is_array( $item ) && isset( $item[ $column_name ] ) ) {
			return empty( $item[ $column_name ] ) ? '&mdash;' : esc_html( $item[ $column_name ] );
		}

		return '&mdash;';
	}
}
