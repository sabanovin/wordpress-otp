<?php

echo'	<div class="mo_otp_form"><input type="checkbox" '.$disabled.' id="formcraft" class="app_enable" data-toggle="formcraft_options" name="mo_customer_validation_formcraft_enable" value="1"
										'.$formcraft_enabled.' /><strong>'.$form_name.'</strong>';
										
									get_plugin_form_link(MoConstants::FORMCRAFT_BASIC_LINK);								 
																	
echo'							<div class="mo_registration_help_desc" '.$formcraft_hidden.' id="formcraft_options">
									<p><input type="radio" '.$disabled.' id="formcraft_email" class="app_enable" data-toggle="fcbe_instructions" name="mo_customer_validation_formcraft_enable_type" value="'.$formcarft_type_email.'"
										'.( $formcraft_enabled_type == $formcarft_type_email ? "checked" : "").' />
										<strong>'. mo_( "Enable Email Verification" ).'</strong>
									</p>
									<div '.($formcraft_enabled_type != $formcarft_type_email ? "hidden" :"").' class="mo_registration_help_desc" id="fcbe_instructions" >
											'. mo_( "Follow the following steps to enable Email Verification for FormCraft" ).': 
											<ol>
												<li><a href="'.$formcraft_list.'" target="_blank">'. mo_( "Click Here" ).'</a> '. mo_( " to see your list of forms" ).'</li>
												<li>'. mo_( "Click on the form to edit it." ).'</li>
												<li>'. mo_( "Add an Email Field to your form. Note the Label of the email field." ).'</li>
												<li>'. mo_( "Add an Verification Field to your form where users will enter the OTP received. Note the Label of the verification field." ).'</li>
												<li>'. mo_( "Enter your Form ID, the label of the Email Field and Verification Field below" ).':<br>
													<br/>'. mo_( "Add Form " ).': <input type="button"  value="+" '. $disabled .' onclick="add_fc_form(\'email\',1);" class="button button-primary" />&nbsp;
													<input type="button" value="-" '. $disabled .' onclick="remove_fc_form(1);" class="button button-primary" /><br/><br/>';
												
													$form_results = get_formcraft_basic_form_list($formcraft_otp_enabled,$disabled,1); 
													$counter1 	  =  !MoUtility::isBlank($form_results['counter']) ? max($form_results['counter']-1,0) : 0 ;

echo'											</li>
												<li>'. mo_( "Click on the Save Button below to save your settings" ).'</li>
											</ol>
									</div>
									<p><input type="radio" '.$disabled.' id="formcraft_phone" class="app_enable" data-toggle="fcbp_instructions" name="mo_customer_validation_formcraft_enable_type" value="'.$formcarft_type_phone.'"
										'.( $formcraft_enabled_type == $formcarft_type_phone ? "checked" : "").' />
										<strong>'. mo_( "Enable Phone Verification" ).'</strong>
									</p>
									<div '.($formcraft_enabled_type != $formcarft_type_phone ? "hidden" : "").' class="mo_registration_help_desc" id="fcbp_instructions" >
											'. mo_( "Follow the following steps to enable Phone Verification for FormCraft" ).': 
											<ol>
												<li><a href="'.$formcraft_list.'" target="_blank">'. mo_( "Click Here" ).'</a> '. mo_( " to see your list of forms" ).'</li>
												<li>'. mo_( "Click on the form to edit it." ).'</li>
												<li>'. mo_( "Add a Phone Field to your form. Note the Label of the phone field." ).'</li>
												<li>'. mo_( "Add an Verification Field to your form where users will enter the OTP received. Note the Label of the verification field." ).'</li>
												<li>'. mo_( "Enter your Form ID, the label of the Email Field and Verification Field below" ).':<br>
													<br/>'. mo_( "Add Form " ).': <input type="button"  value="+" '. $disabled .' onclick="add_fc_form(\'phone\',2);" class="button button-primary" />&nbsp;
													<input type="button" value="-" '. $disabled .' onclick="remove_fc_form(2);" class="button button-primary" /><br/><br/>';
													
													$form_results = get_formcraft_basic_form_list($formcraft_otp_enabled,$disabled,2); 
													$counter2 	  =  !MoUtility::isBlank($form_results['counter']) ? max($form_results['counter']-1,0) : 0 ;

echo'											</li>
												<li>'. mo_( "Click on the Save Button below to save your settings" ).'</li>
											</ol>
									</div>
								</div>
							</div>';

echo 						'<script>
								var countfc1, countfc2;
								function add_fc_form(t,n){
									var countFcIdpAttr = this["countfc"+n];
									var hidden1="",hidden2="",space="";
									if(n==1)
										hidden2 = "hidden";
									if(n==2)
										hidden1 = "hidden";
									countFcIdpAttr += 1;
									var sel = "<div id=\'fc_row"+n+"_"+countFcIdpAttr+"\'> '.mo_( "Form ID").': <input id=\'formcraft_"+n+"_"+countFcIdpAttr+"\' class=\'field_data\' name=\'formcraft[form][]\' type=\'text\' value=\'\'/> <span "+hidden1+" >&nbsp;'.mo_( "Email Field Label").': <input id=\'formcraft_email_"+n+"_"+countFcIdpAttr+"\'  class=\'field_data\' name=\'formcraft[emailkey][]\' type=\'text\' value=\'\'></span> <span "+hidden2+">"+space+"'.mo_( "Phone Field Label").': <input id=\'formcraft_phone_"+n+"_"+countFcIdpAttr+"\' class=\'field_data\' name=\'formcraft[phonekey][]\' type=\'text\' value=\'\'></span> <span>&nbsp;'.mo_( "Verification Field Label").': <input id=\'formcraft_verify_"+n+"_"+countFcIdpAttr+"\'  class=\'field_data\' name=\'formcraft[verifyKey][]\' type=\'text\' value=\'\'></span> </div>"
									if(countFcIdpAttr!=0)
										$mo(sel).insertAfter($mo(\'#fc_row\'+n+\'_\'+(countFcIdpAttr-1)+\'\'));
									this["countfc"+n]=countFcIdpAttr;
								}
								function remove_fc_form(){
									var countFcIdpAttr =   Math.max(this["countfc1"],this["countfc2"]);
									if(countFcIdpAttr != 0){
										$mo("#fc_row1_" + countFcIdpAttr).remove();
										$mo("#fc_row2_" + countFcIdpAttr).remove();
										$mo("#fc_row3_" + countFcIdpAttr).remove();
										countFcIdpAttr -= 1;
										this["countfc1"]=this["countfc2"]=countFcIdpAttr;
									}
								}
								jQuery(document).ready(function(){  countfc1 = '. $counter1 .'; countfc2 = ' .$counter2. '; });
							</script>';


