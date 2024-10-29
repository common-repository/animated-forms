<?php
class PMAF_Animated_Forms_Post {

	public static $instance = null;
	
	public function __construct() {
		
		// create pmaf post type
		add_action( 'init', array( $this, 'pmaf_register_cpt' ) );
		
	}
	
	public function pmaf_register_cpt(){
		$labels = array(
			'name' => esc_html__( 'Animated Forms', 'animated-forms' ),
			'all_items' => esc_html__( 'Animated Forms', 'animated-forms' ),
			'singular_name' => 'pmaf',
			'add_new' => esc_html__( 'New Animated Form', 'animated-forms' ),
			'add_new_item' => esc_html__( 'Add New Animated Form', 'animated-forms' ),
			'edit_item' => esc_html__( 'Edit Animated Form', 'animated-forms' ),
			'new_item' => esc_html__( 'New Animated Form', 'animated-forms' ),
			'view_item' => esc_html__( 'View Animated Form', 'animated-forms' ),
			'search_items' => esc_html__( 'Search Animated Forms', 'animated-forms' ),
			'not_found' => esc_html__( 'No Animated Forms found', 'animated-forms' ),
			'not_found_in_trash' => esc_html__( 'No Animated Forms found in Trash', 'animated-forms' ),
			'parent_item_colon' => esc_html__( 'Parent Animated Form:', 'animated-forms' ),
			'menu_name' => esc_html__( 'WP Auto Post', 'animated-forms' )
		);
		
		$args = array(
			'labels' => $labels,
			'hierarchical' => false,
			'description' => 'Custom Animated Forms',
			'supports' => array( 'title' ),
			'taxonomies' => array( 'options' ),
			'public' => false,
			'show_ui' => true,
			'show_in_menu' => 'pmaf',//'pmaf'/true,
			'show_in_nav_menus' => true,
		
			//'menu_position' => 3,
			'publicly_queryable' => false,
			'exclude_from_search' => true,
			'has_archive' => false,
			'query_var' => true,
			'can_export' => true,
			'rewrite' => true,
			'capability_type' => 'post',
			//'menu_icon' => esc_url( WP_AUTO_URL . 'admin/assets/images/robot-man.png' )	
		);
		
		//admin only
		$admin_caps = array( 'capabilities' => array(
			'edit_post'          => 'manage_options',
			'read_post'          => 'manage_options',
			'delete_post'        => 'manage_options',
			'edit_posts'         => 'manage_options',
			'edit_others_posts'  => 'manage_options',
			'delete_posts'       => 'manage_options',
			'publish_posts'      => 'manage_options',
			'read_private_posts' => 'manage_options'
		));
		
		
		$opt = get_option ( 'pmaf_options', array ('OPT_ADMIN_ONLY') );
		if( in_array ( 'OPT_ADMIN_ONLY', $opt ) ) {			
			$args = array_merge($args,$admin_caps);			
		}
		
		register_post_type( 'pmaf', $args );
	}
		
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
} PMAF_Animated_Forms_Post::instance();