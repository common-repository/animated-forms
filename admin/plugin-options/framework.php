<?php 

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class PMAF_Animated_Forms_Options {
	
	private static $_instance = null;
	
	public static $opt_name;
	
	public static $tab_list = '';
	
	public static $tab_content = '';
	
	public static $parent_tab_count = 1;
	
	public static $tab_count = 1;
	
	public static $pmaf_alf_options = array();
	
	public function __construct() {}
		
	public static function pmaf_set_section( $settings ){
		$tab_item_class = ''; //self::$parent_tab_count <= 1 ? ' active' : '';
		self::$tab_list .= '<li class="tablinks'. esc_attr( $tab_item_class ) .'" data-id="'. esc_attr( $settings['id'] ) .'"><span class="tab-title">'. esc_html( $settings['title'] ) .'</span>';
		self::$tab_list .= '<ul class="tablinks-sub-list">';
		self::$parent_tab_count++;
	}
	
	public static function pmaf_set_sub_section( $settings ){
		$tab_item_class = self::$tab_count <= 1 ? ' active' : '';
		self::$tab_list .= '<li class="tablinks'. esc_attr( $tab_item_class ) .'" data-id="'. esc_attr( $settings['id'] ) .'">';
		self::$tab_list .= '<span class="tab-title">';
		if( isset( $settings['icon_class'] ) && !empty( $settings['icon_class'] ) ) self::$tab_list .= '<i class="'. esc_attr( $settings['icon_class'] ) .'"></i>';
		self::$tab_list .= esc_html( $settings['title'] ) . '</span></li>';
		$tab_class = self::$tab_count != 1 ? ' tab-hide' : '';
		self::$tab_content .= '<div id="'. esc_attr( $settings['id'] ) .'" class="tabcontent'. esc_attr( $tab_class ) .'">'. self::pmaf_set_field( $settings['id'], $settings['fields'] ) .'</div>';
		self::$tab_count++;
	}
	
	public static function pmaf_set_end_section( $settings ){
		self::$tab_list .= '</ul></li>';
	}
	
	public static function pmaf_set_field( $id, $fields ){
	
		$pmaf_alf_options = self::$pmaf_alf_options;
	
		$field_element = '';
		$field_title = '';
		$field_out = '';
		foreach( $fields as $config ){
		
			$description = isset( $config['desc'] ) ? $config['desc'] : '';
			ob_start();
			switch( $config['type'] ){
				case "label":
					self::pmaf_label_field( $config );
				break;
				case "text":
					self::pmaf_text_field( $config );
				break;
				case "number":
					self::pmaf_number_field( $config );
				break;
				case "textarea":
					self::pmaf_textarea_field( $config );
				break;
				case "checkbox":
					self::pmaf_checkbox_field( $config );
				break;
				case "toggle":
					self::pmaf_toggle_switch_field( $config );
				break;				
				case "html":
					self::pmaf_html_field( $config );
				break;
				case "editor":
					self::pmaf_editor_field( $config );
				break;
				case "select":
					self::pmaf_select_field( $config );
				break;
				case "color":
					self::pmaf_color_field( $config );
				break;	
				case "image":
					self::pmaf_image_field( $config );
				break;
				case "multicheck":
					self::pmaf_multi_check_field( $config );
				break;
				case "radioimage":
					self::pmaf_radio_image_field( $config );
				break;				
			}
			$field_out .= ob_get_clean();
			
		}
	
		return $field_out;
	}
	
	public static function pmaf_label_field( $config ){ 
		$pmaf_alf_options = self::$pmaf_alf_options;
		$field_id = $config['id'];
		$sepcific_field = isset( $config['sepcific_field'] ) ? $config['sepcific_field'] : false;
		$html_tag = isset( $config['html_tag'] ) && !empty( $config['html_tag'] ) ? $config['html_tag'] : 'label';
		$custom_img = isset( $config['custom_img'] ) ? $config['custom_img'] : '';
		
		$required = isset( $config['required'] ) ? $config['required'] : '';
		$required_out = $required_class = '';
		$required_class = isset( $config['custom_class'] ) ? $config['custom_class'] : '';
		if( $required ){
			$required_class = ' pmaf-customize-required';
			$req_value = is_array( $required ) && isset( $required[2] ) && !empty( $required[2] ) ? implode( ",", $required[2] ) : '';
			$required_out .= 'data-required="'. $required[0] .'" data-required-cond="'. $required[1] .'"  data-required-val="'. $req_value .'" ';
		}
		$field_id = $sepcific_field ? $field_id : 'pmaf_alf_options['. esc_attr( $field_id ) .']';
		
		$seperator = isset( $config['seperator'] ) ? $config['seperator'] : '';
		
	?>
		<div class="pmaf-control label-control<?php echo esc_attr( $required_class ); ?>" <?php echo !empty( $required_out ) ? wp_kses_post( $required_out ) : ''; ?> data-id="<?php echo esc_attr( $field_id ); ?>">
			<?php if( !empty( $seperator ) && ( $seperator == 'before' || $seperator == 'both' ) ): ?><span class="field-seperator seperator-before"></span><?php endif; ?>
			<?php if( !empty( $custom_img ) ) : ?>
				<img src="<?php echo esc_url( $custom_img ); ?>" alt="<?php echo esc_attr( $config['title'] ); ?>" class="label-img"/>
			<?php endif; ?>
			<?php if( isset( $config['title'] ) && !empty( $config['title'] ) ): ?><<?php echo esc_attr( $html_tag ); ?> class="customize-control-title"><?php echo esc_html( $config['title'] ); ?></<?php echo esc_attr( $html_tag ); ?>><?php endif; ?>
			<?php if( isset( $config['description'] ) && !empty( $config['description'] ) ): ?><span class="description customize-control-description"><?php echo wp_kses_post( $config['description'] ); ?></span><?php endif; ?>
			<?php if( !empty( $seperator ) && ( $seperator == 'after' || $seperator == 'both' ) ): ?><span class="field-seperator seperator-after"></span><?php endif; ?>
		</div>
	<?php
	}
	
	public static function pmaf_text_field( $config ){ 
		$pmaf_alf_options = self::$pmaf_alf_options;
		$field_id = $config['id'];
		$sepcific_field = isset( $config['sepcific_field'] ) ? $config['sepcific_field'] : false;
		$password = isset( $config['password'] ) ? $config['password'] : false;
		$hidden = isset( $config['hidden'] ) ? $config['hidden'] : false;
		$input_type = 'text';
		if( $password ) {
			$input_type = 'password';
		} elseif( $hidden ) {
			$input_type = 'hidden';
		}
		
		$saved_val = '';
		if( $sepcific_field ) {
			$saved_val = get_post_meta( get_the_ID(), $field_id, 1 );
		} else {
			if( isset( $pmaf_alf_options[$field_id] ) ){
				$saved_val = stripslashes( $pmaf_alf_options[$field_id] );
			}else{
				$saved_val = isset( $config['default'] ) ? $config['default'] : '';
			}
		}
		
		$required = isset( $config['required'] ) ? $config['required'] : '';
		$required_out = $required_class = '';
		$required_class = isset( $config['custom_class'] ) ? $config['custom_class'] : '';
		if( $required ){
			$required_class = ' pmaf-customize-required';
			$req_value = is_array( $required ) && isset( $required[2] ) && !empty( $required[2] )  ? implode( ",", $required[2] ) : '';
			$required_out .= 'data-required="'. $required[0] .'" data-required-cond="'. $required[1] .'"  data-required-val="'. $req_value .'" ';
		}
		$field_id = $sepcific_field ? $field_id : 'pmaf_alf_options['. esc_attr( $field_id ) .']';
		
	?>
		<div class="pmaf-control<?php echo esc_attr( $required_class ); ?>" <?php echo !empty( $required_out ) ? wp_kses_post( $required_out ) : ''; ?> data-id="<?php echo esc_attr( $field_id ); ?>">
			<?php if( isset( $config['title'] ) && !empty( $config['title'] ) ): ?><label class="customize-control-title"><?php echo esc_html( $config['title'] ); ?></label><?php endif; ?>
			<?php if( isset( $config['description'] ) && !empty( $config['description'] ) ): ?><span class="description customize-control-description"><?php echo wp_kses_post( $config['description'] ); ?></span><?php endif; ?>
			<input type="<?php echo esc_attr( $input_type ); ?>" class="pmaf-customizer-text-field" data-key="<?php echo esc_attr( $field_id ); ?>" id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $field_id ); ?>" value="<?php echo esc_attr( $saved_val ); ?>">
		</div>
	<?php
	}
	
	public static function pmaf_number_field( $config ){ 
		$pmaf_alf_options = self::$pmaf_alf_options;
		$field_id = $config['id'];
		$sepcific_field = isset( $config['sepcific_field'] ) ? $config['sepcific_field'] : false;
		
		$saved_val = '';
		if( $sepcific_field ) {
			$saved_val = get_post_meta( get_the_ID(), $field_id, 1 );
		} else {
			if( isset( $pmaf_alf_options[$field_id] ) ){
				$saved_val = stripslashes( $pmaf_alf_options[$field_id] );
			}else{
				$saved_val = isset( $config['default'] ) ? $config['default'] : '';
			}
		}
		
		$required = isset( $config['required'] ) ? $config['required'] : '';		
		$required_out = $required_class = '';
		$required_class = isset( $config['custom_class'] ) ? $config['custom_class'] : '';
		if( $required ){
			$required_class = ' pmaf-customize-required';
			$req_value = is_array( $required ) && isset( $required[2] ) && !empty( $required[2] )  ? implode( ",", $required[2] ) : '';
			$required_out .= 'data-required="'. $required[0] .'" data-required-cond="'. $required[1] .'"  data-required-val="'. $req_value .'" ';
		}
		$field_id = $sepcific_field ? $field_id : 'pmaf_alf_options['. esc_attr( $field_id ) .']';
		
	?>
		<div class="pmaf-control<?php echo esc_attr( $required_class ); ?>" <?php echo !empty( $required_out ) ? wp_kses_post( $required_out ) : ''; ?> data-id="<?php echo esc_attr( $field_id ); ?>">
			<?php if( isset( $config['title'] ) && !empty( $config['title'] ) ): ?><label class="customize-control-title"><?php echo esc_html( $config['title'] ); ?></label><?php endif; ?>
			<?php if( isset( $config['description'] ) && !empty( $config['description'] ) ): ?><span class="description customize-control-description"><?php echo wp_kses_post( $config['description'] ); ?></span><?php endif; ?>
			<input type="number" class="pmaf-customizer-text-field" data-key="<?php echo esc_attr( $field_id ); ?>" id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $field_id ); ?>" value="<?php echo esc_attr( $saved_val ); ?>">
		</div>
	<?php
	}
	
	public static function pmaf_textarea_field( $config ){ 
		$pmaf_alf_options = self::$pmaf_alf_options;
		$field_id = $config['id'];
		$sepcific_field = isset( $config['sepcific_field'] ) ? $config['sepcific_field'] : false;
		
		$saved_val = '';
		if( $sepcific_field ) {
			$saved_val = get_post_meta( get_the_ID(), $field_id, 1 );
		} else {
			if( isset( $pmaf_alf_options[$field_id] ) ){
				$saved_val = stripslashes( $pmaf_alf_options[$field_id] );
			}else{
				$saved_val = isset( $config['default'] ) ? $config['default'] : '';
			}
		}
		
		$required = isset( $config['required'] ) ? $config['required'] : '';
		$required_out = $required_class = '';
		$required_class = isset( $config['custom_class'] ) ? $config['custom_class'] : '';
		if( $required ){
			$required_class = ' pmaf-customize-required';
			$req_value = is_array( $required ) && isset( $required[2] ) && !empty( $required[2] )  ? implode( ",", $required[2] ) : '';
			$required_out .= 'data-required="'. $required[0] .'" data-required-cond="'. $required[1] .'"  data-required-val="'. $req_value .'" ';
		}
		$field_id = $sepcific_field ? $field_id : 'pmaf_alf_options['. esc_attr( $field_id ) .']';
		
	?>
		<div class="pmaf-control<?php echo esc_attr( $required_class ); ?>" <?php echo !empty( $required_out ) ? wp_kses_post( $required_out ) : ''; ?> data-id="<?php echo esc_attr( $field_id ); ?>">
			<?php if( isset( $config['title'] ) && !empty( $config['title'] ) ): ?><label class="customize-control-title"><?php echo esc_html( $config['title'] ); ?></label><?php endif; ?>
			<?php if( isset( $config['description'] ) && !empty( $config['description'] ) ): ?><span class="description customize-control-description"><?php echo wp_kses_post( $config['description'] ); ?></span><?php endif; ?>
			<textarea class="pmaf-customizer-textarea-field" data-key="<?php echo esc_attr( $field_id ); ?>" id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_textarea( $saved_val ); ?></textarea>
			<?php
				if( isset( $config['custom_button'] ) ) {
					echo '<input type="button" id="'. esc_attr( $config['custom_button']['key'] ) .'" class="wp-button button pmaf-custom-btn-control" value="'. esc_attr( $config['custom_button']['value'] ) .'" />';
				}
			?>
		</div>
	<?php
	}
	
	public static function pmaf_html_field( $config ){ 
		$pmaf_alf_options = self::$pmaf_alf_options;
		$field_id = $config['id'];
		$rows = isset( $config['rows'] ) ? $config['rows'] : 5;
		$sepcific_field = isset( $config['sepcific_field'] ) ? $config['sepcific_field'] : false;
		
		$saved_val = '';
		if( $sepcific_field ) {
			$saved_val = get_post_meta( get_the_ID(), $field_id, 1 );
		} else {
			if( isset( $pmaf_alf_options[$field_id] ) ){
				$saved_val = stripslashes( $pmaf_alf_options[$field_id] );
			}else{
				$saved_val = isset( $config['default'] ) ? $config['default'] : '';
			}
		}
		
		$required = isset( $config['required'] ) ? $config['required'] : '';
		$required_out = $required_class = '';
		$required_class = isset( $config['custom_class'] ) ? $config['custom_class'] : '';
		if( $required ){
			$required_class = ' pmaf-customize-required';
			$req_value = is_array( $required ) && isset( $required[2] ) && !empty( $required[2] )  ? implode( ",", $required[2] ) : '';
			$required_out .= 'data-required="'. $required[0] .'" data-required-cond="'. $required[1] .'"  data-required-val="'. $req_value .'" ';
		}
		$field_id = $sepcific_field ? $field_id : 'pmaf_alf_options['. esc_attr( $field_id ) .']';
		
	?>
		<div class="pmaf-control<?php echo esc_attr( $required_class ); ?>" <?php echo !empty( $required_out ) ? wp_kses_post( $required_out ) : ''; ?> data-id="<?php echo esc_attr( $field_id ); ?>">
			<?php if( isset( $config['title'] ) && !empty( $config['title'] ) ): ?><label class="customize-control-title"><?php echo esc_html( $config['title'] ); ?></label><?php endif; ?>
			<?php if( isset( $config['description'] ) && !empty( $config['description'] ) ): ?><span class="description customize-control-description"><?php echo wp_kses_post( $config['description'] ); ?></span><?php endif; ?>
			<textarea class="pmaf-customizer-textarea-field" data-key="<?php echo esc_attr( $field_id ); ?>" id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $field_id ); ?>" rows="<?php echo esc_attr( $rows ); ?>"><?php echo esc_textarea( $saved_val ); ?></textarea>
		</div>
	<?php
	}
	
	public static function pmaf_editor_field( $config ){ 
		$pmaf_alf_options = self::$pmaf_alf_options;
		$field_id = $config['id'];		
		$args = isset( $config['args'] ) ? $config['args'] : '';
		$editor_key = isset( $config['editor_key'] ) ? $config['editor_key'] : '';
		$sepcific_field = isset( $config['sepcific_field'] ) ? $config['sepcific_field'] : false;
		
		$saved_val = '';
		if( $sepcific_field ) {
			$saved_val = get_post_meta( get_the_ID(), $field_id, 1 );
		} else {
			if( isset( $pmaf_alf_options[$field_id] ) ){
				$saved_val = stripslashes( $pmaf_alf_options[$field_id] );
			}else{
				$saved_val = isset( $config['default'] ) ? $config['default'] : '';
			}
		}
		
		$required = isset( $config['required'] ) ? $config['required'] : '';
		$required_out = $required_class = '';
		$required_class = isset( $config['custom_class'] ) ? $config['custom_class'] : '';
		if( $required ){
			$required_class = ' pmaf-customize-required';
			$req_value = is_array( $required ) && isset( $required[2] ) && !empty( $required[2] )  ? implode( ",", $required[2] ) : '';
			$required_out .= 'data-required="'. $required[0] .'" data-required-cond="'. $required[1] .'"  data-required-val="'. $req_value .'" ';
		}
		$field_id = $sepcific_field ? $field_id : 'pmaf_alf_options['. esc_attr( $field_id ) .']';
		
	?>
		<div class="pmaf-control<?php echo esc_attr( $required_class ); ?>" <?php echo !empty( $required_out ) ? wp_kses_post( $required_out ) : ''; ?> data-id="<?php echo esc_attr( $field_id ); ?>">
			<?php if( isset( $config['title'] ) && !empty( $config['title'] ) ): ?><label class="customize-control-title"><?php echo esc_html( $config['title'] ); ?></label><?php endif; ?>
			<?php if( isset( $config['description'] ) && !empty( $config['description'] ) ): ?><span class="description customize-control-description"><?php echo wp_kses_post( $config['description'] ); ?></span><?php endif; ?>
			<?php wp_editor( $saved_val, $editor_key, $args ); ?>
		</div>
	<?php
	}
	
	public static function pmaf_select_field( $config ){ 
		$pmaf_alf_options = self::$pmaf_alf_options;
		$field_id = $config['id'];
		
		$choices = isset( $config['choices'] ) ? $config['choices'] : '';
		$saved_val = '';
		if( isset( $pmaf_alf_options[$field_id] ) ){
			$saved_val = $pmaf_alf_options[$field_id];
		}else{
			$saved_val = isset( $config['default'] ) ? $config['default'] : '';
		}
		
		$select2 = isset( $config['select2'] ) && $config['select2'] == true ? true : false;
		$extra_class = '';
		if( $select2 ) {
			$extra_class = ' pmaf-select2';
		}
		
		$multiple = isset( $config['multiple'] ) && $config['multiple'] == true ? true : false;
		
		$required = isset( $config['required'] ) ? $config['required'] : '';
		$required_out = $required_class = '';
		$required_class = isset( $config['custom_class'] ) ? $config['custom_class'] : '';
		if( $required ){
			$required_class = ' pmaf-customize-required';
			$req_value = is_array( $required ) && isset( $required[2] ) && !empty( $required[2] )  ? implode( ",", $required[2] ) : '';
			$required_out .= 'data-required="'. $required[0] .'" data-required-cond="'. $required[1] .'"  data-required-val="'. $req_value .'" ';
		}

	?>
		
		<div class="pmaf-control<?php echo esc_attr( $required_class ); ?>" <?php echo !empty( $required_out ) ? wp_kses_post( $required_out ) : ''; ?> data-field-type="select" data-id="<?php echo esc_attr( $field_id ); ?>" data-id="<?php echo esc_attr( $field_id ); ?>">
			<?php if( isset( $config['title'] ) && !empty( $config['title'] ) ): ?><label class="customize-control-title"><?php echo esc_html( $config['title'] ); ?></label><?php endif; ?>
			<?php if( isset( $config['description'] ) && !empty( $config['description'] ) ): ?><span class="description customize-control-description"><?php echo wp_kses_post( $config['description'] ); ?></span><?php endif; ?>
			<select class="pmaf-customizer-select-field<?php echo esc_attr( $extra_class ); ?>" <?php echo boolval( $multiple ) ? 'multiple="multiple"' : ''; ?> name="pmaf_alf_options[<?php echo esc_attr( $field_id ); ?>]<?php echo boolval( $multiple ) ? '[]' : ''; ?>" data-select-2="<?php if( $multiple && !empty( $saved_val ) && is_array( $saved_val ) ) echo esc_js( htmlspecialchars( json_encode( $saved_val ) ) ); else echo wp_kses_post( $saved_val );?>">
			<?php 
				if( !empty( $choices ) ){
					foreach( $choices as $key => $value ){
						echo '<option value="'. esc_attr( $key ) .'" '. ( ( $multiple && !empty( $saved_val ) && is_array( $saved_val ) ) ? '' : selected( $saved_val, $key ) ) .'>'. esc_html( $value ) .'</option>';
					}
				}
			?>
			</select>
		</div>
	<?php
	}
	
	public static function pmaf_color_field( $config ){ 
		$pmaf_alf_options = self::$pmaf_alf_options;
		$field_id = $config['id'];
		
		$saved_val = '';
		$default_color =  isset( $config['default'] ) ? $config['default'] : '';
		if( isset( $pmaf_alf_options[$field_id] ) ){
			$saved_val = stripslashes( $pmaf_alf_options[$field_id] );
		}else{
			$saved_val = isset( $config['default'] ) ? $config['default'] : '';
		}
		$alpha = isset( $config['alpha'] ) ? $config['alpha'] : false;
		
		$required = isset( $config['required'] ) ? $config['required'] : '';
		$required_out = $required_class = '';
		$required_class = isset( $config['custom_class'] ) ? $config['custom_class'] : '';
		if( $required ){
			$required_class = ' pmaf-customize-required';
			$req_value = is_array( $required ) && isset( $required[2] ) && !empty( $required[2] )  ? implode( ",", $required[2] ) : '';
			$required_out .= 'data-required="'. $required[0] .'" data-required-cond="'. $required[1] .'"  data-required-val="'. $req_value .'" ';
		}
		
	?>
		<div class="pmaf-control<?php echo esc_attr( $required_class ); ?>" <?php echo !empty( $required_out ) ? wp_kses_post( $required_out ) : ''; ?> data-id="<?php echo esc_attr( $field_id ); ?>">
			<?php if( isset( $config['title'] ) && !empty( $config['title'] ) ): ?><label class="customize-control-title"><?php echo esc_html( $config['title'] ); ?></label><?php endif; ?>
			<?php if( isset( $config['description'] ) && !empty( $config['description'] ) ): ?><span class="description customize-control-description"><?php echo wp_kses_post( $config['description'] ); ?></span><?php endif; ?>
			<div class="color-control-wrap">
				<input type="text" class="wp-font-field wp-font-color-field" value="<?php echo esc_attr( $saved_val ); ?>" name="pmaf_alf_options[<?php echo esc_attr( $field_id ); ?>]" data-alpha-enabled="<?php echo esc_attr( $alpha ); ?>" />
			</div><!-- .alpha-wrap -->
		</div>
	<?php
	}
	
	public static function pmaf_image_field( $config ){ 
		$pmaf_alf_options = self::$pmaf_alf_options;
		$field_id = $config['id'];
		
		$saved_val = '';
		if( isset( $pmaf_alf_options[$field_id] ) ){
			$saved_val = $pmaf_alf_options[$field_id];
		}else{
			$saved_val = isset( $config['default'] ) ? $config['default'] : '';
		}
		
		$pmaf_media = $pmaf_media_id = $pmaf_media_url = '';
		$pmaf_media = isset( $saved_val['image'] ) ? $saved_val['image'] : '';				
		if( !empty( $pmaf_media ) && is_array( $pmaf_media ) ){
			$pmaf_media_id = isset( $pmaf_media['id'] ) ? $pmaf_media['id'] : '';
			if ( wp_attachment_is_image( $pmaf_media_id ) ) {
				$pmaf_media_url = isset( $pmaf_media['url'] ) ? wp_get_attachment_url( $pmaf_media_id ) : '';
			}
		}
		
		$required = isset( $config['required'] ) ? $config['required'] : '';
		$required_out = $required_class = '';
		$required_class = isset( $config['custom_class'] ) ? $config['custom_class'] : '';
		if( $required ){
			$required_class = ' pmaf-customize-required';
			$req_value = is_array( $required ) && isset( $required[2] ) && !empty( $required[2] )  ? implode( ",", $required[2] ) : '';
			$required_out .= 'data-required="'. $required[0] .'" data-required-cond="'. $required[1] .'"  data-required-val="'. $req_value .'" ';
		}
		
	?>
		<div class="pmaf-control<?php echo esc_attr( $required_class ); ?>" <?php echo !empty( $required_out ) ? wp_kses_post( $required_out ) : ''; ?> data-id="<?php echo esc_attr( $field_id ); ?>">
			<?php if( isset( $config['title'] ) && !empty( $config['title'] ) ): ?><label class="customize-control-title"><?php echo esc_html( $config['title'] ); ?></label><?php endif; ?>
			<?php if( isset( $config['description'] ) && !empty( $config['description'] ) ): ?><span class="description customize-control-description"><?php echo wp_kses_post( $config['description'] ); ?></span><?php endif; ?>
			
			<div class="pmaf-customizer-image-btn-wrap">
				<div class="pmaf-image-upload-field">
					<input type="hidden" class="pmaf-img-id" name="pmaf_alf_options[<?php echo esc_attr( $field_id ); ?>][image][id]" value="<?php echo esc_attr( $pmaf_media_id ); ?>" />
					<input type="hidden" class="pmaf-img-url" name="pmaf_alf_options[<?php echo esc_attr( $field_id ); ?>][image][url]" value="<?php echo esc_attr( $pmaf_media_url ); ?>" />						
					<div class="img-btn-controls">
						<input type="button" class="wp-background-field bg-upload-image-button" value="<?php esc_html_e( 'Upload Image', 'animated-forms' ); ?>" />
						<input type="button" class="bg-remove-image-button" value="<?php esc_html_e( 'Remove Image', 'animated-forms' ); ?>" />
					</div>
					<div class="img-place">
						<?php
							if( !empty( $pmaf_media_url ) ) :
								$media_alt = $pmaf_media_id ? get_post_meta( $pmaf_media_id, '_wp_attachment_image_alt', true ) : '';
						?>
							<img src="<?php echo esc_url( $pmaf_media_url ); ?>" alt="<?php echo esc_attr( $media_alt ); ?>" class="pmaf-bg-img">
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	<?php
	}
		
	public static function pmaf_multi_check_field( $config ){ 
		$pmaf_alf_options = self::$pmaf_alf_options;
		$field_id = $config['id'];
		
		
		$saved_val = '';
		if( isset( $pmaf_alf_options[$field_id] ) ){
			$saved_val = $pmaf_alf_options[$field_id];
		}else{
			$saved_val = isset( $config['default'] ) ? $config['default'] : [];
		}
		
		$mc_ele = $saved_val; 
		$mc_items = isset( $config['items'] ) ? $config['items'] : '';;
		
		$required = isset( $config['required'] ) ? $config['required'] : '';
		$required_out = $required_class = '';
		$required_class = isset( $config['custom_class'] ) ? $config['custom_class'] : '';
		if( $required ){
			$required_class = ' pmaf-customize-required';
			$req_value = is_array( $required ) && isset( $required[2] ) && !empty( $required[2] )  ? implode( ",", $required[2] ) : '';
			$required_out .= 'data-required="'. $required[0] .'" data-required-cond="'. $required[1] .'"  data-required-val="'. $req_value .'" ';
		}
	
		?>
		<div class="pmaf-control<?php echo esc_attr( $required_class ); ?>" <?php echo !empty( $required_out ) ? wp_kses_post( $required_out ) : ''; ?> data-id="<?php echo esc_attr( $field_id ); ?>">
			<div class="multi-check-wrap">
				
				<?php if( isset( $config['title'] ) && !empty( $config['title'] ) ): ?><label class="customize-control-title"><?php echo esc_html( $config['title'] ); ?></label><?php endif; ?>
				<?php if( isset( $config['description'] ) && !empty( $config['description'] ) ): ?><span class="description customize-control-description"><?php echo wp_kses_post( $config['description'] ); ?></span><?php endif; ?>
				
				<div class="multi-check-inner">
					<ul class="wp-multi-check-list">
					<?php
						if( $mc_items ){
							foreach( $mc_items as $key => $value ){
								$checked = !empty( $mc_ele ) && is_array( $mc_ele ) && in_array( $key, $mc_ele ) ? true : false;
								echo '<li>';
								echo '<div class="pmaf-checkbox-wrap">
				
									<div class="pmaf-checkbox multi-checkbox path">
										<input type="checkbox" '. checked( $checked, true, false ) .' value="'. esc_attr( $key ) .'">
										<svg viewBox="0 0 21 21">
											<path d="M5,10.75 L8.5,14.25 L19.4,2.3 C18.8333333,1.43333333 18.0333333,1 17,1 L4,1 C2.35,1 1,2.35 1,4 L1,17 C1,18.65 2.35,20 4,20 L17,20 C18.65,20 20,18.65 20,17 L20,7.99769186"></path>
										</svg>
									</div>
									<label class="customize-control-title">'. esc_html( $value ) .'</label>							
									<input type="hidden" class="pmaf-control-hidden-val" name="pmaf_alf_options['. esc_attr( $field_id ) .'][]" value="'. ( $checked ? esc_attr( $key ) : '' ) .'">
								</div>';
								echo '</li>';
							}
						}
					?>
					</ul>					
				</div>
			</div>
		</div>
	<?php
	}
	
	public static function pmaf_radio_image_field( $config ){ 
		$pmaf_alf_options = self::$pmaf_alf_options;
		$field_id = $config['id'];
		
		
		$saved_val = '';
		if( isset( $pmaf_alf_options[$field_id] ) ){
			$saved_val = stripslashes( $pmaf_alf_options[$field_id] );
		}else{
			$saved_val = isset( $config['default'] ) ? $config['default'] : '';
		}
		
		$ri_ele = $saved_val; 
		$ri_items = isset( $config['items'] ) ? $config['items'] : '';;
		$classes = isset( $config['cols'] ) && !empty( $config['cols'] ) ? ' image-col-'. $config['cols'] : ' image-col-3';
		
		$required = isset( $config['required'] ) ? $config['required'] : '';
		$required_out = $required_class = '';
		$required_class = isset( $config['custom_class'] ) ? $config['custom_class'] : '';
		if( $required ){
			$required_class = ' pmaf-customize-required';
			$req_value = is_array( $required ) && isset( $required[2] ) && !empty( $required[2] )  ? implode( ",", $required[2] ) : '';
			$required_out .= 'data-required="'. $required[0] .'" data-required-cond="'. $required[1] .'"  data-required-val="'. $req_value .'" ';
		}
		?>
		<div class="pmaf-control<?php echo esc_attr( $required_class ); ?>" data-field-type="radio-image" <?php echo !empty( $required_out ) ? wp_kses_post( $required_out ) : ''; ?> data-id="<?php echo esc_attr( $field_id ); ?>" data-id="<?php echo esc_attr( $field_id ); ?>">
			<div class="radio-image-wrap<?php echo esc_attr( $classes ); ?>">
				
				<?php if( isset( $config['title'] ) && !empty( $config['title'] ) ): ?><label class="customize-control-title"><?php echo esc_html( $config['title'] ); ?></label><?php endif; ?>
				<?php if( isset( $config['description'] ) && !empty( $config['description'] ) ): ?><span class="description customize-control-description"><?php echo wp_kses_post( $config['description'] ); ?></span><?php endif; ?>
				
				<div class="radio-image-inner">
					<ul class="wp-radio-image-list">
					<?php
						if( $ri_items ){
							foreach( $ri_items as $key => $img ){
								$checked = !empty( $ri_ele ) && $key == $ri_ele ? " checked" : "";
								echo '<li><input type="radio" name="pmaf_alf_options['. esc_attr( $field_id ) .']" value="'. esc_attr( $key ) .'" '. esc_attr( $checked ) .' /><span class="wp-radio-image-field"><img alt="'. esc_attr( $key ) .'" src="'. esc_url( $img['url'] ) .'" /></span><span class="wp-color-info">'. esc_html( $img['title'] ) .'</span></li>';
							}
						}
					?>
					</ul>					
				</div>
				<input type="hidden" class="pmaf-control-hidden-val" value="<?php echo esc_attr( $ri_ele ); ?>" />
			</div>
		</div>
	<?php
	}
	
	public static function pmaf_checkbox_field( $config ){ 
		$pmaf_alf_options = self::$pmaf_alf_options;
		$field_id = $config['id'];
		
		$saved_val = '';
		if( isset( $pmaf_alf_options[$field_id] ) ){
			$saved_val = $pmaf_alf_options[$field_id] == 1 ? true : false;
		}else{
			$saved_val = isset( $config['default'] ) ? $config['default'] : 0;
		}
		
		$required = isset( $config['required'] ) ? $config['required'] : '';
		$required_out = $required_class = '';
		$required_class = isset( $config['custom_class'] ) ? $config['custom_class'] : '';	
		if( $required ){
			$required_class = ' pmaf-customize-required';
			$req_value = is_array( $required ) && isset( $required[2] ) && !empty( $required[2] )  ? implode( ",", $required[2] ) : 0;
			$required_out .= 'data-required="'. $required[0] .'" data-required-cond="'. $required[1] .'"  data-required-val="'. $req_value .'" ';
		}
		
	?>
		<div class="pmaf-control<?php echo esc_attr( $required_class ); ?>" <?php echo !empty( $required_out ) ? wp_kses_post( $required_out ) : ''; ?> data-field-type="checkbox" data-id="<?php echo esc_attr( $field_id ); ?>" data-id="<?php echo esc_attr( $field_id ); ?>">
			<div class="pmaf-checkbox-wrap">
				
				<div class="pmaf-checkbox path">
					<input type="checkbox" <?php checked( $saved_val ); ?>>
					<svg viewBox="0 0 21 21">
						<path d="M5,10.75 L8.5,14.25 L19.4,2.3 C18.8333333,1.43333333 18.0333333,1 17,1 L4,1 C2.35,1 1,2.35 1,4 L1,17 C1,18.65 2.35,20 4,20 L17,20 C18.65,20 20,18.65 20,17 L20,7.99769186"></path>
					</svg>
				</div>
				
				<?php if( isset( $config['title'] ) && !empty( $config['title'] ) ): ?><label class="customize-control-title"><?php echo esc_html( $config['title'] ); ?></label><?php endif; ?>
				<?php if( isset( $config['description'] ) && !empty( $config['description'] ) ): ?><span class="description customize-control-description"><?php echo wp_kses_post( $config['description'] ); ?></span><?php endif; ?>
				
				<input type="hidden" class="pmaf-control-hidden-val" name="pmaf_alf_options[<?php echo esc_attr( $field_id ); ?>]" value="<?php echo esc_attr( $saved_val ); ?>">
			</div>
		</div>
	<?php
	}
		
	public static function pmaf_toggle_switch_field( $config ){ 
		$pmaf_alf_options = self::$pmaf_alf_options;
		$field_id = $config['id'];
		
		$saved_val = '';
		if( isset( $pmaf_alf_options[$field_id] ) ){
			$saved_val = $pmaf_alf_options[$field_id] == 1 ? true : false;
		}else{
			$saved_val = isset( $config['default'] ) ? $config['default'] : 0;
		}
		
		$required = isset( $config['required'] ) ? $config['required'] : '';
		$required_out = $required_class = '';
		$required_class = isset( $config['custom_class'] ) ? $config['custom_class'] : '';	
		if( $required ){
			$required_class = ' pmaf-customize-required';
			$req_value = is_array( $required ) && isset( $required[2] ) && !empty( $required[2] )  ? implode( ",", $required[2] ) : 0;
			$required_out .= 'data-required="'. $required[0] .'" data-required-cond="'. $required[1] .'"  data-required-val="'. $req_value .'" ';
		}
		
		$enable_label = isset( $config['enable_label'] ) ? $config['enable_label'] : '';
		$disable_label = isset( $config['disable_label'] ) ? $config['disable_label'] : '';
		
	?>
		<div class="pmaf-control pmaf-toggle-control<?php echo esc_attr( $required_class ); ?>" <?php echo !empty( $required_out ) ? wp_kses_post( $required_out ) : ''; ?> data-field-type="checkbox" data-id="<?php echo esc_attr( $field_id ); ?>" data-id="<?php echo esc_attr( $field_id ); ?>">
			<?php if( $enable_label ) echo '<strong class="pmaf-enabled-label">'. esc_html( $enable_label ) .'</strong>'; ?>
			<div class="checkbox_switch">
				<?php if( isset( $config['title'] ) && !empty( $config['title'] ) ): ?><label class="customize-control-title"><?php echo esc_html( $config['title'] ); ?></label><?php endif; ?>
				<?php if( isset( $config['description'] ) && !empty( $config['description'] ) ): ?><span class="description customize-control-description"><?php echo wp_kses_post( $config['description'] ); ?></span><?php endif; ?>
				<div class="pmaf-switch">
					<input type="checkbox" class="onoffswitch-checkbox" <?php checked( $saved_val ); ?>>
					<span class="slider round"></span>
				</div>
				<input type="hidden" class="pmaf-control-hidden-val" name="pmaf_alf_options[<?php echo esc_attr( $field_id ); ?>]" value="<?php echo esc_attr( $saved_val ); ?>">
			</div>
			<?php if( $disable_label ) echo '<strong class="pmaf-disabled-label">'. esc_html( $disable_label ) .'</strong>'; ?>
		</div>
	<?php
	}
	
	public static function pmaf_put_section(){
		echo self::$tab_list;
	}
	
	public static function pmaf_put_field(){
		echo self::$tab_content;
	}
	
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
}
PMAF_Animated_Forms_Options::instance();