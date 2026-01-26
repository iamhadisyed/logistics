<?php

ob_start();
include_classes([
    'services.class'
]);

class ManifestSummaryReport {

    const SERVICE_LIST_X = 10;
    const SERVICE_LIST_Y = 50;
    const LINE_HEIGHT = 5;
    const LEFT_MARGIN = 20;
    const ROWS_FIRST_PAGE = 1; //23;
    const ROWS_PAGE = 40;
    const START_Y = 25;
    const END_Y = 260;

    private $pdf;
    private $service_list = array();
    private $consignment_list = array();
    protected $diff_array = array();
    private $handling;
    private $pageNumber = 1;
    private $firstpager = 0;
    private $manifestid;
    private $show_main_header_flag = true;

    /**
     * Generate full pdf
     */
    public function __construct() {
        $this->pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    }

    public static function buildPDFDocuments($consignment, $mid) {

        //$this = new ManifestSummaryReport('P', 'mm', 'A4');

        $this->createBookingDocs('', "../images/");
        if (count($consignment) > 0) {
            ManifestSummaryReport::$manifestid = $mid;
            $this->addSummaryAccount($consignment, self::START_Y, true);
            // Output PDF
            $filename = time() . "manifestreport.pdf";
            $fileNameNew = "../_assets/manifest/" . $filename;
            $this->Output($fileNameNew, 'F');
            if ($mid != "") {
                $manifest = new Manifest($mid);
                $manifest->setPdfFile($fileNameNew);
                $manifest->Save();
            }
            //return $filename;
            return $this->pdf->Output($fileNameNew, 'I');
        }
    }

    public function SavePDFFile($consignment, $mid, $pdffile = '', $type = "", $sessionUser = "", $iossManifest=false) {
        $page_format = 'A4';
        if (count($consignment) > 0) {
            if (is_array($consignment))
                $this->addSummaryAccount($consignment, $mid, $type, $sessionUser, $iossManifest);
            else
                $this->addSummaryAccount(array($consignment), $mid, $type, $sessionUser, $iossManifest);
            // Output PDF
            if (trim($pdffile) == '') {
                $filename = time() . "bag_summary.pdf";
                $filepath = "../_assets/manifest/pdf/" . date("Y-m-d") . "/";
                if (!file_exists($filepath))
                    @mkdir($filepath, 0777, true);
                $pdffile = $filepath . $filename;
            } else
                $pdffile;
            $this->pdf->Output($pdffile, "F");
            return $pdffile;
        }
    }

    /**
     * Override Header method to show consistent header on each page.
     *
     */
    public function Header() {


        if ($this->show_main_header_flag) {
            $this->pdf->SetFont('Arial', '', 10);
            // Logo
            /* if(trim($user->getLogo())!= ''){

              $this->pdf->Image('../images/userlogo/'.$user->getLogo(),10,10,53,20);
              } else { */
            //$this->pdf->Image('../images/logo.jpg',6,10,53,20);
            //}
            // Right hand header
            $this->pdf->SetFont('Arial', 'B', 13);
            $this->pdf->SetTextColor(25, 25, 112);

            $this->pdf->SetFont('Arial', '', 10);
            $this->pdf->SetTextColor(0, 0, 0);
            $this->pdf->Text(77, 20, Translation::GetCaption("END_OF_DAY_MANIFEST"));
            $this->pdf->Text(10, 37, Translation::GetCaption("DATE") . " : ");
            $this->pdf->Text(25, 37, $today = date("d.M.y"));


            /*
              $style = array(
              //'position' => '',
              'align' => 'C',
              //'stretch' => false,
              'fitwidth' => true,
              //'cellfitalign' => '',
              //'border' => true,
              //'hpadding' => 'auto',
              //'vpadding' => 'auto',
              'fgcolor' => array(0, 0, 0),
              //'bgcolor' => false, //array(255,255,255),
              'text' => true,
              'font' => 'helvetica',
              'fontsize' => 8,
              'stretchtext' => 4
              );

             */

            $this->pdf->Text(125, 37, Translation::GetCaption("END_OF_DAY_MANIFEST") . " ID: ");

            //return;

            $this->pdf->Text(180, 37, $this->manifestid);

            $manifest = new Manifest($this->manifestid);

            if ($manifest->getId() > 0) {
                $type = $manifest->getType();

                if ($type != '')
                    $this->pdf->Text(90, 37, $type . " Manifest");
            }


            //$this->pdf->write1DBarcode(ManifestSummaryReport::$manifestid, 'C128', 180, 37, '', 10, 0.2, $style, 'N');

            $this->pdf->Text(180, 37, $this->manifestid);
            //$imagecn = '../images/barcode/' . uniqid() . '.png';
            //$this->write1DBarcode(ManifestSummaryReportBarcode::$manifestid, 'C128', 180, 37, '', 10, 0.2, $style, 'N');
            //$barCode = $this->generatebarcode(ManifestSummaryReport::$manifestid, $imagecn);
            //$this->Image($topBarCode,16,43,80,8);
            ///$this->Image($barCode, 155, 10, 30, 10);

            $this->AddPageNumber();
            $this->pdf->Ln(30);
            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->Cell(30, 10, Translation::GetCaption("SHIPMENT_DETAILS"), 1, 0, 'C');
            $this->pdf->Cell(37, 10, Translation::GetCaption("CONSIGNEE"), 1, 0, 'C');
            $this->pdf->Cell(30, 10, Translation::GetCaption("SENDER"), 1, 0, 'C');
            $this->pdf->Cell(30, 10, Translation::GetCaption("SERVICE"), 1, 0, 'C');
            $this->pdf->Cell(13, 10, Translation::GetCaption("WEIGHT"), 1, 0, 'C');
            $this->pdf->Cell(45, 10, Translation::GetCaption("DESCRIPTION"), 1, 0, 'C');
            //	$this->pdf->Cell(30,10,"Bar Code",1,0,'C');    
        }

        $this->pdf->SetDrawColor(0, 0, 0); // Hot Pink
        $this->pdf->SetLineWidth(1); // We will change the line width now to 2mm
        $this->pdf->Rect(5, 5, 200, 285, 'D');
    }

    /**
     * General footer
     *
     */
    public function Footer() {
        // Booking header
        $this->pdf->SetY(-15);
        //Select Times italic 8
        $this->pdf->SetFont('Arial', 'I', 8);
        //Print centered page number
    }

    public function AddPageNumber() {
        $this->pdf->Text(175, 285, "Page No: " . $this->pdf->pageNumber);
        $this->pdf->pageNumber++;
    }

    public function setIncludeHeaderFlag($includeFlag=true) {
        $this->show_main_header_flag = $includeFlag;
    }

    private function setBookingHeader() {
        // Booking header
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->setTextColor(255, 255, 255);
        $this->pdf->setDrawColor(204, 204, 204);
        $this->pdf->SetFillColor(29, 104, 198);
        $this->pdf->Cell(190, 5, 'Manifest Summary', 1, 2, 'C', true);
    }

    private function AddRow($c, $x, $y, $user = "") {

        // Asked Kazim and he said the manifest 3rd column conatain details of account who generated the manifest
        if (!empty($user))
            $this->user = $user;
        else
            $this->user = SessionManager::getUser();
        $parentaccountManifestCrested = '';

        $userAccount = new CustomerAccount($this->user->getUserAccountId());
        $parentId = $userAccount->getParentId();
        $accountCompany = $userAccount->getCompany();
        if ($parentId > 0) {
            $parentUserAccount = new CustomerAccount($parentId);
            $parentaccount = $parentUserAccount->getUserAccount();
        }

        $yLength = 10;
        $xLength = 35;
        $columnHeight = 3.5;
        $this->pdf->SetFont('Arial', '', 6);

        if ($c->getServiceId() != @$serviceId) {

            $subTotal_Weight = 0;
            $subTotal_Pieces = 0;

            $this->pdf->SetFont('Arial', '', 6);

            @$i++;

            $this->pdf->SetFont('Arial', '', 7);

            $number = "";


            @$total_number_pieces += $c->getNumberPieces();
            @$total_weight += $c->getWeight();


            $subTotal_Weight += $c->getWeight();
            $subTotal_Pieces += $c->getNumberPieces();


            $contact = "";

            $contact = trim($c->getContact());
            if ($contact == "" || $contact == "-")
                $contact = $c->getCompany();



            $service = new Services($c->getServiceId());
            $servicename = $service->getName();

            $this->pdf->MultiCell(37, $columnHeight, "Hawb:" . @$c->getHawb() . "\n" . "Reference:" . @(trim($c->getReference()) == '' ? ' N/A' : $c->getReference()) . "\n" . "Account:" . @ $userAccount->getAccount() . "\n" . "Service:" . $servicename, 0, 'L', false, 1, $x, $y);

            $x += $xLength + 2;

            $contact = $c->getContact();
            if ($c->getContact() == '')
                $contact = $c->getCompany();

            //Get countryu name from consignment
            $country = new Country($c->getCountryId());
            $countryName = $country->getName();
            $addressDetail = "";

            if (!empty($contact))
                $addressDetail .= $contact;

            if (!empty($c->getAddressLine1()))
                $addressDetail .= "\n" . cleanCsvCall($c->getAddressLine1());

            if (!empty($c->getAddressLine2()))
                $addressDetail .= cleanCsvCall($c->getAddressLine2());

            if (!empty($c->getCity()))
                $addressDetail .= "\n" . cleanCsvCall($c->getCity());

            if (!empty($c->getPostCode()))
                $addressDetail .= ", " . cleanCsvCall($c->getPostCode());

            if (!empty($countryName))
                $addressDetail .= "\n" . cleanCsvCall($countryName);


            $this->pdf->MultiCell(30, $columnHeight, $addressDetail, 0, 'L', false, 1, $x + 2, $y);


            $current_y = $this->pdf->GetY();
            $current_x = $this->pdf->GetX();

            $this->pdf->SetXY($x, $y);

            $current_y = $this->pdf->GetY();
            $current_x = $this->pdf->GetX();

            $date = "";

            $multiCellHeight1 = $this->pdf->GetY() - $current_y;

            if ($c->getDateBooked() != "" && $c->getDateBooked() > 0)
                $date = date("Y-m-d", $c->getDateBooked());
            else
                $date = date("Y-m-d", $c->getDateLabelCreated());

            if ($date == "1970-01-01" || $date == "" || $date == "0")
                $date = date("Y-m-d");

            $x += $xLength;

            $dateBooked = "";
            $this->pdf->MultiCell(40, $columnHeight, "Corporate Acc: " . (trim(@$parentaccount) == '' ? 'N/A' : $parentaccount) . "\n"
                    . "Scan By: " . $this->user->getUserName() . "\n"
                    . "Company: " . $userAccount->getCompany() . "\n"
                    . "Contact Name: " . $userAccount->getFullName(), 0, 'L', false, 1, $x, $y);


            $x += $xLength + 5;

            $service = new Services($c->getServiceId());

            $servicename = $service->getName();

            $this->pdf->MultiCell(10, $columnHeight, $c->getParcelWeight() . "\n Kg", 0, 'L', false, 1, $x, $y);

            $x += 12;

            $this->pdf->MultiCell(34, $columnHeight, "Pieces:" . $c->getTotalParcel() . "\n"
                    . "Description of Goods:" . "\n" . cleanCsvCall($c->getDescription()) . "\n"
                    . "Value for Customs: " . $c->getValue() . " " . $c->getCurrency(), 0, 'L', false, 1, $x, $y);

            $this->pdf->SetXY($x + $xLength, $y);

            $x = $this->pdf->GetX();
            $y = $this->pdf->GetY();
            $myY = $y;
            $style = array(
                'position' => '',
                'align' => 'C',
                'stretch' => false,
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
                'stretchtext' => 4
            );
            $this->pdf->write1DBarcode(@$c->getAwb(), 'C128', $x, $myY + 3, 30, 15, 0.5, $style, 'T');
            $this->pdf->MultiCell(34, $columnHeight, "\n\n\n", 0, 'L', false, 1, $x, $y + 10);




            // $this->pdf->MultiCell(50,3.5,"Pieces:".$c->getNumberPieces()."\n"."Description of Goods:"."\n".$c->getDescription()."\n"."Value for Customs: ". $c->getValue()." ".$c->getCurrency()."\n"."Date: ". $date."\n"."Reference: ".$c->getReference(),0,'L');
            $current_x2 = $this->pdf->GetX();
            $current_y2 = $this->pdf->GetY();

            $multiCellHeight2 = $this->pdf->GetY() - $current_y;

            $this->pdf->SetX($x);
            $this->pdf->SetY($y);

            if ($multiCellHeight2 > $multiCellHeight1) {
                $row_height = $multiCellHeight2;
            } else {
                $row_height = $multiCellHeight1;
            }
            $row_height = $row_height + 6;
            $this->pdf->Cell(38, $row_height, '', 1, 'L');
            $this->pdf->Cell(32, $row_height, '', 1, 'L');
            //$this->pdf->SetXY($current_x - 30, $current_y);
            $this->pdf->Cell(42, $row_height, '', 1, 'L');

            //$this->pdf->SetXY($current_x, $current_y);

            $this->pdf->Cell(10, $row_height, '', 1, 'L');

            $this->pdf->Cell(35, $row_height, '', 1, 'L');

            $this->pdf->MultiCell(35, $row_height, '', 1, 'L');
        }
    }

   
    private function addSummaryAccount($consignmentlist, $mid, $type = "", $sessionUser = "", $iossManifest=false) {
        if (!empty($sessionUser))
            $user = $sessionUser;
        else
            $user = Sessionmanager::getUser();
        $total_number_pieces = 0;
        $total_weight  = 0;
        $this->pdf->Text(20, 40, $mid);
        if (sizeof($consignmentlist) > 0) {
            $i = 0;
            $this->pdf->SetPrintHeader(false);
            $this->pdf->SetPrintFooter(false);
            $this->pdf->SetFooterMargin(0);
            $this->pdf->SetAutoPageBreak(false, 0);
            $this->pdf->SetFont('Arial', 'B', 12);
            $startY = 30;
            $this->pdf->AddPage();
            $this->pdf->SetX(10);
            $this->pdf->SetY($startY);
            $image = User::getUserCompanyImages(false, $user->getId()); //realpath("../images/logo.jpg");
            $fitbox = 'C';
            $fitbox[1] = 'M';
            $this->pdf->image($image, 8, 4, 40, 25, '', '', '', false, 700, '', false, false, 0, $fitbox, false, false);
            $x = $this->pdf->GetX();
            $y = $this->pdf->GetY();
//            $this->pdf->write1DBarcode($mid, 'C128', 90,260, 120, 20, 0.5, $style, 'Y');
            $this->pdf->write1DBarcode($mid, 'C128', 172, 8, 30, 15, 0.5, @$style, 'Y');
            $this->pdf->SetFont('Arial', '', 6);
            $this->pdf->Text(172, 23, $mid);
            $this->pdf->SetFont('Arial', 'B', 12);
            if ($type == "return") {
                $this->pdf->Text(90, 15, 'End Of Day Manifest [RYP]');
            }
            else if($iossManifest){
                $this->pdf->Text(90, 15, 'End Of Day Manifest [IOSS]');
            }
            else {
                $this->pdf->Text(90, 15, 'End Of Day Manifest');
            }
            $this->pdf->SetFont('Arial', '', 10);
            $this->pdf->Text(100, 20, formatDate(date("Y-m-d")));
            $this->pdf->SetX(10);
            $this->pdf->SetY($startY);
            foreach ($consignmentlist as $c) {
                $x = $this->pdf->GetX();
                $y = $this->pdf->GetY();
                if ($i == 10) {
                    $this->pdf->AddPage();
                    $this->pdf->SetX(10);
                    $this->pdf->SetY($startY);
                    $x = $this->pdf->GetX();
                    $y = $this->pdf->GetY();
                    $i = 0;
                }
                $total_number_pieces += $c->getNumberPieces();
                $total_weight += $c->getWeight();
                $this->AddRow($c, $x, $y, $user);
                $i++;
            }
            $this->pdf->SetFont('Arial', 'B', 10);
            $this->pdf->Text(140, 280, Translation::GetCaption("MANIFEST_TOTAL_WEIGHT") . ": " . number_format($total_weight, 3));
            $this->pdf->Text(140, 285, Translation::GetCaption("MANIFEST_TOTAL_PIECES") . " : " . $total_number_pieces);
        }
    }

    public function createBookingDocs($consignment_list, $image_folder) {

        // get basket       
        $this->consignment_list = $consignment_list;
        $this->image_folder = $image_folder;
        // Need to set any information required for header, before calling add page.
        // - header written by the add page method
        // Order page
        // show the billing details
        // Booking page
//        $this->MultiCell(1, 5, "\n\n", GlOrderPdf::BORDER_OFF);
        // build booking header
        // $this->setBookingHeader();
    }

    private function makeUTF8($str, $encoding = "") {
        $str = preg_replace('/[^(\x20-\x7F)]*/', '', $str);
        $str = str_replace('&', 'and', $str);
        $str = str_replace('<', '&lt;', $str);
        $str = str_replace('>', '&gt;', $str);
        $str = str_replace("'", "", $str);

        if ($str !== "") {
            if (empty($encoding) && self::isUTF8($str))
                $encoding = "UTF-8";
            if (empty($encoding))
                $encoding = mb_detect_encoding($str, 'UTF-8, ISO-8859-1');
            if (empty($encoding))
                $encoding = "ISO-8859-1"; //  if charset can't be detected, default to ISO-8859-1
            return $encoding == "UTF-8" ? $str : @mb_convert_encoding($str, "UTF-8", $encoding);
        }
    }

    function isUTF8($str) {
        return preg_match('%^(?:
         [\x09\x0A\x0D\x20-\x7E]           # ASCII
       | [\xC2-\xDF][\x80-\xBF]            # non-overlong 2-byte
       | \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
       | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
       | \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
       | \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
       | [\xF1-\xF3][\x80-\xBF]{3}         # planes 4-15
       | \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
   )*$%xs', $str);
    }

}
