<?php

namespace AlpineMountainRanch;
use \DustySun\WP_Settings_API\v2 as DSWPSettingsAPI;

class AMR_Options_Settings {

	private $amrsettings = array();

	// Create the object
	public function __construct() {

		// create the various menu pages 
		add_action( 'admin_menu', array($this, 'amr_create_admin_page'));

		// Register the menu
		add_action( 'admin_menu', array($this, 'amr_admin_menu' ));	

	} // end public function __construct()

	public function amr_create_admin_page() {
		// set the settings api options
		$ds_api_settings = array(
			'json_file' => plugin_dir_path( __FILE__ ) . '/alpinemountainranch.json',
			'register_settings' => true,
			'views_dir' => plugin_dir_path( __FILE__ ) . '/admin/views'
		);
		// Create the settings object
		$this->alpinemountainranch_settings_page = new DSWPSettingsAPI\SettingsBuilder($ds_api_settings);

		// Create the customizer
 		// Get the current settings
		$this->alpinemountainranch_settings = $this->alpinemountainranch_settings_page->get_current_settings();

		// Get the plugin options
		$this->alpinemountainranch_main_settings = $this->alpinemountainranch_settings_page->get_main_settings();
	} // end function amr_create_admin_page

	// Adds admin menu under the Sections section in the Dashboard
	public function amr_admin_menu() {

		// $this->alpinemountainranch_plugin_hook = add_menu_page(
		// 		__('SLP H5P Mods', 'thealpinemountainranch'),
		// 		__('SLP H5P Mods', 'thealpinemountainranch'),
		// 		'manage_options',
		// 		'amr',
		// 		array($this, 'amr_menu_options'),
		// 		'dashicons-thumbs-up'
		// 	);

		$this->alpinemountainranch_plugin_hook = add_submenu_page(
			'options-general.php',
			__('AMR Options', 'alpinemountainranch'),
			__('AMR Options', 'alpinemountainranch'),
			'manage_options',
			'alpinemountainranch',
			array($this, 'amr_menu_options')
		);

	} // end public function amr_admin_menu()

	public function amr_admin_scripts( $hook ) {

		// if($hook == $this->alpinemountainranch_plugin_hook) {
		// 	wp_enqueue_style('amr-admin', plugins_url('/css/alpinemountainranch-admin.css', __FILE__));

		// }

	} // end public function amr_admin_scripts


	// Create the actual options page
	public function amr_menu_options() {
		$amrsettings_title = $this->alpinemountainranch_main_settings['name'];

		// Create the main page HTML
		$this->alpinemountainranch_settings_page->build_settings_panel($amrsettings_title);
	} // end function


	
   
} // end class AMR_Options_Settings
if( is_admin() )
    $amr_options_settings_page = new AMR_Options_Settings();
