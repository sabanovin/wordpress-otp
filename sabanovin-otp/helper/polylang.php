<?php

if(! defined( 'ABSPATH' )) exit;


class PolyLangStrings 
{
	private static $_instance = null;
	
	public static function instance() 
	{
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}

	private function __construct()
	{		
		define("MO_POLY_STRINGS", serialize( array(	

			'OTP_SENT_PHONE' 		=> MoMessages::showMessage('OTP_SENT_PHONE'),
			'OTP_SENT_EMAIL' 		=> MoMessages::showMessage('OTP_SENT_EMAIL'),
			'ERROR_OTP_EMAIL' 		=> MoMessages::showMessage('ERROR_OTP_EMAIL'),
			'ERROR_OTP_PHONE' 		=> MoMessages::showMessage('ERROR_OTP_PHONE'),
			'ERROR_PHONE_FORMAT' 	=> MoMessages::showMessage('ERROR_PHONE_FORMAT'),
			'CHOOSE_METHOD' 		=> MoMessages::showMessage('CHOOSE_METHOD'),
			'PLEASE_VALIDATE' 		=> MoMessages::showMessage('PLEASE_VALIDATE'),
			'ERROR_PHONE_BLOCKED' 	=> MoMessages::showMessage('ERROR_PHONE_BLOCKED'),
			'ERROR_EMAIL_BLOCKED' 	=> MoMessages::showMessage('ERROR_EMAIL_BLOCKED'),
			'INVALID_OTP' 			=> MoMessages::showMessage('INVALID_OTP'),
			'EMAIL_MISMATCH' 		=> MoMessages::showMessage('EMAIL_MISMATCH'),
			'PHONE_MISMATCH' 		=> MoMessages::showMessage('PHONE_MISMATCH'),
			'ENTER_PHONE' 			=> MoMessages::showMessage('ENTER_PHONE'),
			'ENTER_EMAIL' 			=> MoMessages::showMessage('ENTER_EMAIL'),	
			'ENTER_PHONE_CODE' 		=> MoMessages::showMessage('ENTER_PHONE_CODE'),	
			'ENTER_EMAIL_CODE' 		=> MoMessages::showMessage('ENTER_EMAIL_CODE'),	
			'ENTER_VERIFY_CODE' 	=> MoMessages::showMessage('ENTER_VERIFY_CODE'),	
			'PHONE_VALIDATION_MSG' 	=> MoMessages::showMessage('PHONE_VALIDATION_MSG'),	
			'MO_REG_ENTER_PHONE' 	=> MoMessages::showMessage('MO_REG_ENTER_PHONE'),	
			'UNKNOWN_ERROR' 		=> MoMessages::showMessage('UNKNOWN_ERROR'),		
			'PHONE_NOT_FOUND' 		=> MoMessages::showMessage('PHONE_NOT_FOUND'),	
			'REGISTER_PHONE_LOGIN' 	=> MoMessages::showMessage('REGISTER_PHONE_LOGIN'),	
			'DEFAULT_SMS_TEMPLATE'	=> MoMessages::showMessage('DEFAULT_SMS_TEMPLATE'),	
			'EMAIL_SUBJECT'			=> MoMessages::showMessage('EMAIL_SUBJECT'),	
			'DEFAULT_EMAIL_TEMPLATE'=> MoMessages::showMessage('DEFAULT_EMAIL_TEMPLATE'),	
			'DEFAULT_BOX_HEADER' 	=> 'Validate OTP (One Time Passcode)',
			'GO_BACK' 				=> '&larr; Go Back',
			'RESEND_OTP' 			=> 'Resend OTP',
			'VALIDATE_OTP' 			=> 'Validate OTP',
			'VERIFY_CODE' 			=> 'Verify Code',
			'SEND_OTP' 				=> 'Send OTP',		
			'VALIDATE_PHONE_NUMBER' => 'Validate your Phone Number',	
			'VERIFY_CODE_DESC' 		=> 'Enter Verification Code',	
			'WC_BUTTON_TEXT'		=> "Verify Your Purchase",	
			'WC_POPUP_BUTTON_TEXT' 	=> "Place Order",	
			'WC_LINK_TEXT' 			=> "[ Click here to verify your Purchase ]",	
			'WC_EMAIL_TTLE' 		=> "Please Enter an Email Address to enable this.",	
			'WC_PHONE_TTLE' 		=> "Please Enter a Phone Number to enable this.",
		)));
	}
}