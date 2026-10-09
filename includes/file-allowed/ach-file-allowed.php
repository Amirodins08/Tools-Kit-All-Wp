<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function ach_file_types($mime_types){
	$mime_types['apk'] = 'application/vnd.android.package-archive';
	$mime_types['zip'] = 'application/zip';
	$mime_types['rar'] = 'application/x-rar-compressed';
	$mime_types['tar'] = 'application/x-tar';
	$mime_types['gz'] = 'application/x-gzip';
	$mime_types['gzip'] = 'application/x-gzip';
	$mime_types['tiff'] = 'image/tiff';
	$mime_types['tif'] = 'image/tiff';
	$mime_types['bmp'] = 'image/bmp';
	$mime_types['webp'] = 'image/webp';
	$mime_types['psd'] = 'image/vnd.adobe.photoshop';
	$mime_types['ai'] = 'application/postscript';
	$mime_types['indd'] = 'application/x-indesign';
	$mime_types['eps'] = 'application/postscript';
	$mime_types['rtf'] = 'application/rtf';
	$mime_types['txt'] = 'text/plain';
	$mime_types['wav'] = 'audio/x-wav';
	$mime_types['csv'] = 'text/csv';
	$mime_types['xml'] = 'application/xml';
	$mime_types['flv'] = 'video/x-flv';
	$mime_types['swf'] = 'application/x-shockwave-flash';
	$mime_types['vcf'] = 'text/x-vcard';
	$mime_types['html'] = 'text/html';
	$mime_types['htm'] = 'text/html';
	$mime_types['css'] = 'text/css';
	$mime_types['js'] = 'application/javascript';
	$mime_types['ico'] = 'image/x-icon';
	$mime_types['otf'] = 'application/x-font-otf';
	$mime_types['ttf'] = 'application/x-font-ttf';
	$mime_types['woff'] = 'application/x-font-woff';
	$mime_types['woff2'] = 'application/x-font-woff2';
	$mime_types['eot'] = 'application/vnd.ms-fontobject';
	$mime_types['exe'] = 'application/x-msdownload';
	$mime_types['ics'] = 'text/calendar';
	return $mime_types;
}
add_filter('upload_mimes', 'ach_file_types', 100000 );

class ACHSVG{
	function __construct(){
		add_action( 'admin_init', array( $this, 'add_svg_support' ) );
		add_action( 'admin_footer', array( $this, 'fix_svg_thumbnail_size' ) );
		add_filter( 'upload_mimes', array( $this, 'add_svg_mime' ) );
		add_filter( 'wp_check_filetype_and_ext', array( $this, 'wp_check_filetype_and_ext' ), 100, 4 );
		add_filter( 'wp_generate_attachment_metadata', array( $this, 'wp_generate_attachment_metadata' ), 10, 2 );
		add_filter( 'fl_module_upload_regex', array( $this, 'fl_module_upload_regex' ), 10, 4 );
	}

	function fl_module_upload_regex( $regex, $type, $ext, $file ){
		if( $ext == "svg" || $ext == "svgz" ){
			$regex['photo'] = str_replace( '|png|', '|png|svgz?|', $regex['photo'] );
		}
		return $regex;
	}

	function fix_svg_thumbnail_size(){
		echo '<style>.attachment-info .thumbnail img[src$=".svg"],#postimagediv .inside img[src$=".svg"]{width:100%}</style>';
	}

	function wp_generate_attachment_metadata( $metadata, $attachment_id ){
		if( get_post_mime_type( $attachment_id ) == 'image/svg+xml' ){
			$svg_path = get_attached_file( $attachment_id );
			$dimensions = $this->svg_dimensions( $svg_path );
			$metadata['width'] = $dimensions->width;
			$metadata['height'] = $dimensions->height;
		}
		return $metadata;
	}

	function wp_check_filetype_and_ext( $filetype_ext_data, $file, $filename, $mimes ){
		if( substr( $filename, -4 ) == '.svg' ){
			$filetype_ext_data['ext'] = 'svg';
			$filetype_ext_data['type'] = 'image/svg+xml';
		}
		if( substr( $filename, -5 ) == '.svgz' ){
			$filetype_ext_data['ext'] = 'svgz';
			$filetype_ext_data['type'] = 'image/svg+xml';
		}
		return $filetype_ext_data;
	}

	public function add_svg_support(){

		function svg_thumbs( $content ){
			return apply_filters( 'final_output', $content );
		}

		ob_start( 'svg_thumbs' );

		add_filter( 'final_output', array( $this, 'final_output' ) );
		add_filter( 'wp_prepare_attachment_for_js', array( $this, 'wp_prepare_attachment_for_js' ), 10, 3 );
	}

	function final_output( $content ){
		$content = str_replace(
			'<# } else if ( \'image\' === data.type && data.sizes && data.sizes.full ) { #>',
			'<# } else if ( \'svg+xml\' === data.subtype ) { #>
				<img class="details-image" src="{{ data.url }}" draggable="false" />
			<# } else if ( \'image\' === data.type && data.sizes && data.sizes.full ) { #>',
			$content
		);

		$content = str_replace(
			'<# } else if ( \'image\' === data.type && data.sizes ) { #>',
			'<# } else if ( \'svg+xml\' === data.subtype ) { #>
				<div class="centered">
					<img src="{{ data.url }}" class="thumbnail" draggable="false" />
				</div>
			<# } else if ( \'image\' === data.type && data.sizes ) { #>',
			$content
		);

		return $content;
	}

	public function add_svg_mime( $mimes = array() ){
		$mimes['svg'] = 'image/svg+xml';
		$mimes['svgz'] = 'image/svg+xml';
		return $mimes;
	}

	function wp_prepare_attachment_for_js( $response, $attachment, $meta ){
		if( $response['mime'] == 'image/svg+xml' && empty( $response['sizes'] ) ){
			$svg_path = get_attached_file( $attachment->ID );
			if( ! file_exists( $svg_path ) ){
				$svg_path = $response['url'];
			}
			$dimensions = $this->svg_dimensions( $svg_path );
			$response['sizes'] = array(
				'full' => array(
					'url' => $response['url'],
					'width' => $dimensions->width,
					'height' => $dimensions->height,
					'orientation' => $dimensions->width > $dimensions->height ? 'landscape' : 'portrait'
				)
			);
		}
		return $response;
	}

	function svg_dimensions( $svg ){
		$svg = simplexml_load_file( $svg );
		$width = 0;
		$height = 0;
		if( $svg ){
			$attributes = $svg->attributes();
			if( isset( $attributes->width, $attributes->height ) ){
				$width = floatval( $attributes->width );
				$height = floatval( $attributes->height );
			}elseif( isset( $attributes->viewBox ) ){
				$sizes = explode( " ", $attributes->viewBox );
				if( isset( $sizes[2], $sizes[3] ) ){
					$width = floatval( $sizes[2] );
					$height = floatval( $sizes[3] );
				}
			}
		}
		return (object)array( 'width' => $width, 'height' => $height );
	}
}

new ACHSVG();

?>