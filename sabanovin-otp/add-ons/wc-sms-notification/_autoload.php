<?php

    if(! defined( 'ABSPATH' )) exit;

    if(!function_exists('includeFile')) return;

    define('MSN_DIR', plugin_dir_path(__FILE__));
    define('MSN_URL', plugin_dir_url(__FILE__));
    define('MSN_VERSION', '1.0.0');
    define('MSN_CSS_URL', MSN_URL . 'includes/css/msn-settings.min.css?version='.MSN_VERSION);
    define('MSN_JS_URL', MSN_URL . 'includes/js/settings.min.js?version='.MSN_VERSION);

    includeFile(MSN_DIR . 'objects');
    require_once 'helper/wc-order-status.php';
    includeFile(MSN_DIR . 'helper');
    includeFile(MSN_DIR . 'handler');

