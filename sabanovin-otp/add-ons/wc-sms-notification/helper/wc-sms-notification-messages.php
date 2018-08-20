<?php


class MoAddOnMessages
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
				define("MO_ADDON_MESSAGES", serialize( array(			
						"NEW_CUSTOMER_NOTIF_HEADER" 	=>	__('NEW ACCOUNT NOTIFICATION','miniorange-wc-sms-notification'),
			"NEW_CUSTOMER_NOTIF_BODY" 		=> 	__('Customers are sent a new account SMS notification when they sign up via checkout or account page.','miniorange-wc-sms-notification'),
			"NEW_CUSTOMER_SMS_WITH_PASS"	=> 	__('Thanks for creating an account on {site-name}.%0aYour username is {username}.%0aYour password is: {password}','miniorange-wc-sms-notification'),
			"NEW_CUSTOMER_SMS"				=> 	__('Thanks for creating an account on {site-name}.%0aYour username is {username}.%0aLogin Here: {accountpage-url}' ,'miniorange-wc-sms-notification'),

						"CUSTOMER_NOTE_NOTIF_HEADER" 	=>	__('CUSTOMER NOTE NOTIFICATION','miniorange-wc-sms-notification'),
			"CUSTOMER_NOTE_NOTIF_BODY" 		=> 	__('Customers are sent a new note SMS notification when the admin adds a customer note to one of their orders.','miniorange-wc-sms-notification'),
			"CUSTOMER_NOTE_SMS"				=> 	__('Hi {username},%0aA Note has been added to your order %23{order-number} with {site-name} ordered on {order-date} ','miniorange-wc-sms-notification'),

						"NEW_ORDER_NOTIF_HEADER"		=>	__('ORDER STATUS NOTIFICATION','miniorange-wc-sms-notification'),
			"NEW_ORDER_NOTIF_BODY"			=>  __('Recipients will be sent a new sms notification notifying that the status of a order has changed and they need to process it.','miniorange-wc-sms-notification'),
			"ADMIN_STATUS_SMS" 				=>  __('{site-name}: Customer Order %23{order-number} status Changed to: {order-status}.%0aCheck your dashboard for complete details','miniorange-wc-sms-notification'),

						"ORDER_ON_HOLD_NOTIF_HEADER" 	=>	__('ORDER ON HOLD NOTIFICATION','miniorange-wc-sms-notification'),
			"ORDER_ON_HOLD_NOTIF_BODY"		=>	__('Customer will be sent a new sms notification notifying that the status of the order has changed to ON-HOLD.','miniorange-wc-sms-notification'),
			"ORDER_ON_HOLD_SMS"				=>	__('Hello {username},%0aYour order %23{order-number} with {site-name} has been put on hold, we will contact you shortly.','miniorange-wc-sms-notification'),

						"ORDER_PROCESSING_NOTIF_HEADER" => __('PROCESSING ORDER NOTIFICATION','miniorange-wc-sms-notification'),
			"ORDER_PROCESSING_NOTIF_BODY" 	=> __('Customer will be sent a new sms notification notifying that the order is currently under processing.','miniorange-wc-sms-notification'),
			"PROCESSING_ORDER_SMS" 			=> __('Hello {username},%0aYour order %23{order-number} with {site-name} ordered on {order-date} is being processed.','miniorange-wc-sms-notification'),

						"ORDER_COMPLETED_NOTIF_HEADER" 	=> __('ORDER COMPLETED NOTIFICATION','miniorange-wc-sms-notification'),
			"ORDER_COMPLETED_NOTIF_BODY" 	=> __('Customer will be sent a new sms notification notifying that the order processing has been completed.','miniorange-wc-sms-notification'),
			"ORDER_COMPLETED_SMS" 			=> __('Hello {username},%0aYour order %23{order-number} with {site-name} has been processed. Item will be dispatched and delivered to you shortly.','miniorange-wc-sms-notification'),

						"ORDER_REFUNDED_NOTIF_HEADER" 	=> __('ORDER REFUNDED NOTIFICATION','miniorange-wc-sms-notification'),
			"ORDER_REUNDED_NOTIF_BODY" 		=> __('Customer will be sent a new sms notification notifying that the ordered has been refunded.','miniorange-wc-sms-notification'),
			"ORDER_REFUNDED_SMS" 			=> __('Hello {username},%0aYour order %23{order-number} with {site-name} ordered on {order-date} has been refunded.','miniorange-wc-sms-notification'),

						"ORDER_CANCELLED_NOTIF_HEADER" 	=> __('ORDER CANCELLED NOTIFICATION','miniorange-wc-sms-notification'),
			"ORDER_CANCELLED_NOTIF_BODY"	=> __('Customer will be sent a new sms notification notifying that the order has been cancelled.','miniorange-wc-sms-notification'),
			"ORDER_CANCELLED_SMS" 			=> __('Hello {username},%0aYour order %23{order-number} with {site-name} ordered on {order-date} has been cancelled.','miniorange-wc-sms-notification'),

						"ORDER_FAILED_NOTIF_HEADER" 	=> __('ORDER FAILED NOTIFICATION','miniorange-wc-sms-notification'),
			"ORDER_FAILED_NOTIF_BODY"		=> __('Customer will be sent a new sms notification notifying that the order processing has failed.','miniorange-wc-sms-notification'),
			"ORDER_FAILED_SMS" 				=> __('Hello {username},%0aProcessing of your %23{order-number} with {site-name} ordered on {order-date} has failed. We will contact you shortly.','miniorange-wc-sms-notification'),

						"ORDER_PENDING_NOTIF_HEADER" 	=> __('ORDER PENDING PAYMENT NOTIFICATION','miniorange-wc-sms-notification'),
			"ORDER_PENDING_NOTIF_BODY"		=> __('Customer will be sent a new sms notification notifying that the order is pending payment.','miniorange-wc-sms-notification'),
			"ORDER_PENDING_SMS" 			=> __('Hello {username},%0aYour order %23{order-number} with {site-name} ordered on {order-date} is pending payment.','miniorange-wc-sms-notification'),

						"INVALID_PHONE" 				=> __('Please enter a valid phone number','miniorange-wc-sms-notification'),
			"ERROR_SENDING_SMS" 			=> __('There was an error sending SMS to the user','miniorange-wc-sms-notification'),
			"SMS_SENT_SUCCESS" 				=> __('SMS was sent successfully.','miniorange-wc-sms-notification'),

						"SETTINGS_SAVED"				=> __('Settings Saved Successfully.','miniorange-wc-sms-notification'),
			"ADD_ON_VERIFIED" 				=> __('Thank you for the upgrade. AddOn Settings have been verified.','miniorange-wc-sms-notification'),
		)));
	}



	
	public static function showMessage($messageKeys , $data=array())
	{
		$displayMessage = "";
		$messageKeys = explode(" ",$messageKeys);
		$messages = unserialize(MO_ADDON_MESSAGES);
		foreach ($messageKeys as $messageKey) 
		{
			if(MoUtility::isBlank($messageKey)) return $displayMessage;
			$formatMessage = $messages[$messageKey];
		    foreach($data as $key => $value)
		    {
		        $formatMessage = str_replace("{{" . $key . "}}", $value ,$formatMessage);
		    }
		    $displayMessage.=$formatMessage;
		}
	    return $displayMessage;
	}
}