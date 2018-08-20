<?php
	
	
	class GravityForm extends FormHandler implements IFormHandler
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
			$this->_formSessionVar = FormSessionVars::GF_FORMS;
			$this->_formEmailVer = FormSessionVars::GF_EMAIL_VER;
			$this->_formPhoneVer = FormSessionVars::GF_PHONE_VER;
			$this->_typePhoneTag = 'mo_gf_contact_phone_enable';
			$this->_typeEmailTag = 'mo_gf_contact_email_enable';
			$this->_formKey = 'GRAVITY_FORM';
			$this->_formName = mo_('Gravity Form');
			$this->_isFormEnabled = get_mo_option('mo_customer_validation_gf_contact_enable') ? TRUE : FALSE;
			$this->_phoneFormId =  ".ginput_container_phone input";
			parent::__construct();
		}

		
		function handleForm()
		{
			$this->_otpType = get_mo_option('mo_customer_validation_gf_contact_type');
			$this->_formDetails = maybe_unserialize(get_mo_option('mo_customer_validation_gf_otp_enabled'));
			if(empty($this->_formDetails)) return;
			add_filter('gform_field_content',array($this,'_add_scripts'),1,5);
			add_filter('gform_field_validation',array($this,'validate_form_submit'),1,5);
				
			$this->routeData();
		}


		function routeData()
		{
			if(!array_key_exists('option', $_GET)) return;

			switch (trim($_GET['option'])) 
			{
				case "miniorange-gf-contact":
					$this->_handle_gf_form($_POST);		break;
			}
		}


		
		function _handle_gf_form($getdata)
		{
			MoUtility::checkSession();
			MoUtility::initialize_transaction($this->_formSessionVar);
			
			$this->processEmailAndStartOTPVerificationProcess($getdata);
			$this->processPhoneAndStartOTPVerificationProcess($getdata);
			$this->sendErrorMessageIfOTPVerificationNotStarted();
		}


		
		function processEmailAndStartOTPVerificationProcess($getdata)
		{
			if(!array_key_exists('user_email', $getdata) || !isset($getdata['user_email'])) return;

			if(MoUtility::isBlank($getdata['user_email'])) 
				wp_send_json( MoUtility::_create_json_response(MoMessages::showMessage('ENTER_EMAIL'),MoConstants::ERROR_JSON_TYPE) );
			
			$_SESSION[$this->_formEmailVer] = $getdata['user_email'];
			miniorange_site_challenge_otp('testuser',$getdata['user_email'],null,$getdata['user_email'],"email");
		}


		
		function processPhoneAndStartOTPVerificationProcess($getdata)
		{
			if(!array_key_exists('user_phone', $getdata) || !isset($getdata['user_phone'])) return;

			if(MoUtility::isBlank($getdata['user_phone'])) 
				wp_send_json( MoUtility::_create_json_response(MoMessages::showMessage('ENTER_EMAIL'),MoConstants::ERROR_JSON_TYPE) );
			
			$_SESSION[$this->_formPhoneVer] = trim($getdata['user_phone']);
			miniorange_site_challenge_otp('testuser','',null, trim($getdata['user_phone']),"phone");
		}
		

		
		function sendErrorMessageIfOTPVerificationNotStarted()
		{
			if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0)
				wp_send_json( MoUtility::_create_json_response(MoMessages::showMessage('ENTER_PHONE'),MoConstants::ERROR_JSON_TYPE) );
			else
				wp_send_json( MoUtility::_create_json_response(MoMessages::showMessage('ENTER_EMAIL'),MoConstants::ERROR_JSON_TYPE) );
		}


		
		function _add_scripts($field_content, $field, $value, $zero, $form_id)
		{
			if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0)
			{   
				foreach($this->_formDetails[0] as $f)
					if($f==$form_id && get_class($field)=="GF_Field_Phone")
						$field_content = $this->_add_shortcode_to_form('phone',$field_content,$field,$form_id);
			}
			else												
			{	
				foreach($this->_formDetails[1] as $f)
					if($f==$form_id && get_class($field)=="GF_Field_Email")
						$field_content = $this->_add_shortcode_to_form('email',$field_content,$field,$form_id);
			}
			return $field_content;	
		}


		
		function _add_shortcode_to_form($mo_type,$field_content,$field,$form_id) 
		{
			$img = "<div style='display:table;text-align:center;'><img src='".MOV_URL. "includes/images/loader.gif'></div>";
			$field_content .= "<div style='margin-top: 2%;'><button type='button' class='button alt' style='width:100%;height:30px;";
			$field_content .= "font-family: Roboto;font-size: 12px !important;' id='miniorange_otp_token_submit' title='Please Enter an " . $mo_type . " to enable this'>";
			$field_content .= "Click Here to Verify ". $mo_type."</button></div><div style='margin-top:2%'>";
			$field_content .= "<div id='mo_message' hidden='' style='background-color: #f7f6f7;padding: 1em 2em 1em 3.5em;''></div></div>";
			$field_content .= '<script>jQuery(document).ready(function(){$mo=jQuery;$mo("#miniorange_otp_token_submit").click(function(o){'; 
			$field_content .= 'var e=$mo("#input_'.$form_id.'_'.$field->id.'").val(); $mo("#mo_message").empty(),$mo("#mo_message").append("'.$img.'")';
			$field_content .= ',$mo("#mo_message").show(),$mo.ajax({url:"'.site_url().'/?option=miniorange-gf-contact",type:"POST",data:{user_';
			$field_content .= $mo_type.':e},crossDomain:!0,dataType:"json",success:function(o){ if(o.result=="success"){$mo("#mo_message").empty()';
			$field_content .= ',$mo("#mo_message").append(o.message),$mo("#mo_message").css("border-top","3px solid green"),$mo("';
			$field_content .= 'input[name=email_verify]").focus()}else{$mo("#mo_message").empty(),$mo("#mo_message").append(o.message),';
			$field_content .= '$mo("#mo_message").css("border-top","3px solid red"),$mo("input[name=phone_verify]").focus()} ;},';
			$field_content .= 'error:function(o,e,n){}})});});</script>';
			return $field_content;
		}


		
		function validate_form_submit( $error, $value, $form, $field )
		{  
		    if($this->_otpType==$this->_typePhoneTag)
				foreach($this->_formDetails[0] as $f)
					$error = $this->validate_otp($field,$error,$f,$value);
			else
				foreach($this->_formDetails[1] as $f)
					$error = $this->validate_otp($field,$error,$f,$value);											
			return $error;
		}


		
		function validate_otp($field,$error,$f,$value)
		{
			MoUtility::checkSession();
			if(!array_key_exists($this->_txSessionId, $_SESSION) && $f==$field->formId 
				&& ( ($this->_otpType==$this->_typePhoneTag && get_class($field)=="GF_Field_Phone") || 
				($this->_otpType==$this->_typeEmailTag && get_class($field)=="GF_Field_Email")))
				return array('is_valid'=>null,'message'=> MoMessages::showMessage('PLEASE_VALIDATE'));
			
			if(strpos($field->label, 'Enter Validation Code') !== false && $error['is_valid']==1 && $f==$field->formId 
				&& array_key_exists($this->_txSessionId, $_SESSION))
				$error = $this->processOTPEntered($value,$error);

			if($this->_otpType==$this->_typePhoneTag && strpos($field->label, 'Phone') !== false)
				$error = $this->validate_submitted_email_or_phone($error['is_valid'],$value,$error);
			
			if($this->_otpType==$this->_typeEmailTag && strpos($field->label, 'Email') !== false)
				$error = $this->validate_submitted_email_or_phone($error['is_valid'],$value,$error);

			if(empty($error)) $this->unsetOTPSessionVariables();

			return $error;
		}


		
		function validateOTPRequest($value)
		{
			do_action('mo_validate_otp',NULL,$value);
		}


		
		function processOTPEntered($value,$error)
		{
			$this->validateOTPRequest($value);
			return strcasecmp($_SESSION[$this->_formSessionVar],'validated')!=0 
				? array('is_valid'=>null,'message'=> MoUtility::_get_invalid_otp_method()) : $error;
		}


		
		function validate_submitted_email_or_phone($isValid,$value,$error)
		{
			if($isValid)
			{
				if(array_key_exists($this->_formEmailVer, $_SESSION) 
					&& strcasecmp($_SESSION[$this->_formEmailVer], $value)!=0)
					return array('is_valid'=>null,'message'=>MoMessages::showMessage('EMAIL_MISMATCH'));
				if(array_key_exists($this->_formPhoneVer, $_SESSION) 
					&& strcasecmp($_SESSION[$this->_formPhoneVer], $value)!=0)
					return array('is_valid'=>null,'message'=>MoMessages::showMessage('PHONE_MISMATCH'));
			}
			return $error;
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
			unset($_SESSION[$this->_formEmailVer]);
			unset($_SESSION[$this->_formPhoneVer]);
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

			$this->_isFormEnabled = isset( $_POST['mo_customer_validation_gf_contact_enable']) ? $_POST['mo_customer_validation_gf_contact_enable'] : 0;
			$this->_otpType = isset( $_POST['mo_customer_validation_gf_contact_type']) ? $_POST['mo_customer_validation_gf_contact_type'] : '';
			$this->_formDetails = isset( $_POST['gravity_form']) ? maybe_serialize(array_filter($_POST['gravity_form'])) : '';

			update_mo_option('mo_customer_validation_gf_otp_enabled', $this->_formDetails);
			update_mo_option('mo_customer_validation_gf_contact_enable', $this->_isFormEnabled);
			update_mo_option('mo_customer_validation_gf_contact_type', $this->_otpType);
		}
	}