<?php

$handler                  = UltimateMemberRegistrationForm::instance();
$um_enabled 			  = $handler->isFormEnabled() ? "checked" : "";
$um_hidden 				  = $um_enabled=="checked" ? "" : "hidden";
$um_enabled_type		  = $handler->getOtpTypeEnabled();
$um_forms 				  = admin_url().'edit.php?post_type=um_form';
$um_type_phone	 		  = $handler->getPhoneHTMLTag();
$um_type_email	 		  = $handler->getEmailHTMLTag();
$um_type_both	 		  = $handler->getBothHTMLTag();
$um_restrict_duplicates   = $handler->restrictDuplicates()? "checked" : "";
$form_name                = $handler->getFormName();

include MOV_DIR . 'views/forms/UltimateMemberRegistrationForm.php';