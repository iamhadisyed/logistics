<?php
require_once("../includes/settings/config.inc.php");
if(isset($_GET['action']) && trim($_GET['action']) == 'GETNEWUSERS'){
		
	$userCorp = new CustomerAccount();
	$userdata = $userCorp->getNewUser();
	$count = count($userdata);
	$json['count'] = $count;
	foreach ($userdata as $user) {

		$json['html'][] = "<li><a href='customers_details.php?id=".$user['id']."'>New Registerd User- ".$user['user_name']."</a> </li>";

	}
	echo json_encode($json);


}


	

	