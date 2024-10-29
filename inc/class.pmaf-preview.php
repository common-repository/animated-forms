<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class PMAF_Preview {
	
	public $form_id = null;
		
	private static $_instance = null;
		
	public function __construct() {
		
		if ( ! $this->is_preview_page() ) {
			return;
		}
		
		$this->hooks();
		
	}
	
	public function is_preview_page() {

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( !isset( $_GET['pmaf_form_preview'] ) || empty( $_GET['pmaf_form_preview'] ) ) {
			return false;
		}

		// Check for logged-in user with correct capabilities.
		if ( !is_user_logged_in() ) {
			return false;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$form_id = absint( $_GET['pmaf_form_preview'] );

		if ( get_post_type( $form_id ) != 'pmaf' ) {
			return false;
		}
		
		$this->form_id = $form_id;

		return true;
		
	}
	
	public function hooks() {

		
		add_action( 'pre_get_posts', [ $this, 'pre_get_posts' ] );
		//add_filter( 'the_title', [ $this, 'the_title' ], 100, 1 );
		add_filter( 'the_content', [ $this, 'the_content' ], 999 );
		add_filter( 'get_the_excerpt', [ $this, 'the_content' ], 999 );
		add_filter( 'home_template_hierarchy', [ $this, 'force_page_template_hierarchy' ] );
		add_filter( 'frontpage_template_hierarchy', [ $this, 'force_page_template_hierarchy' ] );
		add_filter( 'post_thumbnail_html', '__return_empty_string' );
		
	}
	
	public function pre_get_posts( $query ) {

		if ( is_admin() || ! $query->is_main_query() ) {
			return;
		}

		$query->set( 'page_id', '' );
		$query->set( 'post_type', 'pmaf' );
		$query->set( 'post__in', empty( $this->form_id ) ? [] : [ (int) $this->form_id ] );
		$query->set( 'posts_per_page', 1 );

		// The preview page reads as the home page and as an non-singular posts page, neither of which are actually the case.
		// So we hardcode the correct values for those properties in the query.
		$query->is_home     = false;
		$query->is_singular = true;
		$query->is_single   = true;
	}
	
	public function the_content() {
		
		$content = '<div class="pmaf-front-end-preview">';
		$content .= do_shortcode( '[animated_form id="' . absint( $this->form_id ) . '" preview="true"]' );
		$content .= '</div>';

		return $content;
		
	}
	
	public function force_page_template_hierarchy( $templates ) {

		return [ 'page.php', 'single.php', 'index.php' ];
	}
			
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
		
} PMAF_Preview::get_instance();