<?php

/*
 * Class to create Bag label default format of bag label
 */
include_classes([
    'licenceplate.class'
]);
class OcBagLabel {

    public function __construct() {
        
    }

    public function generateOcBagLabel($bagId, $mawb, $flightNo= '') {
        $bagNumber = new Bagging($bagId);
        $bagBarCode = $bagNumber->getBagNumber(); 
        if($bagBarCode != "" && strlen($bagBarCode) != 25){
            $bagBarCode = $this->createOcBagNumber($bagId);
        }
        
        $firstThreeChar = substr($bagBarCode, 0,3);
        if($firstThreeChar == "GGB" || $firstThreeChar == "GKR"){
            $serialNumber = substr($bagBarCode, 7, 4);
        }
        

        $destinationCountryId = $bagNumber->getBagDestinationCountryId();
        $countryObj = new Country($destinationCountryId);
        $countryIso = $countryObj->getIso(); 
        
        $bagDestinationWarehouseId = $bagNumber->getBagDestinationWarehouseId();
        $warehouse = new Warehouse($bagDestinationWarehouseId);
        $weight = $bagNumber->getWeight();
        $pieces = $bagNumber->getPieces();
        $warehouseName = $warehouse->getWarehouseName(); 
        $warehouseCode = $warehouse->getWarehouseCode();
        
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetPrintFooter(false);
        $pdf->SetFooterMargin(0);
        $pdf->SetPrintHeader(false);
        $pdf->SetAutoPageBreak(false, 0);
        $page_size = array(153, 102);
        $pdf->AddPage("P", $page_size);
        //$pdf->setFont("helvetica", "B", 7);
        $pdf->Image('../images/orange.png', 33, 1, 40, 18);
        $pdf->Rect(1, 0, 25, 20, 'F', array(), array(0, 0, 0)); // Left black box
        $pdf->Rect(77, 0, 24, 20, 'F', array(), array(0, 0, 0)); // Right Black box
        $pdf->Rect(1, 0, 100, 152, '', '', ''); // outer box

        $pdf->SetFont('helvetica', 'B', 42);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Text(5, 1, 'S1');
        
        
        
        $pdf->SetFont('helvetica', 'B', 25);
        $pdf->Text(78, 10, '0001'); // sortation dock
        
        $fixedVal = 'GGB';
        if($countryIso != 'CA')
        {
            $pdf->SetFont('helvetica', 'B', 25);
            $pdf->Text(78, 1, $fixedVal);
        }
        else
        {
            $fixedVal = 'GKR';
            $pdf->SetFont('helvetica', 'B', 25);
            $pdf->Text(78, 1, $fixedVal);
        }
        
        $pdf->SetTextColor(0, 0, 0);
        if($countryIso == 'CA')
        {
            $vendorCode = 'UB';
            $pdf->SetFont('helvetica', 'B', 20);
            $pdf->Text(5, 65, 'UBI');
            $pdf->Text(5, 78, $countryIso);

            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->Text(23, 67, 'UBICAEXPE');
            $pdf->Text(29, 80, 'LOW');
            $pdf->Text(57, 67, 'LHR');
            $pdf->Text(54, 80, 'Parcel');
            $pdf->Text(78, 67, $weight.'kg');
            $pdf->Text(78, 80, $pieces.'pcs');

            $pdf->SetFont('helvetica', 'B', 40);
            $pdf->Text(35, 95, $warehouseCode);

            $pdf->SetFont('helvetica', 'B', 16);
            $pdf->write1DBarcode('EZY4', 'C128', 31, 110, 80, 16, 0.4, $style, 'C'); // bag number barcode
            $pdf->Text(42, 125, 'EZY4');

            $pdf->SetFont('helvetica', 'B', 22);
            $pdf->Text(33, 135, $warehouseName);
        }
        elseif($countryIso == 'US')
        {   
            $vendorCode = 'UP';
            $pdf->SetFont('helvetica', 'B', 20);
            $pdf->Text(10, 65, 'UPS');
            $pdf->Text(10, 78, 'US');
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->Text(36, 67, 'EMI');
            $pdf->Text(36, 80, 'NA');
            $pdf->Text(55, 67, 'LHR');
            $pdf->Text(55, 80, $flightNo);
            $pdf->Text(75, 67, $weight.'kg');
            $pdf->Text(76, 80, $pieces.'pcs');
            $pdf->SetFont('helvetica', 'B', 44);
            $pdf->Text(20, 111, $warehouseName);
            $pdf->SetFont('helvetica', 'B', 40);
            $pdf->Text(35, 127, $serialNumber);
        }
        elseif($countryIso == 'AU')
        {
            $vendorCode = 'UB';
            $pdf->SetFont('helvetica', 'B', 20);
            $pdf->Text(5, 65, 'UBI');
            $pdf->Text(5, 78, $countryIso);

            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->Text(25, 67, 'UBIAUEPAR');
            $pdf->Text(27, 80, 'LOW');
            $pdf->Text(57, 67, 'LHR');
            $pdf->Text(55, 80, 'Parcel');
            $pdf->Text(75, 67, $weight.'kg');
            $pdf->Text(76, 80, $pieces.'pcs');

            $pdf->SetFont('helvetica', 'B', 44);
            $pdf->Text(33, 111, $warehouseName);
            $pdf->SetFont('helvetica', 'B', 40);
            $pdf->Text(34, 129, $serialNumber);
        }
        elseif($countryIso == 'NL')
        { 
            
            $vendorCode = 'YE';
            $pdf->SetFont('helvetica', 'B', 20);
            $pdf->Text(5, 65, $vendorCode);
            $pdf->Text(5, 78, $countryIso);

            $pdf->SetFont('helvetica', 'B', 12);
            
            if(strpos($warehouseName, 'HERMES') != '')
            {
                $pdf->Text(24, 67, 'DE-HER');
            }
            else
            {
                $pdf->Text(21, 67, 'DE-DHL-LX');
            }
            $pdf->Text(27, 80, 'AMS');
            $pdf->Text(57, 67, 'LHR');
            $pdf->Text(55, 80, 'Parcel');
            $pdf->Text(75, 67, $weight.'kg');
            $pdf->Text(76, 80, $pieces.'pcs');
            
            $pdf->SetFont('helvetica', 'B', 30);
            if(strpos($warehouseName, 'HERMES')> 0)
            {
                $pdf->Text(18, 103, 'DE_HERMES');
            }
            else
            {
                $pdf->Text(30, 103, 'DE_DHL');
            }
            $pdf->Text(33, 117, 'GB-DE');
            $pdf->SetFont('helvetica', 'B', 48);
            
            if(strpos($warehouseName, 'HERMES')> 0)
            {
                $pdf->Text(19, 129, 'NL02-04');
            }
            else
            {
                $pdf->Text(19, 129,'NL02-01');
            }
        } 
        
        
        $pdf->SetTextColor(0, 0, 0);
        
        $style = array(              
            'border' => false,
            'hpadding' => 'auto',
            'vpadding' => 1,
            'fgcolor' => array(0,0,0),
            'bgcolor' => false, //array(255,255,255),
            'text' => false,
            'font' => 'helvetica',
            'fontsize' => 10,
            'stretchtext' => 1                     
        );
        
        $pdf->write1DBarcode($bagBarCode, 'C128', 10, 28, 80, 24, 0.4, $style, 'C'); // bag number barcode
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Text(19, 53, 'Bag Number : ');
        $pdf->Text(37, 53, $bagBarCode);

        $pdf->SetFont('Times', 'B', 10);
        $folderPath = '../_assets/export_manifest/';
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }
        $fileName = time() . ".pdf";
        $outFile = $folderPath . $fileName;
        $filePathDb = SETTING_MAIN_ASSETS . "export_manifest/" . $fileName;
        $pdf->Output($outFile, 'F'); //die;
        $output["STATUS"] = "SUCCESS";
        $output['LABEL'] = 'export_manifest/'.$fileName;    
        $output['BAG_NUMBER'] = $bagBarCode;
        return $output;
    } 
    
    public function createOcBagNumber($bagId){
        $bagBarCode = "";
        if($bagId > 0){
            $bagging = new Bagging($bagId);
            $destinationCountryId = $bagging->getBagDestinationCountryId();
            $countryObj = new Country($destinationCountryId);
            $countryIso = $countryObj->getIso(); 
            
            $resultArray = LicencePlate::getLicencePlateNumber('208');
            $serialNumber = str_pad($resultArray["RANGE"], '7', '0', STR_PAD_LEFT); 
            
            $fixedVal = 'GGB';
            if($countryIso == 'CA')
            {
                $vendorCode = 'UB';
            }     
            elseif($countryIso == 'US')
            {   
                $vendorCode = 'UP';
            }
            elseif($countryIso == 'NL')
            { 
                $vendorCode = 'YE';
            }
            elseif($countryIso == 'AU')
            {
                $vendorCode = 'UB';
            }
            $bagBarCode = $fixedVal.$serialNumber.'0'.$vendorCode.'A1010001LHR0';
            $bagging->setBagNumber($bagBarCode);
            $bagging->save();
       }
       return $bagBarCode;
    }
}
