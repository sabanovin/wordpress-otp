<?php


class MemberPressRegistrationForm extends FormHandler implements IFormHandler
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
        $this->_formSessionVar = FormSessionVars::MEMBERPRESS_REG;
        $this->_typePhoneTag = 'mo_mrp_phone_enable';
        $this->_typeEmailTag = 'mo_mrp_email_enable';
        $this->_typeBothTag = 'mo_mrp_both_enable';
        $this->_phoneFormId = 'input[name='.$this->_phoneKey.']';
        $this->_formName = mo_("MemberPress Registration Form");
        $this->_formKey = 'MEMBERPRESS';
        $this->_isFormEnabled = get_mo_option('mo_customer_validation_mrp_default_enable') ? TRUE : FALSE;
        parent::__construct();
    }

    
    function handleForm()
    {

        $this->_phoneKey = get_mo_option('mo_customer_validation_mrp_phone_key');
        $this->_otpType = get_mo_option('mo_customer_validation_mrp_enable_type');
        add_filter('mepr-validate-signup', array($this,'miniorange_site_register_form'),99,1);
    }


    
    function miniorange_site_register_form($errors)
    {
        $usermeta = $_POST;
        $errors=$this->validatePhoneNumberField($errors);
        if(is_array($errors) && !empty($errors)) return $errors;
        MoUtility::checkSession();
        if($this->checkIfVerificationIsComplete()) return $errors;
        MoUtility::initialize_transaction($this->_formSessionVar);
        $errors = new WP_Error();
        $phone_number = $_POST[$this->_phoneKey];

        foreach ($_POST as $key => $value)
        {
            if($key=="user_first_name")
                $username = $value;
            elseif ($key=="user_email")
                $email = $value;
            elseif ($key=="mepr_user_password")
                $password = $value;
            else
                $extra_data[$key]=$value;
        }

        $extra_data['usermeta'] = $usermeta;
        $this->startVerificationProcess($username,$email,$errors,$phone_number,$password,$extra_data);
    }


    
    function validatePhoneNumberField($errors){
	    global $phoneLogic;
    	if(MoUtility::isBlank($_POST[$this->_phoneKey])) {
		    $errors[] = __( 'Phone number field can not be blank', 'memberpress' );
	    }
	    else if(!MoUtility::validatePhoneNumber($_POST[$this->_phoneKey])){
		    $errors[] = $phoneLogic->_get_otp_invalid_format_message();
	    }
	    return $errors;
    }


    
    function startVerificationProcess($username,$email,$errors,$phone_number,$password,$extra_data)
    {
        if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0)
            miniorange_site_challenge_otp($username,$email,$errors,$phone_number,'phone',$password,$extra_data);
        else
            miniorange_site_challenge_otp($username,$email,$errors,$phone_number,'email',$password,$extra_data);

    }

    
    function checkIfVerificationIsComplete()
    {
        if(isset($_SESSION[$this->_formSessionVar]) && $_SESSION[$this->_formSessionVar]=='success')
        {
            $this->unsetOTPSessionVariables();
            return TRUE;
        }
        return FALSE;
    }


    
    function moMRPgetphoneFieldId()
    {
        global $wpdb;
        return $wpdb->get_var("SELECT id FROM {$wpdb->prefix}bp_xprofile_fields where name ='".$this->_phoneKey."'");
    }

    
    function handle_post_verification($redirect_to,$user_login,$user_email,$password,$phone_number,$extra_data)
    {
        MoUtility::checkSession();
        if(!isset($_SESSION[$this->_formSessionVar])) return;
        $_SESSION[$this->_formSessionVar] = 'success';
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


    
    public function getPhoneNumberSelector($selector)
    {
        MoUtility::checkSession();
        if(self::isFormEnabled() && $this->isPhoneVerificationEnabled()) array_push($selector, $this->_phoneFormId);
        return $selector;
    }

    
    function isPhoneVerificationEnabled()
    {
        return (strcasecmp($this->_otpType,$this->_typePhoneTag)==0 || strcasecmp($this->_otpType,$this->_typeBothTag)==0);
    }


    
    public function unsetOTPSessionVariables()
    {
        unset($_SESSION[$this->_txSessionId]);
        unset($_SESSION[$this->_formSessionVar]);
    }


    
    function handleFormOptions()
    {
        if(!MoUtility::areFormOptionsBeingSaved()) return;

        $this->_isFormEnabled = isset( $_POST['mo_customer_validation_mrp_default_enable']) ? $_POST['mo_customer_validation_mrp_default_enable'] : 0;
        $this->_otpType = isset( $_POST['mo_customer_validation_mrp_enable_type']) ? $_POST['mo_customer_validation_mrp_enable_type'] :  0;
        $this->_phoneKey = isset( $_POST['mrp_phone_field_key']) ? $_POST['mrp_phone_field_key'] : '';

        update_mo_option('mo_customer_validation_mrp_default_enable', $this->_isFormEnabled);
        update_mo_option('mo_customer_validation_mrp_enable_type', $this->_otpType);
        update_mo_option('mo_customer_validation_mrp_phone_key',$this->_phoneKey);
    }
}