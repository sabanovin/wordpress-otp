<?php 

echo'	<div class="mo_otp_form"><input type="checkbox" '.$disabled.' id="wpcomment" class="app_enable" data-toggle="wpcomment_options" name="mo_customer_validation_wpcomment_enable" value="1"
			'.$wpcomment_enabled.' /><strong>'.$form_name.'</strong>';

echo'		<div class="mo_registration_help_desc" '.$wpcomment_hidden.' id="wpcomment_options">
				<p>
					<input type="checkbox" class="form_options" '.$wpComment_force_verify.' id="wpcomment_enable_for_loggedin_users" name="wpcomment_enable_for_loggedin_users" value="1"> 
						&nbsp;<strong>'. mo_('Force OTP Verification for Logged In users as well?' ).'</strong><br>
						<i>( '.mo_('Even logged in users will have to verify their phone or email in order to be able to submit comments' ). ')</i>
				</p>
				<b>'. mo_( "Choose between Phone or Email Verification" ).'</b>
				<p>
					<input type="radio" '.$disabled.' id="wpcomment_phone" class="app_enable" name="mo_customer_validation_wpcomment_enable_type" value="'.$wpcomment_type_phone.'"
						'.($wpcomment_type == $wpcomment_type_phone  ? "checked" : "" ).'/>
						<strong>'. mo_( "Enable Phone Verification" ).'</strong>
				</p>
				<p>
					<input type="radio" '.$disabled.' id="wpcomment_email" class="app_enable" name="mo_customer_validation_wpcomment_enable_type" value="'.$wpcomment_type_email.'"
						'.($wpcomment_type == $wpcomment_type_email? "checked" : "" ).'/>
						<strong>'. mo_( "Enable Email Verification" ).'</strong>
				</p>
			</div>
		</div>';