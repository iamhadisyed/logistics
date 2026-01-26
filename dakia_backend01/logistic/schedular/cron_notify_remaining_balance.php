<?php
$localSettingsFile = isset($_SERVER["HTTP_HOST"]) ? $_SERVER["HTTP_HOST"] : '';
if (substr($localSettingsFile, 0, 4) == "www.")
    $localSettingsFile = substr($localSettingsFile, 4);
$localSettingsFile = str_replace(".", "_", $localSettingsFile);
if (empty($localSettingsFile)) {
    $localSettingsFile = $_SERVER["SCRIPT_NAME"];
    if (strpos($localSettingsFile, "beta") !== false) {
        $localSettingsFile = "beta_smarttrack_co";
    } else if (strpos($localSettingsFile, "staging") !== false) {
        $localSettingsFile = "staging_smarttrack_co";
    } else if (strpos($localSettingsFile, "local") !== false) {
        $localSettingsFile = "local_oneworldexpress_co_uk";
    } else {
        $localSettingsFile = "smarttrack_co";
    }
}
require_once(__DIR__ . "/../includes/settings/config.inc.php");
include_classes([
    'useraccount.class',
    'useraccountfilter.class',
    'paymentshistory.class',
    'paymentshistoryfilter.class',
    'currency.class',
    'currencyfilter.class',
    'consignmentcharges.class',
    'consignmentchargesfilter.class'
]);
if (isset($_GET['debugcron']) && $_GET['debugcron'] == 'yes') {
    error_reporting(E_ALL);
    ini_set("display_errors", "1");
    $debug = true;
} else {
    $debug = false;
}


$userAccountFilterObj = new UserAccountFilter();
$userAccountFilterObj->addFieldFilter('active_flag', '1');
$userAccountFilterObj->addFieldFilter('parentid', '2301');
//$userAccountFilterObj->addFilter('credit_limit > 0');
$userAccountData = $userAccountFilterObj->getColumnList(' id, is_prepaid,email,billing_email,user_account,full_name, parentid, billing_currency, company, parentid, credit_limit, balance_alert_percentage, email', $debug);
$emailData = array();
$parentData = [];
if (count($userAccountData) > 0) {
    foreach ($userAccountData as $accoutnkey => $accountData) {
        $accountId = $accountData->getId();
        $accountName = $accountData->getUserAccount();
        $accountOwnerName = $accountData->getFullName();
        $accountCompany = $accountData->getCompany();
        $accountParentId = $accountData->getParentId();
        $is_prepaid = $accountData->getIsPrepaid();
        $accountBillingCurrency = $accountData->getBillingCurrency();
        $credit_limit = $accountData->getCreditLimit();
        $parentid = $accountData->getParentid();
        $email = $accountData->getBillingEmail();
        if ($is_prepaid == 1)
            $balance_alert_percentage = ((int) $accountData->getBalanceAlertPercentage() <= 0) ? 200 : $accountData->getBalanceAlertPercentage();
        else
            $balance_alert_percentage = ((int) $accountData->getBalanceAlertPercentage() <= 0) ? 20 : $accountData->getBalanceAlertPercentage();

        $balance = getBalance($accountId);
        //print_r($balance );
        //die;
        /* echo $accountBillingCurrency." - ". print_r($balance,true).
          ", ".$accountId.
          ", ".$accountName.
          ", ".$accountOwnerName.
          ", ".$accountCompany.
          ", ".$accountParentId."<br>";
         */
        $balancePercentage = trim(str_replace("GBP", "", $balance['balance']));
        if($is_prepaid != 1 && $credit_limit > 1){
            $percentageBalnce = number_format((($balancePercentage / $credit_limit) * 100),2);
        } else {
                $percentageBalnce = "";
        }
        $includeInArray = false;
        if ($percentageBalnce <= $balance_alert_percentage && $is_prepaid <> 1) {
            $includeInArray = true;
        } else if ($balancePercentage <= $balance_alert_percentage && $is_prepaid == 1) {
            $includeInArray = true;
        }
        if ($includeInArray) {
            $emailData[$accountId]['balance_percentage'] = $percentageBalnce;
            $emailData[$accountId]['balance'] = $balance['balance'];
            $emailData[$accountId]['outstanding'] = $balance['outstanding'];
            $emailData[$accountId]['credit_limit'] = $credit_limit;
            $emailData[$accountId]['full_name'] = $accountOwnerName;
            $emailData[$accountId]['company'] = $accountCompany;
            $emailData[$accountId]['parentid'] = $accountParentId;
            $emailData[$accountId]['is_prepaid'] = $is_prepaid;
            $emailData[$accountId]['account'] = $accountName;
            $emailData[$accountId]['email'] = $email;
            if (!isset($parentData[$parentid]['PARENT_DATA'])) {
                $parentDetails = new CustomerAccount($parentid);
                $parentData[$parentid]['PARENT_DATA']["email"] = $parentDetails->getBillingEmail();
                $parentData[$parentid]['PARENT_DATA']["full_name"] = $parentDetails->getFullName();
                $parentData[$parentid]['PARENT_DATA']["company"] = $parentDetails->getCompany();
                $parentData[$parentid]['PARENT_DATA']["user_account"] = $parentDetails->getUserAccount();
            }
            
            $parentData[$parentid]['DATA'][$accountId] = $emailData[$accountId];
        }
    
            }
}
//$debug = true;
//////////////////sending email data//////////////////
if (count($parentData) > 0) {
    foreach ($parentData as $parentDataKey => $parentDataValue) {


        $parentDataDetails = $parentDataValue['PARENT_DATA'];
        $to = $parentDataDetails['email'].",itsupport@oneworldexpress.com"; //"itsupport@oneworldexpress.com,finance@oneworldexpress.com";
        $parentName = $parentDataDetails['full_name']; //"itsupport@oneworldexpress.com,finance@oneworldexpress.com";
        $parentAccount = $parentDataDetails['user_account']; //"itsupport@oneworldexpress.com,finance@oneworldexpress.com";
        $parentDataSubaccountDetails = $parentDataValue['DATA'];
        $htmlContentFinance = "";
        $htmlContentFinance .= '<html> 
                            <head> 
                                <title>SmartTrack</title> 
                            </head> 
                            <body> 
                                <h3>SmartTrack Low Balance Alert!</h3>                            
                                <p>Dear Finance Team,</p>
                             
                <p>Please find below list for your child account with low balance.</p>
<table cellspacing="0" style=" width: 100%;"> 
                                    <tr style="background-color: #e0e0e0;"> 
                                        <th align="left">Account Name</th> 
                                        <th>Contact Name</th>
                                        <th>Company</th> 
                                        <th>Email</th>
                                        <th>Account Type</th>' .
                (($parentDataValue['is_prepaid'] != 1) ? '<th>Credit Limit</th>' : '') .
                '<th>Balance Remain</th>' .
                (($parentDataValue['is_prepaid'] != 1) ? '<th>Balance Percentage</th>' : '') .
                (($parentDataValue['is_prepaid'] != 1) ? '<th>Not Invoiced Shipment</th>' : '') .
                '</tr> ';
        $subject = $parentAccount . " -- smarttrack.co portal remaining balance alert";

        if ($parentDataSubaccountDetails > 0) {
            foreach ($parentDataSubaccountDetails as $subAccountKey => $subAccountValue) {

                $fullName = $subAccountValue['full_name'];
                $customerAccount = $subAccountValue['account'];
                $customercompany = $subAccountValue['company'];
                $customerTo = $subAccountValue['email'];
                if($subAccountValue['is_prepaid'] != 1 && $subAccountValue['credit_limit'] <=0 && $subAccountValue['balance_percentage'] <=0 && $subAccountValue['outstanding'] <= 0 )
                    continue;
                
                $htmlContentFinance .= '<tr style="text-align:center;" > 
                                <td>' . $customerAccount . '</td> 
                                <td>' . $fullName . '</td>
                                <td>' . $customercompany . '</td> 
                                <td>' . $subAccountValue['email'] . '</td>' .
                        (($subAccountValue['is_prepaid'] != 1) ? '<td>POSTPAID</td>' : '<td>PREPAID</td>') .
                        (($subAccountValue['is_prepaid'] != 1) ? ( '<td>' . number_format($subAccountValue['credit_limit'],2) . '</td>') : '<td> </td>') .
                        '<td>' . $subAccountValue['balance'] . '</td>' .
                        (($subAccountValue['is_prepaid'] != 1) ? ('<td>' . (trim($subAccountValue['balance_percentage'])!= ''? number_format($subAccountValue['balance_percentage'],2)."%":'') . '</td>') : '<td> </td>') .
                        (($subAccountValue['is_prepaid'] != 1) ? ('<td>' . number_format($subAccountValue['outstanding'],2) . '</td>') : '<td> </td>') .
                        '</tr>';

                $customerSubject = $customerAccount . " -- smarttrack.co portal remaining balance alert";
                $htmlContentCustomer = '<html> 
                            <head> 
                                <title>SmartTrack</title> 
                            </head> 
                            <body> 
                                <h3>SmartTrack Low Balance Alert!</h3>                            
                                <p>Dear ' . $fullName . ',</p>
                           <p>You have below balance in you SMARTTRACK portal account, Please review your account balance below.</p>
                           <b>Account:</b> ' . $customerAccount . '<br>
                           <b>Company:</b> ' . $customercompany . '<br>
                           <b>Account Type:</b> ' . (($subAccountValue['is_prepaid'] != 1) ? 'POSTPAID' : 'PREPAID') . '<br>
                           <b>Balance: </b> ' . number_format($subAccountValue['balance'],2) . '<br>' .
                        (($subAccountValue['is_prepaid'] != 1) ? '<b>Balance Percentage</b>: ' . number_format($subAccountValue['balance_percentage'],2) . '%' : '') . '<br>' .
                        (($subAccountValue['is_prepaid'] != 1) ? '<b>Outstand Balance</b>: <td>' . number_format($subAccountValue['outstanding'],2) . '' : '') . '<br>' .
                        '
                           <br>Thanks & Regards 
                           <br>SmartTrack Portal Team 
                           ';


                if ($debug) {
                    echo $htmlContentCustomer . "<br>------------------------------<br>";
                       die;
                } else {
                    // Set content-type header for sending HTML email                    
                    $headers = "From: smart@smarttrack.co \r\n";
                    $headers .= "Reply-To: smart@smarttrack.co \r\n";
                    $headers .= "MIME-Version: 1.0\r\n";
                    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

                    // Send email 
                   /* if (mail($customerTo, $customerSubject, $htmlContentCustomer, $headers)) {
                        echo 'Email has sent successfully to ' . $customerTo . '.<br>';
                    } else {
                        echo 'Email sending failed  to ' . $customerTo . '.<br>';
                    }*/
                }
            }
        }
        $htmlContentFinance .= ' 
                                </table>
                                <br>
                           <br>Thanks & Regards 
                           <br>SmartTrack Portal Team 
                            </body> 
                            </html>';
        if ($debug) {
            echo $htmlContentFinance . "<br>------------------------------<br>";
            die;
        } else {
            // Set content-type header for sending HTML email                    
            $headers = "From: smart@smarttrack.co \r\n";
            $headers .= "Reply-To: smart@smarttrack.co \r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

            // Send email 
            if (mail($to, $subject, $htmlContentFinance, $headers)) {
                echo 'Email has sent successfully to ' . $to . '.<br>';
            } else {
                echo 'Email sending failed  to ' . $to . '.<br>';
            }
        }
    }
}
die;
?>