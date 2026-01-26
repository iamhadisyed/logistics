<?php

//echo 'asdasd'; die;
// get settings
require_once("../includes/settings/config.inc.php");

$auth_data = false;
$platform_id = false;

/* * *
 * Controller logic
 */
// user must be CLIENT
Sessionmanager::checkUserAccess(USER::PRIVILEGE_CLIENT); 

$user = SessionManager::getUser();

    $arr = array();
    $platform_id                      = $_POST['id'];
    $arr["AWS_ACCESS_KEY_ID"]         = $_POST['awsAccessKey'];
    $arr["AWS_SECRET_ACCESS_KEY"]     = $_POST['awsSecret'];
    $arr["MERCHANT_ID"]               = $_POST['sellerId'];
    $arr["MARKETPLACE_ID"]            = $_POST['marketPlaceId'];
    
    unset($_SESSION['detail']);
    $platform_id = trim($platform_id);

    //GET user platforms
    $UserMarketPlacesMappingFilter = new UserMarketPlacesMappingFilter();
    $UserMarketPlacesMappingFilter->addMarketPlacesMappingIdFilter($platform_id);
    $UserMarketPlacesMappingFilter->addUserIdFilter($user->getId());
    $UserData = $UserMarketPlacesMappingFilter->getList(); 
   
        $UserMarketPlacesMapping = new UserMarketPlacesMapping($UserData[0]->getId());
        $authData = json_encode($arr);
        $UserMarketPlacesMapping->setauthdata($authData);
        $UserMarketPlacesMapping->save();
        $authDataArray = (array)json_decode($authData);    
    
    foreach ($authDataArray as $key => $value) {
        if(empty($value)){
            $this->auth_data = true;
            break;
        }else{
            $_SESSION['detail'][$key] = $value;
        }      
    } 
    require_once("amazon.php");
?>