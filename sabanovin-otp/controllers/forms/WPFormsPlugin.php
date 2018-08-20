<?php

$handler 						  = 	WPFormsPlugin::instance();
$is_wpform_enabled                =     (Boolean) $handler->isFormEnabled()  ? "checked" : "";
$is_wpform_hidden		    	  =     $is_wpform_enabled== "checked" ? "" : "hidden";
$wpform_enabled_type  			  =		$handler->getOtpTypeEnabled();
$wpform_list_of_forms_otp_enabled = 	$handler->getFormDetails();
$wpform_form_list				  = 	admin_url().'admin.php?page=wpforms-overview';
$button_text 					  = 	$handler->getButtonText();
$wpform_phone_type 		          =     $handler->getPhoneHTMLTag();
$wpform_email_type 		          =     $handler->getEmailHTMLTag();
$form_name                        =     $handler->getFormName();
 
include MOV_DIR . 'views/forms/WPFormsPlugin.php';

function get_wpform_list($wpform_list_of_forms_otp_enabled,$disabled,$key)
{
	$keyunter = 0;
	if(!MoUtility::isBlank($wpform_list_of_forms_otp_enabled))
	{	
		foreach ($wpform_list_of_forms_otp_enabled as $form_id=>$wpform) 
		{	
			echo '<div id="ajax_row_wpform'.$key.'_'.$keyunter.'">
					'.mo_("Form ID").': <input class="field_data" id="wp_form_'.$key.'_'.$keyunter.'" name="wpform[form][]" 
                        type="text" value="'.$form_id.'">&nbsp;';
            echo '<span '.($key==2 ? 'hidden' : '' ).'>&nbsp;'.mo_("Email Field Label").': <input class="field_data" 
                id="wp_form_email_'.$key.'_'.$keyunter.'" name="wpform[emailLabel][]" type="text" value="'.$wpform['emailLabel'].'"></span>';
            echo '<span '.($key==1 ? 'hidden' : '' ).'>'.mo_("Phone Field Label").': <input class="field_data" 
				id="wp_form_phone_'.$key.'_'.$keyunter.'" name="wpform[phoneLabel][]" type="text" value="'.$wpform['phoneLabel'].'"></span>';
			echo '<span>'.mo_("Verification Field Label").': <input class="field_data" 
                id="wp_form_verify_'.$key.'_'.$keyunter.'" name="wpform[verifyLabel][]" type="text" value="'.$wpform['verifyLabel'].'"></span>';
			echo '</div>';
			$keyunter+=1;
		}
	}
	else
	{
		echo '<div id="ajax_row_wpform'.$key.'_0"> 
			'.mo_("Form ID").': <input id="wp_form_'.$key.'_0" class="field_data"  name="wpform[form][]" type="text" value="">&nbsp;';
        echo '<span '.($key==2 ? 'hidden' : '' ).'>&nbsp;'.mo_("Email Field Label").': <input id="wp_form_email_'.$key.'_0" class="field_data" 
                name="wpform[emailLabel][]" type="text" value=""></span>';
        echo '<span '.($key==1 ? 'hidden' : '' ).'>'.mo_("Phone Field Label").': <input id="wp_form_phone_'.$key.'_0" class="field_data" 
				name="wpform[phoneLabel][]" type="text" value=""></span>&nbsp;';
		echo '<span>'.mo_("Verification Field Label").': <input id="wp_form_verify_'.$key.'_0" class="field_data" name="wpform[verifyLabel][]" 
				type="text" value=""></span>';
		echo '</div>';
	}
	$result['counter']	 = $keyunter;
	return $result;
}