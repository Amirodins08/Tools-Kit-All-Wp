<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class ach_editor_find_replace_button {
 
    /**
    * Constructor. Called when the plugin is initialised.
    */
    function __construct() {
 
 		if ( is_admin() ) {
			function ach_editor_replace_add_media_button() {
				printf( '<a href="%s" class="button ach_editor_find_replace_button" id="ach_editor_find_replace_button">' . '<span class="wp-media-buttons-icon dashicons dashicons-search"></span> %s' . '</a>', '#', __( 'Find & Replace', 'ach_toolkit' ) );
			}
            add_action( 'media_buttons', 'ach_editor_replace_add_media_button',100 );
			add_action( 'admin_print_footer_scripts', array( &$this, 'ach_editor_find_replace_admin_footer_scripts' ) );
		}
    }

function ach_editor_find_replace_admin_footer_scripts() {

	?>
	<script type="text/javascript">
		jQuery(function($){
			$(document).ready(function(){
				$("#ach_editor_find_replace_button").click(function(){
					//WP Editor always use "content" as ID
					var defaultid = "content";
					var text ="";
					
					if($('#wp-content-wrap').hasClass('html-active')){ // We are in text mode
						text = $('#'+defaultid).val(); // get the textarea's content
					} else { // We are in tinyMCE mode
						var activeEditor = tinyMCE.get(defaultid);
						if(activeEditor!==null){ // Make sure we're not calling getContent on null
							text = activeEditor.getContent({format : 'raw'}); 
						}
					}
					
					if(text==""){
						alert("You TEXT AREA ID is not default value or you don't have any content in the Editor. Please read help file.");
						return;
					}
					
					var search = prompt('Find What:');
					if( !search)
					return;
			
					var pattern = new RegExp(search,"gi");
					var findCount = (text.match(pattern) || []).length;
			
					if(findCount == 0){
						alert("Nothing found!");
						return;
					}
		
					var replace = prompt('Replace With:');
					if(  !replace)
					replace="";                

					if(confirm("find '"+search+"', "+findCount + " matches found, replace with '"+replace+"' now?")){
						var retext = text.replace(pattern, replace);
         
						if($('#wp-content-wrap').hasClass('html-active')){ // We are in text mode
							$('#'+defaultid).val(retext); // Update the textarea's content
						} else { // We are in tinyMCE mode
							var activeEditor = tinyMCE.get(defaultid);
							if(activeEditor!==null){ // Make sure we're not calling setContent on null
								text = activeEditor.setContent(retext); // Update tinyMCE's content
							}
						}
					}
				});
			});
		});		

	</script>
	<?php
}

}

$ach_editor_find_replace_button = new ach_editor_find_replace_button;
?>