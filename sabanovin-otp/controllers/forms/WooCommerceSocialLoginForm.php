<?php
		
		$handler 			= WooCommerceSocialLoginForm::instance();
	$wc_social_login	= (Boolean) $handler->isFormEnabled() ? "checked" : "";
	$form_name          = $handler->getFormName();
	
	include MOV_DIR . 'views/forms/WooCommerceSocialLoginForm.php';
	