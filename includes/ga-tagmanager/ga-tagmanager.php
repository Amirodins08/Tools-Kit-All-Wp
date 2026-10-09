<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Google Analytics Class

class ACH_REALLY_SIMPLE_GA {
	function __construct() {
		add_action('wp_head', array($this, 'ach_show_google_analytics'));
	}

	public function ach_show_google_analytics() {
		$analytics_code = get_option( 'ach_google_anaytics_code' );
		if(isset($analytics_code) && !empty($analytics_code)){
			$ouput = <<<EOT
			
<!-- Tracking code generated with ACh Toolkit plugin -->
<script async src="https://www.googletagmanager.com/gtag/js?id=$analytics_code"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', '$analytics_code');
</script>
<!-- / Global site tag (gtag.js) - Google Analytics -->
				
EOT;
			echo $ouput;
		}
	}
}
new ACH_REALLY_SIMPLE_GA();

// Google Tag Manager Class

class ACH_GOOGLE_TAG_MANAGER {
	function __construct() {
		add_action('wp_head', array($this, 'ach_header_google_tag_manager_id'));
		add_action('wp_body_open', array($this, 'ach_body_google_tag_manager_id'));
	}

	public function ach_header_google_tag_manager_id() {
		$tagmanager_id = get_option( 'ach_tag_manager_id' );
		if(isset($tagmanager_id) && !empty($tagmanager_id)){
			$ouput = <<<EOT
			
<!-- Google Tag Manager code generated with ACh Toolkit plugin -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','$tagmanager_id');</script>
<!-- / Google Tag Manager -->
				
EOT;
			echo $ouput;
		}
	}
	public function ach_body_google_tag_manager_id() {
		$tagmanager_id = get_option( 'ach_tag_manager_id' );
		if(isset($tagmanager_id) && !empty($tagmanager_id)){
			$ouput = <<<EOT
			
<!-- Google Tag Manager (noscript) code generated with ACh Toolkit plugin -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=$tagmanager_id"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- / Google Tag Manager (noscript) -->
				
EOT;
			echo $ouput;
		}
	}
}
new ACH_GOOGLE_TAG_MANAGER();

?>