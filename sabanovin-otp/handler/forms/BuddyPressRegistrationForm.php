<?php
		
	
	class BuddyPressRegistrationForm extends FormHandler implements IFormHandler
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
			$this->_formSessionVar = FormSessionVars::BUDDYPRESS_REG;
			$this->_typePhoneTag = 'mo_bbp_phone_enable';
			$this->_typeEmailTag = 'mo_bbp_email_enable';
			$this->_typeBothTag = 'mo_bbp_both_enabled';
			$this->_formKey = 'BP_DEFAULT_FORM';
			$this->_formName = mo_('BuddyPress Registration Form');
			$this->_isFormEnabled = get_mo_option('mo_customer_validation_bbp_default_enable') ? TRUE : FALSE;
			parent::__construct();
		}

		
		function handleForm()
		{
			$this->_phoneKey = get_mo_option('mo_customer_validation_bbp_phone_key');
			$this->_otpType = get_mo_option('mo_customer_validation_bbp_enable_type');
			$this->_disableAutoActivate = get_mo_option('mo_customer_validation_bbp_disable_activation');
			$this->_phoneFormId = 'input[name=field_'.$this->moBBPgetphoneFieldId().']';

			add_filter( 'bp_registration_needs_activation', '__return_false');
			add_filter( 'bp_registration_needs_activation'	, array($this,'fix_signup_form_validation_text'));
			add_filter( 'bp_core_signup_send_activation_key', array($this,'disable_activation_email'));
			add_filter( 'bp_signup_usermeta', array($this,'miniorange_bp_user_registration'),1,1);
			add_action( 'bp_signup_validate', array($this,'validateOTPRequest'), 99,0);

			if($this->_disableAutoActivate) add_action( 'bp_core_signup_user',array($this,'mo_activate_bbp_user'),1,5);	
		}

		
		function fix_signup_form_validation_text()
		{
			return $this->_disableAutoActivate ? FALSE : TRUE;
		}
		

		
		function disable_activation_email()
		{
			return $this->_disableAutoActivate ? FALSE : TRUE;
		}


		
		function isPhoneVerificationEnabled()
		{
			return (strcasecmp($this->_otpType,$this->_typePhoneTag)==0 || strcasecmp($this->_otpType,$this->_typeBothTag)==0);
		}


		
		function validateOTPRequest()
		{
			global $bp,$phoneLogic;
			$field_key = "field_".$this->moBBPgetphoneFieldId();
			if(isset($_POST[$field_key]) && !MoUtility::validatePhoneNumber($_POST[$field_key]))
				$bp->signup->errors[$field_key] = str_replace("##phone##",$_POST[$field_key],$phoneLogic->_get_otp_invalid_format_message());
		}


		
		function checkIfVerificationIsComplete()
		{
			if(isset($_SESSION[$this->_formSessionVar]) && $_SESSION[$this->_formSessionVar]=='completed')
			{
				$this->unsetOTPSessionVariables();
				return TRUE;
			}
			return FALSE;
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
			$_SESSION[$this->_formSessionVar] = 'completed';	
		}


		
		function miniorange_bp_user_registration($usermeta)
		{
			MoUtility::checkSession();
			if($this->checkIfVerificationIsComplete()) return $usermeta; 
			MoUtility::initialize_transaction($this->_formSessionVar);
			$errors = new WP_Error();
			$phone_number = NULL;
			
			foreach ($_POST as $key => $value)
			{
				if($key=="signup_username")
					$username = $value;
				elseif ($key=="signup_email") 
					$email = $value;
				elseif ($key=="signup_password") 
					$password = $value;
				else
					$extra_data[$key]=$value;
			}
			
			$reg1 = $this->moBBPgetphoneFieldId();
			
			if(isset($_POST["field_".$reg1])) $phone_number = $_POST["field_".$reg1];
			
			$extra_data['usermeta'] = $usermeta;
			$this->startVerificationProcess($username,$email,$errors,$phone_number,$password,$extra_data);
		}


		
		function startVerificationProcess($username,$email,$errors,$phone_number,$password,$extra_data)
		{
			if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0)
				miniorange_site_challenge_otp($username,$email,$errors,$phone_number,'phone',$password,$extra_data);
			else if(strcasecmp($this->_otpType,$this->_typeBothTag)==0)
				miniorange_site_challenge_otp($username,$email,$errors,$phone_number,'both',$password,$extra_data);
			else
				miniorange_site_challenge_otp($username,$email,$errors,$phone_number,'email',$password,$extra_data);
		}

		
		
		function mo_activate_bbp_user($userID,$user_login,$user_password, $user_email, $usermeta)
		{
			$activation_key = $this->moBBPgetActivationKey($user_login); 
			bp_core_activate_signup($activation_key);   
			BP_Signup::validate($activation_key); 				
			$u = new WP_User( $userID ); 
			$u->add_role( 'subscriber' ); 			
			return;
		}


		
		function moBBPgetActivationKey($user_login)
		{
			global $wpdb;
			return $wpdb->get_var( "SELECT activation_key FROM {$wpdb->prefix}signups WHERE active = '0' AND user_login = '".$user_login."'");
		}


		
		function moBBPgetphoneFieldId()
		{
			global $wpdb;
			return $wpdb->get_var("SELECT id FROM {$wpdb->prefix}bp_xprofile_fields where name ='".$this->_phoneKey."'");
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


				
		function handleFormOptions()
		{
			if(!MoUtility::areFormOptionsBeingSaved()) return;

			$this->_isFormEnabled = isset( $_POST['mo_customer_validation_bbp_default_enable']) ? $_POST['mo_customer_validation_bbp_default_enable'] : 0;
			$this->_disableAutoActivate = isset( $_POST['mo_customer_validation_bbp_disable_activation']) ? $_POST['mo_customer_validation_bbp_disable_activation'] : '';
			$this->_otpType = isset( $_POST['mo_customer_validation_bbp_enable_type']) ? $_POST['mo_customer_validation_bbp_enable_type'] : '';
			$this->_phoneKey = isset( $_POST['bbp_phone_field_key']) ? $_POST['bbp_phone_field_key'] : '';

			$this->updateFormData();

			update_mo_option('mo_customer_validation_bbp_default_enable', $this->_isFormEnabled);
			update_mo_option('mo_customer_validation_bbp_disable_activation', $this->_disableAutoActivate);
			update_mo_option('mo_customer_validation_bbp_enable_type', $this->_otpType);
			update_mo_option('mo_customer_validation_bbp_phone_key', $this->_phoneKey);
		}
    }