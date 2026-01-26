<?php
include_classes([
    'ocdatavalidation.class',
    'ocdatavalidationfilter.class'
]);


include_classes([
    'SFTP',
    ], '3rdparty/Net');

class OrangeConnex implements CarrierService
{

    private $OC_HOST_URL;
    private $OC_VALIDATION;
    private $OC_PACKAGE_LABEL;
    private $OC_CONFIRM_SHIPMENT;
    private $OC_MAWB_INFO;

   
    private $OC_API_KEY;
    private $OC_CLIENT_KEY;

    const OC_TEMP = "OCTEMP";
    const IMPORT_GST = "ABN#64652016681 Code:PAID";

    private $user = null;

    public function __construct()
    {
		 
		 $this->OC_VALIDATION = "/api/shipment/package/v1/validation";
		 $this->OC_PACKAGE_LABEL = "/api/shipment/package/v1/label";
		 $this->OC_CONFIRM_SHIPMENT = "/api/shipment/package/v1/confirm";
		 $this->OC_MAWB_INFO = "/api/trace/tracking-mawb/v1/mawb";

         $this->OC_HOST_URL = constant("OC_HOST_URL");
		 $this->OC_API_KEY = constant("OC_API_KEY");
		 $this->OC_CLIENT_KEY = constant("OC_CLIENT_KEY");
    }

    public function validation(Consignment $consignment, Services $service, Country $country)
    {
       
        return $this->OCValidation($consignment);  
    }

    public function remoteareas($consignment, $carrierObject, $sender)
    {

    }

    public function label($consignment, $labelType = 'pdf', $size = '100x150')
    {
        $output = array();
        $isOcTempTrackingNumber = false;

        if($this->OC_API_KEY == '' || $this->OC_CLIENT_KEY == '')
        {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }

        $ocTrackingNumber = $this->GetOcTrackingNumber($consignment); // get oc tracking number from temp. table

        $ocTempTrackingNumber = substr( $consignment->getAwb(), 0, 6 );

        if($ocTempTrackingNumber == 'OCTEMP') 
        {
            $output = $this->OCPackageLabel($consignment, $ocTrackingNumber); // get label  
        }
        elseif($ocTrackingNumber != '')
        {
            
            $firstMileLabel = $this->GetFirstMileLabelLink($consignment);
            $ocTempTrackingNumberArray[] = self::OC_TEMP . $ocTrackingNumber; // OC Temporary Tracking number
            $output['STATUS'] = 'SUCCESS';
            $output['TRACKING_NUMBER'] = $ocTempTrackingNumberArray;
            $output['LABEL'] = $firstMileLabel;

            $parcelList = $consignment->getParcels();

            foreach($parcelList as $parcel)
            {
                $parcel->setTrackingNumber(self::OC_TEMP . $ocTrackingNumber);
                $parcel->save();
            }
                                              
        }
        else
        {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "OC Tracking number does not exist. Please try again later.";
        }

        return $output;
            
    }
         

    function SetHeaders($authKey)
    {
        $headers = array(

            
            "ApiKey: " . $this->OC_API_KEY,
            "clientKey: " . $this->OC_CLIENT_KEY,
            "Content-Type: application/json;charset=UTF-8",
            "vAuthorization: " . $authKey,
            "TimeStamp" => time()
        );

        return $headers;
    }

    function GetAuthString($rawUrl, $json)
    {

       
        $apiKey = $this->OC_API_KEY;
        
        $clientKey = $this->OC_CLIENT_KEY;

        $authString = $rawUrl . "|" . $json . "|" . $apiKey . "|" . $clientKey;

        $authKey = md5($authString);

        return $authKey;

    }

    function GetFirstMileLabelLink($consignment)
    {
        $label_file = "";
        $consignmentFilter = new ConsignmentFilter();
        $consignmentFilter->addFieldFilter("    awb", $consignment->getHawb());
        $conList = $consignmentFilter->getColumnList("label_file");

        if(count($conList) > 0)
        {            
            $conFirstMile = $conList[0];
            $label_file = $conFirstMile->getLabelFile();
        }

        return $label_file;
    } 

    function FinalMileNumberMappingWithDropOffNumber($consignment, $trackingNumber)
    {
        $consignmentDropOffMappingFilter = new ConsignmentDropoffMappingFilter();
        $consignmentDropOffMappingFilter->addFieldFilter("    dispatch_consignment_id", $consignment->getId());
        $consignmentDropOffMappingList = $consignmentDropOffMappingFilter->getList();
        if(count($consignmentDropOffMappingList) > 0)
        {
            $consignmentDropOffMappingObj = $consignmentDropOffMappingList[0];
            
            $dropOffTracking = $consignmentDropOffMappingObj->getDropOffConsignmentTracking();
            $parcelTracking = array($dropOffTracking => $trackingNumber);
            $consignmentDropOffMappingObj->setDispatchConsignmentTracking($trackingNumber);
            $consignmentDropOffMappingObj->setParcelTracking(json_encode($parcelTracking));
            $consignmentDropOffMappingObj->save();
        }

    }

    function GetUserPlatform($con)
    {
        $platForm = "";

        $user = new User($con->getUserId());

        if(!empty($user))
        {
             $userAccount = new CustomerAccount($user->getUserAccountId());
             $userAccountName = $userAccount->getUserAccount();
             if($userAccountName == 'S2WEBAY')
                 $platForm = 1;
             else
                 $platForm = 0;    
        }

        return $platForm;
    }

    function ValidateUsaStateName($state)
    {
        $stateArray = array(

            'NY' => 'NewYork',
            'PR' => 'PuertoRico',
            'VI' => 'VirginIslands',
            'MA' => 'Massachusetts',
            'RI' => 'RhodeIsland',
            'NH' => 'NewHampshire',
            'ME' => 'Maine',
            'VT' => 'Vermont',
            'CT' => 'Connecticut',
            'NJ' => 'NewJersey',
            'AE' => 'Military',
            'PA' => 'Pennsylvania',
            'DE' => 'Delaware',
            'DC' => 'Washington,DC',
            'VA' => 'Virginia',
            'MD' => 'Maryland',
            'WV' => 'WestVirginia',
            'NC' => 'NorthCarolina',
            'SC' => 'SouthCarolina',
            'GA' => 'Georgia',
            'FL' => 'Florida',
            'AA' => 'ArmedForcesAmericas',
            'AL' => 'Alabama',
            'TN' => 'Tennessee',
            'MS' => 'Mississippi',
            'KY' => 'Kentucky',
            'OH' => 'Ohio',
            'IN' => 'Indiana',
            'MI' => 'Michigan',
            'IA' => 'Iowa',
            'WI' => 'Wisconsin',
            'MN' => 'Minnesota',
            'SD' => 'SouthDakota',
            'ND' => 'NorthDakota',
            'MT' => 'Montana',
            'IL' => 'Illinois',
            'MO' => 'Missouri',
            'KS' => 'Kansas',
            'NE' => 'Nebraska',
            'LA' => 'Louisiana',
            'AR' => 'Arkansas',
            'OK' => 'Oklahoma',
            'TX' => 'Texas',
            'CO' => 'Colorado',
            'WY' => 'Wyoming',
            'ID' => 'Idaho',
            'UT' => 'Utah',
            'AZ' => 'Arizona',
            'NM' => 'NewMexico',
            'NV' => 'Nevada',
            'CA' => 'California',
            'AP' => 'ArmedForcesPacific',
            'HI' => 'Hawaii',
            'AS' => 'AmericanSamoa',
            'GU' => 'Guam',
            'PW' => 'Palau',
            'FM' => 'FederatedStatesofMicronesia',
            'MP' => 'NorthernMarianaIslands',
            'MH' => 'MarshallIslands',
            'OR' => 'Oregon',
            'WA' => 'Washington',
            'AK' => 'Alaska',          

        );

        $state = strtoupper($state);

        if (array_key_exists($state, $stateArray)) { // check if short name exists then return the full name
            return $stateArray[$state];
        }
        elseif(array_search(strtolower($state), array_map('strtolower', $stateArray))) // elseif(array_search($state, $stateArray)) 
        {
            return $state;
        }
    }

    function ValidateCanadaStateName($state)
    {
        $stateArray = array(
        'AB' =>  'Alberta',
        'BC' =>  'British Columbia',
        'MB' =>  'Manitoba',
        'NB' =>  'New Brunswick',
        'NL' =>  'Newfoundland and Labrador',
        'NS' =>  'Nova Scotia',
        'NU' =>  'Nunavut',
        'NT' =>  'Northwest Territories',
        'ON' =>  'Ontario',
        'PC' =>  'Prince Edward Island',
        'QC' =>  'Quebec',
        'SK' =>  'Saskatchewan',
        'YT' =>  'Yukon');

        $state = strtoupper($state);

        if (array_key_exists($state, $stateArray)) { // check if short name exists then return the full name
            return $stateArray[$state];
        }
        elseif(array_search(strtolower($state), array_map('strtolower', $stateArray))) // elseif(array_search($state, $stateArray)) 
        {
            return $state;
        }

    }

    
    function ValidateAustraliaStateName($state)
    {
        $stateArray = array(

            'ACT' => 'Australian Capital Territory',
            'NSW' => 'New South Wales',
            'NT'  => 'Northern Territory',
            'VIC' => 'Victoria',
            'QLD' => 'QueensLand',
            'SA'  => 'South Australia',
            'WA'  => 'Western Australia',
            'TAS' => 'Tasmania'

        );

        $state = strtoupper($state);

        if (array_key_exists($state, $stateArray)) { // check if short name exists then return the full name
            return $stateArray[$state];
        }
        elseif(array_search(strtolower($state), array_map('strtolower', $stateArray))) // elseif(array_search($state, $stateArray)) 
        {
            return $state;
        }

    }


	function GetConsignmentItems($consignment)
	{
		$itemsArray = array();
		$currency = $consignment->getCurrency();
		$currencyFilter = new CurrencyFilter();
		$currencyFilter->addFieldFilter("    country_id", $consignment->getCountryId());
		$currencyList = $currencyFilter->getList();
		if (count($currencyList) > 0) {
			$currencyObj = $currencyList[0];
			$toCurrency = $currencyObj->getRightSymbol();
		}
		if($consignment->getId() > 0) {
			$where = array(
				"consignment_id" => $consignment->getId()
			);
			$itemDetailsFilter = new ItemDetailFilter();
			$itemDetailsFilter->where($where);
			$itemsDetailList = $itemDetailsFilter->getList();
			if (count($itemsDetailList) > 0) {
				$itemDetails = $itemsDetailList[0];
				$itemDetail = $itemDetails->getItemDetail();
				$itemDetailObj = json_decode($itemDetail, true);
				foreach ($itemDetailObj as $itemObj) {
					$itemValue = $itemObj['item_value'];
					if ($currency != $toCurrency) {
						$itemValue = Currency::convertCurrency($currency, $toCurrency, $itemObj['item_value']);
						$currency = $toCurrency;
					}
					//$country = new Country($consignment->getCountryId());
					$itemsArray[] = array(
						"sku" => $itemObj['item_sku'],
						"skuDesc" => $itemObj['item_description'],
						"skuValue" => $itemValue,
						"currency" => $currency,
						"quantity" => $itemObj['no_of_items'],
						"txnUnitPrice" => $itemValue,
						"txnQty" => $itemObj['no_of_items'],
						"link" => $itemObj['item_url']
					);
				}
			}
		}else{
			$items = $consignment->getItems();
			if(count($items) > 0){
				foreach ($items as $item){
					$itemValue = $item['item_value'];
					if ($currency != $toCurrency) {
						$itemValue = Currency::convertCurrency($currency, $toCurrency, $itemValue);
						$currency = $toCurrency;
					}
					$itemsArray[] = [
						"sku" => $item['item_sku'],
						"skuDesc" => $item['item_description'],
						"skuValue" => $itemValue,
						"currency" => $currency,
						"quantity" => $item['no_of_items'],
						"txnUnitPrice" => $itemValue,
						"txnQty" => $item['no_of_items'],
						"link" => $item['item_url']
					];
				}
			}
		}
		return $itemsArray;
	}

    // save validation into temp table
    
    function SaveValidation($consignment, $api_request, $api_response) 
    {
        $ocTrackingNumber = "";
        $orderReference = $consignment->getHawb();
        $userId =  $consignment->getUserId();

        $result = json_decode($api_response);

        if (isset($result->code))
        {
            if ($result->code == "0") // success case
            {
                if (isset($result->data))
                {
                    $ocTrackingNumber = $result->data->ocTrackingNumber;
                }
            }

        }

        $ocDataValidation = new OcDataValidation();
        $ocDataValidation->setTrackingNumber($ocTrackingNumber);
        $ocDataValidation->setOrderReference($orderReference);
        $ocDataValidation->setHostUrl($this->OC_HOST_URL);
        $ocDataValidation->setOcRequest($api_request);
        $ocDataValidation->setOcResponse($api_response);
        $ocDataValidation->setDateCreated(date("Y-m-d G:i:s"));
        $ocDataValidation->setAddedBy($userId);
        $ocDataValidation->save(); 

    }
    
    // Get OcTracking Number from temp. table

    function GetOcTrackingNumber($consignment)
    {
        $ocTrackingNumber = "";

        $consignmentFilter = new ConsignmentFilter();
        $consignmentFilter->addFieldFilter("    awb", $consignment->getHawb());
        $conList = $consignmentFilter->getColumnList("hawb");

        if(count($conList) > 0)
        {
            
            $conDropOff = $conList[0];
            $orderReference = $conDropOff->getHawb();
            if($orderReference != '')
            {
                $ocDataValidationFilter = new OcDataValidationFilter();
                $ocDataValidationFilter->addFieldFilter("    order_reference", $orderReference);
                $ocDataValidationFilter->AddOrderBy("id", false);
                $ocValidationFilterList = $ocDataValidationFilter->getList();

                if(count($ocValidationFilterList) > 0)
                {
                    $ocDataValidationObj = $ocValidationFilterList[0]; // get the latest validated record
                    $ocApiJsonResponse = $ocDataValidationObj->getOcResponse();
                    $result = json_decode($ocApiJsonResponse);

                    if (isset($result->code))
                    {
                        if ($result->code == "0") // success case
                        {
                            if (isset($result->data))
                            {
                                $ocTrackingNumber = $result->data->ocTrackingNumber;
                            }
                        }

                    }

                }
            }
            
        }        

        return $ocTrackingNumber;
    }

    function OCValidation($consignment)
    {
        $errorArr = [];
        $contentsDataParcel = [];
        $contentsDataItem = [];
        if($this->OC_API_KEY == '' || $this->OC_CLIENT_KEY == '')
        {
            $output[] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }
        $contentsDataParcel = $consignment->getConsignmentParcels();
        if (count($contentsDataParcel) > 0) {
            foreach ($contentsDataParcel as $contentsParcel) {
                foreach ($contentsParcel['items'] as $indexc => $contents) {
                if (!preg_match("/^\d+(\.\d{1,3})?$/", $contents['no_of_items']) || $contents['no_of_items'] == "") {
                    $errorArr[] = "Please enter valid [up to 3 decimal points] parcel " . ($index + 1) . " contents " . ($indexc + 1) . " no_of_items";
                }
                if (!preg_match("/^\d+(\.\d{1,3})?$/", $contents['item_value']) || $contents['item_value'] == "") {
                    $errorArr[] = "Please enter valid [up to 3 decimal points] parcel " . ($index + 1) . " contents " . ($indexc + 1) . " item_value";
                }
                if (!preg_match("/^\d+(\.\d{1,3})?$/", $contents['weight']) || $contents['weight'] == "") {
                    $errorArr[] = "Please enter valid [up to 3 decimal points] parcel " . ($index + 1) . " contents " . ($indexc + 1) . " weight";
                }

                if (strlen($contents['manufacture_country_iso']) != 2) {
                    $errorArr[] = "Please enter valid 2 character iso code parcel " . ($index + 1) . " contents " . ($indexc + 1) . " manufacture_country_iso";
                }
            }
        }
            }
            if(count($errorArr) > 0){
                return $errorArr;
            }


        $output = [];
        
        $stateName = $consignment->getState();

        $currency = $consignment->getCurrency();
        $packageTotalValue = $consignment->getValue();

        /*
        $serviceAgentConstantFilter = new ServiceConstantValueFilter();
        $serviceAgentConstantFilter->addFilter("service_id = '" . $consignment->getServiceId() . "' AND agent_id = '" . $consignment->getAgentId() . "' ");
        $serviceAgentConstant = $serviceAgentConstantFilter->getConstantList("c.constant 'constant_name', scv.constant_value 'constant_value'");

        if (count($serviceAgentConstant) > 0)
        {
            foreach ($serviceAgentConstant as $serviceAgentConstantData)
            {
                $this->constants[$serviceAgentConstantData->getConstantName() ] = $serviceAgentConstantData->getConstantValue();
            }
        }

        if (trim(@$this->constants['API_KEY']) == '' || trim(@$this->constants['CLIENT_KEY']) == '')
        {
            //$output['STATUS'] = 'ERROR';
            $output[] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }

        */
        if($consignment->getId() == 0){
			$parcelList = $consignment->getConsignmentParcels();
		}else{
        	$parcelList = $consignment->getParcels();
		}

        if (count($parcelList) > 0)
        {

            foreach ($parcelList as $parcel)
            {
				if($consignment->getId() == 0){
					$this->packageTotalWeight += $parcel['weight'];
					$this->totalLength += $parcel['length'];
					$this->totalWidth += $parcel['width'];
					$this->totalHeight += $parcel['height'];
				}else{
					$this->packageTotalWeight += $parcel->getWeight() ;
					$this->totalLength += $parcel->getLength();
					$this->totalWidth += $parcel->getWidth();
					$this->totalHeight += $parcel->getHeight();
				}
            }

            $this->packageTotalWeight = $this->packageTotalWeight * 1000; /// in grams

            $countryId = $consignment->getCountryId();

            $country = new Country($countryId);

            $senderCountryId = $consignment->getSenderCountryId();

            $senderCountry = new Country($senderCountryId);

            $sellerJson = $consignment->getConsignmentSeller();

            if (!empty($sellerJson))
            {
                $seller = json_decode($sellerJson);
                
            }
            else
            {
                //$output['STATUS'] = 'ERROR';
                $output[] = 'Seller information is missing.';
                return $output;
            }           

            $userPlatform = $this->GetUserPlatform($consignment); // get ebay seller or non-ebay seller

            if($consignment->getCountryId() == 13) // australia
            { // only for australia
                $importGST = self::IMPORT_GST;
                //$stateName = preg_replace('/\s+/', '', $consignment->getState());
                //$stateName = $consignment->getState();

                $validatedStateName = $this->ValidateAustraliaStateName($stateName); // state name validation for Australia.

                if($validatedStateName != '')
                {
                    $stateName = ucwords(strtolower($validatedStateName));
                }

                
            
            }
            elseif($consignment->getCountryId() == 226) // only for USA
            {
                //$stateName = preg_replace('/\s+/', '',$consignment->getState());
                //$stateName = $this->makeUTF8($consignment->getState());

                $validatedStateName = $this->ValidateUsaStateName($stateName); // state name validation for USA.

                if($validatedStateName != '')
                {
                    $stateName = ucwords(strtolower($validatedStateName));
                }
            }
            elseif($consignment->getCountryId() == 80) // Germany
            {
                $stateName = '-';
            }
            elseif($consignment->getCountryId() == 38) // canada
            {
               // $stateName = preg_replace('/\s+/', '',$consignment->getState());
                $stateName = $this->makeUTF8($consignment->getState());

                $validatedStateName = $this->ValidateCanadaStateName($stateName); // state name validation for Canada.

                if($validatedStateName != '')
                {
                    $stateName = ucwords(strtolower($validatedStateName));
                }

            }

            $itemsArray = $this->GetConsignmentItems($consignment); // Get Items Array

            $currencyFilter = new CurrencyFilter();
            $currencyFilter->addFieldFilter("    country_id", $consignment->getCountryId());
            $currencyList = $currencyFilter->getList();
    
            if(count($currencyList) > 0)
            {
                $currencyObj = $currencyList[0];
                $toCurrency = $currencyObj->getRightSymbol();
                
                if($currency != $toCurrency)
                {
                    $packageTotalValue = Currency::convertCurrency($currency, $toCurrency, $consignment->getValue());
                    $currency = $toCurrency;  
                }
            }
            
            
            //echo "Before Validation " . $consignment->getState() .  " After Validation " . $stateName;
            //die;

            //echo "before requ" .  $stateName;

            $data = array(
                "MessageId" => uniqid(),
                "data" => array(
                    "isEbayPlatform" =>  $userPlatform, // 1 = ebay seller, 0 = normal seller
                    "serviceCode" => "GS",
                    "orderDate" => date('Y-m-d\TH:i:sO') ,
                    "consigneeFullName" => trim($consignment->getContact()),
                    "consigneePhone" => trim($consignment->getTelephone()),
                    "consigneeCountry" => trim($country->getIso()),
                    "consigneeState" => trim($stateName),
                    "consigneeCity" => trim($consignment->getCity()) ,
                    "consigneeAddr1" => trim($consignment->getAddressLine1()),
                    "consigneeZipCode" => trim($consignment->getPostCode()),
                    "sellerFullName" => $seller->seller_name,
                    "sellerPhone" => $seller->seller_phone, //$con->getSenderTelephone(),
                    "sellerCountry" => $seller->seller_country_iso, //$senderCountry->getIso(),
                    "sellerState" => $seller->seller_state, //$con->getSenderState(),
                    "sellerCity" => $seller->seller_city,
                    "sellerAddr1" => $seller->seller_address_line_1, //$con->getSenderAddressLine1(),
                    "sellerZipCode" => $seller->seller_postcode, //$con->getSenderPostCode(),
                    "currency" => $toCurrency ,
                    "battery" => 0,
                    "incoterm" => 0,
                    "importerGST" => $importGST, // only for australia
                    "packageTotalWeight" => $this->packageTotalWeight,
                    "packageLength" => $this->totalLength,
                    "packageWidth" => $this->totalWidth,
                    "packageHeight" => $this->totalHeight,
                    "packageTotalValue" => $packageTotalValue,
                    "itemInfoList" => 

                        $itemsArray                   

                )
            );

        }

        //print_r($data);


        $json_request = json_encode($data, JSON_UNESCAPED_UNICODE);

        //echo $json_request;

        $host = $this->OC_HOST_URL;

        $rawUrl = $this->OC_VALIDATION;

        $url = $host . $rawUrl;

        $authKey = $this->GetAuthString($rawUrl, $json_request);

        $headers = $this->SetHeaders($authKey);

        $method = "POST";

        ///echo "HOST URL " . $url . $headers;

        $json_response = $this->callAPI($method, $url, $headers, $json_request);

        //$consignment->setApiData($json_request, $json_response, 'VALIDATION_API');

        //echo $json_response;

        //die;

        $result = json_decode($json_response);

        $this->SaveValidation($consignment, $json_request, $json_response); // save validation into temp. table


        if (isset($result->code))
        {
            if ($result->code == "0") // success case            
            {
                if (isset($result->data))
                {
                    $ocTrackingNumber = $result->data->ocTrackingNumber;
                    $trackingNumberArray[] = $ocTrackingNumber;

                    /*$output['STATUS'] = 'SUCCESS';
                    $output['TRACKING_NUMBER'] = $trackingNumberArray; // temporary tracking number
                    $consignment->setOtherRoutingCode($ocTrackingNumber);
                    $consignment->save();
                    $parcelList[0]->setTrackingNumber($ocTrackingNumber);
                    $parcelList[0]->save();*/
                }
            }
            elseif ($result->code == "1") // failure case           
            {
                //$output['STATUS'] = 'ERROR';
                $output[] = $result->message;
            }
            elseif ($result->code == "3") // failure case           
            {
                //$output['STATUS'] = 'ERROR';
                $output[] = $result->message;
            }
            elseif ($result->code == "4") // failure case           
            {
                if (isset($result->data))
                {
                    $error = $result->data;
                    //$output['STATUS'] = 'ERROR';
                    $output[] = $error->failureReason;
                }
                elseif (isset($result->errors))
                {
                    $errors = $result->errors;

                    if (count($errors) > 0)
                    {
                        foreach ($errors as $error)
                        {

                            //$output['STATUS'] = 'ERROR';
                            //$output['MESSAGE'] = $error->message;
							$output[] = $error->message;
                        }
                    }
                }
            }
        }
        else
        {
            $output[] = "Service is not responding. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }

        return $output;
    }

    function OCPackageLabel($consignment, $ocTrackingNumber)
    {
        $output = array();

        $inboundSortArray = array(

            "US-1"  => "ORD-MIX",
            "US-2"  => "ORD-PA",
            "US-3"  => "JFK-MIX",
            "US-4"  => "JFK-PA",
            "US-5"  => "LAX-MIX",
            "CA-21" => "Vic FSAs",
            "AU-26" => "SYD",
            "DE-31" => "DE_HERMES/GB-DE/NL02-04",
            "DE-32" => "DE_DHL/GB-DE/NL02-01"

        );

        $host = $this->OC_HOST_URL;
        $rawUrl = $this->OC_PACKAGE_LABEL;

        $url = $host . $rawUrl;

        $packageTotalWeight = 0;
        $totalLength = 0;
        $totalWidth = 0;
        $totalHeight = 0;

        $parcelList =  $parcel_list = $consignment->getParcels();

        if (count($parcelList) > 0)
        {

            foreach ($parcelList as $parcel)
            {
                $weight = $parcel->getWeight() * 1000; // convert into gm.
                $packageTotalWeight +=  $weight;
                $totalLength += $parcel->getLength();
                $totalWidth += $parcel->getWidth();
                $totalHeight += $parcel->getHeight();
            }
        }

        $data = array(

            "messageId" => uniqid(),
            "data" => array(
                "ocTrackingNumber" => $ocTrackingNumber,
                "packageTotalWeight" => $packageTotalWeight,
                "packageLength" => $totalLength,
                "packageWidth" => $totalWidth,
                "packageHeight" => $totalHeight,
                "timeStamp" => time()
            )
        );

        $jsonRequest = json_encode($data);

        //echo $jsonRequest;

        $authKey = $this->GetAuthString($rawUrl, $jsonRequest);

        $headers = $this->SetHeaders($authKey);

        $method = "POST";

        $jsonResponse = $this->callAPI($method, $url, $headers, $jsonRequest);

        //echo $jsonResponse;

        $result = json_decode($jsonResponse, true);

        $consignment->setApiData($jsonRequest, $jsonResponse, 'PACKAGE_LABEL_API');

        if (isset($result['code']))
        {
            $code = $result['code'];

            if ($code == "0") // success case            
            {
                $inboundGateway = $result['data']['inboundGateway'];
                $lastMileTrackingNumber[] = $result['data']['lastMileTrackingNumber'];
                $sortCode = $result['data']['sortCode'];
                $serviceCode = $result['data']['serviceCode'];
                $hsCode = $result['data']['itemInfoList'][0]['hsCode'];

                $jsonRoutingCodeEur = array("inboundGateway" => $inboundGateway,
                                            "sortCode" => $sortCode,
                                        );

                $jsonRoutingCodeEur = json_encode($jsonRoutingCodeEur);

                $warehouseName = $inboundSortArray[$sortCode];
                $warehouseFilter = new WarehouseFilter();
                $warehouseFilter->addFieldFilter("     warehouse_name",  $warehouseName);
                $warehouseList = $warehouseFilter->getList();
                if(count($warehouseList) > 0)
                {
                    $warehouse = $warehouseList[0];
                    $id = $warehouse->getId();
                    $consignment->setDestinationWarehouseId($id); // assigning destination warehouse id
                }
                
                $consignment->setRoutingCodeEur($jsonRoutingCodeEur); // setting routing for the parcel
                $consignment->save();

                foreach($parcelList as $parcel)
                {
                    $parcel->setTrackingNumber($result['data']['lastMileTrackingNumber']);
                    $parcel->setHsCode(json_encode(array($hsCode)));
                    $parcel->save();
                }

                $consignment->setOtherRoutingCode($ocTrackingNumber);

                $this->FinalMileNumberMappingWithDropOffNumber($consignment, $result['data']['lastMileTrackingNumber']); // mapping with final mile number
            
                $pdf_content = $result["data"]["labelPDF"][0];
                $pdf_decoded = base64_decode($pdf_content);

               
    
                /*
                $fpdi->SetHeaderMargin(0);
                $fpdi->SetFooterMargin(0);
                $fpdi->SetAutoPageBreak(false, 0);

                $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                $pdf->SetPrintFooter(false);
                $pdf->SetFooterMargin(0);
                $pdf->SetAutoPageBreak(false, 0);
                $page_size = array(150, 100);
                $pdf->AddPage("P", $page_size);
                $pdf->IncludeJS("print();");
                $pdf->Output("../_assets/pdf/" . date('Y_m_d') . '/' . $consignment->getId() . ".pdf", "F");
                */

                $fileName = "../_assets/pdf/" . date('Y_m_d') . '/' . $consignment->getId() . ".pdf";

                $pdf = fopen($fileName, 'w');
                fwrite($pdf, $pdf_decoded);
                fclose($pdf);

                if($consignment->getCountryId() != 80)
                {
                    $PDFMerger = new PDFMerger();
                    $PDFMerger->addPDF($fileName);
                    try {
                        $PDFMerger->mergeOrangeConnex('file', $fileName, '', $consignment);
                        $output['STATUS'] = 'SUCCESS';
                        $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
                        $output['TRACKING_NUMBER'] = $lastMileTrackingNumber;
                    } 
                    catch (Exception $e) 
                    {
                        $output['STATUS'] = 'ERROR';
                        $output['MESSAGE'] = $e->getMessage();
                    }
                }
                
                else
                {
                    $output['STATUS'] = 'SUCCESS';
                    $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
                    $output['TRACKING_NUMBER'] = $lastMileTrackingNumber;
                }
                

                
                

                
                /*
                $fpdi = new FPDI();

                $fpdi->SetPrintHeader(false);
                $fpdi->SetPrintFooter(false);
                $count = $fpdi->setSourceFile($fileName);
                //echo  "count " .  $count;
                $template = $fpdi->importPage(1);
                //$size = $fpdi->getTemplateSize($template);
                $fpdi->AddPage('P', array(100, 150));

                $fpdi->useTemplate($template,1, 0, 96, 150, true);

                $fpdi->IncludeJS("print();");
                $fpdi->Output($fileName, 'F');
                */
                
                
                
                


               
               
            }
            elseif($code == "1")
            {
                $output['STATUS'] = 'ERROR';
                $output['MESSAGE'] = $result['message'];
            }
            elseif ($code == "4") // failure case            
            {

                if (isset($result['errors']))
                {
                    if (count($result['errors']) > 0)
                    {
                        $errors = $result['errors'];

                        foreach ($errors as $error)
                        {
                            //echo $error['message'];
                            $output['STATUS'] = 'ERROR';
                            $output['MESSAGE'] = $error['message'];
                        }
                    }
                }
            }
        }

        return $output;

    }

    function ConfirmShipmentApi($bagId)
    {

        if($bagId <= 0 || $bagId == '')
        {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "Bag Id is empty or zero.";
            return $output;
        }
            
        
        $output = array();
       
        $mawbId = "";
        $mawbNumber = "";
        $flightNumber= "";
        $etd = "";
        $eta = "";

        $bagging = new Bagging($bagId);
        $bagActualWeight = $bagging->getActualWeight();
        $bagNumber = $bagging->getBagNumber();

        if($bagActualWeight == 0)
        {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "Bag weight is zero.";
            return $output;
        }
        
        $mawbParcelMappingFilter = new MawbParcelMappingFilter();
        $mawbParcelMappingFilter->addFieldFilter("    bag_id", $bagId);

        $mawbParcelBaggingMappingFilterList = $mawbParcelMappingFilter->getList();


        if(count($mawbParcelBaggingMappingFilterList) > 0)
        {
            $mawbId = $mawbParcelBaggingMappingFilterList[0]->getMawbId();

            if($mawbId > 0)
            {                
                $mawb = new Mawb($mawbId);
                $mawbNumber = $mawb->getMawbNumber();           
    
                foreach($mawbParcelBaggingMappingFilterList as $parcelBaggingMapping)
                {
                   
                    $parcelId  = $parcelBaggingMapping->getParcelId();
                    $parcel = new Parcel($parcelId);
                    $lastMileTrackingNumber = $parcel->getTrackingNumber();
                    $packageInfoList[] = array("lastMileTrackingNumber" => $lastMileTrackingNumber);
                }

                $flightMappingFilter = new FlightMappingFilter();
                $flightMappingFilter->addFieldFilter("    mawb_id", $mawbId);
                $flightMappingFilterList = $flightMappingFilter->getList();

                if(count($flightMappingFilterList) > 0)
                {
                    $flightMappingFilterObj = $flightMappingFilterList[0];
                    $flightId = $flightMappingFilterObj->getFlightInfoId();
                    $flightInfo = new FlightInfo($flightId);
                    $transportId = $flightInfo->getTransportId();

                    $etd = strtotime($flightInfo->getEtd());
                    $eta = strtotime($flightInfo->getEtd());


                    if($etd == 0)
                    {
                        $output['STATUS'] = 'ERROR';
                        $output['MESSAGE'] = "Estimated departure time is invalid.";
                        return $output;
                    }
                    elseif($eta == 0)
                    {
                        $output['STATUS'] = 'ERROR';
                        $output['MESSAGE'] = "Estimated arrival time is invalid.";
                        return $output;
                    }
                    
                    $etd = date('Y-m-d\TH:i:sO', strtotime($flightInfo->getEtd()));
                    $eta = date('Y-m-d\TH:i:sO', strtotime($flightInfo->getEta()));
                    $departureAirport = $flightInfo->getDepartureAirport();
                    $arrivalAirport = $flightInfo->getArrivalAirport();

                    $flight = new Flight($transportId);
                    $flightNumber = $flight->getFlightNumber();

                    $flightNumber = "MAWB(" . $mawbNumber .")-FlightNumber(" . $flightNumber . ")-ETD(". $etd . ")-ETA(" . $eta . ")-DEPT(" .  $departureAirport . ")-ARR(" . $arrivalAirport . ")-ATD()-ATA()";

                    $data = array(

                        "MessageId" => $bagId,
                        "data" => array(
                            "bagId" => $bagNumber ,
                            "bagWeight" => $bagActualWeight,
                            "battery" => 0,
                            "MAWB" => $mawbNumber,
                            "flightNumber" => $flightNumber,
                            "lastMileBagId" => $bagNumber,
                            "packageInfoList" => $packageInfoList

                        )
                    );

                    $json_request = json_encode($data);

                    $host = $this->OC_HOST_URL;

                    $rawUrl = $this->OC_CONFIRM_SHIPMENT;

                    $url = $host . $rawUrl;

                    $authKey = $this->GetAuthString($rawUrl, $json_request);

                    $headers = $this->SetHeaders($authKey);

                    $method = "POST";

                    $json_response = $this->callAPI($method, $url, $headers, $json_request);

                    $result = json_decode($json_response);

                    //print_r($result);

                    $apiData = new ApiData();
                    $apiData->setConsignmentId($bagId);
                    $apiData->setApiRequest($json_request);
                    $apiData->setApiResponse($json_response);
                    $apiData->setDateCreated(date("Y-m-d G:i:s"));
                    $apiData->setType("ConfirmShipmentApi");
                    $apiData->save();


                    if (isset($result->code))
                    {
                        
                        if($result->message == 'Successful')
                        {
                            $output['STATUS'] = 'SUCCESS';
                            $output['MESSAGE'] = $bagNumber . ' bag data sent successfully.';
                            return $output;
                        }
                        elseif(isset($result->errors))    
                        {
                            $output['STATUS'] = 'ERROR';
                            $errorsArray = $result->errors;
                        
                            foreach ($errorsArray as $error)
                                $output['MESSAGE'][] = $error->message;
                            
                            return $output;
                        }
                            
                        
                    }

                }
                else
                {
                    $output['STATUS'] = 'ERROR';
                    $output['MESSAGE'] = 'Flight is not assinged to the mawb.';
                    return $output;
                }

            }
            else
            {
                $output['STATUS'] = 'ERROR';
                $output['MESSAGE'] = 'Mawb Id is not assigned to the bag number';
                return $output;
            }
           
            
        }

       
        

    }

    function MawbInfo($mawbId)
    {
        if($mawbId <= 0 || $mawbId == '')
        {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = 'Mawb Id is empty or zero';
            return $output;
        }
 

        $totalBags = 0;
        $departureAirport = "";
        $arrivalAirport = "";
        $mawbNumber = "";
        $grossWeight = 0;
        $volume_value = (60 * 40 * 60) / 6000;
        $volume = number_format((float)$volume_value, 3, '.', ''); 

        $flightMappingFilter = new FlightMappingFilter();
        $flightMappingFilter->addFieldFilter("    mawb_id", $mawbId);
        $flightMappingFilterList = $flightMappingFilter->getList("*", true);

        if(count($flightMappingFilterList) > 0)
        {
            $flightMappingFilterObj = $flightMappingFilterList[0];
            $flightId = $flightMappingFilterObj->getFlightInfoId();
            
            $flightInfo = new FlightInfo($flightId);

            $departureAirport = $flightInfo->getDepartureAirport();
            $arrivalAirport = $flightInfo->getArrivalAirport();
        }

       

        $mawb = new Mawb($mawbId);
        $mawbNumber = $mawb->getMawbNumber();
        $grossWeight = $mawb->getGrossWeight();
        $chargeableWeight = $mawb->getChargeWeight();

        $mawbParcelMappingFilter = new MawbParcelMappingFilter();
        $mawbParcelMappingFilter->addFieldFilter("    mawb_id", $mawbId);
        $mawbParcelBaggingMappingFilterList = $mawbParcelMappingFilter->getList("distinct count(bag_id) 'id'", false);

        if(count($mawbParcelBaggingMappingFilterList) > 0)
        {
            $mawbParcelBaggingMappingFilterObj = $mawbParcelBaggingMappingFilterList[0];
            $totalBags = $mawbParcelBaggingMappingFilterObj->getId();
        }

        if($chargeableWeight == 0)
            $chargeableWeight = $grossWeight;


        $data = array(

            "MessageId" => $mawbId,
            "data" => array(
                "mawbInfoList" => array(
                    "mawb" => $mawbNumber,
                    "originPort" => $departureAirport ,
                    "destinationPort" => $arrivalAirport,
                    "chargeableWeight" => $chargeableWeight, // wait
                    "quantity" => $totalBags,
                    "grossWeight" => $grossWeight, // wait
                    "volume" => $volume // wait
                )
            )
        );

        $json_request = json_encode($data);

        $json_request;

        $host = $this->OC_HOST_URL;

        $rawUrl = $this->OC_MAWB_INFO;

        $url = $host . $rawUrl;

        $authKey = $this->GetAuthString($rawUrl, $json_request);

        $headers = $this->SetHeaders($authKey);

        $method = "POST";

        $json_response = $this->callAPI($method, $url, $headers, $json_request);

        $result = json_decode($json_response);

        $apiData = new ApiData();
        $apiData->setConsignmentId($mawbId);
        $apiData->setApiRequest($json_request);
        $apiData->setApiResponse($json_response);
        $apiData->setDateCreated(date("Y-m-d G:i:s"));
        $apiData->setType("MawbInfo");
        $apiData->save();

        print_r($result);

        if (isset($result->code))
        {
            if ($result->code == "0") // success case            
            {
                if($result->message == 'Successful')
                {
                    $output['STATUS'] = 'SUCCESS';
                    $output['MESSAGE'] = $mawbNumber . ' mawb data sent successfully.';
                    mail("kazim@onewroldexpress.com", "OC Mawb Number " . $mawbNumber . ": Success", $mawbNumber .  " Mawb data sent successfull.");
                }
            }
            elseif ($result->code == "4") // failed            
            {
                if (isset($result->errors))
                {
                    $errors = $result->errors;

                    if (count($errors) > 0)
                    {
                        foreach ($errors as $error)
                        {
                            $output['STATUS'] = 'ERROR';
                            $output['MESSAGE'] = $error->message;
                            mail("kazim@onewroldexpress.com", "OC Mawb Number " . $mawbNumber . ": Failed", $mawbNumber .  " Mawb data sent failed.");

                        }
                    }
                }
            }
        }
    }

    function callAPI($method, $url, $headers, $data = [])
    {

        $curl = curl_init();
        switch ($method)
        {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);
                if ($data) curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                if ($data) curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            default:
                if ($data) $url = sprintf("%s?%s", $url, http_build_query($data));
            }

            // OPTIONS:
            curl_setopt($curl, CURLOPT_URL, $url);
            if ($headers && !empty($headers))
            {
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            }
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

            // EXECUTE:
            $result = curl_exec($curl);

            /*
            echo "<br><br><br><br>";
            echo "URL : " . $url;
            echo "<br><br><br><br>";
            echo "Headers :";
            print_r($headers);
            echo "<br><br><br><br>";
            print_r($data);
            echo "<br><br><br>";
            echo $result;
            echo "<br><br><br>";
            */

            if (curl_errno($curl))
            {
                $error_msg = curl_error($curl);
            }
            if (empty($error_msg))
            {
                curl_close($curl);
                return $result;
            }
            else
            {
                return false;
            }
        }

        function makeUTF8($str, $encoding = "") {
            $str = preg_replace('/[^(\x20-\x7F)]* /', '', $str);
            //$str = str_replace('&','&amp;', $str);
            $str = str_replace('<', '&lt;', $str);
            $str = str_replace('>', '&gt;', $str);
            $str = str_replace("'", "", $str);
            $str = str_replace('&', '', $str);
        
            if ($str !== "") {
                if (empty($encoding) && $this->isUTF8($str))
                    $encoding = "UTF-8";
                if (empty($encoding))
                    $encoding = mb_detect_encoding($str, 'UTF-8, ISO-8859-1');
                if (empty($encoding))
                    $encoding = "ISO-8859-1"; //  if charset can't be detected, default to ISO-8859-1
                return $encoding == "UTF-8" ? $str : @mb_convert_encoding($str, "UTF-8", $encoding);
            }
        }

        private function isUTF8($str) {
            return preg_match('%^(?:
            [\x09\x0A\x0D\x20-\x7E]           # ASCII
            | [\xC2-\xDF][\x80-\xBF]            # non-overlong 2-byte
            | \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
            | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
            | \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
            | \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
            | [\xF1-\xF3][\x80-\xBF]{3}         # planes 4-15
            | \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
            )*$%xs', $str);
        }

        public function tracking($trackingNumber, $trackBy = 'parcel', $EDI = false) 
    {        
        $tracking = new Tracking();
        $deliveredArray = array( "DELIVERED");

        if($EDI == true && !empty($this->trackingServiceId) && !empty($this->trackingAgentId))
        {
            include_once(BASE_PATH."includes/labels/octrackingstatus.class.php");
            $serviceAgentConstantFilter = new ServiceConstantValueFilter();
            $serviceAgentConstantFilter->addFilter("service_id = '" . $this->trackingServiceId . "' AND agent_id = '" . $this->trackingAgentId . "' ");
            $serviceAgentConstant = $serviceAgentConstantFilter->getConstantList("c.constant 'constant_name', scv.constant_value 'constant_value'");

            if (count($serviceAgentConstant) > 0)
            {
                foreach ($serviceAgentConstant as $serviceAgentConstantData)
                {
                    $this->constants[$serviceAgentConstantData->getConstantName()] = $serviceAgentConstantData->getConstantValue();
                }
            }

            $sftp_server = $this->constants['TRACKING_SFTP_HOST'];
            $sftp_user = $this->constants['TRACKING_SFTP_USERNAME'];
            $sftp_pass = $this->constants['TRACKING_SFTP_PASSWORD'];
            
            $localPath = SETTING_DIR_ASSETS . "tracking_data/ORANGECONNEX/";
            if(!file_exists($localPath))
            {
                @mkdir($localPath, 0777);
            }
            $remotePath = "/TRACKING_PENDING/";
            $remoteProcessedPath = "/TRACKING_PROCESSED/";
            $remoteErrorPath = "/TRACKING_ERROR/";
                
            $ftpConnection = new Net_SFTP($sftp_server, '22');
            if (!$ftpConnection->login($sftp_user, $sftp_pass)) 
            {
                echo "SFTP Login Failed, check your username and passwor";
                exit;
            } 
                
            else 
            {                
                $getAllFiles = $ftpConnection->_list($remotePath);
                
                if (count($getAllFiles) > 0) 
                {
                    foreach ($getAllFiles as $keyFile => $valueFile) 
                    {
                        $fileName = $valueFile['filename'];
                        if (in_array($fileName, array('.', '..')))
                            continue;
                        $remoteFile = $remotePath . $fileName;                        
                        $remoteProcessedFile = $remoteProcessedPath . $fileName;
                        $localFile = $localPath . $fileName;
                        
                        $ftpConnection->get($remoteFile, $localFile);
                        $ftpConnection->rename($remoteFile, $remoteProcessedFile);    
                        
                        $handle = fopen($localFile, "r");
                        $header = 0; 
                        
                        if ($handle) 
                        {
                            while (($data = fgetcsv($handle, 1000, "@")) !== FALSE) 
                            {
                                if($header == 0)
                                {
                                    $header++;
                                    continue;
                                }
                                $otherRoutingCode = removeBomUtf8(str_replace('"', '', $data[2]));
                                $consignmentFilter = new ConsignmentFilter();
                                $consignmentFilter->addFilterNew("other_routing_code = '".$otherRoutingCode."'");
                                $resultCon = $consignmentFilter->getListNew('*');
                                if(count($resultCon) >0 )
                                {
                                    $trackingNumber = $resultCon[0]->getAwb();
                                    echo $trackingNumber;
                                    echo '<br>'; //die;
                                  //  continue;
                                }
                                else
                                {
                                    continue;
                                }
                                ////////////////////// Carrier Received ////////////////////////////////
                                /*$trackingDataFilterObj = new TrackingDataFilter();
                                $trackingDataFilterObj->addFieldFilter('    tracking_number', $trackingNumber);
                                $trackingDataFilterObj->addFilter("carrier_code not in ('','MANIFEST')");
                                $trackingEvents = $trackingDataFilterObj->getList();

                                if (count($trackingEvents) > 0) {
                                    $carrierReceivedCheck = 0;   // there is already carrier received event                
                                    $trackingDataFilterObj = new TrackingDataFilter();
                                    $trackingDataFilterObj->addFieldFilter('    tracking_number', $trackingNumber);
                                    $trackingDataFilterObj->addFilter("status_code_id = '148'");
                                    $CarrierReceivedObj = $trackingDataFilterObj->getList();
                                    if (count($CarrierReceivedObj) > 0) {
                                        $carrierCodeCarrierReceived = $CarrierReceivedObj[0]->getCarrierCode();
                                        $carrierReceivedStatusCode = $CarrierReceivedObj[0]->getStatusCodeId();
                                    }
                                } else {
                                    $carrierReceivedCheck = 1; // No carrier received Event
                                }*/
                                ////////////////////// Carrier Received ////////////////////////////////

                                $dateTime = '';
                                $EventCode = $data[4];
                                $dateTime = explode('T', $data[6]);
                                $date = $dateTime[0];
                                $timeArray = explode('+', $dateTime[1]);
                                $time = $timeArray[0];
                                $DateTime = date($date.' '.$time);
                                $countryIso = $data[7];
                                $EventDescription = $data[5];           
                                
                                //$spTrackingStatus = OcTrackingStatus.$countryIso::getOweStatusCode($EventCode);
                                $entityId = 0;
                                $parcelObj = new ParcelFilter();
                                $parcelObj->addTrackingNumberFilter($trackingNumber);
                                $parcelDataArray = $parcelObj->getColumnList('p.id, p.tracking_number, p.consignment_id');
                                if (count($parcelDataArray) > 0) {
                                    $parcelData = $parcelDataArray[0];
                                    $entityId = $parcelData->getId();
                                }

                                if ($entityId > 0) {
                                    $parcelEntity = new Parcel($entityId);
                                    $finalStatusCode = $parcelEntity->getParcelStatusCode();   
                                    $classObj = 'OcTrackingStatus'. ucfirst(strtolower($countryIso));
                                    $spTrackingStatus = $classObj::getOweStatusCode($EventCode);
                                    ////////////////////// Carrier Received ////////////////////////////////
                                    $tracking->saveTrackPoint($entityId, $trackBy, $DateTime, $trackingNumber, $spTrackingStatus, $ServiceAreaDescription, $EventCode, $EventDescription, $deliveredArray, $finalStatusCode);
                                    $tracking->saveConsignmentTrackingStatus($trackingNumber, 'OcTrackingStatus'.ucfirst(strtolower($countryIso)));
                                }                    
                            }
                        }
                    }
                }
            }
        }
    }

        public function sendData($tracking_numbers = array())
        {

        }

        public function manifest($consignment)
        {

        }

        public function preAdvice($consignment)
        {

        }

        private function addWayBill(Consignment $consignment, $parcel, $trackingNo)
        {

        }

        public function recycledShipment($consignment)
        {
            $output["STATUS"] = "SUCCESS";
            return $output;
        }
        
        public function setTrackingParams($serviceId, $agentId)
    {
        $this->trackingServiceId = $serviceId;
        $this->trackingAgentId = $agentId;
    }

}

    
    
