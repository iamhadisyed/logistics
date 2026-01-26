<?php

/*  © 2013 eBay Inc., All Rights Reserved */ 
/* Licensed under CDDL 1.0 -  http://opensource.org/licenses/cddl1.php */
?>
<?php require_once('get-common/keys.php') //include keys file for auth token and other credentials ?> 
<?php require_once('get-common/eBaySession.php') //include session file for curl operations ?> 
<?php
require_once("../includes/settings/config.inc.php");
//SiteID must also be set in the Request's XML
//SiteID = 0  (US) - UK = 3, Canada = 2, Australia = 15, ....
//SiteID Indicates the eBay site to associate the call with
$siteID = 3;
$userToken = $_SESSION['detail']['USER_TOKEN'];
//the call being made:
$verb = 'CompleteSale';

//Time with respect to GMT
//by default retreive orders in last 30 minutes
//$CreateTimeFrom = gmdate("2014-05-01"); //current time minus 30 minutes
//$CreateTimeTo = gmdate("2014-08-14");


//If you want to hard code From and To timings, Follow the below format in "GMT".
$CreateTimeFrom = '2014-06-01T20:34:44.000Z'; //GMT
$CreateTimeTo = '2014-08-19T20:34:44.000Z'; //GMT

//Build the request Xml string

$hawbNumber	=	$_POST['hawb'];
$congignmentCheck	=	new ConsignmentFilter();
$congignmentCheck->addHawbFilter($hawbNumber);
if( $congignmentCheck->getCount()>0)
{
	$rowlist		=	$congignmentCheck->getList();
	$rowlistData	=	$rowlist[0];
}
else
{
	echo 'ERROR||NO consignment found for submitting information on ebay';
	die;
}

$handling		=	$rowlistData->getHandling();//'OneWorldExpress';
$serviceFilter	=	new ServiceFilter();
$serviceFilter->addSCodeFilter($handling);
if( $serviceFilter->getCount()>0)
{
	$rowslist		=	$serviceFilter->getList();
	$serviceData	=	$rowslist[0];
	
}
else
{
	echo 'ERROR||NO carrir data found for submitting information on ebay';
		die;
}
$storeName		=	'OneWorldExpress';
$hawb			=	$rowlistData->getHawb();//'OneWorldExpress';
$carrier		=	$serviceData->getCarrier();
$serviceType	=	$serviceData->getName();
$awb			=	$rowlistData->getAwb();//'OneWorldExpress';
$id				=	$rowlistData->getId();//'OneWorldExpress';
$transactionid  =   $rowlistData->getTransactionId();
$itemid         =   $rowlistData->getReference();

if($transactionid != $_POST["transactionId"])
{
	$transactionid = $_POST["transactionId"];
	//$itemid		   = $_POST["itemid"];	
}
//$feedback_text = 'good buyer';

$requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>';
$requestXmlBody .= '<CompleteSaleRequest xmlns="urn:ebay:apis:eBLBaseComponents">';

$requestXmlBody .= "<ItemID>$itemid</ItemID>";
$requestXmlBody .= "<TransactionID>$transactionid</TransactionID>";
$requestXmlBody .= "<Shipment>";
$requestXmlBody .= "<ShipmentTrackingDetails>";
$requestXmlBody .= "<ShipmentTrackingNumber>$awb</ShipmentTrackingNumber>";
$requestXmlBody .= "<ShippingCarrierUsed>$carrier</ShippingCarrierUsed>";
$requestXmlBody .= "</ShipmentTrackingDetails>";
$requestXmlBody .= "</Shipment>";

$requestXmlBody .= "<Shipped>true</Shipped>";
$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
$requestXmlBody .= '</CompleteSaleRequest>';

//print_r($requestXmlBody); die;
///Create a new eBay session with all details pulled in from included keys.php
$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $siteID, $verb);

//send the request and get response
$responseXml = $session->sendHttpRequest($requestXmlBody);
if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
    die('<P>Error sending request');

//Xml string is parsed and creates a DOM Document object
$responseDoc = new DomDocument();
$responseDoc->loadXML($responseXml);


//get any error nodes
$errors = $responseDoc->getElementsByTagName('Errors');
$response = simplexml_import_dom($responseDoc);


//$entries = $response->PaginationResult->TotalNumberOfEntries;

foreach($response as $result=>$val)
{
	if(trim($result)=="Ack")
	{
		//echo $val; die;		
		if($val=="Success" || $val=="SUCCESS")
		{			
			$rowlistData->setStatus(CONSIGNMENT::STATUS_DISPATCHED);
			$rowlistData->save();
		}
		else
		{
			$rowlistData->setStatus(CONSIGNMENT::STATUS_INVALID);
			$rowlistData->setMessage();
			$rowlistData->save();
		}
	}
}
exit;
?>
