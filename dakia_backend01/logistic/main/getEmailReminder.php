<?php
require_once("../includes/settings/config.inc.php");
if(isset($_GET['action']) && trim($_GET['action']) == 'GETEMAILREMINDER'){
	$UserSessionManager	=	SessionManager::getUser();
	$SalesCallLogFilter = new SalesCallLogFilter();
    $SalesCallLogFilter->addFieldFilter("userid", $UserSessionManager->getId());
	$SalesCallLogFilter->addFieldFilter("date_format(follow_meeting_date,'%Y-%m-%d')", date("Y-m-d"));
	$saleslist = $SalesCallLogFilter->getList();
	$count = count($saleslist);
	$json['count'] = $count;
	foreach ($saleslist as $list) {

		$json['html'][] = "<li><a href='add_sales_call.php?id=".$list->getId()."'>Meeting - ".$list->getCustomerCode()." @ ". date("H:i",$list->getFollowMeetingDate()) ."</a> </li>";

	}
	echo json_encode($json);


}
 
if (isset($_GET['action']) && trim($_GET['action']) == 'GETCSREMINDER') {
    $csfilter = new CsLogFilter();
    $csList = $csfilter->getAccountCountry(SessionManager::getUser()->getId());

    if (!empty($csList)) {
	foreach ($csList as $list) {

	    $json['html'][] = '  <div id="reminder-message-inner" class="alert alert-danger">
								<a href="#"  class="close" id="saveReminder" data-dismiss="alert" name="saveReminder" value="Add" onclick="updateReminder(' . $list->getId() . ')" ></a>		
								<a href="../main/booking_view.php?id=' . $list->getConsignmentId() . '" > Reminder for ' . $list->getInternalMessage() . '</a>
							</div>';
	}
	echo json_encode($json);
    } else {
	$json['html'][] = "<div></div>";
    }
}

if(isset($_GET['action']) && trim($_GET['action']) == 'UPDATEREMINDER'){
	 $id = $_GET['id'];
	 $csfilter = new CsLogFilter();
	 $csfilter->addFieldFilter("id", $id);
	 $clist = $csfilter->getList();
	 
	 if(count($clist) > 0)
	 {
	 	$clist[0]->setReminderExpiry("");
		$clist[0]->save();
		echo "";
	 }

}


	

	