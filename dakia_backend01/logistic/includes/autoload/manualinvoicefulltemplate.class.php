<?php 
ob_start();
class manualInvoiceFullTemplate extends TCPDF {

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
    
    public function __construct($invoice_id) {
        parent::__construct();
        $this->invoice = new Invoices($invoice_id);
    }

    public function Header() {
        $fromInvoiceUserAccount = new CustomerAccount($this->invoice->getInvoiceBy());
        $address = explode("<br />", nl2br(trim($fromInvoiceUserAccount->getBillingAddress())));
        $this->setFont("times", "B", 8);
        $yAddress = 10;
        if(count($address) > 0) {
            foreach($address as $ad) {
                $this->Text(10, $yAddress, strtoupper(trim($ad)));
                $yAddress = $yAddress + 4;
            }
        }
        if(!empty($fromInvoiceUserAccount->getTelephone())) {
            $this->Text(10, $yAddress, "Tel: " . $fromInvoiceUserAccount->getTelephone());
            $yAddress = $yAddress + 4;
        }
        if(!empty($fromInvoiceUserAccount->getTelephone())) {
            $this->Text(10, $yAddress, "Fax: " . $fromInvoiceUserAccount->getTelephone());
            $yAddress = $yAddress + 4;
        }
        if(!empty($fromInvoiceUserAccount->getWebsiteLink())) {
            $this->Text(10, $yAddress, "Web: " . $fromInvoiceUserAccount->getWebsiteLink());
            $yAddress = $yAddress + 4;
        }
        if(!empty($fromInvoiceUserAccount->getBillingEmail())) {
            $this->Text(10, $yAddress, "Email: " . $fromInvoiceUserAccount->getBillingEmail());
        }
        $this->setFont("times", "B", 21);
        $this->Text(90, 20, "INVOICE");

        $this->setFont("times", "B", 8);
        $this->Text(15, 52, "INVOICE TO");

        $this->Text(128, 50, "INVOICE NO");
        
        $this->Text(128, 57, "DATE");
       
        $this->Text(128, 64, "ACCOUNT");

//        $this->Text(128, 71, "PAGE NO.");

        $this->setFont("times", "B", 7);
        $this->Text(11, 95, "Order Ref");
        if($this->checks['check_box_date'] == 1) {
            $this->Text(39, 95, "Label Created");
        }
        if($this->checks['check_box_reference'] == 1) {
            $this->Text(56, 95, "Reference");
        }
        if($this->checks['check_box_destination'] == 1) {
            $this->Text(85, 95, "Destination");
        }
        if($this->checks['check_box_service'] == 1) {
            $this->Text(114, 95, "Service");
        }
        $this->Text(135, 95, "Weight(KG)");
        $this->Text(150, 95, "Pcs");
        $this->Text(155, 95, "Basic Chrg");
        $this->Text(173, 95, "Add. Chrg");
        $this->Text(192, 95, "Total");

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
        $this->line(160, 48, 160, 70);

        $this->SetLineWidth('0.1');
        $this->line(10, 100, 200, 100);
        
        $this->setFont("times", "B", 10);
        $this->Text(10, 90, $this->invoice->getInvoiceHeading(), false, false, true, 0, 0, 'C');
    }

    public function invoiceFooter($TotalGrossValue = '', $parcelTotalNetWeight = '', $TotalGrossWeight = '', $currency = '', $y, manualInvoiceFullTemplate $PdfObj) {
        $currency = $this->userCurrency;
        $vatChargable = $this->vatChargable;
        $vatValue = $this->vatValue;
        $invoice_sub_total = @number_format($_SESSION['invoice_data']['MN_AMOUNT'], 2);
        $invoice_vatable_total = @number_format($_SESSION['invoice_data']['VAT_AMOUNT'], 2);
        $invoice_vat = @number_format($_SESSION['invoice_data']['VAT_AMOUNT'], 2);

        $y = $y + 11;

        $aditionalY = 268;

        $PdfObj->line(10, 268, 201, 268);
        $PdfObj->SetLineWidth('0.4');
        $y = $y + 1;
        $PdfObj->setFont("times", "", 7);
        $PdfObj->Text(11, $aditionalY + 5, "Payment terms strictly immediate from Date of Invoice");
        $PdfObj->Text(11, $aditionalY + 10, "Any queries on this invoice should be notified in writing within 15 days from date of invoice ");
        $PdfObj->Text(11, $aditionalY + 15, "VAT Registration No 720 542 861");
        if ($this->userCountry == 'GB') {
            //	$PdfObj->Text(11,$aditionalY+11,"R B S:   Sort code: 16-13-18  Account No.: 11437074 ");
            //$PdfObj->Text(11,$aditionalY+14,"Sort code: 16-13-18");
            //$PdfObj->Text(11,$aditionalY+17,"Account No.: 11437074");
        }

        // echo "<pre>";
        // print_r($_SESSION);
        $credit_sub_total = number_format($_SESSION['invoice_data']['MN_AMOUNT'], 2);
        $credit_vat = number_format($_SESSION['invoice_data']['MN_VAT_AMOUNT'], 2);
        $credit_net_total = number_format($_SESSION['invoice_data']['MN_TOTAL_AMOUNT'], 2);


        $PdfObj->setFont("times", "", 8);
        $PdfObj->Text(167, $aditionalY + 5, "Net Total : ");
        $PdfObj->Text(180, $aditionalY + 5, $credit_sub_total, false, false, true, 0, 0, 'R');
        $PdfObj->Text(172, $aditionalY + 10, "VAT : ");
        $PdfObj->Text(180, $aditionalY + 10, $credit_vat, false, false, true, 0, 0, 'R');
        $PdfObj->setFont("times", "B", 8);
        $PdfObj->Text(153, $aditionalY + 15, "Invoice Total: (" . $this->userCurrency . ") : ");
        $PdfObj->Text(180, $aditionalY + 15, $credit_net_total, false, false, true, 0, 0, 'R');

        $lineYaditionalY = $aditionalY + 20;
        $PdfObj->line(10, $lineYaditionalY, 201, $lineYaditionalY);
        $invoiceNumber = 'MNI' . $_SESSION['CURRENT_INVOICE_NUMBER'];
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
        $PdfObj = new manualInvoiceFullTemplate('P', 'mm', $page_format);
        //$PdfObj->SetPrintHeader(false);
        $PdfObj->AddPage();
        $PdfObj->SetAutoPageBreak(TRUE, 0);
        // instantiate PDF creation class
        $PdfObj->setFont("times", "B", 8);
        // $PdfObj->Text(15, 60,$this->userName);
        //$PdfObj->Text(15, 58, $this->userCompany);
        $userAddressNew = $this->userAddress; //explode(',',$this->userAddress);
        $PdfObj->MultiCell(60, 40, $userAddressNew, 0, 'L', 0, 1, 15, 62, true);
        /* foreach($userAddressNew as $addKey=>$addValue)
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

        $PdfObj->Text(15, 84, Country::nameCountry($this->userCountry));

        $PdfObj->Text(163, 50, 'MNI' . $_SESSION['CURRENT_INVOICE_NUMBER']);
        $PdfObj->Text(163, 57, date('d M Y', strtotime($_SESSION['invoice_data']['MN_DATE'])));
        $PdfObj->Text(163, 64, $this->userAccount);
        //$PdfObj->Text(163, 71,$this->userCurrency);
        //$PdfObj->Text(163, 71,$_SESSION['invoice_data']['MN_HEADING']);
        $PdfObj->setFont("times", "B", 10);
        $PdfObj->Text(10, 92, $_SESSION['invoice_data']['MN_HEADING'], false, false, true, 0, 0, 'C');
        // $PdfObj->Text(163, 78,1);	


        $y = 105;

        $reference = @$_SESSION['invoice_data']['MN_REFERENCE'];
        $credit_description = @$_SESSION['invoice_data']['MN_DESCRIPTION'];
        $credit_amount = (float) $_SESSION['invoice_data']['MN_AMOUNT'];
        $invoice_amount = @(float) $_SESSION['invoice_data']['I_AMOUNT'];

        $PdfObj->setFont("times", "", 7);
        if (trim($_SESSION['CURRENT_INVOICE_NUMBER']) != '') {
            $invoiceFilterDetail = new InvoicesManualDetailsFilter();
            $invoiceFilterDetail->addFieldFilter('invoice_id', $_SESSION['CURRENT_INVOICE_NUMBER']);
            $getInvoiceDetails = $invoiceFilterDetail->getlist();

            if (count($getInvoiceDetails) > 0) {
                foreach ($getInvoiceDetails as $invoiceDetailData) {
                    $PdfObj->Text(12, $y, $invoiceDetailData->getHawb());
                    if ($invoiceDetailData->getDateBooked() != '0000-00-00 00:00:00')
                        $PdfObj->Text(48, $y, date('d m Y', strtotime($invoiceDetailData->getDateBooked())));
                    $PdfObj->Text(60, $y, $invoiceDetailData->getReference());
                    $PdfObj->Text(98, $y, $invoiceDetailData->getDestination());
                    //$this->Text(97, 95,"Serv Code");
                    $PdfObj->Text(148, $y, "");
                    $PdfObj->Text(168, $y, number_format($invoiceDetailData->getWeight(), 3));
                    $PdfObj->Text(189, $y, number_format($invoiceDetailData->getAmount(), 2));
                    if ($invoiceDetailData->getIsVat() == 'YES')
                        $PdfObj->Text(205, $y, '*');

                    $PdfObj->Text(12, $y + 5, $invoiceDetailData->getDescription());
                    $y = $y + 10;
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

        $PDFfileLocation = dirname(__FILE__) . '/manual_invoice/temp/';
        $PDFfileLocation = str_replace('includes/autoload/', '', $PDFfileLocation);

        $PDFfilePrefix = "ttinvoice_";
        $PDF_Filename = $PDFfileLocation . $PDFfilePrefix . '_' . date('Y_m_d') . '_' . uniqid() . '_0.pdf';
        $PdfObj->Output($PDF_Filename, 'F');
        //$_SESSION['counter'] = $counter;
        return $PDF_Filename;
    }

}
