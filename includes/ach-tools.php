<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once( ACH_PATH. 'includes/ga-tagmanager/ga-tagmanager.php');
require_once( ACH_PATH. 'includes/encoded.php');

// Creates a database table to store the captcha information.
function ach_create_captcha_db_table() {
    global $wpdb;
    $table_name = ach_get_table_name();
    $collate = '';
    if ( $wpdb->has_cap( 'collation' ) ) {
        $collate = $wpdb->get_charset_collate();
    }
    $sql = 'CREATE TABLE IF NOT EXISTS ' . $table_name . ' ( '
        . 'row_id bigint(20) NOT NULL AUTO_INCREMENT, '
        . 'request_id varchar(32) DEFAULT NULL, '
        . 'answer varchar(5) DEFAULT NULL, '
        . 'unix_time_added int(11) NOT NULL, '
        . 'PRIMARY KEY  (row_id) '
        . ') ' . $collate . ';';
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
    if ( is_multisite() ) {
        update_site_option( 'ach-new-database-table-created', 'yes' );
    }
}

/**
 * Returns the name of the database table we are using. It is different if it is a multisite.
 * @return string
 */
function ach_get_table_name() {
    global $wpdb;
    if ( is_multisite() ) {
        $main_blog_prefix = $wpdb->get_blog_prefix( ach_get_main_site_id() );
        return $main_blog_prefix . 'ach_login_captcha';
    } else {
        return $wpdb->prefix . 'ach_login_captcha';
    }
}

// Creates the database table on plugin activation
register_activation_hook( ACH_PATH. 'ACh.php', 'ach_create_captcha_db_table' );

/* === All ACh tools functions === */

/* =================================== General =================================== */

// 1- Change wp-login logo URL to your home URL.
if (get_option('logourl_setting') == "on") {
    function ach_login_headerurl() {
		return 'https://alefbaweb.com';
	}
	add_filter('login_headerurl', 'ach_login_headerurl', 100, 2);
}

// 2- Change wp-login logo to anything you want.
if (get_option('loginlogo_setting') == "on") {
	function custom_loginlogo() {
		echo '<!-) Customized using My WordPress Login Logo -->
			<style type="text/css">
				h1 a {
					background-image: url('. ACH_URL.'assets/img/loginlogo.svg) !important;
					background-color: #fff;
					border-radius: 3px;
					border-style: solid;
					border-width: 1px;
					border-color: #d7d7d7;
				}  
			</style>';
	}
	add_action('login_head', 'custom_loginlogo');
	
	function custom_loginlogo_title() {
		return 'ACh | Web Developer and Web Designer';
	}
	add_filter( 'login_headertext', 'custom_loginlogo_title' );
}

// 3- Remove ulr box/field from comments.
if (get_option('commenturl_setting') == "on") {
	function unset_url_field($fields){
		if(isset($fields['url']))
			unset($fields['url']);
			return $fields;
		}
	add_filter('comment_form_default_fields', 'unset_url_field');
	add_filter('comment_form_field_url', '__return_false');
}

// 4- Remove your WordPress version number from your head file and RSS feeds.
if (get_option('wpversion_setting') == "on") {
    function ach_remove_version() {
		return '';
	}
	add_filter('the_generator', 'ach_remove_version');	
}

// 5- Remove welcome panel from WordPress dashboard.
if (get_option('welcomepanel_setting') == "on") {
	remove_action('welcome_panel', 'wp_welcome_panel');
}

// 6- Disable XML-RPC, wlwmanifest and RSD-link in WordPress.
if (get_option('disxmlrpc_setting') == "on") {
    add_filter('xmlrpc_enabled', '__return_false');
	remove_action('wp_head', 'wlwmanifest_link');
	remove_action('wp_head', 'rsd_link');
}

// 7- Remove WP logo from top bar.
if (get_option('wplogotop_setting') == "on") {
    function ach_remove_wp_toplogo() {
		global $wp_admin_bar;
		/* Remove their stuff */
		$wp_admin_bar->remove_menu('wp-logo');
	}
	add_action('wp_before_admin_bar_render', 'ach_remove_wp_toplogo');
}

// 8- Fixed zlib compression error in WordPress dashboard.
if (get_option('fixedzlib_setting') == "on") {
    function wp_ob_end_flush_all_fixed() {
		$start = (int) ini_get('zlib.output_compression');
		$levels = ob_get_level();
		for ( $i = $start; $i < $levels; $i++ ) {
			ob_end_flush();
		}
	}
	//remove original callback
	remove_action('shutdown', 'wp_ob_end_flush_all', 1);

	//add fixed callback
	add_action('shutdown', 'wp_ob_end_flush_all_fixed', 1);
}

// 9- Remove hentry class from WordPress.
if (get_option('hentryclass_setting') == "on") {
    function ach_remove_hentry_class( $classes ) {
		$classes = array_diff( $classes, array( 'hentry' ) );
		return $classes;
	}
	add_filter( 'post_class', 'ach_remove_hentry_class' );
}

// 10- Option to duplicate post or CPT.
if (get_option('acduplicate_setting') == "on") {
    include( ACH_PATH. 'includes/duplicate/acduplicate.php');
}

// 10- Add excerpts to WordPress Pages.
if (get_option('acexcerpt_setting') == "on") {
    add_post_type_support( 'page', 'excerpt' );
}

/* =================================== Updates =================================== */

// 1- Disable all WordPress update and notifications.
if (get_option('diswpupn_setting') == "on") {
	function ach_remove_all_updates(){
		global $wp_version;return(object) array('last_checked'=> time(),'version_checked'=> $wp_version,);
	}
	add_filter('pre_site_transient_update_core','ach_remove_all_updates');
	add_filter('pre_site_transient_update_plugins','ach_remove_all_updates');
	add_filter('pre_site_transient_update_themes','ach_remove_all_updates');
}
else {
	// 2- Disable the WordPress plugins updates and notifications.
	if (get_option('dispupn_setting') == "on") {
		remove_action('load-update-core.php', 'wp_update_plugins');
		add_filter('pre_site_transient_update_plugins', '__return_null');
	}
	// 3- Disable the WordPress themes updates and notifications.
	if (get_option('distupn_setting') == "on") {
		remove_action( 'load-update-core.php', 'wp_update_themes' );
		add_filter( 'pre_site_transient_update_themes', '__return_null' );
	}
	// 4- Disable the WordPress core update and notification.
	if (get_option('diswpcupn_setting') == "on") {
		function ach_remove_core_updates(){
			global $wp_version;return(object) array('last_checked'=> time(),'version_checked'=> $wp_version,);
		}
		add_filter('pre_site_transient_update_core','ach_remove_core_updates');
	}
	// 5- Hide WordPress core update notice on admin dashboard.
	if (get_option('diswpcn_setting') == "on") {
		function ach_hide_nag() {
			remove_action( 'admin_notices', 'update_nag', 3 );
		}
		add_action('admin_menu','ach_hide_nag');
	}
}

// 6- This feature allows you to update plugins and themes using a zip file.
if (get_option('upzip_setting') == "on") {
    include( ACH_PATH. 'includes/easy-update/ach-easy-update.php');
}

// 7- Hide all notices from WordPress dashboard.
if (get_option('disalln_setting') == "on") {
	function ach_disable_admin_notices() {
		echo '<style>
		.update-nag, .updated, .update-message, .error, .woocommerce-message, .bsf-update-nag, .notice, .notice-warning, .is-dismissible {
			display: none !important;
		}
		</style>';
	}
	add_action('admin_head', 'ach_disable_admin_notices', 1000, 2);
	add_action('admin_enqueue_scripts', 'ach_disable_admin_notices', 1000, 2);
}

/* =================================== Security =================================== */

// 1- Remove confirm use of weak password checkbox.
if (get_option('weakpass_setting') == "on") {
    function ach_pass_script() {
    	?>
    		<script>
    			jQuery(document).ready(function() {
    				jQuery('.pw-weak').remove();
    			});
    		</script>
    	<?php
    }
    add_action('admin_head','ach_pass_script');

    function ach_login_pass_script() {
    	?>
    		<script>
	    		document.addEventListener("DOMContentLoaded", function(event) { 
	    			var elements = document.getElementsByClassName('pw-weak');
	    			console.log(elements);
	    			var requiredElement = elements[0];
	    			requiredElement.remove();
	    		});
	    	</script>
	    <?php
    }
    add_action('login_enqueue_scripts','ach_login_pass_script');
}

// 2- Disable editing, deleting, and adding admin user to site for other users.
if (get_option('disaddadmin_setting') == "on") {
	class ACh_User_Caps {
		// Add our filters.
		function __construct(){
			add_filter( 'ach_editable_roles', array($this, 'ach_editable_roles'));
			add_filter( 'ach_map_meta_cap', array($this, 'ach_map_meta_cap'), 10, 4);
		}
		
		// Remove 'Administrator' from the list of roles if the current user is not an admin.
		function ach_editable_roles( $roles ){
			if( isset( $roles['administrator'] ) && !current_user_can('administrator') ){
				unset( $roles['administrator']);
			}
			return $roles;
		}

		// If someone is trying to edit or delete and admin and that user isn't an admin, don't allow it.
		function ach_map_meta_cap( $caps, $cap, $user_id, $args ){
			switch( $cap ){
				case 'edit_user':
				case 'remove_user':
				case 'promote_user':
				if( isset($args[0]) && $args[0] == $user_id )
					break;
				elseif( !isset($args[0]) )
					$caps[] = 'do_not_allow';
					$other = new WP_User( absint($args[0]) );
				if( $other->has_cap( 'administrator' ) ){
					if(!current_user_can('administrator')){
						$caps[] = 'do_not_allow';
					}
				}
				break;
				case 'delete_user':
				case 'delete_users':
				if( !isset($args[0]) )
					break;
					$other = new WP_User( absint($args[0]) );
				if( $other->has_cap( 'administrator' ) ){
					if(!current_user_can('administrator')){
						$caps[] = 'do_not_allow';
					}
				}
				break;
				default:
				break;
			}
			return $caps;
		}
	}
	$ach_user_caps = new ACh_User_Caps();
}

// 3- Remove error messages from WordPress and Woocommerce login and registration pages.
if (get_option('errormessage_setting') == "on") {
    function no_wordpress_errors(){
		return __('Something is wrong!', 'ach_toolkit');
	}
	add_filter( 'login_errors', 'no_wordpress_errors' );
}

// 4- Change WP login URL to anything.
if (get_option('loginurl_setting') == "on") {
    include( ACH_PATH. 'includes/change-login/ach-change-login.php');
}

// 5- Set cookie expiration and auto logout for all users (defult 3 days).
if (get_option('cookieexp_setting') == "on") {
    function ach_auto_logout( $expirein ) {
		return 259200; // 3 days to seconds.
	}
	add_filter( 'auth_cookie_expiration', 'ach_auto_logout' );
}

// 6- Remove admin class from comments.
if (get_option('adminclass_setting') == "on") {
	function ach_remove_comment_author_class( $classes ) {
		foreach( $classes as $key => $class ) {
			if(strstr($class, "comment-author-")) {
				unset( $classes[$key] );
			}
		}
		return $classes;
	}
	add_filter( 'comment_class' , 'ach_remove_comment_author_class' );
}

// 7- Hide admin bar from Non-Admin users.
if (get_option('nonadmin_setting') == "on") {
    function ach_remove_admin_bar() {
		if (!current_user_can('administrator') && !is_admin()) {
			show_admin_bar(false);
		}
	}
	add_action('after_setup_theme', 'ach_remove_admin_bar');
}

// 8- Hide a specific user account from users list (default "ach1992").
if (get_option('hideuser_setting') == "on") {
	require_once( ACH_PATH. 'includes/hideuser/hideuser.php' );
}

// 9- Enable simple login captcha for login forms.
if (get_option('logincaptcha_setting') == "on") {
    include( ACH_PATH. 'includes/login-captcha/ach-login-captcha.php');
}

// 10- Stop spam comments in the default commenting system of WordPress.
if (get_option('antispam_setting') == "on") {
    include( ACH_PATH. 'includes/anti-spam/ach-anti-spam.php');
}

/* =================================== Plugins =================================== */

// 1- Delete all structured data generated by woocommerce.
if (get_option('woostructure_setting') == "on") {
	function ach_remove_output_structured_data() {
		if(in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))){ 
			remove_action( 'wp_footer', array( WC()->structured_data, 'output_structured_data' ), 10 ); // This removes structured data from all frontend pages.
			remove_action( 'woocommerce_email_order_details', array( WC()->structured_data, 'output_email_structured_data' ), 30 ); // This removes structured data from all Emails sent by WooCommerce.
		}
	}
	add_action( 'init', 'ach_remove_output_structured_data' );
}

// 2) Delete all structured data generated by Yoast Seo.
if (get_option('yoaststructure_setting') == "on") {
    add_filter( 'wpseo_json_ld_output', '__return_false' );
}

// 3) Filter to add the Keywords meta tag for Rank Math Seo plugin.
if (get_option('keyrankmath_setting') == "on") {
    add_filter( 'rank_math/frontend/show_keywords', '__return_true');
}

// 4- Hide ACh Toolkit and ManageWP Worker from plugin list.
if (get_option('hideplugin_setting') == "on") {
	function ach_hide_plugins($plugins){
		// let's hide ACh-Toolkit
		if(is_plugin_active('ACh-Toolkit/ACh.php')) {
			unset( $plugins['ACh-Toolkit/ACh.php'] );
		}
		// let's hide ManageWP Worker
		if(is_plugin_active('worker/init.php')) {
			unset( $plugins['worker/init.php'] );
		}
		return $plugins;
	}
	add_filter( 'all_plugins', 'ach_hide_plugins');
}

// 5- Add custom word [Free] instead of [0] in woocommerce product.
if (get_option('freezero_setting') == "on") {
	function ach_price_free_zero_empty( $price, $product ){
		if ( '' === $product->get_price() || 0 == $product->get_price() ) {
			$price = __('Free', 'ach_toolkit');
		}
		return $price;
	}
	add_filter( 'woocommerce_get_price_html', 'ach_price_free_zero_empty', 100, 2 );
}

// 6- Enable option for download plugins and themes.
if (get_option('dlplugin_setting') == "on") {
	include( ACH_PATH. 'includes/dl-plugin-theme/ach-dl-plugin-theme.php');
}

// 7) Hide Must-Use plugins menu from WordPress plugin list.
if (get_option('hidemustuse_setting') == "on") {
	function ach_hide_advanced_plugins( $default, $type ) {
		if ( $type == 'mustuse' ) return false; // Hide Must-Use
		return $default;
	}
	add_filter( 'show_advanced_plugins', 'ach_hide_advanced_plugins', 10, 2 );
}

// 8- Add Aparat videos with shortcode.
if (get_option('addaparat_setting') == "on") {
	include( ACH_PATH. 'includes/aparat/acaparat.php');
}

// 9- Disable WooCommerce's cart fragments script.
if (get_option('cartfragments_setting') == "on") {
	include( ACH_PATH. 'includes/cart-fragments/ac-cart-fragments.php');
}

/* =================================== Developer =================================== */

// 1) Add shortlink to WordPress Post Types.
if (get_option('shortlink_setting') == "on") {
    add_filter( 'get_shortlink', function( $shortlink ) {return $shortlink;} );
}

// 2) Add font size selection to WordPress editor.
if (get_option('fontsize_setting') == "on") {
    function ach_mce_buttons( $buttons ) {
		array_unshift( $buttons, 'fontsizeselect' );
		return $buttons;
    }
	add_filter( 'mce_buttons', 'ach_mce_buttons' );
}

// 3) Add an option at the top bar to change the screen from custom languages to English in the admin dashboard.
if (get_option('enlang_setting') == "on") {
	include( ACH_PATH. 'includes/en-switch/ach-en-switch.php');
}

// 4) Add author filter to the WP media library.
if (get_option('authorfilter_setting') == "on") {
    function ach_author_filter_dropdown() {
		$scr = get_current_screen();
		if ( $scr->base !== 'upload' ) return;

		$author		= filter_input(INPUT_GET, 'author', FILTER_SANITIZE_STRING );
		$selected	= (int)$author > 0 ? $author : '-1';
		$args		= array(
			'show_option_none'   => 'All authors',
			'name'               => 'author',
			'selected'           => $selected
		);
		wp_dropdown_users( $args );
	}
	add_action('restrict_manage_posts', 'ach_author_filter_dropdown');
}

// 5) Enable Maintenance Mode & Coming Soon Page.
if (get_option('maintenance_setting') == "on") {
    function ach_maintenance_mode(){
        if(!current_user_can('edit_themes') || !is_user_logged_in()){
            wp_die('<h1 style="color:red" align="center">Coming Soon</h1><br /><h3 align="center">We are currently working on this page</h3>');
        }
    }
    add_action('get_header', 'ach_maintenance_mode');
}

// 6- Delete attachments images or videos when deleting the post or custom post type.
if (get_option('delmedia_setting') == "on") {
	include( ACH_PATH. 'includes/del-media/del-media.php');
}

// 7) Add category filter to posts in editor mode.
if (get_option('catfilter_setting') == "on") {
    include( ACH_PATH. 'includes/cat-filter/ach-category-filter.php');
}

// 8) Show ID for users, posts, pages, custom post types and etc.
if (get_option('showid_setting') == "on") {
    include( ACH_PATH. 'includes/show-id/show-id.php');
}

// 9) Allow any file types upload in WP media library.
if (get_option('filetype_setting') == "on") {
    include( ACH_PATH. 'includes/file-allowed/ach-file-allowed.php');
}

// 10) Add custom CSS, JS and PHP Codes to WordPress.
if (get_option('customcode_setting') == "on") {
    include( ACH_PATH. 'includes/custom-code/ach-custom-code.php');
}

// 11) Add the option to find anything and replace it with what you want.
if (get_option('findreplace_setting') == "on") {
    include( ACH_PATH. 'includes/find-replace/acfindreplace.php');
}

// 12) Add FAQ Schema Markup to WP posts and pages.
if (get_option('faqschema_setting') == "on") {
    include( ACH_PATH. 'includes/faq-schema/ac-faq-schema.php');
}

// 13) Enables the previous "classic" widgets settings screens in Appearance.
if (get_option('classicwg_setting') == "on") {
    // Disables the block editor from managing widgets in the Gutenberg plugin.
	add_filter( 'gutenberg_use_widgets_block_editor', '__return_false' );
	// Disables the block editor from managing widgets.
	add_filter( 'use_widgets_block_editor', '__return_false' );
}

// 14) Add the option to find and replace text in editor mode..
if (get_option('editorfr_setting') == "on") {
    include( ACH_PATH. 'includes/find-replace/editor-find-replace.php');
}

?>