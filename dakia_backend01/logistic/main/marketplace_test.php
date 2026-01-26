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
    'licenceplatefilter.class',
    'tracking.class',
    'customizedservicesroutingfilter.class',
    'customizedservicesrouting.class',
    'trackingdata.class',
    'api2cart.class'
]);

include_classes([
    'tcpdf',
        ], '3rdparty/tcpdf');

include_classes([
    'pdfmerger'], 'labels');

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

    /*     * *
     * Controller logic
     */

    protected function init() {
        $this->user = SessionManager::getUser();
        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            'marketplace.php' => 'Marketplace'
        );

        $this->marketPlaceOrderFilter = new MarketPlaceOrderFilter();
        // common initialisation for ths page
        $this->setTitle("Orders List");
        if (isset($_GET['action']) && $_GET['action'] == "amazon_ajax") {
			$dataTableColumnName = "";
			$orderFalse = "";
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
            if ($dataTableColumnName != '') {
                $this->marketPlaceOrderFilter->AddOrderBy($dataTableColumnName, $orderFalse);
            } else {
                $this->marketPlaceOrderFilter->AddOrderBy('ord.id', false);
            }

            $orders_list = $this->marketPlaceOrderFilter->getPagingList();
            $amazonDataArr = array();
            $rangeDataArr = array();


            foreach ($orders_list as $orders) {
                $marketPlaceId = $orders->getMarketPlaceId();
                $marketPlace = new MarketPlaces($marketPlaceId);
                $marketPlaceName = $marketPlace->getTitle() . '<br />' . $orders->getMarketPlaceOrderNumber();
                $amazonDataArr['actionss'] = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline"><input type=checkbox id= "dispatchIds" name="dispatchArray[]"  value="' . $orders->getMarketPlaceOrderNumber() . '"  class="group-checkable" /><span></span></label>';
                $amazonDataArr['created'] = $orders->getCreateTime();
                $amazonDataArr['description'] = '<a class="popovers" data-trigger="hover" data-content="' . $orders->getTitle() . '" >' . substr($orders->getTitle(), 0, 20) . '</a>';
                $marketPlaces = new MarketPlaces($marketPlaceId);

                if (!empty($marketPlace->getIntegrationLogo())) {
					$marketPlaceLogo = 'thirdparty/' . $marketPlace->getIntegrationLogo();
                } else {
                    $marketPlaceLogo = 'No-image-found.jpg';
                }
                $amazonDataArr['marketplace'] = '<img src=\'../images/' . $marketPlaceLogo . '\' / width=16>&nbsp;&nbsp;&nbsp;' . $marketPlaceName;
                
                if ($orders->getSku() != '') {
                    if($orders->getSendWms() == '1') 
                    {
                        $amazonDataArr['sku'] = '<i class="fa fa-building-o color-view bg-blue-steel bg-font-blue-steel bold uppercase" aria-hidden="true"></i>';
                        $amazonDataArr['sku'] .= '   '.$orders->getSku();                    
                    }
                    else
                    {
                        $amazonDataArr['sku'] = $orders->getSku();     
                    }
                }
                else
                {
                    $amazonDataArr['sku'] = '';
                }
                
                if ($orders->getReceiverName() != '') {
                    $amazonDataArr['contact'] = $orders->getReceiverName() . '<br />' . '<a class="popovers" data-trigger="hover" data-content="' . $orders->getTitle() . '" >' . substr($orders->getTitle(), 0, 20) . '</a>';
                    ;
                } else {
                    $amazonDataArr['contact'] = '<a class="popovers" data-trigger="hover" data-content="' . $orders->getTitle() . '" >' . substr($orders->getTitle(), 0, 20) . '</a>';
                }

                if ($orders->getReceiverCountryId() > 0) {
                    $orderReceiverCity = '';
                    if ($orders->getReceiverCity() != '') {
                        $orderReceiverCity = $orders->getReceiverCity() . "<br />";
                    }
                    $countryId = $orders->getReceiverCountryId();
                    $countryObj = new Country($countryId);
                    $amazonDataArr['country'] = $orderReceiverCity . '<img src=../assets/global/img/flags/' . strtolower($countryObj->getIso()) . '.png /> &nbsp;' . $countryObj->getIso();
                    if ($orders->getReceiverCountryId() != '') {
                        $amazonDataArr['country'] = $orderReceiverCity . '<img src=../assets/global/img/flags/' . strtolower($countryObj->getIso()) . '.png /> &nbsp;' . $countryObj->getIso();
                    }
                } else {
                    $amazonDataArr['country'] = '';
                    if ($orders->getReceiverCountryId() != '') {
                        $amazonDataArr['country'] = $orders->getReceiverCity();
                    }
                }
                $amazonDataArr['status'] = '<div class="margin-bottom-5">' . $this->getStatus($orders->getOrderStatus()) . '</div>';
                if($orders->getOrderStatus() == 'Shipped') {
                    $amazonDataArr['status'] .= '<i>Shipped on: ' . $orders->getShippedDate() . '</i>';
                }
                
                $amazonDataArr['price'] = $orders->getItemPrice() . ' ' . $orders->getCurrency();
                $consignmentId = $orders->getConsignmentId();

                if ($consignmentId > 0) {
                    $consignment = new Consignment($consignmentId);
                    $amazonDataArr['tracking_no'] = $consignment->getAwb();
                } else {
                    $amazonDataArr['tracking_no'] = '';
                }

                //$amazonDataArr['reason'] = '';
                //$marketplaces = new MarketPlaces($orders->getMarketPlaceOrderId());
                //$logo = $marketplaces->getIntegrationLogo();
                
                $amazonDataArr['label'] = '<a class="showOrderDetail tooltipbutton btn-xs blue btn mt-ladda-btn ladda-button btn-outline" rel="tooltip" 
                                                data-toggle="modal" data-target="#show-orderDetail-popup" role="dialog" tabindex="-1" 
                                                data-orderid                ="' . $orders->getId() . '"
                                                data-trackingnumber         ="' . $amazonDataArr['tracking_no'] . '"
                                                data-marketplaceorderid     ="' . $orders->getMarketPlaceOrderId() . '"
                                                data-marketplaceordernumber = "' . $orders->getMarketPlaceOrderNumber() . '"
                                                data-consignmentid = "' . $consignmentId . '"
                                                data-marketplacelogo =  "' . $marketPlaceLogo . '"
                                                data-orderstatus =  "' . $orders->getOrderStatus() . '"
                                                data-action = "ITEMORDERDETAIL"
                                                data-load = "marketplace.php"
                                                data-placement="top"
                                                data-title="View Details"
                                                href="javascript:;" data-original-title="View Details">
                                                <span class=" fa fa-eye"></span>
                                                </a>';
                $rangeDataArr[] = $amazonDataArr;
            }
            $rangeDataArr['data'] = $rangeDataArr;
            $rangeDataArr['draw'] = $sEcho;
            $rangeDataArr['recordsTotal'] = $iTotalRecords;
            $rangeDataArr['recordsFiltered'] = $iTotalRecords;
            echo json_encode($rangeDataArr, JSON_PARTIAL_OUTPUT_ON_ERROR);
            die;        
            
        } 
        
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'download_csv_frm') 
        {
            $orderNumber = $data[0];
                                $title = $data[1];
                                $sku = $data[2];
                                $quantity = $data[3];
                                $price = $data[4];
                                $currency = $data[5];
                                $receiverName = $data[6];
                                $receiverPhone = $data[7];
                                $receiverState = $data[8];
                                $receiverCity = $data[9];
                                $receiverCountry = $data[10];
                                $addressLine1 = $data[11]; 
                                $addressLine2 = $data[12];
                                $postCode = $data[13];
                                $email = $data[14];
            $returnString = "Order Number,Title,Sku,Quantity,Price,Currency,Receiver Name,Receiver Phone,Receiver State,Receiver City,Receiver Country,Address Line 1, Address Line 2, Post Code, Email";
            $fileName = "manual_order_" . time();
            $returnString .= "\r\n";
            $returnString .=  ",";
            $returnString .=   ",";
            $returnString .=  ",";
            $returnString .=  ",";
            $returnString .=   ",";
            $returnString .=  ",";
            $returnString .=  ",";
            $returnString .=  ",";
            $returnString .=   ",";
            $returnString .=  ",";
            $returnString .= "";
        
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=" . $fileName . ".csv");
            header("Pragma: no-cache");
            header("Expires: 0");
            echo $returnString;
            die;
        }
        
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'upload_csv_file') {
            
            $output = array();
            $output['status'] = 'success';
            $output['message'] = formatMessages(SUCCESS_UPLOADED);
            @$csv_file = $_FILES['csv_file'];
            if (!empty($csv_file['name'])) {
                $file_name = $csv_file['name'];
                $path_parts = pathinfo($file_name);
                $ext = strtolower($path_parts['extension']);
                $basename = $path_parts['basename'];
                if ($ext == 'csv') {
                    //$user = SessionManager::getUser();
                    $userId = $this->user->getUserAccountId();
                    
                    $new_file_name = $this->user->getId() . "_" . time() . "_" . $basename;
                    $relPath = '../_assets/manual_order_csv/' . $new_file_name;
                    if (!file_exists("../_assets/manual_order_csv/"))
                        @mkdir("../_assets/manual_order_csv/", 0775);
                    if (move_uploaded_file($csv_file['tmp_name'], $relPath)) {

                        $row = 0;

                        if (($handle = fopen($relPath, "r")) !== FALSE) {
                            $csvContent = '';
                            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                                if ($row < 1) {
                                    $row++;
                                    continue;
                                }
                                $orderNumber = $data[0];
                                $title = $data[1];
                                $sku = $data[2];
                                $quantity = $data[3];
                                $price = $data[4];
                                $currency = $data[5];
                                $receiverName = $data[6];
                                $receiverPhone = $data[7];
                                $receiverState = $data[8];
                                $receiverCity = $data[9];
                                $receiverCountry = $data[10];
                                $addressLine1 = $data[11]; 
                                $addressLine2 = $data[12];
                                $postCode = $data[13];
                                $email = $data[14];
                                
                                $marketPlaceOrderFilter = new MarketPlaceOrderFilter();
                                $marketPlaceOrderFilter->addFilter("     marketplace_order_number = '" . $orderNumber . "'");
                                $marketPlaceOrderFilterResult = $marketPlaceOrderFilter->getList();

                                if (count($marketPlaceOrderFilterResult) > 0) {
                                    $marketplaceOrderDbId = $marketPlaceOrderFilterResult[0]->getId();
                                    $marketPlaceOrderObj = new MarketPlaceOrder($marketplaceOrderDbId);
                                }
                                else
                                {
                                    $marketPlaceOrderObj = new MarketPlaceOrder();
                                }

                                ////// Set values in marketplace order and detail table
                                
                                if($userId != '')
                                {
                                    $marketPlaceOrderObj->setUserId($userId);
                                }
                                
                                if($orderNumber != '')
                                {
                                    $marketPlaceOrderObj->setMarketPlaceOrderNumber($orderNumber);
                                    $marketPlaceOrderObj->setCreateTime(date('Y-m-d H:i:s'));
                                    $marketPlaceOrderObj->setOrderStatus('Unshipped');
                                }
                                else
                                {
                                    $errorMsg[] = $row. ' row rejected. Please enter Order Number for this record';
                                    continue;
                                }
                                
                                if($receiverName != '')
                                {
                                    $marketPlaceOrderObj->setReceiverName($receiverName);                                    
                                }
                                else
                                {
                                    $errorMsg[] = $row. ' row rejected. Please enter Receiver Name for this record';
                                    continue;
                                }

                                if ($receiverPhone  != '') 
                                {
                                    $marketPlaceOrderObj->setReceiverPhone($receiverPhone);                                    
                                }
                                
                                if ($receiverState  != '') 
                                {
                                    $marketPlaceOrderObj->setReceiverState($receiverState);                                    
                                }
                                                                
                                if ($receiverCity  != '') 
                                {
                                    $marketPlaceOrderObj->setReceiverCity($receiverCity);                                    
                                }
                                else
                                {
                                    $errorMsg[] = $row. ' row rejected. Please enter Receiver City for this record';
                                    continue;
                                }
                                
                                if ($receiverCountry  != '') 
                                {
                                    $countryFilter = New CountryFilter();
                                    $countryFilter->addFilter("name = '". ucfirst(strtolower($receiverCountry))."'");
                                    $countryResult = $countryFilter->getList();
                                    $marketPlaceOrderObj->setReceiverCountryId($countryResult[0]->getId());                                    
                                }
                                
                                else
                                {
                                    $errorMsg[] = $row. ' row rejected. Please enter Country for this record';
                                    continue;
                                }
                                
                                
                                if ($addressLine1  != '') 
                                {
                                    $marketPlaceOrderObj->setReceiverAddressLine1($addressLine1);                                    
                                }
                                else
                                {
                                    $errorMsg[] = $row.' row rejected. Please enter Address Line 1';
                                    $continue;
                                }
                                
                                if ($addressLine2  != '') 
                                {
                                    $marketPlaceOrderObj->setReceiverAddressLine2($addressLine2);                                    
                                }
                                
                                if ($postCode  != '') 
                                {
                                    $marketPlaceOrderObj->setReceiverPostCode($postCode);                                    
                                }
                                else
                                {
                                    $errorMsg[] = $row.' row rejected. Please enter Address Line 1';
                                    $continue;
                                }
                                
                                if ($email  != '') 
                                {
                                    $marketPlaceOrderObj->setReceiverEmail($email);                                    
                                }
                                $marketPlaceOrderObj->setMarketPlaceId('93');
                                $marketPlaceOrderObj->save();
                                $marketPlaceOrderId = $marketPlaceOrderObj->getId();
                                
                                //echo $marketPlaceOrderId; die;
                                $marketPlaceOrderDetailsFilter = new MarketPlaceOrderDetailsFilter();
                                $marketPlaceOrderDetailsFilter->addFilter("     marketplace_order_id = '" . $marketPlaceOrderId . "'");
                                $marketPlaceOrderDetailsFilterResult = $marketPlaceOrderDetailsFilter->getList();

                                if (count($marketPlaceOrderDetailsFilterResult) > 0) {
                                    $marketplaceOrderDetailsDbId = $marketPlaceOrderDetailsFilterResult[0]->getId();
                                    $marketPlaceOrderDetail = new MarketPlaceOrderDetails($marketplaceOrderDetailsDbId);
                                }
                                else
                                {
                                    $marketPlaceOrderDetail = new MarketPlaceOrderDetails();
                                }
                                
                                
                                if($marketPlaceOrderId != '')
                                {
                                    $marketPlaceOrderDetail->setMarketPlaceOrderId($marketPlaceOrderId);
                                }
                                
                                if ($sku  != '') 
                                {
                                    $marketPlaceOrderDetail->setSku($sku);                                    
                                }
                                else
                                {
                                    $errorMsg[] = $row.' row rejected. Please enter Sku';
                                    $continue;
                                }
                                
                                if ($title  != '') 
                                {
                                    $marketPlaceOrderDetail->setTitle($title);                                    
                                }
                                else
                                {
                                    $errorMsg[] = $row.' row rejected. Please enter Product Title';
                                    $continue;
                                }
                                
                                if ($quantity  != '') 
                                {
                                    $marketPlaceOrderDetail->setQuantityPurchased($quantity);                                    
                                }
                                else
                                {
                                    $errorMsg[] = $row.' row rejected. Please enter Quantity';
                                    $continue;
                                }
                                
                                if ($price  != '') 
                                {
                                    $marketPlaceOrderDetail->setItemPrice($price);                                    
                                }
                                else
                                {
                                    $errorMsg[] = $row.' row rejected. Please enter Item Price';
                                    $continue;
                                }
                                
                                if ($currency  != '') 
                                {
                                    $marketPlaceOrderDetail->setCurrency($currency);                                    
                                }
                                else
                                {
                                    $errorMsg[] = $row.' row rejected. Please enter Currency';
                                    $continue;
                                }
                                
                               // $marketPlaceOrderObj->setDateCreated(strtotime(date('Y-m-d H:i:s')));
                                $marketPlaceOrderDetail->save();
                 
                                $marketplacedbIds[] = $marketPlaceOrderObj->getId();                  
                                $row++;
                            }
                        }
                        fclose($handle);
                        
                        
                    } else {
                        $output['message'] = formatMessages(ERROR_FILE_UPLOADED); //'File upload fail.';
                        $output['status'] = 'fail';
                    }
                } else {
                    $output['message'] = formatMessages(ERROR_INVALID_FILE);
                    $output['status'] = 'fail';
                }
            } else {
                $output['message'] = formatMessages(ERROR_FILE_EMPTY);
                $output['status'] = 'fail';
            }
            if($errorMsg != '')
            {
                $output['status'] = 'Fail';
                $output['message'] = $errorMsg;
            }
            echo json_encode($output);
            exit;
        }
        
        else if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "saveManualRecord") 
        {
            echo 'kjhkj'; die;
            echo '<pre>';
            print_r($this->form_vars);
            echo '</pre>';
            die;
            //serialize form here and do verifications and save data against manual marketplace
            if($this->form_vars["manual_order_number"] == '')
            {
                $output['MESSAGE'][] = "Please enter order number.";
            }
            else
            {
                $manualOrderNumber = $this->form_vars["manual_order_number"];
            }
            
            if($this->form_vars["receiver_name"] == '')
            {
                $output['MESSAGE'][] = "Please enter Name.";
            }
            else
            {
                $receiverName = $this->form_vars["receiver_name"];
            }
            
            if($this->form_vars["receiver_phone"] != '')
            {             
                $receiverPhone = $this->form_vars["receiver_phone"];
            }   
            
            if($this->form_vars["receiver_city"] == '')
            {
                $output['MESSAGE'][] = "Please enter City Name.";
            }
            else
            {
                $receiverCity = $this->form_vars["receiver_city"];
            }
            
            if($this->form_vars["receiver_state"] != '')
            {             
                $receiverState = $this->form_vars["receiver_state"];
            } 
            
            if($this->form_vars["receiver_country"] == '')
            {
                $output['MESSAGE'][] = "Please enter Country Name.";
            }
            else
            {
                $receiverCountry = $this->form_vars["receiver_country"];
            }
            
            if($this->form_vars["address_line_1"] == '')
            {
                $output['MESSAGE'][] = "Please enter Address Line 1";
            }
            else
            {
                $addressLine1 = $this->form_vars["address_line_1"];
            }
            
            if($this->form_vars["address_line_2"] != '')
            {
                $addressLine2 = $this->form_vars["address_line_2"];
            }
            
            if($this->form_vars["post_code"] == '')
            {
                $output['MESSAGE'][] = "Please enter Post Code";
            }
            else
            {
                $postCode = $this->form_vars["post_code"];
            }
            
            if($this->form_vars["email"] != '')
            {
                $email = $this->form_vars["email"];
            }
           
            echo count($this->form_vars["sku"]);
            die;
            if($this->form_vars["sku"] == '')
            {
                $output['MESSAGE'][] = "Please enter SKU";
            }
            else
            {
                $sku = $this->form_vars["sku"];
            }
            
            if($this->form_vars["quantity"] == '')
            {
                $output['MESSAGE'][] = "Please enter Quantity";
            }
            else
            {
                $quantity = $this->form_vars["quantity"];
            }
            
            if($this->form_vars["price"] == '')
            {
                $output['MESSAGE'][] = "Please enter Price.";
            }
            else
            {
                $price = $this->form_vars["price"];
            }
            
            
            
            if($this->form_vars["title"] == '')
            {
                $output['MESSAGE'][] = "Please enter Title.";
            }
            else
            {
                $title = $this->form_vars["title"];
            }
            
            if($output != '')
            {
                $output['STATUS'] = "ERROR";
                echo json_encode($output);
                die;
            }
            
            $marketPlaceOrder = new MarketPlaceOrder();
            $marketPlaceOrder->setMarketPlaceOrderNumber($manualOrderNumber);
            $marketPlaceOrder->setMarketPlaceId('93');
            $marketPlaceOrder->setCreateTime(date('Y-m-d H:i:s'));
            $marketPlaceOrder->setOrderStatus('UnShipped');
            $marketPlaceOrder->setReceiverName($receiverName);
            $marketPlaceOrder->setReceiverPhone($receiverPhone);
            $marketPlaceOrder->setReceiverState($receiverState);
            $marketPlaceOrder->setReceiverCity($receiverCity);
            
            $marketPlaceOrder->setReceiverCountryId($receiverCountry);
            $marketPlaceOrder->setReceiverAddressLine1($addressLine1);
            $marketPlaceOrder->setReceiverAddressLine2($addressLine2);
            $marketPlaceOrder->setReceiverPostCode($postCode);
            $marketPlaceOrder->setOrderTotal($price);
            $marketPlaceOrder->setReceiverEmail($email);
            $marketPlaceOrder->setUserId($this->user->getUserAccountId());
            $marketPlaceOrder->save();
            
            $marketPlaceOrderTableId = $marketPlaceOrder->getId();
            $marketPlaceOrderDetails = new MarketPlaceOrderDetails();
            $marketPlaceOrderDetails->setMarketPlaceOrderId($marketPlaceOrderTableId);
            $marketPlaceOrderDetails->setSku($sku);
            $marketPlaceOrderDetails->setTitle($title);
            $marketPlaceOrderDetails->setQuantityPurchased($quantity);
            $marketPlaceOrderDetails->setItemPrice($price);
            $marketPlaceOrderDetails->setCurrency($currency);
            $marketPlaceOrderDetails->save();
            
            $output['STATUS'] = "SUCCESS";
            $output["MESSAGE"] = "Item Saved successfully.";
            echo json_encode($output);
            die;
        }
        
        else if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "generateLabel") {
            $marketPlaceOrderId = $this->form_vars["marketPlaceOrderId"];
            $marketPlaceOrderDetailId = $this->form_vars["marketPlaceOrderDetailId"];
            $weight = $this->form_vars["weight"];
            $numberOfPieces = $this->form_vars["numberPieces"];
            $serviceId = $this->form_vars["service"];

            $length = 1;
            $width = 1;
            $height = 1;
            $marketPlaceOrder = new MarketPlaceOrder($marketPlaceOrderId);
            $marketOrderDetailsFilter = new MarketPlaceOrderDetailsFilter();
            $marketOrderDetailsFilter->addFieldFilter('    marketplace_order_id', $marketPlaceOrderId);
            $marketPlaceOrderItems = $marketOrderDetailsFilter->getColumnList('*');
            foreach($marketPlaceOrderItems as $addedItems)
            {
                $value += $addedItems->getItemPrice();
                $description .= $addedItems->getTitle()."||";
                $itemId .= $addedItems->getMarketplaceItemId()."||";
                $currency = $addedItems->getCurrency();
            }
            
            $userId = $this->user->getId();
            $userAccountId = $this->user->getUserAccountId();
            $user = new User($userId);
            $userAccount = new CustomerAccount($userAccountId);
            $serflr = new Services($serviceId);
            if (sizeof($serflr) > 0) {
                $service_id = $serflr->getId();
                $consignmentArray['service'] = $service_id;
                if ($serflr->getIsCustomized())
                    $consignmentArray['is_product'] = 1;
                else
                    $consignmentArray['is_product'] = 0;
            }
            $conFilter = new ConsignmentFilter();
            $conFilter->addFieldFilter("    hawb", $marketPlaceOrder->getMarketPlaceOrderNumber());
            $result = $conFilter->getConList("id");

            if (count($result) > 0) {
                $consignmentArray['id'] = $result[0]->getId();
            }

            $consignmentArray['sender_company'] = substr(($userAccount->getCompany()), 0, 100);
            $consignmentArray['sender_contact'] = substr(($userAccount->getFullName()), 0, 100);
            $consignmentArray['sender_email'] = $this->replaceSpecial($userAccount->getEmail());
            $consignmentArray['sender_telephone'] = (substr(str_replace(' ', '', $userAccount->getTelephone()), 0, 20));
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
            if (empty($consignmentArray['receiver_contact']))
                $consignmentArray['receiver_contact'] = 'One World Express';

            $consignmentArray['receiver_email'] = ''; //$this->replaceSpecial($marketPlaceOrder->getReceiverName());
            $consignmentArray['receiver_telephone'] = (substr(str_replace(' ', '', $marketPlaceOrder->getReceiverPhone()), 0, 20));
            $consignmentArray['receiver_address_line_1'] = $this->replaceSpecial(substr(trim($marketPlaceOrder->getReceiverAddressLine1()), 0, 50));
            //echo strlen($marketPlaceOrder->getReceiverAddressLine2()); die;
            $consignmentArray['receiver_address_line_2'] = $this->replaceSpecial(substr(trim($marketPlaceOrder->getReceiverAddressLine2()), 0, 50));
            /// $consignmentArray['receiver_address_line_3'] = $this->replaceSpecial(substr($marketPlaceOrder->getReceiverAddressLine3(), 0, 50));
            $consignmentArray['receiver_city'] = $this->replaceSpecial(substr((strtoupper($marketPlaceOrder->getReceiverCity())), 0, 50));
            $consignmentArray['receiver_state'] = $this->replaceSpecial(substr((strtoupper($marketPlaceOrder->getReceiverState())), 0, 50));
            $consignmentArray['receiver_postcode'] = trim($marketPlaceOrder->getReceiverPostCode());
            $consignmentArray['receiver_country'] = $marketPlaceOrder->getReceiverCountryId();
            // Set remaning fields
            $consignmentArray['reference'] = substr($itemId, 0, 30);
            $consignmentArray['item_value'] = $value;
            $consignmentArray['item_currency'] = $currency;
            $consignmentArray['item_type'] = '';
            $consignmentArray['notes'] = substr($description, 0, 100);
            $consignmentArray['description'] = substr($this->replaceSpecial($description), 0, 70);
            // Calculate per piece weight ------(weight/no of pieces)
            $consignmentArray['parcel'] = [];
            $weight = $weight / $numberOfPieces;  // do weight validation
            $count = 0;
            for ($count = 0; $count < $numberOfPieces; $count++) { //for loop change .. upto number of pieces
                $consignmentArray['parcel'][$count]['weight'] = $weight;
                $consignmentArray['parcel'][$count]['length'] = 1;
                $consignmentArray['parcel'][$count]['height'] = 1;
                $consignmentArray['parcel'][$count]['width'] = 1;
            }
            $output = array();
            $result = Consignment::saveShipment($consignmentArray, $userId);

            if (trim($result['STATUS']) == 'ERROR') {
                if (is_array($result['MESSAGE'])) {
                    $output['MESSAGE'] = implode(",", $result['MESSAGE']);
                } else {
                    $output['MESSAGE'] = $result['MESSAGE'];
                }
                $output['STATUS'] = 'ERROR';
            } else {
                $consignmentId = $result['CONSIGNMENT_ID'];
                if ($consignmentId > 0) {
                    $labelType = (!empty($formPostArray["label_type"]) ? $formPostArray["label_type"] : 'pdf');
                    $labelSize = (!empty($formPostArray["label_size"]) ? $formPostArray["label_size"] : '100x150');
                    $marketPlaceOrder->setConsignmentid($consignmentId);
                    $marketPlaceOrder->save();
                    $consignment = new Consignment($consignmentId);
                    $output = Consignment::getInstantLabel($consignment, $labelType, $labelSize, true);
                    if (trim($output['STATUS']) == 'ERROR') {
                        $marketPlaceOrder->setConsignmentId('0');
                        $marketPlaceOrder->save();
                        $consignment->setShipmentStatus(Consignment::STATUS_RECYCLED);
                        $consignment->setConsignmentStatus('recycled');
                        $consignment->save();
                    }
                    // Generate Label Case of change status
                    $userMarketPlacesMappingFilter = new UserMarketPlacesMappingFilter();
                    $userMarketPlacesMappingFilter->addFieldFilter('    user_account_id', $userAccountId);
                    $userMarketPlacesMappingFilter->addFieldFilter('market_places_id', $marketPlaceOrder->getMarketplaceId());
                    $userMarketPlacesMappingObj = $userMarketPlacesMappingFilter->getList();
                    $storeKey = '';
                    if (count($userMarketPlacesMappingObj) > 0) {
                        $storeKey = $userMarketPlacesMappingObj[0]->getStoreKey();
                    }
                    $getOrderApiUrl = API2CART_URL . 'order.update.json?api_key=' . API2CART_API_KEY . '&store_key=' . $storeKey . '&date_finished=' . date("Y-m-d H:i:s", time()) . '&order_id=' . $marketPlaceOrder->getMarketPlaceOrderNumber() . '&order_status=Shipped&comment=Order comment&params=force_all';
                    $resultData = json_decode(file_get_contents($getOrderApiUrl), true);
                }
            }
            echo json_encode($output);
            die;
        } // check if all same products are selected
        else if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "bulkLabelVerification") {
            $idArray = array();
            $titleArray = array();

            foreach ($_POST["conIdArr"] as $orderNumber) {
                $marketPlaceOrderFlr = new MarketPlaceOrderFilter();
                $marketPlaceOrderFlr->addFieldFilter('    marketplace_order_number', $orderNumber);
                $id = $marketPlaceOrderFlr->getColumnList("marketplace_order_number");
                $idArray[] = $id[0]->getId();
            }

            foreach ($idArray as $marketPlaceTableId) {
                $marketPlaceOrderDetailFlr = new MarketPlaceOrderDetailsFilter();
                $marketPlaceOrderDetailFlr->addFieldFilter("    marketplace_order_id", $marketPlaceTableId);
                $title = $marketPlaceOrderDetailFlr->getColumnList("title");
                $titleArray[] = $title[0]->getTitle();
            }

            if (count(array_unique($titleArray)) === 1 && end($titleArray) === $titleArray[0]) {
                $output["STATUS"] = "SUCCESS";
                $output["MESSAGE"] = "SUCCESS";
                echo json_encode($output);
                die;
            } else {
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = "Please select the same product for bulk action";
                echo json_encode($output);
                die;
            }
        } else if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "generateBulkLabel") {
            $bulkWeight = $_POST["bulk_weight"];
            $bulkNumberPieces = $_POST["bulk_number_pieces"];
            $bulkservice = $_POST['bulk_service'];
            $bulkIds = $_POST['orderIdArr'];
            $output = array();
            $pdfMerger = new PDFMerger();
            $time = date('Ymdhis');
            $mergeFileName = '../_assets/pdf/' . date('Y_m_d') . '/' . "merged" . $time . ".pdf";

            foreach ($bulkIds as $Ids) {
                $marketPlaceOrderFilter = new MarketPlaceOrderFilter();
                $marketPlaceOrderFilter->addFieldFilter("    marketplace_order_number", $Ids);
                $result = $marketPlaceOrderFilter->getColumnList('id');
                $marketPlaceOrderId = $result[0]->getId();

                $marketPlaceOrderDetailsFilter = new MarketPlaceOrderDetailsFilter();
                $marketPlaceOrderDetailsFilter->addFieldFilter("marketplace_order_id", $marketPlaceOrderId);
                $result = $marketPlaceOrderDetailsFilter->getColumnList('id');
                $marketPlaceOrderDetailId = $result[0]->getId();

                $weight = $bulkWeight;
                $numberOfPieces = $bulkNumberPieces;
                $serviceId = $bulkservice;

                $length = 1;
                $width = 1;
                $height = 1;
                $marketPlaceOrder = new MarketPlaceOrder($marketPlaceOrderId);
                $marketOrderDetails = new MarketPlaceOrderDetails($marketPlaceOrderDetailId);

                // Set Sender Data
                $userId = $this->user->getId();
                $userAccountId = $this->user->getUserAccountId();
                $user = new User($userId);
                $userAccount = new CustomerAccount($userAccountId);
                $serflr = new Services($serviceId);
                if (sizeof($serflr) > 0) {
                    $service_id = $serflr->getId();
                    $consignmentArray['service'] = $service_id;
                    if ($serflr->getIsCustomized())
                        $consignmentArray['is_product'] = 1;
                    else
                        $consignmentArray['is_product'] = 0;
                }
                $conFilter = new ConsignmentFilter();
                $conFilter->addFieldFilter("    hawb", $marketPlaceOrder->getMarketPlaceOrderNumber());
                $result = $conFilter->getConList("id");

                if (count($result) > 0) {
                    $consignmentArray['id'] = $result[0]->getId();
                }

                $consignmentArray['sender_company'] = substr(($userAccount->getCompany()), 0, 100);
                $consignmentArray['sender_contact'] = substr(($userAccount->getFullName()), 0, 100);
                $consignmentArray['sender_email'] = $this->replaceSpecial($userAccount->getEmail());
                $consignmentArray['sender_telephone'] = (substr(str_replace(' ', '', $userAccount->getTelephone()), 0, 20));
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
                $consignmentArray['receiver_email'] = ''; //$this->replaceSpecial($marketPlaceOrder->getReceiverName());
                $consignmentArray['receiver_telephone'] = (substr(str_replace(' ', '', $marketPlaceOrder->getReceiverPhone()), 0, 20));
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
                $weight = $weight / $numberOfPieces;  // do weight validation
                $count = 0;

                for ($count = 0; $count < $numberOfPieces; $count++) { //for loop change .. upto number of pieces
                    $consignmentArray['parcel'][$count]['weight'] = $weight;
                    $consignmentArray['parcel'][$count]['length'] = 1;
                    $consignmentArray['parcel'][$count]['height'] = 1;
                    $consignmentArray['parcel'][$count]['width'] = 1;
                }

                $result = Consignment::saveShipment($consignmentArray, $userId);

                $outputtemp = [];
                if (trim($result['STATUS']) == 'ERROR') {
                    if (is_array($result['MESSAGE'])) {
                        $outputtemp['MESSAGE'] = implode(",", $result['MESSAGE']);
                    } else {
                        $outputtemp['MESSAGE'] = $result['MESSAGE'];
                    }
                    $outputtemp['STATUS'] = 'ERROR';
                    $output[] = $outputtemp;
                } else {
                    $consignmentId = $result['CONSIGNMENT_ID'];
                    if ($consignmentId > 0) {
                        $labelType = (!empty($formPostArray["label_type"]) ? $formPostArray["label_type"] : 'pdf');
                        $labelSize = (!empty($formPostArray["label_size"]) ? $formPostArray["label_size"] : '100x150');
                        $marketPlaceOrder->setConsignmentid($consignmentId);
                        $marketPlaceOrder->save();
                        $consignment = new Consignment($consignmentId);
                        $outputLabel = Consignment::getInstantLabel($consignment, $labelType, $labelSize, true);

                        if ($outputLabel["STATUS"] == "SUCCESS") {
                            $outputtemp["STATUS"] = "SUCCESS";
                            $outputtemp["LABEL"] = $outputLabel['LABEL'];
                            $outputtemp["MERGEDLINK"] = $mergeFileName;
                            $output[] = $outputtemp;

                            $position = strpos($outputLabel['LABEL'], '_assets');
                            $localLink = substr($outputLabel['LABEL'], $position);
                            $pdfMerger->addPDF('../' . $localLink, 'all');
                        }
                    }
                }
            }

            $pdfMerger->merge('file', $mergeFileName);
            echo json_encode($output);
            die;
        } // Get Values for Pop up
        else if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "ITEMORDERDETAIL") {
            $consignmenId = '';
            //$marketPlaceOrderDetailId = $this->form_vars["orderid"];
            $trackingNumber = $this->form_vars["trackingnumber"];
            $marketPlaceOrderId = $this->form_vars["marketplaceorderid"];
            $marketplaceordernumber = $this->form_vars["marketplaceordernumber"];
            $consignmentId = $this->form_vars["consignmentid"];

            //$marketOrderDetails = new MarketPlaceOrderDetails($marketPlaceOrderDetailId);
            $marketOrderDetailsFilter = new MarketPlaceOrderDetailsFilter();
            $marketOrderDetailsFilter->addFieldFilter('marketplace_order_id', $marketPlaceOrderId);
            $orderItems = $marketOrderDetailsFilter->getColumnList('*');
            
            $marketPlaceOrder = new MarketPlaceOrder($marketPlaceOrderId);
            ///Getting marketplace logo
            $marketPlaces = new MarketPlaces($marketPlaceOrder->getMarketPlaceId());
            $marketPlaceLogo = "../images/thirdparty/" . strtolower($marketPlaces->getIntegrationLogo());

            ///////////////END/////////////////
            $countryId = $marketPlaceOrder->getReceiverCountryId();
            $countryObj = new Country($countryId);
            $country = $countryObj->getIso();
            
            // MarketPlace order Details Table
            //$title = $marketOrderDetails->getTitle();
            //$asin = $marketOrderDetails->getAsin();
            //$quantity = $marketOrderDetails->getQuantityPurchased();
            //$currency = $marketOrderDetails->getCurrency();
            //$itemId = $marketOrderDetails->getmarketplaceItemId();
           
            // MarketPlace order table
            $output = '';
            $amount = $marketPlaceOrder->getOrderTotal();
            $contact = $marketPlaceOrder->getReceiverName();
            $addressLine1 = $marketPlaceOrder->getReceiverAddressLine1();
            $addressLine2 = $marketPlaceOrder->getReceiverAddressLine2();
            $city = $marketPlaceOrder->getReceiverCity();
            $postcode = $marketPlaceOrder->getReceiverPostCode();
            $telephone = $marketPlaceOrder->getReceiverPhone();
            //$output .= '<div id= "testdiv"> <img src='.$marketPlaceLogo.'></img></div>';
            $output .= '<div id ="res_message_popup" class="row display-none">
                            <div class="col-md-12">
                            <div class="alert alert-success"></div>
                            </div>
                            </div>';
            $output .= '<div class="col-md-12">
                        <h4 class="text-primary"><strong>Order Detail</strong><span class="pull-right">';
            if($marketPlaceOrder->getOrderStatus() == 'Shipped') {
                $output .= '<small class="margin-right-10">Shipped On: ' . $marketPlaceOrder->getShippedDate() . '</small>';
            }
             $output .= $this->getStatus($marketPlaceOrder->getOrderStatus()) . '</span></h4>
                            <table  class="table table-striped table-bordered table-hover">
                            <tr>                            
                            <td><strong>Item ID</strong></td>
                            <td><strong>Title</strong></td>
                            <td><strong>Asin</strong></td>
                            <td><strong>SKU</strong></td>
                            <td><strong>Qty</strong></td>
                            <td><strong>Price</strong></td>
                            </tr>';
            
            foreach($orderItems as $itemDetail)
            {
                $output .= '<tr>
                            <td>' . $itemDetail->getMarketplaceItemId() . ' </td>                       
                            <td>' . $itemDetail->getTitle() . ' </td>                       
                            <td>' . $itemDetail->getAsin() . '</td>
                            <td>' . $itemDetail->getsku() . '</td>
                            <td>' . $itemDetail->getquantityPurchased() . '</td>
                            <td>' . $itemDetail->getItemPrice().' '.$itemDetail->getCurrency(). '</td>                        
                            </tr>';
                            
                            
            }
            $output .= '</table></div>';
            $output .= '<div class="col-md-12">
                            <h4 class="text-primary"><strong>Shipping Detail</strong></h4>
                            <table  class="table table-striped table-bordered table-hover">
                            <tr>
                            <td><strong>Receiver Name:</strong></td>
                            <td>' . $contact . '</td>
                            </tr>
                            
                            <tr>
                            <td><strong>Country:</strong></td>
                            <td>' . $country . '</td>
                            </tr>
                            <tr>
                            <td><strong>PostCode:</strong></td>
                            <td>' . $postcode . '</td>
                            </tr>
                            <tr>
                            <td>
                            <strong>Telephone:</strong>
                            </td>
                            <td>' . $telephone . '</td>                        
                            </tr>
                            <tr>
                            <td>
                            <strong>Address:</strong> 
                            <td> ' . $addressLine1 . ' ' . $addressLine2 . ' ' . $city . ' </td>                       
                            </tr>
                            </table>
                            </div>
                        </div>';
            $output .= '<div class="col-md-12"><h4 class="text-primary"><strong>OneWorld Service</strong></h4>';

            //if shipment is already in the system and label has been generated then don't show weight service dropdown etc.

            if ($consignmentId > 0) {
                $con = new Consignment($consignmentId);
                $weight = $con->getWeight();
                $numberPieces = $con->getNumberPieces();
                $serviceId = $con->getServiceId();
                $ser = new Services($serviceId);
                $serviceName = $ser->getName();

                $labelUrl = SETTING_URL . '_assets/pdf/' . $con->getLabelFile();
                $numberPieces = $con->getNumberPieces();

                $output .= '<input type="hidden" class="form-control form-filter" name="weight" id= "weight" value=' . $weight . '>
                                            <input type="hidden" class="form-control form-filter" name="numberPieces" id= "noofpieces" value = ' . $numberPieces . '>
                                            <input type="hidden" class="form-control form-filter" name="service" id= "service" value = ' . $serviceName . '>';

                $output .= '
                                             <div class="row">
                                             <div class="col-md-4">
                                             <strong>Weight:</strong>
                                             <span>' . $weight . '</span>
                                             </div>
                                             <div class="col-md-4">
                                             <strong>Service:</strong>
                                             <span>' . $serviceName . '</span>
                                             </div>
                                             <div class="col-md-4">
                                             <strong>No. of Pieces:</strong>
                                             <span>' . $numberPieces . '</span>
                                             </div>';
            } else {
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
                if (count($servicesData) > 0) {
                    foreach ($servicesData as $userServiceData) {
                        $selectedStr = ($userServiceData->getId() == $selected ? ' selected="selected"' : '');
                        $output .= '<option ' . $selectedStr . '" value="' . $userServiceData->getId() . '">' . $userServiceData->getName() . '</option>';
                    }
                }
                $output .= '</select>                                          
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
            $output .= '<input type="hidden" name="marketplaceorderid" id="marketplaceorderid" value="' . $marketPlaceOrderId . '" class="form-control"/>
                                       <input type="hidden" name="marketplaceorderdetailid" id="marketplaceorderdetailid" value="' . $marketPlaceOrderDetailId . '" class="form-control"/>
                                       <input type="hidden" name="labelLink" id="labelLink" value="' . $labelUrl . '" class="form-control"/> 
                                       </div></div>';
            echo $output;
            exit;
        } else if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "fetch_marketplace_orders") {

            //$_SESSION['userId'] = $this->user->getUserAccountId();
            $user = SessionManager::getUser();
            
            $userMarketPlaceMappingFilter = new UserMarketPlacesMappingFilter();
            $userMarketPlaceMappingFilter->addFieldFilter("user_account_id", $user->getUserAccountId());
            $userMarketPlaceMappingFilter->addFieldFilter("market_places_id", $_POST["marketPlace_id"]);
            $userMarketPlaceMappingObj = $userMarketPlaceMappingFilter->getList();

            if (count($userMarketPlaceMappingObj) > 0) {
                $marketPlaces = new MarketPlaces($userMarketPlaceMappingObj[0]->getMarketPlacesId());
                include_classes([
                    strtolower($marketPlaces->getClassName()) . '.class',
                ]);
                $className = ucwords($marketPlaces->getClassName());
                
                //$authdata = json_decode($userMarketPlaceMappingObj[0]->getAuthData());
                if (!empty($className)) {
                    $marketPlaceClassObj = new $className($userMarketPlaceMappingObj[0]);
                    $result = $marketPlaceClassObj->fetchOrders();
                   // print_r($result); die;
                } else {
                    $result["STATUS"] = "ERROR";
                    $result["MESSAGE"] = "Market place is not configured.";
                }
                echo json_encode($result, true);
                die;
            }
            die;
        } else if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "dispatch_label") {

            $hawb = $_POST['hawb'];
            $marketPlaceObjId = $_POST['marketPlaceId'];
            $marketPlaceObj = new MarketPlaceOrder($marketPlaceObjId);
            $marketPlaceId = $marketPlaceObj->getMarketplaceId();

            $user = SessionManager::getUser();

            $userMarketPlaceMappingFilter = new UserMarketPlacesMappingFilter();
            $userMarketPlaceMappingFilter->addFieldFilter("user_account_id", $user->getUserAccountId());
            $userMarketPlaceMappingFilter->addFieldFilter("market_places_id", $marketPlaceId);
            $userMarketPlaceMappingObj = $userMarketPlaceMappingFilter->getList();

            if (count($userMarketPlaceMappingObj) > 0) {
                $marketPlaceDetailObj = new MarketPlaces($marketPlaceId);
                include_classes([
                    $marketPlaceDetailObj->getClassName() . '.class',
                ]);
                $className = ucwords($marketPlaceDetailObj->getClassName());

                if (!empty($className)) {
                    $marketPlaceClassObj = new $className($userMarketPlaceMappingObj[0]);
                    if($marketPlaceDetailObj->getIsApi2cart() == 1) {
                        $marketPlaceClassObj->marketplace_order_id = $marketPlaceObjId;
                    }
                    $result = $marketPlaceClassObj->dispatchLabel($hawb);
                }
                echo json_encode($result);
                die;
            } else {
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = "We are unable to submit information.";
                echo json_encode($output);
                die;
            }
        } 
        else if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "bulk_dispatch_label") {
            $hawb = $_POST['dispatchArray'];

            $marketPlaceOrderFilter = new MarketPlaceOrderFilter();
            $marketPlaceOrderFilter->addFieldFilter('    marketplace_order_number', $hawb[0]);
            $result = $marketPlaceOrderFilter->getColumnList('*');

            if (count($result) > 0) {
                $marketPlaceId = $result[0]->getMarketPlaceId();
            } else {
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = "We are unable to submit information.";
                echo json_encode($output);
                die;
            }

            $user = SessionManager::getUser();
            $userMarketPlaceMappingFilter = new UserMarketPlacesMappingFilter();
            $userMarketPlaceMappingFilter->addFieldFilter("user_account_id", $user->getUserAccountId());
            $userMarketPlaceMappingFilter->addFieldFilter("market_places_id", $marketPlaceId);
            $userMarketPlaceMappingObj = $userMarketPlaceMappingFilter->getList();

            if (count($userMarketPlaceMappingObj) > 0) {
                $marketPlaceDetailObj = new MarketPlaces($marketPlaceId);
                include_classes([
                    $marketPlaceDetailObj->getClassName() . '.class',
                ]);
                $className = ucwords($marketPlaceDetailObj->getClassName());

                if (!empty($className)) {
                    $marketPlaceClassObj = new $className($userMarketPlaceMappingObj[0]);
                    $result = $marketPlaceClassObj->dispatchLabel($hawb);
                }
                echo json_encode($result);
                die;
            } else {
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = "We are unable to submit information.";
                echo json_encode($output);
                die;
            }
        }
        
         else if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "wms_data") {
            $counter = 0;
            $hawb = $_POST['dispatchArray'];            
            $consignmentFilter = new ConsignmentFilter();
            $consignmentFilter->addJoin('marketplace_order mo','mo.marketplace_order_number = c.hawb');
            $consignmentFilter->addJoin('market_places mp','mp.id = mo.marketplace_id');
            $consignmentFilter->addJoin('services s','s.id = c.service_id');
            
            $consignmentFilter->addFilterNew("hawb in ('".implode("','",$hawb)."')");
            $idArray = $consignmentFilter->getListNew("c.id,mp.title,s.code,mo.id as marketplaceorder_id, c.country_id, c.awb, c.hawb");
           // print_r($idArray); die;
            
            foreach($idArray as $id)
            {
                $datatoWMS = $this->DataToWms($id);
                //echo $datatoWMS; die;
                if($datatoWMS == 1)
                {
                    $counter++;
                }
                else
                {
                    $rejectedArray[] = $id->getHawb(). ' Error: '. $datatoWMS;
                }
            }
            
            /*if (count($result) > 0) {
                $marketPlaceId = $result[0]->getMarketPlaceId();
            } else {*/
                if(count($rejectedArray) > 0)
                {
                    $message = implode(',',$rejectedArray);
                    $rejectedMessage =  'Data has not been transfered for '.$message;
                }
                $output["STATUS"] = "STATUS";
                $output["MESSAGE"] = $counter." shipment/s has been exported to WMS successfully</br>".$rejectedMessage;
                echo json_encode($output);
                die;
            //}           
        }
        
        else if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'download_shipments_csv') {
            
            $count = 0;
            $this->addFilters();
            $consignment_array = $this->marketPlaceOrderFilter->getColumnList('*');
            $count = count($consignment_array);

            if ($count > 0) {
                header("Content-Type: application/csv");
                header("Content-Disposition: attachment; filename=exported_consignments.csv");
                $csv = "";
                $csv .= MarketPlaceOrder::exportMarketPlaceOrderHeader();
                foreach ($consignment_array as $consignment) {
                    $csv .= $consignment->exportRow($consignment);
                }
                $this->table_msg = formatMessages(SUCCESS_EXPORTED_FILE);
                echo $csv;
                die;
            } else {
                $this->table_msg = formatMessages(ERROR_DATA_EXPORT);
            }
        } else if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'Recycle') {
            $deleteArray = $_POST['deleteArray'];
            //  print_r($deleteArray); die;
            foreach ($deleteArray as $deleteShipment) {
                $marketPlaceOrderFilter = new MarketPlaceOrderFilter();
                $marketPlaceOrderFilter->addFieldFilter('   marketplace_order_number', $deleteShipment);
                $result = $marketPlaceOrderFilter->getColumnList('*');
                $marketPlaceObjectId = $result[0]->getId();

                if ($result[0]->getConsignmentId() != '') {
                    $consignmentId = $result[0]->getConsignmentId();
                    $consignemnt = new Consignment($consignmentId);
                    $consignemnt->setConsignmentStatus('Recycled');
                    $consignemnt->setShipmentStatus(Consignment::STATUS_RECYCLED);
                    $consignemnt->save();
                }

                $marketPlaceOrder = new MarketPlaceOrder($marketPlaceObjectId);
                $marketPlaceOrder->setOrderStatus('Recycled');
                $marketPlaceOrder->save();
            }
            $output["STATUS"] = "SUCCESS";
            $output["MESSAGE"] = "Shipments have been deleted successfully";
            echo json_encode($output);
            die;
        }
    }

    public function replaceSpecial($str) {
        $chunked = str_split($str, 1);
        $str = "";
        foreach ($chunked as $chunk) {
            $num = ord($chunk);
            // Remove non-ascii & non html characters
            if ($num >= 32 && $num <= 123) {
                $str .= $chunk;
            }
        }
        return mb_convert_encoding($str, 'UTF-8');
    }

    public function addFilters() {
        $this->user = $user = SessionManager::getUser();
        $this->marketPlaceOrderFilter->addJoin("marketplace_order_details ord_details", "ord.id = ord_details.marketplace_order_id", "");
        $this->marketPlaceOrderFilter->addFieldFilter("    ord.user_id", $this->user->getUserAccountId(), "");
        $this->marketPlaceOrderFilter->AddGroupBy('ord.marketplace_order_number');
        $dataTableColumnName = $this->orderByDT;
        /*
         * Column filter
         * For search
         */
        $searchDateFrom = $this->form_vars['search_Date_from'];
        $searchDateTo = $this->form_vars['search_Date_to'];
        if (!empty($searchDateFrom) || !empty($searchDateTo))
            $this->marketPlaceOrderFilter->addDateFilter($searchDateFrom, $searchDateTo, 'create_time', '');

        $shippedDateFrom = $this->form_vars['shipped_Date_from'];
        $shippedDateTo = $this->form_vars['shipped_Date_to'];
        if (!empty($shippedDateFrom) || !empty($shippedDateTo))
            $this->marketPlaceOrderFilter->addDateFilter($shippedDateFrom, $shippedDateTo, 'shipped_date', ''); // need to replace it with shipped_date

        $searchMarketPlace = $this->form_vars['search_MarketPlace'];
        if (!empty($searchMarketPlace)) {
            $this->marketPlaceOrderFilter->addFieldFilter('marketplace_id', trim($searchMarketPlace));
        }

        $searchHAWB = $this->form_vars['search_HAWB'];
        if (!empty($searchHAWB))
            $this->marketPlaceOrderFilter->addFieldLikeFilter('marketplace_order_number', trim($searchHAWB));

        $searchName = $this->form_vars['search_Name'];
        if (!empty($searchName))
            $this->marketPlaceOrderFilter->addFieldLikeFilter('receiver_name', trim($searchName));
        
        $sku = $this->form_vars['search_Sku'];
        if (!empty($sku))
            $this->marketPlaceOrderFilter->addFieldFilter('sku', trim($sku));
        
        $searchFulfillment = $this->form_vars['search_Fulfillment'];
        if (trim($searchFulfillment) != '') {
            $this->marketPlaceOrderFilter->addFilter('send_wms = "'.$searchFulfillment.'"');
        }
//print_r($this->marketPlaceOrderFilter); die;
        $searchAddress = $this->form_vars['search_City'];
        if (!empty($searchAddress))
            $this->marketPlaceOrderFilter->addFieldLikeFilter('receiver_city', trim($searchAddress));

        $searchCountry = $this->form_vars['search_Country'];
        if (!empty($searchCountry)) {
            $this->marketPlaceOrderFilter->addFieldFilter("receiver_country_id", $searchCountry);
        }
        //echo $this->form_vars['search_Status']; die
        $searchStatus = $this->form_vars['search_Status'];
        if ($searchStatus != '') {
            $amazon_status = $this->amazon_status_array[$searchStatus];
            $this->marketPlaceOrderFilter->addFieldFilter("    order_status", trim($amazon_status));
        }

        $searchDescription = $this->form_vars['search_Description'];
        if ($searchDescription != '') {
            $this->marketPlaceOrderFilter->addFieldFilter("    title", trim($searchDescription));
        }

        $searchTracking = $this->form_vars['search_Tracking'];
        if (!empty($searchTracking)) {
            $this->marketPlaceOrderFilter->addJoin("consignment c", "c.id = ord.consignment_Id", "");
            $this->marketPlaceOrderFilter->addFieldFilter("c.awb", $searchTracking);
        }
    }

    public static function getStatus($status) {
        $c_status = trim($status);
        if ($c_status == "Shipped") {
            $new_status = '<span class="label label-sm bg-green-jungle bg-font-green-jungle line-height-2"> ' . $status . '</span>';
        } else if ($c_status == "Unshipped") {
            $new_status = '<span class="label label-sm bg-blue-dark bg-font-blue-dark line-height-2"> ' . $status . '</span>'; //= Translation::GetCaption("SHIPPED");
        } else if ($c_status == "Pending") {
            $new_status = '<span class="label label-sm label-danger line-height-2"> ' . $status . '</span>';
        } else if ($c_status == "Delivered") {
            $new_status = '<span class="label label-sm bg-green-jungle bg-font-green-jungle line-height-2"> ' . $status . '</span>';
        } else if ($c_status == "Canceled") {
            $new_status = '<span class="label label-sm label-warning line-height-2"> ' . $status . '</span>';
        }
        return $new_status;
    }

    /*     * *
     * Insert content in to HTML Head section
     */

    public function addDateFilter($date_value1, $date_value2, $filterDate, $filter = 'marketplaceorderfilter') {
        $this->filter .= " AND ";
        $this->filter .= "(c.date_created>='" . date('Y-m-d 00:00:00', strtotime($date_value1)) . "'";
        $this->filter .= " AND ";
        $this->filter .= "c.date_created<='" . date('Y-m-d 23:59:59', strtotime($date_value2)) . "')";
    }

    protected function renderHead() {
        
    }

    protected function addPagelavelCss() {
        ?>
        <link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
        <!-- <link rel="stylesheet" href="../assets/global/css/bootstrap-select.min.css" /> -->
        <link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet" type="text/css" />
        <style>
            #order-content-display {
                padding: 0 10px 10px 10px;
            }
        </style>
        <?php
    }

    public function addPagelavelJs() {
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

    protected function renderFooter() {
        ?>

        <script>
            function popupwindow(url, title, w, h) {
                var left = (screen.width / 2) - (w / 2);
                var top = (screen.height / 2) - (h / 2);
                return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
            }
            var count = 0;
            function checkedAll(group) {
                if (count == 0) {
                    for (var i = 0, len = group.length; i < len; i++) {
                        if (group[i].checked == false)
                            group[i].click(); //checked = true;
                        count = 1;
                    }
                } else {
                    for (var i = 0, len = group.length; i < len; i++) {
                        if (group[i].checked == true)
                            group[i].click(); // = false;
                        count = 0;
                    }
                }
            }

            $(document).ready(function () {

                $('button.click')
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                }
                
                var elindex = 0;
                if (jQuery('#elindex-hardcode').length > 0) {
                    elindex = jQuery('#elindex-hardcode').val();
                    $('#elindex-hardcode').val(1);
                }
                $(document).on('click', '.add_more_sku_order_keys', function () {
                    elindex++;
                    $('#elindex-hardcode').val(elindex +1);
                    var clone = $(this).parent().parent().parent().clone();
                    
                    var title = $(clone).find('.title').attr('name');
                    var title_id = $(clone).find('.title').attr('id');
                    var sku = $(clone).find('.sku').attr('name');
                    var sku_id = $(clone).find('.sku').attr('id');
                    var quantity = $(clone).find('.quantity').attr('name');
                    var quantity_id = $(clone).find('.quantity').attr('id');
                    var price = $(clone).find('.price').attr('name');
                    var price_id = $(clone).find('.price').attr('id');
                    $(this).remove();
                    $(clone).find('.title').attr('name', title.replace(/\d+/, elindex));
                    $(clone).find('.title').attr('id', title_id.replace(/\d+/, elindex));
                    $(clone).find('.sku').attr('name', sku.replace(/\d+/, elindex));
                    $(clone).find('.sku').attr('id', sku_id.replace(/\d+/, elindex));
                    $(clone).find('.quantity').attr('name', quantity.replace(/\d+/, elindex));
                    $(clone).find('.quantity').attr('id', quantity_id.replace(/\d+/, elindex));
                    $(clone).find('.price').attr('name', price.replace(/\d+/, elindex));
                    $(clone).find('.price').attr('id', price_id.replace(/\d+/, elindex));
                    
                    $(clone).find('button.remove_sku_order_key').show();
                    $(clone).find('button.remove_sku_order_key').removeClass('initial-button');;
                    $(clone).appendTo($('.manual_order'));
                    
                });
                
                $(document).on('click', '#btnSubmitImport', function () {
                    $("#file_in").val("");
                    $("#csv_upload").modal('show');
                });
                
                $(document).on('click', '#btn_Cancel', function () {
                    $.ajax({
                        method: "POST",
                        url: "marketplace.php",
                        data: {func: "cancelrecord"}
                    }).done(function (data) {
                        $("#OrderDetailForm")[0].reset();
                    });
                });

                $(document).on('click', '#btn_ViewLabel', function () {
                    var LabelLink = $('#labelLink').val();
                    popupwindow(LabelLink, 'Label View', 550, 400);
                });

                $(document).on('click', '#btn_bulkLabelLink', function () {
                    var LabelLink = $('#bulkLabelLink').val();
                    alert(LabelLink);
                    popupwindow(LabelLink, 'Label View', 550, 400);
                });
                
                $(document).on('click', '#download_csv', function () {
                     //var groupId = 'download_template';
                     //$("#option_value").val(groupId);
                     $("#hiddenForm").submit();
                });
                
                $(document).on('click', '#upload_csv', function () {
                  //  $('#console_window').show();
                    //$('#console_window').html('');
                    //$('#console_window').html("Uploading CSV File....<br />");
                    var file_data = $('#file_in').prop('files')[0];
                    $('#csv_upload').modal('hide');
                    var form_data = new FormData();
                    form_data.append('csv_file', file_data);
                    form_data.append('func', 'upload_csv_file');
                    $.ajax({
                        url: "marketplace.php",
                        dataType: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        type: 'post',
                        success: function (response) {
                            if (response.status == 'success') {
                                $("#res_message div.alert").removeClass('alert-danger').addClass('alert-success');
                                $("#res_message div.alert").html(response.message);
                                $("#res_message").show();
                                grid.getDataTable().ajax.reload();
                            } else {
                                $("#res_message div.alert").removeClass('alert-success').addClass('alert-danger');
                                $("#res_message div.alert").html(response.message);
                                $("#res_message").show();
                                grid.getDataTable().ajax.reload();
                            }
                        }
                    });
                    return false;
                });

                DataTableFun.init();

                $(document).on('click', '.showOrderDetail', function () {
                    var e = $(this);
                    var orderid = e.data('orderid');
                    var trackingnumber = e.data('trackingnumber');
                    var marketplaceorderid = e.data('marketplaceorderid');
                    var marketplaceordernumber = e.data('marketplaceordernumber');
                    var orderstatus = e.data('orderstatus');
                    var consignmentid = e.data('consignmentid');
                    var marketPlaceLogo = e.data('marketplacelogo');

                    if (trackingnumber == '') {
                        $('#btn_ViewLabel').hide();
                        $('#btn_DispatchLabel').hide();
                        $('#btn_GenerateLabel').show();
                    } else {
                        if (orderstatus == 'Shipped') {
                            $('#btn_DispatchLabel').hide();
                        } else {
                            $('#btn_DispatchLabel').show();
                        }
                        $('#btn_ViewLabel').show();
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
                    $('#flag_marketplace').html("<img src=../images/" + marketPlaceLogo + "  height='40px' />");

                    var action = e.data('action');
                    var url = e.data('load');
                    $.post(url, {
                        func: action,
                        orderid: orderid,
                        trackingnumber: trackingnumber,
                        marketplaceorderid: marketplaceorderid,
                        marketplaceordernumber: marketplaceordernumber,
                        consignmentid: consignmentid
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
                                "url": "marketplace.php?action=amazon_ajax", // ajax source
                                headers: {},
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "actionss"},
                                {"data": "created", sClass: "text-center"},
                                {"data": "marketplace", sClass: "text-center"},
                                {"data": "sku", sclass: "text-center"},
                                {"data": "contact"},
                                {"data": "country", sClass: "text-center"},
                                {"data": "status", sClass: "text-center"},
                                {"data": "price"},
                                {"data": "tracking_no"},
                                {"data": "label"},
                            ],

                            "initComplete": function (settings, json) {
                                $('.popovers').popover();
                                $('.showOrderDetail').tooltip();
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

            $('#btn_fetchOrder').click(function () {
                var marketPlace_id = $('#marketPlace_id_top').val();

                $.ajax({
                    method: "POST",
                    url: "marketplace.php", // need to get this name dynamically
                    data: {
                        marketPlace_id: marketPlace_id,
                        func: 'fetch_marketplace_orders',
                    }
                }).done(function (data) {

                    var rs = JSON.parse(data);
                    if (rs.STATUS === "SUCCESS") {
                        //alert("All orders has been fetched");
                        $("#res_message div.alert").removeClass('alert-danger').addClass('alert-success');
                        $("#res_message div.alert").html(rs.MESSAGE);
                        $("#res_message").show();
                        grid.getDataTable().ajax.reload();
                    } else if (rs.STATUS === "ERROR") {
                        $("#res_message div.alert").removeClass('alert-success').addClass('alert-danger');
                        $("#res_message div.alert").html(rs.MESSAGE);
                        $("#res_message").show();
                        grid.getDataTable().ajax.reload();
                    }
                });
            });

            $(document).on('click', '#btn_GenerateLabel', function () {

                var marketPlaceOrderId = $('#marketplaceorderid').val();
                var marketPlaceOrderDetailId = $('#marketplaceorderdetailid').val();
                var weight = $('#weight').val();
                var numberPieces = $('#number_pieces').val();
                var service = $('#service').val();

                $.ajax({
                    method: "POST",
                    url: "marketplace.php",
                    data: {
                        func: "generateLabel", service: service,
                        numberPieces: numberPieces,
                        weight: weight,
                        marketPlaceOrderId: marketPlaceOrderId,
                        marketPlaceOrderDetailId: marketPlaceOrderDetailId
                    }
                }).done(function (data) {
                    var t = JSON.parse(data);

                    if (t.STATUS == "SUCCESS") {
                        $('#btn_GenerateLabel').hide();
                        $('#btn_ViewLabel').show();
                        $('#btn_DispatchLabel').show();
                        $('#labelLink').val(t.LABEL);
                        $("#res_message_popup div.alert").removeClass('alert-danger').addClass('alert-success');
                        $("#res_message_popup div.alert").html('Label generated successfully.');
                        $("#res_message_popup").show();
                        grid.getDataTable().ajax.reload();

                    } else if (t.STATUS == "ERROR") {
                        $("#res_message_popup div.alert").removeClass('alert-success').addClass('alert-danger');
                        $("#res_message_popup div.alert").html(t.MESSAGE);
                        $("#res_message_popup").show();
                    }
                });
            });
            
             $(document).on('click', '#btn_manual_save', function () {
                    var form = $('#ManualOrder').serialize(); // You need to use standard javascript object here
                   // var formData = new FormData(form);
                    $.ajax({
                        method: "POST",
                        url: "marketplace.php",
                        data: form,
                        dataType: 'json'
                    }).done(function (data) {
                        
                        if (data.STATUS == "SUCCESS") {
                            $("#show_general_msg div.alert").addClass('alert-success').removeClass('alert-danger');
                            $("#show_general_msg div.alert").html('');
                            $("#show_general_msg div.alert").html(data.MESSAGE);
                            $("#show_general_msg").show();
                        } else if (data.STATUS == "ERROR") {
                            $("#show_general_msg div.alert").addClass('alert-danger').removeClass('alert-success');
                            $("#show_general_msg div.alert").html('');
                            $("#show_general_msg div.alert").html(data.MESSAGE);
                            $("#show_general_msg").show();
                        }
        
                });
            });
      

            $('#btn_DispatchLabel').click(function () {
                var hawb = $('#OrderDetail').html();
                var marketPlaceId = $('#marketplaceorderid').val();

                $.ajax({
                    method: "POST",
                    url: "marketplace.php", // your php file name
                    data: {
                        hawb: hawb,
                        marketPlaceId: marketPlaceId,
                        func: 'dispatch_label'},
                }).done(function (data) {
                    var rs = JSON.parse(data);
                    if (rs.STATUS === "SUCCESS") {
                        $("#res_message_popup div.alert").addClass('alert-success');
                        $("#res_message_popup div.alert").html(rs.MESSAGE);
                        $("#res_message_popup").show();
                        grid.getDataTable().ajax.reload();
                        $('#btn_ViewLabel').show();
                        $('#btn_DispatchLabel').hide();
                        $('#btn_GenerateLabel').hide();
                        // alert("Order has been Dispatched");
                        // grid.getDataTable().ajax.reload();
                    } else if (rs.STATUS === "ERROR") {
                        $("#res_message_popup div.alert").addClass('alert-danger');
                        $("#res_message_popup div.alert").html(rs.MESSAGE);
                        $("#res_message_popup").show();
                        grid.getDataTable().ajax.reload();
                    }

                });
            });

            function consignmentSelectionConfirmation(errorMessage, errorMessagenew) {
                if (typeof (document.getElementsByName("dispatchArray[]")) == 'undefined') {
                    swal("", errorMessage, "info");
                    return false;
                }
                var deleteConsign = document.getElementsByName("dispatchArray[]");
                var txt = "";
                var i;
                for (i = 0; i < deleteConsign.length; i++) {
                    if (deleteConsign[i].checked) {
                        txt = txt + deleteConsign[i].value + "";
                    }
                }

                if (txt == "") {
                    swal("", errorMessage, "info");
                    return false;
                }
                return true;
            }

            function bulk_selected_action() {
                var bulkAction = $('#bulk_action option:selected').val();
                conIdArr = [];
                $.each($('input[name="ordersArray[]"]:checkbox:checked'), function () {
                    conIdArr.push($(this).val());
                });

                if (bulkAction == 'export')
                {
                    $('#form_filter_action').val('download_shipments_csv')
                    $("#form_filter").submit();
                } else if (bulkAction == 'delete')
                {
                    deleteArr = [];
                    $.each($('input[name="dispatchArray[]"]:checkbox:checked'), function () {
                        deleteArr.push($(this).val());
                    });
                    if (!consignmentSelectionConfirmation("<?php echo Translation::GetCaption("There is no shipment selected, please select any shipment.") ?>")) {
                        $("#form_filter_action").val('').change();
                        return false;
                    }
                    swal({
                        title: "<?php echo Translation::GetCaption("ARE_YOU_SURE_YOU_WANT_TO_DELETE_THESE_SHIPMENT?") ?>",
                        text: "",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    },
                            function (isConfirm) {
                                if (isConfirm)
                                {
                                    $.ajax({
                                        method: "POST",
                                        url: "marketplace.php",
                                        data: {func: "Recycle", deleteArray: deleteArr}
                                    }).done(function (data) {

                                        var rs = JSON.parse(data);

                                        if (rs.STATUS == "SUCCESS")
                                        {
                                            $("#res_message div.alert").addClass('alert-success');
                                            $("#res_message div.alert").html(rs.MESSAGE);
                                            $("#res_message").show();
                                            grid.getDataTable().ajax.reload();
                                        } else if (t.STATUS == "ERROR")
                                        {
                                        }
                                    });

                                }
                            });
                } else if (bulkAction == 'bulkdispatch')
                {
                    dispatchArr = [];
                    $.each($('input[name="dispatchArray[]"]:checkbox:checked'), function () {
                        dispatchArr.push($(this).val());
                    });

                    // var jsonString = JSON.stringify(dispatchArr);
                    $.ajax({
                        method: "POST",
                        url: "marketplace.php", // your php file name
                        data: {func: "bulk_dispatch_label", dispatchArray: dispatchArr},
                    }).done(function (data)
                    {
                        var rs = JSON.parse(data);
                        if (rs.STATUS === "SUCCESS")
                        {
                            $("#res_message div.alert").addClass('alert-success');
                            $("#res_message div.alert").html(rs.MESSAGE);
                            $("#res_message").show();
                            grid.getDataTable().ajax.reload();
                        } else if (rs.STATUS === "ERROR")
                        {
                            $("#res_message div.alert").addClass('alert-danger');
                            $("#res_message div.alert").html(rs.MESSAGE);
                            $("#res_message").show();
                            grid.getDataTable().ajax.reload();
                        }
                    });
                }
                else if (bulkAction == 'wmsexport')
                {
                    wmsArr = [];
                    $.each($('input[name="dispatchArray[]"]:checkbox:checked'), function () {
                        wmsArr.push($(this).val());
                    });
                    if (!consignmentSelectionConfirmation("<?php echo Translation::GetCaption("There is no shipment selected, please select any shipment.") ?>")) {
                        $("#form_filter_action").val('').change();
                        return false;
                    }
                    
                     $.ajax({
                        method: "POST",
                        url: "marketplace.php", // your php file name
                        data: {func: "wms_data", dispatchArray: wmsArr},
                    }).done(function (data)
                    {
                        var rs = JSON.parse(data);
                        if (rs.STATUS === "STATUS")
                        {
                            $("#res_message div.alert").addClass('alert-success');
                            $("#res_message div.alert").html(rs.MESSAGE);
                            $("#res_message").show();
                            grid.getDataTable().ajax.reload();
                        } 
                    });
                }
            }

            $(document).on('click', '#btn_GenerateBulkLabel', function () {
                var bulk_weight = $('#bulk_weight').val();
                var bulk_number_pieces = $('#bulk_number_pieces').val();
                var bulk_service = $('#bulk_service').val();

                orderIdArr = [];
                $.each($('input[name="ordersArray[]"]:checkbox:checked'), function () {
                    orderIdArr.push($(this).val());
                });

                $.ajax({
                    method: "POST",
                    url: "marketplace.php",
                    data: {
                        func: "generateBulkLabel", bulk_service: bulk_service,
                        bulk_number_pieces: bulk_number_pieces,
                        bulk_weight: bulk_weight,
                        orderIdArr: orderIdArr
                    }
                }).done(function (data) {
                    var t = JSON.parse(data);
                    $.each(t, function (index, item) {
                        //  alert(t[index].LABEL);
                        // alert(t[index].MESSAGE);
                        // alert(t[index].STATUS);
                        //

                        if (t[index].STATUS == "SUCCESS") {
                            $('#btn_bulkLabelLink').show();
                            //var LabelLink = $('#bulkLabel').val();
                            //$('#labelLink').val(t[index].LABEL);
                            $('#bulkLabelLink').val(t[index].MERGEDLINK);
                            grid.getDataTable().ajax.reload();
                        } else if (t[index].STATUS == "ERROR") {
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

                        Marketplace Order Management

                    </div>



                    <div class="actions">                        
                        <a href="javascript:;" class="tooltipbutton btn-xs blue btn mt-ladda-btn ladda-button btn btn-primary" 
                            data-toggle="modal" data-target="#manual-order-popup" role="dialog" tabindex="-1" 
                            data-load = "marketplace.php"
                            data-placement="top"
                            data-title="Manual Order"
                            href="javascript:;" >
                            <span class="">Manual Order</span>
                        </a> 
                        <a id="btnSubmitImport" href="javascript:{};" class="btn btn-sm blue"><span></span><i class="fa fa-upload"></i>&nbsp;<?php echo "Import CSV"; ?></a>
                    <div class="btn-group">
        <?php
        echo Ddl::generateMarketPlaceDDLWithImage('marketPlace_id_top', '', 'id', ' class="bs-select input-sm form-control form-filter pull-right" data-live-search="true"  data-show-subtext="true"');
        ?>
                    </div>
                        <a href="javascript:;" class="btn btn-primary " id="btn_fetchOrder">
                            <i class="fa fa-check"></i>Fetch Order </a>
                    </div>

                </div>
                <div class="portlet-body">
                    <div id="res_message" class="row display-none">
                        <div class="col-md-12">
                            <div class="alert alert-success"></div>
                        </div>
                    </div>
                    <div class="table-actions-wrapper pull-right">
                        <span> </span>
                        <select class="table-group-action-input form-control input-inline input-small input-sm"
                                id="bulk_action" name="bulk_action" style="width:150px !important;">
                            <option value="">Select Action</option>
                            <!-- <option value="bulklabel">Bulk Label</option> -->
                            <option value="bulkdispatch">Dispatch</option>
                            <option value="export">Export</option>
                            <option value="delete">Delete</option>
                            <option value="wmsexport">Export to WMS</option>
                        </select>
                        <button class="btn btn-sm btn-default table-group-action-submit" type="button"
                                onclick="bulk_selected_action()">
                            <i class="fa fa-check"></i> Submit
                        </button>
                    </div>


                    <div class="table-container">
                        <form method="post" name="form_filter" id="form_filter" action="marketplace.php">
                            <input type="hidden" name="func" id="form_filter_action" value="" />
                            <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                                <thead>
                                    <tr role="row" class="heading">
                                        <th width="4%">
                                            <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                                <input type='checkbox' name='checkall' onclick='checkedAll(dispatchIds);' class="group-checkable"/>
                                                <span></span>
                                            </label>
                                        </th>
                                        <th>Created</th>
                                        <th>Market Place<br/>Order Number</th>
                                        <th>Sku<br/>Transferred for Fulfillment</th>
                                        <th>Contact<br/>Description</th>
                                        <th>City<br/>Country</th>
                                        <th>Status<br>Shipped Date</th>
                                        <th>Price</th>
                                        <th>Tracking No</th>
                                        <th>Label</th>
                                    </tr>
                                    <tr role="row" class="filter">
                                        <td width="2%">
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
                                            <input type="text" class="form-control form-filter input-sm" name="search_HAWB" style="margin-top: 5px;">
                                        </td>
                                        <td width="10%">
                                            <input type="text" class="form-control form-filter input-sm" name="search_Sku"> 
                                  <?php $arrayTypeValues = array('1' => 'Yes', '0' => 'No');
                                        echo Ddl::generateArrayDDL('search_Fulfillment', $arrayTypeValues, "", "Is Transferred?", ' class="form-control form-filter select2"', "", 'search_Fulfillment', 'Fulfillment Status', ''); ?>
        
                                        </td>
                                        <td width="12%">
                                            <input type="text" class="form-control form-filter input-sm" name="search_Name">
                                            <input type="text" class="form-control form-filter input-sm" name="search_Description" style="margin-top: 5px;">
                                        </td>                                        
                                        <td class="user_acccount_correct_button">
                                            <input type="text" class="form-control form-filter input-sm" name="search_City" style="margin-bottom: 5px;" >
        <?php echo Ddl::generateCountryDDL('search_Country', '', 'id', ' class="form-filter bs-select form-control input-sm" data-live-search="true" '); ?>
                                        </td>
                                        <td>
                                            <div class="margin-bottom-5">
        <?php
        $this->form_vars["status_list"] = ''; // this line clears the current selected status
        echo Ddl::generateArrayDDL('search_Status', $this->amazon_status_array, $this->form_vars["status_list"], "Select Status", 'class="form-control form-filter input-sm select2 searchbox"', "", 'search_Status', Translation::GetCaption("PLEASE_SELECT_STATUS"));
        ?>
                                            </div>
                                            <div class="input-group date date-picker margin-top-2 margin-bottom-5" data-date-format="yyyy-mm-dd">
                                                <input type="text" class="form-control form-filter input-sm" readonly name="shipped_Date_from" placeholder="From" data-date-format="yyyy-mm-dd">
                                                <span class="input-group-btn">
                                                    <button class="btn btn-sm" type="button"><i class="fa fa-calendar"></i></button>
                                                </span>
                                            </div>
                                            <div class="input-group date date-picker" data-date-format="yyyy-mm-dd">
                                                <input type="text" class="form-control form-filter input-sm" readonly name="shipped_Date_to" placeholder="To" data-date-format="yyyy-mm-dd">
                                                <span class="input-group-btn">
                                                    <button class="btn btn-sm" type="button"><i class="fa fa-calendar"></i></button>
                                                </span>
                                            </div>
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
                        </form>
                    </div>
                    <input type="hidden" name="id" id="id" value="<?php //echo @$id;  ?>" />
                </div>
            </div>
        </div>


        <div class="modal fade" tabindex="-1" role="dialog" id="show-orderDetail-popup">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="modal-title text-primary"><strong>Order Number: <span id="OrderDetail"></span> </strong> </h4>                                
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <div id="flag_marketplace" style="margin-right: 20px;" align="right"></div>

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
                        <input id="btn_ViewLabel" type="button" class="btn btn-primary" value="View Label"/>
                        <input id="btn_DispatchLabel" type="button" class="btn btn-primary" value="Dispatch Label"/>
                        <input id="btn_GenerateLabel" type="button" class="btn btn-primary" value="Generate Label"/>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        
            <form name="hiddenForm" id="hiddenForm" action="" method="POST">
                <input type="hidden" name="option_value" value="" id="option_value"/>
                <input type="hidden" name="action" value="download_csv_frm" />
            </form>
        <!--Model for CSV Upload-->
        <div class="modal fade" id="csv_upload" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Import SKUs</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-group input-group-sm"> <span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
                                        <input class="form-control" id="file_in" name="file_in" type="file" value="" />                
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                        <button type="button" id="upload_csv" class="btn green">Upload</button>
                        <button type="button" id="download_csv" class="btn green">  <i class="fa fa-download"></i> Download Template </button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        
        
        <form name="ManualOrder" id="ManualOrder" action="" method="POST">  
             <input type="hidden" name="func" id="func" value="saveManualRecord" />
        <div class="modal fade" tabindex="-1" role="dialog" id="manual-order-popup">                            
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="modal-title text-primary"><strong>Manual Order Details: <span id="ManualOrderDetail"></span> </strong> </h4>                                
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-body">
                        <div class="row" id="show_general_msg" style="display: none;">
                            <div class="col-md-12">
                                <div class="alert alert-danger"></div>
                            </div>
                        </div>
                    </div>
                           
                                                  
                    <div class="modal-body" >
                               
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Receiver Name:</strong>
                                <input type="text" class="form-control form-filter" name="receiver_name" id= "receiver_name">
                            </div>
                            
                            <div class="col-md-4">
                                <strong>Receiver Phone:</strong>
                                <input type="text" class="form-control form-filter" name="receiver_phone" id= "receiver_phone">
                            </div>
                                   
                            <div class="col-md-4">
                                <strong>Receiver State:</strong>
                                <input type="text" class="form-control form-filter" name="receiver_state" id= "receiver_state">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Receiver City:</strong>
                                <input type="text" class="form-control form-filter" name="receiver_city" id= "receiver_city">
                            </div>
                            
                            <div class="col-md-4">
                                <strong>Receiver Country:</strong>
                                <?php echo Ddl::generateCountryDDL('receiver_country', '', '', ' class="form-filter bs-select form-control input-sm" data-live-search="true"'); ?>
                            </div>
                            
                            <div class="col-md-4">
                                <strong>Address Line 1:</strong>
                                <input type="text" class="form-control form-filter" name="address_line_1" id= "address_line_1">
                            </div>
                        </div>
                                
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Address Line 2</strong>
                                <input type="text" class="form-control form-filter" name="address_line_2" id= "address_line_2">
                            </div>
                        
                            <div class="col-md-4">
                                <strong>Post Code</strong>
                                <input type="text" class="form-control form-filter" name="post_code" id= "post_code">
                            </div>
                        
                            <div class="col-md-4">
                                <strong>Email:</strong>
                                <input type="text" class="form-control form-filter" name="manual_email" id= "manual_email">
                            </div>                                    
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <strong>Order Number:</strong>
                                <input type="text" class="form-control form-filter" name="manual_order_number" id= "manual_order_number">
                            </div>
                        </div>
                        
                        <div class="row"> 
                            <br/>
                            <div class="col-md-4">
                                <label>Title</label>
                            </div>
                            <div class="col-md-2">
                                  <label>Sku</label>
                            </div>
                            <div class="col-md-2">
                                  <label>Quantity</label>
                            </div>
                            <div class="col-md-2">
                                  <label>Price</label>
                            </div>
                            <div class="col-md-2">
                                  <label>Currency</label>
                            </div>
                            <div class="col-md-3">
                                &nbsp;
                            </div>
                        </div>
                        
                        <div class="manual_order" id="manual_order">  
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="has-float-label input-icon right">
                                            <input name="title[0]" id="title_0" value="" size="50" class="form-control title" title="Title" maxlength="35" placeholder="Title" rel="tooltip" data-original-title="Title" type="text" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <div class="has-float-label input-icon right">
                                            <input name="sku[0]" id="sku_0" value="" size="50" class="form-control sku" sku="Sku" maxlength="35" placeholder="Sku" rel="tooltip" data-original-title="Sku" type="text" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <div class="has-float-label input-icon right">
                                            <input name="quantity[0]" id="quantity_0" value="" class="form-control quantity" title="Quantity" maxlength="35" placeholder="Quantity" rel="tooltip" data-original-title="Quantity" type="text" required>
                                        </div>    
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <div class="has-float-label input-icon right">
                                            <input name="price[0]" id="price_0" value="" class="form-control price" title="Price" maxlength="35" placeholder="Price" rel="tooltip" data-original-title="Price" type="text" required>
                                        </div>
                                    </div>
                                    
                                    
                               
                                    <div class="col-md-2">
                                       <button type="button" class="btn btn-success add_more_sku_order_keys"><i class="fa fa-plus"></i></button>
                                       <button type="button" class="btn btn-danger remove_sku_order_key initial-button"><i class="fa fa-minus"></i></button> 
                                    </div>
                                </div>
                            </div>
                        </div>
                                
                        <input type="hidden" name="elindex-hardcode" id="elindex-hardcode" value="0" />                                                                                                    <!--<input type="hidden" name="new" id="new" value="<?php echo @$new; ?>" />-->
                        <br />
                    </div>
                        
                    <div class="modal-footer">
                        <input id="btn_manual_save" type="button"  class="btn btn-primary" value="<?php echo Translation::GetCaption("Save"); ?>"/>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            </div>
                </form>
        
            <!-- /.modal-dialog -->
        
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
                                        
        public function DataToWms($id)
        {
            $con = new Consignment($id->getId());
            $marketplaceName = $id->getTitle();
            $serviceCode = $id->getCode();
            
            $mkpOrderDetailsFilter = new MarketPlaceOrderDetailsFilter();
            $mkpOrderDetailsFilter->addFieldFilter('    marketplace_order_id',$id->getmarketPlaceOrderId());
            $rsDetail = $mkpOrderDetailsFilter->getList();
            
            $country = new Country($id->getCountryId());
            $conIso = $country->getIso();
                        
            $parcelFilter = new ParcelFilter();
            $parcelFilter->addFieldFilter('    tracking_number',$id->getAwb());
            $parcelResult = $parcelFilter->getList();
           
            $pweight = $parcelResult[0]->getWeight();
            $pheight = $parcelResult[0]->getHeight();
            $pwidth = $parcelResult[0]->getWidth();
            $plength = $parcelResult[0]->getLength();
            
            $userAccountObj = new CustomerAccount($this->user->getUserAccountId());
            $userAccountName =  $userAccountObj->getUserAccount();
            
            try 
            {
                    $client = new SoapClient('http://wms-uk.oneworldexpress.cn/WebService/ShipmentService.asmx?wsdl', array('trace' => true));
                    $request = new stdClass();
                    $request->customerCode = $userAccountName;
                    $request->hawb = $con->getHawb();
                    $request->tracking_number = $con->getAwb();
                    $request->Reference = "";
                    $request->company = $con->getCompany();
                    $request->contact = $con->getContact();
                    $request->address_line_1 = $con->getAddressLine1();
                    $request->address_line_2 = $con->getAddressLine2();
                    $request->address_line_3 = $con->getAddressLine3();
                    $request->city = $con->getCity();
                    $request->postcode = $con->getPostCode();
                    $request->country_iso = $conIso;
                    $request->state = "";
                    $request->email = $con->getEmail();
                    $request->telephone = $con->getTelephone();
                    $request->value = $con->getValue();
                    $request->currency = $con->getCurrency();
                    $request->notes = $con->getNotes();

                    $request->servicecode = $serviceCode;
                    $request->weight = $con->getWeight();
                    $request->marketPlaceId = $marketplaceName;
                    $request->label = 'https://www.smarttrack.co/_assets/pdf/'.$con->getlabelFile();
                    $request->consignmentNo = $con->getId(); 
                    $count = 0;
                    foreach($rsDetail as $sku)
                    {
                            $parcelDataModelClass = new stdClass();
                            $parcelDataModelClass->weight = $pweight;
                            $parcelDataModelClass->length = $plength;
                            $parcelDataModelClass->width = $pwidth;
                            $parcelDataModelClass->height = $pheight;
                            $parcelDataModelClass->sku = $sku->getSku();
                            $parcelDataModelClass->description = $sku->getTitle();
                            $parcelDataModelClass->value = $sku->getItemPrice();
                            $parcelDataModelClass->currency = $sku->getCurrency();
                            $parcelDataModelClass->quantity = $sku->getQuantityPurchased();
                            $ParcelDataModelObjArr['ParcelDataModel'][$count] =  $parcelDataModelClass;
                            $count++;
                    }
                    $request->ParcelData = $ParcelDataModelObjArr;
                    $response = $client->CreateShipment( array("request" => $request));
                   // print_r($response); 
                    if($response->CreateShipmentResult->Success == 1)
                    {
                        $marketplaceOrderWms = new MarketPlaceOrder($id->getmarketPlaceOrderId());
                        $marketplaceOrderWms->setSendWms('1');
                        $marketplaceOrderWms->save();
                        return 1;
                    }
                    else
                    {
                        $error = $response->CreateShipmentResult->ErrorMsg;
                        return $error; 
                    }
            } 
            catch (Exception $e) 
            {
                return $e->getMessage();		
            }            
	}

    }

                                    /* ------------------------------------------------------------------------------ */
// create and render page
                                    $page = new Page(CONFIG_TEMPLATE_ADMIN);
                                    $page->show();
