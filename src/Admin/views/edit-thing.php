<?php
/**
 * Admin views: Edit Thing
 *
 * @since 1.0.0
 * @package WC Donation Manager
 */

defined( 'ABSPATH' ) || exit;
$thing_id = filter_input( INPUT_GET, 'edit_thing', FILTER_SANITIZE_NUMBER_INT );
$thing    = wcsp_get_thing( $thing_id );
?>
<h1 class="wp-heading-inline">
	<?php esc_html_e( 'Edit Thing', 'wc-donation-manager' ); ?>
</h1>

<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
	<div class="pev-poststuff">
		<div class="column-1">
			<div class="pev-card">
				<div class="pev-card__header">
					<h3 class="pev-card__title"><?php esc_html_e( 'Thing Details', 'wc-donation-manager' ); ?></h3>
					<p class="pev-card__subtitle">
						#<?php echo esc_html( $thing->get_id() ); ?>
					</p>
				</div>
				<div class="pev-card__body inline--fields">

					<div class="pev-form-field">
						<label>
							<?php esc_html_e( 'Product', 'wc-donation-manager' ); ?>
						</label>
						<?php
						$product = $thing->get_product();
						if ( $product ) {
							echo sprintf( '<a href="%s">#%d %s</a>', esc_url( get_edit_post_link( $product->get_id() ) ), esc_html( $product->get_id() ), esc_html( $product->get_formatted_name() ) );
						} else {
							esc_html_e( 'No product assigned.', 'wc-donation-manager' );
						}
						?>
					</div>

					<div class="pev-form-field">
						<label>
							<?php esc_html_e( 'Order', 'wc-donation-manager' ); ?>
						</label>
						<?php
						$order = $thing->get_order();
						if ( $order ) {
							echo sprintf( '<a href="%s">#%d %s</a>', esc_url( get_edit_post_link( $order->get_id() ) ), esc_html( $order->get_id() ), esc_html( $order->get_formatted_billing_full_name() ) );
						} else {
							esc_html_e( 'No order assigned.', 'wc-donation-manager' );
						}
						?>

					</div>

					<div class="pev-form-field">
						<label>
							<?php esc_html_e( 'Customer', 'wc-donation-manager' ); ?>
						</label>
						<?php
						$customer = $thing->get_customer();
						if ( $customer ) {
							echo sprintf( '<a href="%s">#%d %s</a>', esc_url( get_edit_post_link( $customer->get_id() ) ), esc_html( $customer->get_id() ), esc_html( $thing->get_customer_name() ) );
						} else {
							esc_html_e( 'No customer assigned.', 'wc-donation-manager' );
						}
						?>
					</div>

					<div class="pev-form-field">
						<label for="thing_name"><?php esc_html_e( 'Name', 'wc-donation-manager' ); ?></label>
						<input type="text" name="thing_name" id="thing_name" value="<?php echo esc_attr( $thing->get_name() ); ?>" class="regular-text">
					</div>

				</div>
			</div>
		</div>
		<div class="column-2">
			<div class="pev-card">
				<div class="pev-card__header">
					<h3 class="pev-card__title"><?php esc_html_e( 'Actions', 'wc-donation-manager' ); ?></h3>
				</div>
				<div class="pev-card__footer">
					<a class="del" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'delete', admin_url( 'admin.php?page=wc-donation-manager&id=' . $thing->get_id() ) ), 'bulk-things' ) ); ?>"><?php esc_html_e( 'Delete', 'wc-donation-manager' ); ?></a>
					<button class="button button-primary"><?php esc_html_e( 'Save Thing', 'wc-donation-manager' ); ?></button>
				</div>
			</div>
		</div>
	</div>

	<input type="hidden" name="action" value="wcsp_save_thing">
	<input type="hidden" name="thing_id" value="<?php echo esc_attr( $thing->get_id() ); ?>">
	<?php wp_nonce_field( 'wcsp_save_thing' ); ?>
</form>
