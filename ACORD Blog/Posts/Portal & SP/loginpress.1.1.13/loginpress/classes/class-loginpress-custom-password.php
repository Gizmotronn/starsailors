<?php
/**
* LoginPresss_Custom_Password
*
* Description: Enable Custom Password for Register User.
*
* @package LoginPress
* @since 1.0.22
*/

if ( ! class_exists( 'LoginPresss_Custom_Password' ) ) :
  /**
  * LoginPress Custom Passwords class
  *
  * @since 1.0.22
  */
  class LoginPresss_Custom_Password {

    /* * * * * * * * * *
    * Class constructor
    * * * * * * * * * */
    public function __construct() {

      $this->_hooks();
    }

    public function _hooks() {

      add_action( 'register_form',             array( $this, 'loginpress_reg_password_fields' ) );
      add_filter( 'registration_errors',       array( $this, 'loginpress_reg_pass_errors' ), 10, 3 );
      add_filter( 'random_password',           array( $this, 'loginpress_set_password' ) );
      add_action( 'register_new_user',         array( $this, 'update_default_password_nag' ) );
    }

    /**
     * Custom Password Fields on Registration Form.
     *
     * @since   1.0.22
     * @access  public
     * @return  string html.
     */
    public function loginpress_reg_password_fields() {
      ?>
      <p class="loginpress-reg-pass-wrap">
        <label for="loginpress-reg-pass"><?php _e( 'Password', 'loginpress' ); ?></label>
        <input autocomplete="off" name="loginpress-reg-pass" id="loginpress-reg-pass" class="input" size="20" value="" type="password" />
      </p>
      <p class="loginpress-reg-pass-2-wrap">
        <label for="loginpress-reg-pass-2"><?php _e( 'Confirm Password', 'loginpress' ); ?></label>
        <input autocomplete="off" name="loginpress-reg-pass-2" id="loginpress-reg-pass-2" class="input" size="20" value="" type="password" />
      </p>
      <?php
    }

    /**
    * Handles password field errors for registration form.
    *
    * @since 1.0.22
    * @access public
    *
    * @param Object $errors WP_Error
    * @param Object $sanitized_user_login user login.
    * @param Object $user_email user email.
    * @return WP_Error object.
    */
    public function loginpress_reg_pass_errors( $errors, $sanitized_user_login, $user_email ) {

      // Ensure passwords aren't empty.
      if ( empty( $_POST['loginpress-reg-pass'] ) || empty( $_POST['loginpress-reg-pass-2'] ) ) {
        $errors->add( 'empty_password', __( '<strong>ERROR</strong>: Please enter your password twice.', 'loginpress' ) );

      // Ensure passwords are matched.
      } elseif ( $_POST['loginpress-reg-pass'] != $_POST['loginpress-reg-pass-2'] ) {
        $errors->add( 'password_mismatch', __( '<strong>ERROR</strong>: Please enter the same password in the end password fields.', 'loginpress' ) );

      // Password Set? assign password to a user_pass
      } else {
        $_POST['user_pass'] = $_POST['loginpress-reg-pass'];
      }

      return $errors;
    }

    /**
    * Let's set the user password.
    *
    * @since 1.0.22
    * @access public
    * @param string $password Auto-generated password passed in from filter.
    * @return string Password Choose by User.
    */
    public function loginpress_set_password( $password ) {

      // Make sure password field isn't empty.
      if ( ! empty( $_POST['user_pass'] ) ) {
        $password = $_POST['user_pass'];
      }

      return $password;
    }

    /**
    * Sets the value of default password nag.
    *
    * @since 1.0.22
    * @access public
    * @param int $user_id.
    */
    public function update_default_password_nag( $user_id ) {

      // False => User not using WordPress default password.
      update_user_meta( $user_id, 'default_password_nag', false );
    }

  } // End Of Class.

endif;
