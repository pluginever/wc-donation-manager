<?php

defined( 'ABSPATH' ) || exit;

?>
<h1><?php esc_html_e( 'Add New Campaign', 'wc-donation-manager' ); ?></h1>
<p><?php esc_html_e( 'You can create a new campaign here. This form will create a campaign and will be available on product edit page.', 'wc-donation-manager' ); ?></p>
<form method="POST" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
	<table class="form-table">
		<tbody>
		<tr valign="top">
			<th scope="row">
				<label for="name"><?php esc_html_e( 'Campaign Name', 'wc-donation-manager' ); ?></label>
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
				<label for="amount"><?php esc_html_e( 'Amount', 'wc-donation-manager' ); ?></label>
			</th>
			<td>
				<input type="number" name="amount" id="amount" class="regular-text" value="" required/>
				<p class="description">
					<?php esc_html_e( 'Enter the amount of the campaign. It will use the WooCommerce product currency.', 'wc-donation-manager' ); ?>
				</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="goal"><?php esc_html_e( 'Goal Amount', 'wc-donation-manager' ); ?></label>
			</th>
			<td>
				<input type="number" name="goal" id="goal" class="regular-text" value="" required/>
				<p class="description">
					<?php esc_html_e( 'Enter the goal amount of the campaign.', 'wc-donation-manager' ); ?>
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
					<option value="published">Published</option>
					<option value="private">Private</option>
					<option value="draft">Draft</option>
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
