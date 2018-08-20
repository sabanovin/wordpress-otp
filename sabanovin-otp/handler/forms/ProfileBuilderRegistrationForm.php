<?php
	
	
	class ProfileBuilderRegistrationForm extends FormHandler implements IFormHandler
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
			$this->_formSessionVar 	= FormSessionVars::PB_DEFAULT_REG;
			$this->_typePhoneTag = 'mo_pb_phone_enable';
			$this->_typeEmailTag = 'mo_pb_email_enable';
			$this->_typeBothTag = 'mo_pb_both_enable';
			$this->_formKey = 'PB_DEFAULT_FORM';
			$this->_formName = mo_("Profile Builder Registration Form");
			$this->_isFormEnabled = get_mo_option('mo_customer_validation_pb_default_enable') ? TRUE : FALSE;
			parent::__construct();
		}

		
		function handleForm()
		{
			$this->_otpType = get_mo_option('mo_customer_validation_pb_enable_type');
			$this->_phoneKey = get_mo_option('mo_customer_validation_pb_phone_meta_key');
			$this->_phoneFormId = "input[name=" . $this->_phoneKey . "]";
			add_filter( 'wppb_output_field_errors_filter', array($this,'formbuilder_site_registration_errors'),99,4);
		}


		
		function isPhoneVerificationEnabled()
		{
			return (strcasecmp($this->_otpType,$this->_typePhoneTag)==0 || strcasecmp($this->_otpType,$this->_typeBothTag)==0);
		}


		
		function formbuilder_site_registration_errors($fieldErrors,$fieldArgs,$global_request,$typeArgs)
		{
			MoUtility::checkSession();
			
			if(!empty($fieldErrors)) return $fieldErrors; 			
			if($global_request['action']=='register')
			{
				if(isset($_SESSION[$this->_formSessionVar]) && strcasecmp($_SESSION[$this->_formSessionVar],'validated')==0)
				{
					$this->unsetOTPSessionVariables();
					return $fieldErrors;
				}
				return $this->startOTPVerificationProcess($fieldErrors,$global_request);
			}
			return $fieldErrors;
		}


		
		function startOTPVerificationProcess($fieldErrors,$data)
		{
			MoUtility::initialize_transaction($this->_formSessionVar);
			extract($data);
			if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0 || strcasecmp($this->_otpType,$this->_typeBothTag)==0)
			{
				$phone = $this->_phoneKey;
				miniorange_site_challenge_otp($username,$email,new WP_Error(),$$phone,"phone",$passw1,array());
			}
			else if(strcasecmp($this->_otpType,$this->_typeEmailTag)==0)
			{
				miniorange_site_challenge_otp($username,$email,new WP_Error(),null,"email",$passw1,array());
			}
		}


	    
		function handle_failed_verification($user_login,$user_email,$phone_number)
		{
			MoUtility::checkSession();
			if(!isset($_SESSION[$this->_formSessionVar])) return;
			miniorange_site_otp_validation_form($user_login,$user_email,$phone_number,MoUtility::_get_invalid_otp_method(),"email",FALSE);
		}


	    
		function handle_post_verification($redirect_to,$user_login,$user_email,$password,$phone_number,$extra_data)
		{
			MoUtility::checkSession();
			if(!isset($_SESSION[$this->_formSessionVar])) return;
			$_SESSION[$this->_formSessionVar]='validated';
		}


	    
		public function unsetOTPSessionVariables()
		{
			unset($_SESSION[$this->_txSessionId]);
			unset($_SESSION[$this->_formSessionVar]);
		}


		
		public function getPhoneNumberSelector($selector)	
		{
			if($this->isFormEnabled() && $this->isPhoneVerificationEnabled()) array_push($selector, $this->_phoneFormId); 
			return $selector;
		}


	    
		function handleFormOptions()
		{
			if(!MoUtility::areFormOptionsBeingSaved()) return;

			$this->_isFormEnabled = isset( $_POST['mo_customer_validation_pb_default_enable']) ? $_POST['mo_customer_validation_pb_default_enable'] : 0;
			$this->_otpType = isset( $_POST['mo_customer_validation_pb_enable_type']) ? $_POST['mo_customer_validation_pb_enable_type'] : '';
			$this->_phoneKey = isset( $_POST['pb_phone_field_key']) ? $_POST['pb_phone_field_key'] : '';
			
			update_mo_option('mo_customer_validation_pb_default_enable',$this->_isFormEnabled);
			update_mo_option('mo_customer_validation_pb_enable_type',$this->_otpType);
			update_mo_option('mo_customer_validation_pb_phone_meta_key',$this->_phoneKey);
		}
	}