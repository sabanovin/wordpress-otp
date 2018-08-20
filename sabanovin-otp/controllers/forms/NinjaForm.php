<?php

$handler 				  = NinjaForm::instance();
$ninja_form_enabled		  = $handler->isFormEnabled() ? "checked" : "";
$ninja_form_hidden		  = $ninja_form_enabled== "checked" ? "" : "hidden";
$ninja_form_enabled_type  = $handler->getOtpTypeEnabled();
$ninja_form_list 		  = admin_url().'admin.php?page=ninja-forms';
$ninja_form_otp_enabled   = $handler->getFormDetails();
$ninja_form_type_phone 	  = $handler->getPhoneHTMLTag();
$ninja_form_type_email 	  = $handler->getEmailHTMLTag();
$ninja_form_type_both 	  = $handler->getBothHTMLTag();
$form_name                = $handler->getFormName();

include MOV_DIR . 'views/forms/NinjaForm.php';


function get_nf_form_list($ninja_form_otp_enabled,$disabled,$key)
{
	$keyunter = 0;
	if(!MoUtility::isBlank($ninja_form_otp_enabled))
	{
		foreach ($ninja_form_otp_enabled as $form_id=>$ninja_form) 
		{
			echo '<div id="row'.$key.'_'.$keyunter.'">
					'.mo_("Form ID").': <input class="field_data" id="ninja_form_'.$key.'_'.$keyunter.'" name="ninja_form[form][]" type="text" value="'.$form_id.'">&nbsp;';
			echo '<span '.($key==2 ? 'hidden' : '' ).'> '.mo_("Email Field ID").': <input class="field_data" id="ninja_form_email_'.$key.'_'.$keyunter.'" name="ninja_form[emailkey][]" type="text" value="'.$ninja_form['emailkey'].'"></span>';
			echo '<span '.($key==1 ? 'hidden' : '' ).'> '.mo_("Phone Field ID").': <input class="field_data" id="ninja_form_phone_'.$key.'_'.$keyunter.'" name="ninja_form[phonekey][]" type="text" value="'.$ninja_form['phonekey'].'"></span>';
			echo '</div>';
			$keyunter+=1;
		}
	}
	else
	{
		echo '<div id="row'.$key.'_0"> 
					'.mo_("Form ID").': <input id="ninja_form_'.$key.'_0" class="field_data"  name="ninja_form[form][]" type="text" value="">&nbsp;';
		echo '<span '.($key==2 ? 'hidden' : '' ).'> '.mo_("Email Field ID").': <input id="ninja_form_email_'.$key.'_0" class="field_data" name="ninja_form[emailkey][]" type="text" value=""></span>';
		echo '<span '.($key==1 ? 'hidden' : '' ).'> '.mo_("Phone Field ID").': <input id="ninja_form_phone_'.$key.'_0" class="field_data"  name="ninja_form[phonekey][]" type="text" value=""></span>';
		echo '</div>';
	}
	$result['counter']	 = $keyunter;
	return $result;
}