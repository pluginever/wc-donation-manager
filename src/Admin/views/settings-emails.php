<?php
/**
 * Admin views: Settings Emails Tab
 *
 * @since 1.0.0
 * @package WooCommerceDonationManager
 */

defined( 'ABSPATH' ) || exit;

?>
<form method="POST" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
	<h2><?php esc_html_e( 'Email Settings', 'wc-donation-manager' ); ?></h2>
	<p><?php esc_html_e( 'The following options are the plugin emails settings.', 'wc-donation-manager' ); ?></p>
	<table class="form-table">
		<tbody>
		<tr valign="top">
			<th scope="row">
				<label for="wcdm_email_title"><?php esc_html_e( 'Email title', 'wc-donation-manager' ); ?></label>
			</th>
			<td>
				<input type="text" name="wcdm_email_title" id="wcdm_email_title" class="regular-text" value=""/>
				<p class="description">
					<?php esc_html_e( 'Enter the email title.', 'wc-donation-manager' ); ?>
				</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="wcdm_email_contents"><?php esc_html_e( 'Email Contents', 'wc-donation-manager' ); ?></label>
			</th>
			<td>
				<textarea rows="10" name="wcdm_email_contents" id="wcdm_email_contents" class="regular-text"></textarea>
				<p class="description">
					<?php esc_html_e( 'Enter the email contents.', 'wc-donation-manager' ); ?>
				</p>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<input type="hidden" name="action" value="wcdm_settings_emails"/>
				<?php wp_nonce_field( 'wcdm_settings_emails' ); ?>
				<?php submit_button( __( 'Save Changes', 'wc-donation-manager' ), 'primary', 'add_campaign' ); ?>
			</th>
			<td>&nbsp;</td>
		</tr>
	</tbody>
	</table>
</form>
