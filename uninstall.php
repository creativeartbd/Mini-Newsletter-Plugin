<?php
// If uninstall not called from WordPress, then exit.
if( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit();

// delete all options for the newsletter settings
delete_option( 'mn_title' );
delete_option( 'mn_placeholder' );
delete_option( 'mn_incorrect' );
delete_option( 'mn_already_exist' );
delete_option( 'mn_register_success' );

// delete the list of email from the database
global $wpdb;
$table_prefix 	=	$wpdb->prefix;
$table_name 	=	$table_prefix . 'mini_nletter_email';
$delete_sql 	=	"DROP TABLE IF EXISTS $table_name";
$wpdb->query($delete_sql);