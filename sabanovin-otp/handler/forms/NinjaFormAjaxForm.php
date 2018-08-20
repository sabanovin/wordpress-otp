<?php 
	

	
	class NinjaFormAjaxForm extends FormHandler implements IFormHandler
	{
		private $_ninjaFormSessionId = 'nja_form_id';
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
			$this->_formSessionVar = FormSessionVars::NINJA_FORM_AJAX;
			$this->_formEmailVer = FormSessionVars::NINJA_FORM_AJAX_EMAIL;
			$this->_formPhoneVer = FormSessionVars::NINJA_FORM_AJAX_PHONE;
			$this->_typePhoneTag = 'mo_ninja_form_phone_enable';
			$this->_typeEmailTag = 'mo_ninja_form_email_enable';
			$this->_typeBothTag = 'mo_ninja_form_both_enable';
			$this->_formKey = 'NINJA_FORM_AJAX';
			$this->_formName = mo_('Ninja Forms ( Above version 3.0 )');
			$this->_isFormEnabled = get_mo_option('mo_customer_validation_nja_enable') ? TRUE : FALSE;
            $this->_buttonText = get_mo_option('mo_customer_validation_nja_button_text');
            $this->_buttonText = !MoUtility::isBlank($this->_buttonText) ? $this->_buttonText : mo_('Click Here to send OTP');
			$this->_phoneFormId = array();
			parent::__construct();
		}

		
		function handleForm()
		{
			$this->_otpType = get_mo_option('mo_customer_validation_ninja_form_enable_type');
			$this->_formDetails = maybe_unserialize(get_mo_option('mo_customer_validation_ninja_form_otp_enabled'));
			if(empty($this->_formDetails)) return;
			foreach ($this->_formDetails as $key => $value) {
				array_push($this->_phoneFormId,'input[name=nf-field-'.$value['phonekey'].']');
			}

			add_action( 'ninja_forms_after_form_display'	, array($this,'enqueue_nj_form_script'),  99 , 1);
			add_filter( 'ninja_forms_submit_data'			, array($this,'_handle_nj_ajax_form_submit') , 99 ,1);
			add_filter( 'ninja_forms_display_fields'		, array($this,'_add_button') ,99,1);
			add_filter( 'ninja_forms_display_form_settings'	, array($this,'setFormId') ,99 ,2);

			$this->routeData();
		}

		function routeData()
		{
			if(!array_key_exists('option', $_GET)) return;
			switch (trim($_GET['option'])) 
			{
				case "miniorange-nj-ajax-verify":
					$this->_send_otp_nj_ajax_verify($_POST);		break;
			}
		}


		
		function enqueue_nj_form_script($form_id)
		{
			if(array_key_exists($form_id,$this->_formDetails))
			{
				$formdata = $this->_formDetails[$form_id];
				wp_register_script( 'njscript', MOV_URL . 'includes/js/ninjaformajax.min.js', array('jquery'), MOV_VERSION, true );
				wp_localize_script('njscript', 'moninjavars', array(
					'imgURL'		=> MOV_URL. "includes/images/loader.gif",
					'key'     		=> 	$this->_otpType==$this->_typePhoneTag 
											? "nf-field-".$formdata['phonekey'] : "nf-field-".$formdata['emailkey'],
					'fieldName'		=> 	$this->_otpType==$this->_typePhoneTag
											? "phone number" : "email",	
					'verifyField'	=>	get_mo_option('mo_customer_validation_nfa_verify_field'),
					'siteURL' 		=> 	site_url(),
				));
				wp_enqueue_script('njscript');
			}
			return $form_id;
		}


		
		function setFormId($settings,$form_id)
		{
			MoUtility::checkSession();
			$_SESSION[$this->_ninjaFormSessionId] = $form_id;
			return $settings;
		}


		
		function _add_button($fields)
		{
			MoUtility::checkSession();

			if(!array_key_exists($_SESSION[$this->_ninjaFormSessionId],$this->_formDetails)) return $fields;
			
			$formdata = $this->_formDetails[$_SESSION[$this->_ninjaFormSessionId]];
			$fieldName = $this->_otpType==$this->_typePhoneTag ? "phone number" : "email";
			$fieldKey = $this->_otpType==$this->_typePhoneTag ? "phonekey" : "emailkey";
			
			foreach ($fields as $key => $field) 
			{
				if($field['id']==$formdata[$fieldKey]){
					$fields[$key]['afterField']='<div id="nf-field-4-container" class="nf-field-container submit-container  label-above ">
					<div class="nf-before-field"><nf-section></nf-section></div><div class="nf-field"><div class="field-wrap submit-wrap">
					<div class="nf-field-label"></div><div class="nf-field-element"><input id="miniorange_otp_token_submit" class="ninja-forms-field nf-element" 
					value="'.$this->_buttonText.'" type="button"></div></div></div><div class="nf-after-field"><nf-section><div class="nf-input-limit">
					</div><div class="nf-error-wrap nf-error"></div></nf-section></div></div>
					<div id="mo_message" hidden="" style="background-color: #f7f6f7;padding: 1em 2em 1em 3.5em;"></div>';
				}
			}
			
			return $fields;
		}


		
		function _handle_nj_ajax_form_submit($data)
		{
			MoUtility::checkSession();
			
			if(!array_key_exists($data['id'],$this->_formDetails)) return $data;

			$formdata = $this->_formDetails[$data['id']];
			$data = $this->checkIfOtpVerificationStarted($formdata,$data);

			if(isset($data['errors']['fields'])) return $data;

			if(strcasecmp($this->_otpType,$this->_typeEmailTag)==0)
				$data = $this->processEmail($formdata,$data);
			if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0)
				$data = $this->processPhone($formdata,$data);
			if(!isset($data['errors']['fields']))
				$data = $this->processOTPEntered($data,$formdata);	
			
			return $data;
		}


		
		function validateOTPRequest($value)
		{
			do_action('mo_validate_otp',NULL,$value);
		}


		
		function processOTPEntered($data,$formdata)
		{
			$verify_field = $formdata['verifyKey'];	
			$this->validateOTPRequest($data['fields'][$verify_field]['value']);
			if(strcasecmp($_SESSION[$this->_formSessionVar],'validated')!=0)
				$data['errors']['fields'][$verify_field]=MoUtility::_get_invalid_otp_method();
			else
				$this->unsetOTPSessionVariables();
			return $data;
		}


		
		function checkIfOtpVerificationStarted($formdata,$data)
		{
			if(array_key_exists($this->_formSessionVar, $_SESSION)) return $data;

			if(strcasecmp($this->_otpType,$this->_typeEmailTag)==0)
				$data['errors']['fields'][$formdata['emailkey']]=MoMessages::showMessage('ENTER_VERIFY_CODE');
			else
				$data['errors']['fields'][$formdata['phonekey']]=MoMessages::showMessage('ENTER_VERIFY_CODE');
			
			return $data;
		}


		
		function processEmail($formdata,$data)
		{
			$field_id = $formdata['emailkey'];
			if($_SESSION[$this->_formEmailVer]!=$data['fields'][$field_id]['value'])
				$data['errors']['fields'][$field_id]=MoMessages::showMessage('EMAIL_MISMATCH');
			return $data;
		}


		
		function processPhone($formdata,$data)
		{
			$field_id = $formdata['phonekey'];
			if($_SESSION[$this->_formPhoneVer]!= $data['fields'][$field_id]['value'])
				$data['errors']['fields'][$field_id]=MoMessages::showMessage('PHONE_MISMATCH');
			return $data;
		}


		
		function _send_otp_nj_ajax_verify($data)
		{
			MoUtility::checkSession();
			MoUtility::initialize_transaction($this->_formSessionVar);
			if($this->_otpType==$this->_typePhoneTag)
				$this->_send_nj_ajax_otp_to_phone($data);
			else
				$this->_send_nj_ajax_otp_to_email($data);
		}


		
		function _send_nj_ajax_otp_to_phone($data)
		{
			if(!array_key_exists('user_phone', $data) || !isset($data['user_phone']))
				wp_send_json( MoUtility::_create_json_response(MoMessages::showMessage('ENTER_PHONE'),MoConstants::ERROR_JSON_TYPE) );
			else
				$this->setSessionAndStartOTPVerification(trim($data['user_phone']),NULL,trim($data['user_phone']),"phone");
		}


		
		function _send_nj_ajax_otp_to_email($data)
		{
			if(!array_key_exists('user_email', $data) || !isset($data['user_email']))
				wp_send_json( MoUtility::_create_json_response(MoMessages::showMessage('ENTER_EMAIL'),MoConstants::ERROR_JSON_TYPE) );
			else
				$this->setSessionAndStartOTPVerification($data['user_email'],$data['user_email'],NULL,"email");
		}


		
		function setSessionAndStartOTPVerification($sessionvalue,$useremail,$phoneNumber,$otpType)
		{
			$_SESSION[ strcasecmp($this->_otpType,$this->_typePhoneTag)==0 ? $this->_formPhoneVer : $this->_formEmailVer ] = $sessionvalue;
			miniorange_site_challenge_otp('testUser',$useremail,NULL,$phoneNumber,$otpType);
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
			unset($_SESSION[$this->_ninjaFormSessionId]);
		}


		
		public function getPhoneNumberSelector($selector)	
		{
			MoUtility::checkSession();
			if($this->isFormEnabled() && ($this->_otpType == $this->_typePhoneTag)) $selector = array_merge($selector, $this->_phoneFormId); 
			return $selector;
		}


		
		function getFieldId($data)
		{
			global $wpdb;
			return $wpdb->get_var("SELECT id FROM {$wpdb->prefix}nf3_fields where `key` ='".$data."'");
		}


		
		function handleFormOptions()
		{
			if(!MoUtility::areFormOptionsBeingSaved()) return;
			if(isset($_POST['mo_customer_validation_ninja_form_enable'])) return;

			$form = $this->parseFormDetails();

			$this->_formDetails = !empty($form) ? maybe_serialize($form) : "";
			$this->_otpType = isset( $_POST['mo_customer_validation_nja_enable_type']) ? $_POST['mo_customer_validation_nja_enable_type'] : '';
			$this->_isFormEnabled = isset( $_POST['mo_customer_validation_nja_enable']) ? $_POST['mo_customer_validation_nja_enable'] : 0;
            $this->_buttonText = isset($_POST['mo_customer_validation_nja_button_text']) ? $_POST['mo_customer_validation_nja_button_text'] : '';
			
			update_mo_option('mo_customer_validation_ninja_form_enable',0);
			update_mo_option('mo_customer_validation_nja_enable', $this->_isFormEnabled);
			update_mo_option('mo_customer_validation_ninja_form_enable_type',$this->_otpType);
			update_mo_option('mo_customer_validation_ninja_form_otp_enabled',$this->_formDetails);
		}


		function parseFormDetails()
		{
		    $form = array();
			if(!array_key_exists('ninja_ajax_form',$_POST)) return array();
			foreach (array_filter($_POST['ninja_ajax_form']['form']) as $key => $value)
			{
				$form[$value]= array(
					'emailkey'=> $this->getFieldId($_POST['ninja_ajax_form']['emailkey'][$key]),
					'phonekey'=> $this->getFieldId($_POST['ninja_ajax_form']['phonekey'][$key]),
					'verifyKey'=> $this->getFieldId($_POST['ninja_ajax_form']['verifyKey'][$key]),
					'phone_show_key'=>$_POST['ninja_ajax_form']['phonekey'][$key],
					'email_show_key'=>$_POST['ninja_ajax_form']['emailkey'][$key],
					'verify_show_key'=>$_POST['ninja_ajax_form']['verifyKey'][$key]
				);
			}
			return $form;
		}
	
	}