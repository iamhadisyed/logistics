<?php
require_once("../includes/settings/config.inc.php");
include_classes([
    'pdfmerger'
    ], 'labels');

$error_list = array();
$msg = "";
$left_to_print = 0;
$labelFile 			=	null;

/*Next token means more records*/
if(isset($_POST['action']) && trim($_POST['action']) == 'NextToken')
{
	require_once("../main/amazon_nextToken.php");
	$_SESSION["filter"] = $_SESSION["orders"];
}

/* Generate Single invoice*/
if(isset($_POST['action']) && trim($_POST['action']) == 'GenerateInvoice')
{
		$orderid = $_POST['hawb'];
		$consignment = new ConsignmentFilter();
		$consignment->addHawbFilter($orderid);
		$result = $consignment->getList();
		$description = $result[0]->getDescription();
		//$pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
 		$pdf->SetX(1.0);				
		$glOrderPdf	 = new ProformaPDFamazon($pdf);		
		$glOrderPdf->AddHTML($result,$description,'bulk');
}

if(isset($_POST['action']) && trim($_POST['action']) == 'GenerateBulkInvoice')
{
	$i = 0;
	$pdf2 		=	new PDFMerger();
	//print_r($_SESSION['bulkInvoice']);
	for($i=0; $i<(sizeof($_SESSION['bulkInvoice'])) ; $i++)
	{
		$pdf2->addPDF($_SESSION['bulkInvoice'][$i], 'all');
	}
	
	if(sizeof($_SESSION['bulkInvoice'])>0)
	{		
		$path_to_mergeLabel = getFullPath(time().'merge_amazon_invoice.pdf');
		echo '<a href="'.$path_to_mergeLabel.'" target = "_blank">Bulk Invoice</a>';
		$pdf2->merge('file', $path_to_mergeLabel);		
	}
	unset($_SESSION['bulkInvoice']);	
}


if(isset($_POST['action']) && trim($_POST['action']) == 'GENERATEBULKAMAZONLABEL')
{
	$i = 0;
	$pdfMerger 		=	new PDFMerger();	

	/*for($i=0; $i<(sizeof($_SESSION['BULKLABEL'])) ; $i++)
	{
		if(trim($_SESSION['BULKLABEL'][$i]) != '' && file_exists($_SESSION['BULKLABEL'][$i]))		
			$pdf2->addPDF($_SESSION['BULKLABEL'][$i], 'all');
	}
	*/
	foreach($_SESSION['BULKLABEL'] as $labelLint)
	{
		if(trim($labelLint) != '')		
		{
			$labels	=	str_replace('https://oneworldexpress.co.uk/remote/',$_SERVER['DOCUMENT_ROOT'].'/remote/', $labelLint);
			
			$pdfMerger->addPDF(trim($labels	), 'all');
		}
		
	}
	if(sizeof($_SESSION['BULKLABEL'])>0)
	{		
		$path_to_mergeLabel = 'merge_amazon.pdf';
		echo '<a class="held  btn-primary btn" href="'.$path_to_mergeLabel.'" target = "_blank">Bulk Label Link</a>';
		$pdfMerger->merge('file', $path_to_mergeLabel);		
	}
	//print_r($_SESSION['BULKLABEL']);
	$_SESSION['BULKLABEL'] = array();
	unset($_SESSION['BULKLABEL']);
	//print_r($_SESSION['BULKLABEL']);
}

/*if(isset($_POST['action']) && trim($_POST['action']) == 'Consolidate')
{
	$hawb 		 = $_POST["hawb"];
	$matchedHawb = $_POST["matchedHawb"];
	
	$conFlr 	 = new ConsignmentFilter();
	$conFlr->addHawbFilter($matchedHawb);
	$consignment_array = $conFlr->getColumnList('awb');
	
	foreach ($consignment_array as $consign)
	{
		$awb = $consign->getAwb();	
		$handling = $consign->getHandling();
	}
	echo $awb;
}*/

if(isset($_POST['action']) && trim($_POST['action']) == 'GENERATEAMAZONLABEL')
{
	
	
	if (isset($_POST["hawb"]) && trim($_POST["hawb"]) != '')
	{		
		$index			= $_POST["index"];
		$hawb 			= $_POST["hawb"];
		$weight 		= $_POST["weight"];
		$name 			= $_POST["name"];
		$city 			= $_POST["city"];
		$postcode 		= $_POST["postcode"];
		//mail('pleasant.bright@gmail.com','country','dasd'.$_POST["country"]);
		/*if($country=='Deutschland')
			$country = 'DE';
		else
		if(strtolower($country)=="united kingdom" || $country == 'GB')
			$country = 'GB';
		else*/
		$country 		= $_POST["country"];
		//echo $country;		
		$status 		= $_POST["status"];
		$serviceName 	= $_POST["servicename"];
		$address1		= $_POST["address1"];
		$address2		= $_POST["address2"];
		$address3		= $_POST["address3"];	
		$phone			= $_POST["phone"];
		$transactionid	= @$_POST["transactionid"];
		$currency		= $_POST["currency"];
		$useraccount	= $_POST["useraccount"];
		$username		= $_POST["username"];
		$password		= $_POST["password"];
		$routing		= $_POST["routing"];
		$title			= substr($_POST["title"],0,20);
		$quantity		= $_POST["quantity"];
		
		if($_POST["amount"] != '' && $_POST["amount"] > 0)
			$amount = $_POST["amount"];
		else			
			$amount			= $_POST["amountpaid"];
		
		$reference  	= $_POST["reference"];
		
		//echo $transactionid; exit;
		
		if($routing == '0')
		{
			$serviceFilter = new ServiceFilter();		
			$serviceFilter->addName2SFilter($serviceName);
			
			$result = $serviceFilter->getList();
			$serviceCode = $result[0]->getCode();			
		}
		else
		{
			$serviceCode = '';
		}		
		
		$conFlr = new ConsignmentFilter();
		$conFlr->addFilter(" consignment_status <> 'recycled'");
		$conFlr->addHawbFilter($hawb);
		
		//echo "hawb  " . $hawb;	
		
		//mail('kiran.iftikhar@oneworldexpress.com', 'dasd', $hawb);
		if($conFlr->getCount()<=0)
		{	
			 $consignmentinformation = $hawb."||".$name."||".$name."||".$address1."||".$address2."||".$address3."||"
			.$city."||".$country."||".$postcode."||".$phone."||".$quantity."||".$weight ."||".$title."||".$amount."||"
			.$currency."||".$name."||".$reference."||".$serviceCode.
			"||".$useraccount."||".$username."||IN_SYSTEM_LABEL||".$routing."||||||||||||||||||".$transactionid."||||||||"; 
			
			//mail("kiran.iftikhar@oneworldexpress.com", "EBAY ORDERR", $consignmentinformation);
			//echo $consignmentinformation; exit;
			$client = new SoapClient(null, array(
								'location' => "https://oneworldexpress.co.uk/remote/main/smartsystemintegration.php?wsdl",
								'uri'      => "https://oneworldexpress.co.uk/remote/main/index.php"));
                        
			$results =  $client->__soapCall('getLabelsAmazon', array('consignmentinformation' => $consignmentinformation));
			$arr = explode("||",$results);
			if(trim($arr[0]) == 'SUCCESS')
			{
				$mergeLabel =  str_replace('https://oneworldexpress.co.uk/remote/',' /var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/', $arr[1]);
				//mail("mkazim4u@gmail.com", "merge label", $mergeLabel);
				$_SESSION['BULKLABEL'][] = $mergeLabel;
				echo $results; 
			}
			else
			{
				echo $results; 
			}
			
			exit;
		
		}
		else
		{
			$hawbinSystem = $conFlr->getList();
		//	print_r($hawbinSystem); exit;
			$label_link = $hawbinSystem[0]->getSingleLabel();
			//echo $label_link; exit;
			if(@$label_link !='')
			{
				$_SESSION['BULKLABEL'][] = $label_link;
			}
		}
	}
	
	echo $results;
}

else
if(isset($_POST['action']) && trim($_POST['action']) == 'SHOWLABEL')
{
	if (isset($_POST["consignmentid"]) && trim($_POST["consignmentid"]) != '')
	{
		$arr_status = array(CONSIGNMENT::STATUS_DISPATCHED, CONSIGNMENT::STATUS_LABEL_CREATED, CONSIGNMENT::STATUS_DELIVERED, CONSIGNMENT::STATUS_WAREHOUSE_RECEIVED,CONSIGNMENT::STATUS_PRINTED);
		$hawbNumber	=	$_POST["consignmentid"];
		// Determine number of labels need to be generated
		$filter = new ConsignmentFilter();
		$filter->addStatusEbayArr($arr_status);
		$filter->addHawbFilter_bag($hawbNumber);
		$filter->AddOrderByID();
		//print_r($filter);
		if($filter->getCount()<=0)
		{
			echo 'ERROR||Either consignment is invalid or label has not been generated by smart system against this HAWB Number';
			exit;
		}
		else
		{
			$consignment_array = $filter->getList();
			foreach ($consignment_array as $consignment)
			{
				$labelId		=	$consignment->getPrintedFileId();
				$singleLabel	=	$consignment->getSingleLabel();
				//die;
				if(trim($singleLabel) != '')
				{
					echo $singleLabel;
					die;
				}
				if($labelId<=0)
				{
					echo 'ERROR||Label doesn\'t exist in our system';
					break;
				}
				
				$labelFile = new LabelFile($labelId);
				if(count($labelFile)>0)
					if(file_exists($labelFile->getFullPath()))
					{
						echo $outFile = $labelFile->getFullPath();
						die;
					}
					else
					{
						echo 'ERROR||Consignment Label file has been removed';
						die;
					}
				else
					{
						echo 'ERROR||No label found';
						die;
					}
			
			}
		}
	}
}

function getFullPath($fileName)
	{
		$path = "../_assets/pdf/" . date("Y_m_d", time()) . "/";

		if (!file_exists($path)) @mkdir($path, 0775);

		return $path . $fileName;
	}

	

	