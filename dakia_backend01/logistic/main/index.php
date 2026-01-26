<?php
////////////////////////////////////////////////////
//
// Controller for Admin - Index page
//
////////////////////////////////////////////////////
// get settings

require_once("../includes/settings/config.inc.php");
include_classes([
    'auditlogs.inc'    
], 'library');

$includelogs = [
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'invoices.class',
    'invoicesfilter.class',
    'helpdeskticket.class',
    'helpdeskticketfilter.class',
    'userdepartment.class',
    'userdepartmentfilter.class',
    'services.class' ,
    'servicefilter.class',
    'trackingdata.class',
    'trackingdatafilter.class',
    'assignvehicle.class',
    'assignvehiclefilter.class'
];

if(isset($_POST['table']) && trim($_POST['table'])!= '')
{
    $includelogs[]= str_replace("_", "", $_POST['table']).'.class';
    $includelogs[]= str_replace("_", "", $_POST['table']).'filter.class';
    $includelogs[]= str_replace("_", "", $_POST['table']).'log.class';
    $includelogs[]= str_replace("_", "", $_POST['table']).'logfilter.class';
}
include_classes($includelogs);

$UserSessionManager = SessionManager::getUser();
$userAccountId = $UserSessionManager->getUserAccountId();

if ($userAccountId > 0) {
    $useraccountData = new CustomerAccount($userAccountId);
    if($useraccountData->getThemeId() > 0) {
        $theme = new Themes($useraccountData->getThemeId());
        if($theme->getDashboardTemplate() != "") {
            include_once("index-".$theme->getDashboardTemplate().".php");
        } else {
            include_once("index-normal.php");
        }
    } else {
        include_once("index-normal.php");
    }
} else {
    header("Location:" . BASE_URL . "login");
}