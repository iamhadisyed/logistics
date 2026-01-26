<?php

class ManifestBrief {

    private $pdf;

    /*     * *
     * Create instance
     */

    public function __construct() {

        $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf = $pdf;
    }

    public function Header() {
        $this->pdf->Ln(30);
        $this->pdf->Cell(15, 10, "ID", 1, 0, 'C');
        $this->pdf->Cell(37, 10, "Tracking NO", 1, 0, 'C');
        $this->pdf->Cell(45, 10, "Name", 1, 0, 'C');
        $this->pdf->Cell(30, 10, "Country", 1, 0, 'C');
        $this->pdf->Cell(13, 10, "Weight", 1, 0, 'C');
        $this->pdf->Cell(45, 10, "Cust Ref", 1, 0, 'C');
        //	$this->Cell(30,10,"Bar Code",1,0,'C');    
    }

    public function AddHTML($consignment, $mid) {

        $user = SessionManager::getUser();
        // set default monospaced font
        $this->pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        // set margins
        $this->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $this->pdf->SetFont('dejavusans', '', 8);
        // add a page
        $this->pdf->AddPage();
        $i = 0;

        $image = User::getUserCompanyImages(false, $user->getId()); //realpath("../images/logo.jpg");
        $fitbox = 'C';
        $fitbox[1] = 'M';
        $x = 40;
        $y = 9;
        $w = 25;
        $h = 20;

        $img = explode(".", $image);
        //$this->pdf->Image($image, 20, 5, $w, $h, $img[1], '', '', false, 700, '', false, false, 0, $fitbox, false, false);

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
            'text' => false,
            'font' => 'helvetica',
            'fontsize' => 8,
            'stretchtext' => 1
        );
        $this->pdf->write1DBarcode($mid, 'C128', 155, 5, 120, 15, 0.5, $style);

        $this->pdf->text(170, 20, $mid);
        $this->pdf->SetFont('dejavusans', 'B', 10);

        $this->pdf->text(70, 20, "Parcel Manifest Summary");

        $this->pdf->SetFont('dejavusans', 'B', 6);
        $this->pdf->text(5, 30, " ID", false, false, true, 1);
        $this->pdf->text(10, 30, "          TRACKING NO", false, false, true, 1);
        $this->pdf->text(45, 30, "   CONTACT NAME", false, false, true, 1);
        $this->pdf->text(70, 30, " CITY/COUNTRY", false, false, true, 1);
        $this->pdf->text(90, 30, "  WGT.(Kg)", false, false, true, 1);
        $this->pdf->text(105, 30, "   VALUE", false, false, true, 1);
        $this->pdf->text(120, 30, "              DESCRIPTION", false, false, true, 1);
        $this->pdf->text(160, 30, "              ORDER NUMBER", false, false, true, 1);

        $pagecount = 0;

        $height = 6;
        $y = 9;
        $max_pagecount = 47;

        foreach ($consignment as $c) {
            $country = new Country($c->getCountryId());
            $pagecount++;
            $y = $this->pdf->GetY();
            $i++;
            $contact = $c->getContact();
            if ($contact == "")
                $contact = $c->getCompany();
            $this->pdf->MultiCell(5, $height, $i, 1, 'L', 0, 0, 5, $y + $height, true, 0, false, true, 0, 'M');


            //$this->pdf->SetFont('arial', '', 7);
            //$this->pdf->SetXY(15, $y + 10);
            //$this->pdf->Cell(40, 10, '', '1');
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
           // $this->pdf->write1DBarcode($c->getAwb(), 'C128', 17, $y + 11, 35, 5, 0.5, $style);
           
            //$this->pdf->text(22, $y + 17, $c->getAwb());
            $this->pdf->SetFont('arial', '', 6);
            $this->pdf->MultiCell (35, $height, $c->getAwb(), 1, 'L', 0, 0, 10, $y+$height, true, 0, false, true,0, 'M');
            $this->pdf->MultiCell(25, $height, $contact, 1, 'L', 0, 0, 45, $y + $height, true, 0, false, true, 0, 'M');
            $this->pdf->MultiCell(20, $height, $c->getCity().",".$country->getIso(), 1, 'L', 0, 0, 70, $y + $height, true, 0, false, true, 0, 'M');
            $this->pdf->MultiCell(15, $height, number_format($c->getWeight(), 2), 1, 'L', 0, 0, 90, $y + $height, true, 0, false, true, 0, 'M');
            $this->pdf->MultiCell(15, $height, number_format($c->getValue(), 2)." ".$c->getCurrency(), 1, 'L', 0, 0, 105, $y + $height, true, 0, false, true, 0, 'M');
            $this->pdf->MultiCell(40, $height, substr($c->getDescription(), 0, 60), 1, 'L', 0, 0, 120, $y + $height, true, 0, false, true, 0, 'M');
            $this->pdf->MultiCell(40, $height, $c->getHawb(), 1, 'L', 0, 0, 160, $y + $height, true, 0, false, true, 0, 'M');


            if ($pagecount == $max_pagecount) {
                $this->pdf->AddPage();
                $pagecount = 0;
            }
        }
        $filename = time() . "briefmanifest_summary.pdf";
        $filepath = "../_assets/manifest/pdf/" . date("Y-m-d") . "/";
        if (!file_exists($filepath))
            @mkdir($filepath, 0777, true);
        $fileNameNew = $filepath . $filename;

        $this->pdf->Output($fileNameNew, 'F');
        return $fileNameNew;


        // return $pdfpath;
    }

}
