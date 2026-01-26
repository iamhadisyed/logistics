<?php
require_once('get-common/eBaySession.php');
require_once('get-common/keys.php');
require_once("../includes/settings/config.inc.php");

$siteID = 3;
$verb = 'FetchToken';

$sessionUser = SessionManager::getUser();
$account = $sessionUser->getAccount(); 
//echo $_SESSION["ses"] = 'om8CAA**74dbfde514c0a6248424c7b4fffffdc2';
$ses = $_SESSION["ses"];
$requestXmlBody='<?xml version="1.0" encoding="utf-8"?>
<FetchTokenRequest xmlns="urn:ebay:apis:eBLBaseComponents">
<SessionID>'.trim($ses).'</SessionID>
</FetchTokenRequest>';

$session = new eBaySession($userToken, $devID, $appID, $certID, 
											$serverUrl, $compatabilityLevel, $siteID, $verb);
	//echo $devID; exit;										
$responseXml = $session->sendHttpRequest($requestXmlBody);
//print_r($responseXml); exit;
if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
					die('<P>Error sending request');
				
				//Xml string is parsed and creates a DOM Document object
				$responseDoc = new DomDocument();
				$responseDoc->loadXML($responseXml);	
				
				//get any error nodes
				$errors = $responseDoc->getElementsByTagName('Errors');
				$response = simplexml_import_dom($responseDoc);
				
				
				//echo $response->eBayAuthToken; exit;
				$myfile = fopen("file_token".$account.".txt", "w");
				fwrite($myfile, $response->eBayAuthToken);
				fclose($myfile);
	
				$myfile = fopen("file_token".$account.".txt", "r");
				$token = fread($myfile, filesize("file_token".$account.".txt"));
				fclose($myfile);
				unlink("file_token".$account.".txt");
				//echo $token.'sdgsdfg'; exit;
				$_SESSION['detail']['USER_TOKEN'] 		=	$token;

				util_redirect("https://www.oneworldexpress.co.uk/remote/main/ebay_list.php");				
?> 