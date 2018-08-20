<?php

echo'	<div class="mo_otp_form"><input type="checkbox" '.$disabled.' id="docdirect_default" class="app_enable" data-toggle="docdirect_options" name="mo_customer_validation_docdirect_enable" value="1"
			'.$docdirect_enabled.' /><strong>'. $form_name .'</strong>';

			get_plugin_form_link(MoConstants::DOCDIRECT_THEME);

echo'		<div class="mo_registration_help_desc" '.$docdirect_hidden.' id="docdirect_options">
				<b>'. mo_( "Choose between Phone or Email Verification" ).'</b>
				<p>
					<input type="radio" '.$disabled.' id="docdirect_phone" class="app_enable" data-toggle="docdirect_phone_options" name="mo_customer_validation_docdirect_enable_type" value="'.$docdirect_type_phone.'"
						'.($docdirect_enabled_type == $docdirect_type_phone  ? "checked" : "" ).'/>
						<strong>'. mo_( "Enable Phone Verification" ).'</strong>
				</p>
				<p>
					<input type="radio" '.$disabled.' id="docdirect_email" class="app_enable" name="mo_customer_validation_docdirect_enable_type" value="'.$docdirect_type_email.'"
						'.($docdirect_enabled_type == $docdirect_type_email? "checked" : "" ).'/>
						<strong>'. mo_( "Enable Email Verification" ).'</strong>
				</p>
			</div>
		</div>';