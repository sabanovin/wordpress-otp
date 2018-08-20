<?php


/**
 * Plugin Name: Sabanovin OTP
 * Plugin URI: https://sabanovin.com/
 * Description: Email verification for all forms Woocommerce, Contact 7 etc. SMS and Mobile Verification for all forms. Enterprise grade. Active Support.
 * Version: 3.2.47
 * Author: Alireza Akhtari
 * Author URI: https://twitter.com/akhtarialireza
 * Text Domain: miniorange-otp-verification
 * Domain Path: /lang
 * WC requires at least: 2.0.0
 * WC tested up to: 3.4.4
 * License: GPL2
 */

if(! defined( 'ABSPATH' )) exit;
include '_autoload.php';
include 'main.php';
MoOTP::instance(); 
