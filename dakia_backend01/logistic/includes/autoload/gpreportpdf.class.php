<?php

ob_start();

class GPReportPDF extends TCPDF {

    const BORDER_OFF = 0;
    const BORDER_FRAME = 1;
    const END_POS_NEXT_LINE = 2;
    const END_POS_TO_RIGHT = 0;
    const FOOTER = "";
    public $accountReport;
    public $userAccountId;
    public $dateFrom;
    public $dateTo;
    public $countData;
    
 
    /**
     * Generate full pdf
     */
    public function Header() {

        $this->setFont("times", "B", 8);
        $user = SessionManager::getUser();
        $userAccount = new CustomerAccount($user->getUserAccountId());
        $parentId = $userAccount->getParentId();
        $accountCompany = $userAccount->getCompany();
        if ($parentId > 0) {
            $parentUserAccount = new CustomerAccount($parentId);
            $parentaccount = $parentUserAccount->getUserAccount();
        }
        $this->Text(10, 10, strtoupper($accountCompany));
        $this->MultiCell(60, 40,  $userAccount->getBillingAddress(), 0, 'L', 0, 1, 10, 14, true);
        $this->Text(10, 28, "Tel: " . $userAccount->getTelephone());
        $this->Text(10, 32, "Web: " . $userAccount->getWebsiteLink());
        $this->Text(10, 36, "Email: " . $userAccount->getBillingEmail());
       // $this->Text(10, 40, "Fax: " . $userAccount->getWebsiteLink());
       $image = User::getUserCompanyImages(); //realpath("../images/logo.jpg");
        $this->image($image, 145, 7, 50);

        $this->Text(147, 40, "From: " . date('d-m-Y', strtotime($this->dateFrom))." - To: ".date('d-m-Y', strtotime($this->dateTo)));
    }
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
    public static function generateReportPdf($accountConsignmentData , $countData, $accountReport,$userAccountId, $dateFrom,$dateTo ) {
        $page_format = array(210, 300);
        $pdfObj = new GPReportPDF('P', 'mm', $page_format, true, 'UTF-8');
        $pdfObj->accountReport  =   $accountReport;
        $pdfObj->userAccountId  =   $userAccountId;
        $pdfObj->dateFrom       =   $dateFrom;
        $pdfObj->dateTo         =   $dateTo;
        $pdfObj->countData      =   $countData;
        $pdfObj->AddPage();
        $pdfObj->SetAutoPageBreak(TRUE, 0);
        $pdfObj->setFont("times", "B", 8);
        $y = 60;
        $pdfObj->setHeading();
        foreach ($accountConsignmentData as $keyConsignmentRecodeIds => $consignmentItemData) {
            $totalBasicCharges = $totalAgentCharges = $marign = $profit = 0;
            if ($pdfObj->accountReport)
                $pdfObj->Text(10, $y, $consignmentItemData->getServiceName());
            else
                $pdfObj->Text(10, $y, $consignmentItemData->getAccount());

            $totalBasicCharges = $consignmentItemData->getBasicCharges() + $consignmentItemData->getFuelCharges();
            $totalAgentCharges = $consignmentItemData->getAgentBasicCharges()+$consignmentItemData->getAgentFuelCharges();
            $marign = ($totalBasicCharges) - ( $totalAgentCharges );
           
            if ($marign > 0 && $totalAgentCharges > 0)
                $profit = (($marign / $totalAgentCharges) * 100);

            $pdfObj->Text(75, $y, $consignmentItemData->getShipmentCount());
            $pdfObj->Text(90, $y, number_format($consignmentItemData->getWeight(), 3));
            $pdfObj->Text(105, $y, $consignmentItemData->getNumberPieces());
            $pdfObj->Text(120, $y, number_format($consignmentItemData->getBasicCharges(), 2));
            $pdfObj->Text(135, $y, number_format($consignmentItemData->getFuelCharges(), 2));
            $pdfObj->Text(150, $y, number_format($consignmentItemData->getAgentBasicCharges(), 2));
            $pdfObj->Text(165, $y, number_format($consignmentItemData->getAgentFuelCharges(), 2));
            $pdfObj->Text(180, $y, number_format($marign, 2));
            $pdfObj->Text(195, $y, number_format($profit, 2));

            if ($y >= 245) {
                $pdfObj->AddPage();
                $count++;
                $y = 60;

                $pdfObj->setHeading();
            }

            $y = $y + 5;
        }

        $fileName = 'gp-report-'.time() . '.pdf';
        return $pdfObj->Output($fileName, 'D');
    }

    public function setHeading() {

        $selectedAccount = new CustomerAccount($this->userAccountId);
        $this->setFont("times", "B", 9);
        $yHeading = 50;
        if ($this->accountReport)
            $this->Text(10, $yHeading, "Service Name (".$selectedAccount->getUserAccount().")");
        else
            $this->Text(10, $yHeading, "Account  (".$selectedAccount->getUserAccount().")");

        $this->Text(75, $yHeading, "Shpt");
        $this->Text(90, $yHeading, "Kg");
        $this->Text(105, $yHeading, "Pce");
        $this->Text(120, $yHeading, "Income");
        $this->Text(135, $yHeading, "FSS");
        $this->Text(150, $yHeading, "Cost");
        $this->Text(165, $yHeading, "FSS");
       // $this->Text(180, $yHeading, "LNH");
        $this->Text(180, $yHeading, "MRG");
        $this->Text(195, $yHeading, "Profit%");
        $this->setFont("times", "N", 8);
    }

}
