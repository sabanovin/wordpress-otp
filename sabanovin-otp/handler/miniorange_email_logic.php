<?php 


class EmailVerificationLogic extends VerificationLogic
{

	
	public function _handle_logic($user_login,$user_email,$phone_number,$otp_type,$from_both)
	{
		$message = str_replace("##email##",$user_email,$this->_get_is_blocked_message());
		if($this->_is_blocked($user_email,$phone_number)) 
			if($this->_is_ajax_form())
				wp_send_json(MoUtility::_create_json_response($message,MoConstants::ERROR_JSON_TYPE));
			else
				miniorange_site_otp_validation_form(null,null,null,$message,$otp_type,$from_both);
		else
			$this->_start_otp_verification($user_login,$user_email,$phone_number,$otp_type,$from_both);
	}


	
	public function _start_otp_verification($user_login,$user_email,$phone_number,$otp_type,$from_both)
	{
		$content = MO_TEST_MODE ? array('status'=>'SUCCESS','txId'=> MoUtility::rand()) 
					: json_decode(MocURLOTP::mo_send_otp_token('EMAIL',$user_email,''), true);
		switch ($content['status']) 
		{
			case 'SUCCESS':
				$this->_handle_otp_sent($user_login,$user_email,$phone_number,$otp_type,$from_both,$content); 		break;
			default:
				$this->_handle_otp_sent_failed($user_login,$user_email,$phone_number,$otp_type,$from_both,$content);break;
		}
	}


	
	public function _handle_otp_sent($user_login,$user_email,$phone_number,$otp_type,$from_both,$content)
	{
		MoUtility::checkSession();
		$_SESSION[FormSessionVars::TX_SESSION_ID] = $content['txId'];

		if(MoUtility::micr() && !MoUtility::mclv()) update_mo_option('mo_customer_email_transactions_remaining',
												get_mo_option('mo_customer_email_transactions_remaining')-1);

		$message = str_replace("##email##",$user_email,$this->_get_otp_sent_message());

		if($this->_is_ajax_form())
			wp_send_json(MoUtility::_create_json_response($message,MoConstants::SUCCESS_JSON_TYPE));
		else
			miniorange_site_otp_validation_form($user_login, $user_email,$phone_number,$message,$otp_type,$from_both);
	}


	
	public function _handle_otp_sent_failed($user_login,$user_email,$phone_number,$otp_type,$from_both,$content)
	{
		$message = str_replace("##email##",$user_email,$this->_get_otp_sent_failed_message());

		if($this->_is_ajax_form())
			wp_send_json(MoUtility::_create_json_response($message,MoConstants::ERROR_JSON_TYPE));
		else
			miniorange_site_otp_validation_form(null,null,null,$message,$otp_type,$from_both);
	}


	
	public function _get_otp_sent_message()
	{
		return get_mo_option("mo_otp_success_email_message") ? mo_(get_mo_option('mo_otp_success_email_message')) 
														: MoMessages::showMessage('OTP_SENT_EMAIL');
	}


	
	public function _get_otp_sent_failed_message()
	{
		return get_mo_option("mo_otp_error_email_message") ? mo_(get_mo_option('mo_otp_error_email_message')) 
														: MoMessages::showMessage('ERROR_OTP_EMAIL');
	}


	
	public function _is_blocked($user_email,$phone_number)
	{
		$blocked_email_domains = explode(";",get_mo_option('mo_customer_validation_blocked_domains'));	
		$blocked_email_domains = apply_filters("mo_blocked_email_domains",$blocked_email_domains);
		return in_array(MoUtility::getDomain($user_email),$blocked_email_domains);
	}


	
	public function _get_is_blocked_message()
	{
		return get_mo_option("mo_otp_blocked_email_message") ? mo_(get_mo_option('mo_otp_blocked_email_message')) 
														: MoMessages::showMessage('ERROR_EMAIL_BLOCKED');
	}


	
	public function _get_otp_invalid_format_message() { return; }


	
	public function _handle_not_matched($phone_number,$otp_type,$from_both){ return; }


	
	public function _handle_matched($user_login,$user_email,$phone_number,$otp_type,$from_both){ return; }
}