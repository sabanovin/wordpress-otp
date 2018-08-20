<?php

    if(! defined( 'ABSPATH' )) exit;

    if(!function_exists('includeFile')) return;

    define('MCM_DIR', plugin_dir_path(__FILE__));
    define('MCM_URL', plugin_dir_url(__FILE__));
    define('MCM_VERSION', '1.0.0');

    includeFile(MCM_DIR . 'handler');
