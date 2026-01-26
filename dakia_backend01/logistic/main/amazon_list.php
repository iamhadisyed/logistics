	<?php
	// get settings
	require_once("../includes/settings/config.inc.php");
        
        include_classes([
            'marketplaceorderfilter.class',
            'marketplaceorder.class',
            'countryfilter.class',
            'country.class',
            'usermarketplacesmappingfilter.class',
            'usermarketplacesmapping.class',
            'services.class',
            'servicefilter.class',
            'user.class',
            'userfilter.class',
            'marketplaces.class',
            'marketplacesfilter.class',
            'consignment.class',
            'consignmentfilter.class',
            'marketplaceorderdetails.class',
            'marketplaceorderdetailsfilter.class',
            'currency.class',
            'userservicesroutingfilter.class',
            'userservicesrouting.class',
            'agentdatafilter.class',
            'agentdata.class',
            'serviceagentmappingdatafilter.class',
            'serviceagentmappingdata.class',
            'serviceagentmappingfilter.class',
            'serviceagentmapping.class',
            'carrierservicecustomizerulesfilter.class',
            'carrierservicecustomizerules.class',
            'carrierservicedefaultrulesfilter.class',
            'carrierservicedefaultrules.class',
            'carrier.class',
            'remoteareas.class',
            'consignmentlog.class',
            'serviceconstantvalufilter.class',
            'serviceconstantvalue.class',
            'warehouse.class',
            'serviceconstantvaluefilter.class',
            'serviceconstantvalue.class',
            'servicerangemappingfilter.class',
            'servicerangemapping.class',
            'licenceplate.class',
            'tracking.class',
            'trackingdata.class'
        ]);
        
        include_classes([
            'tcpdf',
            ], '3rdparty/tcpdf');
        
        include_classes([
            'pdfmerger'   ], 'labels');
        
	class Page extends BasePage {
        private $breadcrumb = '';
        private $user = NULL;
        private $status_array; 
        private $amazon_status_array = array("Shipped",
                                             "UnShipped",
                                             "Pending",
                                             "Delivered",
                                             "Canceled");
        private $labelError;
        private $marketPlaceOrderFilter;
        private $mergeFileName;
        /* * *
         * Controller logic
         */
        protected function init() 
        {
            $this->user =  SessionManager::getUser();
            $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
                'amazon_list.php' => 'Amazon'
            );
            
            $this->marketPlaceOrderFilter = new MarketPlaceOrderFilter();
            // common initialisation for ths page
            $this->setTitle("Orders List");
            if (isset($_GET['action']) && $_GET['action'] == "amazon_ajax") 
            {
                $this->marketPlaceOrderFilter->addJoin("marketplace_order_details ord_details", "ord.id = ord_details.marketplace_order_id", "");
                $this->marketPlaceOrderFilter->addFieldFilter("ord.user_id" , $this->user->getId(), "");
                /*
                 * Set columns orders for sorting
                 */
                /*
                if (isset($this->form_vars['order'][0]['column']) && $this->form_vars['order'][0]['column'] != 0) {
                    $dataTableColumnId = $this->form_vars['order'][0]['column'];
                    $orderBy = $this->form_vars['order'][0]['dir'];
                    $orderFalse = TRUE;
                    if ($orderBy == 'desc') {
                        $orderFalse = FALSE;
                    }
                    $dataTableColumnName = ucfirst($this->form_vars['columns'][$dataTableColumnId]['data']);
                    //$functionName = 'AddOrderBy' . $dataTableColumnName;
                    //echo $functionName; die;
                    $this->marketPlaceOrder->AddOrderBy(strtolower("l." . $dataTableColumnName), $orderFalse);
                } */
                $this->addFilters();
                $iTotalRecords = $this->marketPlaceOrderFilter->getPagingCount();
                $iDisplayLength = intval($_REQUEST['length']);
                $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
                $iDisplayStart = intval($_REQUEST['start']);
                $sEcho = intval($_REQUEST['draw']);
                $end = $iDisplayStart + $iDisplayLength;
                $end = $end > $iTotalRecords ? $iTotalRecords : $end;
                $this->marketPlaceOrderFilter->setRowsPerPage($iDisplayLength);
                // the offset of the list, based on current page
                $this->marketPlaceOrderFilter->setOffset($iDisplayStart);
                if ($dataTableColumnName != '') 
                {
                    $this->marketPlaceOrderFilter->AddOrderBy($dataTableColumnName, $orderFalse);
                } 
                else 
                {
                    $this->marketPlaceOrderFilter->AddOrderBy('ord.id', false);
                }
                    
                $orders_list = $this->marketPlaceOrderFilter->getPagingList(); 
                $amazonDataArr = array();
                $rangeDataArr = array();

                foreach ($orders_list as $orders) 
                {
                    $marketPlaceId                = $orders->getMarketPlaceId();
                    $marketPlace                  = new MarketPlaces($marketPlaceId);
                    $marketPlaceName              = $marketPlace->getTitle();

                    $amazonDataArr['ordernumber'] = $orders->getMarketPlaceOrderNumber();
                    $amazonDataArr['actionss']    = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline"><input type=checkbox id= "delete55" name="ordersArray[]"  value="' . $amazonDataArr['ordernumber'] . '"  class="group-checkable" /><span></span></label>';                
                    $amazonDataArr['created']     = $orders->getCreateTime();                        
                    $amazonDataArr['description'] = '<a class="popovers" data-trigger="hover" data-content="'.$orders->getTitle().'" >'.substr($orders->getTitle(), 0, 20).'</a>';
                    $marketPlaces                 = new MarketPlaces($marketPlaceId);
                    $marketPlaceLogo              = $marketPlace->getIntegrationLogo();
                    $amazonDataArr['marketplace'] = '<img src=\'../images/' . $marketPlaceLogo . '\' / width=16>&nbsp;&nbsp;&nbsp;'.  $marketPlaceName;

                    if($orders->getReceiverName() != '')
                    {
                        $amazonDataArr['contact']     = $orders->getReceiverName();
                    } 
                    else 
                    {
                        $amazonDataArr['contact']     = '';
                    }
                        
                    if($orders->getReceiverCountryId() > 0)
                    {
                        $countryId                    = $orders->getReceiverCountryId();
                        $countryObj                   = new Country($countryId);
                        $amazonDataArr['country']     = '<img src=../assets/global/img/flags/'.strtolower($countryObj->getIso()).'.png /> &nbsp;&nbsp;&nbsp; ' .$countryObj->getIso();                           
                    }
                    else
                    {                            
                         $amazonDataArr['country']    = '';
                    }

                    if($orders->getReceiverCountryId() != '')
                    {
                        $amazonDataArr['city']        = $orders->getReceiverCity();
                    }
                    else
                    {
                        $amazonDataArr['city'] = '';
                    }

                    $amazonDataArr['status']      = $orders->getOrderStatus();;
                    $amazonDataArr['price']       = $orders->getItemPrice().' '. $orders->getCurrency();
                    $consignmentId                = $orders->getConsignmentId();

                    if($consignmentId > 0 )
                    {
                        $consignment = new Consignment($consignmentId);
                        $amazonDataArr['tracking_no'] = $consignment->getAwb();                              
                    }
                    else
                    {
                        $amazonDataArr['tracking_no'] = '';                         
                    }

                    $amazonDataArr['reason'] = ''; 
                    $marketplaces = new MarketPlaces($orders->getMarketPlaceOrderId());
                    $logo = $marketPlaces->getIntegrationLogo();
                    $amazonDataArr['label']  = '<a class="showOrderDetail btn-xs blue btn mt-ladda-btn ladda-button btn-outline" rel="tooltip" 
                                                data-toggle="modal" data-target="#show-orderDetail-popup" role="dialog" tabindex="-1" 
                                                data-orderid                ="' . $orders->getId() . '"
                                                data-trackingnumber         ="' . $amazonDataArr['tracking_no'] . '"
                                                data-marketplaceorderid     ="' . $orders->getMarketPlaceOrderId() . '"
                                                data-marketplaceordernumber = "'.$orders->getMarketPlaceOrderNumber().'"
                                                data-consignmentid = "'.$consignmentId.'"
                                                data-marketplacelogo =  "'.$logo.'"
                                                data-action = "ITEMORDERDETAIL"
                                                data-load = "amazon_list.php"
                                                href="javascript:;">
                                                <span class=" fa fa-globe"></span>
                                                </a>';
                    $rangeDataArr[] = $amazonDataArr;
                } 
                $rangeDataArr['data'] = $rangeDataArr;
                $rangeDataArr['draw'] = $sEcho;
                $rangeDataArr['recordsTotal'] = $iTotalRecords;
                $rangeDataArr['recordsFiltered'] = $iTotalRecords;
                echo json_encode($rangeDataArr);
                die; 
            }
            else if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "generateLabel") 
            {
                $marketPlaceOrderId       = $this->form_vars["marketPlaceOrderId"];
                $marketPlaceOrderDetailId = $this->form_vars["marketPlaceOrderDetailId"];
                $weight                   = $this->form_vars["weight"];
                $numberOfPieces           = $this->form_vars["numberPieces"];
                $serviceId                = $this->form_vars["service"];
                
                $length                   = 1; 
                $width                    = 1; 
                $height                   = 1;
                $marketPlaceOrder         = new MarketPlaceOrder($marketPlaceOrderId);
                $marketOrderDetails       = new MarketPlaceOrderDetails($marketPlaceOrderDetailId);
                   
                $userId = $this->user->getId();
                $userAccountId = $this->user->getUserAccountId();
                $user = new User($userId);
                $userAccount = new CustomerAccount($userAccountId);
                $serflr = new Services($serviceId);
                if (sizeof($serflr) > 0) 
                {
                    $service_id = $serflr->getId();
                    $consignmentArray['service'] = $service_id;
                    if($serflr->getIsCustomized())
                        $consignmentArray['is_product'] = 1;
                    else
                        $consignmentArray['is_product'] = 0;               
                }
                $conFilter = new ConsignmentFilter();
                $conFilter->addFieldFilter("    hawb", $marketPlaceOrder->getMarketPlaceOrderNumber());
                $result = $conFilter->getConList("id");
    
                if(count($result) >0)
                {
                $consignmentArray['id'] = $result[0]->getId();
                }
                
                $consignmentArray['sender_company'] = substr(($userAccount->getCompany()), 0, 100);
                $consignmentArray['sender_contact'] = substr(($userAccount->getFullName()), 0, 100);
                $consignmentArray['sender_email']  = $this->replaceSpecial($userAccount->getEmail());
                $consignmentArray['sender_telephone']  = (substr(str_replace(' ', '', $userAccount->getTelephone()), 0, 20));
                $consignmentArray['sender_address_line_1'] = $this->replaceSpecial(substr($user->getAddress(), 0, 50));
                $consignmentArray['sender_address_line_2'] = $this->replaceSpecial(substr($user->getAddress2(), 0, 50));
                $consignmentArray['sender_address_line_3'] = $this->replaceSpecial(substr($user->getAddress3(), 0, 50));
                $consignmentArray['sender_city'] = $this->replaceSpecial(substr((strtoupper($user->getCity())), 0, 50));
                $consignmentArray['sender_state'] = $this->replaceSpecial(substr((strtoupper($user->getState())), 0, 50));
                $consignmentArray['sender_postcode'] = (substr($user->getPostCode(), 0, 11));
                $consignmentArray['sender_country'] = $user->getCountryId(); 
                // Set Receiver Data
                $consignmentArray['order_reference'] = $marketPlaceOrder->getMarketPlaceOrderNumber();                    
                $consignmentArray['receiver_company'] = substr($marketPlaceOrder->getReceiverName(), 0, 100);
                $consignmentArray['receiver_contact'] = substr($marketPlaceOrder->getReceiverName(), 0, 100);
                $consignmentArray['receiver_email']  = '';//$this->replaceSpecial($marketPlaceOrder->getReceiverName());
                $consignmentArray['receiver_telephone']  = (substr(str_replace(' ', '', $marketPlaceOrder->getReceiverPhone()), 0, 20));
                $consignmentArray['receiver_address_line_1'] = $this->replaceSpecial(substr(trim($marketPlaceOrder->getReceiverAddressLine1()), 0, 50));
                //echo strlen($marketPlaceOrder->getReceiverAddressLine2()); die;
                $consignmentArray['receiver_address_line_2'] = $this->replaceSpecial(substr(trim($marketPlaceOrder->getReceiverAddressLine2()), 0, 50));
               /// $consignmentArray['receiver_address_line_3'] = $this->replaceSpecial(substr($marketPlaceOrder->getReceiverAddressLine3(), 0, 50));
                $consignmentArray['receiver_city'] = $this->replaceSpecial(substr((strtoupper($marketPlaceOrder->getReceiverCity())), 0, 50));
                $consignmentArray['receiver_state'] = $this->replaceSpecial(substr((strtoupper($marketPlaceOrder->getReceiverState())), 0, 50));
                $consignmentArray['receiver_postcode'] = trim($marketPlaceOrder->getReceiverPostCode());
                $consignmentArray['receiver_country'] = $marketPlaceOrder->getReceiverCountryId();
                // Set remaning fields
                $consignmentArray['reference'] = (substr((strtolower($marketOrderDetails->getMarketPlaceItemId())), 0, 20));
                $consignmentArray['item_value'] = $this->replaceSpecial($marketOrderDetails->getItemPrice());
                $consignmentArray['item_currency'] = $this->replaceSpecial($marketOrderDetails->getCurrency());
                $consignmentArray['item_type'] = '';
                $consignmentArray['notes'] = (substr($this->replaceSpecial($marketOrderDetails->getTitle()), 0, 100));
                $consignmentArray['description'] = (substr($this->replaceSpecial($marketOrderDetails->getTitle()), 0, 100));
                // Calculate per piece weight ------(weight/no of pieces)
                $consignmentArray['parcel'] = [];
                $weight    = $weight/$numberOfPieces;  // do weight validation
                $count = 0;
                for($count=0; $count<$numberOfPieces; $count++ ) //for loop change .. upto number of pieces
                {
                    $consignmentArray['parcel'][$count]['weight'] = $weight;
                    $consignmentArray['parcel'][$count]['length'] = 1;
                    $consignmentArray['parcel'][$count]['height'] = 1;
                    $consignmentArray['parcel'][$count]['width']  = 1;
                }
                $output = array();	            
                $result = Consignment::saveShipment($consignmentArray, $userId);
                  
                if(trim($result['STATUS']) == 'ERROR')
                {                       
                    if(is_array($result['MESSAGE']))
                    {
                        $output['MESSAGE'] = implode(",",$result['MESSAGE']);
                    }
                    else 
                    {
                        $output['MESSAGE'] = $result['MESSAGE'];
                    }                       
                    $output['STATUS'] = 'ERROR';
                } 
                else 
                {
                    $consignmentId = $result['CONSIGNMENT_ID'];
                    if($consignmentId > 0) 
                    {
                        $labelType = (!empty($formPostArray["label_type"]) ? $formPostArray["label_type"] : 'pdf');
                        $labelSize = (!empty($formPostArray["label_size"]) ? $formPostArray["label_size"] : '100x150');
                        $marketPlaceOrder->setConsignmentid($consignmentId);
                        $marketPlaceOrder->save();
                        $consignment = new Consignment($consignmentId);
                        $output = Consignment::getInstantLabel($consignment,$labelType,$labelSize,true);
                        //print_r($output); die;
                        //$output['LABEL'] = $output['LABEL'];
                    }
	        }
                echo json_encode($output);
                die;
	    }
            // check if all same products are selected 
            else if(isset($this->form_vars["func"]) && $this->form_vars["func"] == "bulkLabelVerification")
            {
                $idArray = array();
                $titleArray = array();

                foreach($_POST["conIdArr"] as $orderNumber)
                {
                    $marketPlaceOrderFlr = new MarketPlaceOrderFilter();
                    $marketPlaceOrderFlr->addFieldFilter('    marketplace_order_number',$orderNumber);
                    $id = $marketPlaceOrderFlr->getColumnList("marketplace_order_number");
                    $idArray[] = $id[0]->getId(); 
                }

                foreach($idArray as $marketPlaceTableId)
                {
                    $marketPlaceOrderDetailFlr = new MarketPlaceOrderDetailsFilter();
                    $marketPlaceOrderDetailFlr->addFieldFilter("    marketplace_order_id",$marketPlaceTableId);
                    $title = $marketPlaceOrderDetailFlr->getColumnList("title");
                    $titleArray[] = $title[0]->getTitle();
                }

                if (count(array_unique($titleArray)) === 1 && end($titleArray) === $titleArray[0]) 
                {
                    $output["STATUS"] =  "SUCCESS";
                    $output["MESSAGE"] = "SUCCESS";
                    echo json_encode($output);
                    die;
                }
                else
                {
                    $output["STATUS"] =  "ERROR";
                    $output["MESSAGE"] = "Please select the same product for bulk action";
                    echo json_encode($output);
                    die;
                }
            }
                
            else if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "generateBulkLabel") 
            {
                $bulkWeight = $_POST["bulk_weight"]; 
                $bulkNumberPieces = $_POST["bulk_number_pieces"];
                $bulkservice = $_POST['bulk_service'];
                $bulkIds = $_POST['orderIdArr'];   
                $output = array();
                $pdfMerger = new PDFMerger();
                $time = date('Ymdhis'); 
                $mergeFileName = '../_assets/pdf/' . date('Y_m_d') . '/' ."merged".$time.".pdf";
                 
                foreach($bulkIds as $Ids)
                {
                    $marketPlaceOrderFilter   = new MarketPlaceOrderFilter();
                    $marketPlaceOrderFilter->addFieldFilter("    marketplace_order_number", $Ids);
                    $result = $marketPlaceOrderFilter->getColumnList('id');
                    $marketPlaceOrderId = $result[0]->getId();

                    $marketPlaceOrderDetailsFilter = new MarketPlaceOrderDetailsFilter();
                    $marketPlaceOrderDetailsFilter->addFieldFilter("marketplace_order_id", $marketPlaceOrderId);
                    $result = $marketPlaceOrderDetailsFilter->getColumnList('id');
                    $marketPlaceOrderDetailId = $result[0]->getId();

                    $weight                   = $bulkWeight;
                    $numberOfPieces           = $bulkNumberPieces;
                    $serviceId                = $bulkservice;

                    $length                   = 1; 
                    $width                    = 1; 
                    $height                   = 1;
                    $marketPlaceOrder         = new MarketPlaceOrder($marketPlaceOrderId);
                    $marketOrderDetails       = new MarketPlaceOrderDetails($marketPlaceOrderDetailId);

                    // Set Sender Data
                    $userId = $this->user->getId();
                    $userAccountId = $this->user->getUserAccountId();
                    $user = new User($userId);
                    $userAccount = new CustomerAccount($userAccountId);
                    $serflr = new Services($serviceId);
                    if (sizeof($serflr) > 0) 
                    {
                        $service_id = $serflr->getId();
                        $consignmentArray['service'] = $service_id;
                        if($serflr->getIsCustomized())
                            $consignmentArray['is_product'] = 1;
                        else
                            $consignmentArray['is_product'] = 0;               
                    }
                    $conFilter = new ConsignmentFilter();
                    $conFilter->addFieldFilter("    hawb", $marketPlaceOrder->getMarketPlaceOrderNumber());
                    $result = $conFilter->getConList("id");

                    if(count($result) >0)
                    {
                        $consignmentArray['id'] = $result[0]->getId();                      
                    }

                    $consignmentArray['sender_company'] = substr(($userAccount->getCompany()), 0, 100);
                    $consignmentArray['sender_contact'] = substr(($userAccount->getFullName()), 0, 100);
                    $consignmentArray['sender_email']  = $this->replaceSpecial($userAccount->getEmail());
                    $consignmentArray['sender_telephone']  = (substr(str_replace(' ', '', $userAccount->getTelephone()), 0, 20));
                    $consignmentArray['sender_address_line_1'] = $this->replaceSpecial(substr($user->getAddress(), 0, 50));
                    $consignmentArray['sender_address_line_2'] = $this->replaceSpecial(substr($user->getAddress2(), 0, 50));
                    $consignmentArray['sender_address_line_3'] = $this->replaceSpecial(substr($user->getAddress3(), 0, 50));
                    $consignmentArray['sender_city'] = $this->replaceSpecial(substr((strtoupper($user->getCity())), 0, 50));
                    $consignmentArray['sender_state'] = $this->replaceSpecial(substr((strtoupper($user->getState())), 0, 50));
                    $consignmentArray['sender_postcode'] = (substr($user->getPostCode(), 0, 11));
                    $consignmentArray['sender_country'] = $user->getCountryId(); 
                    // Set Receiver Data
                    $consignmentArray['order_reference'] = $marketPlaceOrder->getMarketPlaceOrderNumber();                    
                    $consignmentArray['receiver_company'] = substr($marketPlaceOrder->getReceiverName(), 0, 100);
                    $consignmentArray['receiver_contact'] = substr($marketPlaceOrder->getReceiverName(), 0, 100);
                    $consignmentArray['receiver_email']  = '';//$this->replaceSpecial($marketPlaceOrder->getReceiverName());
                    $consignmentArray['receiver_telephone']  = (substr(str_replace(' ', '', $marketPlaceOrder->getReceiverPhone()), 0, 20));
                    $consignmentArray['receiver_address_line_1'] = $this->replaceSpecial(substr($marketPlaceOrder->getReceiverAddressLine1(), 0, 50));
                    $consignmentArray['receiver_address_line_2'] = $this->replaceSpecial(substr(trim($marketPlaceOrder->getReceiverAddressLine2()), 0, 50));
                    $consignmentArray['receiver_city'] = $this->replaceSpecial(substr((strtoupper($marketPlaceOrder->getReceiverCity())), 0, 50));
                    $consignmentArray['receiver_state'] = $this->replaceSpecial(substr((strtoupper($marketPlaceOrder->getReceiverState())), 0, 50));
                    $consignmentArray['receiver_postcode'] = trim($marketPlaceOrder->getReceiverPostCode());
                    $consignmentArray['receiver_country'] = $marketPlaceOrder->getReceiverCountryId();
                    // Set remaning fields
                    $consignmentArray['reference'] = (substr((strtolower($marketOrderDetails->getMarketPlaceItemId())), 0, 20));
                    $consignmentArray['item_value'] = $this->replaceSpecial($marketOrderDetails->getItemPrice());
                    $consignmentArray['item_currency'] = $this->replaceSpecial($marketOrderDetails->getCurrency());
                    $consignmentArray['item_type'] = '';
                    $consignmentArray['notes'] = (substr($this->replaceSpecial($marketOrderDetails->getTitle()), 0, 100));
                    $consignmentArray['description'] = (substr($this->replaceSpecial($marketOrderDetails->getTitle()), 0, 100));
                    // Calculate per piece weight ------(weight/no of pieces)
                    $consignmentArray['parcel'] = [];
                    $weight    = $weight/$numberOfPieces;  // do weight validation
                    $count = 0;

                    for($count=0; $count<$numberOfPieces; $count++ ) //for loop change .. upto number of pieces
                    {
                        $consignmentArray['parcel'][$count]['weight'] = $weight;
                        $consignmentArray['parcel'][$count]['length'] = 1;
                        $consignmentArray['parcel'][$count]['height'] = 1;
                        $consignmentArray['parcel'][$count]['width']  = 1;
                    }
                    
                    $result = Consignment::saveShipment($consignmentArray, $userId);
                    
                    $outputtemp = [];
                    if(trim($result['STATUS']) == 'ERROR')
                    {                       
                        if(is_array($result['MESSAGE']))
                        {                            
                            $outputtemp['MESSAGE'] = implode(",",$result['MESSAGE']);
                        }
                        else 
                        {
                            $outputtemp['MESSAGE'] = $result['MESSAGE'];
                        }                       
                        $outputtemp['STATUS'] = 'ERROR';
                        $output[] = $outputtemp;
                    } 
                    else 
                    {
                        $consignmentId = $result['CONSIGNMENT_ID'];
                        if($consignmentId > 0) 
                        {
                            $labelType = (!empty($formPostArray["label_type"]) ? $formPostArray["label_type"] : 'pdf');
                            $labelSize = (!empty($formPostArray["label_size"]) ? $formPostArray["label_size"] : '100x150');
                            $marketPlaceOrder->setConsignmentid($consignmentId);
                            $marketPlaceOrder->save();
                            $consignment = new Consignment($consignmentId);
                            $outputLabel = Consignment::getInstantLabel($consignment,$labelType,$labelSize,true);                            
                            
                            if($outputLabel["STATUS"] == "SUCCESS")
                            {
                                $outputtemp["STATUS"] = "SUCCESS";
                                $outputtemp["LABEL"] = $outputLabel['LABEL'];
                                $outputtemp["MERGEDLINK"] = $mergeFileName;
                                $output[] = $outputtemp;
                                
                                $position = strpos($outputLabel['LABEL'],'_assets' );
                                $localLink = substr($outputLabel['LABEL'], $position);
                                $pdfMerger->addPDF('../'.$localLink, 'all');                                                       
                            }                      
                        }
                    }                   
                } 
                
                $pdfMerger->merge('file', $mergeFileName);   
                echo json_encode($output);
                die;
            }
                
            // Get Values for Pop up 
            else if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "ITEMORDERDETAIL") 
            {
                $consignmenId = '';
                $marketPlaceOrderDetailId            = $this->form_vars["orderid"];
                $trackingNumber                      = $this->form_vars["trackingnumber"];
                $marketPlaceOrderId                  = $this->form_vars["marketplaceorderid"];
                $marketplaceordernumber              = $this->form_vars["marketplaceordernumber"];
                $consignmentId                       = $this->form_vars["consignmentid"];

                $marketOrderDetails = new MarketPlaceOrderDetails($marketPlaceOrderDetailId);

                $marketPlaceOrder   = new MarketPlaceOrder($marketPlaceOrderId);
                ///Getting marketplace logo
                $marketPlaces     = new MarketPlaces($marketPlaceOrder->getMarketPlaceId());
                $marketPlaceLogo  = "../images/".strtolower($marketPlaces->getIntegrationLogo());

                ///////////////END/////////////////
                $countryId          = $marketPlaceOrder->getReceiverCountryId();
                $countryObj         = new Country($countryId);
                $country            = $countryObj->getIso();
                $title              = $marketOrderDetails->getTitle();
                $asin               = $marketOrderDetails->getAsin();
                $quantity           = $marketOrderDetails->getQuantityPurchased();
                $amount             = $marketPlaceOrder->getOrderTotal();
                $contact            = $marketPlaceOrder->getReceiverName();
                $addressLine1       = $marketPlaceOrder->getReceiverAddressLine1();
                $addressLine2       = $marketPlaceOrder->getReceiverAddressLine2();
                $city               = $marketPlaceOrder->getReceiverCity();
                $postcode           = $marketPlaceOrder->getReceiverPostCode();
                $telephone          = $marketPlaceOrder->getReceiverPhone();

                //$output .= '<div id= "testdiv"> <img src='.$marketPlaceLogo.'></img></div>';
                $output .= '<div id ="res_message_popup" class="row display-none">
                            <div class="col-md-12">
                            <div class="alert alert-success"></div>
                            </div>
                            </div>';
                $output .= '<div class="row">
                            </div>';
                $output .= '<div class="col-md-6">
                            <h4 class="text-primary"><strong>Order Detail:</strong></h4>
                            <table  class="table table-striped table-bordered table-hover">
                            <tr>
                            <td>
                            <strong>Title:</strong> 
                            <td>
                            '.$title.'
                            </td>                       
                            </tr>
                            <tr>
                            <td><strong>ASIN:</strong></td>
                            <td>'.$asin.'</td>
                            </tr>
                            <tr>
                            <td width="85px"><strong>Seller SKU:</strong></td>
                            <td>'.$quantity.'</td>
                            </tr>
                            <tr>
                            <td>
                            <strong>Price:</strong>
                            </td>
                            <td>'.$amount.'</td>                        
                            </tr>
                            </table>
                            </div>
                            ';
                $output .= '<div class="col-md-6">
                            <h4 class="text-primary"><strong>Shipping Detail:</strong></h4>
                            <table  class="table table-striped table-bordered table-hover">
                            <tr>
                            <td><strong>Country:</strong></td>
                            <td>'.$country.'</td>
                            </tr>
                            <tr>
                            <td><strong>PostCode:</strong></td>
                            <td>'.$postcode.'</td>
                            </tr>
                            <tr>
                            <td>
                            <strong>Telephone:</strong>
                            </td>
                            <td>'.$telephone.'</td>                        
                            </tr>
                            <tr>
                            <td>
                            <strong>Address:</strong> 
                            <td> '.$addressLine1.' '.$addressLine2.' '.$city.' </td>                       
                            </tr>
                            </table>
                            </div>
                        </div>';
                $output .= '<h4 class="text-primary"><strong>OneWorld Service:</strong></h4>';

                            //if shipment is already in the system and label has been generated then don't show weight service dropdown etc.
                          
                            if($consignmentId > 0 )
                            {
                                $con = new Consignment($consignmentId);
                                $weight = $con->getWeight();
                                $numberPieces = $con->getNumberPieces();
                                $serviceId = $con->getServiceId();
                                $ser = new Services($serviceId);
                                $serviceName = $ser->getName();
                                
                                $labelUrl = "http://local.oneworldexpress.co.uk:8080/_assets/pdf/".$con->getLabelFile();
                                $numberPieces = $con->getNumberPieces();
                                
                                $output .= '<input type="hidden" class="form-control form-filter" name="weight" id= "weight" value='.$weight.'>
                                            <input type="hidden" class="form-control form-filter" name="numberPieces" id= "noofpieces" value = '.$numberPieces.'>
                                            <input type="hidden" class="form-control form-filter" name="service" id= "service" value = '.$serviceName.'>';
                               
                                $output .= '
                                             <div class="row">
                                             <div class="col-md-4">
                                             <strong>Weight:</strong>
                                             <span>'.$weight.'</span>
                                             </div>
                                             <div class="col-md-4">
                                             <strong>Service:</strong>
                                             <span>'.$serviceName.'</span>
                                             </div>
                                             <div class="col-md-4">
                                             <strong>No. of Pieces:</strong>
                                             <span>'.$numberPieces.'</span>
                                             </div>';
                            }
                            else
                            {
                                $output .= '<div class="row">	
                                <div class="col-md-4">

                                <strong>Select a Service:</strong>';
                                $userAccountId = $this->user->getUserAccountId();
                                $fromCountry = $this->user->getCountryId();
                                $serviceFilter = new ServiceFilter();
                                $servicesData = $serviceFilter->getUserAccountServices($userAccountId, $fromCountry, $countryId, 'D');
                                $output .= '<select name="service" id="service" class="bs-select form-control" data-live-search="true" data-show-subtext="true" data-toggle="tooltip" title="" data-original-title="Service" data-container="body" placeholder="Service">'; 
                                $output .= '<option value="">Please Select</option>';
                                /*
                                *   Standard base customer
                                */
                                $selectedStr = "";
                                if (count($servicesData) > 0) 
                                {
                                    foreach ($servicesData as $userServiceData) 
                                    {
                                        $selectedStr = ($userServiceData->getId() == $selected ? ' selected="selected"' : '');
                                        $output .= '<option ' . $selectedStr .  '" value="' . $userServiceData->getId() . '">' . $userServiceData->getName() . '</option>';
                                    }
                                }
                                $output .='</select>                                          
                                           </div>
                                           <div class="col-md-4">
                                           <strong>Weight:</strong>
                                           <input type="text" class="form-control form-filter" name="weight" id= "weight">
                                           </div>
                                           <div class="col-md-4">
                                           <strong>Number of Pieces:</strong>
                                           <input type="text" class="form-control form-filter" name="number_pieces" id= "number_pieces">
                                           </div>';
                            }                                         
                            $output .= '<input type="hidden" name="marketplaceorderid" id="marketplaceorderid" value="'. $marketPlaceOrderId.'" class="form-control"/>
                                       <input type="hidden" name="marketplaceorderdetailid" id="marketplaceorderdetailid" value="'. $marketPlaceOrderDetailId .'" class="form-control"/>
                                       <input type="hidden" name="labelLink" id="labelLink" value="'.$labelUrl.'" class="form-control"/> 
                                       </div>';
                echo $output;
                exit;
                } 
	    }
            
	    public function replaceSpecial($str) 
            {
	        $chunked = str_split($str, 1);
	        $str = "";
	        foreach ($chunked as $chunk) 
                {
	            $num = ord($chunk);
	            // Remove non-ascii & non html characters
	            if ($num >= 32 && $num <= 123) 
                    {
	                $str .= $chunk;
	            }
	        }
	        return mb_convert_encoding($str, 'UTF-8');
	    }
            
	    public function addFilters()
            {
	        $this->user = $user = SessionManager::getUser();
	        $dataTableColumnName = $this->orderByDT;
                /*
                 * Column filter
                 * For search
                */
                $searchDateFrom = $this->form_vars['search_Date_from'];
	        $searchDateTo   = $this->form_vars['search_Date_to'];
	        if (!empty($searchDateFrom) || !empty($searchDateTo))
	            $this->marketPlaceOrderFilter->addDateFilter($searchDateFrom, $searchDateTo, '');
	        
                $searchMarketPlace = $this->form_vars['search_MarketPlace'];
	        if (!empty($searchMarketPlace))
	        {
	            $this->marketPlaceOrderFilter->addFieldFilter('marketplace_id', trim($searchMarketPlace));
	        }
	        
                $searchHAWB = $this->form_vars['search_HAWB'];
	        if (!empty($searchHAWB))
	            $this->marketPlaceOrderFilter->addFieldLikeFilter('marketplace_order_number', trim($searchHAWB));
	        
                $searchName = $this->form_vars['search_Name'];
	        if (!empty($searchName))
	            $this->marketPlaceOrderFilter->addFieldLikeFilter('receiver_name', trim($searchName));
	        
                $searchAddress = $this->form_vars['search_City'];
	        if (!empty($searchAddress))
	            $this->marketPlaceOrderFilter->addFieldLikeFilter('receiver_city', trim($searchAddress));
	        
                $searchCountry = $this->form_vars['search_Country'];
	        if (!empty($searchCountry))
	        {                    
	            $this->marketPlaceOrderFilter->addFieldFilter("receiver_country_id", $searchCountry);                    
	        }
	        //echo $this->form_vars['search_Status']; die
                $searchStatus = $this->form_vars['search_Status'];              
	        if ($searchStatus != '')
	        {
                    $amazon_status = $this->amazon_status_array[$searchStatus];
                    $this->marketPlaceOrderFilter->addFieldFilter("    order_status",trim($amazon_status));  
	        }
                
                $searchDescription = $this->form_vars['search_Description'];              
	        if ($searchDescription != '')
	        {
                    $this->marketPlaceOrderFilter->addFieldFilter("    title",trim($searchDescription));  
	        }
                
                $searchTracking = $this->form_vars['search_Tracking'];
	        if (!empty($searchTracking))
	        {                   
	            $this->marketPlaceOrderFilter->addJoin("consignment c","c.id = ord.consignment_Id","");             
	            $this->marketPlaceOrderFilter->addFieldFilter("c.awb",$searchTracking);
	        }	        
	    }
	    /*     * *
	     * Insert content in to HTML Head section
	     */
	    public function addDateFilter($date_value1, $date_value2, $filterDate,$filter='marketplaceorderfilter') 
	    {
	            $this->filter .= " AND ";
	            $this->filter .= "(c.date_created>='" . date('Y-m-d 00:00:00',strtotime($date_value1)) . "'";
	            $this->filter .= " AND ";
	            $this->filter .= "c.date_created<='" . date('Y-m-d 23:59:59',strtotime($date_value2)) . "')";       
	    }
	    protected function renderHead() 
	    {
	    }
	    protected function addPagelavelCss() 
	    {
	        ?>
                <link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
	        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
	        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
	        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
	        <!-- <link rel="stylesheet" href="../assets/global/css/bootstrap-select.min.css" /> -->
	        <link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
	        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
	        <link href="../assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet" type="text/css" />
	        <?php
	    }
	    public function addPagelavelJs() 
	    {
	        ?>
                <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
	        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
	        <script type="text/javascript" src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
	        <script src="../js/validator.min.js" type="text/javascript"></script>
	        <script type="text/javascript" src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
	        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
	        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
	        <script src="../js/bootstrap-select.min.js"></script>
	        <script src="../js/validator.min.js" type="text/javascript"></script> 
	        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
	        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
	        <script src="../assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js" type="text/javascript"></script>
	        <script src="../assets/global/plugins/quicksearch/jquery.quicksearch.js" type="text/javascript"></script>
	        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
                <?php
	    }
	    protected function renderFooter() 
	    {
	        ?>
                
	        <script>
                function popupwindow(url, title, w, h)
                {
                    var left = (screen.width / 2) - (w / 2);
                    var top = (screen.height / 2) - (h / 2);
                    return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
                }
	        
                $(document).ready(function () {
                    
	            if ($('.date-picker').length > 0) {
	                    //init date pickers
	                $('.date-picker').datepicker({
	                        autoclose: true
	                });
	        }
                $(document).on('click', '#btn_Cancel', function () {
                    $.ajax({
                        method: "POST",
                        url: "amazon_list.php",
                        data: {func: "cancelrecord"}
                    }).done(function (data) {
                        $("#OrderDetailForm")[0].reset();
                    });
                });

                $(document).on('click','#btn_ViewLabel', function(){
                    var LabelLink = $('#labelLink').val();
                    popupwindow(LabelLink, 'Label View', 550, 400);                    
                });
                
                $(document).on('click','#btn_bulkLabelLink', function(){
                    var LabelLink = $('#bulkLabelLink').val();
                    alert(LabelLink);
                    popupwindow(LabelLink, 'Label View', 550, 400);                    
                });
               
                    DataTableFun.init();
                   
                        $(document).on('click', '.showOrderDetail', function () {
                        var e = $(this);
                        var orderid = e.data('orderid');
                        var trackingnumber = e.data('trackingnumber');
                        var marketplaceorderid = e.data('marketplaceorderid');
                        var marketplaceordernumber = e.data('marketplaceordernumber');
                        var consignmentid = e.data('consignmentid');
                        var marketPlaceLogo = e.data('marketplacelogo');

                        if(trackingnumber == '')
                        {
                            $('#btn_ViewLabel').hide();
                            $('#btn_DispatchLabel').hide();
                            $('#btn_GenerateLabel').show();
                        }
                        else
                        {
                            $('#btn_ViewLabel').show();
                            $('#btn_DispatchLabel').show();
                            $('#btn_GenerateLabel').hide();
                            //document.getElementById("weight").readOnly = true;
                           // $("#weight").attr("readonly", true);
                           // $("#number_pieces").attr("readonly", true);
                           // $("#service").prop("readonly", true);
                        }

                        $('#marketplaceorderid').val(marketplaceorderid);
                        $('#marketplaceorderdetailid').val(orderid);
                        $('#OrderDetail').html(marketplaceordernumber);
                        $('#consignmentId').val(consignmentid);
                        $('#flag_marketplace').html("<img src=../images/" + marketPlaceLogo + " width= '40px' height='40px'>");

                        var action = e.data('action');
                        var url = e.data('load');
                        $.post(url, {func: action, orderid: orderid, trackingnumber: trackingnumber, 
                            marketplaceorderid: marketplaceorderid, marketplaceordernumber: marketplaceordernumber, consignmentid : consignmentid
                        }, function (d) {
                            $("#order-content-display").html(d);
                        });
                    }); 
                });
         
                  
            
	           var grid = "";
	            var DataTableFun = function () {
	                var handleDataTable = function () {
	                    grid = new Datatable();
	                    grid.init({
	                        src: $("#manage-data-table"),
	                        onSuccess: function (grid) {
	                            // execute some code after table records loaded
	                        },
	                        onError: function (grid) {
	                            // execute some code on network or other general error  
	                        },
                                dataTable: {// here you can define a typical datatable settings from http://datatables.net/usage/options 
	                            "lengthMenu": [
	                                [10, 20, 50, 100],
	                                [10, 20, 50, 100] // change per page values here 
	                            ],
	                            "pageLength": 10, // default record count per page
	                            "ajax": {
	                                "url": "amazon_list.php?action=amazon_ajax", // ajax source
	                                headers: {
	                                },
	                            },
	                            "bStateSave": true,
	                            "columns": [
	                                {"data": "actionss"},
	                                {"data": "created"},
	                                {"data": "marketplace"},
	                                {"data": "ordernumber"},
                                        {"data": "description"},
	                                {"data": "contact"},
	                                {"data": "city"},
	                                {"data": "country"},
	                                {"data": "status"},
	                                {"data": "price"},
	                                {"data": "tracking_no"},
	                                {"data": "label"},
	                            ],
                                    
                                    "initComplete": function( settings, json ) {
                                        $('.popovers').popover();
                                    }
	                        }
	                    });
	                }
	                return {
	                    //main function to initiate the module
	                    init: function () {
	                        handleDataTable();
	                    }
	                };
	            }();
                    
                    $('#btn_fetchOrder').click(function(){                        
                    var marketPlace_id = $('#marketPlace_id_top').val();
                   
                    $.ajax({
                        method: "POST",
                        url : "FetchAmazonOrders.php", // need to get this name dynamically 
                        data : {marketPlace_id : marketPlace_id }
                    }).done(function (data){
                       
                       var rs = JSON.parse(data);
                       if(rs.STATUS === "SUCCESS")
                       {
                           //alert("All orders has been fetched");
                           $("#res_message div.alert").addClass('alert-success');
                           $("#res_message div.alert").html(rs.MESSAGE);
                           $("#res_message").show();
                           grid.getDataTable().ajax.reload();
                       }
                       else if(rs.STATUS === "ERROR")
                       {
                            $("#res_message div.alert").addClass('alert-danger');
                            $("#res_message div.alert").html(rs.MESSAGE);
                            $("#res_message").show();
                            grid.getDataTable().ajax.reload();
                       }
                    });
              });
            
            $(document).on('click', '#btn_GenerateLabel', function () {

                var marketPlaceOrderId       = $('#marketplaceorderid').val();
                var marketPlaceOrderDetailId = $('#marketplaceorderdetailid').val();
                //alert(marketPlaceOrderDetailId); return;
                var weight                   = $('#weight').val();
                var numberPieces             = $('#number_pieces').val();
                var service                  = $('#service').val();
                
                $.ajax({
                method: "POST",
                url: "amazon_list.php",
                data: {func: "generateLabel", service:service, 
                                              numberPieces:numberPieces,
                                              weight: weight,
                                              marketPlaceOrderId: marketPlaceOrderId, 
                                              marketPlaceOrderDetailId: marketPlaceOrderDetailId }
                }).done(function (data) {
                    var t = JSON.parse(data);
                    
                    if(t.STATUS == "SUCCESS")
                    {
                        $('#btn_GenerateLabel').hide();
                        $('#btn_ViewLabel').show();
                        $('#btn_DispatchLabel').show();
                        $('#labelLink').val(t.LABEL);    
                        grid.getDataTable().ajax.reload();
                    }
                    else if(t.STATUS == "ERROR")
                    {
                        $("#res_message_popup div.alert").addClass('alert-danger');
                        $("#res_message_popup div.alert").html(t.MESSAGE);
                        $("#res_message_popup").show();
                    }
                });
                });
            
            $('#btn_DispatchLabel').click(function()
            {
                var hawb = $('#OrderDetail').html();
                var marketPlaceId =  $('#marketplaceorderid').val();
                
                $.ajax({
                   method: "POST",
                   url: "fullfilment.php", // your php file name                   
                   data: {hawb: hawb, marketPlaceId :marketPlaceId },
                   }).done(function (data){
                       
                       var rs = JSON.parse(data);
                       if(rs.STATUS === "SUCCESS")
                       {
                           $("#res_message_popup div.alert").addClass('alert-success');
                           $("#res_message_popup div.alert").html(rs.MESSAGE);
                           $("#res_message_popup").show();
                           grid.getDataTable().ajax.reload();
                           
                          // alert("Order has been Dispatched");
                          // grid.getDataTable().ajax.reload();
                       }
                       else if(rs.STATUS === "ERROR")
                       {
                            $("#res_message div.alert").addClass('alert-danger');
                            $("#res_message div.alert").html(rs.MESSAGE);
                            $("#res_message").show();
                            grid.getDataTable().ajax.reload();
                       }
                    });               
            });
            
            function bulk_selected_action()
            {   
                var bulkAction = $('#bulk_action option:selected').val();
                conIdArr = [];
                $.each($('input[name="ordersArray[]"]:checkbox:checked'), function() {
                    conIdArr.push($(this).val());
                });
                
                if (typeof conIdArr !== 'undefined' && conIdArr.length > 0) 
                {
                    if(bulkAction == "bulklabel")
                    {
                        $.ajax({
                        method: "POST",
                        url: "amazon_list.php",
                        data: {func: "bulkLabelVerification", conIdArr: conIdArr},                    
                        }).done(function (data){
                       rs = JSON.parse(data);
                       if(rs.STATUS == "SUCCESS")
                       {
                           $('#bulk-orderDetail-popup').modal('show');
                           $('#bulkLabelLink').hide();                          
                       }

                       if(rs.STATUS == "ERROR")
                       {
                           swal(rs.MESSAGE);
                       }
                    });
                    }  
                    else if(bulkAction == "bulkdispatch")
                    {
                        if(conIdArr.length > 15)
                        {
                           swal("Please select maximum 15 shipments to dispatch");
                        }
                        else
                        {
                            alert(orderIdArr);
                            $.ajax({
                                    method: "POST",
                                    url: "fullfilment.php", // your php file name                   
                                    data: {conIdArr: conIdArr, marketPlaceId :marketPlaceId },
                            }).done(function (data)
                            {
                            var rs = JSON.parse(data);
                            if(rs.STATUS === "SUCCESS")
                            {
                                $("#res_message_popup div.alert").addClass('alert-success');
                                $("#res_message_popup div.alert").html(rs.MESSAGE);
                                $("#res_message_popup").show();
                                grid.getDataTable().ajax.reload();                           
                            }
                            else if(rs.STATUS === "ERROR")
                            {
                                $("#res_message div.alert").addClass('alert-danger');
                                $("#res_message div.alert").html(rs.MESSAGE);
                                $("#res_message").show();
                                grid.getDataTable().ajax.reload();
                            }                    
                            });
                        }
                    }
                } 
                else 
                {
                    swal("Sorry!", "Please check the checkbox for action", "error");
                }
            }
            
            $(document).on('click', '#btn_GenerateBulkLabel', function () 
            {
                var bulk_weight =  $('#bulk_weight').val();
                var bulk_number_pieces =  $('#bulk_number_pieces').val();
                var bulk_service =  $('#bulk_service').val();
                          
                orderIdArr = [];
                $.each($('input[name="ordersArray[]"]:checkbox:checked'), function() {
                    orderIdArr.push($(this).val());
                });
                
                $.ajax({
                method: "POST",
                url: "amazon_list.php",
                data:   {func: "generateBulkLabel", bulk_service:bulk_service, 
                                                  bulk_number_pieces:bulk_number_pieces,
                                                  bulk_weight: bulk_weight,
                                                  orderIdArr :orderIdArr
                                              }
                }).done(function (data) 
                {
                    var t = JSON.parse(data);
                    $.each(t,function(index, item)
                    {
                     //  alert(t[index].LABEL); 
                      // alert(t[index].MESSAGE);
                      // alert(t[index].STATUS);
                     //  

                    if(t[index].STATUS == "SUCCESS")
                    {
                        $('#btn_bulkLabelLink').show();
                        //var LabelLink = $('#bulkLabel').val();
                        //$('#labelLink').val(t[index].LABEL);    
                        $('#bulkLabelLink').val(t[index].MERGEDLINK);    
                        grid.getDataTable().ajax.reload();
                    }
                    else if(t[index].STATUS == "ERROR")
                    {
                        $("#res_message_popup div.alert").addClass('alert-danger');
                        $("#res_message_popup div.alert").html(t[index].MESSAGE);
                        $("#res_message_popup").show();
                    }
                    });
                });
            });
          </script>
            <?php
	    }
	    /*     * *
	     * Content View
	     */
	    protected function renderBody() {
	        ?>
	        <?php
	        // transfer form variables into local values (form variables come from parent)
	        foreach ($this->form_vars as $key => $val) {
	            $$key = $val;
	        }
                if (errorList::getItem()->getErrorCount() > 0) {
	            ?>
            	            <div class="alert alert-info"><?php errorList::getItem()->render(); ?></div>
            
	            <?php
	        }
	        ?>
                    
	        <div class="main_formpage">        
	            <div class="portlet light">
	                <div class="portlet-title">
	                    <div class="caption"><i class="icon-list"></i>
	                            Orders List
	                    </div>
                            
                            <div class="actions">
                                <?php 
                                echo Ddl::generateMarketPlaceDDLWithImage('marketPlace_id_top', '', 'id', ' class="bs-select input-sm form-control form-filter pull-right" data-live-search="true"  data-show-subtext="true"');
                                ?>
                                <button id= "btn_fetchOrder" class="btn btn-sm btn-default pull-right" type="button">
                                        <i class="fa fa-check"></i>Fetch Order</button>
                            </div>
	                </div>
	                 <div class="portlet-body"> 
                            <div class="table-actions-wrapper pull-right">
                            <span> </span>
                            <select class="table-group-action-input form-control input-inline input-small input-sm" id="bulk_action" name="bulk_action" style="width:150px !important;">
                                <option value="">Select Action</option>
                               <!-- <option value="bulklabel">Bulk Label</option>
                                <option value="bulkdispatch">Bulk Dispatch</option> -->
                                <option value="export">Export</option>
                                <option value="delete">Delete</option>                                
                            </select>
                            <button class="btn btn-sm btn-default table-group-action-submit" type="button" onclick="bulk_selected_action()">
                                <i class="fa fa-check"></i> Submit</button>
                        </div>
                            <div id ="res_message" class="row display-none">
                                <div class="col-md-12">
                                <div class="alert alert-success"></div>
                                </div>
                            </div>
                            
	                    <div class="table-container">
	                        
	                        <table class="table table-striped table-bordered table-hover" id="manage-data-table">
	                            <thead>
	                                <tr role="row" class="heading">                                   
                                            
	                                    <th width="4%">
                                            <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                                <input type='checkbox' name='checkall' onclick='checkedAll(delete55);' class="group-checkable"/>
                                                <span></span>
                                            </label>
                                            </th>
                                            <th>Created</th>
	                                    <th>Market Place</th>
	                                    <th>Order Number</th>
                                            <th>Description</th>
	                                    <th>Contact</th>
	                                    <th>City</th>
	                                    <th>Country</th>
	                                    <th>Status</th>
	                                    <th>Price</th>
	                                    <th>Tracking No</th>
	                                    <th>Label</th>
	                                </tr>
	                                <tr role="row" class="filter">
	                                   <td width = "2%">
	                                        <div class="margin-bottom-5">
	                                            <button class="btn-xs filter-submit margin-bottom blue btn btn-default mt-ladda-btn ladda-button btn-outline"><i class="fa fa-search"></i></button>
	                                            <!-- <button class="btn-xs red filter-cancel btn mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i></button> -->
	                                        </div>
	                                    </td>
	                                    <td>
	                                        <div class="input-group date date-picker margin-bottom-5" data-date-format="yyyy-mm-dd">
	                                            <input type="text" class="form-control form-filter input-sm" readonly name="search_Date_from" placeholder="From" data-date-format="yyyy-mm-dd">
	                                            <span class="input-group-btn">
	                                                <button class="btn btn-sm" type="button"><i class="fa fa-calendar"></i></button>
	                                            </span>
	                                        </div>
	                                        <div class="input-group date date-picker" data-date-format="yyyy-mm-dd">
	                                            <input type="text" class="form-control form-filter input-sm" readonly name="search_Date_to" placeholder="To" data-date-format="yyyy-mm-dd">
	                                            <span class="input-group-btn">
	                                                <button class="btn btn-sm" type="button"><i class="fa fa-calendar"></i></button>
	                                            </span>
	                                        </div>
	                                    </td>
	                                    <td class="user_acccount_correct_button">
	                                        <?php 
	                                        echo Ddl::generateMarketPlaceDDLWithImage('search_MarketPlace', '', 'id', ' class="bs-select input-sm form-control form-filter" data-live-search="true"  data-show-subtext="true"');                                                
	                                        ?> 
	                                   </td>
	                                    <td width = "12%">
	                                        <input type="text" class="form-control form-filter input-sm" name="search_HAWB">
	                                    </td>
                                            <td width = "12%">
	                                        <input type="text" class="form-control form-filter input-sm" name="search_Description">
	                                    </td>
	                                    <td>
	                                        <input type="text" class="form-control form-filter input-sm" name="search_Name">
	                                    </td>
	                                    <td> 
	                                        <input type="text" class="form-control form-filter input-sm" name="search_City">
	                                    </td>
	                                    <td class="user_acccount_correct_button">
	                                    <?php echo Ddl::generateCountryDDL('search_Country', '', 'id',' class="form-filter bs-select form-control input-sm" data-live-search="true"'); ?>
	                                    </td>
	                                    <td>
	                                        <?php
	                                        $this->form_vars["status_list"] = ''; // this line clears the current selected status
	                                        echo Ddl::generateArrayDDL('search_Status', $this->amazon_status_array, $this->form_vars["status_list"], "Select Status", 'class="form-control form-filter input-sm select2 searchbox"', "", 'search_Status', Translation::GetCaption("PLEASE_SELECT_STATUS"));
	                                        ?>             
	                                    </td>
	                                    <td>
	                                        <input type="text" class="form-control form-filter input-sm" name="search_Pricing">
	                                    </td>                            
	                                    <td>
	                                        <input type="text" class="form-control form-filter input-sm" name="search_Tracking">
	                                    </td>                                    
	                                    <td>
	                                    </td>                                                                      
	                                </tr>
	                            </thead>
	                            <tbody>
	                            </tbody>
	                        </table>
	                    </div>
	                    <input type="hidden" name="id" id="id" value="<?php //echo @$id; ?>" />
	                </div>
	            </div>
	        </div>
	       
                
                <div class="modal fade" tabindex="-1" role="dialog" id="show-orderDetail-popup" >
	            <div class="modal-dialog modal-lg">
	                <div class="modal-content">
	                    <div class="modal-header">
                                <div class="row">
                                    <div class="col-md-6">                                        
                                        <h4 class="modal-title text-primary"><strong>Order Number: <span id="OrderDetail"></span> </strong> </h4>                                
                                    </div>
                                    <div class="col-md-6">
                                         <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <div id="flag_marketplace" align="right"></div>
                                       
                                    </div>
                                    
                                </div>
	                        
	                    </div>
	                    <div class="modal-body">
	                        <div class="row">
	                            <div class="col-md-12" id="errorMessage">
	                            </div>
	                            <div class="modal-body" id="order-content-display">                               
	                            </div>
	                        </div>                            
	                    </div>
                            <div class="modal-footer">  
                                <input id="btn_ViewLabel" type="button"  class="btn btn-primary" value="View Label"/>                                
                                <input id="btn_DispatchLabel" type="button"  class="btn btn-primary" value="Dispatch Label"/>
                                <input id="btn_GenerateLabel" type="button"  class="btn btn-primary" value="Generate Label"/>
                            </div>
	                </div>
	                    <!-- /.modal-content --> 
	             </div>
	                <!-- /.modal-dialog --> 
	            </div>
                    
                    
                <div class="modal fade" tabindex="-1" role="dialog" id="bulk-orderDetail-popup" >
	            <div class="modal-dialog modal-lg" role="document">
	                <div class="modal-content">	                    
                            <div class="modal-header"></div>
	                    <div class="modal-body">
                                <div id ="res_message_popup" class="row display-none">
                                <div class="col-md-12">
                                <div class="alert alert-success"></div>
                                </div>
                                </div>
	                        <div class="row">
                                    <div class="col-md-4">                                        
                                    <strong>Select a Service:</strong>
                                    <?php
                                    $userAccountId = $this->user->getUserAccountId();
                                    $fromCountry = $this->user->getCountryId();
                                    $serviceFilter = new ServiceFilter();
                                    $servicesData = $serviceFilter->getUserAccountServices($userAccountId, $fromCountry, $this->user->getCountryId(), 'D');
                                    
                                    echo '<select name="bulk_service" id="bulk_service" class="bs-select form-control" data-live-search="true" data-show-subtext="true" data-toggle="tooltip" title="" data-original-title="Service" data-container="body" placeholder="Service">'; 
                                    echo '<option value="">Please Select</option>';
                                   
                                    $selectedStr = "";
                                    if (count($servicesData) > 0) 
                                    {
                                        foreach ($servicesData as $userServiceData) 
                                        {
                                            $selectedStr = ($userServiceData->getId() == $selected ? ' selected="selected"' : '');
                                            echo '<option ' . $selectedStr .  '" value="' . $userServiceData->getId() . '">' . $userServiceData->getName() . '</option>';
                                        }
                                    }
                                    ?>
                                    </select>                                          
                                               </div>
                                               <div class="col-md-4">
                                               <strong>Weight:</strong>
                                               <input type="text" class="form-control form-filter input-sm" name="bulk_weight" id= "bulk_weight">
                                               </div>
                                               <div class="col-md-4">
                                               <strong>Number of Pieces:</strong>
                                               <input type="text" class="form-control form-filter input-sm" name="bulk_number_pieces" id= "bulk_number_pieces">
                                               </div></div>
	                    
	                        <div class="modal-footer">  
                                <input id="btn_GenerateBulkLabel" type="button"  class="btn btn-primary" value="Generate Labels"/>
                                <input id="btn_bulkLabelLink" type="button"  class="btn btn-primary" value="View Labels"/>  
                                <input type="hidden" name="bulkLabelLink" id="bulkLabelLink">
	                        </div>
	                    </div>
	                    <!-- /.modal-content --> 
	                </div>
	                <!-- /.modal-dialog --> 
                    </div>
                    </div>
	        <?php
	    }
	    /**
	     * Override to show the menu
	     *
	     */
	    public function renderMenu() {
	        $menu = new Adminmenu(Adminmenu::CUSTOMERS);
	        $menu->render();
	    }    
	}
	/* ------------------------------------------------------------------------------ */
	// create and render page
	$page = new Page(CONFIG_TEMPLATE_ADMIN);
	$page->show();