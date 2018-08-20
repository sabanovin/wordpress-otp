<?php

echo'	<div class="mo_otp_form"><input type="checkbox" '.$disabled.' id="formcraft_premium" class="app_enable" data-toggle="fcpremium_options" name="mo_customer_validation_fcpremium_enable" value="1"
										'.$fcpremium_enabled.' /><strong>'.$form_name.'</strong>';
										
									get_plugin_form_link(MoConstants::FORMCRAFT_PREMIUM);								 
																	
echo'							<div class="mo_registration_help_desc" '.$fcpremium_hidden.' id="fcpremium_options">
									<p><input type="radio" '.$disabled.' id="fcpremium_email" class="app_enable" data-toggle="fcpe_instructions" name="mo_customer_validation_fcpremium_enable_type" value="'.$fcpremium_type_email.'"
										'.( $fcpremium_enabled_type == $fcpremium_type_email ? "checked" : "").' />
										<strong>'. mo_( "Enable Email Verification" ).'</strong>
									</p>
									<div '.($fcpremium_enabled_type != $fcpremium_type_email ? "hidden" :"").' class="mo_registration_help_desc" id="fcpe_instructions" >
											'. mo_( "Follow the following steps to enable Email Verification for FormCraft" ).': 
											<ol>
												<li><a href="'.$fcpremium_list.'" target="_blank">'. mo_( "Click Here" ).'</a> '. mo_( " to see your list of forms" ).'</li>
												<li>'. mo_( "Click on the form to edit it." ).'</li>
												<li>'. mo_( "Add an Email Field to your form. Note the Label of the email field." ).'</li>
												<li>'. mo_( "Add an Verification Field to your form where users will enter the OTP received. Note the Label of the verification field." ).'</li>
												<li>'. mo_( "Enter your Form ID, the label of the Email Field and Verification Field below" ).':<br>
													<br/>'. mo_( "Add Form " ).': <input type="button"  value="+" '. $disabled .' onclick="add_fcp_form(\'email\',1);" class="button button-primary" />&nbsp;
													<input type="button" value="-" '. $disabled .' onclick="remove_fcp_form(1);" class="button button-primary" /><br/><br/>';
												
													$form_results = get_formcraft_premium_form_list($fcpremium_otp_enabled,$disabled,1); 
													$counter1 	  =  !MoUtility::isBlank($form_results['counter']) ? max($form_results['counter']-1,0) : 0 ;

echo'											</li>
												<li>'. mo_( "Click on the Save Button below to save your settings" ).'</li>
											</ol>
									</div>
									<p><input type="radio" '.$disabled.' id="fcpremium_phone" class="app_enable" data-toggle="fcpp_instructions" name="mo_customer_validation_fcpremium_enable_type" value="'.$fcpremium_type_phone.'"
										'.( $fcpremium_enabled_type == $fcpremium_type_phone ? "checked" : "").' />
										<strong>'. mo_( "Enable Phone Verification" ).'</strong>
									</p>
									<div '.($fcpremium_enabled_type != $fcpremium_type_phone ? "hidden" : "").' class="mo_registration_help_desc" id="fcpp_instructions" >
											'. mo_( "Follow the following steps to enable Phone Verification for FormCraft" ).': 
											<ol>
												<li><a href="'.$fcpremium_list.'" target="_blank">'. mo_( "Click Here" ).'</a> '. mo_( " to see your list of forms" ).'</li>
												<li>'. mo_( "Click on the form to edit it." ).'</li>
												<li>'. mo_( "Add a Phone Field to your form. Note the Label of the phone field." ).'</li>
												<li>'. mo_( "Add an Verification Field to your form where users will enter the OTP received. Note the Label of the verification field." ).'</li>
												<li>'. mo_( "Enter your Form ID, the label of the Email Field and Verification Field below" ).':<br>
													<br/>'. mo_( "Add Form " ).': <input type="button"  value="+" '. $disabled .' onclick="add_fcp_form(\'phone\',2);" class="button button-primary" />&nbsp;
													<input type="button" value="-" '. $disabled .' onclick="remove_fcp_form(2);" class="button button-primary" /><br/><br/>';
													
													$form_results = get_formcraft_premium_form_list($fcpremium_otp_enabled,$disabled,2); 
													$counter2 	  =  !MoUtility::isBlank($form_results['counter']) ? max($form_results['counter']-1,0) : 0 ;

echo'											</li>
												<li>'. mo_( "Click on the Save Button below to save your settings" ).'</li>
											</ol>
									</div>
								</div>
							</div>';

echo 						'<script>
								var countfcp1, countfcp2;
								function add_fcp_form(t,n){
									var countFcpIdpAttr = this["countfcp"+n];
									var hidden1="",hidden2="",space="";
									if(n==1)
										hidden2 = "hidden";
									if(n==2)
										hidden1 = "hidden";
									countFcpIdpAttr += 1;
									var sel = "<div id=\'fcp_row"+n+"_"+countFcpIdpAttr+"\'> '.mo_( "Form ID" ).': <input id=\'fcpremium_"+n+"_"+countFcpIdpAttr+"\' class=\'field_data\' name=\'fcpremium[form][]\' type=\'text\' value=\'\'/> <span "+hidden1+" >&nbsp;'.mo_( "Email Field Label" ).': <input id=\'fcpremium_email_"+n+"_"+countFcpIdpAttr+"\'  class=\'field_data\' name=\'fcpremium[emailkey][]\' type=\'text\' value=\'\'></span> <span "+hidden2+">"+space+"'.mo_( "Phone Field Label" ).': <input id=\'fcpremium_phone_"+n+"_"+countFcpIdpAttr+"\' class=\'field_data\' name=\'fcpremium[phonekey][]\' type=\'text\' value=\'\'></span> <span>&nbsp;'.mo_( "Verification Field Label" ).': <input id=\'fcpremium_verify_"+n+"_"+countFcpIdpAttr+"\'  class=\'field_data\' name=\'fcpremium[verifyKey][]\' type=\'text\' value=\'\'></span> </div>"
									if(countFcpIdpAttr!=0)
										$mo(sel).insertAfter($mo(\'#fcp_row\'+n+\'_\'+(countFcpIdpAttr-1)+\'\'));
									this["countfcp"+n]=countFcpIdpAttr;
								}
								function remove_fcp_form(){
									var countFcpIdpAttr =   Math.max(this["countfcp1"],this["countfcp2"]);
									if(countFcpIdpAttr != 0){
										$mo("#fcp_row1_" + countFcpIdpAttr).remove();
										$mo("#fcp_row2_" + countFcpIdpAttr).remove();
										$mo("#fcp_row3_" + countFcpIdpAttr).remove();
										countFcpIdpAttr -= 1;
										this["countfcp1"]=this["countfcp2"]=countFcpIdpAttr;
									}
								}
								jQuery(document).ready(function(){  countfcp1 = '. $counter1 .'; countfcp2 = ' .$counter2. '; });
							</script>';


