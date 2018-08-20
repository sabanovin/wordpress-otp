<?php
	
	
	
	class UltimateProRegistrationForm extends FormHandler implements IFormHandler
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
			$this->_formSessionVar = FormSessionVars::ULTIMATE_PRO;
			$this->_formEmailVer = FormSessionVars::ULTIMATE_PRO_EMAIL_VER;	
			$this->_formPhoneVer = FormSessionVars::ULTIMATE_PRO_PHONE_VER;
			$this->_phoneFormId = 'input[name=phone]';
			$this->_formKey = 'ULTIMATE_MEM_PRO';
			$this->_typePhoneTag = "mo_ultipro_phone_enable";
			$this->_typeEmailTag = "mo_ultipro_email_enable";
			$this->_formName = mo_("Ultimate Membership Pro Form");
			$this->_isFormEnabled = get_mo_option('mo_customer_validation_ultipro_enable') ? TRUE : FALSE;
			parent::__construct();
		}

		
		function handleForm()
		{	
			$this->_otpType = get_mo_option('mo_customer_validation_ultipro_type');
			add_action("wp_ajax_nopriv_ihc_check_reg_field_ajax", array($this,"_ultipro_handle_submit"),1 );
			add_action('wp_ajax_ihc_check_reg_field_ajax', array($this,'_ultipro_handle_submit'), 1);
			
			if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0)
				add_shortcode('mo_phone', array($this,'_phone_shortcode'));
			if(strcasecmp($this->_otpType,$this->_typeEmailTag)==0)
				add_shortcode('mo_email', array($this,'_email_shortcode'));

			$this->routeData();
		}

		
		function routeData()
		{
			if(!array_key_exists('option', $_GET)) return;

			switch (trim($_GET['option'])) 
			{
				case "miniorange-ulti":
					$this->_handle_ulti_form($_POST);		break;
			}
		}


		
		function _ultipro_handle_submit()
		{
			$field_check_list = array('phone','user_email','validate');
			$register_msg = ihc_return_meta_arr('register-msg');

		    if (isset($_REQUEST['type']) && isset($_REQUEST['value']))
		    	echo ihc_check_value_field($_REQUEST['type'], $_REQUEST['value'], $_REQUEST['second_value'], $register_msg);
		    else if (isset($_REQUEST['fields_obj']))
		    {
		        $arr = $_REQUEST['fields_obj'];
		        foreach ($arr as $k=>$v)
		        {
		        	if(in_array($v['type'],$field_check_list))
		        		$return_arr[] = $this->validate_umpro_submitted_value($v['type'],$v['value'],$v['second_value'],$register_msg);
					else
						$return_arr[] = array( 'type' => $v['type'], 'value' => ihc_check_value_field($v['type'], 
												$v['value'], $v['second_value'], $register_msg) );
		        }
		        echo json_encode($return_arr);
		    }
		    die();
		}


		
		function _phone_shortcode()
		{
			$img   = "<div style='display:table;text-align:center;'><img src='".MOV_URL. "includes/images/loader.gif'></div>";
			$div   = "<div style='margin-top: 2%;'><button type='button' disabled='disabled' class='button alt' style='width:100%;height:30px;";
			$div  .= "font-family: Roboto;font-size: 12px !important;' id='miniorange_otp_token_submit' title='Please Enter an phone to enable this.'>";
			$div  .= "Click Here to Verify Phone</button></div><div style='margin-top:2%'><div id='mo_message' hidden='' ";
			$div  .= "style='background-color: #f7f6f7;padding: 1em 2em 1em 3.5em;''></div></div>";
			
			$html  = '<script>jQuery(document).ready(function(){$mo=jQuery; var divElement = "'.$div.'"; ';
			$html .= '$mo("input[name=phone]").change(function(){ if(!$mo(this).val()){ $mo("#miniorange_otp_token_submit").prop("disabled",true);';
			$html .= ' }else{ $mo("#miniorange_otp_token_submit").prop("disabled",false); } });';
			$html .= ' $mo(divElement).insertAfter($mo( "input[name=phone]")); $mo("#miniorange_otp_token_submit").click(function(o){ ';
			$html .= 'var e=$mo("input[name=phone]").val(); $mo("#mo_message").empty(),$mo("#mo_message").append("'.$img.'"),';
			$html .= '$mo("#mo_message").show(),$mo.ajax({url:"'.site_url().'/?option=miniorange-ulti",type:"POST",';
			$html .= 'data:{user_phone:e},crossDomain:!0,dataType:"json",success:function(o){ if(o.result=="success"){$mo("#mo_message").empty(),';
			$html .= '$mo("#mo_message").append(o.message),$mo("#mo_message").css("border-top","3px solid green"),';
			$html .= '$mo("input[name=email_verify]").focus()}else{$mo("#mo_message").empty(),$mo("#mo_message").append(o.message),';
			$html .= '$mo("#mo_message").css("border-top","3px solid red"),$mo("input[name=phone_verify]").focus()} ;},';
			$html .= 'error:function(o,e,n){}})});});</script>';
			return $html;
		}


		
		function _email_shortcode()
		{
			$img   = "<div style='display:table;text-align:center;'><img src='".MOV_URL. "includes/images/loader.gif'></div>";
			$div   = "<div style='margin-top: 2%;'><button type='button' disabled='disabled' class='button alt' ";
			$div  .= "style='width:100%;height:30px;font-family: Roboto;font-size: 12px !important;' id='miniorange_otp_token_submit' ";
			$div  .= "title='Please Enter an email to enable this.'>Click Here to Verify your email</button></div><div style='margin-top:2%'>";
			$div  .= "<div id='mo_message' hidden='' style='background-color: #f7f6f7;padding: 1em 2em 1em 3.5em;''></div></div>";
			$html  = '<script>jQuery(document).ready(function(){$mo=jQuery; var divElement = "'.$div.'"; ';
			$html .= '$mo("input[name=user_email]").change(function(){ if(!$mo(this).val()){ ';
			$html .= '$mo("#miniorange_otp_token_submit").prop("disabled",true); }else{ ';
			$html .= '$mo("#miniorange_otp_token_submit").prop("disabled",false); } }); ';
			$html .= '$mo(divElement).insertAfter($mo( "input[name=user_email]")); $mo("#miniorange_otp_token_submit").click(function(o){ ';
			$html .= 'var e=$mo("input[name=user_email]").val(); $mo("#mo_message").empty(),$mo("#mo_message").append("'.$img.'"),';
			$html .= '$mo("#mo_message").show(),$mo.ajax({url:"'.site_url().'/?option=miniorange-ulti",type:"POST",data:{user_email:e},crossDomain:!0,dataType:"json",success:function(o){ if(o.result=="success"){$mo("#mo_message").empty(),$mo("#mo_message").append(o.message),$mo("#mo_message").css("border-top","3px solid green"),$mo("input[name=email_verify]").focus()}else{$mo("#mo_message").empty(),$mo("#mo_message").append(o.message),$mo("#mo_message").css("border-top","3px solid red"),$mo("input[name=phone_verify]").focus()} ;},error:function(o,e,n){}})});});</script>';	
			return $html;
		}



		function _handle_ulti_form($data)
		{
			MoUtility::checkSession();
			MoUtility::initialize_transaction($this->_formSessionVar);

			if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0)
			{
				$_SESSION[$this->_formPhoneVer] = $data['user_phone'];
				miniorange_site_challenge_otp('testuser',null,null,$data['user_phone'],"phone");
			}
			else
			{
				$_SESSION[$this->_formEmailVer] = trim($data['user_email']);
				miniorange_site_challenge_otp('testuser',$data['user_email'],null,null,"email");
			}
		}


		
		function validate_umpro_submitted_value($type,$value,$second_value,$register_msg)
		{
			MoUtility::checkSession();
			$return = array();
			switch ($type)
			{
				case 'phone':
					$this->processPhone($return,$type,$value,$second_value,$register_msg);			break;
				case 'user_email':
					$this->processEmail($return,$type,$value,$second_value,$register_msg);			break;	
				case 'validate':
					$this->processOTPEntered($return,$type,$value,$second_value,$register_msg); 	break;
			}
			return $return;
		}


		
		function processPhone(&$return,$type,$value,$second_value,$register_msg)
		{
			if(strcasecmp($this->_otpType,$this->_typePhoneTag)!=0)
				$return = array( 'type' => $type, 'value' => ihc_check_value_field($type, $value, $second_value, $register_msg) );
			else
				if(!array_key_exists($this->_txSessionId, $_SESSION))
					$return = array( 'type' => $type, 'value' =>   MoMessages::showMessage('UMPRO_VERIFY') );
				else if(strcasecmp($_SESSION[$this->_formPhoneVer], $value)!=0)
					$return = array( 'type' => $type, 'value' =>   MoMessages::showMessage('PHONE_MISMATCH') );
				else
					$return = array( 'type' => $type, 'value' => ihc_check_value_field($type, $value, $second_value, $register_msg) );				
		}


		
		function processEmail(&$return,$type,$value,$second_value,$register_msg)
		{
			if(strcasecmp($this->_otpType,$this->_typeEmailTag)!=0)
				$return = array( 'type' => $type, 'value' => ihc_check_value_field($type, $value, $second_value, $register_msg) );
			else
				if(!array_key_exists($this->_txSessionId, $_SESSION))
					$return = array( 'type' => $type, 'value' =>   MoMessages::showMessage('UMPRO_VERIFY') );
				else if(strcasecmp($_SESSION[$this->_formEmailVer], $value)!=0)
					$return = array( 'type' => $type, 'value' =>  MoMessages::showMessage('EMAIL_MISMATCH') );
				else
					$return = array( 'type' => $type, 'value' => ihc_check_value_field($type, $value, $second_value, $register_msg) );
		}


		
		function processOTPEntered(&$return,$type,$value,$second_value,$register_msg)
		{
			if(!array_key_exists($this->_txSessionId, $_SESSION))
				$return = array( 'type' => $type, 'value' =>   MoMessages::showMessage('UMPRO_VERIFY') );
			else
				$this->validateAndProcessOTP($return,$type,$value);				
		}


		
		function validateOTPRequest($otpToken)
		{
			do_action('mo_validate_otp',NULL,$otpToken);
		}


		
		function validateAndProcessOTP(&$return,$type,$otpToken)
		{
			$this->validateOTPRequest($otpToken);
			if(strcasecmp($_SESSION[$this->_formSessionVar], 'validated') != 0)
				$return = array( 'type' => $type, 'value' =>  MoUtility::_get_invalid_otp_method() );
			else
			{
				$this->unsetOTPSessionVariables();
				$return = array( 'type' => $type, 'value' => 1 );
			}
		}


		
		function handle_failed_verification($user_login,$user_email,$phone_number)
		{
			MoUtility::checkSession();
			if(!isset($_SESSION[$this->_formSessionVar])) return;
			$_SESSION[$this->_formSessionVar] = 'failed';
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
			MoUtility::checkSession();
			if($this->isFormEnabled() && $this->_otpType==$this->_typePhoneTag) array_push($selector, $this->_phoneFormId); 
			return $selector;
		}


		
		function handleFormOptions()
	    {
			if(!MoUtility::areFormOptionsBeingSaved()) return;
			
			$this->_isFormEnabled = isset( $_POST['mo_customer_validation_ultipro_enable']) ? $_POST['mo_customer_validation_ultipro_enable'] : 0;
			$this->otpType = isset( $_POST['mo_customer_validation_ultipro_type']) ? $_POST['mo_customer_validation_ultipro_type'] : '';
			update_mo_option('mo_customer_validation_ultipro_enable', $this->_isFormEnabled);
			update_mo_option('mo_customer_validation_ultipro_type', $this->_otpType);
	    }
	}