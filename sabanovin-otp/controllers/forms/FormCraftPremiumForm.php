<?php

$handler 				   = FormCraftPremiumForm::instance();
$fcpremium_enabled		   = $handler->isFormEnabled() ? "checked" : "";
$fcpremium_hidden		   = $fcpremium_enabled== "checked" ? "" : "hidden";
$fcpremium_enabled_type    = $handler->getOtpTypeEnabled();
$fcpremium_list 		   = admin_url().'admin.php?page=formcraft_admin';
$fcpremium_otp_enabled     = $handler->getFormDetails();
$fcpremium_type_phone 	   = $handler->getPhoneHTMLTag();
$fcpremium_type_email 	   = $handler->getEmailHTMLTag();
$form_name                 = $handler->getFormName();

include MOV_DIR . 'views/forms/FormCraftPremiumForm.php';


function get_formcraft_premium_form_list($fc_premium_otp_enabled,$disabled,$key)
{
	$keyunter = 0;
	if(!MoUtility::isBlank($fc_premium_otp_enabled))
	{
		foreach ($fc_premium_otp_enabled as $form_id=>$form) 
		{
			echo '<div id="fcp_row'.$key.'_'.$keyunter.'">
					'.mo_("Form ID").': <input class="field_data" id="fcpremium_'.$key.'_'.$keyunter.'" name="fcpremium[form][]" type="text" value="'.$form_id.'">&nbsp;';
			echo '<span '.($key==2 ? 'hidden' : '' ).'>&nbsp;'.mo_("Email Field Label").': <input class="field_data" id="fcpremium_email_'.$key.'_'.$keyunter.'" name="fcpremium[emailkey][]" type="text" value="'.$form['email_label'].'"></span>';
			echo '<span '.($key==1 ? 'hidden' : '' ).'>'.mo_("Phone Field Label").': <input class="field_data" id="fcpremium_phone_'.$key.'_'.$keyunter.'" name="fcpremium[phonekey][]" type="text" value="'.$form['phone_label'].'"></span>';
			echo '<span>&nbsp; '.mo_("Verification Field Label").': <input class="field_data" id="fcpremium_verify_'.$key.'_'.$keyunter.'" name="fcpremium[verifyKey][]" type="text" value="'.$form['verify_label'].'"></span>';
			echo '</div>';
			$keyunter+=1;
		}
	}
	else
	{
		echo '<div id="fcp_row'.$key.'_0"> 
			'.mo_("Form ID").': <input id="fcpremium_'.$key.'_0" class="field_data"  name="fcpremium[form][]" type="text" value="">&nbsp;';
		echo '<span '.($key==2 ? 'hidden' : '' ).'>&nbsp;'.mo_("Email Field Label").': <input id="fcpremium_email_'.$key.'_0" class="field_data" name="fcpremium[emailkey][]" type="text" value=""></span>';
		echo '<span '.($key==1 ? 'hidden' : '' ).'>'.mo_("Phone Field Label").': <input id="fcpremium_phone_'.$key.'_0" class="field_data"  name="fcpremium[phonekey][]" type="text" value=""></span>';
		echo '<span>&nbsp; '.mo_("Verification Field Label").': <input class="field_data" id="fcpremium_verify_'.$key.'_0" name="fcpremium[verifyKey][]" type="text" value=""></span>';
		echo '</div>';
	}
	$result['counter']	 = $keyunter;
	return $result;
}