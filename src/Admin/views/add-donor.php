<?php
/**
 * Admin views: Add Donor
 *
 * @since 1.0.0
 * @package WooCommerceDonationManager
 */

defined( 'ABSPATH' ) || exit;

?>
<h1><?php esc_html_e( 'Add New Donor', 'wc-donation-manager' ); ?></h1>
<p><?php esc_html_e( 'You can create a new donor here. This form will create a donor and will be available on product edit page.', 'wc-donation-manager' ); ?></p>
<form method="POST" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
	<table class="form-table">
		<tbody>
		<tr valign="top">
			<th scope="row">
				<label for="name"><?php esc_html_e( 'Donor Name *', 'wc-donation-manager' ); ?></label>
			</th>
			<td>
				<input type="text" name="name" id="name" class="regular-text" value="" required/>
				<p class="description">
					<?php esc_html_e( 'Enter the name of the donor.', 'wc-donation-manager' ); ?>
				</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="donation_no"><?php esc_html_e( 'Donation No. *', 'wc-donation-manager' ); ?></label>
			</th>
			<td>
				<input type="number" min="0" step="1" name="donation_no" id="donation_no" class="regular-text" value="" required/>
				<p class="description">
					<?php esc_html_e( 'Enter the unique donation number.', 'wc-donation-manager' ); ?>
				</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="order"><?php esc_html_e( 'Order', 'wc-donation-manager' ); ?></label>
			</th>
			<td>
				<input type="text" name="order" id="order" class="regular-text" value=""/>
				<p class="description">
					<?php esc_html_e( 'Enter the name of the order.', 'wc-donation-manager' ); ?>
				</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="order_id"><?php esc_html_e( 'Order ID *', 'wc-donation-manager' ); ?></label>
			</th>
			<td>
				<input type="number" min="0" step="1" name="order_id" id="order_id" class="regular-text" value="" required/>
				<p class="description">
					<?php esc_html_e( 'Enter the order ID.', 'wc-donation-manager' ); ?>
				</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="amount"><?php esc_html_e( 'Total Amount *', 'wc-donation-manager' ); ?></label>
			</th>
			<td>
				<input type="number" min="0" step="any" name="amount" id="amount" class="regular-text" value="" required/>
				<p class="description">
					<?php esc_html_e( 'Enter the amount of the donor.', 'wc-donation-manager' ); ?>
				</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="type"><?php esc_html_e( 'Type *', 'wc-donation-manager' ); ?></label>
			</th>
			<td>
				<select id="type" name="type" style="width: 300px" required>
					<option value="recurring">Recurring</option>
					<option value="onetime">Onetime</option>
				</select>
				<p class="description">
					<?php esc_html_e( 'Select the type of the donation.', 'wc-donation-manager' ); ?>
				</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">&nbsp;</th>
			<td>
				<input type="hidden" name="action" value="wcdm_add_donor"/>
				<?php wp_nonce_field( 'wcdm_add_donor' ); ?>
				<?php submit_button( __( 'Add Donor', 'wc-donation-manager' ), 'primary', 'add_donor' ); ?>
			</td>
		</tr>
		</tbody>
	</table>
</form>
