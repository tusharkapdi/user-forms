/*------------------------ 
Backend related javascript
------------------------*/

/**
 * COMMENT START
 * 
 * This file contains all of the backend related javascript. 
 * With backend, it is meant the WordPress admin area.
 * 
 * Since you added the jQuery dependency within the "Add JS support" module, you see down below
 * the helper comment a function that allows you to use jQuery with the commonly known notation: $('')
 * By default, this notation is deactivated since WordPress uses the noConflict mode of jQuery
 * You can also use jQuery outside using the following notation: jQuery('')
 * 
 * Here's some jQuery example code you can use to fire code once the page is loaded: $(document).ready( function(){} );
 * 
 * Using the ajax example, you can send data back and forth between your frontend and the 
 * backend of the website (PHP to ajax and vice-versa). 
 * As seen in the example below, we use the jQuery $.ajax function to send data to the WordPress
 * callback my_demo_ajax_call, which was added within the User_Forms_Run class.
 * From there, we process the data and send it back to the code below, which will then display the 
 * example within the console of your browser.
 * 
 * You can add the localized variables in here as followed: userforms.plugin_name
 * These variables are defined within the localization function in the following file:
 * core/includes/classes/class-user-forms-run.php
 * 
 * COMMENT END
 */

function convertToSlug(Text) {
  return Text.trim().toString().toLowerCase()
    .replace(/[^\w ]+/g, "")
    .replace(/ +/g, "-")
    .substring(0,25);
}

(function( $ ) {

	"use strict";

	$(document).ready( function() {
		
		$('.addfield').on('click', function(e){
			
			if( $('table.form-table-tk tbody:contains("initialrow")') ){

				$(".initialrow").hide();
			}

			var tr = $('<tr><td><span title="Drag & Drop" class="movefield dashicons dashicons-move"></span></td><td><select class="type" name="user_forms_opt[fields][type][]"><option value="text">Text</option><option value="number">Number</option><option value="textarea">Textarea</option><option value="select">Dorpdown</option><option value="checkbox">Checkbox</option><option value="radio">Radio</option><option value="country">Country</option><option value="heading">Heading</option></select></td><td><input type="hidden" name="user_forms_opt[fields][slug][]" /><input type="text" name="user_forms_opt[fields][title][]" /></td><td><textarea name="user_forms_opt[fields][option][]"></textarea></td><td><select name="user_forms_opt[fields][required][]"><option value="0">No</option><option value="1">Yes</option></select></td><td><span title="Delete" class="removefield dashicons dashicons-remove"></span></td></tr>').hide();
			$("table.form-table-tk tbody").append(tr); 
			tr.show('slow');
		});

		$(document).on('click','.removefield',function() {
			//$(this).parent().parent().remove();
			var tr = $(this).parent().parent();
			tr.fadeOut("slow", function() {
				tr.remove();
			});

			if( $('table.form-table-tk tbody tr').length == 1 ){
				$(".initialrow").show();
			}
		});

		$(document).on('change','table.form-table-tk select.type',function() {
			
			if( $.inArray( $(this).val(), ['checkbox', 'radio', 'select'] ) != -1 ){
				$(this).parent().parent().find("textarea").fadeIn('slow');
			}else{
				$(this).parent().parent().find("textarea").fadeOut('slow');
			}
		});

		$(document).on('blur','table.form-table-tk input[type="text"]',function() {
			
			$(this).parent().find('input[type="hidden"]').val( convertToSlug( $(this).val() ) );
		});

	});

})( jQuery );