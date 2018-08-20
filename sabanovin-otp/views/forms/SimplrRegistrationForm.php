<?php

echo'	<div class="mo_otp_form"><input type="checkbox" '.$disabled.' id="simplr_default" data-toggle="simplr_default_options" class="app_enable" name="mo_customer_validation_simplr_default_enable" value="1"
				'.$simplr_enabled.' /><strong>'.$form_name.'</strong>';

					get_plugin_form_link(MoConstants::SIMPLR_FORM_LINK);

echo'			<div class="mo_registration_help_desc" '.$simplr_hidden.' id="simplr_default_options">
					<b>'. mo_( "Choose between Phone or Email Verification" ).'</b>
						<p><input type="radio" '.$disabled.' data-toggle="simplr_phone_instruction" id="simplr_phone" class="form_options app_enable" name="mo_customer_validation_simplr_enable_type" value="'.$simplr_type_phone.'"
							'.( $simplr_enabled_type == $simplr_type_phone ? "checked" : "" ).' />
								<strong>'. mo_( "Enable Phone Verification" ).'</strong>';

echo'						<div '.($simplr_enabled_type!= $simplr_type_phone ? "hidden" : "").' id="simplr_phone_instruction" class="mo_registration_help_desc">
								'. mo_( "Follow the following steps to enable Phone Verification" ).':
								<ol>
									<li><a href="'.$simplr_fields_page.'" target="_blank">'. mo_( "Click Here" ).'</a> '. mo_( " to see your list of fields" ).'</li>
									<li>'. mo_( "Add a new Phone Field by clicking the <b>Add Field</b> button." ).'</li>
									<li>'. mo_( "Give the <b>Field Name</b> and <b>Field Key</b> for the new field. Remember the Field Key as you will need it later." ).'</li>
									<li>'. mo_( "Click on <b>Add Field</b> button at the bottom of the page to save your new field." ).'</li>
									<li><a href="'.$page_list.'" target="_blank	">'. mo_( "Click Here" ).'</a> '. mo_( " to see your list of pages" ).'</li>
									<li>'. mo_( "Click on the <b>Edit</b> link of your page to modify it." ).'</li>
									<li>'. mo_( "In the ShortCode add the following attribute" ).': <b>fields="{Field Key you provided in Step 2}"</b>. '. mo_( "If you already have the fields attribute defined then just add the new field key to the list." ).'</li>
									<li>'. mo_( "Click on <b>update</b> to save your page." ).'</li>
									<li>'. mo_( "Enter the Field Key of the phone field" ).':<input class="mo_registration_table_textbox" id="simplr_phone_field_key1" name="simplr_phone_field_key" type="text" value="'.$simplr_field_key.'"></li>
								</ol>
							</div>
							</p>
							<p><input type="radio" '.$disabled.' id="simplr_email" class="form_options app_enable" name="mo_customer_validation_simplr_enable_type" value="'.$simplr_type_email.'"
									'.( $simplr_enabled_type == $simplr_type_email ? "checked" : "").' />
									<strong>'. mo_( "Enable Email Verification" ).'</strong>
							</p>
							<p><input type="radio" '.$disabled.' data-toggle="simplr_both_instruction" id="simplr_both" class="form_options app_enable" name="mo_customer_validation_simplr_enable_type" value="'.$simplr_type_both.'"
									'.( $simplr_enabled_type == $simplr_type_both ? "checked" : "").' />
									<strong>'. mo_( "Let the user choose" ).'</strong>';
			
									mo_draw_tooltip(MoMessages::showMessage('INFO_HEADER'),MoMessages::showMessage('ENABLE_BOTH_BODY'));

echo'							<div '.($simplr_enabled_type != $simplr_type_both ? "hidden" : "").' id="simplr_both_instruction" class="mo_registration_help_desc">
									'. mo_( "Follow the following steps to enable Email and Phone Verification" ).':
									<ol>
										<li><a href="'.$simplr_fields_page.'" target="_blank">'. mo_( "Click Here" ).'</a> '. mo_( " to see your list of fields" ).'
										<li>'. mo_( "Add a new Phone Field by clicking the <b>Add Field</b> button." ).'</li>
										<li>'. mo_( "Give the <b>Field Name</b> and <b>Field Key</b> for the new field. Remember the Field Key as you will need it later." ).'</li>
										<li>'. mo_( "Click on <b>Add Field</b> button at the bottom of the page to save your new field." ).'</li>
										<li><a href="'.$page_list.'" target="_blank	">'. mo_( "Click Here" ).'</a> '. mo_( " to see your list of pages" ).'</li>
										<li>'. mo_( "Click on the <b>Edit</b> link of your page to modify it." ).'</li>
										<li>'. mo_( "In the ShortCode add the following attribute" ).': <b>fields="{Field Key you provided in Step 2}"</b>. '. mo_( "If you already have the fields attribute defined then just add the new field key to the list." ).'</li>
										<li>'. mo_( "Click on <b>update</b> to save your page." ).'</li>
										<li>'. mo_( "Enter the Field Key of the phone field" ).': <input class="mo_registration_table_textbox" id="simplr_phone_field_key2" name="simplr_phone_field_key" type="text" value="'.$simplr_field_key.'"></li>
									</ol>
								</div>
							</p>
						</div>
					</div>';