<?php
/**
 * This is an Add-ons page. Purpose of this page is to show a list of all the add-ons available to extend the functionality of LoginPress.
 *
 * @package LoginPress
 * @since 1.0.19
 *
 */

if ( ! class_exists( 'LoginPress_Addons' ) ) :

	class LoginPress_Addons {

		/**
		* Get the plugins list.
		*
		* @since  1.0.19
		* @access protected
		* @var    array
		*/
		protected $plugins_list;

		/* * * * * * * * * *
		* Class constructor
		* * * * * * * * * */
		function __construct() {

			$this->plugins_list = get_plugins();
		}

		function _get_addons() {

			// For Testing
			//delete_transient( 'loginpress_api_addons' );

		    // Get the transient where the addons are stored on-site.
		    $data = get_transient( 'loginpress_api_addons' );

		    // If we already have data, return it.
		    if ( ! empty( $data ) )
		        return $data;

		    // Make sure this matches the exact URL from your site.
		    //$url = 'http://localhost/wpbrigade.local/wp-json/wpbrigade/v1/plugins?addons=loginpress-pro-add-ons';
		    $url = 'https://wpbrigade.com/wp-json/wpbrigade/v1/plugins?addons=loginpress-pro-add-ons';

		    // Get data from the remote URL.
		    $response = wp_remote_get( $url, array( 'timeout' => 20 ) );

		    if ( ! is_wp_error( $response ) ) {

		        // Decode the data that we got.
		        $data = json_decode( wp_remote_retrieve_body( $response ) );

		        if ( ! empty( $data ) && is_array( $data ) ) {

		            // Store the data for a week.
		            set_transient( 'loginpress_api_addons', $data, 7 * DAY_IN_SECONDS );

		            return $data;
		        }
		    }

		    return false;
		}

		function _addon_card( $addon ) { ?>

			<div class="loginpress-extension<?php if( in_array('loginpress-free-add-ons', $this->convert_to_array($addon->categories) ) ){ echo ' loginpress-free-add-ons'; } ?>">
				<a target="_blank" href="https://wpbrigade.com/wordpress/plugins/loginpress-pro/?utm_source=loginpress-lite&utm_medium=addons-coming-soon&utm_campaign=pro-upgrade" class="logoinpress_addons_links">

				  <h3><img src=<?php if ( $addon->media->icon->url ) echo $addon->media->icon->url; else echo plugins_url( '../img/thumbnail/gray-loginpress.png', __FILE__ );?> class="logoinpress_addons_thumbnails"/><span><?php echo esc_html( $addon->title ); ?></span></h3>
				</a>

				<?php echo wpautop( wp_strip_all_tags( $addon->excerpt ) ); ?>
				<p>
					<?php
					$this->check_plugin_status( $addon->id, $addon->slug, $this->convert_to_array( $addon->categories ) );
					?>
				</p>
			</div>

		<?php }

		function _addon_card_free( $addon ) { ?>

			<div class="loginpress-extension<?php if( in_array('loginpress-free-add-ons', $this->convert_to_array($addon->categories) ) ){ echo ' loginpress-free-add-ons'; } ?>">
				<a target="_blank" href="https://wpbrigade.com/wordpress/plugins/loginpress-pro/?utm_source=loginpress-lite&utm_medium=addons-coming-soon&utm_campaign=pro-upgrade" class="logoinpress_addons_links">

				  <h3><img src=<?php if ( $addon->media->icon->url ) echo $addon->media->icon->url; else echo plugins_url( '../img/thumbnail/gray-loginpress.png', __FILE__ );?> class="logoinpress_addons_thumbnails"/><span><?php echo esc_html( $addon->title ); ?></span></h3>
				</a>

				<?php echo wpautop( wp_strip_all_tags( $addon->excerpt ) );

				if( in_array('loginpress-free-add-ons', $this->convert_to_array($addon->categories) ) ){
					$slug = $addon->slug.'/'.$addon->slug.'.php';

					if ( is_plugin_active( $slug ) ) {

						echo sprintf( esc_html__( '%1$s Already Installed %2$s', 'loginpress' ), '<button class="button-primary">', '</button>' );
					}else if ( array_key_exists( $slug , $this->plugins_list ) ) {

						$link = wp_nonce_url( add_query_arg( array( 'action' => 'activate', 'plugin' => $slug ), admin_url( 'plugins.php' ) ),  'activate-plugin_' . $slug ) ;
						echo sprintf( esc_html__( '%1$s Activate Plugin %2$s', 'loginpress' ), '<a href="' .  $link . '" class="button-primary">', '</a>' );
					}else{

						$action = 'install-plugin';
						$slug   = 'login-logout-menu';
						$link   = wp_nonce_url( add_query_arg( array( 'action' => $action, 'plugin' => $slug ), admin_url( 'update.php' ) ), $action . '_' . $slug );
						?>
						<p>
						  <a target="_self" href="<?php echo $link; ?>" class="button-primary">INSTALL</a>
						</p>
					<?php
					}
				}else{

				?>
				<p>
				  <a target="_blank" href="https://wpbrigade.com/wordpress/plugins/loginpress-pro/?utm_source=loginpress-lite&utm_medium=addons-coming-soon&utm_campaign=pro-upgrade" class="button-primary">UPGRADE NOW</a>
				</p>
				<?php
				}
				?>
			</div>

		<?php }

		function convert_to_array( $categories ) {

			$arr = array();

			foreach ($categories as $category) {

				$arr[] = $category->slug;
			}

			return $arr;
		}


		function get_addons_link() {

			$addons = $this->get_addons_name();

			foreach ( $addons as $addon ) {

				$action = 'install-plugin';
				$slug   = $addon['key'];
				$link   = wp_nonce_url( add_query_arg( array( 'action' => $action, 'plugin' => $slug, 'lgp' => 1 ), admin_url( 'update.php' ) ), $action . '_' . $slug );
			}
		}


		function is_addon_licensed( $categories ) {

			if( LoginPress_Pro::get_license_id() == 2 and in_array('loginpress-pro-small-business', $categories) ){
				return true;
			}
			else if( LoginPress_Pro::get_license_id() == 3 and in_array('loginpress-pro-agency', $categories) ){
				return true;
			}else if( LoginPress_Pro::get_license_id() == 4 and in_array('loginpress-pro-agency', $categories) ){
				return true;
			}else if( LoginPress_Pro::get_license_id() == 1 and in_array('loginpress-free-add-ons', $categories) ){
				return true;
			}else{
				return false;
			}
		}

		/**
		* Check plugin status
		*
		* @return array
		* @since 1.0.19
		*/
		public function check_plugin_status( $id, $slug, $categories = array() ) {

			$slug = $slug.'/'.$slug.'.php';

			if ( $this->is_addon_licensed ( $categories ) ) {

			 	if ( is_plugin_active( $slug ) ) {

					echo sprintf( esc_html__( '%1$s Already Installed %2$s', 'loginpress' ), '<button class="button-primary">', '</button>' );
				}
				else if ( array_key_exists( $slug , $this->plugins_list ) ) {

						$link = wp_nonce_url( add_query_arg( array( 'action' => 'activate', 'plugin' => $slug ), admin_url( 'plugins.php' ) ),  'activate-plugin_' . $slug ) ;
						echo sprintf( esc_html__( '%1$s Activate Plugin %2$s', 'loginpress' ), '<a href="' .  $link . '" class="button-primary">', '</a>' );
				}
				else{

					$link   = wp_nonce_url( add_query_arg( array( 'action' => 'install-plugin', 'plugin' => $slug, 'lgp' => 1, 'id' => $id), admin_url( 'update.php' ) ), 'install-plugin_' . $slug );
					echo sprintf( esc_html__( '%1$s Install %2$s', 'loginpress' ), '<a  href="' . $link . '" class="button-primary">', '</a>' );

					?>
					<!-- <a target="_blank" href="https://wpbrigade.com/wordpress/plugins/loginpress-pro/?utm_source=loginpress-lite&utm_medium=addons-coming-soon&utm_campaign=pro-upgrade" class="button-primary">INSTALL NOW</a> -->
					<?php
				}
			}
			else {

				?>
					<a target="_blank" href="https://wpbrigade.com/wordpress/plugins/loginpress-pro/?utm_source=loginpress-lite&utm_medium=addons-coming-soon&utm_campaign=pro-upgrade" class="button-primary">UPGRADE NOW</a>
					<?php
			}
		}

		public function validate_addons() {

			$data = get_transient( 'loginpress_api_addons' );


		}

		public function show_addon_page() {

			if ( class_exists('LoginPress_Pro') ) {

				if ( LoginPress_Pro::is_activated() ) {

			            $expiration_date = LoginPress_Pro::get_expiration_date();

			            if ( 'lifetime' == $expiration_date ) {
			                echo esc_html__( 'You have a lifetime license, it will never expire.', 'loginpress-pro' );
			            }
			            else {
			                echo '<div class="main_notice_msg">' . sprintf(
			                  esc_html__( 'Your (%2$s) license key is valid until %s.', 'loginpress-pro' ),
			                  '<strong>' . date_i18n( get_option( 'date_format' ), strtotime( $expiration_date, current_time( 'timestamp' ) ) ) . '</strong>', LoginPress_Pro::get_license_type()
			                ) . '</div>';
			            }

			    	?> <div class="addon_cards_wraper"> <?php
					foreach( $this->_get_addons() as $addon ) {

						$this->_addon_card( $addon );
					}
					?> </div> <?php

				}else{

					echo '<div class="main_notice_msg">' . sprintf( esc_html__( 'You need to activate your license to download the following add-ons.', 'loginpress-pro' ) ) . '</div>';

					// Show full list of add-ons
					?> <div class="addon_cards_wraper"> <?php
					foreach( $this->_get_addons() as $addon ) {

						$this->_addon_card_free( $addon );
					}
					?> </div> <?php
				}

			}else{

				echo '<div class="main_notice_msg">' . sprintf( esc_html__( 'You need to upgrade to LoginPress Pro to access these add-ons.', 'loginpress-pro' ) ) . '</div>';

				// Show full list of add-ons
				?> <div class="addon_cards_wraper"> <?php
				foreach( $this->_get_addons() as $addon ) {

					$this->_addon_card_free( $addon );
				}
				?> </div> <?php
			}
		}

	} // Enf of Class.

endif;


	$obj_loginpress_addons	= new LoginPress_Addons();
	$loginpress_addons 		= $obj_loginpress_addons->_get_addons();
	?>

	<!-- Style for Add-ons Page -->
	<style media="screen">
		.loginpress-extension h3 {
			box-sizing: border-box;
			height: 110px;
			margin: -2px -2px 0 0;
			padding: 20px 10px 0 135px;
			border-bottom: 1px solid #a5dff6;

			background-size: 115px 100px;
			position: relative;
			overflow: hidden;
		}
		.loginpress-free-add-ons h3:after{
		    content: "Free";
		    position: absolute;
		    top: 10px;
		    right: -30px;
		    width: 100px;
		    height: 30px;
		    background-color: #00a0d2;
		    color: #fff;
		    transform: rotate(45deg);
		    line-height: 30px;
		    text-align: center;
		    font-size: 13px;
		}
		.loginpress-extension {
			float: none;
			box-sizing: border-box;
			width: calc(33% - 20px);
			margin: 0px 0px 30px 30px;
			border: 1px solid #a5dff6;
			display: inline-block;
			height: auto;
			vertical-align: top;
			background: #fff;
			min-height: 300px;
			position: relative;
			padding-bottom: 50px;
			max-width: 465px;
	  	}
		.loginpress-extension:nth-child(3n+1){
			margin-left: 0;
		}
	  .loginpress-extension .button-primary{
			border:0;
			text-shadow:none;
			background:#1a61a7;
			padding:8px 18px;
			height:auto;
			font-size:15px;
			cursor: pointer;
		    position: absolute;
    		bottom: 15px;
    		left: 50%;
    		transform: translateX(-50%);
			box-shadow:none;
			border-radius:0;
			transition: background-color .3s;
	  	}
	   .loginpress-extension .button-primary:active,.loginpress-extension .button-primary:hover,.loginpress-extension .button-primary:focus{
		background: #36bcf2;
		box-shadow: none;
		outline: none;
	  }
	  .notice_msg{
	  	box-shadow: rgba(0, 0, 0, 0.1) 0px 1px 1px 0px;
	    background: rgb(255, 255, 255);
	    border-left: 4px solid #46b450;
	    margin: 5px 0 20px;
	    padding: 15px;
	  }
	  .main_notice_msg{
	    background: #1a61a7;
	    margin: 5px 0 20px;
	    padding: 15px;
		color: #fff;
		display: inline-block;
	  }
	  .loginpress-extension button.button-primary{
		background: #f9fafa;
		border-radius: 0;
		box-shadow: none;
		color: #444;
		    position: absolute;
    bottom: 15px;
    left: 50%;
    transform: translateX(-50%);

 border: 2px solid #a5dff6 !important;
    background: #d3f3ff54 !important;
		cursor: default;
		transition: background-color .3s;
	  }
	  .loginpress-extension button.button-primary:visited,.loginpress-extension button.button-primary:active,.loginpress-extension button.button-primary:hover,.loginpress-extension button.button-primary:focus{
		background: #36bcf2;
		color: #444;
		border: 0;

		outline: none;
		box-shadow: none;
	  }
	  .logoinpress_addons_thumbnails{

		max-width: 100px;
		position: absolute;
		top: 5px;
		left: 10px;
		max-height: 95px;
		height: auto;
		width: auto;
	  }
	  .loginpress-extension .logoinpress_addons_links{
		position: relative;
		background-color: #d3f3ff;
	  }
	  .loginpress-extension p {
		margin: 0;
		padding: 10px 20px;
	  }
	  .loginpress-addons-loading-errors {
		padding-top: 15px;
	  }
	  .loginpress-addons-loading-errors img {
		float: left;
		padding-right: 10px;
	  }
		.loginpress-addons-wrap{

		}
		.loginpress-extension h3 {
	    box-sizing: border-box;
	    /* height: 110px; */

	    padding: 0 10px 0 20px;
	    border-bottom: 2px solid #a5dff6;
	    background-size: 115px 100px;
	    height: 100px;
	     color: #000000;
	}
		a.logoinpress_addons_links {
		    display: inline-block;
		    width: 100%;
		    line-height: 90px;
		    padding-bottom: 0px;
		    height: auto;
				text-decoration: none;
		}
		.logoinpress_addons_thumbnails {
		    max-width: 100px;
		    position: absolute;
		    top: 5px;
		    left: 10px;
		    max-height: 75px;
		    height: auto;
		    width: auto;
		    position: static;
		    vertical-align: middle;
		    margin-right: 20px;
		}
		.loginpress-extension{
			border-width: 2px;
		}
		@media only screen and (min-width: 1680px) {
			.loginpress-extension{
				min-height: 315px;
				width: calc(25% - 27px);
			}
			.loginpress-extension:nth-child(3n+1){
				margin-left: 30px;
			}
			.loginpress-extension:nth-child(4n+1){
				margin-left: 0;
			}
		}
		@media only screen and (max-width: 1500px) {
			.loginpress-extension{
				min-height: 330px
			}
		}
		@media only screen and (max-width: 1400px) {
			.loginpress-extension{
				min-height: 300px;
				width: calc(50% - 20px);
			}
			.loginpress-extension:nth-child(3n+1){
				margin-left: 30px;
			}
			.loginpress-extension:nth-child(2n+1){
				margin-left: 0;
			}
		}
		@media only screen and (max-width: 600px) {
			.loginpress-extension{
				width:100%;
			}
		}

  	</style>

	<div class="wrap loginpress-addons-wrap">

		<h2 class='opt-title'>
		  <?php esc_html_e( 'Extend the functionality of LoginPress with these awesome Add-ons', 'loginpress' ); ?>
		</h2>

		<div class="tabwrapper">
			<?php
			$obj_loginpress_addons->show_addon_page();
			?>
		</div>
	</div>
