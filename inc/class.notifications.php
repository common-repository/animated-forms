<?php

class PMAF_Animated_Forms_Notifications {
	
	public $form_id = null;
		
	private static $_instance = null;
			
	public function __construct() {}
	
	public function email_notification( $attachments, $smtp = false ) {
		
		// default response
		$response = [ 'status' => 'success' ];
		
		$form_id = isset( $_POST['form_id'] ) && $_POST['form_id'] ? $_POST['form_id'] : '';
		$form_settings = pmaf_forms_data()->get_form_settings( $form_id );		
		$mail_attachments = $attachments != null ? $attachments : [];
					
		$d_notifi = $form_settings['d_notifi']; 
		$form_data = $_POST;
		if( !empty( $d_notifi ) ) {
			$to_mail = $this->pre_format_to_data( $d_notifi['send_email'] );
			$subj = $d_notifi['email_subj'];
			$from_email = $this->pre_format_to_data( $d_notifi['from_email'] );
			$reply_to = $this->pre_format_to_data( $d_notifi['replay_to'] );
			$email_body = $this->pre_format_to_data( $d_notifi['email_msg'], $form_data );
			
			

			$headers = [ 'Content-Type: text/html; charset=UTF-8' ];
			$headers[] = 'From: '. $from_email;
			if( $reply_to ) {
				$headers[] = 'Reply-To: '. $reply_to;
			}
											
			wp_mail( $to_mail, $subj, $email_body, $headers, $mail_attachments );
			
		}
		
		return $response;
	
	}
	
	public function pre_format_to_data( $str, $form_data = [] ) {
		
		$result = '';
		if( !empty( $str ) && strpos( $str, '{admin_email}' ) !== false ) {
			$result = str_replace( '{admin_email}', get_bloginfo( 'admin_email' ), $str );
		} elseif( !empty( $str ) && strpos( $str, '{all_form_fields}' ) !== false ) {
			$id = $this->form_id;
			$sform_data = pmaf_forms_data()->get_form_data( $id );
			
			// remove unwanted fields from form data
			$form_data = $this->filter_data( $form_data );
			
			$form_data_out = '';
			foreach( $form_data as $key => $data ) {
				$field_id = str_replace( 'field_', '', $key );
				$f_label = $sform_data[$field_id]['label']; 
				$f_type = $sform_data[$field_id]['type']; 
				if( $f_type != 'file' ) {
					if( !empty( $data ) && is_array( $data ) ) {
						$form_data_out .= $f_label .': ';
						$option_val = '';
						foreach( $data as $d ) {
							$option_val .= $d .',';						
						}
						if( !empty( $option_val ) ) {
							$option_val = rtrim( $option_val, ',' );
						}
						$form_data_out .= $option_val .'<br>';
					} else {
						$form_data_out .= $f_label .': '. $data .'<br>';
					}
				}
			}
			$result = str_replace( '{all_form_fields}', $form_data_out, $str );
		}
		
		return $result;
		
	}
	
	public function filter_data( $data ) {
		
		unset( $data['action'] );
		unset( $data['nonce'] );
		unset( $data['form_id'] );
		
		return $data;
		
	}
		
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
		
}

function pmaf_notifications() {
	return PMAF_Animated_Forms_Notifications::get_instance();
}