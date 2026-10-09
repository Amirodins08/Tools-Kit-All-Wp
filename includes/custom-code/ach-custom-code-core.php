<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'ACh_Custom_Code_Core' ) ) :

class ACh_Custom_Code_Core {

	/**
	 * Constructor.
	 */
	function __construct() {
		// CSS
		if ( 'yes' === get_ach_code_option( 'front_end_css_enabled', 'no' ) && '' != get_ach_code_option( 'front_end_css', '' ) ) {
			add_action( 'wp_'    . get_ach_code_option( 'front_end_css_position', 'head' ),   array( $this, 'hook_custom_css_front_end' ), PHP_INT_MAX );
		}
		if ( 'yes' === get_ach_code_option( 'back_end_css_enabled', 'no' )  && '' != get_ach_code_option( 'back_end_css', '' ) ) {
			add_action( 'admin_' . get_ach_code_option( 'back_end_css_position', 'head' ),    array( $this, 'hook_custom_css_back_end' ),  PHP_INT_MAX );
		}
		// JS
		if ( 'yes' === get_ach_code_option( 'front_end_js_enabled', 'no' )  && '' != get_ach_code_option( 'front_end_js', '' ) ) {
			add_action( 'wp_'    . get_ach_code_option( 'front_end_js_position', 'head' ),    array( $this, 'hook_custom_js_front_end' ),  PHP_INT_MAX );
		}
		if ( 'yes' === get_ach_code_option( 'back_end_js_enabled', 'no' )   && '' != get_ach_code_option( 'back_end_js', '' ) ) {
			add_action( 'admin_' . get_ach_code_option( 'back_end_js_position', 'head' ),     array( $this, 'hook_custom_js_back_end' ),   PHP_INT_MAX );
		}
		// PHP
		if ( 'yes' === get_ach_code_option( 'php_enabled', 'no' ) ) {
			$this->run_custom_php();
		}
	}

	/**
	 * get_custom_php_file_path.
	 */
	function get_custom_php_file_path( $do_mkdir = false, $do_return_dir_only = false ) {
		$upload_dir = wp_upload_dir();
		$upload_dir = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . 'ach-custom-php';
		if ( $do_mkdir && ! file_exists( $upload_dir ) ) {
			mkdir( $upload_dir, 0755, true );
		}
		return ( $do_return_dir_only ? $upload_dir : $upload_dir . DIRECTORY_SEPARATOR . 'custom-php.php' );
	}

	/**
	 * run_custom_php.
	 */
	function run_custom_php() {
		// Check for custom PHP execution stops
		if ( isset( $_GET['ach_disable_custom_php'] ) ) {
			// Maybe stop custom PHP execution on `ach_disable_custom_php`
			if ( ! function_exists( 'wp_get_current_user' ) || ! function_exists( 'is_user_logged_in' ) ) {
				include( ABSPATH . 'wp-includes/pluggable.php' );
			}
			if ( current_user_can( 'manage_woocommerce' ) ) {
				// Stop custom PHP execution
				return;
			} elseif ( ! is_user_logged_in() ) {
				// Redirect to login page
				wp_redirect( wp_login_url( add_query_arg( '', '' ) ) );
				exit;
			}
		}
		if ( $GLOBALS['pagenow'] === 'wp-login.php' ) {
			// Stop custom PHP execution if it's the login page
			return;
		}
		// Executing custom PHP code
		$file_path = $this->get_custom_php_file_path();
		if ( file_exists( $file_path ) ) {
			include_once( $file_path );
		}
	}

	/**
	 * hook_custom_js_front_end.
	 */
	function hook_custom_js_front_end() {
		echo '<script>' . get_ach_code_option( 'front_end_js', '' ) . '</script>';
	}

	/**
	 * hook_custom_js_back_end.
	 */
	function hook_custom_js_back_end() {
		echo '<script>' . get_ach_code_option( 'back_end_js', '' ) . '</script>';
	}

	/**
	 * hook_custom_css_front_end.
	 */
	function hook_custom_css_front_end() {
		echo '<style>' . get_ach_code_option( 'front_end_css', '' ) . '</style>';
	}

	/**
	 * hook_custom_css_back_end.
	 */
	function hook_custom_css_back_end() {
		echo '<style>' . get_ach_code_option( 'back_end_css', '' ) . '</style>';
	}

}

endif;

return new ACh_Custom_Code_Core();
