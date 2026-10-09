<?php

if (!defined('ABSPATH')) {
	echo "Go Back!!";
	exit();
}

// Duplicate Function

function achdp_duplicate_as_draft(){
	global $wpdb;
	if (! ( isset( $_GET['post']) || isset( $_POST['post'])  || ( isset($_REQUEST['action']) && 'achdp_duplicate_as_draft' == $_REQUEST['action'] ) ) ) {
		wp_die('No post for duplicating!');
	}

	/*
	 * Nonce verification
	 */
	if ( !isset( $_GET['duplicate_nonce'] ) || !wp_verify_nonce( $_GET['duplicate_nonce'], basename( __FILE__ ) ) )
		return;

	/*
	 * get the original post id
	 */
	$achdp_post_id = (isset($_GET['post']) ? absint( $_GET['post'] ) : absint( $_POST['post'] ) );
	/*
	 * and all the original post data then
	 */
	$achdp_post = get_post( $achdp_post_id );

	/*
	 * if you don't want current user to be the new post author,
	 * then change next couple of lines to this: $new_post_author = $post->post_author;
	 */
	$achdp_current_user = wp_get_current_user();
	$achdp_new_post_author = $achdp_current_user->ID;

	/*
	 * if post data exists, create the post duplicate
	 */
	if (isset( $achdp_post ) && $achdp_post != null) {

		/*
		 * new post data array
		 */
		$achdp_args = array(
			'comment_status' => $achdp_post->comment_status,
			'ping_status'    => $achdp_post->ping_status,
			'post_author'    => $achdp_new_post_author,
			'post_content'   => $achdp_post->post_content,
			'post_excerpt'   => $achdp_post->post_excerpt,
			'post_name'      => $achdp_post->post_name,
			'post_parent'    => $achdp_post->post_parent,
			'post_password'  => $achdp_post->post_password,
			'post_status'    => 'draft',
			'post_title'     => $achdp_post->post_title,
			'post_type'      => $achdp_post->post_type,
			'to_ping'        => $achdp_post->to_ping,
			'menu_order'     => $achdp_post->menu_order
		);

		/*
		 * insert the post by wp_insert_post() function
		 */
		$achdp_new_post_id = wp_insert_post( $achdp_args );

		/*
		 * get all current post terms ad set them to the new post draft
		 */
		$achdp_taxonomies = get_object_taxonomies($achdp_post->post_type); // returns array of taxonomy names for post type, ex array("category", "post_tag");
		foreach ($achdp_taxonomies as $achdp_taxonomy) {
			$achdp_post_terms = wp_get_object_terms($achdp_post_id, $achdp_taxonomy, array('fields' => 'slugs'));
			wp_set_object_terms($achdp_new_post_id, $achdp_post_terms, $achdp_taxonomy, false);
		}

		/*
		 * duplicate all post meta just in two SQL queries
		 */
		$achdp_post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$achdp_post_id");
		if (count($achdp_post_meta_infos)!=0) {
			$achdp_sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
			foreach ($achdp_post_meta_infos as $achdp_meta_info) {
				$achdp_meta_key = $achdp_meta_info->meta_key;
				if( $achdp_meta_key == '_wp_old_slug' ) continue;
				$achdp_meta_value = addslashes($achdp_meta_info->meta_value);
				$achdp_sql_query_sel[]= "SELECT $achdp_new_post_id, '$achdp_meta_key', '$achdp_meta_value'";
			}
			$achdp_sql_query.= implode(" UNION ALL ", $achdp_sql_query_sel);
			$wpdb->query($achdp_sql_query);
		}


		/*
		 * finally, redirect to the edit post screen for the new draft
		 */

		$achdp_all_post_types = get_post_types([],'names');

		foreach ($achdp_all_post_types as $achdp_key=>$achdp_value) {
			$achdp_names[] = $achdp_key;
		}

		$achdp_current_post_type=  get_post_type($achdp_post_id);

		if (is_array($achdp_names) && in_array($achdp_current_post_type, $achdp_names)) {
			wp_redirect( admin_url( 'edit.php?post_type='.$achdp_current_post_type) );
		}

		exit;
	} else {
		wp_die('Failed. Not Found Post: ' . $achdp_post_id);
	}
}
add_action( 'admin_action_achdp_duplicate_as_draft', 'achdp_duplicate_as_draft' );

/*
 * Add the duplicate link to action list for post_row_actions
 */
function achdp_duplicate_link( $actions, $post ) {
	if (current_user_can('edit_posts')) {
		$actions['duplicate'] = '<a href="' . wp_nonce_url('admin.php?action=achdp_duplicate_as_draft&post=' . $post->ID, basename(__FILE__), 'duplicate_nonce' ) . '" title="Duplicate it" rel="permalink">Duplicate</a>';
	}
	return $actions;
}

add_filter( 'post_row_actions', 'achdp_duplicate_link', 10, 2 );

add_filter('page_row_actions', 'achdp_duplicate_link', 10, 2);



/*
* Redirecting
*/
function achdp_user_redirecting( $plugin ) {
	if( plugin_basename(__FILE__) == $plugin ){
		wp_redirect( admin_url( 'tools.php?page=duplicate-pp' ) );
		die();
	}
}
add_action( 'activated_plugin', 'achdp_user_redirecting' );

?>