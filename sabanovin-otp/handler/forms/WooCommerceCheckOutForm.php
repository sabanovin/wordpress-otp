<?php
	
	
	class WooCommerceCheckOutForm extends FormHandler implements IFormHandler
	{
		private $_guestCheckOutOnly;
		private $_showButton;
		private $_popupEnabled;
		private $_paymentMethods;
		private $_selectivePayment;
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
			$this->_formSessionVar = FormSessionVars::WC_CHECKOUT;
			$this->_typePhoneTag = 'mo_wc_phone_enable';
			$this->_typeEmailTag = 'mo_wc_email_enable';
			$this->_phoneFormId = 'input[name=billing_phone]';
			$this->_formKey = 'WC_CHECKOUT_FORM';
			$this->_formName = mo_("Woocommerce Checkout Form");
			$this->_isFormEnabled = get_mo_option('mo_customer_validation_wc_checkout_enable') ? TRUE : FALSE;
			$this->_buttonText = get_mo_option('mo_customer_validation_wc_checkout_button_link_text');
			$this->_buttonText = !MoUtility::isBlank($this->_buttonText) ? $this->_buttonText 
									: (!$this->_popupEnabled ? mo_("Verify Your Purchase") : mo_("Place Order"));
			parent::__construct();
		}

		
		function handleForm()
		{
			if(!function_exists('is_plugin_active')) include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			if( !is_plugin_active( 'woocommerce/woocommerce.php' ) ) return;
			add_action( 'woocommerce_checkout_process', array($this,'my_custom_checkout_field_process'));
			$this->_paymentMethods = maybe_unserialize(get_mo_option('mo_customer_validation_wc_checkout_payment_type'));
			$this->_paymentMethods = $this->_paymentMethods ? $this->_paymentMethods : WC()->payment_gateways->payment_gateways();
			$this->_popupEnabled = !MoUtility::isBlank(get_mo_option('mo_customer_validation_wc_checkout_popup')) ? TRUE : FALSE;
			$this->_guestCheckOutOnly= !MoUtility::isBlank(get_mo_option('mo_customer_validation_wc_checkout_guest')) ? TRUE : FALSE;
			$this->_showButton = !MoUtility::isBlank(get_mo_option('mo_customer_validation_wc_checkout_button'))? TRUE : FALSE;
			$this->_otpType = get_mo_option('mo_customer_validation_wc_checkout_type');
			$this->_selectivePayment = get_mo_option('mo_customer_validation_wc_checkout_selective_payment') ? TRUE : FALSE;

			if($this->_popupEnabled)  add_action( 'woocommerce_after_checkout_billing_form' , array($this,'add_custom_popup') 		,99		);
			if($this->_popupEnabled)  add_action( 'woocommerce_review_order_after_submit'   , array($this,'add_custom_button')		, 1, 1	);
			if(!$this->_popupEnabled) add_action( 'woocommerce_after_checkout_billing_form' , array($this,'my_custom_checkout_field'), 99	);
			
			add_action( 'wp_enqueue_scripts', array($this,'enqueue_script_on_page'));
			$this->routeData();
		}


		
		function routeData()
		{
			if(!array_key_exists('option', $_GET)) return;
			if(strcasecmp(trim($_GET['option']),'miniorange-woocommerce-checkout') == 0) $this->handle_woocommere_checkout_form($_POST);
		}


		
		function handle_woocommere_checkout_form($getdata)
		{
			MoUtility::checkSession();
			MoUtility::initialize_transaction($this->_formSessionVar);
			if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0)
				miniorange_site_challenge_otp('test',$getdata['user_email'],null, trim($getdata['user_phone']),"phone");
			else
				miniorange_site_challenge_otp('test',$getdata['user_email'],null,null,"email");
		}


		
		function checkIfVerificationNotStarted()
		{
			MoUtility::checkSession();
			if(!isset($_SESSION[$this->_formSessionVar])){
				wc_add_notice(  MoMessages::showMessage('ENTER_VERIFY_CODE'), 'error' );
				return TRUE;
			}
			return FALSE;
		}


		
		function checkIfVerificationCodeNotEntered()
		{
			if(array_key_exists('order_verify', $_POST) && !MoUtility::isBlank($_POST['order_verify'])) return FALSE;

			if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0)
				wc_add_notice(  MoMessages::showMessage('ENTER_PHONE_CODE'), 'error' );
			else
				wc_add_notice(  MoMessages::showMessage('ENTER_EMAIL_CODE'), 'error' );
			return TRUE;
		}


		
		function add_custom_button($order_id)
		{
			if($this->_guestCheckOutOnly && is_user_logged_in())  return;
			$this->show_validation_button_or_text(TRUE);
			$this->common_button_or_link_enable_disable_script();
			echo ',$mo("#miniorange_otp_token_submit").click(function(o){
			            var requiredFields = areAllMandotryFieldsFilled(),
			            e=$mo("input[name=billing_email]").val(),
			            m=$mo("#billing_phone").val(),
			            a=$mo("div.woocommerce");
			            if(requiredFields=="")
			            {
			                a.addClass("processing").block({message:null,overlayCSS:{background:"#fff",opacity:.6}});
			                $mo.ajax({url:"'.site_url().'/?option=miniorange-woocommerce-checkout",type:"POST",
			                      data:{user_email:e,user_phone:m},crossDomain:!0,dataType:"json",
			                      success:function(o){
			                        "success"==o.result?($mo(".blockUI").hide(),$mo("#mo_message").empty(),
			                         $mo("#mo_message").append(o.message),$mo("#mo_message").addClass("woocommerce-message"),
			                         $mo("#myModal").show(),$mo("#mo_validate_field").show(),
			                         $mo("#mo_customer_validation_otp_token").focus()):($mo(".blockUI").hide(),$mo("#mo_message").empty(),
			                         $mo("#mo_message").append(o.message),$mo("#mo_message").addClass("woocommerce-error"),
			                         $mo("#mo_validate_field").hide(),$mo("#myModal").show())},
			                      error:function(o,e,m){}});
			            }else{
			                $mo(".woocommerce-NoticeGroup-checkout").empty();
			                $mo("form.woocommerce-checkout").prepend(requiredFields);
			                $mo("html, body").animate({scrollTop: $mo(".woocommerce-error").offset().top}, 2000);
			            }
			            o.preventDefault()});
			            $mo("#miniorange_otp_validate_submit").click(function(o){$mo("#myModal").hide(),$mo(\'form[name="checkout"]\').submit()}),
			            $mo(".close").click(function(){$mo(".modal").hide();});});';
			echo 'function areAllMandotryFieldsFilled(){
                    var err = \'<div class="woocommerce-NoticeGroup woocommerce-NoticeGroup-checkout"><ul class="woocommerce-error" role="alert">{{errors}}</ul></div>\';
                    var errors = "";
                    $mo(".validate-required").each(function(){
                        var id = $mo(this).attr("id");
                        var n = id.replace("_field","");
                        if(($mo("#"+n).val()=="" || $mo("#"+n).val()=="select")&& 
                            (n!="account_password" || (n=="account_password" && $mo("#createaccount:checked").length > 0))) {
                            $mo("#"+n).addClass("woocommerce-invalid woocommerce-invalid-required-field");
                            errors  += "<li><strong>Billing "+$mo("#"+n).prev("label").text().replace("*","")+"</strong> is a required field.</li>";
                        }
                    });
                    return errors != "" ? err.replace("{{errors}}",errors) : 0;
                }</script>';
		}


		
		function add_custom_popup()
		{
			if($this->_guestCheckOutOnly && is_user_logged_in())  return;
			echo '<style>.modal{display:none;position:fixed;z-index:1;padding-top:100px;left:0;top:0;width:100%;height:100%;overflow:auto;background-color:rgb(0,0,0);background-color:rgba(0,0,0,0.4);}.modal-content{position:relative;background-color:#fefefe;margin:auto;padding:0;border:1px solid #888;width:40%;box-shadow:04px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);-webkit-animation-name:animatetop;-webkit-animation-duration:0.4s;animation-name:animatetop;animation-duration:0.4s}@-webkit-keyframes animatetop{from{top:-300px;opacity:0}to{top:0;opacity:1}}@keyframes animatetop{from{top:-300px;opacity:0}to{top:0;opacity:1}}.close{color:white;font-weight:bold;}.close:hover,.close:focus{color:#000;text-decoration:none;cursor:pointer;}.modal-header{background-color:#5cb85c;color:white;}.modal-footer{background-color:#5cb85c;color:white;</style>';
			echo '<script>
                    var e = \'<div id="myModal" class="modal"><div class="modal-content"><div class="modal-header"> <i><span style="margin-left:90%;" class="close" id="close"> close </span></i> </div><div class="modal-body"><div id="mo_message">EMPTY</div><div id="mo_validate_field" style="margin:1em"><input type="number" name="order_verify" autofocus="true" placeholder="" id="mo_customer_validation_otp_token" required="true" style="color: #000;font-family: Helvetica,sans-serif;padding: 7px;text-shadow: 1px 1px 0 #fff;width: 100%;border-radius: 2px;" class="mo_customer_validation-textbox" autofocus="true" pattern="[0-9]{4,8}" title="'.mo_("Only digits within range 4-8 are allowed.").'"/><input type="button" name="miniorange_otp_validate_submit"  style="margin-top:1%;width:100%" id="miniorange_otp_validate_submit" class="miniorange_otp_token_submit"  value="'.mo_("Validate OTP").'" /></div></div></div></div>\';
                    jQuery(\'form[name="checkout"]\').append(e);
                 </script>';
					}


		
		function my_custom_checkout_field( $checkout )
		{
			if($this->_guestCheckOutOnly && is_user_logged_in())  return;

			$this->show_validation_button_or_text();

			echo '<div id="mo_message" hidden></div>';

			woocommerce_form_field( 'order_verify', array(
	        'type'          => 'text',
	        'class'         => array('form-row-wide'),
	        'label'         => mo_('Verify Code'),
	        'required'  	=> true,
	        'placeholder'   => mo_('Enter Verification Code'),
	        ), $checkout->get_value( 'order_verify' ));

	        $this->common_button_or_link_enable_disable_script();
			
			echo ',$mo(".woocommerce-error").length>0&&$mo("html, body").animate({scrollTop:$mo("div.woocommerce").offset().top-50},1e3),$mo("#miniorange_otp_token_submit").click(function(o){var e=$mo("input[name=billing_email]").val(),n=$mo("#billing_phone").val(),a=$mo("div.woocommerce");a.addClass("processing").block({message:null,overlayCSS:{background:"#fff",opacity:.6}}),$mo.ajax({url:"'.site_url().'/?option=miniorange-woocommerce-checkout",type:"POST",data:{user_email:e, user_phone:n},crossDomain:!0,dataType:"json",success:function(o){ if(o.result=="success"){$mo(".blockUI").hide(),$mo("#mo_message").empty(),$mo("#mo_message").append(o.message),$mo("#mo_message").addClass("woocommerce-message"),$mo("#mo_message").show(),$mo("#order_verify").focus()}else{$mo(".blockUI").hide(),$mo("#mo_message").empty(),$mo("#mo_message").append(o.message),$mo("#mo_message").addClass("woocommerce-error"),$mo("#mo_message").show();} ;},error:function(o,e,n){}}),o.preventDefault()});});</script>';
		}


		
		function show_validation_button_or_text($popup=FALSE)
		{
			if(!$this->_showButton) $this->showTextLinkOnPage();
			if($this->_showButton) $this->_showButtonOnPage();
		}


		
		function showTextLinkOnPage()
		{
			if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0)
				echo '<div title="'.mo_("Please Enter a Phone Number to enable this link").'"><a href="#" style="text-align:center;color:grey;pointer-events:initial;display:none;" id="miniorange_otp_token_submit" class="" >'.$this->_buttonText.'</a></div>';
			else
				echo '<div title="'.mo_("Please Enter an Email Address to enable this link").'"><a href="#" style="text-align:center;color:grey;pointer-events:initial;display:none;" id="miniorange_otp_token_submit" class="" >'.$this->_buttonText.'</a></div>';
		}


		
		function _showButtonOnPage()
		{
			if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0)
				echo '<input type="button" class="button alt" style="'
					. ( $this->_popupEnabled ? 'float: right;line-height: 1;margin-right: 2em;padding: 1em 2em; display:none;' : 'display:none;width:100%' )
					.'" id="miniorange_otp_token_submit" title="'
					.mo_("Please Enter a Phone Number to enable this.").'" value="';
			else
				echo '<input type="button" class="button alt" style="'
					. ( $this->_popupEnabled ? 'float: right;line-height: 1;margin-right: 2em;padding: 1em 2em; display:none;' : 'display:none;width:100%' )
					.'" id="miniorange_otp_token_submit" title="'
					.mo_("Please Enter an Email Address to enable this.").'" value="';
			echo $this->_buttonText.'"></input>';
		}


		
		function common_button_or_link_enable_disable_script()
		{
			echo '<script>jQuery(document).ready(function() { $mo = jQuery,';
	        echo '$mo(".woocommerce-message").length>0&&($mo("#order_verify").focus(),$mo("#mo_message").addClass("woocommerce-message"),$mo("#mo_message").show())';
		}


		
		function my_custom_checkout_field_process()
		{
			if($this->_guestCheckOutOnly && is_user_logged_in()) return; 
			if(!$this->isPaymentVerificationNeeded()) return;
			if($this->checkIfVerificationNotStarted()) return;
			if($this->checkIfVerificationCodeNotEntered()) return;
			$this->handle_otp_token_submitted(FALSE);		
		}


		
		function handle_otp_token_submitted($error)
		{
			$error = FALSE;
			if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0)
				$error = $this->processPhoneNumber();
			else
				$error = $this->processEmail();
			if(!$error) $this->validateOTPRequest();
		}


		
		function isPaymentVerificationNeeded()
		{
			$payment_method = $_POST['payment_method'];
			return $this->_selectivePayment ? array_key_exists($payment_method,$this->_paymentMethods) : TRUE;
		}


		
		function processPhoneNumber()
		{
			MoUtility::checkSession();
			if(array_key_exists('phone_number_mo', $_SESSION) 
					&& strcasecmp($_SESSION['phone_number_mo'], MoUtility::processPhoneNumber($_POST['billing_phone']))!=0)
			{
				wc_add_notice(  MoMessages::showMessage('PHONE_MISMATCH'), 'error' );
				return TRUE;
			}
		}


		
		function processEmail()
		{
			if(array_key_exists('user_email', $_SESSION) 
					&& strcasecmp($_SESSION['user_email'], $_POST['billing_email'])!=0)
			{
				wc_add_notice(  MoMessages::showMessage('EMAIL_MISMATCH'), 'error' );
				return TRUE;
			}
		}


		
		function handle_failed_verification($user_login,$user_email,$phone_number)
		{
			MoUtility::checkSession();
			if(!isset($_SESSION[$this->_formSessionVar])) return;
			wc_add_notice( MoUtility::_get_invalid_otp_method(), 'error' );
		}


	    
		function handle_post_verification($redirect_to,$user_login,$user_email,$password,$phone_number,$extra_data)
		{
			MoUtility::checkSession();
			if(!isset($_SESSION[$this->_formSessionVar])) return;
			$this->unsetOTPSessionVariables();
		}


		
		function enqueue_script_on_page()
		{
			wp_register_script( 'wccheckout', MOV_URL . 'includes/js/wccheckout.min.js?version='.MOV_VERSION , array('jquery') ,MOV_VERSION,true);
			wp_localize_script( 'wccheckout', 'mowccheckout', array(
				'paymentMethods' => $this->_paymentMethods,
				'selectivePaymentEnabled' => $this->_selectivePayment,
				'popupEnabled' => $this->_popupEnabled,
				'isLoggedIn' => $this->_guestCheckOutOnly && is_user_logged_in(),
			));
			wp_enqueue_script('wccheckout');
		}

		
		function validateOTPRequest()
		{
			do_action('mo_validate_otp','order_verify',NULL);
		}


		
		public function unsetOTPSessionVariables()
		{
			unset($_SESSION[$this->_txSessionId]);
			unset($_SESSION[$this->_formSessionVar]);
		}

		
		public function getPhoneNumberSelector($selector)	
		{
			MoUtility::checkSession();
			if($this->isFormEnabled() && ($this->_otpType == $this->_typePhoneTag)) array_push($selector, $this->_phoneFormId); 
			return $selector;
		}


		
		function handleFormOptions()
		{
			if(!MoUtility::areFormOptionsBeingSaved()) return;
			if(!function_exists('is_plugin_active')) include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			if( !is_plugin_active( 'woocommerce/woocommerce.php' ) ) return;
			$_paymentMethods = array();
			if(array_key_exists('wc_payment',$_POST)){
				foreach ($_POST['wc_payment'] as $selected) {
					$_paymentMethods[$selected] = $selected;
				}
			}

			$this->_isFormEnabled = isset( $_POST['mo_customer_validation_wc_checkout_enable']) ? $_POST['mo_customer_validation_wc_checkout_enable'] : 0;
			$this->_otpType = isset(  $_POST['mo_customer_validation_wc_checkout_type']) ? $_POST['mo_customer_validation_wc_checkout_type'] : '';
			$this->_guestCheckOutOnly = isset(  $_POST['mo_customer_validation_wc_checkout_guest']) ? $_POST['mo_customer_validation_wc_checkout_guest'] : '';
			$this->_showButton = isset(  $_POST['mo_customer_validation_wc_checkout_button']) ? $_POST['mo_customer_validation_wc_checkout_button'] : '';
			$this->_popupEnabled = isset(  $_POST['mo_customer_validation_wc_checkout_popup']) ? $_POST['mo_customer_validation_wc_checkout_popup'] : '';
			$this->_selectivePayment = isset(  $_POST['mo_customer_validation_wc_checkout_selective_payment']) ? $_POST['mo_customer_validation_wc_checkout_selective_payment'] : '';
			$this->_buttonText = isset($_POST['mo_customer_validation_wc_checkout_button_link_text']) ? $_POST['mo_customer_validation_wc_checkout_button_link_text'] : '';
			$this->_paymentMethods = maybe_serialize($_paymentMethods);


			update_mo_option('mo_customer_validation_wc_checkout_enable', $this->_isFormEnabled);
			update_mo_option('mo_customer_validation_wc_checkout_type', $this->_otpType);
			update_mo_option('mo_customer_validation_wc_checkout_guest', $this->_guestCheckOutOnly);
			update_mo_option('mo_customer_validation_wc_checkout_button',$this->_showButton);
			update_mo_option('mo_customer_validation_wc_checkout_popup',$this->_popupEnabled);
			update_mo_option('mo_customer_validation_wc_checkout_selective_payment',$this->_selectivePayment);
			update_mo_option('mo_customer_validation_wc_checkout_button_link_text', $this->_buttonText);
			update_mo_option('mo_customer_validation_wc_checkout_payment_type',$this->_paymentMethods);
		}

		
		
		

		
		public function isGuestCheckoutOnlyEnabled(){ return $this->_guestCheckOutOnly; }
		
		public function showButtonInstead(){ return $this->_showButton; }
		
		public function isPopUpEnabled(){ return $this->_popupEnabled; }
		
		public function getPaymentMethods(){ return maybe_unserialize($this->_paymentMethods); }
		
		public function isSelectivePaymentEnabled(){ return $this->_selectivePayment; }
	}