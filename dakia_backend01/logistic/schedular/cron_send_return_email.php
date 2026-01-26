<?php

require_once(__DIR__ . "/../includes/settings/config.inc.php");

include_classes([
    'services.class',
    'servicesfilter.class',
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'parcel.class',
    'parcelfilter.class',
    'country.class',
    'countryfilter.class',
    'notfoundrecord.class',
    'notfoundrecordfilter.class',
]);
DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo $date = "2019-09-19"; 
$consignmentFilter = new ConsignmentFilter();
$consignmentFilter->addJoin("user u", " u.id = c.user_id");
$consignmentFilter->addFilterNew("     c.user_id in (select id from user where user_account_id in (select id from user_account where allow_return_email = 1)) and  c.date_created >= '2019-09-19' and  c.shipment_status  in ('27')");
$consignmentList = $consignmentFilter->getListNew("c.awb, c.user_id, u.user_name as username");
$trackingNumbersArray = array();
if (count($consignmentList) > 0) {
    $count = 0;
    foreach ($consignmentList as $consignment) {
        $trackingNumbersArray[$count]['awb'] = $consignment->getAwb();	
        $trackingNumbersArray[$count]['userName'] = $consignment->getUsername();	
        $trackingNumbersArray[$count++]['user_id'] = $consignment->getUserId();
    }
    EmailSendingToCustomers($trackingNumbersArray);
}

function EmailSendingToCustomers($trackingNumbersArray) {
    if (count($trackingNumbersArray) > 0) {
        ////////////////////////// EMAIL SENDING TO PARENT CUSTOMERS /////////////////////////////////////////////////
        $userId = array();
        foreach($trackingNumbersArray as $trackingUserId){
            $userId[] =  $trackingUserId["user_id"];
        }
        $struserId = "'" . implode("','", array_unique($userId)) . "'";
        echo "me h ere";
        echo $sql = "SELECT 
                u.user_name, ua.email, ua.alternative_email
                FROM
                customer_account ua 
                inner join   user u on u.user_account_id = ua.id
                WHERE
                ua.id = (SELECT 
                u.user_account_id
                FROM
                user u
                WHERE
                u.id IN (".$struserId."))
                GROUP BY ua.id";

        $result = DbAccess3::runQuery($sql);
       
        $accountEmailAddress = array();

        while ($row = $result->fetch_assoc()) {
            $email = $row['email'] . "," . $row['alternative_email'];
            if ($email != '')
            SendEmail($trackingNumbersArray, $email);
        }

    }
}

function SendEmail($trackingNumbersArray, $email) {
    if (count($trackingNumbersArray) > 0) {


        //$to = $email;
        $to = "mruga@oneworldexpress.com";
        $subject = 'Return Shipments List : ' . date("d-m-Y");
        $headers = "From: ITSupport@oneworldexpress.com \r\n";
        $headers .= "Reply-To: ITSupport@oneworldexpress.com \r\n";
        $headers .= "CC: ITSupport@oneworldexpress.com, ops@oneworldexpress.com, finance@oneworldexpress.com, cs@oneworldexpress.com\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        $message .= 'Dear All,<br><br>';
        $message .= "Please find the below list of return shipments which we processed into our warehouse on " . date("d-m-Y") . ". According to our Returns shipment                         policy, goods are stored locally for 30 days and not returned immediately unless pre-arranged. 
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
            $message .= "<tr>";
            $message .= "<td>";
            $message .= $trackingNumber['userName'];
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
        echo $message;
        echo "Send Email to Customer for Return Shipments <br>";
    }
}

?>