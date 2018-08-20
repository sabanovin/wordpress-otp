<?php
	

	
	class PieRegistrationForm extends FormHandler implements IFormHandler
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
			$this->_formSessionVar 	= FormSessionVars::PIE_REG;
			$this->_typePhoneTag = 'mo_pie_phone_enable';
			$this->_typeEmailTag = 'mo_pie_email_enable';
			$this->_typeBothTag = 'mo_pie_both_enable';
			$this->_formKey = 'PIE_FORM';
			$this->_formName = mo_('PIE Registration Form');
			$this->_isFormEnabled = get_mo_option('mo_customer_validation_pie_default_enable') ? TRUE : FALSE;
			parent::__construct();
		}

		
		function handleForm()
		{
			$this->_otpType = get_mo_option('mo_customer_validation_pie_enable_type');
			$this->_phoneKey = get_mo_option('mo_customer_validation_pie_phone_key');
			$this->_phoneFormId = $this->getPhoneFieldKey();
			add_action( 'pie_register_after_register_validate', array($this,'miniorange_pie_user_registration'),99,0);
		}

		
		function isPhoneVerificationEnabled()
		{
			return (strcasecmp($this->_otpType,$this->_typePhoneTag)==0 || strcasecmp($this->_otpType,$this->_typeBothTag)==0);
		}


		
		function miniorange_pie_user_registration()
		{
			MoUtility::checkSession();
			if(!array_key_exists($this->_formSessionVar,$_SESSION))
			{
				$phone_field = $this->getPhoneFieldKey();
				$phone = !MoUtility::isBlank($phone_field) ? $_POST[$phone_field] : NULL;
				$this->startTheOTPVerificationProcess($_POST['username'],$_POST['e_mail'],$phone);
			}
			elseif(strcasecmp($_SESSION[$this->_formSessionVar],'validated')==0)
				$_SESSION[$this->_formSessionVar] = 'validationChecked';
			elseif(strcasecmp($_SESSION[$this->_formSessionVar],'validationChecked')==0)
				$this->unsetOTPSessionVariables();
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


		
		function getPhoneFieldKey()
		{
			$fields = unserialize(get_mo_option('pie_fields'));
			$keys = array_keys($fields);
			foreach($keys as $key)
			{
				if(strcasecmp(trim($fields[$key]['label']),$this->_phoneKey)==0)
					return str_replace("-","_",sanitize_title($fields[$key]['type']."_"
						.(isset($fields[$key]['id']) ? $fields[$key]['id'] : "")));
			}
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
			$_SESSION[$this->_formSessionVar]="validated";
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

			$this->_isFormEnabled = isset( $_POST['mo_customer_validation_pie_default_enable']) ? $_POST['mo_customer_validation_pie_default_enable'] : 0;
			$this->_otpType = isset( $_POST['mo_customer_validation_pie_enable_type']) ? $_POST['mo_customer_validation_pie_enable_type'] : '';
			$this->_phoneKey = isset( $_POST['pie_phone_field_key']) ? $_POST['pie_phone_field_key'] : '';

			update_mo_option('mo_customer_validation_pie_default_enable',$this->_isFormEnabled);
			update_mo_option('mo_customer_validation_pie_enable_type',$this->_otpType);
			update_mo_option('mo_customer_validation_pie_phone_key',$this->_phoneKey);
		}	
	}