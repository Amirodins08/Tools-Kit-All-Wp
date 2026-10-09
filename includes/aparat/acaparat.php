<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

	function aparat($atts) {
		//$atts = str_replace("’", "", $atts);
		extract( shortcode_atts( array(
			'id' => '',
			'width' => '100%',
			'height' => 450,
			'style' => 'margin: 15px 1px 10px 1px;'
		), $atts ) );
		$id = str_replace("’", "", $id);
		return "<center style='{$style}'><iframe src='".(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http')."://www.aparat.com/video/video/embed/videohash/{$id}/vt/frame' width='{$width}' height='{$height}' allowfullscreen='true' style='border:none!important'></iframe></center>";
	}
	add_shortcode( 'aparat', 'aparat' );

	function aparat_editor_btn($buttons) {
		array_push($buttons, "separator", "aparat_shortcode");
		return $buttons;
	}
	add_filter('mce_buttons', 'aparat_editor_btn', 0);

	function aparat_shortcode_register($plugin_array)	{
		$plugin_array['aparat_shortcode'] = plugins_url ('editor_plugin.js?v1.6', __FILE__);
		return $plugin_array;
	}
	add_filter('mce_external_plugins', "aparat_shortcode_register");

	function video_embed_link( $id, $regex, $callback, $priority ) {
		$output = sprintf(
			"[aparat id='%s']",
			$id[1]
		);
		return apply_filters( 'aparat_embed', $output, $id, $regex, $callback, $priority );
	}
	function register_embed() {
		wp_embed_register_handler( 'aparat', '#^https?://(?:www)\.aparat\.com\/v\/(.*?)\/?$#i', 'video_embed_link' );
	}
	add_action( 'init', 'register_embed' );
	
?>