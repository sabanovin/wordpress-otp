<?php
	

	
	class TmlRegistrationForm extends FormHandler implements IFormHandler
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
            $this->_isLoginOrSocialForm = TRUE;
            $this->_isAjaxForm = FALSE;
			$this->_formSessionVar = FormSessionVars::TML_REG;
			$this->_typePhoneTag = 'mo_tml_phone_enable';
			$this->_typeEmailTag = 'mo_tml_email_enable';
			$this->_typeBothTag = 'mo_tml_both_enable';
			$this->_phoneFormId= '#phone_number_mo';
			$this->_formKey = 'TML_FORM';
			$this->_formName = mo_("Theme My Login Form");
			$this->_isFormEnabled = get_mo_option('mo_customer_validation_tml_enable') ? TRUE : FALSE;
			parent::__construct();
		}

		
		function handleForm()
		{
			$this->_otpType = get_mo_option('mo_customer_validation_tml_enable_type');

			add_action( 'register_form', array($this,'miniorange_tml_register_form'));
			add_filter( 'registration_errors',  array($this,'miniorange_tml_registration_errors'), 1, 3 );
			add_action( 'admin_post_nopriv_validation_goBack',  array($this,'_handle_validation_goBack_action'));
			add_action( 'user_register',  array($this,'miniorange_tml_registration_save'), 10, 1 );
		}


		
		function isPhoneVerificationEnabled()
		{
			return (strcasecmp($this->_otpType,$this->_typePhoneTag)==0 || strcasecmp($this->_otpType,$this->_typeBothTag)==0);
		}


				
		function miniorange_tml_register_form()
		{	
	 		echo '<input type="hidden" name="register_tml_nonce" value="register_tml_nonce"/>';
	 		if($this->isPhoneVerificationEnabled())
	 			echo '<label for="phone_number_mo">'.mo_("Phone Number").'<br />
					  <input type="text" name="phone_number_mo" id="phone_number_mo" class="input" value="" style=""  /></label>';
		}
		

		
		function miniorange_tml_registration_save($user_id)
		{
			if (isset( $_SESSION['phone_number_mo'] ) ) add_user_meta($user_id, 'telephone', $_SESSION['phone_number_mo']);
		}


		
		function miniorange_tml_registration_errors($errors, $sanitized_user_login, $user_email )
		{
			MoUtility::checkSession();

			$phone_number = isset($_POST['phone_number_mo'])? $_POST['phone_number_mo'] : null;
			if($this->isPhoneVerificationEnabled() && MoUtility::validatePhoneNumber($phone_number))
				$this->startOTPTransaction($sanitized_user_login,$user_email,$errors,$phone_number);
			elseif($this->_otpType == $this->_typeEmailTag)
				$this->startOTPTransaction($sanitized_user_login,$user_email,$errors,$phone_number);
			else
				$errors->add( 'invalid_phone', MoMessages::showMessage('ENTER_PHONE_DEFAULT') );
			return $errors;
		}


		
		function startOTPTransaction($sanitized_user_login,$user_email,$errors,$phone_number)
		{
			if(!MoUtility::isBlank(array_filter($errors->errors)) || !isset($_POST['register_tml_nonce'])) return;

			if(array_key_exists($this->_formSessionVar, $_SESSION) && $_SESSION[$this->_formSessionVar]=='validated') return;
			
			MoUtility::initialize_transaction($this->_formSessionVar);
			if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0)
				miniorange_site_challenge_otp($sanitized_user_login,$user_email,$errors,$phone_number,"phone");
			else if(strcasecmp($this->_otpType,$this->_typeBothTag)==0)
				miniorange_site_challenge_otp($sanitized_user_login,$user_email,$errors,$phone_number,"both");
			else
				miniorange_site_challenge_otp($sanitized_user_login,$user_email,$errors,$phone_number,"email");
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
			$_SESSION[$this->_formSessionVar] = 'validated';
			$errors = register_new_user($user_login, $user_email);
			$this->unsetOTPSessionVariables();
			if ( !is_wp_error($errors) ) {
				$redirect_to = !MoUtility::isBlank( $redirect_to ) ? $redirect_to :  wp_login_url()."?checkemail=registered";
				wp_redirect( $redirect_to );
				exit();
			}
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
			
			$this->_isFormEnabled = isset( $_POST['mo_customer_validation_tml_enable']) ? $_POST['mo_customer_validation_tml_enable'] : 0;
			$this->_otpType = isset( $_POST['mo_customer_validation_tml_enable_type']) ? $_POST['mo_customer_validation_tml_enable_type'] : 0;

			update_mo_option('mo_customer_validation_tml_enable',$this->_isFormEnabled);
			update_mo_option('mo_customer_validation_tml_enable_type',$this->_otpType);
	    }
	}