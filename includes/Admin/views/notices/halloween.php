<?php
/**
 * Admin notice: Halloween offer.
 *
 * @since 1.0.7
 * @return void
 *
 * @package WooCommerceDonationManager\Admin\Views\Notices
 */

defined( 'ABSPATH' ) || exit;

$plugin_url = defined( 'WCDM_PRO_VERSION' ) ? trailingslashit( WCDM()->author_uri ) . 'plugins/' : trailingslashit( WCDM()->plugin_uri );

?>
<div class="notice-body">
	<div class="notice-icon">
		<img src="<?php echo esc_url( WCDM()->get_assets_url( 'images/halloween-icon.svg' ) ); ?>" alt="Donation Manager Halloween Offer">
	</div>
	<div class="notice-content">
		<h3>
			<?php esc_html_e( 'Limited Time Offer! PluginEver Halloween Sale: 30% OFF!!', 'wc-donation-manager' ); ?>
		</h3>
		<p>
			<?php
			echo wp_kses_post(
				sprintf(
					// translators: 1.Offer Percentage, 2. Coupon Code.
					__( 'Spectacular Halloween Deal! Get %1$s on all premium plugins with code %2$s. Don\'t miss out — this offer vanishes soon! 👻', 'wc-donation-manager' ),
					'<strong>' . esc_attr( '30% OFF' ) . '</strong>',
					'<strong>' . esc_attr( 'EVERSAVE30' ) . '</strong>'
				)
			);
			?>
		</p>
	</div>
</div>
<div class="notice-footer">
	<div class="footer-btn">
		<a href="<?php echo esc_url( trailingslashit( $plugin_url ) . '?utm_source=plugin&utm_medium=notice&utm_campaign=halloween-sale&discount=EVERSAVE30' ); ?>" class="primary halloween-upgrade-btn" target="_blank">
			<span class="dashicons dashicons-cart"></span>
			<?php esc_html_e( 'Claim your discount!!', 'wc-donation-manager' ); ?>
		</a>
		<a href="#" class="halloween-remind-btn" data-snooze="<?php echo esc_attr( WEEK_IN_SECONDS ); ?>">
			<span class="dashicons dashicons-clock"></span>
			<?php esc_html_e( 'Remind me later', 'wc-donation-manager' ); ?>
		</a>
		<a href="#" class="primary halloween-remove-btn" data-dismiss>
			<span class="dashicons dashicons-remove"></span>
			<?php esc_html_e( 'Never show this again!', 'wc-donation-manager' ); ?>
		</a>
		<a href="#" class="primary halloween-dismiss-btn" data-dismiss>
			<span class="dashicons dashicons-dismiss"></span>
			<?php esc_html_e( 'DISMISS', 'wc-donation-manager' ); ?>
		</a>
	</div>
	<strong class="halloween-footer-text"><?php esc_html_e( 'Valid until November 8, 2025', 'wc-donation-manager' ); ?></strong>
</div>
