<?php

require_once("../includes/settings/config.inc.php");

global $allContent;

$fileLine = 1;
putContents(($fileLine++) ." - ". date('Y-M-d, H:i:s')." --PageStart-- <br />", 'w');

$raw_post_data = file_get_contents('php://input');
$raw_post_array = explode('&', $raw_post_data);
$myPost = array();
foreach ($raw_post_array as $keyval) {
    $keyval = explode('=', $keyval);
    if (count($keyval) == 2)
        $myPost[$keyval[0]] = urldecode($keyval[1]);
}

$somecontent = "";
foreach ($myPost as $keys => $value)
    $somecontent.= $keys . " = " . $value . "\n";

putContents(($fileLine++) ." - ". date('Y-M-d, H:i:s') ." -- " . $somecontent . '<br />');

// No ipn post means this script does not exist
if (!@$myPost['txn_type']) {
    putContents(($fileLine++) . " - " .date('Y-M-d, H:i:s')." -- sending 404 not found header <br />");
    @header("Status: 404 Not Found");
} else {
    @header("Status: 200 OK");  // Prevents ipn reposts on some servers
    putContents(($fileLine++) . " - " .date('Y-M-d, H:i:s') . " -- Status: 200 OK <br />");

    // Add "cmd" to prepare for post back validation
    // Read the ipn post from paypal or eliteweaver uk
    // Fix issue with php magic quotes enabled on gpc
    // Apply variable antidote (replaces array filter)
    // Destroy the original ipn post (security reason)
    // Reconstruct the ipn string ready for the post
    $postipn = 'cmd=_notify-validate'; // Notify validate
    foreach ($myPost as $ipnkey => $ipnval) {
        //if (get_magic_quotes_gpc())
        //  $ipnval = stripslashes ($ipnval); // Fix issue with magic quotes
        // ^ Antidote to potential variable injection and poisoning
        //if (!eregi("^[_0-9a-z-]{1,30}$",$ipnkey) || !strcasecmp ($ipnkey, 'cmd'))
        //  unset ($ipnkey); unset ($ipnval); 
        // Eliminate the above
        if (@$ipnkey != '') { // Remove empty keys (not values)
            putContents(($fileLine++) . " - " .date('Y-M-d, H:i:s') . " -- found key-" . $ipnkey . '=' . $ipnval . '<br />');

            @$_PAYPAL[$ipnkey] = stripslashes($ipnval); // Assign data to new global array
            //unset ($myPost); // Destroy the original ipn post array, sniff...
            $postipn.='&' . @$ipnkey . '=' . urlencode(stripslashes(@$ipnval));
        }
    } // Notify string

    $error = 0; // No errors let's hope it's going to stays like this!

    putContents(($fileLine++) . " - " .date('Y-M-d, H:i:s') . " -- after loop" . '<br />');

    // IPN validation mode 1: Live Via PayPal Network
    //$live = ($_PAYPAL['custom'] == '6ab7685da29b27de852aaa04d85b1e47') ? false: PAYPAL_LIVE;
    $domain = (PAYPAL_LIVE) ? "www.paypal.com" : "www.sandbox.paypal.com";

    putContents(($fileLine++) . " - " .date('Y-M-d, H:i:s') . " -- posting to " . $domain . '<br />');

    @set_time_limit(60); // Attempt to double default time limit incase we switch to Get

    putContents(($fileLine++) . " - " .date('Y-M-d, H:i:s') . " --Post back the reconstructed instant payment notification " . '<br />');

    // Post back the reconstructed instant payment notification

    if (PAYPAL_LIVE == true) {
        $paypal_url = "https://www.paypal.com/cgi-bin/webscr";
    } else {
        $paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
    }

    $ch = curl_init($paypal_url);
    if ($ch == FALSE) {
        return FALSE;
    }

    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postipn);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);

//    if(DEBUG == true) {
//        curl_setopt($ch, CURLOPT_HEADER, 1);
//        curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
//    }
    // CONFIG: Optional proxy configuration
    //curl_setopt($ch, CURLOPT_PROXY, $proxy);
    //curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
    // Set TCP timeout to 30 seconds
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));

    // CONFIG: Please download 'cacert.pem' from "http://curl.haxx.se/docs/caextract.html" and set the directory path
    // of the certificate as shown below. Ensure the file is readable by the webserver.
    // This is mandatory for some environments.
    //$cert = __DIR__ . "./cacert.pem";
    //curl_setopt($ch, CURLOPT_CAINFO, $cert);

    $res = curl_exec($ch);
    if (curl_errno($ch) != 0) { // cURL error
        putContents(($fileLine++)  . " - " .date('Y-M-d, H:i:s'). " --Can't connect to PayPal to validate IPN message: " . curl_error($ch) .'<br />');
        curl_close($ch);
        exit;
    } else {        
        // Log the entire HTTP response if debug is switched on.
        putContents(($fileLine++) . " - " .date('Y-M-d, H:i:s') . " --HTTP request of validation request:: " . curl_getinfo($ch, CURLINFO_HEADER_OUT) ." for IPN payload: ".$postipn. '<br />');
        putContents(($fileLine++) . " - " .date('Y-M-d, H:i:s') . " --HTTP response of validation request: $res ". '<br />');
        curl_close($ch);
    }

    $tokens = explode("\r\n\r\n", trim($res));
    $response = trim(end($tokens));
    
    // Also required on some environments
    // uncomment '#' to assign posted variables to local variables
    #extract($_PAYPAL); // if globals is on they are already local
    // and/or >>>
    // refer to each ipn variable by reference (recommended)
    // $_PAYPAL['receiver_email']; etc... (see: ipnvars.txt)
    // IPN was confirmed as both genuine and VERIFIED
    putContents(($fileLine++) . " - " .date('Y-M-d, H:i:s') . " --IPN was confirmed and now check both genuine and VERIFIED:" . $response . '<br />');
    if (!strcmp($response, "VERIFIED")) {
        putContents(($fileLine++) . " - " .date('Y-M-d, H:i:s') . " --IPN has as both genuine and VERIFIED" . '<br />');
        putContents(($fileLine++) . " - " .date('Y-M-d, H:i:s') . " --Payment Status: ".$myPost['payment_status'].'<br />');

        if ($myPost['txn_type'] == 'web_accept' && $myPost['payment_status'] == 'Completed') {
            putContents(($fileLine++) . " - " .date('Y-M-d, H:i:s') . " --variableAudit return true" . '<br />');

            $paypal_payment_id = $myPost['invoice'];
            $transaction_id = $myPost['txn_id'];
            
            
            $paymentDetails = new Payment($paypal_payment_id);
            $paymentDetails->setIsCompleted(1);
            $paymentDetails->setTransactionId($transaction_id);
            $paymentDetails->setUpdatedDate(date("Y-m-d H:i:s"));            
            $paymentDetails->save();
            
            putContents(($fileLine++) . " - " .date('Y-M-d, H:i:s') . " --Payment details fetched successfuly  " . var_dump($paymentDetails) . '<br />');
            if (empty($paymentDetails)) {
                putContents(($fileLine++) . " - " .date('Y-M-d, H:i:s') . " -- Payment Not available " . '<br />');
            } else {

                putContents(($fileLine++) . " - " .date('Y-M-d, H:i:s') . " --Payment was from " . $paymentDetails->getCustomerId() . '<br />');

                $amount_currency = new Currency($paymentDetails->getAmountCurrencyId());

                // verify transaction
                //$myPost['custom'] == md5($myPost['invoice'].$myPost['mc_gross_1'].$myPost['mc_currency'].ORDER_SALT) 
                $securityHeader = md5($paymentDetails->getId() . $paymentDetails->getAmount() . $amount_currency->getRightsymbol() . PAYPAL_EMAIL_ADDRESS . ORDER_SALT);
                // Verify security header
                if ($myPost['custom'] == $securityHeader) {
                    putContents(($fileLine++) . " - " .date('Y-M-d, H:i:s') . " -- Payment Security Verified" . '<br />');

                    mail('irshadali18@gmail.com', 'OneWorldExpress paypal Payment', 'Payment ' . $paymentDetails->getAmount() . ' ' . $amount_currency->getRightsymbol() . ' received against ' . $paymentDetails->getId());


                    putContents(($fileLine++) . " - " .date('Y-M-d, H:i:s') . " -- Payment Emails Sent" . '<br />');

                    //redirect('checkout/order_success');
                } else {
                    putContents(($fileLine++) . " - " .date('Y-M-d, H:i:s') . " -- payment Available but Security Failed" .'<br />');
                }
            }
        }
    }
    // Check that the "payment_status" variable is: Completed
    // If it is Pending you may want to inform your customer?
    // Check your db to ensure this "txn_id" is not a duplicate
    // You may want to check "payment_gross" or "mc_gross" matches listed prices?
    // You definately want to check the "receiver_email" or "business" is yours
    // Update your db and process this payment accordingly
    //***************************************************************//
    //* Tip: Use the internal auditing function to do some of this! *//
    //* **************************************************************************************//
    //* Help: if(variableAudit('mc_gross','0.01') &&					 *//
    //* 	     variableAudit('receiver_email','paypal@domain.com') && 			 *//
    //* 	     variableAudit('payment_status','Completed')){ $do_this; } else { do_that; } *//
    //****************************************************************************************//
    // IPN was not validated as genuine and is INVALID
    elseif (!strcmp($response, "INVALID")) {

        // Check your code for any post back validation problems
        // Investigate the fact that this could be a spoofed IPN
        // If updating your db, ensure this "txn_id" is not a duplicate
        putContents(($fileLine++) . " - " .date('Y-M-d, H:i:s') . " --Invalid Response" . '<br />');
    } else { // Just incase something serious should happen!
        putContents(($fileLine++) . " - " .date('Y-M-d, H:i:s') . " --something serious should happen!" . '<br />');
    }
}

putContents(($fileLine++) . " - " .date('Y-M-d, H:i:s') . " -- writing to database ". '<br />');
$fieldsVal = array('payment_id' => $myPost['invoice'],
                    'dump' => file_get_contents(PAYPAL_LOG_FILE),
                    'added_date' => date("Y-m-d H:i:s")
                );
$PaypalDumpObj = new PaypalDump($fieldsVal);
$PaypalDumpObj->save(true);

//$rec = $this->PaypalDump->new_record();
//$rec->order_id = isset($myPost['invoice']) ? $myPost['invoice'] : 0;
//$rec->dump = file_get_contents(PAYPAL_LOG_FILE);
//$rec->date_added = mdate("%Y-%m-%d %h:%i:%s");
//$rec->save();

mail('irshadali18@gmail.com', 'OneWorldExpress paypal Contents ', $allContent);

function putContents($contents, $mode = 'a') {
    // $mode = 'a' for append
    //$LOG_FILE_CONTENTS.= $contents;
    global $allContent;
    $allContent .= $contents;
    if ($fp = fopen(PAYPAL_LOG_FILE, $mode)) {
        if (fwrite($fp, $contents) === FALSE) {
            mail('irshadali18@gmail.com', 'OneWorldExpress paypal put contents write fail', 'can write file');
        }
        fclose($fp);
    } else {
        mail('irshadali18@gmail.com', 'OneWorldExpress paypal put contents open fail', 'can not open file');
    }
}

?>