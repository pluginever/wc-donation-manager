<?php
/**
 * Customer completed donation email
 *
 * @since 1.0.0
 * @package WooCommerceDonationManager\Templates\Emails
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php /* translators: %s: Customer first name */ ?>
	<p><?php printf( esc_html__( 'Hi %s,', 'wc-donation-manager' ), esc_html( $order->get_billing_first_name() ) ); ?></p>
	<p><?php esc_html_e( 'We have finished processing your donation.', 'wc-donation-manager' ); ?></p>
<?php if ( $donation_amount ) { ?>
	<p><?php printf( /* translators: 1: Woocommerce currency symbol 2: Donated amount */ esc_html__( 'Your donated amount: %1$s%2$.2f', 'wc-donation-manager' ), esc_html( get_woocommerce_currency_symbol() ), floatval( $donation_amount ) ); ?></p>
	<?php
}

/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
