<?php 
// Provide a admin area view for the plugin
class Mini_Newsletter_Admin_Display {
	// navbar of all settings
	public function mn_tab () {
		?>
		<div class="wrap">	        
	        <h2><?php _e( 'Mini Newsletter Settings', 'mini-newsletter'); ?></h2>
	        <h1 class="nav-tab-wrapper">
	            <a href="?page=mini-newsletter" class="nav-tab <?php if ( $_GET['page'] == "mini-newsletter" ) echo 'active-nav'; ?>"><?php _e( 'Settings', 'mini-newsletter'); ?></a>
	            <a href="?page=mini-newsletter-list-email" class="nav-tab <?php if($_GET['page'] == "mini-newsletter-list-email" ) echo 'active-nav'; ?>"><?php _e( 'Email List', 'mini-newsletter'); ?></a>
	            <a href="?page=mini-newsletter-send-email" class="nav-tab <?php if($_GET['page'] == "mini-newsletter-send-email" ) echo 'active-nav'; ?>" ><?php _e( 'Send Email', 'mini-newsletter'); ?></a>
	        </h1>      
	    </div>
		<?php
	}
	// Setting tab form
	public function mn_settings_tab ( $db_titlle, $db_placeholder, $db_incorrect, $db_already_exist, $db_register_success, $db_header_name, $db_header_email ) {
		// load the tab
		$this->mn_tab();
		?>	
		<div class="container-wrapper mn-content">
			<div class="row">				
				<div class="column">					
					<form method="post" action="" id="mn_settings">
						<table class="wp-list-table fixed posts">
							<tr>
								<td colspan="2">
								<b>
									<?php _e('Settings for the frontend newsletter form', 'mini-newsletter'); ?>
									</b>
								</td>
							</tr>
							<tr width="">
								<td><?php _e( 'Title', 'mini-newsletter'); ?></td>
								<td><input type="text" name="mn_title" value="<?php echo esc_html ( $db_titlle ); ?>" class="regular-text"></td>
							</tr>
							<tr>
								<td><?php _e( 'Placeholder Text', 'mini-newsletter'); ?></td>
								<td><input type="text" name="mn_placeholder" value="<?php echo esc_html ( $db_placeholder ); ?>" class="regular-text"></td>
							</tr>
							<tr>
								<td><?php _e( 'If email address is incorrect', 'mini-newsletter'); ?></td>
								<td><input type="text" name="mn_incorrect" value="<?php echo esc_html ( $db_incorrect ); ?>" class="regular-text"></td>
							</tr>
							<tr>
								<td><?php _e( 'If email address is already exist', 'mini-newsletter'); ?></td>
								<td><input type="text" name="mn_already_exist" value="<?php echo esc_html ( $db_already_exist ); ?>" class="regular-text"></td>
							</tr>
							<tr>
								<td><?php _e( 'Registered Successfully', 'mini-newsletter'); ?></td>
								<td><input type="text" name="mn_register_success" value="<?php echo esc_html ( $db_register_success ); ?>" class="regular-text"></td>
							</tr>
							<tr>
								<td colspan="2">
									<b>
									<?php _e('Email header settings', 'mini-newsletter'); ?>
									</b>
								</td>
							</tr>
							<tr>
								<td><?php _e( 'From Name', 'mini-newsletter' );  ?></td>
								<td><input type="text" name="mn_header_name" value="<?php echo $db_header_name; ?>" class="regular-text"></td>
							</tr>
							<tr>
								<td><?php _e( 'From email address', 'mini-newsletter' );  ?></td>
								<td>
									<input type="text" name="mn_header_email" value="<?php echo $db_header_email; ?>" class="regular-text">
									<br/>
									<p class='mn-warning'><?php _e( 'From email address must be end with your site domain name. For example: xxxx@yourdomain.com', 'mini-newsletter' ); ?></p>
								</td>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td>
									<?php wp_nonce_field( 'mn_settings_tab','mn_settings_tab' ); ?>
									<input type="submit" name="save" id="mn_settings_btn" value="Save options" class="button-primary">
								</td>
							</tr>
						</table>
						<div id="mn_form_result"></div>
					</form>
				</div>
			</div>
		</div>
		<?php
	}

	// list of registered email addresses
	public function mn_email_list () {
		// load the tab
		$this->mn_tab();
		?>
		<div class="container-wrapper mn-content">
			<div class="row">				
				<div class="column">	
				
					<?php 
					global $wpdb;
					$table_prefix	=	$wpdb->prefix . 'mini_nletter_email';
					$get_sql 		=	"SELECT * FROM $table_prefix ORDER BY id DESC";
					$results		=	$wpdb->get_results($get_sql, ARRAY_A);

					echo '<div style="overflow-x:auto">';
					echo '<table>';
						echo '<tr>';
							echo '<th>Sl.</th>';
							echo '<th>Email Address</th>';
							echo '<th>Added On</th>';
							echo '<th>Action</th>';
						echo '</tr>';

					$count = 1;
					foreach ( $results as $result ) {					
						echo "<tr>";
							echo "<td>" . $count ."</td>";
							echo "<td>" . esc_html ( $result[ 'email_address'] ) ."</td>";
							echo "<td>" . esc_html ( $result[ 'added_time'] ) ."</td>";
							echo "<td><a href='?page=mini-newsletter-send-email&e=". esc_html ( $result['email_address'] ) ."'>Send Email</a> | <a href='#' data-id='". $result['id'] ."' data-nonce='" . wp_create_nonce( 'mn_email_list_tab' ) . "' class='mn_remove_email'>Remove</a></td>";
						echo "</tr>";
						$count++;	
					}
					echo '</table>';
					echo '<div id="mn_form_result"></div>';		
					echo '</div>';
					?>
				</div>
			</div>
		</div>
		<?php
	}

	// Send newsletter email form
	public function mn_send_email_form ( $mn_email_to_send = null ) {
		// load the tab
		$this->mn_tab();
		?>
		<div class="container-wrapper mn-content">
			<form method="post" action="#" id="mn_form_send_message">
				<div class="row">				
					<div class="double-column">				
						<table>
							<tr>
								<th><?php _e( 'To', 'mini-newsletter' );  ?></th>
							</tr>
							<tr>
								<td>
									<input type="text" id="mn_to" name="mn_to" value="<?php echo esc_html ( $mn_email_to_send ); ?>" class="long-input">
								</td>
							</tr>
							<tr>									
								<th><?php _e( 'Subject', 'mini-newsletter' ); ?></th>
							</tr>
							<tr>
								<td><input type="text" name="mn_subject" class="long-input"></td>
							</tr>
							<tr>
								<th><?php _e( 'What do you want to send?', 'mini-newsletter' ) ?></th>
							</tr>
							<tr>
								<td>
									<select id="choose_option" name="mn_choose_option">
										<option value="">
											<?php _e( '--Choose --', 'mini-newsletter' ) ?></option>
										<option value="post">
											<?php _e( 'Post', 'mini-newsletter' ); ?>
											</option>
										<option value="custom-message">
											<?php _e( 'Custom Message', 'mini-newsletter' ); ?>
										</option>
									</select>										
								</td>
							</tr>
							<tr class="initail_hide show_post_option">
								<th>
									<?php _e( 'How many latest post do you want to send?', 'mini-newsletter' ); ?>										
								</th>
							</tr>
							<tr class="initail_hide show_post_option">
								<td>
									<select name="mn_no_of_post">
										<option value="">
											<?php _e( '--Choose No of post--', 'mini-newsletter' ) ?>
										</option>
										<?php
										for ( $i = 1; $i <= 10 ; $i++ ) {
											$mn_post_text = $i > 1 ? 'Posts' : 'Post';
											echo "<option value='$i'> {$i} {$mn_post_text}</option>";
										}
										?>
									</select>
								</td>
							</tr>
							<tr class="initail_hide show_post_option">
								<th><?php _e( 'Message before the email body', 'mini-newsletter' ); ?></th>
							</tr>
							<tr class="initail_hide show_post_option">
								<td>
								<?php 		
								$mn_message_before_body_args = array(
								    'tinymce'       	=> array(
								        'toolbar1'      => 'bold,italic,underline,separator,alignleft,aligncenter,alignright,separator,link,unlink,undo,redo',
								        'toolbar2'      => '',
								        'toolbar3'      => '',
								    ),
								    'editor_height' 	=> 100,
									'drag_drop_upload' 	=> true,
									'textarea_name'		=>	'mn_message_before_body',
									'teeny'				=>	true,
									'media_buttons'		=>	false,
								);							
								wp_editor( '', 'mn_message_before_body', $mn_message_before_body_args );
								?>		
								</td>
							</tr>
							<tr class="initail_hide show_post_option">
								<th><?php _e( '--Choose Template--', 'mini-newsletter' ); ?></th>
							</tr>
							<tr class="initail_hide show_post_option">
								<td>
									<table>
										<tr>
											<td>
												<input type="radio" name="mn_template" value="mn_default_template" checked="checked"> 
												<?php _e( 'Default', 'mini-newsletter' ); ?> <br/>
												<img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'images/mn_default_template.png'; ?>">
											</td>
											<td>
												<?php _e( 'More template comming soon', 'mini-newsletter' ); ?>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr class="initail_hide show_post_option">
								<th><?php _e( 'Message after the email body', 'mini-newsletter' ); ?></th>
							</tr>
							<tr class="initail_hide show_post_option">
								<td>
								<?php 
								$mn_message_after_body_args = array(
								    'tinymce'      		=> array(
								        'toolbar1'      => 'bold,italic,underline,separator,alignleft,aligncenter,alignright,separator,link,unlink,undo,redo',
								        'toolbar2'      => '',
								        'toolbar3'      => '',
								    ),
								    'editor_height' 	=> 100,
									'drag_drop_upload' 	=> true,
									'textarea_name'		=>	'mn_message_after_body',
									'teeny'				=>	true,
									'media_buttons'		=>	false,
								);										
								wp_editor( '', 'mn_message_after_body', $mn_message_after_body_args);
								?>
								</td>
							</tr>
							<tr class="initail_hide show_custom_message_option">
								<th valign="top"><?php _e( 'Message', 'mini-newsletter' ); ?></th>
							</tr>
							<tr class="initail_hide show_custom_message_option">
								<td>
								<?php					
								wp_editor( '', 'mn_message', array (			
									'editor_height' 	=> 250,
									'drag_drop_upload' 	=> true,
									'teeny'				=>	true,
									'media_buttons'		=>	false,
								) );	
								?>								
								</td>
							</tr>							
							<tr>								
								<td>
									<?php wp_nonce_field( 'mn_send_message_action', 'mn_nonce_field' ); ?>
									<input type="submit" name="send" id="mn_send_message" value="Send Message" class="button-primary">
								</td>
							</tr>
						</table>
						<div id="mn_form_result"></div>
					</div>
					<div class="column">
						<div class="multiple_email">
							<p>
								<b>
									<?php _e( 'Choose Multiple Address', 'mini-newsletter' ); ?>
								</b> 
								<input type="checkbox" class="mn_choose_all" class="mn_choose_all">
							</p>
							<table>
							<?php 
							global $wpdb;
							$table_prefix	=	$wpdb->prefix . 'mini_nletter_email';
							$get_sql 		=	"SELECT * FROM $table_prefix";
							$results		=	$wpdb->get_results($get_sql, ARRAY_A);
							foreach ( $results as $result ) {
								?>							
								<div class="email-list">
									<input type="checkbox" class="mn_check_me" name="mn_multiple_email[]" value="<?php echo esc_attr( $result['email_address'] ); ?>">
									<label><?php echo esc_html ( $result['email_address'] ); ?></label>
								</div>
								<?php
							}
							?>
							</table>
						</div>
					</div>
				</div>
			</form>
		</div>
		<?php
	}

	// email template to send to registered email addresses
	public function mn_send_email ( $mn_to, $mn_subject, $mn_choose_option, $mn_no_of_post, $mn_message_before_body, $mn_template, $mn_message_after_body, $mn_message ) {

		$mn_message 		= 	$mn_message;
		$mn_post_args		=	array( 
			'posts_per_page'	=> 	 absint ( $mn_no_of_post ) 
		);
		$mn_recent_posts 	=  wp_get_recent_posts ( $mn_post_args );

		if ( has_custom_logo() ) {
			$mn_site_logo 	=	get_custom_logo();	
		} else {
			$mn_site_logo 	=	"<h1 style='color : #000; margin-top: 50px; margin-bottom: 50px; font-size: 36px;'>".get_bloginfo( 'name' )."</h1>";
		}
		
		$default_template_style_header = <<<TEMPLATE
TEMPLATE;

		$default_template_style_body = <<<TEMPLATE

			<body style="padding:0 !important; margin:0 !important; display:block !important; min-width:100% !important; width:100% !important; background:#f3f4f6; -webkit-text-size-adjust:none;">
				<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#f3f4f6">
					<tr>
						<td align="center" valign="top">
							<table width="650" border="0" cellspacing="0" cellpadding="0" >
								<tr>
									<td style="width:650px; min-width:650px; font-size:0pt; line-height:0pt; margin:0; font-weight:normal; padding:55px 0px;">
										<!-- Header -->
										<table width="100%" border="0" cellspacing="0" cellpadding="0">
											<tr>
												<td style="padding: 30px 30px 0 30px; border-radius:26px 26px 0px 0px;" bgcolor="#ffffff">
													<table width="100%" border="0" cellspacing="0" cellpadding="0">
														<tr>
															<th width="145" style="font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal; vertical-align:top;">
																<table width="100%" border="0" cellspacing="0" cellpadding="0">
																	<tr>
																		<td style="font-size:0pt; line-height:0pt; text-align:left;">$mn_site_logo</td>
																	</tr>
																</table>
															</th>
															<th width="1" style="font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal; vertical-align:top;"></th>
															<th style="font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal;">
																<table width="100%" border="0" cellspacing="0" cellpadding="0">
																	<tr>
																		<td style="color:#999999; font-family:'Playfair Display', Georgia,serif; font-size:13px; line-height:18px; text-align:right;"><a href="#" target="_blank"  style="color:#999999; text-decoration:none;"><span style="color:#999999; text-decoration:none;"></span></a></td>
																	</tr>
																</table>
															</th>
														</tr>
													</table>
												</td>
											</tr>
TEMPLATE;
		
		if( !empty ( $mn_message_before_body ) ) {
			$default_template_style_body .= <<<TEMPLATE
			<tr>
				<td style="padding:0 30px 30px 30px;" bgcolor="#ffffff">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tbody>											
							<tr>
								<td style="text-align : center; padding-bottom : 25px; color:#999999; font-family:Arial,sans-serif; font-size:14px; line-height:26px; text-align:left; padding-bottom:20px;">
									$mn_message_before_body
								</td>
							</tr>											
						</tbody>
					</table>
				</td>
			</tr>
TEMPLATE;
		}

		$default_template_style_body .= <<<TEMPLATE
			</table>
TEMPLATE;
		
$default_template_style_body .= <<<TEMPLATE
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td style="padding: 0px 30px 50px 30px;" bgcolor="#ffffff">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
TEMPLATE;

		foreach ( $mn_recent_posts as $mn_recent_post ) {

			$mn_post_id 			=	$mn_recent_post['ID'];
			$mn_post_title 			=	$mn_recent_post['post_title'];
			$mn_post_content 		= 	wp_trim_words ( $mn_recent_post['post_content'], 30 );
			$mn_post_image_url		= 	wp_get_attachment_image_src( get_post_thumbnail_id ( $mn_post_id ) );						
			$mn_post_image_url 		=	$mn_post_image_url[0];
			if( empty ( $mn_post_image_url ) ) {
				$not_found_image	=	plugin_dir_url( dirname( __FILE__ ) ) . 'admin/images/no-image.png';
				$mn_post_image 		=	"<img src='$not_found_image' width='124' height='53' border='0'>";
			} else {
				$mn_post_image 		=	"<img src='$mn_post_image_url' width='124' height='53' border='0'>";	
			}						
			$mn_post_link 			=	get_the_permalink( $mn_post_id );

$default_template_style_body .= <<<TEMPLATE
			<tr>
				<td style="padding-bottom: 30px;">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<th width="190" style="font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal; vertical-align:top;">
								<table width="100%" border="0" cellspacing="0" cellpadding="0">
									<tr>
										<td style="font-size:0pt; line-height:0pt; text-align:left;">
											<img src="$mn_post_image_url" width="190" height="190" border="0" alt="" />
										</td>
									</tr>
								</table>
							</th>
							<th width="30" style="font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal; vertical-align:top;"></th>
							<th style="font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal;">
								<table width="100%" border="0" cellspacing="0" cellpadding="0">
									<tr>
										<td style="color:#000000; font-family:'Playfair Display', Georgia,serif; font-size:20px; line-height:28px; text-align:left; padding-bottom:20px;">
											$mn_post_title;
										</td>
									</tr>
									<tr>
										<td style="color:#999999; font-family:Arial,sans-serif; font-size:14px; line-height:26px; text-align:left; padding-bottom:20px;">
											$mn_post_content
										</td>
									</tr>
									<!-- Button -->
									<tr>
										<td align="left" >
											<table border="0" cellspacing="0" cellpadding="0">
												<tr>
													<td style="background:#7ba9fe; color:#000000; font-family:'Playfair Display', Georgia,serif; font-size:14px; line-height:18px; text-align:center; border-radius:25px; text-transform:uppercase; background-color:transparent; border:2px solid #7ba9fe; padding:12px 40px;"><a href="$mn_post_link" target="_blank" style="color:#000001; text-decoration:none;"><span style="color:#000001; text-decoration:none;">VIEW DETAILS</span></a></td>
												</tr>
											</table>
										</td>
									</tr>
									<!-- END Button -->
								</table>
							</th>
						</tr>
					</table>
				</td>
			</tr>
TEMPLATE;

		}

$default_template_style_body .= <<<TEMPLATE
						</table>
					</td>
				</tr>
			</table>
TEMPLATE;

		if( !empty ( $mn_message_after_body ) ) {

				$default_template_style_body .= <<<TEMPLATE
							<table width="100%" border="0" cellspacing="0" cellpadding="0">
								<tr>
									<td style="padding-bottom:10px;"></td>
								</tr>
								<tr>
									<td style="padding: 50px 30px; border-radius:0px 0px 26px 26px;" bgcolor="#ffffff">
										<table width="100%" border="0" cellspacing="0" cellpadding="0">
											<tr>
												<td style="color:#999999; font-family:Arial,sans-serif; font-size:14px; line-height:26px; text-align:left; padding-bottom:20px;">$mn_message_after_body</td>
											</tr>											
										</table>
									</td>
								</tr>
							</table>

						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>
</html>
TEMPLATE;
		}

		if( $mn_choose_option == 'custom-message' ) {
			$mn_custom_mssage = <<<TEMPLATE
			<body class="body" style="padding:0 !important; margin:0 !important; display:block !important; min-width:100% !important; width:100% !important; background:#f3f4f6; -webkit-text-size-adjust:none;">
				<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#f3f4f6">
					<tr>
						<td align="center" valign="top">
							<table width="650" border="0" cellspacing="0" cellpadding="0" >
								<tr>
									<td style="width:650px; min-width:650px; font-size:0pt; line-height:0pt; margin:0; font-weight:normal; padding:55px 0px;">
										<!-- Header -->
										<table width="100%" border="0" cellspacing="0" cellpadding="0">
											<tr>
												<td style="padding: 30px; border-radius:26px 26px 0px 0px;" bgcolor="#ffffff">
													<table width="100%" border="0" cellspacing="0" cellpadding="0">
														<tr>
															<th width="145" style="font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal; vertical-align:top;">
																<table width="100%" border="0" cellspacing="0" cellpadding="0">
																	<tr>
																		<td style="font-size:0pt; line-height:0pt; text-align:left;">
																			$mn_site_logo
																		</td>
																	</tr>
																</table>
															</th>
															<th width="1" style="font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal; vertical-align:top;"></th>
															<th style="font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal;">
																<table width="100%" border="0" cellspacing="0" cellpadding="0">
																	<tr>
																		<td style="color:#999999; font-family:'Playfair Display', Georgia,serif; font-size:13px; line-height:18px; text-align:right;"><a href="#" target="_blank"  style="color:#999999; text-decoration:none;"><span style="color:#999999; text-decoration:none;"></span></a></td>
																	</tr>
																</table>
															</th>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td style="padding: 0 30px 30px 30px;" bgcolor="#ffffff">
													<table width="100%" border="0" cellspacing="0" cellpadding="0">
														<tbody>											
															<tr>
																<td style="text-align : center; padding-bottom : 25px; color:#999999; font-family:Arial,sans-serif; font-size:14px; line-height:26px; text-align:left; padding-bottom:20px;">$mn_message</td>
															</tr>											
														</tbody>
													</table>
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</body>
TEMPLATE;
			$mn_email_body 	=	$default_template_style_header . $mn_custom_mssage;
		} elseif ( $mn_choose_option == 'post' ) {
			$mn_email_body 	=	$default_template_style_header . $mn_message_before_body = '' . $default_template_style_body . $mn_message_after_body = '';
		} 
		
		if ( wp_mail( $mn_to, $mn_subject, $mn_email_body ) ) {
			echo '<div class="mn_update_message"><p>';
				_e( 'Successfully sent your message.', 'mini-newsletter');
			echo '</p></div>';
		} else {
			echo '<div class="mn_error_message"><p>';
				_e( 'OPPs! Something wen\'t wrong. Mail can\'t be send.', 'mini-newsletter' );
			echo '</p></div>';
		}	
	}
}