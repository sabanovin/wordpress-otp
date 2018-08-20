<?php

$handler                    = UserProRegistrationForm::instance();
$userpro_enabled 		    = $handler->isFormEnabled() ? "checked" : "";
$userpro_hidden 			= $userpro_enabled== "checked" ? "" : "hidden";
$userpro_enabled_type 		= $handler->getOtpTypeEnabled();
$userpro_field_list 		= admin_url().'admin.php?page=userpro&tab=fields';
$automatic_verification		= $handler->disableAutoActivation() ? "checked" : "";
$userpro_type_phone 		= $handler->getPhoneHTMLTag();
$userpro_type_email 		= $handler->getEmailHTMLTag();
$form_name                  = $handler->getFormName();

include MOV_DIR . 'views/forms/UserProRegistrationForm.php';