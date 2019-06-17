( function( $ ) {

	'use strict';

	var ukai_current_selector;

	$( document ).ready( function () {

		// Check if Icons Type is "Social Networks"
		function yz_isSocialNetwork( value, array ) {
			return array.indexOf( value ) > -1;
		}

		/**
		 * KAINELABS Icon Picker
		 */
		$.fn.ukai_iconPicker = function( selector ) {

			// Init icons list array .
		    var social_icons_list = [],
		 		icons_set = "adjust american-sign-language-interpreting anchor archive area-chart arrows arrows-h arrows-v assistive-listening-systems asterisk at audio-description balance-scale ban bar-chart barcode bars battery-empty battery-full battery-half battery-quarter battery-three-quarters bed beer bell bell-o bell-slash bell-slash-o bicycle binoculars birthday-cake blind bluetooth bluetooth-b bolt bomb book bookmark bookmark-o braille briefcase bug building building-o bullhorn bullseye bus calculator calendar calendar-check-o calendar-minus-o calendar-o calendar-plus-o calendar-times-o camera camera-retro car caret-square-o-down caret-square-o-left caret-square-o-right caret-square-o-up cart-arrow-down cart-plus cc certificate check check-circle check-circle-o check-square check-square-o child circle circle-o circle-o-notch circle-thin clock-o clone cloud cloud-download cloud-upload code code-fork coffee cog cogs comment comment-o commenting commenting-o comments comments-o compass copyright creative-commons credit-card credit-card-alt crop crosshairs cube cubes cutlery database deaf desktop diamond dot-circle-o download ellipsis-h ellipsis-v envelope envelope-o envelope-square eraser exchange exclamation exclamation-circle exclamation-triangle external-link external-link-square eye eye-slash eyedropper fax female fighter-jet file-archive-o file-audio-o file-code-o file-excel-o file-image-o file-pdf-o file-powerpoint-o file-video-o file-word-o film filter fire fire-extinguisher flag flag-checkered flag-o flask folder folder-o folder-open folder-open-o frown-o futbol-o gamepad gavel gift glass globe graduation-cap handshake-o hand-lizard-o hand-paper-o hand-peace-o hand-pointer-o hand-rock-o hand-scissors-o hand-spock-o hashtag hdd-o headphones heart heart-o heartbeat history home hourglass hourglass-end hourglass-half hourglass-o hourglass-start i-cursor inbox industry info info-circle key keyboard-o language laptop leaf lemon-o level-down level-up life-ring lightbulb-o line-chart location-arrow lock low-vision magic magnet male map map-marker map-o map-pin map-signs meh-o microphone microphone-slash minus minus-circle minus-square minus-square-o mobile money moon-o motorcycle mouse-pointer music newspaper-o object-group object-ungroup paint-brush paper-plane paper-plane-o paw pencil pencil-square pencil-square-o percent phone phone-square picture-o pie-chart plane plug plus plus-circle plus-square plus-square-o power-off print puzzle-piece qrcode question question-circle question-circle-o quote-left quote-right random recycle refresh registered reply reply-all retweet road rocket rss rss-square search search-minus search-plus server share share-alt share-alt-square share-square share-square-o shield ship shopping-bag shopping-basket shopping-cart sign-in sign-language sign-out signal sitemap sliders smile-o sort sort-alpha-asc sort-alpha-desc sort-amount-asc sort-amount-desc sort-asc sort-desc sort-numeric-asc sort-numeric-desc space-shuttle spinner spoon square square-o star star-half star-half-o star-o sticky-note sticky-note-o street-view suitcase sun-o tablet tachometer tag tags tasks taxi television terminal thumb-tack thumbs-down thumbs-o-down thumbs-o-up thumbs-up ticket times times-circle times-circle-o tint toggle-off toggle-on trademark trash trash-o tree trophy truck tty umbrella universal-access university unlock unlock-alt upload user user-plus user-secret user-times users video-camera volume-control-phone volume-down volume-off volume-up wheelchair wheelchair-alt wifi wrench 500px adn amazon android angellist apple behance behance-square bitbucket bitbucket-square black-tie bluetooth bluetooth-b btc buysellads cc-amex cc-diners-club cc-discover cc-jcb cc-mastercard cc-paypal cc-stripe cc-visa chrome codepen codiepie connectdevelop contao css3 dashcube delicious deviantart digg dribbble dropbox drupal edge empire envira expeditedssl facebook facebook-official facebook-square firefox first-order flickr font-awesome fonticons fort-awesome forumbee foursquare get-pocket gg gg-circle git git-square github github-alt github-square gitlab glide glide-g google google-plus google-plus-official google-plus-square google-wallet gratipay hacker-news houzz html5 instagram internet-explorer ioxhost joomla jsfiddle lastfm lastfm-square leanpub linkedin linkedin-square linux maxcdn meanpath medium mixcloud modx odnoklassniki odnoklassniki-square opencart openid opera optin-monster pagelines paypal pied-piper pied-piper-alt pied-piper-pp pinterest pinterest-p pinterest-square product-hunt qq rebel reddit reddit-alien reddit-square renren safari scribd sellsy share-alt share-alt-square shirtsinbulk simplybuilt skyatlas skype slack slideshare snapchat snapchat-ghost snapchat-square soundcloud spotify stack-exchange stack-overflow steam steam-square stumbleupon stumbleupon-circle tencent-weibo themeisle trello tripadvisor telegram tumblr tumblr-square twitch twitter twitter-square usb viacoin viadeo viadeo-square vimeo vimeo-square vine vk weibo weixin whatsapp wikipedia-w windows wordpress wpbeginner wpforms xing xing-square y-combinator yahoo yelp yoast youtube youtube-play youtube-square btc eur gbp gg gg-circle ils inr jpy krw money rub try usd arrows-alt backward compress eject expand fast-backward fast-forward forward pause pause-circle pause-circle-o play play-circle play-circle-o random step-backward step-forward stop stop-circle stop-circle-o youtube-play",
				social_icons_set = "500px adn amazon android angellist apple behance behance-square bitbucket bitbucket-square black-tie bluetooth bluetooth-b btc buysellads cc-amex cc-diners-club cc-discover cc-jcb cc-mastercard cc-paypal cc-stripe cc-visa chrome codepen codiepie connectdevelop contao css3 dashcube delicious deviantart digg dribbble dropbox drupal edge empire envira expeditedssl facebook facebook-official facebook-square firefox first-order flickr font-awesome fonticons fort-awesome forumbee foursquare get-pocket gg gg-circle git git-square github github-alt github-square gitlab glide glide-g google google-plus google-plus-official google-plus-square google-wallet gratipay hacker-news houzz html5 instagram internet-explorer ioxhost joomla jsfiddle lastfm lastfm-square leanpub linkedin linkedin-square linux maxcdn meanpath medium mixcloud modx odnoklassniki odnoklassniki-square opencart openid opera optin-monster pagelines paypal pied-piper pied-piper-alt pied-piper-pp pinterest pinterest-p pinterest-square product-hunt qq rebel reddit reddit-alien reddit-square renren safari scribd sellsy share-alt share-alt-square shirtsinbulk simplybuilt skyatlas skype slack slideshare snapchat snapchat-ghost snapchat-square soundcloud spotify stack-exchange stack-overflow steam steam-square stumbleupon stumbleupon-circle tencent-weibo themeisle trello tripadvisor telegram tumblr tumblr-square twitch twitter twitter-square usb viacoin viadeo viadeo-square vimeo vimeo-square vine vk weibo weixin whatsapp wikipedia-w windows wordpress wpbeginner wpforms xing xing-square y-combinator yahoo yelp yoast youtube youtube-play youtube-square btc eur gbp gg gg-circle ils inr jpy krw money rub try usd arrows-alt backward compress eject expand fast-backward fast-forward forward pause pause-circle pause-circle-o play play-circle play-circle-o random step-backward step-forward stop stop-circle stop-circle-o youtube-play";

			$.each( social_icons_set.split( ' ' ).slice( 0 , -1 ), function( i, item ) {
			 	social_icons_list.push( item );
			});

		    // Add icon picker list parent div .
		    $( 'body' ).append( '<div class="ukai-icons-list"></div>' );

			$.each( icons_set.split( " " ).slice( 0, -1 ), function( index, item ) {

				// Prepare icon Class Name
		    	var icon_class = 'fa-' + item;

		    	if ( yz_isSocialNetwork( item, social_icons_list ) ) {
		    		icon_class += " ukai-sn-icon";
		    	} else {
		    		icon_class += " ukai-apps-icon";
		    	}

		    	// Prepare icon html code .
			 	var icon_item = '<i class="ukai-font-icon fa ' + icon_class + '" aria-hidden="true" title="' + item + '"></i>';

			 	// Add icon the the icon picker list
				$( 'body .ukai-icons-list' ).append( icon_item );

			});

		    jQuery( document ).on( 'click', '.ukai-font-icon' , function( e ) {

		    	// Change the background color of the clicked icon
		      	$( 'i.ukai_active_icon' ).removeClass( 'ukai_active_icon' );
				$( this ).toggleClass( 'ukai_active_icon' );

				// Change current icon by the new selected icon
				var ukai_icon_name = $( this ).attr( 'title' );
				ukai_current_selector.find( '.ukai-selected-icon' ).val( ukai_icon_name );
				ukai_current_selector.find( '.ukai_icon_selector > i' ).removeClass();
				ukai_current_selector.find( '.ukai_icon_selector > i' ).addClass( 'fa fa-' + ukai_icon_name );

			});

		};

		/**
		 *  Show/Hide Icon Picker.
		 */
		$( document ).on( 'click', '.ukai_select_icon' , function( e ) {

		    $( 'i.ukai_active_icon' ).removeClass( 'ukai_active_icon' );
			ukai_current_selector = $( this ).closest( '.ukai_iconPicker' );
			var picker_offset 	  = $( this ).closest( '.ukai_iconPicker' ).offset(),
				picker_data 	  = ukai_current_selector.data( 'iconsType' );

			if ( picker_data === 'web-application' ) {
				$( '.ukai-apps-icon' ).show();
				$( '.ukai-sn-icon' ).show();
			} else if ( picker_data === 'social-networks' ) {
				$( '.ukai-apps-icon' ).hide();
			}

			// Append icon Picker To the active icons Field.
			$( this ).closest( '.ukai_iconPicker' ).append( $( '.ukai-icons-list' ).css( 'display', 'block' ) );

			// Change background color of the current icon in the icons list
		    var active_icon = $( this ).closest( '.ukai_iconPicker' )
		    .find( '.ukai-selected-icon' )
		    .attr( 'value' );

			// Make Current Icon Active
			$( '.ukai-icons-list' ).find( '.fa-' + active_icon ).addClass( 'ukai_active_icon' );

		});

		/**
		 *  Hide Icon Picker when user click outside of it.
		 */
		$( document ).mouseup( function ( e ) {

			// iP = iconPicker
	        var iP_list   = $( '.ukai-icons-list' ),
	        	iP_button = $( '.ukai_select_icon' );

	        // Hide iconPicker
	        if ( ! iP_list.is( e.target ) && iP_list.has( e.target ).length === 0 &&
	        	 ! iP_button.is( e.target ) && iP_button.has( e.target ).length === 0 ) {
	             iP_list.fadeOut( 250 );
	        }

	    });

		// Call Icon Picker Function
		$( 'body' ).ukai_iconPicker();

	});

})( jQuery );