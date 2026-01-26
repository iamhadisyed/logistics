<?php

require_once("../includes/settings/config.inc.php");

$url = "api.dpd.co.uk/user?action=login";
$username = "DSmith";
$password = "MYPassWd66";
$loginCredentail = base64_encode("$username:$password");
echo "Authorization: Basic " . $loginCredentail;
$headers = array(
    "Content-Type: application/json",
    "Authorization: Basic " . $loginCredentail);


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$result = curl_exec($ch);
print_r($result);
$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
echo "HTTP: " . $status_code . "\n";
die;
?>