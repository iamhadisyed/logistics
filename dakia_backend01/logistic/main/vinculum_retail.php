<?php
require_once("../includes/settings/config.inc.php");

$user = SessionManager::getUser();
if (!isset($_GET['id']) || $_GET['id'] == "") {
    util_redirect("../main/index.php");
}

$id = $_GET['id'];
$MarketPlacesFilter = new MarketPlacesFilter();
$MarketPlacesFilter->addFieldFilter("is_active", "1");
$MarketPlacesFilter->addMd5IdByFilter($id);
$marketPlaceList = $MarketPlacesFilter->getColumnList("id, parent_id, channel_type");
if (count($marketPlaceList) > 0) {
    $parentid = $marketPlaceList[0]->getParentId();
    $channel_type = $marketPlaceList[0]->getChannelType();
    if ($parentid > 0) {
        $platform_id = md5($parentid);
    } else {
        $platform_id = $id;
    }
}


$UserShoppingPlatformMappingFilter = new UserMarketPlacesMappingFilter();
$UserShoppingPlatformMappingFilter->addUserIdFilter($user->getId());
$UserShoppingPlatformMappingFilter->addMarketPlacesMappingIdMd5Filter($platform_id);
$UserData = $UserShoppingPlatformMappingFilter->getList();
if (count($UserData) > 0) {
    $UserData = $UserData[0];
    $authDataArray = (array) json_decode($UserData->getAuthData());

    $extClientId = $authDataArray["extClientId"];

    util_redirect("http://owe.vineretailexpress.com//eRetailWeb/doLogin?extClientId=" . $extClientId . "&channelType=" . $channel_type);
} else {
    util_redirect("../main/index.php");
    exit();
}
?>
<center><h1>Please wait.... <br /> Its redirecting to Vinculum.</h1></center>