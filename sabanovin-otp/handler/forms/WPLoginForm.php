<?php
	
	
	class WPLoginForm extends FormHandler implements IFormHandler
	{
		private $_formSessionVar2;
		private $_savePhoneNumbers;
		private $_byPassAdmin;
		private $_allowLoginThroughPhone;
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
            $this->_isAjaxForm = TRUE;
			$this->_formSessionVar = FormSessionVars::WP_LOGIN_REG_PHONE;
			$this->_formSessionVar2 = FormSessionVars::WP_DEFAULT_LOGIN;
			$this->_phoneFormId = '#mo_phone_number';
            $this->_typePhoneTag = 'mo_wp_login_phone_enable';
            $this->_typeEmailTag = 'mo_wp_login_email_enable';
			$this->_formKey = 'WP_DEFAULT_LOGIN';
			$this->_formName = mo_("WordPress Default Login Form");
			$this->_isFormEnabled = get_mo_option('mo_customer_validation_wp_login_enable') ? TRUE : FALSE;
			parent::__construct();
		}

		
		function handleForm()
		{
            $this->_otpType = get_mo_option('mo_customer_validation_wp_login_enable_type');
			$this->_phoneKey = get_mo_option('mo_customer_validation_wp_login_key');
			$this->_savePhoneNumbers = get_mo_option('mo_customer_validation_wp_login_register_phone') ? true : false;
			$this->_byPassAdmin = get_mo_option('mo_customer_validation_wp_login_bypass_admin') ? true : false;
			$this->_allowLoginThroughPhone = get_mo_option('mo_customer_validation_wp_login_allow_phone_login') ? true : false;
			$this->_restrictDuplicates = get_mo_option('mo_customer_validation_wp_login_restrict_duplicates') ? true : false;
			add_filter( 'authenticate', array($this,'_handle_mo_wp_login'), 99, 3 );
			$this->routeData();
		}


		function routeData()
		{
			if(!array_key_exists('option', $_REQUEST)) return;
			switch (trim($_REQUEST['option'])) 
			{
				case "miniorange-ajax-otp-generate":
					$this->_handle_wp_login_ajax_send_otp($_POST);				break;
				case "miniorange-ajax-otp-validate":
					$this->_handle_wp_login_ajax_form_validate_action($_POST);	break;
				case "mo_ajax_form_validate":
					$this->_handle_wp_login_create_user_action($_POST);			break;
			}
		}


		
		function byPassLogin($user_role)
		{
			return in_array('administrator',$user_role) && $this->byPassCheckForAdmins() ? true : false;
		}


		
		function _handle_wp_login_create_user_action($postdata)
		{
			MoUtility::checkSession();
			if(!isset($_SESSION[$this->_formSessionVar]) 
				|| $_SESSION[$this->_formSessionVar]!='validated') 	return;
			
			$user = is_email( $postdata['log'] ) ? get_user_by("email",$postdata['log']) : get_user_by("login",$postdata['log']);
			update_user_meta($user->data->ID, $this->_phoneKey ,$postdata['mo_phone_number']);
			$this->login_wp_user($user->data->user_login);
		}


		
		function login_wp_user($user_log,$extra_data=null)
		{
			$user = is_email( $user_log ) ? get_user_by("email",$user_log) 
					: ( $this->allowLoginThroughPhone() && MoUtility::validatePhoneNumber($user_log) 
						? $this->getUserFromPhoneNumber($user_log,$this->_phoneKey) : get_user_by("login",$user_log) );
			wp_set_auth_cookie($user->data->ID);
			$this->unsetOTPSessionVariables();
			do_action( 'wp_login', $user->user_login, $user );	
			$redirect = MoUtility::isBlank($extra_data) ? site_url() : $extra_data;
			wp_redirect($redirect);
			exit;
		}


		
		function _handle_mo_wp_login($user,$username,$password)
		{
			$user = $this->getUserIfUsernameIsPhoneNumber($user,$username,$password,$this->_phoneKey);
			
			if(is_wp_error($user)) return $user;

			MoUtility::checkSession();		
			$user_meta 	= get_userdata($user->data->ID);
			$user_role 	= $user_meta->roles;

			if($this->byPassLogin($user_role)) return $user;

            if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0){
                $phone_number = get_user_meta($user->data->ID, $this->_phoneKey);
                $phone_number = !empty($phone_number[0]) ? MoUtility::processPhoneNumber($phone_number[0]) : $phone_number[0];
                $this->askPhoneAndStartVerification($user,$this->_phoneKey,$username,$phone_number);
                $this->fetchPhoneAndStartVerification($user,$this->_phoneKey,$username,$password,$phone_number);
            }
            else if(strcasecmp($this->_otpType,$this->_typeEmailTag)==0){
                $email= $user->data->user_email;
                $this->startEmailVerification($user,$this->_phoneKey,$username,$password,$email);
            }
			return $user;
		} 


		
		function getUserIfUsernameIsPhoneNumber($user,$username,$password,$key)
		{
			if(!$this->allowLoginThroughPhone() || !MoUtility::validatePhoneNumber($username)) return $user;
			$user_info = $this->getUserFromPhoneNumber($username,$key);
			return $user_info ? wp_authenticate_username_password(NULL,$user_info->data->user_login,$password) 
				: new WP_Error( 'INVALID_USERNAME' , mo_(" <b>ERROR:</b> Invalid UserName. ") );
		}


		
		function getUserFromPhoneNumber($username,$key)
		{
			global $wpdb;
			$results = $wpdb->get_row("SELECT `user_id` FROM `{$wpdb->prefix}usermeta` WHERE `meta_key` = '$key' AND `meta_value` =  '$username'");			
			return !MoUtility::isBlank($results) ? get_userdata($results->user_id) : false;
		}


		
		function askPhoneAndStartVerification($user,$key,$username,$phone_number)
		{
			if(!MoUtility::isBlank($phone_number)) return;
				
			if( !$this->savePhoneNumbers() )			
				miniorange_site_otp_validation_form(null,null,null, MoMessages::showMessage('PHONE_NOT_FOUND'),null,null);
			else
			{
				MoUtility::initialize_transaction($this->_formSessionVar);
				miniorange_site_challenge_otp(NULL,$user->data->user_login,NULL,NULL,'external',NULL,
					array('data'=>array('user_login'=>$username),'message'=>MoMessages::showMessage('REGISTER_PHONE_LOGIN'),
					'form'=>$key,'curl'=>MoUtility::currentPageUrl()));
			}					
		}


		
		function fetchPhoneAndStartVerification($user,$key,$username,$password,$phone_number)
		{
			if((array_key_exists($this->_formSessionVar,$_SESSION) && strcasecmp($_SESSION[$this->_formSessionVar],'validated')==0)
				|| (array_key_exists($this->_formSessionVar2,$_SESSION) && strcasecmp($_SESSION[$this->_formSessionVar2],'validated')==0)) return;
			MoUtility::initialize_transaction($this->_formSessionVar2);
			$redirect_to = isset($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : MoUtility::currentPageUrl();
			miniorange_site_challenge_otp($username,null,null,$phone_number,"phone",$password,$redirect_to,false);
		}


        
        function startEmailVerification($user,$key,$username,$password,$email)
        {
            if((array_key_exists($this->_formSessionVar,$_SESSION) && strcasecmp($_SESSION[$this->_formSessionVar],'validated')==0)
                || (array_key_exists($this->_formSessionVar2,$_SESSION) && strcasecmp($_SESSION[$this->_formSessionVar2],'validated')==0)) return;
            MoUtility::initialize_transaction($this->_formSessionVar2);
            miniorange_site_challenge_otp($username,$email,null,null,"email");
        }
		

		
		function _handle_wp_login_ajax_send_otp($data)
		{
			MoUtility::checkSession();
			if($this->restrictDuplicates() 
				&& !MoUtility::isBlank($this->getUserFromPhoneNumber($data['user_phone'],$this->_phoneKey)))
				wp_send_json(MoUtility::_create_json_response(MoMessages::showMessage('PHONE_EXISTS'),MoConstants::ERROR_JSON_TYPE));
			elseif(isset($_SESSION[$this->_formSessionVar]))
				miniorange_site_challenge_otp('ajax_phone','',null, trim($data['user_phone']),"phone",null,$data);
		}


		
		function _handle_wp_login_ajax_form_validate_action($data)
		{
			MoUtility::checkSession();
			if(!isset($_SESSION[$this->_formSessionVar])) return;

			if(strcmp($_SESSION['phone_number_mo'], MoUtility::processPhoneNumber($data['user_phone'])))
				wp_send_json( MoUtility::_create_json_response( MoMessages::showMessage('PHONE_MISMATCH'),'error'));
			else
				do_action('mo_validate_otp','mo_customer_validation_otp_token',NULL);
		}


		
		function handle_failed_verification($user_login,$user_email,$phone_number)
		{
			MoUtility::checkSession();
			if(!isset($_SESSION[$this->_formSessionVar]) 
				&& !isset($_SESSION[$this->_formSessionVar2]) ) return;

			if(isset($_SESSION[$this->_formSessionVar])){	
				$_SESSION[$this->_formSessionVar] = 'verification_failed';
				wp_send_json( MoUtility::_create_json_response(MoUtility::_get_invalid_otp_method(),'error'));
			}

			if(isset($_SESSION[$this->_formSessionVar2]))
				miniorange_site_otp_validation_form($user_login,$user_email,$phone_number,MoUtility::_get_invalid_otp_method(),"phone",FALSE);
		}


	    
		function handle_post_verification($redirect_to,$user_login,$user_email,$password,$phone_number,$extra_data)
		{
			MoUtility::checkSession();
			if(!isset($_SESSION[$this->_formSessionVar])
				 && !isset($_SESSION[$this->_formSessionVar2])) return;

			if(isset($_SESSION[$this->_formSessionVar])){
				$_SESSION[$this->_formSessionVar] = 'validated';
				wp_send_json( MoUtility::_create_json_response('','success') );
			}

			if(isset($_SESSION[$this->_formSessionVar2]))
				$this->login_wp_user(array_key_exists('log',$_POST) ? $_POST['log'] : $_POST['username']);
		}


		
		public function unsetOTPSessionVariables()
		{
			unset($_SESSION[$this->_txSessionId]);
			unset($_SESSION[$this->_formSessionVar]);
			unset($_SESSION[$this->_formSessionVar2]);
		}


		
		public function getPhoneNumberSelector($selector)	
		{
			MoUtility::checkSession();
			if($this->isFormEnabled()) array_push($selector, $this->_phoneFormId); 
			return $selector;
		}


		
		function handleFormOptions()
	    {
			if(!MoUtility::areFormOptionsBeingSaved()) return;
			
			$this->_isFormEnabled = isset( $_POST['mo_customer_validation_wp_login_enable']) ? $_POST['mo_customer_validation_wp_login_enable'] : 0;
			$this->_savePhoneNumbers = isset( $_POST['mo_customer_validation_wp_login_register_phone']) ? $_POST['mo_customer_validation_wp_login_register_phone'] : '';
			$this->_byPassAdmin = isset( $_POST['mo_customer_validation_wp_login_bypass_admin']) ? $_POST['mo_customer_validation_wp_login_bypass_admin'] : '';
			$this->_phoneKey = isset( $_POST['wp_login_phone_field_key']) ? $_POST['wp_login_phone_field_key'] : '';
			$this->_allowLoginThroughPhone = isset( $_POST['mo_customer_validation_wp_login_allow_phone_login']) ? $_POST['mo_customer_validation_wp_login_allow_phone_login'] : '';
			$this->_restrictDuplicates = isset( $_POST['mo_customer_validation_wp_login_restrict_duplicates']) ? $_POST['mo_customer_validation_wp_login_restrict_duplicates'] : '';
            $this->_otpType = isset( $_POST['mo_customer_validation_wp_login_enable_type']) ? $_POST['mo_customer_validation_wp_login_enable_type'] : '';

            update_mo_option('mo_customer_validation_wp_login_enable_type', $this->_otpType);
			update_mo_option('mo_customer_validation_wp_login_enable', $this->_isFormEnabled);
			update_mo_option('mo_customer_validation_wp_login_register_phone', $this->_savePhoneNumbers);
			update_mo_option('mo_customer_validation_wp_login_bypass_admin', $this->_byPassAdmin);
			update_mo_option('mo_customer_validation_wp_login_key', $this->_phoneKey);
			update_mo_option('mo_customer_validation_wp_login_allow_phone_login', $this->_allowLoginThroughPhone);
			update_mo_option('mo_customer_validation_wp_login_restrict_duplicates', $this->_restrictDuplicates);
		}
		

		
		
		

		
		public function savePhoneNumbers() { return $this->_savePhoneNumbers; }

		
		function byPassCheckForAdmins() { return $this->_byPassAdmin; }

		
		function allowLoginThroughPhone() { return $this->_allowLoginThroughPhone; }
	}