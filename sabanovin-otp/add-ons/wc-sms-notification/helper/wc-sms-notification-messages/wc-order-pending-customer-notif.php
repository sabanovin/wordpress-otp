<?php

	
	class WooCommerceOrderPendingNotification extends SMSNotification
	{
		public static $instance;
		
		function __construct()
		{
			parent::__construct();
			$this->title 			= 'Order Pending Payment';
			$this->page 			= 'wc_order_pending_notif';
			$this->isEnabled 		= FALSE;
			$this->tooltipHeader 	= 'ORDER_PENDING_NOTIF_HEADER';
			$this->tooltipBody 		= 'ORDER_PENDING_NOTIF_BODY';
			$this->recipient 		= "customer";
			$this->smsBody 			= MoAddOnMessages::showMessage('ORDER_PENDING_SMS');
			$this->defaultSmsBody 	= MoAddOnMessages::showMessage('ORDER_PENDING_SMS');
			$this->availableTags 	= '{site-name},{order-number},{username}{order-date}';
			$this->pageHeader 		= mo_("ORDER PENDING PAYMENT NOTIFICATION SETTINGS");
			$this->pageDescription 	= mo_("SMS notifications settings for Order Pending Payment SMS sent to the users");
			self::$instance 		= $this;
		}


		
		public static function getInstance()
		{
			return self::$instance === null ? new self() : self::$instance;
		}


		
		function sendSMS(array $args)
		{
			if(!$this->isEnabled) return;
			$orderDetails 	= $args['orderDetails'];
			if(MoUtility::isBlank($orderDetails)) return;
			$userdetails 	= get_userdata($orderDetails->get_customer_id());
			$siteName 		= get_bloginfo();
			$username 		= MoUtility::isBlank($userdetails) ? "" : $userdetails->user_login;
			$phoneNumber 	= $orderDetails->get_billing_phone();
			$dateCreated 	= $orderDetails->get_date_created()->date_i18n();
			$orderNo 		= $orderDetails->get_order_number();
			$smsBody 		= MoAddOnUtiltiy::replaceString(array('site-name'=>$siteName,'username'=>$username,'order-date'=>$dateCreated,
																'order-number'=>$orderNo), $this->smsBody);
			if(MoUtility::isBlank($phoneNumber)) return;
			MoAddOnUtiltiy::send_notif($phoneNumber, $smsBody);		
		}
	}