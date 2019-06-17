<?php
/**
* Plugin Name: LoginPress - Customizing the WordPress Login
* Plugin URI: https://WPBrigade.com/wordpress/plugins/loginpress/
* Description: LoginPress is the best <code>wp-login</code> Login Page Customizer plugin by <a href="https://wpbrigade.com/">WPBrigade</a> which allows you to completely change the layout of login, register and forgot password forms.
* Version: 1.1.13
* Author: WPBrigade
* Author URI: https://WPBrigade.com/
* Text Domain: loginpress
* Domain Path: /languages
*
* @package loginpress
* @category Core
* @author WPBrigade
*/


if ( ! class_exists( 'LoginPress' ) ) :

  final class LoginPress {

    /**
    * @var string
    */
    public $version = '1.1.13';

    /**
    * @var The single instance of the class
    * @since 1.0.0
    */
    protected static $_instance = null;

    /**
    * @var WP_Session session
    */
    public $session = null;

    /**
    * @var WP_Query $query
    */
    public $query = null;

    /**s
    * @var WP_Countries $countries
    */
    public $countries = null;

    /* * * * * * * * * *
    * Class constructor
    * * * * * * * * * */
    public function __construct() {

      $this->define_constants();
      $this->includes();
      $this->_hooks();

    }

    /**
    * Define LoginPress Constants
    */
    private function define_constants() {

      $this->define( 'LOGINPRESS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
      $this->define( 'LOGINPRESS_DIR_PATH', plugin_dir_path( __FILE__ ) );
      $this->define( 'LOGINPRESS_DIR_URL', plugin_dir_url( __FILE__ ) );
      $this->define( 'LOGINPRESS_ROOT_PATH',  dirname( __FILE__ ) . '/' );
      $this->define( 'LOGINPRESS_ROOT_FILE', __FILE__ );
      $this->define( 'LOGINPRESS_VERSION', $this->version );
      $this->define( 'LOGINPRESS_FEEDBACK_SERVER', 'https://wpbrigade.com/' );
      }

    /**
    * Include required core files used in admin and on the frontend.
    * @version 1.1.7
    */

    public function includes() {

      include_once( LOGINPRESS_DIR_PATH . 'include/compatibility.php' );
      include_once( LOGINPRESS_DIR_PATH . 'custom.php' );
      include_once( LOGINPRESS_DIR_PATH . 'classes/class-loginpress-setup.php' );
      include_once( LOGINPRESS_DIR_PATH . 'classes/class-loginpress-ajax.php' );
      include_once( LOGINPRESS_DIR_PATH . 'classes/class-loginpress-filter-plugin.php' );
      include_once( LOGINPRESS_DIR_PATH . 'classes/class-loginpress-developer-hooks.php' );
      if ( is_multisite() ) {
  			require_once( LOGINPRESS_DIR_PATH . 'include/class-loginpress-theme-template.php' );
      }

      $loginpress_setting = get_option( 'loginpress_setting' );

      $loginpress_privacy_policy = isset( $loginpress_setting['enable_privacy_policy'] ) ?  $loginpress_setting['enable_privacy_policy'] : 'off';
      if ( 'off' != $loginpress_privacy_policy ) {
        include_once( LOGINPRESS_DIR_PATH . 'include/privacy-policy.php' );
      }

			$login_with_email = isset( $loginpress_setting['login_order'] ) ?  $loginpress_setting['login_order'] : 'default';
      if ( 'default' != $login_with_email ) {
        include_once( LOGINPRESS_DIR_PATH . 'classes/class-loginpress-login-order.php' );
        new LoginPress_Login_Order();
      }

      $enable_reg_pass_field = isset( $loginpress_setting['enable_reg_pass_field'] ) ?  $loginpress_setting['enable_reg_pass_field'] : 'off';
      if ( 'off' != $enable_reg_pass_field ) {
        include_once( LOGINPRESS_DIR_PATH . 'classes/class-loginpress-custom-password.php' );
        new LoginPresss_Custom_Password();
      }
    }

    /**
    * Hook into actions and filters
    * @since  1.0.0
    */
    private function _hooks() {

      add_action( 'admin_menu',             array( $this, 'register_options_page' ) );
      add_action( 'plugins_loaded',         array( $this, 'textdomain' ) );
      add_filter( 'plugin_row_meta',        array( $this, '_row_meta'), 10, 2 );
      add_action( 'admin_enqueue_scripts',  array( $this, '_admin_scripts' ) );
      add_action( 'admin_footer',           array( $this, 'add_deactive_modal' ) );
			add_action( 'admin_init',             array( $this, 'loginpress_review_notice' ) );
      add_action( 'admin_init' ,            array( $this, 'loginpress_addon_notice' ) );
      add_action( 'plugin_action_links', 	  array( $this, 'loginpress_action_links' ), 10, 2 );
      add_action( 'admin_init',             array( $this, 'redirect_optin' ) );
      add_filter( 'auth_cookie_expiration', array( $this, '_change_auth_cookie_expiration' ), 10, 3 );
      //add_filter( 'plugins_api',            array( $this, 'get_addon_info_' ) , 100, 3 );
      if ( is_multisite() ) {
  			add_action( 'admin_init',             array( $this, 'redirect_loginpress_edit_page' ) );
  			add_action( 'admin_init',             array( $this, 'check_loginpress_page' ) );
      }

    }

    /**
    * Redirect to Optin page.
    *
    * @since 1.0.15
    */
    function redirect_optin() {

      // delete_option( '_loginpress_optin' );

      if ( isset( $_POST['loginpress-submit-optout'] ) ) {

        update_option( '_loginpress_optin', 'no' );
        $this->_send_data( array(
          'action'	=>	'Skip',
        ) );

      } elseif ( isset( $_POST['loginpress-submit-optin'] ) ) {

        update_option( '_loginpress_optin', 'yes' );
        $fields = array(
          'action'	        =>	'Activate',
          'track_mailchimp' =>	'yes'
        );
        $this->_send_data( $fields );

      } elseif ( ! get_option( '_loginpress_optin' ) && isset( $_GET['page'] ) && ( $_GET['page'] === 'loginpress-settings' || $_GET['page'] === 'loginpress' || $_GET['page'] === 'abw' ) ) {

        wp_redirect( admin_url('admin.php?page=loginpress-optin&redirect-page=' . $_GET['page'] ) );
        exit;
      } elseif ( get_option( '_loginpress_optin' ) && ( get_option( '_loginpress_optin' ) == 'yes' || get_option( '_loginpress_optin' ) == 'no' ) && isset( $_GET['page'] ) && $_GET['page'] === 'loginpress-optin' ) {

        wp_redirect( admin_url( 'admin.php?page=loginpress-settings' ) );
        exit;
      }
    }


    /**
    * Main Instance
    *
    * @since 1.0.0
    * @static
    * @see loginPress_loader()
    * @return Main instance
    */
    public static function instance() {
      if ( is_null( self::$_instance ) ) {
        self::$_instance = new self();
      }
      return self::$_instance;
    }


    /**
    * Load Languages
    * @since 1.0.0
    */
    public function textdomain() {

      $plugin_dir =  dirname( plugin_basename( __FILE__ ) ) ;
      load_plugin_textdomain( 'loginpress', false, $plugin_dir . '/languages/' );
    }

    /**
    * Define constant if not already set
    * @param  string $name
    * @param  string|bool $value
    */
    private function define( $name, $value ) {
      if ( ! defined( $name ) ) {
        define( $name, $value );
      }
    }

    /**
    * Init WPBrigade when WordPress Initialises.
    */
    public function init() {
      // Before init action
    }

    /**
		 * Create LoginPress Page Template.
		 *
		 * @since 1.1.3
		 */
		public function check_loginpress_page() {

			// Retrieve the LoginPress admin page option, that was created during the activation process.
			$option = $this->get_loginpress_page();

      include LOGINPRESS_DIR_PATH . 'include/create-loginpress-page.php';
			// Retrieve the status of the page, if the option is available.
			if ( $option ) {
				$page   = get_post( $option );
				$status = $page->post_status;
			} else {
				$status = null;
			}

			// Check the status of the page. Let's fix it, if the page is missing or in the trash.
			if ( empty( $status ) || 'trash' === $status ) {
				new LoginPress_Page_Create();
			}
		}

    /**
		 * function for redirect the LoginPress page on editing.
		 *
		 * @since 1.1.3
		 */
		public function redirect_loginpress_edit_page() {
			global $pagenow;

			$page = $this->get_loginpress_page();

			if ( ! $page ) {
				return;
			}

			$page_url = get_permalink( $page );
			$page_id  = get_post( $page );
			$page_id  = $page->ID;

			// Generate the redirect url.
			$url = add_query_arg(
				array(
					'autofocus[section]' => 'loginpress_panel',
					'url'                => rawurlencode( $page_url ),
				),
				admin_url( 'customize.php' )
			);

			/* Check current admin page. */
			if ( $pagenow == 'post.php' && isset( $_GET['post'] ) && $_GET['post'] == $page_id ) {
				wp_safe_redirect( $url );
			}
		}

    /**
    * Add new page in Apperance to customize Login Page
    */
    public function register_options_page() {

      add_submenu_page( null, __( 'Activate', 'loginpress' ), __( 'Activate', 'loginpress' ), 'manage_options', 'loginpress-optin', array( $this, 'render_optin' )  );

      add_theme_page( __( 'LoginPress', 'loginpress' ), __( 'LoginPress', 'loginpress' ), 'manage_options', "abw", '__return_null' );
    }


    /**
     * Show Opt-in Page.
     *
     * @since 1.0.15
     */
		function render_optin() {
			include LOGINPRESS_DIR_PATH . 'include/loginpress-optin-form.php';
		}

    /**
    * Wrapper function to send data.
    * @param  [arrays]  $args.
    *
    * @since  1.0.15
    * @version 1.0.23
    */
 function _send_data( $args ) {

   $cuurent_user = wp_get_current_user();
   $fields = array(
     'email' 		         => get_option( 'admin_email' ),
     'website' 			     => get_site_url(),
     'action'            => '',
     'reason'            => '',
     'reason_detail'     => '',
     'display_name'			 => $cuurent_user->display_name,
     'blog_language'     => get_bloginfo( 'language' ),
     'wordpress_version' => get_bloginfo( 'version' ),
     'php_version'       => PHP_VERSION,
     'plugin_version'    => LOGINPRESS_VERSION,
     'plugin_name' 			 => 'LoginPress Free',
   );

   $args = array_merge( $fields, $args );
   $response = wp_remote_post( LOGINPRESS_FEEDBACK_SERVER, array(
     'method'      => 'POST',
     'timeout'     => 5,
     'httpversion' => '1.0',
     'blocking'    => true,
     'headers'     => array(),
     'body'        => $args,
   ) );

  //  echo '<pre>'; print_r( $args ); echo '</pre>';

 }

   /**
    * Session Expiration
    *
    * @since  1.0.18
    */
   function _change_auth_cookie_expiration( $expiration, $user_id, $remember ) {

     $loginpress_setting = get_option( 'loginpress_setting' );
     $_expiration        = isset( $loginpress_setting['session_expiration'] ) ? intval( $loginpress_setting['session_expiration'] ) : '';

     if ( empty( $_expiration ) || '0' == $_expiration ) {
       return $expiration;
     }

      $expiration = $_expiration * 60; // Convert Duration (minutes) of the expiration period in seconds.

     return $expiration;
   }

    /**
     * Load JS or CSS files at admin side and enqueue them
     * @param  string tell you the Page ID
     * @return void
     */
    function _admin_scripts( $hook ) {

      if( $hook == 'toplevel_page_loginpress-settings' || $hook == 'loginpress_page_loginpress-help' || $hook == 'loginpress_page_loginpress-import-export' || $hook == 'loginpress_page_loginpress-license' ) {

        wp_enqueue_style( 'loginpress_stlye', plugins_url( 'css/style.css', __FILE__ ), array(), LOGINPRESS_VERSION );
        wp_enqueue_script( 'loginpress_js', plugins_url( 'js/admin-custom.js', __FILE__ ), array(), LOGINPRESS_VERSION );

        // Array for localize.
        $loginpress_localize = array(
          'plugin_url' => plugins_url(),
        );

        wp_localize_script( 'loginpress_js', 'loginpress_script', $loginpress_localize );
      }

    }


    public function _row_meta( $links, $file ) {

      if ( strpos( $file, 'loginpress.php' ) !== false ) {

        // Set link for Reviews.
        $new_links = array('<a href="https://wordpress.org/support/plugin/loginpress/reviews/?filter=5" target="_blank"><span class="dashicons dashicons-thumbs-up"></span> ' . __( 'Vote!', 'loginpress' ) . '</a>',
        );

        $links = array_merge( $links, $new_links );
      }

      return $links;
    }

    /**
     * Add deactivate modal layout.
     */
    function add_deactive_modal() {
      global $pagenow;

      if ( 'plugins.php' !== $pagenow ) {
        return;
      }

      include LOGINPRESS_DIR_PATH . 'include/deactivate_modal.php';
      include LOGINPRESS_DIR_PATH . 'include/loginpress-optout-form.php';
    }

  /**
   * Plugin activation
   *
   * @since  1.0.15
   * @version 1.0.22
   */
  static function plugin_activation() {

    $loginpress_key     = get_option( 'loginpress_customization' );
    $loginpress_setting = get_option( 'loginpress_setting' );

    // Create a key 'loginpress_customization' with empty array.
    if ( ! $loginpress_key ) {
      update_option( 'loginpress_customization', array() );
    }

    // Create a key 'loginpress_setting' with empty array.
    if ( ! $loginpress_setting ) {
      update_option( 'loginpress_setting', array() );
    }
  }

  static function plugin_uninstallation() {

    $email         = get_option( 'admin_email' );

    $fields = array(
      'email' 		        => $email,
      'website' 			    => get_site_url(),
      'action'            => 'Uninstall',
      'reason'            => '',
      'reason_detail'     => '',
      'blog_language'     => get_bloginfo( 'language' ),
      'wordpress_version' => get_bloginfo( 'version' ),
      'php_version'       => PHP_VERSION,
      'plugin_version'    => LOGINPRESS_VERSION,
      'plugin_name' 			=> 'LoginPress Free',
    );

    $response = wp_remote_post( LOGINPRESS_FEEDBACK_SERVER, array(
      'method'      => 'POST',
      'timeout'     => 5,
      'httpversion' => '1.0',
      'blocking'    => false,
      'headers'     => array(),
      'body'        => $fields,
    ) );

  }

  /**
	 * Ask users to review our plugin on wordpress.org
	 *
	 * @since 1.1.3
	 * @return boolean false
	 */
	public function loginpress_addon_notice() {

		$this->loginpress_addon_dismissal();

		$activation_time 	= get_site_option( 'loginpress_addon_active_time' );
		$addon_dismissal	= get_site_option( 'loginpress_addon_dismiss_1' );

		if ( 'yes' == $addon_dismissal ) return;

		if ( ! $activation_time ) :

			$activation_time = time();
			add_site_option( 'loginpress_addon_active_time', $activation_time );
		endif;

		// 432000 = 5 Days in seconds.
		// if ( time() - $activation_time > 432000 ) :

			add_action( 'admin_notices' , array( $this, 'loginpress_addon_notice_text' ) );
		// endif;

	}

  /**
	 * Ask users to review our plugin on wordpress.org
	 *
	 * @since 1.0.11
	 * @return boolean false
	 * @version 1.1.3
	 */
	public function loginpress_review_notice() {

		$this->loginpress_review_dismissal();
		$this->loginpress_review_pending();

		$activation_time 	= get_site_option( 'loginpress_active_time' );
		$review_dismissal	= get_site_option( 'loginpress_review_dismiss' );

		if ( 'yes' == $review_dismissal ) return;

		if ( ! $activation_time ) :

			$activation_time = time();
			add_site_option( 'loginpress_active_time', $activation_time );
		endif;

		// 1296000 = 15 Days in seconds.
		if ( time() - $activation_time > 1296000 ) :

      wp_enqueue_style( 'loginpress_review_stlye', plugins_url( 'css/style-review.css', __FILE__ ), array(), LOGINPRESS_VERSION );
			add_action( 'admin_notices' , array( $this, 'loginpress_review_notice_message' ) );
		endif;

	}

  /**
	 *	Check and Dismiss review message.
	 *
	 *	@since 1.0.11
	 */
	private function loginpress_review_dismissal() {

		if ( ! is_admin() ||
			! current_user_can( 'manage_options' ) ||
			! isset( $_GET['_wpnonce'] ) ||
			! wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ), 'loginpress-review-nonce' ) ||
			! isset( $_GET['loginpress_review_dismiss'] ) ) :

			return;
		endif;

		add_site_option( 'loginpress_review_dismiss', 'yes' );
	}

  /**
	 * Set time to current so review notice will popup after 14 days
	 *
	 * @since 1.0.11
	 */
	function loginpress_review_pending() {

		if ( ! is_admin() ||
			! current_user_can( 'manage_options' ) ||
			! isset( $_GET['_wpnonce'] ) ||
			! wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ), 'loginpress-review-nonce' ) ||
			! isset( $_GET['loginpress_review_later'] ) ) :

			return;
		endif;

		// Reset Time to current time.
		update_site_option( 'loginpress_active_time', time() );
	}

  /**
	 * Review notice message
	 *
	 * @since  1.0.11
	 */
	public function loginpress_review_notice_message() {

		$scheme      = ( parse_url( $_SERVER['REQUEST_URI'], PHP_URL_QUERY ) ) ? '&' : '?';
		$url         = $_SERVER['REQUEST_URI'] . $scheme . 'loginpress_review_dismiss=yes';
		$dismiss_url = wp_nonce_url( $url, 'loginpress-review-nonce' );

		$_later_link = $_SERVER['REQUEST_URI'] . $scheme . 'loginpress_review_later=yes';
		$later_url   = wp_nonce_url( $_later_link, 'loginpress-review-nonce' );
    ?>

		<div class="loginpress-review-notice">
			<div class="loginpress-review-thumbnail">
				<img src="<?php echo plugins_url( 'img/thumbnail/gray-loginpress.png', __FILE__ ) ?>" alt="">
			</div>
			<div class="loginpress-review-text">
				<h3><?php _e( 'Leave A Review?', 'loginpress' ) ?></h3>
				<p><?php _e( 'We hope you\'ve enjoyed using LoginPress! Would you consider leaving us a review on WordPress.org?', 'loginpress' ) ?></p>
				<ul class="loginpress-review-ul">
          <li><a href="https://wordpress.org/support/view/plugin-reviews/loginpress?rate=5#postform" target="_blank"><span class="dashicons dashicons-external"></span><?php _e( 'Sure! I\'d love to!', 'loginpress' ) ?></a></li>
          <li><a href="<?php echo $dismiss_url ?>"><span class="dashicons dashicons-smiley"></span><?php _e( 'I\'ve already left a review', 'loginpress' ) ?></a></li>
          <li><a href="<?php echo $later_url ?>"><span class="dashicons dashicons-calendar-alt"></span><?php _e( 'Maybe Later', 'loginpress' ) ?></a></li>
          <li><a href="<?php echo $dismiss_url ?>"><span class="dashicons dashicons-dismiss"></span><?php _e( 'Never show again', 'loginpress' ) ?></a></li></ul>
			</div>
		</div>
	<?php
	}

  /**
   * Review notice message
   *
   * @since  1.1.3
   */
  public function loginpress_addon_notice_text() {

    $scheme      = ( parse_url( $_SERVER['REQUEST_URI'], PHP_URL_QUERY ) ) ? '&' : '?';
    $url         = $_SERVER['REQUEST_URI'] . $scheme . 'loginpress_addon_dismiss_1=yes';
    $dismiss_url = wp_nonce_url( $url, 'loginpress-addon-nonce' );
    wp_enqueue_style( 'loginpress_review_stlye', plugins_url( 'css/style-review.css', __FILE__ ), array(), LOGINPRESS_VERSION );
    ?>
    <div class="loginpress-alert-notice">
      <a href="<?php echo $dismiss_url ?>" class="notice-dismiss" ><span class="screen-reader-text"></span></a>
      <a href="https://wpbrigade.com/wordpress/plugins/loginpress/addons/?utm_source=loginpress-lite&utm_medium=addons-notice-banner&utm_campaign=pro-upgrade" class="loginpress-addon-notice-link" target="_blank">
      <div class="loginpress-alert-thumbnail">
        <img src="<?php echo plugins_url( 'img/notification_logo.svg', __FILE__ ) ?>" alt="">
      </div>
      <div class="loginpress-alert-text">
        <h3><?php _e( 'Introducing LoginPress Addons!', 'loginpress' ) ?></h3>
        <p><?php _e( 'Extend LoginPress with these add-ons and supercharge your login pages.', 'loginpress' ) ?></p>
      </div>
      </a>
      <div class="loginpress-alert-button-section">
        <a href="https://wpbrigade.com/wordpress/plugins/loginpress/addons/?utm_source=loginpress-lite&utm_medium=addons-notice-more&utm_campaign=pro-upgrade" class="loginpress-alert-button" target="_blank"><?php _e( 'Learn More', 'loginpress' ) ?></a>
      </div>
    </div>
  <?php
  }

  /**
	 *	Check and Dismiss addon message.
	 *
	 *	@since 1.1.3
	 */
	private function loginpress_addon_dismissal() {

		if ( ! is_admin() ||
			! current_user_can( 'manage_options' ) ||
			! isset( $_GET['_wpnonce'] ) ||
			! wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ), 'loginpress-addon-nonce' ) ||
			! isset( $_GET['loginpress_addon_dismiss_1'] ) ) :

			return;
		endif;

		add_site_option( 'loginpress_addon_dismiss_1', 'yes' );
	}

  /**
	 * Pull the LoginPress page from options.
	 *
	 * @access public
	 * @since 1.1.3
	 * @version 1.1.7
	 */
	public function get_loginpress_page() {

		$loginpress_setting = get_option( 'loginpress_setting', array() );
    if ( ! is_array( $loginpress_setting ) && empty( $loginpress_setting ) ) {
      $loginpress_setting = array();
    }
		$page = array_key_exists( 'loginpress_page', $loginpress_setting ) ? get_post( $loginpress_setting['loginpress_page'] ) : false;

		return $page;
	}

  /**
	 * Add a link to the settings page to the plugins list
	 *
	 * @since  1.0.11
	 */
	public function loginpress_action_links( $links, $file ) {

		static $this_plugin;

		if ( empty( $this_plugin ) ) {

			$this_plugin = 'loginpress/loginpress.php';
		}

		if ( $file == $this_plugin ) {

			$settings_link = sprintf( esc_html__( '%1$s Settings %2$s | %3$s Customize %4$s', 'loginpress' ), '<a href="' . admin_url( 'admin.php?page=loginpress-settings' ) . '">', '</a>', '<a href="' . admin_url( 'admin.php?page=loginpress' ) . '">', '</a>' );


      if( 'yes' == get_option( '_loginpress_optin' ) ){
        $settings_link .= sprintf( esc_html__( ' | %1$s Opt Out %2$s ', 'loginpress'), '<a class="opt-out" href="' . admin_url( 'admin.php?page=loginpress-settings' ) . '">', '</a>' );
      } else {
        $settings_link .= sprintf( esc_html__( ' | %1$s Opt In %2$s ', 'loginpress'), '<a href="' . admin_url( 'admin.php?page=loginpress-optin&redirect-page=' .'loginpress-settings' ) . '">', '</a>' );
      }

      array_unshift( $links, $settings_link );

      if ( ! has_action( 'loginpress_pro_add_template' ) ) :
        $pro_link = sprintf( esc_html__( '%1$s %3$s Upgrade Pro %4$s %2$s', 'loginpress' ),  '<a href="https://wpbrigade.com/wordpress/plugins/loginpress-pro/?utm_source=loginpress-lite&utm_medium=plugin-action-link&utm_campaign=pro-upgrade" target="_blank">', '</a>', '<span class="loginpress-dasboard-pro-link">', '</span>' );
        array_push( $links, $pro_link );
      endif;
		}

		return $links;
	}

  // function get_addon_info_( $api, $action, $args ) {

  //   if ( $action == 'plugin_information' && empty( $api ) && ( ! empty( $_GET['lgp'] )  ) ) {

  //     $raw_response = wp_remote_post( 'https://wpbrigade.com/loginpress-api/search.php', array(
  //       'body' => array(
  //         'slug' => $args->slug
  //       ) )
  //      );

  //      if ( is_wp_error( $raw_response ) || $raw_response['response']['code'] != 200 ) {
  //        return false;
  //      }

  // 		$plugin = unserialize( $raw_response['body'] );

  //     $api                = new stdClass();
  //     $api->name          = $plugin['title'];
  //     $api->version       = $plugin['version'];
  //     $api->download_link = $plugin['download_url'];
  //     $api->tested        = '10.0';

  //   }

  //   return $api;
  // }
} // End Of Class.
endif;


/**
* Returns the main instance of WP to prevent the need to use globals.
*
* @since  1.0.0
* @return LoginPress
*/
function loginPress_loader() {
  return LoginPress::instance();
}

// Call the function
loginPress_loader();

/**
* Create the Object of Custom Login Entites and Settings.
*
* @since  1.0.0
*/
new LoginPress_Entities();
new LoginPress_Settings();

/**
* Create the Object of Remote Notification.
*
* @since  1.0.9
*/
if (!class_exists('TAV_Remote_Notification_Client')) {
  require( LOGINPRESS_ROOT_PATH . 'include/class-remote-notification-client.php' );
}
$notification = new TAV_Remote_Notification_Client( 125, '16765c0902705d62', 'https://wpbrigade.com?post_type=notification' );

register_activation_hook( __FILE__, array( 'LoginPress', 'plugin_activation' ) );
register_uninstall_hook( __FILE__, array( 'LoginPress', 'plugin_uninstallation' ) );
?>
