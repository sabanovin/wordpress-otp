<?php

		$handler 				  = WooCommerceRegistrationForm::instance();
	$woocommerce_registration = (Boolean) $handler->isFormEnabled()  ? "checked" : "";
	$wc_hidden 				  = $woocommerce_registration=="checked" ? "" : "hidden";
	$wc_enable_type			  = $handler->getOtpTypeEnabled();
	$wc_restrict_duplicates   = (Boolean) $handler->restrictDuplicates() ? "checked" : "";
	$wc_reg_type_phone 		  = $handler->getPhoneHTMLTag();
	$wc_reg_type_email 		  = $handler->getEmailHTMLTag();
	$wc_reg_type_both 		  = $handler->getBothHTMLTag();
	$form_name                = $handler->getFormName();
	$redirect_page            = $handler->redirectToPage();
	$redirect_page_id         = MoUtility::isBlank($redirect_page) ? '' : get_page_by_title($redirect_page)->ID;
	
	include MOV_DIR . 'views/forms/WooCommerceRegistrationForm.php';