<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! defined( 'ACH_CODE_ID' ) ) {
	/**
	 * ACH_CODE_ID.
	 *
	 * Should be named `ach_code`, but is named `ach_custom_code` for backwards compatibility.
	 *
	 */
	define( 'ACH_CODE_ID', 'ach_custom_code' );
}

if ( ! function_exists( 'get_ach_code_option' ) ) {
	/**
	 * get_ach_code_option.
	 */
	function get_ach_code_option( $option, $default = false ) {
		return get_option( ACH_CODE_ID . '_' . $option, $default );
	}
}

if ( ! class_exists( 'ACh_CODE' ) ) :

/**
 * Main ACh_CODE Class
 */
final class ACh_CODE {

	/**
	 * @var   ACh_CODE The single instance of the class
	 */
	protected static $_instance = null;

	/**
	 * Main ACh_CODE Instance
	 *
	 * Ensures only one instance of ACh_CODE is loaded or can be loaded.
	 * @return  ACh_CODE - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * ACh_CODE Constructor.
	 */
	function __construct() {

		// Core
		$this->core = require_once( ACH_PATH. 'includes/custom-code/ach-custom-code-core.php' );

		// Admin
		if ( is_admin() ) {
			// Settings
			$this->settings = array();
			$this->settings['general'] = require_once( ACH_PATH. 'includes/custom-code/ach-custom-code-settings.php' );
			// Version updated
			if ( get_ach_code_option( 'version', '' ) !== ACH_PLUGIN_VERSION ) {
				add_action( 'admin_init', array( $this, 'version_updated' ), PHP_INT_MAX );
			}
		}
	}

	/**
	 * version_updated.
	 */
	function version_updated() {
		foreach ( $this->settings as $section ) {
			foreach ( $section->get_settings() as $value ) {
				if ( isset( $value['default'] ) && isset( $value['id'] ) ) {
					$autoload = isset( $value['autoload'] ) ? (bool) $value['autoload'] : true;
					add_option( ACH_CODE_ID . '_' . $value['id'], $value['default'], '', $autoload );
				}
			}
		}
		update_option( ACH_CODE_ID . '_' . 'version', ACH_PLUGIN_VERSION );
	}

}

endif;

if ( ! function_exists( 'ach_code' ) ) {
	/**
	 * Returns the main instance of ACh_CODE to prevent the need to use globals.
	 * @return  ACh_CODE
	 */
	function ach_code() {
		return ACh_CODE::instance();
	}
}

ach_code();
