(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-specific JavaScript source
	 * should reside in this file.
	 *
	 * Note that this assume you're going to use jQuery, so it prepares
	 * the $ function reference to be used within the scope of this
	 * function.
	 *
	 * From here, you're able to define handlers for when the DOM is
	 * ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * Or when the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and so on.
	 *
	 * Remember that ideally, we should not attach any more than a single DOM-ready or window-load handler
	 * for any particular page. Though other scripts in WordPress core, other plugins, and other themes may
	 * be doing this, we should try to minimize doing that in our own work.
	 */
	 
	 $(document).ready(function(){
		 $('#runimport').click(function(e){
			 $('#importresults').html('');
			 $('#loader').show();
			 $('#loadermsg').show();
			 $('#showdetails').show();
			 var postidval = $('#postidval').val();
			 var maxproducts = $('#saff_noproducts').val();			
			 $.ajax({
				type: "post",
				url: ajaxurl,
   	 			dataType: "json",
				data: {postid : postidval,action: 'saff_import'}, 
				error: function(jqXHR, exception) {
					if (jqXHR.status === 0) {
						alert('Not connect.\n Verify Network.');
					} else if (jqXHR.status == 404) {
						alert('Requested page not found. [404]');
					} else if (jqXHR.status == 500) {
						alert('Internal Server Error [500].');
					} else if (exception === 'parsererror') {
						alert('Requested JSON parse failed.');
					} else if (exception === 'timeout') {
						alert('Time out error.');
					} else if (exception === 'abort') {
						alert('Ajax request aborted.');
					} else {
						alert('Uncaught Error.\n' + jqXHR.responseText);
					}
				},
				success: function(response){
					//console.log(response);
					//alert(response);
					var prodarr = [];
					var ic = 0;
					$.each( response.products, function( key, value ) {
						prodarr.push(value)
						ic++;
					});	
					var maxproducts = $('#saff_noproducts').val();	
					if(prodarr.length < maxproducts){
						maxproducts = prodarr.length;
					}
					$('#importresults').append('<b>Products Data Received. Posting Products now...</b><br /><br />');
					add_product(prodarr, 0,maxproducts);			
				}
			}); 
			e.preventDefault();
		 });
		 $('#showdetails').click(function(e){
			 $('#importresults').toggle();
			 e.preventDefault();
		 });
	 });
	 
	 function add_product(prarr, arindex,maxcount){
		var postidval = $('#postidval').val();
		var tot = maxcount;
		var prodinx = arindex;
		var newprodarr = prarr;
		var array1 = {};
		$.each( newprodarr[arindex], function( skey, svalue ) {
			array1[skey] = svalue;				
		});	
		array1['campaignid'] = postidval;
		var myJsonString = JSON.stringify(array1);
		$.ajax({
			type: "post",
			url: ajaxurl,
			data: {myJsonString : myJsonString,action: 'saff_import_data'}, 
			success: function(response){
				//console.log(response);
				$('#importresults').append(response);		
				var newprodinx = parseInt(prodinx)+parseInt(1);
				//alert(newprodinx);
				if(newprodinx < tot){
					add_product(newprodarr, newprodinx,tot);								
				}else{
					$('#loader').hide();
					$('#loadermsg').hide();
					$('#importresults').append('<b>All Products Updated!...</b><br /><br />');
					alert('All Products Updated!');
				}
			}
		}); 
		 
	 }
	
})( jQuery );
