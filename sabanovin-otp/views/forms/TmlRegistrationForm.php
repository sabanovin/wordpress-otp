<?php

echo'	<div class="mo_otp_form"><input type="checkbox" '.$disabled.' id="tml_default" class="app_enable" data-toggle="tml_options" name="mo_customer_validation_tml_enable" value="1"
			'.$tml_enabled.' /><strong>'.$form_name.'</strong>';

			get_plugin_form_link(MoConstants::TML_FORM_LINK);

echo'		<div class="mo_registration_help_desc" '.$tml_hidden.' id="tml_options">
				<b>'. mo_( "Choose between Phone or Email Verification" ).'</b>
				<p>
					<input type="radio" '.$disabled.' id="tml_phone" class="app_enable" name="mo_customer_validation_tml_enable_type" value="'.$tml_type_phone.'"
						'.($tml_enable_type == $tml_type_phone ? "checked" : "" ).'/>
							<strong>'. mo_( "Enable Phone Verification" ).'</strong>
				</p>
				<p>
					<input type="radio" '.$disabled.' id="tml_email" class="app_enable" name="mo_customer_validation_tml_enable_type" value="'.$tml_type_email.'"
						'.($tml_enable_type == $tml_type_email? "checked" : "" ).'/>
							<strong>'. mo_( "Enable Email Verification" ).'</strong>
				</p>
				<p>
					<input type="radio" '.$disabled.' id="tml_both" class="app_enable" name="mo_customer_validation_tml_enable_type" value="'.$tml_type_both.'"
						'.($tml_enable_type == $tml_type_both? "checked" : "" ).'/>
							<strong>'. mo_( "Let the user choose" ).'</strong>';
							mo_draw_tooltip(MoMessages::showMessage('INFO_HEADER'),MoMessages::showMessage('ENABLE_BOTH_BODY'));
echo '			</p>
			</div>
		</div>';