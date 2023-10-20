<?php

namespace WooCommerceDonationManager\Frontend;

defined( 'ABSPATH' ) || exit;

/**
 * Class Frontend.
 *
 * This class is responsible for all frontend functionality.
 *
 * @since   1.0.0
 * @package WooCommerceDonationManager\Frontend
 */
class Frontend {
	/**
	 * Frontend constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue frontend scripts.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function enqueue_scripts() {
		wc_donation_manager()->enqueue_style( 'wcdm-frontend', 'css/wcdm-frontend.css' );
		wc_donation_manager()->enqueue_script( 'wcdm-frontend', 'js/wcdm-frontend.js', array( 'jquery' ) );
	}
}
