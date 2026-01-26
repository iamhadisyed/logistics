<?php
//define("SETTING_MAIN_URL",   "http://www.oneoneworldexpress.com/");
define("SETTING_URL_NO_HTTP",   "http://smarttrack.test/");
// define the website
define("COMMON_SITE", "gl");
define("SETTING_URL",   "http://smarttrack.test/");
define("SETTING_MAIN_URL",   "http://smarttrack.test/");
define("SETTING_MAIN_ASSETS",   "http://smarttrack.test/_assets/");
define("SETTING_MAIN_URL_ACCOUNT",   "http://smarttrack.test/");
define("SETTING_URL_LABEL_RELATIVE", "../_assets/pdf/");

define("SETTING_DIR_ASSETS",  "D:/xampp/htdocs/smarttrack/_assets/");
define("SETTING_DIR_REMOTE",  "D:/xampp/htdocs/smarttrack/");


define("OPTIMUS_IMAGE_PATH","http://164.39.218.212/Optimus/image/");

define("SETTING_URL", "http://local.oneworldexpress.co.uk/");
define("SETTING_URL_ASSETS", "http://local.oneworldexpress.co.uk/_assets/");
define("SETTING_URL_LABEL", "http://local.oneworldexpress.co.uk/_assets/pdf/");
define("SETTING_URL_LABEL_RELATIVE", "../_assets/pdf/");
define("SETTING_DIR_REMOTE", "C:/SmartTrackOptimization/");
define("SETTING_DIR_ASSETS", "C:/SmartTrackOptimization/_assets/");

//irshadali18-business@gmail.com
define("PAYPAL_EMAIL_ADDRESS" , 'itsupport@oneworldexpress.com');
define("PAYPAL_LIVE" , false);
define("ORDER_SALT" , 'chaloyepaymentkar');
define("PAYPAL_LOG_FILE", 'paypal-log.html');

define("BASE_URL",   "http://smarttrack.test/");
define("BASE_PATH",  "D:/xampp/htdocs/smarttrack/");

define("_ASSETS_URL",   "http://smarttrack.test/_assets/");
define("_ASSETS_PATH",   "D:/xampp/htdocs/smarttrack/_assets/");

define("WARN_TIME_LIMIT",  60); //in seconds
define("IDLE_TIME_LIMIT",  6000); //in seconds

/*
// Email settings
define("SETTING_SMTP_SERVER",   "mail.exigen.co.uk");
define("SETTING_SMTP_USER",     "extest@exigen.co.uk");
define("SETTING_SMTP_PASSWORD", "1crAvA2393");

define("SETTING_EMAIL_ERROR", "aqazi@glslogistics.co.uk");
define("SETTING_EMAIL_ADMIN", "aqazi@glslogistics.co.uk");

define("SETTING_EMAIL_CAPTURE", "");
define("SETTING_EMAIL_BCC", "azzam.qazi@glslogistics.com.pk");*/



if(isset($_REQUEST['LIVEDB']))
{

	define("SETTING_DB_SERVER",   "localhost");
	define("SETTING_DB_USER",     "rumba19a");
	define("SETTING_DB_PASSWORD", "J78VQ>52wrV!Q@8");
	define("SETTING_DB_DATABASE", "rumba19");
}
else
{
//	define("SETTING_DB_SERVER",   "89.197.43.132");
//	define("SETTING_DB_USER",     "optimization_usr");
//	define("SETTING_DB_PASSWORD", 'UsrOpt$E£$7846@');
//	define("SETTING_DB_DATABASE", "rumba19_opt");
//        define("SETTING_DB_SERVER",   "localhost");
//        define("SETTING_DB_USER",     "root");
//        define("SETTING_DB_PASSWORD", '');
//        define("SETTING_DB_DATABASE", "rumba19_opt");
//       define("SETTING_DB_SERVER",   "192.168.10.102");
    // Office local setting
//        define("SETTING_DB_SERVER",   "SERVER-PC");
//        define("SETTING_DB_USER",     "stellar");
//        define("SETTING_DB_PASSWORD", 'stellar');
//        define("SETTING_DB_DATABASE", "smarttrack");
        
        define("SETTING_DB_SERVER",   "localhost");
        define("SETTING_DB_USER",     "root");
        define("SETTING_DB_PASSWORD", '');
        define("SETTING_DB_DATABASE", "smarttrack_staging");

//        define("SETTING_DB_SERVER",   "213.246.109.140");
//        define("SETTING_DB_USER",     "mruga");
//        define("SETTING_DB_PASSWORD", 'Yack^070');
//        define("SETTING_DB_DATABASE", "smarttrack_staging");
//	629720311714154361
}

/*
* FTP configuration for sending YODEL booking files
*/
define("SETTING_FTP_SITE_YODEL",      "cs.yodel.co.uk");
define("SETTING_FTP_USER_YODEL",      "oneworld");
define("SETTING_FTP_PASSWORD_YODEL",  "serv1ce");

define("SETTING_FTP_SITE_INT_YODEL",      "ftp2.dhl.com");
define("SETTING_FTP_USER_INT_YODEL",      "ftptglb");
define("SETTING_FTP_PASSWORD_INT_YODEL",  "EDCrfvRe");





//////SYNC SERVER
define("SETTING_ARCHIVE_SERVER",   "89.197.43.132");

define("SETTING_ARCHIVE_USER",     "smarttrack_usr");
define("SETTING_ARCHIVE_PASSWORD", "UsrOpt$E£$7846@");
define("SETTING_ARCHIVE_DATABASE", "rumba19_opt");



define("SERVER_IP_INTERNAL", "89.197.43.130");
define("SERVER_USERNAME_INVOICE_INTERNAL", "cmssystem");
define("SERVER_PASSWORD_INVOICE_INTERNAL", "cybernet");


define("YPS_HOST",      "213.246.110.102");
define("YPS_USER",      "ypsus3r_29Oct13");
define("YPS_PASSWORD",      "myp@ss.com543");
define("YPS_DB",      "YPS_COM_29Oct2013");

define("API2CART_URL",   "https://api.api2cart.com/v1.0/");
define("API2CART_API_KEY",  "361db42568e164ef0c44197e4c34fed2");

