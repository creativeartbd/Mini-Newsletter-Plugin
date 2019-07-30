<?php 
// core plugin class
class Mini_Newsletter {
	// maintain and register all hooks for the plugin
	protected $loader;
	// uniquely identify this plugin
	protected $plugin_name;
	// current version of the plugin
	protected $version;
	// core functionality of the plugin 
	public function __construct () {
		if( defined ( 'MINI_NEWSLETTER_VERSION' ) ) {
			$this->version = MINI_NEWSLETTER_VERSION;
		} else {
			$this->version = '1.0.0';			
		}

		$this->plugin_name = 'Mini_Newsletter';
		$this->load_dependencies();
		$this->set_local();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	// load the required dependencies for this plugin
	public function load_dependencies () {
		// The class responsible for orchestrating the actions and filters of the core plugin.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mini-newsletter-loader.php';
		//  The class responsible for defining internationalization functionality of the plugin.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mini-newsletter-i18n.php';
		// The class responsible for defining all actions that occur in the admin area.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-mini-newsletter-admin.php';
		// The class responsible for defining all actions that occur in the public-facing side of the site.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-mini-newsletter-public.php';
		$this->loader = new Mini_Newsletter_Loader();

	}

	// Define the locale for this plugin for internationalization.
	public function set_local () {
		$plugin_i18n 	=	new Mini_Newsletter_i18n();		
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	// Register all of the hooks related to the admin area functionality
	public function define_admin_hooks () {
		$plugin_admin = new Mini_Newsletter_Admin( $this->get_plugin_name(), $this->get_version() );		
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_admin_setting_menu' );
		// form the send email tab
		$this->loader->add_action( 'wp_ajax_mn_send_message_action', $plugin_admin, 'mn_send_message_action' );		
		// for the settings tab
		$this->loader->add_action( 'wp_ajax_mn_settings_tab', $plugin_admin, 'mn_setting_tab_action' );		
		// for list of email list tab
		$this->loader->add_action( 'wp_ajax_mn_email_list_tab', $plugin_admin, 'mn_email_list_tab_action' );
		// set the email header name, header email and return type
		$this->loader->add_filter( 'wp_mail_content_type', $plugin_admin, 'mn_mail_content_type' );
		$this->loader->add_filter( 'wp_mail_from', $plugin_admin, 'mn_mail_from' );
		$this->loader->add_filter( 'wp_mail_from_name', $plugin_admin, 'mn_mail_from_name' );
	}

	// Register all of the hooks related to the public-facing functionality of the plugin.
	public function define_public_hooks () {				
		$plugin_public = new Mini_Newsletter_Public ( $this->get_plugin_name(), $this->get_version() );		
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'wp_ajax_mn_action', $plugin_public, 'mn_front_ajax' );		 
		$this->loader->add_action( 'wp_ajax_nopriv_mn_action', $plugin_public, 'mn_front_ajax' );
		$this->loader->add_shortcode('newsletter_form', $plugin_public, 'newsletter_form_call');		
	}

	// Run the loader to execute all of the hooks with WordPress
	public function run () {
		$this->loader->run();
	}

	// The name of the plugin used to uniquely identify it within the context of WordPress and to define internationalization functionality.
	public function get_plugin_name () {
		return $this->plugin_name;
	}

	// The reference to the class that orchestrates the hooks with the plugin.
	public function get_loader () {
		return $this->loader;
	}

	// Retrieve the version number of the plugin.
	public function get_version () {
		return $this->version;
	}	

}