<?php

echo' 	<div class="mo_otp_form"><input type="checkbox" '.$disabled.' id="crf_default" class="app_enable" data-toggle="crf_default_options" name="mo_customer_validation_crf_default_enable" value="1"
				'.$crf_enabled.' /><strong>'. $form_name . ' <i>( RegistrationMagic )</i></strong>';

						get_plugin_form_link(MoConstants::CRF_FORM_ENABLE);

echo'			<div class="mo_registration_help_desc" '.$crf_hidden.' id="crf_default_options">
					<b>'. mo_( "Choose between Phone or Email Verification").'</b>
					<p><input type="radio" '.$disabled.' id="crf_phone" data-toggle="crf_phone_instructions" class="form_options app_enable" name="mo_customer_validation_crf_enable_type" value="'.$crf_type_phone.'"
						'.( $crf_enable_type == $crf_type_phone ? "checked" : "" ).' />
							<strong>'. mo_( "Enable Phone Verification").'</strong>';

echo'					<div '.($crf_enable_type != $crf_type_phone ? "hidden" :"").' id="crf_phone_instructions" class="mo_registration_help_desc">
							'. mo_( "Follow the following steps to enable Phone Verification").':
							<ol>
								<li><a href="'.$crf_form_list.'" target="_blank">'. mo_( "Click Here").'</a> '. mo_( " to see your list of forms").'</li>
								<li>'. mo_( "Click on <b>fields</b> link of your form to see <i>special field</i> list of fields.").'</li>
								<li>'. mo_( "Choose <b>phone number / mobile number </b> field from the list.").'</li>
								<li>'. mo_( "Enter the <b>Label</b> of your new field. Keep this handy as you will need it later.").'</li>
								<li>'. mo_( "Under RULES section check the box which says <b>Is Required</b>.").'</li>
								<li>'. mo_( "Click on <b>Save</b> button to save your new field.").'<br/>
								<br/>'. mo_( "Add Form" ).' : <input type="button"  value="+" '. $disabled .' onclick="add_crfform(\'phone\',2);" class="button button-primary" />&nbsp;
								<input type="button" value="-" '. $disabled .' onclick="remove_crfform(2);" class="button button-primary" /><br/><br/>';
								
								$form_results = get_crf_form_list($crf_form_otp_enabled,$disabled,2); 
								$crfcounter2 	  = !MoUtility::isBlank($form_results['counter']) ? max($form_results['counter']-1,0) : 0 ;
echo'											
								</li>								
								<li>'.mo_( "Click on the Save Button to save your settings and keep a track of your Form Ids." ).'</li>
							</ol>
						</div>
					</p>
					<p><input type="radio" '.$disabled.' id="crf_email" data-toggle="crf_email_instructions" class="form_options app_enable" name="mo_customer_validation_crf_enable_type" value="'.$crf_type_email.'"
						'.( $crf_enable_type == $crf_type_email ? "checked" : "").' />
						<strong>'. mo_( "Enable Email Verification").'</strong>
					</p>
					<div '.($crf_enable_type != $crf_type_email ? "hidden" :"").' id="crf_email_instructions" class="crf_form mo_registration_help_desc">
						<ol>
							<li><a href="'.$crf_form_list.'" target="_blank">'. mo_( "Click Here").'</a> '. mo_( " to see your list of forms").'</li>
							<li>'. mo_( "Click on <b>fields</b> link of your form to see <i>special field</i> list of fields.").'</li>
							<li>'. mo_( "Choose <b>email</b> field from the list.").'</li>
							<li>'. mo_( "Enter the <b>Label</b> of your new field. Keep this handy as you will need it later.").'</li>
							<li>'. mo_( "Under RULES section check the box which says <b>Is Required</b>.").'</li>
							<li>'. mo_( "Click on <b>Save</b> button to save your new field.").'<br/>
							<br/>'. mo_( "Add Form" ).' : <input type="button"  value="+" '. $disabled .' onclick="add_crfform(\'email\',1);" class="button button-primary"/>&nbsp;
								<input type="button" value="-" '. $disabled .' onclick="remove_crfform(1);" class="button button-primary" /><br/><br/>';
								
								$form_results = get_crf_form_list($crf_form_otp_enabled,$disabled,1); 
								$crfcounter1 	  =  !MoUtility::isBlank($form_results['counter']) ? max($form_results['counter']-1,0) : 0 ;

echo                        '</li>
						
							
							<li>'.mo_( "Click on the Save Button to save your settings and keep a track of your Form Ids." ).'</li>
						</ol>
					</div>
					<p><input type="radio" '.$disabled.' id="crf_both" data-toggle="crf_both_instructions"  class="form_options app_enable" name="mo_customer_validation_crf_enable_type" value="'.$crf_type_both.'"
						'.( $crf_enable_type == $crf_type_both? "checked" : "" ).' />
						<strong>'. mo_( "Let the user choose").'</strong>';

						mo_draw_tooltip(MoMessages::showMessage('INFO_HEADER'),MoMessages::showMessage('ENABLE_BOTH_BODY'));

echo'				<div '.($crf_enable_type != $crf_type_both ? "hidden" :"").' id="crf_both_instructions" class="mo_registration_help_desc">
						'. mo_( "Follow the following steps to enable both Email and Phone Verification").':
						<ol>
							<li><a href="'.$crf_form_list.'" target="_blank">'. mo_( "Click Here").'</a> '. mo_( " to see your list of forms").'</li>
							<li>'. mo_( "Click on <b>fields</b> link of your form to see <i>special field</i> list of fields.").'</li>
							<li>'. mo_( "Choose <b>phone number / mobile number </b> field from the list.").'</li>
							<li>'. mo_( "Enter the <b>Label</b> of your new field. Keep this handy as you will need it later.").'</li>
							<li>'. mo_( "Under RULES section check the box which says <b>Is Required</b>.").'</li>
							<li>'. mo_( "Click on <b>Save</b> button to save your new field.").'<br/>
							<br/>'. mo_( "Add Form" ).' : <input type="button"  value="+" '. $disabled .' onclick="add_crfform(\'both\',3);" class="button button-primary"/>&nbsp;
								<input type="button" value="-" '. $disabled .' onclick="remove_crfform(3);" class="button button-primary" /><br/><br/>';
								
								$form_results = get_crf_form_list($crf_form_otp_enabled,$disabled,3); 
								$crfcounter3	  =  !MoUtility::isBlank($form_results['counter']) ? max($form_results['counter']-1,0) : 0 ;

echo                        '</li>
						
							
							<li>'.mo_( "Click on the Save Button to save your settings and keep a track of your Form Ids." ).'</li>
						</ol>
					</div>
				</p>
			</div>
		</div>';

echo 				'<script>
						var crfcount1, crfcount2, crfcount3;
						function add_crfform(t,n){
							var countIdpAttr = this["crfcount"+n];
							var hidden1="",hidden2="",space="",both="";
							if(n==1) hidden2 = "hidden";
							if(n==2) hidden1 = "hidden";
							if(n!=3) space = "&nbsp;";
							if(n==3) both = "both_";
							countIdpAttr += 1;
							var sel = "<div id=\'crfrow"+n+"_"+countIdpAttr+"\'> '.mo_( "Form ID" ).': <input id=\'crf_form_"+n+"_"+countIdpAttr+"\' class=\'field_data\' name=\'crf_form[form][]\' type=\'text\' value=\'\'/> <span "+hidden1+" >&nbsp;'.mo_( "Email Field Label" ).': <input id=\'crf_form_email_"+n+"_"+countIdpAttr+"\'  class=\'field_data\' name=\'crf_form[emailkey][]\' type=\'text\' value=\'\'></span> <span "+hidden2+">"+space+"'.mo_( "Phone Field Label" ).': <input id=\'crf_form_phone_"+n+"_"+countIdpAttr+"\' class=\'field_data\' name=\'crf_form[phonekey][]\' type=\'text\' value=\'\'></span></div>"
							if(countIdpAttr!=0)
								$mo(sel).insertAfter($mo(\'#crfrow\'+n+\'_\'+(countIdpAttr-1)+\'\'));
							this["crfcount"+n]=countIdpAttr;
						}
						function remove_crfform(){
							var countIdpAttr =   Math.max(this["crfcount1"],this["crfcount2"],this["crfcount3"]);
							if(countIdpAttr != 0){
								$mo("#crfrow1_" + countIdpAttr).remove();
								$mo("#crfrow2_" + countIdpAttr).remove();
								$mo("#crfrow3_" + countIdpAttr).remove();
								countIdpAttr -= 1;
								this["crfcount3"]=this["crfcount1"]=this["crfcount2"]=countIdpAttr;
							}
						}
						jQuery(document).ready(function(){  crfcount1 = '. $crfcounter1 .'; crfcount2 = ' .$crfcounter2. '; crfcount3 = ' .$crfcounter3. ' });
					</script>';