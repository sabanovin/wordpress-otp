<?php
	
	
	class WooCommerceRegistrationForm extends FormHandler implements IFormHandler
	{
		private $_redirectToPage;
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
			$this->_formSessionVar = FormSessionVars::WC_DEFAULT_REG;
			$this->_typePhoneTag = 'mo_wc_phone_enable';
			$this->_typeEmailTag = 'mo_wc_email_enable';
			$this->_typeBothTag = 'mo_wc_both_enable';
			$this->_phoneFormId = '#reg_billing_phone';
			$this->_formKey = 'WC_REG_FROM';
			$this->_formName = mo_("Woocommerce Registration Form");
			$this->_isFormEnabled = get_mo_option('mo_customer_validation_wc_default_enable') ? TRUE : FALSE;
			parent::__construct();
		}
			
		
		function handleForm()
		{
			$this->_otpType = get_mo_option('mo_customer_validation_wc_enable_type');
			$this->_redirectToPage = get_mo_option('mo_customer_validation_wc_redirect'); 
			$this->_restrictDuplicates = get_mo_option('mo_customer_validation_wc_restrict_duplicates') ? true : false;

			add_filter('woocommerce_process_registration_errors', array($this,'woocommerce_site_registration_errors'),99,4);
            add_action('woocommerce_created_customer', array( $this, 'register_woocommerce_user' ),1,3);
            add_filter('woocommerce_registration_redirect', array( $this,'custom_registration_redirect'), 1, 1);
			if($this->isPhoneVerificationEnabled()) add_action( 'woocommerce_register_form', array($this,'mo_add_phone_field'),1);

		}


        
        function custom_registration_redirect($var) {
		    return  MoUtility::isBlank($this->_redirectToPage) ? $var
                : get_permalink( get_page_by_title($this->_redirectToPage)->ID);
        }


		
		function isPhoneVerificationEnabled()
		{
			return (strcasecmp($this->_otpType,$this->_typePhoneTag)==0 || strcasecmp($this->_otpType,$this->_typeBothTag)==0);
		}


        
		function woocommerce_site_registration_errors(WP_Error $errors,$username,$password,$email)
		{
			MoUtility::checkSession();
			if(!MoUtility::isBlank(array_filter($errors->errors))) return $errors;

			if(isset($_SESSION[$this->_formSessionVar]) && $_SESSION[$this->_formSessionVar]=='validated') {
                $this->unsetOTPSessionVariables();
                return $errors;
            }
			
			MoUtility::initialize_transaction($this->_formSessionVar);
			if( get_mo_option( 'woocommerce_registration_generate_username' )==='no' )
			{
				if (  MoUtility::isBlank( $username ) || ! validate_username( $username ) )
					return new WP_Error( 'registration-error-invalid-username', __( 'Please enter a valid account username.', 'woocommerce' ) );
				if ( username_exists( $username ) )
					return new WP_Error( 'registration-error-username-exists', __( 'An account is already registered with that username. Please choose another.', 'woocommerce' ) );
			}

			if( get_mo_option( 'woocommerce_registration_generate_password' )==='no' )
			{
				if (  MoUtility::isBlank( $password ) )
					return new WP_Error( 'registration-error-invalid-password', __( 'Please enter a valid account password.', 'woocommerce' ) );
			}

			if ( MoUtility::isBlank( $email ) || ! is_email( $email ) )
				return new WP_Error( 'registration-error-invalid-email', __( 'Please enter a valid email address.', 'woocommerce' ) );
			if ( email_exists( $email ) )
				return new WP_Error( 'registration-error-email-exists', __( 'An account is already registered with your email address. Please login.', 'woocommerce' ) );

			do_action( 'woocommerce_register_post', $username, $email, $errors );
			if($errors->get_error_code())
				throw new Exception( $errors->get_error_message() );

						return $this->processFormFields($username,$email,$errors,$password); 		
		}


        
		function processFormFields($username,$email,$errors,$password)
		{
			global $phoneLogic;
			if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0)
			{
				if ( !isset( $_POST['billing_phone'] ) || !MoUtility::validatePhoneNumber($_POST['billing_phone']))
					return new WP_Error( 'billing_phone_error',
						str_replace("##phone##",$_POST['billing_phone'],$phoneLogic->_get_otp_invalid_format_message()) );
				elseif($this->_restrictDuplicates && $this->isPhoneNumberAlreadyInUse($_POST['billing_phone'],'billing_phone'))
					return new WP_Error( 'billing_phone_error', MoMessages::showMessage('PHONE_EXISTS'));
				miniorange_site_challenge_otp($username,$email,$errors,$_POST['billing_phone'],"phone",$password);
			}
			else if(strcasecmp($this->_otpType,$this->_typeEmailTag)==0)
			{
				$phone = isset($_POST['billing_phone']) ? $_POST['billing_phone'] : "";
				miniorange_site_challenge_otp($username,$email,$errors,$phone,"email",$password);
			}
			else if(strcasecmp($this->_otpType,$this->_typeBothTag)==0)
			{
				if ( !isset( $_POST['billing_phone'] ) || !MoUtility::validatePhoneNumber($_POST['billing_phone']))
					return new WP_Error( 'billing_phone_error',
						str_replace("##phone##",$_POST['billing_phone'],$phoneLogic->_get_otp_invalid_format_message()) );
				miniorange_site_challenge_otp($username,$email,$errors,$_POST['billing_phone'],"both",$password);
			}
		}


		
		public function register_woocommerce_user($customer_id, $new_customer_data, $password_generated)
		{
			if(isset($_POST['billing_phone'])) {
                update_user_meta($customer_id, 'billing_phone', sanitize_text_field($_POST['billing_phone']));
            }
		} 


		
		function mo_add_phone_field()
		{
			echo '<p class="form-row form-row-wide">
					<label for="reg_billing_phone">'.mo_('Phone').'<span class="required">*</span></label>
					<input type="text" class="input-text" name="billing_phone" id="reg_billing_phone" value="'.(!empty( $_POST['billing_phone'] ) ? $_POST['billing_phone'] : "").'" />
			  	  </p>';
		}


		
		function handle_failed_verification($user_login,$user_email,$phone_number)
		{
			MoUtility::checkSession();
			if(!isset($_SESSION[$this->_formSessionVar])) return;
			$otpVerType = strcasecmp($this->_otpType,$this->_typePhoneTag)==0 ? "phone" 
							: (strcasecmp($this->_otpType,"mo_wc_both_enable")==0 ? "both" : "email" );
			$fromBoth = strcasecmp($otpVerType,"both")==0 ? TRUE : FALSE;
			miniorange_site_otp_validation_form($user_login,$user_email,$phone_number,MoUtility::_get_invalid_otp_method(),$otpVerType,$fromBoth);
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
		}


        
		public function getPhoneNumberSelector($selector)	
		{
			if($this->isFormEnabled() && $this->isPhoneVerificationEnabled()) array_push($selector, $this->_phoneFormId); 
			return $selector;
		}


        
		function isPhoneNumberAlreadyInUse($phone,$key)
		{
			global $wpdb;
			MoUtility::processPhoneNumber($phone);
			$results = $wpdb->get_row("SELECT `user_id` FROM `{$wpdb->prefix}usermeta` WHERE `meta_key` = '$key' AND `meta_value` =  '$phone'");			
			return !MoUtility::isBlank($results);
		}


		
		function handleFormOptions()
		{
			if(!MoUtility::areFormOptionsBeingSaved()) return;

			$this->_isFormEnabled = isset( $_POST['mo_customer_validation_wc_default_enable']) ? $_POST['mo_customer_validation_wc_default_enable'] : 0;
			$this->_otpType = isset( $_POST['mo_customer_validation_wc_enable_type']) ? $_POST['mo_customer_validation_wc_enable_type'] : '';
			$this->_restrictDuplicates = isset( $_POST['mo_customer_validation_wc_restrict_duplicates']) ? $_POST['mo_customer_validation_wc_restrict_duplicates'] : '';
			$this->_redirectToPage = isset( $_POST['page_id']) ? get_the_title($_POST['page_id']) : 'My Account';

			update_mo_option('mo_customer_validation_wc_default_enable',$this->_isFormEnabled);
			update_mo_option('mo_customer_validation_wc_enable_type',$this->_otpType);
			update_mo_option('mo_customer_validation_wc_restrict_duplicates',$this->_restrictDuplicates);
			update_mo_option('mo_customer_validation_wc_redirect',$this->_redirectToPage);
		}

		
		
		
		
		public function redirectToPage(){ return $this->_redirectToPage; }
	}