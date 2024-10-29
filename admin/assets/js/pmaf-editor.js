/*
 * Animated Forms Editor JS
 */ 

(function( $ ) {

	"use strict";
	
	var _pmaf_index = 1;
	var _pmaf_field_values = pmaf_obj.form_data ? pmaf_obj.form_data : {};
	var _pmaf_current_field = '';	
	var _pmaf_settings = pmaf_obj.form_settings ? pmaf_obj.form_settings : {};
	var _pmaf_ani_settings = pmaf_obj.form_ani_settings ? pmaf_obj.form_ani_settings : {};
	if( !_pmaf_ani_settings.hasOwnProperty("overlay") ) {
		_pmaf_ani_settings['overlay'] = {"html":"","style":"","name":""};
	}

	var _tmp_pmaf_ani_settings = '';
			  
	$( document ).ready(function() {
				
		if( $(".pmaf-campaign-import-form").length ) {			
			// call import function
			pmaf_import();
		}
		
		if( $(".pmaf-edtior-wrap, .pmaf-field-editor-wrap").length ) {
			$("body").addClass("pmaf-overflow-hide");
		}
		
		if( $(".pmaf-filter-list").length ) {
			$(".pmaf-filter-bulk-operations").on("change", function(){
				let _bulk = $(this).val();
				
				if( _bulk != 'none' ) {
					$(".pmaf-posts-list").addClass("bulk-active");
				}
				
				if( _bulk == 'select-all' ) {
					$('.pmaf-bulk-select input[type="checkbox"]').attr("checked", "checked");					
				} else if( _bulk == 'unselect-all' ) {
					$('.pmaf-bulk-select input[type="checkbox"]').removeAttr("checked", "checked");
				} else if( _bulk == 'none' ) {
					$(".pmaf-posts-list").removeClass("bulk-active");
				}
				
				/*if( _bulk != 'none' ) {
					console.log( $('.pmaf-bulk-select input').val() );
				}*/
				
			});
			
			$(".pmaf-filter-bulk-process").on("change", function(){
				let _bulk = $(".pmaf-filter-bulk-operations").val();
				let _process = $(this).val();
				if( _bulk != 'none' ) {
					if( _process == 'delete' ) {						
						$.confirm({
							title: 'Delete Confirmation!',
							content: 'Are you sure want to delete these forms?',
							buttons: {
								confirm: function () {
									let _forms = [];
									$("input:checkbox[name=pmaf]:checked").each(function() {
										_forms.push($(this).val());
									});
									pmaf_forms_delete( _forms, false );
								},
								cancel: function(){
									$(".pmaf-filter-bulk-process").val('none');
								}
							}
						});						
					} else if( _process == 'export' ) {
						let _forms = [];
						$("input:checkbox[name=pmaf]:checked").each(function() {
							_forms.push($(this).val());
						}); 
						pmaf_forms_export(_forms);
					}
				}
			});
			
		}
		
		$(".pmaf-shortcode-copy").on("click", function(e){
			e.preventDefault();
			let _ele = $(this);
			pmaf_copy_to_clipboard( _ele );
			$(_ele).addClass("copied");
			setTimeout(function(){ $(_ele).removeClass("copied"); }, 1000);
			return false;
		});
		$(".pmaf-shortcode-copy-parent > i").on("click", function(e){
			e.preventDefault();
			$(this).prev("a").trigger("click");
			return false;
		});
		
		$(".pmaf-toolbar .pmaf-form-title").on("click", function(e){
			e.preventDefault();
			if( $(document).find(".pmaf-field-editor-content-wrap").hasClass("pmaf-full-preview") ) {
				$(document).find(".pmaf-field-toggle").trigger("click");
			}
			$(document).find('.pmaf-editor-common-controls a[href="#pmaf-form-settings"]').trigger("click");
			if( $(document).find(".pmaf-animate-notification").hasClass("active") ) {
				$(document).find(".pmaf-animate-settings-general").trigger("click");
			}
			$(document).find('.pmaf-form-basic-settings input[name="form_name"]').focus();
			return false;
		});
		
		$(".pmaf-single-item-export").on("click", function(e){
			e.preventDefault();
			let _ele = $(this);
			let _forms = [_ele.data("id")];
			pmaf_forms_export(_forms);
			return false;
		});
		
		$(".pmaf-single-item-delete").on("click", function(e){
			e.preventDefault();
			let _ele = $(this);
			let _forms = [_ele.data("id")];
			_ele.html('Deleting...');
			pmaf_forms_delete(_forms, true);
			return false;
		});
		
		$(".pmaf-single-status-change > input").on("change", function(e){
			e.preventDefault();
			let _ele = $(this); let _stat = false;
			if( _ele.is(":checked") ) _stat = true;
			let _forms = [_ele.data("id")];
			pmaf_forms_status_change( _forms, _stat );
			return false;
		});
		
		if( $(".pmaf-create-form").length ) {
			$(".pmaf-create-form").on("click", function(e){
				e.preventDefault();
				
				$(".pmaf-wrap-inner").addClass("processing");
				
				// create nre form
				$.ajax({
					type: "POST",
					url: ajaxurl,
					data: {
						action: 'pmaf_new_form',
						nonce: pmaf_obj.nonce.new_form
					},
					success: function( data ) { 
						if( data.status == 'success' ) {
							//console.log(data.redirect);
							window.location.href = data.redirect;
						}
					},error: function(xhr, status, error) {
						console.log("failed");						
					},complete: function(){
						$(".pmaf-wrap-inner").removeClass("processing");
					}
				});
				
				return false;
			});
		}
		
		// admin form basic settings
		pmaf_form_settings_config();
		
		// admin form animation settings
		pmaf_form_animation_settings_config();
		
		pmaf_sub_tab_process();
		$(".pmaf-editor-common-controls li a").on("click", function(e){
			e.preventDefault();			
			if( !$(this).hasClass("active") ) {
				if( $(this).hasClass("pmaf-add-new") ) {
					$.confirm({
						title: 'Discard the changes?',
						content: 'Are you sure want to discard the form editing?',
						buttons: {
							confirm: function () {
								window.location.href = pmaf_obj.new_form;
							},
							cancel: function(){}
						}
					});
				} else if( $(this).hasClass("pmaf-get-pro") ) {
					window.open($(this).attr("href"), "_blank");
				} else {
					if( $(".pmaf-editor-options").hasClass("active") ) {
						$(".pmaf-editor-options").removeClass("active");
					}
					$(".pmaf-editor-common-controls a.active, .pmaf-main-tab-content.active").removeClass("active");
					$(this).addClass("active");
					$($(this).attr("href")).addClass("active");
					
					pmaf_sub_tab_process();

				}
			} else {
				if( $(this).attr("href") == "#pmaf-editor-controls" ) {
					$(".pmaf-editor-options").removeClass("active");
				}
			}
			return false;
		});
		
		$("a.pmaf-animate-settings-general").on("click", function(e){
			e.preventDefault();	
			$(".pmaf-inner-header a").removeClass("active");
			$(this).addClass("active");
			$(".pmaf-animate-options-2").hide();
			$(".pmaf-animate-options-1").fadeIn(350);
			return false;
		});
		$("a.pmaf-animate-notification").on("click", function(e){
			e.preventDefault();	
			$(".pmaf-inner-header a").removeClass("active");
			$(this).addClass("active");
			$(".pmaf-animate-options-1").hide();
			$(".pmaf-animate-options-2").fadeIn(350);
			return false;
		});
						
		// sortable
		$( "#pmaf-sortable" ).sortable({
			revert: true,
			placeholder: "ui-state-highlight",
		});
		
		// draggable
		$( ".pmaf-draggable > li" ).draggable({
			connectToSortable: "#pmaf-sortable",
			helper: "clone",
			revert: "invalid",
			stop: function( event, ui ) {
				if( $(document).find(".pmaf-preview-fields .no-fields-preview").length ) {
					$(document).find(".pmaf-preview-fields .no-fields-preview").remove();
					$(document).find("#pmaf-sortable").addClass("pmaf-fields-exists");
				}
				let _cur_ele = $(ui.helper);
				if( $(_cur_ele).parent("#pmaf-sortable").length ) {
					let _field_type = $(_cur_ele).data("type");
					
					let _default_value = pmaf_single_field_config( _field_type );
					_pmaf_current_field = 'fi_'+ _pmaf_index;
					_pmaf_field_values[_pmaf_current_field] = _default_value;
					_pmaf_field_values[_pmaf_current_field].name = 'field_fi_'+ _pmaf_index;					
					
					let _field_html = pmaf_create_field( _field_type, _default_value );
					$(_cur_ele).html(_field_html);
					$(_cur_ele).attr("data-field-id", _pmaf_current_field);
					
					pmaf_update_preview();
					_pmaf_index++;
					
					// field option events
					pmaf_field_options_events();
					
				}
			}
		});
		
		// check saved value exists
		if( Object.keys(_pmaf_field_values).length !== 0 ) {
			
			// set saved form fields
			pmaf_create_saved_fields();
			
			// field option events
			pmaf_field_options_events();
			
			$(document).find("#pmaf-sortable").addClass("pmaf-fields-exists");
			
		} else {
			$(".pmaf-preview-fields").prepend('<div class="no-fields-preview">'+ pmaf_arrow_svg() + pmaf_obj.strings.empty_form +'</div>');
			$(document).find("#pmaf-sortable").removeClass("pmaf-fields-exists");
		}
		
		// save form 
		$("a#pmaf-save-form").on("click", function(e){
			e.preventDefault();
						
			$(".pmaf-preview-trigger").trigger("click");
			
			let _cur_ele = $(this);
			$(_cur_ele).addClass("processing");
			
			let _fields_order = pmaf_get_fields_order();
			
			if( $(document).find(".pmaf-all-form-preview").length ) {
				$(".pmaf-all-form-preview").addClass("loading");
			}
						
			// format the animation settings values before save
			pmaf_format_settings_for_save(); //return false;
			
			// save by ajax
			$.ajax({
				type: "POST",
				url: ajaxurl,
				data: {
					action: 'pmaf_save_form',
					nonce: pmaf_obj.nonce.save_form,
					af: _pmaf_field_values,
					id: pmaf_obj.post_id,
					order: _fields_order,
					settings: _pmaf_settings,
					ani_settings: _pmaf_ani_settings
				},
				success: function( data ) { 
					if( $(document).find(".pmaf-all-form-preview").length ) {
						let _preview_src = $(document).find(".pmaf-all-form-preview iframe").attr("src");
						$(document).find(".pmaf-all-form-preview iframe").attr("src", _preview_src);
					}
				},error: function(xhr, status, error) {
					console.log("failed");						
				},complete: function(){
					$(_cur_ele).removeClass("processing");
				}
			});
			
			return false;
		});
		
		// import background templates wrapper
		$(".pmaf-choose-bg-animation").on("click", function(e){
			e.preventDefault();
			$(".pmaf-animation-templates").addClass("active");
			$("body").addClass("pmaf-overflow-hide");

			pmaf_search_animated_template_fun();
			
			return false;
		});
		
		// import animate packs 
		$(".pmaf-import-animate-pack").on("click", function(e){
			e.preventDefault();
			$(".pmaf-animate-pack-templates").addClass("active");
			$("body").addClass("pmaf-overflow-hide");

			pmaf_search_animate_pack_fun();
			
			return false;
		});
		
		// import overlay templates wrapper
		$(".pmaf-choose-overlay").on("click", function(e){
			e.preventDefault();
			$(".pmaf-overlay-templates").addClass("active");
			$("body").addClass("pmaf-overflow-hide");
			
			pmaf_search_overlay_template_fun();
			
			return false;
		});
		
		// import form inner templates
		$(".pmaf-choose-form-styles").on("click", function(e){
			e.preventDefault();
			$(".pmaf-fi-templates").addClass("active");
			$("body").addClass("pmaf-overflow-hide");
			
			pmaf_search_form_inner_template_fun();
			
			return false;
		});
		
		// close templates wrapper
		$(".pmaf-templates-close").on("click", function(e){
			e.preventDefault();
			if( $(".pmaf-animate-pack-templates").hasClass("active") ) {
				$(".pmaf-animate-pack-templates").removeClass("active");
				if( $(".pmaf-editor-common-controls > li:nth-child(2) > a:not(.pmaf-import-animate-pack)").length ) {
					$(".pmaf-editor-common-controls > li:nth-child(2) > a:not(.pmaf-import-animate-pack)").trigger("click");					
				} else {
					$(".pmaf-editor-common-controls > li:nth-child(3) > a").trigger("click");					
				}
			} else {
				$(".pmaf-animation-templates, .pmaf-overlay-templates, .pmaf-animate-pack-templates, .pmaf-fi-templates").removeClass("active");
			}
			//$("body").removeClass("pmaf-overflow-hide");
			return false;
		});
				
		// bg image import
		$(document).find(".pmaf-template-import").on("click", function(e){
			e.preventDefault();
			let _ele = $(this);
			_ele.parents(".pmaf-template-item").addClass("importing");
			
			// save by ajax
			$.ajax({
				type: "POST",
				url: ajaxurl,
				data: {
					action: 'pmaf_get_attachments',
					nonce: pmaf_obj.nonce.import_attachments,
					attachment: _ele.data("id"),
					id: pmaf_obj.post_id,
				},
				success: function( data ) { 
					if( data.status == 'success' ) {
						_pmaf_ani_settings['outer']['bg']['image'] = data.attachment;
						let attachment = data.attachment;
						$(document).find(".pmaf-bg-preview").remove();
						$('<div class="pmaf-bg-preview"><img src="'+ attachment.url + '" class="'+ attachment.id +'" /><span class="pmaf-bg-img-remove"><i class="dashicons dashicons-no-alt"></i></span></div>').insertAfter($(".pmaf-bg-set"));
						pmaf_bg_img_remove();
						_ele.parents(".pmaf-template-item").addClass("done");
						setTimeout( function(){
							_ele.parents(".pmaf-template-item").removeClass("done");
							$(".pmaf-animation-templates").toggleClass("active");
							
							$(".pmaf-preview-trigger").trigger("click");
							
							pmaf_instant_result();
							//$(document).find("a#pmaf-save-form").trigger("click");
							
						}, 2000 );
					}
				},error: function(xhr, status, error) {
					console.log("failed");						
				},complete: function(){
					_ele.parents(".pmaf-template-item").removeClass("importing");
					//$("body").removeClass("pmaf-overflow-hide");
				}
			});
			
			return false;
		});
		
		// pro popup
		$(document).find(".pmaf-pro-popup").on("click", function(e){
			e.preventDefault();
			
			let _template_title = $(this).parents(".pmaf-template-item").find("h3").text();
			let _demo_url = $(this).next("a").attr("href");
			
			$.confirm({
				//icon: 'af-pro',
				theme: 'material',
				title: '<img class="pmaf-popup-logo" src="'+ pmaf_obj.logo +'"> Animated Forms Pro',
				content: '<strong>'+ _template_title + '</strong> template is not available in free version. Please upgrade to pro.',
				columnClass: 'pmaf-pro-popup-col',
				closeIcon: true,
				alignMiddle: true,
				buttons: {
					upgrade: {
						btnClass: 'pmaf-btn',
						text: 'Download Now',
						action: function () {
							window.open( 'https://plugin.net/items/animated-forms-pro/', '_blank' );
						}
					},
					demo: {
						btnClass: 'pmaf-btn',
						text: 'View Demo',
						action: function () {
							//console.log(_demo_url);
							window.open( _demo_url, '_blank' );
						}
					}
				},
				defaultButtons: {},
			});			
			
			return false;
		});
		
		// overlay templates import
		pmaf_overlay_remove();
		$(document).find(".pmaf-overlay-templates-import").on("click", function(e){
			e.preventDefault();
			let _ele = $(this);
			_ele.parents(".pmaf-template-item").addClass("importing");
			
			// save by ajax
			$.ajax({
				type: "POST",
				url: ajaxurl,
				data: {
					action: 'pmaf_overlay_import',
					nonce: pmaf_obj.nonce.import_overlay_templates,
					overlay: _ele.data("id"),
					id: pmaf_obj.post_id,
				},
				success: function( data ) { 
					if( data.status == 'success' ) {
						_pmaf_ani_settings.overlay.html = data.overlay;
						_pmaf_ani_settings.overlay.inner_html = data.inner_html;
						_pmaf_ani_settings.overlay.form_html = data.form_html;
						_pmaf_ani_settings.overlay.outer_html = data.outer_html;
						_pmaf_ani_settings.overlay.style = data.style;
						_pmaf_ani_settings.overlay.name = data.name;
						_pmaf_ani_settings.overlay.js = data.js;
						$(document).find(".pmaf-selected-overlay").remove();
						$(data.preview).insertAfter($(document).find(".pmaf-choose-overlay"));
						pmaf_overlay_remove();
						//console.log(_pmaf_ani_settings);
					}
				},error: function(xhr, status, error) {
					console.log("failed");						
				},complete: function(){
					_ele.parents(".pmaf-template-item").removeClass("importing");
					_ele.parents(".pmaf-template-item").addClass("done");
					setTimeout( function(){
						_ele.parents(".pmaf-template-item").removeClass("done");
						_ele.parents(".pmaf-template-item").removeClass("importing");
						$(document).find(".pmaf-overlay-templates").removeClass("active");						
						$(document).find("a#pmaf-save-form").trigger("click");
					}, 2000 );
				}
			});
			
			return false;
		});
		
		// form inner templates import
		pmaf_fi_remove();
		$(document).find(".pmaf-fi-templates-import").on("click", function(e){
			e.preventDefault();
			let _ele = $(this);
			_ele.parents(".pmaf-template-item").addClass("importing");	
			let _fi_id = $(this).data("id");
			_pmaf_ani_settings.inner = {model: _fi_id};	
			_ele.parents(".pmaf-template-item").removeClass("importing");
			
			$.ajax({
				type: "POST",
				url: ajaxurl,
				data: {
					action: 'pmaf_fi_get_selected',
					nonce: pmaf_obj.nonce.get_fi,
					fi: _fi_id
				},
				success: function( data ) { 
					if( data.status == 'success' ) {
						$(document).find(".pmaf-selected-fi").remove();						
						$(data.preview).insertAfter($(document).find(".pmaf-choose-form-styles"));
						pmaf_fi_remove();
					}
				},error: function(xhr, status, error) {
					console.log("failed");						
				},complete: function(){
					_ele.parents(".pmaf-template-item").removeClass("importing");
					_ele.parents(".pmaf-template-item").addClass("done");
					setTimeout( function(){
						_ele.parents(".pmaf-template-item").removeClass("done");
						$(document).find(".pmaf-fi-templates").removeClass("active");
						$(document).find("a#pmaf-save-form").trigger("click");
						//$("body").removeClass("pmaf-overflow-hide");
					}, 2000 );
				}
			});
			
			
			return false;
		});
		
		// import animation pack
		$(document).find(".pmaf-pack-import").on("click", function(e){
			e.preventDefault();
			let _ele = $(this);
			_ele.parents(".pmaf-template-item").addClass("importing");
			
			// save by ajax
			$.ajax({
				type: "POST",
				url: ajaxurl,
				data: {
					action: 'pmaf_get_pack',
					nonce: pmaf_obj.nonce.import_pack,
					bg: _ele.data("bg"),
					overlay: _ele.data("overlay"),
					fi: _ele.data("fi"),
					id: pmaf_obj.post_id,
				},
				success: function( data ) { 
					//console.log(data);
					if( data.status == 'success' ) {
						_pmaf_ani_settings['outer']['bg']['image'] = data.attachment;
						let attachment = data.attachment;
						$(document).find(".pmaf-bg-preview").remove();
						$('<div class="pmaf-bg-preview"><img src="'+ attachment.url + '" class="'+ attachment.id +'" /><span class="pmaf-bg-img-remove"><i class="dashicons dashicons-no-alt"></i></span></div>').insertAfter($(".pmaf-bg-set"));
						pmaf_bg_img_remove();
						
						_pmaf_ani_settings.overlay.html = data.overlay;
						_pmaf_ani_settings.overlay.inner_html = data.inner_html;
						_pmaf_ani_settings.overlay.form_html = data.form_html;
						_pmaf_ani_settings.overlay.outer_html = data.outer_html;
						_pmaf_ani_settings.overlay.style = data.style;
						_pmaf_ani_settings.overlay.name = data.name;
						_pmaf_ani_settings.overlay.js = data.js;
						
						if( _ele.data("fi") ) {
							_pmaf_ani_settings.inner = { model: _ele.data("fi") };
							$(document).find(".pmaf-selected-fi").remove();
							$(data.fi_preview).insertAfter($(document).find(".pmaf-choose-form-styles"));
							pmaf_fi_remove();
						}
						$(document).find(".pmaf-selected-overlay").remove();
						$(data.preview).insertAfter($(document).find(".pmaf-choose-overlay"));
						pmaf_overlay_remove();
						
						_ele.parents(".pmaf-template-item").addClass("done");
						setTimeout( function(){
							_ele.parents(".pmaf-template-item").removeClass("done");
							$("a.pmaf-import-animate-pack").removeClass("active");
							$(".pmaf-editor-common-controls > li:nth-child(2) > a").trigger("click");
							$(".pmaf-animate-pack-templates").toggleClass("active");
							$(".pmaf-animate-customizer").trigger("click");
							$(document).find("a#pmaf-save-form").trigger("click");
						}, 2000 );
					}
				},error: function(xhr, status, error) {
					console.log("failed");						
				},complete: function(){
					_ele.parents(".pmaf-template-item").removeClass("importing");
					//$("body").removeClass("pmaf-overflow-hide");
				}
			});
			
			return false;
		});
		
		$(document).find("#pmaf-form-templates-search").on("input", function(){
			if( $(this).val() != '' ) {
				pmaf_search_templates( 'pmaf-form-templates-search', 'pmaf-form-templates-list', 'pmaf-form-template' );
			}
		});
		
		$(document).find("#pmaf-form-fields-search").on("input", function(){
			if( $(this).val() != '' ) {
				$(".pmaf-form-fields-wrap h3").hide();
				pmaf_search_fields( 'pmaf-form-fields-search', 'pmaf-form-fields-wrap', 'ui-state-highlight' );
			} else {
				$(".pmaf-form-fields-wrap h3").show();
				$(".pmaf-form-fields-wrap .ui-state-highlight").show();
			}
		});
		
		$(document).find(".pmaf-form-templates-categories:not(.pmaf-animate-pack-categories) > li").on("click", function(){
			let _cur = $(this);
			$(document).find(".pmaf-form-templates-categories > li").removeClass("active");
			if( _cur.data("category") == 'all' ) {
				$("#pmaf-form-templates-list .pmaf-form-template").show();
			} else {
				pmaf_category_templates( 'pmaf-form-templates-search', 'pmaf-form-templates-list', 'pmaf-form-template', _cur.data("category") );
			}
			_cur.addClass("active");
		});
		
		$(document).find(".pmaf-animate-pack-categories > li").on("click", function(){
			let _cur = $(this);
			$(document).find(".pmaf-animate-pack-categories > li").removeClass("active");
			if( _cur.data("category") == 'all' ) {
				$(".pmaf-import-templates-list .pmaf-template-item").show();
			} else {
				pmaf_category_templates( 'pmaf-import-pack-search', 'pmaf-import-pack-list', 'pmaf-import-template', _cur.data("category") );
			}
			_cur.addClass("active");
		});
		
		// templates import
		$(document).find(".pmaf-from-templates-import").on("click", function(e){
			e.preventDefault();
			let _ele = $(this);
			$(".pmaf-loader").addClass("active");
			
			let _fname = $(document).find(".pmaf-form-name-wrap input").val();
			if( _fname == '' ) {
				_fname = _ele.parents(".pmaf-form-template").find(".pmaf-form-template-name").text();
			}
			
			// save by ajax
			$.ajax({
				type: "POST",
				url: ajaxurl,
				data: {
					action: 'pmaf_form_templates_import',
					nonce: pmaf_obj.nonce.import_templates,
					id: _ele.data("id"),
					name: _fname
				},
				success: function( data ) { 
					if( data.status == 'success' ) {
						window.location.href = data.redirect;
					}
				},error: function(xhr, status, error) {
					console.log("failed");						
				},complete: function(){
					//$(".pmaf-loader").removeClass("active");
				}
			});
			
			return false;
		});		
		
		// use overlay templates
		$(document).find(".pmaf-use-overlay-templates").on("click", function(e){
			e.preventDefault();
			let _ele = $(this);
			_ele.parents(".pmaf-template-item").addClass("importing");
			
			// save by ajax
			$.ajax({
				type: "POST",
				url: ajaxurl,
				data: {
					action: 'pmaf_use_overlay_template',
					nonce: pmaf_obj.nonce.import_overlay_templates,
					overlay: _ele.data("id"),
					id: pmaf_obj.post_id,
				},
				success: function( data ) { 
					if( data.status == 'success' ) {
						//_pmaf_ani_settings.overlay.html = data.overlay;
						//_pmaf_ani_settings.overlay.style = data.style;
						//_pmaf_ani_settings.overlay.name = data.name;
						
						_pmaf_ani_settings.overlay.html = data.overlay;
						_pmaf_ani_settings.overlay.inner_html = data.inner_html;
						_pmaf_ani_settings.overlay.form_html = data.form_html;
						_pmaf_ani_settings.overlay.outer_html = data.outer_html;
						_pmaf_ani_settings.overlay.style = data.style;
						_pmaf_ani_settings.overlay.name = data.name;
						_pmaf_ani_settings.overlay.js = data.js;
						
						$(document).find(".pmaf-selected-overlay").remove();
						$(data.preview).insertAfter($(document).find(".pmaf-choose-overlay"));
						pmaf_overlay_remove();
					}
				},error: function(xhr, status, error) {
					console.log("failed");						
				},complete: function(){
					_ele.parents(".pmaf-template-item").removeClass("importing");
					_ele.parents(".pmaf-template-item").addClass("done");
					setTimeout( function(){
						_ele.parents(".pmaf-template-item").removeClass("done");
						$(document).find(".pmaf-overlay-templates").removeClass("active");						
						$(document).find("a#pmaf-save-form").trigger("click");
					}, 2000 );
				}
			});
			
			return false;
		});
		
		if( $(document).find("#pmaf-form-preview-frame").length ) {
			$(document).find("#pmaf-form-preview-frame").load(function(){
				$(".pmaf-all-form-preview").removeClass("loading");
			});
		}
		
		if( $(".pmaf-preview-trigger").length ) {			
			$(".pmaf-preview-trigger").on("click", function(e){
				e.preventDefault();
				$(".pmaf-editor-options").removeClass("active");
				$(".pmaf-field-editor-content-wrap").addClass("pmaf-preview-activated");
				$(".pmaf-tools-wrap").addClass("preview-activated");
				$(".pmaf-editor-preview-wrap .pmaf-preview-fields").hide();
				$(".pmaf-editor-preview-wrap .pmaf-all-form-preview").fadeIn(350);
				return false;
			});
			$(".pmaf-fields-editor-trigger").on("click", function(e){
				e.preventDefault();
				$(".pmaf-field-editor-content-wrap").removeClass("pmaf-hide-fields-set");
				$(".pmaf-field-editor-content-wrap").removeClass("pmaf-full-preview");
				$(".pmaf-field-editor-content-wrap").removeClass("pmaf-preview-activated");
				$(".pmaf-tools-wrap").removeClass("preview-activated");				
				$(".pmaf-editor-preview-wrap .pmaf-all-form-preview").hide();
				$(".pmaf-editor-preview-wrap .pmaf-preview-fields").fadeIn(350);
				return false;
			});
		}
		
		$("a.pmaf-field-toggle").on("click", function(e){
			e.preventDefault();
			
			$(".pmaf-field-editor-content-wrap").toggleClass("pmaf-hide-fields-set");
			//setTimeout( function(){
				//if( $(".pmaf-field-editor-content-wrap").hasClass("pmaf-preview-activated") ) {
					$(".pmaf-field-editor-content-wrap").toggleClass("pmaf-full-preview");
					$(".pmaf-editor-preview-wrap").fadeIn(350);
				//}
			//}, 700 );
			
			return false;
		});
		
		// forms pagination
		pmaf_form_pagination();
		
	});
	
	function pmaf_format_settings_for_save() {
		$.each( _pmaf_ani_settings, function( name, fields ) { 
			if( fields ) {
				$.each( fields, function( key, data ) {		
					let _dim = [ "padding", "margin", "dimension", "border" ];
					if( data && data.hasOwnProperty('field') ) {
						if( _dim.includes( data.field ) ) { 
							data = { top: ( data.hasOwnProperty('top') ? data.top : '' ), left: ( data.hasOwnProperty('left') ? data.left : '' ), right: ( data.hasOwnProperty('right') ? data.right : '' ), bottom: ( data.hasOwnProperty('bottom') ? data.bottom : '' ) };
						} else if( data.hasOwnProperty('value') ) {
							data = data.value;
						}
					}
					_pmaf_ani_settings[name][key] = data;
				});	
			}
		});		
	}
	
	function pmaf_search_animated_template_fun() {
		$(document).find("#pmaf-import-templates-search").off("input");
		$(document).find("#pmaf-import-templates-search").on("input", function(){
			if( $(this).val() != '' ) {
				pmaf_search_templates( 'pmaf-import-templates-search', 'pmaf-import-templates-list', 'pmaf-import-template' );
			} else {
				$(document).find(".pmaf-import-template").show();
			}
		});
	}
	
	function pmaf_search_animate_pack_fun() {
		$(document).find("#pmaf-import-pack-search").off("input");
		$(document).find("#pmaf-import-pack-search").on("input", function(){
			if( $(this).val() != '' ) {
				pmaf_search_templates( 'pmaf-import-pack-search', 'pmaf-import-pack-list', 'pmaf-import-template' );
			} else {
				$(document).find(".pmaf-import-template").show();
			}
		});
	}
	
	function pmaf_search_overlay_template_fun() {
		$(document).find("#pmaf-import-otemplates-search").off("input");
		$(document).find("#pmaf-import-otemplates-search").on("input", function(){
			if( $(this).val() != '' ) {
				pmaf_search_templates( 'pmaf-import-otemplates-search', 'pmaf-import-otemplates-list', 'pmaf-import-template' );
				
				// for downloaded templates too
				//if( $(document).find(".pmaf-bg-templates.pmaf-overlay-templates").length && $(document).find(".pmaf-bg-templates.pmaf-overlay-templates").hasClass("active") ){
					pmaf_search_templates( 'pmaf-import-otemplates-search', 'pmaf-import-odownloaded-list', 'pmaf-import-template' );
				//}
				
			} else {
				$(document).find(".pmaf-import-template").show();
			}
		});
	}
	
	function pmaf_search_form_inner_template_fun() {
		$(document).find("#pmaf-import-fitemplates-search").off("input");
		$(document).find("#pmaf-import-fitemplates-search").on("input", function(){
			if( $(this).val() != '' ) {
				pmaf_search_templates( 'pmaf-import-fitemplates-search', 'pmaf-import-fitemplates-list', 'pmaf-import-template' );
			} else {
				$(document).find(".pmaf-import-template").show();
			}
		});
	}
	
	function pmaf_overlay_remove() {
		
		$(document).find(".pmaf-overlay-remove").off("click");
		$(document).find(".pmaf-overlay-remove").on("click", function(e){
			e.preventDefault();
			_pmaf_ani_settings.overlay.html = '';
			_pmaf_ani_settings.overlay.name = '';
			_pmaf_ani_settings.overlay.style = '';
			_pmaf_ani_settings.overlay.js = [];
			$(this).parent(".pmaf-selected-overlay").remove();
			return false;
		});
		
	}
	
	function pmaf_fi_remove() {
		
		$(document).find(".pmaf-fi-remove").off("click");
		$(document).find(".pmaf-fi-remove").on("click", function(e){
			e.preventDefault();
			_pmaf_ani_settings.inner = {};
			$(this).parent(".pmaf-selected-fi").remove();
			return false;
		});
		
	}
	
	function pmaf_copy_to_clipboard( _ele ) {

		// Create a "hidden" input
		var aux = document.createElement("input");

		// Assign it the value of the specified element
		aux.setAttribute("value", $(_ele).html());

		// Append it to the body
		document.body.appendChild(aux);

		// Highlight its content
		aux.select();

		// Copy the highlighted text
		document.execCommand("copy");

		// Remove it from the body
		document.body.removeChild(aux);

	}
	
	function pmaf_forms_delete( _forms, _single ) {
		
		// delete form
		$.ajax({
			type: "POST",
			url: ajaxurl,
			data: {
				action: 'pmaf_delete_form',
				nonce: pmaf_obj.nonce.delete_form,
				f: _forms
			},
			success: function( data ) { 
				if( data.status == 'success' ) {
					if( !_single ) {
						location.reload();
					} else {
						$(document).find('.pmaf-single-form-row-'+ _forms[0]).remove();
					}
				}
			},error: function(xhr, status, error) {
				console.log("failed");						
			},complete: function(){
				//$(".pmaf-wrap-inner").removeClass("processing");
			}
		});
		
	}
	
	function pmaf_forms_status_change( _forms, _status ) {
		
		// status change
		$.ajax({
			type: "POST",
			url: ajaxurl,
			data: {
				action: 'pmaf_form_status_change',
				nonce: pmaf_obj.nonce.status_change,
				f: _forms,
				stat: _status
			},
			success: function( data ) { 
				if( data.status == 'success' ) {
					//location.reload();
				}
			},error: function(xhr, status, error) {
				console.log("failed");						
			},complete: function(){
				//$(".pmaf-wrap-inner").removeClass("processing");
			}
		});
		
	}
	
	function pmaf_forms_export(_forms) {
		
		/*let _forms = [];
		$("input:checkbox[name=pmaf]:checked").each(function() {
			_forms.push($(this).val());
		}); */
		
		// export form
		$.ajax({
			type: "POST",
			url: ajaxurl,
			data: {
				action: 'pmaf_export_form',
				nonce: pmaf_obj.nonce.export_form,
				f: _forms
			},
			success: function( data ) {
				if( data.status == 'success' ) {
					let date = (new Date()).toISOString().split('T')[0];
					let json = JSON.stringify(data)
					let blob = new Blob([json])
					let link = document.createElement('a')
					link.href = window.URL.createObjectURL(blob)
					link.download = 'pmaf-export-'+date+'.json'
					link.click()
				}
			},error: function(xhr, status, error) {
				console.log("failed");						
			},complete: function(){
				//$(".pmaf-wrap-inner").removeClass("processing");
			}
		});
		
	}
	
	function pmaf_import() {
		
		// import file
		$("#pmaf-import-file").on( "change", function(e) {
			e.preventDefault();

			if( $('#pmaf-import-file').get(0).files.length > 0 ) {
				$(document).find("#import-upload-form #submit").removeAttr("disabled");
			} else {
				$(document).find("#import-upload-form #submit").attr("disabled", "disabled");
			}
			
			return false;
		});
		
		$("#import-upload-form").on( "submit", function(e) {
			e.preventDefault();
			// import form data
			let _data = new FormData(this);
			_data['action'] = 'pmaf_import_form';
			_data['nonce'] = pmaf_obj.nonce.import_form;
					
			$(".pmaf-wrap-inner").addClass("processing");
			$(document).find("#import-upload-form #submit").attr("disabled", "disabled");
			
			$.ajax({
				type: "POST",
				url: ajaxurl,
				dataType: "json",
				data: _data,
				crossDomain: true,
				processData: false,
				contentType: "multipart/form-data",
				contentType: false,
				headers: {
                    "Accept": "application/json"
                },
				success: function( data ) { 
					if( data.status == 'success' ) {
						
						let _checkmark_html = '<div class="pmaf-import-successs-wrap"><div class="pmaf-import-swrap-inner"><div class="pmaf-animation-ctn"><div class="icon icon--order-success svg"><svg height=154px width=154px xmlns=http://www.w3.org/2000/svg><g fill=none stroke=#22AE73 stroke-width=2><circle cx=77 cy=77 r=72 style=stroke-dasharray:480px,480px;stroke-dashoffset:960px></circle><circle cx=77 cy=77 r=72 style=stroke-dasharray:480px,480px;stroke-dashoffset:960px fill=#22AE73 id=colored></circle><polyline class=st0 points="43.5,77.8 63.7,97.9 112.2,49.4 "stroke=#fff stroke-width=10 style=stroke-dasharray:100px,100px;stroke-dashoffset:200px /></g></svg></div></div><h3 class="pmaf-import-successs">Animated Forms are imported successfully.</h3></div></div>';
						$("#wpcontent").prepend(_checkmark_html);
						
						setTimeout(function(){ $(document).find(".pmaf-import-successs-wrap").remove(); window.location.href = pmaf_obj.pmaf_page; }, 2000);
						
					}
				},error: function(xhr, status, error) {
					console.log("failed");						
				},complete: function(){
					$(".pmaf-wrap-inner").removeClass("processing");
				}
			});
			
			return false;
		});
		
	}
	
	function pmaf_settings_values_update() {
		
		$("#pmaf-form-settings input, #pmaf-form-settings textarea, #pmaf-form-notifications input, #pmaf-form-notifications textarea").on("input", function(){
			let _cur = $(this);
			_pmaf_settings[_cur.data("parent")][_cur.attr("name")] = _cur.val();
		});
		
		$("#pmaf-form-settings .pmaf-text-editor").each(function(){
			
			var editor_id = $(this).attr("id");
			let _parent_id = $(this).data("parent");
			let _field_name = $(this).attr("name");
			
			// initialize editor
			wp.editor.remove(editor_id);
			wp.editor.initialize( editor_id, { 
				tinymce: {
					wpautop: true,
                    setup: function (editor) {
						editor.on('dirty', function (e) {
							_pmaf_settings[_parent_id][_field_name] = editor.getContent();
						});
						// fix for missing dirty event when editor content is fully deleted    
						editor.on('keyup', function (e) {
							_pmaf_settings[_parent_id][_field_name] = editor.getContent();
						});
                    }			
				},
			});
			
		});
		
		
		$('#pmaf-form-settings .pmaf-field-checkbox input, #pmaf-form-notifications .pmaf-field-checkbox input, #pmaf-form-entries .pmaf-field-checkbox input').on("change", function(){
			let _cur = $(this);
			let _res = this.checked ? 'on' : 'off';
			_pmaf_settings[_cur.data("parent")][_cur.attr("name")] = _res;
		});
		
	}
	
	function pmaf_ani_settings_values_update( _config ) {
		
		$("#pmaf-form-customizer .pmaf-admin-field:not(.pmaf-admin-field-border):not(.pmaf-admin-field-padding):not(.pmaf-admin-field-dimension):not(.pmaf-admin-field-option) input:not(.pmaf-wp-color), #pmaf-form-customizer textarea").on("input", function(){
			let _cur = $(this);
			_pmaf_ani_settings[_cur.data("parent")][_cur.attr("name")] = _cur.val();
			pmaf_instant_result( _cur.data("parent") +'-'+ _cur.attr("name"), _cur.val() );
		});
		
		$("#pmaf-form-customizer .pmaf-text-editor").each(function(){
			
			var editor_id = $(this).attr("id");
			let _parent_id = $(this).data("parent");
			let _field_name = $(this).attr("name");
			
			// initialize editor
			wp.editor.remove(editor_id);
			wp.editor.initialize( editor_id, { 
				tinymce: {
					wpautop: true,
                    setup: function (editor) {
						editor.on('change', function (e) {
							_pmaf_ani_settings[_parent_id][_field_name] = editor.getContent();
							pmaf_instant_result(_parent_id+'-'+_field_name, editor.getContent());
						});
                    }			
				},
			});
			
		});
		
		$("#pmaf-form-customizer select").on("change", function(){
			let _cur = $(this);
			_pmaf_ani_settings[_cur.data("parent")][_cur.attr("name")] = this.value;
			pmaf_instant_result( _cur.data("parent") +'-'+ _cur.attr("name"), this.value );
		});
		
		$('#pmaf-form-customizer .pmaf-field-checkbox input, #pmaf-form-customizer .pmaf-admin-field-option input').off("change");
		$('#pmaf-form-customizer .pmaf-field-checkbox input, #pmaf-form-customizer .pmaf-admin-field-option input').on("change", function(){
			let _cur = $(this);
			let _res = this.checked ? 'on' : 'off';
			_pmaf_ani_settings[_cur.data("parent")][_cur.attr("name")] = _res;
			
			// check req 
			if( _cur.parents(".pmaf-admin-field-option").length ) {
				pmaf_chck_required( _cur, _config );
			}
			
			pmaf_instant_result( _cur.data("parent") +'-'+ _cur.attr("name"), _res );
						
		});
		$('#pmaf-form-customizer .pmaf-admin-field-option input').trigger("change");
		
		$("#pmaf-form-customizer .pmaf-admin-field-color input, #pmaf-form-customizer input.pmaf-wp-color").wpColorPicker({
			change: function (event, ui) {
				var element = event.target;
				var color = ui.color.toString();
				let _cur = $(element);
				if( _cur.data("child") ) {
					_pmaf_ani_settings[_cur.data("parent")][_cur.attr("name")][_cur.data("child")] = color;
				} else {
					_pmaf_ani_settings[_cur.data("parent")][_cur.attr("name")] = color;
				}
				pmaf_instant_result();
			}
		});
		$("#pmaf-form-customizer .pmaf-admin-field-color input.wp-picker-clear, #pmaf-form-customizer input.wp-picker-clear").off("click");
		$("#pmaf-form-customizer .pmaf-admin-field-color input.wp-picker-clear, #pmaf-form-customizer input.wp-picker-clear").on("click", function(){
			let _ele = $(this).parent(".wp-picker-input-wrap").find(".wp-color-picker");
			$(_ele).val("");
			$(_ele).trigger("change");
			let _cur = $(_ele);
			if( _cur.data("child") ) {
				_pmaf_ani_settings[_cur.data("parent")][_cur.attr("name")][_cur.data("child")] = '';
			} else {
				_pmaf_ani_settings[_cur.data("parent")][_cur.attr("name")] = '';
			}
			pmaf_instant_result();
		});
		
		// dimension 
		$("#pmaf-form-customizer .pmaf-admin-field.pmaf-admin-field-dimension input").on("input", function(){
			let _cur = $(this);
			_pmaf_ani_settings[_cur.data("parent")][_cur.attr("name")][_cur.data("child")] = _cur.val();
			pmaf_instant_result();
		});
		
		// border 
		$("#pmaf-form-customizer .pmaf-admin-field.pmaf-admin-field-border input").on("input", function(){
			let _cur = $(this);
			_pmaf_ani_settings[_cur.data("parent")][_cur.attr("name")][_cur.data("child")] = _cur.val();
			pmaf_instant_result();
			
		});
				
		// image upload
		$(document).find(".pmaf-bg-img").off( 'click' );
		$(document).find(".pmaf-bg-img").on( 'click', function( event ){

			event.preventDefault(); // prevent default link click and page refresh
			
			let _cur_ele = $(this)
			let imageId = _cur_ele.next().next().val();
			
			let customUploader = wp.media({
				title: 'Insert image', // modal window title
				library : {
					type : 'image'
				},
				button: {
					text: 'Use this image' // button label text
				},
				multiple: false
			}).on( 'select', function() { // it also has "open" and "close" events
				let attachment = customUploader.state().get( 'selection' ).first().toJSON();
				_cur_ele.parents(".pmaf-admin-field-bg").find(".pmaf-bg-preview").remove();
				_pmaf_ani_settings[_cur_ele.data('parent')]['bg']['image'] = { 'id': attachment.id, 'url': attachment.url };
				$('<div class="pmaf-bg-preview"><img src="'+ attachment.url + '" class="'+ attachment.id +'" /><span class="pmaf-bg-img-remove"><i class="dashicons dashicons-no-alt"></i></span></div>').insertAfter(_cur_ele.parents(".pmaf-bg-set"));
				pmaf_bg_img_remove();
				
				pmaf_instant_result();
				
			})
			
			// already selected images
			customUploader.on( 'open', function() {

				if( imageId ) {
				  let selection = customUploader.state().get( 'selection' )
				  attachment = wp.media.attachment( imageId );
				  attachment.fetch();
				  selection.add( attachment ? [attachment] : [] );
				}
				
			})

			customUploader.open()
		
		});
		
		// bg img remove event
		pmaf_bg_img_remove();
				
	}
	
	function pmaf_chck_required( _ele, _config ) {
		let _f_name = _ele.attr("name");
		let _parent = _ele.data("parent");
		$.each(_config[_parent], function( key, fields ) { 
			if( fields.hasOwnProperty("required") ) { 
				if( fields.required.option == _f_name ) {
					if( _pmaf_ani_settings[_parent][_f_name] == fields.required.value ) {
						_ele.parents(".pmaf-main-tab-content").find('.pmaf-admin-field.pmaf-req[data-name="'+ _f_name +'"]').addClass("shown");
					} else {
						_ele.parents(".pmaf-main-tab-content").find('.pmaf-admin-field.pmaf-req[data-name="'+ _f_name +'"]').removeClass("shown");
					}
				}
			}
		});
	}
	
	function pmaf_bg_img_remove() {
		$(document).find(".pmaf-bg-img-remove").off("click");
		$(document).find(".pmaf-bg-img-remove").on("click", function(){
			_pmaf_ani_settings[$(this).parents(".pmaf-admin-field-bg").find("a.pmaf-bg-img").data('parent')]['bg']['image'] = {};
			$(this).parents(".pmaf-bg-preview").remove();
			pmaf_instant_result();
		});
	}
		
	function pmaf_form_settings_making( name, data, _default, _parent ) {
		
		let _field_html = '';
		switch( data.field ) {
			case "text":
				let _text_val = data.hasOwnProperty('value') ? data.value : _default;
				_field_html = '<div class="pmaf-admin-field pmaf-admin-field-text'+ ( data.hasOwnProperty('required') ? ' pmaf-req' : '' ) +'" data-name="'+ ( data.hasOwnProperty('required') ? data.required.option : '' ) +'"><label>'+ data.label +'</label><input type="text" name="'+ name +'" data-parent="'+ _parent +'" value="'+ _text_val +'" /><div class="pmaf-desc">'+ data.description +'</div></div>';
			break;
			
			/*case "name":
				_field_html = '<div class="pmaf-admin-field pmaf-admin-field-name'+ ( data.hasOwnProperty('required') ? ' pmaf-req' : '' ) +'" data-name="'+ ( data.hasOwnProperty('required') ? data.required.option : '' ) +'"><label>'+ data.label +'</label>Name field<div class="pmaf-desc">'+ data.description +'</div></div>';
				$.each( data.options, function( key, value ) {
					_field_html += '<span>'+ key +'</span>';
				});
			break;*/
			
			case "checkbox":
				_field_html = '<div class="pmaf-field pmaf-field-checkbox'+ ( data.hasOwnProperty('required') ? ' pmaf-req' : '' ) +'" data-name="'+ ( data.hasOwnProperty('required') ? data.required.option : '' ) +'"><input type="checkbox" name="'+ name +'" data-parent="'+ _parent +'" value="'+ _default +'" '+ ( _default == 'on' ? 'checked' : '' ) +' /><label>'+ data.label +'</label><div class="pmaf-desc">'+ data.description +'</div></div>';
			break;
			
			case "textarea":
				_field_html = '<div class="pmaf-admin-field pmaf-admin-field-textarea'+ ( data.hasOwnProperty('required') ? ' pmaf-req' : '' ) +'" data-name="'+ ( data.hasOwnProperty('required') ? data.required.option : '' ) +'"><label>'+ data.label +'</label><textarea name="'+ name +'" data-parent="'+ _parent +'" rows="5">'+ _textarea_val +'</textarea><div class="pmaf-desc">'+ data.description +'</div></div>';
			break;
			
			case "editor":
				_default = _default.length ? _default : '';
				_field_html = '<div class="pmaf-admin-field pmaf-admin-field-editor'+ ( data.hasOwnProperty('required') ? ' pmaf-req' : '' ) +'" data-name="'+ ( data.hasOwnProperty('required') ? data.required.option : '' ) +'"><label>'+ data.label +'</label><textarea class="pmaf-text-editor" id="pmaf-text-editor-'+ name +'" name="'+ name +'" data-parent="'+ _parent +'" rows="5">'+ _default +'</textarea><div class="pmaf-desc">'+ data.description +'</div></div>';
			break;
			
			case "number":
				_field_html = '<div class="pmaf-admin-field pmaf-admin-field-number'+ ( data.hasOwnProperty('required') ? ' pmaf-req' : '' ) +'" data-name="'+ ( data.hasOwnProperty('required') ? data.required.option : '' ) +'"><label>'+ data.label +'</label><input type="number" name="'+ name +'" data-parent="'+ _parent +'" value="'+ _default +'" min="'+ data.min +'" max="'+ data.max +'" step="'+ data.step +'" /><div class="pmaf-desc">'+ data.description +'</div></div>';
			break;
			
			case "color":
				_field_html = '<div class="pmaf-admin-field pmaf-admin-field-color'+ ( data.hasOwnProperty('required') ? ' pmaf-req' : '' ) +'" data-name="'+ ( data.hasOwnProperty('required') ? data.required.option : '' ) +'"><label>'+ data.label +'</label><input type="text" name="'+ name +'" data-parent="'+ _parent +'" value="'+ _default +'" /><div class="pmaf-desc">'+ data.description +'</div></div>';
			break;
			
			case "option":
				_field_html = '<div class="pmaf-admin-field pmaf-admin-field-option'+ ( data.hasOwnProperty('required') ? ' pmaf-req' : '' ) +'" data-name="'+ ( data.hasOwnProperty('required') ? data.required.option : '' ) +'"><label>'+ data.label +'</label><label class="pmaf-switch"><input type="checkbox" name="'+ name +'" data-parent="'+ _parent +'" '+ ( _default == 'on' ? 'checked="checked"' : '' ) +'><span class="slider round"></span></label><div class="pmaf-desc">'+ data.description +'</div></div>';
			break;
			
			case "select":
				_field_html = '<div class="pmaf-admin-field pmaf-admin-field-select'+ ( data.hasOwnProperty('required') ? ' pmaf-req' : '' ) +'" data-name="'+ ( data.hasOwnProperty('required') ? data.required.option : '' ) +'"><label>'+ data.label +'</label>';
				_field_html += '<select name="'+ name +'" data-parent="'+ _parent +'">';
				$.each( data.options, function( key, value ) {
					_field_html += '<option value="'+ key +'" '+ ( _default == key ? 'selected="selected"' : '' ) +'>'+ value +'</option>';
				});
				_field_html += '</select>';
				_field_html += '<div class="pmaf-desc">'+ data.description +'</div></div>';
			break;
			
			case "bg":
				_field_html = '<div class="pmaf-admin-field pmaf-admin-field-bg'+ ( data.hasOwnProperty('required') ? ' pmaf-req' : '' ) +'" data-name="'+ ( data.hasOwnProperty('required') ? data.required.option : '' ) +'"><label>'+ data.label +'</label><ul class="pmaf-bg-set"><li><span>Upload Image: </span><a href="#" class="pmaf-bg-img" data-child="image" name="'+ name +'" data-parent="'+ _parent +'"><i class="dashicons dashicons-cloud-upload"></i></a></li>';
				_field_html += '<li><span>Primary Color: </span><input type="text" class="pmaf-wp-color" data-child="pcolor" name="'+ name +'" data-parent="'+ _parent +'" value="'+ ( _default.hasOwnProperty('pcolor') ? _default.pcolor : '' ) +'" /></li>';
				_field_html += '<li><span>Secondary Color: </span><input type="text" class="pmaf-wp-color" data-child="scolor" name="'+ name +'" data-parent="'+ _parent +'" value="'+ ( _default.hasOwnProperty('scolor') ? _default.scolor : '' ) +'" /></li></ul>';				
				if( _default.hasOwnProperty('image') ) {
					_field_html += '<div class="pmaf-bg-preview"><img src="'+ _default.image.url + '" class="'+ _default.image.id +'" /><span class="pmaf-bg-img-remove"><i class="dashicons dashicons-no-alt"></i></span></div>';
				}
				_field_html += '<div class="pmaf-desc">'+ data.description +'</div></div>';
			break;
			
			case "filter":
				let _filter_obj = { f_1: ( _default.hasOwnProperty('f_1') ? _default.f_1 : '' ), f_2: ( _default.hasOwnProperty('f_2') ? _default.f_2 : '' ), f_3: ( _default.hasOwnProperty('f_3') ? _default.f_3 : '' ), f_4: ( _default.hasOwnProperty('f_4') ? _default.f_4 : '' ) };
				_field_html = '<div class="pmaf-admin-field pmaf-admin-field-filter'+ ( data.hasOwnProperty('required') ? ' pmaf-req' : '' ) +'" data-name="'+ ( data.hasOwnProperty('required') ? data.required.option : '' ) +'"><label>'+ data.label +'</label>';
				_field_html += '<ul class="pmaf-filter-set"><li><span>#1 Color: </span><input type="text" class="pmaf-wp-color" data-child="f_1" name="'+ name +'" data-parent="'+ _parent +'" value="'+ _filter_obj.f_1 +'" /></li><li><span>#2 Color: </span><input type="text" class="pmaf-wp-color" data-child="f_2" name="'+ name +'" data-parent="'+ _parent +'" value="'+ _filter_obj.f_2 +'" /></li><li><span>#3 Color: </span><input type="text" class="pmaf-wp-color" data-child="f_3" name="'+ name +'" data-parent="'+ _parent +'" value="'+ _filter_obj.f_3 +'" /></li><li><span>#4 Color: </span><input type="text" class="pmaf-wp-color" data-child="f_4" name="'+ name +'" data-parent="'+ _parent +'" value="'+ _filter_obj.f_4 +'" /></li></ul>';
				_field_html += '<div class="pmaf-desc">'+ data.description +'</div></div>';
			break;
			
			case "border":
				let _border_obj = { top: ( _default.hasOwnProperty('top') ? _default.top : '' ), right: ( _default.hasOwnProperty('right') ? _default.right : '' ), bottom: ( _default.hasOwnProperty('bottom') ? _default.bottom : '' ), left: ( _default.hasOwnProperty('left') ? _default.left : '' ) };
				_field_html = '<div class="pmaf-admin-field pmaf-admin-field-border'+ ( data.hasOwnProperty('required') ? ' pmaf-req' : '' ) +'" data-name="'+ ( data.hasOwnProperty('required') ? data.required.option : '' ) +'"><label>'+ data.label +'</label>';
				_field_html += '<ul class="pmaf-border-set"><li><span>Border Color: </span><input type="text" class="pmaf-wp-color" data-child="color" name="'+ name +'" data-parent="'+ _parent +'" value="'+ ( _default.hasOwnProperty('color') ? _default.color : '' ) +'" /></li><li><span>Border Size: </span><div class="pmaf-border-sizes"><input type="text" data-child="top" name="'+ name +'" data-parent="'+ _parent +'" value="'+ _border_obj.top +'" /><input type="text" data-child="right" name="'+ name +'" data-parent="'+ _parent +'" value="'+ _border_obj.right +'" /><input type="text" data-child="bottom" name="'+ name +'" data-parent="'+ _parent +'" value="'+ _border_obj.bottom +'" /><input type="text" data-child="left" name="'+ name +'" data-parent="'+ _parent +'" value="'+ _border_obj.left +'" /></div></li></ul>';
				_field_html += '<div class="pmaf-desc">'+ data.description +'</div></div>';
			break;
			
			case "dimension":
			case "margin":
			case "padding":
				let _dim_obj = { top: ( _default.hasOwnProperty('top') ? _default.top : '' ), right: ( _default.hasOwnProperty('right') ? _default.right : '' ), bottom: ( _default.hasOwnProperty('bottom') ? _default.bottom : '' ), left: ( _default.hasOwnProperty('left') ? _default.left : '' ) };
				_field_html = '<div class="pmaf-admin-field pmaf-admin-field-dimension'+ ( data.hasOwnProperty('required') ? ' pmaf-req' : '' ) +'" data-name="'+ ( data.hasOwnProperty('required') ? data.required.option : '' ) +'"><label>'+ data.label +'</label>';
				_field_html += '<ul class="pmaf-padding-set"><li><div class="pmaf-field-dimensions"><input type="text" data-child="top" name="'+ name +'" data-parent="'+ _parent +'" value="'+ _dim_obj.top +'" /><input type="text" data-child="right" name="'+ name +'" data-parent="'+ _parent +'" value="'+ _dim_obj.right +'" /><input type="text" data-child="bottom" name="'+ name +'" data-parent="'+ _parent +'" value="'+ _dim_obj.bottom +'" /><input type="text" data-child="left" name="'+ name +'" data-parent="'+ _parent +'" value="'+ _dim_obj.left +'" /></div></li></ul>';
				_field_html += '<div class="pmaf-desc">'+ data.description +'</div></div>';
			break;
		}
				
		return _field_html;
	
	}
	
	function pmaf_form_animation_settings_config() {
		
		let _global_align = { 'start': 'Start', 'center': 'Center', 'end': 'End' };
		let _global_tags = { 'h1': 'H1', 'h2': 'H2', 'h3': 'H3', 'h4': 'H4', 'h5': 'H5', 'h6': 'H6', 'p': 'p', 'span': 'span', 'div': 'div' };
		let _global_fweight = { '100': '100', '200': '200', '300': '300', '400': '400', '500': '500', '600': '600', '700': '700', '800': '800', '900': '900', 'normal': 'Normal', 'lighter': 'Lighter', 'bold': 'Bold', 'bolder': 'Bolder' };
		let _padding_desc = 'Enter padding values in numeric form for top, right, bottom, and left. Example 10 10 10 10';
		let _margin_desc = 'Enter Margin values in numeric form for top, right, bottom, and left. Example 10 10 10 10';
		let _border_desc = 'Enter border sizes from top, right, bottom and left. It should be a number. Example 5 5 5 5';
		let _font_desc = 'Enter the font size. Example 14';
			
		let ani_config = {
			'outer': {
				'bg': { 'field': 'bg', 'label': 'Background', 'value': {}, 'description': '' },	
				'enable_filter': { 'field': 'option', 'label': 'Enable Gradient Background', 'value': 'off', 'description': 'Show a moving gradient animation in the background.' },				
				'filter': { 'field': 'filter', 'label': 'Gradient Animation Filters', 'value': {}, 'description': 'Pick colors for your gradient animation.', 'required': { 'option': 'enable_filter', 'condition': '=', 'value': 'on' } }
			},
			'overlay': {"html":"","inner_html":"","form_html":"","outer_html":"","style":"","name":"","js":[]},
			'inner': {},
			'form': {
				'inner_padding': { 'field': 'padding', 'label': 'Form Inner Padding', 'value': {}, 'description': _padding_desc },
				'outer_padding': { 'field': 'padding', 'label': 'Form Outer Padding', 'value': {}, 'description': _padding_desc },
				'border': { 'field': 'border', 'label': 'Form Border', 'value': {}, 'description': _border_desc },
				'align': { 'field': 'select', 'label': 'Form Alignment', 'value': 'center', 'description': '', 'options': _global_align },
				'fwidth': { 'field': 'number', 'label': 'Form Width', 'value': '', 'description': 'Enter form width. It should be a number. Example 500', 'min': 1, 'max': 1200, 'step': 1 },
			},
			'title': {
				'enable_title': { 'field': 'option', 'label': 'Enable Title', 'value': 'off', 'description': 'Enable this option to customize the title and its settings.' },
				'text': { 'field': 'text', 'label': 'Title', 'value': pmaf_obj.post_title, 'description': '', 'required': { 'option': 'enable_title', 'condition': '=', 'value': 'on' } },
				'inner_title_opt': { 'field': 'option', 'label': 'Place Title to Form Inner', 'value': 'on', 'description': 'Enable this option to show title on form inner wrapper.', 'required': { 'option': 'enable_title', 'condition': '=', 'value': 'on' } },
				'color': { 'field': 'color', 'label': 'Title Color', 'value': '', 'description': '', 'required': { 'option': 'enable_title', 'condition': '=', 'value': 'on' } },
				'fsize': { 'field': 'number', 'label': 'Title Font Size', 'value': '', 'description': _font_desc, 'min': 0, 'max': 100, 'step': 1, 'required': { 'option': 'enable_title', 'condition': '=', 'value': 'on' } },
				'fweight': { 'field': 'select', 'label': 'Title Font Weight', 'value': '600', 'description': '', 'options': _global_fweight, 'required': { 'option': 'enable_title', 'condition': '=', 'value': 'on' } },
				'align': { 'field': 'select', 'label': 'Title Alignment', 'value': '', 'description': '', 'options': _global_align, 'required': { 'option': 'enable_title', 'condition': '=', 'value': 'on' } },
				'tag': { 'field': 'select', 'label': 'Title Tag', 'value': 'h3', 'description': '', 'options': _global_tags, 'required': { 'option': 'enable_title', 'condition': '=', 'value': 'on' } },
				'padding': { 'field': 'padding', 'label': 'Padding', 'value': {}, 'description': _padding_desc, 'required': { 'option': 'enable_title', 'condition': '=', 'value': 'on' } },
				'margin': { 'field': 'margin', 'label': 'Margin', 'value': {}, 'description': _margin_desc, 'required': { 'option': 'enable_title', 'condition': '=', 'value': 'on' } },
				'enable_title_desc': { 'field': 'option', 'label': 'Enable Content', 'value': 'off', 'description': 'Enable this option to show title description and their settings.' },
				'description': { 'field': 'editor', 'label': 'Form Content', 'value': '', 'description': '', 'required': { 'option': 'enable_title_desc', 'condition': '=', 'value': 'on' } },
				'inner_desc_opt': { 'field': 'option', 'label': 'Place Description to Form Inner', 'value': 'on', 'description': 'Enable this option to show description on form inner wrapper.', 'required': { 'option': 'enable_title_desc', 'condition': '=', 'value': 'on' } },
				'desc_color': { 'field': 'color', 'label': 'Content Color', 'value': '', 'description': '', 'required': { 'option': 'enable_title_desc', 'condition': '=', 'value': 'on' } },
				'desc_fsize': { 'field': 'number', 'label': 'Content Font Size', 'value': '', 'description': _font_desc, 'min': 0, 'max': 100, 'step': 1, 'required': { 'option': 'enable_title_desc', 'condition': '=', 'value': 'on' } },
				'desc_fweight': { 'field': 'select', 'label': 'Content	 Font Weight', 'value': '400', 'description': '', 'options': _global_fweight, 'required': { 'option': 'enable_title_desc', 'condition': '=', 'value': 'on' } },
				'desc_align': { 'field': 'select', 'label': 'Content Alignment', 'value': '', 'description': '', 'options': _global_align, 'required': { 'option': 'enable_title_desc', 'condition': '=', 'value': 'on' } },
				'desc_padding': { 'field': 'padding', 'label': 'Content Padding', 'value': {}, 'description': _padding_desc, 'required': { 'option': 'enable_title_desc', 'condition': '=', 'value': 'on' } },
				'desc_margin': { 'field': 'margin', 'label': 'Content Margin', 'value': {}, 'description': _margin_desc, 'required': { 'option': 'enable_title_desc', 'condition': '=', 'value': 'on' } },
			},
			'labels': {
				'label_move': { 'field': 'option', 'label': 'Enable Label Move Animation', 'value': 'off', 'description': '' },
				'label_gradient': { 'field': 'option', 'label': 'Enable Label Gradient Animation', 'value': 'off', 'description': '' },
				'color': { 'field': 'color', 'label': 'Label Color', 'value': '', 'description': '' },
				'fsize': { 'field': 'number', 'label': 'Label Font Size', 'value': '', 'description': 'Enter label font size. It should be a number. Example 14', 'min': 0, 'max': 100, 'step': 1 },
				'fweight': { 'field': 'select', 'label': 'Label Font Weight', 'value': '400', 'description': '', 'options': _global_fweight },
				'align': { 'field': 'select', 'label': 'Label Alignment', 'value': '', 'description': '', 'options': _global_align },
				'padding': { 'field': 'padding', 'label': 'Padding', 'value': {}, 'description': _padding_desc },
				'margin': { 'field': 'margin', 'label': 'Margin', 'value': {}, 'description': _margin_desc },
			},
			'box': {
				'box_color': { 'field': 'color', 'label': 'Box Text Color', 'value': '', 'description': '' },
				'box_bg_color': { 'field': 'color', 'label': 'Box Background Color', 'value': '', 'description': '' },
				'box_border': { 'field': 'border', 'label': 'Box Border', 'value': {}, 'description': _border_desc },	
				'padding': { 'field': 'padding', 'label': 'Padding', 'value': {}, 'description': _padding_desc },
			},
			'btn': {
				'btnstyle': { 'field': 'select', 'label': 'Select Button Style', 'value': 'default', 'description': '', 'options': {'default': 'Default', 'classic': 'Classic', 'modern': 'Modern' } },
				'color': { 'field': 'color', 'label': 'Text Color', 'value': '', 'description': '' },
				'bg': { 'field': 'color', 'label': 'Background Color', 'value': '', 'description': '' },
				'border': { 'field': 'border', 'label': 'Button Border', 'value': {}, 'description': _border_desc },
				'width': { 'field': 'number', 'label': 'Button Width', 'value': '', 'description': '', 'min': 0, 'max': 500, 'step': 1 },
				'model': { 'field': 'select', 'label': 'Button Style', 'value': '', 'description': '', 'options': { 'default': 'Default', 'round': 'Rounded', 'circle': 'Circle' } },
				'align': { 'field': 'select', 'label': 'Button Alignment', 'value': '', 'description': '', 'options': _global_align },
			},
		};
		
		let _ani_full_config = jQuery.extend(true, {}, ani_config); 
								
		let _settings_stat = 0;
		if( !_pmaf_ani_settings.hasOwnProperty('outer') ) {
			_pmaf_ani_settings = ani_config;
		} else {
			_settings_stat = 1;
		}
		
		// outer layer settings
		let _ani_outer_layer_settings_html = pmaf_config_to_html( 'outer', ani_config.outer, _pmaf_ani_settings, _settings_stat );
		$(".pmaf-form-ani-outer-settings").html(_ani_outer_layer_settings_html);
		
		// form settings
		let _ani_form_settings_html = pmaf_config_to_html( 'form', ani_config.form, _pmaf_ani_settings, _settings_stat );
		$(".pmaf-form-outer-settings").html(_ani_form_settings_html);
		
		// form title settings
		let _ani_title_settings_html = pmaf_config_to_html( 'title', ani_config.title, _pmaf_ani_settings, _settings_stat );
		$(".pmaf-form-title-settings").html(_ani_title_settings_html);
		
		// labels settings
		let _ani_labels_settings_html = pmaf_config_to_html( 'labels', ani_config.labels, _pmaf_ani_settings, _settings_stat );
		$(".pmaf-form-ani-labels-settings").html(_ani_labels_settings_html);
		
		// box settings
		let _ani_box_settings_html = pmaf_config_to_html( 'box', ani_config.box, _pmaf_ani_settings, _settings_stat );
		$(".pmaf-form-ani-box-settings").html(_ani_box_settings_html);
		
		// btn settings		
		let _ani_btn_settings_html = pmaf_config_to_html( 'btn', ani_config.btn, _pmaf_ani_settings, _settings_stat );
		$(".pmaf-form-btn-settings").html(_ani_btn_settings_html);
		
		pmaf_ani_settings_values_update( _ani_full_config );
		
	}
	
	function pmaf_config_to_html( _key, _fields, _xfields, _settings_stat ) {
				
		if( !_xfields.hasOwnProperty(_key) ) {
			_xfields[_key] = _fields;
		}
		
		let _config_html = '';
		$.each( _fields, function( name, data ) {
			if( data.hasOwnProperty('field') ) {
				 _xfields[_key][name] = _settings_stat && _xfields[_key].hasOwnProperty(name) ? _xfields[_key][name] : data.value;
				_config_html += pmaf_form_settings_making( name, data, _xfields[_key][name], _key );
			}
		});
		
		return _config_html; 
	}
	
	function pmaf_form_settings_config() {
				
		let _config = {
			'basic': {
				'form_name': { 'field': 'text', 'label': 'Form Name', 'value': pmaf_obj.post_title, 'description': '' },
				'submit_txt': { 'field': 'text', 'label': 'Submit Button Text', 'value': 'Submit', 'description': '' },
				'processing_txt': { 'field': 'text', 'label': 'Submit Button Processing Text', 'value': 'Sending..', 'description': '' },
			},
			'security': {
				'security_nonce': { 'field': 'text', 'label': 'Security Nonce', 'value': 'pmaf-security', 'description': '' },
				'enable_ajax': { 'field': 'checkbox', 'label': 'Enable AJAX form submission', 'value': 'on', 'description': '' },
			},
			'style': {
				'form_css_class': { 'field': 'text', 'label': 'Form CSS Class', 'value': '', 'description': '' },
				'btn_css_class': { 'field': 'text', 'label': 'Submit Button CSS Class', 'value': '', 'description': '' },
			},
			'notifications': {
				'enable_notifications': { 'field': 'checkbox', 'label': 'Enable Notifications on Email', 'value': 'on', 'description': '' },
			},
			'entries': {
				'enable_entries': { 'field': 'checkbox', 'label': 'Enable Entries', 'value': 'off', 'description': 'Enable to save form data to the local server on submission.' },
			},
			'd_notifi': {
				'enable_smtp': { 'field': 'checkbox', 'label': 'Use SMTP Email(Pro)', 'value': 'off', 'description': 'You can enable this option to use SMTP mail. Check SMTP Settings in Global Settings' },
				'send_email': { 'field': 'text', 'label': 'Send To Email', 'value': '{admin_email}', 'description': '' },
				'from_email': { 'field': 'text', 'label': 'From Email', 'value': '{admin_email}', 'description': '' },
				'email_subj': { 'field': 'text', 'label': 'Email Subject', 'value': 'Form #'+ pmaf_obj.post_id +' Entry', 'description': '' },				
				'replay_to': { 'field': 'text', 'label': 'Reply-To', 'value': '', 'description': '' },
				'email_msg': { 'field': 'editor', 'label': 'Email Message', 'value': '{all_form_fields}', 'description': '' },
			},
			'confirmation': {
				'confirm_msg': { 'field': 'editor', 'label': 'Show a message after submission', 'value': 'Thanks for contacting us! We will be in touch with you shortly.', 'description': '' },
			}
		};
		
		let _settings_stat = 0;
		if( !_pmaf_settings.hasOwnProperty('basic') ) {
			_pmaf_settings = _config;
		} else {
			_settings_stat = 1;
		}

		// basic settings
		let _basic_settings_html = '';
		$.each( _config.basic, function( name, data ) {
			 _pmaf_settings.basic[name] = _settings_stat && _pmaf_settings.basic.hasOwnProperty(name) ? _pmaf_settings.basic[name] : data.value;
			_basic_settings_html += pmaf_form_settings_making( name, data, _pmaf_settings.basic[name], 'basic' );
		});		
		$(".pmaf-form-basic-settings").html(_basic_settings_html);
		
		// secutiry settings
		let _security_settings_html = '';
		$.each( _config.security, function( name, data ) {
			_pmaf_settings.security[name] = _settings_stat && _pmaf_settings.security.hasOwnProperty(name) ? _pmaf_settings.security[name] : data.value;
			_security_settings_html += pmaf_form_settings_making( name, data, _pmaf_settings.security[name], 'security' );
		});		
		$(".pmaf-form-security-settings").html(_security_settings_html);
		
		// style settings
		let _style_settings_html = '';
		$.each( _config.style, function( name, data ) {
			_pmaf_settings.style[name] = _settings_stat && _pmaf_settings.style.hasOwnProperty(name) ? _pmaf_settings.style[name] : data.value;
			_style_settings_html += pmaf_form_settings_making( name, data, _pmaf_settings.style[name], 'style' );
		});		
		$(".pmaf-form-style-settings").html(_style_settings_html);
		
		// notifications settings
		let _notification_settings_html = ''; let notifi_stat = 0;
		if( !_pmaf_settings.hasOwnProperty('notifications') ) {
			notifi_stat = 1;
			_pmaf_settings.notifications = _config.notifications;			
		}
		$.each( _config.notifications, function( name, data ) {
			 _pmaf_settings.notifications[name] = _settings_stat && !notifi_stat && _pmaf_settings.notifications.hasOwnProperty(name) ? _pmaf_settings.notifications[name] : data.value;
			_notification_settings_html += pmaf_form_settings_making( name, data, _pmaf_settings.notifications[name], 'notifications' );
		});		
		$(".pmaf-form-notifications-settings").html(_notification_settings_html);
		
		// entries settings
		let _entries_settings_html = ''; let entries_stat = 0;
		if( !_pmaf_settings.hasOwnProperty('entries') ) {
			entries_stat = 1;
			_pmaf_settings.entries = _config.entries;			
		}
		$.each( _config.entries, function( name, data ) {
			 _pmaf_settings.entries[name] = _settings_stat && !entries_stat && _pmaf_settings.entries.hasOwnProperty(name) ? _pmaf_settings.entries[name] : data.value;
			_entries_settings_html += pmaf_form_settings_making( name, data, _pmaf_settings.entries[name], 'entries' );
		});
		$(".pmaf-form-entries-settings").html(_entries_settings_html);
		
		// default notifications settings
		let _d_notification_settings_html = ''; let d_notifi_stat = 0;
		if( !_pmaf_settings.hasOwnProperty('d_notifi') ) {
			d_notifi_stat = 1;
			_pmaf_settings.d_notifi = _config.d_notifi;			
		}
		$.each( _config.d_notifi, function( name, data ) {
			 _pmaf_settings.d_notifi[name] = _settings_stat && !d_notifi_stat && _pmaf_settings.d_notifi.hasOwnProperty(name) ? _pmaf_settings.d_notifi[name] : data.value;
			_d_notification_settings_html += pmaf_form_settings_making( name, data, _pmaf_settings.d_notifi[name], 'd_notifi' );
		});		
		$(".pmaf-form-default-notifications-settings").html(_d_notification_settings_html);
		
		// confirmation settings
		let _confirmation_settings_html = ''; let confirm_stat = 0;
		if( !_pmaf_settings.hasOwnProperty('confirmation') ) {
			confirm_stat = 1;
			_pmaf_settings.confirmation = _config.confirmation;			
		}
		$.each( _config.confirmation, function( name, data ) {
			 _pmaf_settings.confirmation[name] = _settings_stat && !confirm_stat && _pmaf_settings.confirmation.hasOwnProperty(name) ? _pmaf_settings.confirmation[name] : data.value;
			_confirmation_settings_html += pmaf_form_settings_making( name, data, _pmaf_settings.confirmation[name], 'confirmation' );
		});		
		$(".pmaf-form-confirmation-settings").html(_confirmation_settings_html);
				
		// settings value update/set
		pmaf_settings_values_update();
		
	}
	
	function pmaf_single_field_config( field_type ) {
		
		let _config = {
			'text': {
				'findex': _pmaf_index,
				'name': '',
				'type': 'text',
				'label': pmaf_obj.strings.simpletext,
				'description': '',
				'default': '',
				'placeholder': '',
				'req': 'off',
				'req_msg': '',
				'atts': {					
					'classes': '',
					'id': ''					
				}
			},
			'number': {
				'findex': _pmaf_index,
				'name': '',
				'type': 'number',
				'label': pmaf_obj.strings.numbertext,
				'description': '',
				'default': '',
				'placeholder': '',
				'req': 'off',
				'req_msg': '',
				'atts': {
					'classes': '',
					'id': '',
					'min': '',
					'max': '',
					'step': '',
				}
			},
			'name': {
				'findex': _pmaf_index,
				'name': '',
				'type': 'name',
				'label': pmaf_obj.strings.name,
				'options': {
					'fl': { 'enabled': true, 'placeholder': '', 'label': '', 'default': '' },
					'fml': { 'enabled': false, 'placeholder': '', 'label': '', 'default': '' }
				},
				'description': '',
				'req': 'off',
				'req_msg': '',
				'atts': {
					'classes': '',
					'id': ''					
				}
			},
			'email': {
				'findex': _pmaf_index,
				'name': '',
				'type': 'email',
				'label': pmaf_obj.strings.emailtext,
				'description': '',
				'default': '',
				'placeholder': '',
				'req': 'off',
				'req_msg': '',
				'atts': {
					'classes': '',
					'id': ''					
				}
			},
			'link': {
				'findex': _pmaf_index,
				'name': '',
				'type': 'link',
				'label': pmaf_obj.strings.link,
				'description': '',
				'default': '',
				'placeholder': '',
				'req': 'off',
				'req_msg': '',
				'atts': {
					'classes': '',
					'id': ''					
				}
			},
			'phone': {
				'findex': _pmaf_index,
				'name': '',
				'type': 'phone',
				'label': pmaf_obj.strings.phone,
				'description': '',
				'default': '',
				'placeholder': '',
				'req': 'off',
				'req_msg': '',
				'atts': {
					'classes': '',
					'id': ''					
				}
			},
			'textarea': {
				'findex': _pmaf_index,
				'name': '',
				'type': 'textarea',
				'label': pmaf_obj.strings.paratext,
				'description': '',
				'default': '',
				'placeholder': '',
				'req': 'off',
				'req_msg': '',
				'atts': {
					'classes': '',
					'id': '',
					'rows': 5
				}
			},
			'editor': {
				'findex': _pmaf_index,
				'name': '',
				'type': 'editor',
				'label': pmaf_obj.strings.editor,
				'description': '',
				'default': '',
				'placeholder': '',
				'req': 'off',
				'req_msg': '',
				'atts': {
					'classes': '',
					'id': '',
					'rows': 5
				}
			},
			'select': {
				'findex': _pmaf_index,
				'name': '',
				'type': 'select',
				'multi': false,
				'label': pmaf_obj.strings.selecttext,
				'description': '',
				'default': '',
				'placeholder': '',
				'req': 'off',
				'req_msg': '',
				'options': {
					'ch_1': pmaf_obj.strings.opt + ' 1',
					'ch_2': pmaf_obj.strings.opt + ' 2',
					'ch_3': pmaf_obj.strings.opt + ' 3',
				},
				'atts': {
					'classes': '',
					'id': ''
				}
			},
			'radio': {
				'findex': _pmaf_index,
				'name': '',
				'type': 'radio',
				'multi': false,
				'label': pmaf_obj.strings.radiotext,
				'description': '',
				'default': '',
				'req': 'off',
				'req_msg': '',
				'options': {
					'ch_1': pmaf_obj.strings.opt + ' 1',
					'ch_2': pmaf_obj.strings.opt + ' 2',
					'ch_3': pmaf_obj.strings.opt + ' 3',
				},
				'atts': {
					'classes': '',
					'id': ''
				}
			},
			'checkbox': {
				'findex': _pmaf_index,
				'name': '',				
				'type': 'checkbox',
				'multi': true,
				'label': pmaf_obj.strings.checkboxtext,
				'description': '',
				'default': '',
				'req': 'off',
				'req_msg': '',
				'options': {
					'ch_1': pmaf_obj.strings.opt + ' 1',
					'ch_2': pmaf_obj.strings.opt + ' 2',
					'ch_3': pmaf_obj.strings.opt + ' 3',
				},
				'atts': {
					'classes': '',
					'id': ''
				}
			},
			'consent': {
				'findex': _pmaf_index,
				'name': '',				
				'type': 'consent',
				'multi': false,
				'label': pmaf_obj.strings.consent,
				'content': '<p>Yes, I agree with the <a href="#" target="_blank" rel="noopener">privacy policy</a> and <a href="#" target="_blank" rel="noopener">terms and conditions</a>.</p>',
				'description': '',
				'req': 'off',
				'req_msg': '',				
				'atts': {
					'classes': '',
					'id': ''
				}
			},
			'imageradio': {
				'findex': _pmaf_index,
				'name': '',
				'type': 'imageradio',
				'multi': false,
				'label': pmaf_obj.strings.imageradiotext,
				'description': '',
				'default': '',
				'req': 'off',
				'req_msg': '',
				'options': {
					'ch_1': pmaf_obj.strings.opt + ' 1',
					'ch_2': pmaf_obj.strings.opt + ' 2',
					'ch_3': pmaf_obj.strings.opt + ' 3',
				},
				'images': {},
				'atts': {
					'classes': '',
					'id': ''
				}
			},
			'slider': {
				'findex': _pmaf_index,
				'name': '',
				'type': 'slider',
				'label': pmaf_obj.strings.numbertext,
				'description': '',
				'default': '10',
				'result': pmaf_obj.strings.selected_val+ ' {value}',
				'atts': {
					'classes': '',
					'id': '',
					'min': '1',
					'max': '100',
					'step': '1',
				}
			},
			'file': {
				'findex': _pmaf_index,
				'name': '',
				'type': 'file',
				'label': pmaf_obj.strings.filetext,
				'description': '',
				'default': '',
				'atts': {
					'classes': '',
					'id': ''
				},
				'form': {
					'size': '5',
					'nfiles': '1',
					'accept': 'image/*,.pdf',
					'model': 'basic',
				}
			},
		};
		
		return _config[field_type];
		
	}
	
	function pmaf_get_fields_order() {
		
		let _fields_order = [];
		$(document).find("#pmaf-sortable > li").each(function(){
			_fields_order.push( $(this).find('.pmaf-field-options').data("field-id") );
		});
		return _fields_order;
		
	}
	
	function pmaf_create_saved_fields() {

		let _field_keys = [];
		
		let _field_html = '';
		$.each( _pmaf_field_values, function( field_id, field_data ) {
			_pmaf_index = field_data.findex;
			_field_keys.push(_pmaf_index);
			_field_html += '<li class="ui-state-highlight ui-draggable ui-draggable-handle" data-type="'+ field_data.type +'" data-field-id="'+ field_id +'">';
			_field_html += pmaf_create_field( field_data.type, field_data );
			_field_html += '</li>';
			_pmaf_index++;
		});
		$("#pmaf-sortable").html(_field_html);
		_pmaf_index = _field_keys.reduce( ( a, b ) => Math.max( a, b ), -Infinity );
		_pmaf_index++;
		
		$.each( _pmaf_field_values, function( field_id, field_data ) {
			_pmaf_current_field = field_id;
			pmaf_update_preview();
		});
		
	}
	
	function pmaf_field_options( field_type ) {
		
		let _options_html = '<div class="pmaf-field-options" data-type="'+ field_type +'" data-field-id="fi_'+ ( _pmaf_index ) +'"><a href="#" class="pmaf-field-edit"><i class="dashicons dashicons-edit"></i></a><a href="#" class="pmaf-field-delete"><i class="dashicons dashicons-trash"></i></a></div>';
		
		return _options_html;
		
	}
	
	function pmaf_field_pro_options( field_type ) {
		
		let _options_html = '<div class="pmaf-field-options" data-type="'+ field_type +'" data-field-id="fi_'+ ( _pmaf_index ) +'"><a href="#" class="pmaf-field-delete"><i class="dashicons dashicons-trash"></i></a></div>';
		
		return _options_html;
		
	}
	
	function pmaf_create_field( field_type, field_data ) {
		
		let _field_html = ''; let _edit_button_html = ''; 
		let _pro_btn_html = '<a href="'+ pmaf_obj.pro_link +'" class="pmaf-pro-link">'+ pmaf_obj.strings.get_pro +'</a>';
		
		_edit_button_html = field_type != 'file' ? pmaf_field_options( field_type ) : pmaf_field_pro_options( field_type );
		
		
		switch( field_type ) {
			
			case "text":
				_field_html = '<div class="pmaf-field pmaf-field-text"><label>'+ field_data.label +'</label><input type="text" placeholder="'+ field_data.placeholder +'" name="'+ field_data.name +'" value="'+ field_data.default +'" /><div class="pmaf-desc">'+ field_data.description +'</div></div>';
			break;
			
			/*case "name":
				_field_html = '<div class="pmaf-field pmaf-field-name"><label>'+ field_data.label +'</label>name Field<div class="pmaf-desc">'+ field_data.description +'</div></div>';
			break;*/
			
			case "link":
				_field_html = '<div class="pmaf-field pmaf-field-link"><label>'+ field_data.label +'</label><input type="url" placeholder="'+ field_data.placeholder +'" name="'+ field_data.name +'" value="'+ field_data.default +'" /><div class="pmaf-desc">'+ field_data.description +'</div></div>';
			break;
			
			case "phone":
				_field_html = '<div class="pmaf-field pmaf-field-phone"><label>'+ field_data.label +'</label><input type="text" placeholder="'+ field_data.placeholder +'" name="'+ field_data.name +'" value="'+ field_data.default +'" /><div class="pmaf-desc">'+ field_data.description +'</div></div>';
			break;
			
			case "editor":
			case "textarea":
				_field_html = '<div class="pmaf-field pmaf-field-textarea"><label>'+ field_data.label +'</label><textarea placeholder="'+ field_data.placeholder +'" name="'+ field_data.name +'" rows="'+ field_data.atts.rows +'">'+ field_data.default +'</textarea><div class="pmaf-desc">'+ field_data.description +'</div></div>';
			break;
						
			case "checkbox":
				_field_html = '<div class="pmaf-field pmaf-field-checkbox"><label>'+ field_data.label +'</label><div class="pmaf-checkbox-group"></div><div class="pmaf-desc">'+ field_data.description +'</div></div>';
			break;
			
			case "consent":
				_field_html = '<div class="pmaf-field pmaf-field-consent"><label>'+ field_data.label +'</label><div class="pmaf-checkbox"><input type="checkbox" name="'+ field_data.name +'" value="consent" /><div class="pmaf-consent-content">'+ field_data.content +'</div></div><div class="pmaf-desc">'+ field_data.description +'</div></div>';
			break;
			
			case "radio":
				_field_html = '<div class="pmaf-field pmaf-field-radio"><label>'+ field_data.label +'</label><div class="pmaf-radio-group"></div><div class="pmaf-desc">'+ field_data.description +'</div></div>';
			break;
			
			case "select":
				_field_html = '<div class="pmaf-field pmaf-field-select"><label>'+ field_data.label +'</label><select name="'+ field_data.name +'"></select><div class="pmaf-desc">'+ field_data.description +'</div></div>';
			break;
			
			case "imageradio":
				_field_html = '<div class="pmaf-field pmaf-field-imageradio"><label>'+ field_data.label +'</label><div class="pmaf-img-radio-group"></div><div class="pmaf-desc">'+ field_data.description +'</div></div>';
			break;
			
			case "number":
				_field_html = '<div class="pmaf-field pmaf-field-number"><label>'+ field_data.label +'</label><input type="number" placeholder="'+ field_data.placeholder +'" name="'+ field_data.name +'" min="'+ field_data.atts.min +'" max="'+ field_data.atts.max +'" step="'+ field_data.atts.step +'" value="'+ field_data.default +'" /><div class="pmaf-desc">'+ field_data.description +'</div></div>';
			break;
			
			case "email":
				_field_html = '<div class="pmaf-field pmaf-field-email"><label>'+ field_data.label +'</label><input type="email" placeholder="'+ field_data.placeholder +'" name="'+ field_data.name +'" value="'+ field_data.default +'" /><div class="pmaf-desc">'+ field_data.description +'</div></div>';
			break;
			
			case "slider":
				let _selected_val = field_data.result;
				_selected_val = _selected_val.replace( "{value}", '<span class="pmaf-range-val">'+ field_data.default +'</span>' );
				_field_html = '<div class="pmaf-field pmaf-field-slider"><label>'+ field_data.label +'</label><input type="range" class="pmaf-slider" name="'+ field_data.name +'" min="'+ field_data.atts.min +'" max="'+ field_data.atts.max +'" step="'+ field_data.atts.step +'" value="'+ field_data.default +'" /><div class="pmaf-slider-value">'+ _selected_val +'</div><div class="pmaf-desc">'+ field_data.description +'</div></div>';
			break;
			
			case "file":
				_field_html = '<div class="pmaf-field pmaf-field-file"><label>'+ field_data.label +'</label><div class="pmaf-file-inner pmaf-pro-alert">'+ pmaf_obj.strings.file_pro + _pro_btn_html +'</div>';
			break;
			
		}
		
		_field_html += _edit_button_html;
		
		return _field_html;
		
	}
	
	function pmaf_field_general_settings( _key, _val ) { 
		
		let _settings_html = '';		
		
		switch( _key ) {
						
			case "label":
				_settings_html = '<div class="pmaf-field pmaf-field-label"><label>'+ pmaf_obj.strings.label +'</label><input type="text" value="'+ _val +'" data-name="label" /></div>';
			break;
			
			case "content":
				_settings_html = '<div class="pmaf-field pmaf-field-content"><label>'+ pmaf_obj.strings.content +'</label><textarea rows="5" data-name="content">'+ _val +'</textarea></div>';
			break;
			
			case "description":
				_settings_html = '<div class="pmaf-field pmaf-field-description"><label>'+ pmaf_obj.strings.desc +'</label><textarea rows="5" data-name="description">'+ _val +'</textarea></div>';
			break;
						
			case "req":
				let _req_msg = _pmaf_field_values[_pmaf_current_field]["req_msg"] ? _pmaf_field_values[_pmaf_current_field]["req_msg"] : '';
				_settings_html = '<div class="pmaf-field pmaf-field-req"><input type="checkbox" data-name="req" '+ ( _val == 'on' ? 'checked="checked"' : '' ) +' /><label>'+ pmaf_obj.strings.req +'</label><div class="pmaf-req-msg-wrap"><div class="pmaf-req-msg-inner"><span>'+ pmaf_obj.strings.req_msg +'</span><input type="text" class="pmaf-req-msg-val" value="'+ _req_msg +'" /></div></div></div>';
			break;
			
			case "result":
				_settings_html = '<div class="pmaf-field pmaf-field-result"><label>'+ pmaf_obj.strings.val_disp +'</label><input type="text" data-name="result" value="'+ _val +'" /></div>';
			break;
			
			case "default":
				_settings_html = '<div class="pmaf-field pmaf-field-default"><label>'+ pmaf_obj.strings.default +'</label><input type="text" value="'+ _val +'" data-name="default" /></div>';
			break;
						
			case "placeholder":
				_settings_html = '<div class="pmaf-field pmaf-field-placeholder"><label>'+ pmaf_obj.strings.placeholder +'</label><input type="text" value="'+ _val +'" data-name="placeholder" /></div>';
			break;
			
		}
		
		return _settings_html;
		
	}
	
	function pmaf_field_attr_settings( _key, _val ) {
		
		let _attrs_html = '';		
		
		switch( _key ) {
			
			case "name":
				_val = _val ? _val : 'field_'+ _pmaf_current_field;
				_attrs_html = '<div class="pmaf-field pmaf-field-name"><label>'+ pmaf_obj.strings.field_name +'</label><input type="text" value="'+ _val +'" data-name="name" /><div class="pmaf-editor-field-desc">'+ pmaf_obj.strings.f_empty +'</div></div>';
			break;
			
			case "classes":
				_attrs_html = '<div class="pmaf-field pmaf-field-classes"><label>'+ pmaf_obj.strings.classes +'</label><input type="text" value="'+ _val +'" data-parent="atts" data-name="classes" /></div>';
			break;
			
			case "id":
				_attrs_html = '<div class="pmaf-field pmaf-field-id-name"><label>'+ pmaf_obj.strings.id +'</label><input type="text" value="'+ _val +'" data-parent="atts" data-name="id" /></div>';
			break;
			
			case "min":
				_attrs_html = '<div class="pmaf-field pmaf-field-min"><label>'+ pmaf_obj.strings.min +'</label><input type="number" value="'+ _val +'" data-parent="atts" data-name="min" /></div>';
			break;
			
			case "max":
				_attrs_html = '<div class="pmaf-field pmaf-field-max"><label>'+ pmaf_obj.strings.max +'</label><input type="number" value="'+ _val +'" data-parent="atts" data-name="max" /></div>';
			break;
			
			case "step":
				_attrs_html = '<div class="pmaf-field pmaf-field-step"><label>'+ pmaf_obj.strings.step +'</label><input type="number" value="'+ _val +'" data-parent="atts" data-name="step" /></div>';
			break;
							
		}
		
		return _attrs_html;
		
	}
	
	function pmaf_field_form_settings( _key, _val ) {
		
		let _forms_html = '';		
		
		switch( _key ) {
			
			case "size":
				_forms_html = '<div class="pmaf-field pmaf-field-file-size"><label>'+ pmaf_obj.strings.file_size +'</label><input type="text" value="'+ _val +'" data-parent="form" data-name="size" /><div class="pmaf-editor-field-desc">'+ pmaf_obj.strings.file_size_desc +'</div></div>';
			break;
			
			case "nfiles":
				_forms_html = '<div class="pmaf-field pmaf-field-file-nfiles"><label>'+ pmaf_obj.strings.nfiles +'</label><input type="text" value="'+ _val +'" data-parent="form" data-name="nfiles" /></div>';
			break;
			
			case "accept":
				_forms_html = '<div class="pmaf-field pmaf-field-accept"><label>'+ pmaf_obj.strings.accept +'</label><input type="text" value="'+ _val +'" data-parent="form" data-name="accept" /></div>';
			break;
			
			case "model": 
				_forms_html = '<div class="pmaf-field pmaf-field-file-model"><label>'+ pmaf_obj.strings.model +'</label><select data-parent="form" data-name="model"><option value="'+ pmaf_obj.strings.classic +'" '+ ( _val == pmaf_obj.strings.classic ? 'selected="selected"' : '' ) +'>'+ pmaf_obj.strings.classic +'</option><option value="'+ pmaf_obj.strings.modern +'" '+ ( _val == pmaf_obj.strings.modern ? 'selected="selected"' : '' ) +'>'+ pmaf_obj.strings.modern +'</option></select></div>';
			break;
			
		}
		
		return _forms_html;
		
	}
	
	function pmaf_field_options_row( key, value, _default, _type ) {
		
		let _img_html = '';
		if( _type == 'imageradio' ) {
			//_pmaf_current_field
			let _images = _pmaf_field_values[_pmaf_current_field].images;
			if( _images.hasOwnProperty(key) ) {
				let _attachment = _images[key];
				_img_html = '<div class="pmaf-img-item"><img src="'+ _attachment.url + '" class="'+ _attachment.id +'" /><span class="pmaf-img-item-remove"><i class="dashicons dashicons-no-alt"></i></span></div>';
			}
			
		}
		
		value = value ? value : pmaf_obj.strings.opt +' '+ key.replace("ch_","");
		let _row_html = '<li class="'+ ( _img_html ? 'image-active' : '' ) +'" data-id="'+ key +'"><input type="text" value="'+ value +'" /><a href="#" class="pmaf-options-add-more" title="'+ pmaf_obj.strings.am +'"><i class="dashicons dashicons-plus-alt2"></i></a><a href="#" class="pmaf-options-remove" title="'+ pmaf_obj.strings.remove +'"><i class="dashicons dashicons-no-alt"></i></a><a href="#" class="pmaf-options-make-def'+ ( _default != '' && _default == value ? ' active' : '' ) +'" title="'+ pmaf_obj.strings.md +'"><i class="dashicons dashicons-saved"></i></a>';
		
		if( _type == 'imageradio' ) { 
			if( _img_html ) {
				_row_html += _img_html;
			}
			_row_html += '<a href="#" class="pmaf-upload-img"><i class="dashicons dashicons-cloud-upload"></i></a>';
		}
		_row_html += '</li>';
		return _row_html;
	}
	
	function pmaf_field_options_settings( _options, _default, _type ) {
		
		let _options_html = '<ul class="pmaf-field pmaf-field-dd">';	
				
		if( _options ) {
			$.each( _options, function( key, value ) {
				_options_html += pmaf_field_options_row( key, value, _default, _type );
			});
		}
		
		_options_html += '</ul>';
		
		return _options_html;
		
	}
	
	function pmaf_update_form_atts_values( _field_id ) {
		
		if( _pmaf_field_values[_field_id] ) { 
			let _field_ele = $(document).find('li[data-field-id="'+ _field_id +'"]');
			$.each(_pmaf_field_values[_field_id].atts, function( key, value ) {
				switch( key ) {
					
					case "min":
						$(_field_ele).find(".pmaf-field input").attr("min", value);
					break;
					
					case "max":
						$(_field_ele).find(".pmaf-field input").attr("max", value);
					break;
					
					case "step":
						$(_field_ele).find(".pmaf-field input").attr("step", value);
					break;					
					
					case "accept": 
						$(_field_ele).find(".pmaf-field input").attr("accept",value);
					break;
				}
			});
		}
		
	}
	
	function pmaf_update_form_option_values( _field_id ) {
		
		if( _pmaf_field_values[_field_id] ) { 
			let _field_ele = $(document).find('li[data-field-id="'+ _field_id +'"]');
			$.each(_pmaf_field_values[_field_id].form, function( key, value ) {
				switch( key ) {			
					
					case "accept": 
						$(_field_ele).find(".pmaf-field input").attr("accept",value);
					break;
				}
			});
		}
		
	}
		
	function pmaf_update_form_values( _field_id ) {
				
		if( _pmaf_field_values[_field_id] ) { 
			let _type = _pmaf_field_values[_field_id]['type'];
			let _field_ele = $(document).find('li[data-field-id="'+ _field_id +'"]');
			$.each(_pmaf_field_values[_field_id], function( key, value ) {
				switch( key ) {
					case "label":
						$(_field_ele).find(".pmaf-field label").html(value);
					break;
					
					case "description":
						/*if( value ) {
							$(_field_ele).find(".pmaf-field .pmaf-desc").html(value);
						} else {
							$(_field_ele).find(".pmaf-field .pmaf-desc").html();
						}*/
						$(_field_ele).find(".pmaf-field .pmaf-desc").html(value);
					break;
					
					case "default":
						if( _type == 'text' ) {
							$(_field_ele).find(".pmaf-field-text input").val(_pmaf_field_values[_field_id]['default']);
						} else if( _type == 'number' ) {
							$(_field_ele).find(".pmaf-field-number input").val(_pmaf_field_values[_field_id]['default']);
						} else if( _type == 'email' ) {
							$(_field_ele).find(".pmaf-field-email input").val(_pmaf_field_values[_field_id]['default']);
						} else if( _type == 'slider' ) {
							$(_field_ele).find(".pmaf-field-slider input").val(_pmaf_field_values[_field_id]['default']);
							let _selected_val = _pmaf_field_values[_field_id].result;
							_selected_val = _selected_val.replace( "{value}", _pmaf_field_values[_field_id].default );
							$(document).find('.pmaf-preview-fields li[data-field-id="'+ _field_id +'"] .pmaf-slider-value').html(_selected_val);
						} else if( _type == 'textarea' ) {
							$(_field_ele).find(".pmaf-field-textarea textarea").val(_pmaf_field_values[_field_id]['default']);
						}
					break;
					
					case "placeholder":
						let _ftype = _pmaf_field_values[_field_id]['type'];
						if( _ftype == 'text' ) {
							$(_field_ele).find(".pmaf-field-text input").attr("placeholder", _pmaf_field_values[_field_id]['placeholder']);
						} else if( _ftype == 'number' ) {
							$(_field_ele).find(".pmaf-field-number input").attr("placeholder", _pmaf_field_values[_field_id]['placeholder']);
						} else if( _ftype == 'email' ) {
							$(_field_ele).find(".pmaf-field-email input").attr("placeholder", _pmaf_field_values[_field_id]['placeholder']);
						} else if( _ftype == 'textarea' ) {
							$(_field_ele).find(".pmaf-field-textarea textarea").attr("placeholder", _pmaf_field_values[_field_id]['placeholder']);
						} else if( _ftype == 'select' ) {
							$(_field_ele).find(".pmaf-field-select select .placeholder").remove();
							$(_field_ele).find(".pmaf-field-select select").prepend('<option value="" class="placeholder">'+ _pmaf_field_values[_field_id]['placeholder'] +'</option>');
							$(_field_ele).find(".pmaf-field-select select").val("");
						}
					break;
					
				}
			});
		}
		
	}
	
	function pmaf_update_dd_options_order( _field_id ) {
		
		if( $(document).find(".pmaf-field-dd").length ) {
			
			let _new_options = {};
			$(document).find("ul.pmaf-field-dd > li").each(function(){
				_new_options[$(this).data("id")] = $(this).find("input").val();
			});
			_pmaf_field_values[_field_id].options = _new_options;
			pmaf_update_preview();
			
	
		}
		
	}
	
	function pmaf_img_remove_opt() {
		
		$(document).find(".pmaf-img-item-remove").off("click");
		$(document).find(".pmaf-img-item-remove").on("click", function(){
			let _cur_id = $(this).parents("li").data("id");
			delete _pmaf_field_values[_pmaf_current_field]['images'][_cur_id];
			$(this).parents("li").removeClass("image-active");
			$(this).parent(".pmaf-img-item").remove();
			pmaf_update_preview();
		});	
		
	}
	
	function pmaf_update_preview() {
		
		let _current_field = _pmaf_field_values[_pmaf_current_field];
		let _field_html = ''; let _default = '';
		switch( _current_field.type ) {
			case "checkbox":
				_default = _current_field.default;
				$.each( _current_field.options, function( key, _val ) {
					_field_html += '<div class="pmaf-checkbox-single"><input type="checkbox" name="'+ _current_field.name +'" value="'+ _val +'" '+ ( _default != '' && _default == _val ? ' checked' : '' ) +' ><label>'+ _val +'</label></div>';					
				});
				$(document).find('.pmaf-preview-fields li[data-field-id="'+ _pmaf_current_field +'"] .pmaf-checkbox-group').html(_field_html);
			break;
			case "radio":
				_default = _current_field.default;
				$.each( _current_field.options, function( key, _val ) {
					_field_html += '<div class="pmaf-radio-single"><input type="radio"  name="'+ _current_field.name +'" value="'+ _val +'" '+ ( _default != '' && _default == _val ? ' checked' : '' ) +' ><label>'+ _val +'</label></div>';
				});
				$(document).find('.pmaf-preview-fields li[data-field-id="'+ _pmaf_current_field +'"] .pmaf-radio-group').html(_field_html);
			break;
			case "select":
				_default = _current_field.default;
				let _placeholder = _pmaf_field_values[_pmaf_current_field]['placeholder'];
				if( _placeholder != '' && _default == '' ) {
					_field_html += '<option value="">'+ _placeholder +'</option>';
					_default = '';
				}
				$.each( _current_field.options, function( key, _val ) {
					_field_html += '<option value="'+ _val +'" '+ ( _default != '' && _default == _val ? ' selected="selected"' : '' ) +'>'+ _val +'</option>';
				});
				$(document).find('.pmaf-preview-fields li[data-field-id="'+ _pmaf_current_field +'"] .pmaf-field-select select').html(_field_html);
			break;
			case "imageradio":
				if( !_current_field.hasOwnProperty('images') ) { _current_field['images'] = {}; }
				_default = _current_field.default; let _images = _current_field.images;
				$.each( _current_field.options, function( key, value ) {
					_field_html += '<div class="pmaf-img-radio-single'+ ( _default != '' && _default == value ? ' selected' : '' ) +'"><input type="radio" name="'+ _current_field.name +'" value="'+ value +'" '+ ( _default != '' && _default == value ? ' checked' : '' ) +'>';
					if( _images.hasOwnProperty(key) ) {
						_field_html += '<img src="'+ _images[key].url +'" /><label>'+ value +'</label>';
					} else {
						_field_html += '<span class="pmaf-empty-img"><i class="dashicons dashicons-media-default"></i></span><label>'+ value +'</label>';
					}
					_field_html += '</div>';					
				});
				$(document).find('.pmaf-preview-fields li[data-field-id="'+ _pmaf_current_field +'"] .pmaf-img-radio-group').html(_field_html);
			break;
			case "file":
				let _form_model = _current_field.form.model;
				if( _form_model == 'Modern' ) {
					let _modern_html = '<div class="pmaf-modern-file-field"><figure><svg xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17"><path d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"></path></svg></figure><span>'+ pmaf_obj.strings.file_field +'</span></div>';
					$(document).find('.pmaf-preview-fields li[data-field-id="'+ _pmaf_current_field +'"] .pmaf-modern-file-field').remove();
					$(document).find('.pmaf-preview-fields li[data-field-id="'+ _pmaf_current_field +'"] .pmaf-file-inner').append(_modern_html);
					$(document).find('.pmaf-preview-fields li[data-field-id="'+ _pmaf_current_field +'"] .pmaf-file-inner').addClass("pmaf-modern");
				} else {
					$(document).find('.pmaf-preview-fields li[data-field-id="'+ _pmaf_current_field +'"] .pmaf-modern-file-field').remove();
					$(document).find('.pmaf-preview-fields li[data-field-id="'+ _pmaf_current_field +'"] .pmaf-file-inner').removeClass("pmaf-modern");
				}
			break;
			
		}
		
	}
	
	function pmaf_close_field_edit( _field_id, _fields_config ) {
		
		// close edit wrap
		$(document).find(".pmaf-field-options-close").off( "click");
		$(document).find(".pmaf-field-options-close").on( "click", function(e){
			e.preventDefault();
			// editing stat update
			$(document).find("#pmaf-sortable > li").removeClass("editing");

			$(".pmaf-editor-options").removeClass("active");
			$(".pmaf-preview-trigger").show();
			return false;
		});
		
		// tab click
		$(document).find(".pmaf-form-tab > li").off( "click");
		$(document).find(".pmaf-form-tab > li").on( "click", function(e){
			e.preventDefault();
			$(document).find(".pmaf-form-tab > li, .pmaf-form-tab-content").removeClass("active");			
			$(this).addClass("active");
			$(document).find('#'+ $(this).data("id")).addClass("active");
			return false;
		});
		
		// field input event
		$(document).find(".pmaf-editor-options .pmaf-field input, .pmaf-editor-options .pmaf-field textarea").off( "input");
		$(document).find(".pmaf-editor-options .pmaf-field input, .pmaf-editor-options .pmaf-field textarea").on( "input", function(e){
			e.preventDefault();
			
			_pmaf_field_values[_field_id] = _fields_config;
			let _name = $(this).data("name");
			let _parent = $(this).data("parent");
			if( _parent == 'atts' ) {				
				_pmaf_field_values[_field_id]['atts'][_name] = $(this).val();				
				// update form values
				pmaf_update_form_atts_values( _field_id );				
			} else if( _parent == 'form' ) {				
				_pmaf_field_values[_field_id]['form'][_name] = $(this).val();
				// update form values
				pmaf_update_form_option_values( _field_id );				
			} else {
				_pmaf_field_values[_field_id][_name] = $(this).val();
				// update form values
				pmaf_update_form_values( _field_id );				
			}
			
			return false;
		});
		
		// field checkbox event
		$(document).find('.pmaf-editor-options .pmaf-field input[type="checkbox"]').off( "change");
		$(document).find('.pmaf-editor-options .pmaf-field input[type="checkbox"]').on( "change", function(e){
			
			_pmaf_field_values[_field_id] = _fields_config;
			let _name = $(this).data("name");
			let _parent = $(this).data("parent");
			
			let _value = 'off';
			if ($(this).is(':checked')) {
				_value = 'on';
			}
				
			if( _parent == 'atts' ) {
				_pmaf_field_values[_field_id]['atts'][_name] = _value;
				// update form values
				pmaf_update_form_atts_values( _field_id );
			} else if( _parent == 'form' ) {				
				_pmaf_field_values[_field_id]['form'][_name] = $(this).val();
				// update form values
				pmaf_update_form_option_values( _field_id );				
			} else {			
				_pmaf_field_values[_field_id][_name] = _value;
				// update form values
				pmaf_update_form_values( _field_id );
			}
			
		});
		
		// field select event
		$(document).find('.pmaf-editor-options select').off( "change");
		$(document).find('.pmaf-editor-options select').on( "change", function(e){
			_pmaf_field_values[_field_id] = _fields_config;
			let _name = $(this).data("name");
			let _parent = $(this).data("parent");
				
			if( _parent == 'form' ) {				
				_pmaf_field_values[_field_id]['form'][_name] = $(this).val();
				// update form values
				pmaf_update_form_option_values( _field_id );
				
				//update preview
				pmaf_update_preview();
			}
		});
				
		// dd make default event
		$(document).find(".pmaf-options-make-def").off( "click");
		$(document).find(".pmaf-options-make-def").on( "click", function(e){			
			e.preventDefault();
			
			let _default = '';
			if( $(this).hasClass("active") ) {
				
				let _cur_value = $(this).parent("li").find("input").val();
				/*if( _pmaf_field_values[_field_id]['multi'] != true ) {
					$(this).removeClass("active");
				} else {
					//$(this).parents("ul.pmaf-field-dd").find(".pmaf-options-make-def").removeClass("active");
				}*/
				$(this).removeClass("active");
				
				if( _pmaf_field_values[_field_id].type == 'select' ) {
					$(document).find('li[data-field-id="'+ _field_id +'"] select option').removeAttr("selected");
				} else if( _pmaf_field_values[_field_id].type == 'radio' ) {
					$(document).find('li[data-field-id="'+ _field_id +'"] .pmaf-radio-group input').removeAttr("checked");
				}  else if( _pmaf_field_values[_field_id].type == 'checkbox' ) {
					$(document).find('li[data-field-id="'+ _field_id +'"] .pmaf-checkbox-group input[value="'+ _cur_value +'"]').removeAttr("checked");
				}
				_pmaf_field_values[_field_id]['default'] = '';
			} else {
				if( _pmaf_field_values[_field_id]['multi'] != true ) {
					$(this).parents("ul.pmaf-field-dd").find(".pmaf-options-make-def").removeClass("active");
				}				
				$(this).addClass("active");
				_default = _pmaf_field_values[_field_id].options[$(this).parent("li").data("id")];			
				_pmaf_field_values[_field_id]['default'] = _default;
				if( _pmaf_field_values[_field_id].type == 'select' ) {
					$(document).find('li[data-field-id="'+ _field_id +'"] select').val(_default).change();
				} else if( _pmaf_field_values[_field_id].type == 'radio' ) {
					$(document).find('li[data-field-id="'+ _field_id +'"] .pmaf-radio-group input[value="'+ _default +'"]').val(_default).trigger("click");
				} else if( _pmaf_field_values[_field_id].type == 'checkbox' ) {
					$(document).find('li[data-field-id="'+ _field_id +'"] .pmaf-checkbox-group input[value="'+ _default +'"]').val(_default).trigger("click");
				}
			}

			return false;
		});
		
		// set the req msg		
		if( !_pmaf_field_values[_pmaf_current_field].hasOwnProperty("req_msg") ) {			
			_pmaf_field_values[_pmaf_current_field]["req_msg"] = '';
		}
		$(document).find('.pmaf-req-msg-wrap').hide();
		if( $(document).find('.pmaf-field-req input[type="checkbox"]').is(':checked')) {
			$(document).find('.pmaf-req-msg-wrap').show();
		}		
		$(document).find('.pmaf-field-req input[type="checkbox"]').off( "click");
		$(document).find('.pmaf-field-req input[type="checkbox"]').on( "click", function(e){	
			if( $(this).is(':checked')) {
				$(document).find('.pmaf-req-msg-wrap').show();				
			} else {
				$(document).find('.pmaf-req-msg-wrap').hide();
			}
			_pmaf_field_values[_pmaf_current_field]["req_msg"] = $(document).find('.pmaf-req-msg-val').val();
		});
		$(document).find('.pmaf-field-req input.pmaf-req-msg-val').off( "input");
		$(document).find('.pmaf-field-req input.pmaf-req-msg-val').on( "input", function(e){	
			_pmaf_field_values[_pmaf_current_field]["req_msg"] = $(document).find('.pmaf-req-msg-val').val();
		});
		
		
		// dd option remove event
		$(document).find(".pmaf-options-remove").off( "click");
		$(document).find(".pmaf-options-remove").on( "click", function(e){
			e.preventDefault();
			delete _pmaf_field_values[_field_id].options[$(this).parent("li").data("id")];
			$(this).parent("li").remove();
			pmaf_update_dd_options_order( _field_id );
			
			if( _pmaf_field_values[_field_id].type == 'imageradio' ) {
				pmaf_update_preview();
			}
			
			return false;
		});

		// dd option add more event
		$(document).find(".pmaf-options-add-more").off( "click");
		$(document).find(".pmaf-options-add-more").on( "click", function(e){			
			e.preventDefault();
			
			let _cur_ele = $(this);
			let _options = _pmaf_field_values[_field_id].options;
			let _options_keys = [];
			$.each( _options, function( key, value ) {
				_options_keys.push( key.replace("ch_","") );
			});
			let _ch_index = _options_keys.reduce( ( a, b ) => Math.max( a, b ), -Infinity );
			_ch_index++;
			let _option_html = pmaf_field_options_row( 'ch_'+ _ch_index, '', '', _pmaf_field_values[_field_id].type );
			$(_option_html).insertAfter($(_cur_ele).parent("li"));
			
			pmaf_close_field_edit( _field_id, _fields_config );
			
			pmaf_update_dd_options_order( _field_id );
			
			if( _pmaf_field_values[_field_id].type == 'imageradio' ) {
				pmaf_update_preview();
			}
			
			return false;
		});
		
		// dd option input event
		$(document).find(".pmaf-field-dd input").off( "input");
		$(document).find(".pmaf-field-dd input").on( "input", function(e){	
			e.preventDefault();
			pmaf_update_dd_options_order( _field_id );
			return false;
		});
		
		// image upload
		$(document).find(".pmaf-upload-img").off( 'click' );
		$(document).find(".pmaf-upload-img").on( 'click', function( event ){

			event.preventDefault(); // prevent default link click and page refresh
			
			const _cur_ele = $(this)
			const imageId = _cur_ele.next().next().val();
			
			const customUploader = wp.media({
				title: 'Insert image', // modal window title
				library : {
					type : 'image'
				},
				button: {
					text: 'Use this image' // button label text
				},
				multiple: false
			}).on( 'select', function() { // it also has "open" and "close" events
				const attachment = customUploader.state().get( 'selection' ).first().toJSON();
				let _f_id = _cur_ele.parent("li").data("id");
				_pmaf_field_values[_pmaf_current_field]['images'][_f_id] = { 'id': attachment.id, 'url': attachment.url }; // attachment.url, attachment.id
				$('<div class="pmaf-img-item"><img src="'+ attachment.url + '" class="'+ attachment.id +'" /><span class="pmaf-img-item-remove"><i class="dashicons dashicons-no-alt"></i></span></div>').insertBefore(_cur_ele);
				_cur_ele.parent("li").addClass("image-active");
				
				pmaf_img_remove_opt();
				
				pmaf_update_preview();
				
							
				
			})
			
			// already selected images
			customUploader.on( 'open', function() {

				if( imageId ) {
				  const selection = customUploader.state().get( 'selection' )
				  attachment = wp.media.attachment( imageId );
				  attachment.fetch();
				  selection.add( attachment ? [attachment] : [] );
				}
				
			})

			customUploader.open()
		
		});
		
	}
	
	function pmaf_field_options_events() {
		
		// delete field set
		$(document).find(".pmaf-field-delete").off( "click");
		$(document).find(".pmaf-field-delete").on( "click", function(e){
			e.preventDefault();
			let _field_ele = $(this);			
			
			// get current field id
			_pmaf_current_field = $(_field_ele).parent(".pmaf-field-options").data("field-id");
			delete _pmaf_field_values[_pmaf_current_field]; 
						
			$.confirm({
				title: 'Delete Confirmation!',
				content: 'Are you sure want to delete this field?',
				buttons: {
					confirm: function () {
						$(_field_ele).parents("li.ui-draggable").remove();
						if( $(".pmaf-editor-options").hasClass("active") ) {
							setTimeout( function(){ $(".pmaf-editor-options").removeClass("active"); }, 700 );
						}
						if( !$(document).find("#pmaf-sortable").html() ) {
							$(".pmaf-preview-fields").prepend('<div class="no-fields-preview">'+ pmaf_arrow_svg() + pmaf_obj.strings.empty_form +'</div>');
							$(document).find("#pmaf-sortable").removeClass("pmaf-fields-exists");
						}
					},
					cancel: function(){}
				}
			});		
			return false;
		});
		
		// edit field
		$(document).find(".pmaf-field-edit").off( "click");
		$(document).find(".pmaf-field-edit").on( "click", function(e){
			e.preventDefault();
			
			// editing stat update
			$(document).find("#pmaf-sortable > li").removeClass("editing");
			$(this).parents("li").addClass("editing");
			
			let _field_type = $(this).parent(".pmaf-field-options").data("type");
			
			// get current field id
			_pmaf_current_field = $(this).parent(".pmaf-field-options").data("field-id");
			$(this).parents("li").attr("data-field-id", _pmaf_current_field);
			
			// get saved or typed or default values
			let _fields_config = '';
			if( _pmaf_field_values[_pmaf_current_field] ) {
				_fields_config = _pmaf_field_values[_pmaf_current_field];
			} else {
				_fields_config = pmaf_single_field_config( _field_type );
			}
			
			let _fields_html = '<a href="#" class="pmaf-field-options-close"><span class="dashicons dashicons-no-alt"></span></a><ul class="pmaf-form-tab"><li class="pmaf-form-tab-item active" data-id="pmaf-tab-genral"><span>'+ pmaf_obj.strings.gen +'</span></li>';
			if( _fields_config.hasOwnProperty('options') ) {
				_fields_html += '<li class="pmaf-tab-item" data-id="pmaf-tab-options"><span>'+ pmaf_obj.strings.options +'</span></li>';
			}
			if( _fields_config.hasOwnProperty('form') ) {
				_fields_html += '<li class="pmaf-tab-item" data-id="pmaf-tab-form"><span>'+ pmaf_obj.strings.form_opt +'</span></li>';
			}
			_fields_html += '<li class="pmaf-tab-item" data-id="pmaf-tab-attrs"><span>'+ pmaf_obj.strings.attrs +'</span></li>';
			_fields_html += '</ul>';
			
			// general tab
			_fields_html += '<div id="pmaf-tab-genral" class="pmaf-form-tab-content active">';
			let _field_attrs = ''; let _field_options = ''; let _form_fields = '';
			
			// field name change to atts tab - start v1.0.2 update
			if( _fields_config['atts'].hasOwnProperty("name") ) {
				delete _fields_config['atts']['name'];
			}
			let _cur_name = _fields_config['name'];
			let _t_obj = {name: _cur_name};
			_fields_config['atts'] = { ..._t_obj, ..._fields_config.atts };			
			// field name change to atts tab - end			
			
			$.each( _fields_config, function( key, value ) {
				if( ( _field_type != 'select' || key != 'default' ) && ( _field_type != 'radio' || key != 'default' ) && ( _field_type != 'checkbox' || key != 'default' ) && ( _field_type != 'imageradio' || key != 'default' ) ) {
					_fields_html += pmaf_field_general_settings( key, value );
				}
				if( key == 'atts' ) _field_attrs = value;
				else if( key == 'options' ) _field_options = value;
				else if( key == 'form' ) _form_fields = value;
			});			
			_fields_html += '</div>';
			
			// options tab
			if( _field_options ) {
				_fields_html += '<div id="pmaf-tab-options" class="pmaf-form-tab-content">';
				_fields_html += pmaf_field_options_settings( _field_options, _pmaf_field_values[_pmaf_current_field]['default'], _pmaf_field_values[_pmaf_current_field].type );
				_fields_html += '</div>';
			}
			
			// attributes tab
			if( _field_attrs ) {
				_fields_html += '<div id="pmaf-tab-attrs" class="pmaf-form-tab-content">';
				$.each( _field_attrs, function( key, value ) {
					_fields_html += pmaf_field_attr_settings( key, value );
				});
				_fields_html += '</div>';
			}
						
			// form tab
			if( _form_fields ) {
				_fields_html += '<div id="pmaf-tab-form" class="pmaf-form-tab-content">';
				$.each( _form_fields, function( key, value ) {
					_fields_html += pmaf_field_form_settings( key, value );
				});
				_fields_html += '</div>';
			}
			
			$(".pmaf-editor-options").html(_fields_html);
			$(".pmaf-editor-options").addClass("active");
			
			// sort fields update
			if( _field_options ) {
				
				// set default value
				pmaf_update_dd_options_order( _pmaf_current_field );
				
				$(document).find( "#pmaf-tab-options .pmaf-field.pmaf-field-dd" ).sortable({
					revert: true,
					placeholder: "ui-state-highlight",
					stop: function( event, ui ) {
						pmaf_update_dd_options_order( _pmaf_current_field );
					}
				});		
			
			}
			
			// close edit wrap
			pmaf_close_field_edit( _pmaf_current_field, _fields_config );
			
			if( _pmaf_field_values[_pmaf_current_field].type == 'imageradio' ) {
				pmaf_img_remove_opt();
			}
			
			return false;
		});			
		
	}
	
	function pmaf_search_fields( _input_id, _wrap_class, _item_class ) {
		var input, filter, wrap, items, a, p, i, tit, des;
		input = document.getElementById(_input_id);
		filter = input.value.toUpperCase();
		wrap = document.getElementsByClassName(_wrap_class);
		items = wrap[0].getElementsByClassName(_item_class);
		for( i = 0; i < items.length; i++ ) {
			a = items[i].getElementsByTagName("span")[0];
			if( a.textContent !== undefined ) {
				tit = a.textContent || a.innerText ;
				if( tit.toUpperCase().indexOf( filter ) > -1 ) {
					items[i].style.display = "";
				} else {
					items[i].style.display = "none";
				}
			}
		}
	}
	
	function pmaf_search_templates( _input_id, _list_id, _item_class ) {
		var input, filter, wrap, items, a, p, i, tit, des;
		input = document.getElementById(_input_id);
		filter = input.value.toUpperCase();
		wrap = document.getElementById(_list_id);
		items = wrap.getElementsByClassName(_item_class);
		for( i = 0; i < items.length; i++ ) {
			a = items[i].getElementsByTagName("h3")[0];
			p = items[i].getElementsByTagName("p")[0];
			tit = a.textContent || a.innerText ;
			des = p.textContent || p.innerText ;
			if( tit.toUpperCase().indexOf( filter ) > -1 || des.toUpperCase().indexOf( filter ) > -1 ) {
				items[i].style.display = "";
			} else {
				items[i].style.display = "none";
			}
		}
	}
	
	function pmaf_category_templates( _input_id, _list_id, _item_class, _category ) {
		var input, wrap, items, i;
		input = document.getElementById(_input_id);
		wrap = document.getElementById(_list_id);
		$('#'+ _list_id).find('.'+ _item_class).hide();
		items = wrap.querySelectorAll('[data-category*="'+ _category +'"]');
		for( i = 0; i < items.length; i++ ) {
			items[i].style.display = "";
		}
	}
	
	function pmaf_sub_tab_process() {
		let _parent_sub_tab = $(document).find(".pmaf-main-tab-content.active");
		_parent_sub_tab.find(".pmaf-sub-tab-content").hide();
		
		_parent_sub_tab.find("h3").off("click");
		_parent_sub_tab.find("h3").on("click", function(e){
			e.preventDefault();
			$(this).next(".pmaf-sub-tab-content").slideToggle(350);
			return false;
		});
		
		$(document).find(".pmaf-main-tab-content.active > h3:first-child").next(".pmaf-sub-tab-content").slideDown(0);
		
	}
	
	function pmaf_form_pagination() {
		
		if( $(document).find("#pmaf-forms-pagination").length ) {
			//console.log("test");
			let _ele = $(document).find("#pmaf-forms-pagination");
			let _total = _ele.data("total");
			let _page_limit = _ele.data("limit");
			let _page_current = _ele.data("current");
			
			_ele.pagination({
				items: _total,
				itemsOnPage: _page_limit,
				currentPage: _page_current,
				prevText: "&laquo;",
				nextText: "&raquo;",
				onPageClick: function( pageNumber, event) {		
					window.location = pmaf_obj.pmaf_page + '&p='+ pageNumber;
				}
			});
			
		}
	}
	
	function pmaf_instant_result( _target = '', _value = '' ) {

		let form_html = $('#pmaf-form-preview-frame').contents().find('.pmaf-front-end-preview');
		let _form_id = form_html.find(".pmaf-form-wrap").attr("id");
					
		if( _target ) {
			
			let _instant_atts = pmaf_get_process_id_fun( _target, _form_id );
			if( _instant_atts.hasOwnProperty('reflect') ) {
				if( _instant_atts.type == 'select' ) {
					if( _instant_atts.process == 'class' ) {
						let _cur_class = $(form_html).find(_instant_atts.reflect).attr("class");
						if( _cur_class ) {
							$.each(_instant_atts.exclass, function( index, value ) {
								_cur_class = _cur_class.replace( _instant_atts.prefix + value, _instant_atts.prefix + _value );
							});
							$(form_html).find(_instant_atts.reflect).attr("class", _cur_class);
						}
					}
				} else if( _instant_atts.type == 'option' ) {
					let _cur_class = $(form_html).find(_instant_atts.reflect).attr("class");
					if( _cur_class ) {
						$.each(_instant_atts.exclass, function( index, value ) {
							_cur_class = _cur_class.replace( _instant_atts.prefix + value, '' );
							if( _value == 'on' ) {
								_cur_class += value;
							}
							$(form_html).find(_instant_atts.reflect).attr("class", _cur_class);
						});
					}
				} else if( _instant_atts.type == 'text' ) {
					if( _instant_atts.process == 'html' ) {
						$(form_html).find(_instant_atts.reflect).html(_value);
					}
				} else if( _instant_atts.type == 'editor' ) {
					$(form_html).find(_instant_atts.reflect).html(_value);
				}
			} else if( _instant_atts.hasOwnProperty('function') ) {
				if( _instant_atts['function'] == 'title' ) {
					pmaf_preview_title_append();
				}
			}
			
		}
		
		
		if( $('#pmaf-form-preview-frame').contents().find("#pmaf-instant-form-styles").length ) {
			let _style_out = pmaf_instant_style();
			if( _style_out ) {
				$('#pmaf-form-preview-frame').contents().find("#pmaf-instant-form-styles").html( _style_out );
			}
		}
		
	}
		
	function pmaf_get_process_id_fun( _target, _form_id ) {
		
		let _fun_obj = {
			'btn-btnstyle': { 'type': 'select', 'process': 'class', 'reflect': '.pmaf-submit', 'prefix': 'pmaf-btn-', 'exclass': [ 'modern', 'default', 'classic' ] },
			'outer-enable_filter': { 'type': 'option', 'process': 'class', 'reflect': '.pmaf-form-wrap', 'prefix': '', 'exclass': [ ' pmaf-bg-gradient-ani' ] },
			'labels-label_move': { 'type': 'option', 'process': 'class', 'reflect': '.pmaf-field', 'prefix': '', 'exclass': [ ' pmaf-label-animate' ] },
			'labels-label_gradient': { 'type': 'option', 'process': 'class', 'reflect': '.pmaf-field', 'prefix': '', 'exclass': [ ' pmaf-hue-animate' ] },
			'title-enable_title': { 'type': 'option', 'function': 'title', },
			'title-inner_title_opt': { 'type': 'option', 'function': 'title', },
			'title-enable_title_desc': { 'type': 'option', 'function': 'title', },
			'title-inner_desc_opt': { 'type': 'option', 'function': 'title', },
			'title-text': { 'type': 'text', 'process': 'html', 'reflect': '.pmaf-form-title' },
			'title-description': { 'type': 'editor', 'reflect': '.pmaf-form-title-description' },
		}
		
		return _fun_obj.hasOwnProperty(_target) ? _fun_obj[_target] : '';
		
	}
	
	function pmaf_preview_title_append() {
		
		if( _pmaf_ani_settings.hasOwnProperty('title') ) {			
			let _title_output = ''; let _title_desc_output = ''; let _tit_desc_stat = false;
			let _title_stng = _pmaf_ani_settings['title'];	
			if( _title_stng.hasOwnProperty('enable_title') && _title_stng['enable_title'] == 'on' && _title_stng.hasOwnProperty('text') ) {
				let _title_txt = _title_stng['text'];
				let _title_tag = _title_stng.hasOwnProperty('tag') ? _title_stng['tag'] : 'h3';
				if( _title_txt ) {
					_tit_desc_stat = true;
					_title_output += '<'+ _title_tag +' class="pmaf-form-title">'+ _title_txt +'</'+ _title_tag +'>';
				}
			}
			if( _title_stng.hasOwnProperty('enable_title_desc') && _title_stng['enable_title_desc'] == 'on' && _title_stng.hasOwnProperty('description') ) {
				let _title_description = _title_stng['description'];
				if( _title_description  ) {
					_tit_desc_stat = true;
					_title_desc_output += '<div class="pmaf-form-title-description">'+ _title_description +'</div>';
				}					
			}

			
			// remove older title warp
			$('#pmaf-form-preview-frame').contents().find(".pmaf-form-title-wrap").remove();
			
			let _output = '';
			let _tit_inner_stat = _title_output && _title_stng.hasOwnProperty('inner_title_opt') && _title_stng['inner_title_opt'] == 'on' ? true : false;
			let _desc_inner_stat = _title_desc_output && _title_stng.hasOwnProperty('inner_desc_opt') && _title_stng['inner_desc_opt'] == 'on' ? true : false;
			
			if( _tit_desc_stat && ( _tit_inner_stat || _desc_inner_stat ) ) {
				_output += _tit_inner_stat ? _title_output : '';
				_output += _desc_inner_stat ? _title_desc_output : '';
				if( _output ) {
					_output = '<div class="pmaf-form-title-wrap">'+ _output +'</div>';
					$('#pmaf-form-preview-frame').contents().find(".pmaf-form-wrap form").prepend(_output);
				}
			}
			
			_output = '';
			if( _tit_desc_stat && ( !_tit_inner_stat || !_desc_inner_stat ) ) {				
				_output += !_tit_inner_stat ? _title_output : '';
				_output += !_desc_inner_stat ? _title_desc_output : '';
				if( _output ) {
					_output = '<div class="pmaf-form-title-wrap">'+ _output +'</div>';
					$('#pmaf-form-preview-frame').contents().find(".pmaf-form-wrap").prepend(_output);
				}
				
			}
		}
		
	}
	
	function pmaf_preview_title_desc_append( _stat ) {
		
		if( _pmaf_ani_settings.hasOwnProperty('title') ) {			
			let _title_output = '';
			let _title_stng = _pmaf_ani_settings['title'];	
			if( _title_stng.hasOwnProperty('description') ) {
				let _title_description = _title_stng['description'];
				_title_output += '<div class="pmaf-form-title-description">'+ _title_description +'</div>';				
			}
			if( _stat == 'on' ) {
				let _output = '<div class="pmaf-form-title-wrap">'+ _title_output +'</div>';
			}
		}
		
	}
	
	function pmaf_arrow_svg() {
		return '<svg height=128 version=1.1 width=128 xmlns=http://www.w3.org/2000/svg><path d="M0 0 C5.01413714 0.42287904 9.08466116 2.22911538 13.5 4.5 C14.19706055 4.85602295 14.89412109 5.2120459 15.61230469 5.57885742 C27.86576389 11.95426834 38.13882928 19.34484259 48 29 C48.86367187 29.81339844 49.72734375 30.62679687 50.6171875 31.46484375 C59.22901073 39.74131561 69.62113027 50.74571037 74 62 C73.64453125 64.78125 73.64453125 64.78125 73 67 C69.36096541 66.39877237 69.05845486 66.07643358 66.625 62.89453125 C65.74162823 61.53927429 64.86716902 60.17818116 64 58.8125 C58.5969685 50.62968797 52.58604178 43.25196098 46 36 C45.38769531 35.31550781 44.77539062 34.63101563 44.14453125 33.92578125 C32.9419772 22.05937215 17.3826507 11.7527164 1.72265625 7.046875 C0 6 0 6 -0.81640625 3.453125 C-0.87699219 2.64359375 -0.93757813 1.8340625 -1 1 C-0.67 0.67 -0.34 0.34 0 0 Z "fill=#cccccc transform=translate(21,15)></path><path d="M0 0 C2.0625 0.4375 2.0625 0.4375 4 1 C4.19205195 6.94450133 4.37080791 12.8891951 4.53710938 18.83447266 C4.59560279 20.85801991 4.65776484 22.88146452 4.72363281 24.90478516 C4.81753791 27.80872917 4.898356 30.71278367 4.9765625 33.6171875 C5.00875885 34.52556366 5.0409552 35.43393982 5.0741272 36.36984253 C5.10438492 37.63092331 5.10438492 37.63092331 5.13525391 38.91748047 C5.15746002 39.65916473 5.17966614 40.400849 5.20254517 41.16500854 C5 43 5 43 3 45 C1.0390625 44.9296875 1.0390625 44.9296875 -1 44 C-2.3515625 41.8828125 -2.3515625 41.8828125 -3.625 39.125 C-9.82560827 27.20313222 -18.57518456 20.39341005 -30 14 C-30.99 13.34 -31.98 12.68 -33 12 C-32.67 10.68 -32.34 9.36 -32 8 C-22.64213963 8.20795245 -14.63040614 14.83218033 -8 21 C-6.92985482 22.20633944 -5.87425493 23.42570729 -4.83203125 24.65625 C-4.29940674 25.28337891 -3.76678223 25.91050781 -3.21801758 26.55664062 C-2.81607178 27.03294922 -2.41412598 27.50925781 -2 28 C-1.97494385 27.18732666 -1.9498877 26.37465332 -1.92407227 25.53735352 C-1.80850882 21.87889248 -1.68560416 18.22071472 -1.5625 14.5625 C-1.52318359 13.28310547 -1.48386719 12.00371094 -1.44335938 10.68554688 C-1.40146484 9.47060547 -1.35957031 8.25566406 -1.31640625 7.00390625 C-1.27974854 5.87799072 -1.24309082 4.7520752 -1.20532227 3.59204102 C-1 1 -1 1 0 0 Z "fill=#cccccc transform=translate(102,68)></path></svg>';
	}
	
	// instant style functions
	function pmaf_instant_style() {

		let _form_id = pmaf_obj.post_id;
		let fa_stngs = _pmaf_ani_settings;
				
		let input_type_classes = 'form#pmaf-form-'+ ( _form_id ) +' .pmaf-field input[type="text"], form#pmaf-form-'+ ( _form_id ) +' .pmaf-field input[type="password"], form#pmaf-form-'+ ( _form_id ) +' .pmaf-field input[type="email"], form#pmaf-form-'+ ( _form_id ) +' .pmaf-field input[type="url"], form#pmaf-form-'+ ( _form_id ) +' .pmaf-field input[type="number"], form#pmaf-form-'+ ( _form_id ) +' .pmaf-field select, form#pmaf-form-'+ ( _form_id ) +' .pmaf-field textarea';
		
		let custom_styles = '';
		
		// box styles
		if( fa_stngs.hasOwnProperty('box') ) {
			let _box_stngs = fa_stngs['box'];
			let an_border = _box_stngs.hasOwnProperty( 'box_border' ) && ( _box_stngs['box_border'] ) ? _box_stngs['box_border'] : '';
			let border_style = '';
			if( an_border && an_border.hasOwnProperty( 'color' ) && ( an_border['color'] ) ) {
				border_style += 'border-color:'+ ( an_border['color'] ) +';';
			}
			let border_width_atts = [ 'left', 'top', 'right', 'bottom' ];
			$.each( border_width_atts, function( index, att ){
				border_style += an_border.hasOwnProperty(att) && an_border[att] != '' ? "border-"+ att +"-width:"+ an_border[att] +"px;": '';
			});
			if( border_style ) custom_styles = input_type_classes +' {border-style: solid;'+ border_style +'}';

			let box_color = _box_stngs.hasOwnProperty('box_color') ? _box_stngs['box_color'] : '';
			let box_bg_color = _box_stngs.hasOwnProperty('box_bg_color') ? _box_stngs['box_bg_color'] : '';
			if( box_color ) {
				custom_styles += input_type_classes +'{color: '+ ( box_color ) +';}';
			}
			if( box_bg_color ) {
				custom_styles += input_type_classes +'{background-color: '+ ( box_bg_color ) +';}';
			}
			
			// padding
			let box_padding = _box_stngs.hasOwnProperty('padding') ? _box_stngs['padding'] : '';			
			if( box_padding ) {
				let box_padding_style = pmaf_style_maker( _form_id, 'padding', _box_stngs, 'padding' );
				if( box_padding_style ) {
					custom_styles += input_type_classes +'{'+ box_padding_style +'}';
				}
			}
		}
		
		// background styles
		if( fa_stngs.hasOwnProperty('outer') ) {
			let _outer_stngs = fa_stngs['outer'];
			let bg = _outer_stngs.hasOwnProperty( 'bg' ) ? _outer_stngs['bg'] : '';
			let pcolor = bg.hasOwnProperty('pcolor') ? bg['pcolor'] : '';
			let scolor = bg.hasOwnProperty('scolor') ? bg['scolor'] : '';
			if( bg && bg.hasOwnProperty('image') && bg['image'] && bg['image'].hasOwnProperty('url') ) {
				let bg_img = bg['image'];
				if( bg_img.hasOwnProperty('url') && ( bg_img['url'] ) ) {
					let img_url = bg_img['url'];
					if( img_url ) custom_styles += '#pmaf-form-wrap-'+ ( _form_id ) +'{background-image:url('+ img_url +');background-repeat:no-repeat;background-size: cover;background-position: center center;}';
				}			
			} 
			
			if( ( pcolor ) && ( scolor ) ) {
				custom_styles += '#pmaf-form-wrap-'+ _form_id +'{background: linear-gradient(180deg, '+ pcolor +' 0%, '+ scolor +' 100%);}';
			} else if( ( pcolor ) ) {
				custom_styles += '#pmaf-form-wrap-'+ _form_id +'{background-color: '+ pcolor +';}';
			}
			
			// gradient filter styles
			let enable_filter = _outer_stngs.hasOwnProperty( 'enable_filter' ) && _outer_stngs['enable_filter'] == 'on' ? true : false;
			if( enable_filter ) {
				let filter_atts = [ 'f_1', 'f_2', 'f_3', 'f_4' ];
				let gradient_atts = _outer_stngs.hasOwnProperty( 'filter' ) ? _outer_stngs['filter'] : {};
				let filter_colors = '';
				$.each( filter_atts, function( index, att ){
					filter_colors += gradient_atts.hasOwnProperty(att) && gradient_atts[att] ? gradient_atts[att] +"," : '';
				});					
				if( filter_colors ) {
					filter_colors = filter_colors.replace(/,(\s+)?$/, '')
					custom_styles += '#pmaf-form-wrap-'+ ( _form_id ) +'.pmaf-bg-gradient-ani{background:linear-gradient(-45deg,'+ ( filter_colors ) +');background-size: 400% 400%;animation:15s infinite pmaf_gradient}@keyframes pmaf_gradient{0%,100%{background-position:0 50%}50%{background-position:100% 50%}}';
				} else {
					custom_styles += '#pmaf-form-wrap-'+ ( _form_id ) +'.pmaf-bg-gradient-ani{background:linear-gradient(-45deg,#ee7752,#e73c7e,#23a6d5,#23d5ab);background-size:400% 400%;animation:15s infinite pmaf_gradient}@keyframes pmaf_gradient{0%,100%{background-position:0 50%}50%{background-position:100% 50%}}';
				}
			}
		}

		// label styles
		if( fa_stngs.hasOwnProperty('labels') ) {
			
			let f_labels = fa_stngs['labels'];
			
			// label align
			let label_align = f_labels.hasOwnProperty('align') && f_labels['align'] ? f_labels['align'] : 'start';
			if( label_align ) {
				custom_styles += '#pmaf-form-wrap-'+ _form_id +' .pmaf-field label{ display: flex; justify-content: '+ label_align +';}';
			}
			
			// label color
			let label_color = f_labels.hasOwnProperty('color') ? f_labels['color'] : '';
			if( label_color ) {
				custom_styles += '#pmaf-form-wrap-'+ _form_id +' .pmaf-field label{color: '+ label_color +';}';
			}
			
			// label font size
			let label_fsize = f_labels.hasOwnProperty('fsize') && f_labels['fsize'] ? f_labels['fsize'] : '';
			if( label_fsize ) {
				custom_styles += '#pmaf-form-wrap-'+ _form_id +' .pmaf-field label{ font-size: '+ label_fsize +'px;}';
			}
			
			// label font weight
			let label_fweight = f_labels.hasOwnProperty('fweight') && f_labels['fweight'] ? f_labels['fweight'] : '';
			if( label_fweight ) {
				custom_styles += '#pmaf-form-wrap-'+ _form_id +' .pmaf-field label{ font-weight: '+ label_fweight +';}';
			}
			
			// label padding
			let label_padding = pmaf_style_maker( _form_id, 'padding', f_labels, 'padding' );
			if( label_padding ) {
				custom_styles += '#pmaf-form-wrap-'+ _form_id +' .pmaf-field label{ '+ label_padding +'}';
			}
			
			// label margin
			let label_margin = pmaf_style_maker( _form_id, 'margin', f_labels, 'margin' );
			if( label_margin ) {
				custom_styles += '#pmaf-form-wrap-'+ _form_id +' .pmaf-field label{ '+ label_margin +'}';
			}
			
		}
			
		// overlay styles
		if( fa_stngs.hasOwnProperty('overlay') ) {
			let overlay_styles = fa_stngs['overlay'];
			if( overlay_styles.hasOwnProperty('style') ) {
				custom_styles += overlay_styles['style'];
			}
		}

		// inner/outer padding styles	
		if( fa_stngs.hasOwnProperty('form') ) {

			let form_stng = fa_stngs['form'];
			let padding_d_atts = [ 'left', 'top', 'right', 'bottom' ];
						
			// inner padding styles
			let fi_padding = form_stng.hasOwnProperty('inner_padding') && form_stng['inner_padding'] ? form_stng['inner_padding'] : '';
			if( fi_padding ) {
				let i_padding_style = '';
				$.each( padding_d_atts, function( index, att ){
					i_padding_style += fi_padding.hasOwnProperty(att) && fi_padding[att] != '' ? "padding-"+ att +":"+ fi_padding[att] +"px;": '';
				});
				if( i_padding_style ) custom_styles += '#pmaf-form-wrap-'+ _form_id +' .pmaf-inner form{'+ i_padding_style +'}';
			}
			
			// outer padding styles
			let fo_padding = form_stng.hasOwnProperty('outer_padding') && form_stng['outer_padding'] ? form_stng['outer_padding'] : '';
			let o_padding_style = '';
			if( fo_padding ) {
				$.each( padding_d_atts, function( index, att ){
					o_padding_style += fo_padding.hasOwnProperty(att) && fo_padding[att] != '' ? "padding-"+ att +":"+ fo_padding[att] +"px;": '';
				});
				if( o_padding_style ) custom_styles += '#pmaf-form-wrap-'+ _form_id +' .pmaf-inner {'+ o_padding_style +'}';
			}
			
			// border styles
			let border = form_stng.hasOwnProperty('border') && form_stng['border'] ? form_stng['border'] : '';			
			if( border ) {
				let border_style = '';
				if( border.hasOwnProperty('color') && border['color'] ) {
					border_style += 'border-color:'+ border['color'] +';';
				}		
				let border_width_atts = [ 'left', 'top', 'right', 'bottom' ];
				$.each( border_width_atts, function( index, att ){
					border_style += border.hasOwnProperty(att) && border[att] != '' ? "border-"+ att +"-width:"+ border[att] +"px;": '';
				});
				if( border_style ) custom_styles += '#pmaf-form-wrap-'+ _form_id +' .pmaf-inner form {border-style: solid;'+ border_style +'}';
			}
			
			// form align
			let form_align = form_stng.hasOwnProperty('align') && form_stng['align'] ? form_stng['align'] : 'center';
			if( form_align ) {
				custom_styles += '#pmaf-form-wrap-'+ _form_id +' .pmaf-inner{ justify-content: '+ form_align +';}';
			}
			
			// form width
			let form_wdth = form_stng.hasOwnProperty('fwidth') && form_stng['fwidth'] ? form_stng['fwidth'] : '';
			if( form_wdth ) {
				custom_styles += '#pmaf-form-wrap-'+ _form_id +' .pmaf-inner form { width: '+ form_wdth +'px;}';
			}
		}		

		// button styles
		if( fa_stngs.hasOwnProperty('btn') ) {
		
			let btn_stng = fa_stngs['btn'];
			if( btn_stng.hasOwnProperty('color') && btn_stng['color'] ) {
				custom_styles += '#pmaf-form-wrap-'+ _form_id +' .pmaf-submit{color: '+ btn_stng['color'] +';}';
			}
			if( btn_stng.hasOwnProperty('bg') && btn_stng['bg'] ) {
				custom_styles += '#pmaf-form-wrap-'+ _form_id +' .pmaf-submit{background-color: '+ btn_stng['bg'] +';}';
			}
			// border styles
			let border = btn_stng.hasOwnProperty('border') && btn_stng['border'] ? btn_stng['border'] : '';
			if( border ) {
				let border_style = '';		
				if( border.hasOwnProperty('color') && border['color'] ) {
					border_style += 'border-color:'+ border['color'] +';';
				}		
				let border_width_atts = [ 'left', 'top', 'right', 'bottom' ];
				$.each( border_width_atts, function( index, att ){
					border_style += border.hasOwnProperty(att) && border[att] != '' ? "border-"+ att +"-width:"+ border[att] +"px;": '';
				});
				if( border_style ) custom_styles += 'form#pmaf-form-'+ _form_id +' .pmaf-submit{border-style: solid;'+ border_style +'}';
			}

			// btn model
			let btn_model = btn_stng.hasOwnProperty('model') && btn_stng['model'] ? btn_stng['model'] : 'default';
			if( btn_model ) {
				let btn_model_val = { 'default': '0', 'round': '4px', 'circle': '100px' };
				custom_styles += 'form#pmaf-form-'+ _form_id +' .pmaf-submit{border-radius: '+ btn_model_val[btn_model] +';}';
			}
				
			// btn align
			let btn_align = btn_stng.hasOwnProperty('align') && btn_stng['align'] ? btn_stng['align'] : 'start';
			if( btn_align ) {
				custom_styles += 'form#pmaf-form-'+ _form_id +' .submit-wrap{ display: flex; justify-content: '+ btn_align +';}';
			}
			
			// btn width
			let btn_width = btn_stng.hasOwnProperty('width') && btn_stng['width'] ? btn_stng['width'] : '';
			if( btn_width ) {
				custom_styles += 'form#pmaf-form-'+ _form_id +' .pmaf-submit{ width: '+ btn_width +'px;}';
			}
			
		}
		
		// title and description styles
		if( fa_stngs.hasOwnProperty('title') ) {
			
			let f_title = fa_stngs['title'];
			
			// title align
			let title_align = f_title.hasOwnProperty('align') && f_title['align'] ? f_title['align'] : 'start';
			if( title_align ) {
				custom_styles += '#pmaf-form-wrap-'+ _form_id +' .pmaf-form-title{ display: flex; justify-content: '+ title_align +';}';
			}
			
			// title color
			let title_color = f_title.hasOwnProperty('color') && f_title['color'] ? f_title['color'] : '';
			if( title_color ) {
				custom_styles += '#pmaf-form-wrap-'+ _form_id +' .pmaf-form-title{ color: '+ title_color +';}';
			}
			
			// title font size
			let title_fsize = f_title.hasOwnProperty('fsize') && f_title['fsize'] ? f_title['fsize'] : '';
			if( title_fsize ) {
				custom_styles += '#pmaf-form-wrap-'+ _form_id +' .pmaf-form-title{ font-size: '+ title_fsize +'px;}';
			}
			
			// title font weight
			let title_fweight = f_title.hasOwnProperty('fweight') && f_title['fweight'] ? f_title['fweight'] : '';
			if( title_fweight ) {
				custom_styles += '#pmaf-form-wrap-'+ _form_id +' .pmaf-form-title{ font-weight: '+ title_fweight +';}';
			}
			
			// title padding
			let title_padding = pmaf_style_maker( _form_id, 'padding', f_title, 'padding' );
			if( title_padding ) {
				custom_styles += '#pmaf-form-wrap-'+ _form_id +' .pmaf-form-title{ '+ title_padding +'}';
			}
			
			// title margin
			let title_margin = pmaf_style_maker( _form_id, 'margin', f_title, 'margin' );
			if( title_margin ) {
				custom_styles += '#pmaf-form-wrap-'+ _form_id +' .pmaf-form-title{ '+ title_margin +'}';
			}
			
			// desc align
			let desc_align = f_title.hasOwnProperty('desc_align') && f_title['desc_align'] ? f_title['desc_align'] : 'start';
			if( desc_align ) {
				custom_styles += '#pmaf-form-wrap-'+ _form_id +' .pmaf-form-title-description{ display: flex; justify-content: '+ desc_align +';}';
			}
			
			// desc color
			let title_desc_color = f_title.hasOwnProperty('desc_color') && f_title['desc_color'] ? f_title['desc_color'] : '';
			if( title_desc_color ) {
				custom_styles += '#pmaf-form-wrap-'+ _form_id +' .pmaf-form-title-description{ color: '+ title_desc_color +';}';
			}
			
			// desc font size
			let title_desc_fsize = f_title.hasOwnProperty('desc_fsize') && f_title['desc_fsize'] ? f_title['desc_fsize'] : '';
			if( title_desc_fsize ) {
				custom_styles += '#pmaf-form-wrap-'+ _form_id +' .pmaf-form-title-description{ font-size: '+ title_desc_fsize +'px;}';
			}
			
			// desc font weight
			let title_desc_fweight = f_title.hasOwnProperty('desc_fweight') && f_title['desc_fweight'] ? f_title['desc_fweight'] : '';
			if( title_desc_fweight ) {
				custom_styles += '#pmaf-form-wrap-'+ _form_id +' .pmaf-form-title-description{ font-weight: '+ title_desc_fweight +';}';
			}
			
			// desc padding
			let desc_padding = pmaf_style_maker( _form_id, 'padding', f_title, 'desc_padding' );
			if( desc_padding ) {
				custom_styles += '#pmaf-form-wrap-'+ _form_id +' .pmaf-form-title-description{ '+ desc_padding +'}';
			}
			
			// desc margin
			let desc_margin = pmaf_style_maker( _form_id, 'margin', f_title, 'desc_margin' );
			if( desc_margin ) {
				custom_styles += '#pmaf-form-wrap-'+ _form_id +' .pmaf-form-title-description{ '+ desc_margin +'}';
			}
			
		}
		
		return custom_styles;
		
	}
	
	function pmaf_style_maker( form_id, key, settings, field ) {
		
		let custom_styles = '';
		let dimension_atts = [ 'left', 'top', 'right', 'bottom' ];
		
		let f_dim = settings.hasOwnProperty(field) && settings[field] ? settings[field] : '';
		let dim_style = '';
		if( f_dim ) {
			$.each( dimension_atts, function( index, att ){
				dim_style += f_dim.hasOwnProperty(att) && f_dim[att] != '' ? key +"-"+ att +":"+ f_dim[att] +"px;": '';
			});
		}
		
		return dim_style;
		
	}
	
})( jQuery );