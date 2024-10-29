( function( $ ) {
	
	"use strict";

	$(document).ready(function(){
		
		// input filled condition models
		if( $(document).find(".pm-filled-model-form").length ) {
			
			$(document).find(".pm-filled-model-form input").each(function() {
				$(this).on( "input", function(e) {
					if( $(this).val() != '' ) {
						$(this).parent("p").addClass("af-input-filled");
					} else {
						$(this).parent("p").removeClass("af-input-filled");
					}
				});
			});
			
			$(document).find(".pm-filled-model-form form p label").on( "click", function() {
				if( $(this).next('input[type="text"]') ) {
					$(this).next('input').focus();
				}
			});
			
		}
		
		// model 7
		if( $(document).find(".pmaf-7-container").length ) { 
			
			$(document).find(".pmaf-7-container .pmaf-7-register").on( "click", function() {
				$(document).find(".pmaf-7-container").addClass("right-panel-active");
				return false;
			});
			
			$(document).find(".pmaf-7-container .pmaf-7-signin").on( "click", function() {
				$(document).find(".pmaf-7-container").removeClass("right-panel-active lost-password-panel-active");
				return false;
			});
			
			$(document).find(".pmaf-7-container .pmaf-7-lost-password").on( "click", function() {
				$(document).find(".pmaf-7-container").addClass("lost-password-panel-active");
				return false;
			});
			
		}
		
		// dog model
		if( $(document).find(".pm-af-login-form.pm-af-style-5").length ) {
			$(document).find(".pm-af-login-form.pm-af-style-5 input#user_login").focusin( function(e) {
				$(document).find(".dog-face").addClass("dog-face-rotate");
				$(document).find(".dog-hand-left, .dog-hand-right").addClass("dog-hand-up");
			});
			$(document).find(".pm-af-login-form.pm-af-style-5 input#user_login").focusout( function(e) {
				$(document).find(".dog-face").removeClass("dog-face-rotate");
				$(document).find(".dog-hand-left, .dog-hand-right").removeClass("dog-hand-up");
			});
			$(document).find(".pm-af-login-form.pm-af-style-5 input#user_pass").focusin( function(e) {
				$(document).find(".dog-hand-left, .dog-hand-right").addClass("dog-hand-hide");
			});
			$(document).find(".pm-af-login-form.pm-af-style-5 input#user_pass").focusout( function(e) {
				$(document).find(".dog-hand-left, .dog-hand-right").removeClass("dog-hand-hide");
			});
		}

		// Sticky cart close click
		$(document).find(".pm-af-register-trigger").on( "click", function(e) {
			e.preventDefault();			
			$('.pm-af-login-parent .pm-af-lost-password-form, .pm-af-login-parent .pm-af-login-form').removeClass('form-state-show').addClass('form-state-hide');
			$('.pm-af-login-parent .pm-af-registration-form').removeClass('form-state-hide').addClass('form-state-show');	
			return false;
		});
		
		$(document).find(".pm-reg-to-login-form, .pm-forget-to-login-form").on( "click", function(e) {
			e.preventDefault();
			$('.pm-af-login-parent .pm-af-lost-password-form, .pm-af-login-parent .pm-af-registration-form').removeClass('form-state-show').addClass('form-state-hide');
			$('.pm-af-login-parent .pm-af-login-form').removeClass('form-state-hide').addClass('form-state-show');				
			return false;
		});
		
		$(document).find(".pm-af-lost-password-trigger").on( "click", function(e) {
			e.preventDefault();
			$('.pm-af-login-parent .pm-af-registration-form, .pm-af-login-parent .pm-af-login-form').removeClass('form-state-show').addClass('form-state-hide');
			$('.pm-af-login-parent .pm-af-lost-password-form').removeClass('form-state-hide').addClass('form-state-show');	
			return false;
		});
		
		// login ajax
		$( document ).on( 'submit', 'form#pm-af-login-form', function(e) {
			
			e.preventDefault();
			
			if( $('form#pm-af-login-form #user_login').val() != '' && $('form#pm-af-login-form #user_pass').val() != '' ){
				$('.pm-af-login-form p.status').show().text(pmaf_ajax_var.loadingmessage);
				
				let _remember_me = $('form#pm-af-login-form #rememberme').is(':checked') ? true : false;
				
				($).ajax({
					type: 'post',
					dataType: 'json',
					url: pmaf_ajax_var.ajaxurl,
					data: { 
						'action': 'pmaf_ajax_login', //calls wp_ajax_nopriv_ajaxlogin
						'username': $('form#pm-af-login-form #user_login').val(),
						'password': $('form#pm-af-login-form #user_pass').val(),
						'security': $('.pm-af-login-form #pmaf_security').val(),
						'rememberme': _remember_me,
					},
					success: function(data){
						$('.pm-af-login-form p.status').text(data.message);
						if( data.loggedin == true ){
							if( data.redirect_url ){
								window.location.href = data.redirect_url;
							}else{
								window.location.reload();
							}
						}
					}
				});				
			}else{
				$('.pm-af-login-form p.status').text(pmaf_ajax_var.valid_login);
				return false;
			}
		});
		
		// register ajax
		$( document ).on( 'submit', 'form#pm-registration-form', function(e) {
			
			let _validation = true;
			$('form#pm-registration-form .af-req').each(function() {
				if( $(this).val() == '' ) _validation = false;
			});
			
			if( _validation ){
				$('form#pm-registration-form p.status').show().text(pmaf_ajax_var.loadingmessage);
	
				($).ajax({
					type: 'post',
					dataType: 'json',
					url: pmaf_ajax_var.ajaxurl,
					data: { 
						'action': 'pmaf_ajax_register', //calls pmaf_ajax_register
						'name': $('form#pm-registration-form #pm-name').val(),
						'email': $('form#pm-registration-form #pm-email').val(),
						'nick_name': $('form#pm-registration-form #pm-nick-name').val(),
						'username': $('form#pm-registration-form #pm-new-username').val(),
						'password': $('form#pm-registration-form #pm-new-password').val(), 
						'security': $('form#pm-registration-form #security').val() },
					success: function(data){
						$('form#pm-registration-form p.status').text(data.message);
						if (data.register == true){
							
							$('form#pm-registration-form p.status').text(data.message);
							setTimeout(function() {
								$('.pm-af-login-parent .pm-af-lost-password-form, .pm-af-login-parent .pm-af-registration-form').removeClass('form-state-show').addClass('form-state-hide');
								$('.pm-af-login-parent .pm-af-login-form').removeClass('form-state-hide').addClass('form-state-show');	
							}, 1000);
							
						}else{
							$('form#pm-registration-form p.status').text(data.message);	
						}
					}
				});
				e.preventDefault();
			}else{
				$('form#pm-registration-form p.status').text(pmaf_ajax_var.req_reg);
				return false;
			}
		});
		
		// forget password ajax
		$( document ).on( 'submit', 'form#pm-forgot-password-form', function(e) {
			if( $('#user_fp_login').val() != '' ){
				
				$('p.status', this).show().text(pmaf_ajax_var.loadingmessage);

				($).ajax({
					type: 'post',
					dataType: 'json',
					url: pmaf_ajax_var.ajaxurl,
					data: { 
						'action': 'pmaf_lost_pass', 
						'user_login': $('form#pm-forgot-password-form #user_fp_login').val(), 
						'security': $('#forgotsecurity').val(), 
					},
					success: function(data){					
						$('form#pm-forgot-password-form p.status').text(data.message);
					}
				});
				e.preventDefault();
				return false;
			}else{
				$('form#pm-forgot-password-form p.status').text(pmaf_ajax_var.valid_email);	
				return false;
			}
		});
	
	});		
		
} )( jQuery );