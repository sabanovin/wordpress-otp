<?php
	
	
	class WPFormsPlugin extends FormHandler implements IFormHandler
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
			$this->_formSessionVar = FormSessionVars::WPFORM;
			$this->_formEmailVer = FormSessionVars:: WPFORM_EMAIL_VER;	
			$this->_formPhoneVer = FormSessionVars:: WPFORM_PHONE_VER;
			$this->_phoneFormId = array();
			$this->_formKey = 'WPFORMS';
			$this->_typePhoneTag = "mo_wpform_phone_enable";
			$this->_typeEmailTag = "mo_wpform_email_enable";
			$this->_formName = mo_("WP Forms");
			$this->_isFormEnabled = get_mo_option('mo_customer_validation_wpform_enable') ? TRUE : FALSE;
			$this->_buttonText = get_mo_option('mo_customer_validation_wpforms_button_text');
			$this->_buttonText = !MoUtility::isBlank($this->_buttonText) ? $this->_buttonText : mo_("Click Here to send OTP");
			parent::__construct();
		}

		
		function handleForm()
		{	
			$this->_otpType = get_mo_option('mo_customer_validation_wpform_enable_type');
			$this->_formDetails = maybe_unserialize(get_mo_option('mo_customer_validation_wpform_forms'));
			if(empty($this->_formDetails)) return;
			
			if($this->_otpType==$this->_typePhoneTag) {
				foreach ($this->_formDetails as $key => $value) {
					array_push($this->_phoneFormId,'#wpforms-'.$key.'-field_'.$value["phonekey"]);
				}
			}
			
			add_action('wpforms_display_field_after',array($this,'showVerificationButton'),1,2);
			add_filter('wpforms_process_initial_errors',array($this,'validateForm'),1,2);
			
			$this->routeData();
		}


		
		public function isFormEnabled() 
		{
			return get_mo_option('mo_customer_validation_wpform_enable') ? true : false;
		}


		private function routeData()
		{
			if(!array_key_exists('option', $_GET)) return;
			switch (trim($_GET['option'])) 
			{
				case "miniorange-wpforms":
					$this->_send_otp($_POST);		break;
			}
		}


		
		function _send_otp($data)
		{
			MoUtility::checkSession();
			MoUtility::initialize_transaction($this->_formSessionVar);
			if($this->_otpType==$this->_typePhoneTag)
				$this->_processPhoneAndStartOTPVerificationProcess($data);
			else
				$this->_processEmailAndStartOTPVerificationProcess($data);
		}


		
		private function _processEmailAndStartOTPVerificationProcess($data)
		{
			if(!array_key_exists('user_email', $data) || !isset($data['user_email']))
				wp_send_json( MoUtility::_create_json_response(MoMessages::showMessage('ENTER_EMAIL'),MoConstants::ERROR_JSON_TYPE) );
			else
				$this->setSessionAndStartOTPVerification($data['user_email'],$data['user_email'],NULL,"email");
		}


		
		private function _processPhoneAndStartOTPVerificationProcess($data)
		{
			if(!array_key_exists('user_phone', $data) || !isset($data['user_phone']))
				wp_send_json( MoUtility::_create_json_response(MoMessages::showMessage('ENTER_PHONE'),MoConstants::ERROR_JSON_TYPE) );
			else
				$this->setSessionAndStartOTPVerification(trim($data['user_phone']),NULL,trim($data['user_phone']),"phone");		
		}


		
		private function setSessionAndStartOTPVerification($sessionvalue,$useremail,$phoneNumber,$otpType)
		{
			$_SESSION[ strcasecmp($this->_otpType,$this->_typePhoneTag)==0 ? $this->_formPhoneVer : $this->_formEmailVer ] = $sessionvalue;
			miniorange_site_challenge_otp('testUser',$useremail,NULL,$phoneNumber,$otpType);
		}


		
		public function showVerificationButton($field,$formData)
		{
			$formId = $formData['id'];
			if(!array_key_exists($formId,$this->_formDetails)) return;
			$formData = $this->_formDetails[$formId];
			if($this->_otpType==$this->_typePhoneTag && strcasecmp($field['label'],$formData['phoneLabel'])==0) {
				echo $this->getButtonAndScriptCode('phone',$formData,$formId);
			}
			elseif($this->_otpType==$this->_typeEmailTag && strcasecmp($field['label'],$formData['emailLabel'])==0) {
				echo $this->getButtonAndScriptCode('email',$formData,$formId);
			}
		}


		
		private function getButtonAndScriptCode($mo_type,$formData,$formId)
		{
			$button_title = $mo_type == "phone" ? mo_("Please Enter your phone details to enable this.") :  mo_("Please Enter your email to enable this.");
			$img = "<div style='display:table;text-align:center;'><img src='".MOV_URL. "includes/images/loader.gif'></div>";
			$field_content = '<div style="margin-top: 2%;"><div class="wpforms-submit-container"><button type="button" style="width:100%;"';
			$field_content .= 'class="wpforms-submit wpforms-page-button" id="miniorange_otp_token_submit" title="'.$button_title.'">';
			$field_content .= $this->_buttonText.'</button></div></div><div style="margin-top:2%">';
			$field_content .= '<div id="mo_message" hidden="" style="background-color: #f7f6f7;padding: 1em 2em 1em 3.5em;"></div></div>';
			$field_content .= '<script>jQuery(document).ready(function(){$mo=jQuery;$mo("#miniorange_otp_token_submit").click(function(o){'; 
			$field_content .= 'var e=$mo("#wpforms-'.$formId.'-field_'.$formData[$mo_type."key"].'").val();';
			$field_content .= '$mo("#mo_message").empty(),$mo("#mo_message").append("'.$img.'")';
			$field_content .= ',$mo("#mo_message").show(),$mo.ajax({url:"'.site_url().'/?option=miniorange-wpforms",type:"POST",data:{user_';
			$field_content .= $mo_type.':e},crossDomain:!0,dataType:"json",success:function(o){ if(o.result=="success"){$mo("#mo_message").empty()';
			$field_content .= ',$mo("#mo_message").append(o.message),$mo("#mo_message").css("border-top","3px solid green"),$mo("';
			$field_content .= '#wpforms-'.$formId.'-field_'.$formData[$mo_type."key"].'").focus()}else{$mo("#mo_message").empty(),';
			$field_content .= '$mo("#mo_message").append(o.message),$mo("#mo_message").css("border-top","3px solid red"),';
			$field_content .= '$mo("#wpforms-'.$formId.'-field_'.$formData["verifyKey"].'").focus()} ;},';
			$field_content .= 'error:function(o,e,n){}})});});</script>';
			return $field_content;
		}


		
		public function validateForm($errors,$formData)
		{
			MoUtility::checkSession();
			$id = $formData['id'];
			if(!array_key_exists($id,$this->_formDetails)) return;
			$formData = $this->_formDetails[$id];
			$errors = $this->checkIfOtpVerificationStarted($formData,$errors,$id);
			if(!empty($errors)) return $errors; 
			if(strcasecmp($this->_otpType,$this->_typeEmailTag)==0)
				$errors = $this->processEmail($formData,$errors,$id);
			if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0)
				$errors = $this->processPhone($formData,$errors,$id);
			if(empty($errors)) 
				$errors = $this->processOTPEntered($formData,$errors,$id);
			return $errors;
		}


		
		function processOTPEntered($formdata,$errors,$id)
		{
			$verify_field = $formdata['verifyKey'];	
			do_action('mo_validate_otp',NULL,$_POST['wpforms']['fields'][$verify_field]);
			if(strcasecmp($_SESSION[$this->_formSessionVar],'validated')!=0)
				$errors[$id][$verify_field]=MoUtility::_get_invalid_otp_method();
			else
				$this->unsetOTPSessionVariables();
			return $errors;
		}


		
		function checkIfOtpVerificationStarted($formdata,$errors,$id)
		{
			if(array_key_exists($this->_formSessionVar, $_SESSION)) return $errors;
			if(strcasecmp($this->_otpType,$this->_typeEmailTag)==0)
				$errors[$id][$formdata['emailkey']]=MoMessages::showMessage('ENTER_VERIFY_CODE');
			else
				$errors[$id][$formdata['phonekey']]=MoMessages::showMessage('ENTER_VERIFY_CODE');
			return $errors;
		}


		
		function processEmail($formdata,$errors,$id)
		{
			$field_id = $formdata['emailkey'];
			if($_SESSION[$this->_formEmailVer]!=$_POST['wpforms']['fields'][$field_id])
				$errors[$id][$field_id]=MoMessages::showMessage('EMAIL_MISMATCH');
			return $errors;
		}


		
		function processPhone($formdata,$errors,$id)
		{
			$field_id = $formdata['phonekey'];
			if($_SESSION[$this->_formPhoneVer]!= $_POST['wpforms']['fields'][$field_id])
				$errors[$id][$field_id]=MoMessages::showMessage('PHONE_MISMATCH');
			return $errors;
		}


		
		function handle_failed_verification($user_login,$user_email,$phone_number)
		{
			MoUtility::checkSession();			
			if(!isset($_SESSION[$this->_formSessionVar])) return;
			$_SESSION[$this->_formSessionVar] = 'verification_failed';
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
			unset($_SESSION[$this->_formPhoneVer]);
			unset($_SESSION[$this->_formEmailVer]);
		}
        

        
		public function getPhoneNumberSelector($selector)	
		{
			MoUtility::checkSession();
			if($this->isFormEnabled() && $this->_otpType==$this->_typePhoneTag) 
				$selector = array_merge($selector, $this->_phoneFormId); 
			return $selector;
		}


		
		function handleFormOptions()
	    {
            if(!MoUtility::areFormOptionsBeingSaved()) return;
			
			
			$form = $this->parseFormDetails();

			$this->_isFormEnabled = isset( $_POST['mo_customer_validation_wpform_enable']) ? $_POST['mo_customer_validation_wpform_enable'] : 0;
			$this->_otpType = isset( $_POST['mo_customer_validation_wpform_enable_type']) ? $_POST['mo_customer_validation_wpform_enable_type'] : '';
			$this->_buttonText = isset($_POST['mo_customer_validation_wpforms_button_text']) ? $_POST['mo_customer_validation_wpforms_button_text'] : '';
			$this->_formDetails = !empty($form) ? maybe_serialize($form) : "";

			update_mo_option('mo_customer_validation_wpform_enable', $this->_isFormEnabled);
			update_mo_option('mo_customer_validation_wpform_enable_type',$this->_otpType);
			update_mo_option('mo_customer_validation_wpforms_button_text',$this->_buttonText);
			update_mo_option('mo_customer_validation_wpform_forms', $this->_formDetails);
		}

		function parseFormDetails()
		{
			if(!array_key_exists('wpform',$_POST)) return array();
			foreach (array_filter($_POST['wpform']['form']) as $key => $value)
			{
				$formData = $this->getFormDataFromID($value);
				if(MoUtility::isBlank($formData)) continue;
				$fieldIds = $this->getFieldIDs($_POST,$key,$formData);
				$form[$value]= array(
					'emailkey'=> $fieldIds['emailKey'],
					'phonekey'=> $fieldIds['phoneKey'],
					'verifyKey'=> $fieldIds['verifyKey'],
					'phoneLabel'=> $_POST['wpform']['phoneLabel'][$key],
					'emailLabel'=> $_POST['wpform']['emailLabel'][$key],
					'verifyLabel'=> $_POST['wpform']['verifyLabel'][$key]
				);
			}
			return $form;
		}

		
		private function getFormDataFromId($id)
		{
			if(Moutility::isBlank($id)) return;
			$form = get_post( absint( $id ) );
			if(MoUtility::isBlank($id)) return;
			return wp_unslash( json_decode($form->post_content));
		}


		
		private function getFieldIDs($data,$key,$formData)
		{
			$fieldIds = array('emailKey'=>'','phoneKey'=>'','verifyKey'=>'');
			if(empty($data)) return;
			foreach($formData->fields as $field) {
				if(!property_exists($field,'label')) continue;
				if(strcasecmp($field->label,$data['wpform']['emailLabel'][$key])==0) $fieldIds['emailKey']=$field->id;
				if(strcasecmp($field->label,$data['wpform']['phoneLabel'][$key])==0) $fieldIds['phoneKey']=$field->id;
				if(strcasecmp($field->label,$data['wpform']['verifyLabel'][$key])==0) $fieldIds['verifyKey']=$field->id;
			}
			return $fieldIds;
		}
	}