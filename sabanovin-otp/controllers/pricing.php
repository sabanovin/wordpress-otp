<?php

$form_action 	  	= MoConstants::HOSTNAME.'/moas/login';
$redirect_url	  	= MoConstants::HOSTNAME .'/moas/initializepayment';
$free_plan_name 	= 'Free';
$basic_plan_name 	= 'Your Gateway';
$premium_plan_name 	= 'miniOrange Gateway';
$free_plan_price	= 'Free';
$basic_plan_price 	= '$99 - One Time Payment';
$premium_plan_price	= '$0 - One Time Payment';
$width 				= $plan ? "63%" : "95%";
$vl 				= MoUtility::mclv();
$nonce              = $adminHandler->getNonceValue();

$free_plan_features =array(
		mo_("Email Address Verifications"),
		mo_("Phone Verifications"),
		mo_("Custom Email Template"),
		mo_("Custom SMS Template"),
		"",
		mo_("Block Email Domains"),
		mo_("Block SMS numbers"),
		mo_("Send Custom SMS Messages"),
		mo_("Country Code Dropdown for your form"),
		"",
		"",
		""
);

$basic_plan_features =array(
		mo_("Email Address Verifications"),
		mo_("Phone Verifications"),
		mo_("Custom Email Template"),
		mo_("Custom SMS Template"),
		mo_("Custom SMS/SMTP Gateway"),
		mo_("Block Email Domains"),
		mo_("Block SMS numbers"),
		mo_("Send Custom SMS Messages"),
		mo_("Country Code Dropdown for your form"),
		"",
		"",
		mo_("Custom Integration/Work***")
);


$premium_plan_features =array(
		mo_("Email Address Verifications"),
		mo_("Phone Verifications"),
		mo_("Custom Email Template"),
		mo_("Custom SMS Template"),
		mo_("Custom SMS/SMTP Gateway"),
		mo_("Block Email Domains"),
		mo_("Block SMS numbers"),
		mo_("Send Custom SMS Messages"),
		mo_("Country Code Dropdown for your form"),
		mo_("Custom OTP Length"),
		mo_("Custom OTP Validity Time"),
		mo_("Custom Integration/Work***")
);

include MOV_DIR . 'views/pricing.php';