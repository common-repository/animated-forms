<?php

class PMAF_Animated_Forms_Validation {
	
	public $form_id = null;
		
	private static $_instance = null;
	
	public $confirmed_attachments = [];
		
	public function __construct() {}
	
	public function validation() {
		
		// default response
		$response = [ 'status' => 'success' ];
		
		if( !empty( $this->confirmed_attachments ) ) return $response;
		
		$form_id = isset( $_POST['form_id'] ) && $_POST['form_id'] ? $_POST['form_id'] : '';
		$form_settings = pmaf_forms_data()->get_form_settings( $form_id );
		
		
		
		if( isset( $_FILES ) ) {
			
			$file_fields = array_keys( $_FILES );
			$file_stat = false;
			foreach( $file_fields as $f_field ) { 
				if( isset( $_FILES[$f_field]['name'] ) ) {
					if( isset( $_FILES[$f_field]['name'][0] ) && !empty( $_FILES[$f_field]['name'][0] ) ) {
						$file_stat = true;
					} elseif( !empty( $_FILES[$f_field]['name'] ) && !is_array( $_FILES[$f_field]['name'] ) ) {
						$file_stat = true;
					}
				}
			}
			
			if( $file_stat ) {
				
				$files = $_FILES; 
				
				$sform_data = pmaf_forms_data()->get_form_data( $form_id );  
				foreach( $files as $key => $all_files ) {
					
					$mail_attachments = [];
					$field_id = str_replace( 'field_', '', $key );
					$supported_formats = $sform_data[$field_id]['form']['accept'];
					$nfiles = $sform_data[$field_id]['form']['nfiles'];
					$max_size = !empty( $sform_data[$field_id]['form']['size'] ) ? $sform_data[$field_id]['form']['size'] : 1;
					
					if( strpos( $supported_formats, 'image/*' ) !== false ) {
						$supported_formats = str_replace( 'image/*', 'jpg,jpeg,png,gif,webp', $supported_formats );
					} elseif( strpos( $supported_formats, 'image/' ) !== false ) {
						$supported_formats = str_replace( 'image/', '', $supported_formats );
					}
					$supported_formats = str_replace( '.', '', $supported_formats );
					$supported_formats = str_replace( ',,', ',', $supported_formats );
					$supported_formats = rtrim( $supported_formats, ',' );

					$allowedTypes = !empty( $supported_formats ) ? explode( ',', $supported_formats ) : [];
					
					if( !empty( $all_files['name'] ) && !is_array( $all_files['name'] ) ) {
						$all_files = array_map( function( $f ){ return $f ? [$f] : ''; }, $all_files );
					}

					$n_files = !empty( $all_files['name'] ) ? count( $all_files['name'] ) : 0; 
					if( $n_files ) { 
						for( $i = 0; $i < $n_files; $i++ ) {
							
							$fileName = $all_files['name'][$i];
							if( !empty( $fileName ) ) {
								$fileType = strtolower( pathinfo( $fileName, PATHINFO_EXTENSION ) );
								if( !in_array( $fileType, $allowedTypes ) ) {
									$response['status'] = 'failed';
									$response['msg'] = $fileName. ' is not under given formats. "'. $supported_formats .'"';
									return $response;
								}
								
								$fileSize = $all_files['size'][$i];
								$maxFileSize = absint( $max_size ) * 1024 * 1024;
								if( $fileSize > $maxFileSize ) {
									$response['status'] = 'failed';
									$response['msg'] = $fileName. " File size exceeds the maximum limit of ". esc_attr( $maxFileSize ) ."MB.";
									return $response;
								}
								
								$mail_attachments[$fileName] = $all_files['tmp_name'][$i];
							}
							
						}
						$this->confirmed_attachments[$field_id] = $mail_attachments;
					}
					
				}
			}
			
		}
		
		return $response;
	
	}
			
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
		
}

function pmaf_file_validation() {
	return PMAF_Animated_Forms_Validation::get_instance();
}