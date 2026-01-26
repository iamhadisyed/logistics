<?php

class Viva implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $constants = null;
    private $user = null;
    private $country = null;
    private $trackingServiceId = null;
    private $trackingAgentId = null;


    public function __construct() {

    }

    public function validation(Consignment $consignment, Services $service, Country $country) {

    }

    public function remoteareas($consignment, $carrierObject, $sender) {

    }

    public function label($consignment, $labelType = 'pdf', $size = '100x150') {

        $output = array();
        $this->user = SessionManager::getUser();

        $this->serviceValues = new Services($consignment->getServiceId());
        $this->country = new Country($consignment->getCountryId());

        /*
         * Service COnstant
         */
        $serviceAgentConstantFilter = new ServiceConstantValueFilter();
        $serviceAgentConstantFilter->addFilter("service_id = '" . $consignment->getServiceId() . "' AND agent_id = '" . $consignment->getAgentId() . "' ");
        $serviceAgentConstant = $serviceAgentConstantFilter->getConstantList("c.constant 'constant_name', scv.constant_value 'constant_value'");
        if (count($serviceAgentConstant) > 0) {
            foreach ($serviceAgentConstant as $serviceAgentConstantData) {
                $this->constants[$serviceAgentConstantData->getConstantName()] = $serviceAgentConstantData->getConstantValue();
            }
        }
        if (trim(@$this->constants['VIVA_SHIPPER_POSTCODE']) == '' || trim(@$this->constants['VIVA_SHIPPER_COUNTRY']) == '' || trim(@$this->constants['VIVA_SHIPPER_ADDRESS_LINE1']) == '') {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }


        /*
         *  Get Tracking Number ranges
         */

        $serviceRangeMappingFilter = new ServiceRangeMappingFilter();
        $serviceRangeMappingFilter->addFilter("service_id = '" . $consignment->getServiceId() . "' AND agent_id = '" . $consignment->getAgentId() . "' ");
        $serviceRange = $serviceRangeMappingFilter->getList(" licence_plate_id ");
        if (count($serviceRange) > 0) {
            $licence_plate_id = (int) $serviceRange[0]->getLicencePlateId();
        }
        if ($licence_plate_id < 0) {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "This service does not have tracking number range. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }
        $licencePlate = new LicencePlate($licence_plate_id);


        $parcel_list = $consignment->getParcels();
        $parcel_count = sizeof($parcel_list);
        $parcel_idx = 0;
        $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf = $pdf;
        // Generate label for each parecel
        foreach ($parcel_list as $parcel) {
            if ($parcel->getTrackingNumber() == '') {
                $resultArray = LicencePlate::getLicencePlateNumber($licence_plate_id);

                if (trim($resultArray['STATUS']) == 'ERROR')
                    return $resultArray;
                else {
                    $licence_plate = $resultArray["PREFIX"] . $resultArray["RANGE"] . $resultArray["SUFIX"];
                }
                $parcel->setTrackingNumber($licence_plate);
                $parcel->save();
            } else {
                $licence_plate = $parcel->getTrackingNumber();
            }
            $licence_plate_array[$parcel_idx] = $licence_plate;

            $this->pdf->SetPrintFooter(false);
            $this->pdf->SetFooterMargin(0);
            $this->pdf->SetAutoPageBreak(false, 0);
            $page_size = array(100, 150);
            $this->pdf->AddPage("P", $page_size);
            $this->addWayBill($consignment, $parcel_idx, $licence_plate);
            ++$parcel_idx;
        }

        $this->pdf->IncludeJS("print();");
        $this->pdf->Output("../_assets/pdf/" . date('Y_m_d') . '/' . $consignment->getId() . ".pdf", "F");
        $output['STATUS'] = 'SUCCESS';
        $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
        $output['TRACKING_NUMBER'] = $licence_plate_array;
        return $output;
    }

   public function tracking($trackingNumber, $trackBy = 'parcel', $EDI = false)
    {
        $tracking = new Tracking();
        $deliveredArray = array( "00", "21", "22");

        if($EDI == true && !empty($this->trackingServiceId) && !empty($this->trackingAgentId))
        {
            include_once(BASE_PATH."includes/labels/vivatrackingstatus.class.php");
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

            $ftp_server = $this->constants['VIVA_TRACKING_URL'];
            $ftp_user = $this->constants['VIVA_TRACKING_USERNAME'];
            $ftp_pass = $this->constants['VIVA_TRACKING_PASSWORD'];
            $ftp_local_path = SETTING_DIR_ASSETS . "tracking_data/VIVA/";

            if (!file_exists($ftp_local_path))
                @mkdir($ftp_local_path, 0777, true);

            $conn_id = ftp_connect($ftp_server);

            if(!$conn_id)
            {
                echo "FTP connection failed";
                exit;
            }

            else
            {
                if (@ftp_login($conn_id, $ftp_user, $ftp_pass))
                {
                    $dir_files = array();
                    $dir = "/ShipmentsOut";
                    ftp_pasv($conn_id, true);
                    $arrfile =  ftp_nlist($conn_id, $dir);

                    if($arrfile === false)
                    {
                        echo "Unable List FTP Files.";
                        exit;
                    }

                    foreach($arrfile as $fileName)
                    {
                        $filename = explode("/", $fileName);
                        $filename = $filename[2];
                        $date = date('Ymd');

                        if(strpos($filename,$date) === false)
                        {
                           // continue;
                        }
                        else
                        {
                            $fp = fopen($ftp_local_path . $filename, 'w');

                            if (ftp_fget($conn_id, $fp, $fileName,  FTP_ASCII, FTP_AUTORESUME))
                            {
                                $handle = fopen($ftp_local_path . $filename, "r");
                                $trackingArray = simplexml_load_file($ftp_local_path .$filename);
                                if(ftp_rename($conn_id, '/ShipmentsOut/'.$filename, '/processed/'.$filename))

                                if (count($trackingArray) > 0)
                                {
                                    

                                    foreach($trackingArray->SSR as $event)
                                    {
                                        $awb      = $event->HAWB;
                                        $consignmentFilter = new ConsignmentFilter();
                                        $consignmentFilter->addAwbAndHawbOrFilter($awb);
                                        $con_list = $consignmentFilter->getConList();
                                        $trackingNumber = $awb;
                                        
                                        ////////////////////// Carrier Received ////////////////////////////////

                                        $trackingDataFilterObj = new TrackingDataFilter();
                                        $trackingDataFilterObj->addFieldFilter('    tracking_number', $trackingNumber);
                                        $trackingDataFilterObj->addFilter("carrier_code not in ('')");
                                        $trackingEvents = $trackingDataFilterObj->getList();

                                        if(count($trackingEvents) > 0)
                                        {
                                            $carrierReceivedCheck = 0;   // there is already carrier received event
                                            $trackingDataFilterObj = new TrackingDataFilter();
                                            $trackingDataFilterObj->addFieldFilter('    tracking_number', $trackingNumber);
                                            $trackingDataFilterObj->addFilter("status_code_id = '148'");
                                            $CarrierReceivedObj = $trackingDataFilterObj->getList();

                                            if(count($CarrierReceivedObj) >0 )
                                            {
                                                $carrierCodeCarrierReceived = $CarrierReceivedObj[0]->getCarrierCode();
                                                $carrierReceivedStatusCode = $CarrierReceivedObj[0]->getStatusCodeId();
                                            }
                                        }
                                        else
                                        {
                                            $carrierReceivedCheck = 1; // No carrier received Event
                                        }
                                        ////////////////////// Carrier Received ////////////////////////////////
                                        
                                        if(count($con_list) > 0)
                                        {
                                            $trackingNo = $con_list[0]->getAwb();
                                            $dateTime = date("Y-m-d G:i:s", strtotime($event->Event_Date . " " . $event->Event_Time));
                                            $EventCode = trim($event->SSR_CODE);
                                            $EventDescription   = VivaTrackingStatus::$viva_status_code[$EventCode];

                                            // Dont enter any other event code if it is against 148 Event Code
                                            if($carrierCodeCarrierReceived == $EventCode)
                                            {
                                                continue;
                                            }
                                            $spTrackingStatus = VivaTrackingStatus::getOweStatusCode($EventCode);

                                            $trackPoint = trim($event->Subject);
                                            if($trackPoint != '')
                                            {
                                                $EventDescription = $EventDescription.' - '.$trackPoint;
                                            }

                                            /*if(strtolower($EventDescription) == "delivered")
                                            {
                                                $trackPoint = $EventDescription;
                                            }*/
                                            //echo $trackPoint; continue;
                                            $entityId = 0;
                                            $parcelObj = new ParcelFilter();
                                            $parcelObj->addTrackingNumberFilter($trackingNo);
                                            $parcelDataArray = $parcelObj->getColumnList('p.id, p.tracking_number, p.consignment_id');
                                            if (count($parcelDataArray) > 0) {
                                                $parcelData = $parcelDataArray[0];
                                                $entityId = $parcelData->getId();
                                            }

                                            if($entityId >0)
                                            {
                                                $parcelEntity = new Parcel($entityId);
                                                $finalStatusCode = $parcelEntity->getParcelStatusCode();
                                                ////////////////////// Carrier Received ////////////////////////////////
                                                if($carrierReceivedCheck == 1)
                                                {
                                                    $spTrackingStatus = '148';
                                                    $carrierReceivedCheck = 0 ;
                                                }
                                                else
                                                {
                                                    $spTrackingStatus = VivaTrackingStatus::getOweStatusCode($EventCode);
                                                }
                                                ////////////////////// Carrier Received ////////////////////////////////
                                                $tracking->saveTrackPoint($entityId, $trackBy, $dateTime, $trackingNo, $spTrackingStatus, $ServiceAreaDescription, $EventCode, $EventDescription, $deliveredArray, $finalStatusCode);
                                                $tracking->saveConsignmentTrackingStatus($trackingNo,'VivaTrackingStatus');
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function sendData($tracking_numbers = array()) {

    }

    public function manifest($consignment) {

    }

    public function preAdvice($consignment) {

    }

    private function addWayBill(Consignment $consignment, $parcel_idx, $licence_plate) {

        $this->pdf->line(3, 48, 98, 48);
        $this->pdf->line(3, 3, 98, 3);
        $this->pdf->line(98, 3, 98, 23.8);
        $this->pdf->line(3, 3, 3, 30);
        $this->pdf->line(41, 3, 41, 23.8);
        $this->pdf->line(3, 24, 98, 24);
        $this->pdf->line(3, 24, 3, 115);
        $this->pdf->line(3, 115, 98, 115);
        $this->pdf->line(98, 24, 98, 115);
        $this->pdf->line(3, 70, 98, 70);
        $this->pdf->line(3, 70, 3, 145);
        $this->pdf->line(98, 70, 98, 145);
        $this->pdf->line(3, 133, 98, 133);
        $this->pdf->line(3, 113, 3, 146);
        $this->pdf->line(3, 146, 98, 146);
        $this->pdf->line(98, 113, 98, 146);
        $this->pdf->line(30, 133, 30, 146);


       $userImage =    User::getUserCompanyImages(false,$consignment->getUserId());

        if ($userImage == '') {
            $userImage = realpath("../images/viva.png");
        }
            $image = $userImage;

            $y = 5;
            $x = 50;
            $w = 40;
            $h = 35;
            $this->pdf->image($image, $x, $y, $w, $h, '', '', '', false, 700, '', false, false, 0, '', false, false);

        if ($this->serviceValues->getIsUntrack() == 1)
            $productName = "UN-TRACKED";
        else
            $productName = "VIVA";


        $this->pdf->setFont("helvetica", "b", 11);
        // $this->pdf->Text(27, 5 , "48" );
        $handling = $this->serviceValues->getCode();
        $this->pdf->MultiCell(35, 20, $this->serviceValues->getName(), $border=0, $align='J', $fill=0, $ln=1, 5, 5, $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);

        /*if ($handling == "STVIVAEXP") {
            $this->pdf->Text(10, 10, $productName);
            $this->pdf->Text(8.5, 15, "RAPID");
        } else if ($handling == "VIVAB2C") {
            $y = 3.5;
            $x = 10;
            $w = 45;
            $h = 20;

            $VIVAB2CImage = realpath("../images/viva-b2c.png");
            $this->pdf->image($VIVAB2CImage, $x, $y, $w, $h, 'PNG', '', '', false, 700, '', false, false, 0, '', false, false);
        }
        */

        $this->pdf->setFont("helvetica", "b", 10);
        $this->pdf->Text(5, 59, "Desc:");
		$this->pdf->Text(5, 54, "Notes:");
        $this->pdf->setFont("helvetica", "", 9);
        $this->pdf->Text(18, 59, ucfirst(strtolower($consignment->getDescription())));
		$this->pdf->Text(18, 54, ucfirst(strtolower($consignment->getNotes())));
        $this->pdf->setFont("helvetica", "b", 10);
        $this->pdf->Text(5, 64, "Pieces : " . ($parcel_idx + 1) . "/" . $consignment->getNumberPieces());
        $this->pdf->Text(35, 64, "Weight : " . $consignment->getWeight());
        $this->pdf->Text(50, 59, "Value : " . $consignment->getValue() . " " . $consignment->getCurrency());


        $this->pdf->setFont("helvetica", "b", 13);
        $this->pdf->Text(30, 72, "Delivery Details:");
        $this->pdf->Text(11, 136.5, $this->country->getIso());

        $style = array(
            'position' => '',
            'align' => 'C',
            'stretch' => true,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => false,
            'hpadding' => 'auto',
            'vpadding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255),
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 8,
            'stretchtext' => 1
        );


        $this->pdf->write1DBarcode($licence_plate, 'C128', 3, 30, '', 20, 2, $style, 'Y');

        $style1 = array(
            'position' => '',
            'align' => 'C',
            'stretch' => true,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => false,
            'hpadding' => 'auto',
            'vpadding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255),
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 8,
            'stretchtext' => 1
        );

        $this->pdf->write1DBarcode($consignment->getHawb(), 'C128', 3, 115, '', 20, 2, $style1, 'Y');


        $company = "";
        if ($consignment->getCompany() != "")
            $company = $consignment->getCompany();
        if ($consignment->getAddressLine1() != "")
            $address1 = ($consignment->getAddressLine1());
        if ($consignment->getAddressLine2() != "")
            $address2 = $consignment->getAddressLine2();
        if ($consignment->getAddressLine3() != "")
            $address3 = $consignment->getAddressLine3();



        $this->pdf->setFont("Arial", "L", 11);
        //$fontname = $pdf->addTTFfont(‘/path-to-font/DejaVuSans.ttf’, ‘TrueTypeUnicode’, “, 32);
        //$fontname = $this->pdf->addTTFfont('../assets/fonts/simhei.ttf', 'TrueTypeUnicode', '', 32);
        //$this->pdf->SetFont($fontname, '', 8);
        $this->pdf->Text(5, 81, $company);
        $this->pdf->Text(5, 85, $consignment->getContact());
        $this->pdf->Text(5, 89, $address1);
        $this->pdf->Text(5, 93, $address2);
        $this->pdf->Text(5, 97, $address3);
        $this->pdf->Text(5, 101, $consignment->getCity());
        $this->pdf->Text(5, 105, $consignment->getPostcode());
        $this->pdf->Text(55, 109, "Tel: " . $consignment->getTelephone());
        $this->pdf->setFont("helvetica", "B", 11);
        $this->pdf->Text(5, 109, $this->country->getName());
        $this->pdf->setFont("helvetica", "B", 8);
        $this->pdf->Text(34, 133, "if undelivered return to:");
        $this->pdf->setFont("helvetica", "L", 8);

        $this->pdf->Text(34, 136, $this->constants['VIVA_SHIPPER_ADDRESS_LINE1'] . " " . $this->constants['VIVA_SHIPPER_ADDRESS_LINE2']);
        $this->pdf->Text(34, 139, $this->constants['VIVA_SHIPPER_ADDRESS_LINE3'] . " " . $this->constants['VIVA_SHIPPER_CITY'] . " " . $this->constants['VIVA_SHIPPER_POSTCODE']);

        $shipper_country = $this->constants['VIVA_SHIPPER_COUNTRY'];
        if ((int) $shipper_country > 0) {
            $shipperCountry = new Country($shipper_country);
            $sCountry = $shipperCountry->getName();
            $sIso = $shipperCountry->getIso();
        } else {
            $sCountry = $shipper_country;
        }
        $this->pdf->Text(34, 142, $sCountry . " " . $sIso);
        return;
    }

    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }
    public function setTrackingParams($serviceId, $agentId)
    {
        $this->trackingServiceId = $serviceId;
        $this->trackingAgentId = $agentId;
    }

    public function reconciliation_data($headingArr,$carrierId,$relPath,$new_csv_file_created,$filePath,$batchNumber) {
        $output = [];
        $comaSeptHeading = implode(",",$headingArr);
        $tableColumn = rtrim($comaSeptHeading,',');
        $csvStr = $tableColumn;
        $csvStr .= "\r\n";
        $templateCheck = 1;
        $invoice_number = '';
        $output['new_invoice_save'] = 0;
        $output['total_weight'] = 0;
        $output['total_pieces'] = 0;
        $output['total_amount'] = 0;
        $csvData = [];
        $row = 1;
        $dataArr = [];
        if (($handle = fopen($relPath, "r")) !== FALSE) {
            while (($data = fgetcsv($handle)) !== FALSE) {
                if($row > 1) {
                    $account_number = '';
                    $agent_reference_number = '';
                    $awb = '';
                    $mawb = '';
                    $volWeight = '';
                    $length = '';
                    $width = '';
                    $height = '';
                    $vat = '';
                    $total_amount = '';
                    $hawb = $data['2'];
                    $service = $data['4'];
                    $weight = 0;
                    if (!empty($data['5'])) {
                        $weight = $data['5'];
                    }
                    $serviceType = $data['7'];
                    $carrierServiceCode = $service . $serviceType;
                    $serviceObj = new ServiceFilter();
                    $serviceObj->addCarrierFilter($carrierId);
                    $serviceObj->addFieldFilter('         carrier_service_code', $carrierServiceCode);
                    $serviceObj = $serviceObj->getColumnList('code');
                    $service_code = '';
                    $service_name = '';
                    if (!empty($serviceObj)) {
                        $service_code = $serviceObj[0]->getCode();
                        $service_name = $serviceObj[0]->getName();
                    }
                    $delivery_country = $data['13'];
                    $chargeType = $data['22'];
                    $notes = $data['23'];
                    $basic_charges = 0;
                    $fuel_charges = 0;
                    $additional_charges = 0;
                    $charge = $data['31'];
                    $currency = $data['32'];
                    if (!empty($charge)) {
                        if ($chargeType == "STS") {
                            $basic_charges = $charge;
                        } else if ($chargeType == "FSS") {
                            $fuel_charges = $charge;
                        } else {
                            $additional_charges = $charge;
                        }
                    }
                    $invoice_number = $data['34'];
                    $collection_date = $data['35'];
                    $number_of_pieces = $data['36'];
                    $csvData[$hawb][] = [
                        'account_number' => $account_number,
                        'invoice_number' => $invoice_number,
                        'agent_reference_number' => $agent_reference_number,
                        'collection_date' => $collection_date,
                        'delivery_country' => $delivery_country,
                        'mawb' => $mawb,
                        'awb' => $awb,
                        'hawb' => $hawb,
                        'service_name' => $service_name,
                        'service_code' => $service_code,
                        'weight' => $weight,
                        'currency' => $currency,
                        'vol_weight' => $volWeight,
                        'length' => $length,
                        'width' => $width,
                        'height' => $height,
                        'number_of_pieces' => $number_of_pieces,
                        'basic_charges' => $basic_charges,
                        'fuel_charges' => $fuel_charges,
                        'additional_charges' => $additional_charges,
                        'vat' => $vat,
                        'total_amount' => $total_amount,
                        'notes' => $notes
                    ];
                }
                $row++;
            }
        } else {
            $output['status'] = 'error';
            $output['message'] = 'File can not open please check permission';
        }


        if(count($csvData) && $output['status'] != "error") {
            foreach($csvData as $parcelArr) {
                $account_number = '';
                $invoice_number = '';
                $agent_reference_number = '';
                $collection_date = '';
                $delivery_country = '';
                $mawb = '';
                $awb = '';
                $hawb = '';
                $service_name = '';
                $service_code = '';
                $weight = '';
                $volWeight = '';
                $length = '';
                $width = '';
                $height = '';
                $number_of_pieces = '';
                $basic_charges = '';
                $fuel_charges = '';
                $additional_charges = '';
                $vat = '';
                $total_amount = '';
                $notes = '';
                $currency = '';
                foreach($parcelArr as $parcel) {
                    $account_number = $parcel['account_number'];
                    $invoice_number = $parcel['invoice_number'];
                    $agent_reference_number = $parcel['agent_reference_number'];
                    $collection_date = $parcel['collection_date'];
                    $delivery_country = $parcel['delivery_country'];
                    $mawb = $parcel['mawb'];
                    $awb = $parcel['awb'];
                    $hawb = $parcel['hawb'];
                    $service_name = $parcel['service_name'];
                    $service_code = $parcel['service_code'];
                    $weight = $parcel['weight'];
                    $volWeight = $parcel['vol_weight'];
                    $length = $parcel['length'];
                    $width = $parcel['width'];
                    $height = $parcel['height'];
                    $number_of_pieces = $parcel['number_of_pieces'];
                    $basic_charges += $parcel['basic_charges'];
                    $fuel_charges = +$parcel['fuel_charges'];
                    $additional_charges += $parcel['additional_charges'];
                    $notes = $parcel['notes'];
                    $currency = $parcel['currency'];
                }
                $total = $fuel_charges + $basic_charges + $additional_charges;
                $vat = ($total * 20) / 100;
                $total_amount = $total + $vat;
                $dt = [
                    'account_number' => $account_number,
                    'invoice_number' => $invoice_number,
                    'agent_reference_number' => $agent_reference_number,
                    'collection_date' => $collection_date,
                    'delivery_country' => $delivery_country,
                    'mawb' => $mawb,
                    'awb' => $awb,
                    'hawb' => $hawb,
                    'service_name' => $service_name,
                    'service_code' => $service_code,
                    'weight' => $weight,
                    'vol_weight' => $volWeight,
                    'length' => $length,
                    'width' => $width,
                    'height' => $height,
                    'number_of_pieces' => $number_of_pieces,
                    'basic_charges' => $basic_charges,
                    'fuel_charges' => $fuel_charges,
                    'additional_charges' => $additional_charges,
                    'vat' => $vat,
                    'total_amount' => $total_amount,
                    'notes' => $notes,
                    'currency' => $currency,
                ];
                $output['total_weight'] += $weight;
                $output['total_pieces'] += $number_of_pieces;
                $output['total_amount'] += $total_amount;
                $output['currency'] = $currency;
                $dataArr[] = $dt;
                $comaSept = implode(",", $dt);
                $csvStr .= rtrim($comaSept, ',');
                $csvStr .= "\r\n";
            }
        }
        if($output['status'] != "error") {
            $myCsvFile = fopen($new_csv_file_created, "a") or die("Unable to open file!");
            fwrite($myCsvFile, $csvStr);
            fclose($myCsvFile);

            $dateNow = date('Y-m-d H:i:s');
            $load_data_sql = "LOAD DATA LOCAL INFILE '" . $new_csv_file_created . "' INTO TABLE `reconciliation_data`
                    FIELDS ENCLOSED BY '\"' 
                    TERMINATED BY ',' LINES TERMINATED BY '\n' IGNORE 1 LINES (
                        " . $tableColumn . "
                    ) 
                    SET  created_at='" . $dateNow . "', batch_number= '" . $batchNumber . "'";

            $res = DbAccess3::runQueryWithError($load_data_sql);

            if ($res === false) {
                $error = DbAccess3::$dbError;
                $output['status'] = 'error';
                $output['message'] = $error[0];
            } else {
                $sql = "SELECT * FROM reconciliation_data WHERE batch_number='" . $batchNumber . "' ";
                $resultSql = DbAccess3::runQuery($sql);
                $res = [];
                while ($obj = mysqli_fetch_object($resultSql)) {
                    $res[] = $obj;
                }
                $output['status'] = 'success';
                $output['file_path'] = $filePath;
                $output['batch_number'] = $batchNumber;
                $output['invoice_number'] = $invoice_number;
                $output['template'] = $templateCheck;
                $output['data'] = $res;
            }
        }
        return $output;
    }
    public function getSummeryData($relPath){
        $returnArr = [];
        $row = 1;
        $isInvoiceType = false;
        $isDataSet = true;
        if (($handle = fopen($relPath, "r")) !== FALSE) {
            while (($data = fgetcsv($handle)) !== FALSE) {
                if($row > 1 && !empty($data['34'])) {
                    $returnArr['collection_date'] = $data['35'];
                    $returnArr['invoice_number'] = $data['34'];
                    $isInvoiceType = true;
                }
                if($isInvoiceType){
                    break;
                }
                $row++;
            }
        }
        return $returnArr;
    }
}
