(function( $ ) {
	'use strict';

  $("#obselling-link-to-inventory-tab").click( function(e) {
    e.preventDefault();
    $( '.inventory_tab a' ).click();
    var aid = $(this).attr("href");
    $('html,body').animate({scrollTop: $(aid).offset().top - 100},'slow');
  });

	$( '#the-list' ).on(
		'click',
		'.editinline',
		function() {

			inlineEditPost.revert();

			var post_id = $( this ).closest( 'tr' ).attr( 'id' );

			post_id = post_id.replace( 'post-', '' );

			var $is_obsolete_data = jQuery('#is_obsolete_inline_' + post_id);
			if( 'yes' == $is_obsolete_data.find("#is_obsolete").text() ) {
				jQuery( '.is_obsolete_field input' ).prop( 'checked', true );
			} else {
				jQuery( '.is_obsolete_field input' ).prop( 'checked', false );
			}

		});

})( jQuery );
