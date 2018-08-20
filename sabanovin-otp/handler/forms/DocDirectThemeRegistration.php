<?php
		
	
	class DocDirectThemeRegistration extends FormHandler implements IFormHandler
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
			$this->_formSessionVar = FormSessionVars::DOCDIRECT_REG;
			$this->_formPhoneVer = FormSessionVars::DOCDIRECT_PHONE_VER;
			$this->_formEmailVer = FormSessionVars::DOCDIRECT_EMAIL_VER;
			$this->_typePhoneTag = 'mo_docdirect_phone_enable';
			$this->_typeEmailTag = 'mo_docdirect_email_enable';
			$this->_formKey = 'DOCDIRECT_THEME';
			$this->_formName = mo_('Doc Direct Theme by ThemoGraphics');
			$this->_isFormEnabled = get_mo_option('mo_customer_validation_docdirect_enable') ? TRUE : FALSE;
			$this->_phoneFormId = 'input[name=phone_number]';
			parent::__construct();
		}

		
		function handleForm()
		{
			$this->_otpType = get_mo_option('mo_customer_validation_docdirect_enable_type');
			add_action( 'wp_enqueue_scripts', array($this,'addScriptToRegistrationPage'));
			add_action('wp_ajax_docdirect_user_registration', array($this,'mo_validate_docdirect_user_registration'),1);
			add_action('wp_ajax_nopriv_docdirect_user_registration', array($this,'mo_validate_docdirect_user_registration'),1);
			$this->routeData();
		}


		function routeData()
		{
			if(!array_key_exists('option', $_GET)) return;
			switch (trim($_GET['option'])) 
			{
				case "miniorange-docdirect-verify":
					$this->startOTPVerificationProcess($_POST);			break;
			}
		}
		

		
		function addScriptToRegistrationPage()
		{
			wp_register_script( 'docdirect', MOV_URL . 'includes/js/docdirect.min.js?version='.MOV_VERSION , array('jquery') ,MOV_VERSION,true);
			wp_localize_script( 'docdirect', 'modocdirect', array(
				'imgURL'		=> MOV_URL. "includes/images/loader.gif",
				'buttonText' 	=> mo_("Click Here to Verify Yourself"),
				'insertAfter'	=> strcasecmp($this->_otpType,$this->_typePhoneTag)==0 ? 'input[name=phone_number]' : 'input[name=email]',
				'placeHolder' 	=> mo_('OTP Code'),
				'siteURL' 		=> 	site_url(),
			));
			wp_enqueue_script('docdirect');
		}


		
		function startOtpVerificationProcess($data)
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


		
		function mo_validate_docdirect_user_registration()
		{
			MoUtility::checkSession();
			$this->checkIfVerificationNotStarted();
			$this->checkIfVerificationCodeNotEntered();
			$this->handle_otp_token_submitted();
		}


		
		function checkIfVerificationNotStarted()
		{
			if(!isset($_SESSION[$this->_formSessionVar])) {
				echo json_encode( array('type' => 'error', 'message' =>  MoMessages::showMessage('DOC_DIRECT_VERIFY')) );
				die();
			}
		}

		
		
		function checkIfVerificationCodeNotEntered()
		{
			if(!array_key_exists('mo_verify', $_POST) || MoUtility::isBlank($_POST['mo_verify'])){
				echo json_encode( array('type' => 'error', 'message' =>  MoMessages::showMessage('DCD_ENTER_VERIFY_CODE')) );
				die();
			}
		}


		
		function handle_otp_token_submitted()
		{
			if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0) $this->processPhoneNumber();
			else $this->processEmail();
			do_action('mo_validate_otp','mo_verify',NULL);
		}


		
		function processPhoneNumber()
		{
			MoUtility::checkSession();
			if(strcasecmp($_SESSION[$this->_formPhoneVer], MoUtility::processPhoneNumber($_POST['phone_number']))!=0) {
				echo json_encode( array('type' => 'error', 'message' =>  MoMessages::showMessage('PHONE_MISMATCH')) );
				die();
			}
		}


		
		function processEmail()
		{
			MoUtility::checkSession();
			if(strcasecmp($_SESSION[$this->_formEmailVer], $_POST['email'])!=0) {
				echo json_encode( array('type' => 'error', 'message' =>  MoMessages::showMessage('EMAIL_MISMATCH')) );
				die();
			}
		}


		
		function handle_failed_verification($user_login,$user_email,$phone_number)
		{
			MoUtility::checkSession();
			if(!isset($_SESSION[$this->_formSessionVar])) return;
			echo json_encode( array('type' => 'error', 'message' =>  MoUtility::_get_invalid_otp_method()) );
			die();
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
			unset($_SESSION[$this->_formPhoneVer]);
			unset($_SESSION[$this->_formEmailVer]);
		}


		
		public function getPhoneNumberSelector($selector)	
		{
			MoUtility::checkSession();
			if($this->isFormEnabled() && ($this->_otpType == $this->_typePhoneTag)) array_push($selector, $this->_phoneFormId); 
			return $selector;
		}


				
		function handleFormOptions()
		{
			if(!MoUtility::areFormOptionsBeingSaved()) return;

			$this->_otpType = isset( $_POST['mo_customer_validation_docdirect_enable_type']) ? $_POST['mo_customer_validation_docdirect_enable_type'] : '';
			$this->_isFormEnabled = isset( $_POST['mo_customer_validation_docdirect_enable']) ? $_POST['mo_customer_validation_docdirect_enable'] : 0;
			
			update_mo_option('mo_customer_validation_docdirect_enable',$this->_isFormEnabled);
			update_mo_option('mo_customer_validation_docdirect_enable_type', $this->_otpType);
		}
	}