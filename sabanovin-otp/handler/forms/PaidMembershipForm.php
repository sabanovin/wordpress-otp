<?php


class PaidMembershipForm extends FormHandler implements IFormHandler
{
    private static $_instance = null;

    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    protected function __construct()
    {
        $this->_isLoginOrSocialForm = FALSE;
        $this->_isAjaxForm = FALSE;
        $this->_formSessionVar = FormSessionVars::PMPRO_REGISTRATION;
        $this->_formKey = 'PM_PRO_FORM';
        $this->_formName = mo_('Paid MemberShip Pro Registration Form');
        $this->_phoneFormId = 'input[name=phone_paidmembership]';
        $this->_typePhoneTag = "pmpro_phone_enable";
        $this->_typeEmailTag = "pmpro_email_enable";
        $this->_isFormEnabled = get_mo_option('mo_customer_validation_pmpro_enable') ? TRUE : FALSE;
        parent::__construct();
    }


    
    function handleForm()
    {
        $this->_otpType = get_mo_option('mo_customer_validation_pmpro_otp_type');
        add_action( 'wp_enqueue_scripts', array($this,'_show_phone_field_on_page'));
        add_filter( 'pmpro_checkout_before_processing', array($this,'_paidMembershipProRegistrationCheck'), 1, 1 );
        add_filter( 'pmpro_checkout_confirmed', array( $this, 'isValidated' ), 99, 2 );
    }


    
    public function isValidated($pmpro_confirmed, $morder)
    {
     	global $pmpro_msgt;
     	return $pmpro_msgt=="pmpro_error" ? false : $pmpro_confirmed;
    }


    
    public function _paidMembershipProRegistrationCheck()
    {
        global $pmpro_msgt;
        MoUtility::checkSession();
        if(isset($_SESSION[$this->_formSessionVar])
            && strcasecmp($_SESSION[$this->_formSessionVar],'validated')==0){
            $this->unsetOTPSessionVariables();
            return;
        }
        $this->validatePhone($_POST);
        if($pmpro_msgt != "pmpro_error") {
            MoUtility::initialize_transaction($this->_formSessionVar);
            $this->startOTPVerificationProcess($_POST);
        }
    }


    
    private function startOTPVerificationProcess($data)
    {
        if(strcasecmp($this->_otpType, $this->_typePhoneTag)==0){
            miniorange_site_challenge_otp('testuser','',null, trim($data['phone_paidmembership']),"phone");
        } elseif(strcasecmp($this->_otpType, $this->_typeEmailTag)==0){
            miniorange_site_challenge_otp('testuser',$data['bemail'],null,$data['bemail'],"email");
        }
    }


    
    public function validatePhone($data)
    {
        global $pmpro_msg, $pmpro_msgt,$phoneLogic,$pmpro_requirebilling;
        if($pmpro_msgt=='pmpro_error') return;
        $phoneValue= $data['phone_paidmembership'];
        if(!MoUtility::validatePhoneNumber($phoneValue))
        {
            $message = str_replace("##phone##",$phoneValue,$phoneLogic->_get_otp_invalid_format_message());
            $pmpro_msgt = "pmpro_error";
            $pmpro_requirebilling = false;
            $pmpro_msg = apply_filters('pmpro_set_message', $message, $pmpro_msgt);
        }
    }



    
    function _show_phone_field_on_page()
    {
        if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0){
            wp_enqueue_script('paidmembershipscript', MOV_URL . 'includes/js/paidmembershippro.min.js?version='.MOV_VERSION , array('jquery'));
        }
    }


    
    function handle_failed_verification($user_login, $user_email, $phone_number)
    {
        MoUtility::checkSession();
        if (!isset($_SESSION[$this->_formSessionVar])) return;
        $otpVerType = strcasecmp($this->_otpType, $this->_typePhoneTag) == 0 ? "phone"
            : (strcasecmp($this->_otpType, $this->_typeBothTag) == 0 ? "both" : "email");
        $fromBoth = strcasecmp($otpVerType, "both") == 0 ? TRUE : FALSE;
        miniorange_site_otp_validation_form($user_login, $user_email, $phone_number, MoUtility::_get_invalid_otp_method(), $otpVerType, $fromBoth);
    }


    
    function handle_post_verification($redirect_to, $user_login, $user_email, $password, $phone_number, $extra_data)
    {
        MoUtility::checkSession();
        if (!isset($_SESSION[$this->_formSessionVar])) return;
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
        if(self::isFormEnabled() && $this->_otpType==$this->_typePhoneTag) array_push($selector, $this->_phoneFormId);
        return $selector;
    }


    
    function handleFormOptions()
    {
        if (!MoUtility::areFormOptionsBeingSaved()) return;

        $this->_isFormEnabled = isset( $_POST['mo_customer_validation_pmpro_enable']) ? $_POST['mo_customer_validation_pmpro_enable'] : 0;
        $this->_otpType = isset( $_POST['mo_customer_validation_pmpro_contact_type']) ? $_POST['mo_customer_validation_pmpro_contact_type'] : '';

        update_mo_option('mo_customer_validation_pmpro_enable',$this->_isFormEnabled);
        update_mo_option('mo_customer_validation_pmpro_otp_type',$this->_otpType);
    }
}