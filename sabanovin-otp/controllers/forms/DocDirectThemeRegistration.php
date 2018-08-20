<?php

$handler                = DocDirectThemeRegistration::instance();
$docdirect_enabled 		= $handler->isFormEnabled() ? "checked" : "";
$docdirect_hidden 		= $docdirect_enabled== "checked" ? "" : "hidden";
$docdirect_enabled_type = $handler->getOtpTypeEnabled();
$docdirect_type_phone 	= $handler->getPhoneHTMLTag();
$docdirect_type_email 	= $handler->getEmailHTMLTag();
$form_name              = $handler->getFormName();

include MOV_DIR . 'views/forms/DocDirectThemeRegistration.php';