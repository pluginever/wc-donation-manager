<?php
/**
 * Customer completed donation email (plain text)
 *
 * @since 1.0.0
 * @package WooCommerceDonationManager\Templates\Emails\Plain
 */

defined( 'ABSPATH' ) || exit;

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n";
echo esc_html( wp_strip_all_tags( $email_heading ) );
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

/* translators: %s: Customer first name */
echo sprintf( esc_html__( 'Hi %s,', 'wc-donation-manager' ), esc_html( $order->get_billing_first_name() ) ) . "\n\n";
/* translators: %s: Site title */
echo esc_html__( 'We have finished processing your donation.', 'wc-donation-manager' ) . "\n\n";

echo "\n----------------------------------------\n\n";

printf( /* translators: 1: Woocommerce currency symbol 2: Donated amount */ esc_html__( 'Your donated amount: %1$s%2$.2f', 'wc-donation-manager' ), esc_html( get_woocommerce_currency_symbol() ), floatval( $donation_amount ) );

echo "\n\n----------------------------------------\n\n";

/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo esc_html( wp_strip_all_tags( wptexturize( $additional_content ) ) );
	echo "\n\n----------------------------------------\n\n";
}

echo wp_kses_post( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );
