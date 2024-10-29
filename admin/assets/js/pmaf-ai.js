/*
 * Auto Rank Options Script
 */ 

(function( $ ) {

	"use strict";
			  
	$( document ).ready(function() {
		
		if( $('#pmaf_generate_keywords').length ){
			$('#pmaf_generate_keywords').on( "click", function ( e ) {
				e.preventDefault();
				let _cur_ele = $(this);
				let _ex_label = _cur_ele.attr("value");
				let _new_label = animated_login_forms_obj.strings.generating;
				_cur_ele.attr("value",_new_label);
				_cur_ele.attr("disabled","disabled");
				$.ajax({
					type: "POST",
					url: ajaxurl,
					data: {
						action: 'pmaf_keywords_generate',
						title: $("#titlewrap input").val(),
						nonce: animated_login_forms_obj.ai_generate_nonce
					},
					success: function (data) {
						if( data.status == 'success' ){
							_cur_ele.prev("textarea").val(data.keywords);
						} else if( data.status == 'failed' ){
							_cur_ele.after('<p class="pmaf-err">'+ data.error +'</p>');
						}
					},error: function(xhr, status, error) {
						console.log("failed");						
					}, complete: function () {
						_cur_ele.removeAttr("disabled");
						_cur_ele.attr("value",_ex_label);
					}
				});
			});
		}
				
	});
	
})( jQuery );