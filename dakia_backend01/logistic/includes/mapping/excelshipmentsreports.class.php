<?php
 require_once("../Classes/PHPExcel.php");
class ShipmentManageExcelReports  {

    /**
     * Construct
     *
     * @param id/array
     */
    public $gpFilterData = [];
    public $reportType;
    public $borderStyle = array(
              'borders' => array(
                'outline' => array(
                  'style' => PHPExcel_Style_Border::BORDER_THIN
                )
              )
            );
    public $borderStyleBold = array(
              'borders' => array(
                'outline' => array(
                  'style' => PHPExcel_Style_Border::BORDER_THICK
                )
              )
            );
    public function __construct($filter=null,$rType) {
        $this->gpFilterData = $filter;
        $this->reportType = ucfirst($rType);
    }
 
 
  public function excelSetCurrency($currency,$excelRow){
         
        $currencyFormatArray = array(
            'GBP' => '"£ "#,##0.00_-',
            'EUR' => '"€ "#,##0.00_-',
            //  'EUR' => '[$EUR ]#,##0.00_-',
            'USD' => '"$ "#,##0.00_-',
            // 'AED' => '[AED ]#,##0.00_-',
            'AED' => '"AED "#,##0.00_-',
            'CNY' => '"¥ "#,##0.00_-',
            'QAR' => '"QAR "#,##0.00_-',
            'RUB' => '"₽ "#,##0.00_-',
            'ZAR' => '"R "#,##0.00_-',
            'SGD' => '"‎S$ "#,##0.00_-',
            'AUD' => '"‎A$ "#,##0.00_-',
        );
         
        if (isset($currencyFormatArray[$currency])) {
           $excelRow->getNumberFormat()->setFormatCode($currencyFormatArray[$currency]);
        } else {
             $excelRow->getNumberFormat()->setFormatCode( '0.00');
        }
    }
    
  public function setExcelHeadingTitle($excelRow,$title){
        $excelRow->mergeCells("A4:D4");
        $excelRow->setCellValue("A4",$title);
        
        $excelRow->getStyle('A4')->applyFromArray(array(
        'font' => array(
                    'bold' => true,
                    'color' => array('rgb' => 'FF0000'),
                    'size' => 15,
                    'name' => 'Verdana'
            ),'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER) ));
         $excelRow->getStyle('A4')->getAlignment()->setWrapText(true);  
         $excelRow->getDefaultRowDimension()->setRowHeight(-1);
        $showFilter="";
         if(count($this->gpFilterData)>0){
             foreach($this->gpFilterData as $key=>$dvalue)
             $showFilter  .= " $key : ".ucfirst ($dvalue)." \n";
         }
         $filterColumn = "E";
         $excelRow->mergeCells($filterColumn."2:F6");
         $excelRow->getStyle($filterColumn."2:F6")->applyFromArray($this->borderStyle);
         $excelRow->getStyle($filterColumn."2:F6")->applyFromArray(array('alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
            )));
         $excelRow->setCellValue($filterColumn."2",$showFilter);
         $excelRow->getStyle($filterColumn."2")->getAlignment()->setWrapText(true);
         $excelRow->getStyle($filterColumn."2")->applyFromArray(array('font'  => array('size'  =>8,    'name'  => 'Verdana','bold' => true,)));
         $excelRow->getDefaultRowDimension()->setRowHeight(-1);
         
       // $excelRow->mergeCells($headingColumn);
         
    }
    
   public function generateGPReportFileName( ){
          $dirPath = "../_assets/excel_reports/";
            if (!file_exists($dirPath)) {
                mkdir($dirPath, 0777, true);
            }
 
            if(isset($this->gpFilterData['Account']) && $this->gpFilterData['Account']!=''){
                $file_name = $dirPath . "/GP-Report-".$this->reportType."-".$this->gpFilterData['Account']."-". time() . '.xlsx'; 
            }else{
            $file_name = $dirPath . "/GP-Report-".$this->reportType."-" . time() . '.xlsx';
            }
            
            return $file_name;
    }
    
    
        
   public function generateVarianceReportFileName( ){
          $dirPath = "../_assets/excel_reports/";
            if (!file_exists($dirPath)) {
                mkdir($dirPath, 0777, true);
            }
 
            if(isset($this->gpFilterData['Account']) && $this->gpFilterData['Account']!=''){
                $file_name = $dirPath . "/Vairance-Report-".$this->reportType."-".$this->gpFilterData['Account']."-". time() . '.xlsx'; 
            }else{
            $file_name = $dirPath . "/Vairance-Report-".$this->reportType."-" . time() . '.xlsx';
            }
            
            return $file_name;
    }
    
   public  function downloadExcelDetailGpReport($dataConsignment, $loggedInUserCurrencySymbol, $subAccountArr,$searchConsignmentAccountId) {
 
        $output = []; 
        if (count($dataConsignment) > 0) {
            $linecount = 9;
            $lastColumn = "T";
            $objPHPExcel = new PHPExcel();
            /////////////////SET HEADINGS////////////////
              $objPHPExcel->getActiveSheet(0)->setTitle("GP Report ".$this->gpFilterData['Account']);
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A8', "Account")
                    ->setCellValue('B8', "HAWB")
                    ->setCellValue('C8', "Tracking No.")
                    ->setCellValue('D8', "Service.")
                    ->setCellValue('E8', "Label Date Created.")
                    ->setCellValue('F8', "No Of Pieces.")
                    ->setCellValue('G8', "Chargable Weight")
                    ->setCellValue('H8', "Weight")
                    ->setCellValue('I8', "Vol Weight")
                    ->setCellValue('J8', "Agent Charges")
                    ->setCellValue('K8', "Agent Charges (".utf8_encode($loggedInUserCurrencySymbol).")")
                    ->setCellValue('L8', "Customer Charges")
                    ->setCellValue('M8', "Customer Charges (".utf8_encode($loggedInUserCurrencySymbol).")")
                    ->setCellValue('N8', "Supplier Charges")
                    ->setCellValue('O8', "Supplier Charges (".utf8_encode($loggedInUserCurrencySymbol).")")
                    ->setCellValue('P8', "Estimated Margin (".utf8_encode($loggedInUserCurrencySymbol).")")
                    ->setCellValue('Q8', "Estimated Margin %")
                    ->setCellValue('R8', "Actual Margin (".utf8_encode($loggedInUserCurrencySymbol).")")
                    ->setCellValue('S8', "Margin %")
                    ->setCellValue('T8', "Variance (Agent Charges less Supplier Charges)");
                    //->setCellValue('U8', "Variance %");
            /////////////////SET DATA ALIGNMENT////////////////
            $objPHPExcel->getActiveSheet()->getStyle("A8:".$lastColumn."8")->applyFromArray(array(
                'font' => array(
                    'bold' => true,
                ) , 'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ) 
                 
            ));
       
            $objPHPExcel->getActiveSheet()->getStyle("A:$lastColumn")->applyFromArray(array('alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )));
            

            
            /////////////////SET COULMNS WIDTH////////////////
            foreach (range('A', $lastColumn) as $columnID) {
                $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
                        ->setAutoSize(true);
            }
            
             /////////////////LOOP THROUGH DATA////////////////
            foreach ($dataConsignment as $consignmentObj) {
                $estimatedMargin = 0;
                $estimatedProfit = 0;
                $actualMargin = 0;
                $actualProfit = 0;
                $discrepancy = 0;
                $discrepancyProfit = 0;
                if ($consignmentObj->getisDeadWeightChargable() > 0) {
                    $chargableWeight = $consignmentObj->getWeight();
                } else {
                    $chargableWeight = $consignmentObj->getChargeWeight();
                }

                if ($consignmentObj->getCustomerCost() != '') {
                    $customerCost = $consignmentObj->getCustomerCost();
                } else {
                    $customerCost = 0;
                }

                if ($consignmentObj->getCustomerCostCompany() != '') {
                    $customerCostCompany = $consignmentObj->getCustomerCostCompany();
                } else {
                    $customerCostCompany = 0;
                }

                if ($consignmentObj->getAgentCostCompany() != '') {
                    $agentCostCompany = $consignmentObj->getAgentCostCompany();
                } else {
                    $agentCostCompany = 0;
                }

                if ($consignmentObj->getAgentCostSupplier() != '') {
                    $agentCostSupplier = $consignmentObj->getAgentCostSupplier();
                } else {
                    $agentCostSupplier = 0;
                }

                if ($consignmentObj->getPurchaseInvoiceCostCompany() != '') {
                    $purchaseCostCompany = $consignmentObj->getPurchaseInvoiceCostCompany();
                } else {
                    $purchaseCostCompany = 0;
                }

                if ($consignmentObj->getPurchaseInvoiceCostSupplier() != '') {
                    $purchaseCostSupplier = $consignmentObj->getPurchaseInvoiceCostSupplier();
                } else {
                    $purchaseCostSupplier = 0;
                }
                
                $estimatedMargin = $customerCostCompany - $agentCostCompany;
                if ($estimatedMargin > 0 && $agentCostCompany > 0)
                    $estimatedProfit = ($estimatedMargin / $agentCostCompany);


                $actualMargin = $customerCostCompany - $purchaseCostCompany;
                if ($actualMargin > 0 && $purchaseCostCompany > 0)
                    $actualProfit = ($actualMargin / $purchaseCostCompany);


                $discrepancy = $agentCostCompany - $purchaseCostCompany;
                if ($discrepancy > 0 && $purchaseCostCompany > 0)
                    $discrepancyProfit = ($discrepancy / $purchaseCostCompany);

                $consignmentAccount = $consignmentObj->getUserAccount();
                if (Permissions::checkFilePermission('hide_subaccount')) {
                    $consignmentAccountId = $consignmentObj->getUserAccountId();
                    $accountObj = new CustomerAccount();
                    $consignmentAccount = $accountObj->showAccount($consignmentAccountId,$subAccountArr,$searchConsignmentAccountId);
                }
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValueExplicit('A' . $linecount, $consignmentAccount)
                        ->setCellValueExplicit('B' . $linecount, $consignmentObj->getHawb(), PHPExcel_Cell_DataType::TYPE_STRING)
                        ->setCellValueExplicit('C' . $linecount, $consignmentObj->getAwb(), PHPExcel_Cell_DataType::TYPE_STRING)
                        ->setCellValue('D' . $linecount, $consignmentObj->getServiceName())
                        ->setCellValue('E' . $linecount, ($consignmentObj->getDateLabelCreated() != '' && $consignmentObj->getDateLabelCreated() > 0 ? formatDate(date("Y-m-d", $consignmentObj->getDateLabelCreated())) : ''))
                        ->setCellValue('F' . $linecount, $consignmentObj->getNumberPieces())
                        ->setCellValue('G' . $linecount, $chargableWeight)
                        ->setCellValue('H' . $linecount, $consignmentObj->getWeight())
                        ->setCellValue('I' . $linecount, $consignmentObj->getVolWeight())
                        ->setCellValue('J' . $linecount, $agentCostSupplier)
                        ->setCellValue('K' . $linecount, $agentCostCompany)
                        ->setCellValue('L' . $linecount, $customerCost)
                        ->setCellValue('M' . $linecount, $customerCostCompany)
                        ->setCellValue('N' . $linecount, $purchaseCostSupplier)
                        ->setCellValue('O' . $linecount, $purchaseCostCompany)
                        ->setCellValue('P' . $linecount, $estimatedMargin)
                        ->setCellValueExplicit('Q' . $linecount, $estimatedProfit, PHPExcel_Cell_DataType::TYPE_NUMERIC)
                        ->setCellValue('R' . $linecount, $actualMargin)
                        ->setCellValueExplicit('S' . $linecount, $actualProfit, PHPExcel_Cell_DataType::TYPE_NUMERIC)
                        ->setCellValue('T' . $linecount, $discrepancy);
 
                $customerCostCurrency = $consignmentObj->getCustomerCostCurrency(); 
                $customerCompanyCurrency = $consignmentObj->getCustomerCompanyCurrency();
                $agentSupplierCurrency = $consignmentObj->getAgentSupplierCurrency();
                $agentCompanyCurrency = $consignmentObj->getAgentCompanyCurrency();
                $purchaseInvoiceSupplierCurrency = $consignmentObj->getPurchaseInvoiceSupplierCurrency();
                $purchaseInvoiceCompanyCurrency = $consignmentObj->getPurchaseInvoiceCompanyCurrency();

                $this->excelSetCurrency($customerCostCurrency, $objPHPExcel->getActiveSheet()->getStyle('L' . $linecount));
                $this->excelSetCurrency($customerCompanyCurrency, $objPHPExcel->getActiveSheet()->getStyle('M' . $linecount));
                $this->excelSetCurrency($agentSupplierCurrency, $objPHPExcel->getActiveSheet()->getStyle('J' . $linecount));
                $this->excelSetCurrency($agentCompanyCurrency, $objPHPExcel->getActiveSheet()->getStyle('K' . $linecount));
                $this->excelSetCurrency($purchaseInvoiceSupplierCurrency, $objPHPExcel->getActiveSheet()->getStyle('N' . $linecount));
                $this->excelSetCurrency($purchaseInvoiceCompanyCurrency, $objPHPExcel->getActiveSheet()->getStyle('O' . $linecount));

                 /////////////////SET PERCENTAGE SIGN////////////////
                $objPHPExcel->getActiveSheet()->getStyle('Q' . $linecount)
                        ->getNumberFormat()->applyFromArray(
                        array(
                            'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00
                        )
                );
                $objPHPExcel->getActiveSheet()->getStyle('S' . $linecount)
                        ->getNumberFormat()->applyFromArray(
                        array(
                            'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00
                        )
                );
                
                
                 /////////////////COMPARE DATA COLUMNS AND SET COLORS////////////////

                if ($customerCostCompany > $agentCostCompany) {
                    $color = "91c997";
                } else if ($customerCostCompany < $agentCostCompany) {
                    $color = "fc9797";
                } else if ($customerCostCompany == $agentCostCompany) {
                    $color = "faf9ed";
                }
                $objPHPExcel->getActiveSheet()
                        ->getStyle("P$linecount:Q$linecount")
                        ->applyFromArray(
                                array(
                                    'fill' => array(
                                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                        'color' => array('rgb' => $color)
                                    )
                                )
                );

                if ($customerCostCompany > $purchaseCostCompany) {
                    $colorp = "5fbf5a";
                } else if ($customerCostCompany < $purchaseCostCompany) {
                    $colorp = "ff8175";
                } else if ($customerCostCompany == $purchaseCostCompany) {
                    $colorp = "fcfcd2";
                }
                $objPHPExcel->getActiveSheet()
                        ->getStyle("R$linecount:S$linecount")
                        ->applyFromArray(
                                array(
                                    'fill' => array(
                                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                        'color' => array('rgb' => $colorp)
                                    )
                                )
                );
 
                $linecount++;
            }
            /////////////////DATA TOTALS////////////////
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("O$linecount", "Total (".utf8_encode($loggedInUserCurrencySymbol).")");
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("P$linecount", "=SUM(P9:P" . ($linecount - 1) . ")");
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("R$linecount", "=SUM(R9:R" . ($linecount - 1) . ")");
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("T$linecount", "=SUM(T9:T" . ($linecount - 1) . ")");
            $objPHPExcel->setActiveSheetIndex(0)->getStyle("O$linecount:".$lastColumn.$linecount)->applyFromArray(array( 'font' => array('bold' => true)));
             
            /////////////////Border Settings////////////////
           //  $objPHPExcel->getActiveSheet()->getStyle('J8:U8')->applyFromArray($borderStyle);
            $objPHPExcel->getActiveSheet()->getStyle('J8:K'.($linecount - 1))->applyFromArray($this->borderStyle);
            $objPHPExcel->getActiveSheet()->getStyle('L8:M'.($linecount - 1))->applyFromArray($this->borderStyle);
            $objPHPExcel->getActiveSheet()->getStyle('N8:O'.($linecount - 1))->applyFromArray($this->borderStyle);
            $objPHPExcel->getActiveSheet()->getStyle('P8:Q'.($linecount - 1))->applyFromArray($this->borderStyle);
            $objPHPExcel->getActiveSheet()->getStyle('R8:S'.($linecount - 1))->applyFromArray($this->borderStyle);
            $objPHPExcel->getActiveSheet()->getStyle('O'.($linecount).':'.$lastColumn.($linecount))->applyFromArray($this->borderStyle);
             $objPHPExcel->getActiveSheet()->getStyle("A8:$lastColumn".($linecount - 1))->applyFromArray($this->borderStyle);
             $objPHPExcel->getActiveSheet()->getStyle("A8:".$lastColumn."8")->applyFromArray($this->borderStyleBold);
            
             /////////////////HIGLIGHT USED COLUMNS ////////////////
            
            $higlightUsedColumn =  array(
                                'fill' => array(
                                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color' => array('rgb' => 'f7f7ff')
                                )
                            );
            
            $objPHPExcel->getActiveSheet()
                    ->getStyle("K8:K" . ($linecount - 1))
                    ->applyFromArray($higlightUsedColumn);

            $objPHPExcel->getActiveSheet()
                    ->getStyle("M8:M" . ($linecount - 1))
                    ->applyFromArray($higlightUsedColumn);

            $objPHPExcel->getActiveSheet()
                    ->getStyle("O8:O" . ($linecount - 1))
                    ->applyFromArray($higlightUsedColumn);
            $objPHPExcel->getActiveSheet()
                    ->getStyle("P2")
                    ->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => "91c997"))));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("P2", "Customer Charges > Agent Charges");
            $objPHPExcel->getActiveSheet()
                    ->getStyle("P3")
                    ->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => "fc9797"))));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("P3", "Customer Charges < Agent Charges");
            $objPHPExcel->getActiveSheet()
                    ->getStyle("P4")
                    ->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => "faf9ed"))));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("P4", "Customer Charges = Agent Charges");
            $objPHPExcel->getActiveSheet()->mergeCells('P2:Q2');
            $objPHPExcel->getActiveSheet()->mergeCells('P3:Q3');
            $objPHPExcel->getActiveSheet()->mergeCells('P4:Q4');


            $objPHPExcel->getActiveSheet()
                    ->getStyle("R2")
                    ->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => "5fbf5a"))));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("R2", "Customer Charges > Purchase Charges");
            $objPHPExcel->getActiveSheet()
                    ->getStyle("R3")
                    ->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => "ff8175"))));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("R3", "Customer Charges < Purchase Charges");
            $objPHPExcel->getActiveSheet()
                    ->getStyle("R4")
                    ->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => "fcfcd2"))));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("R4", "Customer Charges = Purchase Charges");
            $objPHPExcel->getActiveSheet()->mergeCells('R2:S2');
            $objPHPExcel->getActiveSheet()->mergeCells('R3:S3');
            $objPHPExcel->getActiveSheet()->mergeCells('R4:S4');
 
            $dirPath = "../_assets/excel_reports/";
            if (!file_exists($dirPath)) {
                mkdir($dirPath, 0777, true);
            }

            $this->setExcelHeadingTitle($objPHPExcel->setActiveSheetIndex(0)," GP Report (".$this->reportType.")");
            
           $file_name =  $this->generateGPReportFileName();
 
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            ob_clean();
            $objWriter->setPreCalculateFormulas(); 
            $objWriter->save($file_name);


            $output['result'] = 'Please <a href="' . $file_name . '" target="_blank">Click Here</a> to download the xlsx <a href="' . $file_name . '" class="btn btn-primary" target="_blank"> Download GP Report </a>';
        } else {
            $output['result'] = 'No data found to generate the Excel';
        }
        return $output;
        die;
    }
    
   public function downloadExcelSummaryGpReport($dataConsignment, $loggedInUserCurrencySymbol) {
        $output = [];


        if (count($dataConsignment) > 0) {
            $linecount = 9;
            $objPHPExcel = new PHPExcel();
            $lastColumn = "M";
            /////////////////SET HEADINGS////////////////
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A8', "Account")
                    ->setCellValue('B8', "Service")
                    ->setCellValue('C8', "No Of Pieces.")
                    ->setCellValue('D8', "Weight")
                    ->setCellValue('E8', "Vol Weight")
                    ->setCellValue('F8', "Agent Charges (".utf8_encode($loggedInUserCurrencySymbol).")")
                    ->setCellValue('G8', "Customer Charges (".utf8_encode($loggedInUserCurrencySymbol).")")
                    ->setCellValue('H8', "Supplier Charges (".utf8_encode($loggedInUserCurrencySymbol).")")
                    ->setCellValue('I8', "Estimated Margin (".utf8_encode($loggedInUserCurrencySymbol).")")
                    ->setCellValue('J8', "Estimated Margin %")
                    ->setCellValue('K8', "Actual Margin (".utf8_encode($loggedInUserCurrencySymbol).")")
                    ->setCellValue('L8', "Margin %")
                    ->setCellValue('M8', "Variance (Agent Charges less Supplier Charges)") ;
            /////////////////SET DATA ALIGNMENT////////////////
            $objPHPExcel->getActiveSheet()->getStyle("A8:".$lastColumn."8")->applyFromArray(array(
                'font' => array(
                    'bold' => true,
                ),  'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ) 
                 
            )
                    );
       
            $objPHPExcel->getActiveSheet()->getStyle("C:".$lastColumn)->applyFromArray(array('alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )));
            
            /////////////////SET COULMNS WIDTH////////////////
            foreach (range('A', $lastColumn) as $columnID) {
                $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
                        ->setAutoSize(true);
            }
            
             /////////////////LOOP THROUGH DATA////////////////
            foreach ($dataConsignment as $consignmentObj) {
                $estimatedMargin = 0;
                $estimatedProfit = 0;
                $actualMargin = 0;
                $actualProfit = 0;
                $discrepancy = 0;
                $discrepancyProfit = 0;
 

                if ($consignmentObj->getCustomerCostCompany() != '') {
                    $customerCostCompany = $consignmentObj->getCustomerCostCompany();
                } else {
                    $customerCostCompany = 0;
                }

                if ($consignmentObj->getAgentCostCompany() != '') {
                    $agentCostCompany = $consignmentObj->getAgentCostCompany();
                } else {
                    $agentCostCompany = 0;
                }


                if ($consignmentObj->getPurchaseInvoiceCostCompany() != '') {
                    $purchaseCostCompany = $consignmentObj->getPurchaseInvoiceCostCompany();
                } else {
                    $purchaseCostCompany = 0;
                }
 

                $estimatedMargin = $customerCostCompany - $agentCostCompany;
                if ($estimatedMargin > 0 && $agentCostCompany > 0)
                    $estimatedProfit = ($estimatedMargin / $agentCostCompany);


                $actualMargin = $customerCostCompany - $purchaseCostCompany;
                if ($actualMargin > 0 && $purchaseCostCompany > 0)
                    $actualProfit = ($actualMargin / $purchaseCostCompany);


                $discrepancy = $agentCostCompany - $purchaseCostCompany;
                 if ($discrepancy > 0 && $purchaseCostCompany > 0)
                    $discrepancyProfit = ($discrepancy / $purchaseCostCompany);
                

                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValueExplicit('A' . $linecount, $consignmentObj->getUserAccount())
                        ->setCellValue('B' . $linecount, $consignmentObj->getServiceName())
                        ->setCellValue('C' . $linecount, $consignmentObj->getNumberPieces())
                        ->setCellValue('D' . $linecount, $consignmentObj->getWeight())
                        ->setCellValue('E' . $linecount, $consignmentObj->getVolWeight())
                        ->setCellValue('F' . $linecount, $agentCostCompany)
                        ->setCellValue('G' . $linecount, $customerCostCompany)
                        ->setCellValue('H' . $linecount, $purchaseCostCompany)
                        ->setCellValue('I' . $linecount, $estimatedMargin)
                        ->setCellValueExplicit('J' . $linecount, $estimatedProfit, PHPExcel_Cell_DataType::TYPE_NUMERIC)
                        ->setCellValue('K' . $linecount, $actualMargin)
                        ->setCellValueExplicit('L' . $linecount, $actualProfit, PHPExcel_Cell_DataType::TYPE_NUMERIC)
                        ->setCellValue('M' . $linecount, $discrepancy) ;
 

                $customerCompanyCurrency = $consignmentObj->getCustomerCompanyCurrency();
                $agentCompanyCurrency = $consignmentObj->getAgentCompanyCurrency();
                $purchaseInvoiceCompanyCurrency = $consignmentObj->getPurchaseInvoiceCompanyCurrency();


                $this->excelSetCurrency($customerCompanyCurrency, $objPHPExcel->getActiveSheet()->getStyle('G' . $linecount));
                $this->excelSetCurrency($agentCompanyCurrency, $objPHPExcel->getActiveSheet()->getStyle('F' . $linecount));
                $this->excelSetCurrency($purchaseInvoiceCompanyCurrency, $objPHPExcel->getActiveSheet()->getStyle('H' . $linecount));

                 /////////////////SET PERCENTAGE SIGN////////////////
                $objPHPExcel->getActiveSheet()->getStyle('J' . $linecount)
                        ->getNumberFormat()->applyFromArray(
                        array(
                            'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00
                        )
                );
                $objPHPExcel->getActiveSheet()->getStyle('L' . $linecount)
                        ->getNumberFormat()->applyFromArray(
                        array(
                            'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00
                        )
                );
               
                
                 /////////////////COMPARE DATA COLUMNS AND SET COLORS////////////////

                if ($customerCostCompany > $agentCostCompany) {
                    $color = "91c997";
                } else if ($customerCostCompany < $agentCostCompany) {
                    $color = "fc9797";
                } else if ($customerCostCompany == $agentCostCompany) {
                    $color = "faf9ed";
                }
                $objPHPExcel->getActiveSheet()
                        ->getStyle("I$linecount:J$linecount")
                        ->applyFromArray(
                                array(
                                    'fill' => array(
                                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                        'color' => array('rgb' => $color)
                                    )
                                )
                );

                if ($customerCostCompany > $purchaseCostCompany) {
                    $colorp = "5fbf5a";
                } else if ($customerCostCompany < $purchaseCostCompany) {
                    $colorp = "ff8175";
                } else if ($customerCostCompany == $purchaseCostCompany) {
                    $colorp = "fcfcd2";
                }
                $objPHPExcel->getActiveSheet()
                        ->getStyle("K$linecount:L$linecount")
                        ->applyFromArray(
                                array(
                                    'fill' => array(
                                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                        'color' => array('rgb' => $colorp)
                                    )
                                )
                );
 
                $linecount++;
            }
            /////////////////DATA TOTALS////////////////
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("H$linecount", "Total (".utf8_encode($loggedInUserCurrencySymbol).")");
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("I$linecount", "=SUM(I9:I" . ($linecount - 1) . ")");
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("K$linecount", "=SUM(K9:K" . ($linecount - 1) . ")");
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("M$linecount", "=SUM(M9:M" . ($linecount - 1) . ")");
             $objPHPExcel->getActiveSheet()->getStyle("H$linecount:$lastColumn$linecount")->applyFromArray(array( 'font' => array('bold' => true)));
            
            /////////////////Border Settings////////////////
        
        //     $objPHPExcel->getActiveSheet()->getStyle('F8:N8')->applyFromArray($borderStyle);
            $objPHPExcel->getActiveSheet()->getStyle('F8:'.$lastColumn.($linecount - 1))->applyFromArray($this->borderStyle);
             $objPHPExcel->getActiveSheet()->getStyle('I8:J'.($linecount - 1))->applyFromArray($this->borderStyle);
             $objPHPExcel->getActiveSheet()->getStyle('K8:L'.($linecount - 1))->applyFromArray($this->borderStyle);
            $objPHPExcel->getActiveSheet()->getStyle('F8:H'.($linecount - 1))->applyFromArray($this->borderStyle);
            $objPHPExcel->getActiveSheet()->getStyle('H'.($linecount).':'.$lastColumn.($linecount))->applyFromArray($this->borderStyle);
            $objPHPExcel->getActiveSheet()->getStyle('A8:'.$lastColumn.'8')->applyFromArray($this->borderStyleBold);
            
            
            $objPHPExcel->getActiveSheet()
                    ->getStyle("I2")
                    ->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => "91c997"))));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("I2", "Customer Charges > Agent Charges");
            $objPHPExcel->getActiveSheet()
                    ->getStyle("I3")
                    ->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => "fc9797"))));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("I3", "Customer Charges < Agent Charges");
            $objPHPExcel->getActiveSheet()
                    ->getStyle("I4")
                    ->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => "faf9ed"))));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("I4", "Customer Charges = Agent Charges");
            $objPHPExcel->getActiveSheet()->mergeCells('I2:J2');
            $objPHPExcel->getActiveSheet()->mergeCells('I3:J3');
            $objPHPExcel->getActiveSheet()->mergeCells('I4:J4');


            $objPHPExcel->getActiveSheet()
                    ->getStyle("K2")
                    ->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => "5fbf5a"))));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("K2", "Customer Charges > Purchase Charges");
            $objPHPExcel->getActiveSheet()
                    ->getStyle("K3")
                    ->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => "ff8175"))));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("K3", "Customer Charges < Purchase Charges");
            $objPHPExcel->getActiveSheet()
                    ->getStyle("K4")
                    ->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => "fcfcd2"))));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("K4", "Customer Charges = Purchase Charges");
            $objPHPExcel->getActiveSheet()->mergeCells('K2:L2');
            $objPHPExcel->getActiveSheet()->mergeCells('K3:L3');
            $objPHPExcel->getActiveSheet()->mergeCells('K4:L4');

          
            $this->setExcelHeadingTitle($objPHPExcel->setActiveSheetIndex(0)," GP Report (".$this->reportType.")");
             $file_name =  $this->generateGPReportFileName( );

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            ob_clean();
            $objWriter->setPreCalculateFormulas(); 
            $objWriter->save($file_name);


            $output['result'] = 'Please <a href="' . $file_name . '" target="_blank">Click Here</a> to download the xlsx <a href="' . $file_name . '" class="btn btn-primary" target="_blank"> Download GP Summary Report </a>';
        } else {
            $output['result'] = 'No data found to generate the Excel';
        }
        return $output;
        die;
    }
    

  public function downloadExcelVarianceReport($dataConsignment, $loggedInUserCurrencySymbol,$subAccountArr,$searchConsignmentAccountId) {
 
        $output = []; 
        if (count($dataConsignment) > 0) {
            $linecount = 9;
            $lastColumn = "R";
            $objPHPExcel = new PHPExcel();
            /////////////////SET HEADINGS////////////////
              $objPHPExcel->getActiveSheet(0)->setTitle("Variance Report ".$this->gpFilterData['Account']);
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A8', "Account")
                    ->setCellValue('B8', "HAWB")
                    ->setCellValue('C8', "Tracking No.")
                    ->setCellValue('D8', "Service.")
                    ->setCellValue('E8', "Label Date Created.")
                    ->setCellValue('F8', "No Of Pieces.")
                    ->setCellValue('G8', "Chargable Weight")
                    ->setCellValue('H8', "Weight")
                    ->setCellValue('I8', "Vol Weight")
                    ->setCellValue('J8', "Agent Basic Charges")
                    ->setCellValue('K8', "Agent Additional Charges ")
                    ->setCellValue('L8', "Agent Charges Total (".utf8_encode($loggedInUserCurrencySymbol).")")
                    ->setCellValue('M8', "Invoiced Basic Charges")
                    ->setCellValue('N8', "Invoiced Additional Charges")
                    ->setCellValue('O8', "Invoiced Charges Total (".utf8_encode($loggedInUserCurrencySymbol).") ")
                    ->setCellValue('P8', "Variance Basic Charges")
                    ->setCellValue('Q8', "Variance Additional Charges")
                    ->setCellValue('R8', "Total Variance") ;
                    //->setCellValue('U8', "Variance %");
            /////////////////SET DATA ALIGNMENT////////////////
            $objPHPExcel->getActiveSheet()->getStyle("A8:".$lastColumn."8")->applyFromArray(array(
                'font' => array(
                    'bold' => true,
                ) , 'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ) 
                 
            ));
       
            $objPHPExcel->getActiveSheet()->getStyle("A:$lastColumn")->applyFromArray(array('alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )));
            

            
            /////////////////SET COULMNS WIDTH////////////////
            foreach (range('A', $lastColumn) as $columnID) {
                $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
                        ->setAutoSize(true);
            }
            
             /////////////////LOOP THROUGH DATA////////////////
            foreach ($dataConsignment as $consignmentObj) {
                $agentAdditionalCharges = 0;
                $agent_basic_charges = 0;
                $agent_total = 0;
                $invoiced_additional_charges = 0;
                $invoiced_basic_charges = 0;
                $invoiced_total = 0;
                $varianceAdditionalTotal = 0;
                $varianceBasicTotal = 0;
                $varianceInvoicedTotal = 0;

                if ($consignmentObj->getisDeadWeightChargable() > 0) {
                    $chargableWeight = $consignmentObj->getWeight();
                } else {
                    $chargableWeight = $consignmentObj->getChargeWeight();
                }

// agent_additional_charges,
//         agent_basic_charges,
//         agent_total, 
//         agent_currency, 
//         invoiced_additional_charges, 
//         invoiced_basic_charges, 
//         invoiced_total, 
//         invoiced_currency,
       
                if ($consignmentObj->getagentAdditionalCharges() != '') {
                    $agentAdditionalCharges = $consignmentObj->getAgentAdditionalCharges();
                } else {
                    $agentAdditionalCharges = 0;
                }

                if ($consignmentObj->getAgentBasicCharges() != '') {
                    $agentBasicCharges = $consignmentObj->getAgentBasicCharges();
                } else {
                    $agentBasicCharges = 0;
                }

                if ($consignmentObj->getAgentTotal() != '') {
                    $agentTotal = $consignmentObj->getAgentTotal();
                } else {
                    $agentTotal = 0;
                }

                if ($consignmentObj->getInvoicedAdditionalCharges() != '') {
                    $invoicedAdditionalCharges = $consignmentObj->getInvoicedAdditionalCharges();
                } else {
                    $invoicedAdditionalCharges = 0;
                }

               if ($consignmentObj->getInvoicedBasicCharges() != '') {
                    $invoicedBasicCharges = $consignmentObj->getInvoicedBasicCharges();
                } else {
                    $invoicedBasicCharges = 0;
                }
                
                if ($consignmentObj->getInvoicedTotal() != '') {
                    $invoicedTotal = $consignmentObj->getInvoicedTotal();
                } else {
                    $invoicedTotal = 0;
                }
                $consignmentAccount = $consignmentObj->getUserAccount();
                if (Permissions::checkFilePermission('hide_subaccount')) {
                    $consignmentAccountId = $consignmentObj->getUserAccountId();
                    $accountObj = new CustomerAccount();
                    $consignmentAccount = $accountObj->showAccount($consignmentAccountId,$subAccountArr,$searchConsignmentAccountId);
                }
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValueExplicit('A' . $linecount, $consignmentAccount)
                        ->setCellValueExplicit('B' . $linecount, $consignmentObj->getHawb(), PHPExcel_Cell_DataType::TYPE_STRING)
                        ->setCellValueExplicit('C' . $linecount, $consignmentObj->getAwb(), PHPExcel_Cell_DataType::TYPE_STRING)
                        ->setCellValue('D' . $linecount, $consignmentObj->getServiceName())
                        ->setCellValue('E' . $linecount, ($consignmentObj->getDateLabelCreated() != '' && $consignmentObj->getDateLabelCreated() > 0 ? formatDate(date("Y-m-d", $consignmentObj->getDateLabelCreated())) : ''))
                        ->setCellValue('F' . $linecount, $consignmentObj->getNumberPieces())
                        ->setCellValue('G' . $linecount, $chargableWeight)
                        ->setCellValue('H' . $linecount, $consignmentObj->getWeight())
                        ->setCellValue('I' . $linecount, $consignmentObj->getVolWeight())
                        ->setCellValueExplicit('J' . $linecount, $agentAdditionalCharges, PHPExcel_Cell_DataType::TYPE_NUMERIC)
                        ->setCellValueExplicit('K' . $linecount, $agentBasicCharges, PHPExcel_Cell_DataType::TYPE_NUMERIC)
                        ->setCellValueExplicit('L' . $linecount, $agentTotal, PHPExcel_Cell_DataType::TYPE_NUMERIC)
                        ->setCellValueExplicit('M' . $linecount, $invoicedAdditionalCharges, PHPExcel_Cell_DataType::TYPE_NUMERIC)
                        ->setCellValueExplicit('N' . $linecount, $invoicedBasicCharges, PHPExcel_Cell_DataType::TYPE_NUMERIC)
                        ->setCellValueExplicit('O' . $linecount, $invoicedTotal, PHPExcel_Cell_DataType::TYPE_NUMERIC)
                        ->setCellValue('P' . $linecount,   "=(J".$linecount."-M".$linecount.")" )
                        ->setCellValue('Q' . $linecount, "=K".$linecount."-N".$linecount."" )
                        ->setCellValue('R' . $linecount, "=L".$linecount."-O".$linecount."" ) ;
 
     

             
 
                $linecount++;
            }
            /////////////////DATA TOTALS////////////////
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("O$linecount", "Total (".utf8_encode($loggedInUserCurrencySymbol).")");
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("P$linecount", "=SUM(P9:P" . ($linecount - 1) . ")");
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("Q$linecount", "=SUM(Q9:Q" . ($linecount - 1) . ")");
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("R$linecount", "=SUM(R9:R" . ($linecount - 1) . ")");
            $objPHPExcel->setActiveSheetIndex(0)->getStyle("O$linecount:".$lastColumn.$linecount)->applyFromArray(array( 'font' => array('bold' => true)));
             
            /////////////////Border Settings////////////////
           //  $objPHPExcel->getActiveSheet()->getStyle('J8:U8')->applyFromArray($borderStyle);
            $objPHPExcel->getActiveSheet()->getStyle('J8:L'.($linecount - 1))->applyFromArray($this->borderStyle);
            $objPHPExcel->getActiveSheet()->getStyle('M8:O'.($linecount - 1))->applyFromArray($this->borderStyle);
            $objPHPExcel->getActiveSheet()->getStyle('P8:R'.($linecount - 1))->applyFromArray($this->borderStyle);
 
            $objPHPExcel->getActiveSheet()->getStyle('O'.($linecount).':'.$lastColumn.($linecount))->applyFromArray($this->borderStyle);
             $objPHPExcel->getActiveSheet()->getStyle("A8:$lastColumn".($linecount - 1))->applyFromArray($this->borderStyle);
             $objPHPExcel->getActiveSheet()->getStyle("A8:".$lastColumn."8")->applyFromArray($this->borderStyleBold);
            
             /////////////////HIGLIGHT USED COLUMNS ////////////////
            
            $objPHPExcel->getActiveSheet()
                    ->getStyle("J8:L" . ($linecount - 1))
                    ->applyFromArray(array(
                                'fill' => array(
                                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color' => array('rgb' => 'ebeeff')
                                )
                            ));

            $objPHPExcel->getActiveSheet()
                    ->getStyle("M8:O" . ($linecount - 1))
                    ->applyFromArray(array(
                                'fill' => array(
                                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color' => array('rgb' => 'd4eeff')
                                )
                            ));

            $objPHPExcel->getActiveSheet()
                    ->getStyle("P8:R" . ($linecount - 1))
                    ->applyFromArray(array(
                                'fill' => array(
                                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color' => array('rgb' => 'c2e7ff')
                                )
                            ));
           


            $dirPath = "../_assets/excel_reports/";
            if (!file_exists($dirPath)) {
                mkdir($dirPath, 0777, true);
            }

            $this->setExcelHeadingTitle($objPHPExcel->setActiveSheetIndex(0)," Agent Invoice Variance Report (".$this->reportType.")");
            
           $file_name =  $this->generateVarianceReportFileName();
 
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            ob_clean();
            $objWriter->setPreCalculateFormulas(); 
            $objWriter->save($file_name);


            $output['result'] = 'Please <a href="' . $file_name . '" target="_blank">Click Here</a> to download the xlsx <a href="' . $file_name . '" class="btn btn-primary" target="_blank"> Download Variance Report </a>';
        } else {
            $output['result'] = 'No data found to generate the Excel';
        }
        return $output;
        die;
    }
    
    public function downloadExcelVarianceReportSummary($dataConsignment, $loggedInUserCurrencySymbol) {
 
        $output = []; 
        if (count($dataConsignment) > 0) {
            $linecount = 9;
            $lastColumn = "N";
            $objPHPExcel = new PHPExcel();
            /////////////////SET HEADINGS////////////////
              $objPHPExcel->getActiveSheet(0)->setTitle("Variance Summary ".$this->gpFilterData['Account']);
       
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A8', "Account")
                    ->setCellValue('B8', "Service.")
                    ->setCellValue('C8', "No Of Pieces.")
                    ->setCellValue('D8', "Weight")
                    ->setCellValue('E8', "Vol Weight")
                    ->setCellValue('F8', "Agent Basic Charges")
                    ->setCellValue('G8', "Agent Additional Charges ")
                    ->setCellValue('H8', "Agent Charges Total (".utf8_encode($loggedInUserCurrencySymbol).")")
                    ->setCellValue('I8', "Invoiced Basic Charges")
                    ->setCellValue('J8', "Invoiced Additional Charges")
                    ->setCellValue('K8', "Invoiced Charges Total (".utf8_encode($loggedInUserCurrencySymbol).") ")
                    ->setCellValue('L8', "Variance Basic Charges")
                    ->setCellValue('M8', "Variance Additional Charges")
                    ->setCellValue('N8', "Total Variance") ;
                    //->setCellValue('U8', "Variance %");
            /////////////////SET DATA ALIGNMENT////////////////
            $objPHPExcel->getActiveSheet()->getStyle("A8:".$lastColumn."8")->applyFromArray(array(
                'font' => array(
                    'bold' => true,
                ) , 'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ) 
                 
            ));
       
            $objPHPExcel->getActiveSheet()->getStyle("A:$lastColumn")->applyFromArray(array('alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )));
            

            
            /////////////////SET COULMNS WIDTH////////////////
            foreach (range('A', $lastColumn) as $columnID) {
                $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
                        ->setAutoSize(true);
            }
            
             /////////////////LOOP THROUGH DATA////////////////
            foreach ($dataConsignment as $consignmentObj) {
                $agentAdditionalCharges = 0;
                $agent_basic_charges = 0;
                $agent_total = 0;
                $invoiced_additional_charges = 0;
                $invoiced_basic_charges = 0;
                $invoiced_total = 0;
                $varianceAdditionalTotal = 0;
                $varianceBasicTotal = 0;
                $varianceInvoicedTotal = 0;

                if ($consignmentObj->getisDeadWeightChargable() > 0) {
                    $chargableWeight = $consignmentObj->getWeight();
                } else {
                    $chargableWeight = $consignmentObj->getChargeWeight();
                }

                if ($consignmentObj->getagentAdditionalCharges() != '') {
                    $agentAdditionalCharges = $consignmentObj->getAgentAdditionalCharges();
                } else {
                    $agentAdditionalCharges = 0;
                }

                if ($consignmentObj->getAgentBasicCharges() != '') {
                    $agentBasicCharges = $consignmentObj->getAgentBasicCharges();
                } else {
                    $agentBasicCharges = 0;
                }

                if ($consignmentObj->getAgentTotal() != '') {
                    $agentTotal = $consignmentObj->getAgentTotal();
                } else {
                    $agentTotal = 0;
                }

                if ($consignmentObj->getInvoicedAdditionalCharges() != '') {
                    $invoicedAdditionalCharges = $consignmentObj->getInvoicedAdditionalCharges();
                } else {
                    $invoicedAdditionalCharges = 0;
                }

               if ($consignmentObj->getInvoicedBasicCharges() != '') {
                    $invoicedBasicCharges = $consignmentObj->getInvoicedBasicCharges();
                } else {
                    $invoicedBasicCharges = 0;
                }
                
                if ($consignmentObj->getInvoicedTotal() != '') {
                    $invoicedTotal = $consignmentObj->getInvoicedTotal();
                } else {
                    $invoicedTotal = 0;
                }

                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValueExplicit('A' . $linecount, $consignmentObj->getUserAccount())
                        ->setCellValue('B' . $linecount, $consignmentObj->getServiceName())
                        ->setCellValue('C' . $linecount, $consignmentObj->getNumberPieces())
                        ->setCellValue('D' . $linecount, $consignmentObj->getWeight())
                        ->setCellValue('E' . $linecount, $consignmentObj->getVolWeight())
                        ->setCellValueExplicit('F' . $linecount, $agentAdditionalCharges, PHPExcel_Cell_DataType::TYPE_NUMERIC)
                        ->setCellValueExplicit('G' . $linecount, $agentBasicCharges, PHPExcel_Cell_DataType::TYPE_NUMERIC)
                        ->setCellValueExplicit('H' . $linecount, $agentTotal, PHPExcel_Cell_DataType::TYPE_NUMERIC)
                        ->setCellValueExplicit('I' . $linecount, $invoicedAdditionalCharges, PHPExcel_Cell_DataType::TYPE_NUMERIC)
                        ->setCellValueExplicit('J' . $linecount, $invoicedBasicCharges, PHPExcel_Cell_DataType::TYPE_NUMERIC)
                        ->setCellValueExplicit('K' . $linecount, $invoicedTotal, PHPExcel_Cell_DataType::TYPE_NUMERIC)
                        ->setCellValue('L' . $linecount,   "=(F".$linecount."-I".$linecount.")" )
                        ->setCellValue('M' . $linecount, "=G".$linecount."-J".$linecount."" )
                        ->setCellValue('N' . $linecount, "=H".$linecount."-K".$linecount."" ) ;
 
     

             
 
                $linecount++;
            }
            /////////////////DATA TOTALS////////////////
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("K$linecount", "Total (".utf8_encode($loggedInUserCurrencySymbol).")");
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("L$linecount", "=SUM(L9:L" . ($linecount - 1) . ")");
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("M$linecount", "=SUM(M9:M" . ($linecount - 1) . ")");
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("N$linecount", "=SUM(N9:N" . ($linecount - 1) . ")");
            $objPHPExcel->setActiveSheetIndex(0)->getStyle("K$linecount:".$lastColumn.$linecount)->applyFromArray(array( 'font' => array('bold' => true)));
             
            /////////////////Border Settings////////////////
           //  $objPHPExcel->getActiveSheet()->getStyle('J8:U8')->applyFromArray($borderStyle);
            $objPHPExcel->getActiveSheet()->getStyle('F8:H'.($linecount - 1))->applyFromArray($this->borderStyle);
            $objPHPExcel->getActiveSheet()->getStyle('I8:K'.($linecount - 1))->applyFromArray($this->borderStyle);
            $objPHPExcel->getActiveSheet()->getStyle('L8:N'.($linecount - 1))->applyFromArray($this->borderStyle);
 
            $objPHPExcel->getActiveSheet()->getStyle('K'.($linecount).':'.$lastColumn.($linecount))->applyFromArray($this->borderStyle);
             $objPHPExcel->getActiveSheet()->getStyle("A8:$lastColumn".($linecount - 1))->applyFromArray($this->borderStyle);
             $objPHPExcel->getActiveSheet()->getStyle("A8:".$lastColumn."8")->applyFromArray($this->borderStyleBold);
            
             /////////////////HIGLIGHT USED COLUMNS ////////////////
            
            $objPHPExcel->getActiveSheet()
                    ->getStyle("F8:H" . ($linecount - 1))
                    ->applyFromArray(array(
                                'fill' => array(
                                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color' => array('rgb' => 'ebeeff')
                                )
                            ));

            $objPHPExcel->getActiveSheet()
                    ->getStyle("I8:K" . ($linecount - 1))
                    ->applyFromArray(array(
                                'fill' => array(
                                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color' => array('rgb' => 'd4eeff')
                                )
                            ));

            $objPHPExcel->getActiveSheet()
                    ->getStyle("L8:N" . ($linecount - 1))
                    ->applyFromArray(array(
                                'fill' => array(
                                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color' => array('rgb' => 'c2e7ff')
                                )
                            ));
           


            $dirPath = "../_assets/excel_reports/";
            if (!file_exists($dirPath)) {
                mkdir($dirPath, 0777, true);
            }

            $this->setExcelHeadingTitle($objPHPExcel->setActiveSheetIndex(0)," Agent Invoice Variance Report (".$this->reportType.")");
            
           $file_name =  $this->generateVarianceReportFileName();
 
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            ob_clean();
            $objWriter->setPreCalculateFormulas(); 
            $objWriter->save($file_name);


            $output['result'] = 'Please <a href="' . $file_name . '" target="_blank">Click Here</a> to download the xlsx <a href="' . $file_name . '" class="btn btn-primary" target="_blank"> Download Variance Report </a>';
        } else {
            $output['result'] = 'No data found to generate the Excel';
        }
        return $output;
        die;
    }
}

