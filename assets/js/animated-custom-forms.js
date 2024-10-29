( function( $ ) {
	
	"use strict";

	$(document).ready(function(){
		
		if( $(".pmaf-form").length ) {
			
			// label animation
			if( $(".pmaf-field.pmaf-label-animate").length ) { 
				$('.pmaf-field.pmaf-label-animate input, .pmaf-field.pmaf-label-animate textarea').on( "focus", function(e) {
					$(this).parent(".pmaf-label-animate").addClass("pmaf-label-back");
				}).on( "focusout", function(e) {
					if( !$(this).val() ) $(this).parent(".pmaf-label-animate").removeClass("pmaf-label-back");
				});
				
				$('.pmaf-field.pmaf-label-animate > label, .pmaf-field.pmaf-label-animate > label').on( "click", function(e) {
					if( $(this).parent(".pmaf-label-animate").hasClass("pmaf-label-back") ) {
						$(this).parent(".pmaf-label-animate").removeClass("pmaf-label-back");
					} else {
						$(this).parent(".pmaf-label-animate").addClass("pmaf-label-back");
						$(this).next("input, textarea").focus();
					}
				});
			}
			
			// radio group value change
			if( $(".pmaf-img-radio-group").length ) {
				
				$('.pmaf-img-radio-group .pmaf-img-radio-single input[type="radio"]').on( "change", function(e) {
					let _cur_ele = $(this);
					_cur_ele.parents(".pmaf-img-radio-group").find(".pmaf-img-radio-single").removeClass("selected");
					_cur_ele.parent(".pmaf-img-radio-single").addClass("selected");
				});
				
			}
			
			if( $(".pmaf-field-slider").length ) {
				$('.pmaf-field-slider input[type=range]').on('change', function () {
					let _cur_ele = $(this);
					$(_cur_ele).parent(".pmaf-field-slider").find(".pmaf-range-val").html(_cur_ele.val());
				});
			}
			
			if( $(".pmaf-field-file").length ) {
				pmaf_file_preview();
			}
			
			// submission			
			$(".pmaf-form").on( "submit", function(e) {
			
				let _cur_form = $(this);
				let _ajax_opt = _cur_form.data("ajax");
				if( _ajax_opt == 'on' ) {
			
					e.preventDefault();
					
					
					let _form_id = _cur_form.find('input[name="form_id"]').val();
					$(".pmaf-form-msg").html(_cur_form.data("ajax-msg"));
					_cur_form.find(".pmaf-alert-warning").remove(); _cur_form.find(".pmaf-alert-success").remove();
					let _data = new FormData(this);
					
					/*if( _cur_form.find('input[name="pmaf_login"]').length ) {
						console.log("login form");
						return false;
					}*/
					
					$.ajax({
						type: "POST",
						url: pmaf_fe_ajax_var.ajaxurl,
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
								
								if( data.register && data.register == 'success' ){
									$(_cur_form).append('<div id="pmaf-alert-'+ _form_id +'" class="pmaf-alert-success">'+ data.msg +'</div>');
									setTimeout(function() {										
										$(".pm-reg-to-login-form").trigger("click");
										$(".pmaf-regiser-form-wrap").find("form")[0].reset();
										$(".pmaf-regiser-form-wrap").find(".pmaf-alert-success").remove();
									}, 1000 );
								} else if( data.loggedin && data.loggedin == true ){
									if( data.redirect_url ){
										window.location.href = data.redirect_url;
									}else{
										window.location.reload();
									}
								} else if( data.forget && data.forget == 'success' ){
									$(".pmaf-forget-form-wrap").find("form")[0].reset();
									$(_cur_form).append('<div id="pmaf-alert-'+ _form_id +'" class="pmaf-alert-success">'+ data.msg +'</div>');
								} else {								
									$(_cur_form).replaceWith('<div id="pmaf-alert-'+ _form_id +'" class="pmaf-alert-sccess">'+ data.msg +'</div>');
									$('html, body').animate({
										scrollTop: $('#pmaf-alert-'+ _form_id +'').position().top 
									},300);
								}
								
							} else if( data.status == 'failed' ) {
								$(_cur_form).append('<div id="pmaf-alert-'+ _form_id +'" class="pmaf-alert-warning">'+ data.msg +'</div>');
							}
						},error: function(xhr, status, error) {
							console.log("failed");						
						},complete: function(){
							$(".pmaf-form-msg").html("");
						}
					});
					
					return false;
					
				}
				
			});
			
			$(".pmaf-form .pmaf-submit").on( "click", function(e) {
								
				e.preventDefault();				
				let _cur_form = $(this).parents(".pmaf-form");
				_cur_form = $(_cur_form);
				let _form_id = _cur_form.find('input[name="form_id"]').val();
				let _validate = pmaf_validation( _cur_form );

				if( _validate ) {					
					_cur_form.trigger("submit");					
				}

				return false;
				
			});
			
			$(".pm-af-register-trigger").on("click", function(e) {
				e.preventDefault();	
				let _cur = $(this);
				_cur.parents(".pmaf-inner").find(".pmaf-login-form-wrap").addClass("deactive");
				_cur.parents(".pmaf-inner").find(".pmaf-regiser-form-wrap").addClass("active");
				return false;
			});
			
			$(".pm-reg-to-login-form").on("click", function(e) {
				e.preventDefault();	
				let _cur = $(this);
				_cur.parents(".pmaf-inner").find(".pmaf-login-form-wrap").removeClass("deactive");
				_cur.parents(".pmaf-inner").find(".pmaf-regiser-form-wrap").removeClass("active");
				return false;
			});
			
			$(".pm-af-lost-password-trigger").on("click", function(e) {
				e.preventDefault();	
				let _cur = $(this);
				_cur.parents(".pmaf-inner").find(".pmaf-login-form-wrap").addClass("deactive");
				_cur.parents(".pmaf-inner").find(".pmaf-forget-form-wrap").addClass("active");
				return false;
			});
			
			$(".pm-reg-to-login-form").on("click", function(e) {
				e.preventDefault();	
				let _cur = $(this);
				_cur.parents(".pmaf-inner").find(".pmaf-login-form-wrap").removeClass("deactive");
				_cur.parents(".pmaf-inner").find(".pmaf-forget-form-wrap").removeClass("active");
				return false;
			});
			
		}
		
		if( $(document).find(".pmaf-alert-warning, .pmaf-alert-sccess").length ) {
			$('html, body').animate({
				scrollTop: $(document).find(".pmaf-alert-warning, .pmaf-alert-sccess").position().top 
			},300);
		}
		
		/*if( $('.pmaf-alert-sccess').length ) {
			pmaf_remove_file_storage();
		}*/
		
	});
	
	$(window).load(function(){
		
		/*if( $(".pmaf-form").length ) {
			pmaf_set_cookie( 'pmaf_files', '', 30 );
		}*/
		
	});
		
	function pmaf_is_email(email) {
		const regex =
/^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		if (!regex.test(email)) {
			return false;
		}
		else {
			return true;
		}
		
	}
	
	function pmaf_validation( _cur_form ) {
		
		let _stat = true; 
		
		if( $(_cur_form).find(".pmaf-field.pmaf-required").length ) {
			$(_cur_form).find(".pmaf-field.pmaf-required").each(function(e){
				
				let _cur_stat = true;
				let _req_ele = $(this).find("input, select, textarea");
				let _err_msg = $(this).data("req-msg") ? $(this).data("req-msg") : pmaf_fe_ajax_var.strings.required.default;
				let _val = $(_req_ele).val();
				
				if( _val == '' ) {
					_stat = false; _cur_stat = false;
				}

				if( _cur_stat && $(_req_ele).attr("type") == 'email' ) { 						
					if( !pmaf_is_email( _val ) ) {
						_stat = false; _cur_stat = false;
					}
				} else if( _cur_stat && $(_req_ele).attr("type") == 'number' ) {									
					if( !$.isNumeric( _val ) ) {
						_stat = false; _cur_stat = false;
					}
				} else if( _cur_stat && $(_req_ele).attr("type") == 'url' ) {									
					if( !pmaf_validate_url( _val ) ) {
						_stat = false; _cur_stat = false;
					}
				} else if( _cur_stat && $(_req_ele).attr("type") == 'radio' ) {
					if( $(_req_ele).parents(".pmaf-img-radio-group").length ) {
						if( !$(_req_ele).parents(".pmaf-img-radio-group").find(".pmaf-img-radio-single.selected").length ) {
							_stat = false; _cur_stat = false;
						}
					} else if( $(_req_ele).parents(".pmaf-radio-group").length ) {
						if( !$(_req_ele).parents(".pmaf-radio-group").find('input:checked').val() ) {
							_stat = false; _cur_stat = false;
						}
					}
				} else if( _cur_stat && $(_req_ele).attr("type") == 'checkbox' && $(_req_ele).data("type") == 'consent' ) {
					let _chk_stat = false; 
					if( $(_req_ele).is(':checked') ) _chk_stat = true;
					if( !_chk_stat ) {
						_stat = false; _cur_stat = false;
					}
				} else if( _cur_stat && $(_req_ele).attr("type") == 'checkbox' ) {
					let _chk_stat = false; 
					$(_req_ele).parents(".pmaf-checkbox-group").find('input[type="checkbox"]').each(function(e) {
						if( $(this).is(':checked') ) _chk_stat = true;
					});
					if( !_chk_stat ) {
						_stat = false; _cur_stat = false;
					}
				}
				
				if( _err_msg && !_cur_stat ) {
					$(_req_ele).addClass("pmaf-err");
					$(this).closest(".pmaf-field.pmaf-required").find("span.pmaf-err-ele").remove();
					$(this).closest(".pmaf-field.pmaf-required").append('<span class="pmaf-err-ele">'+ _err_msg +'</span>');
				}
				
			});
			
			$(_cur_form).find(".pmaf-field.pmaf-required input, .pmaf-field.pmaf-required textarea").off("input");
			$(_cur_form).find(".pmaf-field.pmaf-required input, .pmaf-field.pmaf-required textarea").on("input", function(e){
				$(this).removeClass("pmaf-err");
				$(this).closest(".pmaf-field.pmaf-required").find("span.pmaf-err-ele").remove();
			});
			
		}
		
		return _stat;
		
	}
	
	function pmaf_file_preview() {
		//var imgWrap = "";
		var g_imgArray = [];
		
		$('.pmaf-field-file input[type=file]').each(function () {
			
			var imgWrap = "";
			let imgArray = [];
			
			$(this).on('change', function (e) {
				
				
				imgWrap = $(this).parents(".pmaf-field-file").find(".pmaf-img-preview-wrap");
				var maxLength = $(this).attr('data-max_length');
				var _accept = $(this).attr('accept');
				var files = e.target.files; //console.log(files);
				var filesArr = Array.prototype.slice.call(files);
				var iterator = 0;
				filesArr.forEach(function (f, index) {
					
					let _valid_stat = false;
					var accept_array = _accept.split(",");
					$.each(accept_array,function(i){
						if( f.type.match(accept_array[i]) ) { //if (!f.type.match('image.*')) {
							_valid_stat = true;
						} 
					});
					
					if( !_valid_stat ) {
						return;
					}
					

					if (imgArray.length >= maxLength) {
						return false
					} else {
						var len = 0;
						for (var i = 0; i < imgArray.length; i++) {
							if (imgArray[i] !== undefined) {
								len++;
							}
						}
						if (len >= maxLength) {
							return false;
						} else {
							imgArray.push(f); //console.log( e.target.result );

							var reader = new FileReader();
							reader.onload = function (e) { 						
								
								//localStorage.setItem( 'pmaf_files_'+ f.name, e.target.result );
								
								var html = "<div class='pmaf-img-preview-item'>";
								if( f.type.match('image.*') ) {
									html += "<div style='background-image: url(" + e.target.result + ")' data-number='" + $(".pmaf-img-preview-remove").length + "' data-file='" + f.name + "' class='img-bg'>";
								} else {
									html += "<div data-number='" + $(".pmaf-img-preview-remove").length + "' data-file='" + f.name + "' class='file-bg'>";
								}
								html += "<div id='pmaf-img-"+ $(".pmaf-img-preview-remove").length +"-preview-remove' class='pmaf-img-preview-remove'></div></div>";
								
								html += "<span class='pmaf-file-name' title='"+ f.name +"'>"+ f.name +"</span></div>";
								imgWrap.append(html);
								iterator++;		
								
								
							}
							reader.readAsDataURL(f);
							
						}
					}
				});
				g_imgArray[$(this).attr("name")] = imgArray;
				//pmaf_set_cookie( 'pmaf_files', g_imgArray, 30 );
			});
			
		});

		$('body').on('click', ".pmaf-img-preview-remove", function (e) {
			
			let _cur_file_ele = $(this).parents(".pmaf-field.pmaf-field-file").find('input[type="file"]');
			let _cur_name = $(this).parents(".pmaf-field.pmaf-field-file").find('input[type="file"]').attr("name");
			let _img_arr = g_imgArray[_cur_name];
			var file = $(this).parent().data("file");

			//localStorage.removeItem( 'pmaf_files_'+ file );
			
			for (var i = 0; i < _img_arr.length; i++) {
				if (_img_arr[i].name === file) {					
					_img_arr.splice(i, 1);
					break;
				}
			}
			$(this).parent().parent().remove();
			g_imgArray[_cur_name] = _img_arr;
			
			if( _img_arr.length ) { //console.log( _img_arr );
				let _i = 0;
				let container = new DataTransfer(); 
				$(_img_arr).each(function(){				
					container.items.add(_img_arr[_i]);
					_i++;
					_cur_file_ele[0].files = container.files;
				});
			} else {
				_cur_file_ele.val(null); 
			}
			
		});
		
		// develop mode after validation file assign
		/*$('.pmaf-field-file input[type=file]').each(function () {
			let _cur_file_ele = $(this);
			let _loaded_files = $(_cur_file_ele).data("files") ? $(_cur_file_ele).data("files") : ''; 
			if( _loaded_files ) { 
				let container = new DataTransfer();
				for( let _i = 0; _i < _loaded_files.name.length; _i++ ) {
						
						let data = localStorage.getItem( 'pmaf_files_'+ _loaded_files.name[_i] );						
						let myFile = new File([data], _loaded_files.name[_i], {
							type: _loaded_files.type[_i],
							lastModified: new Date(),
						});
						container.items.add(myFile);
						_cur_file_ele[0].files = container.files;
					
				};
				_cur_file_ele.trigger("change");
			} else {
				//pmaf_remove_file_storage();
			}
			
		});*/
		
		
	}
	
	function pmaf_remove_file_storage() {
		
		var arr = []; // Array to hold the keys
		// Iterate over localStorage and insert the keys that meet the condition into arr
		for (var i = 0; i < localStorage.length; i++){
			if (localStorage.key(i).substring(0,4) == 'pmaf') {
				arr.push(localStorage.key(i));
			}
		}

		// Iterate over arr and remove the items by key
		for (var i = 0; i < arr.length; i++) {
			localStorage.removeItem(arr[i]);
		}
		
	}
	
	function pmaf_set_cookie(cname, cvalue, minutes) {
		const d = new Date();
		d.setTime(d.getTime() + ( minutes * 1000 ) );
		let expires = "expires="+d.toUTCString();
		document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
	}

	function pmaf_get_cookie(cname) {
		let name = cname + "=";
		let ca = document.cookie.split(';');
		for(let i = 0; i < ca.length; i++) {
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
	
	function pmaf_validate_url(_link) {
		var urlregex = new RegExp("^(http:\/\/|https:\/\/+\.)");
		return urlregex.test(_link);
	}
	
	function pmaf_validate_phone(phone) {
		var phoneregex = /^[0-9\s]*$/;
		return phoneregex.test(_link);
	}
		
} )( jQuery );