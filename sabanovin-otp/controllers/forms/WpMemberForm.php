<?php

$handler                    = WpMemberForm::instance();
$wp_member_reg_enabled 		= (Boolean) $handler->isFormEnabled()? "checked" : "";
$wp_member_reg_hidden 		= $wp_member_reg_enabled== "checked" ? "" : "hidden";
$wpmember_enabled_type 	 	= $handler->getOtpTypeEnabled();
$wpm_field_list				= admin_url().'admin.php?page=wpmem-settings&tab=fields';
$wpm_type_phone 			= $handler->getPhoneHTMLTag();
$wpm_type_email 			= $handler->getEmailHTMLTag();
$form_name                  = $handler->getFormName();

include MOV_DIR . 'views/forms/WpMemberForm.php';