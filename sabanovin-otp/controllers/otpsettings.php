<?php

$otp_blocked_email_domains  = get_mo_option('mo_customer_validation_blocked_domains');
$otp_blocked_phones 		= get_mo_option('mo_customer_validation_blocked_phone_numbers');
$show_trans 				= get_mo_option('mo_customer_validation_show_remaining_trans') ? "checked" : "";
$show_dropdown_on_form 		= get_mo_option('mo_customer_validation_show_dropdown_on_form') ? "checked" : "";
$mo_otp_length 				= get_mo_option('mo_customer_validation_otp_length') ? get_mo_option('mo_customer_validation_otp_length') : 5;
$mo_otp_validity 			= get_mo_option('mo_customer_validation_otp_validity') ? get_mo_option('mo_customer_validation_otp_validity') : 5;
$showTransactionOptions 	= !MoUtility::mclv();
$nonce                      = $adminHandler->getNonceValue();

include MOV_DIR . 'views/otpsettings.php';