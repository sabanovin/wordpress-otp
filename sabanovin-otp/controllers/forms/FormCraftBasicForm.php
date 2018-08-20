<?php

$handler 				  = FormCraftBasicForm::instance();
$formcraft_enabled		  = $handler->isFormEnabled() ? "checked" : "";
$formcraft_hidden		  = $formcraft_enabled== "checked" ? "" : "hidden";
$formcraft_enabled_type   = $handler->getOtpTypeEnabled();
$formcraft_list 		  = admin_url().'admin.php?page=formcraft_basic_dashboard';
$formcraft_otp_enabled    = $handler->getFormDetails();
$formcarft_type_phone 	  = $handler->getPhoneHTMLTag();
$formcarft_type_email 	  = $handler->getEmailHTMLTag();
$form_name                = $handler->getFormName();

include MOV_DIR . 'views/forms/FormCraftBasicForm.php';


function get_formcraft_basic_form_list($formcraft_otp_enabled,$disabled,$key)
{
	$keyunter = 0;
	if(!MoUtility::isBlank($formcraft_otp_enabled))
	{
		foreach ($formcraft_otp_enabled as $form_id=>$form) 
		{
			echo '<div id="fc_row'.$key.'_'.$keyunter.'">
					'.mo_( "Form ID").': <input class="field_data" id="formcraft_'.$key.'_'.$keyunter.'" name="formcraft[form][]" type="text" value="'.$form_id.'">&nbsp;';
			echo '<span '.($key==2 ? 'hidden' : '' ).'>&nbsp;'.mo_( "Email Field Label").': <input class="field_data" id="formcraft_email_'.$key.'_'.$keyunter.'" name="formcraft[emailkey][]" type="text" value="'.$form['email_label'].'"></span>';
			echo '<span '.($key==1 ? 'hidden' : '' ).'>'.mo_( "Phone Field Label").': <input class="field_data" id="formcraft_phone_'.$key.'_'.$keyunter.'" name="formcraft[phonekey][]" type="text" value="'.$form['phone_label'].'"></span>';
			echo '<span>&nbsp; '.mo_( "Verification Field Label").': <input class="field_data" id="formcraft_verify_'.$key.'_'.$keyunter.'" name="formcraft[verifyKey][]" type="text" value="'.$form['verify_label'].'"></span>';
			echo '</div>';
			$keyunter+=1;
		}
	}
	else
	{
		echo '<div id="fc_row'.$key.'_0"> 
			'.mo_( "Form ID").': <input id="formcraft_'.$key.'_0" class="field_data"  name="formcraft[form][]" type="text" value="">&nbsp;';
		echo '<span '.($key==2 ? 'hidden' : '' ).'>&nbsp;'.mo_( "Email Field Label").': <input id="formcraft_email_'.$key.'_0" class="field_data" name="formcraft[emailkey][]" type="text" value=""></span>';
		echo '<span '.($key==1 ? 'hidden' : '' ).'>'.mo_( "Phone Field Label").': <input id="formcraft_phone_'.$key.'_0" class="field_data"  name="formcraft[phonekey][]" type="text" value=""></span>';
		echo '<span>&nbsp; '.mo_( "Verification Field Label").': <input class="field_data" id="formcraft_verify_'.$key.'_0" name="formcraft[verifyKey][]" type="text" value=""></span>';
		echo '</div>';
	}
	$result['counter']	 = $keyunter;
	return $result;
}