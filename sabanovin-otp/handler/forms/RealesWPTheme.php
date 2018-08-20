<?php
	
			
			
	
	class RealesWPTheme extends FormHandler implements IFormHandler
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
            $this->_isAjaxForm = TRUE;
			$this->_formSessionVar = FormSessionVars::REALESWP_REGISTER;
			$this->_formPhoneVer = FormSessionVars::REALESWP_PHONE_VER;
			$this->_formEmailVer = FormSessionVars::REALESWP_EMAIL_VER;
			$this->_typePhoneTag = 'mo_reales_phone_enable';
			$this->_typeEmailTag = 'mo_reales_email_enable';
			$this->_phoneFormId = '#phoneSignup';
			$this->_formKey = 'REALES_REGISTER';
			$this->_formName = mo_("Reales WP Theme Registration Form");
			$this->_isFormEnabled = get_mo_option('mo_customer_validation_reales_enable') ? TRUE : FALSE;
			parent::__construct();
		}

		
		function handleForm()
		{
			$this->_otpType = get_mo_option('mo_customer_validation_reales_enable_type');
			add_action('wp_enqueue_scripts', array($this,'enqueue_script_on_page'));
			$this->routeData();
		}


		function routeData()
		{
			if(!array_key_exists('option', $_GET)) return;
			switch (trim($_GET['option'])) 
			{
				case "miniorange-realeswp-verify":
					$this->_send_otp_realeswp_verify($_POST);		break;
				case "miniorange-validate-realeswp-otp":
					$this->_reales_validate_otp($_POST);			break;
			}
		}


		
		function enqueue_script_on_page()
		{
			wp_register_script( 'realeswpScript', MOV_URL . 'includes/js/realeswp.min.js?version='.MOV_VERSION , array('jquery') );
			wp_localize_script('realeswpScript', 'movars', array(
				'imgURL'		=> MOV_URL. "includes/images/loader.gif",
				'fieldname' 	=> $this->_otpType==$this->_typePhoneTag ? 'phone number' : 'email',
				'field'     	=> $this->_otpType==$this->_typePhoneTag ? 'phoneSignup' : 'emailSignup',
				'siteURL' 		=> site_url(),
				'insertAfter'	=> $this->_otpType==$this->_typePhoneTag ? '#phoneSignup' : '#emailSignup',
				'placeHolder' 	=> mo_('OTP Code'),
				'buttonText'	=> mo_('Validate and Sign Up'),
				'ajaxurl'       => admin_url('admin-ajax.php'),
			));
			wp_enqueue_script('realeswpScript');
		}


		
		function _send_otp_realeswp_verify($data)
		{
			MoUtility::checkSession();
			MoUtility::initialize_transaction($this->_formSessionVar);
			if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0)
				$this->_send_otp_to_phone($data);
			else
				$this->_send_otp_to_email($data);
		}


		
		function _send_otp_to_phone($data)
		{
			if(array_key_exists('user_phone', $data) && !MoUtility::isBlank($data['user_phone']))
			{
				$_SESSION[$this->_formPhoneVer] = trim($data['user_phone']);
				miniorange_site_challenge_otp('test','',null, trim($data['user_phone']),"phone");
			}
			else
				wp_send_json( MoUtility::_create_json_response( MoMessages::showMessage('ENTER_PHONE'),MoConstants::ERROR_JSON_TYPE) );
		}


		
		function _send_otp_to_email($data)
		{
			if(array_key_exists('user_email', $data) && !MoUtility::isBlank($data['user_email']))
			{
				$_SESSION[$this->_formEmailVer] = $data['user_email'];
				miniorange_site_challenge_otp('test',$data['user_email'],null,$data['user_email'],"email");
			}
			else
				wp_send_json( MoUtility::_create_json_response( MoMessages::showMessage('ENTER_EMAIL'),MoConstants::ERROR_JSON_TYPE) );
		}


		
		function _reales_validate_otp($data)
		{
			MoUtility::checkSession();
			$moOTP = !isset($data['otp']) ? sanitize_text_field( $data['otp'] ) : '';
			
			$this->checkIfOTPVerificationHasStarted();
			$this->validateSubmittedFields($data);
			if(!array_key_exists($this->_txSessionId, $_SESSION)) return;

			$this->validateOTPRequest($moOTP);
		}


		
		function validateSubmittedFields($data)
		{
			
			if(array_key_exists($this->_formEmailVer, $_SESSION) 
				&& strcasecmp($_SESSION[$this->_formEmailVer],$data['user_email'])!=0)
				wp_send_json( MoUtility::_create_json_response( MoMessages::showMessage('EMAIL_MISMATCH'),MoConstants::ERROR_JSON_TYPE) );
			
			if(array_key_exists($this->_formPhoneVer, $_SESSION) 
				&& strcasecmp($_SESSION[$this->_formPhoneVer],$data['user_phone'])!=0)
				wp_send_json( MoUtility::_create_json_response( MoMessages::showMessage('PHONE_MISMATCH'),MoConstants::ERROR_JSON_TYPE) );
		}


		
		function checkIfOTPVerificationHasStarted()
		{
			if(!array_key_exists($this->_txSessionId, $_SESSION))
				wp_send_json( MoUtility::_create_json_response( MoMessages::showMessage('PLEASE_VALIDATE'),MoConstants::ERROR_JSON_TYPE) );
		}


		
		function handle_failed_verification($user_login,$user_email,$phone_number)
		{
			MoUtility::checkSession();
			if(!isset($_SESSION[$this->_formSessionVar])) return;
			wp_send_json( MoUtility::_create_json_response(MoUtility::_get_invalid_otp_method(),MoConstants::ERROR_JSON_TYPE) );
		}


		
		function handle_post_verification($redirect_to,$user_login,$user_email,$password,$phone_number,$extra_data)
		{
			MoUtility::checkSession();
			if(!isset($_SESSION[$this->_formSessionVar])) return;
			wp_send_json( MoUtility::_create_json_response(MoUtility::_get_invalid_otp_method(),MoConstants::ERROR_JSON_TYPE) );
		}


		
		function validateOTPRequest($moOTP)
		{
			do_action('mo_validate_otp',NULL,$moOTP);
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

			$this->_isFormEnabled = isset( $_POST['mo_customer_validation_reales_enable']) ? $_POST['mo_customer_validation_reales_enable'] : 0;
			$this->_otpType = isset( $_POST['mo_customer_validation_reales_enable_type']) ? $_POST['mo_customer_validation_reales_enable_type'] : 0;

			update_mo_option('mo_customer_validation_reales_enable',$this->_isFormEnabled);
			update_mo_option('mo_customer_validation_reales_enable_type',$this->_otpType);
		}
	}