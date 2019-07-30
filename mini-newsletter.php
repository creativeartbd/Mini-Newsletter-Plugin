<?php
/*
Plugin Name: Mini Newsletter
Plugin URI:  http://creativeartbd.com/plugins/
Description: Mini Newsletter, where you can send custom email or latest posts to your subscribers.
Version:     1.0.0
Author:      Sibbir Ahmed
Author URI:  http://creativeartbd.com/about/
Text Domain: mini-newsletter
Domain Path: /languages
License:     GPL2
*/

// if the file is called directly 
if( ! defined( 'WPINC' ) ) {
	die;
}

// plugin version 
define( 'MINI_NEWSLETTER_VERSION', '1.0.0' );

// plugin activation hook
function active_mini_newsletter () {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mini-newsletter-activator.php';
	Mini_Newsletter_Activator::activate();
}
register_activation_hook( __FILE__, 'active_mini_newsletter' );

// plugin de-activation hook
function deactive_mini_newsletter () {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mini-newsletter-deactivator.php';
	Mini_Newsletter_Deactivator::deactivate();
}
register_deactivation_hook( __FILE__, 'deactive_mini_newsletter' );

// include core plugin class
require plugin_dir_path( __FILE__ ) . 'includes/class-mini-newsletter.php';

// begin execution of the plugin
function run_mini_newsletter () {
	$plugin 	=	new mini_Newsletter();
	$plugin->run();
}
run_mini_newsletter();