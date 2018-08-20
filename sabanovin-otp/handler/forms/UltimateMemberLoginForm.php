<?php


class UltimateMemberLoginForm extends FormHandler implements IFormHandler
{
    private $_formSessionVar2;
    private $_savePhoneNumbers;
    private $_byPassAdmin;
    private $_allowLoginThroughPhone;
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
        $this->_isLoginOrSocialForm = TRUE;
        $this->_formSessionVar = FormSessionVars::UM_LOGIN_AJAX;
        $this->_formSessionVar2 = FormSessionVars::UM_LOGIN;
        $this->_phoneFormId = '#mo_um_phone_number';
        $this->_phoneKey = "mobile_number";
        $this->_typePhoneTag = 'mo_um_login_phone_enable';
        $this->_typeEmailTag = 'mo_um_login_email_enable';
        $this->_formKey = 'UM_LOGIN_FORM';
        $this->_formName = mo_("Ultimate Member Login Form");
        $this->_isFormEnabled = get_mo_option('mo_customer_validation_um_login_enable') ? TRUE : FALSE;
        parent::__construct();
    }

    
    function handleForm()
    {
        MoUtility::checkSession();
        $this->_isAjaxForm = isset($_SESSION[$this->_formSessionVar]) ? TRUE : FALSE;
        $this->_otpType = get_mo_option('mo_customer_validation_um_login_enable_type');
        $this->_savePhoneNumbers = get_mo_option('mo_customer_validation_um_login_register_phone') ? true : false;
        $this->_byPassAdmin = get_mo_option('mo_customer_validation_um_login_bypass_admin') ? true : false;
        $this->_allowLoginThroughPhone = get_mo_option('mo_customer_validation_um_login_allow_phone_login') ? true : false;
        $this->_restrictDuplicates = get_mo_option('mo_customer_validation_um_login_restrict_duplicates') ? true : false;
        add_filter('authenticate',array($this,'_handle_mo_um_login'),99,3);
        add_filter('wp_authenticate_user',array($this,'_get_and_return_user'),99,2);
        $this->routeData();
    }


    function routeData()
    {
        if(!array_key_exists('option', $_REQUEST)) return;
        switch (trim($_REQUEST['option']))
        {
            case "miniorange-ajax-otp-generate":
                $this->_handle_wp_login_ajax_send_otp($_POST);				break;
            case "miniorange-ajax-otp-validate":
                $this->_handle_wp_login_ajax_form_validate_action($_POST);	break;
        }
    }


    
    function byPassLogin($user_role)
    {
        return in_array('administrator',$user_role) && $this->byPassCheckForAdmins() ? true : false;
    }


    
    function _handle_mo_um_login($user,$username,$password)
    {
        MoUtility::checkSession();
        $user = $this->getUser($username,$password);
        if(is_wp_error($user)) return $user;
        if($this->_isValidatedUser($user)) return $user;
        $user_meta 	= get_userdata($user->data->ID);
        $user_role 	= $user_meta->roles;
        if($this->byPassLogin($user_role)) return $user;
        $this->startOTPVerificationProcess($user,$username,$password);
    }


    
    function _get_and_return_user($username,$password)
    {
        if(is_object($username)) return $username;
        $user = $this->getUser($username,$password);
        if(is_wp_error($user)) return $user;
        UM()->login()->auth_id = $user->data->ID;
        UM()->form()->errors = null;
        return $user;
    }


    
    function _isValidatedUser($user)
    {
        if((isset($_SESSION[$this->_formSessionVar]) && $_SESSION[$this->_formSessionVar]=='validated')
            || (isset($_SESSION[$this->_formSessionVar2]) && $_SESSION[$this->_formSessionVar2]=='validated'))
        {
            $this->unsetOTPSessionVariables();
            if(isset($_SESSION[$this->_formSessionVar]) && $_SESSION[$this->_formSessionVar]=='validated')
            {
                update_user_meta($user->data->ID, $this->_phoneKey ,$_POST['mo_phone_number']);
            }
            return TRUE;
        }
        return FALSE;
    }


    
    function startOTPVerificationProcess($user,$username,$password)
    {
        if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0)
        {
            $phone_number = get_user_meta($user->data->ID, $this->_phoneKey);
            if(!empty($phone_number)) $phone_number = MoUtility::processPhoneNumber($phone_number[0]);
            $this->askPhoneAndStartVerification($user,$this->_phoneKey,$username,$phone_number);
            $this->fetchPhoneAndStartVerification($user, $this->_phoneKey, $username, $password, $phone_number);
        }
        else
        {
            $email= $user->data->user_email;
            $this->startEmailVerification($user,$this->_phoneKey,$username,$password,$email);
        }
    }


    
    function getUser($username,$password)
    {
        $user = is_email( $username ) ? get_user_by("email",$username) : get_user_by("login",$username);
        if($this->allowLoginThroughPhone() && MoUtility::validatePhoneNumber($username) && !$user) {
            $user = $this->getUserFromPhoneNumber($username);
        }
        return $user ? wp_authenticate_username_password(NULL,$user->data->user_login,$password)
            : new WP_Error( 'INVALID_USERNAME' , mo_(" <b>ERROR:</b> Invalid UserName. ") );
    }


    
    function getUserFromPhoneNumber($username)
    {
        global $wpdb;
        $results = $wpdb->get_row("SELECT `user_id` FROM `{$wpdb->prefix}usermeta` WHERE `meta_key` = '$this->_phoneKey' AND `meta_value` =  '$username'");
        return !MoUtility::isBlank($results) ? get_userdata($results->user_id) : false;
    }


    
    function askPhoneAndStartVerification($user,$key,$username,$phone_number)
    {
        if(!MoUtility::isBlank($phone_number)) return;
        if($this->savePhoneNumbers())
        {
            MoUtility::initialize_transaction($this->_formSessionVar);
            miniorange_site_challenge_otp(NULL, $user->data->user_login, NULL, NULL, 'external', NULL,
                array('data' => array('user_login' => $username), 'message' => MoMessages::showMessage('REGISTER_PHONE_LOGIN'),
                    'form' => $key, 'curl' => MoUtility::currentPageUrl()));
        }
    }


    
    function fetchPhoneAndStartVerification($user,$key,$username,$password,$phone_number)
    {
        if( isset($_SESSION[$this->_formSessionVar]) && strcasecmp($_SESSION[$this->_formSessionVar],'validated')==0
            || (isset($_SESSION[$this->_formSessionVar2]) && strcasecmp($_SESSION[$this->_formSessionVar2],'validated')==0)) return;
        MoUtility::initialize_transaction($this->_formSessionVar2);
        miniorange_site_challenge_otp($username,null,null,$phone_number,"phone",$password,false);
    }


    
    function startEmailVerification($user,$key,$username,$password,$email)
    {
        if((array_key_exists($this->_formSessionVar,$_SESSION) && strcasecmp($_SESSION[$this->_formSessionVar],'validated')==0)
            || (array_key_exists($this->_formSessionVar2,$_SESSION) && strcasecmp($_SESSION[$this->_formSessionVar2],'validated')==0)) return;
        MoUtility::initialize_transaction($this->_formSessionVar2);
        miniorange_site_challenge_otp($username,$email,null,null,"email");
    }


    
    function _handle_wp_login_ajax_send_otp($data)
    {
        MoUtility::checkSession();
        if($this->restrictDuplicates() && !MoUtility::isBlank($this->getUserFromPhoneNumber($data['user_phone'])))
            wp_send_json(MoUtility::_create_json_response(MoMessages::showMessage('PHONE_EXISTS'),MoConstants::ERROR_JSON_TYPE));
        elseif(isset($_SESSION[$this->_formSessionVar]))
            miniorange_site_challenge_otp('ajax_phone','',null, trim($data['user_phone']),"phone",null,$data);
    }


    
    function _handle_wp_login_ajax_form_validate_action($data)
    {
        MoUtility::checkSession();
        if(!isset($_SESSION[$this->_formSessionVar])) return;

        if(strcmp($_SESSION['phone_number_mo'], MoUtility::processPhoneNumber($data['user_phone'])))
            wp_send_json( MoUtility::_create_json_response( MoMessages::showMessage('PHONE_MISMATCH'),'error'));
        else
            do_action('mo_validate_otp','mo_customer_validation_otp_token',NULL);
    }


    
    function handle_failed_verification($user_login,$user_email,$phone_number)
    {
        MoUtility::checkSession();
        if(!isset($_SESSION[$this->_formSessionVar])
            && !isset($_SESSION[$this->_formSessionVar2]) ) return;

        if(isset($_SESSION[$this->_formSessionVar])){
            $_SESSION[$this->_formSessionVar] = 'verification_failed';
            wp_send_json( MoUtility::_create_json_response(MoUtility::_get_invalid_otp_method(),'error'));
        }

        if(isset($_SESSION[$this->_formSessionVar2]))
            miniorange_site_otp_validation_form($user_login,$user_email,$phone_number,MoUtility::_get_invalid_otp_method(),"phone",FALSE);
    }


    
    function handle_post_verification($redirect_to,$user_login,$user_email,$password,$phone_number,$extra_data)
    {
        MoUtility::checkSession();
        if(!isset($_SESSION[$this->_formSessionVar])
            && !isset($_SESSION[$this->_formSessionVar2])) return;

        if(isset($_SESSION[$this->_formSessionVar])){
            $_SESSION[$this->_formSessionVar] = 'validated';
            wp_send_json( MoUtility::_create_json_response('','success') );
        }

        if(isset($_SESSION[$this->_formSessionVar2])) {
            $_SESSION[$this->_formSessionVar2] = 'validated';
        }
    }


    
    public function unsetOTPSessionVariables()
    {
        unset($_SESSION[$this->_txSessionId]);
        unset($_SESSION[$this->_formSessionVar]);
        unset($_SESSION[$this->_formSessionVar2]);
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


        $this->_isFormEnabled = isset( $_POST['mo_customer_validation_um_login_enable']) ? $_POST['mo_customer_validation_um_login_enable'] : 0;
        $this->_savePhoneNumbers = isset( $_POST['mo_customer_validation_um_login_register_phone']) ? $_POST['mo_customer_validation_um_login_register_phone'] : '';
        $this->_byPassAdmin = isset( $_POST['mo_customer_validation_um_login_bypass_admin']) ? $_POST['mo_customer_validation_um_login_bypass_admin'] : '';
        $this->_phoneKey = isset( $_POST['um_login_phone_field_key']) ? $_POST['um_login_phone_field_key'] : '';
        $this->_allowLoginThroughPhone = isset( $_POST['mo_customer_validation_um_login_allow_phone_login']) ? $_POST['mo_customer_validation_um_login_allow_phone_login'] : '';
        $this->_restrictDuplicates = isset( $_POST['mo_customer_validation_um_login_restrict_duplicates']) ? $_POST['mo_customer_validation_um_login_restrict_duplicates'] : '';
        $this->_otpType = isset( $_POST['mo_customer_validation_um_login_enable_type']) ? $_POST['mo_customer_validation_um_login_enable_type'] : '';

        update_mo_option('mo_customer_validation_um_login_enable_type', $this->_otpType);
        update_mo_option('mo_customer_validation_um_login_enable', $this->_isFormEnabled);
        update_mo_option('mo_customer_validation_um_login_register_phone', $this->_savePhoneNumbers);
        update_mo_option('mo_customer_validation_um_login_bypass_admin', $this->_byPassAdmin);
        update_mo_option('mo_customer_validation_um_login_key', $this->_phoneKey);
        update_mo_option('mo_customer_validation_um_login_allow_phone_login', $this->_allowLoginThroughPhone);
        update_mo_option('mo_customer_validation_um_login_restrict_duplicates', $this->_restrictDuplicates);
    }


    
    
    

    
    public function savePhoneNumbers() { return $this->_savePhoneNumbers; }

    
    function byPassCheckForAdmins() { return $this->_byPassAdmin; }

    
    function allowLoginThroughPhone() { return $this->_allowLoginThroughPhone; }
}