<?php

echo' 	<div class="mo_otp_form"><input type="checkbox" '.$disabled.' id="emember_reg" class="app_enable" data-toggle="emember_default_options" name="mo_customer_validation_emember_default_enable" value="1"
										'.$emember_enabled.' /><strong>'.$form_name.'</strong>';
										
									get_plugin_form_link(MoConstants::EMEMBER_FORM_LINK);
																	
echo'								<div class="mo_registration_help_desc" '.$emember_hidden.' id="emember_default_options">
									<b>'. mo_( "Choose between Phone or Email Verification" ).'</b>
									<p><input type="radio" '.$disabled.' id="emember_phone" class="app_enable" name="mo_customer_validation_emember_enable_type" 
											value="'.$emember_type_phone.'" data-toggle="emember_phone_instructions"
										'.( $emember_enable_type == $emember_type_phone ? "checked" : "").' />
										<strong>'. mo_( "Enable Phone Verification" ).'</strong>
									</p>
									<div '.($emember_enable_type != $emember_type_phone ? "hidden" :"").' class="mo_registration_help_desc" 
											id="emember_phone_instructions" >
											'. mo_( "Follow the following steps to enable Phone Verification for" ).'
											eMember Form: 
											<ol>
												<li><a href="'.$form_settings_link.'" target="_blank">'. mo_( "Click Here" ).'</a> '. mo_( "to see your form settings." ).'</li>
												<li>'. mo_( "Go to the <b>Registration Form Fields</b> section." ).'</li>
												<li>'. mo_( "Check the \"Show phone field on registration page\" option to show Phone field on your form." ).'</li>
												<li>'. mo_( "Click on the Save Button below to save your settings" ).'</li>
											</ol>
									</div>
									<p><input type="radio" '.$disabled.' id="emember_email" class="app_enable" name="mo_customer_validation_emember_enable_type" value="'.$emember_type_email.'"
										'.( $emember_enable_type == $emember_type_email ? "checked" : "").' />
										<strong>'. mo_( "Enable Email Verification" ).'</strong>
									</p>
									<p><input type="radio" '.$disabled.' id="emember_both" class="app_enable" name="mo_customer_validation_emember_enable_type" 
										value="'.$emember_type_both.'" data-toggle="emember_both_instructions"
										'.( $emember_enable_type == $emember_type_both ? "checked" : "").' />
											<strong>'. mo_( "Let the user choose" ).'</strong>';
										
											mo_draw_tooltip(MoMessages::showMessage('INFO_HEADER'),MoMessages::showMessage('ENABLE_BOTH_BODY'));

echo'										
									</p>
									<div '.($emember_enable_type != $emember_type_both ? "hidden" :"").' class="mo_registration_help_desc" 
											id="emember_both_instructions" >
											'. mo_( "Follow the following steps to enable Phone Verification for" ).'
											eMember Form: 
											<ol>
												<li><a href="'.$form_settings_link.'" target="_blank">'. mo_( "Click Here" ).'</a> '. mo_( "to see your form settings." ).'</li>
												<li>'. mo_( "Go to the <b>Registration Form Fields</b> section." ).'</li>
												<li>'. mo_( "Check the \"Show phone field on registration page\" option to show Phone field on your form." ).'</li>
												<li>'. mo_( "Click on the Save Button below to save your settings" ).'</li>
											</ol>
									</div>
								</div>
							</div>';