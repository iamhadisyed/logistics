<?php

ob_start();

class creditNoteFullTemplate extends TCPDF {

    const BORDER_OFF = 0;
    const BORDER_FRAME = 1;
    const END_POS_NEXT_LINE = 2;
    const END_POS_TO_RIGHT = 0;
    const FOOTER = "";

    private $pageNumber = 1;
    private $pdf;
    private $firstpager = 0;
    //private $y = 105;

    private $show_main_header_flag = true;
    public $userCompany = '';
    public $userAddress = '';
    public $userCountry = '';
    public $userAccount = '';
    public $userName = '';
    public $vatChargable = '';
    public $vatValue = '';
    private $credit_note = '';


    public function __construct($credit_note_id) {
        parent::__construct();
        $this->credit_note = new CreditNote($credit_note_id);
    }

    public function Header() {
        $fromCreditUserAccount = new CustomerAccount($this->credit_note->getCreditNoteBy());
        $address = explode("<br />", nl2br($fromCreditUserAccount->getBillingAddress()));
        $this->setFont("times", "B", 8);
        $yAddress = 10;
        if(count($address) > 0) {
            foreach($address as $ad) {
                $this->Text(10, $yAddress, strtoupper(trim($ad)));
                $yAddress = $yAddress + 4;
            }
        }
        if(!empty($fromCreditUserAccount->getTelephone())) {
            $this->Text(10, $yAddress, "Tel: " . $fromCreditUserAccount->getTelephone());
            $yAddress = $yAddress + 4;
        }
        if(!empty($fromCreditUserAccount->getTelephone())) {
            $this->Text(10, $yAddress, "Fax: " . $fromCreditUserAccount->getTelephone());
            $yAddress = $yAddress + 4;
        }
        if(!empty($fromCreditUserAccount->getWebsiteLink())) {
            $this->Text(10, $yAddress, "Web: " . $fromCreditUserAccount->getWebsiteLink());
            $yAddress = $yAddress + 4;
        }
        if(!empty($fromCreditUserAccount->getBillingEmail())) {
            $this->Text(10, $yAddress, "Email: " . $fromCreditUserAccount->getBillingEmail());
        }

        $this->setFont("times", "B", 21);
        $this->Text(80, 20, "CREDIT NOTE");

        $this->setFont("times", "B", 8);
        $this->Text(15, 52, "CREDIT TO");

        $this->Text(128, 50, "CREDIT NO");
        $this->Text(128, 57, "DATE");
        $this->Text(128, 64, "ACCOUNT");
        // $this->Text(128, 71,"USER CURRENCY");
        
        $this->setFont("times", "B", 10);
        $this->Text(10, 92,$this->credit_note->getCreditNoteHeading(), false, false, true, 0, 0, 'C');


        $this->setFont("times", "B", 7);
        $this->Text(13, 100, "Order Ref");
        //$this->Text(39, 95,"Date");
        $this->Text(40, 100, "Reference");
        $this->Text(65, 100, "Description");
        //$this->Text(97, 95,"Serv Code");
        //$this->Text(120, 95,"Type");
        //$this->Text(131, 95,"Weight");
        //$this->Text(141, 95,"Charged Amt");
        $this->Text(140, 100, "Charged Amt");
        $this->Text(160, 100, "Actual Amt");
        $this->Text(180, 100, "Credit Amt");


        $usercClassObj = new User();
        $logo = $usercClassObj->getUserCompanyImages();
        //$this->image($logo, 145, 7, 50);
        $fitbox = 'C';
        $fitbox[1] = 'M';
        $this->image($logo, 145, 7, 40, 30, '', '', '', false, 700, '', false, false, 0, $fitbox, false, false);

        $this->SetLineWidth('0.5');
        $this->Rect(12, 48, 80, 40);
        $this->line(12, 58, 92, 58);


        $this->Rect(125, 48, 70, 22);
        $this->line(125, 55, 195, 55);
        $this->line(125, 62, 195, 62);
        // $this->line(125,69,195,69);
        //  $this->line(125,76,195,76);
        $this->line(160, 48, 160, 70);

        $this->SetLineWidth('0.1');
        $this->line(11, 104, 198, 104);
    }

    public function Footer() {
        $this->setFont("times", "B", 8);
        //$this->Text(12, 290,"Any inqueries on this invoice should be notified in writting within 15 days from date of invoice VAT No. 720 542 861");
    }

    public function invoiceFooter($TotalGrossValue = '', $parcelTotalNetWeight = '', $TotalGrossWeight = '', $currency = '', $y, creditNoteFullTemplate $PdfObj) {
        $currency = $this->userCurrency;
        $vatChargable = $this->vatChargable;
        $vatValue = $this->vatValue;
        $invoice_sub_total = @number_format($_SESSION['credit_note_data']['C_AMOUNT'], 2);
        $invoice_vatable_total = @number_format($_SESSION['credit_note_data']['VAT_AMOUNT'], 2);
        $invoice_vat = @number_format($_SESSION['credit_note_data']['VAT_AMOUNT'], 2);

        $y = $y + 11;

        $aditionalY = 276;

        $PdfObj->line(10, 274, 200, 274);
        $PdfObj->SetLineWidth('0.4');
        $y = $y + 1;
        $PdfObj->setFont("times", "", 7);
        $PdfObj->Text(11, $aditionalY + 2, "Payment terms strictly immediate from Date of Invoice");
        $PdfObj->Text(11, $aditionalY + 6, "Any queries on this invoice should be notifies in writing within 15 days from date of invoice ");
        $PdfObj->Text(11, $aditionalY + 11, "VAT Registration No 720 542 861");
        // echo "<pre>";
        // print_r($_SESSION);
        $credit_sub_total = number_format($_SESSION['credit_note_data']['C_AMOUNT'], 2);
        $credit_vat = number_format($_SESSION['credit_note_data']['C_VAT_AMOUNT'], 2);
        $credit_net_total = number_format($_SESSION['credit_note_data']['C_TOTAL_AMOUNT'], 2);


        $PdfObj->setFont("times", "", 8);
        $PdfObj->Text(167, $aditionalY + 2, "Net Total : ");
        $PdfObj->Text(180, $aditionalY + 2, $credit_sub_total, false, false, true, 0, 0, 'R');
        $PdfObj->Text(173, $aditionalY + 6, "VAT : ");
        $PdfObj->Text(180, $aditionalY + 6, $credit_vat, false, false, true, 0, 0, 'R');
        $PdfObj->setFont("times", "B", 8);
        $PdfObj->Text(155, $aditionalY + 10, "Credit Total: (" . $_SESSION['credit_note_data']['C_CURRENCY'] . ") : ");
        $PdfObj->Text(180, $aditionalY + 10, $credit_net_total, false, false, true, 0, 0, 'R');
        $invoiceNumber = $_SESSION['CURRENT_CREDIT_NUMBER'];
    }

    public function AddHTML($IsSaveToPDF, $Account, $consignment, $count, $data = array()) {

        $this->userCurrency = $data['userCurrency'];
        $this->userAccount = $data['userAccount'];
        $this->userCountry = $data['userCountry'];
        $this->userAddress = $data['userAddress'];
        $this->userCompany = $data['userCompany'];
        $this->userName = $data['userName'];
        $this->vatChargable = $data['vatchargable'];
        $this->vatValue = $data['vatvalue'];

        $page_format = array(210, 300);
        $PdfObj = new creditNoteFullTemplate('P', 'mm', $page_format);
        //$PdfObj->SetPrintHeader(false);
        $PdfObj->AddPage();
        $PdfObj->SetAutoPageBreak(TRUE, 0);
        // instantiate PDF creation class
        $PdfObj->setFont("times", "B", 8);
        // $PdfObj->Text(15, 60,$this->userName);
        // $PdfObj->Text(15, 64,$this->userCompany);
        // $userAddressNew	=	explode(',',$this->userAddress);

        //$PdfObj->Text(15, 58, $this->userCompany);
        $userAddressNew = $this->userAddress; //explode(',',$this->userAddress);
        $PdfObj->MultiCell(60, 40, $userAddressNew, 0, 'L', 0, 1, 15, 58, true);

        /* 	
          foreach($userAddressNew as $addKey=>$addValue)
          {
          if($addKey == '0')
          $PdfObj->Text(15, 68,$addValue);
          if($addKey == '1')
          $PdfObj->Text(15, 72,$addValue);
          if($addKey == '2')
          $PdfObj->Text(15, 76,$addValue);
          if($addKey == '3')
          $PdfObj->Text(15, 80,$addValue.' '.@$userAddressNew[4].' '.@$userAddressNew[5].' '.@$userAddressNew[6]);
          } */

        // $PdfObj->Text(15, 84,$this->userCountry);
        $PdfObj->Text(15, 84, Country::nameCountry($this->userCountry));

        $PdfObj->Text(163, 50, 'CRN' . $_SESSION['CURRENT_CREDIT_NUMBER']);
        $PdfObj->Text(163, 57, $_SESSION['credit_note_data']['C_DATE']);
        $PdfObj->Text(163, 64, $this->userAccount);
        //  $PdfObj->Text(163, 71,$this->userCurrency);
        //$PdfObj->Text(163, 78,1);	


        $y = 105;

        $reference = @$_SESSION['credit_note_data']['C_REFERENCE'];
        $credit_description = $_SESSION['credit_note_data']['C_DESCRIPTION'];
        $credit_amount = (float) $_SESSION['credit_note_data']['C_AMOUNT'];
        "<br>";
        $invoice_amount = @(float) $_SESSION['credit_note_data']['I_AMOUNT'];

        /*
          $PdfObj->setFont("times", "B", 6);
          $PdfObj->Text(13, $y,''); // HAWb NUMBER
          $PdfObj->Text(40, $y,isset($reference)?$reference:'');
          $PdfObj->Text(65, $y,$credit_description);
          $PdfObj->Text(140, $y,number_format($invoice_amount,2));
          $PdfObj->Text(160, $y,number_format(($invoice_amount-$credit_amount),2));
          $PdfObj->Text(180, $y,number_format($credit_amount,2));
          //	$PdfObj->Text(195+5, $y,$vatInfo);
          $y = $y+5; */


        $PdfObj->setFont("times", "B", 10);
        $PdfObj->Text(10, 92, $_SESSION['credit_note_data']['C_DESCRIPTION'], false, false, true, 0, 0, 'C');


        $PdfObj->setFont("times", "", 7);
        if (trim($_SESSION['CURRENT_CREDIT_NUMBER']) != '') {
            $invoiceFilterDetail = new CreditNoteDetailsFilter();
            $invoiceFilterDetail->addFieldFilter('credit_id', $_SESSION['CURRENT_CREDIT_NUMBER']);
            $getInvoiceDetails = $invoiceFilterDetail->getlist();

            if (count($getInvoiceDetails) > 0) {
                foreach ($getInvoiceDetails as $invoiceDetailData) {
                    if (trim($invoiceDetailData->getHawb()) != '')
                        $PdfObj->Text(13, $y, $invoiceDetailData->getHawb());
                    //if(trim($invoiceDetailData->getDateBooked()) != '0000-00-00' && trim($invoiceDetailData->getDateBooked()) != '1970-01-01')
                    //$PdfObj->Text(48, $y,date('d m Y', strtotime($invoiceDetailData->getDateBooked())));
                    //$PdfObj->Text(40, $y,$invoiceDetailData->getReference());
                    //$PdfObj->Text(98, $y,'');
                    //$this->Text(97, 95,"Serv Code");
                    //$PdfObj->Text(148, $y,"");

                    $PdfObj->Text(140, $y, number_format($invoiceDetailData->getInvoiceAmount(), 2));
                    $PdfObj->Text(160, $y, number_format(($invoiceDetailData->getInvoiceAmount() - $invoiceDetailData->getCreditAmount()), 2));
                    $PdfObj->Text(180, $y, number_format($invoiceDetailData->getCreditAmount(), 2));

                    if ($invoiceDetailData->getIsVatable() == 'YES')
                        $PdfObj->Text(195, $y, '*');
                    //$PdfObj->Text(65, $y,$invoiceDetailData->getDescription());
                    $PdfObj->MultiCell(25, 40, $invoiceDetailData->getReference(), 0, 'L', 0, 1, 40, $y, true);
                    $PdfObj->MultiCell(65, 40, $invoiceDetailData->getDescription(), 0, 'L', 0, 1, 65, $y, true);
                    $y = $y + 7;
                }
            }
        }
        $y = $y + 10;




        $PdfObj->setFont("times", "B", 6);
        //if($_SESSION['counter']==($_SESSION['sizeOfArr'])-1)
        {
            $y = $y + 5;

            $this->invoiceFooter('', '', '', '', $y, $PdfObj);
        }

        $PDFfileLocation = dirname(__FILE__) . '/credit_notes/temp/';
        $PDFfileLocation = str_replace('includes/autoload/', '', $PDFfileLocation);

        $PDFfilePrefix = "ttcreditNotes_";
        $PDF_Filename = $PDFfileLocation . $PDFfilePrefix . '_' . date('Y_m_d') . '_' . uniqid() . '_0.pdf';
        $PdfObj->Output($PDF_Filename, 'F');
        //$_SESSION['counter'] = $counter;
        echo $PDF_Filename;
        ;
        return $PDF_Filename;
    }

}
