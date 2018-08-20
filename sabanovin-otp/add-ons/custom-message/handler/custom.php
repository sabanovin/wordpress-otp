<?php


class CustomMessages extends BaseAddOnHandler implements AddOnHandlerInterface
{

    private static $_instance = null;

    public static function instance()
    {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    
    function __construct()
    {
        parent::__construct();
        $this->_nonce = 'mo_admin_actions';
        if(!$this->isValidPlugin()) return;
        add_action( 'admin_init',  array( $this, '_handle_admin_actions' ) );
    }

    
    function _handle_admin_actions()
    {
        if(!isset($_POST['option'])) return;
        switch($_POST['option'])
        {
            case "mo_customer_validation_custom_phone_notif":
                $this->_mo_validation_send_sms_notif_msg(); 	break;
            case "mo_customer_validation_custom_email_notif":
                $this->_mo_validation_send_email_notif_msg();	break;
        }
    }


    
    function isValidPlugin()
    {
        return MoUtility::micr();
    }


    
    function _mo_validation_send_sms_notif_msg()
    {
        $this->isValidRequest();
        $phone_numbers = explode(";",$_POST['mo_phone_numbers']);
        $message = $_POST['mo_customer_validation_custom_sms_msg'];
        $content = null;
        foreach ($phone_numbers as $phone) {
            $phone = MoUtility::processPhoneNumber($phone);
            $content = json_decode(MocURLOTP::send_notif(new NotificationSettings($phone,$message)));
        }
        $this->checkStatusAndShowMessage($content);
    }


    
    function _mo_validation_send_email_notif_msg()
    {
        $this->isValidRequest();
        $email_addresses = explode(";",$_POST['toEmail']);
        $content = null;
        foreach ($email_addresses as $email) {
            $content = json_decode(MocURLOTP::send_notif(new NotificationSettings($_POST['fromEmail'],$_POST['fromName'],
                $email,$_POST['subject'],stripslashes($_POST['content']))));
        }
        $this->checkStatusAndShowMessage($content);
    }


    
    private function checkStatusAndShowMessage($content)
    {
        if(is_null($content)) return;
        switch ($content->status)
        {
            case 'SUCCESS':
                do_action('mo_registration_show_message', MoMessages::showMessage('CUSTOM_MSG_SENT'),'SUCCESS'); 						break;
            case 'ERROR':
                do_action('mo_registration_show_message', MoMessages::showMessage('CUSTOM_MSG_SENT_FAIL',
                    array('error',$content->message)),'ERROR'); 		break;
        }
    }
}