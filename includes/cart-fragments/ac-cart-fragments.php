<?php
 
defined( 'ABSPATH' ) or die( __( 'No script kiddies please!' ) );

if ( ! class_exists( 'ACh_Disable_Cart_Fragments' ) ) {

	class ACh_Disable_Cart_Fragments {

		function __construct(){
			if( $this->achcf_is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
				add_action( 'wp_enqueue_scripts', array( $this, 'ach_disable_cart_fragments' ), 999 );
			}
		}

		function achcf_is_plugin_active( $path ) {
			return in_array( $path, apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
		}

		/*
		 * Disable Cart Fragments Function
		 */
		function ach_disable_cart_fragments() {
			global $wp_scripts;

			$handle = 'wc-cart-fragments';

			$load_cart_fragments_path = $wp_scripts->registered[ $handle ]->src;
			$wp_scripts->registered[ $handle ]->src = null;
			wp_add_inline_script(
				'jquery',
				'
				function optimocha_getCookie(name) {
					var v = document.cookie.match("(^|;) ?" + name + "=([^;]*)(;|$)");
					return v ? v[2] : null;
				}

				function optimocha_check_wc_cart_script() {
				var cart_src = "' . $load_cart_fragments_path . '";
				var script_id = "optimocha_loaded_wc_cart_fragments";

					if( document.getElementById(script_id) !== null ) {
						return false;
					}

					if( optimocha_getCookie("woocommerce_cart_hash") ) {
						var script = document.createElement("script");
						script.id = script_id;
						script.src = cart_src;
						script.async = true;
						document.head.appendChild(script);
					}
				}

				optimocha_check_wc_cart_script();
				document.addEventListener("click", function(){setTimeout(optimocha_check_wc_cart_script,1000);});
				'
			);
		}
	}

	new ACh_Disable_Cart_Fragments();
}
