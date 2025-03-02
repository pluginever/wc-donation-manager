<?php
/**
 * Admin notice for upgrade.
 *
 * @package WooCommerceDonationManager
 * @since 1.0.0
 * @return void
 */

defined( 'ABSPATH' ) || exit;

?>
<div class="notice-body">
	<div class="notice-icon">
		<img src="<?php echo esc_attr( WCDM()->get_assets_url( 'images/plugin-icon.png' ) ); ?>" alt="WooCommerce Donation Manager">
	</div>
	<div class="notice-content">
		<h3><?php esc_attr_e( 'Flash Sale Alert!', 'wc-donation-manager' ); ?></h3>
		<p>
			<?php
			echo wp_kses_post(
				sprintf(
					// translators: %1$s: WooCommerce Donation Manager ORG link, %2$s: Coupon code.
					__( 'Get access to %1$s with a <strong>20%% discount</strong> for the next <strong>72 hours</strong> only! Use coupon code %2$s at checkout. Hurry up, the offer ends soon.', 'wc-donation-manager' ),
					'<a href="https://pluginever.com/?utm_source=plugin&utm_medium=notice&utm_campaign=flash-sale" target="_blank"><strong>All Plugins</strong></a>',
					'<strong>FLASH20</strong>'
				)
			);
			?>
		</p>
	</div>
</div>
<div class="notice-footer">
	<a class="primary" href="https://pluginever.com/?utm_source=plugin&utm_medium=notice&utm_campaign=flash-sale" target="_blank">
		<span class="dashicons dashicons-cart"></span>
		<?php esc_attr_e( 'Claim discount ow', 'wc-donation-manager' ); ?>
	</a>
	<a href="#" data-snooze="<?php echo esc_attr( MONTH_IN_SECONDS ); ?>">
		<span class="dashicons dashicons-clock"></span>
		<?php esc_attr_e( 'Maybe later', 'wc-donation-manager' ); ?>
	</a>
</div>
