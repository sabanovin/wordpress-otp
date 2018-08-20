<?php
	
	
	class UltimateMemberRegistrationForm extends FormHandler implements IFormHandler
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
            $this->_isLoginOrSocialForm = TRUE;
			$this->_formSessionVar = FormSessionVars::UM_DEFAULT_REG;
			$this->_typePhoneTag = 'mo_um_phone_enable';
			$this->_typeEmailTag = 'mo_um_email_enable';
			$this->_typeBothTag = 'mo_um_both_enable';
			$this->_phoneFormId= "input[name^='mobile_number']";
			$this->_formKey = 'ULTIMATE_FORM';
			$this->_formName = mo_("Ultimate Member Registration Form");
			$this->_isFormEnabled = get_mo_option('mo_customer_validation_um_default_enable') ? TRUE : FALSE;
			$this->_restrictDuplicates = get_mo_option('mo_customer_validation_um_restrict_duplicates') ? TRUE : FALSE;
			parent::__construct();
		}

		
		function handleForm()
		{
			$this->_otpType = get_mo_option('mo_customer_validation_um_enable_type');
            if( is_plugin_active( 'ultimate-member/ultimate-member.php' ) ) {
                add_action('um_submit_form_errors_hook__registration', array($this, 'miniorange_um2_phone_validation'), 99, 1);
                add_filter('um_registration_user_role', array($this, 'miniorange_um2_user_registration'), 99, 2);
            }else{
                add_action( 'um_submit_form_errors_hook_', array($this,'miniorange_um_phone_validation'), 99,1);
                add_action( 'um_before_new_user_register', array($this,'miniorange_um_user_registration'), 99,1);
            }
		}


		
		function isPhoneVerificationEnabled()
		{
			return (strcasecmp($this->_otpType,$this->_typePhoneTag)==0 || strcasecmp($this->_otpType,$this->_typeBothTag)==0);
		}


		
		function miniorange_um2_user_registration($user_role,$args)
		{
			MoUtility::checkSession();
			$errors = new WP_Error();
            if(isset($_SESSION[$this->_formSessionVar]) && $_SESSION[$this->_formSessionVar]=="validated")
            {
                $this->unsetOTPSessionVariables();
                return $user_role;
            }
            else
            {
                MoUtility::initialize_transaction($this->_formSessionVar);
                extract( $args );
                $this->startOtpTransaction($user_login,$user_email,$errors,$mobile_number,$user_password,null);
            }
		}


        
        function miniorange_um_user_registration($args)
        {
            MoUtility::checkSession();
            $errors = new WP_Error();
            MoUtility::initialize_transaction($this->_formSessionVar);
            foreach ($args as $key => $value)
            {
                if($key=="user_login")
                    $username = $value;
                elseif ($key=="user_email")
                    $email = $value;
                elseif ($key=="user_password")
                    $password = $value;
                elseif ($key == 'mobile_number')
                    $phone_number = $value;
                else
                    $extra_data[$key]=$value;
            }
            $this->startOtpTransaction($username,$email,$errors,$phone_number,$password,$extra_data);
        }


		
		function startOtpTransaction($username,$email,$errors,$phone_number,$password,$extra_data)
		{
			if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0)
				miniorange_site_challenge_otp($username,$email,$errors,$phone_number,"phone",$password,$extra_data);
			elseif(strcasecmp($this->_otpType,$this->_typeBothTag)==0)
				miniorange_site_challenge_otp($username,$email,$errors,$phone_number,"both",$password,$extra_data);
			else
				miniorange_site_challenge_otp($username,$email,$errors,$phone_number,"email",$password,$extra_data);
		}


		
		function miniorange_um2_phone_validation($args)
		{
			global $phoneLogic;
			foreach ($args as $key => $value) {
                			    if ($key == 'mobile_number' && !MoUtility::validatePhoneNumber($value))
                     UM()->form()->add_error($key, str_replace("##phone##", $value, $phoneLogic->_get_otp_invalid_format_message()));
                if($this->_restrictDuplicates && $key == 'mobile_number' && $this->isPhoneNumberAlreadyInUse($value,$key))
                     UM()->form()->add_error($key, MoMessages::showMessage('PHONE_EXISTS'));
            }
		}

        
        function miniorange_um_phone_validation($args)
        {
            global $ultimatemember,$phoneLogic;
            foreach ($args as $key => $value)
                if ($key == 'mobile_number' && !MoUtility::validatePhoneNumber($value))
                        $ultimatemember->form->add_error($key, str_replace("##phone##",$value,$phoneLogic->_get_otp_invalid_format_message()));
                if($this->_restrictDuplicates && $key == 'mobile_number' && $this->isPhoneNumberAlreadyInUse($value,$key))
                    $ultimatemember->form->add_error($key, MoMessages::showMessage('PHONE_EXISTS'));
        }

        
        function isPhoneNumberAlreadyInUse($phone,$key)
        {
            global $wpdb;
            MoUtility::processPhoneNumber($phone);
            $q = "SELECT `user_id` FROM `{$wpdb->prefix}usermeta` WHERE `meta_key` = '$key' AND `meta_value` =  '$phone'";
            $results = $wpdb->get_row($q);
            return !MoUtility::isBlank($results);
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
            if(!function_exists('is_plugin_active')) include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            if( is_plugin_active( 'ultimate-member/ultimate-member.php' ) ) {
                $_SESSION[$this->_formSessionVar] = 'validated';
            }else{
                $this->register_ultimateMember_user($user_login,$user_email,$password,$phone_number,$extra_data);
            }
		}


        
        function register_ultimateMember_user($user_login,$user_email,$password,$phone_number,$extra_data)
        {
            $args = Array();
            $args['user_login'] = $user_login;
            $args['user_email'] = $user_email;
            $args['user_password'] = $password;
            $args = array_merge($args,$extra_data);
            $user_id = wp_create_user( $user_login,$password, $user_email );
            $this->unsetOTPSessionVariables();
            do_action('um_after_new_user_register', $user_id, $args);
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
			
			$this->_isFormEnabled = isset( $_POST['mo_customer_validation_um_default_enable']) ? $_POST['mo_customer_validation_um_default_enable'] : 0;
			$this->_otpType = isset( $_POST['mo_customer_validation_um_enable_type']) ? $_POST['mo_customer_validation_um_enable_type'] : '';

            $this->_restrictDuplicates = ($this->_otpType!=$this->_typePhoneTag) ? ""
                : (isset($_POST['mo_customer_validation_um_restrict_duplicates']) ? $_POST['mo_customer_validation_um_restrict_duplicates'] : '');

			update_mo_option('mo_customer_validation_um_default_enable',$this->_isFormEnabled);
			update_mo_option('mo_customer_validation_um_enable_type',$this->_otpType);
			update_mo_option('mo_customer_validation_um_restrict_duplicates',$this->_restrictDuplicates);
	    }
	}


