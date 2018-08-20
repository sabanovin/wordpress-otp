<?php

$handler          = TmlRegistrationForm::instance();
$tml_enabled      = $handler->isFormEnabled() ? "checked" : "";
$tml_hidden 	  = $tml_enabled == "checked" ? "" : "hidden";
$tml_enable_type  = $handler->getOtpTypeEnabled();
$tml_type_phone   = $handler->getPhoneHTMLTag();
$tml_type_email   = $handler->getEmailHTMLTag();
$tml_type_both    = $handler->getBothHTMLTag();
$form_name        = $handler->getFormName();

include MOV_DIR . 'views/forms/TmlRegistrationForm.php';