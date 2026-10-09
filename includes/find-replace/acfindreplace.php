<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class AChFindandReplace{
    
    function __construct() {
        // add menu to Tools tab
        add_action('admin_menu', array($this, 'addSettingPage'));
        // populate input data and update function
        add_action('admin_init', array($this, 'pageInit'));
        // if posts need to publish by backend or publicize to social media
        add_filter( 'wp_insert_post_data', array($this,'custom_filter'),'99',2);
        // if you have huge amount of posts already publish with tag. 
        add_action( 'template_redirect', array($this,'filter_posts_contents' ));
    }
	
	public function addSettingPage(){
		add_submenu_page( 'tools.php', __( 'ACh Find and Replace', 'ach_toolkit' ), __( 'ACh Find Replace', 'ach_toolkit' ), 'manage_options', 'ach-find-replace', array( $this, 'generateSettingsPage' ) );
	}
    public function generateSettingsPage(){
?>
	<div class="wrap">
		<h2 class="find-replace-header"><span class="find-header">ACh Find and Replace</span></h2>
		<div class="row">
			<div class="achfind">
				<form class="form-horizontal" method="post" action="options.php">
				<?php settings_fields('achfr_option_group'); ?>
				<div class="panel panel-default">
					<div class="panel-heading"><h3 class="panel-title">Rules Setup</h3></div>
					<table class="table table-striped">
						<?php
						$get_Rtfar =  get_option('achfr_settings');
						// Check at least one rule has been setup.
						if(empty($get_Rtfar)){ 
						// this action will be fire if no rule has been setup.
						?>
						<thead>
							<tr class="ach-td-group">
								<td>
									<div class="input-group">
										<span class="input-group-addon" id="basic-addon1">Find</span>
										<textarea cols="" rows="" name="array_key[find][]" class="form-control" placeholder="Set find rules"></textarea>
									</div>
								</td>
								<td colspan="2">
									<div class="input-group">
										<span class="input-group-addon" id="basic-addon1">Replace</span>
										<textarea cols="" rows="" name="array_key[replace][]" class="form-control" placeholder="Set replace rules"></textarea>
									</div>
								</td>
							</tr>
						</thead>
						<?php }else{ 
						// if at least one rule setup this action will be fired.
						foreach ($get_Rtfar as $find=>$replace){
						?>
						<thead>
							<tr>
								<td>
									<div class="input-group">
										<span class="input-group-addon" id="basic-addon1">Find</span>
										<textarea cols="" rows="" name="array_key[find][]" class="form-control" placeholder="Set find rules"><?php echo $find?></textarea>
									</div>
								</td>
								<td colspan="2">
									<div class="input-group">
										<span class="input-group-addon" id="basic-addon1">Replace</span>
										<textarea cols="" rows="" name="array_key[replace][]" class="form-control" placeholder="Set replace rules"><?php echo $replace?></textarea>
									</div>
								</td>
							</tr>
						</thead>
						<?php } }?>
						<tbody id="add_more_fields"></tbody>
						<tfoot>
							<tr>
								<td colspan="2">
									<input type="hidden" id="more_field_count" value="1"/>
									<a class="addmorebtn btn btn-default pull-right" onClick="addFormField(); return false;" >Add More</a>
								</td>
							</tr>
						</tfoot>
					</table>
					<div><p class="ach-note-p"><span class="ach-note">Note: </span>If you want to delete some rules, just put a space in <span class="ach-note-find">both fields (Find & Replace)</span> then press the <span class="ach-note-update">Update Rules</span> button.</p></div>
				</div>
				<div class="panel-footer">
					<input type="submit" class="btn btn-success" value="Update Rules"/>
				</div>
				</form>
			</div>
		</div>
	</div>
<?php
    }
    
    public function pageInit(){
        register_setting('achfr_option_group', 'array_key', array($this, 'saveInputData'));
    }
    public function saveInputData($input){
        $find_A=$input['find'];$replace_A=$input['replace'];$find_Replace=array();
        if(!empty($find_A[0])){
            foreach($find_A as $key=>$find){
                if(!empty($find)){
                    $find_Replace [trim($find)] =trim($replace_A[$key]);
                }
            }
            delete_option('achfr_settings');
            update_option('achfr_settings', $find_Replace);
        }
    }
   
    function custom_filter($data , $postarr){
    $get_Rtfar = get_option( 'achfr_settings' );
	if ( is_array( $get_Rtfar ) ) {
            foreach ($get_Rtfar as $find=>$replace){
                $data['post_content'] = str_replace( $find, $replace, $data['post_content'] );
                $data['post_title'] = str_replace( $find, $replace, $data['post_title'] );
            }
        }
        return $data;
    }
    
    function filter_posts_contents(){
        ob_start();
        ob_start( array($this,'filterContents' ));
    }
    
    function filterContents($buffer) {
        $get_Rtfar = get_option( 'achfr_settings' );
        if ( is_array( $get_Rtfar ) ) {
            foreach ($get_Rtfar as $find=>$replace){
                $buffer = str_replace( $find, $replace, $buffer );
            }
        }
        return $buffer;
    }
    
}
$RTAFAR=new AChFindandReplace();