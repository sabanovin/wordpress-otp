<?php

	
	class RegistrationMagicForm extends FormHandler implements IFormHandler
	{
		private $_formIDSession;
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
			$this->_formSessionVar = FormSessionVars::CRF_DEFAULT_REG;
			$this->_typePhoneTag = 'mo_crf_phone_enable';
			$this->_typeEmailTag = 'mo_crf_email_enable';
			$this->_typeBothTag = 'mo_crf_both_enable';
			$this->_formKey = 'CRF_FORM';
			$this->_formName = mo_('Custom User Registration Form Builder (Registration Magic)');
			$this->_isFormEnabled = get_mo_option('mo_customer_validation_crf_default_enable') ? TRUE : FALSE;
			$this->_phoneFormId = array();
			parent::__construct();
		}

		
		function handleForm()
		{
			$this->_otpType = get_mo_option('mo_customer_validation_crf_enable_type');
			$this->_formDetails =  maybe_unserialize(get_mo_option('mo_customer_validation_crf_otp_enabled'));
			if(empty($this->_formDetails)) return;
			foreach ($this->_formDetails as $key => $value) {
				array_push($this->_phoneFormId,'input[name='.$this->getFieldID($value['phonekey']).']');
			}
			
			if(!$this->checkIfPromptForOTP()) return;
			$this->_handle_crf_form_submit($_REQUEST);
		}

		
		private function checkIfPromptForOTP()
		{
			if(array_key_exists('option',$_POST) || !array_key_exists('rm_form_sub_id',$_POST)) return FALSE;
			foreach($this->_formDetails as $key => $value) {
				if (strpos($_POST['rm_form_sub_id'], 'form_' . $key . '_') !== FALSE){
					MoUtility::checkSession();
					$_SESSION[$this->_formIDSession] = $key;
					return TRUE;
				} 
			}
			return FALSE;
		}


		
		private function isPhoneVerificationEnabled()
		{
			return (strcasecmp($this->_otpType,$this->_typePhoneTag)==0 || strcasecmp($this->_otpType,$this->_typeBothTag)==0);
		}


		
		private function _handle_crf_form_submit($requestdata)
		{
			MoUtility::checkSession();
			if($this->checkIfValidated()) return;
			$email = $this->_otpType == $this->_typeEmailTag || $this->_otpType == $this->_typeBothTag 
				? $this->getCRFEmailFromRequest($requestdata) : "";
			$phone = $this->isPhoneVerificationEnabled() ? $this->getCRFPhoneFromRequest($requestdata) : "";
			$this->miniorange_crf_user($email, isset($requestdata['user_name']) ? $requestdata['user_name'] : NULL ,$phone);
		}


		
		private function checkIfValidated() 
		{
			if(array_key_exists($this->_formSessionVar,$_SESSION) && $_SESSION[$this->_formSessionVar]=='validated') {
				$this->unsetOTPSessionVariables();
				return TRUE;
			}
			return FALSE;
		}


		
		private function getCRFEmailFromRequest($requestdata)
		{
			$emailKey = $this->_formDetails[$_SESSION[$this->_formIDSession]]['emailkey'];
			return $this->getFormPostSubmittedValue($this->getFieldID($emailKey),$requestdata);
		}


		
		private function getCRFPhoneFromRequest($requestdata)
		{
			$phonekey = $this->_formDetails[$_SESSION[$this->_formIDSession]]['phonekey'];
			return $this->getFormPostSubmittedValue($this->getFieldID($phonekey),$requestdata);
		}


		
		private function getFormPostSubmittedValue($reg1,$requestdata)
		{
			return isset($requestdata[$reg1]) ? $requestdata[$reg1] : "";
		}


		
		private function getFieldID($key)
		{
			global $wpdb;
			$crf_fields =$wpdb->prefix."rm_fields";
			$row1 = $wpdb->get_row("SELECT * FROM $crf_fields where field_label ='".$key."'");
			return isset($row1) ? $row1->field_type.'_'.$row1->field_id : "null";
		}


		
		private function miniorange_crf_user($user_email,$user_name,$phone_number)
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
			$_SESSION[$this->_formSessionVar] = 'validated';	
		}


		
		public function unsetOTPSessionVariables()
		{
			unset($_SESSION[$this->_txSessionId]);
			unset($_SESSION[$this->_formSessionVar]);
			unset($_SESSION[$this->_formIDSession]);
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

			$form = $this->parseFormDetails();

			$this->_formDetails = !empty($form) ? $form : "";
			$this->_isFormEnabled = isset( $_POST['mo_customer_validation_crf_default_enable']) ? $_POST['mo_customer_validation_crf_default_enable'] : 0;
			$this->_otpType = isset( $_POST['mo_customer_validation_crf_enable_type']) ? $_POST['mo_customer_validation_crf_enable_type'] : 0;
		
			update_mo_option('mo_customer_validation_crf_default_enable', $this->_isFormEnabled);
			update_mo_option('mo_customer_validation_crf_enable_type', $this->_otpType);
			update_mo_option('mo_customer_validation_crf_otp_enabled',maybe_serialize($this->_formDetails));
		}

		function parseFormDetails()
		{
			if(!array_key_exists('crf_form',$_POST) && empty($_POST['crf_form']['form'])) return array();
			foreach (array_filter($_POST['crf_form']['form']) as $key => $value) {
				$form[$value]=array('emailkey'=>$_POST['crf_form']['emailkey'][$key],
									'phonekey'=>$_POST['crf_form']['phonekey'][$key]);
			}
			return $form;
		}
	}