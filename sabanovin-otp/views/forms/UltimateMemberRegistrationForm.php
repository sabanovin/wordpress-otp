<?php

echo'		<div class="mo_otp_form"><input type="checkbox" '.$disabled.' id="um_default" data-toggle="um_default_options" class="app_enable" name="mo_customer_validation_um_default_enable" value="1"
					'.$um_enabled.' /><strong>'. $form_name . '</strong>';

							get_plugin_form_link(MoConstants::UM_ENABLED);

echo'		<div class="mo_registration_help_desc" '.$um_hidden.' id="um_default_options">
				<b>'. mo_( "Choose between Phone or Email Verification" ).'</b>
				<p>
					<input type="radio" '.$disabled.' id="um_phone" data-toggle="um_phone_instructions" class="app_enable" name="mo_customer_validation_um_enable_type" value="'.$um_type_phone.'"
					'.( $um_enabled_type == $um_type_phone ? "checked" : "").'/>
						<strong>'. mo_( "Enable Phone Verification" ).'</strong>
					
					<div '.($um_enabled_type != $um_type_phone ? "hidden" : "").' id="um_phone_instructions" class="mo_registration_help_desc">
						'. mo_( "Follow the following steps to enable Phone Verification" ).':
						<ol>
							<li><a href="'.$um_forms.'"  target="_blank">'. mo_( "Click Here" ).'</a> '. mo_( " to see your list of forms" ).'</li>
							<li>'. mo_( "Click on the <b>Edit link</b> of your form." ).'</li>
							<li>'. mo_( "Add a new <b>Mobile Number</b> Field from the list of predefined fields." ).'</li>
							<li>'. mo_( "Click on <b>update</b> to save your form." ).'</li>
						</ol>
						<input type="checkbox" '.$disabled.' name="mo_customer_validation_um_restrict_duplicates" value="1"'.$um_restrict_duplicates.'/>
				        <strong>'. mo_( "Do not allow users to use the same phone number for multiple accounts." ).'</strong>
					</div>
				</p>
				<p>
					<input type="radio" '.$disabled.' id="um_email" class="app_enable" name="mo_customer_validation_um_enable_type" value="'.$um_type_email.'"
					'.( $um_enabled_type == $um_type_email ? "checked" : "").' />
						<strong>'. mo_( "Enable Email Verification" ).'</strong>
				</p>
				<p>
					<input type="radio" '.$disabled.' id="um_both" data-toggle="um_both_instructions" class="app_enable" name="mo_customer_validation_um_enable_type" value="'.$um_type_both.'"
						'.( $um_enabled_type == $um_type_both ? "checked" : "").' />
						<strong>'. mo_( "Let the user choose" ).'</strong>';
				
						mo_draw_tooltip(MoMessages::showMessage('INFO_HEADER'),MoMessages::showMessage('ENABLE_BOTH_BODY'));

echo'				<div '.($um_enabled_type != $um_type_both ? "hidden" : "").' id="um_both_instructions" class="mo_registration_help_desc">
						'. mo_( "Follow the following steps to enable Email and Phone Verification" ).':
						<ol>
							<li><a href="'.$um_forms.'">'. mo_( "Click Here" ).'</a> '. mo_( " to see your list of forms" ).'</li>
							<li>'. mo_( "Click on the <b>Edit link</b> of your form." ).'</li>
							<li>'. mo_( "Add a new <b>Mobile Number</b> Field from the list of predefined fields." ).'</li>
							<li>'. mo_( "Click on <b>update</b> to save your form." ).'</li>
						</ol>
						<input type="checkbox" '.$disabled.' name="mo_customer_validation_um_restrict_duplicates" value="1"'.$um_restrict_duplicates.'/>
				        <strong>'. mo_( "Do not allow users to use the same phone number for multiple accounts." ).'</strong>
					</div>
				</p>
			</div>
		</div>';