<?php
/**
 * Admin views: Settings General Tab
 *
 * @since 1.0.0
 * @package WooCommerceDonationManager
 */

defined( 'ABSPATH' ) || exit;

?>
<form method="POST" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
	<h2><?php esc_html_e( 'General settings', 'wc-donation-manager' ); ?></h2>
	<p><?php esc_html_e( 'The following options are the plugin general settings. Theses options affect how the plugin will work.', 'wc-donation-manager' ); ?></p>
	<table class="form-table">
		<tbody>
		<tr valign="top">
			<th scope="row">
				<label for="wcdm_success_page"><?php esc_html_e( 'Success page', 'wc-donation-manager' ); ?></label>
			</th>
			<td>
				<select id="wcdm_success_page" name="wcdm_success_page" style="width: 300px">
					<option value="0"><?php echo esc_attr( __( 'Select page', 'wc-donation-manager' ) ); ?></option>
					<?php
					$pages = get_pages();
					foreach ( $pages as $page ) {
						echo '<option value="' . $page->ID . '">' . $page->post_title . '</option>';
					}
					?>
				</select>
				<p class="description">
					<?php esc_html_e( 'Select a success page. This page will display after completed the donation process.', 'wc-donation-manager' ); ?>
				</p>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<label for="wcdm_donor_wall_page"><?php esc_html_e( 'Donor wall page', 'wc-donation-manager' ); ?></label>
			</th>
			<td>
				<select id="wcdm_donor_wall_page" name="wcdm_donor_wall_page" style="width: 300px">
					<option value="0"><?php echo esc_attr( __( 'Select page', 'wc-donation-manager' ) ); ?></option>
					<?php
					$pages = get_pages();
					foreach ( $pages as $page ) {
						echo '<option value="' . $page->ID . '">' . $page->post_title . '</option>';
					}
					?>
				</select>
				<p class="description">
					<?php esc_html_e( 'Select a donor wall page. This page will display donor wall details.', 'wc-donation-manager' ); ?>
				</p>
			</td>
		</tr>
		</tbody>
	</table>

	<h2><?php esc_html_e( 'Default Donation Settings', 'wc-donation-manager' ); ?></h2>
	<p><?php esc_html_e( 'The following options are the plugin general settings. Theses options affect how the plugin will work.', 'wc-donation-manager' ); ?></p>

	<table class="form-table">
		<tbody>
		<tr valign="top">
			<th scope="row">
				<label for="wcdm_add_to_cart_btn_text"><?php esc_html_e( 'Add to cart button text', 'wc-donation-manager' ); ?></label>
			</th>
			<td>
				<input type="text" name="wcdm_add_to_cart_btn_text" id="wcdm_add_to_cart_btn_text" class="regular-text" value=""/>
				<p class="description">
					<?php esc_html_e( 'Enter the add to cart button text.', 'wc-donation-manager' ); ?>
				</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="wcdm_anonymous_donation"><?php esc_html_e( 'Anonymous donation', 'wc-donation-manager' ); ?></label>
			</th>
			<td>
				<label for="wcdm_anonymous_donation">
					<input name="wcdm_anonymous_donation" id="wcdm_anonymous_donation" type="checkbox" class="" value="1" checked="checked">
					<?php esc_html_e( 'Enable anonymous donation.', 'wc-donation-manager' ); ?>
				</label>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="wcdm_skip_cart"><?php esc_html_e( 'Skip cart', 'wc-donation-manager' ); ?></label>
			</th>
			<td>
				<label for="wcdm_skip_cart">
					<input name="wcdm_skip_cart" id="wcdm_skip_cart" type="checkbox" class="" value="1" checked="checked">
					<?php esc_html_e( 'Skip cart.', 'wc-donation-manager' ); ?>
				</label>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="wcdm_order_note"><?php esc_html_e( 'Disabled order note', 'wc-donation-manager' ); ?></label>
			</th>
			<td>
				<label for="wcdm_order_note">
					<input name="wcdm_order_note" id="wcdm_order_note" type="checkbox" class="" value="1" checked="checked">
					<?php esc_html_e( 'Disabled order note.', 'wc-donation-manager' ); ?>
				</label>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="wcdm_coupon_field"><?php esc_html_e( 'Disabled coupon field', 'wc-donation-manager' ); ?></label>
			</th>
			<td>
				<label for="wcdm_coupon_field">
					<input name="wcdm_coupon_field" id="wcdm_coupon_field" type="checkbox" class="" value="1" checked="checked">
					<?php esc_html_e( 'Disabled coupon field.', 'wc-donation-manager' ); ?>
				</label>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="wcdm_fast_checkout"><?php esc_html_e( 'Enable fast checkout', 'wc-donation-manager' ); ?></label>
			</th>
			<td>
				<label for="wcdm_coupon_field">
					<input name="wcdm_coupon_field" id="wcdm_coupon_field" type="checkbox" class="" value="1" checked="checked">
					<?php esc_html_e( 'Enable fast checkout', 'wc-donation-manager' ); ?>
				</label>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="wcdm_contribution_text"><?php esc_html_e( 'Contribution text', 'wc-donation-manager' ); ?></label>
			</th>
			<td>
				<input type="text" name="wcdm_contribution_text" id="wcdm_contribution_text" class="regular-text" value=""/>
				<p class="description">
					<?php esc_html_e( 'Enter the contribution text.', 'wc-donation-manager' ); ?>
				</p>
			</td>
		</tr>
		</tbody>
	</table>

	<table class="form-table">
		<tbody>
		<tr valign="top">
			<th scope="row">
				<input type="hidden" name="action" value="wcdm_settings_general"/>
				<?php wp_nonce_field( 'wcdm_settings_general' ); ?>
				<?php submit_button( __( 'Save Changes', 'wc-donation-manager' ), 'primary', 'add_campaign' ); ?>
			</th>
			<td>&nbsp;</td>
		</tr>
	</tbody>
	</table>
</form>
