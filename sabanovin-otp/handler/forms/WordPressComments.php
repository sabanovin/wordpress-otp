<?php
	
	
	class WordPressComments extends FormHandler implements IFormHandler
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
			$this->_formSessionVar = FormSessionVars::WPCOMMENT;
			$this->_formEmailVer = FormSessionVars::WPCOMMENT_EMAIL;
			$this->_formPhoneVer = FormSessionVars::WPCOMMENT_PHONE;
			$this->_phoneFormId = "input[name=phone]";
			$this->_formKey = 'WPCOMMENT';
			$this->_typePhoneTag = "mo_wpcomment_phone_enable";
			$this->_typeEmailTag = "mo_wpcomment_email_enable";
			$this->_formName = mo_("WordPress Comment Form");
			$this->_isFormEnabled = get_mo_option('mo_customer_validation_wpcomment_enable') ? TRUE : FALSE;
			parent::__construct();
		}

		
		function handleForm()
		{
			$this->_otpType = get_mo_option('mo_customer_validation_wpcomment_enable_type');
			$this->_byPassLogin = !MoUtility::isBlank(get_mo_option('mo_customer_validation_wpcomment_enable_for_loggedin_users')) ? FALSE : TRUE;
			
			if(!$this->_byPassLogin) {
				add_action( 'comment_form_logged_in_after', array($this,'_add_scripts_and_additional_fields'),1 );
				add_action( 'comment_form_after_fields', array($this,'_add_scripts_and_additional_fields'),1);
			}else{
				add_filter('comment_form_default_fields', array($this,'_add_custom_fields'),99,1);
			}
			add_filter( 'preprocess_comment', array($this,'verify_comment_meta_data'),1,1);
			add_action( 'comment_post', array($this,'save_comment_meta_data') ,1 ,1);
			add_action( 'add_meta_boxes_comment', array($this,'extend_comment_add_meta_box'),1,1);
			add_action( 'edit_comment', array($this,'extend_comment_edit_metafields'),1,1);

			$this->routeData();
		}


		function routeData()
		{
			if(!array_key_exists('option', $_GET)) return; 

			switch (trim($_GET['option'])) 
			{
				case "mo-comments-verify":
					$this->_startOTPVerificationProcess($_POST);	break; 			
			}
		}


		
		function _startOTPVerificationProcess($getdata)
		{
			MoUtility::checkSession();
			MoUtility::initialize_transaction($this->_formSessionVar);

			if(strcasecmp($this->_otpType, $this->_typeEmailTag)==0 && array_key_exists('user_email', $getdata) 
				&& !MoUtility::isBlank($getdata['user_email']))
			{
				$_SESSION[$this->_formEmailVer] = $getdata['user_email'];
				miniorange_site_challenge_otp('test',$getdata['user_email'],null,$getdata['user_email'],"email");
			}
			else if(strcasecmp($this->_otpType, $this->_typePhoneTag)==0 && array_key_exists('user_phone', $getdata) 
				&& !MoUtility::isBlank($getdata['user_phone']))
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


		
		function extend_comment_edit_metafields( $comment_id ) 
		{
		    if( ! isset( $_POST['extend_comment_update'] ) 
		    	|| ! wp_verify_nonce( $_POST['extend_comment_update'], 'extend_comment_update' ) ) return;

		  	if ( ( isset( $_POST['phone'] ) ) && ( $_POST['phone'] != '') ){
		  		$phone = wp_filter_nohtml_kses($_POST['phone']); 		  		update_comment_meta( $comment_id, 'phone', $phone );
		  	}else{
		  		delete_comment_meta( $comment_id, 'phone');
		  	}
		}


		
		function extend_comment_add_meta_box() 
		{
		    add_meta_box( 'title', mo_( 'Extra Fields'  ), array($this,'extend_comment_meta_box')
		    			, 'comment', 'normal', 'high' );
		}


		
		function extend_comment_meta_box ( $comment ) 
		{
		    $phone = get_comment_meta( $comment->comment_ID, 'phone', true );
		    wp_nonce_field( 'extend_comment_update', 'extend_comment_update', false );
		   
		    echo '<table class="form-table editcomment">
					<tbody>
					<tr>
						<td class="first"><label for="phone">'.mo_( 'Phone'  ).':</label></td>
						<td><input type="text" name="phone" size="30" value="'.esc_attr( $phone ).'" id="phone"></td>
					</tr>
					</tbody>
				</table>';
		}


		
		function verify_comment_meta_data( $commentdata ) 
		{
			if($this->_byPassLogin && is_user_logged_in()) return $commentdata;
			
			MoUtility::checkSession();
		  	if ( ! isset( $_POST['phone'] ) && strcasecmp($this->_otpType,$this->_typePhoneTag)==0)
		  		wp_die( mo_( 'Error: You did not add a phone number. Hit the Back button on your 
							  Web browser and resubmit your comment with a phone number.'  ) );
							  
		  	if ( ! isset( $_POST['verifyotp'] ) )
		  		wp_die( mo_( 'Error: You did not add a Verification Code. Hit the Back button on your 
		  					Web browser and resubmit your comment with a verification code.'  ) );

		  	if(!array_key_exists($this->_formSessionVar,$_SESSION))
		  		wp_die(mo_(MoMessages::showMessage('PLEASE_VALIDATE')) );

		  	if(array_key_exists($this->_formEmailVer, $_SESSION) 
				&& strcasecmp($_SESSION[$this->_formEmailVer], $_POST['email'])!=0)
				 wp_die(mo_(MoMessages::showMessage('EMAIL_MISMATCH')) );

			if(array_key_exists($this->_formPhoneVer, $_SESSION) 
				&& strcasecmp($_SESSION[$this->_formPhoneVer], $_POST['phone'])!=0)
				wp_die(mo_(MoMessages::showMessage('PHONE_MISMATCH')) );

			do_action('mo_validate_otp',NULL,$_POST['verifyotp']);

		  	return $commentdata;
		}


		
		function _add_scripts_and_additional_fields()
		{
			if(strcasecmp($this->_otpType, $this->_typeEmailTag)==0)
				echo '<p class="comment-form-phone">'
							.'<label for="email">' . mo_( 'Email *' ) . '</label>'
				      		.'<input id="email" name="email" type="text" size="30"  tabindex="4" /></p>' 
				      		. $this->get_otp_html_content("email");

			if(strcasecmp($this->_otpType, $this->_typePhoneTag)==0)
		   		echo '<p class="comment-form-email">'
							.'<label for="phone">' . mo_( 'Phone *' ) . '</label>'
				      		.'<input id="phone" name="phone" type="text" size="30"  tabindex="4" /></p>'
				      		. $this->get_otp_html_content("phone");

			echo '<p class="comment-form-phone">'.
					    '<label for="verifyotp">' . mo_( 'Verification Code'  ) . '</label>'.
					    '<input id="verifyotp" name="verifyotp" type="text" size="30"  tabindex="4" /></p>';
		}


		
		function _add_custom_fields($fields)
		{
			$commenter = wp_get_current_commenter();

			if(strcasecmp($this->_otpType, $this->_typeEmailTag)==0)
				$fields[ 'email' ] = '<p class="comment-form-phone">'
										.'<label for="email">' . mo_( 'Email *' ) . '</label>'
				      					.'<input id="email" name="email" type="text" size="30"  tabindex="4" /></p>' 
				      					. $this->get_otp_html_content("email");

			if(strcasecmp($this->_otpType, $this->_typePhoneTag)==0)
		   		$fields['phone'] = '<p class="comment-form-email">'
										.'<label for="phone">' . mo_( 'Phone *' ) . '</label>'
				      					.'<input id="phone" name="phone" type="text" size="30"  tabindex="4" /></p>'
				      					. $this->get_otp_html_content("phone");

			$fields[ 'verifyotp' ] = '<p class="comment-form-phone">'.
					      '<label for="verifyotp">' . mo_( 'Verification Code'  ) . '</label>'.
					      '<input id="verifyotp" name="verifyotp" type="text" size="30"  tabindex="4" /></p>';
			return $fields;
		}


		
		function get_otp_html_content($id)
		{
			$img   = "<div style='display:table;text-align:center;'><img src='".MOV_URL. "includes/images/loader.gif'></div>";

			$html  = '<div style="margin-bottom:3%"><input type="button" class="button alt" style="width:100%" id="miniorange_otp_token_submit"';
			$html .= strcasecmp($this->_otpType, $this->_typePhoneTag)==0 ? 'title="Please Enter a phone number to enable this." '
																		: 'title="Please Enter a email number to enable this." ';
			$html .= strcasecmp($this->_otpType, $this->_typePhoneTag)==0 ? 'value="Click here to verify your Phone">'
																		: 'value="Click here to verify your Email">';
			$html .= '<div id="mo_message" hidden="" style="background-color: #f7f6f7;padding: 1em 2em 1em 3.5em;"></div></div>';

			$html .= '<script>jQuery(document).ready(function(){$mo=jQuery;$mo("#miniorange_otp_token_submit").click(function(o){'; 
			$html .= 'var e=$mo("input[name='.$id.']").val(); $mo("#mo_message").empty(),$mo("#mo_message").append("'.$img.'"),';
			$html .= '$mo("#mo_message").show(),$mo.ajax({url:"'.site_url().'/?option=mo-comments-verify",type:"POST",';
			$html .= 'data:{user_phone:e,user_email:e},crossDomain:!0,dataType:"json",success:function(o){ if(o.result=="success"){';
			$html .= '$mo("#mo_message").empty(),$mo("#mo_message").append(o.message),$mo("#mo_message").css("border-top","3px solid green"),';
			$html .= '$mo("input[name=email_verify]").focus()}else{$mo("#mo_message").empty(),$mo("#mo_message").append(o.message),';
			$html .= '$mo("#mo_message").css("border-top","3px solid red"),$mo("input[name=phone_verify]").focus()} ;},';
			$html .= 'error:function(o,e,n){}})});});</script>';
			return $html;
		}


		
		function save_comment_meta_data( $comment_id ) {
		  	if ( ( isset( $_POST['phone'] ) ) && ( $_POST['phone'] != '') ){
		  		$phone = wp_filter_nohtml_kses($_POST['phone']);
		  		add_comment_meta( $comment_id, 'phone', $phone );
		  	}
		}
		

		
		function handle_failed_verification($user_login,$user_email,$phone_number)
		{
			MoUtility::checkSession();
			if(!isset($_SESSION[$this->_formSessionVar])) return;
			wp_die(MoUtility::_get_invalid_otp_method());
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

			$this->_isFormEnabled = isset( $_POST['mo_customer_validation_wpcomment_enable']) ? $_POST['mo_customer_validation_wpcomment_enable'] : 0;
			$this->_otpType = isset( $_POST['mo_customer_validation_wpcomment_enable_type']) ? $_POST['mo_customer_validation_wpcomment_enable_type'] : '';
			$this->_byPassLogin = isset( $_POST['wpcomment_enable_for_loggedin_users']) ? $_POST['wpcomment_enable_for_loggedin_users'] : '';
			
			update_mo_option('mo_customer_validation_wpcomment_enable', $this->_isFormEnabled);
			update_mo_option('mo_customer_validation_wpcomment_enable_type', $this->_otpType);
			update_mo_option('mo_customer_validation_wpcomment_enable_for_loggedin_users',$this->_byPassLogin);
		}
	}