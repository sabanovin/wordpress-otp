<?php


class MultiSiteFormRegistration extends FormHandler implements IFormHandler
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
        $this->_formSessionVar =  FormSessionVars::MULTISITE;
        $this->_phoneFormId = 'input[name=multisite_user_phone_miniorange]';
        $this->_typePhoneTag = 'mo_multisite_contact_phone_enable';
        $this->_typeEmailTag = 'mo_multisite_contact_email_enable';
        $this->_formKey = 'WP_SIGNUP_FORM';
        $this->_formName = mo_('WordPress Multisite SignUp Form');
        $this->_isFormEnabled = get_mo_option('mo_customer_validation_multisite_enable') ? TRUE : FALSE;
        $this->_phoneKey = 'telephone';
        parent::__construct();
    }

    
    public function handleForm()
    {
        add_action( 'wp_enqueue_scripts', array($this,'addPhoneFieldScript'));
        add_action( 'user_register', array($this,'_savePhoneNumbere'), 10, 1 );
        $this->_otpType= get_mo_option('mo_customer_validation_multisite_otp_type');

        if(!array_key_exists('option',$_POST))  return;

        switch(trim($_POST['option']))
        {
            case 'multisite_register':
                $this->_sanitizeAndRouteData($_POST);   break;
            case 'miniorange-validate-otp-form':
                $this->_startValidation();              break;
        }
    }


    
    public function unsetOTPSessionVariables()
    {
        unset($_SESSION[$this->_txSessionId]);
        unset($_SESSION[$this->_formSessionVar]);
    }


    
    public function handle_post_verification($redirect_to,$user_login,$user_email,$password,$phone_number,$extra_data)
    {
        MoUtility::checkSession();
        if(!isset($_SESSION[$this->_formSessionVar])) return;
        $_SESSION[$this->_formSessionVar]= 'validated';
        $this->unsetOTPSessionVariables();
    }


    
    public function _savePhoneNumber($user_id)
    {
        if ( isset( $_SESSION['phone_number_mo'] ) ) add_user_meta($user_id, $this->_phoneKey, $_SESSION['phone_number_mo']);
    }

    
    public function handle_failed_verification($user_login,$user_email,$phone_number)
    {
        MoUtility::checkSession();
        if(!isset($_SESSION[$this->_formSessionVar])) return;
        $otpVerType = strcasecmp($this->_otpType,$this->_typePhoneTag)==0 ? "phone"
            : (strcasecmp($this->_otpType,$this->_typeEmailTag)==0 ? "email" : "");
        $fromBoth = strcasecmp($otpVerType,"both")==0 ? TRUE : FALSE;
        miniorange_site_otp_validation_form($user_login,$user_email,$phone_number,MoUtility::_get_invalid_otp_method(),$otpVerType,$fromBoth);
    }


    function _sanitizeAndRouteData($getdata)
    {
        $result= wpmu_validate_user_signup($_POST['user_name'], $_POST['user_email']);
        $errors = $result['errors'];
        if ($errors->get_error_code()) return false;
        MoUtility::checkSession();
        Moutility::initialize_transaction($this->_formSessionVar);

        if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0)
            $this->_processPhone($getdata);
        else if (strcasecmp($this->_otpType, $this->_typeEmailTag)==0)
            $this->_processEmail($getdata);
    }

    private function _startValidation()
    {
        MoUtility::checkSession();
        if(!isset($_SESSION[$this->_formSessionVar])) return;
        if(isset($_SESSION[$this->_formSessionVar])||
            strcasecmp($_SESSION[$this->_formSessionVar],'validated')==0){
            return;
        }
        do_action('mo_validate_otp');
    }

    
    public function addPhoneFieldScript()
    {
        wp_enqueue_script('multisitescript', MOV_URL . 'includes/js/multisite.min.js?version='.MOV_VERSION , array('jquery'));
    }

    
    private function _processPhone($getdata)
    {
        if(!isset($getdata['multisite_user_phone_miniorange'])) return;
        miniorange_site_challenge_otp('testuser','',null, trim($getdata['multisite_user_phone_miniorange']),"phone");
    }

    
    private function _processEmail($getdata)
    {
        if(!isset($getdata['user_email'])) return;
        miniorange_site_challenge_otp('testuser', $getdata['user_email'], null, null,'email',"");
    }


    
    public function getPhoneNumberSelector($selector)
    {
        MoUtility::checkSession();
        if(self::isFormEnabled()) array_push($selector, $this->_phoneFormId);
        return $selector;
    }

    
    public function handleFormOptions()
    {
        if (!MoUtility::areFormOptionsBeingSaved()) return;

        $this->_isFormEnabled = isset($_POST['mo_customer_validation_multisite_enable']) ? $_POST['mo_customer_validation_multisite_enable'] : 0;
        $this->_otpType = isset($_POST['mo_customer_validation_multisite_contact_type']) ? $_POST['mo_customer_validation_multisite_contact_type'] : '';

        update_mo_option('mo_customer_validation_multisite_enable',$this->_isFormEnabled);
        update_mo_option('mo_customer_validation_multisite_otp_type',$this->_otpType);
    }
}