<?php

	
	class ContactForm7 extends FormHandler implements IFormHandler
	{
		private $_formFinalEmailVer;
		private $_formFinalPhoneVer;
		private $_formSessionTagName;
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
			$this->_formSessionVar 	= FormSessionVars::CF7_FORMS;
			$this->_formEmailVer = FormSessionVars::CF7_EMAIL_VER;
			$this->_formPhoneVer = FormSessionVars::CF7_PHONE_VER;
			$this->_formFinalEmailVer = FormSessionVars::CF7_EMAIL_SUB;
			$this->_formFinalPhoneVer = FormSessionVars::CF7_PHONE_SUB;
			$this->_typePhoneTag = 'mo_cf7_contact_phone_enable';
			$this->_typeEmailTag = 'mo_cf7_contact_email_enable';
			$this->_formKey = 'CF7_FORM';
			$this->_formName = mo_('Contact Form 7 - Contact Form');
			$this->_isFormEnabled = get_mo_option('mo_customer_validation_cf7_contact_enable') ? TRUE : FALSE;
			parent::__construct();
		}

		
		function handleForm()
		{
			$this->_otpType = get_mo_option('mo_customer_validation_cf7_contact_type');
			$this->_emailKey = get_mo_option('mo_customer_validation_cf7_email_key');
			$this->_phoneKey = 'mo_phone';
			$this->_phoneFormId = 'input[name='.$this->_phoneKey.']';
			
			add_filter( 'wpcf7_validate_text*'	, array($this,'validateFormPost'), 1 , 2 );
			add_filter( 'wpcf7_validate_email*'	, array($this,'validateFormPost'), 1 , 2 );
			add_filter( 'wpcf7_validate_email'	, array($this,'validateFormPost'), 1 , 2 );
			add_filter( 'wpcf7_validate_tel*'	, array($this,'validateFormPost'), 1 , 2 );
			
			add_shortcode('mo_verify_email', array($this,'_cf7_email_shortcode') );
			add_shortcode('mo_verify_phone', array($this,'_cf7_phone_shortcode') );

			$this->routeData();
		}

		function routeData()
		{
			if(!array_key_exists('option', $_GET)) return; 

			switch (trim($_GET['option'])) 
			{
				case "miniorange-cf7-contact":
					$this->_handle_cf7_contact_form($_POST);	break; 			
			}
		}	


		
		function _handle_cf7_contact_form($getdata)
		{
			MoUtility::checkSession();
			MoUtility::initialize_transaction($this->_formSessionVar);

			if(array_key_exists('user_email', $getdata) && !MoUtility::isBlank($getdata['user_email']))
			{
				$_SESSION[$this->_formEmailVer] = $getdata['user_email'];
				miniorange_site_challenge_otp('test',$getdata['user_email'],null,$getdata['user_email'],"email");
			}
			else if(array_key_exists('user_phone', $getdata) && !MoUtility::isBlank($getdata['user_phone']))
			{
				$_SESSION[$this->_formPhoneVer] = trim($getdata['user_phone']);
				miniorange_site_challenge_otp('test','',null, trim($getdata['user_phone']),"phone");
			}
			else
			{
				if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0)
					wp_send_json( MoUtility::_create_json_response(MoMessages::showMessage('ENTER_PHONE'),MoConstants::ERROR_JSON_TYPE) );
				else
					wp_send_json( MoUtility::_create_json_response(MoMessages::showMessage('ENTER_EMAIL'),MoConstants::ERROR_JSON_TYPE) );
			}
		}


		
		function validateFormPost($result, $tag)
		{
			MoUtility::checkSession();
			$tag = new WPCF7_FormTag( $tag );
			$name = $tag->name;
			$value = isset( $_POST[$name] ) ? trim( wp_unslash( strtr( (string) $_POST[$name], "\n", " " ) ) ) : '';

			if ( 'email' == $tag->basetype && $name==$this->_emailKey) $_SESSION[$this->_formFinalEmailVer] = $value;

			if ( 'tel' == $tag->basetype && $name==$this->_phoneKey) $_SESSION[$this->_formFinalPhoneVer]  = $value;

			if ( 'text' == $tag->basetype && $name=='email_verify' || 'text' == $tag->basetype && $name=='phone_verify') 
			{
				$_SESSION[$this->_formSessionTagName] = $name;
								if($this->checkIfVerificationCodeNotEntered($name)) $result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
								if($this->checkIfVerificationNotStarted()) $result->invalidate( $tag, mo_(MoMessages::showMessage('PLEASE_VALIDATE')) );
								if($this->processEmail()) $result->invalidate( $tag, mo_(MoMessages::showMessage('EMAIL_MISMATCH')) );
								if($this->processPhoneNumber()) $result->invalidate( $tag, mo_(MoMessages::showMessage('PHONE_MISMATCH')) ); 
								if(empty($result->invalid_fields)) {
				if(!$this->processOTPEntered())
					$result->invalidate( $tag, MoUtility::_get_invalid_otp_method());
				else
					$this->unsetOTPSessionVariables();
				}
			}
			return $result;
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


		
		function validateOTPRequest()
		{
			do_action('mo_validate_otp',$_SESSION[$this->_formSessionTagName],NULL);
		}


		
		function processOTPEntered()
		{
			$this->validateOTPRequest();
			return strcasecmp($_SESSION[$this->_formSessionVar],'validated')!=0 ? FALSE : TRUE;
		}


		
		function processEmail()
		{
			return array_key_exists($this->_formEmailVer, $_SESSION) 
				&& strcasecmp($_SESSION[$this->_formEmailVer], $_SESSION[$this->_formFinalEmailVer])!=0;
		}


		
		function processPhoneNumber()
		{
			return array_key_exists($this->_formPhoneVer, $_SESSION) 
				&& strcasecmp($_SESSION[$this->_formPhoneVer], $_SESSION[$this->_formFinalPhoneVer])!=0;
		}


		
		function checkIfVerificationNotStarted()
		{
			return !array_key_exists($this->_formSessionVar,$_SESSION); 
		}


		
		function checkIfVerificationCodeNotEntered($name)
		{
			return !isset($_REQUEST[$name]);
		}


		
		function _cf7_email_shortcode()
		{
			$img   = "<div style='display:table;text-align:center;'><img src='".MOV_URL. "includes/images/loader.gif'></div>";
			$html  = '<script>jQuery(document).ready(function(){$mo=jQuery;$mo("#miniorange_otp_token_submit").click(function(o){'; 
			$html .= 'var e=$mo("input[name='.$this->_emailKey.']").val(); $mo("#mo_message").empty(),$mo("#mo_message").append("'.$img.'"),';
			$html .= '$mo("#mo_message").show(),$mo.ajax({url:"'.site_url().'/?option=miniorange-cf7-contact",type:"POST",data:{user_email:e},';
			$html .= 'crossDomain:!0,dataType:"json",success:function(o){ if(o.result=="success"){$mo("#mo_message").empty(),';
			$html .= '$mo("#mo_message").append(o.message),$mo("#mo_message").css("border-top","3px solid green"),';
			$html .= '$mo("input[name=email_verify]").focus()}else{$mo("#mo_message").empty(),$mo("#mo_message").append(o.message),';
			$html .= '$mo("#mo_message").css("border-top","3px solid red"),$mo("input[name=email_verify]").focus()} ;},';
			$html .= 'error:function(o,e,n){}})});});</script>';
			return $html;
		}


		
		function _cf7_phone_shortcode()
		{
			$img   = "<div style='display:table;text-align:center;'><img src='".MOV_URL. "includes/images/loader.gif'></div>";
			$html  = '<script>jQuery(document).ready(function(){$mo=jQuery;$mo("#miniorange_otp_token_submit").click(function(o){'; 
			$html .= 'var e=$mo("input[name='.$this->_phoneKey.']").val(); $mo("#mo_message").empty(),$mo("#mo_message").append("'.$img.'"),';
			$html .= '$mo("#mo_message").show(),$mo.ajax({url:"'.site_url().'/?option=miniorange-cf7-contact",type:"POST",data:{user_phone:e},';
			$html .= 'crossDomain:!0,dataType:"json",success:function(o){ if(o.result=="success"){$mo("#mo_message").empty(),';
			$html .= '$mo("#mo_message").append(o.message),$mo("#mo_message").css("border-top","3px solid green"),';
			$html .= '$mo("input[name=email_verify]").focus()}else{$mo("#mo_message").empty(),$mo("#mo_message").append(o.message),';
			$html .= '$mo("#mo_message").css("border-top","3px solid red"),$mo("input[name=phone_verify]").focus()} ;},';
			$html .= 'error:function(o,e,n){}})});});</script>';
			return $html;
		}


		
		public function unsetOTPSessionVariables()
		{
			unset($_SESSION[$this->_txSessionId]);
			unset($_SESSION[$this->_formSessionVar]);
			unset($_SESSION[$this->_formEmailVer]);
			unset($_SESSION[$this->_formPhoneVer]);
			unset($_SESSION[$this->_formFinalEmailVer]);
			unset($_SESSION[$this->_formFinalPhoneVer]);
			unset($_SESSION[$this->_formSessionTagName]);
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

			$this->_isFormEnabled = isset( $_POST['mo_customer_validation_cf7_contact_enable']) ? $_POST['mo_customer_validation_cf7_contact_enable'] : 0;
			$this->_otpType = isset( $_POST['mo_customer_validation_cf7_contact_type']) ? $_POST['mo_customer_validation_cf7_contact_type'] : '';
			$this->_emailKey = isset( $_POST['cf7_email_field_key']) ? $_POST['cf7_email_field_key'] : '';

			update_mo_option('mo_customer_validation_cf7_contact_enable', $this->_isFormEnabled);
			update_mo_option('mo_customer_validation_cf7_contact_type',$this->_otpType);
			update_mo_option('mo_customer_validation_cf7_email_key',$this->_emailKey);
		}
	}