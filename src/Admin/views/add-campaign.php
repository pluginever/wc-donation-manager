<?php
/**
 * Admin views: Add Campaign
 *
 * @since 1.0.0
 * @package WooCommerceDonationManager
 */

defined( 'ABSPATH' ) || exit;

?>
<h1><?php esc_html_e( 'Add New Campaign', 'wc-donation-manager' ); ?></h1>
<p><?php esc_html_e( 'You can create a new campaign here. This form will create a campaign/donation product. It will be available on woocommerce products page as well for latter edit or updates.', 'wc-donation-manager' ); ?></p>
<form method="POST" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
	<table class="form-table">
		<tbody>
		<tr valign="top">
			<th scope="row">
				<label for="name"><?php esc_html_e( 'Campaign Name *', 'wc-donation-manager' ); ?></label>
			</th>
			<td>
				<input type="text" name="name" id="name" class="regular-text" value="" required/>
				<p class="description">
					<?php esc_html_e( 'Enter the name of the campaign.', 'wc-donation-manager' ); ?>
				</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="amount"><?php esc_html_e( 'Amount *', 'wc-donation-manager' ); ?></label>
			</th>
			<td>
				<input type="number" min="0" step="any" name="amount" id="amount" class="regular-text" value="" required/>
				<p class="description">
					<?php esc_html_e( 'Enter the default amount of the campaign. It will use the WooCommerce product currency.', 'wc-donation-manager' ); ?>
				</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="goal_amount"><?php esc_html_e( 'Goal Amount', 'wc-donation-manager' ); ?></label>
			</th>
			<td>
				<input type="number" min="0" step="any" name="goal_amount" id="goal_amount" class="regular-text" value="" required/>
				<p class="description">
					<?php esc_html_e( 'Enter the goal amount of the campaign.', 'wc-donation-manager' ); ?>
				</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="donation_products"><?php esc_html_e( 'Donation Products', 'wc-donation-manager' ); ?></label>
			</th>
			<td>
				<select name="donation_products[]" id="donation_products" multiple="multiple" class="regular-text"></select>
				<p class="description">
					<?php esc_html_e( 'Select the campaign donation products.', 'wc-donation-manager' ); ?>
				</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="cause"><?php esc_html_e( 'Cause', 'wc-donation-manager' ); ?></label>
			</th>
			<td>
				<textarea name="cause" id="cause" rows="6" placeholder="<?php esc_html_e( 'Enter the cause of the campaign...', 'wc-donation-manager' ); ?>"></textarea>
				<p class="description">
					<?php esc_html_e( 'Enter the cause of the campaign.', 'wc-donation-manager' ); ?>
				</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="status"><?php esc_html_e( 'Status', 'wc-donation-manager' ); ?></label>
			</th>
			<td>
				<select id="status" name="status" style="width: 300px" required>
					<option value="Publish">Publish</option>
					<option value="Pending">Pending</option>
					<option value="Draft">Draft</option>
				</select>
				<p class="description">
					<?php esc_html_e( 'Select the campaign status.', 'wc-donation-manager' ); ?>
				</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">&nbsp;</th>
			<td>
				<input type="hidden" name="action" value="wcdm_add_campaign"/>
				<?php wp_nonce_field( 'wcdm_add_campaign' ); ?>
				<?php submit_button( __( 'Add Campaign', 'wc-donation-manager' ), 'primary', 'add_campaign' ); ?>
			</td>
		</tr>
		</tbody>
	</table>
</form>
