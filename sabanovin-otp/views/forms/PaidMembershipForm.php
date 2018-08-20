<?php

echo'	<div class="mo_otp_form"><input type="checkbox" '.$disabled.' id="pmpro_reg" class="app_enable" data-toggle="pmpro_options" name="mo_customer_validation_pmpro_enable" value="1"
			'.$pmpro_enabled.' /><strong>'. $form_name .'</strong>';

            get_plugin_form_link(MoConstants::PAID_MEMBERSHIP_PRO);

echo'		<div class="mo_registration_help_desc" '.$pmpro_hidden.' id="pmpro_options">
				<b>Choose between Phone or Email Verification</b>
				<p>
					<input type="radio" '.$disabled.' id="pmpro_phone" class="app_enable" name="mo_customer_validation_pmpro_contact_type" value="'.$pmpro_type_phone.'"
						'.($pmpro_enabled_type == $pmpro_type_phone ? "checked" : "" ).'/>
						<strong>'. mo_( "Enable Phone Verification" ).'</strong>
				</p>
				<p>
					<input type="radio" '.$disabled.' id="pmpro_email" class="app_enable" name="mo_customer_validation_pmpro_contact_type" value="'.$pmpro_type_email.'"
						'.($pmpro_enabled_type == $pmpro_type_email? "checked" : "" ).'/>
						<strong>'. mo_( "Enable Email Verification" ).'</strong>
				</p>
			</div>
		</div>';
