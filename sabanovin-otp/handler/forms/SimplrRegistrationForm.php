<?php
		
	
	class SimplrRegistrationForm extends FormHandler implements IFormHandler
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
			$this->_formSessionVar = FormSessionVars::SIMPLR_REG;
			$this->_typePhoneTag = 'mo_phone_enable';
			$this->_typeEmailTag = 'mo_email_enable';
			$this->_typeBothTag = 'mo_both_enable';
			$this->_formKey = 'SIMPLR_FORM';
			$this->_formName = mo_("Simplr User Registration Form Plus");
			$this->_isFormEnabled = get_mo_option('mo_customer_validation_simplr_default_enable') ? TRUE : FALSE;
			parent::__construct();
		}

		
		function handleForm()
		{
			$this->_formKey = get_mo_option('mo_customer_validation_simplr_field_key');
			$this->_otpType = get_mo_option('mo_customer_validation_simplr_enable_type');
			$this->_phoneFormId = 'input[name='.$this->_formKey.']';
			add_filter( 'simplr_validate_form', array($this,'simplr_site_registration_errors'),10,1);
		}


		
		function isPhoneVerificationEnabled()
		{
			return (strcasecmp($this->_otpType,$this->_typePhoneTag)==0 || strcasecmp($this->_otpType,$this->_typeBothTag)==0);
		}


		
	    function simplr_site_registration_errors($errors)
	    {
	    	global $phoneLogic;
	    	$password = $phone_number = "";
			MoUtility::checkSession();
			if(!empty($errors) || isset($_POST['fbuser_id'])) return $errors;
			
			foreach ($_POST as $key => $value)
			{
				if($key=="username")
					$username = $value;
				elseif ($key=="email")
					$email = $value;
				elseif ($key=="password")
					$password = $value;
				elseif ($key==$this->_formKey)
					$phone_number = $value;
				else
					$extra_data[$key]=$value;
			}
			if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0 
				&& !$this->processPhone($phone_number,$errors)) return $errors;
			$this->processAndStartOTPVerificationProcess($username,$email,$errors,$phone_number,$password,$extra_data);	
		}


		
		function processPhone($phone_number,&$errors)
		{
			if(!MoUtility::validatePhoneNumber($phone_number))
			{
				global $phoneLogic;
				$errors[].= str_replace("##phone##",$phone_number,$phoneLogic->_get_otp_invalid_format_message());
				add_filter($this->_formKey.'_error_class','_sreg_return_error');
				return FALSE;
			}
			return TRUE;
		}


		
		function processAndStartOTPVerificationProcess($username,$email,$errors,$phone_number,$password,$extra_data)
		{
			MoUtility::initialize_transaction($this->_formSessionVar);
			if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0)
				miniorange_site_challenge_otp($username,$email,$errors,$phone_number,"phone",$password,$extra_data);
			else if(strcasecmp($this->_otpType,$this->_typeBothTag)==0)
				miniorange_site_challenge_otp($username,$email,$errors,$phone_number,"both",$password,$extra_data);
			else
				miniorange_site_challenge_otp($username,$email,$errors,$phone_number,"email",$password,$extra_data);
		}


		
	    function register_simplr_user($user_login,$user_email,$password,$phone_number,$extra_data)
	    {
	    	$data = array(); 
	    	global $sreg;
	    	if( !$sreg ) $sreg = new stdClass;
	    	$data['username'] 	= $user_login;
	    	$data['email'] 		= $user_email;
	    	$data['password'] 	= $password;
	    	if($this->_formKey) $data[$this->_formKey] = $phone_number;
	    	$data = array_merge($data,$extra_data);
	    	$atts = $extra_data['atts'];
	    	$sreg->output = simplr_setup_user($atts,$data);
	    	if(MoUtility::isBlank($sreg->errors))
	    		$this->checkMessageAndRedirect($atts);
	    }


	    
	    function checkMessageAndRedirect()
	    {
	    	global $sreg,$simplr_options;
				
			$page = isset($atts['thanks']) ? get_permalink($atts['thanks']) 
					: (!MoUtility::isBlank($simplr_options->thank_you) ? get_permalink($simplr_options->thank_you) : '' );
			if(MoUtility::isBlank($page)) 
				$sreg->success = $sreg->output;
			else
			{
				wp_redirect($page);
				exit;
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
			$this->unsetOTPSessionVariables();
			$this->register_simplr_user($user_login,$user_email,$password,$phone_number,$extra_data);
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
			
			$this->_isFormEnabled = isset( $_POST['mo_customer_validation_simplr_default_enable']) ? $_POST['mo_customer_validation_simplr_default_enable'] : 0;
			$this->_otpType = isset( $_POST['mo_customer_validation_simplr_enable_type']) ? $_POST['mo_customer_validation_simplr_enable_type'] : '';
			$this->_phoneKey = isset( $_POST['simplr_phone_field_key']) ? $_POST['simplr_phone_field_key'] : '';

			update_mo_option('mo_customer_validation_simplr_default_enable',$this->_isFormEnabled);
			update_mo_option('mo_customer_validation_simplr_enable_type',$this->_otpType);
			update_mo_option('mo_customer_validation_simplr_field_key',$this->_phoneKey);
	    }
	}