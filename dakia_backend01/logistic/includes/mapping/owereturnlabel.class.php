<?php
class OweReturnLabel {

    private $pdf;
    private $pdf2;

    const FONT_SMALL = 8;
    const FONT_MEDIUM = 11;
    const FONT_LARGE = 14;
    const FONT_EXTRA_LARGE = 16;
    //
    CONST LINE_VERY_NARROW = 1.5;
    CONST LINE_NARROW = 2.5;
    CONST LINE_MEDIUM = 3.5;
    CONST LINE_WIDE = 5;
    //
    const FONT_FAMILY = "helvetica";
    //
    const MARGIN_LEFT = 0.75;
    const MARGIN_TOP = 0.75;
    const MARGIN_LEFT_WIDE = 8;
    //
    const BARCODE_HEIGHT = 31;
    //
    const WIDTH = 120;

    public $hermes_barcode_display = "";

    /*     * *
     * Create instance
     */

    public function __construct() {
        // echo 'asdas'; exit;
        $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf = $pdf;
        //$this->pdf2 = $pdf2;
    }

    /*     * *
     * Add consignment to label
     */

    public function AddConsignment(Consignment $consignment) {
        $this->country = new Country($consignment->getCountryId());
        $parcel_list = $consignment->getParcels();
        $parcel_count = sizeof($parcel_list);
        $parcel_idx = 0;
        foreach ($parcel_list as $parcel) {

            $licence_plate = (trim($parcel->getTrackingNumber())== ''? $consignment->getHawb() : trim($parcel->getTrackingNumber()));//$consignment->getHawb();
 
            $this->pdf->SetPrintFooter(false);
            $this->pdf->SetHeaderMargin(0);
            $this->pdf->SetFooterMargin(0);
            $this->pdf->SetAutoPageBreak(false, 0);
            $page_size = array(100, 150);
            $this->pdf->AddPage("P", $page_size);
            $awb = $this->addWayBill($consignment, $parcel_idx, $licence_plate, $parcel_count);
            // Add Boxes
            // all further labels will need to be on new page.
            $new_page_flag = true;
            ++$parcel_idx;
        }
      
        $fileName = $this->getFullPath($consignment->getId().".pdf",$consignment->getId());
        $this->pdf->Output($fileName, "F");
        $returnData['status'] = "SUCCESS";
        $fileName = str_replace("../","",$fileName);
        $returnData['label'] = SETTING_MAIN_URL . $fileName;
        return $returnData;
    }

    private function addWayBill(Consignment $consignment, $parcel_idx, $licence_plate, $parcel_count) {
        // Get current user
        $user = SessionManager::getUser();
        
        $this->pdf->setFont("helvetica", "L", 7);
        $userImage = User::getUserCompanyImages(true,$user->getId());

        if ($userImage != '') {
            $image = $userImage;
            $img = explode(".", $userImage);
            $y = 5;
            $x = 50;
            $w = 40;
            $h = 35;
            $this->pdf->image($image, $x, $y, $w, $h, strtoupper($img[1]), '', '', false, 700, '', false, false, 0, '', false, false);
        }

        $this->pdf->line(3, 58, 98, 58);
        $this->pdf->line(3, 3, 98, 3);
        $this->pdf->line(98, 3, 98, 23.8);
        $this->pdf->line(3, 3, 3, 30);
        $this->pdf->line(41, 3, 41, 23.8);

        $this->pdf->setFont("helvetica", "B", 11);
        if ($consignment->getAccount() != "OWEAIRC")
            $this->pdf->Text(5, 8, "    RYP");
        else
            $this->pdf->Text(5, 8, "AIR CONNECT");

        $this->pdf->setFont("helvetica", "b", 8);
        $this->pdf->Text(5, 62, "NOTE:");
        if ($consignment->getAccount() != "OWEAIRC") {
            $this->pdf->Text(20, 60, "Please take your parcel to nearest post office and ");
            $this->pdf->Text(20, 64, "get receipt as proof of dispatched.");
        }
        $this->pdf->setFont("helvetica", "b", 13);
        $this->pdf->Text(30, 72, "Delivery Details:");
        $this->pdf->Text(11, 136.5, $this->country->getIso());
        $this->pdf->setFont("helvetica", "L", 9);
        $pieces = str_pad($parcel_idx + 1, 3, "0", STR_PAD_LEFT);
        $this->addBarcode($licence_plate, 7, 25);
        $this->addUPSBarcode1($consignment->getHawb(), 3, 115);

        $replace_prefix = "";
        $text = $licence_plate;
        $barcode = "";
        $barcode_prefix = $replace_prefix;


        $barcode_sufffix = $consignment->getCountryIsoCode();
        $barcode = $text;




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
        $address = "";
        $company = "";
        


        $this->pdf->setFont("helvetica", "L", 11);
        //  $this->pdf->Text(55, 81, $consignment->getHawb());
        $this->pdf->Text(5, 81, $consignment->getSenderCompany());
        //$this->pdf->Text(55, 85, "Email: ".$consignment->getEmail());
        $this->pdf->Text(5, 85, $consignment->getSenderName());
        $this->pdf->Text(5, 89, $consignment->getSenderAddressLine1());
        $this->pdf->Text(5, 93, $consignment->getSenderAddressLine2());
        $this->pdf->Text(5, 97, $consignment->getSenderAddressLine3());
        $this->pdf->Text(5, 101, $consignment->getSenderCity());
        $this->pdf->Text(5, 105, $consignment->getSenderPostcode());
        
        $this->pdf->setFont("helvetica", "B", 11);
        $senderCountry = new Country($consignment->getSenderCountryId());
        $this->pdf->Text(5, 109, $senderCountry->getName());

        if ($parcel_count > 0) {
            $this->pdf->setFont("helvetica", "b", 13);
            $this->pdf->Text(33, 136.5, "Pcs : " . ($parcel_idx + 1) . "/" . $parcel_count);
        }
        

        $this->pdf->setFont("helvetica", "", 9);
      //  $this->pdf->Text(60, 134, ucfirst(strtolower($consignment->getNotes())));
        $this->pdf->line(57, 133, 57, 146);
        
        $this->pdf->setFont("helvetica", "b", 10);
        $consignmentUser = new User($consignment->getUserId());
        $accountObj = new CustomerAccount();
        $searchConsignmentAccountId = 0;
        $accountSubAccountArr = $accountObj->getSubAccountsArrayShowConsignmentAccount($consignmentUser->getUserAccountId(),$searchConsignmentAccountId);
        $consignmentAccountId = $consignmentUser->getUserAccountId();
        $consignmentAccount = $accountObj->showAccount($consignmentAccountId,$accountSubAccountArr,$searchConsignmentAccountId,true);
        $userAccountObj = new CustomerAccount($consignmentAccount);
        
        
        $this->pdf->Text(60, 136.5, $userAccountObj->getUserAccount());
        return $licence_plate;
    }

    private function addBarcode($code, $x, $y, $h = self::BARCODE_HEIGHT) {

        $style = array(
            'position' => '',
            'align' => 'C',
            'stretch' => true,
            'fitwidth' => true,
            'cellfitalign' => '55',
            'border' => false,
            'hpadding' => '7',
            'vpadding' => '7',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255),
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 15,
            'stretchtext' => 1
        );


        $this->pdf->write1DBarcode($code, 'C128', $x, $y, '', 40, 2, $style, '');
    }

    private function addUPSBarcode1($text, $x, $y, $h = self::BARCODE_HEIGHT) {
        // barcode file
        $file = microtime();
        $file = str_replace("0.", "", $file);
        $file = str_replace(" ", "_", $file);

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

        $this->pdf->write1DBarcode($text, 'C128', $x, $y, '', 20, 2, $style, 'Y');






        /*
          $file = LabelFormatting::createBarcodeUsingPearLib($text, $file, "code128");

          if ($file != "")
          {
          // imaage has few millimetres of white space either side of barcode and below
          // - therefore shift left slightly.
          // - add extra height8
          // barcode number displayed below image
          // - add extra to height.
          $this->pdf->Image($file, $x, 325, '', 80);
          if (file_exists($file)) unlink ($file);  // remove file after use
          }
         */
    }

    public function getFullPath($fileName,$consignmentId) {
        $path = "../_assets/pdf/return/" . date("Y_m_d", time()) . "/".$consignmentId."/";

        if (!file_exists($path))
            @mkdir($path, 0777,true);

        return $path . $fileName;
    }

}
