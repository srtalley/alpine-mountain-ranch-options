<?php
/*
Plugin Name: Alpine Mountain Ranch Options
Description: Modifications specific to AMR
Author: Dusty Sun
Author URI: https://dustysun.com
Version: 1.4.5a
Text Domain: alpinemountainranch
License: GPLv2
*/


namespace AlpineMountainRanch;
use \DustySun\WP_Settings_API\v2 as DSWPSettingsAPI;

define( 'AMR__FILE__', __FILE__ );

//Include the admin panel page
require_once( dirname( __FILE__ ) . '/alpinemountainranch-admin.php');

require_once( dirname( __FILE__ ) . '/lib/dustysun-wp-settings-api/ds_wp_settings_api.php');
require_once( dirname( __FILE__ ) . '/classes/amr-woocommerce-appointments.php');

require_once( dirname( __FILE__ ) . '/classes/amr-eventsmanager.php');
require_once( dirname( __FILE__ ) . '/classes/amr-owner-portal.php');

// require_once( dirname( __FILE__ ) . '/classes/amr-pinpoint-booking.php');

// require_once( dirname( __FILE__ ) . '/classes/amr-lightbox.php');


class AMR_Options {

  private $amrjson_file;
  private $amrsettings_obj;
  public $current_settings;
  public $amrmain_settings;

  public function __construct() {
    // Template Path
    define( 'AMR_TEMPLATE_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/templates/' );


    add_action( 'wp_enqueue_scripts', array( $this, 'amr_register_styles_scripts' ), 1000 );
    
    // add_filter('get_alpinemountainranch_main_settings', array($this, 'get_alpinemountainranch_main_settings'));
    // add_filter('get_alpinemountainranch_current_settings', array($this, 'get_alpinemountainranch_current_settings'));

    
  } // end public function __construct

  public function amr_create_settings() {

    // set the settings api options
		$ds_api_settings = array(
      'json_file' => plugin_dir_path( __FILE__ ) . '/alpinemountainranch.json'
    );
    
    $this->alpinemountainranch_settings_obj = new DSWPSettingsAPI\SettingsBuilder($ds_api_settings);

    // get the settings
    $this->current_settings = $this->alpinemountainranch_settings_obj->get_current_settings();
    // $this->wl($this->current_settings);

    // Get the plugin options
    $this->alpinemountainranch_main_settings = $this->alpinemountainranch_settings_obj->get_main_settings();
    

  } // end function amr_create_settings

  /**
   * Function to return the main settings object
   */
  public function get_alpinemountainranch_main_settings() {
    return $this->alpinemountainranch_main_settings;
  }
  /**
   * Function to return the main settings object
   */
  public function get_alpinemountainranch_current_settings() {
    return $this->current_settings;
  }


  public function amr_register_styles_scripts() {
    if(is_user_logged_in()) {
      // wp_enqueue_script('dslp-h5p-mods-lightbox', plugins_url('js/dslp-h5p-mods-lightbox.js', __FILE__), array('jquery'), $this->alpinemountainranch_main_settings['version'], true);

    
    }
    $plugin_data = get_plugin_data( __FILE__ );
    wp_enqueue_style( 'amr-main', plugins_url('css/main.css', __FILE__), array(), $plugin_data['Version']);

    wp_enqueue_script('amr-main', plugins_url('js/main.js', __FILE__), array('jquery'), $plugin_data['Version'], true);

    wp_enqueue_script('amr-woocommerce-appointments', plugins_url('js/amr-woocommerce-appointments.js', __FILE__), array('jquery'), $plugin_data['Version'], true);


    // wp_enqueue_script('amr-lookup', plugins_url('js/alpinemountainranch-lookup.js', __FILE__), array('jquery'), $plugin_data['Version'], true);
  
    // wp_localize_script( 'amr-lookup', 'amr_lookup', array(
    //   'ajaxurl'   => admin_url( 'admin-ajax.php' ),
    //   'ajaxnonce' => wp_create_nonce( 'amr_lookup' )
    // ) );

  }
    // Logging function 
    public function wl ( $log )  {
      if ( true === WP_DEBUG ) {
          if ( is_array( $log ) || is_object( $log ) ) {
              error_log( print_r( $log, true ) );
          } else {
              error_log( $log );
          }
      }
    } // end public function wl 
} // end class AMR_Options

$alpinemountainranch = new AMR_Options();
