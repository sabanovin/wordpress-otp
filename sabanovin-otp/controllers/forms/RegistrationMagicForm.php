<?php

$handler 				  = RegistrationMagicForm::instance();
$crf_enabled 			  = $handler->isFormEnabled() ? "checked" : "";
$crf_hidden  			  = $crf_enabled== "checked" ? "" : "hidden";
$crf_enable_type		  = $handler->getOtpTypeEnabled();
$crf_form_list  		  = admin_url().'admin.php?page=rm_form_manage';
$crf_form_otp_enabled     = $handler->getFormDetails();
$crf_type_phone 		  = $handler->getPhoneHTMLTag();
$crf_type_email 		  = $handler->getEmailHTMLTag();
$crf_type_both  		  = $handler->getBothHTMLTag();
$form_name                = $handler->getFormName();

include MOV_DIR .'views/forms/RegistrationMagicForm.php';


function get_crf_form_list($crf_form_otp_enabled,$disabled,$key)
{
	$keyunter = 0;
	if(!MoUtility::isBlank($crf_form_otp_enabled))
	{
		foreach ($crf_form_otp_enabled as $form_id=>$crf_form) 
		{
			echo '<div id="crfrow'.$key.'_'.$keyunter.'">
					'.mo_("Form ID").': <input class="field_data" id="crf_form_'.$key.'_'.$keyunter.'" name="crf_form[form][]" type="text" value="'.$form_id.'">&nbsp;';
			echo '<span '.($key==2 ? 'hidden' : '' ).'> '.mo_("Email Field Label").': <input class="field_data" id="crf_form_email_'.$key.'_'.$keyunter.'" name="crf_form[emailkey][]" type="text" value="'.$crf_form['emailkey'].'"></span>';
			echo '<span '.($key==1 ? 'hidden' : '' ).'> '.mo_("Phone Field Label").': <input class="field_data" id="crf_form_phone_'.$key.'_'.$keyunter.'" name="crf_form[phonekey][]" type="text" value="'.$crf_form['phonekey'].'"></span>';
			echo '</div>';
			$keyunter+=1;
		}
	}
	else
	{
		echo '<div id="crfrow'.$key.'_0"> 
					'.mo_("Form ID").': <input id="crf_form_'.$key.'_0" class="field_data"  name="crf_form[form][]" type="text" value="">&nbsp;';
		echo '<span '.($key==2 ? 'hidden' : '' ).'> '.mo_("Email Field Label").': <input id="crf_form_email_'.$key.'_0" class="field_data" name="crf_form[emailkey][]" type="text" value=""></span>';
		echo '<span '.($key==1 ? 'hidden' : '' ).'> '.mo_("Phone Field Label").': <input id="crf_form_phone_'.$key.'_0" class="field_data"  name="crf_form[phonekey][]" type="text" value=""></span>';
		echo '</div>';
	}
	$result['counter']	 = $keyunter;
	return $result;
}