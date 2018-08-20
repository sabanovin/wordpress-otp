<?php

	
	class ClassifyRegistrationForm extends FormHandler implements IFormHandler
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
			$this->_formSessionVar 	= FormSessionVars::CLASSIFY_REGISTER;
			$this->_typePhoneTag = 'classify_phone_enable';
			$this->_typeEmailTag = 'classify_email_enable';
			$this->_formKey = 'CLASSIFY_REGISTER';
			$this->_formName = mo_('Classify Theme Registration Form');
			$this->_isFormEnabled = get_mo_option('mo_customer_validation_classify_enable') ? TRUE : FALSE;
			$this->_phoneFormId = 'input[name=phone]';
			parent::__construct();
		}

		
		function handleForm()
		{
			$this->_otpType = get_mo_option('mo_customer_validation_classify_type');

			add_action( 'wp_enqueue_scripts', array($this,'_show_phone_field_on_page'));
			add_action( 'user_register', array($this,'save_phone_number'), 10, 1);

			$this->routeData();
		}

		function routeData()
		{
			MoUtility::checkSession();
			if(array_key_exists($this->_formSessionVar,$_SESSION) && $_SESSION[$this->_formSessionVar]=="success")
				$this->unsetOTPSessionVariables();
			else if(array_key_exists('option',$_POST) && $_POST['option']=="verify_user_classify")
				$this->_handle_classify_theme_form_post($_POST);				
		}	


		
		function _show_phone_field_on_page()
		{
			wp_enqueue_script('classifyscript', MOV_URL . 'includes/js/classify.min.js?version='.MOV_VERSION , array('jquery'));
		}


		
		function _handle_classify_theme_form_post($data)
		{
			$username = $data['username'];
			$email_id = $data['email'];
			$phone 	  = $data['phone'];
	       	
	       	if ( username_exists( $username )!=FALSE ) return;
			if ( email_exists( $email_id )!=FALSE ) return;

			MoUtility::initialize_transaction($this->_formSessionVar);
		   
			if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0)
				miniorange_site_challenge_otp($_POST['username'],$email_id,null,$phone,"phone",null,null);
			else if(strcasecmp($this->_otpType,$this->_typeEmailTag)==0)
				miniorange_site_challenge_otp($_POST['username'],$email_id,null, null,"email",null,null);
			else
				miniorange_site_challenge_otp($_POST['username'],$email_id,null, $phone,"both",null,null);
		}


		
		function save_phone_number($user_id)
		{
			MoUtility::checkSession();	
			if(array_key_exists('phone_number_mo',$_SESSION))
	        	update_user_meta($user_id, 'phone', $_SESSION['phone_number_mo']);
		}


		
		function handle_failed_verification($user_login,$user_email,$phone_number)
		{
			MoUtility::checkSession();
			if(!isset($_SESSION[$this->_formSessionVar])) return;
			$otpVerType = strcasecmp($this->_otpType,$this->_typePhoneTag)==0 ? "phone" 
							: (strcasecmp($this->_otpType,$this->_typeEmailTag)==0 ? "email" : "both" );
			$fromBoth = strcasecmp($otpVerType,"both")==0 ? TRUE : FALSE;
			miniorange_site_otp_validation_form($user_login,$user_email,$phone_number,MoUtility::_get_invalid_otp_method(),$otpVerType,$fromBoth);
		}


	    
		function handle_post_verification($redirect_to,$user_login,$user_email,$password,$phone_number,$extra_data)
		{
			MoUtility::checkSession();
			if(!isset($_SESSION[$this->_formSessionVar])) return;
			$_SESSION[$this->_formSessionVar] = 'success';	
		}
		

		
		public function unsetOTPSessionVariables()
		{
			unset($_SESSION[$this->_txSessionId]);
			unset($_SESSION[$this->_formSessionVar]);
		}


		
		public function getPhoneNumberSelector($selector)	
		{
			MoUtility::checkSession();
			if($this->isFormEnabled() && $this->_otpType==$this->_typePhoneTag) array_push($selector, $this->_phoneFormId); 
			return $selector;
		}


		
		function handleFormOptions()
		{
			if(!MoUtility::areFormOptionsBeingSaved()) return;

			$this->_otpType = isset( $_POST['mo_customer_validation_classify_type']) ? $_POST['mo_customer_validation_classify_type'] : '';
			$this->_isFormEnabled = isset( $_POST['mo_customer_validation_classify_enable']) ? $_POST['mo_customer_validation_classify_enable'] : 0;

			update_mo_option('mo_customer_validation_classify_enable',$this->_isFormEnabled);
			update_mo_option('mo_customer_validation_classify_type',$this->_otpType);
		}
	}