<?php 

class PMAF_Register_Tabel {
	
	public $pmaf_db_version = '1.0';
	
	private static $_instance = null;
	
	public function __construct() {
		
		// create table when plugin regitrsation
		register_activation_hook( PMAF_FILE, [ $this, 'pmaf_install' ] );
		
		// check table version
		$this->pm_update_db_check();
		
		// create upload folder
		$this->upload_folder();
						
	}

	function pmaf_install() {
		
		global $wpdb;

		$table_name = $wpdb->prefix . 'pmaf_entries';
		
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			form_id bigint(20) NOT NULL,
			form_data longtext DEFAULT '',
			source_url varchar(255) DEFAULT '',			
			status varchar(10) DEFAULT 'unread',
			is_favourite boolean DEFAULT 0,
			ip varchar(45) DEFAULT '',
			created_at timestamp DEFAULT '0000-00-00 00:00:00' NOT NULL,
			updated_at timestamp DEFAULT '0000-00-00 00:00:00' NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		update_option( 'pmaf_db_version', $this->pmaf_db_version );
			
				
	}
		
	public function pm_update_db_check() {
		
		$pmaf_db_version = get_site_option( 'pmaf_db_version' );
		if( empty( $pmaf_db_version ) || $pmaf_db_version != $this->pmaf_db_version ) {
			$this->pmaf_install();
		}
		//$this->pmaf_install();
		
	}
	
	public function upload_folder() {
		
		$path = trailingslashit( wp_upload_dir()['basedir'] ) . 'pmaf';
		if( !is_dir( $path ) ) {
			$uploads_dir = trailingslashit( wp_upload_dir()['basedir'] ) . 'pmaf';
			wp_mkdir_p( $uploads_dir );
		}
		
	}
	
	/**
	 * Creates and returns an instance of the class
	 * @since 1.0.0
	 * @access public
	 * return object
	 */
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

} PMAF_Register_Tabel::get_instance();