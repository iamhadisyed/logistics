<?php
// get settings
		require_once("../includes/settings/config.inc.php");
	
		
		
		$user = SessionManager::getUser();
		
		if($_POST["action"] == "getManifestId")
		{
			
		
		
		$commentColletion = $_POST["collectionComment"];
		$dateColletion = $_POST["colelctionDate"];
		$dateColletionTo = $_POST["colelctionDateTo"];
		
		if($dateColletion < date("Y-m-d", strtotime("+1 day")))
		{		
			$output['message'] = Translation::GetCaption("ERR_MESSAGE_COLLECTION_DATE");
			$output['response'] = "ERROR";
			echo json_encode($output);
			exit;
		}
		

		$user = SessionManager::getUser();	
		$fromDate = date("Y-m-d", strtotime("-1 month"));					
		$dateTo = date("Y-m-d");
					
		$ManifestDataFilter = new ManifestDataFilter();
		$ManifestDataFilter->addFilter('AND m.collection_date IS NULL AND m.account = "'.$user->getAccount().'" '); 
		$list = $ManifestDataFilter->getColumnList("id, date_created, handling, product, pieces, weight, file_name, pdf_file");
		
		$manifestIdArray = array();
		
		foreach($list as $manifest)
		{
			//echo "manifest id " . $manifest->getId();
			$manifestIdArray[] = $manifest->getId();
		}
		
		$strManifestId = implode(", ", $manifestIdArray);			
		
		$manifestFilter = new ManifestDataFilter();
		$manifestFilter->addIdFilterIn($strManifestId);
		$list = $manifestFilter->getList();

		$pickup = new PickupSmart();
		//$pickup->setPickupNumber($);
		//echo $dateColletion;
		$pickup->setPickupDate(strtotime($dateColletion));
		$pickup->setDateCreated(time());		
		$pickup->setAddressLine1($user->getCollectionAddLine1());
		$pickup->setAddressLine2($user->getCollectionAddLine2());
		$pickup->setAddressLine3($user->getCollectionAddLine3());
		$pickup->setCity($user->getCollectionCity());
		$pickup->setCountry($user->getCollectionCountry());
		$pickup->setPostCode($user->getCollectionPostCode());		
		//print_r($pickup);
		//die;
		//$pickup->setDeliveryNote($deliveryNote);			
		$pickup->save();
		$pickUpId = $pickup->getId();
		
		//print_r($manifestIdArray);

		$pickNoteReport = new PickNoteReport();
		
		
				
		$pickupFileName = $pickNoteReport->SavePDFFile($pickUpId, $manifestIdArray, "Collection");
		$pickup->setCollectionPDF($pickupFileName);
		
		$pickup->save();		

            //echo "collection";
            //die;
            //$manifestFilter =	new Manifest(trim($id));
			
		if (count($list) > 0) 
		{
			foreach ($list as $manifest) 
			{
				$manifest->setCollectionComment($commentColletion);
				$manifest->setCollectionDate(strtotime($dateColletion));
				$manifest->setCollectionDateTo(strtotime($dateColletionTo));
				$manifest->setPickUpId($pickUpId);
				$manifest->save();
			}
			
			$api_response = SendCollectionRequest($pickUpId, $list);
			
			if($api_response == "SUCCESS")
			{
			//echo "<pre>";
			//print_r($manifestFilter);
			//die;
			$subject = " Collection for Manifest Number " . $id . " .";
			$headers = "From: itsupport@oneworldexpress.com \r\n";
			$headers .= "Reply-To: itsupport@oneworldexpress.com \r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
			$headers .= 'Cc: itsupport@oneworldexpress.com; ' . "\r\n";
			$message = 'Dear Sir, <br><br>A collection has been booked for manifest number '.$strManifestId.' on  '. $dateColletion ;
			//mail("kathrin@oneworldexpress.com", $subject, $message, $headers);
			//mail("Martin Fuell<martin.fuell@postconsulting.at>", 'Collection for manifest number '.$id, 'Dear Sir, <br><br>A collection has been booked got manifest number '.$strManifestId.' on  '. $dateColletion, $headers);
			$output['manifest_id'] = $strManifestId;
			$output['delivery_note'] = $pickupFileName;
			$output['response'] = 'SUCCESS';
			}
			else{
				$output['response'] = "ERROR";
				$output['message'] = Translation::GetCaption("COLLECTION_NOT_BOOKED") . " " . $api_response;
				
			}
		} else {
			$output['response'] = 'ERROR';
			$output['message'] = Translation::GetCaption("COLLECTION_NOT_BOOKED");
		}
			
			//print_r($output);
			
            echo json_encode($output);
            die;
		
	
	    }
		
		
		
		
		
		
		if($_POST['action'] == 'put_shipment_on_hold')
		{
			$trackingNumber = trim($_POST['scannumber']);
			$status = $_POST['status'];
			$trackingNumber = ParseTrackingNumber::Parse($trackingNumber);			
			AddShipmentOnHold($trackingNumber, $status);
		}
		
		
		
		
		
		
	   function AddShipmentOnHold($trackingNumber, $status)
	   {
  		   $user = SessionManager::getUser();
		   
		   
		   $output = array();
		   $consignmentFilter = new ConsignmentFilter();	
		   $consignmentFilter->addFieldFilter("awb", $trackingNumber);	
		   $consignmentFilter->addFieldNotFilter("awb", "");
		   $consignmentFilter->addAccountFilter($user->getAccount());
		   //$consignmentFilter->addFieldNotFilter("isinvoiced", "Y");
		  // print_r($consignmentFilter);											
		   $con_list = $consignmentFilter->getColumnList("id, awb, consignment_status");			   
		   
	
		   if(count($con_list) > 0)
		   {
			   
			    $consignmentFilter = new ConsignmentFilter();	
		  	    $consignmentFilter->addFieldFilter("awb", $trackingNumber);	
		   		$consignmentFilter->addFieldNotFilter("awb", "");
		   		$consignmentFilter->addFieldNotFilter("isinvoiced", "Y");
				$consignmentFilter->addAccountFilter();
				$con_list_invoiced = $consignmentFilter->getColumnList("id, awb, consignment_status");	
				
				if(count($con_list_invoiced) > 0)
		   		{
					
					$con = $con_list[0];
					
					TrackingData::putShipmentOnHold($con, $status);
					
					if($status == Consignment::STATUS_HOLD)
						$message = Translation::GetCaption("MSG_SHIPMENT_ADDED_ON_HOLD"); 
					else
						$message = Translation::GetCaption("MSG_SHIPMENT_REMOVED_FROM_HOLD");

					
					$output['response'] = 'success';
					$output['message'] = $message;
					$output['totalonhold'] = TrackingData::getUserOnHoldRecords();
					
				}
				else
				{
					$output['response'] = 'error';
			  		$output['message'] = Translation::GetCaption("MSG_SHIPMENT_ALREADY_INVOICED");					
				}
			   
					   
		   }
		   else
		   {
			   $output['response'] = 'error';
			   $output['message'] = Translation::GetCaption("SHIPMENT_NOT_FOUND");
		   }
		   
		   echo json_encode($output);
	   }
		
		
		
		
		if($_POST['action'] == 'GetPickupDocuments')
		{
			$pickUpId = $_POST["pickupid"];
			$output = array();
			if($pickUpId > 0)
			{
				$pickup = new PickupSmart($pickUpId);		
				$collection_pdf = $pickup->getCollectionPdf();		
				$label_link = '';		
				
				$manifestDataFilter = new ManifestDataFilter();
				$manifestDataFilter->addPickupIdFilter($pickUpId);
				$manifestList = $manifestDataFilter->getColumnList("label_link");			
				if(count($manifestList) > 0)
				{
					$manifest = $manifestList[0];
					$label_link = $manifest->getLabelLink();
				}
				
				$output['collection_pdf'] = "<a target='_blank' href='".str_replace("../", SETTING_MAIN_URL,  $collection_pdf)."'>".Translation::GetCaption("DELIVERY_NOTE")."</a>";				
				$output['label'] = "<a target='_blank' href='".str_replace("../", SETTING_MAIN_URL,  $label_link)."'>".Translation::GetCaption("OUTER_LABEL")."</a>";
				$output['status'] = 'success';				
			}
			else
			{
				$output['message'] = Translation::GetCaption("MSG_PICKUP_ID_NOT_EXIST");
				$output['status'] = 'error';				
			}
			
			echo json_encode($output);
			exit;
			
		}
			
	
		if($_POST['action'] == 'CreateCollection')
		{
			
			$commentColletion = $_POST["collectionComment"];
			$dateColletion = $_POST["colelctionDate"];
			$dateColletionTo = $_POST["colelctionDateTo"];
			$numberOfBoxes = $_POST["number_of_boxes"];
			$conIdArr = $_POST["conIdArr"];
			
			//echo $dateColletion . " " . date("Y-m-d");#
			
			$date1Timestamp = strtotime($dateColletion);
			$date2Timestamp = strtotime( date("Y-m-d"));
 
//Calculate the difference.
			$difference = $date1Timestamp - $date2Timestamp;
			
			//$diff = date_diff($dateColletion, date("Y-m-d"));
			
			
			
			if($difference < 0)
			{		
				$output['message'] = Translation::GetCaption("ERR_MESSAGE_COLLECTION_DATE");
				$output['response'] = "ERROR";
				echo json_encode($output);
				exit;
			}
			
			if(!is_array($numberOfBoxes))
			{
				$output['message'] = Translation::GetCaption("ERR_ENTER_VALID_NUMBER_OF_BOXES");
				$output['response'] = "ERROR";
				echo json_encode($output);
				exit;
			}
				
			$trackingNumberList = array();
			
			if($user->getBagging() == 'YES')
			{
			
				if($_POST["type"] == "collection_all")
				{		
					$manifestId = Manifest::ManifestAll();	
				}
				else
				{
					
					foreach($conIdArr as $conId)
					{
						$consignment = new Consignment($conId);
						$trackingNumberList[] = $consignment->getAwb();
					}					
			
					
					$manifestId = TrackingData::SendEmail($trackingNumberList, $user);	
					
					$strTrackingNumbers = implode("','", $trackingNumberList);
			
					$set_update_columns = " date_endofday = '" . date("Y-m-d G:i:s") . "'";	
												   
					$where = "awb IN ('$strTrackingNumbers')";
					
					Consignment::bulkUpdate($set_update_columns, $where);				
	
				}
			}
			
			
			
			if($manifestId > 0)
			{
				
				//echo "Manifest Id " . $manifestId;
			
				Manifest::CreateBoxes($manifestId, $numberOfBoxes); 
			
				$manifest = new Manifest($manifestId);
				//$manifest->setPieces(count($trackingNumberList));
					
				$dpdlabellink = HandlerbundAgentDispatchLabel::buildPDFDocuments(0, $manifestId);
				//echo $dpdlabellink;
				$manifest->setLabelLink($dpdlabellink);
				
				$pickup = new PickupSmart();
			//$pickup->setPickupNumber($);
				//echo $dateColletion;
				$pickup->setPickupDate(strtotime($dateColletion));
				$pickup->setDateCreated(time());		
				$pickup->setAddressLine1($user->getCollectionAddLine1());
				$pickup->setAddressLine2($user->getCollectionAddLine2());
				$pickup->setAddressLine3($user->getCollectionAddLine3());
				$pickup->setCity($user->getCollectionCity());
				$pickup->setCountry($user->getCollectionCountry());
				$pickup->setPostCode($user->getCollectionPostCode());
						
				//print_r($pickup);
				//die;
				//$pickup->setDeliveryNote($deliveryNote);			
				$pickup->save();
				$pickUpId = $pickup->getId();
				
					
				
				//print_r($manifestIdArray);
		
				$pickNoteReport = new PickNoteReport();
				
				
						
				$pickupFileName = $pickNoteReport->SavePDFFile($pickUpId, array($manifestId), "Collection");
				$pickup->setCollectionPDF($pickupFileName);
				
				$pickup->save();		
	
				//echo "collection";
				//die;
				//$manifestFilter =	new Manifest(trim($id));
				
			//if (count($list) > 0) 
			//{
				//foreach ($list as $manifest) 
				//{
					
				//}
				
				$manifest->setCollectionComment($commentColletion);
				$manifest->setCollectionDate(strtotime($dateColletion));
				$manifest->setCollectionDateTo(strtotime($dateColletionTo));
				$manifest->setPickUpId($pickUpId);
				$manifest->save();
				
				
				//$pickUpId = 9000001;
				
				
				$api_response = SendCollectionRequest($pickUpId, array($manifest));
				
			
				
				if($api_response == "SUCCESS")
				{
					
				//echo "<pre>";
				//print_r($manifestFilter);
				//die;
				$subject = " Collection for Manifest Number " . $id . " .";
				$headers = "From: itsupport@oneworldexpress.com \r\n";
				$headers .= "Reply-To: itsupport@oneworldexpress.com \r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
				$headers .= 'Cc: itsupport@oneworldexpress.com; ' . "\r\n";
				$message = 'Dear Sir, <br><br>A collection has been booked for manifest number '.$strManifestId.' on  '. $dateColletion ;
				//mail("kathrin@oneworldexpress.com", $subject, $message, $headers);
				//mail("Martin Fuell<martin.fuell@postconsulting.at>", 'Collection for manifest number '.$id, 'Dear Sir, <br><br>A collection has been booked got manifest number '.$strManifestId.' on  '. $dateColletion, $headers);
				$output['manifest_id'] = $strManifestId;
				$output['delivery_note'] = $pickupFileName;
				$output['label_link'] = $dpdlabellink;
				$output['pickup_id'] = $pickUpId;
				$output['response'] = 'SUCCESS';
				
				}
				else
				{
					MakeShipmentAvailableForCollection($conIdArr);
					$output['response'] = "ERROR";
					$output['message'] = Translation::GetCaption("COLLECTION_NOT_BOOKED") . " " . $api_response;
				}
				
				//print_r($output);
				
				echo json_encode($output);
				die;
			
			
		}
		else
		{
			$output['message'] = "No shipment is available is for manifest.";
			$output['response'] = "ERROR";
			echo json_encode($output);
			exit;
		}
			
			
			
		
		
		
		
		}
		
		
		
		
		
		
		
		
		
		
		/*							
		
		$fromDate = date("Y-m-d", strtotime("-1 month"));					
		$dateTo = date("Y-m-d");
		
		
		
		
			$ManifestDataFilter = new ManifestDataFilter();
			$ManifestDataFilter->addFilter('AND m.collection_date IS NULL AND m.account = "'.$user->getAccount().'" '); 
			$list = $ManifestDataFilter->getColumnList("id, date_created, handling, product, pieces, weight, file_name, pdf_file");		
		
		
		
		
		
		$manifestIdArray = array();
		
		foreach($list as $manifest)
		{
			//echo "manifest id " . $manifest->getId();
			$manifestIdArray[] = $manifest->getId();
		}
		
		$strManifestId = implode(", ", $manifestIdArray);			
		
		$manifestFilter = new ManifestDataFilter();
		$manifestFilter->addIdFilterIn($strManifestId);
		$list = $manifestFilter->getList();
		
		*/
		
		

	function MakeShipmentAvailableForCollection($conIdArr)
	{		
		
		foreach($conIdArr as $conId)
		{
			$consignment = new Consignment($conId);
			$consignment->setDateEndOfDay('');
			$consignment->save();		
						
		}

	}
		
		
	
	
	 function SendCollectionRequest($pickupId, $manifestList) 
	 {
	  
	  $shipmentArray = array();
	
	  $user = SessionManager::getUser();
	  $customerInfoArray = array(    
		  "customer-id" => $user->getAccount(),
		  "mail" => $user->getEmail(),
		  "contact-name" => $user->getFirstName(),
		  "return-address" => $user->getReturnAddress()
     );
	  
	  $pickup = new PickupSmart($pickupId);
	 
	  $pickupPdf = $pickup->getCollectionPdf();
	  
	  $addressLine1 = $pickup->getAddressLine1();
	  $addressLine2 = $pickup->getAddressLine2();
	  $addressLine3 = $pickup->getAddressLine3();
	  $city = $pickup->getCity();
	  $country = $pickup->getCountry();
	  $postCode = $pickup->getPostCode();
	  
	  
	  $totalPieces = 0;
	  $totalWeight = 0;
	  
	  $totalManifestWeight = 0;
	  
	  
	
	  foreach($manifestList as $manifest)
	  {
	  	 $totalManifestWeight = 0;
		
		 $ManifestConsignmentDataFilter = new ManifestConsignmentDataFilter();
		 $ManifestConsignmentDataFilter->addManifestIDFilter($manifest->getId());
		 $manifestList =  $ManifestConsignmentDataFilter->getList();
		 
		
		
		 
		 
	   	 if(count($manifestList) > 0)
	     {
			 
			foreach($manifestList as $m)
			{
			 	$consignmentIdArray[] = $m->getConsignmentId();
			}
			
				
		//$strConsignmentId = "'" . implode("','", $consignmentIdArray) . "'";  
		
		$consignentFilter = new ConsignmentFilter();
		$consignentFilter->addIdArrayFilter($consignmentIdArray);
		$list = $consignentFilter->getColumnList("id, account, hawb, reference, awb, company, 
												  contact, address_line_1,address_line_2, address_line_3, city, postcode, country,                    
												  telephone, weight, telephone, number_pieces, 
												  description, date_submitted, date_booked, handling, service_type,
												  consignment_number ");
												  
												  
	  									  
												  
	
					
		foreach($list as $consignment)
		{	
		
			 			
			
			$totalManifestWeight += $consignment->getWeight();				
		 	$totalWeight += $consignment->getWeight();
			
		 	$shipmentArray[] = 
				 array(  "Account" => $consignment->getAccount(),
					 	 "HawbNo" => $consignment->getHawb(),
					 	 "SSCC" => $consignment->getAwb(),
					     "Reference" => $consignment->getReference(),
					     "TrackingNumber" => $consignment->getAwb(),
					   "Company" => $consignment->getCompany(),
					   "Contact" => $consignment->getContact(),
					   "Address1" => $consignment->getAddressLine1(),
					   "Address2" => $consignment->getAddressLine2(),
					   "Address3" => $consignment->getAddressLine3(),
					   "City" => $consignment->getCity(),
					   "Postcode" => $consignment->getPostCode(),
					   "Country" => $consignment->getCountry(),
					   "Telephone" => $consignment->getTelephone(),
					   "Weight" => $consignment->getWeight(),
					   "NumberOfPieces" => $consignment->getNumberPieces(),
					   "Description" => $consignment->getDescription(),
					   "DateCreated" =>  date_format(date_create($consignment->getDateSubmitted()), 'c'),
					   "DateBooked" => $consignment->getDateBooked(),
					   "ServiceType" => $consignment->getServiceType()
				   //"ServiceCode" => $consignment->getHandling(),
				   //"ServiceName" => $consignment->getServiceType()
				 
				   
				);
				   
				   
		}
		
	   $totalPieces += $manifest->getPieces();
	   $collectionDateFrom = date("Y-m-d", $manifest->getCollectionDate());
	   $collectionDateTo = date("Y-m-d", $manifest->getCollectionDateTo());
	   
	   //$totalWeight += $manifest->getWeight();  
	   //$collectionDateFrom = date_format(date_create(date("Y-m-d", $manifest->getCollectionDate())), 'c');
	   //$collectionDateTo = date_format(date_create(date("Y-m-d",  $manifest->getCollectionDateTo())), 'c');
	   
	   $manifestArray[] =   array(  
	
			 "date" => date_format(date_create(date("Y-m-d", $manifest->getDateCreated())), 'c'),
			 "id"   => $manifest->getId(),  
			 "number-of-shipments" => $manifest->getPieces(),
			 "total-weight" => $totalManifestWeight,
			 "shipments" => $shipmentArray,
			 	 
			 
			);	
		
	   
	   } 
	   
		
	 }  
	  
	    $arrayCollection = array(
		   "_description" => "OWE pickup dataset",
		   "id" => $pickupId,
		   "customer-info" =>  $customerInfoArray,
		   "delivery-note-pdf" => SETTING_MAIN_URL.str_replace("../","",$pickupPdf),
		   "requested-pickup-date" => $collectionDateFrom, 
		   //"requested-pickup-date-to" => $collectionDateTo, //date_format(date_create($collectionDateTo))), 'c'),
		   "number-of-pieces" => $totalPieces,
		   "total-weight" => $totalWeight,
		   "manifests" => $manifestArray,
		   "collection_address_line_1" => $addressLine1,
		   "collection_address_line_2" => $addressLine2,
		   "collection_address_line_3" => $addressLine3,
		   "collection_address_city" => $city,
		   "collection_address_country" => $country,
		   "collection_address_postcode" => $postCode,	
       );
	   
	   
	   
	 
	  
	   
		//$service_url='https://api-ops-test.azurewebsites.net/api/hub/data/pickup/'.$pickupId;
		$service_url='https://api-ops-test.azurewebsites.net/api/backend/owe/pickup/'.$pickupId;
		//$service_url='https://cloud.open-postal-systems.net/api/backend/owe/pickup/'.$pickupId;
		
		
  
	    $username = "oweBackend";
	    $password = "WMAmc9hoTCdz2jvIoDCp";
	    $headers = array();
	    $headers[] = 'Content-Type: application/json';
	  
		$curl = curl_init($service_url);
		$curl_post_data = $json ;
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		// don't knpow how to use get - let's hope it's the default :)
	    curl_setopt($curl, CURLOPT_POST, true);
	    curl_setopt($curl, CURLOPT_POSTFIELDS,  json_encode($arrayCollection));
		curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		
		$curl_response = curl_exec($curl);
		
		//print_r($curl_response);
		
		//echo "API RESULT\n";
		//echo "\n";
		$messageCode =  curl_getinfo($curl, CURLINFO_HTTP_CODE);    
		curl_close($curl);
		if($messageCode == "200")
		{
			$pickup->setActive('Y');
			$pickup->save();
			mail("kazim@oneworldexpress.com", "OPS COLLECTION SUCCESS", json_encode($arrayCollection));
			return "SUCCESS";
		}
		else if($messageCode == "409")
		{
			$pickup->setActive('N');
			$pickup->save();
			mail("kazim@oneworldexpress.com","OPS COLLECTION FAILED","FOR" . $pickupId . " " . $curl_response);
			return $curl_response;
		}
		else
		{
			$pickup->setActive('N');
			$pickup->save();			
			mail("kazim@oneworldexpress.com","OPS COLLECTION FAILED","FOR" . $pickupId . " " . $curl_response);
			return $curl_response;
		}
		
		
	 }
	 
	 if(isset($_POST["pickupid"]))
	 {
		$pickupId = $_POST["pickupid"];
	
	

	$manifestDataFilter = new ManifestDataFilter();
	$manifestDataFilter->addPickupIdFilter($pickupId);
	$manifestList = $manifestDataFilter->getColumnList("id, 
														account, 
														file_name, 
														date_created, 
														pieces, 
														weight, 
														handling, 
														pdf_file, 
														product,
														label_link");		
	

	$manifestDetail = '
	               <label class="control-label font-green-soft">' . Translation::GetCaption("LIST_OF_ALL_MANIFESTS").':</label>
				   <table id="table_container" class="consignment_list_tbl table table-striped table-bordered table-advance table-hover">
                                    <thead>
                                        <tr>
                                        	<th class="text-center">'.Translation::GetCaption("DATE_CREATED").'</th>	
                                            <th class="text-center">'.Translation::GetCaption("MANIFEST_ID").'</th>                                            <th class="text-center">'.Translation::GetCaption("PRODUCT").'</th>
                                            <th class="text-center">'.Translation::GetCaption("WEIGHT").'</th>
											<th class="text-center">'.Translation::GetCaption("NUMBER_OF_PIECES").'</th>
											<th class="text-center">'.Translation::GetCaption("CSV").'</th>
											<th class="text-center">'.Translation::GetCaption("PDF").'</th> 
											<th class="text-center">'.Translation::GetCaption("LABELS").'</th>                                            
                                        </tr>
                                    </thead>     
                                    <tbody>';
											
		if(count($manifestList) > 0)
		{
			
			$totalWeight = 0;
			$totalPieces = 0;
			
			foreach($manifestList as $item)
			{
				
				$pdfFile = str_replace("../", SETTING_MAIN_URL, $item->getPdfFile());
				
				$manifestDetail .= "<tr>";
				$manifestDetail .= "<td class='text-center'>";
				$manifestDetail .= date("d.m.Y G:i",($item->getDateCreated()));
				$manifestDetail .= "</td>";
				$manifestDetail .= "<td class='text-center'>";
				$manifestDetail .= "<a href=client_list.php?ManifestId=".$item->getId().">".$item->getId()."</a>";
				$manifestDetail .= "</td>";														
				$manifestDetail .= "<td class='text-center'>";
				$manifestDetail .= $item->getProduct();
				$manifestDetail .= "</td>";				
				$manifestDetail .= "<td class='text-center'>";
				$manifestDetail .= $item->getWeight()." ".Translation::GetCaption("KG").".";
				$manifestDetail .= "</td>";	
				$manifestDetail .= "<td class='text-center'>";
				$manifestDetail .= $item->getPieces();
				$manifestDetail .= "</td>";																																							
				$manifestDetail .= "<td class='text-center'>";
				$manifestDetail .= '&nbsp;&nbsp;&nbsp;<a href="#" onclick="downloadCsv(\''.$item->getId().'\',\'download\')" title="Downlaod Shipment"><span class="glyphicon glyphicon-download-alt">&nbsp;</span> </a>';
				$manifestDetail .= "</td>";	
				$manifestDetail .= "<td class='text-center'>";
				$manifestDetail .= '&nbsp;&nbsp;&nbsp;<a href="'.$pdfFile.'" target="_blank"><span class="glyphicon glyphicon-download-alt">&nbsp;</span></a>';
				
				$manifestDetail .= "<td class='text-center'>";
				$manifestDetail .= '&nbsp;&nbsp;&nbsp;<a target="_blank" href="'.$item->getLabelLink().'" target="_blank"><span class="glyphicon glyphicon-download-alt">&nbsp;</span></a>';
				$manifestDetail .= "</td>";																											
				$manifestDetail .= "</tr>";	
				
				$totalWeight += $item->getWeight();
				$totalPieces += $item->getPieces();			
											
				
			}
			
				$manifestDetail .= "<tr>";
				$manifestDetail .= "<td colspan='3' class='text-center'>";
				$manifestDetail .= "<b>Summary</b>";
				$manifestDetail .= "</td>";
				$manifestDetail .= "<td class='text-center'>";
				$manifestDetail .= "<b>".$totalWeight." ".Translation::GetCaption("KG").".</b>";
				$manifestDetail .= "</td>";
				$manifestDetail .= "<td class='text-center'>";
				$manifestDetail .= "<b>".$totalPieces."</b>";
				$manifestDetail .= "</td>";
				$manifestDetail .= "</tr>";				
			
					
		}
			
			
	 $manifestDetail .= '</tbody>';
	 $manifestDetail .= '</table>';
	 
	 echo $manifestDetail;
	  
	 }
	
	
	
		
	 
	 
	 
	 if($_POST['action'] == 'getTotalWeight')
	 {
		$totalWeight = 0;
		$conIdArr = $_POST['conIdArr'];
		foreach($conIdArr as $conId)
		{
			$consignment = new Consignment($conId);
			$totalWeight += $consignment->getWeight();			
		}
		
		$totalWeight = number_format($totalWeight, 3);
		
		echo $totalWeight;
	 }
								 
								 
								 
								 
								 
								 
								 
								 
								 
								 
								 
								 
								 
								 
								 
								 
								 