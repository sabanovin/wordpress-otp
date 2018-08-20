<?php

$handler                  = WooCommerceCheckOutForm::instance();
$wc_checkout 			  = (Boolean) $handler->isFormEnabled() ? "checked" : "";
$wc_checkout_hidden		  = $wc_checkout=="checked" ? "" : "hidden";
$wc_checkout_enable_type  = $handler->getOtpTypeEnabled();
$guest_checkout 		  = (Boolean) $handler->isGuestCheckoutOnlyEnabled()  ? "checked" : "";
$checkout_button 		  = (Boolean) $handler->showButtonInstead(0) ? "checked" : "";
$checkout_popup 		  = (Boolean) $handler->isPopUpEnabled()  ? "checked" : "";
$checkout_payment_plans   = $handler->getPaymentMethods();
$checkout_selection       = (Boolean) $handler->isSelectivePaymentEnabled() ? "checked" : "";
$checkout_selection_hidden= $checkout_selection=="checked" ? "" : "hidden";
$wc_type_phone 			  = $handler->getPhoneHTMLTag();
$wc_type_email 			  = $handler->getEmailHTMLTag();
$button_text              = $handler->getButtonText();
$form_name                = $handler->getFormName();

include MOV_DIR . 'views/forms/WooCommerceCheckOutForm.php';