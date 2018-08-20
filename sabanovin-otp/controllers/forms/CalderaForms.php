<?php

		$handler 							= CalderaForms::instance();
	$is_caldera_enabled               	= (Boolean) $handler->isFormEnabled()  ? "checked" : "";
	$is_caldera_hidden		    	  	= $is_caldera_enabled== "checked" ? "" : "hidden";
	$caldera_enabled_type  			  	= $handler->getOtpTypeEnabled();
	$caldera_list_of_forms_otp_enabled  = $handler->getFormDetails();
	$caldera_form_list				  	= admin_url().'admin.php?page=caldera-forms';
	$button_text 					  	= $handler->getButtonText();
	$caldera_phone_type 		      	= $handler->getPhoneHTMLTag();	
	$caldera_email_type 		      	= $handler->getEmailHTMLTag();
    $form_name                          = $handler->getFormName();
	
	include MOV_DIR . 'views/forms/CalderaForms.php';

	function get_caldera_list($caldera_list_of_forms_otp_enabled,$disabled,$key)
	{
		$keyunter = 0;
		if(!MoUtility::isBlank($caldera_list_of_forms_otp_enabled))
		{	
			foreach ($caldera_list_of_forms_otp_enabled as $form_id=>$caldera) 
			{	
				echo '<div id="ajax_row_caldera'.$key.'_'.$keyunter.'">
						'.mo_("Form ID").': <input class="field_data" id="wp_form_'.$key.'_'.$keyunter.'" name="caldera[form][]" 
							type="text" value="'.$form_id.'">&nbsp;';
				echo '<span '.($key==2 ? 'hidden' : '' ).'>&nbsp;'.mo_("Email Field ID").': <input class="field_data" 
					id="wp_form_email_'.$key.'_'.$keyunter.'" name="caldera[emailkey][]" type="text" value="'.$caldera['emailkey'].'"></span>';
				echo '<span '.($key==1 ? 'hidden' : '' ).'>'.mo_("Phone Field ID").': <input class="field_data" 
					id="wp_form_phone_'.$key.'_'.$keyunter.'" name="caldera[phonekey][]" type="text" value="'.$caldera['phonekey'].'"></span>';
				echo '<span>'.mo_("Verification Field ID").': <input class="field_data" 
					id="wp_form_verify_'.$key.'_'.$keyunter.'" name="caldera[verifyKey][]" type="text" value="'.$caldera['verifyKey'].'"></span>';
				echo '</div>';
				$keyunter+=1;
			}
		}
		else
		{
			echo '<div id="ajax_row_caldera'.$key.'_0"> 
				'.mo_("Form ID").': <input id="wp_form_'.$key.'_0" class="field_data"  name="caldera[form][]" type="text" value="">&nbsp;';
			echo '<span '.($key==2 ? 'hidden' : '' ).'>&nbsp;'.mo_("Email Field ID").': <input id="wp_form_email_'.$key.'_0" class="field_data" 
					name="caldera[emailkey][]" type="text" value=""></span>';
			echo '<span '.($key==1 ? 'hidden' : '' ).'>'.mo_("Phone Field ID").': <input id="wp_form_phone_'.$key.'_0" class="field_data" 
					name="caldera[phonekey][]" type="text" value=""></span>&nbsp;';
			echo '<span>'.mo_("Verification Field ID").': <input id="wp_form_verify_'.$key.'_0" class="field_data" name="caldera[verifyKey][]" 
					type="text" value=""></span>';
			echo '</div>';
		}
		$result['counter']	 = $keyunter;
		return $result;
	}