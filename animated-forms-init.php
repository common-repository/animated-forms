<?php 

/*
* Intialize the plugin
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class PMAF_Animated_Forms_Init {
	
	private static $_instance = null;
	
	public static $pmaf_alf_options = null; //PMAF_Animated_Forms_Init::$pmaf_alf_options
	
	public function __construct() {
		
		// register table
		$this->register_table();
		
		// save option values
		add_action( 'admin_init', [ $this, 'save_option_values' ] );

		// set plugin options
		$pmaf_alf_options = pmaf_forms_data()->get_option();
		
		// set default option values
		if( empty( $pmaf_alf_options ) ) {
			$pmaf_alf_options = $this->set_default_values();
		}
		self::$pmaf_alf_options = !empty( $pmaf_alf_options ) ? $pmaf_alf_options : [];
		
		// post type register call
		$this->post_type_register();
		
		// admin ajax call
		if( is_admin() ) {
			$this->admin_ajax_call();
		}
		
		// ajax call
		$this->ajax_call();
		
		// init action
		add_action( 'init', [ $this, 'init' ] );
		
		// editor supports
		//$this->editor_supports();
		
		// admin menu
		$this->admin_menu();
		
		// widget
		//$this->reg_widget();
		
	}
	
	public static function get_option( $key ) { //PMAF_Animated_Forms_Init::get_option
		
		return isset( self::$pmaf_alf_options[$key] ) ? self::$pmaf_alf_options[$key] : '';
		
	}
	
	public function register_table() {
		
		// register table
		require_once PMAF_DIR . 'admin/class.pmaf-register-table.php';
		
	}
	
	public function post_type_register() {
		
		// post type register
		require_once PMAF_DIR . 'admin/post-type/class.pmaf-post.php';
		
	}
	
	public function admin_ajax_call() {
		
		// admin ajax functions
		require_once PMAF_DIR . "admin/class.admin-ajax-functions.php";
		
	}
	
	public function ajax_call() {
		
		// ajax call
		require_once PMAF_DIR . 'inc/class.ajax-functions.php';
		
	}
	
	public function init() {
				
		// shortcode
		require_once PMAF_DIR . 'inc/class.shortcodes.php';
		
		// preview
		$this->show_preview();
		
	}
	
	public function admin_menu() {
		
		// plugin menu
		require_once PMAF_DIR . 'admin/class.pmaf-menu.php';
		
	}
	
	public function show_preview() {
		
		// preview
		require_once PMAF_DIR . 'inc/class.pmaf-preview.php';
		
	}
	
	public function reg_widget() {
		
		// widget
		//require_once PMAF_DIR . 'inc/class-widget.php';
		//require_once PMAF_DIR . 'inc/test-widget.php';
		
	}
	
	public function editor_supports() {
		
		// support
		require_once PMAF_DIR . 'admin/supports/gutenberg/class.supporter.php';
		
	}
	
	public function save_option_values() {
		
		// check wp auto seo options save request
		if( isset( $_REQUEST['pmaf_alf_options_nonce'] ) && wp_verify_nonce( $_REQUEST['pmaf_alf_options_nonce'], 'animated-forms-save-options&^%$$' ) ) {
						
			if( isset( $_POST['pmaf_alf_options'] ) ) {
				$options = $_POST['pmaf_alf_options'];
				update_option( 'pmaf_alf_options', $options );
				
				// update action
				do_action( 'pmaf_update_options' );
				
				self::$pmaf_alf_options = $options;
			}
		}
		
	}
	
	public function set_default_values() {
		
		$options = '{"required-text":"(*)","enable-remember-me":"1","enable-register":"1","enable-forget":"1","login-username-label":"Username","login-password-label":"Password","login-btn-label":"Login","register-link-label":"Register","register-separator":"\/","forget-link-label":"Lost your password?","remember-label":"Remember me","login-submit-msg":"Verifying user info, please wait...","login-success-msg":"Successfully logged in.. Redirecting...","login-failed-msg":"Incorrect username or password. Please try again.","login-security":"pmaf-login-security","name-required":"","nickname-required":"","name-label":"Your Name","email-label":"Your Email","nick-name-label":"Nick Name","username-label":"Choose Username","password-label":"Choose Password","register-btn-label":"Register","r-back-to-login":"Back to login","register-submit-msg":"Sending user info, please wait...","forget-username":"Username or E-mail","f-back-to-login":"Back to login","forget-btn-label":"Submit","forget-submit-msg":"Verifying user info, please wait...","hide-admin-menu":""}';
		update_option( 'pmaf_alf_options', json_decode( $options, true ) );
		
		return $options;
		
	}
	
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

} PMAF_Animated_Forms_Init::get_instance();