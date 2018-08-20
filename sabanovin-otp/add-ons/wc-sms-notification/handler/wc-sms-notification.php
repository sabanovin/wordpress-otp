<?php 
	
	
	class WooCommerceNotifications extends BaseAddonHandler implements AddOnHandlerInterface
	{

        private static $_instance = null;
        private $notificationSettings;

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
			if(!$this->isValidPlugin()) return;
			$this->notificationSettings = get_option('mo_wc_sms_notification_settings') 
				? get_option('mo_wc_sms_notification_settings') : WooCommerceNotificationsList::instance();

			add_action( 'woocommerce_created_customer_notification', array( $this, 'mo_send_new_customer_sms_notif' ), 1, 3 );
			add_action( 'woocommerce_new_customer_note_notification', array( $this, 'mo_send_new_customer_sms_note'), 1, 1 );
			add_action( 'woocommerce_order_status_changed', array( $this, 'mo_send_admin_order_sms_notif' ), 1, 3 );
			add_action( 'woocommerce_order_status_changed', array( $this, 'mo_customer_order_hold_sms_notif' ), 1, 3 );
			add_action( 'add_meta_boxes', array( $this,'add_custom_msg_meta_box' ), 1);
						add_action( 'admin_init',  array( $this, '_handle_admin_actions' ) );
		}


		
		function _handle_admin_actions()
		{
			if(!current_user_can('manage_options')) return;
			if(array_key_exists('option',$_GET) && $_GET['option']=="mo_send_order_custom_msg")
				$this->_send_custom_order_msg($_POST);
		}


		
		function isValidPlugin()
		{
			return MoUtility::micr();
		}


		
		function mo_send_new_customer_sms_notif($customer_id, $new_customer_data = array(), $password_generated = false)
		{
			$this->notificationSettings->getWcNewCustomerNotif()->sendSMS(
				array( 'customer_id'=>$customer_id, 'new_customer_data' => $new_customer_data, 'password_generated' => $password_generated )
			);
		}


		
		function mo_send_new_customer_sms_note($args)
		{
			$this->notificationSettings->getWcCustomerNoteNotif()->sendSMS( array('orderDetails' => wc_get_order($args['order_id'])));
		}


		
		function mo_send_admin_order_sms_notif($order_id, $old_status, $new_status)
		{
			$order = new WC_Order( $order_id );
			if(!is_a($order,'WC_Order')) return;
			$this->notificationSettings->getWcAdminOrderStatusNotif()->sendSMS( 
				array('orderDetails' =>$order, 'new_status'=>$new_status, 'old_status'=> $old_status)
			);
		}


		
		function mo_customer_order_hold_sms_notif($order_id, $old_status, $new_status)
		{
			$order = new WC_Order( $order_id );
			if(!is_a($order,'WC_Order')) return;
			if(strcasecmp($new_status,WcOrderStatus::ON_HOLD)==0)
				$this->notificationSettings->getWcOrderOnHoldNotif()->sendSMS( array('orderDetails' =>$order) );
			elseif(strcasecmp($new_status,WcOrderStatus::PROCESSING)==0)
				$this->notificationSettings->getWcOrderProcessingNotif()->sendSMS( array('orderDetails' =>$order) );
			elseif(strcasecmp($new_status,WcOrderStatus::COMPLETED)==0)
				$this->notificationSettings->getWcOrderCompletedNotif()->sendSMS( array('orderDetails' =>$order) );
			elseif(strcasecmp($new_status,WcOrderStatus::REFUNDED)==0)
				$this->notificationSettings->getWcOrderRefundedNotif()->sendSMS( array('orderDetails' =>$order) );
			elseif(strcasecmp($new_status,WcOrderStatus::CANCELLED)==0)
				$this->notificationSettings->getWcOrderCancelledNotif()->sendSMS( array('orderDetails' =>$order) );
			elseif(strcasecmp($new_status,WcOrderStatus::FAILED)==0)
				$this->notificationSettings->getWcOrderFailedNotif()->sendSMS( array('orderDetails' =>$order) );
			elseif(strcasecmp($new_status,WcOrderStatus::PENDING)==0)
				$this->notificationSettings->getWcOrderPendingNotif()->sendSMS( array('orderDetails' =>$order) );
		}


		
		function unhook($email_class)
		{
			remove_action( 'woocommerce_low_stock_notification', array( $email_class, 'low_stock' ) );
			remove_action( 'woocommerce_no_stock_notification', array( $email_class, 'no_stock' ) );
			remove_action( 'woocommerce_product_on_backorder_notification', array( $email_class, 'backorder' ) );
			remove_action( 'woocommerce_order_status_pending_to_processing_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
			remove_action( 'woocommerce_order_status_pending_to_completed_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
			remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
			remove_action( 'woocommerce_order_status_failed_to_processing_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
			remove_action( 'woocommerce_order_status_failed_to_completed_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
			remove_action( 'woocommerce_order_status_failed_to_on-hold_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
			remove_action( 'woocommerce_order_status_pending_to_processing_notification', array( $email_class->emails['WC_Email_Customer_Processing_Order'], 'trigger' ) );
			remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $email_class->emails['WC_Email_Customer_Processing_Order'], 'trigger' ) );
			remove_action( 'woocommerce_order_status_completed_notification', array( $email_class->emails['WC_Email_Customer_Completed_Order'], 'trigger' ) );
			remove_action( 'woocommerce_new_customer_note_notification', array( $email_class->emails['WC_Email_Customer_Note'], 'trigger' ) );
		}


		
		function add_custom_msg_meta_box()
		{
			add_meta_box( 'mo_wc_custom_sms_meta_box', 'Custom SMS', array($this,'mo_show_send_custom_msg_box'),'shop_order', 'side', 'default' );
		}


		
		function mo_show_send_custom_msg_box($data)
		{
			$orderDetails = new WC_Order($data->ID);
			include MSN_DIR . 'views/custom-order-msg.php';
		}


		
		function _send_custom_order_msg($POST)
		{
			if(!array_key_exists('numbers',$POST) || MoUtility::isBlank($POST['numbers'])) 
				MoUtility::_create_json_response(MoAddOnMessages::showMessage("INVALID_PHONE"),MoConstants::ERROR_JSON_TYPE);
			else
			{
				foreach (explode(";",$POST['numbers']) as $number) {
					if(MoAddOnUtiltiy::send_notif($number,$POST['msg']))
						wp_send_json(MoUtility::_create_json_response(
							MoAddOnMessages::showMessage("SMS_SENT_SUCCESS"),MoConstants::SUCCESS_JSON_TYPE));
					else
						wp_send_json(MoUtility::_create_json_response(
							MoAddOnMessages::showMessage("ERROR_SENDING_SMS"),MoConstants::ERROR_JSON_TYPE));
				}
			}
		}

	}