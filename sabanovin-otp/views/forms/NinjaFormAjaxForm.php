<?php

echo'	<div class="mo_otp_form"><input type="checkbox" '.$disabled.' id="ninja_form" class="app_enable" data-toggle="ninja_ajax_form_options" name="mo_customer_validation_nja_enable" value="1"
										'.$ninja_ajax_form_enabled.' /><strong>'. $form_name .'</strong>';
										
									get_plugin_form_link(MoConstants::NINJA_FORMS_LINK);								 
																	
echo'							<div class="mo_registration_help_desc" '.$ninja_ajax_form_hidden.' id="ninja_ajax_form_options">
									<p><input type="radio" '.$disabled.' id="ninja_ajax_form_email" class="app_enable" data-toggle="nfae_instructions" name="mo_customer_validation_nja_enable_type" value="'.$ninja_ajax_form_type_email.'"
										'.( $ninja_ajax_form_enabled_type == $ninja_ajax_form_type_email ? "checked" : "").' />
										<strong>'. mo_( "Enable Email Verification" ).'</strong>
									</p>
									<div '.($ninja_ajax_form_enabled_type != $ninja_ajax_form_type_email ? "hidden" :"").' class="mo_registration_help_desc" id="nfae_instructions" >
											'. mo_( "Follow the following steps to enable Email Verification for" ).' Ninja Form: 
											<ol>
												<li><a href="'.$ninja_ajax_form_list.'" target="_blank">'. mo_( "Click Here" ).'</a> '. mo_( " to see your list of forms" ).'</li>
												<li>'. mo_( "Click on the <b>Edit</b> option of your ninja form." ).'</li>
												<li>'. mo_( "Add an Email Field to your form. Note the Field Key of the email field." ).'</li>
												<li>'. mo_( "Add an Verification Field to your form where users will enter the OTP received. Note the Field Key of the verification field." ).'</li>
												<li>'. mo_( "Enter your Form ID, the Email Field Key and the Verification Field Key below" ).':<br>
													<br/>'. mo_( "Add Form " ).': <input type="button"  value="+" '. $disabled .' onclick="add_ninja_form(\'email\',1);" class="button button-primary" />&nbsp;
													<input type="button" value="-" '. $disabled .' onclick="remove_ninja_form(1);" class="button button-primary" /><br/><br/>';

													$form_results = get_nfa_form_list($ninja_ajax_form_otp_enabled,$disabled,1); 
													$counter1 	  =  !MoUtility::isBlank($form_results['counter']) ? max($form_results['counter']-1,0) : 0 ;

echo'											</li>
												<li>'. mo_( "Click on the Save Button below to save your settings" ).'</li>
											</ol>
									</div>
									<p><input type="radio" '.$disabled.' id="ninja_ajax_form_phone" class="app_enable" data-toggle="nfap_instructions" name="mo_customer_validation_nja_enable_type" value="'.$ninja_ajax_form_type_phone.'"
										'.( $ninja_ajax_form_enabled_type == $ninja_ajax_form_type_phone ? "checked" : "").' />
										<strong>'. mo_( "Enable Phone Verification" ).'</strong>
									</p>
									<div '.($ninja_ajax_form_enabled_type != $ninja_ajax_form_type_phone ? "hidden" : "").' class="mo_registration_help_desc" id="nfap_instructions" >
											'. mo_( "Follow the following steps to enable Phone Verification for Ninja Form" ).': 
											<ol>
												<li><a href="'.$ninja_ajax_form_list.'" target="_blank">'. mo_( "Click Here" ).'</a> '. mo_( " to see your list of forms" ).'</li>
												<li>'. mo_( "Click on the <b>Edit</b> option of your ninja form." ).'</li>
												<li>'. mo_( "Add an Phone Field to your form. Note the Field Key of the phone field." ).'</li>
												<li>'. mo_( "Make sure you have set the Input Mask type to None for the phone field." ).'</li>
												<li>'. mo_( "Add an Verification Field to your form where users will enter the OTP received. Note the Field Key of the verification field." ).'</li>
												<li>'. mo_( "Enter your Form ID, the Phone Field Key and the Verification Field Key below" ).':<br>
													<br/>'. mo_( "Add Form " ).': <input type="button"  value="+" '. $disabled .' onclick="add_ninja_form(\'phone\',2);" class="button button-primary" />&nbsp;
													<input type="button" value="-" '. $disabled .' onclick="remove_ninja_form(2);" class="button button-primary" /><br/><br/>';
													
													$form_results = get_nfa_form_list($ninja_ajax_form_otp_enabled,$disabled,2); 
													$counter2 	  = !MoUtility::isBlank($form_results['counter']) ? max($form_results['counter']-1,0) : 0 ;
echo'											</li>
												<li>'. mo_( "Click on the Save Button below to save your settings" ).'</li>
											</ol>
									</div>
									<p style="margin-left:2%;">
                                        <i><b>'.mo_("Verification Button text").':</b></i>
                                        <input class="mo_registration_table_textbox" name="mo_customer_validation_nja_button_text" type="text" value="'.$button_text.'">					
                                    </p>
								</div>
							</div>';

echo 						'<script>
								var countnja1, countnja2;
								function add_ninja_form(t,n){
									var countNjaIdpAttr = this["countnja"+n];
									var hidden1="",hidden2="",space="";
									if(n==1)
										hidden2 = "hidden";
									if(n==2)
										hidden1 = "hidden";
									countNjaIdpAttr += 1;
									var sel = "<div id=\'ajax_row"+n+"_"+countNjaIdpAttr+"\'> '.mo_( "Form ID" ).': <input id=\'ninja_ajax_form_"+n+"_"+countNjaIdpAttr+"\' class=\'field_data\' name=\'ninja_ajax_form[form][]\' type=\'text\' value=\'\'/> <span "+hidden1+" >&nbsp;'.mo_( "Email Field Key" ).': <input id=\'ninja_ajax_form_email_"+n+"_"+countNjaIdpAttr+"\'  class=\'field_data\' name=\'ninja_ajax_form[emailkey][]\' type=\'text\' value=\'\'></span> <span "+hidden2+">"+space+"'.mo_( "Phone Field Key" ).': <input id=\'ninja_ajax_form_phone_"+n+"_"+countNjaIdpAttr+"\' class=\'field_data\' name=\'ninja_ajax_form[phonekey][]\' type=\'text\' value=\'\'></span> <span>&nbsp;'.mo_( "Verification Field Key" ).': <input id=\'ninja_ajax_form_verify_"+n+"_"+countNjaIdpAttr+"\'  class=\'field_data\' name=\'ninja_ajax_form[verifyKey][]\' type=\'text\' value=\'\'></span> </div>"
									if(countNjaIdpAttr!=0)
										$mo(sel).insertAfter($mo(\'#ajax_row\'+n+\'_\'+(countNjaIdpAttr-1)+\'\'));
									this["countnja"+n]=countNjaIdpAttr;
								}
								function remove_ninja_form(){
									var countNjaIdpAttr =   Math.max(this["countnja1"],this["countnja2"]);
									if(countNjaIdpAttr != 0){
										$mo("#ajax_row1_" + countNjaIdpAttr).remove();
										$mo("#ajax_row2_" + countNjaIdpAttr).remove();
										$mo("#ajax_row3_" + countNjaIdpAttr).remove();
										countNjaIdpAttr -= 1;
										this["countnja1"]=this["countnja2"]=countNjaIdpAttr;
									}
								}
								jQuery(document).ready(function(){  countnja1 = '. $counter1 .'; countnja2 = ' .$counter2. '; });
							</script>';


