<?php

echo'	<div class="mo_otp_form"><input type="checkbox" '.$disabled.' id="uultra_default" class="app_enable" data-toggle="uultra_default_options" name="mo_customer_validation_uultra_default_enable" value="1"
										'.$uultra_enabled.' /><strong>'.$form_name.'</strong>';

										get_plugin_form_link(MoConstants::UULTRA_FORM_LINK);

echo'							<div class="mo_registration_help_desc" '.$uultra_hidden.' id="uultra_default_options">
									<b>'. mo_( "Choose between Phone or Email Verification" ).'</b>
									<p><input type="radio" '.$disabled.' data-toggle="uultra_phone_instructions" id="uultra_phone" class="form_options app_enable" name="mo_customer_validation_uultra_enable_type" value="'.$uultra_type_phone.'"
										'.( $uultra_enable_type == $uultra_type_phone ? "checked" : "").' />
											<strong>'. mo_( "Enable Phone Verification" ).'</strong>';									
										
echo'									<div '.($uultra_enable_type  != $uultra_type_phone ? "hidden" : "").' id="uultra_phone_instructions" class="mo_registration_help_desc">
											'. mo_( "Follow the following steps to enable Phone Verification" ).':
											<ol>
												<li><a href="'.$uultra_form_list.'" target="_blank">'. mo_( "Click Here" ).'</a> '. mo_( " to see your list of fields" ).'</li>
												<li>'. mo_( "Click on <b>Click here to add new field</b> button to add a new phone field." ).'</li>
												<li>'. mo_( "Fill up the details of your new field and click on <b>Submit New Field.</b>" ).'</li>
												<li>'. mo_( "Keep the <b>Meta Key</b> handy as you will need it later on." ).'</li>
												<li>'. mo_( "Enter the Meta Key of the phone field" ).': <input class="mo_registration_table_textbox" id="uultra_phone_field_key" name="uultra_phone_field_key" type="text" value="'.$uultra_field_key.'"></li>
											</ol>
										</div>
									</p>
									<p><input type="radio" '.$disabled.' id="uultra_email" class="form_options app_enable" name="mo_customer_validation_uultra_enable_type" value="'.$uultra_type_email.'"
										 '.( $uultra_enable_type == $uultra_type_email ? "checked" : "" ).' />
										 <strong>'. mo_( "Enable Email Verification" ).'</strong>
									</p>
									<p><input type="radio" '.$disabled.' data-toggle="uultra_both_instructions" id="uultra_both" class="form_options app_enable" name="mo_customer_validation_uultra_enable_type" value="'.$uultra_type_both.'"
										'.( $uultra_enable_type == $uultra_type_both ? "checked" : "" ).' />
											<strong>'. mo_( "Let the user choose" ).'</strong>';

										mo_draw_tooltip(MoMessages::showMessage('INFO_HEADER'),MoMessages::showMessage('ENABLE_BOTH_BODY'));

echo'									<div '.($uultra_enable_type  != $uultra_type_both ? "hidden" :"").' id="uultra_both_instructions" class="mo_registration_help_desc">
											'. mo_( "Follow the following steps to enable both Email and Phone Verification" ).':
											<ol>
												<li><a href="'.$uultra_form_list.'" target="_blank">'. mo_( "Click Here" ).'</a> '. mo_( " to see your list of fields" ).'</li>
												<li>'. mo_( "Click on <b>Click here to add new field</b> button to add a new phone field." ).'</li>
												<li>'. mo_( "Fill up the details of your new field and click on <b>Submit New Field.</b>" ).'</li>
												<li>'. mo_( "Keep the <b>Meta Key</b> handy as you will need it later on." ).'</li>
												<li>'. mo_( "Enter the Meta Key of the phone field" ).': <input class="mo_registration_table_textbox" id="uultra_phone_field_key1" name="uultra_phone_field_key" type="text" value="'.$uultra_field_key.'"></li>
											</ol>
										</div>
									</p>
								</div>
							</div>';