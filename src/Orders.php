<?php

namespace WooCommerceDonationManager;

defined( 'ABSPATH' ) || exit;

/**
 * Orders class.
 *
 * Handles checkout functionality.
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
	protected function __construct() {
		add_action( 'woocommerce_order_item_meta_end', array( __CLASS__, 'display_order_item_meta' ), 10, 3 );
	}

	/**
	 * Display order item meta.
	 *
	 * @param int            $item_id Item ID.
	 * @param \WC_Order_Item $item Item data.
	 * @param \WC_Order      $order Order data.
	 *
	 * @since 1.0.0
	 */
	public static function display_order_item_meta( $item_id, $item, $order ) {
		$quantity = $item->get_quantity();
		$data     = array(
			array(
				'key'   => __( 'first_name', 'wc-donation-manager' ),
				'label' => __( 'First Name', 'wc-donation-manager' ),
				'value' => __( 'John', 'wc-donation-manager' ),
			),
			array(
				'key'   => __( 'last_name', 'wc-donation-manager' ),
				'label' => __( 'Last Name', 'wc-donation-manager' ),
				'value' => __( 'Doe', 'wc-donation-manager' ),
			),
			array(
				'key'   => __( 'email', 'wc-donation-manager' ),
				'label' => __( 'Email', 'wc-donation-manager' ),
				'value' => __( 'john@local.com', 'wc-donation-manager' ),
			),
			array(
				'key'   => __( 'phone', 'wc-donation-manager' ),
				'label' => __( 'Phone', 'wc-donation-manager' ),
				'value' => __( '555-555-5555', 'wc-donation-manager' ),
			),
		);
		?>
		<ul class="wc-item-meta">
			<?php for ( $i = 0; $i < $quantity; $i ++ ) : ?>
				<li>
					<strong class="wc-item-meta-label">
						<?php echo sprintf( '#%s:', esc_html( $i + 1 ) ); ?>
					</strong>
					<ul style="clear:both;">
						<?php foreach ( $data as $item_data ) : ?>
							<li class="<?php echo esc_attr( sanitize_html_class( $item_data['key'] ) ); ?>">
								<?php echo sprintf( '<strong>%s</strong>: %s', esc_html( $item_data['label'] ), esc_html( $item_data['value'] ) ); ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</li>
			<?php endfor; ?>
		</ul>
		<?php
	}
}
