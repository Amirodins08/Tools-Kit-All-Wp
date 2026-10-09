<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Add settings panel to ACh-Toolkit plugin.
function ach_option_page() {

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die();
	}

	?>
	<div class="ach-header"><h2 class="ach-toolkit-header"><?php echo _e('ACh Toolkit', 'ach_toolkit'); ?> <span class="ach-version">1.1.8</span></h2></div>
	<div class="ach-description"><p><?php echo _e('The ACh Toolkit plugin for WordPress. Manage updates, security, plugins, add custom codes, Find and Replace and etc with one click!', 'ach_toolkit'); ?></p></div>
	<?php
	// Update GA and Tag Manager settings
	if( isset($_POST['acgatm_submit_btn']) && isset($_POST['acgatm_nonce']) && wp_verify_nonce($_POST['acgatm_nonce'], 'acgatm_security')) {
		$success = '';

		$analytics_code = sanitize_text_field($_POST['acga_analytics_code']);
		if( $analytics_code != ''){
			update_option( 'ach_google_anaytics_code', $analytics_code );
		}
		else{
			delete_option( 'ach_google_anaytics_code' );
		}
		$tagmanager_id = sanitize_text_field($_POST['actag_manager_id']);
		if( $tagmanager_id != ''){
			update_option( 'ach_tag_manager_id', $tagmanager_id );
		}
		else{
			delete_option( 'ach_tag_manager_id' );
		}
		if( $success == ''){
			$success .= '<div class="updated notice notice-success is-dismissible below-h2" id="ach-message"><p><strong>'.__('Settings Updated. Please clear cache if you have a cache service.','ach_toolkit').'</strong></p></div>';
		}
	}
    if( isset($success )){ echo $success; }
    ?>
<div class="ach-wrapper">
	<div id="ach-tabs" style="display:none">
	<ul>
		<li><a href="#general"><?php esc_html_e( 'General', 'ach_toolkit' ); ?></a></li>
		<li><a href="#updates"><?php esc_html_e( 'Updates', 'ach_toolkit' ); ?></a></li>
		<li><a href="#security"><?php esc_html_e( 'Security', 'ach_toolkit' ); ?></a></li>
		<li><a href="#plugins"><?php esc_html_e( 'Plugins', 'ach_toolkit' ); ?></a></li>
		<li><a href="#developer"><?php esc_html_e( 'Developer', 'ach_toolkit' ); ?></a></li>
		<li><a href="#googleatm"><?php esc_html_e( 'GA and Tag Manager', 'ach_toolkit' ); ?></a></li>
		<li><a href="#support"><?php esc_html_e( 'Support', 'ach_toolkit' ); ?></a></li>
	</ul>
	
	<!-- =================================== General =================================== -->
	<form action="" method="POST">
	<?php wp_nonce_field( 'ach_option_page' ); ?>
	<div id="general">
		<table class="ac-content">
		<tbody class="ach-box-left">
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="logourl"><?php _e('&#10004; Change wp-login logo url', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
						<input class="ach-switch-input" name="logourl" type="checkbox" <?php if (get_option("logourl_setting") == "on") { echo "checked"; } ?>/>
                        <span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Change wp-login logo URL to your home URL.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="loginlogo"><?php _e('&#10004; Change wp-login logo', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
						<input class="ach-switch-input" name="loginlogo" type="checkbox" <?php if (get_option("loginlogo_setting") == "on") { echo "checked"; } ?>/>
                        <span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Change wp-login logo to ACh logo.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="commenturl"><?php _e('&#10004; Remove URL box from comments', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
						<input class="ach-switch-input" name="commenturl" type="checkbox" <?php if (get_option("commenturl_setting") == "on") { echo "checked"; } ?>/>
                        <span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Remove URL box/field from comments.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="wpversion"><?php _e('&#10004; Remove WordPress version', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
                        <input class="ach-switch-input" name="wpversion" type="checkbox" <?php if (get_option("wpversion_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Remove your WordPress version number from your head file and RSS feeds.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="welcomepanel"><?php _e('&#10004; Remove welcome panel', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
                        <input class="ach-switch-input" name="welcomepanel" type="checkbox" <?php if (get_option("welcomepanel_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Remove welcome panel from WordPress dashboard.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="disxmlrpc"><?php _e('&#10004; Disable XML-RPC and wlwmanifest', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
                        <input class="ach-switch-input" name="disxmlrpc" type="checkbox" <?php if (get_option("disxmlrpc_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Disable XML-RPC, wlwmanifest and RSD-link in WordPress.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="wplogotop"><?php _e('&#10004; Remove WP logo from top bar', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
                        <input class="ach-switch-input" name="wplogotop" type="checkbox" <?php if (get_option("wplogotop_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Remove WordPress logo from top bar.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="fixedzlib"><?php _e('&#10004; Fixed zlib compression error', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
                        <input class="ach-switch-input" name="fixedzlib" type="checkbox" <?php if (get_option("fixedzlib_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Fixed zlib compression error in WordPress dashboard.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="hentryclass"><?php _e('&#10004; Remove hentry class', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
                        <input class="ach-switch-input" name="hentryclass" type="checkbox" <?php if (get_option("hentryclass_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Remove hentry class from WordPress.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="acduplicate"><?php _e('&#10004; Option to duplicate post or CPT', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
                        <input class="ach-switch-input" name="acduplicate" type="checkbox" <?php if (get_option("acduplicate_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Enable this to add the duplicate option to posts, pages, or Custom Post Types.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="acexcerpt"><?php _e('&#10004; Add excerpts to WordPress Pages', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
                        <input class="ach-switch-input" name="acexcerpt" type="checkbox" <?php if (get_option("acexcerpt_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Add excerpts to WordPress pages as well as posts.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
		</tbody>
		</table>
	<p class="ac-footer"><input type="submit" name="publish" id="publish" class="button button-primary" value="<?php _e('Update Settings', 'ach_toolkit'); ?>"></p>
	</div>

	<!-- =================================== Updates =================================== -->

	<div id="updates">
		<table class="ac-content">
		<tbody class="ach-box-left">
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="diswpupn"><?php _e('&#10004; Disable all WP updates and notifs', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
                        <input class="ach-switch-input" name="diswpupn" type="checkbox" <?php if (get_option("diswpupn_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>			        
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Disable all WordPress core, themes, plugins and translations updates and notifications.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="disalln"><?php _e('&#10004; Hide all notices from WP dashboard', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
                        <input class="ach-switch-input" name="disalln" type="checkbox" <?php if (get_option("disalln_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Disable all notices from the WordPress dashboard. e.g. errors, updates, warning, rate us, license, dismissible and etc.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
				<th class="ac-th" scope="row">
					<label for="dispupn"><?php _e('&#10004; Disable plugins updates and notifs', 'ach_toolkit'); ?></label>
				</th>
				<td>
					<label class="ach-switch">
						<input class="ach-switch-input" name="dispupn" type="checkbox" <?php if (get_option("dispupn_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
						<span class="ach-switch-handle"></span>
					</label>
				</td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Disable the WordPress plugins updates and notifications.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
				<th class="ac-th" scope="row">
					<label for="distupn"><?php _e('&#10004; Disable themes updates and notifs', 'ach_toolkit'); ?></label>
				</th>
				<td>
					<label class="ach-switch">
						<input class="ach-switch-input" name="distupn" type="checkbox" <?php if (get_option("distupn_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
						<span class="ach-switch-handle"></span>
					</label>
				</td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Disable the WordPress themes updates and notifications.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
				<th class="ac-th" scope="row">
					<label for="diswpcupn"><?php _e('&#10004; Disable WP core update and notifs', 'ach_toolkit'); ?></label>
				</th>
				<td>
					<label class="ach-switch">
						<input class="ach-switch-input" name="diswpcupn" type="checkbox" <?php if (get_option("diswpcupn_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
						<span class="ach-switch-handle"></span>
					</label>
				</td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Disable the WordPress core update and notifications.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="diswpcn"><?php _e('&#10004; Hide WordPress core update notice', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
						<input class="ach-switch-input" name="diswpcn" type="checkbox" <?php if (get_option("diswpcn_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Hide WordPress core update notice from WP dashboard.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="upzip"><?php _e('&#10004; Update theme and plugin from zip', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
                        <input class="ach-switch-input" name="upzip" type="checkbox" <?php if (get_option("upzip_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'This feature allows you to update plugins and themes using a zip file. While upgrading, a backup copy of the old theme or plugin is first created. This allows you to install the old version in case of problems with the new version.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
		</tbody>
		</table>
	<p class="ac-footer"><input type="submit" name="publish" id="publish" class="button button-primary" value="<?php _e('Update Settings', 'ach_toolkit'); ?>"></p>
	</div>
	
	<!-- =================================== Security =================================== -->
	
	<div id="security">
		<table class="ac-content">
		<tbody class="ach-box-left">
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="weakpass"><?php _e('&#10004; Remove confirm use of weak password', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
                        <input class="ach-switch-input" name="weakpass" type="checkbox" <?php if (get_option("weakpass_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Remove confirm use of weak password checkbox.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="disaddadmin"><?php _e('&#10004; Disable adding admin user by other user', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
                        <input class="ach-switch-input" name="disaddadmin" type="checkbox" <?php if (get_option("disaddadmin_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Disable editing, deleting, and adding admin user to site for other users.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="errormessage"><?php _e('&#10004; Remove error message from login page', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
                        <input class="ach-switch-input" name="errormessage" type="checkbox" <?php if (get_option("errormessage_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Remove error messages from WordPress and Woocommerce login and registration pages.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="loginurl"><?php _e('&#10004; Change WP login URL', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
                        <input class="ach-switch-input" name="loginurl" type="checkbox" <?php if (get_option("loginurl_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Change WP login URL to anything. Go to Settings&rarr; Permalinks.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="cookieexp"><?php _e('&#10004; Set cookie expiration (3 days)', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
                        <input class="ach-switch-input" name="cookieexp" type="checkbox" <?php if (get_option("cookieexp_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Set cookie expiration and auto logout for all users (defult 3 days).', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="adminclass"><?php _e('&#10004; Remove admin class from comments', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
                        <input class="ach-switch-input" name="adminclass" type="checkbox" <?php if (get_option("adminclass_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Remove admin class from comments.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="nonadmin"><?php _e('&#10004; Hide admin bar from Non-Admins', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
                        <input class="ach-switch-input" name="nonadmin" type="checkbox" <?php if (get_option("nonadmin_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Hide admin bar from Non-Admin users.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="hideuser"><?php _e('&#10004; Hide a specific user account', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
                        <input class="ach-switch-input" name="hideuser" type="checkbox" <?php if (get_option("hideuser_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Hide a specific user account from users list, default [ach1992]. if you want to change the username, Go to [wp-content/plugins/ACh-Toolkit/includes/ach-tools.php] [line: 344] and change [ach1992] to any username that you want.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="logincaptcha"><?php _e('&#10004; Enable simple login captcha', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
                        <input class="ach-switch-input" name="logincaptcha" type="checkbox" <?php if (get_option("logincaptcha_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Enable simple login captcha for login forms.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="antispam"><?php _e('&#10004; Enable Anti-spam for comments', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
                        <input class="ach-switch-input" name="antispam" type="checkbox" <?php if (get_option("antispam_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Stop spam comments in the default commenting system of WordPress. Please clear cache after enabling this option.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
		</tbody>
		</table>
	<p class="ac-footer"><input type="submit" name="publish" id="publish" class="button button-primary" value="<?php _e('Update Settings', 'ach_toolkit'); ?>"></p>
	</div>
	
	<!-- =================================== Plugins =================================== -->
	
	<div id="plugins">
		<table class="ac-content">
		<tbody class="ach-box-left">
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="woostructure"><?php _e('&#10004; Remove Woocommerce structured data', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
                        <input class="ach-switch-input" name="woostructure" type="checkbox" <?php if (get_option("woostructure_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Delete all structured data generated by Woocommerce. Please make sure Woocommers is enabled.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="yoaststructure"><?php _e('&#10004; Remove Yoast SEO structured data', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
                        <input class="ach-switch-input" name="yoaststructure" type="checkbox" <?php if (get_option("yoaststructure_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Delete all structured data generated by Yoast SEO.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="keyrankmath"><?php _e('&#10004; Add keywords meta tag for Rank Math', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
                        <input class="ach-switch-input" name="keyrankmath" type="checkbox" <?php if (get_option("keyrankmath_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Filter to add the Keywords meta tag for Rank Math SEO plugin.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="hideplugin"><?php _e('&#10004; Hide ACh Toolkit and ManageWP', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
                        <input class="ach-switch-input" name="hideplugin" type="checkbox" <?php if (get_option("hideplugin_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Hide ACh Toolkit and ManageWP Worker from plugin list.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="freezero"><?php _e('&#10004; Add [Free] instead of [0] in product', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
                        <input class="ach-switch-input" name="freezero" type="checkbox" <?php if (get_option("freezero_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Add custom word [Free] instead of [0] in woocommerce product.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="dlplugin"><?php _e('&#10004; Option to download plugin and theme', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
                        <input class="ach-switch-input" name="dlplugin" type="checkbox" <?php if (get_option("dlplugin_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Enable option for download plugin and theme.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="hidemustuse"><?php _e('&#10004; Hide Must-Use plugins', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
                        <input class="ach-switch-input" name="hidemustuse" type="checkbox" <?php if (get_option("hidemustuse_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Hide Must-Use plugins menu from WordPress plugin list.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="addaparat"><?php _e('&#10004; Add Aparat videos with shortcode', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
                        <input class="ach-switch-input" name="addaparat" type="checkbox" <?php if (get_option("addaparat_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Enable this option to add Aparat videos with shortcode to your web pages. Default Shortcode: [aparat id=\'iybdS\'], Customized Shortcode: [aparat id=\'iybdS\' width=\'600\' height=\'450\' style=\'margin: 15px; padding: 7px\'].', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="cartfragments"><?php _e('&#10004; Disable Woo cart fragments script', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
                        <input class="ach-switch-input" name="cartfragments" type="checkbox" <?php if (get_option("cartfragments_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Option to disable WooCommerce cart fragments script, and re-enqueue it when the cart is updated. Works with all caching plugins.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
		</tbody>
		</table>
	<p class="ac-footer"><input type="submit" name="publish" id="publish" class="button button-primary" value="<?php _e('Update Settings', 'ach_toolkit'); ?>"></p>
	</div>
	
	<!-- =================================== Developer =================================== -->
	
	<div id="developer">
		<table class="ac-content">
		<tbody class="ach-box-left">
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="shortlink"><?php _e('&#10004; Add shortlink to WordPress', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
                        <input class="ach-switch-input" name="shortlink" type="checkbox" <?php if (get_option("shortlink_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Add shortlink to WordPress Post Types in editor mode.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="fontsize"><?php _e('&#10004; Add font size to editor', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
                        <input class="ach-switch-input" name="fontsize" type="checkbox" <?php if (get_option("fontsize_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Add font size selection to WordPress editor.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="enlang"><?php _e('&#10004; Enable Switch to English option', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
                        <input class="ach-switch-input" name="enlang" type="checkbox" <?php if (get_option("enlang_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Add an option at the top bar to change the screen from custom languages to English in the admin dashboard.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="authorfilter"><?php _e('&#10004; Add author filter to media library', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
                        <input class="ach-switch-input" name="authorfilter" type="checkbox" <?php if (get_option("authorfilter_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Add author filter to the WP media library.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="maintenance"><?php _e('&#10004; Enable Maintenance Mode', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
                        <input class="ach-switch-input" name="maintenance" type="checkbox" <?php if (get_option("maintenance_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Enable Maintenance Mode & Coming Soon Page.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="delmedia"><?php _e('&#10004; Delete attachments media', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
                        <input class="ach-switch-input" name="delmedia" type="checkbox" <?php if (get_option("delmedia_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Delete attachments images or videos when deleting a post or custom post type.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="catfilter"><?php _e('&#10004; Add category filter to posts', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
                        <input class="ach-switch-input" name="catfilter" type="checkbox" <?php if (get_option("catfilter_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Add category filter to posts in editor mode.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="showid"><?php _e('&#10004; Show ID for anything', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
                        <input class="ach-switch-input" name="showid" type="checkbox" <?php if (get_option("showid_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Show ID for users, posts, pages, custom post types and etc.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="filetype"><?php _e('&#10004; Allow any file types upload', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
                        <input class="ach-switch-input" name="filetype" type="checkbox" <?php if (get_option("filetype_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Allow any file types upload in WP media library. Included: svg, zip, rar, apk, tar, gz, gzip, tiff, tif, bmp, webp, psd, ai, indd, eps, rtf, txt, wav, csv, xml, flv, swf, vcf, html, htm, css, js, ico, otf, ttf, woff, woff2, eot, exe and ics file formats.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="customcode"><?php _e('&#10004; Add custom CSS-JS-PHP codes', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
                        <input class="ach-switch-input" name="customcode" type="checkbox" <?php if (get_option("customcode_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Enable option to add custom CSS-JS-PHP codes to WordPress. Go to Tools&rarr; ACh Custom Codes.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="findreplace"><?php _e('&#10004; Real-Time Find and Replace', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
                        <input class="ach-switch-input" name="findreplace" type="checkbox" <?php if (get_option("findreplace_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Add the option to find anything and replace it with what you want. Go to Tools&rarr; ACh Find Replace.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="faqschema"><?php _e('&#10004; Enable FAQ Schema Markup', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
                        <input class="ach-switch-input" name="faqschema" type="checkbox" <?php if (get_option("faqschema_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Add FAQ Schema Markup to WordPress posts and pages.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="classicwg"><?php _e('&#10004; Enables the Classic widgets', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
                        <input class="ach-switch-input" name="classicwg" type="checkbox" <?php if (get_option("classicwg_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Enables the previous (classic) widgets settings screens in Appearance - Widgets and the Customizer. Disables the block editor from managing widgets.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
			<tr>
			    <th class="ac-th" scope="row">
			        <label for="editorfr"><?php _e('&#10004; Find and replace text in editor', 'ach_toolkit'); ?></label>
			    </th>
			    <td>
                    <label class="ach-switch">
                        <input class="ach-switch-input" name="editorfr" type="checkbox" <?php if (get_option("editorfr_setting") == "on") { echo "checked"; } ?>/>
						<span class="ach-switch-label" data-on="ON" data-off="OFF"></span>
                        <span class="ach-switch-handle"></span>
                    </label>
			    </td>
				<td class="ach-tooltip">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Add find and replace function in WordPress editor in both visual and text mode.', 'ach_toolkit' ); ?>"><span title="" class="dashicons dashicons-editor-help" id="ach-dashicon"></span></span></td>
			</tr>
		</tbody>
		</table>
	<p class="ac-footer"><input type="submit" name="publish" id="publish" class="button button-primary" value="<?php _e('Update Settings', 'ach_toolkit'); ?>"></p>
	</form>
	</div>
	
	<!-- =================================== GA and Tag Manager =================================== -->

	<div id="googleatm">
    <form action="<?php echo basename($_SERVER['REQUEST_URI']); ?>" method="post">
        <?php wp_nonce_field('acgatm_security', 'acgatm_nonce'); ?>
        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row"><label for="set_google_anaytics_code"><?php _e('Global Site Tag or GA ID:', 'ach_toolkit'); ?></label>
                    </th>
                    <td>
                        <?php 
                            $ga_analytics_code = '';
                            //get default
                            if( get_option( 'ach_google_anaytics_code' ) ) {
                                $ga_analytics_code = get_option( 'ach_google_anaytics_code' );
                            }
                        ?>
                        <input name="acga_analytics_code" type="text" id="acga_analytics_code" value="<?php echo $ga_analytics_code; ?>" class="regular-text">
                        <p class="description" id="acga_analytics_code-description"><?php _e('It looks like: <strong>UA-XXXXXXXXX-Y</strong> or <strong>G-XXXXXXXXXX</strong>', 'ach_toolkit'); ?></p>
                    </td>
                </tr>
				<tr>
                    <th scope="row"><label for="set_google_tag_manager_id"><?php _e('Google Tag Manager ID:', 'ach_toolkit'); ?></label>
                    </th>
                    <td>
                        <?php 
                            $tag_manager_id = '';
                            //get default
                            if( get_option( 'ach_tag_manager_id' ) ) {
                                $tag_manager_id = get_option( 'ach_tag_manager_id' );
                            }
                        ?>
                        <input name="actag_manager_id" type="text" id="actag_manager_id" value="<?php echo $tag_manager_id; ?>" class="regular-text">
                        <p class="description" id="actag_manager_id-description"><?php _e('It looks like: <strong>GTM-XXXXXXX</strong>', 'ach_toolkit'); ?></p>
                    </td>
                </tr>
            </tbody>
        </table>
		<p class="ac-footer"><input type="submit" name="acgatm_submit_btn" id="acgatm_submit_btn" class="button button-primary" value="<?php _e('Update Settings', 'ach_toolkit'); ?>"></p>
    </form>
	</div>
	
	<!-- =================================== Support =================================== -->

	<div id="support">
		<div class="ach-help support-ach">
			<div class="ac-support-row support-ach">
				<div class="ac-support-col">
					<div class="support-box">
						<h3 class="support-box-heading ac-support-heading"><i class="fa fa-question-circle"></i> <?php _e( 'Support', 'ach_toolkit' ); ?></h3>
						<div class="ach-content">
							<div class="support-ach ac-support-about-text">
								<h2><?php _e( 'If you need assistance, see our help resources.', 'ach_toolkit' ); ?></h2>
								<p><?php _e( 'Please make a search to find help with your problem, or head over to our support forum to ask a question.', 'ach_toolkit' ); ?></p>
							</div>
							<div class="ac-support-row support-ach">
								<div class="ac-support-col">
									<a class="ac-support-forum-button" href="https://alefbaweb.com" target="_blank"><i class="fa fa-globe"></i><?php _e( 'Visit my site', 'ach_toolkit' ); ?></a>
								</div>
								<div class="ac-support-col">
									<a class="ac-support-forum-button" href="mailto:info@alefbaweb.com"><i class="fa fa-envelope"></i><?php _e( 'Send email', 'ach_toolkit' ); ?></a>
								</div>
								<div class="ac-support-col">
									<a class="ac-support-forum-button" href="https://lrl.li/chat" target="_blank"><i class="fa fa-comments"></i><?php _e( 'Support forum', 'ach_toolkit' ); ?></a>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="ac-support-col">
					<div class="ac-support-col ach-last">
						<div class="support-box">
							<h3 class="support-box-heading ac-support-heading"><i class="fa fa-heart"></i> <?php _e( 'About us', 'ach_toolkit' ); ?></h3>
							<div class="ach-content">
								<div class="support-ach ac-support-about-text">
									<h3><?php _e( 'The ACh Toolkit plugin for WordPress. Manage updates, security, plugins, add custom codes and etc with one click!', 'ach_toolkit' ); ?></h3>
									<p><?php _e( 'The ACh Toolkit was developed by <a class="ach-link-text" href="https://alefbaweb.com" target="_blank">ACh</a> and is <a class="ach-link-text" href="https://wordpress.org" target="_blank">available for free</a> on WordPress.', 'ach_toolkit' ); ?></p>
									<p><?php _e( 'We work hard to give you an exceptional premium products and 5 star support. To show your appreciation you can buy us a coffee or simply by sharing or follow us on social media.', 'ach_toolkit' ); ?></p>
								</div>
								<div class="support-ach ac-support-social-links">
									<ul>
										<li class="ach-buy-coffee"><a href="https://paypal.me/AChopani/10usd" target="_blank"><i class="fa fa-coffee"></i> <?php _e( 'Buy us a coffee', 'ach_toolkit' ); ?></a></li>
										<li class="ach-facebook"><a href="#"><i class="fa fa-facebook-f"></i> <?php _e( 'Like us', 'ach_toolkit' ); ?></a></li>
										<li class="ach-twitter"><a href="#"><i class="fa fa-twitter"></i> <?php _e( 'Tweet us', 'ach_toolkit' ); ?></a></li>
										<li class="ach-rate"><a href="#"><i class="fa fa-thumbs-up"></i> <?php _e( 'Rate us', 'ach_toolkit' ); ?></a></li>
									</ul>
								</div>
								<p class="footer-links"><a href="https://wordpress.org" class="ach-link">www.wordpress.org</a> | <a href="https://alefbaweb.com" class="ach-link">www.alefbaweb.com</a></p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	</div>
	<?php
	if(isset($_GET['success'])) {
	?>	
    <div class="updated notice notice-success is-dismissible below-h2" id="ach-message"><p><strong><?php echo _e('Settings Updated.', 'ach_toolkit'); ?></strong></p></div>
	<?php
	}
	?>
</div>
<?php

// Update plugin settings

    if (isset($_REQUEST['publish'])) {
		
        // set the variables
        if(isset($_POST['logourl']))			{$logourl='on';} 			else {$logourl='off';}
        if(isset($_POST['loginlogo']))			{$loginlogo='on';} 			else {$loginlogo='off';}
        if(isset($_POST['commenturl']))			{$commenturl='on';} 		else {$commenturl='off';}
		if(isset($_POST['wpversion']))			{$wpversion='on';} 			else {$wpversion='off';}
		if(isset($_POST['welcomepanel']))		{$welcomepanel='on';} 		else {$welcomepanel='off';}
		if(isset($_POST['disxmlrpc']))			{$disxmlrpc='on';} 			else {$disxmlrpc='off';}
		if(isset($_POST['wplogotop']))			{$wplogotop='on';} 			else {$wplogotop='off';}
		if(isset($_POST['fixedzlib']))			{$fixedzlib='on';} 			else {$fixedzlib='off';}
		if(isset($_POST['hentryclass']))		{$hentryclass='on';} 		else {$hentryclass='off';}
		if(isset($_POST['acduplicate']))		{$acduplicate='on';} 		else {$acduplicate='off';}
		if(isset($_POST['acexcerpt']))			{$acexcerpt='on';} 			else {$acexcerpt='off';}
		if(isset($_POST['diswpupn']))			{$diswpupn='on';} 			else {$diswpupn='off';}
		if(isset($_POST['disalln']))			{$disalln='on';} 			else {$disalln='off';}
		if(isset($_POST['dispupn']))			{$dispupn='on';} 			else {$dispupn='off';}
		if(isset($_POST['distupn']))			{$distupn='on';} 			else {$distupn='off';}
		if(isset($_POST['diswpcupn']))			{$diswpcupn='on';} 			else {$diswpcupn='off';}
		if(isset($_POST['diswpcn']))			{$diswpcn='on';} 			else {$diswpcn='off';}
		if(isset($_POST['upzip']))				{$upzip='on';} 				else {$upzip='off';}
		if(isset($_POST['weakpass']))			{$weakpass='on';} 			else {$weakpass='off';}
		if(isset($_POST['disaddadmin']))		{$disaddadmin='on';} 		else {$disaddadmin='off';}
		if(isset($_POST['errormessage']))		{$errormessage='on';} 		else {$errormessage='off';}
		if(isset($_POST['loginurl']))			{$loginurl='on';} 			else {$loginurl='off';}
		if(isset($_POST['addadmin']))			{$addadmin='on';} 			else {$addadmin='off';}
		if(isset($_POST['cookieexp']))			{$cookieexp='on';} 			else {$cookieexp='off';}
		if(isset($_POST['adminclass']))			{$adminclass='on';} 		else {$adminclass='off';}
		if(isset($_POST['nonadmin']))			{$nonadmin='on';} 			else {$nonadmin='off';}
		if(isset($_POST['hideuser']))			{$hideuser='on';} 			else {$hideuser='off';}
		if(isset($_POST['logincaptcha']))		{$logincaptcha='on';} 		else {$logincaptcha='off';}
		if(isset($_POST['antispam']))			{$antispam='on';} 			else {$antispam='off';}
		if(isset($_POST['woostructure']))		{$woostructure='on';} 		else {$woostructure='off';}
		if(isset($_POST['yoaststructure']))		{$yoaststructure='on';} 	else {$yoaststructure='off';}
		if(isset($_POST['keyrankmath']))		{$keyrankmath='on';} 		else {$keyrankmath='off';}
		if(isset($_POST['hideplugin']))			{$hideplugin='on';} 		else {$hideplugin='off';}
		if(isset($_POST['freezero']))			{$freezero='on';} 			else {$freezero='off';}
		if(isset($_POST['dlplugin']))			{$dlplugin='on';} 			else {$dlplugin='off';}
		if(isset($_POST['hidemustuse']))		{$hidemustuse='on';} 		else {$hidemustuse='off';}
		if(isset($_POST['addaparat']))			{$addaparat='on';} 			else {$addaparat='off';}
		if(isset($_POST['cartfragments']))		{$cartfragments='on';} 		else {$cartfragments='off';}
		if(isset($_POST['shortlink']))			{$shortlink='on';} 			else {$shortlink='off';}
		if(isset($_POST['fontsize']))			{$fontsize='on';} 			else {$fontsize='off';}
		if(isset($_POST['enlang']))				{$enlang='on';} 			else {$enlang='off';}
		if(isset($_POST['authorfilter']))		{$authorfilter='on';} 		else {$authorfilter='off';}
		if(isset($_POST['maintenance']))		{$maintenance='on';} 		else {$maintenance='off';}
		if(isset($_POST['delmedia']))			{$delmedia='on';} 			else {$delmedia='off';}
		if(isset($_POST['catfilter']))			{$catfilter='on';} 			else {$catfilter='off';}
		if(isset($_POST['showid']))				{$showid='on';} 			else {$showid='off';}
		if(isset($_POST['filetype']))			{$filetype='on';} 			else {$filetype='off';}
		if(isset($_POST['customcode']))			{$customcode='on';} 		else {$customcode='off';}
		if(isset($_POST['findreplace']))		{$findreplace='on';} 		else {$findreplace='off';}
		if(isset($_POST['faqschema']))			{$faqschema='on';} 			else {$faqschema='off';}
		if(isset($_POST['classicwg']))			{$classicwg='on';} 			else {$classicwg='off';}
		if(isset($_POST['editorfr']))			{$editorfr='on';} 			else {$editorfr='off';}
		
        // update in option table
        update_option('logourl_setting' , $logourl);
        update_option('loginlogo_setting' , $loginlogo);
        update_option('commenturl_setting' , $commenturl);
		update_option('wpversion_setting' , $wpversion);
		update_option('welcomepanel_setting' , $welcomepanel);
		update_option('disxmlrpc_setting' , $disxmlrpc);
		update_option('wplogotop_setting' , $wplogotop);
		update_option('fixedzlib_setting' , $fixedzlib);
		update_option('hentryclass_setting' , $hentryclass);
		update_option('acduplicate_setting' , $acduplicate);
		update_option('acexcerpt_setting' , $acexcerpt);
		update_option('diswpupn_setting' , $diswpupn);
		update_option('disalln_setting' , $disalln);
		update_option('dispupn_setting' , $dispupn);
		update_option('distupn_setting' , $distupn);
		update_option('diswpcupn_setting' , $diswpcupn);
		update_option('diswpcn_setting' , $diswpcn);
		update_option('upzip_setting' , $upzip);
		update_option('weakpass_setting' , $weakpass);
		update_option('disaddadmin_setting' , $disaddadmin);
		update_option('errormessage_setting' , $errormessage);
		update_option('loginurl_setting' , $loginurl);
		update_option('addadmin_setting' , $addadmin);
		update_option('cookieexp_setting' , $cookieexp);
		update_option('adminclass_setting' , $adminclass);
		update_option('nonadmin_setting' , $nonadmin);
		update_option('hideuser_setting' , $hideuser);
		update_option('logincaptcha_setting' , $logincaptcha);
		update_option('antispam_setting' , $antispam);
		update_option('woostructure_setting' , $woostructure);
		update_option('yoaststructure_setting' , $yoaststructure);
		update_option('keyrankmath_setting' , $keyrankmath);
		update_option('hideplugin_setting' , $hideplugin);
		update_option('freezero_setting' , $freezero);
		update_option('dlplugin_setting' , $dlplugin);
		update_option('hidemustuse_setting' , $hidemustuse);
		update_option('addaparat_setting' , $addaparat);
		update_option('cartfragments_setting' , $cartfragments);
		update_option('shortlink_setting' , $shortlink);
		update_option('fontsize_setting' , $fontsize);
		update_option('enlang_setting' , $enlang);
		update_option('authorfilter_setting' , $authorfilter);
		update_option('maintenance_setting' , $maintenance);
		update_option('delmedia_setting' , $delmedia);
		update_option('catfilter_setting' , $catfilter);
		update_option('showid_setting' , $showid);
		update_option('filetype_setting' , $filetype);
		update_option('customcode_setting' , $customcode);
		update_option('findreplace_setting' , $findreplace);
		update_option('faqschema_setting' , $faqschema);
		update_option('classicwg_setting' , $classicwg);
		update_option('editorfr_setting' , $editorfr);

        echo ("<SCRIPT LANGUAGE='JavaScript'>
               window.location.href='".$_SERVER['HTTP_REFERER']."&success';
            </SCRIPT>");
    }

}

?>