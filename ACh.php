<?php
/*
 * Plugin Name: ACh Toolkit
 * Plugin URI: https://alefbaweb.com
 * Description: ACh Toolkit is a special plugin to add extra option to your WordPress site. e.g. Security Options, Custom CSS-JS-Functions, Find and Replace, Disable Notifications and Updates, Maintenance Mode, Anti Spam, Google Tag Manager, and etc.
 * Author: ACh
 * Version: 1.1.8
 * Author URI: https://alefbaweb.com
 * Text Domain: ach_toolkit
 * Domain Path: /languages/
 */


if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

define( 'ACH_URL', plugin_dir_url( __FILE__ ) );
define( 'ACH_PATH', plugin_dir_path( __FILE__ ) );
define( 'ACH_BASENAME', plugin_basename( __FILE__ ) );
define( 'ACH_PLUGIN_VERSION', '1.1.8' );

require_once( ACH_PATH. 'includes/ach-settings.php' );
require_once( ACH_PATH. 'includes/ach-tools.php' );

// Load plugin styles.
function ach_admin_style() {
	$current_screen = get_current_screen();
    if ( strpos( $current_screen->base, 'a-ch' ) !== false ) {
		wp_enqueue_style( 'ach_style', ACH_URL. 'assets/css/style.css', array(), '1.0.0' );
		wp_style_add_data( 'ach_style', 'rtl', 'replace' );
		wp_enqueue_style( 'load-fa', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' );
		wp_enqueue_script( 'jquery-achTabs', ACH_URL. 'assets/js/jquery-achTabs.js', array(), '1.0.0', false );
	}
	if ( get_option( 'WPLANG' ) == 'fa_IR' ) {
		wp_enqueue_style( 'ach_font_fa', ACH_URL. 'assets/css/font-fa.css', array(), '1.0.0' );
	}
	if ( strpos( $current_screen->base, 'ach-custom-code' ) !== false ) {
		wp_enqueue_style( 'ac_pages', ACH_URL. 'assets/css/pages.css', array(), '1.0.0' );
		wp_style_add_data( 'ac_pages', 'rtl', 'replace' );
	}
	if ( strpos( $current_screen->base, 'ach-find-replace' ) !== false ) {
		wp_enqueue_style( 'ac_pages', ACH_URL. 'assets/css/pages.css', array(), '1.0.0' );
		wp_style_add_data( 'ac_pages', 'rtl', 'replace' );
		wp_enqueue_script( 'find-replace', ACH_URL. 'assets/js/achfindreplace.js', array(), '1.0.0', false );
	}
}
add_action( 'admin_enqueue_scripts', 'ach_admin_style', 100, 10 );

function ach_login_style() {
	if ( get_option( 'WPLANG' ) == 'fa_IR' ) {
		wp_enqueue_style( 'ac_login_fa', ACH_URL. 'assets/css/login-fa.css', array(), '1.0.0' );
	}
	wp_enqueue_style( 'ac_login', ACH_URL. 'assets/css/login.css', array(), '1.1.0' );
}
add_action( 'login_enqueue_scripts', 'ach_login_style', 100, 10 );
add_action( 'wp_enqueue_scripts', 'ach_login_style', 100, 10 );

function ach_scrollbar_style() {
	wp_enqueue_style( 'ac_scrollbar_style', ACH_URL. 'assets/css/scrollbar.css', array(), '1.1.0' );
}
add_action( 'login_enqueue_scripts', 'ach_scrollbar_style', 100, 10 );
add_action( 'wp_enqueue_scripts', 'ach_scrollbar_style', 100, 10 );
add_action( 'admin_enqueue_scripts', 'ach_scrollbar_style', 100, 10 );

// Plugin activation hook and notice.
register_activation_hook( __FILE__, 'ach_plugin_activation' );
register_deactivation_hook( __FILE__, 'ach_plugin_deactivation' );

function ach_plugin_activation() {
    if ( ! current_user_can( 'activate_plugins' ) ) {
        return;
    }
    set_transient( 'ach-admin-notice-on-activation', true, 5 );
}

/*
function ach_plugin_deactivation() {
	if( get_option( 'ach_google_anaytics_code' ) ) {
		delete_option( 'ach_google_anaytics_code' );
	}
	if( get_option( 'ach_tag_manager_id' ) ) {
		delete_option( 'ach_tag_manager_id' );
	}
}
*/

function ach_plugin_install_notice() { 
    if( get_transient( 'ach-admin-notice-on-activation' ) ) { ?>
        <div class="notice notice-success is-dismissible">
            <p><strong><?php printf( __( 'Thanks for installing %1$s v%2$s plugin. 😎 Click <a href="%3$s">here</a> to configure plugin settings.', 'ach_toolkit' ), 'ACh Toolkit', ACH_PLUGIN_VERSION, admin_url( 'options-general.php?page=a-ch' ) ); ?></strong></p>
        </div> <?php
        delete_transient( 'ach-admin-notice-on-activation' );
    }
}
add_action( 'admin_notices', 'ach_plugin_install_notice' ); 

// add plugin action links.
function ach_add_action_links( $links ) {
    $rmlinks = array(
        '<a href="' . admin_url( 'options-general.php?page=a-ch' ) . '">' . __( 'Manage', 'ach_toolkit' ) . '</a>',
    );
    return array_merge( $rmlinks, $links );
}
add_filter( 'plugin_action_links_' . ACH_BASENAME, 'ach_add_action_links', 10, 2 );

// plugin row links and elements.
function ach_plugin_meta_links( $links, $file ) {
	$plugin = ACH_BASENAME;
	if ( $file == $plugin ) // only for this plugin
		return array_merge( $links, 
            array( '<a href="https://alefbaweb.com" target="_blank">' . __( 'Support', 'ach_toolkit' ) . '</a>' ),
            array( '<a href="https://paypal.me/AChopani/10usd" target="_blank" style="color:#3db634;">' . __( 'Buy developer a coffee', 'ach_toolkit' ) . '</a>' )
		);
	return $links;
}
add_filter( 'plugin_row_meta', 'ach_plugin_meta_links', 10, 2 );

/*
 * load language
 */
function ach_load_textdomain() {
	load_plugin_textdomain('ach_toolkit', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');
}
add_action('plugins_loaded', 'ach_load_textdomain');

// add ACh settings in admin side panel.
function admin_menu_ach_settings() {
    add_options_page( 'ACh Toolkit', 'ACh Toolkit', 'manage_options', 'a-ch', 'ach_option_page');
}
add_action('admin_menu', 'admin_menu_ach_settings');

// Prevent from Deactivating ACh-Toolkit Plugin.
function ach_disable_plugin_deactivation( $actions, $plugin_file, $plugin_data, $context ) {
 
    if ( array_key_exists( 'deactivate', $actions ) && in_array( $plugin_file, array(
        'ACh-Toolkit/ACh.php'
    )))
    unset( $actions['deactivate'] );
    return $actions;
}
add_filter( 'plugin_action_links', 'ach_disable_plugin_deactivation', 10, 4 );

?>