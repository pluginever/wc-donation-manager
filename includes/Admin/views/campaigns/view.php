<?php
/**
 * Admin views: Edit Campaign
 *
 * @since 1.0.0
 * @package WooCommerceDonationManager
 */

defined( 'ABSPATH' ) || exit;
$campaign_id       = filter_input( INPUT_GET, 'view_campaign', FILTER_SANITIZE_NUMBER_INT );
$campaign          = wcdm_get_campaign( $campaign_id );
$currency_symbol   = get_woocommerce_currency_symbol();
$campaign_products = wcdm_get_campaign_products( $campaign->get_id() );
?>
<h1 class="wp-heading-inline"><?php echo esc_html( $campaign->get_name() ); ?></h1>
<p><?php esc_html_e( 'The campaign details.', 'wc-donation-manager' ); ?></p>

<form class="campaign" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
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
						<div class="field-group">
							<h3 class="pev-card__title"><?php echo esc_html( $campaign->get_name() ); ?></h3>
							<p class="description">
								<?php esc_html_e( 'The name of the campaign.', 'wc-donation-manager' ); ?>
							</p>
						</div>
					</div>
					<div class="pev-form-field">
						<label for="cause">
							<?php esc_html_e( 'Cause', 'wc-donation-manager' ); ?>
						</label>
						<div class="field-group">
							<p class="description"><?php echo wp_kses_post( $campaign->get_cause() ); ?></p>
						</div>
					</div>
					<div class="pev-form-field">
						<label for="goal_amount">
							<?php esc_html_e( 'Goal Amount *', 'wc-donation-manager' ); ?>
						</label>
						<div class="field-group">
							<h3><?php echo esc_html( $currency_symbol ) . esc_html( $campaign->get_goal_amount() ); ?></h3>
							<p class="description">
								<?php esc_html_e( 'The goal amount of the campaign.', 'wc-donation-manager' ); ?>
							</p>
						</div>
					</div>
					<div class="pev-form-field">
						<label for="end_date">
							<?php esc_html_e( 'End date *', 'wc-donation-manager' ); ?>
						</label>
						<div class="field-group">
							<p><?php echo esc_html( $campaign->get_end_date() ); ?></p>
							<p class="description">
								<?php esc_html_e( 'The end date of the campaign.', 'wc-donation-manager' ); ?>
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
						<label for="campaign-progressbar"><?php echo sprintf( /* translators: 1: WC currency symbol 2: Raised amount */ __( '%1$s%2$.2f raised', 'wc-donation-manager' ), esc_html( $currency_symbol ), esc_html( get_post_meta( $campaign_id, '_raised_amount', true ) ) ); // phpcs:ignore ?></label>
						<label for="campaign-progressbar"><?php echo sprintf( /* translators: 1: WC currency symbol 2: Raised amount */ __( '%1$s%2$.2f goal', 'wc-donation-manager' ), esc_html( $currency_symbol ), esc_html( get_post_meta( $campaign_id, '_goal_amount', true ) ) ); // phpcs:ignore ?></label>
					</div>
					<progress id="campaign-progressbar" value="<?php echo esc_attr( get_post_meta( $campaign_id, '_raised_amount', true ) ); ?>" max="<?php echo esc_attr( get_post_meta( $campaign_id, '_goal_amount', true ) ); ?>"><?php echo esc_html( printf( /* translators: 1: Raised amount */ __( '%1$s%%', 'wc-donation-manager' ), esc_html( get_post_meta( $campaign_id, '_raised_amount', true ) ) ) ); ?></progress>
				</div>
			</div>
			<div class="pev-card">
				<div class="pev-card__header">
					<h3 class="pev-card__title"><?php esc_html_e( 'Actions', 'wc-donation-manager' ); ?></h3>
				</div>
				<div class="pev-card__main">
					<label for="status">
						<?php esc_html_e( 'Status: ', 'wc-donation-manager' ); ?>
						<strong><?php echo esc_html( $campaign->get_status() ); ?></strong>
					</label>
					<p class="description">
						<?php esc_html_e( 'The campaign status.', 'wc-donation-manager' ); ?>
					</p>
				</div>

				<div class="pev-card__footer">
					<a class="del" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'delete', admin_url( 'admin.php?page=wc-donation-manager&id=' . $campaign->get_id() ) ), 'bulk-campaigns' ) ); ?>"><?php esc_html_e( 'Delete', 'wc-donation-manager' ); ?></a>
					<a class="button button-primary" href="<?php echo esc_url( add_query_arg( 'edit_campaign', $campaign->get_id(), admin_url( 'admin.php?page=wc-donation-manager&tab=campaign' ) ) ); ?>"><?php esc_html_e( 'Edit Campaign', 'wc-donation-manager' ); ?></a>
				</div>
			</div>
		</div>
	</div>
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