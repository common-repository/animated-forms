<?php

class PMAF_Animated_Forms_AJAX {
		
	private static $_instance = null;
		
	public function __construct() {
		
		// user login ajax
		add_action( 'wp_ajax_pmaf_ajax_login', [ $this, 'pmaf_ajax_login' ] );
		add_action( 'wp_ajax_nopriv_pmaf_ajax_login', [ $this, 'pmaf_ajax_login' ], 99 );
		
		// user registration ajax
		add_action( 'wp_ajax_pmaf_ajax_register', [ $this, 'pmaf_ajax_register' ] );
		add_action( 'wp_ajax_nopriv_pmaf_ajax_register', [ $this, 'pmaf_ajax_register' ] );
		
		// forget password
		add_action( 'wp_ajax_pmaf_lost_pass', [ $this, 'pmaf_lost_pass' ] );
		add_action( 'wp_ajax_nopriv_pmaf_lost_pass', [ $this, 'pmaf_lost_pass' ] );
		
		// custom form submit
		add_action( 'wp_ajax_pmaf_frontend_submit', [ $this, 'pmaf_frontend_submit' ] );
		add_action( 'wp_ajax_nopriv_pmaf_frontend_submit', [ $this, 'pmaf_frontend_submit' ] );
		
    }
	
	public function pmaf_frontend_submit() {
		
		$form_id = isset( $_POST['form_id'] ) && $_POST['form_id'] ? sanitize_text_field( $_POST['form_id'] ) : '';
		if ( empty( $form_id ) ) {
			wp_send_json_error();
		}
		
		$login_stat = isset( $_POST['pmaf_login'] ) && $_POST['pmaf_login'] == 1 ? true : false;
		$register_stat = isset( $_POST['pmaf_register'] ) && $_POST['pmaf_register'] == 1 ? true : false;
		$forget_stat = isset( $_POST['pmaf_forget'] ) && $_POST['pmaf_forget'] == 1 ? true : false;
		
		$response = [ 'status' => 'failed' ];
		
		if( $login_stat ) {
			
			$options = pmaf_forms_data()->get_option();
			$nonce = isset( $options['login-security'] ) ? $options['login-security'] : '';
			if( !empty( $nonce ) ) check_ajax_referer( $nonce, 'nonce' );
			
			$success_msg = isset( $options['login-success-msg'] ) && !empty( $options['login-success-msg'] ) ? $options['login-success-msg'] : esc_html__( 'Login successful, redirecting...', 'animated-forms' );
			$failed_msg = isset( $options['login-failed-msg'] ) && !empty( $options['login-failed-msg'] ) ? $options['login-failed-msg'] : esc_html__( 'Wrong username/password.. Try again.', 'animated-forms' );
			
			$info = array();
			$info['user_login'] = sanitize_text_field( $_POST['username'] );
			$info['user_password'] = sanitize_text_field( $_POST['password'] );
			$info['remember'] = isset( $_POST['rememberme'] ) && $_POST['rememberme'] ? true : false;
		
			$user_signon = wp_signon( $info, false );
			$response = [ 'status' => 'error' ];
			if ( is_wp_error($user_signon) ){
				$response = [ 'status' => 'failed', 'loggedin' => false, 'msg' => esc_html( $failed_msg ) ];
			} else {		
				$redirect_url = isset( $options['login-redirect'] ) ? $options['login-redirect'] : home_url( '/' );			
				$response = [ 'status' => 'success', 'loggedin' => true, 'msg' => esc_html( $success_msg ), 'redirect_url' => $redirect_url ];
			}
			
			wp_send_json( $response );
			
		} elseif( $register_stat ) {
			
			$options = pmaf_forms_data()->get_option();
			$nonce = isset( $options['login-security'] ) ? $options['login-security'] : '';
			if( !empty( $nonce ) ) check_ajax_referer( $nonce, 'nonce' );
			
			$userdata = array();
			$userdata['first_name'] = isset( $_POST['pmaf_new_name'] ) ? sanitize_text_field( $_POST['pmaf_new_name'] ) : '';
			$userdata['user_email'] = isset( $_POST['pmaf_new_email'] ) ? sanitize_email( $_POST['pmaf_new_email'] ) : '';
			$userdata['nickname'] = isset( $_POST['pmaf_new_nick_name'] ) ? sanitize_text_field( $_POST['pmaf_new_nick_name'] ) : '';
			$userdata['user_login'] = isset( $_POST['pmaf_new_email'] ) ? sanitize_email( $_POST['pmaf_new_email'] ) : '';
			$userdata['user_pass'] = isset( $_POST['pmaf_new_password'] ) ? sanitize_text_field( $_POST['pmaf_new_password'] ) : '';
			//wp_send_json( $userdata );
			$status = false;
			$msg = ''; $status = 'failed';
			
			if( empty( $userdata['user_login'] ) || empty( $userdata['user_pass'] ) ) {
				$status = 'failed';
				$msg = esc_html__( 'Username and password field should not be empty and should be valid.', 'animated-forms' );
			} elseif( is_email( $userdata['user_email'] ) && validate_username( $userdata['user_login'] ) ) {

				$user_id = wp_insert_user( $userdata ) ;
				if( !is_wp_error($user_id) ) {			
					$status = 'success';
					$msg = esc_html__( 'Registered successful, redirecting....', 'animated-forms' );
				} else {
					$status = 'failed';
					$msg = $user_id->get_error_message();
				}

			} else {
				$status = 'failed';
				$msg = esc_html__( 'Enter valid email/user name!.', 'animated-forms' );
			}
			
			$response = [ 'status' => $status, 'register' => $status, 'msg' => esc_html( $msg ) ];
			wp_send_json( $response );
			
		} elseif( $forget_stat ) {
			
			$options = pmaf_forms_data()->get_option();
			$nonce = isset( $options['login-security'] ) ? $options['login-security'] : '';
			if( !empty( $nonce ) ) check_ajax_referer( $nonce, 'nonce' );
			
			$account = isset( $_POST['user_fp_login'] ) ? sanitize_text_field( $_POST['user_fp_login'] ) : '';
		
			if( empty( $account ) ) {
				$error = esc_html__( 'Enter an username or e-mail address.', 'animated-forms' );
			} else {
				if (validate_username( $account )) {
					if( username_exists($account) ) 
						$get_by = 'login';
					else	
						$error = esc_html__( 'There is no user registered with that username.', 'animated-forms' );				
				} elseif(is_email( $account )) {
					if( email_exists($account) ) 
						$get_by = 'email';
					else	
						$error = esc_html__( 'There is no user registered with that email address.', 'animated-forms' );			
				} else
					$error = esc_html__( 'Invalid username or e-mail address.', 'animated-forms' );		
			}	
			
			if(empty ($error)) {
				
				// lets generate our new password
				$random_password = wp_generate_password();
					
				// Get user data by field and data, fields are id, slug, email and login
				$user = get_user_by( $get_by, $account );
					
				$update_user = wp_update_user( array ( 'ID' => $user->ID, 'user_pass' => $random_password ) );
					
				// if  update user return true then lets send user an email containing the new password
				if( $update_user ) {
					
					$admin_info = get_userdata(1);
					$to_emails = $user->user_email;
					$to_emails = array(
						$admin_info->user_email,
						$user->user_email
					 );
					
					$sitename = strtolower( $_SERVER['SERVER_NAME'] );
					if ( substr( $sitename, 0, 4 ) == 'www.' ) {
						$sitename = substr( $sitename, 4 );					
					}
					$from = apply_filters( 'pmaf_lost_pass_admin_mail_id', 'admin@'.$sitename );
					
					$to = $to_emails;
					$subject = 'Your new password';
					$sender = 'From: '.get_option('name').' <'.$from.'>' . "\r\n";
					
					$message = 'Your new password is: '.$random_password;
						
					$headers[] = 'MIME-Version: 1.0' . "\r\n";
					$headers[] = 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
					$headers[] = "X-Mailer: PHP \r\n";
					$headers[] = $sender;
						
					$mail = wp_mail( $to, $subject, $message, $headers );
					if( $mail ) 
						$success = esc_html__( 'Check your email address for you new password.', 'animated-forms' );
					else
						$error = esc_html__( 'System is unable to send you mail containg your new password.', 'animated-forms' );						
				} else {
					$error = esc_html__( 'Oops! Something went wrong while updaing your account.', 'animated-forms' );
				}
			}
			
			if( ! empty( $error ) )
				wp_send_json( [ 'status' => 'failed', 'forget' => 'failed', 'msg'=> $error ] );
					
			if( ! empty( $success ) )
				wp_send_json( [ 'status' => 'success', 'forget' => 'success', 'msg'=> $success ] );
			
		} else {
				
			$form_settings = pmaf_forms_data()->get_form_settings( $form_id );
			if( isset( $form_settings['security'] ) && !empty( $form_settings['security']['security_nonce'] ) ) {
				check_ajax_referer( $form_settings['security']['security_nonce'], 'nonce' );
			}
			
			do_action( 'pmaf_ajax_submit_before_processing', $form_id );
			
			// validation file connect
			require_once PMAF_DIR . 'inc/class.file-validations.php';
			$valida_response = pmaf_file_validation()->validation();
			$attachments = null;
			if( isset( $valida_response['status'] ) && $valida_response['status'] == 'success' ) {
				$attachments = pmaf_file_validation()->confirmed_attachments;
			} else {
				wp_send_json( $valida_response );
			}
			
			do_action( 'pmaf_ajax_submit_after_validation', $form_id );
			
			// form entries
			$entries_filter = apply_filters( 'pmaf_ajax_submit_entries_enable', true );
			$entries_stat = ''; 
			if( isset( $form_settings['entries'] ) && $entries_filter ) {
				$entries_settings = $form_settings['entries'];
				$entries_stat = isset( $entries_settings['enable_entries'] ) && $entries_settings['enable_entries'] == 'on' ? true : false;
				if( $entries_stat ) {
					
					// notification file connect
					require_once PMAF_DIR . 'inc/class.entries.php';
					pmaf_entries()->form_id = $form_id;
					
					// get form post data
					$posted_data = pmaf_entries()->get_form_post_data( $form_id );
					
					// upload files
					if( !empty( $attachments ) ) {
						$uploaded_files = pmaf_entries()->upload_files( $attachments );					
						if( !empty( $uploaded_files ) ) {
							$posted_data = array_merge( $posted_data, $uploaded_files );
						}
					}
										
					// make entries
					pmaf_entries()->make_entry( $posted_data );
					
				}
			}
			
			do_action( 'pmaf_ajax_submit_after_entries', $form_id );
									
			// mail notification	
			$notification_filter = apply_filters( 'pmaf_ajax_submit_mail_notifications_enable', true );
			$noti_stat = '';
			if( isset( $form_settings['notifications'] ) && $notification_filter ) {
				$noti = $form_settings['notifications'];
				$noti_stat = isset( $noti['enable_notifications'] ) && $noti['enable_notifications'] == 'on' ? true : false;
				$enable_smtp = isset( $noti['enable_smtp'] ) && $noti['enable_smtp'] == 'on' ? true : false;
				if( $noti_stat ) {
					// notification file connect
					require_once PMAF_DIR . 'inc/class.notifications.php';
					pmaf_notifications()->form_id = $form_id;
					$noti_response = pmaf_notifications()->email_notification( $attachments, $enable_smtp );
					if( !empty( $noti_response ) && $noti_response['status'] == 'failed' ) {
						wp_send_json( $noti_response );
					}
				}
			}
			
			do_action( 'pmaf_ajax_submit_after_mail_notifications', $form_id );					
					
			$confirm_msg = '';
			if( isset( $form_settings['confirmation'] ) ) {
				$confirm_msg = isset( $form_settings['confirmation']['confirm_msg'] ) ? $form_settings['confirmation']['confirm_msg'] : '';
			}
			
			$response = [
				'status' => 'success',
				'msg' => $confirm_msg
			];
			
			$response = apply_filters( 'pmaf_form_response', $response, $form_id );
			
			wp_send_json( $response );
		}
		
	}
	
	public function pmaf_ajax_login(){
			
		// First check the nonce, if it fails the function will break
		check_ajax_referer( 'pm-ajax-login-nonce', 'security' );
	
		// Nonce is checked, get the POST data and sign user on
		$info = array();
		$info['user_login'] = sanitize_text_field( $_POST['username'] );
		$info['user_password'] = sanitize_text_field( $_POST['password'] );
		$info['remember'] = isset( $_POST['rememberme'] ) && $_POST['rememberme'] ? true : false;
	
		$user_signon = wp_signon( $info, false );
		$response = [ 'status' => 'error' ];
		if ( is_wp_error($user_signon) ){
			$response = [ 'status' => 'success', 'loggedin' => false, 'message' => esc_html__( 'Wrong username or password.', 'animated-forms' ) ];
		} else {	
			$options = pmaf_forms_data()->get_option();
			$redirect_url = isset( $options['login-redirect'] ) ? $options['login-redirect'] : home_url( '/' );		
			$response = [ 'status' => 'success', 'loggedin' => true, 'message' => esc_html__( 'Login successful, redirecting...', 'animated-forms' ), 'redirect_url' => $redirect_url ];
		}
	
		wp_send_json( $response );
	}
	
	function pmaf_ajax_register(){
			
		// First check the nonce, if it fails the function will break
		check_ajax_referer( 'pm-ajax-register-nonce', 'security' );
	
		// Nonce is checked, get the POST data and sign user on
		$email = esc_attr( $_POST['email'] );
		$username = esc_attr( $_POST['username'] );
		$user_display_name = $_POST['name'];
		
		$userdata = array();
		$userdata['first_name'] = esc_attr( $user_display_name );
		$userdata['user_email'] = esc_attr( $email );
		$userdata['nickname'] = esc_attr( $_POST['nick_name'] );
		$userdata['user_login'] = esc_attr( $username );
		$userdata['user_pass'] = esc_attr( $_POST['password'] );
		
		$status = false;
		$msg = '';
		
		if( is_email( $email ) && validate_username( $username ) ) {

			$user_id = wp_insert_user( $userdata ) ;
			if( !is_wp_error($user_id) ) {			
				$status = true;
				$msg = esc_html__( 'Registered successful, redirecting....', 'animated-forms' );
			} else {
				$status = false;
				$msg = $user_id->get_error_message();
			}

		}else{
			$status = false;
			$msg = esc_html__( 'Enter valid email/user name!.', 'animated-forms' );
		}
	
		echo json_encode( array( 'register' => $status, 'message' => $msg ) );
	
		exit;
	}
	
	public function pmaf_lost_pass() {
		
		check_ajax_referer( 'pm-ajax-forgot-nonce', 'security' );
		
		global $wpdb;
		
		$account = esc_attr( $_POST['user_login'] );
		
		if( empty( $account ) ) {
			$error = esc_html__( 'Enter an username or e-mail address.', 'animated-forms' );
		} else {
			if (validate_username( $account )) {
				if( username_exists($account) ) 
					$get_by = 'login';
				else	
					$error = esc_html__( 'There is no user registered with that username.', 'animated-forms' );				
			} elseif(is_email( $account )) {
				if( email_exists($account) ) 
					$get_by = 'email';
				else	
					$error = esc_html__( 'There is no user registered with that email address.', 'animated-forms' );			
			} else
				$error = esc_html__( 'Invalid username or e-mail address.', 'animated-forms' );		
		}	
		
		if(empty ($error)) {
			// lets generate our new password
			$random_password = wp_generate_password();
	
				
			// Get user data by field and data, fields are id, slug, email and login
			$user = get_user_by( $get_by, $account );
				
			$update_user = wp_update_user( array ( 'ID' => $user->ID, 'user_pass' => $random_password ) );
				
			// if  update user return true then lets send user an email containing the new password
			if( $update_user ) {
				
				$admin_info = get_userdata(1);
				$to_emails = $user->user_email;
				$to_emails = array(
					$admin_info->user_email,
					$user->user_email
				 );
				
				$sitename = strtolower( $_SERVER['SERVER_NAME'] );
				if ( substr( $sitename, 0, 4 ) == 'www.' ) {
					$sitename = substr( $sitename, 4 );					
				}
				$from = apply_filters( 'pmaf_lost_pass_admin_mail_id', 'admin@'.$sitename );
				
				$to = $to_emails;
				$subject = 'Your new password';
				$sender = 'From: '.get_option('name').' <'.$from.'>' . "\r\n";
				
				$message = 'Your new password is: '.$random_password;
					
				$headers[] = 'MIME-Version: 1.0' . "\r\n";
				$headers[] = 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				$headers[] = "X-Mailer: PHP \r\n";
				$headers[] = $sender;
					
				$mail = wp_mail( $to, $subject, $message, $headers );
				if( $mail ) 
					$success = esc_html__( 'Check your email address for you new password.', 'animated-forms' );
				else
					$error = esc_html__( 'System is unable to send you mail containg your new password.', 'animated-forms' );						
			} else {
				$error = esc_html__( 'Oops! Something went wrong while updaing your account.', 'animated-forms' );
			}
		}
		
		if( ! empty( $error ) )
			wp_send_json( [ 'loggedin'=>false, 'message'=> $error ] );
				
		if( ! empty( $success ) )
			wp_send_json( [ 'loggedin'=>false, 'message'=> $success ] );
		
	}
	
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
		
} PMAF_Animated_Forms_AJAX::get_instance();