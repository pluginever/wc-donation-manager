<?php
/**
 * Admin views: Edit Donor
 *
 * @since 1.0.0
 * @package WooCommerceDonationManager
 */

defined( 'ABSPATH' ) || exit;
$donor_id = filter_input( INPUT_GET, 'edit_donor', FILTER_SANITIZE_NUMBER_INT );
$donor    = wcdm_get_donor( $donor_id );
?>
<h1 class="wp-heading-inline"><?php esc_html_e( 'Edit Donor', 'wc-donation-manager' ); ?></h1>
<p><?php esc_html_e( 'Edit and update the donor.', 'wc-donation-manager' ); ?></p>
<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
	<div class="pev-poststuff">
		<div class="column-1">
			<div class="pev-card">
				<div class="pev-card__header">
					<h3 class="pev-card__title"><?php esc_html_e( 'Donor Details', 'wc-donation-manager' ); ?></h3>
					<p class="pev-card__subtitle">
						#<?php echo esc_html( $donor->get_id() ); ?>
					</p>
				</div>
				<div class="pev-card__body form-inline">

					<div class="pev-form-field">
						<label for="name">
							<?php esc_html_e( 'Donor Name *', 'wc-donation-manager' ); ?>
						</label>

						<input type="text" name="name" id="name" class="regular-text" value="<?php echo esc_html( $donor->get_name() ); ?>" required/>
						<p class="description">
							<?php esc_html_e( 'Edit or update the name of the donor.', 'wc-donation-manager' ); ?>
						</p>
					</div>
					<div class="pev-form-field">
						<label for="donation_no">
							<?php esc_html_e( 'Donation No. *', 'wc-donation-manager' ); ?>
						</label>
						<input type="number" min="0" step="1" name="donation_no" id="donation_no" class="regular-text" value="<?php echo esc_html( $donor->get_donation_no() ); ?>" required/>
						<p class="description">
							<?php esc_html_e( 'Edit or update the donation no.', 'wc-donation-manager' ); ?>
						</p>
					</div>
					<div class="pev-form-field">
						<label for="order">
							<?php esc_html_e( 'Order', 'wc-donation-manager' ); ?>
						</label>
						<input type="text" name="order" id="order" class="regular-text" value="<?php echo esc_html( $donor->get_order() ); ?>"/>
						<p class="description">
							<?php esc_html_e( 'Edit or update the name of the order.', 'wc-donation-manager' ); ?>
						</p>
					</div>
					<div class="pev-form-field">
						<label for="order_id">
							<?php esc_html_e( 'Order ID *', 'wc-donation-manager' ); ?>
						</label>
						<input type="number" min="0" step="1" name="order_id" id="order_id" class="regular-text" value="<?php echo esc_html( $donor->get_order_id() ); ?>" required/>
						<p class="description">
							<?php esc_html_e( 'Edit or update the order ID.', 'wc-donation-manager' ); ?>
						</p>
					</div>
					<div class="pev-form-field">
						<label for="amount">
							<?php esc_html_e( 'Amount', 'wc-donation-manager' ); ?>
						</label>
						<input type="number" min="0" step="any" name="amount" id="amount" class="regular-text" value="<?php echo esc_html( $donor->get_amount() ); ?>" required/>
						<p class="description">
							<?php esc_html_e( 'Edit or update the amount of the donor.', 'wc-donation-manager' ); ?>
						</p>
					</div>
				</div>
			</div>
		</div>
		<div class="column-2">
			<div class="pev-card">
				<div class="pev-card__header">
					<h3 class="pev-card__title"><?php esc_html_e( 'Actions', 'wc-donation-manager' ); ?></h3>
				</div>
				<div class="pev-card__main">
					<label for="type">
						<?php esc_html_e( 'Type', 'wc-donation-manager' ); ?>
					</label>
					<select id="type" name="type" style="width: 300px" required>
						<?php $type = esc_html( $donor->get_type() ); ?>
						<option value="recurring" <?php echo 'recurring' === $type ? 'selected' : '' ?>>Recurring</option>
						<option value="onetime" <?php echo 'onetime' === $type ? 'selected' : '' ?>>Onetime</option>
					</select>
					<p class="description">
						<?php esc_html_e( 'Update the donor type.', 'wc-donation-manager' ); ?>
					</p>
				</div>
				<div class="pev-card__footer">
					<a class="del" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'delete', admin_url( 'admin.php?page=wc-donation-manager&id=' . $donor->get_id() ) ), 'bulk-donors' ) ); ?>"><?php esc_html_e( 'Delete', 'wc-donation-manager' ); ?></a>
					<button class="button button-primary"><?php esc_html_e( 'Save Donor', 'wc-donation-manager' ); ?></button>
				</div>
			</div>
		</div>
	</div>
	<input type="hidden" name="action" value="wcdm_edit_donor">
	<input type="hidden" name="id" value="<?php echo esc_attr( $donor->get_id() ); ?>">
	<?php wp_nonce_field( 'wcdm_edit_donor' ); ?>
</form>
