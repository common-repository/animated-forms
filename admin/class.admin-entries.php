<?php

class PMAF_Animated_Forms_Admin_Entries {
		
	private static $_instance = null;
	
	public $last_qry_str = '';
		
	public function __construct() {}
	
	public function get_form_titles() {
		
		global $wpdb;
		$querystr = "SELECT $wpdb->posts.ID, $wpdb->posts.post_title FROM $wpdb->posts WHERE $wpdb->posts.post_type = 'pmaf' AND $wpdb->posts.post_status = 'publish'";
			
		//$cache_key = 'wpauto_post_title_exists_'. sanitize_title( $title );
		$cache_key = 'wpauto_post_title_exists';
		$posts = wp_cache_get( $cache_key );
		$posts = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder
			$querystr // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			
		, OBJECT );
		wp_cache_set( $cache_key, $posts );	
		
		return $posts;
		
	}
	
	public function get_entries_table( $limit = 50, $page = 1 ) {
		
		global $wpdb;
		$prefix = $wpdb->prefix;
				
		$querystr = "SELECT * FROM ". $prefix ."pmaf_entries LIMIT ". $wpdb->_real_escape( $limit ) ." OFFSET 0";
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$entries = $wpdb->get_results( 
			// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder
			$querystr // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		, OBJECT );
		
		return $entries;
		
	}
	
	public function get_entries_count() {
		
		global $wpdb;
		$prefix = $wpdb->prefix;
				
		$querystr = "SELECT COUNT(*) as total_entries FROM ". $prefix ."pmaf_entries";
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$entries = $wpdb->get_results( 
			// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder
			$querystr // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		, OBJECT );
		
		return $entries;
		
	}
	
	public function get_entries_by_filter( $form_id, $read_filt, $fav_filt, $limit = 50, $page = 1 ) {
		
		global $wpdb;
		$prefix = $wpdb->prefix;
		
		$and_stat = false; $where_stat = false;
		$querystr = "SELECT * FROM ". $prefix ."pmaf_entries";
		
		if( $form_id ) {
			$querystr .= " WHERE form_id=". $wpdb->_real_escape( $form_id );
			$and_stat = true; $where_stat = true;
		}

		if( !empty( $read_filt ) && $read_filt != 'all' ) {
			if( !$where_stat ){
				$querystr .= " WHERE";
				$where_stat = true;
			}
			if( $and_stat ) $querystr .= " AND";
			$querystr .= " status='". $wpdb->_real_escape( $read_filt ) ."'";
			$and_stat = true;
		}
		
		if( !empty( $fav_filt ) && $fav_filt != 'all' ) {
			if( !$where_stat ){
				$querystr .= " WHERE";
				$where_stat = true;
			}
			if( $and_stat ) $querystr .= " AND";
			$fav_filt = $fav_filt == 'fav' ? 1 : 0;
			$querystr .= " is_favourite=". $wpdb->_real_escape( $fav_filt );
		}
		
		$offset = $limit * ( $page - 1 );
		$querystr .= " LIMIT ". $wpdb->_real_escape( $limit ) ." OFFSET ". $wpdb->_real_escape( $offset );
		
		$this->last_qry_str = $querystr;
		//wp_send_json( $querystr );
		
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$entries = $wpdb->get_results( 
			// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder
			$querystr // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		, OBJECT );
		
		return $entries;
		
	}
	
	public function get_count_from_query() {
		
		global $wpdb;
		$querystr = $this->last_qry_str;
		$querystr = str_replace( "SELECT *", "SELECT COUNT(*) as total", $querystr );
		
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$result = $wpdb->get_results( 
			// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder
			$querystr // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		, OBJECT );
		
		return $result;
		
	}
	
	public function get_entry( $id ) {
		
		global $wpdb;
		$prefix = $wpdb->prefix;
		$querystr = "SELECT * FROM ". $prefix ."pmaf_entries WHERE id=". $wpdb->_real_escape( $id );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$entry = $wpdb->get_results( 
			// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder
			$querystr // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		, OBJECT );
		
		return $entry;
		
	}
	
	public function get_next_entry( $id ) {
		
		global $wpdb;
		$prefix = $wpdb->prefix;
		$querystr = "SELECT id FROM ". $wpdb->_real_escape( $prefix ) ."pmaf_entries WHERE id > ". $wpdb->_real_escape( $id ) ." ORDER BY id LIMIT 1";
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$entry = $wpdb->get_results( 
			// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder
			$querystr // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		, OBJECT );
		
		return $entry;
		
	}
	
	public function get_prev_entry( $id ) {
		
		global $wpdb;
		$prefix = $wpdb->prefix;
		$querystr = "SELECT id FROM ". $wpdb->_real_escape( $prefix ) ."pmaf_entries WHERE id < ". $wpdb->_real_escape( $id ) ." ORDER BY id LIMIT 1";
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$entry = $wpdb->get_results( 
			// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder
			$querystr // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		, OBJECT );
		
		return $entry;
		
	}
	
	public function update_entry_data( $id, $field, $value, $string = true ) {
		
		global $wpdb;
		$prefix = $wpdb->prefix;
		if( $string ) {
			$querystr = "UPDATE ". $wpdb->_real_escape( $prefix ) ."pmaf_entries SET ". $wpdb->_real_escape( $field ) ."='". $wpdb->_real_escape( $value ) ."' WHERE id=". $wpdb->_real_escape( $id );
		} else {
			$querystr = "UPDATE ". $wpdb->_real_escape( $prefix ) ."pmaf_entries SET ". $wpdb->_real_escape( $field ) ."=". $wpdb->_real_escape( $value ) ." WHERE id=". $wpdb->_real_escape( $id );
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->query( 
			// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder
			$querystr // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		, OBJECT );
		
	}
	
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
		
} 

function pmaf_animated_admin_entries() {
	return PMAF_Animated_Forms_Admin_Entries::get_instance();
}