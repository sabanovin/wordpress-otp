<?php

echo'		<div class="mo_otp_form"><input type="checkbox" '.$disabled.' id="bbp_default" class="app_enable" data-toggle="bbp_default_options" name="mo_customer_validation_bbp_default_enable" value="1"
				'.$bbp_enabled.' /><strong>'. $form_name .'</strong>';

					get_plugin_form_link(MoConstants::BBP_FORM_LINK);

echo'			<div class="mo_registration_help_desc" '.$bbp_hidden.' id="bbp_default_options">
					<p><input type="checkbox" '.$disabled.' class="form_options" '.$automatic_activation.' id="bbp_disable_activation_link" 
						name="mo_customer_validation_bbp_disable_activation" value="1"/> 
						&nbsp;<strong>'. mo_("Automatically activate users after verification").'</strong><br/>
						<i>'. mo_("( No activation email would be sent after verification )").'</i></p>
					<b>'. mo_("Choose between Phone or Email Verification").'</b>
					<p><input type="radio" '.$disabled.' data-toggle="bbp_phone_instructions" id="bbp_phone" class="form_options app_enable" 
						name="mo_customer_validation_bbp_enable_type" value="'.$bbp_type_phone.'"
							'.( $bbp_enable_type == $bbp_type_phone ? "checked" : "").' />
								<strong>'. mo_("Enable Phone verification").'</strong>
						
						<div '.($bbp_enable_type != $bbp_type_phone ? "hidden" : "").' id="bbp_phone_instructions" 
							class="mo_registration_help_desc">'. mo_("Follow the following steps to enable Phone Verification"
								).':
							<ol>
								<li><a href="'.$bbp_fields.'" target="_blank">'. mo_("Click here").'</a> '. mo_(" to see your list of fields." ).'</li>
								<li>'. mo_("Add a new Phone Field by clicking the <b>Add New Field</b> button.").'</li>
								<li>'. mo_("Give the <b>Field Name</b> and <b>Description</b> for the new field. Remember the Field Name as you will 
									need it later.").'</li>
								<li>'. mo_("Select the field <b>type</b> from the select box. 
									Choose <b>Text Field</b>.").'</li>
								<li>'. mo_("Select the field <b>requirement</b> from the select box to the right.").'</li>
								<li>'. mo_("Click on <b>Save</b> button to save your new field.").'</li>
								<li>'. mo_("Enter the Name of the phone field").':
									<input class="mo_registration_table_textbox" id="bbp_phone_field_key" name="bbp_phone_field_key" type="text" 
									value="'.$bbp_field_key.'"></li>
							</ol>
						</div>
					</p>
					<p><input type="radio" '.$disabled.' id="bbp_email" class="form_options app_enable" 
						name="mo_customer_validation_bbp_enable_type" value="'.$bbp_type_email.'"
						'.( $bbp_enable_type == $bbp_type_email? "checked" : "" ).' />
						<strong>'. mo_("Enable Email verification").'</strong>
					</p>
					<p><input type="radio" '.$disabled.' data-toggle="bbp_both_instructions" id="bbp_both" class="form_options app_enable" 
						name="mo_customer_validation_bbp_enable_type" value="'.$bbp_type_both.'"
							'.( $bbp_enable_type == $bbp_type_both ? "checked" : "").' />
							<strong>'. mo_("Let the user choose").'</strong>';
	
						mo_draw_tooltip(MoMessages::showMessage('INFO_HEADER'),MoMessages::showMessage('ENABLE_BOTH_BODY'));

echo'				<div '.($bbp_enable_type != $bbp_type_both ? "hidden" : "").' id="bbp_both_instructions" class="mo_registration_help_desc">
						'. mo_("Follow the following steps to enable Email and Phone Verification").':
						<ol>
							<li><a href="'.$bbp_fields.'" target="_blank">'. mo_("Click here").'</a> '. mo_(" to see your list of fields.").'</li>
							<li>'. mo_("Add a new Phone Field by clicking the <b>Add New Field</b> button.").'</li>
							<li>'. mo_("Give the <b>Field Name</b> and <b>Description</b> for the new field. Remember the Field Name as you 
										will need it later.").'</li>
							<li>'. mo_("Select the field <b>type</b> from the select box. Choose <b>Text Field</b>.").'</li>
							<li>'. mo_("Select the field <b>requirement</b> from the select box to the right.").'</li>
							<li>'. mo_("Click on <b>Save</b> button to save your new field.").'</li>
							<li>'. mo_("Enter the Name of the phone field").':
								<input class="mo_registration_table_textbox" id="bbp_phone_field_key1" name="bbp_phone_field_key" 
									type="text" value="'.$bbp_field_key.'"></li>
						</ol>
					</div>
					</p>
				</div>
			</div>';


