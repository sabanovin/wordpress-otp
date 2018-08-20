<?php

$handler                 = UltimateProRegistrationForm::instance();
$ultipro_enabled 		 = (Boolean) $handler->isFormEnabled() ? "checked" : "";
$ultipro_hidden 		 = $ultipro_enabled== "checked" ? "" : "hidden";
$ultipro_enabled_type 	 = $handler->getOtpTypeEnabled();
$umpro_custom_field_list = admin_url().'admin.php?page=ihc_manage&tab=register&subtab=custom_fields';
$umpro_type_phone 		 = $handler->getPhoneHTMLTag();
$umpro_type_email 		 = $handler->getEmailHTMLTag();
$form_name               = $handler->getFormName();

include MOV_DIR . 'views/forms/UltimateProRegistrationForm.php';
