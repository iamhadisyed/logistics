<?php

//require_once('tcpdf_include.php');

class InvoicePDF extends TCPDF
{
    const FONT_SMALL = 8;
    const FONT_MEDIUM = 11;
    const FONT_LARGE = 14;
    const FONT_EXTRA_LARGE = 16;
    CONST LINE_VERY_NARROW = 1.5;
    CONST LINE_NARROW = 2.5;
    CONST LINE_MEDIUM = 3.5;
    CONST LINE_WIDE = 5;
    const FONT_FAMILY = "helvetica";
    const MARGIN_LEFT = 0.75;
    const MARGIN_TOP = 0.75;
    const MARGIN_LEFT_WIDE = 8;
    const BARCODE_HEIGHT = 31;
    const WIDTH = 120;
    public $hermes_barcode_display = "";
    public $first = true;
    public $last = true;
    public $userCompany = '';
    public $userAddress = '';
    public $userCountry = '';
    public $userAccount = '';
    public $userName = '';
    private $pdf;
    public $creditNoteObj;

    /*
     * Create instance
     */

    public function SavePDFFile($consignments, $invoice_id, $checks)
    {
        $consignmentUpdateIds = array();
        $invoiceObj = new Invoices($invoice_id);
        if($invoiceObj->getInvoiceDate() > 0) {
            $date = date('d-m-Y', $invoiceObj->getInvoiceDate());
        } else {
            $date = date('d-m-Y');
        }
        $userAccountObj = new CustomerAccount($invoiceObj->getUserAccountId());
        $count = 1;
        
        $grandTotal = 0;
        $vatCostTotal = $invoiceObj->getVatableAmount();
        
        $this->pdf = new invoicetemplate($invoice_id, $checks);
        
        $this->pdf->AddPage();
        $this->pdf->SetAutoPageBreak(TRUE, 0);
        $this->pdf->setFont("times", "B", 8);
        $full_address = $userAccountObj->getBillingAddress();
        $userAddressNew = strtoupper(trim($full_address));
        $this->pdf->MultiCell(60, 40, $userAddressNew, 0, 'L', 0, 1, 15, 60, true);
        
        $this->pdf->Text(163, 50, $invoiceObj->getInvoiceNo());
        $this->pdf->Text(163, 57, $date);
        $this->pdf->Text(163, 64, $userAccountObj->getUserAccount());
        $this->pdf->Text(163, 71, $count);
        if(trim($invoiceObj->getInvoiceReference())!=''){
            $this->pdf->SetLineWidth('0.5');
            $this->pdf->line(125, 83, 195, 83);
            $this->pdf->line(125, 71, 125, 83);
            $this->pdf->line(160, 71, 160, 83);
            $this->pdf->line(195, 71, 195, 83);
            $this->pdf->Text(127, 78, "REFERENCE");
            $this->pdf->Text(163, 78, $invoiceObj->getInvoiceReference());
            $this->pdf->SetLineWidth('0.1');
        }
        $y = 105;
        $invoiceCountrySubTotal = [];
        $currentCountryId = 0;
        if(count($consignments) > 0) {
            $currentCountryId = $consignments[0]->getCountryId();
        }
        foreach ($consignments as $key => $consignmentObj) {
            $countryObj = new Country($consignmentObj->getCountryId());
            $hawb = $consignmentObj->getHawb();
            $reference = $consignmentObj->getReference();
            $destination = ucwords(strtolower($consignmentObj->getCity())) . ", " . ucwords(strtolower($countryObj->getName()));
            $weight = $consignmentObj->getChargeWeight();
            $pieces = $consignmentObj->getNumberPieces();
             if($consignmentObj->getCustomizedServiceId() > 0) {
                 $sereviceObj = new Services($consignmentObj->getCustomizedServiceId());
             } else {
                 $sereviceObj = new Services($consignmentObj->getServiceId());
             }
            $service = ucwords(strtolower($sereviceObj->getName()));

            $consignmentChargesFilter = new ConsignmentChargesFilter();
            $consignmentChargesFilter->addConsignmentChargesTypeJoin();
            $consignmentChargesFilter->addFieldFilter("     cc.consignment_id", $consignmentObj->getId());
            $consignmentChargesFilter->addFieldFilter("     cc.invoice_id", $invoice_id);
            $consignmentChargesFilter->addFieldFilter("     cc.cost_type", "customer");
            $consignmentChargesFilterObj = $consignmentChargesFilter->getList("cc.*,cct.charges_key");

            $vatCharge = 0;
            $basicCharge = 0;
            $additionalCharge = 0;
            $total = 0;
            if (count($consignmentChargesFilterObj) > 0) {
                foreach ($consignmentChargesFilterObj as $obj) {
                    if ($obj->getChargesKey() == "BASIC_CHARGES") {
                        $basicCharge = $obj->getCost();
                    } else {
                        if($obj->getChargesKey() != "FUEL_CHARGES" && $obj->getChargesKey() != "VAT") {
                            $additionalCharge += $obj->getCost();
                        }
                    }
                    if($obj->getChargesKey() != "FUEL_CHARGES" && $obj->getChargesKey() != "VAT") {
                        $total += $obj->getCost();
                    }
                    if($obj->getChargesKey() == "VAT") {
                        $vatCharge += $obj->getCost();
                    }
                }
            }
            $invoiceCountrySubTotal[$countryObj->getId()] += $total;
            $grandTotal += $total;
            $vatInfo = "";
            if($vatCharge > 0) {
                $vatInfo = "*";
            }          
            if($currentCountryId != $countryObj->getId()) {
                $oldCountryObj = new country($currentCountryId);
                $this->pdf->Text(100, $y, "Sub-Total for");
                $this->pdf->setFont("times", "B", 6);
                $this->pdf->Text(180, $y, ucwords(strtolower($oldCountryObj->getName())) . " :          " . number_format($invoiceCountrySubTotal[$currentCountryId], 2), false, false, true, 0, 0, 'R');
                $y = $y + 5;
                $currentCountryId = $countryObj->getId();
            }
            
            $this->pdf->setFont("times", "", 6);
            
            if($checks['check_box_date'] == 1) {
                $labelCreatedDate = (!empty($consignmentObj->getDateLabelCreated()) ? date("d-m-Y",$consignmentObj->getDateLabelCreated()) : "");
                $this->pdf->Text(37, $y, $labelCreatedDate);
            }
            if($checks['check_box_reference'] == 1) {
                $this->pdf->Text(55, $y, trim($reference));
            }
            
            $this->pdf->Text(135, $y, $weight);
            $this->pdf->Text(148, $y, $pieces);
            $this->pdf->Text(156, $y, number_format($basicCharge, 2));
            $this->pdf->Text(175, $y, number_format($additionalCharge, 2));
            $this->pdf->Text(180, $y, number_format($total, 2), false, false, true, 0, 0, 'R');
            $this->pdf->Text(195 + 5, $y, $vatInfo);
            
            if (strlen($hawb) <= 15) {
                $this->pdf->Text(10, $y, $hawb);
            } else {
                $this->pdf->MultiCell(25, 4, $hawb, 0, 'L', 0, 1, 10, $y, true);
                $y = $y + 8;
            }
            
            if($checks['check_box_destination'] == 1) {
                if (strlen($hawb) > 15) {
                    $y = $y - 8;
                }
                if (strlen($destination) <= 15) {
                    $this->pdf->Text(85, $y, $destination);
                } else {
                    $this->pdf->MultiCell(25, 4, $destination, 0, 'L', 0, 1, 85, $y, true);
                    $y = $y + 8;
                }
            }
            if($checks['check_box_service'] == 1) {
                if (strlen($destination) > 15) {
                    $y = $y - 8;
                }
                if (strlen($service) <= 15) {
                    $this->pdf->Text(108, $y, $service);
                } else {
                    $heightCellServices = 5;
                    if (strlen($service) > 15 && strlen($service) < 30)
                        $heightCellServices = 2;
                    else if (strlen($service) > 30 && strlen($service) < 45)
                        $heightCellServices = 3;
                    else if (strlen($service) > 45 && strlen($service) < 60)
                        $heightCellServices = 4;
                    
                    $this->pdf->MultiCell(25, $heightCellServices, $service, 0, 'L', 0, 1, 108, $y, true);
                    $y = $y + 8;
                }
            }
            $this->pdf->setFont("times", "B", 8);
            $y = $y + 8;
            if ($y >= 255) {
                $this->pdf->AddPage();
                $count++;
                $y = 105;
                $this->pdf->setFont("times", "B", 8);

                $this->pdf->MultiCell(60, 40, $userAddressNew, 0, 'L', 0, 1, 15, 58, true);
                $this->pdf->Text(163, 50, $invoiceObj->getInvoiceNo());
                $this->pdf->Text(163, 57, $date);
                $this->pdf->Text(163, 64, $userAccountObj->getUserAccount());
                $this->pdf->Text(163, 71, $count);
            }
            $this->pdf->setFont("times", "B", 7);
            if ($key == count($consignments) - 1) {
                $oldCountryObj = new country($currentCountryId);
                $this->pdf->Text(100, $y, "Sub-Total for");
                $this->pdf->setFont("times", "B", 6);
                $this->pdf->Text(180, $y, ucwords(strtolower($oldCountryObj->getName())) . " :          " . number_format($invoiceCountrySubTotal[$currentCountryId], 2), false, false, true, 0, 0, 'R');
                $y = $y + 5;
                $currentCountryId = $countryObj->getId();
                $this->invoiceFooter($invoice_id, $y, $vatCostTotal, $checks);
            }
        }
        $PDFfileLocation = SETTING_DIR_REMOTE . '_assets/InvoicesFiles/pdf/';
        $fileNameInvoice = $invoiceObj->getInvoiceNo(). "_" . time() . '.pdf';
        $PDF_Filename = $PDFfileLocation . $fileNameInvoice;

        $this->pdf->Output($PDF_Filename, 'F');
        $consignmentUpdateIds['FILENAME'] = $fileNameInvoice;
        $consignmentUpdateIds['LINK'] = $PDF_Filename;

        return $consignmentUpdateIds;
    }

    public function invoiceFooter($invoice_id, $y, $vatValue, $checks) {
        $invoiceObj = new Invoices($invoice_id);

        $currencyObj = new Currency($invoiceObj->getCurrencyId());
        $currency = $currencyObj->getRightsymbol();
        
        $userAccountObj = new CustomerAccount($invoiceObj->getUserAccountId());
        $fuel_charge = $invoiceObj->getFuelCharges();
        $net_amount = $invoiceObj->getNetAmount();
        $net_total = $net_amount + $fuel_charge;
        $vat_amount = $invoiceObj->getVat();
		$vatCchargable = $userAccountObj->getVatChargable();

        $vat_on_surcharge = 0.00;
        $vatPercentage = 20;
        
        $total = $net_total + $vat_amount;
        $userBankAccountObj = new InvoiceBankDetails($userAccountObj->getInvoiceBankDetailsId());
        if($checks['check_box_bank_details'] == 1) {
            $this->setFont("times", "B", 8);
            if(!empty($userBankAccountObj->getBankAddress())) {
                $this->pdf->Text(10, 250, "Bank Details: " . trim($userBankAccountObj->getBankName()) . " " . trim($userBankAccountObj->getBankAddress()));
            }
            if(!empty($userBankAccountObj->getAccountNumber()) ) {
                $this->pdf->Text(10, 253, /*'Title: ' . trim($userBankAccountObj->getAccountTitle()) . */
									'Account No: ' . trim($userBankAccountObj->getAccountNumber()) . 
									(trim($userBankAccountObj->getAccountSortcode())!= ''?'      Sort Code: ' . trim($userBankAccountObj->getAccountSortcode()) : '').
									(trim($userBankAccountObj->getAccountSwiftCode())!= ''?'      SWIFT Code: ' . trim($userBankAccountObj->getAccountSwiftCode()) : '')
									
									);
            }
        }
        $y = $y + 11;
        $aditionalY = 258;

        $this->pdf->line(10, 256, 200, 256);
        $this->pdf->SetLineWidth('0.4');
        $y = $y + 1;
		if(trim($vatCchargable)== '1'){
			$this->pdf->Rect(10, $aditionalY, 98, 29);
			$this->pdf->Text(33, $aditionalY + 4, "VAT Analysis");
			$this->pdf->Text(70, $aditionalY + 4, "*VAT Applicable");
			$this->pdf->Text(13, $aditionalY + 8, "V/C");
			$this->pdf->Text(25, $aditionalY + 8, "VAT Rate");
			$this->pdf->Text(45, $aditionalY + 8, "Service Amount");
			$this->pdf->Text(65, $aditionalY + 8, "VAT Amount");
			$this->pdf->Text(85, $aditionalY + 8, "VAT ON Surcharge");
			$this->pdf->Text(13, $aditionalY + 12, "S");
			$this->pdf->Text(25, $aditionalY + 12, ( number_format($vatPercentage, 2)) . "%");
			$this->pdf->Text(45, $aditionalY + 12, number_format($vatValue, 2));
			$this->pdf->Text(65, $aditionalY + 12, number_format($vat_amount, 2));
			$this->pdf->Text(85, $aditionalY + 12, number_format($vat_on_surcharge, 2));
			$this->pdf->Text(13, $aditionalY + 16, "Z");
			$this->pdf->Text(25, $aditionalY + 16, "0.00%");
			$this->pdf->Text(45, $aditionalY + 16, "0.00");
			$this->pdf->Text(65, $aditionalY + 16, "0.00");
			$this->pdf->Text(85, $aditionalY + 16, "0.00");
			$this->pdf->Text(65, $aditionalY + 20, "Total VAT");
			$this->pdf->Text(85, $aditionalY + 20, number_format($vat_amount, 2));
		}
        /*
         *THIS IS ME FOR QUERY TERMS 
         *QUERYTERMS ARE ABC
         * */
        $queryTermObj = explode("<br />", nl2br($userAccountObj->getQueryTerm()));
        if(count($queryTermObj) > 0) {
            foreach($queryTermObj as $queryTermsKey => $queryTermsTerm) {
                if(trim($queryTermsTerm) == '')
                    unset($queryTermObj[$queryTermsKey]);
            }
        }
        if(count($queryTermObj)>0)
            $totalText = implode("<br/>",$queryTermObj). "<br/>All goods are carried subject to our conditions of carriage.";
        else
            $totalText = "All goods are carried subject to our conditions of carriage.";

        $this->setFont("times", "B", 6);
        $plusY = 22;
        $this->pdf->MultiCell(100, 4, $totalText, 0, 'L', 0, 1, 11, $aditionalY + $plusY, true, 0, true);

        $this->pdf->setFont("times", "B", 8);
        $this->pdf->Text(140, $aditionalY, "Total For Services");
        $this->pdf->Text(180, $aditionalY, number_format($net_amount, 2), false, false, true, 0, 0, 'R');
        $this->pdf->Text(140, $aditionalY + 5, "Fuel Surcharge");
        $this->pdf->Text(180, $aditionalY + 5, number_format($fuel_charge, 2), false, false, true, 0, 0, 'R');

        $this->pdf->Text(140, $aditionalY + 10, "Net Total ");
        $this->pdf->Text(180, $aditionalY + 10, number_format($net_total, 2), false, false, true, 0, 0, 'R');
		if(trim($vatCchargable)== '1'){
			$this->pdf->Text(140, $aditionalY + 15, "VAT  " . (number_format($vatPercentage, 2)) . "% ");
			$this->pdf->Text(180, $aditionalY + 15, number_format($vat_amount, 2), false, false, true, 0, 0, 'R');
		}
        if (trim($currency) != '') {
            $this->pdf->Text(140, $aditionalY + 20, "Total (" . strtoupper($currency) . ")");
        } else {
            $this->pdf->Text(140, $aditionalY + 20, "Total (GBP)");
        }
        $this->pdf->Text(180, $aditionalY + 20, (number_format($total, 2)), false, false, true, 0, 0, 'R');

    }
    
    
    public function SaveManualPDFInvoiceFile($invoice_id) {
        $output = array();
        $invoiceObj = new Invoices($invoice_id);
        if($invoiceObj->getInvoiceDate() > 0) {
            $date = formatDate(date('d-m-Y', $invoiceObj->getInvoiceDate()));
        } else {
            $date = formatDate(date('d-m-Y'));
        }
        $userAccountObj = new CustomerAccount($invoiceObj->getUserAccountId());
        $count = 1;

        $this->pdf = new manualInvoiceFullTemplate($invoice_id);
        $this->pdf->AddPage();
        $this->pdf->SetAutoPageBreak(TRUE, 0);
        $this->pdf->setFont("times", "B", 8);
        //$this->pdf->Text(15, 60, strtoupper($userAccountObj->getCompany()));
        $userAddressNew = strtoupper($userAccountObj->getBillingAddress());
        $this->pdf->MultiCell(60, 40, $userAddressNew, 0, 'L', 0, 1, 15, 60, true);
       
        $this->pdf->Text(163, 50, $invoiceObj->getInvoiceNo());
        $this->pdf->Text(163, 57, $date);
        $this->pdf->Text(163, 64, $userAccountObj->getUserAccount());
//        $this->pdf->Text(163, 71, $count);
        $manualInvoiceDetailFilter = new InvoicesManualDetailsFilter();
        $manualInvoiceDetailFilter->where(['imd.invoice_id' => $invoice_id]);
        $manualInvoiceDetailFilterObj = $manualInvoiceDetailFilter->getList();
        
        $vatChargable = $userAccountObj->getVatChargable();
        $vatInfo = "";
        if($vatChargable > 0) {
            $vatInfo = "*";
        }

        $y = 105;
        if(count($manualInvoiceDetailFilterObj) > 0) {
            foreach($manualInvoiceDetailFilterObj as $key => $manualInvoiceObj) {
                if($manualInvoiceObj->getServiceId() > 0) {
                    $serviceObj = new Services($manualInvoiceObj->getServiceId());
                    $service = $serviceObj->getCode();
                } else {
                    $service = "";
                }
                if(!empty($manualInvoiceObj->getHawb())) {
                    $order_ref = $manualInvoiceObj->getHawb();
                } else {
                    $order_ref = $manualInvoiceObj->getReference();
                }
                $this->pdf->setFont("times", "", 7);
                $this->pdf->Text(12, $y, $order_ref);
                if($manualInvoiceObj->getDateBooked() > 0)
                    $this->pdf->Text(38, $y, formatDate(date("d-m-Y", $manualInvoiceObj->getDateBooked())));
                $this->pdf->Text(60, $y, $service);
                $this->pdf->Text(89, $y, $manualInvoiceObj->getDestination());
                $this->pdf->Text(149, $y, "");
                $this->pdf->Text(169, $y, number_format($manualInvoiceObj->getWeight(), 3));
                $this->pdf->Text(189, $y, number_format($manualInvoiceObj->getAmount(), 2));
                $this->pdf->Text(189 + 15, $y, $vatInfo);
                //$y = $y + 5;
                //$this->pdf->Text(12, $y, $manualInvoiceObj->getDescription());
                $this->pdf->MultiCell(120, 30, $manualInvoiceObj->getDescription(), 0, 'L', false, 1, 12, $y);
                $ycount = ceil(strlen($manualInvoiceObj->getDescription()) / 100);
                $y = $y  + (5 * $ycount);
                
                if ($y >= 255) {
                    $this->pdf->AddPage();
                    $count++;
                    $y = 105;
                    $this->pdf->setFont("times", "B", 8);

                    $this->pdf->Text(15, 58, strtoupper($userAccountObj->getCompany()));
                    $this->pdf->MultiCell(60, 40, $userAddressNew, 0, 'L', 0, 1, 15, 62, true);
                    $this->pdf->Text(163, 50, $invoiceObj->getInvoiceNo());
                    $this->pdf->Text(163, 57, $date);
                    $this->pdf->Text(163, 64, $userAccountObj->getUserAccount());
//                    $this->pdf->Text(163, 71, $count);
                }
                $this->pdf->setFont("times", "B", 7);
                if ($key == count($manualInvoiceDetailFilterObj) - 1) {
                    $this->manualInvoiceFooter($invoice_id);
                }
            }
        }
        
        $PDFfileLocation = SETTING_DIR_REMOTE . '_assets/InvoicesFiles/manual_pdf/';
        $fileNameInvoice = $invoiceObj->getInvoiceNo(). "_" . time() . '.pdf';
        $PDF_Filename = $PDFfileLocation . $fileNameInvoice;

        $this->pdf->Output($PDF_Filename, 'F');
        
        $output['FILENAME'] = $fileNameInvoice;
        $output['LINK'] = $PDF_Filename;

        return $output;
    }
    
    public function manualInvoiceFooter($invoice_id) {
        $invoiceObj = new Invoices($invoice_id);
        $fromInvoiceUserAccount = new CustomerAccount($invoiceObj->getUserAccountId());
        $UserAccount = new CustomerAccount($invoiceObj->getInvoiceBy());
        $userBankAccountObj = new InvoiceBankDetails($fromInvoiceUserAccount->getInvoiceBankDetailsId());
        
        if(count($userBankAccountObj)>0){
			if(!empty($userBankAccountObj->getBankAddress())) {
				$this->pdf->Text(10, 262, "Bank Details: " . trim($userBankAccountObj->getBankName()) . " " . trim($userBankAccountObj->getBankAddress()));
			}
			if(!empty($userBankAccountObj->getAccountNumber()) && !empty($userBankAccountObj->getAccountSortcode())) {
				 
				 $this->pdf->Text(10, 264, /*'Title: ' . trim($userBankAccountObj->getAccountTitle()) . */
									'Account No: ' . trim($userBankAccountObj->getAccountNumber()) . 
									(trim($userBankAccountObj->getAccountSortcode())!= ''?'      Sort Code: ' . trim($userBankAccountObj->getAccountSortcode()) : '').
									((trim($userBankAccountObj->getAccountSwiftCode())!= '' && trim($userBankAccountObj->getAccountSwiftCode())!= '-')?'      SWIFT Code: ' . trim($userBankAccountObj->getAccountSwiftCode()) : '').
									((trim($userBankAccountObj->getAccountIban())!= '' && trim($userBankAccountObj->getAccountIban())!= '*')?'      IBAN: ' . trim($userBankAccountObj->getAccountIban()) : '')
									);
			}
		}
		
		
        
        if(!empty($invoiceObj->getCurrencyId())) {
            $currencyObj = new Currency($invoiceObj->getCurrencyId());
            $currency = $currencyObj->getRightsymbol();
        } else {
            $currency = "GBP";
        }
        $aditionalY = 268;

        $this->pdf->line(10, 268, 201, 268);
        $this->pdf->SetLineWidth('0.4');
        $this->pdf->setFont("times", "", 7);
        $paymentTermObj = explode("<br />", nl2br($fromInvoiceUserAccount->getPaymentTerm()));
        $plusY = 5;
        if(count($paymentTermObj) > 0) {
            foreach($paymentTermObj as $paymentTerm) {
                $this->pdf->Text(11, $aditionalY + $plusY, trim($paymentTerm));
                $plusY = $plusY + 5; 
            }
        }
        $queryTermObj = explode("<br />", nl2br($fromInvoiceUserAccount->getQueryTerm()));
        if(count($queryTermObj) > 0) {
            foreach($queryTermObj as $queryTerm) {
                $this->pdf->Text(11, $aditionalY + $plusY, trim($queryTerm));
                $plusY = $plusY + 5; 
            }
        }
        $vatNumber = (!empty($UserAccount->getVatNumber()) ? " VAT Registration No  " . $UserAccount->getVatNumber() : '' );
        if(!empty($vatNumber)) {
            $this->pdf->Text(11, $aditionalY + $plusY, $vatNumber);
        }

        $credit_sub_total = number_format($invoiceObj->getNetAmount(), 2);
        $credit_vat = number_format($invoiceObj->getVat(), 2);
        $credit_net_total = number_format($invoiceObj->getTotalAmount(), 2);


        $this->pdf->setFont("times", "", 8);
        $this->pdf->Text(167, $aditionalY + 5, "Net Total : ");
        $this->pdf->Text(180, $aditionalY + 5, $credit_sub_total, false, false, true, 0, 0, 'R');
        $this->pdf->Text(172, $aditionalY + 10, "VAT : ");
        $this->pdf->Text(180, $aditionalY + 10, $credit_vat, false, false, true, 0, 0, 'R');
        $this->pdf->setFont("times", "B", 8);
        $this->pdf->Text(153, $aditionalY + 15, "Invoice Total: (" . $currency . ") : ");
        $this->pdf->Text(180, $aditionalY + 15, $credit_net_total, false, false, true, 0, 0, 'R');

        $lineYaditionalY = $aditionalY + 20;
        $this->pdf->line(10, $lineYaditionalY, 201, $lineYaditionalY);
    }
    
    public function SaveCreditNotePDF($credit_note_id) {
        
        $output = array();
        $this->creditNoteObj = $creditNoteObj = new CreditNote($credit_note_id);
        if($creditNoteObj->getCreditDate() > 0) {
            $date = date('d-m-Y', $creditNoteObj->getCreditDate());
        } else {
            $date = date('d-m-Y');
        }
        $userAccountObj = new CustomerAccount($creditNoteObj->getUserAccountId());
        $count = 1;
        
        $this->pdf = new creditNoteFullTemplate($credit_note_id);
        $this->pdf->AddPage();
        $this->pdf->SetAutoPageBreak(TRUE, 0);
        $this->pdf->setFont("times", "B", 8);
        //$this->pdf->Text(15, 60, strtoupper($userAccountObj->getCompany()));
        $userAddressNew = strtoupper($userAccountObj->getBillingAddress());
        $this->pdf->MultiCell(60, 40, $userAddressNew, 0, 'L', 0, 1, 15, 60, true);
       
        $this->pdf->Text(163, 50, $creditNoteObj->getCreditNoteNumber());
        $this->pdf->Text(163, 57, $date);
        $this->pdf->Text(163, 64, $userAccountObj->getUserAccount());
//        $this->pdf->Text(163, 71, $count);
        $creditNoteDetailFilter = new CreditNoteDetailsFilter();
        $creditNoteDetailFilter->where(['cnd.credit_note_id' => $credit_note_id]);
        $creditNoteDetailFilterObj = $creditNoteDetailFilter->getList();

        $y = 105;
        if(count($creditNoteDetailFilterObj) > 0) {
            foreach($creditNoteDetailFilterObj as $key => $cteditNoteDetailObj) {
                $orderReference = $cteditNoteDetailObj->getHawb();
                $reference = $cteditNoteDetailObj->getReference();
                $description = $cteditNoteDetailObj->getDescription();
                $chargeAmount = $cteditNoteDetailObj->getInvoiceAmount();
                $actualAmount = $cteditNoteDetailObj->getChargeableAmount();
                $CreditAmount = $cteditNoteDetailObj->getCreditAmount();
                $this->pdf->setFont("times", "", 7);
                $this->pdf->Text(13, $y, $orderReference);
                //$this->Text(39, $y,"Date");
                $this->pdf->Text(40, $y, $reference);
               // $this->pdf->Text(65, $y, $description);
                $this->pdf->MultiCell(60, 40, $description, 0, 'L', 0, 1, 65, $y, true);
                //$this->pdf->Text(97, $y,"Serv Code");
                //$this->pdf->Text(120, $y,"Type");
                //$this->pdf->Text(131, $y,"Weight");
                //$this->pdf->Text(141, $y,"Charged Amt");
                $this->pdf->Text(140, $y, $chargeAmount);
                $this->pdf->Text(160, $y, $actualAmount);
                $this->pdf->Text(180, $y, $CreditAmount);
                
                $y = $y + 10;
                if ($y >= 255) {
                    $this->pdf->AddPage();
                    $count++;
                    $y = 105;
                    $this->pdf->setFont("times", "B", 8);

                    //$this->pdf->Text(15, 58, strtoupper($userAccountObj->getCompany()));
                    $this->pdf->MultiCell(60, 40, $userAddressNew, 0, 'L', 0, 1, 15, 58, true);
                    $this->pdf->Text(163, 50, $creditNoteObj->getInvoiceType() . $credit_note_id);
                    $this->pdf->Text(163, 57, $date);
                    $this->pdf->Text(163, 64, $userAccountObj->getUserAccount());
//                    $this->pdf->Text(163, 71, $count);
                }
                $this->pdf->setFont("times", "B", 7);
                if ($key == count($creditNoteDetailFilterObj) - 1) {
                    $this->CreditNoteFooter($credit_note_id);
                }
            }
        }
        
        $PDFfileLocation = SETTING_DIR_REMOTE . '_assets/credit_note/pdf/';
        $fileNameInvoice = $creditNoteObj->getCreditNoteNumber() . "_" . time() . '.pdf';
        $PDF_Filename = $PDFfileLocation . $fileNameInvoice;

        $this->pdf->Output($PDF_Filename, 'F');
        
        $output['FILENAME'] = $fileNameInvoice;
        $output['LINK'] = $PDF_Filename;

        return $output;
    }
    
    public function CreditNoteFooter($credit_note_id) {
        $creditNoteObj = new CreditNote($credit_note_id);
        if(!empty($creditNoteObj->getCurrencyId())) {
            $currencyObj = new Currency($creditNoteObj->getCurrencyId());
            $currency = $currencyObj->getRightsymbol();
        } else {
            $currency = "GBP";
        }
        $aditionalY = 268;
        $userAccountObj = new CustomerAccount($creditNoteObj->getUserAccountId());
        $UserAccount = new CustomerAccount($creditNoteObj->getCreditNoteBy());
        $this->pdf->line(10, 268, 201, 268);
        $this->pdf->SetLineWidth('0.4');
        $this->pdf->setFont("times", "", 7);

        $paymentTermObj = explode("<br />", nl2br($userAccountObj->getPaymentTerm()));
        $plusY = 5;
        
        
        if(trim($userAccountObj->getPaymentTerm()) == ""){
            $this->pdf->Text(11, $aditionalY + 5, "Payment terms strictly within 15 days from Date of Invoice ");
        } else 
        {
            if(count($paymentTermObj) > 0) {
                foreach($paymentTermObj as $paymentTerm) {
                    $this->pdf->Text(11, $aditionalY + $plusY, trim($paymentTerm));
                    $plusY = $plusY + 5; 
                }
            }
        }
        
        if(trim($userAccountObj->getQueryTerm()) == ""){
            $this->pdf->Text(11, $aditionalY + 10, "Any queries on this invoice should be notified in writing within 15 days from date of invoice ");
        } else 
        {
            $queryTermObj = explode("<br />", nl2br($userAccountObj->getQueryTerm()));
            if(count($queryTermObj) > 0) {
                foreach($queryTermObj as $queryTerm) {
                    $this->pdf->Text(11, $aditionalY + $plusY, trim($queryTerm));
                    $plusY = $plusY + 5; 
                }
            }
        }
        
        $vatNumber = (!empty($UserAccount->getVatNumber()) ? " VAT Registration No  " . $UserAccount->getVatNumber() : '' );
        if(!empty($vatNumber) && $userAccountObj->getVatChargable()) {
            $this->pdf->Text(11, $aditionalY + $plusY, $vatNumber);
        }
        
                

        $credit_sub_total = number_format($creditNoteObj->getNetAmount(), 2);
        $credit_vat = number_format($creditNoteObj->getVatAmount(), 2);
        $credit_net_total = number_format($creditNoteObj->getCreditTotal(), 2);


        $this->pdf->setFont("times", "", 8);
        $this->pdf->Text(167, $aditionalY + 5, "Net Total : ");
        $this->pdf->Text(180, $aditionalY + 5, $credit_sub_total, false, false, true, 0, 0, 'R');
        if($userAccountObj->getVatChargable()){
            $this->pdf->Text(172, $aditionalY + 10, "VAT : ");
            $this->pdf->Text(180, $aditionalY + 10, $credit_vat, false, false, true, 0, 0, 'R');
        }
        $this->pdf->setFont("times", "B", 8);
        $this->pdf->Text(153, $aditionalY + 15, "Invoice Total: (" . $currency . ") : ");
        $this->pdf->Text(180, $aditionalY + 15, $credit_net_total, false, false, true, 0, 0, 'R');

        $lineYaditionalY = $aditionalY + 20;
        $this->pdf->line(10, $lineYaditionalY, 201, $lineYaditionalY);
    }
    
    public function endOfDayPdf($endOfDayData = ''){
        $output = [];
        $this->pdf = new TCPDF();
        $this->pdf->AddPage();
        $this->pdf->SetAutoPageBreak(TRUE, 0);
        $this->pdf->setFont("times", "B", 8);
        $this->pdf->Text(15, 60, 'HELLo');
        
        $PDFfileLocation = SETTING_DIR_REMOTE . '_assets/endofday/pdf/';
        $fileNameInvoice = "HELLO_" . time() . '.pdf';
        $PDF_Filename = $PDFfileLocation . $fileNameInvoice;

        $this->pdf->Output($PDF_Filename, 'F');
        
        $output['FILENAME'] = $fileNameInvoice;
        $output['LINK'] = $PDF_Filename;

        return $output;
    }
    
    public function SaveSummaryInvoicePDFFile($sumaryArray, $invoice_id, $checks)
    {
        $output = array();
        $userObj = SessionManager::getUser();
        $fromInvoiceUserAccount = new CustomerAccount($userObj->getUserAccountId());
        $invoiceObj = new Invoices($invoice_id);
        if($invoiceObj->getInvoiceDate() > 0) {
            $date = date('d-m-Y', $invoiceObj->getInvoiceDate());
        } else {
            $date = date('d-m-Y');
        }
        $userAccountObj = new CustomerAccount($invoiceObj->getUserAccountId());
        $count = 1;
        $toUserCurrency = $userAccountObj->getBillingCurrency();
        $this->pdf = new summaryinvoicetemplate($invoice_id, $checks);
        
        $this->pdf->AddPage();
        $this->pdf->SetAutoPageBreak(TRUE, 0);
        $this->pdf->setFont("times", "B", 8);
        
        $full_address = $userAccountObj->getBillingAddress();
        $userAddressNew = strtoupper(trim($full_address));
        $this->pdf->MultiCell(60, 40, $userAddressNew, 0, 'L', 0, 1, 15, 60, true);
        
        $this->pdf->Text(163, 50, $invoiceObj->getInvoiceNo());
        $this->pdf->Text(163, 57, $date);
        $this->pdf->Text(163, 64, $userAccountObj->getUserAccount());
        $this->pdf->Text(163, 71, $count);
        
        
        $y = 105;
        $total = 0.00;
        $vatTotal = 0.00;
        $fuelTotal = 0.00;
        foreach ($sumaryArray as $countryId => $serviceArray) {
            $this->pdf->setFont("times", "B", 7);
            $countryObj = new Country($countryId);
            $this->pdf->Text(11, $y, $countryObj->getName());
            $this->pdf->setFont("times", "", 7);
            foreach ($serviceArray as $serviceId => $consignmentArray) {
                $serviceObj = new Services($serviceId);
                $this->pdf->Text(39, $y, $serviceObj->getName()." (" . $serviceObj->getCode() . ")");
                $this->pdf->Text(100, $y, count($consignmentArray));
                $chargesArray = [];
                $consignmentWeight = 0.00;
                foreach ($consignmentArray as $consignmentId => $consignmentChargesArray) {
                    $consignmentObj = new Consignment($consignmentId);
                    $consignmentWeight += $consignmentObj->getChargeWeight();
                    foreach ($consignmentChargesArray as $consignmentChargesId) {
                        $consignmentCharges = new ConsignmentCharges($consignmentChargesId);
                        $consignmentChargesType = new ConsignmentChargesTypes($consignmentCharges->getChargeTypeId());
                        if($consignmentCharges->getCost() > 0) {
                            $chargesArray[$consignmentChargesType->getChargesKey()]["cost"] += $consignmentCharges->getCost();
                            $chargesArray[$consignmentChargesType->getChargesKey()]["title"] = $consignmentChargesType->getTitle();
                        }
                    }   
                }
                $this->pdf->Text(127, $y, number_format($consignmentWeight, 3));
                $subTotalCost = 0.00;
                foreach($chargesArray as $key => $charge) {
                    if($key == "VAT") {
                        $vatTotal += $charge['cost'];
                    } else if($key == "FUEL_CHARGES") {
                        $fuelTotal += $charge['cost'];
                    } else {
                        $this->pdf->Text(150, $y, $charge['title']);
                        $this->pdf->Text(180, $y, number_format($charge['cost'], 2) . " " . (($toUserCurrency == "") ? "GBP" : $toUserCurrency));
                        $y = $y + 5;
                        $subTotalCost += $charge['cost'];
                    }
                } 
                $total += $subTotalCost;
                $this->pdf->setFont("times", "B", 6);
                $this->pdf->Text(150, $y, "Sub Total ");
                $this->pdf->Text(180, $y, number_format($subTotalCost, 2) . " " . (($toUserCurrency == "") ? "GBP" : $toUserCurrency));
                if($vatTotal > 0) {
                    $this->pdf->Text(200, $y, "*");
                }
                $this->pdf->setFont("times", "", 6);
                $y = $y + 5;
                if ($y >= 255) {
                    $this->pdf->AddPage();
                    $count++;
                    $y = 105;
                    $this->pdf->setFont("times", "B", 8);

                    $this->pdf->MultiCell(60, 40, $userAddressNew, 0, 'L', 0, 1, 15, 58, true);
                    $this->pdf->Text(163, 50, $invoiceObj->getInvoiceNo());
                    $this->pdf->Text(163, 57, $date);
                    $this->pdf->Text(163, 64, $userAccountObj->getUserAccount());
                    $this->pdf->Text(163, 71, $count);
                }
            }
            if(!next($sumaryArray)) {
                /*calculate 20% vat*/
                $vatCostTotal = $invoiceObj->getVatableAmount();
                $calculateVat = ($vatCostTotal * 0.20);
                $this->summaryInvoiceFooter($calculateVat, $vatCostTotal,$fuelTotal, $total, $toUserCurrency, $invoiceObj->getUserAccountId());
            }
        }
        $PDFfileLocation = SETTING_DIR_REMOTE . '_assets/InvoicesFiles/summary_pdf/';
        if (!file_exists($PDFfileLocation)) {
            mkdir($PDFfileLocation, 0777);
        }
        $fileNameInvoice = $invoiceObj->getInvoiceNo(). "_" . time() . '.pdf';
        $PDF_Filename = $PDFfileLocation . $fileNameInvoice;

        $this->pdf->Output($PDF_Filename, 'F');
        $output['FILENAME'] = $fileNameInvoice;
        $output['LINK'] = $PDF_Filename;

        return $output;
    }
    
    public function summaryInvoiceFooter($vatTotal, $vatCostTotal, $fuelTotal, $total, $currency, $accountId) {
        $userAccountObj = new CustomerAccount($accountId);
        $userBankAccountObj = new InvoiceBankDetails($userAccountObj->getInvoiceBankDetailsId());
        $this->setFont("times", "B", 8);
        if(!empty($userBankAccountObj->getBankAddress())) {
            $this->pdf->Text(10, 250, "Bank Details: " . trim($userBankAccountObj->getBankName()) . " " . trim($userBankAccountObj->getBankAddress()));
        }
        if(!empty($userBankAccountObj->getAccountNumber()) && !empty($userBankAccountObj->getAccountSortcode())) {
            $this->pdf->Text(10, 253, /*'Title: ' . trim($userBankAccountObj->getAccountTitle()) . */ 'Account No: ' . trim($userBankAccountObj->getAccountNumber()) . '       Sort Code: ' . trim($userBankAccountObj->getAccountSortcode()));
        }
        
        $aditionalY = 258;
        $vatPercentage = 20;
        
        $this->pdf->setFont("times", "B", 6);
        $this->pdf->line(10, 256, 200, 256);
        $this->pdf->SetLineWidth('0.4');
        $this->pdf->Rect(10, $aditionalY, 98, 29);

        $this->pdf->Text(33, $aditionalY + 4, "VAT Analysis");
        $this->pdf->Text(70, $aditionalY + 4, "*VAT Applicable");
        $this->pdf->Text(13, $aditionalY + 8, "V/C");
        $this->pdf->Text(25, $aditionalY + 8, "VAT Rate");
        $this->pdf->Text(45, $aditionalY + 8, "Service Amount");
        $this->pdf->Text(65, $aditionalY + 8, "VAT Amount");
        $this->pdf->Text(85, $aditionalY + 8, "VAT ON Surcharge");
        $this->pdf->Text(13, $aditionalY + 12, "S");
        $this->pdf->Text(25, $aditionalY + 12, (number_format($vatPercentage, 2)) . "%");
        $this->pdf->Text(45, $aditionalY + 12, number_format($vatCostTotal, 2));
        $this->pdf->Text(65, $aditionalY + 12, number_format($vatTotal, 2));
        $this->pdf->Text(85, $aditionalY + 12, "0.00");
        $this->pdf->Text(13, $aditionalY + 16, "Z");
        $this->pdf->Text(25, $aditionalY + 16, "0.00%");
        $this->pdf->Text(45, $aditionalY + 16, "0.00");
        $this->pdf->Text(65, $aditionalY + 16, "0.00");
        $this->pdf->Text(85, $aditionalY + 16, "0.00");
        $this->pdf->Text(65, $aditionalY + 20, "Total VAT");
        $this->pdf->Text(85, $aditionalY + 20, number_format($vatTotal, 2));
        
         /*
         *THIS IS ME FOR QUERY TERMS 
         *QUERYTERMS ARE ABC
         * */
        $queryTermObj = explode("<br />", nl2br($userAccountObj->getQueryTerm()));
        if(count($queryTermObj) > 0) {
            foreach($queryTermObj as $queryTermsKey => $queryTermsTerm) {
                if(trim($queryTermsTerm) == '')
                    unset($queryTermObj[$queryTermsKey]);
            }
        }
        
        if(count($queryTermObj)>0)
            $totalText = implode("<br/>",$queryTermObj). "<br/>All goods are carried subject to our conditions of carriage.";
        else
            $totalText = "All goods are carried subject to our conditions of carriage.";

        $this->setFont("times", "B", 6);
        $plusY = 22;
        $this->pdf->MultiCell(100, 4, $totalText, 0, 'L', 0, 1, 11, $aditionalY + $plusY, true, 0, true);
        
        $this->pdf->setFont("times", "B", 8);
        $this->pdf->Text(140, $aditionalY, "Total For Services");
        $this->pdf->Text(180, $aditionalY, number_format($total, 2), false, false, true, 0, 0, 'R');
        $this->pdf->Text(140, $aditionalY + 6, "Fuel Surcharge");
        $this->pdf->Text(180, $aditionalY + 6, number_format($fuelTotal, 2), false, false, true, 0, 0, 'R');
        $netTotal = $total + $fuelTotal;
        $this->pdf->Text(140, $aditionalY + 12, "Net Total ");
        $this->pdf->Text(180, $aditionalY + 12, number_format($netTotal, 2), false, false, true, 0, 0, 'R');
        $this->pdf->Text(140, $aditionalY + 18, "VAT " . (number_format($vatPercentage,2)) . "% ");
        $this->pdf->Text(180, $aditionalY + 18, number_format($vatTotal, 2), false, false, true, 0, 0, 'R');
        if (trim($currency) != '') {
            $this->pdf->Text(140, $aditionalY + 24, "Total (" . strtoupper($currency) . ")");
        } else {
            $this->pdf->Text(140, $aditionalY + 24, "Total (GBP)");
        }
        $allTotal = $netTotal + $vatTotal;
        $this->pdf->Text(180, $aditionalY + 24, (number_format($allTotal, 2)), false, false, true, 0, 0, 'R');
    }

    public function oweInvoiceTemplate($consignments, $invoice_id, $checks) {
        // create new PDF document
        $this->pdf = new oweinvoicetemplate($invoice_id, $checks, true, 'UTF-8', false);
        $this->pdf->SetCreator(PDF_CREATOR);
        $this->pdf->SetAuthor('One World');
        $this->pdf->SetTitle('Invoice Pdf');
        $this->pdf->SetSubject('One World Invoice');
        $this->pdf->SetKeywords('invoice, one world, template');
        $this->pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
        $this->pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $this->pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $this->pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $this->pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $this->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $this->pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
        }
        $this->pdf->SetFont('times', 'BI', 12);
        $txt = '';
        $consignmentUpdateIds = array();
        $invoiceObj = new Invoices($invoice_id);
        if($invoiceObj->getInvoiceDate() > 0) {
            $date = date('d-m-Y', $invoiceObj->getInvoiceDate());
        } else {
            $date = date('d-m-Y');
        }
        $userAccountObj = new CustomerAccount($invoiceObj->getUserAccountId());
        $count = 1;

        $grandTotal = 0;
        $vatCostTotal = $invoiceObj->getVatableAmount();

        $this->pdf->AddPage();
        $this->pdf->SetAutoPageBreak(TRUE, 0);
        $this->pdf->setFont("times", "B", 8);
        $full_address = $userAccountObj->getBillingAddress();
        $userAddressNew = strtoupper(trim($full_address));
        $this->pdf->MultiCell(80, 33, trim($userAddressNew), 0, 'L', 0, 0, 12, 36, '', 0, '','', '', '','');

        $this->pdf->MultiCell(35, 8, $invoiceObj->getInvoiceNo(), 0, 'C', 0, 0, 125, 43, '', '', '', '', '8','M', true);
        $this->pdf->MultiCell(35, 8, $date, 0, 'C', 0, 0, 160, 43, '', '', '', '', '8','M', true);
        $this->pdf->MultiCell(35, 8, $userAccountObj->getUserAccount(), 0, 'C', 0, 0, 125, 59, '', '', '', '', '8','M', true);
        $this->pdf->MultiCell(35, 8, "30 Days", 0, 'C', 0, 0, 160, 59, '', '', '', '', '8','M', true);

//        if(trim($invoiceObj->getInvoiceReference())!=''){
//            $this->pdf->SetLineWidth('0.5');
//            $this->pdf->line(125, 83, 195, 83);
//            $this->pdf->line(125, 71, 125, 83);
//            $this->pdf->line(160, 71, 160, 83);
//            $this->pdf->line(195, 71, 195, 83);
//            $this->pdf->Text(127, 78, "REFERENCE");
//            $this->pdf->Text(163, 78, $invoiceObj->getInvoiceReference());
//            $this->pdf->SetLineWidth('0.1');
//        }
        $y = 80;
        $invoiceCountrySubTotal = [];
        $currentCountryId = 0;
        if(count($consignments) > 0) {
            $currentCountryId = $consignments[0]->getCountryId();
        }
        foreach ($consignments as $key => $consignmentObj) {
            $countryObj = new Country($consignmentObj->getCountryId());
            $hawb = $consignmentObj->getHawb();
            $reference = $consignmentObj->getReference();
            $destination = ucwords(strtolower($consignmentObj->getCity())) . ", " . ucwords(strtolower($countryObj->getName()));
            $weight = $consignmentObj->getChargeWeight();
            $pieces = $consignmentObj->getNumberPieces();
            if($consignmentObj->getCustomizedServiceId() > 0) {
                $sereviceObj = new Services($consignmentObj->getCustomizedServiceId());
            } else {
                $sereviceObj = new Services($consignmentObj->getServiceId());
            }
            $service = ucwords(strtolower($sereviceObj->getName()));

            $consignmentChargesFilter = new ConsignmentChargesFilter();
            $consignmentChargesFilter->addConsignmentChargesTypeJoin();
            $consignmentChargesFilter->addFieldFilter("     cc.consignment_id", $consignmentObj->getId());
            $consignmentChargesFilter->addFieldFilter("     cc.invoice_id", $invoice_id);
            $consignmentChargesFilter->addFieldFilter("     cc.cost_type", "customer");
            $consignmentChargesFilterObj = $consignmentChargesFilter->getList("cc.*,cct.charges_key");

            $vatCharge = 0;
            $basicCharge = 0;
            $additionalCharge = 0;
            $total = 0;
            if (count($consignmentChargesFilterObj) > 0) {
                foreach ($consignmentChargesFilterObj as $obj) {
                    if ($obj->getChargesKey() == "BASIC_CHARGES") {
                        $basicCharge = $obj->getCost();
                    } else {
                        if($obj->getChargesKey() != "FUEL_CHARGES" && $obj->getChargesKey() != "VAT") {
                            $additionalCharge += $obj->getCost();
                        }
                    }
                    if($obj->getChargesKey() != "FUEL_CHARGES" && $obj->getChargesKey() != "VAT") {
                        $total += $obj->getCost();
                    }
                    if($obj->getChargesKey() == "VAT") {
                        $vatCharge += $obj->getCost();
                    }
                }
            }
            $invoiceCountrySubTotal[$countryObj->getId()] += $total;
            $grandTotal += $total;
            $vatInfo = "";
            if($vatCharge > 0) {
                $vatInfo = "*";
            }
            if($currentCountryId != $countryObj->getId()) {
                $oldCountryObj = new country($currentCountryId);
                $this->pdf->Text(100, $y, "Sub-Total for");
                $this->pdf->setFont("times", "B", 6);
                $this->pdf->Text(180, $y, ucwords(strtolower($oldCountryObj->getName())) . " :          " . number_format($invoiceCountrySubTotal[$currentCountryId], 2), false, false, true, 0, 0, 'R');
                $y = $y + 5;
                $currentCountryId = $countryObj->getId();
            }

            $this->pdf->setFont("times", "", 6);

            if($checks['check_box_date'] == 1) {
                $labelCreatedDate = (!empty($consignmentObj->getDateLabelCreated()) ? date("d-m-Y",$consignmentObj->getDateLabelCreated()) : "");
                $this->pdf->Text(37, $y, $labelCreatedDate);
            }
            if($checks['check_box_reference'] == 1) {
                $this->pdf->Text(55, $y, trim($reference));
            }

            $this->pdf->Text(135, $y, $weight);
            $this->pdf->Text(148, $y, $pieces);
            $this->pdf->Text(156, $y, number_format($basicCharge, 2));
            $this->pdf->Text(175, $y, number_format($additionalCharge, 2));
            $this->pdf->Text(180, $y, number_format($total, 2), false, false, true, 0, 0, 'R');
            $this->pdf->Text(195 + 5, $y, $vatInfo);

            if (strlen($hawb) <= 15) {
                $this->pdf->Text(10, $y, $hawb);
            } else {
                $this->pdf->MultiCell(25, 4, $hawb, 0, 'L', 0, 1, 10, $y, true);
                $y = $y + 8;
            }

            if($checks['check_box_destination'] == 1) {
                if (strlen($hawb) > 15) {
                    $y = $y - 8;
                }
                if (strlen($destination) <= 15) {
                    $this->pdf->Text(85, $y, $destination);
                } else {
                    $this->pdf->MultiCell(25, 4, $destination, 0, 'L', 0, 1, 85, $y, true);
                    $y = $y + 8;
                }
            }
            if($checks['check_box_service'] == 1) {
                if (strlen($destination) > 15) {
                    $y = $y - 8;
                }
                if (strlen($service) <= 15) {
                    $this->pdf->Text(108, $y, $service);
                } else {
                    $heightCellServices = 5;
                    if (strlen($service) > 15 && strlen($service) < 30)
                        $heightCellServices = 2;
                    else if (strlen($service) > 30 && strlen($service) < 45)
                        $heightCellServices = 3;
                    else if (strlen($service) > 45 && strlen($service) < 60)
                        $heightCellServices = 4;

                    $this->pdf->MultiCell(25, $heightCellServices, $service, 0, 'L', 0, 1, 108, $y, true);
                    $y = $y + 8;
                }
            }
            $this->pdf->setFont("times", "B", 8);
            $y = $y + 8;
            if ($y >= 255) {
                $this->pdf->AddPage();
                $count++;
                $y = 85;
                $this->pdf->setFont("times", "B", 8);

                $this->pdf->MultiCell(80, 33, trim($userAddressNew), 0, 'L', 0, 0, 12, 36, true, 0, '', true, '','', '');
                $this->pdf->MultiCell(35, 8, $invoiceObj->getInvoiceNo(), 0, 'C', 0, 0, 125, 43, '', '', '', '', '8','M', true);
                $this->pdf->MultiCell(35, 8, $date, 0, 'C', 0, 0, 160, 43, '', '', '', '', '8','M', true);
                $this->pdf->MultiCell(35, 8, $userAccountObj->getUserAccount(), 0, 'C', 0, 0, 125, 59, '', '', '', '', '8','M', true);
                $this->pdf->MultiCell(35, 8, "30 Days", 0, 'C', 0, 0, 160, 59, '', '', '', '', '8','M', true);
//                $this->pdf->MultiCell(60, 40, $userAddressNew, 0, 'L', 0, 1, 15, 58, true);
//                $this->pdf->Text(163, 50, $invoiceObj->getInvoiceNo());
//                $this->pdf->Text(163, 57, $date);
//                $this->pdf->Text(163, 64, $userAccountObj->getUserAccount());
//                $this->pdf->Text(163, 71, $count);
            }
            $this->pdf->setFont("times", "B", 7);
            if ($key == count($consignments) - 1) {
                $oldCountryObj = new country($currentCountryId);
                $this->pdf->Text(100, $y, "Sub-Total for");
                $this->pdf->setFont("times", "B", 6);
                $this->pdf->Text(180, $y, ucwords(strtolower($oldCountryObj->getName())) . " :          " . number_format($invoiceCountrySubTotal[$currentCountryId], 2), false, false, true, 0, 0, 'R');
                $y = $y + 5;
                $currentCountryId = $countryObj->getId();
                $this->oweInvoiceFooter($invoice_id, $y, $vatCostTotal, $checks);
            }
        }
        $this->pdf->Write(0, $txt, '', 0, 'C', true, 0, false, false, 0);

        $PDFfileLocation = SETTING_DIR_REMOTE . '_assets/InvoicesFiles/pdf/';
        $fileNameInvoice = $invoiceObj->getInvoiceNo(). "_" . time() . '.pdf';
        $PDF_Filename = $PDFfileLocation . $fileNameInvoice;

        $this->pdf->Output($PDF_Filename, 'F');
        $consignmentUpdateIds['FILENAME'] = $fileNameInvoice;
        $consignmentUpdateIds['LINK'] = $PDF_Filename;

        return $consignmentUpdateIds;



//        $PDFfileLocation = SETTING_DIR_REMOTE . '_assets/InvoicesFiles/pdf/';
//        $fileNameInvoice =  "najam_" . time() . '.pdf';
//        $PDF_Filename = $PDFfileLocation . $fileNameInvoice;
////        $this->pdf->Output($PDF_Filename, 'F');
//        $this->pdf->Output($PDF_Filename, 'I');
    }

    public function oweInvoiceFooter($invoice_id, $y, $vatValue, $checks) {
        $invoiceObj = new Invoices($invoice_id);

        $currencyObj = new Currency($invoiceObj->getCurrencyId());
        $currency = $currencyObj->getRightsymbol();

        $userAccountObj = new CustomerAccount($invoiceObj->getUserAccountId());
        $fuel_charge = $invoiceObj->getFuelCharges();
        $net_amount = $invoiceObj->getNetAmount();
        $net_total = $net_amount + $fuel_charge;
        $vat_amount = $invoiceObj->getVat();

        $vat_on_surcharge = 0.00;
        $vatPercentage = 20;

        $total = $net_total + $vat_amount;
        $y = $y + 11;
        $aditionalY = 250;

        $this->pdf->SetLineStyle(array('color' => array(250, 5, 5)));
        $this->pdf->SetLineWidth('0.4');
        $y = $y + 1;
        $this->pdf->SetTextColor(13, 132, 237);
        $this->pdf->SetFillColor(118, 174, 223  );
        $this->pdf->setFont("times", "B", 17);
        // MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, isHtml=false, autopadding=true, maxh='', valign='', fitcell='')
        $this->pdf->MultiCell(130, 10, "Thank you for your business!", 0, 'C', 1, 0, 10, $aditionalY, false, 0, false, false, '','M', '');
        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->setFont("times", "", 14);
        $this->pdf->MultiCell(35, 10, "Net", 0, 'L', 1, 0, 130, $aditionalY, false, 0, false, false, '','M', '');
        $this->pdf->MultiCell(35, 10, "Vat @ 20%", 0, 'L', 1, 0, 130, $aditionalY + 10, false, 0, false, false, '','M', '');
        $this->pdf->SetTextColor(34, 142, 238);
        $this->pdf->setFont("times", "B", 14);
        $this->pdf->MultiCell(35, 10, "Total", 0, 'L', 1, 0, 130, $aditionalY + 20, false, 0, false, false, '','M', '');

        $userBankAccountObj = new InvoiceBankDetails($userAccountObj->getInvoiceBankDetailsId());
        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->setFont("times", "", 10);
        if(!empty($userBankAccountObj->getBankAddress())) {
            $this->pdf->Text(10, $aditionalY + 15, "Bank Details: " . trim($userBankAccountObj->getBankName()) . " " . trim($userBankAccountObj->getBankAddress()));
        }
        /*if(!empty($userBankAccountObj->getAccountTitle())) {
            $this->pdf->Text(10, $aditionalY + 20, 'Account Name: ' . trim($userBankAccountObj->getAccountTitle()));
        }*/
        if(!empty($userBankAccountObj->getAccountNumber())) {
            $this->pdf->Text(10, $aditionalY + 25, 'Account No: ' . trim($userBankAccountObj->getAccountNumber()));
        }
        if(!empty($userBankAccountObj->getAccountSortcode())) {
            $this->pdf->Text(10, $aditionalY + 30, 'Sort Code: ' . trim($userBankAccountObj->getAccountSortcode()));
        }
//       s $this->pdf->Text(70, $aditionalY + 4, "*VAT Applicable");
//        $this->pdf->Text(13, $aditionalY + 8, "V/C");
//        $this->pdf->Text(25, $aditionalY + 8, "VAT Rate");
//        $this->pdf->Text(45, $aditionalY + 8, "Service Amount");
//        $this->pdf->Text(65, $aditionalY + 8, "VAT Amount");
//        $this->pdf->Text(85, $aditionalY + 8, "VAT ON Surcharge");
//        $this->pdf->Text(13, $aditionalY + 12, "S");
//        $this->pdf->Text(25, $aditionalY + 12, ( number_format($vatPercentage, 2)) . "%");
//        $this->pdf->Text(45, $aditionalY + 12, number_format($vatValue, 2));
//        $this->pdf->Text(65, $aditionalY + 12, number_format($vat_amount, 2));
//        $this->pdf->Text(85, $aditionalY + 12, number_format($vat_on_surcharge, 2));
//        $this->pdf->Text(13, $aditionalY + 16, "Z");
//        $this->pdf->Text(25, $aditionalY + 16, "0.00%");
//        $this->pdf->Text(45, $aditionalY + 16, "0.00");
//        $this->pdf->Text(65, $aditionalY + 16, "0.00");
//        $this->pdf->Text(85, $aditionalY + 16, "0.00");
//        $this->pdf->Text(65, $aditionalY + 20, "Total VAT");
//        $this->pdf->Text(85, $aditionalY + 20, number_format($vat_amount, 2));

        /*
         *THIS IS ME FOR QUERY TERMS
         *QUERYTERMS ARE ABC
         * */
//        $queryTermObj = explode("<br />", nl2br($userAccountObj->getQueryTerm()));
//        if(count($queryTermObj) > 0) {
//            foreach($queryTermObj as $queryTermsKey => $queryTermsTerm) {
//                if(trim($queryTermsTerm) == '')
//                    unset($queryTermObj[$queryTermsKey]);
//            }
//        }
//
//
//        if(count($queryTermObj)>0)
//            $totalText = implode("<br/>",$queryTermObj). "<br/>All goods are carried subject to our conditions of carriage.";
//        else
//            $totalText = "All goods are carried subject to our conditions of carriage.";
//
//        $this->setFont("times", "B", 6);
//        $plusY = 22;
//        $this->pdf->MultiCell(100, 4, $totalText, 0, 'L', 0, 1, 11, $aditionalY + $plusY, true, 0, true);
//
//        $this->pdf->setFont("times", "B", 8);
//        $this->pdf->Text(140, $aditionalY, "Total For Services");
        $this->pdf->setFont("times", "", 13);
//        $this->pdf->Text(180, $aditionalY + 3, number_format($net_amount, 2), false, false, true, 0, 0, 'R');
//        $this->pdf->Text(140, $aditionalY + 5, "Fuel Surcharge");
//        $this->pdf->Text(180, $aditionalY + 5, number_format($fuel_charge, 2), false, false, true, 0, 0, 'R');
//
//        $this->pdf->Text(140, $aditionalY + 10, "Net Total ");
//        $this->pdf->Text(180, $aditionalY + 10, number_format($net_total, 2), false, false, true, 0, 0, 'R');
//        $this->pdf->Text(140, $aditionalY + 15, "VAT  " . (number_format($vatPercentage, 2)) . "% ");
//        $this->pdf->Text(180, $aditionalY + 13, number_format($vat_amount, 2), false, false, true, 0, 0, 'R');
//        if (trim($currency) != '') {
//            $this->pdf->Text(140, $aditionalY + 20, "Total (" . strtoupper($currency) . ")");
//        } else {
//            $this->pdf->Text(140, $aditionalY + 20, "Total (GBP)");
//        }
        if (trim($currency) != '') {
            $this->pdf->Text(180, $aditionalY + 3, strtoupper($currency) . " " . number_format($net_total, 2), false, false, true, 0, 0, 'R');
            $this->pdf->Text(180, $aditionalY + 13, strtoupper($currency) . " " . number_format($vat_amount, 2), false, false, true, 0, 0, 'R');
            $this->pdf->Text(180, $aditionalY + 23, strtoupper($currency) . " " . (number_format($total, 2)), false, false, true, 0, 0, 'R');
        } else {
            $this->pdf->Text(180, $aditionalY + 3, "GBP ". number_format($net_total, 2), false, false, true, 0, 0, 'R');
            $this->pdf->Text(180, $aditionalY + 13, "GBP ". number_format($vat_amount, 2), false, false, true, 0, 0, 'R');
            $this->pdf->Text(180, $aditionalY + 23, "GBP ". (number_format($total, 2)), false, false, true, 0, 0, 'R');
        }
        $this->pdf->line(10, 285, 200, 285);
        $fromInvoiceUserAccount = new CustomerAccount($invoiceObj->getInvoiceBy());
        $fromAddressStr = str_replace(array("\r","\n")," ",$fromInvoiceUserAccount->getBillingAddress());
        $this->pdf->setFont("times", "B", 8);
        $this->pdf->MultiCell(285, 10, trim($fromAddressStr), 0, 'L', 0, 0, 10, 285, '', 0, '','', '', '','');
        $this->pdf->MultiCell(285, 10, "Vat No: " . $fromInvoiceUserAccount->getVatNumber(), 0, 'L', 0, 0, 10, 286, '', 0, '','', '', '','');






//        if($userAccountObj->getIsPrepaid() == 1) {
//            $this->pdf->Text(140, $aditionalY + 25, "Invoice Status");
//            $this->pdf->Text(180, $aditionalY + 25, "PAID", false, false, true, 0, 0, 'R');
//        } else {
//            $this->pdf->Text(140, $aditionalY + 25, "Invoice Status");
//            $this->pdf->Text(180, $aditionalY + 25, "UNPAID", false, false, true, 0, 0, 'R');
//        }
    }

    public function oweSummaryInvoiceTemplate($sumaryArray, $invoice_id, $checks)
    {
        // create new PDF document
        $this->pdf = new summaryoweinvoicetemplate($invoice_id, $checks, true, 'UTF-8', false);
        $this->pdf->SetCreator(PDF_CREATOR);
        $this->pdf->SetAuthor('One World');
        $this->pdf->SetTitle('Invoice Pdf');
        $this->pdf->SetSubject('One World Invoice');
        $this->pdf->SetKeywords('invoice, one world, template');
        $this->pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
        $this->pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $this->pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $this->pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $this->pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $this->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $this->pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $PDFfileLocation = SETTING_DIR_REMOTE . '_assets/invoice/pdf/';
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
        }
        $this->pdf->SetFont('times', 'BI', 12);
        $txt = '';

        $output = array();
        $userObj = SessionManager::getUser();
        $fromInvoiceUserAccount = new CustomerAccount($userObj->getUserAccountId());
        $invoiceObj = new Invoices($invoice_id);
        if($invoiceObj->getInvoiceDate() > 0) {
            $date = date('d-m-Y', $invoiceObj->getInvoiceDate());
        } else {
            $date = date('d-m-Y');
        }
        $userAccountObj = new CustomerAccount($invoiceObj->getUserAccountId());
        $count = 1;
        $toUserCurrency = $userAccountObj->getBillingCurrency();
//        $this->pdf = new oweinvoicetemplate($invoice_id, $checks);

        $this->pdf->AddPage();
        $this->pdf->SetAutoPageBreak(TRUE, 0);
        $this->pdf->setFont("times", "B", 8);

        $full_address = $userAccountObj->getBillingAddress();
        $userAddressNew = strtoupper(trim($full_address));
//        $this->pdf->MultiCell(60, 40, $userAddressNew, 0, 'L', 0, 1, 15, 60, true);

        $this->pdf->MultiCell(80, 33, trim($userAddressNew), 0, 'L', 0, 0, 12, 36, true, 0, '', true, '','', '');

        $this->pdf->MultiCell(35, 8, $invoiceObj->getInvoiceNo(), 0, 'C', 0, 0, 125, 43, '', '', '', '', '8','M', true);
        $this->pdf->MultiCell(35, 8, $date, 0, 'C', 0, 0, 160, 43, '', '', '', '', '8','M', true);
        $this->pdf->MultiCell(35, 8, $userAccountObj->getUserAccount(), 0, 'C', 0, 0, 125, 59, '', '', '', '', '8','M', true);
        $this->pdf->MultiCell(35, 8, "30 Days", 0, 'C', 0, 0, 160, 59, '', '', '', '', '8','M', true);

//        $this->pdf->Text(163, 50, $invoiceObj->getInvoiceNo());
//        $this->pdf->Text(163, 57, $date);
//        $this->pdf->Text(163, 64, $userAccountObj->getUserAccount());
//        $this->pdf->Text(163, 71, $count);


        $y = 80;
        $total = 0.00;
        $vatTotal = 0.00;
        $fuelTotal = 0.00;
        foreach ($sumaryArray as $countryId => $serviceArray) {
            $this->pdf->setFont("times", "B", 7);
            $countryObj = new Country($countryId);
            $this->pdf->Text(11, $y, $countryObj->getName());
            $this->pdf->setFont("times", "", 7);
            foreach ($serviceArray as $serviceId => $consignmentArray) {
                $serviceObj = new Services($serviceId);
                $this->pdf->Text(39, $y, $serviceObj->getName()." (" . $serviceObj->getCode() . ")");
                $this->pdf->Text(100, $y, count($consignmentArray));
                $chargesArray = [];
                $consignmentWeight = 0.00;
                foreach ($consignmentArray as $consignmentId => $consignmentChargesArray) {
                    $consignmentObj = new Consignment($consignmentId);
                    $consignmentWeight += $consignmentObj->getChargeWeight();
                    foreach ($consignmentChargesArray as $consignmentChargesId) {
                        $consignmentCharges = new ConsignmentCharges($consignmentChargesId);
                        $consignmentChargesType = new ConsignmentChargesTypes($consignmentCharges->getChargeTypeId());
                        if($consignmentCharges->getCost() > 0) {
                            $chargesArray[$consignmentChargesType->getChargesKey()]["cost"] += $consignmentCharges->getCost();
                            $chargesArray[$consignmentChargesType->getChargesKey()]["title"] = $consignmentChargesType->getTitle();
                        }
                    }
                }
                $this->pdf->Text(127, $y, number_format($consignmentWeight, 3));
                $subTotalCost = 0.00;
                foreach($chargesArray as $key => $charge) {
                    if($key == "VAT") {
                        $vatTotal += $charge['cost'];
                    } else if($key == "FUEL_CHARGES") {
                        $fuelTotal += $charge['cost'];
                    } else {
                        $this->pdf->Text(150, $y, $charge['title']);
                        $this->pdf->Text(180, $y, number_format($charge['cost'], 2) . " " . (($toUserCurrency == "") ? "GBP" : $toUserCurrency));
                        $y = $y + 5;
                        $subTotalCost += $charge['cost'];
                    }
                }
                $total += $subTotalCost;
                $this->pdf->setFont("times", "B", 6);
                $this->pdf->Text(150, $y, "Sub Total ");
                $this->pdf->Text(180, $y, number_format($subTotalCost, 2) . " " . (($toUserCurrency == "") ? "GBP" : $toUserCurrency));
                if($vatTotal > 0) {
                    $this->pdf->Text(200, $y, "*");
                }
                $this->pdf->setFont("times", "", 6);
                $y = $y + 5;
                if ($y >= 255) {
                    $this->pdf->AddPage();
                    $count++;
                    $y = 80;
                    $this->pdf->setFont("times", "B", 8);

                    $this->pdf->MultiCell(80, 33, trim($userAddressNew), 0, 'L', 0, 0, 12, 36, true, 0, '', true, '','', '');

                    $this->pdf->MultiCell(35, 8, $invoiceObj->getInvoiceNo(), 1, 'C', 0, 0, 125, 43, '', '', '', '', '8','M', true);
                    $this->pdf->MultiCell(35, 8, $date, 1, 'C', 0, 0, 160, 43, '', '', '', '', '8','M', true);
                    $this->pdf->MultiCell(35, 8, $userAccountObj->getUserAccount(), 1, 'C', 0, 0, 125, 59, '', '', '', '', '8','M', true);
                    $this->pdf->MultiCell(35, 8, "30 Days", 1, 'C', 0, 0, 160, 59, '', '', '', '', '8','M', true);

//                    $this->pdf->MultiCell(60, 40, $userAddressNew, 0, 'L', 0, 1, 15, 58, true);
//                    $this->pdf->Text(163, 50, $invoiceObj->getInvoiceNo());
//                    $this->pdf->Text(163, 57, $date);
//                    $this->pdf->Text(163, 64, $userAccountObj->getUserAccount());
//                    $this->pdf->Text(163, 71, $count);
                }
            }
            if(!next($sumaryArray)) {
                /*calculate 20% vat*/
                $vatCostTotal = $invoiceObj->getVatableAmount();
                $calculateVat = ($vatCostTotal * 0.20);
                $this->OwesummaryInvoiceFooter($calculateVat, $vatCostTotal,$fuelTotal, $total, $toUserCurrency, $invoiceObj->getUserAccountId(),$invoice_id);
            }
        }
        $this->pdf->Write(0, $txt, '', 0, 'C', true, 0, false, false, 0);

        $PDFfileLocation = SETTING_DIR_REMOTE . '_assets/InvoicesFiles/summary_pdf/';
        if (!file_exists($PDFfileLocation)) {
            mkdir($PDFfileLocation, 0777);
        }
        $fileNameInvoice = $invoiceObj->getInvoiceNo(). "_" . time() . '.pdf';
        $PDF_Filename = $PDFfileLocation . $fileNameInvoice;

        $this->pdf->Output($PDF_Filename, 'F');
        $output['FILENAME'] = $fileNameInvoice;
        $output['LINK'] = $PDF_Filename;

        return $output;



//        $PDFfileLocation = SETTING_DIR_REMOTE . '_assets/InvoicesFiles/pdf/';
//        $fileNameInvoice =  "najam_" . time() . '.pdf';
//        $PDF_Filename = $PDFfileLocation . $fileNameInvoice;
////        $this->pdf->Output($PDF_Filename, 'F');
//        $this->pdf->Output($PDF_Filename, 'I');
//        $PDFfileLocation = SETTING_DIR_REMOTE . '_assets/InvoicesFiles/summary_pdf/';
//        if (!file_exists($PDFfileLocation)) {
//            mkdir($PDFfileLocation, 0777);
//        }
//        $fileNameInvoice = $invoiceObj->getInvoiceNo(). "_" . time() . '.pdf';
//        $PDF_Filename = $PDFfileLocation . $fileNameInvoice;
//
//        $this->pdf->Output($PDF_Filename, 'F');
//        $output['FILENAME'] = $fileNameInvoice;
//        $output['LINK'] = $PDF_Filename;
//
//        return $output;
    }

    public function OwesummaryInvoiceFooter($vatTotal, $vatCostTotal, $fuelTotal, $total, $currency, $accountId, $invoice_id) {
        $userAccountObj = new CustomerAccount($accountId);
        $userBankAccountObj = new InvoiceBankDetails($userAccountObj->getInvoiceBankDetailsId());
        $this->setFont("times", "B", 8);
//        if(!empty($userBankAccountObj->getBankAddress())) {
//            $this->pdf->Text(10, 250, "Bank Datails: " . trim($userBankAccountObj->getBankName()) . " " . trim($userBankAccountObj->getBankAddress()));
//        }
//        if(!empty($userBankAccountObj->getAccountNumber()) && !empty($userBankAccountObj->getAccountSortcode())) {
//            $this->pdf->Text(10, 253, 'Title: ' . trim($userBankAccountObj->getAccountTitle()) . '      Account No: ' . trim($userBankAccountObj->getAccountNumber()) . '       Sort Code: ' . trim($userBankAccountObj->getAccountSortcode()));
//        }

        $vatPercentage = 20;
        $aditionalY = 250;

        $this->pdf->SetLineStyle(array('color' => array(250, 5, 5)));
        $this->pdf->SetLineWidth('0.4');
        $this->pdf->SetTextColor(13, 132, 237);
        $this->pdf->SetFillColor(118, 174, 223  );
        $this->pdf->setFont("times", "B", 17);
        // MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, isHtml=false, autopadding=true, maxh='', valign='', fitcell='')
        $this->pdf->MultiCell(130, 10, "Thank you for your business!", 0, 'C', 1, 0, 10, $aditionalY, false, 0, false, false, '','M', '');
        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->setFont("times", "", 14);
        $this->pdf->MultiCell(35, 10, "Net", 0, 'L', 1, 0, 130, $aditionalY, false, 0, false, false, '','M', '');
        $this->pdf->MultiCell(35, 10, "Vat @ 20%", 0, 'L', 1, 0, 130, $aditionalY + 10, false, 0, false, false, '','M', '');
        $this->pdf->SetTextColor(34, 142, 238);
        $this->pdf->setFont("times", "B", 14);
        $this->pdf->MultiCell(35, 10, "Total", 0, 'L', 1, 0, 130, $aditionalY + 20, false, 0, false, false, '','M', '');

        $userBankAccountObj = new InvoiceBankDetails($userAccountObj->getInvoiceBankDetailsId());
        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->setFont("times", "", 10);
        if(!empty($userBankAccountObj->getBankAddress())) {
            $this->pdf->Text(10, $aditionalY + 15, "Bank Details: " . trim($userBankAccountObj->getBankName()) . " " . trim($userBankAccountObj->getBankAddress()));
        }
        /*if(!empty($userBankAccountObj->getAccountTitle())) {
            $this->pdf->Text(10, $aditionalY + 20, 'Account Name: ' . trim($userBankAccountObj->getAccountTitle()));
        }*/
        if(!empty($userBankAccountObj->getAccountNumber())) {
            $this->pdf->Text(10, $aditionalY + 25, 'Account No: ' . trim($userBankAccountObj->getAccountNumber()));
        }
        if(!empty($userBankAccountObj->getAccountSortcode())) {
            $this->pdf->Text(10, $aditionalY + 30, 'Sort Code: ' . trim($userBankAccountObj->getAccountSortcode()));
        }
//       s $this->pdf->Text(70, $aditionalY + 4, "*VAT Applicable");
//        $this->pdf->Text(13, $aditionalY + 8, "V/C");
//        $this->pdf->Text(25, $aditionalY + 8, "VAT Rate");
//        $this->pdf->Text(45, $aditionalY + 8, "Service Amount");
//        $this->pdf->Text(65, $aditionalY + 8, "VAT Amount");
//        $this->pdf->Text(85, $aditionalY + 8, "VAT ON Surcharge");
//        $this->pdf->Text(13, $aditionalY + 12, "S");
//        $this->pdf->Text(25, $aditionalY + 12, ( number_format($vatPercentage, 2)) . "%");
//        $this->pdf->Text(45, $aditionalY + 12, number_format($vatValue, 2));
//        $this->pdf->Text(65, $aditionalY + 12, number_format($vat_amount, 2));
//        $this->pdf->Text(85, $aditionalY + 12, number_format($vat_on_surcharge, 2));
//        $this->pdf->Text(13, $aditionalY + 16, "Z");
//        $this->pdf->Text(25, $aditionalY + 16, "0.00%");
//        $this->pdf->Text(45, $aditionalY + 16, "0.00");
//        $this->pdf->Text(65, $aditionalY + 16, "0.00");
//        $this->pdf->Text(85, $aditionalY + 16, "0.00");
//        $this->pdf->Text(65, $aditionalY + 20, "Total VAT");
//        $this->pdf->Text(85, $aditionalY + 20, number_format($vat_amount, 2));

        /*
         *THIS IS ME FOR QUERY TERMS
         *QUERYTERMS ARE ABC
         * */
//        $queryTermObj = explode("<br />", nl2br($userAccountObj->getQueryTerm()));
//        if(count($queryTermObj) > 0) {
//            foreach($queryTermObj as $queryTermsKey => $queryTermsTerm) {
//                if(trim($queryTermsTerm) == '')
//                    unset($queryTermObj[$queryTermsKey]);
//            }
//        }
//
//
//        if(count($queryTermObj)>0)
//            $totalText = implode("<br/>",$queryTermObj). "<br/>All goods are carried subject to our conditions of carriage.";
//        else
//            $totalText = "All goods are carried subject to our conditions of carriage.";
//
//        $this->setFont("times", "B", 6);
//        $plusY = 22;
//        $this->pdf->MultiCell(100, 4, $totalText, 0, 'L', 0, 1, 11, $aditionalY + $plusY, true, 0, true);
//
//        $this->pdf->setFont("times", "B", 8);
//        $this->pdf->Text(140, $aditionalY, "Total For Services");
        $this->pdf->setFont("times", "", 13);
//        $this->pdf->Text(180, $aditionalY + 3, number_format($net_amount, 2), false, false, true, 0, 0, 'R');
//        $this->pdf->Text(140, $aditionalY + 5, "Fuel Surcharge");
//        $this->pdf->Text(180, $aditionalY + 5, number_format($fuel_charge, 2), false, false, true, 0, 0, 'R');
//
//        $this->pdf->Text(140, $aditionalY + 10, "Net Total ");
//        $this->pdf->Text(180, $aditionalY + 10, number_format($net_total, 2), false, false, true, 0, 0, 'R');
//        $this->pdf->Text(140, $aditionalY + 15, "VAT  " . (number_format($vatPercentage, 2)) . "% ");
//        $this->pdf->Text(180, $aditionalY + 13, number_format($vat_amount, 2), false, false, true, 0, 0, 'R');
//        if (trim($currency) != '') {
//            $this->pdf->Text(140, $aditionalY + 20, "Total (" . strtoupper($currency) . ")");
//        } else {
//            $this->pdf->Text(140, $aditionalY + 20, "Total (GBP)");
//        }
        $netTotal = $total + $fuelTotal;
        $allTotal = $netTotal + $vatTotal;
        if (trim($currency) != '') {
            $this->pdf->Text(180, $aditionalY + 3, strtoupper($currency) . " " . number_format($netTotal, 2), false, false, true, 0, 0, 'R');
            $this->pdf->Text(180, $aditionalY + 13, strtoupper($currency) . " " . number_format($vatTotal, 2), false, false, true, 0, 0, 'R');
            $this->pdf->Text(180, $aditionalY + 23, strtoupper($currency) . " " . (number_format($allTotal, 2)), false, false, true, 0, 0, 'R');
        } else {
            $this->pdf->Text(180, $aditionalY + 3, "GBP ". number_format($netTotal, 2), false, false, true, 0, 0, 'R');
            $this->pdf->Text(180, $aditionalY + 13, "GBP ". number_format($vatTotal, 2), false, false, true, 0, 0, 'R');
            $this->pdf->Text(180, $aditionalY + 23, "GBP ". (number_format($allTotal, 2)), false, false, true, 0, 0, 'R');
        }
        $this->pdf->line(10, 285, 200, 285);
        $invoiceObj = new Invoices($invoice_id);
        $fromInvoiceUserAccount = new CustomerAccount($invoiceObj->getInvoiceBy());
        $fromAddressStr = $str = str_replace(array("\r","\n"),"",$fromInvoiceUserAccount->getBillingAddress());;
        $this->pdf->setFont("times", "B", 8);
        $this->pdf->MultiCell(285, 10, trim($fromAddressStr), 0, 'L', 0, 0, 10, 285, '', 0, '','', '', '','');













//        $this->pdf->setFont("times", "B", 6);
//        $this->pdf->line(10, 256, 200, 256);
//        $this->pdf->SetLineWidth('0.4');
//        $this->pdf->Rect(10, $aditionalY, 98, 29);
//
//        $this->pdf->Text(33, $aditionalY + 4, "VAT Analysis");
//        $this->pdf->Text(70, $aditionalY + 4, "*VAT Applicable");
//        $this->pdf->Text(13, $aditionalY + 8, "V/C");
//        $this->pdf->Text(25, $aditionalY + 8, "VAT Rate");
//        $this->pdf->Text(45, $aditionalY + 8, "Service Amount");
//        $this->pdf->Text(65, $aditionalY + 8, "VAT Amount");
//        $this->pdf->Text(85, $aditionalY + 8, "VAT ON Surcharge");
//        $this->pdf->Text(13, $aditionalY + 12, "S");
//        $this->pdf->Text(25, $aditionalY + 12, (number_format($vatPercentage, 2)) . "%");
//        $this->pdf->Text(45, $aditionalY + 12, number_format($vatCostTotal, 2));
//        $this->pdf->Text(65, $aditionalY + 12, number_format($vatTotal, 2));
//        $this->pdf->Text(85, $aditionalY + 12, "0.00");
//        $this->pdf->Text(13, $aditionalY + 16, "Z");
//        $this->pdf->Text(25, $aditionalY + 16, "0.00%");
//        $this->pdf->Text(45, $aditionalY + 16, "0.00");
//        $this->pdf->Text(65, $aditionalY + 16, "0.00");
//        $this->pdf->Text(85, $aditionalY + 16, "0.00");
//        $this->pdf->Text(65, $aditionalY + 20, "Total VAT");
//        $this->pdf->Text(85, $aditionalY + 20, number_format($vatTotal, 2));
//
//        /*
//        *THIS IS ME FOR QUERY TERMS
//        *QUERYTERMS ARE ABC
//        * */
//        $queryTermObj = explode("<br />", nl2br($userAccountObj->getQueryTerm()));
//        if(count($queryTermObj) > 0) {
//            foreach($queryTermObj as $queryTermsKey => $queryTermsTerm) {
//                if(trim($queryTermsTerm) == '')
//                    unset($queryTermObj[$queryTermsKey]);
//            }
//        }
//
//        if(count($queryTermObj)>0)
//            $totalText = implode("<br/>",$queryTermObj). "<br/>All goods are carried subject to our conditions of carriage.";
//        else
//            $totalText = "All goods are carried subject to our conditions of carriage.";
//
//        $this->setFont("times", "B", 6);
//        $plusY = 22;
//        $this->pdf->MultiCell(100, 4, $totalText, 0, 'L', 0, 1, 11, $aditionalY + $plusY, true, 0, true);
//
//        $this->pdf->setFont("times", "B", 8);
//        $this->pdf->Text(140, $aditionalY, "Total For Services");
//        $this->pdf->Text(180, $aditionalY, number_format($total, 2), false, false, true, 0, 0, 'R');
//        $this->pdf->Text(140, $aditionalY + 6, "Fuel Surcharge");
//        $this->pdf->Text(180, $aditionalY + 6, number_format($fuelTotal, 2), false, false, true, 0, 0, 'R');
//        $netTotal = $total + $fuelTotal;
//        $this->pdf->Text(140, $aditionalY + 12, "Net Total ");
//        $this->pdf->Text(180, $aditionalY + 12, number_format($netTotal, 2), false, false, true, 0, 0, 'R');
//        $this->pdf->Text(140, $aditionalY + 18, "VAT " . (number_format($vatPercentage,2)) . "% ");
//        $this->pdf->Text(180, $aditionalY + 18, number_format($vatTotal, 2), false, false, true, 0, 0, 'R');
//        if (trim($currency) != '') {
//            $this->pdf->Text(140, $aditionalY + 24, "Total (" . strtoupper($currency) . ")");
//        } else {
//            $this->pdf->Text(140, $aditionalY + 24, "Total (GBP)");
//        }
//        $allTotal = $netTotal + $vatTotal;
//        $this->pdf->Text(180, $aditionalY + 24, (number_format($allTotal, 2)), false, false, true, 0, 0, 'R');
    }

}
