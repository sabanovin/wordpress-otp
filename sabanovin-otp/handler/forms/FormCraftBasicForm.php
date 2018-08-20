<?php
	
	
	class FormCraftBasicForm extends FormHandler implements IFormHandler
	{
		private $_currentFormID 	= 'fc_form_id';
		private static $_instance 	= null;

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
			$this->_formSessionVar = FormSessionVars::FORMCRAFT;
			$this->_formEmailVer = FormSessionVars::FORMCRAFT_EMAIL_VER;
			$this->_formPhoneVer = FormSessionVars::FORMCRAFT_PHONE_VER;
			$this->_typePhoneTag = 'mo_formcraft_phone_enable';
			$this->_typeEmailTag = 'mo_formcraft_email_enable';
			$this->_formKey = 'FORMCRAFTBASIC';
			$this->_formName = mo_('FormCraft Basic (Free Version)');
			$this->_isFormEnabled = get_mo_option('mo_customer_validation_formcraft_enable') ? TRUE : FALSE;
			$this->_phoneFormId = array();
			parent::__construct();
		}

		
		function handleForm()
		{
			if(!$this->isFormCraftPluginInstalled()) return; 			
			$this->_otpType = get_mo_option('mo_customer_validation_formcraft_enable_type');
			$this->_formDetails = maybe_unserialize(get_mo_option('mo_customer_validation_formcraft_otp_enabled'));
			if(empty($this->_formDetails)) return;
			foreach ($this->_formDetails as $key => $value) {
				array_push($this->_phoneFormId,".fcb_form input[name=".$value['phonekey']."]");
			}

			add_action( 'wp_ajax_formcraft_basic_form_submit', array($this,'validate_formcraft_form_submit'),1);
			add_action( 'wp_ajax_nopriv_formcraft_basic_form_submit', array($this,'validate_formcraft_form_submit'),1);
			add_action( 'wp_enqueue_scripts', array($this,'enqueue_script_on_page'));
			$this->routeData();
		}

		function routeData()
		{
			if(!array_key_exists('option', $_GET)) return; 

			switch (trim($_GET['option'])) 
			{
				case "miniorange-formcraft-verify":
					$this->_handle_formcraft_form($_POST);										break; 			
				case "miniorange-formcraft-form-otp-enabled":
					wp_send_json($this->isVerificationEnabledForThisForm($_POST['form_id']));	break;
			}
		}	


		
		function _handle_formcraft_form($data)
		{
			MoUtility::checkSession();
			if(!$this->isVerificationEnabledForThisForm($_POST['form_id'])) return;
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


		
		function validate_formcraft_form_submit()
		{
			global $wpdb, $forms_table;
			MoUtility::checkSession();
			$errors = array();
			$id = $_POST['id'];
			if(!$this->isVerificationEnabledForThisForm($id)) return;

			$this->checkIfVerificationNotStarted($errors,$id);
			$formdata = $this->_formDetails[$id];

			if($this->_otpType==$this->_typePhoneTag 
				&& strcasecmp($_SESSION[$this->_formPhoneVer],$_POST[$formdata['phonekey']])!=0)
				$this->sendJSONErrorMessage(array( "errors" => array( $this->_formDetails[$id]['phonekey'] 
					=> mo_(MoMessages::showMessage('PHONE_MISMATCH')))));
			if($this->_otpType==$this->_typeEmailTag 
				&& strcasecmp($_SESSION[$this->_formEmailVer],$_POST[$formdata['emailkey']])!=0)
				$this->sendJSONErrorMessage(array( "errors" => array( $this->_formDetails[$id]['emailkey'] 
					=>  mo_(MoMessages::showMessage('EMAIL_MISMATCH')))));
			if(MoUtility::isBlank($_POST[$formdata['verifyKey']]))
				$this->sendJSONErrorMessage(array( "errors" => array( $this->_formDetails[$id]['verifyKey'] 
					=>  mo_(MoMessages::showMessage('INVALID_OTP')))));

			$_SESSION[$this->_currentFormID] = $id;
			do_action('mo_validate_otp',NULL,$_POST[$formdata['verifyKey']]);
		}


		
		function enqueue_script_on_page()
		{
			wp_register_script( 'formcraftscript', MOV_URL . 'includes/js/formcraftbasic.min.js?version='.MOV_VERSION , array('jquery') );
			wp_localize_script( 'formcraftscript', 'mofcvars', array(
				'imgURL'		=> 	MOV_URL. "includes/images/loader.gif",
				'formCraftForms'=> 	$this->_formDetails,
				'siteURL' 		=> 	site_url(),
				'otpType' 		=>  $this->_otpType,
				'buttonText'	=> 	mo_('Click here to send OTP'),
				'buttonTitle'	=> 	$this->_otpType==$this->_typePhoneTag ? 
									mo_('Please enter a Phone Number to enable this field.' ) 
									: mo_('Please enter a Phone Number to enable this field.' ),
				'ajaxurl'       => 	admin_url('admin-ajax.php'),
				'typePhone'		=>  $this->_typePhoneTag,
				'countryDrop'	=> get_mo_option('mo_customer_validation_show_dropdown_on_form'),
			));
			wp_enqueue_script('formcraftscript');
		}


		
		function isVerificationEnabledForThisForm($id)
		{
			return array_key_exists($id,$this->_formDetails);
		}


		
		function sendJSONErrorMessage($errors)
		{
			$response['failed'] = mo_('Please correct the errors');
			$response['errors'] = $errors;
			echo json_encode($response);
			die();
		}


		
		function checkIfVerificationNotStarted($errors,$id)
		{
			if(array_key_exists($this->_formSessionVar,$_SESSION)) return;

			if($this->_otpType==$this->_typePhoneTag)
				$this->sendJSONErrorMessage( array( "errors" => array( $this->_formDetails[$id]['phonekey'] 
					=>  mo_(MoMessages::showMessage('PLEASE_VALIDATE')) )));
			else
				$this->sendJSONErrorMessage( array( "errors" => array( $this->_formDetails[$id]['emailkey'] 
					=>  mo_(MoMessages::showMessage('PLEASE_VALIDATE')) )));
		}


		
		function handle_failed_verification($user_login,$user_email,$phone_number)
		{
			MoUtility::checkSession();
			if(!isset($_SESSION[$this->_formSessionVar])) return;
			$form_id = $_SESSION[$this->_currentFormID];
			$this->sendJSONErrorMessage( array( "errors" => array( $this->_formDetails[$form_id]['verifyKey'] 
				=>  MoUtility::_get_invalid_otp_method() )));
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
			unset($_SESSION[$this->_formEmailVer]);
			unset($_SESSION[$this->_formPhoneVer]);
			unset($_SESSION[$this->_currentFormID]);
		}


		
		public function getPhoneNumberSelector($selector)	
		{
			MoUtility::checkSession();
			if($this->isFormEnabled() && $this->_otpType==$this->_typePhoneTag) 
				$selector = array_merge($selector, $this->_phoneFormId); 
			return $selector;
		}


		
		function isFormCraftPluginInstalled()
		{
			if( !function_exists('is_plugin_active') ) include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			return is_plugin_active( 'formcraft-form-builder/formcraft-main.php' );
		}


		
		function handleFormOptions()
		{
			if(!MoUtility::areFormOptionsBeingSaved()) return;
			if(!$this->isFormCraftPluginInstalled()) return; 			if(!array_key_exists('formcraft',$_POST)) return;
			foreach (array_filter($_POST['formcraft']['form']) as $key => $value)
			{
				$formData = $this->getFormCraftFormDataFromID($value);
				if(MoUtility::isBlank($formData)) continue;
				$fieldIds = $this->getFieldIDs($_POST,$key,$formData);
				$form[$value]= array(
					'emailkey'=> $fieldIds['emailKey'],
					'phonekey'=> $fieldIds['phoneKey'],
					'verifyKey'=> $fieldIds['verifyKey'],
					'phone_label'=>$_POST['formcraft']['phonekey'][$key],
					'email_label'=>$_POST['formcraft']['emailkey'][$key],
					'verify_label'=>$_POST['formcraft']['verifyKey'][$key]
				);
			}

			$this->_isFormEnabled = isset( $_POST['mo_customer_validation_formcraft_enable']) ? $_POST['mo_customer_validation_formcraft_enable'] : 0;
			$this->_otpType = isset( $_POST['mo_customer_validation_formcraft_enable_type']) ? $_POST['mo_customer_validation_formcraft_enable_type'] : '';
			$this->_formDetails = !empty($form) ? maybe_serialize($form) : "";
			
			update_mo_option('mo_customer_validation_formcraft_enable',$this->_isFormEnabled);
			update_mo_option('mo_customer_validation_formcraft_enable_type',$this->_otpType);
			update_mo_option('mo_customer_validation_formcraft_otp_enabled',$this->_formDetails);
		}


		
		private function getFieldIDs($data,$key,$formData)
		{
			$fieldIds = array('emailKey'=>'','phoneKey'=>'','verifyKey'=>'');
			if(empty($data)) return;
			foreach ($formData as $form) {
				if(strcasecmp($form['elementDefaults']['main_label'],$data['formcraft']['emailkey'][$key])==0) 
					$fieldIds['emailKey']=$form['identifier'];
				if(strcasecmp($form['elementDefaults']['main_label'],$data['formcraft']['phonekey'][$key])==0) 
					$fieldIds['phoneKey']=$form['identifier'];
				if(strcasecmp($form['elementDefaults']['main_label'],$data['formcraft']['verifyKey'][$key])==0) 
					$fieldIds['verifyKey']=$form['identifier'];
			}
			return $fieldIds;
		}


		
		function getFormCraftFormDataFromID($id)
		{
			global $wpdb,$forms_table;
			$meta = $wpdb->get_var( "SELECT meta_builder FROM $forms_table WHERE id=$id" );
			$meta = json_decode(stripcslashes($meta),1);
			return $meta['fields'];
		}
	}