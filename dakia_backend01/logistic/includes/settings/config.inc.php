<?php

////////////////////////////////////////////////////
// Constants and globals common to whole site (including admin).
// There should be no includes in this file!
////////////////////////////////////////////////////
/* ------------------------------------------------------------------------------------ */
/* Standard Config settings */
define("CONFIG_COMPANY", "Logistic");
define("CONFIG_HEAD_TITLE_PREFIX", "Logistic");
define("CONFIG_META_DESCRIPTION", "low cost express parcel service");
define("CONFIG_META_KEYWORDS", "parcels, express parcels");
define("CONFIG_COOKIE_NAME", "gl_");
/* Application Config settings */
// set emails
define("CONFIG_SITE_EMAIL", "itsupport@oneworldexpress.com");
define("CONFIG_SITE_EMAIL_NAME", "Hadi Logistics");
define("CONFIG_SITE_NAME", "Hadi Logistics");
// page templates
define("CONFIG_TEMPLATE_ADMIN", "admin");
define("CONFIG_TEMPLATE_ADMIN_LOGIN", "login");
define("CONFIG_SIMPLE_TEMPLATE", "simple");
define("UKMAILTHEME", 2);
define("HANDLERBUND", 1);
define("DEUTSHCEPOST", 3);
define("VIVATHEME", 4);
define("SPARTHEME", 5);
define("RTCLEARTHEME", 6);


// Country Default Weight Limit
define("DEFAULT_BAG_WEIGHT_LIMIT", 30);

//define("CONFIG_TEMPLATE_ADMIN_LOGIN",     "login");
define("CONFIG_TEMPLATE_ADMIN_LOGIN_DEUCHE", "logindeuche");
// www.postcodesoftware.net postcode lookup parameters
define("CONFIG_POSTCODE_LOOKUP_URL", "http://ws1.postcodesoftware.co.uk/lookup.asmx/getAddress");
define("CONFIG_POSTCODE_LOOKUP_ACCOUNT", "1113");
define("CONFIG_POSTCODE_LOOKUP_PASSWORD", "i42cacoh");

////////////////////////////////////////////////////
/*
 * load local setting config file 
 */

//
/*if (@$_SERVER["HTTP_HOST"] == 'smarttrack.co') {//|| $_SERVER["HTTP_HOST"] == 'staging.smarttrack.co'
    error_reporting(0);
    ini_set('display_errors', '0');
} else {
    error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED & ~E_STRICT);
    ini_set('display_errors', 'On');
}*/
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED & ~E_STRICT);
ini_set('display_errors', 'On');
ini_set('display_startup_errors', 'On');

$localSettingsFile = util_localSettingsFile();

if (!empty($localSettingsFile))
    require_once( __DIR__."/".util_localSettingsFile() );
else if ((php_sapi_name() !== 'cli')) {
    echo "configuration missing.";
    exit;
}

////////////////////////////////////////////////////
// Configuration File
// site wide common configuration

require_once (BASE_PATH . "includes/general/errors.trait.php");
require_once(BASE_PATH . "includes/settings/common_includes.inc.php");
// local settings file - not in SVN
// Start Sessions
session_name(CONFIG_COOKIE_NAME . "_session");
session_start();
Sessionmanager::setSessionId(session_id());
//Expire the session if user is inactive for 30
//minutes or more.
$expireAfter = 15;
//Check to see if our "last action" session
//variable has been set.
if (isset($_SESSION['last_action'])) {
    //Figure out how many seconds have passed //since the user was last active.
    $secondsInactive = time() - $_SESSION['last_action'];
    //Convert our minutes into seconds.
    $expireAfterSeconds = $expireAfter * 60;
    //Check to see if they have been inactive for too long.
    if ($secondsInactive >= $expireAfterSeconds) {
        //User has been inactive for too long. //Kill their session.
        unset($_SESSION);
        session_unset();
        session_destroy();
    }
}

//Assign the current timestamp as the user's
//latest activity
$_SESSION['last_action'] = time();

// Configure library classes
DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);
//BasePage::setPostArrayRead(false); // don't read page variables.  
// Set class autoloader
Sessionmanager::loadPermission();

function my_autoload($class_name) {
    $file = strtolower(BASE_PATH . "includes/autoload/$class_name.class.php");
    if (file_exists($file))
        require_once $file;
}

function include_classes($class_name, $folderName = 'mapping',$debug = false) {
    if (is_array($class_name) && count($class_name) > 0) {
        foreach ($class_name as $classname) {
            //strtolower
            $file = (BASE_PATH . "includes/" . (trim($folderName) != '' ? $folderName . '/' : '') . $classname . ".php");
            if (file_exists($file))
                require_once $file;
            if($debug)
                echo $file."<br>";
        }
    } else if (is_string($class_name) && trim($class_name) !== '') {
        //strtolower
        $file = (BASE_PATH . "includes/" . (trim($folderName) != '' ? $folderName . '/' : '') . $class_name . ".php");
        if (file_exists($file))
            require_once $file;
        if($debug)
                echo $file."<br>";
    }
    if($debug)
        die;
}

/**
 * Determine the name of settings file based on current host server.
 *
 * If the program is run as a script then there could be
 * no host or computer name to base file name, allow
 * a default file name to be passed for this case.
 *
 * @param $defaultFile
 * @return string
 */
function util_localSettingsFile($defaultFile = "") {
    
    $file = $defaultFile;
    if ((php_sapi_name() == 'cli')) 
        return "local_settings/smarttrack_co.php";
    else
    // Used the default passed for situations where there is no host.
    if (isset($_SERVER["HTTP_HOST"])) {
        if ($_SERVER["HTTP_HOST"] == "localhost") {
            if (isset($_SERVER["COMPUTERNAME"]))
                $pc_name = strtolower($_SERVER["COMPUTERNAME"]);
            if ($pc_name == "")
                $pc_name = "default";
            $file = "localhost_" . $pc_name;
        } else {
            $file = isset($_SERVER["HTTP_HOST"]) ? $_SERVER["HTTP_HOST"] : '';
            if (substr($file, 0, 4) == "www.")
                $file = substr($file, 4);
            $file = str_replace(".", "_", $file);

            if (empty($file)) {
                $file = $_SERVER["SCRIPT_NAME"];
                if (strpos($file, "beta") !== false) {
                    $file = "beta_smarttrack_co";
                } else if (strpos($file, "staging") !== false) {
                    $file = "staging_smarttrack_co";
                }else if (strpos($file, "logistics") !== false) {
                    $file = "logistics_funsocio_com";
                } else if (strpos($file, "local") !== false) {
                    $file = "local_oneworldexpress_co_uk";
                } else {
                    $file = "smarttrack_co";
                }
            }
        }
    }
    if (!empty($file))
        return "local_settings/" . str_replace(":8080", "", $file) . ".php";
    else
        return '';
}

spl_autoload_register("my_autoload");
//ini_set("memory_limit", "-1");

