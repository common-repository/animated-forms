/*
 * Auto Rank Options Script
 */ 

(function( $ ) {

	"use strict";
	var font_key = '';
	var _last_select_val = '';
			  
	$( document ).ready(function() {
		
		if( $(".wpap-grid-card").length ) {
			//$(".wpap-grid-card a").on( "click", function(e){
				
				//e.preventDefault();
				
				$(".wpap-grid-card a").magnificPopup({
					type: 'inline',
					preloader: false,
					// When elemened is focused, some mobile browsers in some cases zoom in
					// It looks not nice, so we disable it:
					callbacks: {
						/*beforeOpen: function() {
							if($(window).width() < 700) {
								this.st.focus = false;
							} else {
								this.st.focus = '#name';
							}
						}*/
						open: function(){
							let btn_html = '<a href="#" class="pmaf-save-individual-settings btn-primary">'+ animated_login_forms_obj.strings.save_btn_txt +'</a>';
							if( !this.wrap.find(".mfp-content .pmaf-save-individual-settings").length ){
								this.wrap.find(".mfp-content > div").append(btn_html);
							}
						}
					}
				});
				
				$(document).on( "click", ".pmaf-save-individual-settings", function(){
								$.magnificPopup.close();
							});
			//});
		}	
		
		if( $("#pmaf-plugin-form-wrapper").length ){
			var sticky_ele = $("#pmaf-plugin-form-wrapper .pmaf-header-right .button");
			var btn_x_offset = $(sticky_ele).offset().left;
			var pos_top = $(sticky_ele).offset().top;
			$(window).scroll(function() {
				var win_top = $(window).scrollTop();
				if( pos_top < win_top ) {
					$(sticky_ele).addClass( "option-btn-fixed" );
					$(sticky_ele).css({ 'left' : btn_x_offset + 'px' });
				} else {
					$(sticky_ele).removeClass( "option-btn-fixed" );
					$(sticky_ele).css({ 'left' : 'auto' });
				}
			});
		}		
				
		//WP Color Picker
		$( ".wp-font-color-field" ).wpColorPicker();
		
		if( $(".pmaf-select2").length ){
			$(".pmaf-select2").each(function(){
				let cur_s = $(this);
				let _saved_val = $(cur_s).data("select-2");
				$(cur_s).val(_saved_val);
				$(cur_s).select2();
			});			
		}
						
		if( $(".wp-radio-image-list").length ){
			$('body').on( 'click', '.wp-radio-image-list input[type="radio"]', function(e){ 
				$(this).parents(".radio-image-wrap").find(".pmaf-control-hidden-val").val($(this).val());
				customizer_required_settings();
			});
		}	
		
		if( $(".pmaf-switch").length ){
			$('body').on( 'change', '.pmaf-switch input[type="checkbox"]', function(e){ 
				var sel_val = $(this).is(":checked") ? 1 : 0;
				$(this).parents(".checkbox_switch").find(".pmaf-control-hidden-val").val(sel_val);
				customizer_required_settings();
			});
		}
		
		if( $(".pmaf-checkbox").length ){
			$('body').on( 'change', '.pmaf-checkbox:not(.multi-checkbox) input[type="checkbox"]', function(e){ 
				var sel_val = $(this).is(":checked") ? 1 : 0;
				$(this).parents(".pmaf-checkbox-wrap").find(".pmaf-control-hidden-val").val(sel_val);
				customizer_required_settings();
			});
			
			$('body').on( 'change', '.pmaf-checkbox.multi-checkbox input[type="checkbox"]', function(e){ 
				var sel_val = $(this).is(":checked") ? $(this).val() : '';
				$(this).parents(".pmaf-checkbox-wrap").find(".pmaf-control-hidden-val").val(sel_val);
				customizer_required_settings();
			});
		}

		if( $(".pmaf-customizer-select-field").length ){
			$('body').on( 'change', '.pmaf-customizer-select-field', function(e){ 
				customizer_required_settings();
			});
		}
		
		/*$('.pmaf-select2').on('', function (e) {
			var data = e.params.data; console.log( data );
			customizer_required_settings();
		});*/
		
		$('.pmaf-select2').on('select2:select select2:unselect', function (e) {
			var data = e.params.data; 
			let _existing_data = $(this).data("select-2"); //console.log( data );
			if( data.selected ) {
				_existing_data.push(data.id);
			} else {
				_existing_data = jQuery.grep(_existing_data, function(value) {
				  return value != data.id;
				});
			}
			_last_select_val = _existing_data;
			customizer_required_settings();
		});
		
		//custom code
		if( $('.pmaf-select2[name="pmaf_alf_options[post_type][]"]').length ) { 
			_last_select_val = $('.pmaf-select2[name="pmaf_alf_options[post_type][]"]').data("select-2");
			customizer_required_settings();
		}
		
		if( $('.pmaf-save-settings').length ){
			$('.pmaf-save-settings').on( "click", function ( e ) {
				e.preventDefault();
				let _cur_ele = $(this);
				_cur_ele.addClass("processing");
				$.ajax({
					type: "POST",
					url: ajaxurl,
					data: $("#pmaf-settings-form").serialize(),
					success: function (data) {
						if( data.status == 'success' ){
							_cur_ele.addClass("success");
							setTimeout( function(){ _cur_ele.removeClass("success"); }, 3000 );
						}
					},error: function(xhr, status, error) {
						console.log("failed");						
					}, complete: function () {
						_cur_ele.removeClass("processing");
					}
				});
			});
		}
				
	});
	
	$( window ).load(function() {
	
		if( $(".pmaf-tab").length ){

			$(".pmaf-tab-list > li").click(function() {
				
				let cur_ele = $(this);
			
				var last_tab = ''; 
				if( $(".pmaf-settings-wrap").length ){
					last_tab = animated_login_forms_get_cookie("animated_login_forms_admin_tab");
					last_tab = last_tab ? last_tab : 'pmaf-general-settings'; 
				}else{				
					last_tab = animated_login_forms_get_cookie("animated_login_forms_singular_admin_tab");
					last_tab = last_tab ? last_tab : ''; 
				}
				if( !last_tab ) last_tab = 'pmaf-general-settings';
				
				$('.pmaf-tab-list > li').removeClass("active");
				$('.pmaf-tab-list > li[data-id="'+ cur_ele.attr("data-id") +'"]').addClass("active");
				
				$('.pmaf-tab .tabcontent').addClass("tab-hide").fadeOut(0);
				$( '#' + cur_ele.attr("data-id") ).fadeIn(200);
				customizer_required_settings_specific( '#' + cur_ele.attr("data-id") );
				
				if( $(".pmaf-settings-wrap").length ){
					animated_login_forms_set_cookie( "animated_login_forms_admin_tab", cur_ele.attr("data-id"), 1 );					
				}else{
					animated_login_forms_set_cookie( "animated_login_forms_singular_admin_tab", cur_ele.attr("data-id"), 1 );
				}
			});
			
			// Auto trigger at first
			var last_tab = ''; 
			if( $(".pmaf-settings-wrap").length ){
				last_tab = animated_login_forms_get_cookie("animated_login_forms_admin_tab"); 
				last_tab = last_tab ? last_tab : 'pmaf-general-settings'; 
			}else{				
				last_tab = animated_login_forms_get_cookie("animated_login_forms_singular_admin_tab");
				last_tab = last_tab ? last_tab : ''; 
			}
			if( last_tab ){				
				$('.pmaf-tab-list > li[data-id="'+ last_tab +'"]').trigger("click");
			}else{
				$('.pmaf-tab-list > li:first-child').trigger("click");
			}
			
			// check custom functions
			/*if( $(document).find(".pmaf-char-count").length ) {
				$(document).find(".pmaf-char-count").each(function( index ) {
					$('input[data-key="pmaf_alf_options[home-meta-title]"]')
				});
			}*/
			
		}
		
	});
		
	// New required code start
	function customizer_required_settings(){ 
		var find_ele = '.pmaf-control.pmaf-customize-required';
		var req_parent = $(document).find('.tabcontent');
		
		if( $(req_parent).find(find_ele).length ){
			$(req_parent).find(find_ele).each(function( index ) {
				animated_login_forms_check_required( $(this) );
			});
		}
	}

	function customizer_required_settings_specific( req_parent ){
		var find_ele = '.pmaf-control.pmaf-customize-required';
		$(req_parent).find(find_ele).each(function( index ) {
			animated_login_forms_check_required( $(this) );
		});
	}
		
	function animated_login_forms_check_required( ele ){
		var req_parent_id = $(ele).attr("data-required");
		//var data_id = $(ele).attr("data-id");
		if( $('.pmaf-control[data-id="'+ req_parent_id +'"]').attr("data-stat") == "0" ){
			$(ele).attr("data-stat", "0");
			$(ele).fadeOut(0);
		}else{
			var req_parent = $('.pmaf-control[data-id="'+ req_parent_id +'"]');
			var sel_val = animated_login_forms_get_parent_sel_val( req_parent );
			animated_login_forms_show_hide_customizer_fields( sel_val, ele );
		}		
	}
	function animated_login_forms_get_parent_sel_val( req_parent ){
		var field_type = $(req_parent).attr('data-field-type');
		var sel_val = '';
		if( field_type == 'checkbox' ){
			sel_val = $(req_parent).find(".pmaf-control-hidden-val").val() == '1' ? 'true' : 'false';
		}else if( field_type == 'select' ){
			if( $(req_parent).find('.pmaf-select2').length ) {
				sel_val = _last_select_val; //$(req_parent).find('.pmaf-select2').data("select-2"); //console.log(sel_val);				
			} else {
				sel_val = $(req_parent).find('select.pmaf-customizer-select-field').val();
			}
		}else if( field_type == 'radio-image' ){
			sel_val = $(req_parent).find('.pmaf-control-hidden-val').val();
		}
		return sel_val;
	}
	function animated_login_forms_show_hide_customizer_fields( sel_val, field ){
		var req_val = $(field).attr("data-required-val");
		var req_cond = $(field).attr("data-required-cond");
		
		if( req_cond == '=' ){
			if( sel_val ){
				var req_val_arr = req_val.split(",");
				/*$.each( sel_val_arr, function( index, value ){
					if( value == req_val ){ 
						$(field).fadeIn(0); $(field).attr("data-stat", "1");
					}else{
						$(field).fadeOut(0); $(field).attr("data-stat", "0");
					}
				});*/
				//console.log(sel_val); console.log(req_val_arr);
				//console.log(req_val_arr.indexOf( sel_val ));
				if( req_val_arr.indexOf( sel_val ) != -1 ){
					$(field).fadeIn(0); $(field).attr("data-stat", "1");
				}else{
					$(field).fadeOut(0); $(field).attr("data-stat", "0");
				}				
			}			
		}else if( req_cond == '!=' ){
			if( sel_val != req_val ){
				$(field).fadeIn(0); $(field).attr("data-stat", "1");
			}else{
				$(field).fadeOut(0); $(field).attr("data-stat", "0");
			}
		}else if( req_cond == 'IN' && _last_select_val != '' ){ 
			if( $.inArray(req_val, sel_val) != -1 ){
				$(field).fadeIn(0); $(field).attr("data-stat", "1");
			}else{
				$(field).fadeOut(0); $(field).attr("data-stat", "0");
			}
		}
		//_last_select_val = '';
	}
	function animated_login_forms_get_cookie(cname) {
		let name = cname + "=";
		let decodedCookie = decodeURIComponent(document.cookie);
		let ca = decodedCookie.split(';');
		for(let i = 0; i <ca.length; i++) {
			let c = ca[i];
			while (c.charAt(0) == ' ') {
				c = c.substring(1);
			}
			if (c.indexOf(name) == 0) {
				return c.substring(name.length, c.length);
			}
		}
		return "";
	}	
	function animated_login_forms_set_cookie(cname, cvalue, exdays) {
		const d = new Date();
		d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
		let expires = "expires="+d.toUTCString();
		document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
	}
	
	// On load required check
	customizer_required_settings();
	
})( jQuery );

/*function tabProcess( e, tab_id ){
	
	tablinks = document.getElementsByClassName("tabcontent");
	for (i = 0; i < tablinks.length; i++) {
		document.getElementById(tablinks[i].getAttribute("id")).style.display = "none";
	}
	
	document.getElementById(tab_id).style.display = "block";
}*/