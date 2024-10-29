<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class PMAF_Animated_Forms_Admin_Menu {

	public static $instance = null;
		
	public function __construct() {
		
		// Admin Menu Page
		add_action( 'admin_menu', array( $this, 'animated_login_forms_add_menu_page' ), 99 );
		
		$opt = pmaf_forms_data()->get_option();
		if( isset( $opt['hide-admin-menu'] ) && !$opt['hide-admin-menu'] ) {
			add_action( 'admin_bar_menu', array( $this, 'pmaf_admin_bar_item' ), 99 );
		}
				
		//Plugin Links
		add_filter( 'plugin_action_links', array( $this, 'pmaf_plugin_action_links' ), 90, 2 );
		
		add_filter( 'plugin_row_meta', array( $this, 'pmaf_plugin_row_meta' ), 10, 2 );
		
		add_action( 'admin_enqueue_scripts', array( $this, 'options_page_scripts' ), 10 );
		
		add_action( 'admin_notices', array( $this, 'pmaf_pro_notice' ) );
		
	}
	
	public function pmaf_pro_notice() {
		
		$class = 'notice notice-pmaf is-dismissible';
		$message = __( 'Unlock the full potential of Animated Forms with our PRO! Loving Animated Forms? Tell us what you think and review us!  It will help us improve our product.', 'animated-froms' );
		$btns = '<a href="https://plugin.net/items/animated-forms-pro/" target="_blank" class="pmaf-filled-btn">Upgrade to Pro</a><a href="https://wordpress.org/support/plugin/animated-forms/reviews/?filter=5#new-post" target="_blank" class="pmaf-filled-btn pmaf-trans-btn">Leave a Review</a>';

		printf( '<div class="%1$s"><h5>Loving Animated Forms?</h5><p>%2$s</p><div class="pmaf-notice-btns">%3$s</div></div>', esc_attr( $class ), esc_html( $message ), $btns );
		
	}
	
	public function options_page_scripts( $hook ) {
		
		wp_enqueue_style( 'pmaf-icon', PMAF_URL . 'admin/assets/css/animated-forms-icon.css', array(), '1.0', 'all' );
		wp_enqueue_style( 'pmaf-admin-global', PMAF_URL . 'admin/assets/css/pmaf-admin-global.css', array(), '1.0', 'all' );
		
		if ( $hook == 'post-new.php' || $hook == 'post.php' || 'animated-forms_page_alf_settings' ) {
			wp_enqueue_style( 'select2', PMAF_URL . 'admin/plugin-options/assets/css/select2.min.css', array(), '4.0.13', 'all' );
			wp_enqueue_script( 'select2', PMAF_URL . 'admin/plugin-options/assets/js/select2.full.min.js', array( 'jquery' ), '4.0.13' );
			wp_enqueue_media();
			wp_enqueue_style( 'pmaf-plugin-options', PMAF_URL . 'admin/plugin-options/assets/css/theme-options.css', array(), '1.0', 'all' );
			wp_enqueue_style( 'wp-color-picker');
			wp_enqueue_script( 'wp-color-picker-alpha', PMAF_URL . 'admin/plugin-options/assets/js/wp-color-picker-alpha.min.js', array( 'jquery', 'wp-color-picker' ), '3.0.3' );
			wp_enqueue_script( 'pmaf-plugin-options', PMAF_URL . 'admin/plugin-options/assets/js/theme-options.js', array( 'jquery' ), '1.0', true );
			wp_enqueue_script( 'pmaf-ai', PMAF_URL . 'admin/assets/js/pmaf-ai.js', array( 'jquery' ), '1.0', true );
		}
		
		if( $hook == 'animated-forms_page_alf_custom_forms' || $hook == 'animated-forms_page_pmaf_new' || $hook == 'animated-forms_page_pmaf_import' ) {
			wp_enqueue_style( 'jquery-confirm', PMAF_URL . 'admin/assets/css/jquery-confirm.css', array(), '3.3.4', 'all' );
			wp_enqueue_script( 'jquery-confirm', PMAF_URL . 'admin/assets/js/jquery-confirm.js', array( 'jquery' ), '3.3.4', true );
			
			wp_enqueue_style( 'pmaf-editor', PMAF_URL . 'admin/assets/css/edit-forms.css', array(), '1.0', 'all' );
			wp_enqueue_editor();
			wp_enqueue_script( 'simple-pagination', PMAF_URL . 'admin/assets/js/simple-pagination.js', array( 'jquery' ), '1.6', true );
			wp_enqueue_script( 'pmaf-editor', PMAF_URL . 'admin/assets/js/pmaf-editor.js', [ 'jquery', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-sortable', 'wp-color-picker' ], '1.0', true );
			
			
			$pmaf_id = isset( $_GET['pmaf'] ) && !empty( $_GET['pmaf'] ) ? absint( $_GET['pmaf'] ) : '';
			$form_settings = $pmaf_id ? pmaf_forms_data()->get_form_settings( $pmaf_id ) : '';
			$form_ani_settings = $pmaf_id ? pmaf_forms_data()->get_form_animation_settings( $pmaf_id ) : '';
			$data = $pmaf_id ? pmaf_forms_data()->get_form_data( $pmaf_id ) : '';
			$fields_order = $pmaf_id ? pmaf_forms_data()->get_form_fields_order( $pmaf_id ) : '';
			if( $data && $fields_order ) {
				$reordered_data = [];
				foreach( $fields_order as $ord ) {
					$reordered_data[$ord] = $data[$ord];
				}
				$data = $reordered_data;
			}
			$pmaf_forms_args = [
				'nonce' => [
					'save_form' => wp_create_nonce( 'pmaf-save-form-()*^&%' ),
					'new_form' => wp_create_nonce( 'pmaf-new-form-(4t*^&%' ),
					'delete_form' => wp_create_nonce( 'pmaf-delete-form-)(*^%$' ),
					'status_change' => wp_create_nonce( 'pmaf-form-status-)(*^%$' ),
					'export_form' => wp_create_nonce( 'pmaf-export-form-&*^%#$#' ),
					'import_form' => wp_create_nonce( 'pmaf-import-&*^%#$#' ),
					'import_attachments' => wp_create_nonce( 'pmaf-import-attachments-&*^%#$#' ),
					'import_templates' => wp_create_nonce( 'pmaf-import-templates-&*^%#$#' ),
					'import_overlay_templates' => wp_create_nonce( 'pmaf-import-overlay-templates-&*^%#$#' ),
					'import_pack' => wp_create_nonce( 'pmaf-import-pack-&*^%#$#' ),
					'get_fi' => wp_create_nonce( 'pmaf-get-fi-&*^%#$#' ),
				],
				'strings' => [
					'simpletext' => esc_html__( 'Simple Text', 'animated-forms' ),
					'link' => esc_html__( 'Link', 'animated-forms' ),
					'phone' => esc_html__( 'Phone', 'animated-forms' ),
					'numbertext' => esc_html__( 'Numbers', 'animated-forms' ),
					'name' => esc_html__( 'Name', 'animated-forms' ),
					'paratext' => esc_html__( 'Paragraph Text', 'animated-forms' ),
					'editor' => esc_html__( 'Custom HTML', 'animated-forms' ),
					'selecttext' => esc_html__( 'Select', 'animated-forms' ),
					'radiotext' => esc_html__( 'Choices', 'animated-forms' ),
					'checkboxtext' => esc_html__( 'Checkboxes', 'animated-forms' ),
					'consent' => esc_html__( 'Consent', 'animated-forms' ),
					'imageradiotext' => esc_html__( 'Image Radio', 'animated-forms' ),
					'emailtext' => esc_html__( 'Email', 'animated-forms' ),
					'slidertext' => esc_html__( 'Number Slider', 'animated-forms' ),
					'filetext' => esc_html__( 'File Upload', 'animated-forms' ),
					'gen' => esc_html__( 'General', 'animated-forms' ),
					'attrs' => esc_html__( 'Attributes', 'animated-forms' ),
					'label' => esc_html__( 'Label', 'animated-forms' ),
					'content' => esc_html__( 'Content', 'animated-forms' ),
					'desc' => esc_html__( 'Description', 'animated-forms' ),					
					'req' => esc_html__( 'Required', 'animated-forms' ),
					'classes' => esc_html__( 'CSS Class Names', 'animated-forms' ),
					'id' => esc_html__( 'CSS ID', 'animated-forms' ),
					'val_disp' => esc_html__( 'Value Display', 'animated-forms' ),
					'req_msg' => esc_html__( 'Required Message', 'animated-forms' ),
					'opt' => esc_html__( 'Option', 'animated-forms' ),
					'md' => esc_html__( 'Make default', 'animated-forms' ),
					'am' => esc_html__( 'Add more', 'animated-forms' ),
					'remove' => esc_html__( 'Remove', 'animated-forms' ),
					'default' => esc_html__( 'Default Value', 'animated-forms' ),
					'advanced' => esc_html__( 'Advanced', 'animated-forms' ),
					'placeholder' => esc_html__( 'Placeholder', 'animated-forms' ),
					'choice' => esc_html__( 'Choice', 'animated-forms' ),
					'options' => esc_html__( 'Options', 'animated-forms' ),
					'field_name' => esc_html__( 'Field Name', 'animated-forms' ),
					'f_empty' => esc_html__( 'This field should not be empty. If it belongs empty, then field will not appear in front.', 'animated-forms' ),
					'min' => esc_html__( 'Minumum Value', 'animated-forms' ),
					'max' => esc_html__( 'Maximum Value', 'animated-forms' ),
					'step' => esc_html__( 'Step', 'animated-forms' ),
					'selected_val' => esc_html__( 'Selected Value', 'animated-forms' ),
					'accept' => esc_html__( 'Accept Formats', 'animated-forms' ),
					'file_size' => esc_html__( 'Max File Size', 'animated-forms' ),
					'file_size_desc' => esc_html__( 'Enter file size without mention MB. Example 5', 'animated-forms' ),
					'form_opt' => esc_html__( 'Form Options', 'animated-forms' ),
					'advanced' => esc_html__( 'Advanced', 'animated-forms' ),
					'model' => esc_html__( 'Model', 'animated-forms' ),
					'classic' => esc_html__( 'Classic', 'animated-forms' ),
					'modern' => esc_html__( 'Modern', 'animated-forms' ),
					'nfiles' => esc_html__( 'No.of Files', 'animated-forms' ),
					'empty_form' => sprintf( '<h4>%s</h4><p>%s</p>', esc_html__( 'You haven\'t created any fields yet. Start by adding some!' ), esc_html__( 'Choose from our diverse range of fields and begin constructing your form!' ) ),
					'file_field' => esc_html__( 'Choose or drag a file.', 'animated-forms' ),
					'file_pro' => esc_html__( 'File field available only in pro.', 'animated-forms' ),
					'get_pro' => esc_html__( 'Get Pro.', 'animated-forms' ),
				],
				'post_id' => $pmaf_id,
				'post_title' => get_the_title( $pmaf_id ),
				'form_data' => $data,
				'fields_order' => $fields_order,
				'form_settings' => $form_settings,
				'form_ani_settings' => $form_ani_settings,
				'pmaf_page' => admin_url( 'admin.php?page=alf_custom_forms' ),
				'new_form' => admin_url('/admin.php?page=pmaf_new'), //admin_url('/admin.php?page=alf_custom_forms&pmaf_new=1')
				'logo' => esc_url(  PMAF_URL . 'admin/assets/images/logo.png'),
				'pro_link' => PMAF_PRO_LINK
			];
			wp_localize_script( 'pmaf-editor', 'pmaf_obj', $pmaf_forms_args );
		}
		
		if( $hook == 'animated-forms_page_alf_entries' || ( isset( $_GET['pmaf_entry'] ) && !empty( $_GET['pmaf_entry'] ) ) ) {
			wp_enqueue_style( 'pmaf-editor', PMAF_URL . 'admin/assets/css/edit-forms.css', array(), '1.0', 'all' );
			wp_enqueue_style( 'pmaf-entries', PMAF_URL . 'admin/assets/css/pmaf-entries.css', array(), '1.0', 'all' );
			
			wp_enqueue_script( 'simple-pagination', PMAF_URL . 'admin/assets/js/simple-pagination.js', array( 'jquery' ), '1.6', true );
			wp_enqueue_script( 'pmaf-entries', PMAF_URL . 'admin/assets/js/pmaf-entries.js', array( 'jquery' ), '1.0', true );
			$pmaf_entry = isset( $_GET['pmaf_entry'] ) ? $_GET['pmaf_entry'] : '';
			
			require_once PMAF_DIR . "admin/class.admin-entries.php";
			$entry_args = array(
				'strings' => array(
					'liked' => esc_html__( 'Liked', 'animated-forms' ),
					'make_fav' => esc_html__( 'Make this favourite', 'animated-forms' )
				),
				'loader' => PMAF_URL . 'admin/assets/images/loader.gif',
				'total' => pmaf_animated_admin_entries()->get_entries_count()[0]->total_entries,
				'page_title' => sprintf( __( 'Entry %1$s - Animated Form', 'animated-forms' ), $pmaf_entry ),	
				'get_entry_nonce' => wp_create_nonce( 'pmaf-get-entry-*&%#$^%*&(' ),
			);
			wp_localize_script( 'pmaf-entries', 'animated_entry_obj', $entry_args );
			
		}
		
		if( $hook == 'animated-forms_page_alf_custom_forms' || $hook == 'animated-forms_page_pmaf_new' || $hook == 'animated-forms_page_pmaf_import' || $hook == 'animated-forms_page_alf_entries' || $hook == 'animated-forms_page_alf_settings' ) {
			add_filter( 'admin_footer_text', function(){ return sprintf( __( 'Please rate Animated Forms %1$s on %2$s to help us improve our product.' ), '&#9733;&#9733;&#9733;&#9733;&#9733;', '<a href="https://wordpress.org/support/plugin/animated-forms/reviews/?filter=5#new-post" target="_blank">WordPress.org</a>' ); } );
			add_filter( 'update_footer', function(){ return sprintf( __( 'Version %1$s' ), PMAF_Animated_Forms::$version ); }, 99 );
		}
		
		$support_pages = [
			'animated-forms_page_alf_welcome',
			'animated-forms_page_alf_settings',
			'animated-forms_page_alf_license'
		];
		
		
		
		if( in_array( $hook, $support_pages ) ) {
			wp_enqueue_style( 'pmaf-admin', PMAF_URL . 'admin/assets/css/admin-styles.css', array(), '1.0', 'all' );
		}
					
		$animated_login_forms_args = array(
			'strings' => array(
				'save_btn_txt' => esc_html__( 'Save Settings', 'animated-forms' ),
				'generating' => esc_html__( 'Generating..', 'animated-forms' )
			),
			'ai_generate_nonce' => wp_create_nonce( 'pmaf-generate-keywords-*&%#$^%*&(' ),			
		);
		wp_localize_script( 'pmaf-plugin-options', 'animated_login_forms_obj', $animated_login_forms_args );
		
	}
	
	public function pmaf_admin_bar_item( WP_Admin_Bar $admin_bar ) {
		
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		
		$admin_bar->add_menu( array(
			'id'    => 'pmaf-menu',
			'title' => esc_html__( 'Animated Forms', 'animated-forms' ),
			'href'  => admin_url('admin.php?page=alf_custom_forms'),
		) );
		
		$admin_bar->add_menu( array(
			'id'    => 'pmaf-all-forms',
			'parent' => 'pmaf-menu',
			'title' => esc_html__( 'All Forms', 'animated-forms' ),
			'href'  => admin_url('admin.php?page=alf_custom_forms'),
		) );
		
		$admin_bar->add_menu( array(
			'id'    => 'pmaf-new-form',
			'parent' => 'pmaf-menu',
			'title' => esc_html__( 'New Form', 'animated-forms' ),
			'href'  => admin_url('admin.php?page=pmaf_new'),
		) );
		
		$admin_bar->add_menu( array(
			'id'    => 'pmaf-entries',
			'parent' => 'pmaf-menu',
			'title' => esc_html__( 'Entries', 'animated-forms' ),
			'href'  => admin_url('admin.php?page=alf_entries'),
		) );
		
		$admin_bar->add_menu( array(
			'id'    => 'pmaf-settings',
			'parent' => 'pmaf-menu',
			'title' => esc_html__( 'Global Settings', 'animated-forms' ),
			'href'  => admin_url('admin.php?page=alf_settings'),
		) );
		
		$admin_bar->add_menu( array(
			'id'    => 'pmaf-ie',
			'parent' => 'pmaf-menu',
			'title' => esc_html__( 'Import/Export', 'animated-forms' ),
			'href'  => admin_url('admin.php?page=pmaf_import'),
		) );
		
		$admin_bar->add_menu( array(
			'id'    => 'pmaf-demo',
			'parent' => 'pmaf-menu',
			'title' => esc_html__( 'Demo', 'animated-forms' ),
			'href'  => esc_url( 'https://animatedforms.com/demos/' ),
		) );
		
		$admin_bar->add_menu( array(
			'id'    => 'pmaf-pro',
			'parent' => 'pmaf-menu',
			'title' => esc_html__( 'Upgrade Pro', 'animated-forms' ),
			'href'  => esc_url( PMAF_PRO_LINK ),
		) );
		
	}
	
	public function animated_login_forms_add_menu_page() {
		
		// Main menu
		add_menu_page(
			esc_html__( 'Animated Forms', 'animated-forms' ),
			esc_html__( 'Animated Forms', 'animated-forms' ),			
			'administrator',
			'animated_login_forms',
			null,
			esc_url( PMAF_URL . 'admin/assets/images/logo-icon.png' ),
			55.56
		);
		
		// Submenu - Welcome
		/*add_submenu_page(
			'animated_login_forms',
			esc_html__( 'Animated Forms Welcome', 'animated-forms' ),
			esc_html__( 'Welcome', 'animated-forms' ),
			'administrator',
			'alf_welcome',
			array( $this, 'alf_welcome_page' )
		);*/
		
		// Submenu - All Forms
		add_submenu_page(
			'animated_login_forms',
			esc_html__( 'All Forms', 'animated-forms' ),
			esc_html__( 'All Forms', 'animated-forms' ),
			'administrator',
			'alf_custom_forms',
			array( $this, 'alf_custom_forms_page' )
		);
		
		// Submenu - New Form
		add_submenu_page(
			'animated_login_forms',
			esc_html__( 'New Form', 'animated-forms' ),
			esc_html__( 'New Form', 'animated-forms' ),
			'administrator',
			'pmaf_new', //'?page=alf_custom_forms&pmaf_new=1',
			array( $this, 'alf_new_forms_page' )
		);
		
		// Submenu - Entries
		add_submenu_page(
			'animated_login_forms',
			esc_html__( 'Entries', 'animated-forms' ),
			esc_html__( 'Entries', 'animated-forms' ),
			'administrator',
			'alf_entries',
			array( $this, 'alf_entries_page' )
		);

		// Submenu - Settings
		add_submenu_page(
			'animated_login_forms',
			esc_html__( 'Animated Forms Settings', 'animated-forms' ),
			esc_html__( 'Global Settings', 'animated-forms' ),
			'administrator',
			'alf_settings',
			array( $this, 'alf_settings_page' )
		);
		
		/*
		// Submenu - Form Templates
		add_submenu_page(
			'animated_login_forms',
			esc_html__( 'Form Templates', 'animated-forms' ),
			esc_html__( 'Form Templates', 'animated-forms' ),
			'administrator',
			'?page=alf_custom_forms&pmaf_new=1',
			''
		);*/
		
		// Submenu - Import
		add_submenu_page(
			'animated_login_forms',
			esc_html__( 'Animated Forms Import', 'animated-forms' ),
			esc_html__( 'Import/Export', 'animated-forms' ),
			'administrator',
			'pmaf_import', //'?page=alf_custom_forms&pmaf_import=1',
			array( $this, 'alf_import_page' )
		);
				
		global $submenu;
		unset( $submenu['animated_login_forms'][0] ); // remove first link
		//unset( $submenu['animated_login_forms'][3] ); // remove edit link
		
		$submenu['animated_login_forms'][] = array( 'Demo', 'manage_options', 'https://animatedforms.com/demos/' );
		$submenu['animated_login_forms'][] = array( 'Upgrade Pro', 'manage_options', PMAF_PRO_LINK );
		
	}
	
	public function animated_login_forms_add_menu_link() {
		//add_menu_page('animated_login_forms', 'Google', 'read', 'https://google.com/', '', 'dashicons-text', 1);
	}
	
	public function alf_welcome_page() {
		require_once PMAF_DIR . "admin/pages/welcome.php";
	}
	
	public function alf_custom_forms_page() {
		require_once PMAF_DIR . "admin/pages/forms.php";
	}
	
	public function alf_new_forms_page() {
		require_once PMAF_DIR . "admin/pages/new-forms.php";
	}
	
	public function alf_edit_page() {
		require_once PMAF_DIR . "admin/pages/edit.php";
	}
	
	public function alf_entries_page() {
		require_once PMAF_DIR . "admin/pages/entries.php";
	}
	
	public function alf_import_page() {
		require_once PMAF_DIR . "admin/pages/import.php";
	}
	
	public function alf_settings_page() {
		require_once PMAF_DIR . "admin/pages/settings.php";
	}
	
	public function alf_license_page() {
		require_once PMAF_DIR . "admin/pages/license.php";
	}
	
	public function pmaf_plugin_action_links( $plugin_actions, $plugin_file ){		
		$new_actions = array(); 
		if( 'animated-forms/animated-forms.php' === $plugin_file ) {			
			$new_actions = [ 
				sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'admin.php?page=alf_settings' ) ), esc_html__( 'Settings', 'animated-forms' ) ),
				sprintf( '<a href="%s" class="pmaf-pro-btn">%s</a>', esc_url( PMAF_PRO_LINK ), esc_html__( 'Upgrade to Pro', 'animated-forms' ) )
			];
		}
		return array_merge( $plugin_actions, $new_actions );
	}
	
	public function pmaf_plugin_row_meta( $plugin_meta, $plugin_file ) {
		if( PMAF_BASENAME === $plugin_file ) {
			$row_meta = [
				'docs' => '<a href="https://plugin.net/" aria-label="' . esc_attr( esc_html__( 'View Animated Forms Documentation', 'animated-forms' ) ) . '" target="_blank">' . esc_html__( 'Docs & Demo', 'animated-forms' ) . '</a>',
				'ideo' => '<a href="https://plugin.net/" aria-label="' . esc_attr( esc_html__( 'View Animated Forms Support', 'animated-forms' ) ) . '" target="_blank">' . esc_html__( 'Support', 'animated-forms' ) . '</a>',
			];

			$plugin_meta = array_merge( $plugin_meta, $row_meta );
		}
		return $plugin_meta;
	}
	
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
} PMAF_Animated_Forms_Admin_Menu::instance();