<?php 
		
	
	class NinjaForm extends FormHandler implements IFormHandler
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
			$this->_formSessionVar = FormSessionVars::NINJA_FORM;
			$this->_typePhoneTag = 'mo_ninja_form_phone_enable';
			$this->_typeEmailTag = 'mo_ninja_form_email_enable';
			$this->_typeBothTag = 'mo_ninja_form_both_enable';
			$this->_formKey = 'NINJA_FORM';
			$this->_formName = mo_('Ninja Forms ( Below version 3.0 )');
			$this->_isFormEnabled = get_mo_option('mo_customer_validation_ninja_form_enable') ? TRUE : FALSE;
			parent::__construct();
		}

		
		function handleForm()
		{
			$this->_otpType = get_mo_option('mo_customer_validation_ninja_form_enable_type');
			$this->_formDetails = maybe_unserialize(get_mo_option('mo_customer_validation_ninja_form_otp_enabled'));
			if(empty($this->_formDetails)) return;
			foreach ($this->_formDetails as $key => $value) {
				array_push($this->_phoneFormId,'input[name=ninja_forms_field_'.$value['phonekey'].']');
			}

			if($this->checkIfOTPOptions()) return;
			if($this->checkIfNinjaFormSubmitted()) $this->_handle_ninja_form_submit($_REQUEST);
		}


		
		function checkIfOTPOptions()
		{
			return array_key_exists('option',$_POST) && (strpos($_POST['option'], 'verification_resend_otp_') 
				|| $_POST['option']=='miniorange-validate-otp-form' || $_POST['option']=='miniorange-validate-otp-choice-form');
		}


		
		function checkIfNinjaFormSubmitted()
		{
			return array_key_exists('_ninja_forms_display_submit',$_REQUEST)  && array_key_exists('_form_id',$_REQUEST);
		}


		
		function isPhoneVerificationEnabled()
		{
			return (strcasecmp($this->_otpType,$this->_typePhoneTag)==0 || strcasecmp($this->_otpType,$this->_typeBothTag)==0);
		}


		
		function isEmailVerificationEnabled()
		{
			return (strcasecmp($this->_otpType,$this->_typeEmailTag)==0 || strcasecmp($this->_otpType,$this->_typeBothTag)==0);
		}
		

		
		function _handle_ninja_form_submit($requestdata) 
		{
			if(!array_key_exists($requestdata['_form_id'],$this->_formDetails)) return; 
			$formdata = $this->_formDetails[$requestdata['_form_id']];
			$email = $this->processEmail($formdata,$requestdata); 			
			$phone = $this->processPhone($formdata,$requestdata); 			
			$this->miniorange_ninja_form_user($email,null,$phone);
		}


		
		function processPhone($formdata,$requestdata)
		{
			if($this->isPhoneVerificationEnabled())
			{
				$field = "ninja_forms_field_".$formdata['phonekey'];
				return array_key_exists($field,$requestdata) ? $requestdata[$field] : NULL;
			}
		}


		
		function processEmail($formdata,$requestdata)
		{
			if($this->isEmailVerificationEnabled())
			{
				$field = "ninja_forms_field_".$formdata['emailkey'];
				return array_key_exists($field,$requestdata) ? $requestdata[$field] : NULL;
			}
		}


		
		function miniorange_ninja_form_user($user_email,$user_name,$phone_number)
		{
			MoUtility::checkSession();
			MoUtility::initialize_transaction($this->_formSessionVar);
			$errors = new WP_Error();
			if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0)
				miniorange_site_challenge_otp($user_name,$user_email,$errors,$phone_number,"phone");
			else if(strcasecmp($this->_otpType,$this->_typeBothTag)==0)
				miniorange_site_challenge_otp($user_name,$user_email,$errors,$phone_number,"both");
			else
				miniorange_site_challenge_otp($user_name,$user_email,$errors,$phone_number,"email");
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
			if($this->isFormEnabled() && $this->isPhoneVerificationEnabled()) $selector = array_merge($selector, $this->_phoneFormId); 
			return $selector;
		}


		
		function handleFormOptions()
		{
			if(!MoUtility::areFormOptionsBeingSaved()) return;
			if(isset($_POST['mo_customer_validation_nja_enable'])) return;

			$form = $this->parseFormDetails();

			$this->_isFormEnabled = isset( $_POST['mo_customer_validation_ninja_form_enable']) ? $_POST['mo_customer_validation_ninja_form_enable'] : 0;
			$this->_otpType = isset( $_POST['mo_customer_validation_ninja_form_enable_type']) ? $_POST['mo_customer_validation_ninja_form_enable_type'] : '';
			$this->_formDetails = !empty($form) ? maybe_serialize($form) : "";
			
			update_mo_option('mo_customer_validation_ninja_form_enable',$this->_isFormEnabled);
			update_mo_option('mo_customer_validation_nja_enable',0);
			update_mo_option('mo_customer_validation_ninja_form_enable_type',$this->_otpType);
			update_mo_option('mo_customer_validation_ninja_form_otp_enabled',$this->_formDetails);
		}

		function parseFormDetails()
		{
			if(!array_key_exists('ninja_form',$_POST)) return array();
			foreach (array_filter($_POST['ninja_form']['form']) as $key => $value) {
				$form[$value]=array('emailkey'=>$_POST['ninja_form']['emailkey'][$key],
									'phonekey'=>$_POST['ninja_form']['phonekey'][$key]);
			}
			return $form;
		}
		
	}