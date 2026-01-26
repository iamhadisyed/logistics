<?php 
ob_start(); 
class summaryinvoicetemplate extends TCPDF {

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

        $this->Text(128, 71, "PAGE NO.");

        $this->setFont("times", "B", 7);
        $this->Text(11, 95, "Country");
        $this->Text(39, 95, "Service");
        $this->Text(100, 95, "No of Shipment");
        $this->Text(127, 95, "Total Weight(KG)");
        $this->Text(150, 95, "Type of charges");
        $this->Text(180, 95, "Charges(GBP)");

        $usercClassObj = new User();
        $logo = $usercClassObj->getUserCompanyImages();
        //$this->image($logo, 145, 7, 50);
        $fitbox = 'C';
        $fitbox[1] = 'M';
        $this->image($logo, 145, 7, 40, 30, '', '', '', false, 700, '', false, false, 0, $fitbox, false, false);
        

        $this->SetLineWidth('0.5');
        $this->Rect(12, 48, 80, 40);
        $this->line(12, 58, 92, 58);


        $this->Rect(125, 48, 70, 28);
        $this->line(125, 55, 195, 55);
        $this->line(125, 62, 195, 62);
        $this->line(125, 69, 195, 69);
        $this->line(160, 48, 160, 76);

        $this->SetLineWidth('0.1');
        $this->line(10, 100, 200, 100);
    }

    public function Footer() {
        $this->setFont("times", "B", 8);
        $invoiceUserAccount = new CustomerAccount($this->invoice->getUserAccountId());
        $paymentTermObj = explode("<br />", nl2br($invoiceUserAccount->getPaymentTerm()));
        if(count($paymentTermObj) > 0) {
            $str = "";
            foreach($paymentTermObj as $paymentTerm) {
                $str .= trim($paymentTerm) . " ";
            }
            $vatNumber = (!empty($invoiceUserAccount->getVatNumber()) ? " VAT No. " . $invoiceUserAccount->getVatNumber() : '' );
            $this->Text(12, 288, trim($str) . $vatNumber);
        }
    }

}
