( function( $ ) {

	'use strict';

	$( document ).ready( function() {

		/**
		 * #  Move Profile Widgets
		 */
		$( '#yz-profile-main-widgets, #yz-profile-sidebar-widgets' ).sortable( {
			connectWith: '#yz-profile-sidebar-widgets,#yz-profile-main-widgets',
			receive : function( event, ui ) {
				// Block Moving Unsortable Items to The Other Side
				if ( ui.item.hasClass( 'yz_unsortable' ) )  {
					ui.sender.sortable( 'cancel' );
					// Show Error Message
					$.ShowPanelMessage( {
						msg  : yz.move_wg,
						type : 'error'
					});
					return false;
				}

				// Get Widget Data
				var wg_type 	 = $( this ).data( 'widgetsType'),
					wg_name 	 = ui.item.data( 'widgetName' ),
					wg_name_attr = 'yz_profile_' + wg_type + '[]' + '[' + wg_name +  ']';

				// Change widget name.
				ui.item.find( '.yz_profile_widget' ).attr( 'name', wg_name_attr );
		    }

		} );

		/**
		 * #  Hide Profile Widgets
		 */
		$( '.yz-hide-wg' ).live( 'click' , function() {
			var widget = $( this ).closest( 'li' );
			widget.toggleClass( 'yz-hidden-wg' );
			// Change Input Value
			if ( widget.hasClass( 'yz-hidden-wg' ) ) {
				widget.find( '.yz_profile_widget' ).val( 'invisible' );
				widget.find( '.yz-hide-wg' ).attr( 'title', yz.show_wg );
			} else {
				widget.find( '.yz_profile_widget' ).val( 'visible' );
				widget.find( '.yz-hide-wg' ).attr( 'title', yz.hide_wg );
			}
		});

	});

})( jQuery );