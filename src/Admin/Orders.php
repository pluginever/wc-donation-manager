<?php

namespace WooCommerceDonationManager\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Orders class.
 *
 * Handles admin order functionality.
 *
 * @since 1.0.0
 * @package WooCommerceDonationManager
 */
class Orders {
	/**
	 * Orders Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'woocommerce_after_order_itemmeta', array( __CLASS__, 'display_order_item_meta' ), 10, 3 );
	}

	/**
	 * Show order item meta.
	 *
	 * @param int            $item_id Item ID.
	 * @param \WC_Order_Item $item Item.
	 * @param \WC_Product    $product Product.
	 *
	 * @since 1.0.0
	 */
	public static function display_order_item_meta( $item_id, $item, $product ) {
		// Show example order item meta in the order details.
		$quantity = $item->get_quantity();
		$data     = array(
			array(
				'label' => __( 'First Name', 'wc-donation-manager' ),
				'value' => __( 'John', 'wc-donation-manager' ),
			),
			array(
				'label' => __( 'Last Name', 'wc-donation-manager' ),
				'value' => __( 'Doe', 'wc-donation-manager' ),
			),
			array(
				'label' => __( 'Email', 'wc-donation-manager' ),
				'value' => __( 'john@local.com', 'wc-donation-manager' ),
			),
			array(
				'label' => __( 'Phone', 'wc-donation-manager' ),
				'value' => __( '555-555-5555', 'wc-donation-manager' ),
			),
		);
		?>
		<?php for ( $i = 0; $i < $quantity; $i ++ ) : ?>
			<table cellspacing="0" class="display_meta" style="margin-bottom: 10px;">
				<tbody>
				<tr>
					<th colspan="2">
						<?php // translators: %s is the item number. ?>
						<?php echo sprintf( '#%s:', esc_html( $i + 1 ) ); ?>
					</th>
				</tr>
				<?php foreach ( $data as $field ) : ?>
					<tr>
						<th><?php echo esc_html( $field['label'] ); ?>:</th>
						<td><?php echo wp_kses_post( $field['value'] ); ?></td>
					</tr>
				<?php endforeach; ?>
				<tr>
					<td colspan="2">
						<a href="#" class=""><?php esc_html_e( 'View Details', 'wc-donation-manager' ); ?></a>
					</td>
				</tr>
				</tbody>
			</table>
		<?php endfor; ?>
		<?php
	}
}
