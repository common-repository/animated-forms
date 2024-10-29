<?php 

/*
	Plugin Name: Animated Forms
	Plugin URI: https://wordpress.org/plugins/animated-forms
	Description: Animated Forms can create beautiful WordPress contact us form, custom, login forms or any forms with animations for an visually appealing experience.
	Version: 1.0.4
	Author: Plugin Market
	Author URI: https://plugin.net/
	Text Domain: animated-forms
	License: GPLv3 or later
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

register_activation_hook( __FILE__, 'pmaf_free_activation' );
function pmaf_free_activation() {
	// activation redirect
	add_option( 'pmaf_activation_redirect', true );
}

// Check pro status 
$pro_stat = get_option( 'pmaf_pro_status' );
if( ( !empty( $pro_stat ) && $pro_stat == 'activated' ) || ( is_admin() && isset( $_GET['action'] ) && $_GET['action'] == 'activate' && isset( $_GET['plugin'] ) && $_GET['plugin'] == 'animated-forms-pro/animated-forms-pro.php' ) || in_array( 'animated-forms-pro/animated-forms-pro.php', apply_filters('active_plugins', get_option('active_plugins') ) ) ) return;

define( 'PMAF_FILE', __FILE__ );
define( 'PMAF_DIR', plugin_dir_path( __FILE__ ) );
define( 'PMAF_URL', plugin_dir_url( __FILE__ ) );
define( 'PMAF_BASENAME', plugin_basename( __FILE__ ) );
define( 'PMAF_PRO_LINK', esc_url( 'https://plugin.net/items/animated-forms-pro/' ) );

/*
* Intialize and Sets up the plugin
*/
class PMAF_Animated_Forms {
	
	private static $_instance = null;
	
	public static $version = '1.0.4'; //PMAF_Animated_Forms::$version
	
	/**
	* Sets up needed actions/filters for the plug-in to initialize.
	* @since 1.0.0
	* @access public
	* @return void
	*/
	public function __construct() {

		// plugin loaded actions
		add_action( 'plugins_loaded', array( $this, 'setup') );
		
		// initial actions
		$this->init();
		
		add_action( 'admin_init', array( $this, 'pmaf_activation_redirect' ) );
		
	}
	
	/**
	* Installs translation text domain
	* @since 1.0.0
	* @access public
	* @return void
	*/
	public function setup() {
		//Load text domain
		$this->load_domain();
	}
	
	/**
	 * Load plugin translated strings using text domain
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function load_domain() {
		load_plugin_textdomain( 'animated-forms', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');
	}
		
	/**
	* Load required file for addons integration
	* @return void
	*/
	public function init() {
		
		// connect data
		require_once PMAF_DIR . 'inc/class.pmaf-form-data.php';
		
		// process initiate
		require_once PMAF_DIR . 'animated-forms-init.php';
		
	}
	
	public function pmaf_activation_redirect() {
		// Make sure it's the correct user
		if ( get_option( 'pmaf_activation_redirect', false ) ) {
			// Make sure we don't redirect again after this one
			delete_option( 'pmaf_activation_redirect' );
			wp_safe_redirect( admin_url( 'admin.php?page=alf_custom_forms' ) );
			exit;
		}
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

}
PMAF_Animated_Forms::get_instance();


//add_filter( 'admin_footer_text', function(){ return "testing"; } );
//add_filter( 'update_footer', function(){ return "sdfhdjfh"; }, 99 );