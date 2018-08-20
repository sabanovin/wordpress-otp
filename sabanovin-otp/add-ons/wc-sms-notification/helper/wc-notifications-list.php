<?php

	
	
	class WooCommerceNotificationsList
	{
		public $wc_new_customer_notif;
		public $wc_customer_note_notif;
		public $wc_admin_order_status_notif;
		public $wc_order_on_hold_notif;
		public $wc_order_processing_notif;
		public $wc_order_completed_notif;
		public $wc_order_refunded_notif;
		public $wc_order_cancelled_notif;
		public $wc_order_failed_notif;
		public $wc_order_pending_notif;
		public static $_instance;

		function __construct()
		{
			$this->wc_new_customer_notif  		= WooCommerceNewCustomerNotification::getInstance();
			$this->wc_customer_note_notif 		= WooCommerceCutomerNoteNotification::getInstance();
			$this->wc_admin_order_status_notif 	= WooCommerceAdminOrderstatusNotification::getInstance(); 
			$this->wc_order_on_hold_notif 		= WooCommerceOrderOnHoldNotification::getInstance();
			$this->wc_order_processing_notif 	= WooCommerceOrderProcessingNotification::getInstance();
			$this->wc_order_completed_notif 	= WooCommerceOrderCompletedNotification::getInstance();
			$this->wc_order_refunded_notif 		= WooCommerceOrderRefundedNotification::getInstance();
			$this->wc_order_cancelled_notif	 	= WooCommerceOrderCancelledNotification::getInstance();
			$this->wc_order_failed_notif 		= WooCommerceOrderFailedNotification::getInstance();
			$this->wc_order_pending_notif 		= WooCommerceOrderPendingNotification::getInstance();
		}


		
		public static function instance()
	 	{
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self;
            }
            return self::$_instance;
	 	}
	
	
	    
	    public function getWcNewCustomerNotif()
	    {
	        return $this->wc_new_customer_notif;
	    }
	    
	
	    
	    public function getWcCustomerNoteNotif()
	    {
	        return $this->wc_customer_note_notif;
	    }


	    
	    public function getWcAdminOrderStatusNotif()
	    {
	        return $this->wc_admin_order_status_notif;
	    }

	    
	    public function getWcOrderOnHoldNotif()
	    {
	        return $this->wc_order_on_hold_notif;
	    }

	    
	    public function getWcOrderProcessingNotif()
	    {
	        return $this->wc_order_processing_notif;
	    }
	
	    
	    public function getWcOrderCompletedNotif()
	    {
	        return $this->wc_order_completed_notif;
	    }
	
	    
	    public function getWcOrderRefundedNotif()
	    {
	        return $this->wc_order_refunded_notif;
	    }

	    
	    public function getWcOrderCancelledNotif()
	    {
	        return $this->wc_order_cancelled_notif;
	    }
	
	    
	    public function getWcOrderFailedNotif()
	    {
	        return $this->wc_order_failed_notif;
	    }

	    
	    public function getWcOrderPendingNotif()
	    {
	        return $this->wc_order_pending_notif;
	    }
	}