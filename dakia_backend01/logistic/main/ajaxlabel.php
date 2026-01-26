<?php
require_once("../includes/settings/config.inc.php");
include_classes([
    'pdfmerger'
    ], 'labels');
include_classes([
    'tcpdf'
    ], '3rdparty/tcpdf');
include_classes([
    'carrierservice.class'
    ], 'general');
include_classes([
    'include_list',
    
    ], 'reamus');
include_classes([
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'consignmentcharges.class',
    'consignmentchargesfilter.class',
    'userservicesrouting.class',
    'userservicesroutingfilter.class',
    'tariffs.class',
    'tariffsfilter.class',
    'serviceagentmapping.class',
    'serviceagentmappingfilter.class',
    'services.class',
    'servicefilter.class',
    'serviceconstantvalue.class',
    'serviceconstantvaluefilter.class',
    'country.class',
    'countryfilter.class',
    'servicerangemapping.class',
    'servicerangemappingfilter.class',
    'licenceplate.class',
    'licenceplatefilter.class',
    'parcel.class',
    'parcelfilter.class',
    'warehouse.class',
    'warehousefilter.class',
    'tracking.class',
    'trackingdata.class',
    'trackingdatafilter.class',
    'consignmenthscode.class',
    'consignmenthscodefilter.class',
    'paymentshistory.class',
    'paymentshistoryfilter.class',

    ]);

$error_list = array();
$msg = "";
$left_to_print = 0;


if(isset($_POST['action']) && trim($_POST['action']) == 'GET_SERVICE_DROPDOWN')
{
 	$servieType			=	$_POST['service_type'];//	=	'';
	$servieTypeOption	=	isset($_POST['service_type_option'])?$_POST['service_type_option']:'';//	=	'';
	// Domestic service starts from here
	$p_filter = new CustomizedServicesRoutingfilter();
	$p_filter->addUserIdFilter(Sessionmanager::getUser()->getId());
	$p_filter->addStatusFilter('active');
	if(trim($servieTypeOption)== '')
	{
		if ($servieType == Consignment::SERVICE_DOMESTIC)
			$p_filter->addTypeFilter(Consignment::SERVICE_DOMESTIC);
		else if ($servieType == Consignment::SERVICE_EUROPE_ROAD ) 
			$p_filter->addTypeFilter(Consignment::SERVICE_EUROPE_ROAD);
		else if ($servieType == Consignment::SERVICE_INTERNATIONAL ) 
			$p_filter->addTypeFilter(Consignment::SERVICE_INTERNATIONAL);
		else if ($servieType == Consignment::SERVICE_RETURN ) 
			$p_filter->addTypeFilter(Consignment::SERVICE_RETURN);
	}
	else
	{	
		if(trim($servieType) != Consignment::SERVICE_DOMESTIC)	
		{
			$p_filter->addTypeArrayFilter(array(Consignment::SERVICE_INTERNATIONAL,Consignment::SERVICE_EUROPE_ROAD));
//			$p_filter->addTypeFilter(Consignment::SERVICE_EUROPE_ROAD);
		}
	}
	if(trim($servieTypeOption)!= '')
	{
		$p_filter->addFieldFilter('service_type', 'B', 'ser.');
	}
	$p_list3 = $p_filter->getUserAllowServiceList();
	$servicecodes	=	array();
	if(count($p_list3)>0 )
	{
		foreach ($p_list3 as $partner)
		{				
		   $servicecodes[] = $partner->getServiceName();            
		}
	}
		echo json_encode($servicecodes	);
		die;
}

if(isset($_POST['action']) && trim($_POST['action']) == 'CHECK_HAWB')
{
	$responseAraay	=	array();
	$hawb	=	trim($_POST['hawb']);
	$filter = new ConsignmentFilter();
		$filter->addFieldFilter('hawb', $hawb);
		$filter->addFilter(" c.consignment_status <> 'recycled'");
		$consignmentListData	=	$filter->getColumnList("id, hawb");
		if(count($consignmentListData)>0)
		{
			$responseAraay['STATUS']	=	'ERROR';
			$responseAraay['MESSAGE']	=	'HAWB already exist, Please change HAWB and try again';
		}
		else
		{
			$responseAraay['STATUS']	=	'SUCCESS';
			$responseAraay['MESSAGE']	=	'';
		}
		
		echo json_encode($responseAraay	);
		die;

}

if(isset($_POST['action']) && trim($_POST['action']) == 'FIND_REMOTE_AREA_POSTCODE')
{
	
	$postcode	=	str_replace(' ','',	$_POST['postcode']);
	$user = SessionManager::getUser();
	if(isset($_POST['postcode']) && (int)$postcode == $postcode)
	{
		
	
		$filterRemote		=	new RemoteareaUserMappingFilter();
		$remotearesPostCode	=	$filterRemote->getRemoteAreaPostCodeFilter( $postcode, $user->getUserAccount());
		
		if(count($remotearesPostCode)>0)
		{
			if($remotearesPostCode[0]->getId() > 0)
			{
				echo 'SUCCESS';
			}
			else
			{
				echo 'FAILED - 2';
			}
		}
	}
	else
	{
		echo 'FAILED - 1 ';
	}
	die;
}
// is this form being posted back?
if(isset($_POST['action']) && trim($_POST['action']) == 'CollectionEmail')
{
	$id = $_POST["id"];
	$linkToEmail = str_replace("..","http://oneworldexpress.co.uk/remote", $_POST["label_link"]);
	$subject = "DHL Collection";
	$from = "ITSupport@oneworldexpress.com";
	$message = "Hi, \n\nThanks for using the collection service. Following is the label link of collection Service: \n"
			   .$linkToEmail. ".\n\nThank you. \n\nKind Regards \n\nIT Support\n\n If you find any problem please contact 
			   your local DHL office";
			   
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'Cc:'.$ccemail . "\r\n";
	$headers .= 'From: Finance OneWorldExpress <finance@oneworldexpress.com>' . "\r\n";
			
	mail('IT Support Collection<itsupport@oneworldexpress.com>', $subject, "<font face='Calibri'><span style='font-size:12px !important;'>".$message."</span></font>", $headers, "-f$from");
			
	//mail("ITSupport@oneworldexpress.com", $subject, $message,$header, "-f$from");
												
												
	$consignment = new consignment($id);
												
	$consignment->setEmailCheck("Y");
	
	$consignment->save();

}


if(isset($_POST['action']) && trim($_POST['action']) == 'GENERATELABEL')
{
    
    if (isset($_POST["consignmentid"]) && trim($_POST["consignmentid"]) != '')
    {
        $consignmentid  =	(int)$_POST["consignmentid"]; // getting HAWB
        $outputArray        =   array();
        
        // Determine number of labels need to be generated
        $consignment = new Consignment($consignmentid);
        //$filter->addServiceTypeFilter("DBP");
//echo $consignment->getShipmentStatus(); 
//echo Consignment::STATUS_READY_TO_PRINT;
//die;

        if(trim($consignment->getShipmentStatus())!= Consignment::STATUS_READY_TO_PRINT)
        {
            $outputArray['status']      =   'ERROR';
            $outputArray['MESSAGE']     =   'Either consignment is invalid or label has not been generated by smart system against this HAWB Number';
        }
        else
        {
            $outputArray['status']      =   'SUCCESS';
            $labelArray                 =    array();
            
            $labelReturn    =   Consignment::getInstantLabel($consignment,'pdf','100x150', false,false,true);
            if($labelReturn['STATUS'] == "ERROR"){
                $labelArray['STATUS'] = 'ERROR';
                $labelArray['MESSAGE'] = $labelReturn['MESSAGE'];
                $outputArray['DATA'][] = $labelArray;
                echo json_encode($outputArray);
                die;
            }
            if($consignment->getShipmentStatus()  ==   Consignment::STATUS_INVALID )
            {
                $labelArray['STATUS']       =   'ERROR';
                $labelArray['MESSAGE']      =   $consignment->getMessage();
            }
            else
            {
                if($consignment->getIsWhiteLabel()>0)
                        $singleLabel = str_replace (".pdf","_overlabel.pdf",$consignment->getlabelFile());
                    else
                        $singleLabel = $consignment->getlabelFile();
                    
                $labelArray['STATUS']   =   'SUCCESS';
                $labelArray['URL'] = "../main/client_list.php?show=printed";
                $labelArray['INSTANT_LABEL'] = $singleLabel;
                $labelArray['AWB'] = $consignment->getAwb();
                $labelArray['ID'] = $consignment->getId();
                $labelArray['HAWB'] = $consignment->getHawb();
                if( $consignment->getRemoteCharges() == '1')
                    $labelArray['MESSAGE'] = $consignment->getHawb()." is a remote area. It will be charged as remote area.";
            }
            $outputArray['DATA'][] = $labelArray;
        }

        echo json_encode($outputArray);
        die;
    }
}
else if(isset($_POST['action']) && trim($_POST['action']) == 'GET_SHIPMENT_JSON')
{
        $user           =   SessionManager::getUser();
        $outputArray    =   array();
        $conFilter = new ConsignmentFilter();
        if($user->getUserType() == User::USER_TYPE_CLIENT)
            $conFilter->addFilter("    c.user_id = '" . $user->getId() . "' AND   c.shipment_status = '" . Consignment::STATUS_READY_TO_PRINT . "'");
        else {
            $userId       =   (int)(trim(util_get('uaccount'))!='')?base64_decode( util_get('uaccount')):$user->getId();
            $userobject =   new User($userId);
            $conFilter->addFilter("    c.user_id in ( select id from user where user_account_id = '" . $userobject->getUserAccountId() . "')  AND  c.shipment_status = '" . Consignment::STATUS_READY_TO_PRINT . "'");
        }
//        $conFilter->addFilter("     c.user_id = '".$userId."' and c.shipment_status = '".Consignment::STATUS_READY_TO_PRINT."'");
        $shipmentIds    =   $conFilter->getColumnList('c.id');
        if(count($shipmentIds)>0)
        {
            $outputArray['STATUS']  =   'SUCCESS';
            $outputArray['DATA']  =   array();
            foreach($shipmentIds as $shipmetnId){
                $outputArray['DATA'][]  =   $shipmetnId->getId();
            }
            
        }
        else
        {
            $outputArray['STATUS']  =   'ERROR';
            $outputArray['DATA']  =   'Either consignment(s) is invalid or has been label generated';
            
        }
        echo json_encode($outputArray) ;
        die;
}
else if(isset($_POST['action']) && trim($_POST['action']) == 'GET_LABEL_WITHID_JSON')
{
        $user           =   SessionManager::getUser();
        $outputArray    =   array();
        $shipmentsIds   =   $_POST['shipment_id'];
        $userId       =   (int)(trim(util_get('uaccount'))!='')?base64_decode( util_get('uaccount')):$user->getId();
        $conFilter = new ConsignmentFilter();
        /*AND c.user_id = '".$userId."'*/
        $conFilter->addFilter("     c.id in ('".implode("','",$shipmentsIds)."')  AND c.label_file <> '' and c.shipment_status not in ( '".Consignment::STATUS_READY_TO_PRINT."', '".Consignment::STATUS_RECYCLED."', '".Consignment::STATUS_INVALID."')");
        $shipmentIds    =   $conFilter->getColumnList('c.hawb, c.label_file,c.is_white_label');
        
        if(count($shipmentIds)>0)
        {
            $outputArray['STATUS']  =   'SUCCESS';
            $outputArray['DATA']  =   array();
            foreach($shipmentIds as $shipmetnId){
                if(trim($shipmetnId->getLabelFile()) != '')
                {
                    if($shipmetnId->getIsWhiteLabel()>0)
                        $singleLabel = str_replace (".pdf","_overlabel.pdf",$shipmetnId->getlabelFile());
                    else
                        $singleLabel = $shipmetnId->getlabelFile();
                    
                    $outputArray['DATA'][]  =   $singleLabel; //$shipmetnId->getLabelFile();
                }
            }
        }
        else
        {
            $outputArray['STATUS']  =   'ERROR';
            $outputArray['DATA']  =   'Either consignment(s) is invalid or label has not been generated';
            
        }
        echo json_encode($outputArray) ;
        die;
}
else if(isset($_POST['action']) && trim($_POST['action']) == 'GET_SHIPMENT_WITHID_JSON') {
        $user           =   SessionManager::getUser();
        $outputArray    =   array();
        $shipmentsIds   =   $_POST['shipment_id'];
        $conFilter = new ConsignmentFilter();
        if($user->getUserType() == User::USER_TYPE_CLIENT)
            $conFilter->addFilter("    c.user_id = '" . $user->getId() . "' AND c.id in ('".implode("','",$shipmentsIds)."')  AND   c.shipment_status = '" . Consignment::STATUS_READY_TO_PRINT . "'");
        else
            $conFilter->addFilter("    c.user_id in ( select id from user where user_account_id = '" . $user->getUserAccountId() . "') AND c.id in ('".implode("','",$shipmentsIds)."')  AND  c.shipment_status = '" . Consignment::STATUS_READY_TO_PRINT . "'");


    //$conFilter->addFilter("    c.id in ('".implode("','",$shipmentsIds)."') AND c.user_id = '".$user->getId()."' and c.shipment_status = '".Consignment::STATUS_READY_TO_PRINT."'");
        $shipmentIds    =   $conFilter->getColumnList('c.id');
        if(count($shipmentIds)>0)
        {
            $outputArray['STATUS']  =   'SUCCESS';
            $outputArray['DATA']  =   array();
            foreach($shipmentIds as $shipmetnId){
                $outputArray['DATA'][]  =   $shipmetnId->getId();
            }
            
        }
        else
        {
            $outputArray['STATUS']  =   'ERROR';
            $outputArray['DATA']  =   'Either consignment(s) is invalid or label has not been generated';
            
        }
        echo json_encode($outputArray) ;
        die;
}


else if(isset($_POST['action']) && trim($_POST['action']) == 'GENERATELABELMERGE')
{
    $consignment_labels	=	array_unique($_REQUEST['labels']);
    
    if(count($consignment_labels)>0)
    {
            $pdf2 = new PDFMerger();
            $prefix = date("Ymd_");
            $mergeFileName = uniqid($prefix) . ".pdf";
            foreach($consignment_labels as $labelFile)
            {
                if(trim($labelFile) != '' && file_exists(SETTING_DIR_ASSETS ."pdf/". $labelFile))
                {
                    $pdf = "";
                     $pdf = SETTING_URL_LABEL_RELATIVE.$labelFile;
                    $pdf2->addPDF($pdf, 'all');
                }
            }
            $filePatch  =   date('Y_m_d').'/'.$mergeFileName;
            $newFileVarUrls	=	SETTING_URL_LABEL.$filePatch ;
            $newFileVar	=	SETTING_URL_LABEL_RELATIVE.$filePatch;
            $pdf2->merge('file',$newFileVar);
            
            $outputArray['STATUS']  =   'SUCCESS';
            $outputArray['MESSAGE']  =   'Please <a class="btn btn-primary" href="'.$newFileVarUrls.'" target="_blank" >click here</a> to open  your labels ';
    }
    else
    {
            $outputArray['STATUS']  =   'ERROR';
            $outputArray['MESSAGE']  =   'We have encounter an issue while merging the labels.';
    }
    echo json_encode($outputArray);
    die;
}
else if(isset($_POST['action']) && trim($_POST['action']) == 'SHOWLABEL')
{
    	if (isset($_POST["consignmentid"]) && trim($_POST["consignmentid"]) != '')
	{
            
            $outputArray=   array();
            $user = SessionManager::getUser();
            $arr_status = array(CONSIGNMENT::STATUS_LABEL_CREATED, CONSIGNMENT::STATUS_RECEIVED, CONSIGNMENT::STATUS_PARTIAL_RECEIVED,CONSIGNMENT::STATUS_DISPATCHED,CONSIGNMENT::STATUS_PARTIAL_DISPATCHED, CONSIGNMENT::STATUS_INTRANSIT, CONSIGNMENT::STATUS_DELIVERED, CONSIGNMENT::STATUS_PARTIAL_DELIVERED,CONSIGNMENT::STATUS_CLOSE);
             $consignmentId	=	$_POST["consignmentid"];
            // Determine number of labels need to be generated
            $consignment = new Consignment($consignmentId);
            if(!in_array(trim($consignment->getShipmentStatus()),$arr_status))
            {
                $outputArray['STATUS']  =   'ERROR';
                $outputArray['MESSAGE']  =   'Either consignment is invalid or label has not been generated by smart system against this HAWB Number';
            }
            else
            {
                
                if($consignment->getIsWhiteLabel()>0)
                        $singleLabel = str_replace (".pdf","_overlabel.pdf",$consignment->getlabelFile());
                    else
                        $singleLabel = $consignment->getlabelFile();
                        
                //$singleLabel	=	$consignment->getLabelFile();
                if(trim($singleLabel) == '')
                {
                    $outputArray['STATUS']  =   'ERROR';
                    $outputArray['MESSAGE']  =   "Label doesn't exist in our system";
                }
                else
                {
                    $outputArray['STATUS']      =   'SUCCESS';
                    $outputArray['LABEL']     =   SETTING_URL_LABEL.$singleLabel;
                }
            }
            echo json_encode($outputArray)	;
	}
        
        die;
}

if(isset($_POST['action']) && trim($_POST['action']) == 'SERVICEINFORMATIONPOPULATE')
{
		$serviceName		=	str_replace('+',' ',$_POST["servicename"]);
		$serviceFilter = new ServiceFilter();
		$serviceFilter->addName2SFilter(trim($serviceName));		
		$serviceList = $serviceFilter->getList();
		if($serviceFilter->getCount() > 0)
		{
			echo $serviceList[0]->getDescription();
		}
}
if(isset($_POST['action']) && trim($_POST['action']) == 'COUNTRYPOPULATION')
{
	$enabledCountry	=	array();
	if (isset($_POST["servicename"]) && trim($_POST["servicename"]) != '')
	{
		 $serviceName		=	str_replace('+',' ',$_POST["servicename"]);
		 $selectedCountry	=	$_POST["country"];
		
		
		$CustomizedServicesRoutingFilter	=	new CustomizedServicesRoutingFilter();
		$CustomizedServicesRoutingFilter->addUserIdFilter(Sessionmanager::getUser()->getId());
		$CustomizedServicesRoutingFilter->addSpecialServicesFilter();
		$CustomizedServicesRoutingFilter->addSerServiceNameFilter($serviceName);
                $CustomizedServicesRoutingFilter->addStatusFilter('active');
		$partnerserviCountry			=	$CustomizedServicesRoutingFilter->getCountrytAllList();
		
		
		
			
		$serviceFilter = new ServiceFilter();
		$serviceFilter->addName2SFilter(trim($serviceName));		
		$serviceList = $serviceFilter->getList();
		if($serviceFilter->getCount() > 0)
		{
			$handelingCode 	=	 $serviceList[0]->getCode();
			$CustomizedServicesRoutingFilter	=	new CustomizedServicesRoutingFilter();
			$CustomizedServicesRoutingFilter->addUserIdFilter(Sessionmanager::getUser()->getId());
			$CustomizedServicesRoutingFilter->addSpecialServicesFilter();
			$CustomizedServicesRoutingFilter->addServiceNameFilter($handelingCode);
                        $CustomizedServicesRoutingFilter->addStatusFilter('active');
			$partnerserviCountry	=	$CustomizedServicesRoutingFilter->getCountrytList();
			if(count($partnerserviCountry))
			{
				//	echo count($partnerserviCountry);
				foreach($partnerserviCountry as $countryDataP)
				{
					$enabledCountry[]	=	trim($countryDataP->getCountry());
				}
				//echo count($enabledCountry);
			}
		//	print_r($enabledCountry);
		//	die;
		//	print_r($sountryList); exit;
			//$countryIsoList	=	str_replace(',',"','",$sountryList);
			$countryNameList	=	implode("','",$enabledCountry);
			
			//print_r($enabledCountry);
				//die;
		foreach($serviceList	 as $getCountryList)
		{
			$sountryList	=	$getCountryList->getServiceCountry();
			if($sountryList == 'GB')
			{
				$countryFilter = new CountryFilter();
				//$countryFilter->addIsoFilter($countryIsoList);		
				$countryFilter->addNameArrayFilter($countryNameList);		
				
				if($countryFilter->getCount()>0)
				{
					echo '<option value="">Select Country</option>';
					$CountryAllList = $countryFilter->getList();
					foreach ($CountryAllList as $countryListdata)
					{
						if(trim(strtoupper($selectedCountry)) == strtoupper($countryListdata->getName()))
							echo '<option value="'.strtoupper($countryListdata->getName()).'" selected="selected">'.strtoupper($countryListdata->getName()).'</option>';
						else
							echo '<option value="'.strtoupper($countryListdata->getName()).'">'.strtoupper($countryListdata->getName()).'</option>';
					}
				}
				else
				{
					echo '<option value="">Select Country</option>';
				}
			}
			else if($sountryList == 'ALL')
			{
				$countryFilter = new CountryFilter();
				//$countryFilter->addIsoFilter($countryIsoList);		
				$countryFilter->addNameArrayFilter($countryNameList);		
				
				if($countryFilter->getCount()>0)
				{
					echo '<option value="">Select Country</option>';
					$CountryAllList = $countryFilter->getList();
					foreach ($CountryAllList as $countryListdata)
					{
						if(trim(strtoupper($selectedCountry)) == strtoupper($countryListdata->getName()))
							echo '<option value="'.strtoupper($countryListdata->getName()).'" selected="selected">'.strtoupper($countryListdata->getName()).'</option>';
						else
							echo '<option value="'.strtoupper($countryListdata->getName()).'">'.strtoupper($countryListdata->getName()).'</option>';
					}
				}
				else
				{
					echo '<option value="">Select Country</option>';
				}
			}
			elseif(trim($sountryList) != '')
			{
				$countryIsoList	=	str_replace(',',"','",$sountryList);
				$countryFilter = new CountryFilter();
				//$countryFilter->addIsoFilter($countryIsoList);		
				$countryFilter->addNameArrayFilter($countryNameList);		
				
				$countryFilter->addIsoFilter($countryIsoList);		
				
				if($countryFilter->getCount()>0)
				{
					echo '<option value="">Select Country</option>';
					$CountryAllList = $countryFilter->getList();
					foreach ($CountryAllList as $countryListdata)
					{
						if(trim(strtoupper($selectedCountry)) == strtoupper($countryListdata->getName()))
							echo '<option value="'.strtoupper($countryListdata->getName()).'" selected="selected">'.strtoupper($countryListdata->getName()).'</option>';
						else
						{
							echo '<option value="'.strtoupper($countryListdata->getName()).'">'.strtoupper($countryListdata->getName()).'</option>';							
						 }
						/*if(trim(strtoupper($selectedCountry)) == "United Kingdom")
							echo '<option value="United Kingdom" selected="selected">'.strtoupper("United Kingdom").'</option>';
						else*/
					}
				//	if()
					{
						//echo '<option value="United Kingdom">'.strtoupper("United Kingdom").'</option>';
					}
				}
				else
				{
					echo '<option value="">Select Country</option>';
				}
			}
		}
		}
		else
		{
			echo '<option value="">Select Country</option>';
		}
	}
	else
	{
		echo '<option value="">Select Country</option>';
	}
}


if(isset($_POST['action']) && trim($_POST['action']) == 'POPULATEREGION')
{

				$countryFilter = new CountryFilter();
				
				//$countryFilter->addIsoFilter($countryIsoList);		
				$countryFilter->addNameFilter(trim($_POST['servicename']));		
				$countryData	=	$countryFilter->getColumnList('region');		
				
				if(count($countryData)>0)
				{
					echo 	$countryData[0]->getRegion();
				}
			die;
}


if(isset($_POST['action']) && trim($_POST['action']) == 'COUNTRYCollectionReceiver')
{
   			    $selectedCountry	=	$_POST["country"];
				$countryFilter = new CountryFilter();
				$countryName = $countryFilter->getColumnList('name');		
				if(count($countryName)>0)
				{
					echo '<option value="">Select Country</option>';
					foreach ($countryName as $countryListdata)
					{
						if(trim(strtoupper($selectedCountry)) == strtoupper($countryListdata->getName()))
							echo '<option value="'.strtoupper($countryListdata->getName()).'" selected="selected">'.strtoupper($countryListdata->getName()).'</option>';
						else
							echo '<option value="'.strtoupper($countryListdata->getName()).'">'.strtoupper($countryListdata->getName()).'</option>';
					}
				}
}


if(isset($_POST['action']) && trim($_POST['action']) == 'COUNTRYCollectionSender')
{
	
    $selectedCountry	=	$_POST["country"];
	$serviceName 		= $_POST['servicename'];
	if($serviceName=='DHL Europe Air Express (ECX)')
	{
		$regionCollection = 'EU'; 
	}
	else if(trim($serviceName)=='UKMail @ Next Day')
	{
		$regionCollection = 'DBP'; 
	}
	

	$countryFilter = new CountryFilter();
	if($regionCollection=='EU')
	{
		$countryFilter->addRegionCollection('EU');
	}
	else if($regionCollection=='DBP')
	{
		
		$countryFilter->addRegionFilter('\'DBP\'');
	}
	else
	{
		//$countryFilter->addRegionCollection(array('AP','AM'));
		//$countryFilter->addRegionCollection('AM');					
	}
	$countryFilter->addTypeFilter('C');
	$countryName = $countryFilter->getColumnList('name');
	echo count($countryName);
	//print_r($countryName); exit;
	//$countryName = $countryFilter->getColumnList('name');		
	if(count($countryName)>0)
	{
		echo '<option value="">Select Country</option>';
		foreach ($countryName as $countryListdata)
		{
			if(trim(strtoupper($selectedCountry)) == strtoupper($countryListdata->getName()))
				echo '<option value="'.strtoupper($countryListdata->getName()).'" selected="selected">'.strtoupper($countryListdata->getName()).'</option>';
			else
				echo '<option value="'.strtoupper($countryListdata->getName()).'">'.strtoupper($countryListdata->getName()).'</option>';
		}
	}
}

if(isset($_POST['action']) && trim($_POST['action']) == 'GETCARRIERSERVICETYPES')
{
		$carrierName	=	$_POST["carrierName"];
		$selectArray	=	array('psr.user_id', 'psr.carrier', 'psr.service_name', 'ser.name', 'ser.code'); 
		$tablel2		=	'customizedservicesrouting as psr';
		$tablel1		=	'services as ser';
		$joiningString	=	'ser.code = psr.service_name';
		$groupBy		=	'group by ser.name';
		$orderBy		=	'ORDER BY ser.name ASC';
		$user 			= SessionManager::getUser();
		$userId			=	$user->getId();
		$carrierFilter  = new ServiceFilter();
		$carrierFilter->getUniqueCarrierfilter();
		$carrierFilter->addcoulmnFilter('psr.user_id', $userId	);
		
		$carrierFilter->addCarrierFilter( $carrierName);
//$carrierFilter->getInnerJoinCount($selectArray,$tablel1,$tablel2,$joiningString, $groupBy, $orderBy);

	if($carrierFilter->getInnerJoinCount($selectArray,$tablel1,$tablel2,$joiningString, $groupBy, $orderBy)>0)
	{
		echo '<option value="">Select Service</option>';
		$carrierAllList = $carrierFilter->getInnerJoinList($selectArray,$tablel1,$tablel2,$joiningString, $groupBy, $orderBy);
		foreach ($carrierAllList as $carrierListdata)
		{
			echo '<option value="'.$carrierListdata->getCode().'">'.$carrierListdata->getName().'</option>';
		}
	}
	else
	{
		echo '<option value="">Select Service</option>';
	}

}
if(isset($_POST['action']) && trim($_POST['action']) == 'COUNTRYREGION')
{
	if (isset($_POST["regionname"]) && trim($_POST["regionname"]) != '')
	{
			$regionName			=	$_POST["regionname"];
			$selectedCountry	=	$_POST["country"];
			if(trim($regionName) == 'RTN')
				$regionName		=	'R1';
			else if(trim(strtoupper($regionName)) == 'INTERNATIONAL'){
				$regionName		=	'INT';
			}
			else if(trim(strtoupper($regionName)) == 'DOMESTIC'){
				$regionName		=	'DBP';
			}
			else if(trim(strtoupper($regionName)) == 'EUROPE ROAD'){
				$regionName		=	'R1';
			}
			else if(trim(strtoupper($regionName)) == 'RETURN'){
				$regionName		=	'R1';
			}
			else
			{
				$regionName	;
			}
			
				
			
			
				$countryFilter = new CountryFilter();
				$countryFilter->addRegionFilter( "'".$regionName."'");
				if($countryFilter->getCount()>0)
				{
					echo '<option value="">Select Country</option>';
					$CountryAllList = $countryFilter->getList();
					foreach ($CountryAllList as $countryListdata)
					{
						if(trim(strtoupper($selectedCountry)) == strtoupper($countryListdata->getName()))
							echo '<option value="'.strtoupper($countryListdata->getName()).'" selected="selected">'.strtoupper($countryListdata->getName()).'</option>';
						else
							echo '<option value="'.strtoupper($countryListdata->getName()).'">'.strtoupper($countryListdata->getName()).'</option>';
					}
				}
				else
				{
					echo '<option value="">Select Country</option>';
				}
			
			
	}
	else
	{
		echo '<option value="">Select Country</option>';
	}
	
}

if(isset($_POST['action']) && trim($_POST['action']) == 'ROUTINGCOUNTRY')
{	
				$CountrySelected	=	$_POST['country'];
				$region				=	$_POST['region'];
			
				$countryFilter = CustomizedServicesRouting::getRoutingCountryList(Sessionmanager::getUser()->getAccount(), $region);
		
				if(sizeof($countryFilter) > 0)
				{
					echo '<option value="">Select Country</option>';
					
					foreach ($countryFilter as $countryListdata)
					{
							if($CountrySelected == strtoupper($countryListdata->getCountry()) )
								echo '<option value="'.strtoupper($countryListdata->getCountry()).'" selected="selected">'.strtoupper($countryListdata->getCountry()).'</option>';
							else
								echo '<option value="'.strtoupper($countryListdata->getCountry()).'">'.strtoupper($countryListdata->getCountry()).'</option>';
								
					}
				}
				else
				{
					echo '<option value="">Select Country</option>';
				}
			
			
}
	
	function getOverwritePath($fileName)
	{
		$path = "../_assets/relabel/" . date("Y_m_d", time()) . "/";

		if (!file_exists($path)) @mkdir($path, 0775);

		return $path . $fileName;
	}
		



function getFullPath($fileName)
	{
		$path = "../_assets/pdf/" . date("Y_m_d", time()) . "/";

		if (!file_exists($path)) @mkdir($path, 0775);

		return $path . $fileName;
	}

	

	