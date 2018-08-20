<?php

echo'	<div class="mo_otp_form"><input type="checkbox" '.$disabled.' id="ninja_form" class="app_enable" data-toggle="ninja_form_options" name="mo_customer_validation_ninja_form_enable" value="1"
										'.$ninja_form_enabled.' /><strong>'. $form_name .'</strong>';
										
									get_plugin_form_link(MoConstants::NINJA_FORMS_LINK);								 
																	
echo'							<div class="mo_registration_help_desc" '.$ninja_form_hidden.' id="ninja_form_options">
									<p><input type="radio" '.$disabled.' id="ninja_form_email" class="app_enable" data-toggle="nfe_instructions" name="mo_customer_validation_ninja_form_enable_type" value="'.$ninja_form_type_email.'"
										'.( $ninja_form_enabled_type == $ninja_form_type_email ? "checked" : "").' />
										<strong>'. mo_( "Enable Email Verification" ).'</strong>
									</p>
									<div '.($ninja_form_enabled_type != $ninja_form_type_email ? "hidden" :"").' class="mo_registration_help_desc" id="nfe_instructions" >
											'. mo_( "Follow the following steps to enable Email Verification for Ninja Form" ).': 
											<ol>
												<li><a href="'.$ninja_form_list.'" target="_blank">'. mo_( "Click Here" ).'</a> '. mo_( " to see your list of forms" ).'</li>
												<li>'. mo_( "Click on the <b>Edit</b> option of your ninja form." ).'</li>
												<li>'. mo_( "Add an Email Field to your form. Note the Field Key of the email field." ).'</li>
												<li>'. mo_( "Enter your Form ID and the Email Field ID below" ).':<br>
													<br/>'. mo_( "Add Form " ).': <input type="button"  value="+" '. $disabled .' onclick="add_form(\'email\',1);" class="button button-primary" />&nbsp;
													<input type="button" value="-" '. $disabled .' onclick="remove_form(1);" class="button button-primary" /><br/><br/>';
												
													$form_results = get_nf_form_list($ninja_form_otp_enabled,$disabled,1); 
													$counter1 	  =  !MoUtility::isBlank($form_results['counter']) ? max($form_results['counter']-1,0) : 0 ;

echo'											</li>
												<li>'. mo_( "Click on the Save Button below to save your settings" ).'</li>
											</ol>
									</div>
									<p><input type="radio" '.$disabled.' id="ninja_form_phone" class="app_enable" data-toggle="nfp_instructions" name="mo_customer_validation_ninja_form_enable_type" value="'.$ninja_form_type_phone.'"
										'.( $ninja_form_enabled_type == $ninja_form_type_phone ? "checked" : "").' />
										<strong>'. mo_( "Enable Phone Verification" ).'</strong>
									</p>
									<div '.($ninja_form_enabled_type != $ninja_form_type_phone ? "hidden" : "").' class="mo_registration_help_desc" id="nfp_instructions" >
											'. mo_( "Follow the following steps to enable Phone Verification for Ninja Form" ).': 
											<ol>
												<li><a href="'.$ninja_form_list.'" target="_blank">'. mo_( "Click Here" ).'</a> '. mo_( " to see your list of forms" ).'</li>
												<li>'. mo_( "Click on the <b>Edit</b> option of your ninja form." ).'</li>
												<li>'. mo_( "Add a Phone Field to your form. Note the Field ID of the phone field." ).'</li>
												<li>'. mo_( "Make sure you have set the Input Mask type to None for the phone field." ).'</li>
												<li>'. mo_( "Enter your Form ID and the Email Field ID below" ).':<br>
													<br/>'. mo_( "Add Form " ).': <input type="button"  value="+" '. $disabled .' onclick="add_form(\'phone\',2);" class="button button-primary" />&nbsp;
													<input type="button" value="-" '. $disabled .' onclick="remove_form(2);" class="button button-primary" /><br/><br/>';
													
													$form_results = get_nf_form_list($ninja_form_otp_enabled,$disabled,2); 
													$counter2 	  = !MoUtility::isBlank($form_results['counter']) ? max($form_results['counter']-1,0) : 0 ;
echo'											</li>
												<li>'. mo_( "Click on the Save Button below to save your settings" ).'</li>
											</ol>
									</div>
									<p><input type="radio" '.$disabled.' id="ninja_form_both" class="app_enable" data-toggle="nfb_instructions" name="mo_customer_validation_ninja_form_enable_type" value="'.$ninja_form_type_both.'"
										'.( $ninja_form_enabled_type == $ninja_form_type_both ? "checked" : "").' />
											<strong>'. mo_( "Let the user Choose" ).'</strong>';

										mo_draw_tooltip(MoMessages::showMessage('INFO_HEADER'),MoMessages::showMessage('ENABLE_BOTH_BODY'));

echo								'</p>
									<div '.($ninja_form_enabled_type != $ninja_form_type_both ? "hidden" : "").' class="mo_registration_help_desc" id="nfb_instructions" >
											'. mo_( "Follow the following steps to enable Phone and Email Verification for Ninja Form" ).':
											<ol>
												<li><a href="'.$ninja_form_list.'" target="_blank">Click Here</a> to see your list of forms.</li>
												<li>'. mo_( "Click on the <b>Edit</b> option of your ninja form." ).'</li>
												<li>'. mo_( "Add an Email and Phone Field to your form. Note the Field ID of the fields." ).'</li>
												<li>'. mo_( "Make sure you have set the Input Mask type to None for the phone field." ).'</li>
												<li>'. mo_( "Enter your Form ID, Email Field ID and Phone Field ID below:" ).'<br>
													<br/>'. mo_( "Add Form" ).': <input type="button"  value="+" '. $disabled .' onclick="add_form(\'both\',3);" class="button button-primary" />&nbsp;
													<input type="button" value="-" '. $disabled .' onclick="remove_form(3);" class="button button-primary" /><br/><br/>';
													
													$form_results = get_nf_form_list($ninja_form_otp_enabled,$disabled,3); 
													$counter3	  =  !MoUtility::isBlank($form_results['counter']) ? max($form_results['counter']-1,0) : 0 ;
echo'											</li>
												<li>'. mo_( "Click on the Save Button below to save your settings" ).'</li>
											</ol>
									</div>
								</div>
							</div>';

echo 						'<script>
								var count1, count2, count3;
								function add_form(t,n){
									var countIdpAttr = this["count"+n];
									var hidden1="",hidden2="",space="",both="";
									if(n==1)
										hidden2 = "hidden";
									if(n==2)
										hidden1 = "hidden";
									if(n!=3)
										space = "&nbsp;";
									if(n==3)
										both = "both_";
									countIdpAttr += 1;
									var sel = "<div id=\'row"+n+"_"+countIdpAttr+"\'> '.mo_( "Form ID" ).': <input id=\'ninja_form_"+n+"_"+countIdpAttr+"\' class=\'field_data\' name=\'ninja_form[form][]\' type=\'text\' value=\'\'/> <span "+hidden1+" >&nbsp;'.mo_( "Email Field ID" ).': <input id=\'ninja_form_email_"+n+"_"+countIdpAttr+"\'  class=\'field_data\' name=\'ninja_form[emailkey][]\' type=\'text\' value=\'\'></span> <span "+hidden2+">"+space+"'.mo_( "Phone Field ID" ).': <input id=\'ninja_form_phone_"+n+"_"+countIdpAttr+"\' class=\'field_data\' name=\'ninja_form[phonekey][]\' type=\'text\' value=\'\'></span></div>"
									if(countIdpAttr!=0)
										$mo(sel).insertAfter($mo(\'#row\'+n+\'_\'+(countIdpAttr-1)+\'\'));
									this["count"+n]=countIdpAttr;
								}
								function remove_form(){
									var countIdpAttr =   Math.max(this["count1"],this["count2"],this["count3"]);
									if(countIdpAttr != 0){
										$mo("#row1_" + countIdpAttr).remove();
										$mo("#row2_" + countIdpAttr).remove();
										$mo("#row3_" + countIdpAttr).remove();
										countIdpAttr -= 1;
										this["count3"]=this["count1"]=this["count2"]=countIdpAttr;
									}
								}
								jQuery(document).ready(function(){  count1 = '. $counter1 .'; count2 = ' .$counter2. '; count3 = ' .$counter3. ' });
							</script>';


