<?php

if(! defined( 'ABSPATH' )) exit;


class MoUtility
{


	public static function get_hidden_phone($phone)
	{
		return 'xxxxxxx' . substr($phone,strlen($phone) - 3);
	}



	public static function isBlank( $value )
	{
		if( ! isset( $value ) || empty( $value ) ) return TRUE;
		return FALSE;
	}



	public static function _create_json_response($message,$type)
	{
		return array( 'message' => $message, 'result' => $type);
	}



	public static function mo_is_curl_installed()
	{
		if  (in_array  ('curl', get_loaded_extensions()))
			return 1;
		else
			return 0;
	}



	public static function currentPageUrl()
	{
		$pageURL = 'http';

		if ((isset($_SERVER["HTTPS"])) && ($_SERVER["HTTPS"] == "on"))
			$pageURL .= "s";

		$pageURL .= "://";

		if ($_SERVER["SERVER_PORT"] != "80")
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];

		else
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];

		if ( function_exists('apply_filters') ) apply_filters('wppb_curpageurl', $pageURL);

        return $pageURL;
	}



	public static function getDomain($email)
	{
		return $domain_name = substr(strrchr($email, "@"), 1);
	}



	public static function validatePhoneNumber($phone)
	{
		return preg_match(MoConstants::PATTERN_PHONE,MoUtility::processPhoneNumber($phone),$matches);
	}



	public static function isCountryCodeAppended($phone)
	{
		return preg_match(MoConstants::PATTERN_COUNTRY_CODE,$phone,$matches) ? true : false;
	}


	public static function processPhoneNumber($phone)
	{
		$phone = preg_replace(MoConstants::PATTERN_SPACES_HYPEN,"", ltrim(trim($phone),'0'));
		$defaultCountryCode = CountryList::getDefaultCountryCode();
		$phone = !isset($defaultCountryCode) || MoUtility::isCountryCodeAppended($phone) ? $phone : $defaultCountryCode.$phone;
		return apply_filters("mo_process_phone",$phone);
	}



	public static function areFormOptionsBeingSaved()
	{
		return current_user_can('manage_options')
				&& MoUtility::micr()
				&& isset($_POST['option'])
				&& 'mo_customer_validation_settings'==$_POST['option'];
	}



	public static function micr()
	{
		$apiKey 			= get_mo_option('sabanovin_api_key');
		$gateway 	= get_mo_option('sabanovin_gateway');
		if( empty($apiKey) or empty($gateway) or !is_numeric($gateway) )
			return 0;
		else
			return 1;
	}



	public static function rand()
	{
		$length = wp_rand(0,15);
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[wp_rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}



	public static function micv()
	{
		$email 			= get_mo_option('mo_customer_validation_admin_email');
		$customerKey 	= get_mo_option('mo_customer_validation_admin_customer_key');
		if( ! $email || ! $customerKey || ! is_numeric( trim( $customerKey ) ) )
			return 0;
		else
			return get_mo_option('mo_customer_check_ln') ? get_mo_option('mo_customer_check_ln') :  0;
	}



	public static function checkSession()
	{
		if (session_id() == '' || !isset($_SESSION)) {
			session_start();
		}
	}



	public static function _handle_mo_check_ln($showMessage,$customerKey,$apiKey)
	{
		$msg = 'FREE_PLAN_MSG';
		$plan = array();
		$content = json_decode(MocURLOTP::check_customer_ln($customerKey,$apiKey), true);
		if(strcasecmp($content['status'], 'SUCCESS') == 0)
		{
			array_key_exists('licensePlan', $content) && isset($content['licensePlan'])
				? update_mo_option('mo_customer_check_ln',base64_encode($content['licensePlan'])) : update_mo_option('mo_customer_check_ln','');
			if(array_key_exists('licensePlan', $content) && isset($content['licensePlan']))
			{
				$msg = 'UPGRADE_MSG';
				$plan = array('plan'=>$content['licensePlan']);
			}
			$emailRemaining = isset($content['emailRemaining']) ? $content['emailRemaining'] : 0;
			$smsRemaining = isset($content['smsRemaining']) ? $content['smsRemaining'] : 0;
			update_mo_option('mo_customer_email_transactions_remaining',$emailRemaining);
			update_mo_option('mo_customer_phone_transactions_remaining',$smsRemaining);
		}
		if($showMessage)
			do_action('mo_registration_show_message', MoMessages::showMessage($msg,$plan),'SUCCESS');
	}



	public static function initialize_transaction($form,$sessionValue = "true")
	{
		MoUtility::checkSession();
		$reflect = new ReflectionClass('FormSessionVars');
		foreach ($reflect->getConstants()  as $key => $value)
			unset($_SESSION[$value]);
		$_SESSION[$form] = $sessionValue;
	}



	public static function _get_invalid_otp_method()
	{
		return get_mo_option("mo_otp_invalid_message") ? mo_(get_mo_option("mo_otp_invalid_message"))
			: MoMessages::showMessage('INVALID_OTP');
	}



	public static function _is_polylang_installed()
	{
		return function_exists('pll__') && function_exists('pll_register_string');
	}





	public static function mclv()
	{
		return FALSE;
	}
}
