<?php

	add_action(	'init', 'miniorange_customer_validation_handle_form' , 1 );
	add_action( 'mo_validate_otp', '_handle_validation_form_action' , 1, 2);
	add_filter( 'mo_filter_phone_before_api_call','_filter_phone_before_api_call',1,1);
	
	
	function miniorange_site_challenge_otp($user_login, $user_email, $errors, $phone_number=null,
											$otp_type,$password="",$extra_data=null,$from_both=false)
	{
		MoUtility::checkSession();
		$phone_number = MoUtility::processPhoneNumber($phone_number);
		$_SESSION['current_url'] 	= MoUtility::currentPageUrl();
		$_SESSION['user_email'] 	= $user_email;
		$_SESSION['user_login'] 	= $user_login;
		$_SESSION['user_password'] 	= $password;
		$_SESSION['phone_number_mo']= $phone_number;
		$_SESSION['extra_data'] 	= $extra_data;
		_handle_otp_action($user_login,$user_email,$phone_number,$otp_type,$from_both,$extra_data);
	}
	

	
	function _handle_verification_resend_otp_action($otp_type,$from_both)
	{
		MoUtility::checkSession();
		$user_email 	= $_SESSION['user_email'];
		$user_login 	= $_SESSION['user_login'];
		$password 		= $_SESSION['user_password'];
		$phone_number 	= $_SESSION['phone_number_mo'];
		$extra_data 	= $_SESSION['extra_data'];
		_handle_otp_action($user_login,$user_email,$phone_number,$otp_type,$from_both,$extra_data);
	}


	
	function _handle_otp_action($user_login,$user_email,$phone_number,$otp_type,$from_both,$extra_data)
	{
		global $phoneLogic,$emailLogic;
		switch ($otp_type)
		{
			case 'phone':
				$phoneLogic->_handle_logic($user_login,$user_email,$phone_number,$otp_type,$from_both);	break;
			case 'email':
				$emailLogic->_handle_logic($user_login,$user_email,$phone_number,$otp_type,$from_both); break;
			case 'both':
				miniorange_verification_user_choice($user_login, $user_email,$phone_number,
					MoMessages::showMessage('CHOOSE_METHOD'),$otp_type);								break;	
			case 'external':
				mo_external_phone_validation_form($extra_data['curl'],$user_email,
					$extra_data['message'],$extra_data['form'],$extra_data['data']);					break;
		}
	}


	
	function _handle_validation_goBack_action()
	{
		MoUtility::checkSession();
		$url = isset($_SESSION['current_url'])? $_SESSION['current_url'] : '';
		do_action('unset_session_variable');
		header("location:".$url);
	}
		

	
	function _handle_validation_form_action($requestVariable='mo_customer_validation_otp_token',$otp_token=NULL)
	{	
		MoUtility::checkSession();
		$user_login  = array_key_exists('user_login', $_SESSION) 
						&& !MoUtility::isBlank($_SESSION['user_login']) ? $_SESSION['user_login'] : null;
		$user_email  = array_key_exists('user_email', $_SESSION) 
						&& !MoUtility::isBlank($_SESSION['user_email']) ? $_SESSION['user_email'] : null;
		$phone_number= array_key_exists('phone_number_mo', $_SESSION) 
						&& !MoUtility::isBlank($_SESSION['phone_number_mo']) ? $_SESSION['phone_number_mo'] : null;
		$password 	 = array_key_exists('user_password', $_SESSION) 
						&& !MoUtility::isBlank($_SESSION['user_password']) ? $_SESSION['user_password'] : null;
		$extra_data  = array_key_exists('extra_data', $_SESSION) 
						&& !MoUtility::isBlank($_SESSION['extra_data']) ? $_SESSION['extra_data'] : null;
		$txID 		 = array_key_exists(FormSessionVars::TX_SESSION_ID, $_SESSION) 
						&& !MoUtility::isBlank($_SESSION[FormSessionVars::TX_SESSION_ID]) ? $_SESSION[FormSessionVars::TX_SESSION_ID] : null;
		$otp_token 	 = !is_null($requestVariable) && array_key_exists($requestVariable, $_REQUEST) 
						&& !MoUtility::isBlank($_REQUEST[$requestVariable]) ? $_REQUEST[$requestVariable] : $otp_token;
		
		if(!is_null($txID))
		{
			$content = MO_TEST_MODE ? array('status'=>'SUCCESS') 
					: json_decode(MocURLOTP::validate_otp_token($txID, $otp_token),true);
			switch ($content['status']) 
			{
				case 'SUCCESS':
					_handle_success_validated($user_login,$user_email,$password,$phone_number,$extra_data);	break;
				default:
					_handle_error_validated($user_login,$user_email,$phone_number);							break;
			}
		}
	}


	
	function _handle_success_validated($user_login,$user_email,$password,$phone_number,$extra_data)
	{	
		$redirect_to = array_key_exists('redirect_to', $_POST) ? $_POST['redirect_to'] : '';
		do_action('otp_verification_successful',$redirect_to,$user_login,$user_email,$password,$phone_number,$extra_data);
	}


	
	function _handle_error_validated($user_login,$user_email,$phone_number)
	{
		do_action('otp_verification_failed',$user_login,$user_email,$phone_number);
	}
	

	 
	function _handle_validate_otp_choice_form($postdata)
	{
		MoUtility::checkSession();
		if(strcasecmp($postdata['mo_customer_validation_otp_choice'],'user_email_verification')==0)
			miniorange_site_challenge_otp($_SESSION['user_login'],$_SESSION['user_email'],null,$_SESSION['phone_number_mo'],
											"email",$_SESSION['user_password'],$_SESSION['extra_data'],true);
		else
			miniorange_site_challenge_otp($_SESSION['user_login'],$_SESSION['user_email'],null,$_SESSION['phone_number_mo'],
											"phone",$_SESSION['user_password'],$_SESSION['extra_data'],true);
	}


	
	function _filter_phone_before_api_call($phone)
	{
		return str_replace("+","",$phone);
	}


	
	function miniorange_customer_validation_handle_form()
	{
		if(array_key_exists('option', $_REQUEST) && MoUtility::micr())
		{
			switch (trim($_REQUEST['option'])) 
			{
				case "validation_goBack":
					_handle_validation_goBack_action();								        break;
				case "miniorange-validate-otp-form":
					_handle_validation_form_action();								        break;
				case "verification_resend_otp_phone":
					$from_both = $_POST['from_both']=='true' ? true : false;
					_handle_verification_resend_otp_action("phone",$from_both); 	break;
				case "verification_resend_otp_email":
					$from_both = $_POST['from_both']=='true' ? true : false;
					_handle_verification_resend_otp_action("email",$from_both);		break;
				case "verification_resend_otp_both":
					$from_both = $_POST['from_both']=='true' ? true : false;
					_handle_verification_resend_otp_action("both",$from_both);		break;
				case "miniorange-validate-otp-choice-form":
					_handle_validate_otp_choice_form($_POST);						        break;
			}
		}
	}
?>