<?php
require_once("../includes/settings/local_settings/smarttrack_test.php");
require_once("../includes/settings/local_settings/local_oneworldexpress_co_uk.php");
require_once("../includes/settings/local_settings/staging_oneworldexpress_co_uk.php");
require_once("../includes/settings/config.inc.php");
include_classes([
    'paymentshistory.class'
    ]);
include_classes([
    'paymentshistoryfilter.class'
    ]);
//require_once("../includes/library/dbaccess3.class.php");
//require_once '../includes/mapping/paymentshistory.class.php';
//require_once '../includes/mapping/paymentshistoryfilter.class.php';
// CONFIG: Enable debug mode. This means we'll log requests into 'ipn.log' in the same directory.
// Especially useful if you encounter network errors or other intermittent problems with IPN (validation).
// Set this to 0 once you go live or don't require logging.
define("DEBUG", 1);
define("LOG_FILE", "./ipn.log");

// Read POST data
// reading posted data directly from $_POST causes serialization
// issues with array data in POST. Reading raw POST data from input stream instead.
$raw_post_data = file_get_contents('php://input');
$filename = 'logfile.txt';
file_put_contents($filename, $raw_post_data);

$raw_post_array = explode('&', $raw_post_data);
$myPost = array();
foreach ($raw_post_array as $keyval) {
    $keyval = explode('=', $keyval);
    if (count($keyval) == 2) {
        if ($keyval[0] === 'payment_date') {
            if (substr_count($keyval[1], '+') === 1)
                $keyval[1] = str_replace('+', '%2B', $keyval[1]);
        }
        $myPost[$keyval[0]] = urldecode($keyval[1]);
    }
}
// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';
//print_r($_POST);
if (function_exists('get_magic_quotes_gpc')) {
    $get_magic_quotes_exists = true;
}
foreach ($myPost as $key => $value) {
    if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
        $value = urlencode(stripslashes($value));
    } else {
        $value = urlencode($value);
    }
    $req .= "&$key=$value";
}
$dump = '';
$dump .= 'Request =>';
$dump .= $req;
$dump .= '  ';
$dump .= 'Response =>';
// Post IPN data back to PayPal to validate the IPN data is genuine
// Without this step anyone can fake IPN data

if(PAYPAL_LIVE) {
    $paypal_url = PAYPAL_LIVE_URL;
} else {
    $paypal_url = PAYPAL_SANDBOX_URL;
}

$ch = curl_init($paypal_url);
if ($ch == FALSE) {
    return FALSE;
}

curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);

if (DEBUG == true) {
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
}

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
$dump .= $res;
if (curl_errno($ch) != 0) { // cURL error
    if (DEBUG == true) {
        error_log(date('[Y-m-d H:i e] ') . "Can't connect to PayPal to validate IPN message: " . curl_error($ch) . PHP_EOL, 3, LOG_FILE);
    }
    curl_close($ch);
    exit;
} else {
    // Log the entire HTTP response if debug is switched on.
    if (DEBUG == true) {
        error_log(date('[Y-m-d H:i e] ') . "HTTP request of validation request:" . curl_getinfo($ch, CURLINFO_HEADER_OUT) . " for IPN payload: $req" . PHP_EOL, 3, LOG_FILE);
        error_log(date('[Y-m-d H:i e] ') . "HTTP response of validation request: $res" . PHP_EOL, 3, LOG_FILE);
    }
    curl_close($ch);
}

// Inspect IPN validation result and act accordingly
// Split response headers and payload, a better way for strcmp
$tokens = explode("\r\n\r\n", trim($res));
$res = trim(end($tokens));

if (strcmp($res, "VERIFIED") == 0) {
    //echo 'vaified';
    //print_r($_POST);    exit();
    //die("Dead here");
    
    //////////////////////////values from paypal///////////
    // 		$item_name        = $_POST['item_name'];
    $item_number = $_POST['item_number'];
    // 		$payment_status   = $_POST['payment_status'];
    $payment_amount = $_POST['amount'];
    $payment_currency = $_POST['currency'];
    $txn_id = $_POST['txn_id'];
    $payment_status = $_POST['payment_status'];
    // 		$receiver_email   = $_POST['receiver_email'];
    // 		$payer_email      = $_POST['payer_email'];		
    ////////////////////////////////////////////////

    if (isset($item_number) && $item_number != "") {
        $paymenthistoryObj = new PaymentsHistory($item_number);
        if($payment_amount == $paymenthistoryObj->getAmount()){
            if (isset($payment_status) && $payment_status != 'Refunded') {
                $paymenthistoryObj->setTxnId($txn_id);
                $paymenthistoryObj->setPaymentStatus("completed");
                $paymenthistoryObj->setIsCompleted("yes");
            }
            $paymenthistoryObj->Save();
        }
        //$resText = $payment_amount . "---new balance " . $newUpdatedBalance; ///testing///   
        //email after complete         
        //$RecipientsList = array("cs@oneworldexpress.com", "ops@oneworldexpress.com", "irshadali18@gmail.com");
        //SendPaypalPaymentNotification($customerId, $customerFullName, $customerEmail, $cpid, $payment_amount, $payment_currency, $RecipientsList);
    }

    if (DEBUG == true) {
        error_log(date('[Y-m-d H:i e] ') . "Verified IPN: $req " . PHP_EOL, 3, LOG_FILE);
    }
} else if (strcmp($res, "INVALID") == 0) {

    // log for manual investigation
    // Add business logic here which deals with invalid IPN messages
    if (DEBUG == true) {
        error_log(date('[Y-m-d H:i e] ') . "Invalid IPN: $req" . PHP_EOL, 3, LOG_FILE);
    }
}
?>

