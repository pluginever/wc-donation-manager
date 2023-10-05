<?php

namespace WooCommerceDonationManager\Admin\ListTables;

use WooCommerceDonationManager\Models\Campaigns;

defined( 'ABSPATH' ) || exit;

/**
 * CampaignsListTable class.
 *
 * @since 1.0.0
 * @package WooCommerceDonationManager
 */
class CampaignsListTable extends AbstractListTable {
	/**
	 * Get things started
	 *
	 * @param array $args Optional.
	 *
	 * @see WP_List_Table::__construct()
	 * @since  1.0.0
	 */
	public function __construct( $args = array() ) {
		$args         = (array) wp_parse_args(
			$args,
			array(
				'singular' => 'thing',
				'plural'   => 'things',
			)
		);
		$this->screen = get_current_screen();
		parent::__construct( $args );
	}

	/**
	 * Retrieve all the data for the table.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function prepare_items() {
		$columns               = $this->get_columns();
		$sortable              = $this->get_sortable_columns();
		$hidden                = $this->get_hidden_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$args = array(
			'limit'       => $this->get_per_page(),
			'offset'      => $this->get_offset(),
			'search'      => $this->get_search(),
			'order'       => $this->get_order( 'ASC' ),
			'orderby'     => $this->get_orderby( 'post_status' ),
			'post_status' => 'any',
		);

		$meta_props = array(
			'order_id'      => '_order_id',
			'product_id'    => '_product_id',
			'order_item_id' => '_order_item_id',
			'customer_id'   => '_customer_id',
		);
		// If the orderby param is within $meta_props.
		if ( in_array( $args['orderby'], array_keys( $meta_props ), true ) ) {
			$args['meta_key'] = $meta_props[ $args['orderby'] ]; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			$args['orderby']  = 'meta_value_num';
		}

		$this->items       = wcdm_get_campaigns( $args );
		$this->total_count = wcdm_get_campaigns( $args, true );

		$this->set_pagination_args(
			array(
				'total_items' => $this->total_count,
				'per_page'    => $this->get_per_page(),
			)
		);
	}


	/**
	 * No items found text.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function no_items() {
		esc_html_e( 'No items found.', 'wc-donation-manager' );
	}

	/**
	 * Get the table columns
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_columns() {
		return array(
			'cb'           => '<input type="checkbox" />',
			'name'         => __( 'Name', 'wc-donation-manager' ),
			'product'      => __( 'Product', 'wc-donation-manager' ),
			'order'        => __( 'Order', 'wc-donation-manager' ),
			'customer'     => __( 'Customer', 'wc-donation-manager' ),
			'date_created' => __( 'Date Created', 'wc-donation-manager' ),
		);
	}

	/**
	 * Get the table sortable columns
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_sortable_columns() {
		return array(
			'name'         => array( 'post_title', true ),
			'product'      => array( 'product_id', true ),
			'order'        => array( 'order_id', true ),
			'customer'     => array( 'customer_id', true ),
			'date_created' => array( 'date_created', true ),
		);
	}

	/**
	 * Get the table hidden columns
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_hidden_columns() {
		return array();
	}

	/**
	 * Get bulk actions
	 *
	 * since 1.0.0
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		return array(
			'delete' => __( 'Delete', 'wc-donation-manager' ),
		);
	}

	/**
	 * Process bulk action.
	 *
	 * @param string $doaction Action name.
	 *
	 * @since 1.0.2
	 */
	public function process_bulk_action( $doaction ) {
		if ( ! empty( $doaction ) && check_admin_referer( 'bulk-' . $this->_args['plural'] ) ) {
			$id  = filter_input( INPUT_GET, 'id' );
			$ids = filter_input( INPUT_GET, 'ids', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			if ( ! empty( $id ) ) {
				$ids      = wp_parse_id_list( $id );
				$doaction = ( - 1 !== $_REQUEST['action'] ) ? $_REQUEST['action'] : $_REQUEST['action2']; // phpcs:ignore
			} elseif ( ! empty( $ids ) ) {
				$ids = array_map( 'absint', $ids );
			} elseif ( wp_get_referer() ) {
				wp_safe_redirect( wp_get_referer() );
				exit;
			}

			switch ( $doaction ) {
				case 'delete':
					$deleted = 0;
					foreach ( $ids as $id ) {
						$thing = wcsp_get_thing( $id );
						if ( $thing && $thing->delete() ) {
							$deleted ++;
						}
					}
					// translators: %d: number of things deleted.
					wc_donation_manager()->add_notice( sprintf( _n( '%d thing deleted.', '%d things deleted.', $deleted, 'wc-donation-manager' ), $deleted ) );
					break;
			}

			wp_safe_redirect( remove_query_arg( array( 'action', 'action2', 'id', 'ids', 'paged' ) ) );
			exit();
		}

		parent::process_bulk_actions( $doaction );
	}

	/**
	 * Define primary column.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_primary_column_name() {
		return 'name';
	}

	/**
	 * Renders the checkbox column in the items list table.
	 *
	 * @param Thing $item The current ticket object.
	 *
	 * @since  1.0.0
	 * @return string Displays a checkbox.
	 */
	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="ids[]" value="%d"/>', esc_attr( $item->get_id() ) );
	}

	/**
	 * Renders the name column in the items list table.
	 *
	 * @param Thing $item The current ticket object.
	 *
	 * @since  1.0.0
	 * @return string Displays the ticket name.
	 */
	public function column_name( $item ) {
		$admin_url = admin_url( 'admin.php?page=wc-donation-manager&tab=thing' );
		$id_url    = add_query_arg( 'id', $item->get_id(), $admin_url );
		$actions   = array(
			'edit'   => sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( 'edit_thing', $item->get_id(), $admin_url ) ), __( 'Edit', 'wc-donation-manager' ) ),
			'delete' => sprintf( '<a href="%s">%s</a>', wp_nonce_url( add_query_arg( 'action', 'delete', $id_url ), 'bulk-things' ), __( 'Delete', 'wc-donation-manager' ) ),
		);

		return sprintf( '<a href="%s">%s</a> %s', esc_url( add_query_arg( 'edit_thing', $item->get_id(), $admin_url ) ), esc_html( $item->get_name() ), $this->row_actions( $actions ) );
	}

	/**
	 * This function renders most of the columns in the list table.
	 *
	 * @param Thing  $item The current ticket object.
	 * @param string $column_name The name of the column.
	 *
	 * @since 1.0.0
	 */
	public function column_default( $item, $column_name ) {
		$value = '&mdash;';
		switch ( $column_name ) {
			case 'product':
				$product = $item->get_product();
				if ( $product ) {
					$product_id = $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id();
					$value      = sprintf( '<a href="%s">%s</a>', esc_url( get_edit_post_link( $product_id ) ), esc_html( $product->get_name() ) );
				}
				break;
			case 'order':
				$order = $item->get_order();
				if ( $order ) {
					$value = sprintf( '<a href="%s">%s</a>', esc_url( get_edit_post_link( $order->get_id() ) ), esc_html( $order->get_order_number() ) );
				}
				break;
			case 'customer':
				$customer = $item->get_customer();
				if ( $customer ) {
					$full_name = implode( ' ', array_filter( array( $customer->get_first_name(), $customer->get_last_name() ) ) );
					$value     = sprintf( '<a href="%s">%s</a>', esc_url( get_edit_user_link( $customer->get_id() ) ), esc_html( $full_name ) );
				}
				break;
			case 'date_created':
				$date = $item->post_date;
				if ( $date ) {
					$value = sprintf( '<time datetime="%s">%s</time>', esc_attr( $date ), esc_html( date_i18n( get_option( 'date_format' ), strtotime( $date ) ) ) );
				}
				break;
			default:
				$value = parent::column_default( $item, $column_name );
		}

		return $value;
	}
}
