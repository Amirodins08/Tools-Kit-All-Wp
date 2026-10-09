<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function ach_get_attachment_id( $attachment_id ) {

		$used_in_posts = array();

		if ( wp_attachment_is_image( $attachment_id ) ) {
			$query = new WP_Query( array(
				'meta_key'       => '_thumbnail_id',
				'meta_value'     => $attachment_id,
				'post_type'      => 'any',	
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'posts_per_page' => -1,
			) );
			$used_in_posts = array_merge( $used_in_posts, $query->posts );
		}

		$attachment_urls = array( wp_get_attachment_url( $attachment_id ) );

		if ( wp_attachment_is_image( $attachment_id ) ) {
			foreach ( get_intermediate_image_sizes() as $size ) {
				$intermediate = image_get_intermediate_size( $attachment_id, $size );
				if ( $intermediate ) {
					$attachment_urls[] = $intermediate['url'];
				}
			}
		}

		foreach ( $attachment_urls as $attachment_url ) {
			$query = new WP_Query( array(
				's'              => $attachment_url,
				'post_type'      => 'any',	
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'posts_per_page' => -1,
			) );
			$used_in_posts = array_merge( $used_in_posts, $query->posts );
		}
		$used_in_posts = array_unique( $used_in_posts );

		return $used_in_posts;
}

/**
 * Delete attachments if no post is using the attchment.
 * Checks if attachment is used in post and changes the parent id if the original post should be deleted.
 */
function delete_attachments($post_id) {
	$custom = get_post_custom( $post_id );
	$args = array(
		'post_type' => 'attachment',
		'numberposts' => -1,
		'post_status' => null,
		'post_parent' => $post_id
	);
	$attachments = get_posts($args);

	if ($attachments) {
		foreach($attachments as $attachment) {
			$used_in_posts = ach_get_attachment_id( $attachment->ID );
			if( ! empty( $used_in_posts ) ) {
				$args = array(
					'ID' => $attachment->ID,
					'post_parent' => $used_in_posts[0],
				);
				wp_update_post( $args );

			} else {
				wp_delete_attachment( $attachment->ID, true );
			}
		}
	}
}
add_action('before_delete_post', 'delete_attachments');

// Delete Woocommerce product images when deleting the product.
/*
function ach_woo_delete_images($post_id) {
	global $wpdb;
		$arg = array(
			'post_parent' => $post_id,
			'post_type'   => 'attachment', 
			'numberposts' => -1,
			'post_status' => 'any' 
		); 
		$childrens = get_children( $arg);
		if($childrens):
			foreach($childrens as $attachment):   
				wp_delete_attachment( $attachment->ID, true ); 
				$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE post_id = ".$attachment->ID);
				wp_delete_post($attachment->ID,true ); 
			endforeach; 
		endif; 
}
add_action('before_delete_post', 'ach_woo_delete_images', 10 ,1);
*/

?>
