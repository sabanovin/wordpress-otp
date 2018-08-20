<?php

$handler                = UltimateMemberLoginForm::instance();
$um_login_enabled 		= (Boolean) $handler->isFormEnabled() ? "checked" : "";
$um_login_hidden 		= $um_login_enabled == "checked" ? "" : "hidden";
$um_login_enabled_type 	= (Boolean) $handler->savePhoneNumbers() ? "checked" : "";
$um_login_field_key    	= $handler->getPhoneKeyDetails();
$um_login_admin			= (Boolean) $handler->byPassCheckForAdmins() ? "checked" : "";
$um_login_with_phone 	= (Boolean) $handler->allowLoginThroughPhone() ? "checked" : "";
$um_handle_duplicates   = (Boolean) $handler->restrictDuplicates() ? "checked" : "";
$um_enabled_type        = $handler->getOtpTypeEnabled();
$um_phone_type          = $handler->getPhoneHTMLTag();
$um_email_type          = $handler->getEmailHTMLTag();
$form_name              = $handler->getFormName();


include MOV_DIR . 'views/forms/UltimateMemberLoginForm.php';