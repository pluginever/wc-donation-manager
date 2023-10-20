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
		add_action( 'init', array( $this, 'init' ), 1 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Init classes.
	 *
	 * Example:
	 * wc_starter_plugin()->services['frontend/my-account'] = new MyAccount();
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function init() {
		// todo add your frontend classes here.
	}

	/**
	 * Enqueue frontend scripts.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function enqueue_scripts() {
//		wc_starter_plugin()->register_style( 'frontend', 'css/frontend.css' );
//		wc_starter_plugin()->enqueue_script( 'frontend', 'js/frontend.js', array( 'jquery' ) );
	}
}
