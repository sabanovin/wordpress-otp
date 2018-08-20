<?php

    $defaultPopup       = DefaultPopup::instance();
    $userChoicePopup    = UserChoicePopup::instance();
    $externalPopup      = ExternalPopup::instance();
    $errorPopup         = ErrorPopup::instance();

    $email_templates = maybe_unserialize(get_mo_option('mo_customer_validation_custom_popups'));
    $custom_default_popup = $email_templates[$defaultPopup->getTemplateKey()];
    $custom_external_popup = $email_templates[$externalPopup->getTemplateKey()];
    $custom_userchoice_popup = $email_templates[$userChoicePopup->getTemplatekey()];
    $error_popup = $email_templates[$errorPopup->getTemplateKey()];
    $common_template_settings = Template::$templateEditor;
    
    $nonce = $defaultPopup->getNonceValue();

    $editorId 		   = $defaultPopup->getTemplateEditorId();
    $templateSettings  = array_merge($common_template_settings,array('textarea_name' => $editorId));

    $editorId2         = $userChoicePopup->getTemplateEditorId();
    $templateSettings2 = array_merge($common_template_settings,array('textarea_name' => $editorId2));

    $editorId3         = $externalPopup->getTemplateEditorId();
    $templateSettings3 = array_merge($common_template_settings,array('textarea_name' => $editorId3));

    $editorId4         = $errorPopup->getTemplateEditorId();
    $templateSettings4 = array_merge($common_template_settings,array('textarea_name' => $editorId4));

    $default_template_type = $defaultPopup->getTemplateKey(); 
    $userchoice_template_type = $userChoicePopup->getTemplatekey(); 
    $external_template_type = $externalPopup->getTemplateKey();
    $error_template_type = $errorPopup->getTemplateKey();
    
    $loaderimgdiv = str_replace("{{CONTENT}}","<img src='".MOV_LOADER_URL."'>",Template::$paneContent);
    $previewpane = "<span style='font-size: 1.3em;'>PREVIEW PANE<br/><br/></span><span>Click on the Preview button above to check how your popup "
                    ."would look like.</span>";
    $previewpane = str_replace("{{MESSAGE}}",$previewpane,Template::$messageDiv);                    
    $message = str_replace("{{CONTENT}}",$previewpane,Template::$paneContent);

    include MOV_DIR . 'views/design.php';