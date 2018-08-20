<?php

	
	class WooCommerceNewCustomerNotification extends SMSNotification
	{
		public static $instance;
		
		function __construct()
		{
			parent::__construct();
			$this->title 			= 'New Account';
			$this->page 			= 'wc_new_customer_notif';
			$this->isEnabled 		= FALSE;
			$this->tooltipHeader 	= 'NEW_CUSTOMER_NOTIF_HEADER';
			$this->tooltipBody 		= 'NEW_CUSTOMER_NOTIF_BODY';
			$this->recipient 		= 'customer';
			$this->smsBody 			=  get_option('woocommerce_registration_generate_password') === 'yes' 
										? MoAddOnMessages::showMessage('NEW_CUSTOMER_SMS_WITH_PASS') 
										: MoAddOnMessages::showMessage('NEW_CUSTOMER_SMS');
			$this->defaultSmsBody	=  get_option('woocommerce_registration_generate_password') === 'yes' 
										? MoAddOnMessages::showMessage('NEW_CUSTOMER_SMS_WITH_PASS') 
										: MoAddOnMessages::showMessage('NEW_CUSTOMER_SMS');
			$this->availableTags 	= '{site-name},{username},{password},{accountpage-url}';
			$this->pageHeader 		= mo_("NEW ACCOUNT NOTIFICATION SETTINGS");
			$this->pageDescription 	= mo_("SMS notifications settings for New Account creation SMS sent to the users");
			self::$instance 		= $this;
		}


		
		public static function getInstance()
		{
			return self::$instance === null ? new self() : self::$instance;
		}


		
		function sendSMS(array $args)
		{
			if(!$this->isEnabled) return;
			$customer_id 	= $args['customer_id'];
			$customer_data  = $args['new_customer_data']; 
			$siteName 		= get_bloginfo();
			$username 		= get_userdata($customer_id)->user_login;
			$phoneNumber 	= get_user_meta( $customer_id, 'billing_phone', TRUE );
			$phoneNumber 	= MoUtility::isBlank($phoneNumber) && array_key_exists('billing_phone',$_POST) ? $_POST['billing_phone'] : NULL;
			$password 		= ! empty( $customer_data['user_pass'] ) ? $customer_data['user_pass'] : '';
			$accountpage 	= wc_get_page_permalink( 'myaccount' ); 
			$smsBody 		= MoAddOnUtiltiy::replaceString(array('site-name'=>get_bloginfo() , 'username' => $username, 
															'password' => $password, 'accountpage-url' => $accountpage), $this->smsBody);

			if(MoUtility::isBlank($phoneNumber)) return;
			MoAddOnUtiltiy::send_notif($phoneNumber, $smsBody);	
		}
	}