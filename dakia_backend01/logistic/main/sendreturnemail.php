<?php

require_once("../includes/settings/config.inc.php");
//DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);

//require_once("/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/includes/settings/local_settings/oneworldexpress_co_uk.php");
//require_once("/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/includes/settings/config.inc.php");
//require_once("/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/includes/library/dbaccess3.class.php");
//require_once("/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/includes/library/iaddress.class.php");
include_classes([
        'consignment.class','consignmentfilter.class','consignmentrelabel','consignmentrelabelfilter.class','userfilter.class','user.class',
        'trackingdatatfilter.class','tracking_data.class'
        ]);

//require_once("/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/includes/mapping/consignment.class.php");
//require_once("/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/includes/mapping/consignmentfilter.class.php");
require_once("/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/includes/autoload/sessionmanager.class.php");
//require_once("/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/includes/mapping/consignmentrelabel.class.php");
//require_once("/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/includes/mapping/consignmentrelabelfilter.class.php");
//require_once("/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/includes/mapping/userfilter.class.php");
//require_once("/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/includes/mapping/user.class.php");
//require_once("/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/includes/mapping/trackingdatatfilter.class.php");
//require_once("/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/includes/mapping/tracking_data.class.php");





//echo $date = date("2015-10-14");
//require_once("../includes/settings/config.inc.php");

$date = date("Y-m-d");

//$date = "2017-03-14";

DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);

$trackingDataFilter = new TrackingDataFilter();
$trackingDataFilter->DateCreatedFilter($date);
$trackingDataFilter->addStatusFilter(Consignment::STATUS_RETURNED);
//$trackingDataFilter->DateCreatedFilter("2017-03-08");
$trackingDataFilter->addWarehouseIdFilter("9','10");
//print_r($trackingDataFilter);
//die;
$trackingNumbersList = $trackingDataFilter->getColumnList("distinct tracking_number");

//print_r($trackingNumbersList);
//die;

$trackingNumbersArray = array();

foreach ($trackingNumbersList as $trackingData) {
    if ($trackingData->getTrackingNumber() != '') {
        if (!in_array($trackingData->getTrackingNumber(), $trackingNumbersArray)) {
            $trackingNumbersArray[] = $trackingData->getTrackingNumber();
            echo $trackingData->getTrackingNumber() . "<br>";
        }
    }
}

EmailSendingToCustomers($trackingNumbersArray, $date);
EmailSendingToCustomersParent($trackingNumbersArray, $date);

//print_r($trackingNumbersList);
//die;

function EmailSendingToCustomers($trackingNumbersArray, $date) {


    if (count($trackingNumbersArray) > 0) {
        $strTrackingNumbers = "'" . implode("','", $trackingNumbersArray) . "'";

        $sql = "select distinct u.id 'userid' from consignment c, user u 
					where c.account = u.user_account and awb IN ($strTrackingNumbers)
					and parentid in (4, 175)";

        //echo $sql;

        $result = DbAccess3::runQuery($sql);

        $strTrackingNumbers = "'" . implode("','", $trackingNumbersArray) . "'";

        //$trackingNumbersArray = array();

        $count = 0;

        $userIdArray = array();

        while ($row = $result->fetch_assoc()) {
            $userid = $row["userid"];

            $userIdArray[] = $userid;
        }

        if (count($userIdArray) > 0) {

            foreach ($userIdArray as $userId) {
                $sql = "select distinct awb, account from consignment c, user u 
						    where c.account = u.user_account and u.id = $userId and awb IN ($strTrackingNumbers)
						    and parentid in (4, 175)";

                $result = DbAccess3::runQuery($sql);

                $trackingNumbersArray = array();

                $count = 0;

                while ($row = $result->fetch_assoc()) {

                    if (!in_array($row['awb'], $trackingNumbersArray)) {
                        $trackingNumbersArray[$count]['awb'] = $row['awb'];
                        $trackingNumbersArray[$count++]['account'] = $row['account'];
                    }
                }

                $userAccount = new CustomerAccount($userId);
                $email = trim($user->getEmail());

                if (count($trackingNumbersArray) > 0) {
                    if ($email != '')
                        SendEmail($trackingNumbersArray, $email, $date);
                }
            }
        }
    }
}

function EmailSendingToCustomersParent($trackingNumbersArray, $date) {


    if (count($trackingNumbersArray) > 0) {
        //echo "Total Scanned : " .  count($trackingNumbersArray) . "<br>";
        ////////////////////////// EMAIL SENDING TO PARENT CUSTOMERS /////////////////////////////////////////////////

        $strTrackingNumbers = "'" . implode("','", $trackingNumbersArray) . "'";

        $sql = "select distinct parentid from consignment c, user u where c.account = u.user_account
					and awb IN ($strTrackingNumbers)";

        //echo $sql;
        //die;		

        $result = DbAccess3::runQuery($sql);

        $parentIdArray = array();

        while ($row = $result->fetch_assoc()) {
            $parentIdArray[] = $row['parentid'];
        }

        //print_r($parentIdArray);
        //die;


        foreach ($parentIdArray as $parentId) {

            $sql = "select distinct awb, account from consignment c, user u 
						where c.account = u.user_account and u.parentid = $parentId and awb IN ($strTrackingNumbers)
						and parentid not in (4, 175)
						";


            echo $sql;

            $result = DbAccess3::runQuery($sql);

            $trackingNumbersArray = array();

            $count = 0;

            while ($row = $result->fetch_assoc()) {

                if (!in_array($row['awb'], $trackingNumbersArray)) {
                    $trackingNumbersArray[$count]['awb'] = $row['awb'];
                    $trackingNumbersArray[$count++]['account'] = $row['account'];
                }
            }


            //print_r($trackingNumbersArray);			
            //die;			

            $userAccount = new CustomerAccount($parentId);
            $email = trim($user->getEmail());

            if (count($trackingNumbersArray) > 0) {
                if ($email != '')
                    SendEmail($trackingNumbersArray, $email, $date);
            }



            //mail("kazim@oneworldexpress.com", "Return Shipments " . $email, implode(",", $trackingNumbersArray));		
        }
















        /*

          if (!in_array($row['awb'], $trackingNumbersArray))
          {
          $trackingNumbersArray[] = $row['awb'];
          }

          if($previous == '')
          {
          $previous = $row['email'];
          }
          elseif($previous != $row['email'])
          {
          mail("kazim@oneworldexpress.com", "Return Shipments", implode(",", $trackingNumbersArray));
          }


          }
         */
    }
}

function SendEmail($trackingNumbersArray, $email, $date) {

    //echo $email;
    //print_r($trackingNumbersArray);	
    //return;
    //die;



    if (count($trackingNumbersArray) > 0) {


        $to = $email;

        //$to = "kazim@oneworldexpress.com";

        $subject = 'Return Shipments List : ' . $date;

        //$subject = 'Return Shipments List : ' . $date;

        $headers = "From: ITSupport@oneworldexpress.com \r\n";
        $headers .= "Reply-To: ops@oneworldexpress.com \r\n";
        $headers .= "CC: kazim@oneworldexpress.com, ops@oneworldexpress.com\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        $message .= 'Dear All,<br><br>';
        $message .= "Please find the below list of return shipments which we processed into our warehouse on " . $date . ". According to our Returns shipment                         policy, goods are stored locally for 30 days and not returned immediately unless pre-arranged. 
			             During that period, please provide instructions for these shipments:
						 <br><br>
						 <ul>
						 <li>Return the goods to origin</li>
						 <li>Re-send the goods to customer or alternate address</li>
						 <li>Destroy the items – This may incur a destruction cost</li>
						 <li>Goods can/will be sold to defray expenses where applicable – does not apply to damaged goods non-saleable</li>
						 </ul>
						 <b>Note:</b>
						 <ul>
						 <li>
						 You will be 100% responsible for checking tracking online through the SMART TRACK system – Notification cannot be guaranteed 
						 </li>
 						 <li>
                          These terms may be enforced without notification where the goods have been in our care or in storage for more than ninety days 	                          (90 Days).
						 </li> 
						 <li>
                         	Admin and Handling fees may apply – speak to your account manager for details.
						 </li>
						 </ul>	
						 <br><br>";
        $message .= '<style>
						table {
							font-family: arial, sans-serif;
							border-collapse: collapse;
							width: 100%;
						}
						
						td, th {
							border: 1px solid #dddddd;
							text-align: left;
							padding: 8px;
						}
						
						tr:nth-child(even) {
							background-color: #dddddd;
						}
						</style>';

        $message .= "<table>";
        $message .= "<tr>";
        $message .= "<td>";
        $message .= "Account";
        $message .= "</td>";
        $message .= "<td>";
        $message .= "Tracking Number";
        $message .= "</td>";
        $message .= "</tr>";

        foreach ($trackingNumbersArray as $trackingNumber) {
            /*
              $message .= "<td>";
              $message .= "Label Created";
              $message .= "</td>";
              $message .= "</tr>";
             */

            $message .= "<tr>";
            $message .= "<td>";
            $message .= $trackingNumber['account'];
            $message .= "</td>";
            $message .= "<td>";
            $message .= "<a href='" . SETTING_MAIN_URL . "/main/tracking.php?tracking_number=" . $trackingNumber['awb'] . "'>" . $trackingNumber['awb'] . "</a>";
            $message .= "</td>";
            $message .= "</tr>";
        }



        $message .= "</table>";

        $message .= "<br>";
        $message .= "<br>";
        $message .= "Kind Regards";
        $message .= "<br>";
        $message .= "ITSupport";


        mail($to, $subject, $message, $headers);

        echo "Send Email to Customer for Return Shipments <br>";
    }
}

?>
