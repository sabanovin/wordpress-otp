<?php

	$nonce 				= $adminHandler->getNonceValue();
	$otp_success_email 	= get_mo_option("mo_otp_success_email_message") ? get_mo_option('mo_otp_success_email_message') : MoMessages::showMessage('OTP_SENT_EMAIL');
	$otp_success_phone 	= get_mo_option("mo_otp_success_phone_message") ? get_mo_option('mo_otp_success_phone_message') : MoMessages::showMessage('OTP_SENT_PHONE');
	$otp_error_phone 	= get_mo_option("mo_otp_error_phone_message")   ? get_mo_option('mo_otp_error_phone_message')   : MoMessages::showMessage('ERROR_OTP_PHONE');
	$otp_error_email 	= get_mo_option("mo_otp_error_email_message")   ? get_mo_option('mo_otp_error_email_message')   : MoMessages::showMessage('ERROR_OTP_EMAIL');
	$otp_invalid_format = get_mo_option("mo_otp_invalid_phone_message") ? get_mo_option('mo_otp_invalid_phone_message') : MoMessages::showMessage('ERROR_PHONE_FORMAT');
	$invalid_otp 		= get_mo_option("mo_otp_invalid_message")       ? get_mo_option('mo_otp_invalid_message')       : MoMessages::showMessage('INVALID_OTP');
	$otp_blocked_email 	= get_mo_option("mo_otp_blocked_email_message") ? get_mo_option('mo_otp_blocked_email_message') : MoMessages::showMessage('ERROR_EMAIL_BLOCKED');
	$otp_blocked_phone 	= get_mo_option("mo_otp_blocked_phone_message") ? get_mo_option('mo_otp_blocked_phone_message') : MoMessages::showMessage('ERROR_PHONE_BLOCKED');

	include MOV_DIR . 'views/messages.php';