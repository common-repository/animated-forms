<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function pmaf_get_pages() {
	$pages = get_pages();
	$pages_list = [ '' => esc_html__( 'None', 'animated-forms' ) ];
	if( !empty( $pages ) ) {
		foreach ( $pages as $page ) {
			$pages_list[$page->ID] = $page->post_title;
		}
	}
	return $pages_list;
}

PMAF_Animated_Forms_Options::pmaf_set_sub_section( array(
	'title'      => esc_html__( 'General Settings', 'animated-forms' ),
	'id'         => 'pmaf-general-settings',
	'fields'	 => array(
		array(
			'id'			=> 'required-text',
			'type'			=> 'html',
			'default'		=> '(*)',
			'title'			=> esc_html__( 'Required Format Text', 'animated-forms' ),
			'description'	=> esc_html__( 'Place required character or html text. (HTML code allowed here)', 'animated-forms' )
		),
		/*array(
			'id'			=> 'login-text',
			'type'			=> 'html',
			'default'		=> '',
			'title'			=> esc_html__( 'Login Form Front Text', 'animated-forms' ),
			'description'	=> esc_html__( 'Write some html code to show designed html content on login page. (HTML code allowed here)', 'animated-forms' )
		),		
		array(
			'id'			=> 'form-model',
			'type'			=> 'radioimage',
			'default'		=> '1',
			'cols'			=> 5,
			'title'			=> esc_html__( 'Form Model', 'animated-forms' ),
			'description'	=> esc_html__( 'Choose form layout style.', 'animated-forms' ),
			'items'			=> [
				'default' => [ 'url' => PMAF_URL . 'admin/plugin-options/assets/images/form-1.png', 'title' => esc_html__( 'Default', 'animated-forms' ) ],
				'1' => [ 'url' => PMAF_URL . 'admin/plugin-options/assets/images/form-1.png', 'title' => esc_html__( 'Model 1', 'animated-forms' ) ],
				'2' => [ 'url' => PMAF_URL . 'admin/plugin-options/assets/images/form-1.png', 'title' => esc_html__( 'Model 2', 'animated-forms' ) ],
				'3' => [ 'url' => PMAF_URL . 'admin/plugin-options/assets/images/form-1.png', 'title' => esc_html__( 'Model 3', 'animated-forms' ) ],
				'4' => [ 'url' => PMAF_URL . 'admin/plugin-options/assets/images/form-1.png', 'title' => esc_html__( 'Model 4', 'animated-forms' ) ],
				'5' => [ 'url' => PMAF_URL . 'admin/plugin-options/assets/images/form-1.png', 'title' => esc_html__( 'Model 5', 'animated-forms' ) ],
				'6' => [ 'url' => PMAF_URL . 'admin/plugin-options/assets/images/form-1.png', 'title' => esc_html__( 'Model 6', 'animated-forms' ) ],
				'7' => [ 'url' => PMAF_URL . 'admin/plugin-options/assets/images/form-1.png', 'title' => esc_html__( 'Model 7', 'animated-forms' ) ],
				'8' => [ 'url' => PMAF_URL . 'admin/plugin-options/assets/images/form-1.png', 'title' => esc_html__( 'Model 8', 'animated-forms' ) ],
				'9' => [ 'url' => PMAF_URL . 'admin/plugin-options/assets/images/form-1.png', 'title' => esc_html__( 'Model 9', 'animated-forms' ) ],
			]
		),
		array(
			'id'			=> 'login-content',
			'type'			=> 'html',
			'default'		=> '<h1>Welcome Back!</h1><p>To keep connected with us please login with your personal info</p>',
			'title'			=> esc_html__( 'Login Form Front Content', 'animated-forms' ),
			'description'	=> esc_html__( 'Write login form page content. (HTML code allowed here)', 'animated-forms' ),
			'required'		=> array( "form-model", "=", array( '7' ) )
		),
		array(
			'id'			=> 'register-content',
			'type'			=> 'html',
			'default'		=> '<h1>Hello, Friend!</h1><p>Enter your personal details and start journey with us</p>',
			'title'			=> esc_html__( 'Register Form Front Content', 'animated-forms' ),
			'description'	=> esc_html__( 'Write register form page content. (HTML code allowed here)', 'animated-forms' ),
			'required'		=> array( "form-model", "=", array( '7' ) )
		),*/
	)
) );

PMAF_Animated_Forms_Options::pmaf_set_sub_section( array(
	'title'      => esc_html__( 'Login Form', 'animated-forms' ),
	'id'         => 'pmaf-login-form-settings',
	'fields'	 => array(
		array(
			'id'			=> 'loginform-settings',
			'type'			=> 'label',
			'title'			=> esc_html__( 'Login Form Settings', 'animated-forms' ),
			'description'	=> esc_html__( 'These are login form settings', 'animated-forms' )
		),
		array(
			'id'			=> 'enable-remember-me',
			'type'			=> 'checkbox',
			'title'			=> esc_html__( 'Enable Remember Me', 'animated-forms' ),
			'description'	=> esc_html__( 'Check this option to enable login form remember me', 'animated-forms' )
		),
		array(
			'id'			=> 'enable-register',
			'type'			=> 'checkbox',
			'title'			=> esc_html__( 'Enable Register Link', 'animated-forms' ),
			'description'	=> esc_html__( 'Check this option to enable login form registration link', 'animated-forms' )
		),
		array(
			'id'			=> 'enable-forget',
			'type'			=> 'checkbox',
			'title'			=> esc_html__( 'Enable Forget Password', 'animated-forms' ),
			'description'	=> esc_html__( 'Check this option to enable login form forget password link', 'animated-forms' )
		),
		array(
			'id'			=> 'loginform-labels',
			'type'			=> 'label',
			'title'			=> esc_html__( 'Login Form Labels', 'animated-forms' ),
			'description'	=> esc_html__( 'These are login form labels settings', 'animated-forms' )
		),
		array(
			'id'			=> 'login-username-label',
			'type'			=> 'text',
			'default'		=> esc_html__( 'Username', 'animated-forms' ),
			'title'			=> esc_html__( 'Username Label', 'animated-forms' ),
			'description'	=> esc_html__( 'Write label for username box', 'animated-forms' )
		),
		array(
			'id'			=> 'login-password-label',
			'type'			=> 'text',
			'default'		=> esc_html__( 'Password', 'animated-forms' ),
			'title'			=> esc_html__( 'Password Label', 'animated-forms' ),
			'description'	=> esc_html__( 'Write label for password box', 'animated-forms' )
		),
		array(
			'id'			=> 'login-btn-label',
			'type'			=> 'text',
			'default'		=> esc_html__( 'Login', 'animated-forms' ),
			'title'			=> esc_html__( 'Login Button Label', 'animated-forms' ),
			'description'	=> esc_html__( 'Write label for login button', 'animated-forms' )
		),
		array(
			'id'			=> 'register-link-label',
			'type'			=> 'text',
			'default'		=> esc_html__( 'Register', 'animated-forms' ),
			'title'			=> esc_html__( 'Register Link Text', 'animated-forms' ),
			'description'	=> esc_html__( 'Write label text for register link', 'animated-forms' ),
			'required'		=> array( "enable-register", "=", array( 'true' ) )
		),
		array(
			'id'			=> 'register-separator',
			'type'			=> 'html',
			'default'		=> '/',
			'title'			=> esc_html__( 'Register Link Separator', 'animated-forms' ),
			'required'		=> array( "enable-register", "=", array( 'true' ) )
		),
		array(
			'id'			=> 'forget-link-label',
			'type'			=> 'text',
			'default'		=> esc_html__( 'Lost your password?', 'animated-forms' ),
			'title'			=> esc_html__( 'Forget Password Link Text', 'animated-forms' ),
			'description'	=> esc_html__( 'Write label text for forget password link', 'animated-forms' ),
			'required'		=> array( "enable-forget", "=", array( 'true' ) )
		),
		array(
			'id'			=> 'remember-label',
			'type'			=> 'text',
			'default'		=> esc_html__( 'Remember me', 'animated-forms' ),
			'title'			=> esc_html__( 'Remember Me Label', 'animated-forms' ),
			'description'	=> esc_html__( 'Write label text for remember me', 'animated-forms' ),
			'required'		=> array( "enable-remember-me", "=", array( 'true' ) )
		),
		array(
			'id'			=> 'login-submit-msg',
			'type'			=> 'text',
			'default'		=> esc_html__( 'Verifying user info, please wait...', 'animated-forms' ),
			'title'			=> esc_html__( 'Login Submit Message', 'animated-forms' ),
			'description'	=> esc_html__( 'This message will shown while login submission', 'animated-forms' ),
		),
		array(
			'id'			=> 'login-success-msg',
			'type'			=> 'text',
			'default'		=> esc_html__( 'Successfully logged in.. Redirecting...', 'animated-forms' ),
			'title'			=> esc_html__( 'Login Success Message', 'animated-forms' ),
			'description'	=> esc_html__( 'This message will shown after login submission success', 'animated-forms' ),
		),
		array(
			'id'			=> 'login-failed-msg',
			'type'			=> 'text',
			'default'		=> esc_html__( 'Incorrect username or password. Please try again.', 'animated-forms' ),
			'title'			=> esc_html__( 'Login Failed Message', 'animated-forms' ),
			'description'	=> esc_html__( 'This message will shown after login submission success', 'animated-forms' ),
		),
		array(
			'id'			=> 'login-security',
			'type'			=> 'text',
			'default'		=> esc_html__( 'pmaf-login-security', 'animated-forms' ),
			'title'			=> esc_html__( 'Login Security Nonce', 'animated-forms' ),
			'description'	=> esc_html__( 'Enter nonce value for login form submission.', 'animated-forms' ),
		),
		array(
			'id'			=> 'login-redirect',
			'type'			=> 'text',
			'default'		=> '',
			'title'			=> esc_html__( 'Login Redirect URL', 'animated-forms' ),
			'description'	=> esc_html__( 'Enter redirect url to move after login click', 'animated-forms' ),
		),
	)
) );

PMAF_Animated_Forms_Options::pmaf_set_sub_section( array(
	'title'      => esc_html__( 'Register Form', 'animated-forms' ),
	'id'         => 'pmaf-register-form-settings',
	'fields'	 => array(
		array(
			'id'			=> 'registerform-settings',
			'type'			=> 'label',
			'title'			=> esc_html__( 'Register Form Settings', 'animated-forms' ),
			'description'	=> esc_html__( 'These are register form settings', 'animated-forms' )
		),
		array(
			'id'			=> 'name-required',
			'type'			=> 'checkbox',
			'title'			=> esc_html__( 'Enable Required for Name', 'animated-forms' ),
			'description'	=> esc_html__( 'Check this option to enable required condition for name field', 'animated-forms' )
		),
		array(
			'id'			=> 'nickname-required',
			'type'			=> 'checkbox',
			'title'			=> esc_html__( 'Enable Required for Nick Name', 'animated-forms' ),
			'description'	=> esc_html__( 'Check this option to enable required condition for nick name field', 'animated-forms' )
		),
		array(
			'id'			=> 'name-label',
			'type'			=> 'text',
			'default'		=> esc_html__( 'Your Name', 'animated-forms' ),
			'title'			=> esc_html__( 'Name Label', 'animated-forms' ),
			'description'	=> esc_html__( 'Write label for your name', 'animated-forms' )
		),
		array(
			'id'			=> 'email-label',
			'type'			=> 'text',
			'default'		=> esc_html__( 'Your Email', 'animated-forms' ),
			'title'			=> esc_html__( 'Your Email Label', 'animated-forms' ),
			'description'	=> esc_html__( 'Write label for your email', 'animated-forms' )
		),
		array(
			'id'			=> 'nick-name-label',
			'type'			=> 'text',
			'default'		=> esc_html__( 'Nick Name', 'animated-forms' ),
			'title'			=> esc_html__( 'Nick Name Label', 'animated-forms' ),
			'description'	=> esc_html__( 'Write label for nick name', 'animated-forms' )
		),
		array(
			'id'			=> 'username-label',
			'type'			=> 'text',
			'default'		=> esc_html__( 'Choose Username', 'animated-forms' ),
			'title'			=> esc_html__( 'Username Label', 'animated-forms' ),
			'description'	=> esc_html__( 'Write label for username', 'animated-forms' )
		),
		array(
			'id'			=> 'password-label',
			'type'			=> 'text',
			'default'		=> esc_html__( 'Choose Password', 'animated-forms' ),
			'title'			=> esc_html__( 'Password Label', 'animated-forms' ),
			'description'	=> esc_html__( 'Write label for password label', 'animated-forms' )
		),
		array(
			'id'			=> 'register-btn-label',
			'type'			=> 'text',
			'default'		=> esc_html__( 'Register', 'animated-forms' ),
			'title'			=> esc_html__( 'Register Button Text', 'animated-forms' ),
			'description'	=> esc_html__( 'Write label text for register button', 'animated-forms' ),
			'required'		=> array( "enable-register", "=", array( 'true' ) )
		),
		array(
			'id'			=> 'r-back-to-login',
			'type'			=> 'html',
			'default'		=> esc_html__( 'Back to login', 'animated-forms' ),
			'title'			=> esc_html__( 'Lable for Back to Login', 'animated-forms' ),
			'description'	=> esc_html__( 'Write label for back to login from register page. (HTML code allowed here)', 'animated-forms' )
		),
		array(
			'id'			=> 'register-submit-msg',
			'type'			=> 'text',
			'default'		=> esc_html__( 'Sending user info, please wait...', 'animated-forms' ),
			'title'			=> esc_html__( 'Register Submit Message', 'animated-forms' ),
			'description'	=> esc_html__( 'This message will shown while register submission', 'animated-forms' ),
		)
	)
) );

PMAF_Animated_Forms_Options::pmaf_set_sub_section( array(
	'title'      => esc_html__( 'Forget Form', 'animated-forms' ),
	'id'         => 'pmaf-forget-form-settings',
	'fields'	 => array(
		array(
			'id'			=> 'forget-username',
			'type'			=> 'text',
			'default'		=> esc_html__( 'Username or E-mail', 'animated-forms' ),
			'title'			=> esc_html__( 'Enter Forget Username Title', 'animated-forms' ),
		),
		array(
			'id'			=> 'f-back-to-login',
			'type'			=> 'html',
			'default'		=> esc_html__( 'Back to login', 'animated-forms' ),
			'title'			=> esc_html__( 'Lable for Back to Login', 'animated-forms' ),
			'description'	=> esc_html__( 'Write label for back to login from forget form page. (HTML code allowed here)', 'animated-forms' )
		),
		array(
			'id'			=> 'forget-btn-label',
			'type'			=> 'text',
			'default'		=> esc_html__( 'Submit', 'animated-forms' ),
			'title'			=> esc_html__( 'Submit Button Text', 'animated-forms' ),
			'description'	=> esc_html__( 'Write label text for submit button', 'animated-forms' ),
		),
		array(
			'id'			=> 'forget-submit-msg',
			'type'			=> 'text',
			'default'		=> esc_html__( 'Verifying user info, please wait...', 'animated-forms' ),
			'title'			=> esc_html__( 'Forget Form Submit Message', 'animated-forms' ),
			'description'	=> esc_html__( 'This message will shown while forget form submission', 'animated-forms' ),
		)
	)
) );

PMAF_Animated_Forms_Options::pmaf_set_sub_section( array(
	'title'      => esc_html__( 'Other Settings', 'animated-forms' ),
	'id'         => 'pmaf-other-settings',
	'fields'	 => array(
		array(
			'id'			=> 'hide-admin-menu',
			'type'			=> 'checkbox',
			'title'			=> esc_html__( 'Hide Admin Menu', 'animated-forms' ),
			'description'	=> esc_html__( 'Check this option to hide admin menu. Default stat will be disabled.', 'animated-forms' )
		),
	)
) );


//'required'		=> array( "sticky-wishlist", "=", array( 'true' ) )