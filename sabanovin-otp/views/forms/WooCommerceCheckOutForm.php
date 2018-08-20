<?php

echo' 	<div class="mo_otp_form"><input type="checkbox" '.$disabled.' id="wc_checkout" data-toggle="wc_checkout_options" class="app_enable" name="mo_customer_validation_wc_checkout_enable" value="1"
						'.$wc_checkout.' /><strong>'.$form_name. '</strong>';
					
				get_plugin_form_link(MoConstants::WC_FORM_LINK);

echo'			<div class="mo_registration_help_desc" '.$wc_checkout_hidden.' id="wc_checkout_options">
				<b>'. mo_( "Choose between Phone or Email Verification" ).'</b>
				<p><input type="radio" '.$disabled.' id="wc_checkout_phone" class="app_enable" name="mo_customer_validation_wc_checkout_type" value="'.$wc_type_phone.'"
						'.($wc_checkout_enable_type == $wc_type_phone ? "checked" : "" ).' />
							<strong>'. mo_( "Enable Phone Verification" ).'</strong>
				</p>
				<p><input type="radio" '.$disabled.' id="wc_checkout_email" class="app_enable" name="mo_customer_validation_wc_checkout_type" value="'.$wc_type_email.'"
						'.($wc_checkout_enable_type == $wc_type_email ? "checked" : "" ).' />
							<strong>'. mo_( "Enable Email Verification" ).'</strong>
				</p>
				<p style="margin-left:2%;margin-top:3%;">
					<input type="checkbox" '.$disabled.' '.$guest_checkout.' class="app_enable" name="mo_customer_validation_wc_checkout_guest" value="1" ><b>'. mo_( "Enable Verification only for Guest Users." ).'</b>'; 

					mo_draw_tooltip(MoMessages::showMessage('WC_GUEST_CHECKOUT_HEAD'),
									MoMessages::showMessage('WC_GUEST_CHECKOUT_BODY'));

echo'				<br/>
				<p>
				<p style="margin-left:2%;">
					<input type="checkbox" '.$disabled.' '.$checkout_button .' class="app_enable" name="mo_customer_validation_wc_checkout_button" value="1" type="checkbox"><b>'. mo_( "Show a verification button instead of a link on the WooCommerce Checkout Page." ).'</b><br/>
				</p>
				<p style="margin-left:2%;">
					<input type="checkbox" '.$disabled.' '.$checkout_popup.' class="app_enable" name="mo_customer_validation_wc_checkout_popup" value="1" type="checkbox"><b>'. mo_( "Show a popup for validating OTP." ).'</b><br/>
				</p>
				<p style="margin-left:2%;">
					<input type="checkbox" '.$disabled.' '.$checkout_selection.' class="app_enable" data-toggle="selective_payment" name="mo_customer_validation_wc_checkout_selective_payment" value="1" type="checkbox"><b>'. mo_( "Validate OTP for selective Payment Methods." ).'</b><br/>
				</p>
				<div id="selective_payment" class="mo_registration_help_desc" '.$checkout_selection_hidden.' style="margin-left:2%;">
					<b><label for="wc_payment" style="vertical-align:top;">'.mo_("Select Payment Plans for OTP Verification").':</label> </b>
				';
		
					get_wc_payment_dropdown($disabled,$checkout_payment_plans);

echo			'
				</div>
				<p style="margin-left:2%;">
					<i><b>'.mo_("Verification Button text").':</b></i>
					<input class="mo_registration_table_textbox" name="mo_customer_validation_wc_checkout_button_link_text" type="text" value="'.$button_text.'">					
				</p>
			</div>
		</div>';