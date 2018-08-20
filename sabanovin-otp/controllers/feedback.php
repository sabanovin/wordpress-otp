<?php

    $message = mo_('We are sad to see you go :( <br><br>Have you found a bug? Did you feel something was missing? Whatever it is, we\'d love to hear from you and get better.');

    $url = MoConstants::BUSINESS_FREE_TRIAL;

    $notAbleToRegisterText  = mo_("You can even create an account with us here : <a href='".$url."'>link</a>. If you are still having issues creating an account with us then please use the textbox below to send us your email address. We will create an account for you from our end and send you the relevant details.");
    $notReceivingOTPonPhone = mo_("Use the textbox below to send us your phone number that is not receiving the OTP. We will check on our end and get back to you.");
    $notReceivingOTPonEmail = mo_("Use the textbox below to send us your email address that is not receiving the OTP. We will check on our end and get back to you.");
    $issueSettingUp         = mo_("Use the textbox below to briefly describe the issue that you are having. We will get back to you to help you out.");
    $missingFeature         = mo_("Use the textbox below to briefly describe the feature that you are looking for. We will get back to you.");
    $pluginNotWorking       = mo_("Use the textbox below to briefly describe the issue that you are having. We will get back to you to help you out.");
    $otherReasons           = mo_("Use the textbox below to briefly describe the issue that you are having. We will get back to you to help you out.");

    $deactivate_reasons = [
        ["topic" => mo_("Just disabling temporarily"),                   "message" => ""                      ,"showTextBox" => "false" ],
        ["topic" => mo_("Not able to create an account with miniOrange"),"message" => $notAbleToRegisterText  ,"showTextBox" => "true"  ],
        ["topic" => mo_("Not receiving OTP on my Phone Number"),         "message" => $notReceivingOTPonPhone ,"showTextBox" => "true"  ],
        ["topic" => mo_("Not receiving OTP on my Email address"),        "message" => $notReceivingOTPonEmail ,"showTextBox" => "true"  ],
        ["topic" => mo_("Facing issues while setting-up the form"),      "message" => $issueSettingUp         ,"showTextBox" => "true"  ],
        ["topic" => mo_("Missing feature"),                              "message" => $missingFeature         ,"showTextBox" => "true"  ],
        ["topic" => mo_("Plugin is not working as expected"),            "message" => $pluginNotWorking       ,"showTextBox" => "true"  ],
        ["topic" => mo_("Others:"),                                      "message" => $otherReasons           ,"showTextBox" => "true"  ],
    ];

    $submit_message = mo_("Submit & Deactivate");
    $submit_message2= mo_( "Submit");
    $adminHandler 	= MoOTPActionHandlerHandler::instance();
    $nonce          = $adminHandler->getNonceValue();

    include MOV_DIR . 'views/feedback.php';



