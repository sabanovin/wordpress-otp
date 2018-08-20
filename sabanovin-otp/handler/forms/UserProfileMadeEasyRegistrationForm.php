<?php
	
	
	class UserProfileMadeEasyRegistrationForm extends FormHandler implements IFormHandler
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
			$this->_formSessionVar = FormSessionVars::UPME_REG;
			$this->_typePhoneTag = 'mo_upme_phone_enable';
			$this->_typeEmailTag = 'mo_upme_email_enable';
			$this->_typeBothTag = 'mo_upme_both_enable';
			$this->_formKey = 'UPME_FORM';
			$this->_formName = mo_("UserProfile Made Easy Registration Form");
			$this->_isFormEnabled = get_mo_option('mo_customer_validation_upme_default_enable') ? TRUE : FALSE;
			parent::__construct();
		}

		
		function handleForm()
		{
			$this->_otpType = get_mo_option('mo_customer_validation_upme_enable_type');
			$this->_phoneKey = get_mo_option('mo_customer_validation_upme_phone_key');
			$this->_phoneFormId = 'input[name='.$this->_phoneKey.']';

			MoUtility::checkSession();

			add_filter( 'insert_user_meta', array($this,'miniorange_upme_insert_user'),1,3);
			add_filter( 'upme_registration_custom_field_type_restrictions', array($this,'miniorange_upme_check_phone') , 1, 2);
						
			if(array_key_exists('upme-register-form',$_POST) && !array_key_exists($this->_formSessionVar,$_SESSION))
				$this->_handle_upme_form_submit($_POST);
			elseif(array_key_exists($this->_formSessionVar,$_SESSION) 
				&& strcasecmp($_SESSION[$this->_formSessionVar],'validated')==0)
				$this->unsetOTPSessionVariables();
		}


		
		function isPhoneVerificationEnabled()
		{
			return (strcasecmp($this->_otpType,$this->_typePhoneTag)==0 || strcasecmp($this->_otpType,$this->_typeBothTag)==0);
		}


		
		function _handle_upme_form_submit($POSTED)
		{
			foreach($POSTED as $key => $value)
			{
				if($key == $this->_phoneKey)
				{
					$mobile_number = $value;
					break;
				}
			}
			$this->miniorange_upme_user($_POST['user_login'],$_POST['user_email'],$mobile_number);
		}


		
		function miniorange_upme_insert_user($meta, $user, $update)
		{
			global $upme_save;
			MoUtility::checkSession();
			$file_upload = array_key_exists('file_upload',$_SESSION) ? $_SESSION['file_upload'] : null;
			if(!array_key_exists($this->_formSessionVar,$_SESSION) || is_null($file_upload)) return $meta;
			foreach ($file_upload as $key => $value)
			{
				$current_field_url = get_user_meta($user->ID, $key, true);
                if('' != $current_field_url) upme_delete_uploads_folder_files($current_field_url);                                
				update_user_meta($user->ID, $key, $value);
			}	
	    	return $meta;
		}


		
		function miniorange_upme_check_phone($errors,$fields)
		{
			global $phoneLogic;
			if(empty($errors))
				if($fields['meta'] ==$this->_phoneKey)
					if(!MoUtility::validatePhoneNumber($fields['value']))
						$errors[] = str_replace("##phone##",$value,$phoneLogic->_get_otp_invalid_format_message());
			return $errors;
		}


		
		function miniorange_upme_user($user_name,$user_email,$phone_number)
		{
			global $upme_register;
			$upme_register->prepare($_POST);
			$upme_register->handle();
			$file_upload = array();

			if(!MoUtility::isBlank($upme_register->errors)) return;

			MoUtility::checkSession();
			MoUtility::initialize_transaction($this->_formSessionVar);
			
			$this->processFileUpload($file_upload);
			$_SESSION['file_upload'] = $file_upload;
			$this->processAndStartOTPVerification($user_name,$user_email,$phone_number);
	 	}


	 	
	 	function processFileUpload(&$file_upload)
	 	{
	 		if(empty($_FILES)) return;

			$upload_dir =  wp_upload_dir();
			$target_path = $upload_dir['basedir'] . "/upme/";
		 	if (!is_dir($target_path)) mkdir($target_path, 0777);

            foreach ($_FILES as $key => $array)
            {
                $base_name = sanitize_file_name(basename($array['name']));
                $target_path = $target_path . time() . '_' . $base_name;
                $nice_url = $upload_dir['baseurl'] . "/upme/";
                $nice_url = $nice_url . time() . '_' . $base_name;
                move_uploaded_file($array['tmp_name'], $target_path);
				$file_upload[$key]=$nice_url;
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
			$_SESSION[$this->_formSessionVar]='validated';
		}


		
	 	function processAndStartOTPVerification($user_name,$user_email,$phone_number)
	 	{
	 		if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0)
				miniorange_site_challenge_otp($user_name,$user_email,null,$phone_number,"phone");
			else if(strcasecmp($this->_otpType,$this->_typeBothTag)==0)
				miniorange_site_challenge_otp($user_name,$user_email,null,$phone_number,"both");
			else
				miniorange_site_challenge_otp($user_name,$user_email,null,$phone_number,"email");	
	 	}


	 	
	 	function handleFormOptions()
	    {
			if(!MoUtility::areFormOptionsBeingSaved()) return;
			
			$this->_isFormEnabled = isset( $_POST['mo_customer_validation_upme_default_enable']) ? $_POST['mo_customer_validation_upme_default_enable'] : 0;
			$this->_otpType = isset( $_POST['mo_customer_validation_upme_enable_type']) ? $_POST['mo_customer_validation_upme_enable_type'] : '';
			$this->_phoneKey = isset( $_POST['upme_phone_field_key']) ? $_POST['upme_phone_field_key'] : '';

			update_mo_option('mo_customer_validation_upme_default_enable',$this->_isFormEnabled);
			update_mo_option('mo_customer_validation_upme_enable_type',$this->_otpType);
			update_mo_option('mo_customer_validation_upme_phone_key',$this->_phoneKey);
	    }
	}