<?php
/**
 * Admin notice for upgrade.
 *
 * @since 1.0.0
 * @package WooCommerceDonationManager\Admin\Views\Notices
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
					// translators: %1$s: WooCommerce Donation Manager Pro plugin link, %2$s: Coupon code.
					__( 'Enjoy a  <strong>10%% discount</strong> on %1$s! Use coupon code %2$s at checkout to grab the deal. Don’t miss out — this offer won’t last forever!', 'wc-donation-manager' ),
					'<a href="https://pluginever.com/plugins/woocommerce-donation-manager-pro/?utm_source=plugin&utm_medium=notice&utm_campaign=flash-sale" target="_blank"><strong>WC Donation Manager Pro</strong></a>',
					'<strong>FLASH10</strong>'
				)
			);
			?>
		</p>
	</div>
</div>
<div class="notice-footer">
	<a class="primary" href="https://pluginever.com/plugins/woocommerce-donation-manager-pro/?utm_source=plugin&utm_medium=notice&utm_campaign=flash-sale" target="_blank">
		<span class="dashicons dashicons-cart"></span>
		<?php esc_attr_e( 'Upgrade now', 'wc-donation-manager' ); ?>
	</a>
	<a href="#" data-snooze="<?php echo esc_attr( MONTH_IN_SECONDS ); ?>">
		<span class="dashicons dashicons-clock"></span>
		<?php esc_attr_e( 'Maybe later', 'wc-donation-manager' ); ?>
	</a>
</div>
