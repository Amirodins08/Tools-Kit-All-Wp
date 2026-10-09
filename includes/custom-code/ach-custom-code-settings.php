<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'ACh_Custom_Code_Settings' ) ) :

class ACh_Custom_Code_Settings {

	function __construct() {
		if ( is_admin() ) {
			add_action( 'admin_init', array( $this, 'save_options' ) );
			add_action( 'admin_menu', array( $this, 'add_plugin_options_page' ) );
		}
	}

	/*
	 * add_plugin_options_page.
	 */
	function add_plugin_options_page() {
		add_submenu_page(
			'tools.php',
			__( 'ACh Custom Codes', 'ach_toolkit' ),
			__( ' ACh Custom Codes', 'ach_toolkit' ),
			'manage_options',
			'ach-custom-code',
			array( $this, 'create_plugin_options_page' )
		);
	}

	/**
	 * sanitize_content.
	 */
	function sanitize_content( $value ) {
		return stripslashes( $value );
	}

	/**
	 * save_options.
	 */
	function save_options() {
		if ( isset( $_POST['ach_code_submit'] ) ) {
			// Saving options
			foreach ( $this->get_settings() as $settings ) {
				if ( in_array( $settings['type'], array( 'title', 'sectionend' ) ) ) {
					continue;
				}
				$id = ACH_CODE_ID . '_' . $settings['id'];
				switch ( $settings['type'] ) {
					case 'checkbox':
						$value = ( isset( $_POST[ $id ] ) ? 'yes' : 'no' );
						break;
					default: // 'textarea', 'select':
						$value = ( isset( $_POST[ $id ] ) ? $this->sanitize_content( $_POST[ $id ] ) : '' );
						break;
				}
				update_option( $id, $value );
			}
			// (Re)creating / removing PHP file
			$this->handle_custom_php_file();
			// The end
			wp_safe_redirect( remove_query_arg( 'ach_disable_custom_php' ) );
			exit;
		}
	}

	/**
	 * handle_custom_php_file.
	 */
	function handle_custom_php_file() {
		$do_clean_up = false;
		if ( 'yes' === get_ach_code_option( 'php_enabled', 'no' ) ) {
			$file_content = get_ach_code_option( 'php', '' );
			if ( '' !== $file_content ) {
				$file_path = ach_code()->core->get_custom_php_file_path( true );
				file_put_contents( $file_path, '<?php' . PHP_EOL . $file_content );
			} else {
				$do_clean_up = true;
			}
		} else {
			$do_clean_up = true;
		}
		if ( $do_clean_up ) {
			$file_path = ach_code()->core->get_custom_php_file_path();
			if ( file_exists( $file_path ) ) {
				unlink( $file_path );
			}
			$dir_path  = ach_code()->core->get_custom_php_file_path( false, true );
			if ( file_exists( $dir_path ) ) {
				rmdir( $dir_path );
			}
		}
	}

	/**
	 * create_plugin_options_page.
	 */
	function create_plugin_options_page() {
		$table_data = array();
		foreach ( $this->get_settings() as $i => $settings ) {
			if ( in_array( $settings['type'], array( 'title', 'sectionend' ) ) ) {
				if ( 'title' === $settings['type'] ) {
					if ( 0 != $i ) {
						$table_data[] = array( '', '<hr>' );
					}
					$table_data[] = array(
						'<h2 style="color:orange;">' . $settings['title'] . '</h2>',
						( isset( $settings['desc_tip'] ) ? '<em>' . $settings['desc_tip'] . '</em>' : '' ),
					);
				}
				continue;
			}
			$id = ACH_CODE_ID . '_' . $settings['id'];
			$saved_value = get_ach_code_option( $settings['id'], $settings['default'] );
			switch ( $settings['type'] ) {
				case 'checkbox':
					$table_data[] = array(
						'<strong>' . $settings['title'] . '</strong>',
						'<input ' . checked( $saved_value, 'yes', false ) . ' type="checkbox" value="1" id="' . $id . '" name="' . $id . '"> ' .
							'<strong><em>' . isset($settings['desc']) . '</em></strong>',
					);
					break;
				case 'textarea':
					$table_data[] = array(
						'',
						( isset( $settings['desc_tip'] ) ? $settings['desc_tip'] : '' ) .
						'<textarea style="' . $settings['css'] . '" id="' . $id . '" name="' . $id . '">' . esc_textarea( $saved_value ) . '</textarea>' .
						( isset( $settings['desc'] ) ? '<em>' . $settings['desc'] . '</em>' : '' ),
					);
					break;
				case 'select':
					$select_options = '';
					foreach ( $settings['options'] as $option_id => $option_title ) {
						$select_options .= '<option value="' . $option_id . '" ' . selected( $option_id, $saved_value, false ) . '>' . $option_title . '</option>';
					}
					$table_data[] = array(
						( isset( $settings['title'] ) ? '<strong>' . $settings['title'] . '</strong>' : '' ),
						( isset( $settings['desc_tip'] ) ? $settings['desc_tip'] : '' ) .
						'<select style="' . ( isset( $settings['css'] ) ? $settings['css'] : '' ) . '" id="' . $id . '" name="' . $id . '">' . $select_options . '</select>' .
						( isset( $settings['desc'] ) ? '<em>' . $settings['desc'] . '</em>' : '' ),
					);
					break;
			}
		}
		$html = '';
		$html .= '<div class="wrap ach-header">';
		$html .= '<h2 class="code-header"><span class="ach-code-header">' . __( 'ACh custom CSS, JS and PHP codes', 'ach_toolkit' ) . '</span></h2>';
		$html .= '<form action="' . add_query_arg( '', '' ) . '" method="post">';
		$html .= $this->get_table_html( $table_data, array(
			'table_style'        => 'width:100%;',
			'table_class'        => 'widefat striped',
			'table_heading_type' => 'vertical',
			'columns_styles'     => array( 'width:20%', 'width:80%' )
		) );
		$html .= '<p>' . '<input id="ach_code_submit" name="ach_code_submit" type="submit" class="button button-primary" value="' .
			__( 'Save changes', 'ach_toolkit' ) . '">' . '</p>';
		$html .= '</form>';
		$html .= '</div>';
		echo $html;
	}

	/**
	 * get_settings.
	 */
	function get_settings() {
		$disable_custom_php_link = admin_url( 'tools.php?page=ach-custom-code&ach_disable_custom_php' );
		$textarea_style          = 'width:100%; direction:ltr; min-height:300px; font-family:Courier New,Courier,monospace; color: black;';
		return array(
			array(
				'title'    => __( 'CSS Options', 'ach_toolkit' ),
				'type'     => 'title',
				'id'       => 'css_options',
			),
			array(
				'title'    => __( 'Enable custom front-end CSS &rArr;', 'ach_toolkit' ),
				'id'       => 'front_end_css_enabled',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'id'       => 'front_end_css_position',
				'default'  => 'head',
				'type'     => 'select',
				'options'  => array(
					'head'   => __( 'Load in header', 'ach_toolkit' ),
					'footer' => __( 'Load in footer', 'ach_toolkit' ),
				),
			),
			array(
				'desc_tip' => __( 'CSS code:', 'ach_toolkit' ),
				'id'       => 'front_end_css',
				'default'  => '',
				'type'     => 'textarea',
				'css'      => $textarea_style,
			),
			array(
				'title'    => __( 'Enable custom back-end CSS &rArr;', 'ach_toolkit' ),
				'id'       => 'back_end_css_enabled',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'id'       => 'back_end_css_position',
				'default'  => 'head',
				'type'     => 'select',
				'options'  => array(
					'head'   => __( 'Load in header', 'ach_toolkit' ),
					'footer' => __( 'Load in footer', 'ach_toolkit' ),
				),
			),
			array(
				'desc_tip' => __( 'CSS code:', 'ach_toolkit' ),
				'id'       => 'back_end_css',
				'default'  => '',
				'type'     => 'textarea',
				'css'      => $textarea_style,
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'css_options',
			),
			array(
				'title'    => __( 'JS Options', 'ach_toolkit' ),
				'type'     => 'title',
				'id'       => 'js_options',
			),
			array(
				'title'    => __( 'Enable custom front-end JS &rArr;', 'ach_toolkit' ),
				'id'       => 'front_end_js_enabled',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'id'       => 'front_end_js_position',
				'default'  => 'head',
				'type'     => 'select',
				'options'  => array(
					'head'   => __( 'Load in header', 'ach_toolkit' ),
					'footer' => __( 'Load in footer', 'ach_toolkit' ),
				),
			),
			array(
				'desc_tip' => __( 'JS code:', 'ach_toolkit' ),
				'id'       => 'front_end_js',
				'default'  => '',
				'type'     => 'textarea',
				'css'      => $textarea_style,
			),
			array(
				'title'    => __( 'Enable custom back-end JS &rArr;', 'ach_toolkit' ),
				'id'       => 'back_end_js_enabled',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'id'       => 'back_end_js_position',
				'default'  => 'head',
				'type'     => 'select',
				'options'  => array(
					'head'   => __( 'Load in header', 'ach_toolkit' ),
					'footer' => __( 'Load in footer', 'ach_toolkit' ),
				),
			),
			array(
				'desc_tip' => __( 'JS code:', 'ach_toolkit' ),
				'id'       => 'back_end_js',
				'default'  => '',
				'type'     => 'textarea',
				'css'      => $textarea_style,
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'js_options',
			),
			array(
				'title'    => __( 'PHP Options', 'ach_toolkit' ),
				'type'     => 'title',
				'id'       => 'php_options',
				'desc_tip' => sprintf(
					__( 'Please note that if you enable custom PHP and enter non-valid PHP code here, your site will become unavailable. To fix this you will have to add %s attribute to the URL (you must be logged as shop manager or admin (for this reason custom PHP code is not executed on %s page)).', 'ach_toolkit' ),
						'<code>ach_disable_custom_php</code>', '<strong>wp-login.php</strong>' ) . ' ' .
					sprintf( __( 'E.g. %s', 'ach_toolkit' ), '<a href="' . $disable_custom_php_link . '">' . $disable_custom_php_link . '</a>' ),
			),
			array(
				'title'    => __( 'Enable custom PHP &rArr;', 'ach_toolkit' ),
				'id'       => 'php_enabled',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'desc_tip' => sprintf( __( 'PHP code (without the %s tag):', 'ach_toolkit' ), '<code>' . esc_html( '<?php' ) . '</code>' ),
				'id'       => 'php',
				'default'  => '',
				'type'     => 'textarea',
				'css'      => $textarea_style,
				'desc'     => ( file_exists( ach_code()->core->get_custom_php_file_path() ) ?
					sprintf( __( 'Automatically created PHP file: %s', 'ach_toolkit' ), '<code>' . ach_code()->core->get_custom_php_file_path() . '</code>' ) : '' ),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'php_options',
			),
		);
	}

	/**
	 * get_table_html.
	 */
	function get_table_html( $data, $args = array() ) {
		$defaults = array(
			'table_class'        => '',
			'table_style'        => '',
			'table_heading_type' => 'horizontal',
			'columns_classes'    => array(),
			'columns_styles'     => array(),
		);
		$args = array_merge( $defaults, $args );
		$table_class = ( '' == $args['table_class'] ? '' : ' class="' . $args['table_class'] . '"' );
		$table_style = ( '' == $args['table_style'] ? '' : ' style="' . $args['table_style'] . '"' );
		$html = '';
		$html .= '<table' . $table_class . $table_style . '>';
		$html .= '<tbody>';
		foreach( $data as $row_number => $row ) {
			$html .= '<tr>';
			foreach( $row as $column_number => $value ) {
				$th_or_td = ( ( 0 === $row_number && 'horizontal' === $args['table_heading_type'] ) || ( 0 === $column_number && 'vertical' === $args['table_heading_type'] ) ?
					'th' : 'td' );
				$column_class = ( isset( $args['columns_classes'][ $column_number ] ) ? ' class="' . $args['columns_classes'][ $column_number ] . '"' : '' );
				$column_style = ( isset( $args['columns_styles'][ $column_number ]  ) ? ' style="' . $args['columns_styles'][ $column_number ]  . '"' : '' );
				$html .= '<'  . $th_or_td . $column_class . $column_style . '>';
				$html .= $value;
				$html .= '</' . $th_or_td . '>';
			}
			$html .= '</tr>';
		}
		$html .= '</tbody>';
		$html .= '</table>';
		return $html;
	}

}

endif;

return new ACh_Custom_Code_Settings();
