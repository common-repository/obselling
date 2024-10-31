<?php
/**
 * Plugin Name: Obselling
 * Plugin URI: https://obselling.com/
 * Description: Sell other products of an obsolete product or out of stock.
 * Version: 1.2.4
 * Author: Obselling
 * Author URI: https://obselling.com/
 * Text Domain: obselling
 * Domain Path: /languages
 * License: GPLv3
 * Requires at least: 4.7
 * Requires PHP: 7.0
 */

defined( 'ABSPATH' ) || exit;

define( 'OBSELLING_VERSION', '1.2.4' );

define( 'OBSELLING_PLUGIN', __FILE__ );

define( 'OBSELLING_PLUGIN_BASENAME', plugin_basename( OBSELLING_PLUGIN ) );

define( 'OBSELLING_PLUGIN_NAME', trim( dirname( OBSELLING_PLUGIN_BASENAME ), '/' ) );

define( 'OBSELLING_PLUGIN_DIR', untrailingslashit( dirname( OBSELLING_PLUGIN ) ) );

define( 'OBSELLING_OBSOLETE_PRODUCTS_FREEMIUM_LIMIT', 3 );

define( 'OBSELLING_RECOMMENDED_PRODUCTS_FREEMIUM_LIMIT', 1 );

define( 'OBSELLING_WEBSITE_URL', "https://obselling.com/" );

define( 'OBSELLING_DIR_LANGUAGES', OBSELLING_PLUGIN_NAME . '/languages/');

function obselling_on_activation() {
	$error = obselling_verify_requirements();

	if( $error ) {
			die( $error );
	}
}

register_activation_hook( __FILE__, 'obselling_on_activation' );

add_action( 'plugins_loaded', 'obselling_on_plugins_loaded' );

function obselling_on_plugins_loaded() {
	add_action( 'admin_notices', 'obselling_requirement_notices' );
}

function obselling_verify_requirements(){
	$message = '';

	if ( ! class_exists( 'WooCommerce' ) ) {
		obselling_load_textdomain();
		$message = esc_html__( 'WooCommerce is not installed or not activated. You can download %s here.', 'obselling' );
		$message = sprintf( $message, '<a href="https://wordpress.org/plugins/woocommerce/" target="_blank">WooCommerce</a>' );
	}

	return $message;
}

function obselling_requirement_notices() {
	$error = obselling_verify_requirements();

	if( $error ) {
		obselling_load_textdomain();
		echo '<div class="error"><p><strong>' . __( 'There is a problem with Obselling:', 'obselling' ) . '</strong> ' . esc_html( $error ) . '</p></div>';
	}
}

function obselling_load_textdomain(){
		load_plugin_textdomain( 'obselling', false, OBSELLING_DIR_LANGUAGES );
}

function obselling_init(){
	obselling_load_textdomain();
}

add_action( 'init', 'obselling_init', 10, 0);

require_once OBSELLING_PLUGIN_DIR . '/includes/functions.php';

if ( is_admin() ) {
	require_once OBSELLING_PLUGIN_DIR . '/admin/admin.php';
}

add_action( 'wp_enqueue_scripts', 'obselling_enqueue_scripts', 10, 0 );

function obselling_enqueue_scripts() {
	wp_enqueue_style( 'obselling',
		plugins_url( 'includes/css/style.css', OBSELLING_PLUGIN_BASENAME ),
		array(), OBSELLING_VERSION, 'all'
	);

	// right-to-left styling
	if ( is_rtl() ) {
		wp_enqueue_style( 'obselling-rtl',
			plugins_url( 'includes/css/style-rtl.css', OBSELLING_PLUGIN_BASENAME ),
			array(), OBSELLING_VERSION, 'all'
		);
	}
}
