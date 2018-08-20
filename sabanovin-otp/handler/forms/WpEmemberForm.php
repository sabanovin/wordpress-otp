<?php
	

	
	class WpEmemberForm extends FormHandler implements IFormHandler
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
			$this->_formSessionVar = FormSessionVars::EMEMBER;
			$this->_typePhoneTag = 'mo_emember_phone_enable';
			$this->_typeEmailTag = 'mo_emember_email_enable';
			$this->_typeBothTag = 'mo_emember_both_enable';
			$this->_formKey = 'WP_EMEMBER';
			$this->_formName = mo_('WP eMember');
			$this->_isFormEnabled = get_mo_option('mo_customer_validation_emember_default_enable') ? TRUE : FALSE;
			$this->_phoneKey = 'wp_emember_phone';
			$this->_phoneFormId = 'input[name='.$this->_phoneKey.']';
			parent::__construct();
		}

		
		function handleForm()
		{
			$this->_otpType = get_mo_option('mo_customer_validation_emember_enable_type');
			if(array_key_exists('emember_dsc_nonce',$_POST) && !array_key_exists('option',$_POST)) 
				$this->miniorange_emember_user_registration();
		}


		
		function isPhoneVerificationEnabled()
		{
			return (strcasecmp($this->_otpType,$this->_typePhoneTag)==0 || strcasecmp($this->_otpType,$this->_typeBothTag)==0);
		}


		
		function miniorange_emember_user_registration()
		{
			MoUtility::checkSession();
			if($this->validatePostFields())
			{
				$phone = array_key_exists($this->_phoneKey,$_POST) ? $_POST[$this->_phoneKey] : NULL;
				$this->startTheOTPVerificationProcess($_POST['wp_emember_user_name'],$_POST['wp_emember_email'],$phone);
			}
		}


		
		function startTheOTPVerificationProcess($username,$useremail,$phone)
		{
			MoUtility::initialize_transaction($this->_formSessionVar);
			$errors = new WP_Error();
			if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0)
				miniorange_site_challenge_otp( $username,$useremail,$errors,$phone,"phone");
			else if(strcasecmp($this->_otpType,$this->_typeBothTag)==0)
				miniorange_site_challenge_otp( $username,$useremail,$errors,$phone,"both");
			else
				miniorange_site_challenge_otp( $username,$useremail,$errors,$phone,"email");
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


		
		function validatePostFields()
		{
			if(is_blocked_ip(get_real_ip_addr())) return FALSE;
            if(emember_wp_username_exists($_POST['wp_emember_user_name']) 
            	|| emember_username_exists($_POST['wp_emember_user_name']) ) return FALSE;
			if(is_blocked_email($_POST['wp_emember_email']) || emember_registered_email_exists($_POST['wp_emember_email']) 
				|| emember_wp_email_exists($_POST['wp_emember_email'])) return FALSE;
			if(isset($_POST['eMember_Register']) && array_key_exists('wp_emember_pwd_re',$_POST) 
				&& $_POST['wp_emember_pwd'] != $_POST['wp_emember_pwd_re']) return FALSE;
			return TRUE;
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

			$this->_isFormEnabled = isset( $_POST['mo_customer_validation_emember_default_enable']) ? $_POST['mo_customer_validation_emember_default_enable'] : 0;
			$this->_otpType = isset( $_POST['mo_customer_validation_emember_enable_type']) ? $_POST['mo_customer_validation_emember_enable_type'] : '';

			update_mo_option('mo_customer_validation_emember_default_enable',$this->_isFormEnabled);
			update_mo_option('mo_customer_validation_emember_enable_type',$this->_otpType);
		}	
	}