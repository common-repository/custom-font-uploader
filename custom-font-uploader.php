<?php
/**
 * Plugin Name: Wbcom Designs - Custom Font Uploader
 * Plugin URI: https://wbcomdesigns.com/downloads/custom-font-uploader/
 * Description: Enhance your site's typography with Google Web Fonts and custom uploads, no API needed. Personalize fonts easily and host them on your server for performance and privacy.
 * Version: 2.4.0
 * Author: Wbcom Designs
 * Author URI: https://wbcomdesigns.com
 * Text Domain: cfup
 *
 * @package custom-font-uploader
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}
/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'CUSTOM_FONT_UPLOADER_VERSION', '2.4.0' );

// Defining constants.
$uploads_dir = wp_upload_dir();
$cons        = array(
	'CUSTOM_FONT_UPLOADER_PLUGIN_PATH'      => plugin_dir_path( __FILE__ ),
	'CUSTOM_FONT_UPLOADER_PLUGIN_URL'       => plugin_dir_url( __FILE__ ),
	'CUSTOM_FONT_UPLOADER_UPLOADS_DIR_URL'  => $uploads_dir['baseurl'] . '/custom_fonts/',
	'CUSTOM_FONT_UPLOADER_UPLOADS_DIR_PATH' => $uploads_dir['basedir'] . '/custom_fonts/',
	'CUSTOM_FONT_UPLOADER_TEXT_DOMAIN'      => 'cfup',
);
foreach ( $cons as $con => $value ) {
	define( $con, $value );
}

// Include needed files.
$include_files = array(
	'inc/cfup-scripts.php',
	'inc/cfup-functions.php',
	'admin/cfup-admin.php',
	'admin/class-cfup-admin-feedback.php',
	'admin/wbcom/wbcom-admin-settings.php',
);
foreach ( $include_files as $include_file ) {
	include_once plugin_dir_path( __FILE__ ) . $include_file;
}

add_action( 'init', 'cfu_load_textdomain' );
/**
 * Load plugin textdomain.
 *
 * @since 1.0.0
 */
function cfu_load_textdomain() {
	$locale = apply_filters( 'plugin_locale', get_locale(), 'cfup' );
	load_textdomain( 'cfup', 'languages/cfup-' . $locale . '.mo' );
	load_plugin_textdomain( 'cfup', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
}

// Plugin deactivation hook.
register_deactivation_hook( __FILE__, 'cfu_deactivation' );

/**
 * Plugin deactivation functionality.
 *
 * @since  1.0.0
 * @author Wbcom Designs
 */
function cfu_deactivation() {
	/*
	delete_option( 'custom_font_data' );
	delete_option( 'cfupgooglefonts_data' );
	delete_option( 'font_file_name' );
	delete_option( 'googlefont_file_name' );
	*/
}

// Plugin activation hook.
register_activation_hook( __FILE__, 'cfu_activation' );

/**
 * Plugin activation functionality.
 *
 * @since  1.0.0
 * @author Wbcom Designs
 */
function cfu_activation() {
	$cfu_upload     = wp_upload_dir();
	$cfu_upload_dir = $cfu_upload['basedir'];
	$cfu_upload_dir = $cfu_upload_dir . '/custom_fonts/';
	if ( ! file_exists( $cfu_upload_dir ) ) {
		mkdir( $cfu_upload_dir, 0755, true );
	}
}

// Settings link for custom font panel.
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'cfu_admin_page_link' );

/**
 * Settings link for custom font panel.
 *
 * @since  1.0.0
 * @author Wbcom Designs
 * @param  string $links contains plugin setting url.
 */
function cfu_admin_page_link( $links ) {
	$cfu_links = array(
		'<a href="' . admin_url( 'admin.php?page=custom-font-uploader-settings' ) . '">' . __( 'Settings', 'cfup' ) . '</a>',
		'<a href="https://wbcomdesigns.com/contact/" target="_blank">' . __( 'Support', 'cfup' ) . '</a>',
	);
	return array_merge( $links, $cfu_links );
}

/**
 * Redirect to plugin settings page after activated.
 *
 * @since  1.0.0
 *
 * @param string $plugin Path to the plugin file relative to the plugins directory.
 */
function cfu_activation_redirect_settings( $plugin ) {

	if ( plugin_basename( __FILE__ ) === $plugin ) {
		if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'activate' && isset( $_REQUEST['plugin'] ) && $_REQUEST['plugin'] == $plugin) { //phpcs:ignore
			wp_safe_redirect( admin_url( 'admin.php?page=custom-font-uploader-settings' ) );
			exit;
		}
	}
	if ( $plugin == $_REQUEST['plugin'] && class_exists( 'Buddypress' ) ) {//phpcs:ignore
		if ( isset( $_REQUEST['action'] ) && $_REQUEST['action']  == 'activate-plugin' && isset( $_REQUEST['plugin'] ) && $_REQUEST['plugin'] == $plugin) { //phpcs:ignore		
			set_transient( '_cfu_is_new_install', true, 30 );
		}
	}
}
add_action( 'activated_plugin', 'cfu_activation_redirect_settings' );

/**
 * cfu_do_activation_redirect
 *
 * @return void
 */
function cfu_do_activation_redirect() {
	if ( get_transient( '_cfu_is_new_install' ) ) {
		delete_transient( '_cfu_is_new_install' );
		wp_safe_redirect( admin_url( 'admin.php?page=custom-font-uploader-settings' ) );

	}
}
add_action( 'admin_init', 'cfu_do_activation_redirect' );
