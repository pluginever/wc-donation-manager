<?php

namespace WooCommerceDonationManager\Admin\ListTables;

defined( 'ABSPATH' ) || exit;

/**
 * CampaignsListTable class.
 *
 * @since 1.0.0
 * @package WooCommerceDonationManager
 */
class CampaignsListTable extends ListTable {
	/**
	 * Get campaigns started
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
				'singular' => 'campaign',
				'plural'   => 'campaigns',
				'ajax'     => 'true',
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
		$order_by              = isset( $_GET['orderby'] ) ? sanitize_key( wp_unslash( $_GET['orderby'] ) ) : ''; // phpcs:ignore
		$order                 = isset( $_GET['order'] ) ? sanitize_key( wp_unslash( $_GET['order'] ) ) : ''; // phpcs:ignore
		$search                = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : ''; // phpcs:ignore
		$current_page          = isset( $_GET['paged'] ) ? sanitize_key( wp_unslash( $_GET['paged'] ) ) : 1; // phpcs:ignore
		$args                  = array(
			'post_type'      => 'wcdm_campaigns',
			'post_status'    => 'any',
			'order'          => $order,
			'order_by'       => $order_by,
			's'              => $search,
			'posts_per_page' => $per_page,
			'paged'          => $current_page,
		);

		$query       = new \WP_Query( $args );
		$this->items = $query->posts;
		$total       = $query->found_posts;

		$this->set_pagination_args(
			array(
				'total_items' => $total,
				'per_page'    => $per_page,
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
			'cb'          => '<input type="checkbox" />',
			'name'        => __( 'Name', 'wc-donation-manager' ),
			'cause'       => __( 'Cause', 'wc-donation-manager' ),
			'goal_amount' => __( 'Goal', 'wc-donation-manager' ),
			'end_date'    => __( 'End date', 'wc-donation-manager' ),
			'status'      => __( 'Status', 'wc-donation-manager' ),
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
			'goal_amount' => array( 'goal_amount', true ),
			'end_date'    => array( 'end_date', true ),
			'status'      => array( 'post_status', true ),
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
						$campaign = wcdm_get_campaign( $id );
						if ( $campaign && $campaign->delete() ) {
							++$deleted;
						}
					}
					// translators: %d: number of campaigns deleted.
					WCDM()->flash->success( sprintf( _n( '%d campaign deleted.', '%d campaigns deleted.', $deleted, 'wc-donation-manager' ), $deleted ) );
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
	 * @param Campaign $item The current campaign object.
	 *
	 * @return string Displays a checkbox.
	 * @since  1.0.0
	 */
	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="ids[]" value="%d"/>', esc_attr( $item->ID ) );
	}

	/**
	 * Renders the name column in the items list table.
	 *
	 * @param Campaign $item The current campaign object.
	 *
	 * @return string Displays the campaign name.
	 * @since  1.0.0
	 */
	public function column_name( $item ) {
		$admin_url = admin_url( 'admin.php?page=wc-donation-manager&tab=campaign' );
		$id_url    = add_query_arg( 'id', $item->ID, $admin_url );
		$actions   = array(
			'edit'   => sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( 'edit', $item->ID, $admin_url ) ), __( 'Edit', 'wc-donation-manager' ) ),
			'delete' => sprintf( '<a href="%s">%s</a>', wp_nonce_url( add_query_arg( 'action', 'delete', $id_url ), 'bulk-campaigns' ), __( 'Delete', 'wc-donation-manager' ) ),
		);

		return sprintf( '<a href="%s">%s</a> %s', esc_url( add_query_arg( 'edit', $item->ID, $admin_url ) ), esc_html( $item->post_title ), $this->row_actions( $actions ) );
	}

	/**
	 * This function renders most of the columns in the list table.
	 *
	 * @param Campaign $item The current campaign object.
	 * @param string   $column_name The name of the column.
	 *
	 * @since 1.0.0
	 */
	public function column_default( $item, $column_name ) {
		$value = '&mdash;';
		switch ( $column_name ) {
			case 'goal_amount':
				$value = sprintf(
				/* translators: 1: WC currency symbol 2: Product price */
					( '%1$s%2$.2f' ),
					get_woocommerce_currency_symbol(),
					// TODO: Need to recheck this // esc_html( $item->get_goal_amount() );
					esc_html( 'Goal amount' )
				);
				break;
			default:
				$value = parent::column_default( $item, $column_name );
		}

		return $value;
	}
}
