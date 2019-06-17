<?php

class Youzer_Admin {

	function __construct() {

		// Init Admin Area
		$this->youzer_admin_init();

		// Add Youzer Plugin Admin Pages.
		add_action( 'admin_menu', array( &$this, 'admin_init' ) );

		// Load Admin Scripts & Styles .
		add_action( 'admin_print_styles', array( &$this, 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( &$this, 'admin_scripts' ) );

		// Youzer Admin Pages
	    $this->admin_pages = array(
	    	'youzer-panel', 'yz-profile-settings', 'yz-widgets-settings', 'yz-membership-settings'
	    );

        // Add Plugin Links.
        add_filter(
            'plugin_action_links_' . YOUZER_BASENAME,
            array( $this, 'plugin_action_links' )
        );

        // Add Plugin Links in Multisite..
        add_filter(
            'network_admin_plugin_action_links_' . YOUZER_BASENAME,
            array( $this, 'plugin_action_links' )
        );

	}

    /**
     * Youzer Action Links
     */
    function plugin_action_links( $links ) {
        // Get Youzer Plugin Pages. 
        $panel_url = esc_url( add_query_arg( array( 'page' => 'youzer-panel' ), admin_url( 'admin.php' ) ) );
        $plugin_url = "https://codecanyon.net/item/youzer-new-wordpress-user-profiles-era/19716647";
        
        // Add a few links to the existing links array.
        return array_merge( $links, array(
            'settings' => '<a href="' . $panel_url . '">' . esc_html__( 'Settings', 'youzer' ) . '</a>',
            'about'    => '<a href="' . $plugin_url . '">' . esc_html__( 'About',    'youzer' ) . '</a>'
        ) );

    }

	/**
	 * # Initialize Youzer Admin Panel
	 */
	function youzer_admin_init() {

		// Init Admin Files.
		require_once YZ_ADMIN_CORE . 'yz-admin-dashboard.php';
		require_once YZ_ADMIN_CORE . 'yz-admin-panel.php';
		require_once YZ_ADMIN_CORE . 'yz-admin-ajax.php';
		require_once YZ_ADMIN_CORE . 'yz-extensions.php';
		require_once YZ_ADMIN_CORE . 'yz-update-notifier.php';

		// General Settings .
		require_once YZ_ADMIN_CORE . 'general-settings/yz-settings-wall.php';
		require_once YZ_ADMIN_CORE . 'general-settings/yz-settings-panel.php';
		require_once YZ_ADMIN_CORE . 'general-settings/yz-settings-emoji.php';
		require_once YZ_ADMIN_CORE . 'general-settings/yz-settings-author.php';
		require_once YZ_ADMIN_CORE . 'general-settings/yz-settings-groups.php';
		require_once YZ_ADMIN_CORE . 'general-settings/yz-settings-schemes.php';
		require_once YZ_ADMIN_CORE . 'general-settings/yz-settings-widgets.php';
		require_once YZ_ADMIN_CORE . 'general-settings/yz-settings-general.php';
		require_once YZ_ADMIN_CORE . 'general-settings/yz-settings-social-networks.php';
		require_once YZ_ADMIN_CORE . 'general-settings/yz-settings-custom-styling.php';
		require_once YZ_ADMIN_CORE . 'general-settings/yz-settings-groups-directory.php';
		require_once YZ_ADMIN_CORE . 'general-settings/yz-settings-members-directory.php';
		require_once YZ_ADMIN_CORE . 'general-settings/yz-settings-account-verification.php';
		require_once YZ_ADMIN_CORE . 'general-settings/yz-settings-bookmarks.php';
        
        if ( yz_is_mycred_installed() ) {
			require_once YZ_ADMIN_CORE . 'general-settings/yz-settings-mycred.php';
		}

        if ( yz_is_bbpress_installed() ) {
			require_once YZ_ADMIN_CORE . 'general-settings/yz-settings-bbpress.php';
		}

		// Profile Settings .
		require_once YZ_ADMIN_CORE . 'profile-settings/yz-settings-ads.php';
		require_once YZ_ADMIN_CORE . 'profile-settings/yz-settings-tabs.php';
		require_once YZ_ADMIN_CORE . 'profile-settings/yz-settings-posts.php';
		require_once YZ_ADMIN_CORE . 'profile-settings/yz-settings-header.php';
		require_once YZ_ADMIN_CORE . 'profile-settings/yz-settings-navbar.php';
		require_once YZ_ADMIN_CORE . 'profile-settings/yz-settings-general.php';
		require_once YZ_ADMIN_CORE . 'profile-settings/yz-settings-comments.php';
		require_once YZ_ADMIN_CORE . 'profile-settings/yz-settings-structure.php';
		require_once YZ_ADMIN_CORE . 'profile-settings/yz-settings-profile-404.php';

		// init Administration
		$this->panel 	  = new Youzer_Panel();
		$this->dashboard  = new Youzer_Dashboard();
		$this->ajax 	  = new Youzer_Admin_Ajax();
		$this->extentions = new Youzer_Extensions();
	}

	/**
	 * # Add Youzer Admin Pages .
	 */
	function admin_init() {

		// Show Youzer Panel to Admin's Only.
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

	    // Add Youzer Plugin Admin Page.
	    add_menu_page(
	    	__( 'Youzer Panel', 'youzer' ),
	    	__( 'Youzer Panel', 'youzer' ),
	    	'administrator',
	    	'youzer-panel',
	    	array( &$this->dashboard, 'general_settings' ),
	    	YZ_AA . 'images/icon.png'
	    );

		// Add "General Settings" Page .
	    add_submenu_page(
	    	'youzer-panel',
	    	__( 'Youzer - General Settings', 'youzer' ),
	    	__( 'General Settings', 'youzer' ),
	    	'administrator',
	    	'youzer-panel',
	    	array( &$this->dashboard, 'general_settings' ),
	    	false
	    );

	    // Add "Profile Settings" Page .
	    add_submenu_page(
	    	'youzer-panel',
	    	__( 'Youzer - Profile Settings', 'youzer' ),
	    	__( 'Profile Settings', 'youzer' ),
	    	'administrator',
	    	'yz-profile-settings',
	    	array( &$this->dashboard, 'profile_settings' ),
	    	false
	    );

	    // Add "Widgets Settings" Page .
	    add_submenu_page(
	    	'youzer-panel',
	    	__( 'Youzer - Widgets Settings', 'youzer' ),
	    	__( 'Widgets Settings', 'youzer' ),
	    	'administrator',
	    	'yz-widgets-settings',
	    	array( &$this->dashboard, 'widgets_settings' ),
	    	false
	    );

	}

	/**
	 * # Admin Scripts.
	 */
	function admin_scripts() {

		if ( ! isset( $_GET['page'] ) ) {
			return false;
		}

		global $Youzer;
		
		// Set Up Variables
		$jquery = array( 'jquery' );

	    if ( in_array( $_GET['page'], $this->admin_pages ) ) {

	        global $Yz_Translation;

	        // Load Panel JS
	        wp_enqueue_script( 'yz-panel' );
	        wp_enqueue_script( 'ukai-panel' );
	        wp_localize_script( 'yz-panel', 'yz', $Yz_Translation );

	        // Load Color Picker
	        wp_enqueue_script( 'wp-color-picker' );
    		wp_enqueue_style( 'wp-color-picker' );

	    }

	    // Functions script Tabs
	    $functions_script = array( 'custom-widgets', 'user-tags-widget', 'reaction-settings' );

	    if ( 
	    	'youzer-panel' == $_GET['page'] || 'yz-profile-settings' == $_GET['page']
	    	||
	    	( isset( $_GET['tab'] ) && in_array( $_GET['tab'], $functions_script ) )
	    ) {
		    // Admin Panel Script
		    wp_enqueue_script(
		    	'yz-functions',
		    	YZ_AA . 'js/yz-functions.min.js',
		    	array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-draggable', 'yz-iconpicker' ),
		    	$Youzer->version, true
		    );
	    }

	    if ( 'yz-profile-settings' == $_GET['page'] ) {
	    	// Profile Structure
	        wp_enqueue_script( 'yz-profile-structure' );
	    }

	    if ( isset( $_GET['tab'] ) && 'social-networks' == $_GET['tab'] ) {
		    // Load Social Networks Builder .
		    wp_enqueue_script( 'yz-networks', YZ_AA . 'js/yz-networks.min.js', $jquery, $Youzer->version, true );
	    }

        // Load Tags Script
        wp_enqueue_script( 'yz-ukaitags' );

	    if ( in_array( $_GET['page'] , $this->admin_pages) )  {
	        // Uploader Scripts
	        wp_enqueue_media();
	    }

		// Load Ads Builder.
	    if ( isset( $_GET['tab'] ) && 'ads' == $_GET['tab'] ) {
		    wp_enqueue_script( 'yz-ads', YZ_AA . 'js/yz-ads.min.js', $jquery, $Youzer->version, true );
	    }
	    
	    if ( isset( $_GET['tab'] ) && 'custom-widgets' == $_GET['tab'] ) {
	    	// Load Profile Widgets Script.
		    wp_enqueue_script( 'yz-profile-widgets', YZ_AA . 'js/yz-profile-widgets.min.js', $jquery, $Youzer->version, true );
	    }

	    if ( isset( $_GET['tab'] ) && 'custom-tabs' == $_GET['tab'] ) {
	    	// Load Custom Tabs Script.
		    wp_enqueue_script( 'yz-custom-tabs', YZ_AA . 'js/yz-custom-tabs.min.js', $jquery, $Youzer->version, true );
	    }

	    if ( isset( $_GET['tab'] ) && 'user-tags-widget' == $_GET['tab'] ) {
	    	// Load User Tags Script.
		    wp_enqueue_script( 'yz-user-tags', YZ_AA . 'js/yz-user-tags.min.js', $jquery, $Youzer->version, true );
	    }

	}

	/**
	 * # Panel Styles.
	 */
	function admin_styles() {

		if ( ! isset( $_GET['page'] ) ) {
			return false;
		}

		global $Youzer;

		// Load Admin Panel Styles
	    if ( in_array( $_GET['page'], $this->admin_pages ) ) {
	    	// Load Settings Style
		    wp_enqueue_style( 'yz-panel-css', YZ_AA . 'css/yz-panel-css.min.css', $Youzer->version );
	        // Load Admin Panel Style
		    wp_enqueue_style( 'yz-admin-style', YZ_AA . 'css/yz-admin-style.min.css', $Youzer->version );
	        // Load Google Fonts
	        wp_enqueue_style( 'yz-fonts', 'https://fonts.googleapis.com/css?family=Open+Sans:100,400,600' , $Youzer->version);
	    	// Loading Font Awesome.
		    wp_enqueue_style( 'yz-icons' );
		    wp_enqueue_style( 'yz-iconpicker' );
	    }

	}

}