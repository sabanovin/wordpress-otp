<?php
	
	
	class UserUltraRegistrationForm extends FormHandler implements IFormHandler
	{
		private static $_instance = null;

		public static function instance() 
		{
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self;
			}
			return self::$_instance;
		}

		protected function __construct()
		{
            $this->_isLoginOrSocialForm = FALSE;
            $this->_isAjaxForm = FALSE;
			$this->_formSessionVar = FormSessionVars::UULTRA_REG;
			$this->_typePhoneTag = 'mo_uultra_phone_enable';
			$this->_typeEmailTag = 'mo_uultra_email_enable';
			$this->_typeBothTag = 'mo_uultra_both_enable';
			$this->_formKey = 'UULTRA_FORM';
			$this->_formName = mo_("User Ultra Registration Form");
			$this->_isFormEnabled = get_mo_option('mo_customer_validation_uultra_default_enable') ? TRUE : FALSE;
			parent::__construct();
		}

		
		function handleForm()
		{
			$this->_phoneKey = get_mo_option('mo_customer_validation_uultra_phone_key');
			$this->_otpType = get_mo_option('mo_customer_validation_uultra_enable_type');
			$this->_phoneFormId = 'input[name='.$this->_phoneKey.']';
			
			if(!array_key_exists('xoouserultra-register-form',$_POST)) return;
			
			$phone =  $this->isPhoneVerificationEnabled() ? $_POST[$this->_phoneKey] : NULL;
			$this->_handle_uultra_form_submit($_POST['user_login'],$_POST['user_email'],$phone);
		}


		
		function isPhoneVerificationEnabled()
		{
			return (strcasecmp($this->_otpType,$this->_typePhoneTag)==0 || strcasecmp($this->_otpType,$this->_typeBothTag)==0);
		}


		
		function _handle_uultra_form_submit($user_name,$user_email,$phone)
		{	
			MoUtility::checkSession();
			$xoUser = new XooUserRegister;
			
			if(array_key_exists($this->_formSessionVar,$_SESSION)) return;
			
			$xoUser->uultra_prepare_request( $_POST );
			$xoUser->uultra_handle_errors();
			if(!MoUtility::isBlank($xoUser->errors)) $_POST['no_captcha'] = 'yes';
			$this->_handle_otp_verification_uultra($user_name,$user_email,null,$phone);
		
		}


		
		function _handle_otp_verification_uultra($user_name,$user_email,$errors,$phone)
		{
			MoUtility::initialize_transaction($this->_formSessionVar);		
			if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0)
				miniorange_site_challenge_otp($user_name,$user_email,$errors,$phone,"phone");
			else if(strcasecmp($this->_otpType,$this->_typeBothTag)==0)
				miniorange_site_challenge_otp($user_name,$user_email,$errors,$phone,"both");
			else
				miniorange_site_challenge_otp($user_name,$user_email,$errors,$phone,"email");
		}


		
		function handle_failed_verification($user_login,$user_email,$phone_number)
		{
			MoUtility::checkSession();
			if(!isset($_SESSION[$this->_formSessionVar])) return;
			$otpVerType = strcasecmp($this->_otpType,$this->_typePhoneTag)==0 ? "phone" 
							: (strcasecmp($this->_otpType,$this->_typeBothTag)==0 ? "both" : "email" );
			$fromBoth = strcasecmp($otpVerType,"both")==0 ? TRUE : FALSE;
			miniorange_site_otp_validation_form($user_login,$user_email,$phone_number,MoUtility::_get_invalid_otp_method(),$otpVerType,$fromBoth);
		}


	    
		function handle_post_verification($redirect_to,$user_login,$user_email,$password,$phone_number,$extra_data)
		{
			MoUtility::checkSession();
			if(!isset($_SESSION[$this->_formSessionVar])) return;
			$this->unsetOTPSessionVariables();	
		}


		
		public function unsetOTPSessionVariables()
		{
			unset($_SESSION[$this->_txSessionId]);
			unset($_SESSION[$this->_formSessionVar]);
		}
		

		
		public function getPhoneNumberSelector($selector)	
		{
			MoUtility::checkSession();
			if($this->isFormEnabled() && $this->isPhoneVerificationEnabled()) array_push($selector, $this->_phoneFormId); 
			return $selector;
		}


		
		function handleFormOptions()
	    {
			if(!MoUtility::areFormOptionsBeingSaved()) return;
			
			$this->_isFormEnabled = isset( $_POST['mo_customer_validation_uultra_default_enable']) ? $_POST['mo_customer_validation_uultra_default_enable'] : 0;
			$this->_otpType = isset( $_POST['mo_customer_validation_uultra_enable_type']) ? $_POST['mo_customer_validation_uultra_enable_type'] : '';
			$this->_phoneKey = isset( $_POST['uultra_phone_field_key']) ? $_POST['uultra_phone_field_key'] : '';

			update_mo_option('mo_customer_validation_uultra_default_enable',$this->_isFormEnabled);
			update_mo_option('mo_customer_validation_uultra_enable_type',$this->_otpType);
			update_mo_option('mo_customer_validation_uultra_phone_key',$this->_phoneKey);
	    }
	}