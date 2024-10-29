/*
 * Animated Forms Entries JS
 */ 

(function( $ ) {

	"use strict";
	
	var pmaf_form_filter = '';
	var pmaf_read_filter = '';
	var pmaf_fav_filter = '';
	var pmaf_entry_pagi = 1;
	var pmaf_entry_limit = 50;
	
				  
	$( document ).ready(function() {
		
		if( $(".pmaf-single-entry").length ) {
			document.title = animated_entry_obj.page_title;
		}
		
		if( $(".pmaf-entries-select2").length ){
			
			$(".pmaf-entries-select2").each(function(){
				let cur_s = $(this);
				$(cur_s).select2();
			});
			
			$(".pmaf-entries-select2").on("select2:select", function (e) { 
				var select_val = $(e.currentTarget).val();
				select_val = select_val ? select_val : 'all';
				pmaf_form_filter = select_val;
				
				// get filtered entries
				pmaf_get_entries();
				
			});		
			
		}
		
		if( $(".pmaf-entries-filter").length ) {
			$(".pmaf-entries-filter").on("change", function(){
				let _read_stat = this.value;
				pmaf_read_filter = _read_stat;
				
				// get filtered entries
				pmaf_get_entries();
				
			});
		}
		
		if( $(".pmaf-entries-fav").length ) {
			$(".pmaf-entries-fav").on("change", function(){
				let _fav_stat = this.value;
				pmaf_fav_filter = _fav_stat;
				
				// get filtered entries
				pmaf_get_entries();
				
			});
		}
				
		if( $(".pmaf-entries-filter-reset").length ) {
			$(".pmaf-entries-filter-reset").on("click", function(e){
				e.preventDefault();
				
				pmaf_form_filter = '';
				pmaf_read_filter = '';
				pmaf_fav_filter = '';
				
				$("select.pmaf-entries-filter").prop('selectedIndex',0);
				$("select.pmaf-entries-fav").prop('selectedIndex',0);
				$("select.pmaf-form-titles").val("").trigger('change');
								
				pmaf_get_entries();
				
				return false;
			});
		}
		
		// pagination 
		pmaf_pagination( animated_entry_obj.total );
		
		// fav
		pmaf_fav_event();
		
	});
	
	function pmaf_pagination( _total ) {
		if( $(document).find("#pmaf-entries-pagination").length ) {
			
			$('#pmaf-entries-pagination').pagination({
				items: _total,
				itemsOnPage: pmaf_entry_limit,
				prevText: "&laquo;",
				nextText: "&raquo;",
				hrefTextPrefix: '#pmaf-entry-',
				onPageClick: function( pageNumber, event) {		
					pmaf_entry_pagi = pageNumber;
					pmaf_get_entries( true );
				}
			});
			
		}
	}
	
	function pmaf_fav_event() {
		
		if( $(document).find(".pmaf-make-fav").length ) {
			$(document).find(".pmaf-make-fav").on("click", function(e){
				
				e.preventDefault();
				let _cur = $(this);
				let entry_id = _cur.data("id");
				let _fav_stat = _cur.hasClass("liked") ? 0 : 1;
				_cur.addClass("processing");
				if( _cur ) {
					_cur.append('<img src="'+ animated_entry_obj.loader +'" />');
				}
				
				$.ajax({
					type: "POST",
					url: ajaxurl,
					data: {
						action: 'pmaf_make_fav',
						entry_id: entry_id,
						stat: _fav_stat,
						nonce: animated_entry_obj.get_entry_nonce
					},
					success: function(data) {
						if( _fav_stat ) {
							_cur.addClass("liked");
							_cur.attr("title", animated_entry_obj.strings.liked);
						} else {
							_cur.removeClass("liked");
							_cur.attr("title", animated_entry_obj.strings.make_fav);
						}
					},error: function(xhr, status, error) {
						console.log("failed");						
					}, complete: function () {
						_cur.find("img").remove();
						_cur.removeClass("processing");
					}
				});
				
				return false;
				
			});
		}
		
	}
	
	function pmaf_get_entries( from_pagi ) {
		
		$(".pmaf-entries-wrap").addClass("processing");
		$(".pmaf-entries-wrap").append('<img class="pmaf-entry-loader" src="'+ animated_entry_obj.loader +'" />');
		
		$.ajax({
			type: "POST",
			url: ajaxurl,
			data: {
				action: 'pmaf_get_entries',
				form_filter: pmaf_form_filter,
				read_filter: pmaf_read_filter,
				fav_filter: pmaf_fav_filter,
				page: pmaf_entry_pagi,
				limit: pmaf_entry_limit,
				nonce: animated_entry_obj.get_entry_nonce
			},
			success: function(data) { 
				if( $(document).find(".pmaf-all-entries").length ) {
					$(document).find(".pmaf-all-entries").html( data.table );
				} else if( $(document).find(".pmaf-no-entries").length ) {
					$(document).find(".pmaf-no-entries").replaceWith( data.table );
				} else {
					$(document).find(".pmaf-entries-wrap").append( data.table );
				}
				
				if( !from_pagi ) {
					pmaf_entry_pagi = 1;
					pmaf_pagination( data.total );
				}
				
				// fav
				pmaf_fav_event();
				
			},error: function(xhr, status, error) {
				console.log("failed");						
			}, complete: function () {
				$(".pmaf-entries-wrap").removeClass("processing");
				$(".pmaf-entries-wrap > img").remove();
			}
		});
		
	}
	
})( jQuery );