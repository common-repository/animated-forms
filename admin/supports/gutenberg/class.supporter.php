<?php

class PMAF_Gutenberg {
	
	private static $_instance = null;
	
	/**
	* Sets up needed actions/filters for the plug-in to initialize.
	* @since 1.0.0
	* @access public
	* @return void
	*/
	public function __construct() {
		
		// load block
		add_action( 'enqueue_block_editor_assets', [ $this, 'load_block' ] );
		
		// register rest api
		add_action( 'rest_api_init', [ $this, 'register_api_route' ] );
		
	}
	
	public function load_block() {
		
		wp_enqueue_script( 'pmaf-gutenberg', PMAF_URL . 'admin/supports/gutenberg/assets/js/pmaf-gutenberg.js', array( 'wp-blocks', 'wp-editor' ), true );
		
		/*$l_vars = array(
			'forms' => $this->get_forms()
		);
		wp_localize_script( 'pmaf-gutenberg', 'pmaf_obj', $l_vars );*/
		
	}
	   	
	public function register_api_route() {

		/**
		 * Register route with WordPress.
		 *
		 * @see https://developer.wordpress.org/reference/functions/register_rest_route/
		 */
		register_rest_route(
			'pmaf/v1',
			'/forms/',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'protected_data_callback' ],
				'permission_callback' => [ $this, 'protected_permissions_callback' ],
			]
		);
	}
	
	public function protected_data_callback() {
		
		return $this->get_forms();
		
	}
	
	public function get_forms() {
		
		return pmaf_forms_data()->get_forms( [ 'order' => 'DESC' ] );
		
	}
	
	public function protected_permissions_callback() {

		// Restrict endpoint to only users who have the edit_posts capability.
		if ( ! current_user_can( 'edit_posts' ) ) {
			return new WP_Error( 'rest_forbidden', esc_html__( 'This route is private.', 'animated-forms' ), [ 'status' => 401 ] );
		}

		return true;
	}
	
	/**
	 * Creates and returns an instance of the class
	 * @since 2.6.8
	 * @access public
	 * return object
	 */
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
} PMAF_Gutenberg::get_instance();