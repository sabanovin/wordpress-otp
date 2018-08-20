<?php
		
	
	class UserProRegistrationForm extends FormHandler implements IFormHandler
	{
		private $_userAjaxCheck;
		private $_userFieldMeta;

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
			$this->_formSessionVar = FormSessionVars::USERPRO_FORM;
			$this->_formEmailVer = FormSessionVars::USERPRO_EMAIL_VER;
			$this->_formPhoneVer = FormSessionVars::USERPRO_PHONE_VER;
			$this->_typePhoneTag = 'mo_userpro_registration_phone_enable';
			$this->_typeEmailTag = 'mo_userpro_registration_email_enable';
			$this->_phoneFormId = "input[data-label='Phone Number']";
			$this->_userAjaxCheck = "mo_phone_validation";
			$this->_userFieldMeta = "verification_form";
			$this->_formKey = 'USERPRO_FORM';
			$this->_formName = mo_("UserPro Form");
			$this->_isFormEnabled = get_mo_option('mo_customer_validation_userpro_default_enable') ? TRUE : FALSE;
			parent::__construct();
		}

		
		function handleForm()
		{
			$this->_otpType = get_mo_option('mo_customer_validation_userpro_enable_type');
			$this->_disableAutoActivate = get_mo_option('mo_customer_validation_userpro_verify');
			if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0)
			{
				add_action('wp_ajax_userpro_side_validate', array($this,'validate_userpro_phone'),1);
				add_action('wp_ajax_nopriv_userpro_side_validate', array($this,'validate_userpro_phone'),1);
			}

			add_filter('userpro_register_validation',array($this,'_process_userpro_form_submit'),1,2);
			add_action('userpro_after_new_registration',array($this,'_auto_verify_user'),1,1);
			add_shortcode('mo_verify_email_userpro', array($this,'_userpro_email_shortcode') );
			add_shortcode('mo_verify_phone_userpro', array($this,'_userpro_phone_shortcode') );

			$this->routeData();
		}


		function routeData()
		{
			if(!array_key_exists('option', $_GET)) return;
			switch (trim($_GET['option'])) 
			{
				case "miniorange-userpro-form":
					$this->_send_otp($_POST);			break;
			}
		}


		
		function _auto_verify_user($user_id)
		{
			if($this->_disableAutoActivate) update_user_meta($user_id,'userpro_verified', 1);
		}


		
		function validate_userpro_phone()
		{
			global $phoneLogic;
			if($this->checkIfUserHasNotSubmittedTheFormForValidation()) return;
			
			$message = MoUtility::_get_invalid_otp_method();
			if(strcasecmp($_POST['ajaxcheck'],$this->_userAjaxCheck)!=0) return;
			if(!MoUtility::validatePhoneNumber("+".trim($_POST['input_value']))) wp_send_json(array('error'=>$message));
		}


		
		function checkIfUserHasNotSubmittedTheFormForValidation()
		{
			return isset($_POST['action']) && isset($_POST['ajaxcheck']) 
					&& isset($_POST['input_value']) && $_POST['action'] != 'userpro_side_validate' ? TRUE : FALSE;
		}


		
		function _send_otp($getdata)
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
			
			$_SESSION[$this->_formEmailVer] = $getdata['user_email'];
			miniorange_site_challenge_otp('testuser',$getdata['user_email'],null,$getdata['user_email'],"email");
		}


		
		function processPhoneAndStartOTPVerificationProcess($getdata)
		{
			if(!array_key_exists('user_phone', $getdata) || !isset($getdata['user_phone'])) return;
			
			$_SESSION[$this->_formPhoneVer] = trim($getdata['user_phone']);
			miniorange_site_challenge_otp('testuser','',null, trim($getdata['user_phone']),"phone");
		}
		

		
		function sendErrorMessageIfOTPVerificationNotStarted()
		{
			if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0)
				wp_send_json( MoUtility::_create_json_response( MoMessages::showMessage('ENTER_PHONE'),MoConstants::ERROR_JSON_TYPE) );
			else
				wp_send_json( MoUtility::_create_json_response( MoMessages::showMessage('ENTER_EMAIL'),MoConstants::ERROR_JSON_TYPE) );
		}


		
		function _process_userpro_form_submit($output,$form)
		{
			MoUtility::checkSession();

			if(!$this->checkIfValidFormSubmition($output,$form)) return $output;
			
			if(array_key_exists($this->_formEmailVer, $_SESSION) && strcasecmp($_SESSION[$this->_formEmailVer], $form['user_email'])!=0)
				$output['user_email'] =  MoMessages::showMessage('EMAIL_MISMATCH');
			
			if(array_key_exists($this->_formPhoneVer, $_SESSION) && strcasecmp($_SESSION[$this->_formPhoneVer], $form['phone_number'])!=0)
				$output['phone_number'] =  MoMessages::showMessage('PHONE_MISMATCH');

			$this->processOTPEntered($output,$form);
			return $output;
		}


		
		function checkIfValidFormSubmition(&$output,$form)
		{
			if(!array_key_exists($this->_formSessionVar, $_SESSION) && array_key_exists($this->_userFieldMeta,$form))
			{
				$output[$this->_userFieldMeta] =  MoMessages::showMessage('USERPRO_VERIFY');
				return FALSE;
			}
			return TRUE;
		}


		
		function validateOTPRequest($value)
		{
			do_action('mo_validate_otp',NULL,$value);
		}


		
		function processOTPEntered(&$output,$form)
		{
			if(!empty($output)) return;
			$this->validateOTPRequest($form[$this->_userFieldMeta]);
			if(strcasecmp($_SESSION[$this->_formSessionVar],'validated') != 0) 
				$output[$this->_userFieldMeta] = MoUtility::_get_invalid_otp_method();
			else
				$this->unsetOTPSessionVariables();
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


		
		function _userpro_phone_shortcode()
		{
			$img 			 = "<div style='display:table;text-align:center;'><img src='".MOV_URL. "includes/images/loader.gif'></div>";
			$htmlcontent 	 = "<div style='margin-top: 2%;'><button type='button' class='button alt' style='width:100%;height:30px;";
			$htmlcontent 	.= "font-family: Roboto;font-size: 12px !important;' id='miniorange_otp_token_submit' ";
			$htmlcontent  	.= "title='".mo_('Please Enter a phone number to enable this')."'>".mo_('Click Here to Verify Phone')."</button></div>";
			$htmlcontent 	.= "<div style='margin-top:2%'><div id='mo_message' hidden='' style='background-color: "; 
			$htmlcontent	.= "#f7f6f7;padding: 1em 2em 1em 3.5em;''></div></div>";
			$html 		 	 = '<script>jQuery(document).click(function(e){$mo=jQuery;if($mo("#miniorange_otp_token_submit").length==0){';
			$html 			.= 'var unique_id=$mo("#unique_id").val();var phone_field="#phone_number-"+unique_id;if($mo(phone_field).length)';
			$html 			.= '$mo("'.$htmlcontent.'").insertAfter(phone_field);}if(e.target.id=="miniorange_otp_token_submit"){';
			$html 			.= 'var unique_id=$mo("#unique_id").val();var phone_field="phone_number-"+unique_id;var ';
			$html 			.= 'e=$mo("input[name="+phone_field+"]").val();$mo("#mo_message").empty(),$mo("#mo_message").append("'.$img.'"),';
			$html 			.= '$mo("#mo_message").show(),$mo.ajax({url:"'.site_url().'/?option=miniorange-userpro-form",type:"POST",data:{';
			$html 			.= 'user_phone:e},crossDomain:!0,dataType:"json",success:function(o){if(o.result=="success"){$mo("#mo_message").empty(),';
			$html 			.= '$mo("#mo_message").append(o.message),$mo("#mo_message").css("border-top","3px solid green"),';
			$html 			.= '$mo("input[name=email_verify]").focus()}else{$mo("#mo_message").empty(),$mo("#mo_message").append(o.message),';
			$html 			.= '$mo("#mo_message").css("border-top","3px solid red"),$mo("input[name=phone_verify]").focus()};},';
			$html 			.= 'error:function(o,e,n){}});}});</script>';
			return $html;
		}	


		
		function _userpro_email_shortcode()
		{
			$img 			= "<div style='display:table;text-align:center;'><img src='".MOV_URL. "includes/images/loader.gif'></div>";
			$htmlcontent 	= "<div style='margin-top: 2%;'><button type='button' class='button alt' style='width:100%;height:30px;";
			$htmlcontent   .= "font-family: Roboto;font-size: 12px !important;' id='miniorange_otp_token_submit' ";
			$htmlcontent   .= "title='".mo_('Please Enter a email to enable this.')."'>".mo_('Click Here to Verify Email')."</button></div>";
			$htmlcontent   .= "<div style='margin-top:2%'><div id='mo_message' hidden='' style='background-color: #f7f6f7;";
			$htmlcontent   .= "padding: 1em 2em 1em 3.5em;''></div></div>";
			$html 			= '<script>jQuery(document).click(function(e){$mo=jQuery;if($mo("#miniorange_otp_token_submit").length==0){';
			$html 		   .= 'var unique_id=$mo("#unique_id").val();var email_field="#user_email-"+unique_id;if($mo(email_field).length)';
			$html 		   .= '$mo("'.$htmlcontent.'").insertAfter(email_field);}if(e.target.id=="miniorange_otp_token_submit"){';
			$html 		   .= 'var unique_id=$mo("#unique_id").val();var email_field="user_email-"+unique_id;var e=';
			$html 		   .= '$mo("input[name="+email_field+"]").val();$mo("#mo_message").empty(),$mo("#mo_message").append("'.$img.'"),';
			$html 		   .= '$mo("#mo_message").show(),$mo.ajax({url:"'.site_url().'/?option=miniorange-userpro-form",type:"POST",';
			$html 		   .= 'data:{user_email:e},crossDomain:!0,dataType:"json",success:function(o){if(o.result=="success"){';
			$html 		   .= '$mo("#mo_message").empty(),$mo("#mo_message").append(o.message),$mo("#mo_message").css("border-top",';
			$html 		   .= '"3px solid green"),$mo("input[name=email_verify]").focus()}else{$mo("#mo_message").empty(),';
			$html 		   .= '$mo("#mo_message").append(o.message),$mo("#mo_message").css("border-top","3px solid red"),';
			$html 		   .= '$mo("input[name=phone_verify]").focus()};},error:function(o,e,n){}});}});</script>';
			return $html;
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
			
			$this->_isFormEnabled = isset( $_POST['mo_customer_validation_userpro_registration_enable']) ? $_POST['mo_customer_validation_userpro_registration_enable'] : 0;
			$this->_otpType = isset( $_POST['mo_customer_validation_userpro_registration_type']) ? $_POST['mo_customer_validation_userpro_registration_type'] : '';
			$this->_disableAutoActivate = isset( $_POST['mo_customer_validation_userpro_verify']) ? $_POST['mo_customer_validation_userpro_verify'] : 0;

			update_mo_option('mo_customer_validation_userpro_default_enable',$this->_isFormEnabled);
			update_mo_option('mo_customer_validation_userpro_enable_type',$this->_otpType);
			update_mo_option('mo_customer_validation_userpro_verify', $this->_disableAutoActivate);
	    }
	}