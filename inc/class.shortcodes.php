<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class PMAF_Animated_Forms_Shortcodes {
		
	private static $_instance = null;
	
	public $current_form = '';
	
	public static $options = [];
		
	public function __construct() {
		
		self::$options = pmaf_forms_data()->get_option();
		
		//Shortcode
		if( !is_admin() ) {
			
			add_action( 'wp_enqueue_scripts', [ $this, 'af_enqueue_scripts' ] );
			
		}
		
		add_shortcode( 'animated_form', [ $this, 'animated_form_fun' ] );
		
    }
	
	public function af_enqueue_scripts() {
		
		//wp_register_style( 'pmaf-style', PMAF_URL . 'assets/css/animated-forms.css', array(), '1.0', 'all' );
		//wp_register_script( 'pmaf-script', PMAF_URL . 'assets/js/animated-forms.js', array( 'jquery' ), '1.0.0', true );		
		wp_register_style( 'pmaf-frontend-style', PMAF_URL . 'assets/css/animated-forms-frontend.css', array(), '1.0', 'all' );
		wp_register_script( 'pmaf-frontend-script', PMAF_URL . 'assets/js/animated-custom-forms.js', array( 'jquery', 'jquery-ui-core' ), '1.0.0', true );
			
		wp_localize_script( 'pmaf-script', 'pmaf_ajax_var', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'loadingmessage' => esc_html__( 'Sending user info, please wait...', 'animated-forms' ),
			'valid_email' => esc_html__( 'Please enter valid email!', 'animated-forms' ),
			'valid_login' => esc_html__( 'Please enter valid username/password!', 'animated-forms' ),
			'req_reg' => esc_html__( 'Please enter required fields values for registration!', 'animated-forms' ),
			'strings' => [
				'required' => [
					'default' => esc_html__( 'This field is required.', 'animated-forms' ),
					'email' => esc_html__( 'Please enter a valid email address.', 'animated-forms' ),
					'number' => esc_html__( 'Please enter a valid number.', 'animated-forms' ),
				]
			]
		));
		
		
		wp_localize_script( 'pmaf-frontend-script', 'pmaf_fe_ajax_var', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'strings' => [
				'required' => [
					'default' => esc_html__( 'This field is required.', 'animated-forms' ),
				]
			]
		));
		
		wp_register_style( 'pmaf-custom-forms', PMAF_URL . 'assets/css/animated-forms-custom.css', array(), '1.0', 'all' );
		
		$custom_css = apply_filters( 'pmaf_form_styles', '' );
		wp_add_inline_style( 'pmaf-frontend-style', $custom_css );
		
	}
	
	public function animated_form_direct_submit() {
		
		$response = [
			'status' => 'success'
		];
		
		$form_id = isset( $_POST['form_id'] ) && $_POST['form_id'] ? $_POST['form_id'] : '';
		if ( empty( $form_id ) ) {
			$response = [
				'status' => 'failed',
				'msg' => esc_html__( 'Form not exists', 'animated-forms' )
			];
			return $response;
		}
				
		$form_settings = pmaf_forms_data()->get_form_settings( $form_id );
		if( isset( $form_settings['security'] ) && !empty( $form_settings['security']['security_nonce'] ) ) {
			if( !wp_verify_nonce( $_POST['nonce'], $form_settings['security']['security_nonce'] ) ) {			
				$response = [
					'status' => 'failed',
					'msg' => esc_html__( 'Due to security key issue', 'animated-forms' )
				];
				return $response;
			}
		}
		
		do_action( 'pmaf_ajax_submit_before_processing', $form_id );
		
		// validation file connect
		require_once PMAF_DIR . 'inc/class.file-validations.php';
		$valida_response = pmaf_file_validation()->validation();
		$attachments = null;
		if( isset( $valida_response['status'] ) && $valida_response['status'] == 'success' ) {
			$attachments = pmaf_file_validation()->confirmed_attachments;
		} else {
			return $valida_response;
		}
		
		// form entries
		$entries_stat = ''; 
		if( isset( $form_settings['entries'] ) ) {
			$entries_settings = $form_settings['entries'];
			$entries_stat = isset( $entries_settings['enable_entries'] ) && $entries_settings['enable_entries'] == 'on' ? true : false;
			if( $entries_stat ) {
				
				// notification file connect
				require_once PMAF_DIR . 'inc/class.entries.php';
				pmaf_entries()->form_id = $form_id;
				
				// get form post data
				$posted_data = pmaf_entries()->get_form_post_data( $form_id );
				
				// upload files
				if( !empty( $attachments ) ) {
					$uploaded_files = pmaf_entries()->upload_files( $attachments );					
					if( !empty( $uploaded_files ) ) {
						$posted_data = array_merge( $posted_data, $uploaded_files );
					}
				}
									
				// make entries
				pmaf_entries()->make_entry( $posted_data );
				
			}
		}
		
		$noti_stat = '';
		if( isset( $form_settings['notifications'] ) ) {
			$noti = $form_settings['notifications'];
			$noti_stat = isset( $noti['enable_notifications'] ) && $noti['enable_notifications'] == 'on' ? true : false;
			$enable_smtp = isset( $noti['enable_smtp'] ) && $noti['enable_smtp'] == 'on' ? true : false;
			if( $noti_stat ) {
				// notification file connect
				require_once PMAF_DIR . 'inc/class.notifications.php';
				pmaf_notifications()->form_id = $form_id;				
				$noti_response = pmaf_notifications()->email_notification( $attachments, $enable_smtp );
				if( !empty( $noti_response ) && $noti_response['status'] == 'failed' ) {
					return $noti_response;
				}
			}
		}
				
		$confirm_msg = '';
		if( isset( $form_settings['confirmation'] ) ) {
			$confirm_msg = isset( $form_settings['confirmation']['confirm_msg'] ) ? $form_settings['confirmation']['confirm_msg'] : '';
		}
		
		$response = [
			'status' => 'success',
			'msg' => $confirm_msg
		];
		
		return $response;
		
	}
	
	public function pmaf_style_maker( $form_id, $key, $settings, $field ) {
		
		$custom_styles = '';
		$dimension_atts = [ 'left', 'top', 'right', 'bottom' ];
		
		$f_dim = isset( $settings[$field] ) && !empty( $settings[$field] ) ? $settings[$field] : '';
		$dim_style = '';
		if( !empty( $f_dim ) ) {
			foreach( $dimension_atts as $att ) {
				$dim_style .= isset( $f_dim[$att] ) && $f_dim[$att] != '' ? $key ."-". esc_attr( $att ) .":". esc_attr( $f_dim[$att] ) ."px;": '';
			}
		}
		
		return $dim_style;
		
	}
	
	public function pmaf_custom_form_styles( $styles ) {
		
		$form_id = $this->current_form;
		$fa_stngs = pmaf_forms_data()->get_form_animation_settings( $form_id );
		
		$input_type_classes = 'form#pmaf-form-'. esc_attr( $form_id ) .' .pmaf-field input[type="text"], form#pmaf-form-'. esc_attr( $form_id ) .' .pmaf-field input[type="password"], form#pmaf-form-'. esc_attr( $form_id ) .' .pmaf-field input[type="email"], form#pmaf-form-'. esc_attr( $form_id ) .' .pmaf-field input[type="url"], form#pmaf-form-'. esc_attr( $form_id ) .' .pmaf-field input[type="number"], form#pmaf-form-'. esc_attr( $form_id ) .' .pmaf-field select, form#pmaf-form-'. esc_attr( $form_id ) .' .pmaf-field textarea';
		
		// border styles
		$border = isset( $fa_stngs['box']['box_border'] ) && !empty( $fa_stngs['box']['box_border'] ) ? $fa_stngs['box']['box_border'] : '';
		$custom_styles = ''; $border_style = '';		
		if( isset( $border['color'] ) && !empty( $border['color'] ) ) {
			$border_style .= 'border-color:'. esc_attr( $border['color'] ) .';';
		}		
		$border_width_atts = [ 'left', 'top', 'right', 'bottom' ];
		foreach( $border_width_atts as $att ) {
			$border_style .= isset( $border[$att] ) && !empty( $border[$att] ) ? "border-". esc_attr( $att ) ."-width:". esc_attr( $border[$att] ) ."px;": '';
		}
		if( $border_style ) $custom_styles = $input_type_classes .' {border-style: solid;'. $border_style .'}';
		
		// input box styles
		$box_color = isset( $fa_stngs['box']['box_color'] ) ? $fa_stngs['box']['box_color'] : '';
		$box_bg_color = isset( $fa_stngs['box']['box_bg_color'] ) ? $fa_stngs['box']['box_bg_color'] : '';
		if( $box_color ) {
			$custom_styles .= $input_type_classes .'{color: '. esc_attr( $box_color ) .';}';
		}
		if( $box_bg_color ) {
			$custom_styles .= $input_type_classes .'{background-color: '. esc_attr( $box_bg_color ) .';}';
		}
		$box_padding = $this->pmaf_style_maker( $form_id, 'padding', $fa_stngs['box'], 'padding' );
		if( $box_padding ) {
			$custom_styles .= $input_type_classes .'{ '. $box_padding .'}';
		}
				
		// background styles
		$bg = isset( $fa_stngs['outer']['bg'] ) ? $fa_stngs['outer']['bg'] : '';
		$pcolor = isset( $bg['pcolor'] ) ? $bg['pcolor'] : '';
		$scolor = isset( $bg['scolor'] ) ? $bg['scolor'] : '';
		if( $bg && isset( $bg['image'] ) && !empty( $bg['image'] ) ) {
			$bg_img = $bg['image'];
			if( isset( $bg_img['id'] ) && !empty( $bg_img['id'] ) ) {
				$img_url = wp_get_attachment_image_url( $bg_img['id'], 'full' );
				if( $img_url ) $custom_styles .= '#pmaf-form-wrap-'. esc_attr( $form_id ) .'{background-image:url('. esc_url( $img_url ) .');background-repeat:no-repeat;background-size: cover;background-position: center center;}';
			}			
		} 
		if( !empty( $pcolor ) && !empty( $scolor ) ) {
			$custom_styles .= '#pmaf-form-wrap-'. esc_attr( $form_id ) .'{background: linear-gradient(180deg, '. esc_attr( $pcolor ) .' 0%, '. esc_attr( $scolor ) .' 100%);}';
		} elseif( !empty( $pcolor ) ) {
			$custom_styles .= '#pmaf-form-wrap-'. esc_attr( $form_id ) .'{background-color: '. esc_attr( $pcolor ) .';}';
		}
		
		// gradient filter styles
		$enable_filter = isset( $fa_stngs['outer']['enable_filter'] ) && $fa_stngs['outer']['enable_filter'] == 'on' ? true : false;
		$filter_atts = [ 'f_1', 'f_2', 'f_3', 'f_4' ];
		$gradient_atts = isset( $fa_stngs['outer']['filter'] ) ? $fa_stngs['outer']['filter'] : []; //print_r( $gradient_atts );
		$filter_colors = '';
		foreach( $filter_atts as $att ) {
			$filter_colors .= isset( $gradient_atts[$att] ) && !empty( $gradient_atts[$att] ) ? esc_attr( $gradient_atts[$att] ) ."," : '';
		}		
		if( $filter_colors ) {
			$filter_colors = rtrim( $filter_colors, "," );
			$custom_styles .= '#pmaf-form-wrap-'. esc_attr( $form_id ) .'.pmaf-bg-gradient-ani{background:linear-gradient(-45deg,'. esc_attr( $filter_colors ) .');background-size: 400% 400%;animation:15s infinite pmaf_gradient}@keyframes pmaf_gradient{0%,100%{background-position:0 50%}50%{background-position:100% 50%}}';
		} else {
			$custom_styles .= '#pmaf-form-wrap-'. esc_attr( $form_id ) .'.pmaf-bg-gradient-ani{background:linear-gradient(-45deg,#ee7752,#e73c7e,#23a6d5,#23d5ab);background-size:400% 400%;animation:15s infinite pmaf_gradient}@keyframes pmaf_gradient{0%,100%{background-position:0 50%}50%{background-position:100% 50%}}';
		}
		
		// label styles
		
		if( isset( $fa_stngs['labels'] ) && !empty( $fa_stngs['labels'] ) ) {
			
			$f_labels = $fa_stngs['labels'];
			
			// label align
			$label_align = isset( $f_labels['align'] ) && !empty( $f_labels['align'] ) ? $f_labels['align'] : 'start';
			if( $label_align ) {
				$custom_styles .= '#pmaf-form-wrap-'. esc_attr( $form_id ) .' .pmaf-field label{ display: flex; justify-content: '. $label_align .';}';
			}
			
			// label color
			$label_color = isset( $f_labels['color'] ) ? $f_labels['color'] : '';
			if( $label_color ) {
				$custom_styles .= '#pmaf-form-wrap-'. esc_attr( $form_id ) .' .pmaf-field label{color: '. esc_attr( $label_color ) .';}';
			}
			
			// label font size
			$label_fsize = isset( $f_labels['fsize'] ) && !empty( $f_labels['fsize'] ) ? $f_labels['fsize'] : '';
			if( $label_fsize ) {
				$custom_styles .= '#pmaf-form-wrap-'. esc_attr( $form_id ) .' .pmaf-field label{ font-size: '. $label_fsize .'px;}';
			}
			
			// label font weight
			$label_fweight = isset( $f_labels['fweight'] ) && !empty( $f_labels['fweight'] ) ? $f_labels['fweight'] : '';
			if( $label_fweight ) {
				$custom_styles .= '#pmaf-form-wrap-'. esc_attr( $form_id ) .' .pmaf-field label{ font-weight: '. $label_fweight .';}';
			}
			
			// label padding
			$label_padding = $this->pmaf_style_maker( $form_id, 'padding', $f_labels, 'padding' );
			if( $label_padding ) {
				$custom_styles .= '#pmaf-form-wrap-'. esc_attr( $form_id ) .' .pmaf-field label{ '. $label_padding .'}';
			}
			
			// label margin
			$label_margin = $this->pmaf_style_maker( $form_id, 'margin', $f_labels, 'margin' );
			if( $label_margin ) {
				$custom_styles .= '#pmaf-form-wrap-'. esc_attr( $form_id ) .' .pmaf-field label{ '. $label_margin .'}';
			}
			
		}
				
		if( isset( $fa_stngs['overlay'] ) && isset( $fa_stngs['overlay']['style'] ) ) {
			$custom_styles .= $fa_stngs['overlay']['style'];
		}
		
		// inner/outer padding styles		
		if( isset( $fa_stngs['form'] ) && !empty( $fa_stngs['form'] ) ) {
			$form_stng = $fa_stngs['form'];
			$padding_d_atts = [ 'left', 'top', 'right', 'bottom' ];
						
			// inner padding styles
			$fi_padding = isset( $form_stng['inner_padding'] ) && !empty( $form_stng['inner_padding'] ) ? $form_stng['inner_padding'] : '';
			$i_padding_style = '';
			foreach( $padding_d_atts as $att ) {
				$i_padding_style .= isset( $fi_padding[$att] ) && $fi_padding[$att] != '' ? "padding-". esc_attr( $att ) .":". esc_attr( $fi_padding[$att] ) ."px;": '';
			}
			if( $i_padding_style ) $custom_styles .= '#pmaf-form-wrap-'. esc_attr( $form_id ) .' .pmaf-inner form{'. $i_padding_style .'}';
			
			// outer padding styles
			$fo_padding = isset( $form_stng['outer_padding'] ) && !empty( $form_stng['outer_padding'] ) ? $form_stng['outer_padding'] : '';
			$o_padding_style = '';
			foreach( $padding_d_atts as $att ) {
				$o_padding_style .= isset( $fo_padding[$att] ) && $fo_padding[$att] != '' ? "padding-". esc_attr( $att ) .":". esc_attr( $fo_padding[$att] ) ."px;": '';
			}
			if( $o_padding_style ) $custom_styles .= '#pmaf-form-wrap-'. esc_attr( $form_id ) .' .pmaf-inner {'. $o_padding_style .'}';
			
			// border styles
			$border = isset( $form_stng['border'] ) && !empty( $form_stng['border'] ) ? $form_stng['border'] : '';
			$border_style = '';		
			if( isset( $border['color'] ) && !empty( $border['color'] ) ) {
				$border_style .= 'border-color:'. esc_attr( $border['color'] ) .';';
			}		
			$border_width_atts = [ 'left', 'top', 'right', 'bottom' ];
			foreach( $border_width_atts as $att ) {
				$border_style .= isset( $border[$att] ) && !empty( $border[$att] ) ? "border-". esc_attr( $att ) ."-width:". esc_attr( $border[$att] ) ."px;": '';
			}
			if( $border_style ) $custom_styles .= '#pmaf-form-wrap-'. esc_attr( $form_id ) .' .pmaf-inner form {'. $border_style .'}';
			
			// form align
			$form_align = isset( $form_stng['align'] ) && !empty( $form_stng['align'] ) ? $form_stng['align'] : 'center';
			if( $form_align ) {
				$custom_styles .= '#pmaf-form-wrap-'. esc_attr( $form_id ) .' .pmaf-inner{ justify-content: '. $form_align .';}';
			}
			
			// form width
			$form_width = isset( $form_stng['fwidth'] ) && !empty( $form_stng['fwidth'] ) ? $form_stng['fwidth'] : '';
			if( $form_width ) {
				$custom_styles .= '#pmaf-form-wrap-'. esc_attr( $form_id ) .' .pmaf-inner form { width: '. absint( $form_width ) .'px;}';
			}
			
		}
		
		// button styles
		if( isset( $fa_stngs['btn'] ) && !empty( $fa_stngs['btn'] ) ) {
			$btn_stng = $fa_stngs['btn'];
			if( isset( $btn_stng['color'] ) && !empty( $btn_stng['color'] ) ) {
				$custom_styles .= '#pmaf-form-wrap-'. esc_attr( $form_id ) .' .pmaf-submit{color: '. esc_attr( $btn_stng['color'] ) .';}';
			}
			if( isset( $btn_stng['bg'] ) && !empty( $btn_stng['bg'] ) ) {
				$custom_styles .= '#pmaf-form-wrap-'. esc_attr( $form_id ) .' .pmaf-submit{background-color: '. esc_attr( $btn_stng['bg'] ) .';}';
			}
			// border styles
			$border = isset( $btn_stng['border'] ) && !empty( $btn_stng['border'] ) ? $btn_stng['border'] : '';
			$border_style = '';		
			if( isset( $border['color'] ) && !empty( $border['color'] ) ) {
				$border_style .= 'border-color:'. esc_attr( $border['color'] ) .';';
			}		
			$border_width_atts = [ 'left', 'top', 'right', 'bottom' ];
			foreach( $border_width_atts as $att ) {
				$border_style .= isset( $border[$att] ) && !empty( $border[$att] ) ? "border-". esc_attr( $att ) ."-width:". esc_attr( $border[$att] ) ."px;": '';
			}
			if( $border_style ) $custom_styles .= 'form#pmaf-form-'. esc_attr( $form_id ) .' .pmaf-submit{'. $border_style .'}';

			// btn model
			$btn_model = isset( $btn_stng['model'] ) && !empty( $btn_stng['model'] ) ? $btn_stng['model'] : 'default';
			$btn_model_val = [ 'default' => '0', 'round' => '4px', 'circle' => '100px' ];
			$custom_styles .= 'form#pmaf-form-'. esc_attr( $form_id ) .' .pmaf-submit{border-radius: '. $btn_model_val[$btn_model] .';}';
			
			// btn align
			$btn_align = isset( $btn_stng['align'] ) && !empty( $btn_stng['align'] ) ? $btn_stng['align'] : 'start';
			if( $btn_align ) {
				$custom_styles .= 'form#pmaf-form-'. esc_attr( $form_id ) .' .submit-wrap{ display: flex; justify-content: '. $btn_align .';}';
			}
			
			// btn width
			$btn_width = isset( $btn_stng['width'] ) && !empty( $btn_stng['width'] ) ? $btn_stng['width'] : '';
			if( $btn_width ) {
				$custom_styles .= 'form#pmaf-form-'. esc_attr( $form_id ) .' .pmaf-submit{ width: '. absint( $btn_width ) .'px;}';
			}
			
		}
		
		if( isset( $fa_stngs['title'] ) ) {
			
			$f_title = $fa_stngs['title'];
			
			// title align
			$title_align = isset( $f_title['align'] ) && !empty( $f_title['align'] ) ? $f_title['align'] : 'start';
			if( $title_align ) {
				$custom_styles .= '#pmaf-form-wrap-'. esc_attr( $form_id ) .' .pmaf-form-title{ display: flex; justify-content: '. $title_align .';}';
			}
			
			// title color
			$title_color = isset( $f_title['color'] ) && !empty( $f_title['color'] ) ? $f_title['color'] : '';
			if( $title_color ) {
				$custom_styles .= '#pmaf-form-wrap-'. esc_attr( $form_id ) .' .pmaf-form-title{ color: '. $title_color .';}';
			}
			
			// title font size
			$title_fsize = isset( $f_title['fsize'] ) && !empty( $f_title['fsize'] ) ? $f_title['fsize'] : '';
			if( $title_fsize ) {
				$custom_styles .= '#pmaf-form-wrap-'. esc_attr( $form_id ) .' .pmaf-form-title{ font-size: '. $title_fsize .'px;}';
			}
			
			// title font weight
			$title_fweight = isset( $f_title['fweight'] ) && !empty( $f_title['fweight'] ) ? $f_title['fweight'] : '';
			if( $title_fweight ) {
				$custom_styles .= '#pmaf-form-wrap-'. esc_attr( $form_id ) .' .pmaf-form-title{ font-weight: '. $title_fweight .';}';
			}
			
			// title padding
			$title_padding = $this->pmaf_style_maker( $form_id, 'padding', $f_title, 'padding' );
			if( $title_padding ) {
				$custom_styles .= '#pmaf-form-wrap-'. esc_attr( $form_id ) .' .pmaf-form-title{ '. $title_padding .'}';
			}
			
			// title margin
			$title_margin = $this->pmaf_style_maker( $form_id, 'margin', $f_title, 'margin' );
			if( $title_margin ) {
				$custom_styles .= '#pmaf-form-wrap-'. esc_attr( $form_id ) .' .pmaf-form-title{ '. $title_margin .'}';
			}
			
			// desc align
			$desc_align = isset( $f_title['desc_align'] ) && !empty( $f_title['desc_align'] ) ? $f_title['desc_align'] : 'start';
			if( $desc_align ) {
				$custom_styles .= '#pmaf-form-wrap-'. esc_attr( $form_id ) .' .pmaf-form-title-description{ display: flex; justify-content: '. $desc_align .';}';
			}
			
			// desc color
			$title_desc_color = isset( $f_title['desc_color'] ) && !empty( $f_title['desc_color'] ) ? $f_title['desc_color'] : '';
			if( $title_desc_color ) {
				$custom_styles .= '#pmaf-form-wrap-'. esc_attr( $form_id ) .' .pmaf-form-title-description{ color: '. $title_desc_color .';}';
			}
			
			// desc font size
			$title_desc_fsize = isset( $f_title['desc_fsize'] ) && !empty( $f_title['desc_fsize'] ) ? $f_title['desc_fsize'] : '';
			if( $title_desc_fsize ) {
				$custom_styles .= '#pmaf-form-wrap-'. esc_attr( $form_id ) .' .pmaf-form-title-description{ font-size: '. $title_desc_fsize .'px;}';
			}
			
			// desc font weight
			$title_desc_fweight = isset( $f_title['desc_fweight'] ) && !empty( $f_title['desc_fweight'] ) ? $f_title['desc_fweight'] : '';
			if( $title_desc_fweight ) {
				$custom_styles .= '#pmaf-form-wrap-'. esc_attr( $form_id ) .' .pmaf-form-title-description{ font-weight: '. $title_desc_fweight .';}';
			}
			
			// desc padding
			$desc_padding = $this->pmaf_style_maker( $form_id, 'padding', $f_title, 'desc_padding' );
			if( $desc_padding ) {
				$custom_styles .= '#pmaf-form-wrap-'. esc_attr( $form_id ) .' .pmaf-form-title-description{ '. $desc_padding .'}';
			}
			
			// desc margin
			$desc_margin = $this->pmaf_style_maker( $form_id, 'margin', $f_title, 'desc_margin' );
			if( $desc_margin ) {
				$custom_styles .= '#pmaf-form-wrap-'. esc_attr( $form_id ) .' .pmaf-form-title-description{ '. $desc_margin .'}';
			}
			
		}		
		
		echo '<style id="pmaf-instant-form-styles">'. $custom_styles .'</style>';
	}
	
	public function animated_form_fun( $atts ) {
		
		wp_enqueue_style( 'pmaf-frontend-style' );
		wp_enqueue_style( 'pmaf-custom-forms' );
				
		$atts = shortcode_atts( array(
			'id' => '',
			'preview' => false
		), $atts );
		
		extract($atts);
		
		$preview = isset( $preview ) ? true : false;
		$post_status = get_post_meta( $id, 'pmaf_form_status', true );
		$post_status = !$post_status ? 'e' : $post_status;
		if( $post_status == 'd' ) return '';
				
		$form_load = isset( $_POST['action'] ) && $_POST['action'] == 'pmaf_frontend_submit' ? true : false;
		$response = [];
		if( $form_load ) {
			$response = $this->animated_form_direct_submit();
			$output = '';
			if( $response['status'] == 'success' ) {
				$output = '<div id="pmaf-alert-'. $id .'" class="pmaf-alert-sccess">'. $response['msg'] .'</div>';
				return $output;
			}
		}
		
		$output = '';
		
		if( $id ) {
			
			$this->current_form = $id;
			
			$form_ani_settings = pmaf_forms_data()->get_form_animation_settings( $id );
			if( !isset( $_GET['page'] ) || ( isset( $_GET['page'] ) && $_GET['page'] != 'alf_custom_forms' ) ) {
				if( isset( $form_ani_settings['overlay']['js'] ) && !empty( $form_ani_settings['overlay']['js'] ) ) {
					foreach( $form_ani_settings['overlay']['js'] as $js_file ) {
						//if( !is_admin() ) {
							$js_name = sanitize_title( 'PMAF '. $form_ani_settings['overlay']['name'] );
								
								$file_name = explode( "uploads/pmaf/js/", $js_file )[1];
								if( !file_exists( PMAF_DIR . '/custom-assets/'. $file_name ) ) {
									$upload_dir = wp_upload_dir();
									if( file_exists( $upload_dir['basedir'] . '/pmaf/js/'. $file_name ) ) {
										$js_content = file_get_contents( $upload_dir['basedir'] . '/pmaf/js/'. $file_name, true);
										if( $js_content ) {
											$wp_filesystem = $this->pmaf_credentials();
											$wp_filesystem->put_contents( PMAF_DIR .'custom-assets/'. $file_name, $js_content, FS_CHMOD_FILE );
										}
									}
								}
								if( file_exists( PMAF_DIR . '/custom-assets/'. $file_name ) ) {								
									wp_enqueue_script( $js_name, PMAF_URL . 'custom-assets/'. $file_name, array( 'jquery', 'jquery-ui-core' ), '1.0', true );
								}
							
						//}
					}
				}
			}			
			
			wp_enqueue_script( 'pmaf-frontend-script' );
			
			
			add_action( 'wp_footer', [ $this, 'pmaf_custom_form_styles' ], 1 );
						
			$login_form_stat = pmaf_forms_data()->is_login_form( $id );
			if( $login_form_stat ) {
				$form_ani_settings = pmaf_forms_data()->get_form_animation_settings( $id );
				
				$enable_filter = isset( $form_ani_settings['outer']['enable_filter'] ) && $form_ani_settings['outer']['enable_filter'] == 'on' ? true : false;
				$form_classes = $enable_filter ? ' pmaf-bg-gradient-ani' : '';
				
				$output .= '<div id="pmaf-form-wrap-'. esc_attr( $id ) .'" class="pmaf-form-wrap'. esc_attr( $form_classes ) .'">';
				
					$form_title_out = ''; $form_desc_output = ''; $title_outer = true; $desc_outer = true; $tit_desc_stat = false;
					if( isset( $form_ani_settings['title'] ) ) {
						
						$form_title = $form_ani_settings['title'];
						
						// form title display
						if( isset( $form_title['inner_title_opt'] ) && $form_title['inner_title_opt'] == 'on' ) $title_outer = false;
						if( isset( $form_title['enable_title'] ) && $form_title['enable_title'] == 'on' ) {
							$title_text = isset( $form_title['text'] ) && $form_title['text'] != '' ? $form_title['text'] : '';
							$title_tag = isset( $form_title['tag'] ) && $form_title['tag'] != '' ? $form_title['tag'] : 'span';
							if( $title_text ) {
								$tit_desc_stat = true;
								$form_title_out = '<'. esc_attr( $title_tag ) .' class="pmaf-form-title">'. esc_html( $title_text ) .'</'. esc_attr( $title_tag ) .'>';
							}
						}
						
						// form description display
						if( isset( $form_title['inner_desc_opt'] ) && $form_title['inner_desc_opt'] == 'on' ) $desc_outer = false;
						if( isset( $form_title['enable_title_desc'] ) && $form_title['enable_title_desc'] == 'on' ) {
							$title_description = isset( $form_title['description'] ) && $form_title['description'] != '' ? $form_title['description'] : '';
							if( $title_description ) {
								$tit_desc_stat = true;
								$form_desc_output = '<div class="pmaf-form-title-description">'. wp_kses_post( $title_description ) .'</div>';
							}
						}
						
						if( $tit_desc_stat && ( $title_outer || $desc_outer ) ) {
							$output .= '<div class="pmaf-form-title-wrap">';
							if( $title_outer ) $output .= $form_title_out;
							if( $desc_outer ) $output .= $form_desc_output;
							$output .= '</div>';
						}
							
					}
				
					if( isset( $form_ani_settings['overlay']['html'] ) && !empty( $form_ani_settings['overlay']['html'] ) ) {
						$output .= $form_ani_settings['overlay']['html'];
					}			
					$output .= '<div class="pmaf-inner">';
						$output .= '<div class="pmaf-pack-forms-wrap">';
						
						if( $tit_desc_stat && ( !$title_outer || !$desc_outer ) ) {
							$output .= '<div class="pmaf-form-title-wrap">';
							if( !$title_outer ) $output .= $form_title_out;
							if( !$desc_outer ) $output .= $form_desc_output;
							$output .= '</div>';
						}
						
						$output .= $this->pmaf_login_full_form( $id );
						$output .= '</div> <!-- .pmaf-pack-forms-wrap -->';
					$output .= '</div> <!-- .pmaf-inner -->';
				$output .= '</div> <!-- .pmaf-form-wrap -->';
			} else {
			
				$form_settings = pmaf_forms_data()->get_form_settings( $id ); 
				$form_ani_settings = pmaf_forms_data()->get_form_animation_settings( $id );
				$form_data = pmaf_forms_data()->get_form_data( $id );
				$fields_order = pmaf_forms_data()->get_form_fields_order( $id );
				$new_form_data = [];

				if( !empty( $fields_order ) ) {
					foreach( $fields_order as $field_index ) {
						$new_form_data[$field_index] = $form_data[$field_index];
					}
					$form_data = $new_form_data;
				}
				
				
				
				if( !empty( $form_data ) ) {
					
					$form_css_class = '';
					if( isset( $form_ani_settings['inner'] ) && isset( $form_ani_settings['inner']['model'] ) && !empty( $form_ani_settings['inner']['model'] ) ) {
						$form_css_class = ' pmaf-'. $form_ani_settings['inner']['model'];
					}
					
					$form_name = isset( $form_settings['basic']['form_name'] ) ? $form_settings['basic']['form_name'] : '';
					$submit_txt = isset( $form_settings['basic']['submit_txt'] ) ? $form_settings['basic']['submit_txt'] : '';
					$form_css_class .= isset( $form_settings['style']['form_css_class'] ) ? ' '. $form_settings['style']['form_css_class'] : '';
					$btn_css_class = isset( $form_settings['style']['btn_css_class'] ) ? $form_settings['style']['btn_css_class'] : '';
					$processing_txt = isset( $form_settings['basic']['processing_txt'] ) ? $form_settings['basic']['processing_txt'] : '';
					$nonce = isset( $form_settings['security']['security_nonce'] ) ? $form_settings['security']['security_nonce'] : '';
					$enable_ajax = isset( $form_settings['security']['enable_ajax'] ) ? $form_settings['security']['enable_ajax'] : '';
					
					$enable_filter = isset( $form_ani_settings['outer']['enable_filter'] ) && $form_ani_settings['outer']['enable_filter'] == 'on' ? true : false;
					$form_classes = $enable_filter ? ' pmaf-bg-gradient-ani' : '';
					
					$output .= '<div id="pmaf-form-wrap-'. esc_attr( $id ) .'" class="pmaf-form-wrap'. esc_attr( $form_classes ) .'">';
									
						$form_title_out = ''; $form_desc_output = ''; $title_outer = true; $desc_outer = true; $tit_desc_stat = false;
						if( isset( $form_ani_settings['title'] ) ) {
							
							$form_title = $form_ani_settings['title'];
							
							// form title display
							if( isset( $form_title['inner_title_opt'] ) && $form_title['inner_title_opt'] == 'on' ) $title_outer = false;
							if( isset( $form_title['enable_title'] ) && $form_title['enable_title'] == 'on' ) {
								$title_text = isset( $form_title['text'] ) && $form_title['text'] != '' ? $form_title['text'] : '';
								$title_tag = isset( $form_title['tag'] ) && $form_title['tag'] != '' ? $form_title['tag'] : 'span';
								if( $title_text ) {
									$tit_desc_stat = true;
									$form_title_out = '<'. esc_attr( $title_tag ) .' class="pmaf-form-title">'. esc_html( $title_text ) .'</'. esc_attr( $title_tag ) .'>';
								}
							}
							
							// form description display
							if( isset( $form_title['inner_desc_opt'] ) && $form_title['inner_desc_opt'] == 'on' ) $desc_outer = false;
							if( isset( $form_title['enable_title_desc'] ) && $form_title['enable_title_desc'] == 'on' ) {
								$title_description = isset( $form_title['description'] ) && $form_title['description'] != '' ? $form_title['description'] : '';
								if( $title_description ) {
									$tit_desc_stat = true;
									$form_desc_output = '<div class="pmaf-form-title-description">'. wp_kses_post( $title_description ) .'</div>';
								}
							}
							
							if( $tit_desc_stat ) {
								$output .= '<div class="pmaf-form-title-wrap">';
								if( $title_outer ) $output .= $form_title_out;
								if( $desc_outer ) $output .= $form_desc_output;
								$output .= '</div>';
							}
								
						}
						
										
					
						if( isset( $form_ani_settings['overlay']['html'] ) && !empty( $form_ani_settings['overlay']['html'] ) ) {
							$output .= $form_ani_settings['overlay']['html'];
						}
					
						$output .= '<div class="pmaf-inner">';
						
							if( isset( $form_ani_settings['overlay']['inner_html'] ) && !empty( $form_ani_settings['overlay']['inner_html'] ) ) {
								$output .= $form_ani_settings['overlay']['inner_html'];
							}	
						
							$output .= '<form id="pmaf-form-'. esc_attr( $id ) .'" action="'. get_the_permalink() .'" name="'. esc_attr( $form_name ) .'" method="post" class="pmaf-form'. esc_attr( $form_css_class ) .'" data-ajax="'. esc_attr( $enable_ajax ) .'" data-ajax-msg="'. esc_attr( $processing_txt ) .'" enctype="multipart/form-data">';
							
								if( $tit_desc_stat ) {
									$output .= '<div class="pmaf-form-title-wrap">';
									if( !$title_outer ) $output .= $form_title_out;
									if( !$desc_outer ) $output .= $form_desc_output;
									$output .= '</div>';
								}
							
								if( isset( $form_ani_settings['overlay']['form_html'] ) && !empty( $form_ani_settings['overlay']['form_html'] ) ) {
									$output .= $form_ani_settings['overlay']['form_html'];
								}	
							
								$output .= '<input type="hidden" name="action" value="pmaf_frontend_submit" />';
								$output .= '<input type="hidden" name="nonce" value="'. wp_create_nonce( $nonce ) .'" />';
								$output .= '<input type="hidden" name="form_id" value="'. esc_attr( $id ) .'" />';
								foreach( $form_data as $field_index => $field_data ) {
									$output .= self::animated_form_fields( $field_data, $form_ani_settings, $preview );
								}
								
								// button styles
								if( isset( $form_ani_settings['btn'] ) && !empty( $form_ani_settings['btn'] ) ) {
									$btn_stng = $form_ani_settings['btn'];
									$btn_css_class .= isset( $btn_stng['btnstyle'] ) ? ' pmaf-btn-'. esc_attr( $btn_stng['btnstyle'] ) : ' pmaf-btn-default';
								}
								
								$output .= '<div class="submit-wrap"><input type="submit" class="pmaf-btn pmaf-submit '. esc_attr( $btn_css_class ) .'" value="'. esc_attr( $submit_txt ) .'"  />';
								if( $enable_ajax == 'on' ) $output .= '<div class="pmaf-form-msg"></div>';
								$output .= '</div>';

								if( $form_load && $response['status'] == 'failed' ) {
									$output .= '<div id="pmaf-alert-'. $id .'" class="pmaf-alert-warning">'. $response['msg'] .'</div>';
								}
								
							$output .= '</form>';
						$output .= '</div> <!-- .pmaf-inner -->';
					$output .= '</div> <!-- .pmaf-form-wrap -->';
					
				}
			
			} // normal form
			
		}
		
		return $output;
		
	}
	
	public static function animated_form_fields( $f_data, $ani_settings, $preview = false ) {
		
		$html = '';
		$f_type = $f_data['type'];
		$req = isset( $f_data['req'] ) && $f_data['req'] == 'on' ? true : false;
		$field_classes = $req ? ' pmaf-required' : '';
		$req_msg = $req && isset( $f_data['req_msg'] ) ? $f_data['req_msg'] : '';
		$field_label = $req ? $f_data['label'] . '<span class="pmaf-req-label">*<span>' : $f_data['label'];
		$form_load = isset( $_POST['action'] ) && $_POST['action'] == 'pmaf_frontend_submit' ? true : false;
		
		$default = isset( $f_data['default'] ) ? $f_data['default'] : '';
		if( $form_load && isset( $_POST[$f_data['name']] ) ) {
			$default = $_POST[$f_data['name']];
		}
		
		$field_classes .= isset( $ani_settings['labels']['label_move'] ) && $ani_settings['labels']['label_move'] == 'on' ? ' pmaf-label-animate' : '';
		$field_classes .= isset( $ani_settings['labels']['label_gradient'] ) && $ani_settings['labels']['label_gradient'] == 'on' ? ' pmaf-hue-animate' : '';
		
		$f_id = '';
		if( isset( $f_data['atts'] ) ) {
			if( isset( $f_data['atts']['classes'] ) && !empty( $f_data['atts']['classes'] ) ) {
				$field_classes .= ' '. $f_data['atts']['classes'];
			}
			if( isset( $f_data['atts']['id'] ) && !empty( $f_data['atts']['id'] ) ) {
				$f_id = $f_data['atts']['id'];
			}
		}
				
		switch( $f_type ) {
			case "text":	
				$html = '<div class="pmaf-field pmaf-field-text'. esc_attr( $field_classes ) .'" id="'. esc_attr( $f_id ) .'" data-req-msg="'. esc_attr( $req_msg ) .'"><label>'. $field_label .'</label><input type="text" placeholder="'. $f_data['placeholder'] .'" name="'. $f_data['name'] .'" value="'. $default .'" />';
				$html .= !empty( $f_data['description'] ) ? '<div class="pmaf-desc">'. $f_data['description'] .'</div>' : '';
				$html .= '</div>';
			break;
			
			case "link":				
				$html = '<div class="pmaf-field pmaf-field-link'. esc_attr( $field_classes ) .'" id="'. esc_attr( $f_id ) .'" data-req-msg="'. esc_attr( $req_msg ) .'"><label>'. $field_label .'</label><input type="url" placeholder="'. $f_data['placeholder'] .'" name="'. $f_data['name'] .'" value="'. $default .'" />';
				$html .= !empty( $f_data['description'] ) ? '<div class="pmaf-desc">'. $f_data['description'] .'</div>' : '';
				$html .= '</div>';
			break;
			
			case "phone":				
				$html = '<div class="pmaf-field pmaf-field-phone'. esc_attr( $field_classes ) .'" id="'. esc_attr( $f_id ) .'" data-req-msg="'. esc_attr( $req_msg ) .'"><label>'. $field_label .'</label><input type="text" data-type="phone" placeholder="'. $f_data['placeholder'] .'" name="'. $f_data['name'] .'" value="'. $default .'" />';
				$html .= !empty( $f_data['description'] ) ? '<div class="pmaf-desc">'. $f_data['description'] .'</div>' : '';
				$html .= '</div>';
			break;
			
			case "textarea":
				$html = '<div class="pmaf-field pmaf-field-textarea'. esc_attr( $field_classes ) .'" id="'. esc_attr( $f_id ) .'" data-req-msg="'. esc_attr( $req_msg ) .'"><label>'. $field_label .'</label><textarea placeholder="'. $f_data['placeholder'] .'" name="'. $f_data['name'] .'" rows="3" >'. $default .'</textarea>';
				$html .= !empty( $f_data['description'] ) ? '<div class="pmaf-desc">'. $f_data['description'] .'</div>' : '';
				$html .= '</div>';
			break;
			
			case "checkbox":
				$options = isset( $f_data['options'] ) ? $f_data['options'] : '';
				$name = isset( $f_data['name'] ) ? $f_data['name'] : '';
				$options_html = '';
				$default = !empty( $default ) && is_array( $default ) ? $default : [ $default ];
				if( !empty( $options ) ) {
					foreach( $options as $key => $value ) {
						$options_html .= '<div class="pmaf-checkbox-single"><input type="checkbox" name="'. esc_html( $name ) .'[]" value="'. esc_attr( $value ) .'" '. ( in_array( $value, $default ) ? 'checked="checked"' : '' ) .' ><label>'. esc_html( $value ) .'</label></div>';							
					}
				}
				$html = '<div class="pmaf-field pmaf-field-checkbox'. esc_attr( $field_classes ) .'" id="'. esc_attr( $f_id ) .'" data-req-msg="'. esc_attr( $req_msg ) .'"><label>'. $field_label .'</label><div class="pmaf-checkbox-group">'. $options_html .'</div>';
				$html .= !empty( $f_data['description'] ) ? '<div class="pmaf-desc">'. $f_data['description'] .'</div>' : '';
				$html .= '</div>';
			break;
			
			case "radio":
				$options = isset( $f_data['options'] ) ? $f_data['options'] : '';
				$name = isset( $f_data['name'] ) ? $f_data['name'] : '';
				$options_html = '';
				if( !empty( $options ) ) {
					foreach( $options as $key => $value ) {
						$options_html .= '<div class="pmaf-radio-single"><input type="radio" name="'. esc_html( $name ) .'" value="'. esc_attr( $value ) .'" '. ( $value == $default ? 'checked="checked"' : '' ) .' ><label>'. esc_html( $value ) .'</label></div>';
					}
				}
				$html = '<div class="pmaf-field pmaf-field-radio'. esc_attr( $field_classes ) .'" id="'. esc_attr( $f_id ) .'" data-req-msg="'. esc_attr( $req_msg ) .'"><label>'. $field_label .'</label><div class="pmaf-radio-group">'. $options_html .'</div>';
				$html .= !empty( $f_data['description'] ) ? '<div class="pmaf-desc">'. $f_data['description'] .'</div>' : '';
				$html .= '</div>';
			break;
			
			case "select":
				$placeholder = isset( $f_data['placeholder'] ) ? $f_data['placeholder'] : '';
				$options = isset( $f_data['options'] ) ? $f_data['options'] : '';
				$options_html = '';
				if( $placeholder != '' && $default == '' ) {
					$options_html .= '<option value="">'. esc_html( $placeholder ) .'</option>';
				}
				if( !empty( $options ) ) {
					foreach( $options as $key => $value ) {
						$options_html .= '<option value="'. esc_attr( $key ) .'" '. ( $value == $default ? 'selected="selected"' : '' ) .'>'. esc_html( $value ) .'</option>';
					}
				}
				$html = '<div class="pmaf-field pmaf-field-select'. esc_attr( $field_classes ) .'" id="'. esc_attr( $f_id ) .'" data-req-msg="'. esc_attr( $req_msg ) .'"><label>'. $field_label .'</label><select name="'. $f_data['name'] .'">'. $options_html .'</select>';
				$html .= !empty( $f_data['description'] ) ? '<div class="pmaf-desc">'. $f_data['description'] .'</div>' : '';
				$html .= '</div>';
			break;
			
			case "imageradio":
				$images = isset( $f_data['images'] ) ? $f_data['images'] : '';
				$options = isset( $f_data['options'] ) ? $f_data['options'] : '';
				$name = isset( $f_data['name'] ) ? $f_data['name'] : '';
				$options_html = '';
				if( !empty( $options ) ) {
					foreach( $options as $key => $value ) {
						$options_html .= '<div class="pmaf-img-radio-single'. ( $value == $default ? ' selected' : '' ) .'"><input type="radio" name="'. $name .'" value="'. esc_attr( $value ) .'" '. ( $value == $default ? 'checked="checked"' : '' ) .'>';
						if( !empty( $images ) && isset( $images[$key] ) ) {
							$options_html .= '<img src="'. $images[$key]['url'] .'" /><label>'. esc_html( $value ) .'</label>';
						} else {
							$options_html .= '<span class="pmaf-empty-img"><i class="af-page"></i></span><label>'. esc_html( $value ) .'</label>';
						}
						$options_html .= '</div>';
					}
				}
				$html = '<div class="pmaf-field pmaf-field-imageradio'. esc_attr( $field_classes ) .'" id="'. esc_attr( $f_id ) .'" data-req-msg="'. esc_attr( $req_msg ) .'"><label>'. $field_label .'</label><div class="pmaf-img-radio-group">'. $options_html .'</div>';
				$html .= !empty( $f_data['description'] ) ? '<div class="pmaf-desc">'. $f_data['description'] .'</div>' : '';
				$html .= '</div>';
			break;
			
			case "number":
				$html = '<div class="pmaf-field pmaf-field-number'. esc_attr( $field_classes ) .'" id="'. esc_attr( $f_id ) .'" data-req-msg="'. esc_attr( $req_msg ) .'"><label>'. $field_label .'</label><input type="number" placeholder="'. $f_data['placeholder'] .'" name="'. $f_data['name'] .'" min="'. $f_data['atts']['min'] .'" max="'. $f_data['atts']['max'] .'" step="'. $f_data['atts']['step'] .'" value="'. $default .'" />';
				$html .= !empty( $f_data['description'] ) ? '<div class="pmaf-desc">'. $f_data['description'] .'</div>' : '';
				$html .= '</div>';
			break;
			
			case "email":
				$html = '<div class="pmaf-field pmaf-field-email'. esc_attr( $field_classes ) .'" id="'. esc_attr( $f_id ) .'" data-req-msg="'. esc_attr( $req_msg ) .'"><label>'. $field_label .'</label><input type="email" placeholder="'. $f_data['placeholder'] .'" name="'. $f_data['name'] .'" value="'. $default .'" />';
				$html .= !empty( $f_data['description'] ) ? '<div class="pmaf-desc">'. $f_data['description'] .'</div>' : '';
				$html .= '</div>';
			break;
			
			case "slider":
				$selected_val = $f_data['result'];
				$selected_val = str_replace( "{value}", '<span class="pmaf-range-val">'. $default .'</span>', $selected_val );
				$html = '<div class="pmaf-field pmaf-field-slider'. esc_attr( $field_classes ) .'" id="'. esc_attr( $f_id ) .'" data-req-msg="'. esc_attr( $req_msg ) .'"><label>'. $field_label .'</label><input type="range" class="pmaf-slider" name="'. $f_data['name'] .'" min="'. $f_data['atts']['min'] .'" max="'. $f_data['atts']['max'] .'" step="'. $f_data['atts']['step'] .'" value="'. $default .'" /><div class="pmaf-slider-value">'. $selected_val .'</div>';
				$html .= !empty( $f_data['description'] ) ? '<div class="pmaf-desc">'. $f_data['description'] .'</div>' : '';
				$html .= '</div>';
			break;
			
			case "consent":				
				$html = '<div class="pmaf-field pmaf-field-consent'. esc_attr( $field_classes ) .'" id="'. esc_attr( $f_id ) .'" data-req-msg="'. esc_attr( $req_msg ) .'"><label>'. $field_label .'</label><div class="pmaf-checkbox"><input type="checkbox" data-type="consent" name="'. $f_data['name'] .'" value="consent" /><div class="pmaf-consent-content">'.  $f_data['content'] .'</div></div>';
				$html .= !empty( $f_data['description'] ) ? '<div class="pmaf-desc">'. $f_data['description'] .'</div>' : '';
				$html .= '</div>';
			break;
			
		}
		
		return $html;
		
	}

	public function pmaf_login_full_form( $id ){

		wp_enqueue_style( 'pmaf-style' );
		wp_enqueue_script( 'pmaf-script' );	

		//return wp_login_form();
		
		$ani_settings = pmaf_forms_data()->get_form_animation_settings( $id );
		$field_classes = isset( $ani_settings['labels']['label_move'] ) && $ani_settings['labels']['label_move'] == 'on' ? ' pmaf-label-animate' : '';
		$field_classes .= isset( $ani_settings['labels']['label_gradient'] ) && $ani_settings['labels']['label_gradient'] == 'on' ? ' pmaf-hue-animate' : '';

		$options = self::$options;
		$btn_label = isset( $options['login-btn-label'] ) ? $options['login-btn-label'] : '';
		$username_label = isset( $options['login-username-label'] ) ? $options['login-username-label'] : '';
		$password_label = isset( $options['login-password-label'] ) ? $options['login-password-label'] : '';
		
		$form_load = isset( $_POST['action'] ) && $_POST['action'] == 'pmaf_frontend_submit' ? true : false;
		$remember_me_opt = isset( $options['enable-remember-me'] ) && $options['enable-remember-me'] ? true : false;
		$processing_txt = isset( $options['login-submit-msg'] ) ? $options['login-submit-msg'] : esc_html__( 'Verifying user info, please wait...', 'animated-forms' );
		$security = isset( $options['login-security'] ) ? $options['login-security'] : '';
		
		$form_css_class = '';
		if( isset( $ani_settings['inner'] ) && isset( $ani_settings['inner']['model'] ) && !empty( $ani_settings['inner']['model'] ) ) {
			$form_css_class = ' pmaf-'. $ani_settings['inner']['model'];
		}
		$form_css_class .= isset( $form_settings['style']['form_css_class'] ) ? ' '. $form_settings['style']['form_css_class'] : '';
		
		$output = '<div class="pmaf-login-form-wrap">';
		$output .= '<form id="pmaf-form-'. esc_attr( $id ) .'" action="'. get_the_permalink() .'" name="pmafloginform" method="post" class="pmaf-form pmaf-login-form'. esc_attr( $form_css_class ) .'" enctype="multipart/form-data" data-ajax="on" data-ajax-msg="'. esc_attr( $processing_txt ) .'">';
			$output .= '<input type="hidden" name="action" value="pmaf_frontend_submit" />';
			$output .= '<input type="hidden" name="nonce" value="'. wp_create_nonce( $security ) .'" />';
			$output .= '<input type="hidden" name="form_id" value="'. esc_attr( $id ) .'" />';
			$output .= '<input type="hidden" name="pmaf_login" value="1" />';
			$output .= '<div class="pmaf-field pmaf-field-text pmaf-required'. esc_attr( $field_classes ) .'"><label>'. esc_html( $username_label ) .'</label><input type="text" name="username" value="" /></div>';
			$output .= '<div class="pmaf-field pmaf-field-text pmaf-required'. esc_attr( $field_classes ) .'"><label>'. esc_html( $password_label ) .'</label><input type="password" name="password" autocomplete="current-password"  value="" /></div>';
			
			if( $remember_me_opt ) {
				$remember_label = isset( $options['remember-label'] ) ? $options['remember-label'] : '';
				$output .= '<div class="pmaf-field pmaf-field-checkbox"><div class="pmaf-checkbox-group"><div class="pmaf-checkbox-single"><input type="checkbox" name="rememberme" value="forever"><label>'. esc_html( $remember_label ) .'</label></div></div></div>';
			}
			
			// button styles
			$btn_css_class = '';
			if( isset( $ani_settings['btn'] ) && !empty( $ani_settings['btn'] ) ) {
				$btn_stng = $ani_settings['btn'];
				$btn_css_class = isset( $btn_stng['btnstyle'] ) ? ' pmaf-btn-'. esc_attr( $btn_stng['btnstyle'] ) : ' pmaf-btn-default';
			}
			$output .= '<div class="submit-wrap"><input type="submit" class="pmaf-btn pmaf-submit'. esc_attr( $btn_css_class ) .'" value="'. esc_attr( $btn_label ) .'"  />';
			
			$output .= $this->af_login_form_bottom_elements();
			$output .= '<div class="pmaf-form-msg"></div>';
			$output .= '</div>';
			if( $form_load && $response['status'] == 'failed' ) {
				$output .= '<div id="pmaf-alert-'. $id .'" class="pmaf-alert-warning">'. $response['msg'] .'</div>';
			}
			
		$output .= '</form>';
		$output .= '</div>';
		
		$enable_register = isset( $options['enable-register'] ) && $options['enable-register'] ? true : false;
		if( $enable_register ) {
			$output .= $this->pmaf_registration_form_only( $id, $form_css_class );
		}
		
		$enable_forget = isset( $options['enable-forget'] ) && $options['enable-forget'] ? true : false;
		if( $enable_forget ) {
			$output .= $this->pmaf_forget_form_only( $id, $form_css_class );
		}
		
		return $output;
	}
	
	public function pmaf_registration_form_only( $id, $form_css_class = '' ){
		
		$options = self::$options;
		
		$ani_settings = pmaf_forms_data()->get_form_animation_settings( $id );
		$field_classes = isset( $ani_settings['labels']['label_move'] ) && $ani_settings['labels']['label_move'] == 'on' ? ' pmaf-label-animate' : '';
		$field_classes .= isset( $ani_settings['labels']['label_gradient'] ) && $ani_settings['labels']['label_gradient'] == 'on' ? ' pmaf-hue-animate' : '';
		
		$processing_txt = isset( $options['register-submit-msg'] ) ? $options['register-submit-msg'] : esc_html__( 'Verifying user info, please wait...', 'animated-forms' );
		$security = isset( $options['login-security'] ) ? $options['login-security'] : '';
		
		$r_fields = [ 'name-label', 'nick-name-label', 'email-label', 'username-label', 'password-label', 'register-btn-label', 'required-text', 'r-back-to-login' ];
		$form_fields = [];
		foreach( $r_fields as $r_field ) {
			$form_fields[$r_field] = isset( $options[$r_field] ) && !empty( $options[$r_field] ) ? $options[$r_field] : '';
		}
		$name_req = isset( $options['name-required'] ) && $options['name-required'] ? true : false;
		$nickname_req = isset( $options['nickname-required'] ) && $options['nickname-required'] ? true : false;
		
		$output = '<div class="pmaf-regiser-form-wrap">';
		$output .= '<form id="pmaf-form-'. esc_attr( $id ) .'-register" action="'. get_the_permalink() .'" name="pmafregisterform" method="post" class="pmaf-form pmaf-register-form'. esc_attr( $form_css_class ) .'" enctype="multipart/form-data" data-ajax="on" data-ajax-msg="'. esc_attr( $processing_txt ) .'">';
			$output .= '<input type="hidden" name="action" value="pmaf_frontend_submit" />';
			$output .= '<input type="hidden" name="nonce" value="'. wp_create_nonce( $security ) .'" />';
			$output .= '<input type="hidden" name="form_id" value="'. esc_attr( $id ) .'" />';
			$output .= '<input type="hidden" name="pmaf_register" value="1" />';
			$output .= '<div class="pmaf-field pmaf-field-text'. ( $name_req ? ' pmaf-required' : '' ) . esc_attr( $field_classes ) .'"><label>'. esc_html( $form_fields['name-label'] ) .'</label><input type="text" name="pmaf_new_name" value="" /></div>';
			$output .= '<div class="pmaf-field pmaf-field-text'. ( $name_req ? ' pmaf-required' : '' ) . esc_attr( $field_classes ) .'"><label>'. esc_html( $form_fields['nick-name-label'] ) .'</label><input type="text" name="pmaf_new_nick_name" value="" /></div>';
			$output .= '<div class="pmaf-field pmaf-field-text pmaf-required'. esc_attr( $field_classes ) .'"><label>'. esc_html( $form_fields['email-label'] ) .'</label><input type="text" name="pmaf_new_email" value="" /></div>';
			$output .= '<div class="pmaf-field pmaf-field-text pmaf-required'. esc_attr( $field_classes ) .'"><label>'. esc_html( $form_fields['password-label'] ) .'</label><input type="password" name="pmaf_new_password" autocomplete="current-password" value="" /></div>';
			
			// button styles
			$btn_css_class = '';
			if( isset( $ani_settings['btn'] ) && !empty( $ani_settings['btn'] ) ) {
				$btn_stng = $ani_settings['btn'];
				$btn_css_class = isset( $btn_stng['btnstyle'] ) ? ' pmaf-btn-'. esc_attr( $btn_stng['btnstyle'] ) : ' pmaf-btn-default';
			}
			
			$output .= '<div class="submit-wrap"><input type="submit" class="pmaf-btn pmaf-submit'. esc_attr( $btn_css_class ) .'" value="'. esc_attr( $form_fields['register-btn-label'] ) .'"  /><a href="#" class="pm-reg-to-login-form">'. $form_fields['r-back-to-login'] .'</a>';
				$output .= '<div class="pmaf-form-msg"></div>';
			$output .= '</div>';
		$output .= '</form>';
		$output .= '</div>';
		
		return $output;
		
	}
	
	public function pmaf_forget_form_only( $id, $form_css_class = '' ){
		
		$options = self::$options;
		
		$ani_settings = pmaf_forms_data()->get_form_animation_settings( $id );
		$field_classes = isset( $ani_settings['labels']['label_move'] ) && $ani_settings['labels']['label_move'] == 'on' ? ' pmaf-label-animate' : '';
		$field_classes .= isset( $ani_settings['labels']['label_gradient'] ) && $ani_settings['labels']['label_gradient'] == 'on' ? ' pmaf-hue-animate' : '';
		
		$r_fields = [ 'forget-username', 'f-back-to-login', 'forget-btn-label' ];
		$form_fields = [];
		foreach( $r_fields as $r_field ) {
			$form_fields[$r_field] = isset( $options[$r_field] ) && !empty( $options[$r_field] ) ? $options[$r_field] : '';
		}
		
		$processing_txt = isset( $options['forget-submit-msg'] ) ? $options['forget-submit-msg'] : esc_html__( 'Verifying user info, please wait...', 'animated-forms' );
		$security = isset( $options['login-security'] ) ? $options['login-security'] : '';
		
		$output = '<div class="pmaf-forget-form-wrap">';
		$output .= '<form id="pmaf-form-'. esc_attr( $id ) .'-forget" action="'. get_the_permalink() .'" name="pmafforgetform" method="post" class="pmaf-form pmaf-forget-form'. esc_attr( $form_css_class ) .'" enctype="multipart/form-data" data-ajax="on" data-ajax-msg="'. esc_attr( $processing_txt ) .'">';
			$output .= '<input type="hidden" name="action" value="pmaf_frontend_submit" />';
			$output .= '<input type="hidden" name="nonce" value="'. wp_create_nonce( $security ) .'" />';
			$output .= '<input type="hidden" name="form_id" value="'. esc_attr( $id ) .'" />';
			$output .= '<input type="hidden" name="pmaf_forget" value="1" />';
			$output .= '<div class="pmaf-field pmaf-field-text pmaf-required'. esc_attr( $field_classes ) .'"><label>'. esc_html( $form_fields['forget-username'] ) .'</label><input type="text" name="user_fp_login" value="" /></div>';
			
			// button styles
			$btn_css_class = '';
			if( isset( $ani_settings['btn'] ) && !empty( $ani_settings['btn'] ) ) {
				$btn_stng = $ani_settings['btn'];
				$btn_css_class = isset( $btn_stng['btnstyle'] ) ? ' pmaf-btn-'. esc_attr( $btn_stng['btnstyle'] ) : ' pmaf-btn-default';
			}
			
			$output .= '<div class="submit-wrap"><input type="submit" class="pmaf-btn pmaf-submit'. esc_attr( $btn_css_class ) .'" value="'. esc_attr( $form_fields['forget-btn-label'] ) .'"  /><a href="#" class="pm-reg-to-login-form">'. $form_fields['f-back-to-login'] .'</a>';
				$output .= '<div class="pmaf-form-msg"></div>';
			$output .= '</div>';
		$output .= '</form>';
		$output .= '</div>';
		
		return $output;
		
	}
	
	public function af_login_form_top_elements() {
		
		wp_nonce_field( 'pm-ajax-login-nonce', 'pmaf_security' );
		echo '<p class="status"></p>';
		
	}
	
	public function af_login_form_bottom_elements() {
		
		$options = self::$options;
		$register_opt = isset( $options['enable-register'] ) && $options['enable-register'] ? true : false;
		$separator = isset( $options['register-separator'] ) && !empty( $options['register-separator'] ) ? $options['register-separator'] : '';
		$forget_opt = isset( $options['enable-forget'] ) && $options['enable-forget'] ? true : false;
		
		$register_btn_label = isset( $options['register-link-label'] ) ? $options['register-link-label'] : '';
		$forget_btn_label = isset( $options['forget-link-label'] ) ? $options['forget-link-label'] : '';
		ob_start();
	?>
		<div class="pm-login-bottom">
			<?php if ( get_option( 'users_can_register' ) ) : ?>
				<?php if( $register_opt ): ?>
				<a class="pm-af-register-trigger" href="#"><?php printf( '%s', $register_btn_label ); ?></a>
				<?php endif; ?>
				<?php if( ( $register_opt && $forget_opt ) && $separator ): ?>
					<?php echo do_shortcode( $separator ); ?>
				<?php endif; ?>
			<?php endif; ?>
			<?php if( $forget_opt ): ?>
				<a class="pm-af-lost-password-trigger" href="#"><?php printf( '%s', $forget_btn_label ); ?></a>
			<?php endif; ?>
		</div>
	<?php
		return ob_get_clean();
	}
	
	public function pmaf_credentials(){
		
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		/* you can safely run request_filesystem_credentials() without any issues and don't need to worry about passing in a URL */
		$creds = request_filesystem_credentials(site_url() . '/wp-admin/', '', false, false, array());
	
		/* initialize the API */
		if ( ! WP_Filesystem($creds) ) {
			return false;
		}
		global $wp_filesystem;
		return $wp_filesystem;
	}
		
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
		
} PMAF_Animated_Forms_Shortcodes::get_instance();