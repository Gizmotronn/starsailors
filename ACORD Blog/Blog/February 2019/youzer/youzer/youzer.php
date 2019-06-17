<?php
/**
 * Plugin Name: Youzer
 * Plugin URI:  http://youzer.kainelabs.com
 * Description: Youzer is a Community & User Profiles Management Solution with a Secure Membership System, Front-end Account Settings, Powerful Admin Panel, 14 Header Styles, +20 Profile Widgets, 16 Color Schemes, Advanced Author Widgets, Fully Responsive Design, Extremely Customizable and a Bunch of Unlimited Features Provided By KaineLabs.
 * Version:     2.1.5
 * Author:      Youssef Kaine
 * Author URI:  http://www.kainelabs.com
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: youzer
 * Domain Path: /languages/
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Check For Required Plugins.
 */
function yz_have_required_plugins() {

    $required_plugins = array( 'buddypress' => 'bp-loader' );

    // Get Active Plugins List.
    $active_plugins = (array) get_option( 'active_plugins', array() );

    // Is Multisite ??
    if ( is_multisite() ) {
        // Get Site Wide Plugins.
        $sitewide_plugins = get_site_option( 'active_sitewide_plugins', array() );
        // Merge Plugins.
        $active_plugins = array_merge( $active_plugins, $sitewide_plugins );
    }

    foreach ( $required_plugins as $key => $required ) {

        // Get Plugin Path.
        $required = ! is_numeric( $key ) ? "{$key}/{$required}.php" : "{$required}/{$required}.php";

        if (
            ! in_array( $required, $active_plugins )
            &&
            ! array_key_exists( $required, $active_plugins )
        )
            return false;
    }
    return true;
}

if ( ! yz_have_required_plugins() ) {
    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    deactivate_plugins( plugin_basename( __FILE__ ) );
    wp_die( __( 'Please install and activate <strong><a href="https://wordpress.org/plugins/buddypress/">Buddypress</strong></a> plugin to use the <strong>Youzer</strong> plugin .', 'youzer' ), 'Youzer dependency check', array( 'back_link' => true ) );
    return;
}

define( 'YOUZER_FILE', __FILE__ );

require dirname( __FILE__ ) . '/class-youzer.php';

/**
 * The main function responsible for returning the one true BuddyPress Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $bp = buddypress(); ?>
 *
 * @return BuddyPress|null The one true BuddyPress Instance.
 */
function youzer() {
    return Youzer::instance();
}

/*
 * Hook BuddyPress early onto the 'plugins_loaded' action.
 *
 * This gives all other plugins the chance to load before Youzer,
 * to get their actions, filters, and overrides setup without
 * Youzer being in the way.
 */
if ( defined( 'YOUZER_LATE_LOAD' ) ) {
    add_action( 'plugins_loaded', 'youzer', (int) YOUZER_LATE_LOAD );
} else {

    // Add Legacy Theme Support.
    add_theme_support( 'buddypress-use-legacy' );
    
    // Set Globals.    
    $GLOBALS['Youzer'] = youzer();

}