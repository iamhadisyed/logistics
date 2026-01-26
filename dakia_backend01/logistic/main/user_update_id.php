<?php

require_once("/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/includes/settings/local_settings/oneworldexpress_co_uk.php");
require_once("/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/includes/settings/common.inc.php");

ini_set('max_execution_time', -1);


/* $user = new UserAccountFilter(); 
  $list = $user->getDistinctColumnList("distinct user_account, user_code");
  if(count($list) > 0)
  {
  foreach($list as $userlist)
  {
  $account = $userlist->getUserCode();
  echo	$sql = "UPDATE consignment SET user_code = '".$account."' WHERE id > 0  and account = '".$userlist->getUserAccount()."';";
  echo "<br />";


  }
  } */



$user = new UserAccountFilter();
$list = $user->getDistinctColumnList("distinct user_account");
if (count($list) > 0) {
    foreach ($list as $userlist) {
        $user_account = $userlist->getUserAccount();
        $licence_plate = LicencePlate::getLicencePlateNumber("USER_ACCOUNT");
        //DbAccess3::runQuery(" UPDATE user SET user_code = '".$licence_plate."' where user_account = '".$user_account."' and user_code = ''");
    }
}
?>