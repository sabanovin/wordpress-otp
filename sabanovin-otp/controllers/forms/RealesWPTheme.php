<?php

$handler                = RealesWPTheme::instance();
$reales_enabled      	= $handler->isFormEnabled() ? "checked" : "";
$reales_hidden 	  		= $reales_enabled == "checked" ? "" : "hidden";
$reales_enable_type  	= $handler->getOtpTypeEnabled();
$reales_type_phone 		= $handler->getPhoneHTMLTag();
$reales_type_email 		= $handler->getEmailHTMLTag();
$form_name              = $handler->getFormName();

include MOV_DIR . 'views/forms/RealesWPTheme.php';