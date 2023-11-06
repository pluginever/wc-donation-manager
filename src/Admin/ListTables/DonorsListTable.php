<?php

namespace WooCommerceDonationManager\Admin\ListTables;

defined( 'ABSPATH' ) || exit;

/**
 * DonorsListTable class.
 *
 * @since 1.0.0
 * @package WooCommerceDonationManager
 */
class DonorsListTable extends AbstractListTable {
	/**
	 * Get donors started
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
				'singular' => 'donor',
				'plural'   => 'donors',
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
		$per_page              = get_option( 'posts_per_page' );
		$order_by     = isset( $_GET['orderby'] ) ? sanitize_key( wp_unslash( $_GET['orderby'] ) ) : ''; // phpcs:ignore
		$order        = isset( $_GET['order'] ) ? sanitize_key( wp_unslash( $_GET['order'] ) ) : ''; // phpcs:ignore
		$search       = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : ''; // phpcs:ignore
		$current_page = isset( $_GET['paged'] ) ? sanitize_key( wp_unslash( $_GET['paged'] ) ) : 1; // phpcs:ignore
//		$args                  = array(
//			'post_type'      => 'wcdm_donors',
//			'post_status'    => 'any',
//			'order'          => $order,
//			'order_by'       => $order_by,
//			's'              => $search,
//			'posts_per_page' => $per_page,
//			'paged'          => $current_page,
//		);

		$args = array(
			'status' => array('wc-completed'),
		);

		$this->items       = wcdm_get_donors( $args );
		$this->total_count = is_array ( $this->items ) ? count( $this->items ) : 0;

		var_dump($this->total_count);
//		wp_die();

		$this->set_pagination_args(
			array(
				'total_items' => $this->total_count,
				'per_page'    => $per_page,
				'total_pages' => $this->total_pages,
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
//			'cb'          => '<input type="checkbox" />',
			'name'        => __( 'Name', 'wc-donation-manager' ),
			'email'        => __( 'Email', 'wc-donation-manager' ),
			'donation_amount' => __( 'Donation Amount.', 'wc-donation-manager' ),
			'order'       => __( 'Order', 'wc-donation-manager' ),
			'type'        => __( 'Type', 'wc-donation-manager' ),
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
			'name'        => array( 'post_title', true ),
			'donation_amount' => array( 'donation_amount', true ),
			'type'        => array( 'type', true ),
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
//	public function get_bulk_actions() {
//		return array(
//			'delete' => __( 'Delete', 'wc-donation-manager' ),
//		);
//	}

	/**
	 * Process bulk action.
	 *
	 * @param string $doaction Action name.
	 *
	 * @since 1.0.2
	 */
//	public function process_bulk_action( $doaction ) {
//		if ( ! empty( $doaction ) && check_admin_referer( 'bulk-' . $this->_args['plural'] ) ) {
//			$id  = filter_input( INPUT_GET, 'id' );
//			$ids = filter_input( INPUT_GET, 'ids', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
//			if ( ! empty( $id ) ) {
//				$ids      = wp_parse_id_list( $id );
//				$doaction = ( - 1 !== $_REQUEST['action'] ) ? $_REQUEST['action'] : $_REQUEST['action2']; // phpcs:ignore
//			} elseif ( ! empty( $ids ) ) {
//				$ids = array_map( 'absint', $ids );
//			} elseif ( wp_get_referer() ) {
//				wp_safe_redirect( wp_get_referer() );
//				exit;
//			}
//
//			switch ( $doaction ) {
//				case 'delete':
//					$deleted = 0;
//					foreach ( $ids as $id ) {
//						$donor = wcdm_get_donor( $id );
//						if ( $donor && $donor->delete() ) {
//							++$deleted;
//						}
//					}
//					// translators: %d: number of donors deleted.
//					wc_donation_manager()->add_notice( sprintf( _n( '%d donor deleted.', '%d donors deleted.', $deleted, 'wc-donation-manager' ), $deleted ) );
//					break;
//			}
//
//			wp_safe_redirect( remove_query_arg( array( 'action', 'action2', 'id', 'ids', 'paged' ) ) );
//			exit();
//		}
//
//		parent::process_bulk_actions( $doaction );
//	}

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
	 * @param Donor $item The current donor object.
	 *
	 * @since  1.0.0
	 * @return string Displays a checkbox.
	 */
//	public function column_cb( $item ) {
//		return sprintf( '<input type="checkbox" name="ids[]" value="%d"/>', esc_attr( $item->get_id() ) );
//	}

	/**
	 * Renders the name column in the items list table.
	 *
	 * @param \WC_Order $item The order object.
	 *
	 * @since  1.0.0
	 * @return string Displays the donor name.
	 */
	public function column_name( $item ) {
//		$admin_url = admin_url( 'post.php?action=edit' );
//		https://wcplugins.test/wp-admin/post.php?post=302&action=edit
//		$id_url    = add_query_arg( 'id', $item->get_id(), $admin_url );
//		$actions   = array(
//			'edit'   => sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( 'edit_donor', $item->get_id(), $admin_url ) ), __( 'Edit', 'wc-donation-manager' ) ),
//			'delete' => sprintf( '<a href="%s">%s</a>', wp_nonce_url( add_query_arg( 'action', 'delete', $id_url ), 'bulk-donors' ), __( 'Delete', 'wc-donation-manager' ) ),
//		);
//		return sprintf( '<a href="%s">%s</a> %s', esc_url( add_query_arg( 'edit_donor', $item->get_id(), $admin_url ) ), esc_html( $item->get_user()->display_name ), $this->row_actions( $actions ) );
		return sprintf( '<a href="%s">%s %s</a>', esc_url( add_query_arg( 'post', $item->get_id(), admin_url( 'post.php?action=edit' ) ) ), esc_html( $item->get_billing_first_name() ), esc_html( $item->get_billing_last_name() ) );
	}

	/**
	 * This function renders most of the columns in the list table.
	 *
	 * @param \WC_Order  $item The order object.
	 * @param string $column_name The name of the column.
	 *
	 * @since 1.0.0
	 * @return string Displays the donor table columns.
	 */
	public function column_default( $item, $column_name ) {

		$value = '&mdash;';
//		var_dump($item->get_user());
//		wp_die();

		switch ( $column_name ) {
			case 'email':
				$value = sprintf( '%1$s', esc_html( $item->get_billing_email() ) );
				break;
			case 'donation_amount':

				$donation_amount = 0.00;
				$order= wc_get_order($item->get_id());

				foreach ( $order->get_items() as $item ) {

//					var_dump($item->get_total());

					$product = $item->get_product();
					if ( $product->is_type( 'donation' ) ) {
						$donation_amount += floatval( $item->get_total() );
					}
				}

//				var_dump($donation_amount);
//				wp_die();

				$value = sprintf( '%1$s%2$.2f', esc_html( get_woocommerce_currency_symbol() ), esc_html( $donation_amount ) );
				break;
			case 'order':
				$value = sprintf(
					__( 'Order ID: #%1$s %2$s Total Amount: %3$s%4$.2f %5$s <a href="%6$s">View Order</a>', 'wc-donation-manager' ),
					esc_html( $item->get_id() ),
					'<br>',
					esc_html( get_woocommerce_currency_symbol() ),
					esc_html( $item->get_total() ),
					'<br>',
					esc_url( add_query_arg( 'post', $item->get_id(), admin_url( 'post.php?action=edit' ) ) )
					,
				);
				break;
			case 'type':
				$value = __( 'Onetime', 'wc-donation-manager');
				break;
			default:
				$value = parent::column_default( $item, $column_name );
		}

		return $value;
	}
}
