<?php

if(! defined( 'ABSPATH' )) exit;

final class MiniorangeSmsNotification extends BaseAddon implements AddOnInterface
{
    
    private static $_instance = null;

    
    public static function instance()
    {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    public function __construct()
	{
	    parent::__construct();
		add_action( 'admin_enqueue_scripts'					    , array( $this, 'mo_sms_notif_settings_style'   ) );
		add_action( 'admin_enqueue_scripts'					    , array( $this, 'mo_sms_notif_settings_script' 	) );
        add_action( 'mo_otp_verification_delete_addon_options'	, array( $this, 'mo_sms_notif_delete_options' 	) );
	}
	
	
	function mo_sms_notif_settings_style()
	{
		wp_enqueue_style( 'mo_sms_notif_admin_settings_style', MSN_CSS_URL);		
	}

	
	
	function mo_sms_notif_settings_script()
	{
		wp_register_script( 'mo_sms_notif_admin_settings_script', MSN_JS_URL , array('jquery') );
		wp_localize_script( 'mo_sms_notif_admin_settings_script', 'mocustommsg', array(
			'siteURL' 		=> 	admin_url(),
		));
		wp_enqueue_script('mo_sms_notif_admin_settings_script');
	}

    
    function initializeHandlers()
    {
        WooCommerceNotifications::instance();
    }

    
    function initializeHelpers()
    {
        MoAddOnMessages::instance();
        WooCommerceNotificationsList::instance();
    }


    
    function show_addon_settings_page()
    {
        include MSN_DIR . '/controllers/main-controller.php';
    }


    
	function mo_sms_notif_delete_options()
    {
        delete_option('mo_wc_sms_notification_settings');
    }
}