<?php
	
	
	class WpMemberForm extends FormHandler implements IFormHandler
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
			$this->_formSessionVar = FormSessionVars::WPMEMBER_REG;
			$this->_formEmailVer = FormSessionVars::WPM_PHONE_VER;
			$this->_formPhoneVer = FormSessionVars::WPM_EMAIL_VER;
			$this->_emailKey = 'user_email'; 
			$this->_phoneKey = 'phone1';
			$this->_phoneFormId = "input[name=phone1]";
			$this->_formKey = 'WP_MEMBER_FORM';
			$this->_typePhoneTag = "mo_wpmember_reg_phone_enable";
			$this->_typeEmailTag = "mo_wpmember_reg_email_enable";
			$this->_formName = mo_("WP Members");
			$this->_isFormEnabled = get_mo_option('mo_customer_validation_wp_member_reg_enable') ? TRUE : FALSE;
			parent::__construct();
		}

		
		function handleForm()
		{
			$this->_otpType = get_mo_option('mo_customer_validation_wp_member_reg_enable_type');
			add_filter('wpmem_register_form_rows', array($this,'wpmember_add_button'),99,2);
			add_action('wpmem_pre_register_data', array($this,'validate_wpmember_submit'),99,1);
			
			$this->routeData();
		}


		function routeData()
		{
			if(!array_key_exists('option', $_REQUEST)) return;
			switch (trim($_REQUEST['option'])) 
			{
				case "miniorange-wpmember-form":
					$this->_handle_wp_member_form($_POST);		break;
			}
		}

		
		function _handle_wp_member_form($data)
		{		
			MoUtility::checkSession();
			MoUtility::initialize_transaction($this->_formSessionVar);

			$this->processEmailAndStartOTPVerificationProcess($data);
			$this->processPhoneAndStartOTPVerificationProcess($data);
			$this->sendErrorMessageIfOTPVerificationNotStarted();
		}


		
		function processEmailAndStartOTPVerificationProcess($data)
		{
			if(!array_key_exists('user_phone', $data) || !isset($data['user_phone'])) return;

			$_SESSION[$this->_formPhoneVer] = $data['user_phone'];
			miniorange_site_challenge_otp(null,'',null,$data['user_phone'],"phone",null,null,false);
		}


		
		function processPhoneAndStartOTPVerificationProcess($data)
		{
			if(!array_key_exists($this->_emailKey, $data) || !isset($data[$this->_emailKey])) return;

			$_SESSION[$this->_formEmailVer] = $data[$this->_emailKey];
			miniorange_site_challenge_otp(null,$data[$this->_emailKey],null,'',"email",null,null,false);
		}
		

		
		function sendErrorMessageIfOTPVerificationNotStarted()
		{
			if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0)
				wp_send_json( MoUtility::_create_json_response( MoMessages::showMessage('ENTER_PHONE'),MoConstants::ERROR_JSON_TYPE) );
			else
				wp_send_json( MoUtility::_create_json_response( MoMessages::showMessage('ENTER_EMAIL'),MoConstants::ERROR_JSON_TYPE) );
		}


		
		function wpmember_add_button($rows, $tag)
		{
			foreach($rows as $key=>$field)
			{
				if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0 && $key=="phone1")
				{
					$rows[$key]['field'] .= $this->_add_shortcode_to_wpmember("phone",$field['meta']);
					break;
				}
				else if(strcasecmp($this->_otpType,$this->_typeEmailTag)==0 && $key=="user_email")
				{
					$rows[$key]['field'] .= $this->_add_shortcode_to_wpmember("email",$field['meta']);
					break;
				}			
			}
			return $rows;
		}


		
		function validate_wpmember_submit($fields)
		{
			global $wpmem_themsg; 
			MoUtility::checkSession();
			if(!array_key_exists($this->_txSessionId, $_SESSION)) $wpmem_themsg =  MoMessages::showMessage('PLEASE_VALIDATE');
				
			if(!$this->validate_submitted($fields)) return;
			
			do_action('mo_validate_otp',NULL,$fields['validate_otp']);
		}


		
		function validate_submitted($fields)
		{
			global $wpmem_themsg;
			MoUtility::checkSession();
			if(array_key_exists($this->_formEmailVer, $_SESSION) && strcasecmp($_SESSION[$this->_formEmailVer], $fields[$this->_emailKey])!=0)
			{
				$wpmem_themsg =  MoMessages::showMessage('EMAIL_MISMATCH');
				return false;
			}
			else if(array_key_exists($this->_formPhoneVer, $_SESSION) && strcasecmp($_SESSION[$this->_formPhoneVer], $fields[$this->_phoneKey])!=0)
			{	
				$wpmem_themsg =  MoMessages::showMessage('PHONE_MISMATCH');
				return false;
			}
			else
				return true;
		}


		
		function _add_shortcode_to_wpmember($mo_type,$field) 
		{
			$img  			= "<div style='display:table;text-align:center;'><img src='".MOV_URL. "includes/images/loader.gif'></div>";
			$field_content  = "<div style='margin-top: 2%;'><button type='button' class='button alt' style='width:100%;height:30px;";
			$field_content .= "font-family: Roboto;font-size: 12px !important;' id='miniorange_otp_token_submit' ";
			$field_content .= "title='Please Enter an '".$mo_type."'to enable this.'>Click Here to Verify ". $mo_type."</button></div>";
			$field_content .= "<div style='margin-top:2%'><div id='mo_message' hidden='' style='background-color: #f7f6f7;padding: ";
			$field_content .= "1em 2em 1em 3.5em;'></div></div>";
			$field_content .= '<script>jQuery(document).ready(function(){$mo=jQuery;$mo("#miniorange_otp_token_submit").click(function(o){ ';
			$field_content .= 'var e=$mo("input[name='.$field.']").val(); $mo("#mo_message").empty(),$mo("#mo_message").append("'.$img.'"),';
			$field_content .= '$mo("#mo_message").show(),$mo.ajax({url:"'.site_url().'/?option=miniorange-wpmember-form",type:"POST",';
			$field_content .= 'data:{user_'.$mo_type.':e},crossDomain:!0,dataType:"json",success:function(o){ ';
			$field_content .= 'if(o.result=="success"){$mo("#mo_message").empty(),$mo("#mo_message").append(o.message),';
			$field_content .= '$mo("#mo_message").css("border-top","3px solid green"),$mo("input[name=email_verify]").focus()}else{';
			$field_content .= '$mo("#mo_message").empty(),$mo("#mo_message").append(o.message),$mo("#mo_message").css("border-top","3px solid red")';
			$field_content .= ',$mo("input[name=phone_verify]").focus()} ;},error:function(o,e,n){}})});});</script>';
			
			return $field_content;
		}


		
		function handle_failed_verification($user_login,$user_email,$phone_number)
		{
			global $wpmem_themsg; 
			MoUtility::checkSession();
			if(!isset($_SESSION[$this->_formSessionVar])) return;
			$wpmem_themsg =  MoUtility::_get_invalid_otp_method();
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

			$this->_isFormEnabled = isset( $_POST['mo_customer_validation_wp_member_reg_enable']) ? $_POST['mo_customer_validation_wp_member_reg_enable'] : 0;
			$this->_otpType = isset( $_POST['mo_customer_validation_wp_member_reg_enable_type']) ? $_POST['mo_customer_validation_wp_member_reg_enable_type'] : '';
			update_mo_option('mo_customer_validation_wp_member_reg_enable', $this->_isFormEnabled);
			update_mo_option('mo_customer_validation_wp_member_reg_enable_type',$this->_otpType);
		}
	}