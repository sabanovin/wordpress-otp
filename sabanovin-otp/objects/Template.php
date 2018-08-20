<?php

    if(! defined( 'ABSPATH' )) exit;
    
    
    class Template extends BaseActionHandler
    {
        protected $key;
        protected $templateEditorID;
        protected $preview              = FALSE;
        protected $jqueryUrl            = '<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>';
        protected $img                  = "<div style='display:table;text-align:center;'><img src='{{LOADER_CSV}}'></div>";
        protected $requiredTags         = array("{{JQUERY}}","{{GO_BACK_ACTION_CALL}}","{{FORM_ID}}","{{REQUIRED_FIELDS}}","{{REQUIRED_FORMS_SCRIPTS}}");        

        public static $paneContent      = "<div style='text-align: center;width: 100%;height: 450px;display: block;margin-top: 40%;vertical-align: middle;'>{{CONTENT}}</div>";
        public static $messageDiv       = "<div style='font-style: italic;font-weight: 600;color: #23282d;font-family:Segoe UI,Helvetica Neue,sans-serif;color:#942828;'>{{MESSAGE}}</div>";
        public static $successMessageDiv= "<div style='font-style: italic;font-weight: 600;color: #23282d;font-family:Segoe UI,Helvetica Neue,sans-serif;color:#138a3d;'>{{MESSAGE}}</div>";
        public static $templateEditor   = array(
                                            'wpautop' => false, 'media_buttons' => false, 'textarea_rows' => 20, 'tabindex' => '',
                                            'tabfocus_elements' => ':prev,:next', 'editor_css' => '', 'editor_class' => '', 'teeny' => false, 'dfw' => false,
                                            'tinymce' => false, 'quicktags' => true
                                        ); 
                                        
                                                        
        protected function __construct()
        {
            $this->img = str_replace("{{LOADER_CSV}}",MOV_LOADER_URL,$this->img);
            $this->_nonce = 'mo_popup_options';           
            add_filter( 'mo_template_defaults', array($this,'getDefaults'), 1,1);
            add_filter( 'mo_template_build', array($this,'build'), 1,5);
            add_action( 'admin_post_mo_preview_popup', array($this,'showPreview'));
            add_action( 'admin_post_nopriv_mo_preview_popup', array($this,'showPreview'));
            add_action( 'admin_post_mo_popup_save', array($this,'savePopup'));
            add_action( 'admin_post_nopriv_mo_popup_save', array($this,'savePopup'));
        }


        
        public function showPreview()
        {
            if(array_key_exists('popuptype',$_POST)
                && $_POST['popuptype']!=$this->getTemplateKey()) return;
            if(!$this->isValidRequest()) return;
            $message = "<i>" . mo_("PopUp Message shows up here.") . "</i>";
            $otp_type = 'test';
            $from_both = false;
            $template = stripslashes($_POST[$this->getTemplateEditorId()]);
            $this->preview = TRUE;
            wp_send_json(MoUtility::_create_json_response($this->parse($template,$message,$otp_type,$from_both),
                MoConstants::SUCCESS_JSON_TYPE));
        }

        
        public function savePopup()
        {
            if(!$this->isTemplateType() || !$this->isValidRequest()) return;    
            $template = stripslashes($_POST[$this->getTemplateEditorId()]);
            $this->validateRequiredFields($template);
            $email_templates = maybe_unserialize(get_mo_option('mo_customer_validation_custom_popups'));
            $email_templates[$this->getTemplateKey()] = $template;
            update_mo_option('mo_customer_validation_custom_popups',$email_templates);
            wp_send_json(MoUtility::_create_json_response($this->showSuccessMessage(MoMessages::showMessage('TEMPLATE_SAVED')),
                MoConstants::SUCCESS_JSON_TYPE));
        }


        
        public function build($template,$templateType,$message,$otp_type,$from_both)
        {
            if(strcasecmp($templateType,$this->getTemplateKey())!=0) return $template;
            $email_templates = maybe_unserialize(get_mo_option('mo_customer_validation_custom_popups'));
            $template = $email_templates[$this->getTemplateKey()];
            return $this->parse($template,$message,$otp_type,$from_both);
        }


        
        protected function validateRequiredFields($template)
        {
           foreach($this->requiredTags as $tag) {
                if (strpos($template, $tag) === FALSE) {
                    $message = str_replace("{{MESSAGE}}",MoMessages::showMessage('REQUIRED_TAGS',array('TAG'=>$tag)),self::$messageDiv);
                    wp_send_json(MoUtility::_create_json_response(str_replace("{{CONTENT}}",$message,self::$paneContent),MoConstants::ERROR_JSON_TYPE));
                }
           }
        }


        
        protected function showSuccessMessage($message)
        {
            $message = str_replace("{{MESSAGE}}",$message,self::$successMessageDiv);
            return str_replace("{{CONTENT}}",$message,self::$paneContent);
        }


        
        protected function showMessage($message)
        {
            $message = str_replace("{{MESSAGE}}",$message,self::$messageDiv);
            return str_replace("{{CONTENT}}",$message,self::$paneContent);
        }


        
        protected function isTemplateType()
        {
            return array_key_exists('popuptype',$_POST) && strcasecmp($_POST['popuptype'],$this->getTemplateKey())==0 ? TRUE : FALSE;
        }


        
        
        
        

        
        public function getTemplateKey() { return $this->key; }
        
        
        public function getTemplateEditorId(){ return $this->templateEditorID; }
    }