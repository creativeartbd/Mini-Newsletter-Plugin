<?php 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// The admin-specific functionality of the plugin.
class Mini_Newsletter_Admin {
	// The ID of this plugn
	private $plugin_name;
	// The version of this plugn 
	private $version;
	// load the display
	private $dispaly;
	// initialize the class and set ti's properties
	public function __construct ( $plugin_name, $version ) {
		$this->plugin_name 	= $plugin_name;
		$this->version 		= $version;		
		require_once plugin_dir_path(  __FILE__ ) . 'partials/class-mini-newsletter-admin-display.php';
		$this->display = new Mini_Newsletter_Admin_Display();
	}
	// Register the stylsheets for the admin are
	public function enqueue_styles () {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/mini-newsletter-admin.css', array(), $this->version, 'all' );
	}
	// Register the JavaScript for the admin area.
	public function enqueue_scripts() { 
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/mini-newsletter-admin.js', array( 'jquery' ), $this->version, false );
		$ajaxurl 	=	admin_url( 'admin-ajax.php' );
		wp_localize_script( $this->plugin_name, 'urls', array(
			'ajaxurl'	=>	$ajaxurl
		) );
	}
	// add menu to the wordpress admin for this plugin settings page
	public function add_admin_setting_menu () {
		add_menu_page( 'Mini Newsletter', 'Mini Newsletter', 'manage_options', 'mini-newsletter', array( $this, 'mini_newsletter_setting_page'), plugin_dir_url( __FILE__) .'/images/newsletter-icon.png' );
		// sub menu pages under main menu
		add_submenu_page( 'mini-newsletter', 'Email List', 'Email List', 'manage_options', 'mini-newsletter-list-email', array( $this, 'mini_newsletter_list_email' ) );
		add_submenu_page( 'mini-newsletter', 'Send Newsletter', 'Send Newsletter', 'manage_options', 'mini-newsletter-send-email', array( $this, 'mini_newsletter_send_email' ) );
	}	

	public function mini_newsletter_setting_page () {	

		// get existing title and plceholder from the options table
		$db_titlle 				= 	!empty ( get_option( 'mn_title' ) ) ? get_option( 'mn_title' ) : __( 'Newsletter', 'mini-newsletter' );
		$db_placeholder 		= 	!empty ( get_option( 'mn_placeholder' ) ) ? get_option( 'mn_placeholder' ) : __( 'Email Address', 'mini-newsletter' );
		$db_incorrect 			=	!empty ( get_option( 'mn_incorrect' ) ) ? get_option( 'mn_incorrect' ) : __( 'Your email address is incorrect', 'mini-newsletter' );
		$db_already_exist 		=	!empty ( get_option( 'mn_already_exist' ) ) ? get_option( 'mn_already_exist' ) : __( 'Email address is already exist', 'mini-newsletter' );
		$db_register_success	=	!empty ( get_option( 'mn_register_success' ) ) ? get_option( 'mn_register_success' ) : __( 'Successfully added to our newsletter', 'mini-newsletter' );
		$db_header_name			=	!empty ( get_option( 'mn_header_name' ) ) ? get_option( 'mn_header_name' ) : get_bloginfo( 'name' );
		$db_header_email		=	!empty ( get_option( 'mn_header_email' ) ) ? get_option( 'mn_header_email' ) : 'support@',.$_SERVER['SERVER_NAME'];

		// load the settings tab form		
		$this->display->mn_settings_tab( $db_titlle, $db_placeholder, $db_incorrect, $db_already_exist, $db_register_success, $db_header_name, $db_header_email ); 	
	}

	// show the list of email
	public function mini_newsletter_list_email () {
		// load the email list tab
		$this->display->mn_email_list();
	}

	// show the send email form
	public function mini_newsletter_send_email () {
		$mn_email_to_send 			=	'';
		if( isset ( $_GET['e'] ) ) {
			$mn_email_to_send 		=	esc_html ( $_GET['e'] );
		}		
		// load the send email form
		$this->display->mn_send_email_form( $mn_email_to_send );
	}	

	// validate send email form
	public function mn_send_message_action () {
		// verify nonce first
		if( check_admin_referer( 'mn_send_message_action', 's' ) ) {

			$mn_to 					=	sanitize_text_field ( $_POST['mn_to'] );
			$mn_subject 			= 	sanitize_text_field ( $_POST['mn_subject'] );			
			$mn_multiple_email		=	isset( $_POST['mn_multiple_email']) ? $_POST['mn_multiple_email'] : '';
			$mn_choose_option		=	isset( $_POST['mn_choose_option'] ) ? sanitize_text_field ( $_POST['mn_choose_option'] ) : '';
			$mn_no_of_post			=	absint ( $_POST['mn_no_of_post'] );
			$mn_message_before_body =	stripslashes ( $_POST['mn_message_before_body'] );
			$mn_message_after_body 	=	stripslashes ( $_POST['mn_message_after_body'] );
			$mn_template 			=	sanitize_text_field ( $_POST['mn_template'] );
			$mn_message 			=	stripslashes ( $_POST['mn_message'] );
			$mn_errors 				=	array();
			
			if( isset ( $mn_to, $mn_subject, $mn_choose_option, $mn_message ) ) {
				if( empty ( $mn_to ) && empty ( $mn_subject ) && empty( $mn_choose_option ) && empty ( $mn_multiple_email ) && empty ( $mn_message ) ) {
					$mn_errors[] = __( 'All fields are required', 'mini-newsletter' );
				} else {
					if( !empty ( $mn_to ) ) {
						if ( !is_email ( $mn_to ) ) {
							$mn_errors[] = __( 'Email address is incorrect', 'mini-newsletter' );
						}
					}
					if ( $mn_multiple_email ) {
						foreach ( $mn_multiple_email as $mn_email ) {
							$mn_email = sanitize_email( $mn_email );
							if( !is_email( $mn_email ) ) {
								$mn_errors[] = __( 'One of your multiple emails is incorrect', 'mini-newsletter' );
							}
						}
					}
					if( empty( $mn_to ) && empty ( $mn_multiple_email ) ) {
						$mn_errors[] = __( 'Write email address or choose from the multiple email addresses', 'mini-newsletter' );
					}
					if( empty ( $mn_subject ) ) {
						$mn_errors[] = __( 'Subject is required', 'mini-newsletter' );
					} elseif ( strlen ( $mn_subject ) > 150 ) {
						$mn_errors[] = __( 'Subject must be less 150 characters long', 'mini-newsletter' );
					}
					if( empty ( $mn_choose_option ) ) {
						$mn_errors[] = __( 'Choose what do you want to send', 'mini-newsletter' );
					} else {
						if ( $mn_choose_option == 'post' ) {
							if ( empty( $mn_no_of_post ) ) {
								$mn_errors[] = __( 'Chooose how many post(s) you want to send', 'mini-newsletter' );
							} 
							if( empty( $mn_template ) ) {
								$mn_errors[] = __( 'Choose a template for the newsletter email', 'mini-newsletter' );
							}
						} elseif ( $mn_choose_option == 'custom-message' ) {
							if( empty ( $mn_message ) ) {
								$mn_errors[] = __( 'Message is required', 'mini-newsletter' );
							}
						}
					}			
				}

				if( !empty ( $mn_errors ) ) {
					echo "<div class='mn_error_message'>";
						foreach ( $mn_errors as $mn_error ) {
							echo "<p>$mn_error</p>";
						}
					echo "</div>";
				} else {					
					
					if ( empty( $mn_multiple_email ) ) {
						$mn_to 				=	$mn_to;
					} else {
						array_push ( $mn_multiple_email, $mn_to );
						$mn_multiple_email 	= 	array_unique ( $mn_multiple_email );
						$mn_to 				=	implode( ',' , $mn_multiple_email );
					}
					
					// Send email
					$this->display->mn_send_email( $mn_to, $mn_subject, $mn_choose_option, $mn_no_of_post, $mn_message_before_body, $mn_template, $mn_message_after_body, $mn_message );
				}
			}			
		}
		wp_die();			
	}

	// save the setting tab data
	public function mn_setting_tab_action () {
		if ( check_admin_referer( 'mn_settings_tab', 's' ) ) {

			$mn_title 				= isset ( $_POST['mn_title'] ) ? sanitize_text_field ( $_POST['mn_title'] ) : '';
			$mn_placeholder 		= isset ( $_POST['mn_placeholder'] ) ? sanitize_text_field ( $_POST['mn_placeholder'] ) : '';
			$mn_incorrect 			= isset ( $_POST['mn_incorrect'] ) ? sanitize_text_field ( $_POST['mn_incorrect'] ) : '';
			$mn_already_exist 		= isset ( $_POST['mn_already_exist'] ) ? sanitize_text_field ( $_POST['mn_already_exist'] ) : '';
			$mn_register_success 	= isset ( $_POST['mn_register_success'] ) ? sanitize_text_field ( $_POST['mn_register_success'] ) : '';
			$mn_header_name 		= isset ( $_POST['mn_header_name'] ) ? sanitize_text_field ( $_POST['mn_header_name'] ) : '';
			$mn_header_email 		= isset ( $_POST['mn_header_email'] ) ? sanitize_text_field ( $_POST['mn_header_email'] ) : '';
			$mn_errors 				= array();

			$mn_domain_name 		= substr($mn_header_email, strpos($mn_header_email, "@") + 1);			

			if( isset( $mn_title, $mn_placeholder, $mn_incorrect, $mn_already_exist, $mn_register_success, $mn_header_name, $mn_header_email ) ) {
				
				if( empty ( $mn_title ) ) {
					$mn_errors[] = __( 'Title required', 'mini-newsletter' );
				} elseif( strlen ( $mn_title ) > 25 ) {
					$mn_errors[] = __( 'Title must be less than 25 characters', 'mini-newsletter' );
				}

				if( empty ( $mn_placeholder ) ){
					$mn_errors[] = __( 'Placeholder required', 'mini-newsletter' );
				} elseif( strlen ( $mn_placeholder ) > 25 ) {
					$mn_errors[] = __( 'Placeholder must be less than 25 characters', 'mini-newsletter' );
				}

				if( empty ( $mn_incorrect ) ){
					$mn_errors[] = __( 'Incorrect message text is required', 'mini-newsletter' );
				} elseif( strlen ( $mn_incorrect ) > 100 ) {
					$mn_errors[] = __( 'Incorrect message text must be less than 100 characters', 'mini-newsletter' );
				}

				if( empty ( $mn_already_exist ) ){
					$mn_errors[] = __( 'Already exist message text is required', 'mini-newsletter' );
				} elseif( strlen ( $mn_already_exist ) > 100 ) {
					$mn_errors[] = __( 'Already exist message text must be less than 100 characters', 'mini-newsletter' );
				}

				if( empty ( $mn_register_success ) ){
					$mn_errors[] = __( 'Register successfully message text is required', 'mini-newsletter' );
				} elseif( strlen ( $mn_register_success ) > 100 ) {
					$mn_errors[] = __( 'Register successfully message text must be less than 100 characters', 'mini-newsletter' );
				}

				if( empty ( $mn_header_name ) ){
					$mn_errors[] = __( 'From name is required', 'mini-newsletter' );
				} elseif( strlen ( $mn_header_name ) > 50 ) {
					$mn_errors[] = __( 'From name must be less than 50 characters long', 'mini-newsletter' );
				}

				if( empty ( $mn_header_email ) ){
					$mn_errors[] = __( 'From email address is required', 'mini-newsletter' );
				} elseif( !is_email ( $mn_header_email ) ) {
					$mn_errors[] = __( 'From email address is incorrect', 'mini-newsletter' );
				} elseif ( $_SERVER['SERVER_NAME'] <> $mn_domain_name &&  $_SERVER['SERVER_NAME'] !== 'localhost' ) {
					$mn_errors[] = __( 'From email address domain name need to match with site domain name', 'mini-newsletter');
				}
			}

			if( !empty ( $mn_errors ) ) {				
				foreach ( $mn_errors as $mn_error ) {
					echo "<div class='mn_error_message'><p>";
						echo $mn_error;
					echo "</p></div>";
				}
			} else {	
		
				update_option ( 'mn_title', $mn_title );
				update_option ( 'mn_placeholder', $mn_placeholder );
				update_option ( 'mn_incorrect', $mn_incorrect );
				update_option ( 'mn_already_exist', $mn_already_exist );
				update_option ( 'mn_register_success', $mn_register_success );
				update_option ( 'mn_header_name', $mn_header_name );
				update_option ( 'mn_header_email', $mn_header_email );
		
				echo "<div class='mn_update_message'><p>";
					echo _e ( 'Update successfully', 'mini-newsletter' );
				echo "</p></div>";
			}			
		}
		wp_die();
	}

	// delete email from the email list tab
	public function mn_email_list_tab_action () { 
		// get the nonce value
		$mn_nonce  				=	$_POST['s'];
		// verify the nonce first
		if ( wp_verify_nonce ( $mn_nonce, 'mn_email_list_tab' ) ) {
			global $wpdb;
			$mn_email_id		=	absint( $_POST['id'] );
			$mn_table_prefix 	=	$wpdb->prefix;
			$mn_table_name		=	$mn_table_prefix . 'mini_nletter_email';
			$mn_where  			=	array (
				'id'	=>	$mn_email_id
			);
			$mn_where_format 	=	array ( '%d' );
			$mn_output 			=	array();
			if ( $wpdb->delete( $mn_table_name, $mn_where, $mn_where_format ) ) {
				$mn_output['deleted']	=	__( 'Removed.', 'mini-newsletter');				
			} else {
				$mn_output['deleted']	=	__( 'Email cant removing.', 'mini-newsletter');
			}
			echo json_encode( $mn_output );
			wp_die();
		}
	}
	// set html type email
	public function mn_mail_content_type () {
		 return "text/html";
	}	
	// set the email header from email address
	public function mn_mail_from () {
		$mn_header_email 	=	empty ( get_option ( 'mn_header_email' ) ) ? get_bloginfo( 'admin_email' ) : get_option( 'mn_header_email' );
		return $mn_header_email;
	}
	// set the email header name
	public function mn_mail_from_name () {
		$mn_header_name 	=	empty ( get_option ( 'mn_header_name' ) ) ? get_bloginfo( 'name' ) : get_option( 'mn_header_name' );
		return $mn_header_name;
	}
}