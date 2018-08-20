<?php

echo'	<div class="mo_otp_form"><input type="checkbox" '.$disabled.' id="wp_default" class="app_enable" data-toggle="wp_default_options" name="mo_customer_validation_wp_default_enable" value="1"
			'.$default_registration.' /><strong>'.$form_name.'</strong>';

echo'		<div class="mo_registration_help_desc" '.$wp_default_hidden.' id="wp_default_options">
				<b>'. mo_( "Choose between Phone or Email Verification" ).'</b>
				<p>
					<input type="radio" '.$disabled.' id="wp_default_phone" class="app_enable" data-toggle="wp_default_phone_options" name="mo_customer_validation_wp_default_enable_type" value="'.$wpreg_phone_type.'"
						'.($wp_default_type == $wpreg_phone_type  ? "checked" : "" ).'/>
						<strong>'. mo_( "Enable Phone Verification" ).'</strong>
				</p>
				<div '.($wp_default_type != $wpreg_phone_type  ? "hidden" :"").' class="mo_registration_help_desc" 
						id="wp_default_phone_options" >
						<input type="checkbox" '.$disabled.' name="mo_customer_validation_wp_reg_restrict_duplicates" value="1"
								'.$wp_handle_reg_duplicates.' /><strong>'. mo_( "Do not allow users to use the same phone number for multiple accounts." ).'</strong>
				</div>
				<p>
					<input type="radio" '.$disabled.' id="wp_default_email" class="app_enable" name="mo_customer_validation_wp_default_enable_type" value="'.$wpreg_email_type.'"
						'.($wp_default_type == $wpreg_email_type? "checked" : "" ).'/>
						<strong>'. mo_( "Enable Email Verification" ).'</strong>
				</p>
				<p>
					<input type="radio" '.$disabled.' id="wp_default_both" class="app_enable" name="mo_customer_validation_wp_default_enable_type" 
						value="'.$wpreg_both_type.'" data-toggle="wp_default_both_options"
						'.($wp_default_type == $wpreg_both_type? "checked" : "" ).'/>
						<strong>'. mo_( "Let the user choose" ).'</strong>';
							mo_draw_tooltip(MoMessages::showMessage('INFO_HEADER'),MoMessages::showMessage('ENABLE_BOTH_BODY'));
echo '			</p>
				<div '.($wp_default_type != $wpreg_both_type ? "hidden" :"").' class="mo_registration_help_desc" 
						id="wp_default_both_options" >
						<input type="checkbox" '.$disabled.' name="mo_customer_validation_wp_reg_restrict_duplicates" value="1"
								'.$wp_handle_reg_duplicates.' /><strong>'. mo_( "Do not allow users to use the same phone number for multiple accounts." ).'</strong>
				</div>
			</div>
		</div>';