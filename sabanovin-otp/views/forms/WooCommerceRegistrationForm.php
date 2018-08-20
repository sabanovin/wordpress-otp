<?php

echo'	<div class="mo_otp_form"><input type="checkbox" '.$disabled.' id="wc_default" data-toggle="wc_default_options" class="app_enable" name="mo_customer_validation_wc_default_enable" value="1"
		'.$woocommerce_registration.' /><strong>'.$form_name.'</strong>';
					
			get_plugin_form_link(MoConstants::WC_FORM_LINK);

echo'		<div class="mo_registration_help_desc" '.$wc_hidden.' id="wc_default_options">
				<b>'. mo_( "Choose between Phone or Email Verification" ).'</b>
				<p>
					<input type="radio" '.$disabled.' id="wc_phone" class="app_enable" data-toggle="wc_phone_options" name="mo_customer_validation_wc_enable_type" value="'.$wc_reg_type_phone.'"
						'.($wc_enable_type == $wc_reg_type_phone ? "checked" : "" ).'/>
						<strong>'. mo_( "Enable Phone Verification" ).'</strong>
				</p>
				<div '.($wc_enable_type != $wc_reg_type_phone  ? "hidden" :"").' class="mo_registration_help_desc" 
						id="wc_phone_options" >
						<input type="checkbox" '.$disabled.' name="mo_customer_validation_wc_restrict_duplicates" value="1"
								'.$wc_restrict_duplicates.' /><strong>'. mo_( "Do not allow users to use the same phone number for multiple accounts." ).'</strong>
				</div>
				<p>
					<input type="radio" '.$disabled.' id="wc_email" class="app_enable" name="mo_customer_validation_wc_enable_type" value="'.$wc_reg_type_email.'"
						'.($wc_enable_type == $wc_reg_type_email? "checked" : "" ).'/>
						<strong>'. mo_( "Enable Email Verification" ).'</strong>
				</p>
				<p>
					<input type="radio" '.$disabled.' id="wc_both" class="app_enable" name="mo_customer_validation_wc_enable_type" value="'.$wc_reg_type_both.'"
						'.($wc_enable_type == $wc_reg_type_both? "checked" : "" ).'/>
						<strong>'. mo_( "Let the user choose" ).'</strong>';
							mo_draw_tooltip(MoMessages::showMessage('INFO_HEADER'),MoMessages::showMessage('ENABLE_BOTH_BODY'));
echo '			</p>
				<b>'. mo_( "Select page to redirect to after registration" ).': </b>';
                wp_dropdown_pages(array("selected" => $redirect_page_id));
echo'		</div>
		</div>';