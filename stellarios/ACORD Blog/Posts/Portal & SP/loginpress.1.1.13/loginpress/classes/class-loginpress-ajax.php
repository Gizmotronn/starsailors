<?php
if ( ! defined( 'ABSPATH' ) ) {
  // Exit if accessed directly.
  exit;
}

/**
* Handling all the AJAX calls in LoginPress.
*
* @since 1.0.19
* @class LoginPress_AJAX
*/

if ( ! class_exists( 'LoginPress_AJAX' ) ) :

  class LoginPress_AJAX {

    /* * * * * * * * * *
    * Class constructor
    * * * * * * * * * */
    public function __construct() {

      $this::init();
    }
    public static function init() {

      $ajax_calls = array(
        'export'     => false,
        'import'     => false,
        'help'       => false,
        'deactivate' => false,
        'optout_yes' => false,
      );

      foreach ( $ajax_calls as $ajax_call => $no_priv ) {
        // code...
        add_action( 'wp_ajax_loginpress_' . $ajax_call, array( __CLASS__, $ajax_call ) );

        if ( $no_priv ) {
          add_action( 'wp_ajax_nopriv_loginpress_' . $ajax_call, array( __CLASS__, $ajax_call ) );
        }
      }
    }

    /**
    * [Import LoginPress Settings]
    * @return [array] [update settings meta]
    * @since 1.0.19
    */
    public function import() {

      $lg_imp_tmp_name =  $_FILES['file']['tmp_name'];
      $lg_file_content = file_get_contents( $lg_imp_tmp_name );
      $loginpress_json = json_decode( $lg_file_content, true );

      if ( json_last_error() == JSON_ERROR_NONE ) {

        foreach ( $loginpress_json as $object => $array ) {

          // Check for LoginPress customizer images.
          if ( 'loginpress_customization' == $object ) {

            update_option( $object, $array );

            foreach ( $array as $key => $value ) {

              // Array of loginpress customizer images.
              $imagesCheck = array( 'setting_logo', 'setting_background', 'setting_form_background', 'forget_form_background' );

              /**
              * [if json fetched data has array of $imagesCheck]
              * @var [array]
              */
              if ( in_array( $key, $imagesCheck ) ) {

                global $wpdb;
                // Count the $value of that $key from {$wpdb->posts}.
                $query = "SELECT COUNT(*) FROM {$wpdb->posts} WHERE guid='$value'";
                $count = $wpdb->get_var($query);

                if ( $count < 1 && ! empty( $value ) ) {
                  $file = array();
                  $file['name'] = basename( $value );
                  $file['tmp_name'] = download_url( $value ); // Downloads a url to a local temporary file.

                  if ( is_wp_error( $file['tmp_name'] ) ) {
                    @unlink( $file['tmp_name'] );
                    // return new WP_Error( 'lpimgurl', 'Could not download image from remote source' );
                  } else {
                    $id  = media_handle_sideload( $file, 0 ); // Handles a sideloaded file.
                    $src = wp_get_attachment_url( $id ); // Returns a full URI for an attachment file.
                    $loginpress_options = get_option( 'loginpress_customization' ); // Get option that was updated previously.

                    // Change the options array properly.
                    $loginpress_options["$key"] = $src;

                    // Update entire array again for save the attachment w.r.t $key.
                    update_option( $object, $loginpress_options );
                  }
                } // media_upload.
              } // images chaeck.
            } // inner foreach.
          } // loginpress_customization check.

          if ( 'loginpress_setting' == $object ) {

            $loginpress_options = get_option( 'loginpress_setting' );
            // Check $loginpress_options is exists.
            if ( isset( $loginpress_options ) && ! empty( $loginpress_options ) ) {

              foreach ( $array as $key => $value ) {

                // Array of loginpress Settings that import.
                $setting_array = array( 'session_expiration', 'login_with_email' );

                if ( in_array( $key, $setting_array ) ) {

                  // Change the options array properly.
                  $loginpress_options["$key"] = $value;
                  // Update array w.r.t $key exists.
                  update_option( $object, $loginpress_options );
                }
              } // inner foreach.
            } else {

              update_option( $object, $array );
            }
          } // loginpress_setting check.
        } // endforeach.
      } else {
        echo "error";
      }
      wp_die();
    }

    /**
    * [Export LoginPress Settings]
    * @return [string] [return settings in json formate]
    * @since 1.0.19
    */
    public function export(){

      $loginpress_db            = array();
      $loginpress_setting_opt   = array();
      $loginpress_customization = get_option( 'loginpress_customization' );
      $loginpress_setting       = get_option( 'loginpress_setting' );
      $loginpress_setting_fetch = array( 'session_expiration', 'login_with_email' );

      if ( $loginpress_customization ) {

        $loginpress_db['loginpress_customization'] = $loginpress_customization;
      }
      if ( $loginpress_setting ) {

        foreach ( $loginpress_setting as $key => $value) {
          if ( in_array( $key, $loginpress_setting_fetch ) ) {
            $loginpress_setting_opt[$key] = $value;
          }
        }
        $loginpress_db['loginpress_setting'] = $loginpress_setting_opt;
      }
      $loginpress_db = json_encode( $loginpress_db );

      echo $loginpress_db;

      wp_die();
    }

    /**
    * [Download file from help information tab]
    * @return [string] [description]
    * @since 1.0.19
    */
    public function help() {

      include LOGINPRESS_DIR_PATH . 'classes/class-loginpress-log.php';

      echo LoginPress_Log_Info::get_sysinfo();

      wp_die();
    }

    /**
     * [deactivate get response from user on deactivating plugin]
     * @return [string] [response]
     * @since   1.0.15
     * @version 1.0.23
     */
    public function deactivate() {

      $email         = get_option( 'admin_email' );
      $_reason       = sanitize_text_field( wp_unslash( $_POST['reason'] ) );
      $reason_detail = sanitize_text_field( wp_unslash( $_POST['reason_detail'] ) );
      $reason        = '';

      if ( $_reason == '1' ) {
        $reason = 'I only needed the plugin for a short period';
      } elseif ( $_reason == '2' ) {
        $reason = 'I found a better plugin';
      } elseif ( $_reason == '3' ) {
        $reason = 'The plugin broke my site';
      } elseif ( $_reason == '4' ) {
        $reason = 'The plugin suddenly stopped working';
      } elseif ( $_reason == '5' ) {
        $reason = 'I no longer need the plugin';
      } elseif ( $_reason == '6' ) {
        $reason = 'It\'s a temporary deactivation. I\'m just debugging an issue.';
      } elseif ( $_reason == '7' ) {
        $reason = 'Other';
      }
      $fields = array(
        'email' 		        => $email,
        'website' 			    => get_site_url(),
        'action'            => 'Deactivate',
        'reason'            => $reason,
        'reason_detail'     => $reason_detail,
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

      wp_die();
    }

    /**
     * Opt-out
     * @since  1.0.15
     */
    function optout_yes() {
      update_option( '_loginpress_optin', 'no' );
      wp_die();
    }
  }

endif;
new LoginPress_AJAX();
?>
