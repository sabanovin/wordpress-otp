<?php
echo'			<div class="mo_otp_form"><input type="checkbox" '.$disabled.' id="classify_theme" class="app_enable" data-toggle="classify_options" name="mo_customer_validation_classify_enable" value="1"
										'.$classify_enabled.' /><strong>'.$form_name.'</strong>';
										
						get_plugin_form_link(MoConstants::CLASSIFY_LINK);
						
echo'							<div class="mo_registration_help_desc" '.$classify_hidden.' id="classify_options">

									
									<p><input type="radio" '.$disabled.' id="classify_email" class="app_enable" data-toggle="classify_email_instructions" name="mo_customer_validation_classify_type" value="'.$classify_type_email.'"
										'.( $classify_enabled_type == $classify_type_email ? "checked" : "").' />
										<strong>'. mo_( "Enable Email Verification").'</strong>
									</p>
									
										<div '.($classify_enabled_type != $classify_type_email ? "hidden" :"").' class="mo_registration_help_desc" id="classify_email_instructions" >
											'. mo_( "Follow the following to configure your Registration form").': 
											<ol>
												<li><a href="'.$page_list.'" target="_blank">'. mo_( "Click Here").'</a> '. mo_( " to see the list of pages.").'</li>
												<li>'. mo_( "Click on the Edit option of the \"Register\" page").'</li>
												<li>'. mo_( "From the page Attributes section ,set \"Register Page\" from your template dropdown menu.").'</li>
												<li>'. mo_( "Click on the Update button to save your settings.").'</li>
											</ol>
											'. mo_( "Follow the following to configure your Profile form").': 
											<ol>
												<li><a href="'.$page_list.'" target="_blank">'. mo_( "Click Here").'</a> '. mo_( " to see the list of pages.").'</li>
												<li>'.( $classify_enabled_type == "classify_email_enable" ? "checked" : ""). mo_( "Click on the Edit option of the \"Profile\" page").'</li>
												<li>'.( $classify_enabled_type == "classify_email_enable" ? "checked" : ""). mo_( "From the page Attributes section ,set \"Profile Page\" from your template dropdown menu.").'</li>
												<li>'.( $classify_enabled_type == "classify_email_enable" ? "checked" : ""). mo_( "Click on the Update button to save your settings.").'</li>
											</ol>
											'. mo_( "Click on the Save Button below to save your settings").'
											</div>
																															
									<p><input type="radio" '.$disabled.' id="classify_phone" class="app_enable" data-toggle="classify_phone_instructions" 	name="mo_customer_validation_classify_type" value="'.$classify_type_phone.'"
										'.( $classify_enabled_type == $classify_type_phone ? "checked" : "").' />
										<strong>'. mo_( "Enable Phone Verification").'</strong>
									</p>
									
									<div '.($classify_enabled_type != $classify_type_phone ? "hidden" :"").' class="mo_registration_help_desc" id="classify_phone_instructions" >
										'. mo_( "Follow the following to configure your Registration form ").': 
											<ol>
												<li><a href="'.$page_list.'" target="_blank">'. mo_( "Click Here").'</a> '. mo_( " to see the list of pages.").'</li>
												<li>'. mo_( "Click on the Edit option of the \"Register\" page").'</li>
												<li>'. mo_( "From the page Attributes section ,set \"Register Page\" from your template dropdown menu.").'</li>
												<li>'. mo_( "Click on the Update button to save your settings.").'</li>
											</ol>
										'. mo_( "Follow the following to configure your Profile form ").': 
											<ol>
												<li><a href="'.$page_list.'" target="_blank">'. mo_( "Click Here").'</a> '. mo_( " to see the list of pages.").'</li>
												<li>'. mo_( "Click on the Edit option of the \"Profile\" page").'</li>
												<li>'. mo_( "From the page Attributes section ,set \"Profile\" Page from your template dropdown menu.").'</li>
												<li>'. mo_( "Click on the Update button to save your settings.").'</li>
											</ol>
											'. mo_( "Click on the Save Button below to save your settings").'
											</div>
									
									
									
								</div>';
