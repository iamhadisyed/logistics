<?php

class SupplierInvoices extends DbAccess3 {

    public function __construct($mixedCreator = null) {
        $fieldList = [
            'id' => 'number',
            'template' => 'number',
            'account_id' => 'number',
            'invoice_number' => 'string',
            'carrier_id' => 'number',
            'carrier_type' => ['enum' => ['agent', 'carrier'], 'carrier'],
            'date' => 'date',
            'invoice_date' => 'date',
            'total_weight' => 'string',
            'total_pieces' => 'string',
            'total_amount' => 'string',
            'total_processed_weight' => 'string',
            'total_processed_pieces' => 'string',
            'total_processed_amount' => 'string',
            'status' => ['enum' => ['pending', 'processed', 'approved'], 'pending'],
            'upload_file' => 'string',
            'invoice_check_file' => 'string',
            'currency' => 'string',
            'reconciliation_file' => 'string',
            'added_date' => 'datetime',
            'added_by' => 'number'
        ];
        parent::__construct("supplier_invoices", 'id', $fieldList, $mixedCreator);
    }

    public static function getSupplierInvoicesListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfSupplierInvoicesFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    public static function deleteSupplierInvoicesFromSql($sql) {
        self::runQuery($sql);
    }

    public static function updateSupplierInvoicesFromSql($sql) {
        self::runQuery($sql);
    }

    public static function deleteSupplierInvoiceBySupplierInvoiceId($supplierInvoiceId) {
        if ($supplierInvoiceId != "" && $supplierInvoiceId > 0) {
            self::runQuery("DELETE FROM supplier_invoices WHERE id = '" . DbAccess3::escape($supplierInvoiceId) . "'");
        }
    }

    public static function reconciliationCsv($returnArr, $carrierName, $dateFolder = "", $reconciliation_csv_file_name = "",$subAccountArr=[]) {
        $output = [];
        if ($dateFolder == "") {
            $dateFolder = date('Y-m-d');
        }
        $rcsvHeading = [
            'account',
            'customer invoice numer',
            'awb',
            'hawb',
            'invoice number',
            'bag number',
            'Carrier ',
            'service name ',
            'service code',
            'weight',
            'vol weight',
            'number of pieces',
            'length',
            'width',
            'height',            
            'customer total',
            'estimated supplier total',
            'supplier total',
            'margin',
            'gp margin'
        ];
        
        $rCsvcomaSeptHeading = implode(",", $rcsvHeading);
        $rCsvCol = rtrim($rCsvcomaSeptHeading, ',');
        $rcsv = $rCsvCol;
        $rcsv .= "\r\n";
        $batchNumber = '';
        $supplierInvoiceId = '';
        $status = $returnArr['status'];
        if ($status == 'success') {
            $dataRec = $returnArr['data'];
            $batchNumber = $returnArr['batch_number'];
            $supplierInvoiceId = $returnArr['supplier_invoice_id'];
            if (count($dataRec)) {
                foreach ($dataRec as $key => $obj) {
                    $consignmentFilterObjR = "";
                    if (!empty($obj->awb) || !empty($obj->hawb)) {
                        $consignmentFilter = new ConsignmentFilter();
                        $consignmentFilter->addJoin('parcel p', 'c.id = p.consignment_id', 'inner');
                        $consignmentFilter->addJoin('services s', 'c.service_id = s.id', 'left');
                        $consignmentFilter->addJoin('carrier cr', 's.carrier_id = cr.id', 'left');
                        if ($obj->hawb != "") {
                            $consignmentFilter->addFieldFilter('     c.hawb', $obj->hawb);
                        }
                        $consignmentFilter->addFieldFilter('     p.tracking_number', $obj->awb);
                        $consignmentFilterObjR = $consignmentFilter->getListNew('c.*,p.length,p.width,p.height,s.code as service_code, s.name as service_name,  cr.carrier as carrier_name');

                        if (count($consignmentFilterObjR) > 0) {
                            $consignmentObj = $consignmentFilterObjR[0];
                            $userAccountId = Consignment::getConsignmentUserAccountIdForPricing($consignmentObj->getId());
                            $acccountData   =   new CustomerAccount($userAccountId);
                            $customerConsignmentCharges = ConsignmentCharges::getConsignmentTotalCharges($consignmentObj->getId(), $userAccountId, 'customer', false);
                            $customerTotal = $customerConsignmentCharges['cost'];
                            $customerInvoiceNumber = $customerConsignmentCharges['invoice_id'];
                            //;
                            
                            //$rcsv .= $acccountData->getUserAccount() . ',';
                            $rcsv .= $acccountData->showAccount($userAccountId,$subAccountArr) . ',';
                            $rcsv .= $customerInvoiceNumber . ',';
                            $rcsv .= $consignmentObj->getAwb() . ',';
                            $rcsv .= $consignmentObj->getHawb() . ',';
                            $rcsv .= $returnArr['invoice_number'] . ',';
                            $rcsv .= $dataRec['bag_number'] . ',';
                            
                            $rcsv .= $consignmentObj->getCarrierName() . ',';
                            $rcsv .= $consignmentObj->getServiceName() . ',';
                            $rcsv .= $consignmentObj->getServiceCode() . ',';
                            $rcsv .= $consignmentObj->getWeight() . ',';
                            $rcsv .= $consignmentObj->getVolWeight() . ',';
                            $rcsv .= $consignmentObj->getNumberPieces() . ',';
                            $rcsv .= $consignmentObj->getLength() . ',';
                            $rcsv .= $consignmentObj->getWidth() . ',';
                            $rcsv .= $consignmentObj->getHeight() . ',';
                            
                            
                            $rcsv .= $customerTotal . ',';
                            $agentConsignmentCharges = ConsignmentCharges::getConsignmentTotalCharges($consignmentObj->getId(), $userAccountId, 'agent', false);
                            $agentTotal = $agentConsignmentCharges['cost'];
                            $rcsv .= $agentTotal . ',';
                            $supplierConsignmentCharges = ConsignmentCharges::getConsignmentTotalCharges($consignmentObj->getId(), $userAccountId, 'purchase_invoice', false);
                            $supplierTotal = $supplierConsignmentCharges['cost'];
                            $rcsv .= $supplierTotal . ',';
                            $rcsv .= ($customerTotal - $supplierTotal) . ',';
                            $rcsv .= number_format(((($customerTotal - $supplierTotal)/$customerTotal)*100),3) . ',';
                            $rcsv .= "\r\n";
                        }
                    }
                }
            }
        }

        /* create new csv file and write data on it */
        if ($reconciliation_csv_file_name == "") {
            $reconciliation_csv_file_name = strtolower($carrierName) . "_reconciliation_" . $batchNumber . ".csv";
        } else {
            $path = RECONCILIATION_PATH . $carrierName . '/' . $dateFolder . '/' . $reconciliation_csv_file_name;
            unlink($path);
        }
        $reconciliation_csv_file_created = RECONCILIATION_PATH . $carrierName . '/' . $dateFolder . '/' . $reconciliation_csv_file_name;
        $exportCsvFile = fopen($reconciliation_csv_file_created, "a") or die("Unable to open file!");
        fwrite($exportCsvFile, $rcsv);
        fclose($exportCsvFile);
        
        
        $reconciliation_csv_file_url = RECONCILIATION_URL . $carrierName . '/' . $dateFolder . '/' . $reconciliation_csv_file_name;
        /* save file name in db */
        if ($supplierInvoiceId != "") {
            $supplierInvoices = new SupplierInvoices($supplierInvoiceId);
            $supplierInvoices->setReconciliationFile($reconciliation_csv_file_name);
            $supplierInvoices->save();
        }
        $output['status'] == 'success';
        $output['file'] = $reconciliation_csv_file_url;
        $output['file_name'] = $reconciliation_csv_file_name;
        return $output;
    }

    public static function reconciliationBagCsv($returnArr, $carrierName, $dateFolder = "", $reconciliation_csv_file_name = "") {
        $output = [];
        if ($dateFolder == "") {
            $dateFolder = date('Y-m-d');
        }
        $rcsvHeading = [
            'Batch Number',
            'Bag Number',
            'Invoice Number',
            'Weight',
            'Matched Weight',
            'Number of pieces',
            'Matched Piece',
            'Status',
            'Message'
        ];
        $rCsvcomaSeptHeading = implode(",", $rcsvHeading);
        $rCsvCol = rtrim($rCsvcomaSeptHeading, ',');
        $rcsv = $rCsvCol;
        $rcsv .= "\r\n";

        $batchNumber = '';
        $status = $returnArr['status'];
        if (isset($returnArr['data_bag']) && $status == 'success') {
            $dataRec = $returnArr['data_bag'];
            $batchNumber = $returnArr['batch_number'];
            $invoiceNumber = $returnArr['invoice_number'];
            $reconciliationBagDataFilter = New ReconciliationBagDataFilter();
            $reconciliationBagDataFilter->addFieldFilter("      invoice_number", $invoiceNumber);
            $reconciliationBagDataObjs = $reconciliationBagDataFilter->getList('*');
            if (count($reconciliationBagDataObjs)) {
                foreach ($reconciliationBagDataObjs as $reconciliationBagDataObj) {
                    $reconciliationBagId = $reconciliationBagDataObj->getId();
                    $bagNumber = $reconciliationBagDataObj->getBagNumber();
                    $numberOfPieces = $reconciliationBagDataObj->getNumberOfPiece();
                    $bagWeight = $reconciliationBagDataObj->getWeight();
                    // Check if bag exsist
                    $baggingFilter = new BaggingFilter();
                    $baggingFilter->addFieldFilter("     bagnumber", $bagNumber);
                    $bagObj = $baggingFilter->getColumnList("id,weight");
                    $bagCount = count($bagObj);
                    $bagParcelCount = 0;
                    $bagDbWeight = 0.00;
                    if ($bagCount) {
                        $bagStatus = "success";
                        $bagParcelStatus = "success";
                        $bagWeightStatus = "match";
                        $bagStatusMessage = '';
                        $bagId = $bagObj[0]->getId();
                        $bagDbWeight = $bagObj[0]->getWeight();
                        // Check parcel from bag
                        $parcelBagingMapping = new ParcelBaggingMappingFilter();
                        $parcelBagingMapping->addFieldFilter("    bag_id", $bagId);
                        $parcelBagingMapping->addGroupBy("parcel_id");
                        $parcelBagingMappingObj = $parcelBagingMapping->getColumnList("*");
                        $bagParcelCount = count($parcelBagingMappingObj);
                        if ($bagParcelCount > $numberOfPieces) {
                            $bagParcelStatus = "error";
                            $bagStatus = "error";
                            $bagStatusMessage .= 'Bag and CSV parcel count doest not match. System parcel count is greater than CSV ';
                        } else if ($bagParcelCount < $numberOfPieces) {
                            $bagParcelStatus = "error";
                            $bagStatus = "error";
                            $bagStatusMessage .= 'Bag and CSV parcel count doest not match. System parcel count is less than CSV ';
                        }
                        if ($bagDbWeight > $bagWeight) {
                            $bagWeightStatus = "more";
                            $bagStatus = "error";
                            $bagStatusMessage .= 'Bag and CSV parcel weight doest not match. System parcel weight is greater than CSV ';
                        } else if ($bagDbWeight < $bagWeight) {
                            $bagWeightStatus = "less";
                            $bagStatus = "error";
                            $bagStatusMessage .= 'Bag and CSV parcel weight doest not match. System parcel weight is less than CSV ';
                        }
                    } else {
                        $bagStatus = "error";
                        $bagStatusMessage = 'Bag not found in system ';
                    }
                    if ($bagStatus == "error") {
                        $reconciliationBagData = new ReconciliationBagData($reconciliationBagId);
                        $reconciliationBagData->setMatchedPiece($bagParcelCount);
                        $reconciliationBagData->setMatchedWeight($bagDbWeight);
                        $reconciliationBagData->setStatus($bagStatus);
                        $reconciliationBagData->setMessage($bagStatusMessage);
                        $reconciliationBagData->setWeightStatus($bagWeightStatus);
                        $reconciliationBagData->setParcelStatus($bagParcelStatus);
                        $reconciliationBagData->save();
                    } else {
                        $reconciliationBagData = new ReconciliationBagData($reconciliationBagId);
                        $reconciliationBagData->setMatchedPiece($bagParcelCount);
                        $reconciliationBagData->setMatchedWeight($bagDbWeight);
                        $reconciliationBagData->setStatus($bagStatus);
                        $reconciliationBagData->setMessage($bagStatusMessage);
                        $reconciliationBagData->setWeightStatus($bagWeightStatus);
                        $reconciliationBagData->setParcelStatus($bagParcelStatus);
                        $reconciliationBagData->save();
                        $dt = [
                            $batchNumber,
                            $bagNumber,
                            $invoiceNumber,
                            $bagWeight,
                            $bagDbWeight,
                            $numberOfPieces,
                            $bagParcelCount,
                            $bagParcelStatus,
                            $bagStatusMessage
                        ];
                        $comaSept = implode(",", $dt);
                        $rcsv .= rtrim($comaSept, ',');
                        $rcsv .= "\r\n";
                    }
                }
            }
        }
        /* create new csv file and write data on it */
        if ($reconciliation_csv_file_name == "") {
            $reconciliation_csv_file_name = strtolower($carrierName) . "_reconciliation_bag_" . $batchNumber . ".csv";
        } else {
            $path = RECONCILIATION_PATH . $carrierName . '/' . $dateFolder . '/' . $reconciliation_csv_file_name;
            unlink($path);
        }
        $reconciliation_csv_file_created = RECONCILIATION_PATH . $carrierName . '/' . $dateFolder . '/' . $reconciliation_csv_file_name;
        $exportCsvFile = fopen($reconciliation_csv_file_created, "a") or die("Unable to open file!");
        fwrite($exportCsvFile, $rcsv);
        fclose($exportCsvFile);

        $reconciliation_csv_file_url = RECONCILIATION_URL . $carrierName . '/' . $dateFolder . '/' . $reconciliation_csv_file_name;
        $output['status'] == 'success';
        $output['file'] = $reconciliation_csv_file_url;
        $output['file_name'] = $reconciliation_csv_file_name;
        return $output;
    }

    public static function invoiceCheckCsv($returnArr, $carrierName, $dateFolder = "", $invoice_check_csv_file_name = "", $carrierClass = "") {
        
        //$user = SessionManager::getUser();
        
        $output = [];
        if ($dateFolder == "") {
            $dateFolder = date('Y-m-d');
        }
        $pricingType = 'purchase_invoice';
        /* pricing column export */
        $column_export_data = ConsignmentCharges::getPricingColoumn($pricingType);

        $resExportHeader = ["status"];
        $resExportExtra = ["batch_number"];
        $resExportSystem = [];
        foreach ($column_export_data as $key => $columnname) {
            $resExportSystem[] = $columnname;
            if ($key > 2) {
                $resExportSystem[] = "System " . $columnname;
            }
        }
        $resExportHeader = array_merge($resExportHeader, $resExportSystem, $resExportExtra);
        $comaSeptHeading = implode(",", $resExportHeader);
        $col = rtrim($comaSeptHeading, ',');
        $csvExport = $col;
        $csvExport .= "\r\n";
    
        $status = $returnArr['status'];
        $batchNumber = '';
        $supplierInvoiceId = '';
        $csvWeightTotal = "";
        $csvNumberOfPiecesTotal = "";
        $csvWeightAmountTotal = "";
        $invoiceProcessStatus = "success";
        $output['all_success'] = 1;
        
        
        if ($status == 'success') {
            $totalProcessedWeight = 0.000;
            $totalProcessedPieces = 0;
            $totalProcessedAmount = 0.00;
            $data = $returnArr['data'];
            $batchNumber = $returnArr['batch_number'];
            $template = $returnArr['template'];
            $invoiceNumber = $returnArr['invoice_number'];
            $supplierInvoiceId = $returnArr['supplier_invoice_id'];
           
            if (count($data)) {
                foreach ($data as $key => $obj) {
                    $consignmentId = '';
                    $msgStatus = '';
                    $msgCode = '';
                    $processStatus = 'error';
                    $data[$key]->charges_reference = $invoiceNumber;
                    $data[$key]->tracking_number = $obj->awb;

                    $returnServiceCheck = $obj->service_name;

                    $relabelConsigmentId = 0;
                    $relabelConsigmentCheck = false;
                    
                    ConsignmentRelabelIdFOrInvoiceCheck:
                        
                    $consignmentFilter = new ConsignmentFilter();
                    $consignmentFilter->addJoin('parcel p', 'c.id = p.consignment_id', 'inner');
                    /* if($obj->hawb != "") {
                      $consignmentFilter->addFieldFilter('     c.hawb', $obj->hawb);
                      } */
                    if (trim(strtolower($returnServiceCheck)) == 'return to sender')
                        $consignmentFilter->addFieldFilter('     c.consignment_type', 'return');
                    else
                        $consignmentFilter->addFieldFilter('     c.consignment_type', 'outbound');
                    
                    
                    if(!$relabelConsigmentCheck)
                        $consignmentFilter->addFieldFilter('     p.tracking_number', $obj->awb);
                    else
                        $consignmentFilter->addFieldFilter('     c.id', $relabelConsigmentId);
                    //$consignmentFilter->addFilter("      (  = '". $obj->awb."' )", 'filter');
                  
                    $consignmentFilterObjQ = $consignmentFilter->getListNew('c.*');
                  
                    if (count($consignmentFilterObjQ)<=0 && $relabelConsigmentCheck === false){
                        /*************** Check for Relabels *************************/
                        $consignmentRelabelFilter = new ConsignmentRelabelFilter();
                        $consignmentRelabelFilter->addFilter("       (c.old_tracking_no = '".$obj->awb."' OR c.new_tracking_no = '".$obj->awb."') "
                                . " and consignment_id not in (select id from consignment where shipment_status = 22)" );
                        $consignmentRelabelData =  $consignmentRelabelFilter->getColumnList(" consignment_id ");
                        if(count($consignmentRelabelData)>0){
                            $consignmentRelabelItem = $consignmentRelabelData[0];
                            $relabelConsigmentId = $consignmentRelabelItem->getConsignmentId();
                            $relabelConsigmentCheck = true;
                            goto ConsignmentRelabelIdFOrInvoiceCheck;
                        }
                    }
                    $csvWeight = $obj->weight;
                    $csvWeightTotal += $csvWeight;
                    $csvVolWeight = $obj->vol_weight;
                    $csvNumberOfPieces = $obj->number_of_pieces;
                    $csvNumberOfPiecesTotal += $csvNumberOfPieces;
                    $totalAmount = $obj->total_amount;
                    $csvWeightAmountTotal += $totalAmount;
                    $labelClassName = "";
                    
                    if ($obj->status != 'success') {
                        if (count($consignmentFilterObjQ)>0) {
                           
                            if (count($consignmentFilterObjQ) == 1) {
                                $consignmentFilterObj = $consignmentFilterObjQ[0]; //new consignment($parcelFilterObj[0]->getConsignmentId());
                                $consignmentId = $consignmentFilterObj->getId();
                                $csvServiceCode = $obj->service_code;
                                $csvSurchargeServiceCode = $obj->surcharge_service_code;
                                $csvCheckServiceWeight = $obj->check_service_weight;
                                $csvCheckServiceVolWeight = $obj->check_service_vol_weight;
                                $serviceObj = new Services($consignmentFilterObj->getServiceId());
                                $oweServiceCode = $serviceObj->getCarrierServiceCode();
                                $serviceCode = $serviceObj->getCode();
                                $getEstimateCost = 0.00;
                                $getChargeCost = 0.00;
                             // echo   $userAccountId = $returnArr['supplier_invoice']->getAccountId(); //$user->getUserAccountId();
                                
                                $userAccountId = Consignment::getConsignmentUserAccountIdForPricing($consignmentId);
                              
                                $originCountry = $consignmentFilterObj->getSenderCountryId();
                                $destinationCountry = $consignmentFilterObj->getCountryId();

                                $customerConsignmentCharges = ConsignmentCharges::getConsignmentTotalCharges($consignmentId, $userAccountId);
                                $customerTotal = $customerConsignmentCharges['cost'];
                                $agentConsignmentCharges = ConsignmentCharges::getConsignmentTotalCharges($consignmentId, $userAccountId, 'agent');
                                $agentTotal = $agentConsignmentCharges['cost'];
                                

                                // $supplierTariffCsv = Tariffs::getUserQuotationsByAssignedServices($userAccountId,$originCountry,$destinationCountry,'','','','',$csvWeight,$csvNumberOfPieces,$consignmentFilterObj->getServiceId());
                                //$supplierTariffOwe = Tariffs::getUserQuotationsByAssignedServices($userAccountId,$originCountry,$destinationCountry,'','','','',$consignmentFilterObj->getChargeWeight(),$csvNumberOfPieces,$consignmentFilterObj->getServiceId());
//                            if($supplierTariffOwe['STATUS'] == 'SUCCESS') {
//                                $getEstimateCost = isset($supplierTariffOwe['QUOTATIONS'][0]['TOTAL']) ? $supplierTariffOwe['QUOTATIONS'][0]['TOTAL'] : 0.00;
//                            }
//                            if($supplierTariffCsv['STATUS'] == 'SUCCESS') {
//                                $getChargeCost = isset($supplierTariffCsv['QUOTATIONS'][0]['TOTAL']) ? $supplierTariffCsv['QUOTATIONS'][0]['TOTAL'] : 0.00;
//                            }
//                            if($carrierName == "Royal Mail"){
                                // parcel or bags status set
                                // If Royal Mail then check if any parcel belongs to bag have status error then make bags status erros
//                            }else{
                                
      /*                           echo "<pre>";
echo $agentTotal." <= 0 || ".$customerTotal." <= 0 || ".$agentTotal." >= ".$customerTotal ;   
echo "<br>";
echo count($agentConsignmentCharges) ;
print_r($agentConsignmentCharges);
echo $status ;
die;*/
                                if($agentTotal <= 0 || $customerTotal <= 0 || $agentTotal >= $customerTotal){
                                    $msgStatus = 'Please check agent/customer charges against Order reference (' . $obj->hawb . ') ';
                                    $msgCode = 'charges_issue';
                                    
                                } else {   
                                    if ($csvServiceCode == $oweServiceCode || $csvServiceCode == $serviceCode || trim(strtolower($returnServiceCheck)) == 'return to sender' ||  $relabelConsigmentCheck) {
                                        $oweWeight = $consignmentFilterObj->getChargeWeight();
                                        $serviceFromWeight = $serviceObj->getFromWeight();
                                        $serviceToWeight = $serviceObj->getToWeight();
                                        $serviceMaxVolumetricWeight = $serviceObj->getMaxVolumetricWeight();

                                        if (
                                                $csvWeight <= $oweWeight ||
                                                ($customerTotal <= $agentTotal) ||
                                                trim($csvSurchargeServiceCode) != '' ||
                                                ($csvCheckServiceWeight == 1 && $serviceFromWeight <= $csvWeight && $serviceToWeight >= $csvWeight)
                                        ) {
                                            $oweVolWeight = $consignmentFilterObj->getChargeWeight(); // $consignmentFilterObj[0]->getVolWeight();
                                            if ($csvVolWeight <= $oweVolWeight || ($customerTotal <= $agentTotal) || trim($csvSurchargeServiceCode) != '' || $csvCheckServiceVolWeight == 0 || $serviceMaxVolumetricWeight >= $csvVolWeight) {
                                                $oweNumberOfPieces = $consignmentFilterObj->getNumberPieces();
                                                if (        $csvNumberOfPieces <= $oweNumberOfPieces 
                                                        ||  trim($csvSurchargeServiceCode) != '' 
                                                        ||  trim(strtolower($returnServiceCheck)) == 'return to sender' 

                                                    ) {
                                                    $processStatus = 'success';
                                                    if ($template == 2) {

                                                        if (in_array(trim($csvSurchargeServiceCode), array('OGW1', 'OGW2', 'OGW3')))
                                                            $data[$key]->out_of_gauge_weight = $obj->total_amount;
                                                        else if (in_array(trim($csvSurchargeServiceCode), array('OGL1', 'OGL2', 'OGL3')))
                                                            $data[$key]->out_of_gauge_length = $obj->total_amount;
                                                        else if (in_array(trim($csvSurchargeServiceCode), array('OGV1', 'OGV2', 'OGV3')))
                                                            $data[$key]->out_of_gauge_volume = $obj->total_amount;
                                                        else if (in_array(trim($csvSurchargeServiceCode), array('PAN', 'PAN', 'PAN')))
                                                            $data[$key]->late_or_missing_pan = $obj->total_amount;
                                                    }
                                                    $result = ConsignmentCharges::csvUpdateChages($obj, $pricingType, true);
                                                    if ($result['status']) {
                                                        $msgStatus .= $result['MESSAGE'];
                                                        $totalProcessedWeight += $csvWeight;
                                                        $totalProcessedPieces += $csvNumberOfPieces;
                                                        $totalProcessedAmount += $data[$key]->total_amount;
                                                    } else {
                                                        $processStatus = 'error';
                                                        $msgStatus = "Price Update: " . $result['MESSAGE'];
                                                        $msgCode = 'not_found';
                                                    }
                                                } else {
                                                    $msgStatus = 'Number of pieces (' . $csvNumberOfPieces . ') not match';
                                                    $msgCode = 'item_issue';
                                                }
                                            } else {
                                                $msgStatus = 'Vol Weight (Supplier ' . $csvVolWeight . 'Kgs Customer Charge Weight ' . $oweVolWeight . 'Kgs) not match';
                                                $msgCode = 'weight_issue';
                                            }
                                        } else {
                                            $msgStatus = 'Weight (Supplier ' . $csvWeight . 'Kgs Customer Charge Weight ' . $oweWeight . 'Kgs) not match';
                                            $msgCode = 'weight_issue';
                                        }
                                    } else {
                                        $msgStatus = 'Service Code (Supplier ' . $csvServiceCode . ' - Portal '.$oweServiceCode.') not match';
                                        $msgCode = 'service_issue';
                                    }
                                }
//                            }
                            } else {
                                $msgStatus = 'Multiple consignment exist against Order reference (' . $obj->hawb . ')';
                                $msgCode = 'duplicate_shipment';
                            }
                        } 
                        else {
                        
                            $checkAwbNumber = $result = substr($obj->awb, 0, 12);
                            if ($checkAwbNumber != 'JD0002210164' && strtolower($carrierClass) == "yodel") {
                                $msgStatus = 'Please find AWB (' . $obj->awb . ') in old system';
                                $msgCode = 'not_found';
                            } else {
                                $msgStatus = 'AWB (' . $obj->awb . ') and Order reference (' . $obj->hawb . ') not found';
                                $msgCode = 'not_found';
                            }
                        }
                        if ($processStatus == 'error') {
                            
                            if ($obj->action != "" && $obj->action != "query_with_supplier") {
                                $processStatus = "success";
                                $msgStatus = ucwords(str_replace('_', ' ', $obj->action));
                                if ($template == 2) {
                                    $data[$key]->additional_charges = $obj->total_amount;
                                    ;
                                }
                                ConsignmentCharges::csvUpdateChages($obj, $pricingType);
                                $totalProcessedWeight += $csvWeight;
                                $totalProcessedPieces += $csvNumberOfPieces;
                                $totalProcessedAmount += $data[$key]->total_amount;
                            }
                        }


                        /* update status in table */
                        $reconciliationDataFilter = new ReconciliationDataFilter();
                        $reconciliationDataFilter->set(['message' => $msgStatus]);
                        $reconciliationDataFilter->set(['message_code' => $msgCode]);
                        $reconciliationDataFilter->set(['status' => $processStatus]);

                        if ($processStatus == "success") {
                            $reconciliationDataFilter->set(['action' => 'approved']);
//                        $totalProcessedWeight += $csvWeight;
//                        $totalProcessedPieces += $csvNumberOfPieces;
//                        $totalProcessedAmount += $data[$key]->total_amount;
                        }
                        if ($processStatus == "error") {
                            $output['all_success'] = 0;
                            $invoiceProcessStatus = "processed";
                        }
                        $reconciliationDataFilter->where(['awb' => $obj->awb, 'hawb' => $obj->hawb, 'invoice_number' => $obj->invoice_number]);
                        $reconciliationDataFilter->update();
                    }
                    /* update status in table */
                    $csvExport .= $msgStatus . ",";
                    $csvExport .= $obj->hawb . ",";
                    $csvExport .= $obj->awb . ",";
                    //$csvExport .= $obj->bag_number.",";
                    $csvExport .= $obj->invoice_number . ",";
                    foreach ($resExportHeader as $k => $col) {
                        if ($k > 3 && (($k + 1) < count($resExportHeader))) {
                            $chargeCol = str_replace([' ', '/'], '_', strtolower($col));
                            $chargeKey = str_replace([' ', '/'], '_', strtoupper($col));
                            $checkEmptyCol = 0;
                            if (isset($obj->$chargeCol) && $obj->$chargeCol > 0) {
                                $csvExport .= $obj->$chargeCol . ",";
                                $checkEmptyCol = 1;
                            }
                            if ($consignmentId > 0) {
                                $userAccountId = Consignment::getConsignmentUserAccountIdForPricing($consignmentId);
                                $consignmentChargesTypeFilter = new ConsignmentChargesTypesFilter();
                                $consignmentChargesTypeFilter->addFieldFilter('     cct.charges_key', $chargeKey);
                                $consignmentChargesTypeFilterObj = $consignmentChargesTypeFilter->getList();
                                if (count($consignmentChargesTypeFilterObj)) {
                                    $chargeId = $consignmentChargesTypeFilterObj[0]->getId();
                                    $consignmentChargesFilter = new ConsignmentChargesFilter();
                                    $consignmentChargesFilter->addFieldFilter('     cc.account_id', $userAccountId);
                                    $consignmentChargesFilter->addFieldFilter('     cc.consignment_id', $consignmentId);
                                    $consignmentChargesFilter->addFieldFilter('     cc.charge_type_id', $chargeId);
                                    $consignmentChargesFilter->addFieldFilter('     cc.cost_type', 'purchase_invoice');
                                    $consignmentChargesFilterObj = $consignmentChargesFilter->getList();
                                    if ($checkEmptyCol == 0) {
                                        $csvExport .= ",";
                                    }
                                    if (count($consignmentChargesFilterObj)) {
                                        $csvExport .= $consignmentChargesFilterObj[0]->getCost() . ",";
                                    } else {
                                        $csvExport .= ",";
                                    }
                                }
                            } else {
                                if ($checkEmptyCol == 0) {
                                    $csvExport .= ",";
                                }
                            }
                        }
                    }
                    $csvExport .= $obj->batch_number . ",";
                    $csvExport .= "\r\n";
                }
                $output['status'] = 'success';
                $output['total_processed_weight'] = $totalProcessedWeight;
                $output['total_processed_pieces'] = $totalProcessedPieces;
                $output['total_processed_amount'] = $totalProcessedAmount;
            }
            else {
                $output['status'] = 'fail';
                $output['message'] = "Reconcile data not found.";
            }
        } else {
            $output['status'] = 'fail';
            $output['message'] = $returnArr['message'];
        }
        if ($output['status'] == 'success') {
            /* create new csv file and write data on it */
            if ($invoice_check_csv_file_name == "") {
                $invoice_check_csv_file_name = strtolower($carrierName) . "_invoice_check_" . $batchNumber . ".csv";
            } else {
                $path = RECONCILIATION_PATH . $carrierName . '/' . $dateFolder . '/' . $invoice_check_csv_file_name;
                unlink($path);
            }
            $invoice_check_csv_file_created = RECONCILIATION_PATH . $carrierName . '/' . $dateFolder . '/' . $invoice_check_csv_file_name;
            $exportCsvFile = fopen($invoice_check_csv_file_created, "a") or die("Unable to open file!");
            fwrite($exportCsvFile, $csvExport);
            fclose($exportCsvFile);

            $invoice_check_csv_file_url = RECONCILIATION_URL . $carrierName . '/' . $dateFolder . '/' . $invoice_check_csv_file_name;
            /* save file name in db */
            if ($supplierInvoiceId != "") {

                $load_data_sql = "UPDATE 
                    supplier_invoices 
                    SET 
                  total_weight = (select sum(weight) from reconciliation_data where supplier_invoice_id = supplier_invoices.id),
                  total_pieces =  (select sum(number_of_pieces) from reconciliation_data where supplier_invoice_id = supplier_invoices.id),
                  total_amount =  (select sum(total_amount) from reconciliation_data where supplier_invoice_id = supplier_invoices.id),
                  total_processed_weight = (select sum(weight) from reconciliation_data where supplier_invoice_id = supplier_invoices.id and action = 'approved'),
                  total_processed_pieces =  (select sum(number_of_pieces) from reconciliation_data where supplier_invoice_id = supplier_invoices.id and action = 'approved'),
                  total_processed_amount =  (select sum(total_amount) from reconciliation_data where supplier_invoice_id = supplier_invoices.id and action = 'approved'),
                  invoice_check_file = '" . $invoice_check_csv_file_name . "',
                  status = '" . $invoiceProcessStatus . "'
                  WHERE supplier_invoices.id > 0  and supplier_invoices.id = '" . $supplierInvoiceId . "'";

                DbAccess3::runQueryWithError($load_data_sql);

                /* $supplierInvoices = new SupplierInvoices($supplierInvoiceId);
                  $supplierInvoices->setTotalWeight($csvWeightTotal);
                  $supplierInvoices->setTotalPieces($csvNumberOfPiecesTotal);
                  $supplierInvoices->setTotalAmount($csvWeightAmountTotal);
                  $supplierInvoices->setTotalProcessedWeight($totalProcessedWeight);
                  $supplierInvoices->settotalProcessedPieces($totalProcessedPieces);
                  $supplierInvoices->settotalProcessedAmount($totalProcessedAmount);
                  $supplierInvoices->setStatus($invoiceProcessStatus);
                  $supplierInvoices->setInvoiceCheckFile($invoice_check_csv_file_name);
                  $supplierInvoices->save(); */
            }
            $output['file'] = $invoice_check_csv_file_url;
            $output['file_name'] = $invoice_check_csv_file_name;
        }
        return $output;
    }

    public static function reprocessFile($id) {
        $passData = [];
        $output = [];

        $supplierInvoice = new SupplierInvoices($id);
        $invoiceNumber = $supplierInvoice->getInvoiceNumber();
        $invoiceAccount = $supplierInvoice->getAccountId();
        
        $accountObj = new CustomerAccount();
        $searchConsignmentAccountId = $invoiceAccount;
        $subAccountArr = $accountObj->getSubAccountsArrayShowConsignmentAccount($searchConsignmentAccountId,null);
//        echo "<pre>";
//        print_r($subAccountArr);
//        die;


        $sql = "SELECT * FROM reconciliation_data WHERE supplier_invoice_id='" . $id . "' ";
        
        $resultSql = DbAccess3::runQuery($sql);
        $res = [];
        while ($obj = mysqli_fetch_object($resultSql)) {
            $res[] = $obj;
        }
        
        $dateFolder = $supplierInvoice->getDate();
        $carrierObj = new Carrier($supplierInvoice->getCarrierId());
        $carrierName = str_replace(" ", "_", $carrierObj->getCarrier());
        $filePath = RECONCILIATION_PATH . $carrierName . '/' . $dateFolder;
        $batchNumber = '';
        $invoiceNumber = '';
        $supplierInvoiceId = '';
        if (count($res)) {
            $batchNumber = $res[0]->batch_number;
            $invoiceNumber = $res[0]->invoice_number;
            $supplierInvoiceId = $id;
        }
        $templateCheck = $supplierInvoice->getTemplate();
        $passData['status'] = 'success';
        $passData['file_path'] = $filePath;
        $passData['batch_number'] = $batchNumber;
        $passData['invoice_number'] = $invoiceNumber;
        $passData['supplier_invoice_id'] = $supplierInvoiceId;
        $passData['template'] = $templateCheck;

        $passData['supplier_invoice'] = $supplierInvoice;
        $passData['data'] = $res;
        $invoice_check_csv_file_name = $supplierInvoice->getInvoiceCheckFile();
        $outputInvoiceCsv = SupplierInvoices::invoiceCheckCsv($passData, $carrierName, $dateFolder, $invoice_check_csv_file_name);
        /*echo "<pre>";
        print_r($outputInvoiceCsv);
        die;*/
        $invoiceStatusFllag = true;
        if ($outputInvoiceCsv['status'] == 'success') {
            $reconciliation_csv_file_name = $supplierInvoice->getReconciliationFile();
            $outputReconciliationCsv = SupplierInvoices::reconciliationCsv($passData, $carrierName, $dateFolder, $reconciliation_csv_file_name,$subAccountArr);
            $totalProcessedWeight = $outputInvoiceCsv['total_processed_weight'];
            $totalProcessedPieces = $outputInvoiceCsv['total_processed_pieces'];
            $totalProcessedAmount = $outputInvoiceCsv['total_processed_amount'];
           
            if ($outputInvoiceCsv['all_success'] == 1) {
                $supplierInvoicesUpdate = new SupplierInvoices($id);
                $supplierInvoicesUpdate->setStatus('approved');
                //$supplierInvoicesUpdate->setTotalProcessedWeight($totalProcessedWeight);
                //$supplierInvoicesUpdate->setTotalProcessedPieces($totalProcessedPieces);
                //$supplierInvoicesUpdate->setTotalProcessedAmount($totalProcessedAmount);
                $supplierInvoicesUpdate->save();
                $invoiceStatusFllag = false;
            } else {
                $supplierInvoicesUpdate = new SupplierInvoices($id);
                $supplierInvoicesUpdate->setStatus('processed');
                $supplierInvoicesUpdate->save();
                $invoiceStatusFllag = false;
            }
            $output['status'] = 'success';
            $output['message'] = 'Re processed file successfully';
        }
        if (!isset($output['status']) || $output['status'] != 'success') {
            if($invoiceStatusFllag){
                $supplierInvoicesUpdate = new SupplierInvoices($id);
                $supplierInvoicesUpdate->setStatus('processed');
                $supplierInvoicesUpdate->save();
            }
            $output['status'] = 'error';
            $output['message'] = 'File not reprocessed please contact to support';
        }
        return $output;
    }

}

?>