<?php 
// Define the internationalization functionality
class Mini_Newsletter_i18n {
	// Load the plugin text domain for translation.
	public function load_plugin_textdomain () {
		load_plugin_textdomain(
			'mini-newsletter', 
			false, 
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages' 
		);	
	}	
} 