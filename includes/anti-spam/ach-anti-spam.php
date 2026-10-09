<?php

if ( ! defined( 'WPINC' ) ) {
	die;
}
 
// Step 1. Firstly, remove the Comment Action URL from HTML Document to prevent bot comments.
function ach_anti_apam_remove_comment_action_url( $defaults ) {
	$defaults['action'] = '';
	return $defaults;
}
add_filter( 'comment_form_defaults', 'ach_anti_apam_remove_comment_action_url');

// Step 2. As visitor scroll the web page, fix the broken action URL including md5 hash as Query String Parameter.
function ach_anti_spam_modify_action_url() {
	if (is_singular() && comments_open()) {
		echo "<script>\n";
		// Most popular IDs are listed here for compatibility with all popular themes
		echo "let commentForm = document.querySelector(\"#commentform, #ast-commentform, #ht-commentform\");\n";
		echo "document.onscroll = function () {";
		$ach_anti_spam_unique_key  = md5($_SERVER['DOCUMENT_ROOT']); // Generate unique query string for each site
		echo "commentForm.action = \"" . wp_make_link_relative(get_site_url()) . "/wp-comments-post.php?$ach_anti_spam_unique_key\";\n";
		echo "};";
		echo "</script>\n";
	}
}
add_action('wp_footer', 'ach_anti_spam_modify_action_url', 99);

// Step 3. Error message
function ach_anti_spam_block_response() {
	header('HTTP/1.1 400 Bad Request');
	header('Status: 400 Bad Request');
	header('Connection: Close');
	die('<h1>Error 400</h1><span style="padding:10px;background-color:#FFFF00">If you are an admin of this site, please clear once the Page Cache.</span>');
}

// Step 4. Allow POST request for comment if md5 hash matches in Request Header as Query String Parameter.
$ach_anti_spam_request = "wp-comments-post.php";
$ach_anti_spam_requested_uri = isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : "";
$ach_anti_spam_post_request_received = isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST" ? true : false;
$ach_anti_spam_request_recieved = (strpos($ach_anti_spam_requested_uri, $ach_anti_spam_request) !== false) ? true : false;
$ach_anti_spam_query_list = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : false;
$ach_anti_spam_comment_requested_key = md5($_SERVER['DOCUMENT_ROOT']); // Check Request Header against Unique Key
$ach_anti_spam_request_contain_key = $ach_anti_spam_query_list == $ach_anti_spam_comment_requested_key ? true : false;
if ($ach_anti_spam_post_request_received) {
	if ($ach_anti_spam_request_recieved && !$ach_anti_spam_request_contain_key) {
		ach_anti_spam_block_response();
	}
}