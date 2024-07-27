<?php
/**
 * Admin views: Edit Campaign
 *
 * @var \WP_POST $campaign The campaign post object.
 *
 * @since 1.0.0
 * @package WooCommerceDonationManager
 */

defined( 'ABSPATH' ) || exit;

$currency_symbol   = get_woocommerce_currency_symbol();
$campaign_products = wcdm_get_campaign_products( $campaign->ID );
?>
<h1 class="wp-heading-inline"><?php esc_html_e( 'Edit Campaign', 'wc-donation-manager' ); ?></h1>
<p><?php esc_html_e( 'Edit and update the campaign.', 'wc-donation-manager' ); ?></p>

<form class="campaign" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
	<div class="pev-poststuff">
		<div class="column-1">
			<div class="pev-card">
				<div class="pev-card__header">
					<h3 class="pev-card__title"><?php esc_html_e( 'Campaign Details', 'wc-donation-manager' ); ?></h3>
					<p class="pev-card__subtitle">
						#<?php echo esc_html( $campaign->ID ); ?>
					</p>
				</div>
				<div class="pev-card__body form-inline">
					<div class="pev-form-field">
						<label for="name">
							<?php esc_html_e( 'Campaign Name *', 'wc-donation-manager' ); ?>
						</label>
						<div class="field-group">
							<input type="text" name="name" id="name" class="regular-text" value="<?php echo esc_html( $campaign->post_title ); ?>" required/>
							<p class="description">
								<?php esc_html_e( 'Edit or update the name of the campaign.', 'wc-donation-manager' ); ?>
							</p>
						</div>
					</div>
					<div class="pev-form-field">
						<label for="cause">
							<?php esc_html_e( 'Cause', 'wc-donation-manager' ); ?>
						</label>
						<div class="field-group">
							<textarea name="cause" id="cause" class="regular-text" rows="6" placeholder="<?php esc_html_e( 'Enter the cause of the campaign...', 'wc-donation-manager' ); ?>"><?php echo wp_kses_post( $campaign->post_content ); ?></textarea>
							<p class="description">
								<?php esc_html_e( 'Edit or update the cause of the campaign.', 'wc-donation-manager' ); ?>
							</p>
						</div>
					</div>

					<div class="pev-form-field">
						<label for="goal_amount">
							<?php esc_html_e( 'Goal Amount *', 'wc-donation-manager' ); ?>
						</label>
						<div class="field-group">
							<input type="number" min="0" step="any" name="goal_amount" id="goal_amount" class="regular-text" value="<?php echo esc_attr( get_post_meta( $campaign->ID, 'goal_amount', true ) ); ?>" required/>
							<p class="description">
								<?php esc_html_e( 'Edit or update the goal amount of the campaign.', 'wc-donation-manager' ); ?>
							</p>
						</div>
					</div>

					<div class="pev-form-field">
						<label for="end_date">
							<?php esc_html_e( 'End date *', 'wc-donation-manager' ); ?>
						</label>
						<div class="field-group">
							<input type="date" name="end_date" id="end_date" class="regular-text" value="<?php echo esc_attr( get_post_meta( $campaign->ID, 'end_date', true ) ); ?>" required/>
							<p class="description">
								<?php esc_html_e( 'Edit or update the end date of the campaign.', 'wc-donation-manager' ); ?>
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="column-2">
			<div class="pev-card">
				<div class="pev-card__header">
					<h3 class="pev-card__title"><?php esc_html_e( 'Campaign overview', 'wc-donation-manager' ); ?></h3>
				</div>
				<div class="pev-card__main campaign-progress">
					<div class="progress-label">
						<label for="campaign-progressbar"><?php echo sprintf( /* translators: 1: WC currency symbol 2: Raised amount */ __( '%1$s%2$.2f raised', 'wc-donation-manager' ), esc_html( $currency_symbol ), esc_html( get_post_meta( $campaign->ID, '_raised_amount', true ) ) ); // phpcs:ignore ?></label>
						<label for="campaign-progressbar"><?php echo sprintf( /* translators: 1: WC currency symbol 2: Raised amount */ __( '%1$s%2$.2f goal', 'wc-donation-manager' ), esc_html( $currency_symbol ), esc_html( get_post_meta( $campaign->ID, '_goal_amount', true ) ) ); // phpcs:ignore ?></label>
					</div>
					<progress id="campaign-progressbar" value="<?php echo esc_attr( get_post_meta( $campaign->ID, '_raised_amount', true ) ); ?>" max="<?php echo esc_attr( get_post_meta( $campaign->ID, '_goal_amount', true ) ); ?>"><?php echo esc_html( printf( /* translators: 1: Raised amount */ __( '%1$s%%', 'wc-donation-manager' ), esc_html( get_post_meta( $campaign->ID, '_raised_amount', true ) ) ) ); ?></progress>
				</div>
			</div>
			<div class="pev-card">
				<div class="pev-card__header">
					<h3 class="pev-card__title"><?php esc_html_e( 'Actions', 'wc-donation-manager' ); ?></h3>
				</div>

				<div class="pev-card__main">
					<label for="status">
						<?php esc_html_e( 'Status', 'wc-donation-manager' ); ?>
					</label>
					<select id="status" name="status" style="width: 300px" required>
						<?php $status = esc_html( $campaign->post_status ); ?>
						<option value="Publish" <?php echo 'publish' === $status ? 'selected' : ''; ?>>Publish</option>
						<option value="Pending" <?php echo 'pending' === $status ? 'selected' : ''; ?>>Pending</option>
						<option value="Draft" <?php echo 'draft' === $status ? 'selected' : ''; ?>>Draft</option>
					</select>
					<p class="description">
						<?php esc_html_e( 'Update the campaign status.', 'wc-donation-manager' ); ?>
					</p>
				</div>

				<div class="pev-card__footer">
					<a class="del" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'delete', admin_url( 'admin.php?page=wc-donation-manager&id=' . $campaign->ID ) ), 'bulk-campaigns' ) ); ?>"><?php esc_html_e( 'Delete', 'wc-donation-manager' ); ?></a>
					<button class="button button-primary"><?php esc_html_e( 'Save Campaign', 'wc-donation-manager' ); ?></button>
				</div>
			</div>
		</div>
	</div>
	<input type="hidden" name="action" value="wcdm_edit_campaign">
	<input type="hidden" name="id" value="<?php echo esc_attr( $campaign->ID ); ?>">
	<?php wp_nonce_field( 'wcdm_edit_campaign' ); ?>
</form>
<?php if ( $campaign_products ) : ?>
	<div class="campaign-products">
		<div class="pev-poststuff">
			<div class="column-1">
				<div class="pev-card">
					<div class="pev-card__header">
						<h3 class="pev-card__title"><?php esc_html_e( 'Campaign Products', 'wc-donation-manager' ); ?></h3>
					</div>
					<div class="pev-card__body">
						<table>
							<?php
							foreach ( $campaign_products as $campaign_product ) :
								$edit_url = add_query_arg(
									array(
										'post' => $campaign_product->ID,
									),
									admin_url( 'post.php?action=edit' )
								);
								?>
								<tr class="campaign-product">
									<td>
										<div class="product-thumbnail">
											<a href="<?php echo esc_url( $edit_url ); ?>"><?php echo get_the_post_thumbnail( $campaign_product->ID, 'thumbnail' ); ?></a>
										</div>
										<div class="product-title">
											<a href="<?php echo esc_url( $edit_url ); ?>"><strong><?php echo esc_html( $campaign_product->post_title ); ?></strong></a>
											<div class="product-action">
												<a href="<?php echo esc_url( $edit_url ); ?>"><?php esc_html_e( 'Edit', 'wc-donation-manager' ); ?></a>
												<a href="<?php echo esc_url( get_the_permalink( $campaign_product->ID ) ); ?>" target="_blank"><?php esc_html_e( 'View', 'wc-donation-manager' ); ?></a>
											</div>
										</div>
									</td>
								</tr>
							<?php endforeach; ?>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>
