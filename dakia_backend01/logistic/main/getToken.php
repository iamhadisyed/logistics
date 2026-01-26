<?php
require_once('get-common/eBaySession.php');
require_once('get-common/keys.php');
require_once("../includes/settings/config.inc.php");

$requestXmlBody = '<?xml version="1.0" encoding="utf-8"?>';
$requestXmlBody .= '<GetSessionIDRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
$requestXmlBody .= '<RuName>Oneworld-Oneworld-9f72-4-qvpeup</RuName>';
$requestXmlBody .= '</GetSessionIDRequest>';

$siteID = 3;
//the call being made:
$verb = 'GetSessionID';	
$sessionUser = SessionManager::getUser();
$account = $sessionUser->getAccount(); 
//echo $account; exit;
$session = new eBaySession($userToken, $devID, $appID, $certID, 
											$serverUrl, $compatabilityLevel, $siteID, $verb);

$responseXml = $session->sendHttpRequest($requestXmlBody);
//echo 'dfsdfsdf'; exit;
if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
					die('<P>Error sending request');

				//Xml string is parsed and creates a DOM Document object
				$responseDoc = new DomDocument();
				$responseDoc->loadXML($responseXml);	
				
				//get any error nodes
				$errors = $responseDoc->getElementsByTagName('Errors');
				$response = simplexml_import_dom($responseDoc);
				$ses = $response->SessionID;	
				//echo $ses; die;
				$myfile = fopen("file_session".$account.".txt", "w");
				fwrite($myfile, $ses);
				fclose($myfile);
				
				$myfile = fopen("file_session".$account.".txt", "r");
				$_SESSION["ses"] = fread($myfile, filesize("file_session".$account.".txt"));
				fclose($myfile);// die;				
				//echo $_SESSION["ses"]; exit;
				unlink("file_session".$account.".txt");
util_redirect("https://signin.ebay.com/ws/eBayISAPI.dll?SignIn&RuName=Oneworld-Oneworld-9f72-4-qvpeup&SessID=".$ses);
?>

 