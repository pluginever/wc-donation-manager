<?php
/**
 * Admin views: Edit Campaign
 *
 * @since 1.0.0
 * @package WooCommerceDonationManager
 */

defined( 'ABSPATH' ) || exit;
$campaign_id = filter_input( INPUT_GET, 'edit_campaign', FILTER_SANITIZE_NUMBER_INT );
$campaign    = wcdm_get_campaign( $campaign_id );
?>
<h1 class="wp-heading-inline"><?php esc_html_e( 'Edit Campaign', 'wc-donation-manager' ); ?></h1>
<p><?php esc_html_e( 'Edit and update the campaign.', 'wc-donation-manager' ); ?></p>

<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
	<div class="pev-poststuff">
		<div class="column-1">
			<div class="pev-card">
				<div class="pev-card__header">
					<h3 class="pev-card__title"><?php esc_html_e( 'Campaign Details', 'wc-donation-manager' ); ?></h3>
					<p class="pev-card__subtitle">
						#<?php echo esc_html( $campaign->get_id() ); ?>
					</p>
				</div>
				<div class="pev-card__body form-inline">
					<div class="pev-form-field">
						<label for="name">
							<?php esc_html_e( 'Campaign Name *', 'wc-donation-manager' ); ?>
						</label>

						<input type="text" name="name" id="name" class="regular-text" value="<?php echo esc_html( $campaign->get_name() ); ?>" required/>
						<p class="description">
							<?php esc_html_e( 'Edit or update the name of the campaign.', 'wc-donation-manager' ); ?>
						</p>
					</div>
<!--					<div class="pev-form-field">-->
<!--						<label for="amount">-->
<!--							--><?php //esc_html_e( 'Amount *', 'wc-donation-manager' ); ?>
<!--						</label>-->
<!--						<input type="number" min="0" step="any" name="amount" id="amount" class="regular-text" value="--><?php //echo esc_html( $campaign->get_amount() ); ?><!--" required/>-->
<!--						<p class="description">-->
<!--							--><?php //esc_html_e( 'Edit or update the default amount of the campaign.', 'wc-donation-manager' ); ?>
<!--						</p>-->
<!--					</div>-->
					<div class="pev-form-field">
						<label for="cause">
							<?php esc_html_e( 'Cause', 'wc-donation-manager' ); ?>
						</label>
						<textarea name="cause" id="cause" rows="6" placeholder="<?php esc_html_e( 'Enter the cause of the campaign...', 'wc-donation-manager' ); ?>"><?php echo wp_kses_post( $campaign->get_cause() ); ?></textarea>
						<p class="description">
							<?php esc_html_e( 'Edit or update the cause of the campaign.', 'wc-donation-manager' ); ?>
						</p>
					</div>

					<div class="pev-form-field">
						<label for="goal_amount">
							<?php esc_html_e( 'Goal Amount *', 'wc-donation-manager' ); ?>
						</label>
						<input type="number" min="0" step="any" name="goal_amount" id="goal_amount" class="regular-text" value="<?php echo esc_html( $campaign->get_goal_amount() ); ?>" required/>
						<p class="description">
							<?php esc_html_e( 'Edit or update the goal amount of the campaign.', 'wc-donation-manager' ); ?>
						</p>
					</div>

					<div class="pev-form-field">
						<label for="end_date">
							<?php esc_html_e( 'End date *', 'wc-donation-manager' ); ?>
						</label>
						<input type="date" name="end_date" id="end_date" class="regular-text" value="<?php echo esc_html( $campaign->get_end_date() ); ?>" required/>
						<p class="description">
							<?php esc_html_e( 'Edit or update the end date of the campaign.', 'wc-donation-manager' ); ?>
						</p>
					</div>

<!--					<div class="pev-card__main">-->
<!--						--><?php //$donation_products = $campaign->get_products(); ?>
<!--						<label for="donation_products">-->
<!--							--><?php //esc_html_e( 'Donation Products', 'wc-donation-manager' ); ?>
<!--						</label>-->
<!--						<select name="donation_products[]" id="donation_products" multiple="multiple" class="regular-text" style="width: 300px">-->
<!--						--><?php //foreach ( $donation_products as $donation_product ) : ?>
<!--							<option value="--><?php //echo esc_attr( $donation_product ); ?><!--" selected="selected">--><?php //echo esc_html( wcdm_get_the_title( $donation_product ) ); ?><!--</option>-->
<!--						--><?php //endforeach; ?>
<!--						</select>-->
<!--						<p class="description">-->
<!--							--><?php //esc_html_e( 'Select the campaign donation products.', 'wc-donation-manager' ); ?>
<!--						</p>-->
<!--					</div>-->

				</div>
			</div>
		</div>
		<div class="column-2">
			<div class="pev-card">
				<div class="pev-card__header">
					<h3 class="pev-card__title"><?php esc_html_e( 'Actions', 'wc-donation-manager' ); ?></h3>
				</div>

				<div class="pev-card__main">
					<label for="status">
						<?php esc_html_e( 'Status', 'wc-donation-manager' ); ?>
					</label>
					<select id="status" name="status" style="width: 300px" required>
						<?php $status = esc_html( $campaign->get_status() ); ?>
						<option value="Publish" <?php echo 'Publish' === $status ? 'selected' : ''; ?>>Publish</option>
						<option value="Pending" <?php echo 'Pending' === $status ? 'selected' : ''; ?>>Pending</option>
						<option value="Draft" <?php echo 'Draft' === $status ? 'selected' : ''; ?>>Draft</option>
					</select>
					<p class="description">
						<?php esc_html_e( 'Update the campaign status.', 'wc-donation-manager' ); ?>
					</p>
				</div>

				<div class="pev-card__footer">
					<a class="del" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'delete', admin_url( 'admin.php?page=wc-donation-manager&id=' . $campaign->get_id() ) ), 'bulk-campaigns' ) ); ?>"><?php esc_html_e( 'Delete', 'wc-donation-manager' ); ?></a>
					<button class="button button-primary"><?php esc_html_e( 'Save Campaign', 'wc-donation-manager' ); ?></button>
				</div>
			</div>
		</div>
	</div>
	<input type="hidden" name="action" value="wcdm_edit_campaign">
	<input type="hidden" name="id" value="<?php echo esc_attr( $campaign->get_id() ); ?>">
	<?php wp_nonce_field( 'wcdm_edit_campaign' ); ?>
</form>
