<?php

if(! defined( 'ABSPATH' )) exit;

final class MoOTP
{

	private static $_instance = null;

	private function __construct()
	{
		$this->initializeHooks();
		$this->initializeGlobals();
		$this->initializeHelpers();
		$this->initializeTemplateHandlers();
		$this->registerPolyLangStrings();
	}


	public static function instance()
	{
	  if ( is_null( self::$_instance ) ) {
		self::$_instance = new self;
	  }
	  return self::$_instance;
	}


	private function initializeHooks()
	{
		add_action( 'plugins_loaded'				, array( $this, 'otp_load_textdomain'						 )		  );
		add_action( 'admin_menu'					, array( $this, 'miniorange_customer_validation_menu' 		 ) 		  );
		add_action( 'admin_enqueue_scripts'			, array( $this, 'mo_registration_plugin_settings_style'      ) 		  );
		add_action( 'admin_enqueue_scripts'			, array( $this, 'mo_registration_plugin_settings_script' 	 ) 		  );
		add_action( 'wp_enqueue_scripts'		  	, array( $this, 'mo_registration_plugin_frontend_scripts' 	 ),99	  );
		add_action( 'login_enqueue_scripts'		  	, array( $this, 'mo_registration_plugin_frontend_scripts' 	 ),99	  );
		add_action( 'mo_registration_show_message'	, array( $this, 'mo_show_otp_message'    		 			 ),1   , 2);
		add_action( 'hourlySync'					, array( $this, 'hourlySync'								 ) 		  );
		add_action( 'admin_footer'                  , array( $this,	'feedback_request'  						 )        );
	}


	private function initializeHelpers()
	{
		MoMessages::instance();
		PolyLangStrings::instance();
	}


	private function initializeTemplateHandlers()
	{
		MoOTPActionHandlerHandler::instance();
		DefaultPopup::instance();
		ErrorPopup::instance();
		ExternalPopup::instance();
		UserChoicePopup::instance();
		MoRegistrationHandler::instance();
	}


	private function initializeGlobals()
	{
		global $phoneLogic,$emailLogic;
		$phoneLogic = new PhoneVerificationLogic();
		$emailLogic = new EmailVerificationLogic();
	}


	function miniorange_customer_validation_menu()
	{
		$menu_slug = 'mosettings';
		add_menu_page (	mo_('OTP Verification') , mo_('OTP Verification') , 'manage_options', $menu_slug ,
			array( $this, 'mo_customer_validation_options'), plugin_dir_url(__FILE__) . 'includes/images/miniorange_icon.png' );
		add_submenu_page( $menu_slug	,mo_('OTP Verification')	, mo_('Forms'),'manage_options',$menu_slug
			, array( $this, 'mo_customer_validation_options'));
		add_submenu_page( $menu_slug	,mo_('OTP Verification')	, mo_('Security Settings'),'manage_options','otpsettings'
			, array( $this, 'mo_customer_validation_options'));
		add_submenu_page( $menu_slug	,mo_('OTP Verification')	,mo_('Account'),'manage_options','otpaccount'
			, array( $this, 'mo_customer_validation_options'));
		// add_submenu_page( $menu_slug	,'OTP Verification'	,'SMS/EMail Templates','manage_options','config'
		// 	, array( $this, 'mo_customer_validation_options'));
		add_submenu_page( $menu_slug	,mo_('OTP Verification')	,mo_('Messages'),'manage_options','messages'
			, array( $this, 'mo_customer_validation_options'));
		add_submenu_page( $menu_slug	,mo_('OTP Verification')	,mo_('Design'),'manage_options','design'
			, array( $this, 'mo_customer_validation_options'));
		// add_submenu_page( $menu_slug	,'OTP Verification'	,'Licensing Plans','manage_options','pricing'
		// 	, array( $this, 'mo_customer_validation_options'));
		// add_submenu_page( $menu_slug	,'OTP Verification'	,'Troubleshooting','manage_options','help'
		// 	, array( $this, 'mo_customer_validation_options'));
        // add_submenu_page( $menu_slug	,'OTP Verification'	,'AddOns','manage_options','addon'
        //     , array( $this, 'mo_customer_validation_options'));
	}



	function  mo_customer_validation_options()
	{
		include MOV_DIR . 'controllers/main-controller.php';
	}



	function mo_registration_plugin_settings_style()
	{
		wp_enqueue_style( 'mo_customer_validation_admin_settings_style'	 , MOV_CSS_URL);
	}



	function mo_registration_plugin_settings_script()
	{
		wp_enqueue_script( 'mo_customer_validation_admin_settings_script', MOV_JS_URL , array('jquery'));
		wp_enqueue_script( 'mo_customer_validation_form_validation_script', VALIDATION_JS_URL , array('jquery'));
	}



	function mo_registration_plugin_frontend_scripts()
	{
		if(!get_mo_option('mo_customer_validation_show_dropdown_on_form')) return;
		$selector = apply_filters( 'mo_phone_dropdown_selector', array() );
		if (MoUtility::isBlank($selector)) return;
		$selector = array_unique($selector); 		wp_enqueue_script('mo_customer_validation_inttelinput_script', MO_INTTELINPUT_JS , array('jquery'));
		wp_enqueue_style( 'mo_customer_validation_inttelinput_style', MO_INTTELINPUT_CSS);
		wp_register_script('mo_customer_validation_dropdown_script', MO_DROPDOWN_JS , array('jquery'), MOV_VERSION, true);
		wp_localize_script('mo_customer_validation_dropdown_script', 'modropdownvars', array(
			'selector' =>  json_encode($selector),
			'defaultCountry' => CountryList::getDefaultCountryIsoCode(),
			'onlyCountries' => CountryList::getOnlyCountryList(),
		));
		wp_enqueue_script('mo_customer_validation_dropdown_script');
	}



	function mo_show_otp_message($content,$type)
	{
		new MoDisplayMessages($content,$type);
	}



	function otp_load_textdomain()
	{
		load_plugin_textdomain( 'miniorange-otp-verification', FALSE, dirname( plugin_basename(__FILE__) ) . '/lang/' );
		do_action('mo_otp_verification_add_on_lang_files');
	}



	private function registerPolylangStrings()
	{
		if(!MoUtility::_is_polylang_installed()) return;
		foreach (unserialize(MO_POLY_STRINGS) as $key => $value) {
			pll_register_string($key,$value,'miniorange-otp-verification');
		}
	}


	function feedback_request()
	{
		include MOV_DIR . 'controllers/feedback.php';
	}






	function hourlySync()
	{

		$customerKey = get_mo_option('mo_customer_validation_admin_customer_key');
		$apiKey = get_mo_option('mo_customer_validation_admin_api_key');
		if(isset($customerKey) && isset($apiKey)) MoUtility::_handle_mo_check_ln(FALSE, $customerKey, $apiKey);
	}
}
