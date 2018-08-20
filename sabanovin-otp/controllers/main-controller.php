<?php


	$registerd 		= MoUtility::micr();
	$plan       	= MoUtility::micv();
	$disabled  	 	= !$registerd ? "disabled" : "";
	$current_user 	= wp_get_current_user();
	$email 			= get_mo_option("mo_customer_validation_admin_email");
	$phone 			= get_mo_option("mo_customer_validation_admin_phone");
	$controller 	= MOV_DIR . 'controllers/';
	$adminHandler 	= MoOTPActionHandlerHandler::instance();

	include $controller . 'navbar.php';

	echo "<div class='mo-opt-content'>
			<div id='moblock' class='mo_customer_validation-modal-backdrop dashboard'><img src='".MOV_LOADER_URL."'></div>";

	if(isset( $_GET[ 'page' ]))
	{
		switch($_GET['page'])
		{
			case 'mosettings':
				include $controller . 'settings.php';			break;
			case 'messages':
				include $controller . 'messages.php';			break;
			case 'otpaccount':
				include $controller . 'account.php';			break;
			case 'pricing':
				include $controller . 'pricing.php';			break;
			// case 'config':
			// 	include $controller . 'configuration.php';		break;
			case 'otpsettings':
				include $controller . 'otpsettings.php';		break;
			case 'design':
				include $controller . 'design.php';				break;
            // case 'addon':
            //     include $controller . 'add-on.php';             break;
		}

        do_action('mo_otp_verification_add_on_controller');

		if(!in_array($_GET['page'],array("pricing","design"))) {
			include $controller . 'support.php';
		}

		if(in_array($_GET['page'],array("mosettings","otpsettings","messages"))) {
			include $controller . 'floating-box.php';
		}
	}

	echo "</div>";
