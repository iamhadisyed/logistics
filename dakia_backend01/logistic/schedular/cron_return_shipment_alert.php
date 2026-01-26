<?php
require_once(__DIR__ . "/../includes/settings/config.inc.php");
// Get Today returned shipments

$getValidConQuery = " SELECT
                          c.awb,
                          c.`date_created`,
                          ua.`alternative_email`,
                          ua.`user_account`,
                          ua.`id`
                        FROM
                          customer_account ua
                          JOIN `user` u
                            ON u.`user_account_id` = ua.`id`
                          JOIN `consignment` c
                            ON u.id = c.`user_id`
                        WHERE ua.return_shipment_allow = 1
                          AND DATE(c.`date_created`) = CURDATE()
                          AND shipment_status = '27' ORDER BY ua.`id`";
$getValidConResult = DbAccess3::runQuery($getValidConQuery);
if (mysqli_num_rows($getValidConResult) > 0) {
    //$to = "kazim@oneworldexpress.com";
    $dataArr = [];
    while ($rowData  = mysqli_fetch_array($getValidConResult)) {
        $dataArr[$rowData['id']][] = [
                                        "awb"=>$rowData['awb'],
                                        "date_created"=>$rowData['date_created'],
                                        "alternative_email"=>$rowData['alternative_email'],
                                        "user_account"=>$rowData['user_account'],
                                        "id"=>$rowData['id']
                                    ];
    }
    $subject = 'Return Shipments List : ' . date("d-m-Y");

    //$subject = 'Return Shipments List : ' . $date;

    $headers = "From: itsupport@oneworldexpress.com \r\n";
    $headers .= "Reply-To: itsupport@oneworldexpress.com \r\n";
    $headers .= "CC: itsupport@oneworldexpress.com, ops@oneworldexpress.com, finance@oneworldexpress.com, cs@oneworldexpress.com\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    if(count($dataArr) > 0) {
        foreach ($dataArr as $accountId => $data) {
            $message = "";
            $message .= 'Dear All,<br><br>';
            $message .= "Please find the below list of return shipments which we processed into our warehouse on " . date("d-m-Y") . ". According to our Returns shipment policy, goods are stored locally for 30 days and not returned immediately unless pre-arranged. 
			             During that period, please provide instructions for these shipments:
						 <br><br>
						 <ul>
						 <li>Return the goods to origin</li>
						 <li>Re-send the goods to customer or alternate address</li>
						 <li>Destroy the items. This may incur a destruction cost</li>
						 <li>Goods can/will be sold to defray expenses where applicable, does not apply to damaged goods non-saleable</li>
						 </ul>
						 <b>Note:</b>
						 <ul>
						 <li>
						 You will be 100% responsible for checking tracking online through the SMART TRACK system Notification cannot be guaranteed 
						 </li>
 						 <li>
                          These terms may be enforced without notification where the goods have been in our care or in storage for more than ninety days 	                          (90 Days).
						 </li> 
						 <li>
                         	Admin and Handling fees may apply. Speak to your account manager for details.
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

            $messageHtml = "";
            $to = $data[0]['alternative_email'];
            if(count($data) > 0) {
                foreach ($data as $singleEntry) {
                    $messageHtml .= "<tr>";
                    $messageHtml .= "<td>";
                    $messageHtml .= $singleEntry['user_account'];
                    $messageHtml .= "</td>";
                    $messageHtml .= "<td>";
                    $messageHtml .= "<a target='_blank' href='" . SETTING_MAIN_URL . "/main/tracking.php?tracking_number=" . $singleEntry['awb'] . "'>" . $singleEntry['awb'] . "</a>";
                    $messageHtml .= "</td>";
                    $messageHtml .= "</tr>";

                }
            }
            $message .= $messageHtml;
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
}