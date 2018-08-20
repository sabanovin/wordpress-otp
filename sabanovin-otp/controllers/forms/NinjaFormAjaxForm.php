<?php

$handler 						  = NinjaFormAjaxForm::instance();
$ninja_ajax_form_enabled		  = $handler->isFormEnabled() ? "checked" : "";
$ninja_ajax_form_hidden		  	  = $ninja_ajax_form_enabled== "checked" ? "" : "hidden";
$ninja_ajax_form_enabled_type  	  = $handler->getOtpTypeEnabled();
$ninja_ajax_form_list 		      = admin_url().'admin.php?page=ninja-forms';
$ninja_ajax_form_otp_enabled      = $handler->getFormDetails();
$ninja_ajax_form_type_phone 	  = $handler->getPhoneHTMLTag();
$ninja_ajax_form_type_email 	  = $handler->getEmailHTMLTag();
$button_text                      = $handler->getButtonText();
$form_name                        = $handler->getFormName();

include MOV_DIR . 'views/forms/NinjaFormAjaxForm.php';


function get_nfa_form_list($ninja_ajax_form_otp_enabled,$disabled,$key)
{
	$keyunter = 0;
	if(!MoUtility::isBlank($ninja_ajax_form_otp_enabled))
	{
		foreach ($ninja_ajax_form_otp_enabled as $form_id=>$ninja_form) 
		{
			echo '<div id="ajax_row'.$key.'_'.$keyunter.'">
					'.mo_("Form ID").': <input class="field_data" id="ninja_ajax_form_'.$key.'_'.$keyunter.'" name="ninja_ajax_form[form][]" type="text" value="'.$form_id.'">&nbsp;';
			echo '<span '.($key==2 ? 'hidden' : '' ).'>&nbsp;'.mo_("Email Field Key").': <input class="field_data" id="ninja_ajax_form_email_'.$key.'_'.$keyunter.'" name="ninja_ajax_form[emailkey][]" type="text" value="'.$ninja_form['email_show_key'].'"></span>';
			echo '<span '.($key==1 ? 'hidden' : '' ).'>'.mo_("Phone Field Key").': <input class="field_data" id="ninja_ajax_form_phone_'.$key.'_'.$keyunter.'" name="ninja_ajax_form[phonekey][]" type="text" value="'.$ninja_form['phone_show_key'].'"></span>';
			echo '<span>&nbsp; '.mo_("Verification Field Key").': <input class="field_data" id="ninja_ajax_form_verify_'.$key.'_'.$keyunter.'" name="ninja_ajax_form[verifyKey][]" type="text" value="'.$ninja_form['verify_show_key'].'"></span>';
			echo '</div>';
			$keyunter+=1;
		}
	}
	else
	{
		echo '<div id="ajax_row'.$key.'_0"> 
			'.mo_("Form ID").': <input id="ninja_ajax_form_'.$key.'_0" class="field_data"  name="ninja_ajax_form[form][]" type="text" value="">&nbsp;';
		echo '<span '.($key==2 ? 'hidden' : '' ).'>&nbsp;'.mo_("Email Field Key").': <input id="ninja_ajax_form_email_'.$key.'_0" class="field_data" name="ninja_ajax_form[emailkey][]" type="text" value=""></span>';
		echo '<span '.($key==1 ? 'hidden' : '' ).'>'.mo_("Phone Field Key").': <input id="ninja_ajax_form_phone_'.$key.'_0" class="field_data"  name="ninja_ajax_form[phonekey][]" type="text" value=""></span>';
		echo '<span>&nbsp; '.mo_("Verification Field Key").': <input class="field_data" id="ninja_ajax_form_verify_'.$key.'_0" name="ninja_ajax_form[verifyKey][]" type="text" value=""></span>';
		echo '</div>';
	}
	$result['counter']	 = $keyunter;
	return $result;
}