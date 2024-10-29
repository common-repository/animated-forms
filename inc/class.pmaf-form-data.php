<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class PMAF_Forms_Data {
		
	private static $_instance = null;
		
	public function __construct() {}
	
	public function get_option() { // pmaf_forms_data()->get_option();
		
		return get_option( 'pmaf_alf_options' );
		
	}
	
	public function get_form_settings( $id ) { // pmaf_forms_data()->get_form_settings( $id );
		
		return get_post_meta( $id, 'pmaf_form_settings', true );
		
	}
	
	public function get_form_animation_settings( $id ) { // pmaf_forms_data()->get_form_animation_settings( $id )
		
		return get_post_meta( $id, 'pmaf_form_ani_settings', true );
		
	}
	
	public function get_form_fields_order( $id ) { // pmaf_forms_data()->get_form_fields_order( $id )
		
		return get_post_meta( $id, 'pmaf_fields_order', true );
		
	}
	
	public function get_form_data( $id ) { // pmaf_forms_data()->get_form_data( $id )
		
		return get_post_meta( $id, 'pmaf_form_data', true );;
		
	}
	
	public function is_login_form( $id ) { // pmaf_forms_data()->is_login_form( $id )
		
		$login_form_stat = get_post_meta( $id, 'pmaf_login_form_stat', true );
		if( $login_form_stat == 'yes' ) return true;
		
		return false;
		
	}
	
	public function get_forms( $pre_args = [] ) {
		
		$args = [
			'post_type' => 'pmaf',
			'post_status' => 'publish',
			'orderby'          => 'id',
			'order'            => 'ASC',
			'no_found_rows'    => true,
			'nopaging'         => true,
			'suppress_filters' => false,
		];
		
		$args = wp_parse_args( $pre_args, $args );
		
		return get_posts( $args );
		
	}
		
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
		
} 
function pmaf_forms_data() {
	return PMAF_Forms_Data::get_instance();
}