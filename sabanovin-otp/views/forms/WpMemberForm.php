<?php

	echo'	<div class="mo_otp_form"><input type="checkbox" '.$disabled.' id="wp_member_reg" class="app_enable" data-toggle="wp_member_reg_options" name="mo_customer_validation_wp_member_reg_enable" value="1"
	'.$wp_member_reg_enabled.' /><strong>'.$form_name. '</strong>';

		get_plugin_form_link(MoConstants::WP_MEMBER_LINK);	

	echo'	<div class="mo_registration_help_desc" '.$wp_member_reg_hidden.' id="wp_member_reg_options">
				<p><input type="radio" '.$disabled.' id="wpmembers_reg_phone" class="app_enable" data-toggle="wpmembers_reg_phone_instructions" name="mo_customer_validation_wp_member_reg_enable_type" value="'.$wpm_type_phone.'"
					'.( $wpmember_enabled_type == $wpm_type_phone ? "checked" : "").' />
						<strong>'. mo_( "Enable Phone Verification" ).'</strong>
				</p>
								
				<div '.($wpmember_enabled_type != $wpm_type_phone ? "hidden" :"").' class="mo_registration_help_desc" id="wpmembers_reg_phone_instructions">			
					'. mo_( "Follow the following steps to enable Phone Verification for WP Member" ).':
					<ol>
						<li><a href="'.$wpm_field_list.'" target="_blank">'. mo_( "Click Here" ).'</a> '. mo_( "to see your list of the fields." ).'</li>
						<li>'. mo_( "Enable Day Phone field for your form and keep it required." ).'</li>
						<li>'. mo_( "Create a new text field with meta key <i>validate_otp</i> where users can enter the validation code." ).'</li>
						<li>'. mo_( "Click on the save button below to save your settings." ).'</li>
					</ol>
				</div>
									
				<p><input type="radio" '.$disabled.' id="wpmembers_reg_email" class="app_enable" data-toggle="wpmembers_reg_email_instructions" name="mo_customer_validation_wp_member_reg_enable_type" value="'.$wpm_type_email.'"
					'.( $wpmember_enabled_type == $wpm_type_email ? "checked" : "").' />
					<strong>'. mo_( "Enable Email Verification" ).'</strong>
				</p>
										
			<div '.($wpmember_enabled_type != $wpm_type_email ? "hidden" :"").' class="mo_registration_help_desc" id="wpmembers_reg_email_instructions">			
					'. mo_( "Follow the following steps to enable Email Verification for WP Member" ).':
					<ol>
						<li><a href="'.$wpm_field_list.'" target="_blank">'. mo_( "Click Here" ).'</a> '.mo_( "to see your list of fields." ).'</li>
						<li>'. mo_( "Create a new text field with meta key <i>validate_otp</i> where users can enter the validation code." ).'</li>
						<li>'. mo_( "Click on the save button below to save your settings." ).'</li>
					</ol>
			</div>					
		</div>';
			