<?php
	
	
	
	class WooCommerceSocialLoginForm extends FormHandler implements IFormHandler
	{

		private $_oAuthProviders 	= array("facebook","twitter","google",
											"amazon","linkedIn","paypal",
											"instagram","disqus","yahoo","vk");
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
            $this->_isLoginOrSocialForm = TRUE;
            $this->_isAjaxForm = TRUE;
			$this->_formSessionVar = FormSessionVars::WC_SOCIAL_LOGIN;
			$this->_otpType = "phone";
			$this->_phoneFormId = "#mo_phone_number";
			$this->_formKey = 'WC_SOCIAL_LOGIN';
			$this->_formName = mo_("Woocommerce Social Login <i>( SMS Verification Only )</i>");
			$this->_isFormEnabled = get_mo_option('mo_customer_validation_wc_social_login_enable') ? TRUE : FALSE;
			parent::__construct();
		}


		
		function handleForm()
		{
			$this->includeRequiredFiles();
			foreach ($this->_oAuthProviders as $provider)
			{
				add_filter( 'wc_social_login_'.$provider.'_profile', array($this,'mo_wc_social_login_profile'), 99 ,1 );
				add_filter( 'wc_social_login_' . $provider . '_new_user_data', array($this,'mo_wc_social_login'), 99 ,2 );
			}
			$this->routeData();
		}


		function routeData()
		{
			if(!array_key_exists('option', $_REQUEST)) return;

			switch (trim($_REQUEST['option'])) 
			{
				case "miniorange-ajax-otp-generate":
					$this->_handle_wc_ajax_send_otp($_POST);			break; 				
				case "miniorange-ajax-otp-validate":
					$this->processOTPEntered($_REQUEST);				break; 				
				case "mo_ajax_form_validate":
					$this->_handle_wc_create_user_action($_POST);		break; 			
			}
		}


		
		function includeRequiredFiles()
		{
			if( !function_exists('is_plugin_active') ) include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			if( is_plugin_active( 'woocommerce-social-login/woocommerce-social-login.php' ) )
				require_once plugin_dir_path(MOV_DIR) . 'woocommerce-social-login/includes/class-wc-social-login-provider-profile.php';
		}


		
		function mo_wc_social_login_profile($profile)
		{
			MoUtility::checkSession();
			MoUtility::initialize_transaction($this->_formSessionVar);
			$_SESSION['wc_provider'] = maybe_serialize($profile);
			return $profile;
		}


		
		function mo_wc_social_login($usermeta,$profile)
		{
			miniorange_site_challenge_otp(NULL,$usermeta['user_email'],NULL,NULL,'external',NULL,
				array('data'=>$usermeta,'message'=>MoMessages::showMessage('PHONE_VALIDATION_MSG'),
				'form'=>'WC_SOCIAL','curl'=>MoUtility::currentPageUrl()));
		}


		
		function _handle_wc_create_user_action($postdata)
		{
			MoUtility::checkSession();
			if(!$this->checkIfVerificationNotStarted() && $_SESSION[$this->_formSessionVar]=='validated')
				$this->create_new_wc_social_customer($postdata);
		}
		

		
		function create_new_wc_social_customer($userdata)
		{
			require_once  plugin_dir_path(MOV_DIR) . 'woocommerce/includes/class-wc-emails.php';
			WC_Emails::init_transactional_emails();
			
			MoUtility::checkSession();
			$auth = maybe_unserialize($_SESSION['wc_provider']);
			$this->unsetOTPSessionVariables();
			$profile = new WC_Social_Login_Provider_Profile( $auth );
			$phone = $userdata['mo_phone_number'];
			$userdata = array(
				'role'		=>'customer',
				'user_login' => $profile->has_email() ? sanitize_email( $profile->get_email() ) : $profile->get_nickname(),
				'user_email' => $profile->get_email(),
				'user_pass'  => wp_generate_password(),
				'first_name' => $profile->get_first_name(),
				'last_name'  => $profile->get_last_name(),
			);

			if ( empty( $userdata['user_login'] ) )
				$userdata['user_login'] = $userdata['first_name'] . $userdata['last_name'];

			$append     = 1;
			$o_username = $userdata['user_login'];

			while ( username_exists( $userdata['user_login'] ) ) {
				$userdata['user_login'] = $o_username . $append;
				$append ++;
			}

			$customer_id = wp_insert_user( $userdata );

			update_user_meta( $customer_id, 'billing_phone', $phone );
			update_user_meta( $customer_id, 'telephone', $phone );
			
			do_action( 'woocommerce_created_customer', $customer_id, $userdata, false );

			$user = get_user_by( 'id', $customer_id );

			$profile->update_customer_profile( $user->ID, $user );

			if ( ! $message = apply_filters( 'wc_social_login_set_auth_cookie', '', $user ) ) {
				wc_set_customer_auth_cookie( $user->ID );
				update_user_meta( $user->ID, '_wc_social_login_' . $profile->get_provider_id() . '_login_timestamp', current_time( 'timestamp' ) );
				update_user_meta( $user->ID, '_wc_social_login_' . $profile->get_provider_id() . '_login_timestamp_gmt', time() );
				do_action( 'wc_social_login_user_authenticated', $user->ID, $profile->get_provider_id() );
			} else {
				wc_add_notice( $message, 'notice' );
			}

			if ( is_wp_error( $customer_id ) ) {
				$this->redirect( 'error', 0, $customer_id->get_error_code() );
			} else {
				$this->redirect( null, $customer_id );
			}
		}


		
		function redirect( $type = null, $user_id = 0, $error_code = 'wc-social-login-error' ) 
		{
			$user = get_user_by( 'id', $user_id );

			if ( MoUtility::isBlank( $user->user_email ) ) {
				$return_url = add_query_arg( 'wc-social-login-missing-email', 'true', wc_customer_edit_account_url() );
			} else {
				$return_url = get_transient( 'wcsl_' . md5( $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] ) );
				$return_url = $return_url ? esc_url( urldecode( $return_url ) ) : wc_get_page_permalink( 'myaccount' );
				delete_transient( 'wcsl_' . md5( $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] ) );
			}

			if ( 'error' === $type )
				$return_url = add_query_arg( $error_code, 'true', $return_url );
			
			wp_safe_redirect( esc_url_raw( $return_url ) );
			exit;
		}


		
		function handle_failed_verification($user_login,$user_email,$phone_number)
		{
			MoUtility::checkSession();
			if(!isset($_SESSION[$this->_formSessionVar])) return;
			wp_send_json( MoUtility::_create_json_response(MoUtility::_get_invalid_otp_method(),'error'));
		}


	    
		function handle_post_verification($redirect_to,$user_login,$user_email,$password,$phone_number,$extra_data)
		{
			MoUtility::checkSession();
			if(!isset($_SESSION[$this->_formSessionVar])) return;
			$_SESSION[$this->_formSessionVar] = 'validated';	
			wp_send_json( MoUtility::_create_json_response('','success') );
		}


		
		public function unsetOTPSessionVariables()
		{
			unset($_SESSION[$this->_txSessionId]);
			unset($_SESSION[$this->_formSessionVar]);
		}

		
		
		function _handle_wc_ajax_send_otp($data)
		{
			MoUtility::checkSession();
			if(!$this->checkIfVerificationNotStarted())
				miniorange_site_challenge_otp('ajax_phone','',null, trim($data['user_phone']),$this->_otpType,null,$data);
		}


		
		function processOTPEntered($data)
		{
			MoUtility::checkSession();
			if($this->checkIfVerificationNotStarted()) return;
			
			if($this->processPhoneNumber($data)) 
				wp_send_json( MoUtility::_create_json_response( MoMessages::showMessage('PHONE_MISMATCH'),'error'));
			else
				do_action('mo_validate_otp','mo_customer_validation_otp_token',NULL);
		}


		
		function processPhoneNumber($data)
		{
			if(strcmp($_SESSION['phone_number_mo'],MoUtility::processPhoneNumber($data['user_phone']))!=0) return FALSE;
		}


		
		function checkIfVerificationNotStarted()
		{
			MoUtility::checkSession();
			return !isset($_SESSION[$this->_formSessionVar]);
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
			$this->_isFormEnabled = isset( $_POST['mo_customer_validation_wc_social_login_enable']) ? $_POST['mo_customer_validation_wc_social_login_enable'] : '';
			update_mo_option('mo_customer_validation_wc_social_login_enable', $this->_isFormEnabled);
		}
	}