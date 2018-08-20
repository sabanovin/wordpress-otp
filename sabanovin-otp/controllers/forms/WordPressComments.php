<?php 

$handler                    = WordPressComments::instance();
$wpcomment_enabled		  	= (Boolean) $handler->isFormEnabled() ? "checked" : "";
$wpcomment_hidden 		  	= $wpcomment_enabled== "checked" ? "" : "hidden";
$wpcomment_type   			= $handler->getOtpTypeEnabled();
$wpComment_force_verify 	= $handler->bypassForLoggedInUsers() ? "checked" : ""	;
$wpcomment_type_phone 		= $handler->getPhoneHTMLTag();
$wpcomment_type_email 		= $handler->getEmailHTMLTag();
$form_name                  = $handler->getFormName();

include MOV_DIR . 'views/forms/WordPressComments.php';