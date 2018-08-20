<?php

	$goBackURL 			= remove_query_arg( array('sms'), $_SERVER['REQUEST_URI'] );
	$smsSettings		= $notification_settings->getWcOrderFailedNotif();
	$enableDisableTag 	= $smsSettings->page.'_enable';
	$textareaTag		= $smsSettings->page.'_smsbody';
	$recipientTag		= $smsSettings->page.'_recipient';
	$formOptions 		= $smsSettings->page.'_settings';

	if(MoAddOnUtiltiy::areFormOptionsBeingSaved($formOptions))
	{
		$notification_settings->getWcOrderFailedNotif()->setIsEnabled(array_key_exists($enableDisableTag, $_POST) ? TRUE : FALSE);
		if(array_key_exists($recipientTag, $_POST))
			$notification_settings->getWcOrderFailedNotif()->setRecipient(serialize(explode(";",$_POST[$recipientTag])));
		if(array_key_exists($textareaTag, $_POST))
			$notification_settings->getWcOrderFailedNotif()->setSmsBody(
				MoUtility::isBlank($_POST[$textareaTag]) ? $smsSettings->defaultSmsBody : $_POST[$textareaTag]);
		update_option('mo_wc_sms_notification_settings',$notification_settings);
		$smsSettings	= $notification_settings->getWcOrderFailedNotif();
	}
	
	$recipientValue		= $smsSettings->recipient=="customer" ? "customer" : implode(";",maybe_unserialize($smsSettings->recipient));
	$enableDisable 		= $smsSettings->isEnabled ? "checked" : "";
	
	include MSN_DIR . '/views/smsnotifications/wc-customer-sms-template.php';