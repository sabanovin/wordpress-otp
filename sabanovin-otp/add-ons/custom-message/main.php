<?php

final class MiniOrangeCustomMessage extends BaseAddOn implements AddOnInterface
{
    
    private static $_instance = null;

    
    public static function instance()
    {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    
    function initializeHandlers()
    {
        CustomMessages::instance();
    }

    
    function initializeHelpers(){}

    
    function show_addon_settings_page()
    {
        include MCM_DIR . 'controllers/main-controller.php';
    }
}