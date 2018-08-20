<?php

echo'	<div class="mo_otp_form"><input type="checkbox" '.$disabled.' id="multisite_reg" class="app_enable" data-toggle="multisite_options" name="mo_customer_validation_multisite_enable" value="1"
			'.$multisite_enabled.' /><strong>'. $form_name .'</strong>';


echo'		<div class="mo_registration_help_desc" '.$multisite_hidden.' id="multisite_options">
				<b>Choose between Phone or Email Verification</b>
				<p>
					<input type="radio" '.$disabled.' id="multisite_phone" class="app_enable" name="mo_customer_validation_multisite_contact_type" value="'.$multisite_type_phone.'"
						'.($multisite_enabled_type == $multisite_type_phone ? "checked" : "" ).'/>
						<strong>'. mo_( "Enable Phone Verification" ).'</strong>


				</p>
				<p>
					<input type="radio" '.$disabled.' id="multisite_email" class="app_enable" name="mo_customer_validation_multisite_contact_type" value="'.$multisite_type_email.'"
						'.($multisite_enabled_type == $multisite_type_email? "checked" : "" ).'/>
						<strong>'. mo_( "Enable Email Verification" ).'</strong>
				</p>
			</div>
		</div>';
