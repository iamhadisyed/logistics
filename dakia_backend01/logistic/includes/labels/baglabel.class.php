<?php

/*
 * Class to create Bag label default format of bag label
 */

class BagLabel {

    public function __construct() {
        
    }

    public function generateBagLabel($bagId, $dataArr=array()) {
        $user = SessionManager::getUser();
        $dataArr = [];
        if ($bagId > 0) {
            $bagging = new Bagging($bagId);
            $dataArr['bag_number'] = $bagging->getBagnumber();
            $dataArr['bag_weight'] = $bagging->getWeight();
            $dataArr['bag_actual_weight'] = $bagging->getActualWeight();
            $serviceObj = new Services($bagging->getService());
            if (count($serviceObj) > 0) {
                $dataArr['bag_service'] = $serviceObj->getName();
            }
            $destinationWarehouse = $bagging->getBagDestinationWarehouseId();
            if($destinationWarehouse > 0){
                $warehouse = new Warehouse($destinationWarehouse);
                $countryObj = new Country($bagging->getBagDestinationCountryId());
                    if (count($countryObj) > 0) {
                        $dataArr['bag_country'] = $countryObj->getName();
                    }
                    $dataArr['bag_address_line_1'] = $warehouse->getAddressLine1();
                    $dataArr['bag_address_line_2'] = $warehouse->getAddressLine2();
                    $dataArr['bag_city'] = $warehouse->getCitytown();
                    $dataArr['bag_postcode'] = $warehouse->getPostzipcode();
                    $dataArr['bag_company'] = $warehouse->getWarehouseName();
                
            }
            $parcelbaggingCount = $mawbParcelMappingFilter = new MawbParcelMappingFilter();
            $mawbParcelMappingFilter->addFieldFilter("    bag_id", $bagId);
            $mawbParcelMappingObj = $mawbParcelMappingFilter->getColumnList("mawb_id");
            if (count($mawbParcelMappingObj) > 0) {
                $mawbId = $mawbParcelMappingObj[0]->getMawbId();
                $mawbObj = new Mawb($mawbId);
                $dataArr['bag_mawb'] = $mawbObj->getMawbNumber();
                $flighMappingFilter = new FlightMappingFilter();
                $flighMappingFilter->addFieldFilter("    mawb_id", $mawbId);
                $flighMappingFilterObj = $flighMappingFilter->getColumnList("flight_info_id");
                if (count($flighMappingFilterObj) > 0) {
                    $flightInfoId = $flighMappingFilterObj[0]->getFlightInfoId();
                    $fligthInfo = new FlightInfo($flightInfoId);
                    $countryObj = new Country($fligthInfo->getDestinationCountryId());
                    if (count($countryObj) > 0) {
                        $dataArr['bag_country'] = $countryObj->getName();
                    }
                    $dataArr['bag_address_line_1'] = $fligthInfo->getAddressLine1();
                    $dataArr['bag_address_line_2'] = $fligthInfo->getAddressLine2();
                    $dataArr['bag_city'] = $fligthInfo->getCity();
                    $dataArr['bag_postcode'] = $fligthInfo->getPostcode();
                    $dataArr['bag_company'] = $fligthInfo->getCompany();
                }
            }
            
        }
        if (count($dataArr) > 0) {
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf->SetPrintFooter(false);
            $pdf->SetFooterMargin(0);
            $pdf->SetPrintHeader(false);
            $pdf->SetAutoPageBreak(false, 0);
            $page_size = array(150, 100);
            $pdf->AddPage("P", $page_size);
            $pdf->setFont("helvetica", "L", 7);
            $userImage = User::getUserCompanyImages(true, $user->getId());
            $userImage = explode('images/', $userImage);
            $userImage = "../images/" . $userImage[1];
            $pdf->Image($userImage, 5, 2, 40, 20);
            $style = array(
                'position' => '',
                'align' => 'C',
                'stretch' => true,
                'fitwidth' => true,
                'cellfitalign' => '',
                'border' => false,
                'hpadding' => '1',
                'vpadding' => '1',
                'fgcolor' => array(0, 0, 0),
                'bgcolor' => false, //array(255,255,255),
                'text' => false,
                'font' => 'helvetica',
                'fontsize' => 8,
                'stretchtext' => 1
            );
            $pdf->SetFont('Times', 'B', 10);
            $addressline1 = $dataArr['bag_address_line_1'];
            $addressline2 = $dataArr['bag_address_line_2'];
//        $addressline3 = $dataArr['bag_address_line_3'];
            $addressline4 = "";
            $city = $dataArr['bag_city'];
            $postcode = $dataArr['bag_postcode'];
            $countryname = $dataArr['bag_country'];
            $company = $dataArr['bag_company'];
            $y = 25;
            $pdf->Text(12, $y, strtoupper("TO:"));
            if ($company != '') {
                $y += 5;
                $pdf->Text(12, $y, strtoupper($company));
            }
            if ($addressline1 != '') {
                $y += 5;
                $pdf->Text(12, $y, strtoupper($addressline1));
            }
            if ($addressline2 != '') {
                $y += 5;
                $pdf->Text(12, $y, strtoupper($addressline2));
            }
//        if ($addressline3 != '') {
//            $y += 5;
//            $pdf->Text(12, $y, strtoupper($addressline3));
//        }
//        if ($addressline4 != '') {
//            $y += 5;
//            $pdf->Text(12, $y, strtoupper($addressline4));
//        }
            if ($city != '') {
                $y += 5;
                $pdf->Text(12, $y, strtoupper($city));
            }
            if ($postcode != '') {
                $y += 5;
                $pdf->Text(12, $y, strtoupper($postcode));
            }
            if ($countryname != '') {
                $y += 5;
                $pdf->Text(12, $y, strtoupper($countryname));
            }
            $pdf->SetFont('Times', 'B', 8);
            $pdf->Text(10, 68, "MAWB");
            $pdf->Text(10, 78, "Pieces");
            $pdf->Text(10, 88, "Tag No.");
            $pdf->Text(10, 98, "Weight");
            $pdf->Text(10, 108, "Actual Weight");
//        $pdf->Text(20, 108, "Service");
            $pdf->line(30, 65, 30, 115);   //centerline
            $pdf->line(10, 65, 90, 65); // line 2
            $pdf->line(10, 75, 90, 75); // line 3
            $pdf->line(10, 85, 90, 85); // line 4
            $pdf->line(10, 95, 90, 95); // line 5
            $pdf->line(10, 105, 90, 105);
            $pdf->line(10, 115, 90, 115);
            $pdf->line(10, 25, 90, 25);
            $pdf->line(10, 25, 10, 115);
            $pdf->line(90, 25, 90, 115);
            $pdf->SetFont('Times', 'B', 10);
            $pdf->Text(32, 68, $dataArr['bag_mawb']);
            $parcelCount = $this->getParcelListFromBagNumber($bagId, true);
            $pdf->Text(32, 78, count($parcelCount));
            $pdf->Text(32, 88, $dataArr['bag_number']);
            $pdf->Text(32, 98, $dataArr['bag_weight'] . " Kg");
            $pdf->Text(32, 108, $dataArr['bag_actual_weight'] . " Kg");
            $pdf->SetFont('Times', 'B', 10);
            //$pdf->Text(20, 108, $dataArr['bag_service']);
            $pdf->SetFont('Times', 'B', 12);
            $x = $pdf->GetX();
            $y = $pdf->GetY();
            $style = array(              
                'border' => false,
                'hpadding' => 'auto',
                'vpadding' => 1,
                'fgcolor' => array(0,0,0),
                'bgcolor' => false, //array(255,255,255),
                'text' => true,
                'font' => 'helvetica',
                'fontsize' => 10,
                'stretchtext' => 1                     
        );
            $pdf->write1DBarcode($dataArr['bag_number'], 'C128', 10, 120, 80, 18, 0.4, $style, 'C');
            $folderPath = '../_assets/export_manifest/';
            if (!file_exists($folderPath)) {
                mkdir($folderPath, 0777, true);
            }
            $fileName = time() . "_" . $dataArr['bag_number'] . '.pdf';
            $outFile = $folderPath . $fileName;
            $filePathDb = SETTING_MAIN_ASSETS . "export_manifest/" . $fileName;
            $pdf->Output($outFile, 'F');
            $bagging->setBagLabel($filePathDb);
            $bagging->save();
            $output["STATUS"] = "SUCCESS";
            $output['LABEL'] = 'export_manifest/'.$fileName;
            return $output;
        }
    }
    
    private function getParcelListFromBagNumber($bagId,$parcelArr=false) {
        $list = array();
        $parcelListArr = array();
        if ($bagId > 0) {
            $parcelBaggingmapping = new ParcelBaggingMappingFilter();
            $parcelBaggingmapping->addFieldFilter("    bag_id", $bagId);
            $parcelBaggingMappingObj = $parcelBaggingmapping->getColumnList("parcel_id");
            if (count($parcelBaggingMappingObj) > 0) {
                foreach ($parcelBaggingMappingObj as $parcelBaggingMappingArr) {
                    $list[] = new Parcel($parcelBaggingMappingArr->getParcelId());
                    $parcelListArr[] = $parcelBaggingMappingArr->getParcelId();
                }
            }
        }
        if($parcelArr){
            return $parcelListArr;
        }else{
            return $list;
        }
    }

}
