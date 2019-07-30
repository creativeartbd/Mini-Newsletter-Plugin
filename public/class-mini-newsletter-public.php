<?php
// define class for public facing functionality

class Mini_Newsletter_Public {
	// The ID of this plugin
	private $plugin_name;
	// The version of this plugin
	private $version;
	// set the public display
	private $display;

	// initialize the class and set it's properties
	public function __construct ( $plugin_name, $version ) {		
		$this->plugin_name 	= $plugin_name;
		$this->version 		= $version;
		require_once plugin_dir_path( __FILE__ ) . 'partials/class-mini-newsletter-public-display.php';
		$this->display 		= new mini_newsletter_public_display();
	}	
	// register the stylesheet
	public function enqueue_styles () {		
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/mini-newsletter-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . '-icofont', plugin_dir_url( __FILE__ ) . 'css/icofont.min.css', array(), $this->version, 'all' );
	}
	// register the javascript 
	public function enqueue_scripts () {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/mini-newsletter-public.js', array('jquery'), $this->version, true);
		$ajax_url = admin_url( 'admin-ajax.php' );
		wp_localize_script( $this->plugin_name, 'urls', array(
			'ajaxurl'	=>	$ajax_url
		) );
	}
	// show newsletter form to the front-end
	public function newsletter_form_call () {		
		$mn_title 			=	!empty( get_option( 'mn_title' ) ) ? get_option( 'mn_title' ) : __('Newsletter', 'mini-newsletter' );
		$mn_placeholder 	= 	!empty( get_option( 'mn_placeholder' ) ) ? get_option( 'mn_placeholder' ) : __('Email Address', 'mini-newsletter' );
		// load the front-end newsletter form
		$this->display->mn_newsletter_form( $mn_title, $mn_placeholder );		
	}

	// ajax functon for the newsletter front-end form
	public function mn_front_ajax() {		
		// check nonce field first	
		if ( check_ajax_referer( 'mn_action', 's' ) ) {

			$mn_incorrect 			=	!empty ( get_option( 'mn_incorrect' ) ) ? get_option( 'mn_incorrect' ) : __( 'Your email address is incorrect', 'mini-newsletter' );
			$mn_already_exist 		=	!empty ( get_option( 'mn_already_exist' ) ) ? get_option( 'mn_already_exist' ) : __( 'Email address is already exist', 'mini-newsletter' );
			$mn_register_success	=	!empty ( get_option( 'mn_register_success' ) ) ? get_option( 'mn_register_success' ) : __( 'Successfully added to our newsletter', 'mini-newsletter' );

			global $wpdb;
			$mn_email 		=	sanitize_email( $_POST['mn_email'] );
			$mn_table_name =	$wpdb->prefix . 'mini_nletter_email';
			$mn_sql 		=	"SELECT count( email_address ) FROM $mn_table_name WHERE email_address = '$mn_email' ";
			$mn_exits 		=	$wpdb->get_var( $mn_sql );

			if( !is_email( $mn_email )) {
				echo "<div class='mn_error_message'>$mn_incorrect</div>";
			} elseif( $mn_exits > 0 ) {
				echo "<div class='mn_error_message'>$mn_already_exist</div>";
			} else {
				$wpdb->insert ( $mn_table_name, array (
					'email_address'	=>	$mn_email,
					'added_time'	=>	current_time( 'mysql' )
				), array('%s', '%s'));
				echo "<div class='mn_update_message'>$mn_register_success</div>";
			}
		}
		wp_die();
	}
}