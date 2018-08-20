<?php

class RealEstate7 extends FormHandler implements IFormHandler
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
        $this->_formSessionVar = FormSessionVars::REALESTATE_7;
        $this->_phoneFormId = "input[name=ct_user_phone_miniorange]";
        $this->_formKey = 'REAL_ESTATE_7';
        $this->_typePhoneTag = "mo_realestate_contact_phone_enable";
        $this->_typeEmailTag = "mo_realestate_contact_email_enable";
        $this->_formName = mo_("Real Estate 7 Pro Theme");
        $this->_isFormEnabled = get_mo_option('mo_customer_validation_realestate_enable') ? TRUE : FALSE;
        parent::__construct();
    }

    

    public function handleForm()
    {
        $this->_otpType= get_mo_option('mo_customer_validation_realestate_otp_type');

        add_action('wp_enqueue_scripts', array($this,'addPhoneFieldScript'));
        add_action('user_register', array($this,'miniorange_registration_save'), 10, 1 );

        if(!array_key_exists('option',$_POST))return;

        switch($_POST['option'])
        {
            case "realestate_register":
                if($this->sanitizeData($_POST))
                    $this->routeData($_POST);           break;

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


    
    public function handle_failed_verification($user_login,$user_email,$phone_number)
    {
        MoUtility::checkSession();
        if(!isset($_SESSION[$this->_formSessionVar])) return;
        $otpVerType = strcasecmp($this->_otpType,$this->_typePhoneTag)==0 ? "phone"
            : (strcasecmp($this->_otpType,$this->_typeEmailTag)==0 ? "email" : "");
        $fromBoth = strcasecmp($otpVerType,"both")==0 ? TRUE : FALSE;
        miniorange_site_otp_validation_form($user_login,$user_email,$phone_number,MoUtility::_get_invalid_otp_method(),$otpVerType,$fromBoth);
    }

    
    public function sanitizeData($postData)
    {
        if (isset( $postData["ct_user_login"] ) && wp_verify_nonce($postData['ct_register_nonce'], 'ct-register-nonce'))
        {
            $user_login		= $postData["ct_user_login"];
            $user_email		= $postData["ct_user_email"];
            $user_first 	= $postData["ct_user_first"];
            $user_last	 	= $postData["ct_user_last"];
            $user_pass		= $postData["ct_user_pass"];
            $pass_confirm 	= $postData["ct_user_pass_confirm"];

            if(username_exists($user_login) || !validate_username($user_login) || $user_login == ''
                || !is_email($user_email) || email_exists($user_email) || $user_pass == ''
                || $user_pass != $pass_confirm) {
                return false;
            }

            return true;
        }
    }

    
    public function miniorange_registration_save($user_id){
        MoUtility::checkSession();

        if(array_key_exists('phone_number_mo',$_SESSION) && strcasecmp($this->_otpType,$this->_typePhoneTag)==0){
            $add_user_phone= add_user_meta($user_id, 'phone', $_SESSION['phone_number_mo']);
        }
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


    
    public function routeData($postData)
    {
        MoUtility::checkSession();
        Moutility::initialize_transaction($this->_formSessionVar);

        if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0){
            $this->_processPhone($postData);
        }

        else if (strcasecmp($this->_otpType, $this->_typeEmailTag)==0){
            $this->_processEmail($postData);
        }
    }


    
    private function _processPhone($postData)
    {
        if(!array_key_exists('ct_user_phone_miniorange', $postData) || !isset($postData['ct_user_phone_miniorange'])) return;
        miniorange_site_challenge_otp('testuser','',null, trim($postData['ct_user_phone_miniorange']),"phone");
    }


    

    private function _processEmail($postData)
    {
        if(!array_key_exists('ct_user_email', $postData) || !isset($postData['ct_user_email'])) return;
        miniorange_site_challenge_otp('testuser', $postData['ct_user_email'], null, null,'email',"");
    }


    
    public function addPhoneFieldScript()
    {
        wp_enqueue_script('realEstate7Script', MOV_URL . 'includes/js/realEstate7.min.js?version='.MOV_VERSION , array('jquery'));
    }


    
    public function getPhoneNumberSelector($selector)
    {
        MoUtility::checkSession();
        if(self::isFormEnabled() && $this->_otpType==$this->_typePhoneTag) array_push($selector, $this->_phoneFormId);
        return $selector;
    }


    
    public function handleFormOptions()
    {
        if(!MoUtility::areFormOptionsBeingSaved()) return;

        update_mo_option('mo_customer_validation_realestate_enable',
            isset( $_POST['mo_customer_validation_realestate_enable']) ? $_POST['mo_customer_validation_realestate_enable'] : 0);

        update_mo_option('mo_customer_validation_realestate_otp_type',
            isset( $_POST['mo_customer_validation_realestate_contact_type']) ? $_POST['mo_customer_validation_realestate_contact_type'] : '');
    }
}
