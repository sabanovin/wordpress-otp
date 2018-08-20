<?php

echo'	<div class="mo_otp_form"><input type="checkbox" '.$disabled.' id="realestate_reg" class="app_enable" 
            data-toggle="realestate_options" name="mo_customer_validation_realestate_enable" value="1"
			'.$realestate_enabled.' /><strong>'. $form_name .'</strong>';

            get_plugin_form_link(MoConstants::REALESTATE7_THEME_LINK);

echo'		<div class="mo_registration_help_desc" '.$realestate_hidden.' id="realestate_options">
				<b>Choose between Phone or Email Verification</b>
				<p>
					<input type="radio" '.$disabled.' id="realestate_phone" class="app_enable" 
					    name="mo_customer_validation_realestate_contact_type" value="'.$realestate_type_phone.'"
						'.($realestate_enabled_type == $realestate_type_phone ? "checked" : "" ).'/>
						<strong>'. mo_( "Enable Phone Verification" ).'</strong>
				</p>
				<p>
					<input type="radio" '.$disabled.' id="realestate_email" class="app_enable" 
					    name="mo_customer_validation_realestate_contact_type" value="'.$realestate_type_email.'"
						'.($realestate_enabled_type == $realestate_type_email? "checked" : "" ).'/>
						<strong>'. mo_( "Enable Email Verification" ).'</strong>
				</p>
			</div>
		</div>';
