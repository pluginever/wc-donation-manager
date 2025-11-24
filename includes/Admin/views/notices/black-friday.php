<?php
/**
 * Admin notice for black friday promo.
 *
 * @since 1.0.0
 * @package WooCommerceDonationManager/Admin/Views/Notices
 * @return void
 */

defined( 'ABSPATH' ) || exit;

?>
<div class="notice-body">
	<div class="notice-icon">
		<img src="<?php echo esc_attr( WCDM()->get_assets_url( 'images/black-friday-icon.png' ) ); ?>" alt="Donation Manager Black Friday Promo" />
	</div>
	<div class="notice-content">
		<h3>
			<?php esc_html_e( 'Black Friday Mega Sale! Get Flat 40% OFF on Donation Manager Pro Plugin!', 'wc-donation-manager' ); ?>
		</h3>
		<p>
			<?php
			echo wp_kses_post(
				sprintf(
				// translators: 1. Offer Percentage, 2. Coupon Code.
					__( 'Unlock premium features at an unbeatable price this Black Friday! Enjoy %1$s on Donation Manager Pro with code %2$s. Hurry, this deal ends soon! 🖤', 'wc-donation-manager' ),
					'<strong>' . esc_attr( '40% OFF' ) . '</strong>',
					'<strong>' . esc_attr( 'BFCM25' ) . '</strong>'
				)
			);
			?>
		</p>
	</div>
</div>
<div class="notice-footer">
	<div class="footer-btn" style="display: flex;">
		<a href="<?php echo esc_url( trailingslashit( WCDM()->plugin_uri ) . '?utm_source=plugin&utm_medium=notice&utm_campaign=black-friday-sale&discount=BFCM25' ); ?>" class="primary black-friday-upgrade-btn" target="_blank">
			<span class="dashicons dashicons-cart"></span>
			<?php esc_html_e( 'Claim your discount!!', 'wc-donation-manager' ); ?>
		</a>
		<a href="#" data-snooze="<?php echo esc_attr( WEEK_IN_SECONDS ); ?>">
			<span class="dashicons dashicons-clock"></span>
			<?php esc_html_e( 'Remind me later', 'wc-donation-manager' ); ?>
		</a>
		<a href="#" data-dismiss>
			<span class="dashicons dashicons-remove"></span>
			<?php esc_html_e( 'Never show this again!', 'wc-donation-manager' ); ?>
		</a>
		<a href="#" class="btn-data-dismiss" data-dismiss><span class="dashicons dashicons-dismiss"></span></a>
	</div>
	<strong class="black-friday-footer-text"><?php esc_html_e( 'Valid until December 5, 2025', 'wc-donation-manager' ); ?></strong>
</div>
