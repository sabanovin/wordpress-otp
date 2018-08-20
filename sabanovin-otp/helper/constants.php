<?php

if(! defined( 'ABSPATH' )) exit;


class MoConstants
{
    
    const DEFAULT_CUSTOMER_KEY 	= "16555";
    const DEFAULT_API_KEY 		= "fFd2XcvTGDemZvbw1bcUesNJWEqKbbUq";
    const PCODE 				= "UHJlbWl1bSBQbGFuIC0gV1AgT1RQIFZFUklGSUNBVElPTg==";
	const BCODE 				= "RG8gaXQgWW91cnNlbGYgUGxhbiAtIFdQIE9UUCBWRVJJRklDQVRJT04=";
	const CCODE					= "bWluaU9yYW5nZSBTTVMvU01UUCBHYXRld2F5IC0gV1AgT1RQIFZFUklGSUNBVElPTg==";
	const NCODE                 = "d3Bfb3RwX3ZlcmlmaWNhdGlvbl9iYXNpY19wbGFu";
	const HOSTNAME				= "https://auth.miniorange.com";
	const FROM_EMAIL			= "no-reply@miniorange.com";
	const SUPPORT_EMAIL 		= "info@miniorange.com";
	const FEEDBACK_EMAIL 		= "otpsupport@miniorange.com";
	const HEADER_CONTENT_TYPE	= "Content-Type: text/html";
	const SUCCESS				= "SUCCESS";
	const FAILURE				= "FAILURE";
	const AREA_OF_INTEREST		= "WP OTP Verification Plugin";
	const APPLICATION_NAME		= "wp_otp_verification";
	const PATTERN_PHONE			= '/^[\+]\d{1,4}\d{7,12}$|^[\+]\d{1,4}[\s]\d{7,12}$/';
	const PATTERN_COUNTRY_CODE  = '/^[\+]\d{1,4}.*/';
	const PATTERN_SPACES_HYPEN 	= '/([\(\) -]+)/';
	const ERROR_JSON_TYPE 		= 'error';
	const SUCCESS_JSON_TYPE 	= 'success';
	const EMAIL_TRANS_REMAINING = 10;
	const PHONE_TRANS_REMAINING = 10;
	const USERPRO_VER_FIELD_META= "verification_form";
	const BUSINESS_FREE_TRIAL 	= "https://www.miniorange.com/businessfreetrial";
	
		const PB_FORM_LINK			= 'https://wordpress.org/plugins/profile-builder/';
	const WC_FORM_LINK          = 'https://wordpress.org/plugins/woocommerce/';
	const SIMPLR_FORM_LINK  	= 'https://wordpress.org/plugins/simplr-registration-form/';
	const UM_ENABLED 			= 'https://wordpress.org/plugins/ultimate-member/';
	const EVENT_FORM			= 'https://wordpress.org/plugins/event-registration/';
	const BBP_FORM_LINK 		= 'https://wordpress.org/plugins/buddypress/';
	const CRF_FORM_ENABLE		= 'https://wordpress.org/plugins/custom-registration-form-builder-with-submission-manager/';
	const UULTRA_FORM_LINK 		= 'https://wordpress.org/plugins/users-ultra/';
	const UPME_FORM_LINK	    = 'http://codecanyon.net/item/user-profiles-made-easy-wordpress-plugin/4109874';
	const PIE_FORM_LINK 		= 'http://pieregister.com/';
	const CF7_FORM_LINK 		= 'https://wordpress.org/plugins/contact-form-7/';
	const WC_SOCIAL_LOGIN		= 'https://woocommerce.com/products/woocommerce-social-login/';
	const NINJA_FORMS_LINK		= 'https://ninjaforms.com/';
	const TML_FORM_LINK			= 'https://wordpress.org/plugins/theme-my-login/';
	const USERPRO_FORM_LINK 	= 'https://codecanyon.net/item/userpro-user-profiles-with-social-login/5958681';
	const GF_FORM_LINK			= 'http://www.gravityforms.com/';
	const WP_MEMBER_LINK		= 'https://wordpress.org/plugins/wp-members/';
	const UM_PRO_LINK 			= 'https://codecanyon.net/item/ultimate-membership-pro-wordpress-plugin/12159253';
	const CLASSIFY_LINK			= 'https://themeforest.net/item/classify-classified-ads-html-template/11013666';
	const REALES_THEME 			= 'https://themeforest.net/item/reales-wp-real-estate-wordpress-theme/10330568';
	const EMEMBER_FORM_LINK		= 'https://www.tipsandtricks-hq.com/wordpress-emember-easy-to-use-wordpress-membership-plugin-1706';
	const FORMCRAFT_BASIC_LINK	= 'https://wordpress.org/plugins/formcraft-form-builder/';
	const FORMCRAFT_PREMIUM		= 'https://codecanyon.net/item/formcraft-premium-wordpress-form-builder/5335056';
	const DOCDIRECT_THEME 		= 'https://themeforest.net/item/docdirect-responsive-directory-wordpress-theme-for-doctors-and-healthcare-profession/16089820';
	const WP_FORMS_LINK 		= 'https://wordpress.org/plugins/wpforms-lite/';
	const CALDERA_FORMS_LINK 	= 'https://wordpress.org/plugins/caldera-forms/';
    const MRP_FORM_LINK			= 'https://memberpress.com/';
    const REALESTATE7_THEME_LINK= 'https://themeforest.net/item/wp-pro-real-estate-7-responsive-real-estate-wordpress-theme/12473778';
    const PAID_MEMBERSHIP_PRO   = 'https://wordpress.org/plugins/paid-memberships-pro/';

    const FAQ_URL               = 'https://faq.miniorange.com/kb/otp-verification/';
    const FAQ_BASE_URL          = 'https://faq.miniorange.com/knowledgebase/';
}
