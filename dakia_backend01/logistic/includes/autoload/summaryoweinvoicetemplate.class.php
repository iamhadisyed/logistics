<?php
ob_start();

class summaryoweinvoicetemplate extends TCPDF {

    const BORDER_OFF = 0;
    const BORDER_FRAME = 1;
    const END_POS_NEXT_LINE = 2;
    const END_POS_TO_RIGHT = 0;
    const FOOTER = "";

    private $pageNumber = 1;
    private $pdf;
    private $firstpager = 0;
//	private $y = 105;

    private $show_main_header_flag = true;
    public $userCompany = '';
    public $userAddress = '';
    public $userCountry = '';
    public $userAccount = '';
    public $userName = '';
    public $vatChargable = '';
    public $vatValue = '';
    public $invoice = '';
    public $checks = array();

    /**
     * Generate full pdf
     */

    public function __construct($invoice_id, $checks) {
        parent::__construct();
        $this->invoice = new Invoices($invoice_id);
        $this->checks = $checks;
    }

    public function Header() {
        $usercClassObj = new User();
        $logo = $usercClassObj->getUserCompanyImages();
        $fromInvoiceUserAccount = new CustomerAccount($this->invoice->getInvoiceBy());
        $this->image($logo, 10, 7, 40, 30, '', '', '', false, 700, '', false, false, 0, $fitbox, false, false);
        $this->setFont("times", "B", 8);
        $this->Cell(0, 20, trim($fromInvoiceUserAccount->getBillingAddress()), 0, false, 'R', 0, '', 0, false, 'T', 'M');
        $this->SetLineStyle(array('color' => array(250, 5, 5)));
        $this->line(10, 18, 195, 18);
        $this->SetLineStyle(array('color' => array(0, 0, 0 )));
        $phone = "";
        if($fromInvoiceUserAccount->getTelephone() != "") {
            $phone .= 'Tel: '.$fromInvoiceUserAccount->getTelephone();
        }
        if($fromInvoiceUserAccount->getphone() != "") {
            $phone .= '   Fax: '.$fromInvoiceUserAccount->getphone();
        }
        $this->Cell(0, 32, trim($phone), 0, false, 'R', 0, '', 0, false, 'T', 'M');

        $this->setFont("times", "B", 21);
        $this->SetTextColor(34, 142, 238);
        $this->Text(160, 25, "INVOICE");
        $this->SetTextColor(0, 0, 0);

//        $this->SetLineWidth('0.0');
//        $this->Rect(12, 23, 80, 43,'','',array(34, 142, 238));
        $this->line(12, 33, 92, 33);
        $this->setFont("times", "B", 8);
        $this->setCellPaddings(3, 3, 3, 3);
        $this->SetTextColor(255, 255, 255);
        $this->SetFillColor(34, 142, 238);
        $this->MultiCell(80, 10, 'Bill TO', 0, 'L', 1, 0, 12, 23, '', '', '', '', '','C', '');
        $this->SetTextColor(0, 0, 0);
//        $fromInvoiceUserAccount = new CustomerAccount($this->invoice->getInvoiceBy());
//        $this->MultiCell(80, 33, trim($fromInvoiceUserAccount->getBillingAddress()), 0, 'L', 0, 0, 12, 33, true, 0, '', true, '','', '');

        $date = "";
        if( $this->invoice->getInvoiceDate() > 0) {
            $date = date('d-m-Y',  $this->invoice->getInvoiceDate());
        } else {
            $date = date('d-m-Y');
        }
        $invoiceUserAccount = new CustomerAccount($this->invoice->getUserAccountId());
        $UserAccount = new CustomerAccount($this->invoice->getInvoiceBy());
        $this->SetTextColor(255, 255, 255);
        $this->MultiCell(35, 8, 'INVOICE #', 0, 'C', 1, 0, 125, 35, '', '', '', '', '8','M', true);
        $this->MultiCell(35, 8, 'DATE', 0, 'C', 1, 0, 160, 35, '', '', '', '', '8','M', true);
        $this->SetTextColor(0, 0, 0);
//        $this->MultiCell(35, 8, 'DATE', 1, 'L', 0, 0, 125, 43, '', '', '', '', '8','M', true);
//        $this->SetTextColor(0, 0, 0);
//        $this->MultiCell(35, 8, $date, 1, 'L', 0, 0, 160, 43, '', '', '', '', '8','M', true);
        $this->SetTextColor(255, 255, 255);
        $this->MultiCell(35, 8, 'CUSTOMER ID', 0, 'C', 1, 0, 125, 51, '', '', '', '', '8','M', true);
        $this->MultiCell(35, 8, "TERMS", 0, 'C', 1, 0, 160, 51, '', '', '', '', '8','M', true);
        $this->SetTextColor(0, 0, 0);
//        $this->MultiCell(35, 8, 'PAGE NO', 1, 'L', 1, 0, 125, 59, '', '', '', '', '8','M', true);
//        $this->SetTextColor(0, 0, 0);
//        $this->MultiCell(35, 8, '1', 1, 'L', 0, 0, 160, 59, '', '', '', '', '8','M', true);

        $this->setFont("times", "B", 7);
        $this->SetTextColor(255, 255, 255);
        $this->MultiCell(30, 7, 'Country', 0, 'L', 1, 0, 10, 68, '', '', '', '', '8','M', true);
        $this->MultiCell(100, 7, 'Service', 0, 'L', 1, 0, 39,68, '', '', '', '', '8','M', true);
        
        $this->MultiCell(30, 7, 'No of Shipment', 0, 'L', 1, 0, 100,68, '', '', '', '', '8','M', true);
        $this->MultiCell(30, 7, 'Total Weight(KG)', 0, 'L', 1, 0, 127,68, '', '', '', '', '8','M', true);
        $this->MultiCell(30, 7, 'Type of charges', 0, 'L', 1, 0, 150, 68, '', '', '', '', '8','M', true);
        $this->MultiCell(15, 7, 'Charges(GBP)', 0, 'L', 1, 0, 180, 68, '', '', '', '', '8','M', true);
        
        
        
        $this->SetLineWidth('0.1');
        $this->line(10, 75, 200, 75);
    }

    public function Footer() {
        $this->setFont("times", "B", 8);
        $invoiceUserAccount = new CustomerAccount($this->invoice->getUserAccountId());
        $UserAccount = new CustomerAccount($this->invoice->getInvoiceBy());

        $paymentTermObj = explode("<br />", nl2br($invoiceUserAccount->getPaymentTerm()));
        if(count($paymentTermObj) > 0) {
            $str = "";
            foreach($paymentTermObj as $paymentTerm) {
                $str .= trim($paymentTerm) . " ";
            }
            $vatNumber = (!empty($UserAccount->getVatNumber()) ? " VAT No. " . $UserAccount->getVatNumber() : '' );
//            $this->Text(12, 288, trim($str) . $vatNumber);
        }
//        $this->Text(10, 288, trim('One World Express Inc. Ltd One World House, Pump Ln, Hayes UB3 3NB, United Kingdom'));
    }

}
