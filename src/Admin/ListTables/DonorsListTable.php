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
		$current_page = isset( $_GET['paged'] ) ? sanitize_key( wp_unslash( $_GET['paged'] ) ) : 1; // phpcs:ignore

		$args = array(
			'status'   => array( 'wc-completed' ),
			'limit'    => $per_page,
			'paged'    => $current_page,
			'paginate' => true,
			'orderby'  => $order_by,
			'order'    => $order,
		);

		$this->items       = wcdm_get_donors( $args );
		$this->total_count = wcdm_get_donors( $args, true );

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
			'name'            => __( 'Name', 'wc-donation-manager' ),
			'email'           => __( 'Email', 'wc-donation-manager' ),
			'donation_amount' => __( 'Donation Amount.', 'wc-donation-manager' ),
			'order'           => __( 'Order', 'wc-donation-manager' ),
			'type'            => __( 'Type', 'wc-donation-manager' ),
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
			'name'            => array( 'name', true ),
			'donation_amount' => array( 'donation_amount', true ),
			'type'            => array( 'type', true ),
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
	 * Define primary column.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_primary_column_name() {
		return 'name';
	}

	/**
	 * Renders the name column in the items list table.
	 *
	 * @param \WC_Order $item The order object.
	 *
	 * @since  1.0.0
	 * @return string Displays the donor name.
	 */
	public function column_name( $item ) {
		return sprintf( '<a href="%s">%s %s</a>', esc_url( add_query_arg( 'post', $item->get_id(), admin_url( 'post.php?action=edit' ) ) ), esc_html( $item->get_billing_first_name() ), esc_html( $item->get_billing_last_name() ) );
	}

	/**
	 * This function renders most of the columns in the list table.
	 *
	 * @param \WC_Order $item The order object.
	 * @param string    $column_name The name of the column.
	 *
	 * @since 1.0.0
	 * @return string Displays the donor table columns.
	 */
	public function column_default( $item, $column_name ) {

		$value = '&mdash;';

		switch ( $column_name ) {
			case 'email':
				$value = sprintf( '%1$s', esc_html( $item->get_billing_email() ) );
				break;
			case 'donation_amount':
				$donation_amount = 0.00;
				$order           = wc_get_order( $item->get_id() );

				foreach ( $order->get_items() as $item ) {
					$product = $item->get_product();
					if ( $product->is_type( 'donation' ) ) {
						$donation_amount += floatval( $item->get_total() );
					}
				}

				$value = sprintf( '%1$s%2$.2f', esc_html( get_woocommerce_currency_symbol() ), esc_html( $donation_amount ) );
				break;
			case 'order':
				$value = sprintf( /* translators: 1: Order ID 2: HTML tag 3: Woocommerce currency 4: HTML tag 5: Admin order url */
					__( 'Order ID: #%1$s %2$s Total Amount: %3$s%4$.2f %5$s <a href="%6$s">View Order</a>', 'wc-donation-manager' ),
					esc_html( $item->get_id() ),
					'<br>',
					esc_html( get_woocommerce_currency_symbol() ),
					esc_html( $item->get_total() ),
					'<br>',
					esc_url( add_query_arg( 'post', $item->get_id(), admin_url( 'post.php?action=edit' ) ) ),
				);
				break;
			case 'type':
				$value = __( 'Onetime', 'wc-donation-manager' );
				break;
			default:
				$value = parent::column_default( $item, $column_name );
		}

		return $value;
	}
}
