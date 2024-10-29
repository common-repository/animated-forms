<?php

class PMAF_Animated_Forms_Admin_AJAX {
		
	private static $_instance = null;
	
	public $new_files_data;
		
	public function __construct() {
		
		// new form
		add_action( 'wp_ajax_pmaf_new_form', [ $this, 'pmaf_new_form' ] );
		
		// save form
		add_action( 'wp_ajax_pmaf_save_form', [ $this, 'pmaf_save_form' ] );
		
		// delete form
		add_action( 'wp_ajax_pmaf_delete_form', [ $this, 'pmaf_delete_form' ] );
		
		// export forms
		add_action( 'wp_ajax_pmaf_export_form', [ $this, 'pmaf_forms_export' ] );
		
		// form status change
		add_action( 'wp_ajax_pmaf_form_status_change', [ $this, 'pmaf_form_status_change' ] );
		
		// import forms
		add_action( 'wp_ajax_pmaf_import_form', [ $this, 'pmaf_import_form' ] );
		
		// import attachments
		add_action( 'wp_ajax_pmaf_get_attachments', [ $this, 'pmaf_get_attachments' ] );
		
		// import form templates
		add_action( 'wp_ajax_pmaf_form_templates_import', [ $this, 'pmaf_form_templates_import' ] );
		
		// import overlay templates
		add_action( 'wp_ajax_pmaf_overlay_import', [ $this, 'pmaf_overlay_import' ] );
		
		// user overlay templates
		add_action( 'wp_ajax_pmaf_use_overlay_template', [ $this, 'pmaf_use_overlay_template' ] );
		
		// import packs
		add_action( 'wp_ajax_pmaf_get_pack', [ $this, 'pmaf_get_pack' ] );
		
		// get entries by filter
		add_action( 'wp_ajax_pmaf_get_entries', [ $this, 'pmaf_get_entries' ] );
		
		// make fav
		add_action( 'wp_ajax_pmaf_make_fav', [ $this, 'pmaf_make_fav' ] );
		
		// get form inner templates
		add_action( 'wp_ajax_pmaf_fi_get_selected', [ $this, 'pmaf_fi_get_selected' ] );
				
    }
	
	public function pmaf_new_form() {
		
		check_ajax_referer( 'pmaf-new-form-(4t*^&%', 'nonce' );
		
		$args = [
			'post_type' => 'pmaf',
			'post_status' => 'publish'
		];
		$post_id = wp_insert_post( $args );
		
		wp_send_json( [ 'status' => 'success', 'redirect' => admin_url( 'admin.php?page=alf_custom_forms&pmaf='. $post_id ) ] );
		
	}
	
	public function pmaf_form_status_change() {
		
		check_ajax_referer( 'pmaf-form-status-)(*^%$', 'nonce' );
		
		$forms = isset( $_POST['f'] ) ?  $_POST['f'] : '';
		$stat = isset( $_POST['stat'] ) ?  $_POST['stat'] : '';
		if( !empty( $forms ) ) {
			foreach( $forms as $id ) {
				if ( get_post_type( $id ) == 'pmaf' ) {
					if( $stat == 'false' ) {
						update_post_meta( $id, 'pmaf_form_status', 'd' );
					} else {
						update_post_meta( $id, 'pmaf_form_status', 'e' );
					}
				}
			}
		}
		
		wp_send_json( [ 'status' => 'success' ] );
		
	}
	
	public function pmaf_delete_form() {
		
		check_ajax_referer( 'pmaf-delete-form-)(*^%$', 'nonce' );
		
		$forms = isset( $_POST['f'] ) ?  $_POST['f'] : '';
		if( !empty( $forms ) ) {
			foreach( $forms as $id ) {
				if ( get_post_type( $id ) == 'pmaf' ) {
					wp_delete_post( $id, true );
				}
			}
		}
		
		wp_send_json( [ 'status' => 'success' ] );
		
	}
	
	public function pmaf_forms_export() {
		
		check_ajax_referer( 'pmaf-export-form-&*^%#$#', 'nonce' );

		$forms = isset( $_POST['f'] ) ?  $_POST['f'] : '';
		$data = [ 'status' => 'failed' ]; 
		
		if( !empty( $forms ) ) {
			
			$forms_arr = [];
			
			$args = array(
				'post_type'			=> 'pmaf',
				'posts_per_page'	=> -1,
				'post__in'			=> $forms
			);
					
			$the_query = new WP_Query( $args );
			if ( $the_query->have_posts() ) {			
				while ( $the_query->have_posts() ) {
					
					$the_query->the_post();
					$id = get_the_ID();	
					$form_settings = pmaf_forms_data()->get_form_settings( $id );
					$form_data = pmaf_forms_data()->get_form_data( $id );
					$fields_order = pmaf_forms_data()->get_form_fields_order( $id );
					$ani_data = pmaf_forms_data()->get_form_animation_settings( $id );
					if( !empty( $ani_data ) ) {
						$images = [];
						if( isset( $ani_data['outer']['bg']['image'] ) ) {
							$images[] = $ani_data['outer']['bg']['image']['url'];
						}
					}
					$forms_arr[] = [ 'id' => $id, 'form_settings' => $form_settings, 'form_data' => $form_data, 'fields_order' => $fields_order, 'form_ani_data' => $ani_data, 'images' => $images ];
					
				}			
			}
			wp_reset_postdata();
			
			$plugin_data = get_plugin_data(PMAF_FILE);
			$data = array(
				'status' => 'success',
				'generator'	=> 'Animated Forms v' . $plugin_data['Version'],
				'date_created' => gmdate( 'Y-m-d H:i' ),
				'forms'	=> $forms_arr,
			);			
			
		}
				
		wp_send_json( $data );
		
	}
	
	public function pmaf_import_form() { 
		
		check_ajax_referer( 'pmaf-import-&*^%#$#', 'nonce' );
		
		$data = [ 'status' => 'failed' ];
		
		if( !isset( $_FILES['pmaf_import_files'] ) || !count( $_FILES['pmaf_import_files'] ) ) {
			wp_send_json( $data );
		}

		$uploads = $_FILES['pmaf_import_files'];
		
		foreach ( $uploads['tmp_name'] as $i => $import_file ) { 
			
			$ext = pathinfo( $uploads['name'][ $i ] );
			$ext = $ext['extension'];
			$mime_type = $uploads['type'][ $i ];
			
			if ( 'json' === $ext || 'application/json' === $mime_type ) { 
				
				$raw_data = file_get_contents( $import_file );
				$data = json_decode( $raw_data, true );
				if( isset( $data['forms'] ) ) {
					$forms = $data['forms'];					
					foreach ( $forms as $a_form ) { 
						$this->import_single_form( $a_form );
					}						
				} 
				
			}
			
		}
		
		wp_send_json( [ 'status' => 'success' ] );
		
	}
	
	public function import_single_form( $a_form ) { 
		
		$args = [
			'post_type' => 'pmaf',
			'post_status' => 'publish'
		];
		$post_id = wp_insert_post( $args );
		
		$a_form = $this->pmaf_single_form_assets_import( $a_form );
		
		$form_data = isset( $a_form['form_data'] ) ? $a_form['form_data'] : '';
		$order = isset( $a_form['fields_order'] ) ? $a_form['fields_order'] : '';
		$settings = isset( $a_form['form_settings'] ) ? $a_form['form_settings'] : '';		
		$ani_settings = isset( $a_form['form_ani_data'] ) ? $a_form['form_ani_data'] : '';
					
		
		update_post_meta( $post_id, 'pmaf_form_data', $form_data );
		update_post_meta( $post_id, 'pmaf_fields_order', $order );
		update_post_meta( $post_id, 'pmaf_form_settings', $settings );
		update_post_meta( $post_id, 'pmaf_form_ani_settings', $ani_settings );
		
	}
		
	public function pmaf_form_templates_import() {
		
		check_ajax_referer( 'pmaf-import-templates-&*^%#$#', 'nonce' );
		
		$data = [ 'status' => 'failed' ];

		$id = isset( $_POST['id'] ) ? $_POST['id'] : '';
		$form_name = isset( $_POST['name'] ) ? $_POST['name'] : '';
		
		if( !$id  ) {
			wp_send_json( $data );
		}
		
		if( $id == 'blank' ) {
			$args = [
				'post_title' => $form_name,
				'post_type' => 'pmaf',
				'post_status' => 'publish'
			];
			$post_id = wp_insert_post( $args );	
			$data = [ 'status' => 'success', 'redirect' => admin_url( 'admin.php?page=alf_custom_forms&pmaf='. $post_id ) ];
			wp_send_json( $data );
		} elseif( $id == 'login' ) {
			$args = [
				'post_title' => $form_name,
				'post_type' => 'pmaf',
				'post_status' => 'publish',
				'meta_input' => [
					'pmaf_login_form_stat' => 'yes',
				]
			];
			$post_id = wp_insert_post( $args );	
			$data = [ 'status' => 'success', 'redirect' => admin_url( 'admin.php?page=alf_custom_forms&pmaf='. $post_id .'&pmaf_login=1' ) ];
			wp_send_json( $data );
		} else {
					
			require_once PMAF_DIR . "admin/animated-data/class.animated-data.php";
			$args = [
				'sub'	=> 'ft',
				'id'	=> $id,
				'source' => PMAF_BASENAME,
			];
			
			$server_response = pmaf_animated_forms_data()->api_call( $args );

			if ( ( !is_wp_error( $server_response ) ) ) { 
				
				$response = $this->pmaf_images_import( $server_response );
				if( isset( $response['forms'] ) ) {
					$response['forms'][0]['form_settings']['basic']['form_name'] = $form_name;
					$settings = $response['forms'][0]['form_settings'];
					$form_data = $response['forms'][0]['form_data'];
					$order = $response['forms'][0]['fields_order'];
					$ani_settings = $response['forms'][0]['form_ani_data'];
										
					$args = [
						'post_title' => $form_name,
						'post_type' => 'pmaf',
						'post_status' => 'publish',
						'meta_input' => [
							'pmaf_form_data' => $form_data,
							'pmaf_fields_order' => $order,
							'pmaf_form_settings' => $settings,
							'pmaf_form_ani_settings' => $ani_settings
						]
					];
					$post_id = wp_insert_post( $args );	
					$data = [ 'status' => 'success', 'redirect' => admin_url( 'admin.php?page=alf_custom_forms&pmaf='. $post_id ) ];
				}
				
			}
			
		}
		
		wp_send_json( $data );
		
	}
	
	public function pmaf_images_import( $data ) { 
		
		$images = [];
		if( isset( $data['forms'][0]['images'] ) && !empty( $data['forms'][0] ) ) { 
			
			foreach( $data['forms'][0]['images'] as $img ) {
				require_once PMAF_DIR . "admin/animated-data/class.image-import.php";
				$images[$img] = pmaf_image_import_call()->fetch_remote_file( $img );
			}
			
			if( !empty( $images ) ) {
				foreach( $images as $url => $img ) {
					if( isset( $data['forms'][0]['form_ani_data']['outer']['bg']['image']['url'] ) && $url == $data['forms'][0]['form_ani_data']['outer']['bg']['image']['url'] ) {
						$data['forms'][0]['form_ani_data']['outer']['bg']['image']['id'] = $img['id'];
						$data['forms'][0]['form_ani_data']['outer']['bg']['image']['url'] = $img['url'];
					}
				}
			}					
		}
				
		return $data;
		
	}
	
	public function pmaf_single_form_assets_import( $data ) { 
				
		// images download
		$images = [];
		if( isset( $data['images'] ) && !empty( $data['images'] ) ) { 
			
			foreach( $data['images'] as $img ) {
				require_once PMAF_DIR . "admin/animated-data/class.image-import.php";
				$t = pmaf_image_import_call()->fetch_remote_file( $img );
				if( !is_wp_error( $t ) ) {
					$images[$img] = $t;
				}
			}			
			
			if( !empty( $images ) ) {
				foreach( $images as $url => $img ) {
					if( isset( $data['form_ani_data']['outer']['bg']['image']['url'] ) && $url == $data['form_ani_data']['outer']['bg']['image']['url'] ) {
						$data['form_ani_data']['outer']['bg']['image']['id'] = $img['id'];
						$data['form_ani_data']['outer']['bg']['image']['url'] = $img['url'];
					}
				}
			}	
					
		}
		
		// js files download
		$js_libraries = [];
		if( isset( $data['form_ani_data']['overlay']['js'] ) && !empty( $data['form_ani_data']['overlay']['js'] ) ) {
			$js_files = $data['form_ani_data']['overlay']['js']; 
			foreach( $js_files as $js_file ) {
				$js_path = $this->upload_files( $js_file, 'js' );
				if( $js_path ) {
					$js_libraries[] = $js_path;
				}
			}
			$data['form_ani_data']['overlay']['js'] = $js_libraries;
		}
				
		return $data;
		
	}
	
	public function pmaf_save_form(){
			
		// First check the nonce, if it fails the function will break
		check_ajax_referer( 'pmaf-save-form-()*^&%', 'nonce' );
		
		$post_id = isset( $_POST['id'] ) && !empty( $_POST['id'] ) ? absint( $_POST['id'] ) : '';
		$form_data = isset( $_POST['af'] ) && !empty( $_POST['af'] ) ? $_POST['af'] : '';
		$order = isset( $_POST['order'] ) && !empty( $_POST['order'] ) ? $_POST['order'] : '';
		$settings = isset( $_POST['settings'] ) && !empty( $_POST['settings'] ) ? $_POST['settings'] : '';
		$ani_settings = isset( $_POST['ani_settings'] ) && !empty( $_POST['ani_settings'] ) ? $_POST['ani_settings'] : '';
		
		if( $post_id && ( $form_data || $settings ) ) {			
			update_post_meta( $post_id, 'pmaf_form_data', $form_data );
			update_post_meta( $post_id, 'pmaf_fields_order', $order );
			update_post_meta( $post_id, 'pmaf_form_settings', $settings );
			update_post_meta( $post_id, 'pmaf_form_ani_settings', $ani_settings );
			
			$form_title = isset( $settings['basic'] ) && isset( $settings['basic']['form_name'] ) ? $settings['basic']['form_name'] : '';
			$form_post = [
				'post_title' => $form_title,
				'ID' => $post_id
			];
			wp_update_post( $form_post );
			
			
		} elseif( $form_data ) {
			$args = [
				'post_type' => 'pmaf',
				'post_status' => 'publish',
				'meta_input' => [
					'pmaf_form_data' => $form_data,
					'pmaf_fields_order' => $order,
					'pmaf_form_settings' => $settings,
					'pmaf_form_ani_settings' => $ani_settings
				]
			];
			$post_id = wp_insert_post( $args );			
		}
		
		wp_send_json( [ 'status' => 'success', 'post_id' => $post_id ] );
		
	}
	
	public function pmaf_get_attachments() {
		
		check_ajax_referer( 'pmaf-import-attachments-&*^%#$#', 'nonce' );
		
		$response = [ 'status' => 'failed' ];
		$form_id = isset( $_POST['id'] ) && !empty( $_POST['id'] ) ? absint( $_POST['id'] ) : '';
		$attachment = isset( $_POST['attachment'] ) && !empty( $_POST['attachment'] ) ? $_POST['attachment'] : '';
		if( $attachment ) {
			
			require_once PMAF_DIR . "admin/animated-data/class.animated-data.php";
			$args = [
				'sub'	=> 'attachment',
				'id'	=> $attachment,
				'source' => PMAF_BASENAME,
			];
			
			$server_response = pmaf_animated_forms_data()->api_call( $args );

			if( ( !is_wp_error( $server_response ) ) ) {
				$file_url = isset( $server_response['file_url'] ) ? $server_response['file_url'] : '';
				
				if( $file_url ) {
					
					require_once PMAF_DIR . "admin/animated-data/class.image-import.php";
					$attachment_data = pmaf_image_import_call()->fetch_remote_file( $file_url );
					$response = [ 'status' => 'success', 'attachment' => $attachment_data ];
				}
			}
			
		}
		
		wp_send_json( $response );
		
	}
	
	public function pmaf_fi_get_selected() {
		
		check_ajax_referer( 'pmaf-get-fi-&*^%#$#', 'nonce' );
		
		$response = [ 'status' => 'failed' ];
		$fi = isset( $_POST['fi'] ) && !empty( $_POST['fi'] ) ? $_POST['fi'] : '';

		if( !empty( $fi ) ) {
			require_once PMAF_DIR . "admin/animated-data/class.animated-data.php";
			$data = pmaf_animated_forms_data()->get_form_inner_templates( $fi );
			
			$selected_html = '<div class="pmaf-selected-fi"><strong>'. esc_html__( 'Selceted Form Inner Style', 'animated-forms' ) .'</strong><span>'. esc_html( $data['name'] ) .'</span><a href="#" class="pmaf-fi-remove"><i class="af-close"></i></a></div>';
			
			wp_send_json( [ 'status' => 'success', 'preview' => $selected_html ] );
			
		}
		
		wp_send_json( $response );
		
	}
	
	public function pmaf_overlay_import() {
		
		check_ajax_referer( 'pmaf-import-overlay-templates-&*^%#$#', 'nonce' );
		
		$response = [ 'status' => 'failed' ];
		
		
		
		$overlay = isset( $_POST['overlay'] ) && !empty( $_POST['overlay'] ) ? $_POST['overlay'] : '';
		if( $overlay ) {
			
			require_once PMAF_DIR . "admin/animated-data/class.animated-data.php";
			$args = [
				'sub'	=> 'overlay',
				'id'	=> $overlay,
				'source' => PMAF_BASENAME,
			];
			
			$server_response = pmaf_animated_forms_data()->api_call( $args );

			if ( ( !is_wp_error( $server_response ) ) ) { 
							
					$o = $server_response; 
					$overlay_html = $o['html'];
					$inner_html = isset( $o['inner_html'] ) ? $o['inner_html'] : '';
					$form_html = isset( $o['form_html'] ) ? $o['form_html'] : ''; 
					$outer_html = isset( $o['outer_html'] ) ? $o['outer_html'] : '';
					$overlay_style = isset( $o['style'] ) ? $o['style'] : '';
					
					$new_files_data = [];
					
					if( isset( $o['images'] ) && !empty( $o['images'] ) ) {
						require_once PMAF_DIR . "admin/animated-data/class.image-import.php";
						foreach( $o['images'] as $img_data ) {
							$url = $img_data['url'];
							$data = pmaf_image_import_call()->fetch_remote_file( $url );
							if( !empty( $data ) && isset( $data['id'] ) ) {
								$new_files_data['**'. $img_data['id'] .'**'] = $data['url'];
								$overlay_html = str_replace( '**'. $img_data['id'] .'**', $data['url'], $overlay_html );								
								if( $outer_html ) {
									$outer_html = str_replace( '**'. $img_data['id'] .'**', $data['url'], $outer_html );
								}
								if( $overlay_style ) { 
									$overlay_style = str_replace( '**'. $img_data['id'] .'**', $data['url'], $overlay_style );									
								}
							}
							$this->new_files_data = $new_files_data;
						}
					}
					
					if( isset( $o['audios'] ) && !empty( $o['audios'] ) ) {
						require_once PMAF_DIR . "admin/animated-data/class.image-import.php";
						foreach( $o['audios'] as $audio_data ) {
							$url = $audio_data['url'];
							$data = pmaf_image_import_call()->fetch_remote_file( $url );
							if( !empty( $data ) && isset( $data['id'] ) ) {
								$new_files_data['**'. $audio_data['id'] .'**'] = $data['url'];
							}
							$this->new_files_data = $new_files_data;
						}
					}
					
					// js files download
					$js_libraries = [];
					if( isset( $o['js'] ) && !empty( $o['js'] ) ) {
						foreach( $o['js'] as $js_file ) {
							$js_path = $this->upload_files( $js_file, 'js' );
							if( $js_path ) {
								$js_libraries[] = $js_path;
							}
						}
					}
					
					$saved = get_option( 'pmaf_overlay_animations' );
					if( !empty( $saved ) ) {
						$saved[$overlay]['html'] = $overlay_html;
						$saved[$overlay]['inner_html'] = $inner_html;
						$saved[$overlay]['form_html'] = $form_html;
						$saved[$overlay]['outer_html'] = $outer_html;
						$saved[$overlay]['style'] = $overlay_style;
						$saved[$overlay]['js'] = $js_libraries;
					} else {
						$saved = [ $overlay => [ 'html' => $overlay_html, 'inner_html' => $inner_html, 'form_html' => $form_html, 'outer_html' => $outer_html, 'js' => $js_libraries, 'style' => $overlay_style ] ];
					}
										
					update_option( 'pmaf_overlay_animations', $saved );
					
					$overlay_data = pmaf_animated_forms_data()->get_overlay_templates($overlay);
					$overlay_name = $overlay_data['name'];
					
					$selected_html = '<div class="pmaf-selected-overlay"><strong>'. esc_html__( 'Selceted Overlay', 'animated-forms' ) .'</strong><span>'. esc_html( $overlay_name ) .'</span><a href="#" class="pmaf-overlay-remove"><i class="af-close"></i></a></div>';
					
					$response = [ 'status' => 'success', 'overlay' => $overlay_html, 'inner_html' => $inner_html, 'form_html' => $form_html, 'outer_html' => $outer_html, 'js' => $js_libraries, 'preview' => $selected_html, 'name' => $overlay_name, 'style' => $overlay_style ];
			
			}
			
		}
		
		wp_send_json( $response );
		
	}
	
	public function pmaf_get_pack() {
		
		check_ajax_referer( 'pmaf-import-pack-&*^%#$#', 'nonce' );
		
		$response = [ 'status' => 'failed' ];
		
		$form_id = isset( $_POST['id'] ) && !empty( $_POST['id'] ) ? absint( $_POST['id'] ) : '';
		$bg = isset( $_POST['bg'] ) && !empty( $_POST['bg'] ) ? $_POST['bg'] : '';
		$overlay = isset( $_POST['overlay'] ) && !empty( $_POST['overlay'] ) ? $_POST['overlay'] : '';
		$fi = isset( $_POST['fi'] ) && !empty( $_POST['fi'] ) ? $_POST['fi'] : '';
		
		if( $form_id ) {
			
			require_once PMAF_DIR . "admin/animated-data/class.animated-data.php";
			
			// for bg
			if( $bg ) {				
				
				$args = [
					'sub'	=> 'attachment',
					'id'	=> $bg,
					'source' => PMAF_BASENAME,
				];
				
				$server_response = pmaf_animated_forms_data()->api_call( $args );

				if( ( !is_wp_error( $server_response ) ) ) {
					$file_url = isset( $server_response['file_url'] ) ? $server_response['file_url'] : '';
					
					if( $file_url ) {
						
						require_once PMAF_DIR . "admin/animated-data/class.image-import.php";
						$attachment_data = pmaf_image_import_call()->fetch_remote_file( $file_url );
						$response['attachment'] = $attachment_data;
					}
				}
			}
			
			// for overlay
			$overlay_name = pmaf_animated_forms_data()->get_overlay_templates($overlay)['name'];
			require_once PMAF_DIR . "admin/animated-data/class.animated-data.php";
			$args = [
				'sub'	=> 'overlay',
				'id'	=> $overlay,
				'source' => PMAF_BASENAME,
			];
			
			$server_response = pmaf_animated_forms_data()->api_call( $args );

			if ( ( !is_wp_error( $server_response ) ) ) { 
							
					$o = $server_response; 
								
					$overlay_html = $o['html'];
					$inner_html = isset( $o['inner_html'] ) ? $o['inner_html'] : '';
					$form_html = isset( $o['form_html'] ) ? $o['form_html'] : ''; 
					$outer_html = isset( $o['outer_html'] ) ? $o['outer_html'] : '';
					$overlay_style = isset( $o['style'] ) ? $o['style'] : '';
					
					$new_files_data = [];
					
					if( isset( $o['images'] ) && !empty( $o['images'] ) ) {
						require_once PMAF_DIR . "admin/animated-data/class.image-import.php";
						foreach( $o['images'] as $img_data ) {
							$url = $img_data['url'];
							$data = pmaf_image_import_call()->fetch_remote_file( $url );							
							if( !empty( $data ) && isset( $data['id'] ) ) {
								$new_files_data['**'. $img_data['id'] .'**'] = $data['url'];
								
								if( $overlay_html ) {
									$overlay_html = str_replace( '**'. $img_data['id'] .'**', $data['url'], $overlay_html );
								}								
								if( $outer_html ) {
									$outer_html = str_replace( '**'. $img_data['id'] .'**', $data['url'], $outer_html );
								}
								if( $overlay_style ) { 
									$overlay_style = str_replace( '**'. $img_data['id'] .'**', $data['url'], $overlay_style );									
								}
							}
							$this->new_files_data = $new_files_data;
						}
					}
					
					if( isset( $o['audios'] ) && !empty( $o['audios'] ) ) {
						require_once PMAF_DIR . "admin/animated-data/class.image-import.php";
						foreach( $o['audios'] as $audio_data ) {
							$url = $audio_data['url'];
							$data = pmaf_image_import_call()->fetch_remote_file( $url );
							if( !empty( $data ) && isset( $data['id'] ) ) {
								$new_files_data['**'. $audio_data['id'] .'**'] = $data['url'];
							}
							$this->new_files_data = $new_files_data;
						}
					}
					
					// js files download
					$js_libraries = [];
					if( isset( $o['js'] ) && !empty( $o['js'] ) ) {
						foreach( $o['js'] as $js_file ) {
							$js_path = $this->upload_files( $js_file, 'js' );
							if( $js_path ) {
								$js_libraries[] = $js_path;
							}
						}
					}
										
					$saved = get_option( 'pmaf_overlay_animations' );
					if( !empty( $saved ) ) {
						$saved[$overlay]['html'] = $overlay_html;
						$saved[$overlay]['inner_html'] = $inner_html;
						$saved[$overlay]['form_html'] = $form_html;
						$saved[$overlay]['outer_html'] = $outer_html;
						$saved[$overlay]['style'] = $overlay_style;
						$saved[$overlay]['js'] = $js_libraries;
						
					} else {
						$saved = [ $overlay => [ 'html' => $overlay_html, 'inner_html' => $inner_html, 'form_html' => $form_html, 'outer_html' => $outer_html, 'js' => $js_libraries, 'style' => $overlay_style ] ];
					}						
					update_option( 'pmaf_overlay_animations', $saved );
					
					$selected_html = '<div class="pmaf-selected-overlay"><strong>'. esc_html__( 'Selceted Overlay', 'animated-forms' ) .'</strong><span>'. esc_html( $overlay_name ) .'</span><a href="#" class="pmaf-overlay-remove"><i class="af-close"></i></a></div>';
					
					$response['overlay'] = $overlay_html;
					
					$response['inner_html'] = $inner_html;
					$response['form_html'] = $form_html;
					$response['outer_html'] = $outer_html;
					$response['js'] = $js_libraries;
					
					$response['preview'] = $selected_html;
					$response['name'] = $overlay_name;
					$response['style'] = $overlay_style;
					$response['status'] = 'success';
				
			}
			
			$response['status'] = 'success';
			if( !empty( $fi ) ) {
				require_once PMAF_DIR . "admin/animated-data/class.animated-data.php";
				$data = pmaf_animated_forms_data()->get_form_inner_templates( $fi );
				
				$fi_html = '<div class="pmaf-selected-fi"><strong>'. esc_html__( 'Selceted Form Inner Style', 'animated-forms' ) .'</strong><span>'. esc_html( $data['name'] ) .'</span><a href="#" class="pmaf-fi-remove"><i class="af-close"></i></a></div>';
				
				$response['fi'] = $fi;
				$response['fi_preview'] = $fi_html;
				
			} else {
				$response['fi'] = '';
				
			}
			
		} 
		
		wp_send_json( $response );
		
	}
	
	public function pmaf_use_overlay_template() {
		
		check_ajax_referer( 'pmaf-import-overlay-templates-&*^%#$#', 'nonce' );
		
		$response = [ 'status' => 'failed' ];
		$form_id = isset( $_POST['id'] ) && !empty( $_POST['id'] ) ? absint( $_POST['id'] ) : '';
		$overlay = isset( $_POST['overlay'] ) && !empty( $_POST['overlay'] ) ? $_POST['overlay'] : '';
		if( $form_id && $overlay ) {
			require_once PMAF_DIR . "admin/animated-data/class.animated-data.php";
			$overlay_name = pmaf_animated_forms_data()->get_overlay_templates($overlay)['name'];
			$saved = get_option( 'pmaf_overlay_animations' );
			$overlay_html = isset( $saved[$overlay] ) ? $saved[$overlay]['html'] : '';
			$style = isset( $saved[$overlay] ) ? $saved[$overlay]['style'] : '';
			
			$inner_html = isset( $saved[$overlay]['inner_html'] ) ? $saved[$overlay]['inner_html'] : '';
			$form_html = isset( $saved[$overlay]['form_html'] ) ? $saved[$overlay]['form_html'] : '';
			$outer_html = isset( $saved[$overlay]['outer_html'] ) ? $saved[$overlay]['outer_html'] : '';			
			$js_libraries = isset( $saved[$overlay]['js'] ) ? $saved[$overlay]['js'] : '';
			
			$selected_html = '<div class="pmaf-selected-overlay"><strong>'. esc_html__( 'Selceted Overlay', 'animated-forms' ) .'</strong><span>'. esc_html( $overlay_name ) .'</span><a href="#" class="pmaf-overlay-remove"><i class="af-close"></i></a></div>';
					
			$response = [ 'status' => 'success', 'overlay' => $overlay_html, 'inner_html' => $inner_html, 'form_html' => $form_html, 'outer_html' => $outer_html, 'js' => $js_libraries, 'preview' => $selected_html, 'name' => $overlay_name, 'style' => $style ];
			
		}
		
		wp_send_json( $response );
		
	}
	
	public function pmaf_get_entries() {
		
		check_ajax_referer( 'pmaf-get-entry-*&%#$^%*&(', 'nonce' );
		$form_id = isset( $_POST['form_filter'] ) && !empty( $_POST['form_filter'] ) ? sanitize_text_field( $_POST['form_filter'] ) : '';
		$read_filter = isset( $_POST['read_filter'] ) && !empty( $_POST['read_filter'] ) ? sanitize_text_field( $_POST['read_filter'] ) : '';
		$fav_filter = isset( $_POST['fav_filter'] ) && !empty( $_POST['fav_filter'] ) ? sanitize_text_field( $_POST['fav_filter'] ) : '';
		$page = isset( $_POST['page'] ) && !empty( $_POST['page'] ) ? sanitize_text_field( $_POST['page'] ) : 1;
		$limit = isset( $_POST['limit'] ) && !empty( $_POST['limit'] ) ? sanitize_text_field( $_POST['limit'] ) : 50;
		
		$status_arr = [
			'all' => esc_html__( 'All', 'animated-forms' ),
			'unread' => esc_html__( 'Unread', 'animated-forms' ),
			'read' => esc_html__( 'Read', 'animated-forms' )
		];
		
		require_once PMAF_DIR . "admin/class.admin-entries.php";
		$entries = pmaf_animated_admin_entries()->get_entries_by_filter( $form_id, $read_filter, $fav_filter, $limit, $page );
		
		require_once PMAF_DIR . 'inc/class.entries.php';
		ob_start();
		if( !empty( $entries ) ) {
			pmaf_entries()->entries_list( $entries );
		}else {			
			pmaf_entries()->no_entries_html();
		}
		$table = ob_get_clean();
		
		$total = pmaf_animated_admin_entries()->get_count_from_query();
		
		wp_send_json( [ 'table' => $table, 'total' => $total[0]->total ] );
		
	}
	
	public function upload_files( $filename, $folder = '' ) {
		
		if( empty( $filename ) ) return false;
		$sfile_url = '';
		if( filter_var( $filename, FILTER_VALIDATE_URL ) ) {
			$sfile_url = $filename;
			$ext = pathinfo( $filename, PATHINFO_EXTENSION ); // to get extension
			$filename = pathinfo( $filename, PATHINFO_FILENAME ) .'.'. $ext;
		} else {
			
			$args = [
				'sub'	=> 'js',
				'id'	=> $filename,
				'source' => PMAF_BASENAME,
			];			
			$server_response = pmaf_animated_forms_data()->api_call( $args );
			if( ( !is_wp_error( $server_response ) ) ) {
				$sfile_url = isset( $server_response['file_url'] ) ? $server_response['file_url'] : '';
			}
		}	
		
		if( empty( $sfile_url ) ) return false;
		
		$response = wp_remote_get( $sfile_url,
			array(
				'timeout'     => 120,
				'headers' => array(
					'Accept' => 'application/json',
				)
			)
		);
		
		$file_url = '';
		if ( ( !is_wp_error($response)) && (200 === wp_remote_retrieve_response_code( $response ) ) ) {
			$response_body = $response['body'];
			
			if( !empty( $response_body ) ) {
				$path = trailingslashit( wp_upload_dir()['basedir'] ) . 'pmaf';
				if( !empty( $folder ) ) {
					
					$path = $path . '/'. $folder;
					if (! is_dir($path)) {
					   mkdir( $path, 0700 );
					}
				}
				$file_path = $path  .'/'. $filename;
				if( file_exists( $file_path ) ) unlink( $file_path );
								
				// make replace string from js content
				$new_files_data = $this->new_files_data;
				if( !empty( $new_files_data ) && is_array( $new_files_data ) ) {
					foreach( $new_files_data as $key => $value ) {
						$response_body = str_replace( $key, $value, $response_body );
					}
				}
								
				$wp_filesystem = $this->pmaf_credentials();
				$wp_filesystem->put_contents( $file_path, $response_body, FS_CHMOD_FILE );
				
				// get file url
				$file_url = wp_upload_dir()['baseurl'] .'/pmaf';
				if( !empty( $folder ) ) {					
					$file_url = $file_url . '/'. $folder .'/'. $filename;
				}
				
			}
			
		}
		
		return $file_url;
	
	}
	
	public function pmaf_credentials(){
		/* you can safely run request_filesystem_credentials() without any issues and don't need to worry about passing in a URL */
		$creds = request_filesystem_credentials(site_url() . '/wp-admin/', '', false, false, array());
	
		/* initialize the API */
		if ( ! WP_Filesystem($creds) ) {
			return false;
		}
		global $wp_filesystem;
		return $wp_filesystem;
	}
	
	public function pmaf_make_fav() {
		
		check_ajax_referer( 'pmaf-get-entry-*&%#$^%*&(', 'nonce' );
		$entry_id = isset( $_POST['entry_id'] ) && !empty( $_POST['entry_id'] ) ? sanitize_text_field( $_POST['entry_id'] ) : '';
		$stat = isset( $_POST['stat'] ) && !empty( $_POST['stat'] ) ? sanitize_text_field( $_POST['stat'] ) : 0;
		
		if( $entry_id ) {
			require_once PMAF_DIR . "admin/class.admin-entries.php";
			pmaf_animated_admin_entries()->update_entry_data( $entry_id, 'is_favourite', $stat, false );
		}
		
		wp_send_json( [ 'status' => 'success' ] );
		
	}
		
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
		
} PMAF_Animated_Forms_Admin_AJAX::get_instance();