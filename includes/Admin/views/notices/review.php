<?php
/**
 * Admin notice for review.
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
		<h3><?php esc_html_e( 'Enjoying Donation Manager?', 'wc-donation-manager' ); ?></h3>
		<p>
			<?php
			echo wp_kses_post(
				sprintf(
				// translators: %1$s: Donation Manager WP ORG link, %2$s: Coupon code.
					__( 'We hope you had a wonderful experience using %1$s. Please take a moment to show us your support by leaving a 5-star review on <a href="%2$s" target="_blank"><strong>WordPress.org</strong></a>. Thank you! 😊', 'wc-donation-manager' ),
					'<a href="ttps://wordpress.org/plugins/wc-donation-manager/" target="_blank"><strong>Donation Manager</strong></a>',
					'https://wordpress.org/support/plugin/wc-donation-manager/reviews/?filter=5#new-post'
				)
			);
			?>
		</p>
	</div>
</div>
<div class="notice-footer">
	<a class="primary" href="https://wordpress.org/support/plugin/wc-donation-manager/reviews/?filter=5#new-post" target="_blank">
		<span class="dashicons dashicons-heart"></span>
		<?php esc_html_e( 'Sure, I\'d love to help!', 'wc-donation-manager' ); ?>
	</a>
	<a href="#" data-snooze>
		<span class="dashicons dashicons-clock"></span>
		<?php esc_html_e( 'Maybe later', 'wc-donation-manager' ); ?>
	</a>
	<a href="#" data-dismiss>
		<span class="dashicons dashicons-smiley"></span>
		<?php esc_html_e( 'I\'ve already left a review', 'wc-donation-manager' ); ?>
	</a>
</div>
