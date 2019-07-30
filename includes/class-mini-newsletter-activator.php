<?php 
/*
Fired during plugin activation
This class defines all code necessary to run during the plugin's activation.
*/

class Mini_Newsletter_Activator {
		public static function activate () {
		// create a table to store the email addres
		global $wpdb;
		$table_prefix 		= $wpdb->prefix . 'mini_nletter_email';		
		$charset_collate 	= $wpdb->get_charset_collate();

		$sql = " CREATE TABLE $table_prefix (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			email_address VARCHAR(50) NOT NULL, 
			added_time DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL, 
			PRIMARY KEY  (id)
		) $charset_collate ";
		require_once ( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );		
	}
}