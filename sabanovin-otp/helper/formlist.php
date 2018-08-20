<?php

if(! defined( 'ABSPATH' )) exit;


class FormList
{
    private $_forms;
    private static $_instance = null;

    public static function instance() 
    {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    private function __construct() { $this->_forms = array(); }

    public function addForm($key,$form) { $this->_forms[$key] = $form; }

    public function getForms() { return $this->_forms; } 
}