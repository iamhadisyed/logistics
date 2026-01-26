<?php

class CTTExpressManifest {

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

    /*     * *
     * Create instance
     */

    public function __construct() {
        $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf = $pdf;
    }

    /*     * *
     * Add consignment to label
     */

    public function AddConsignment($consignment) {
        
        if(count($consignment) > 0) {
            $this->pdf->SetPrintFooter(false);
            $this->pdf->SetFooterMargin(0);
            $this->pdf->SetAutoPageBreak(false, 0);

            $page_size = array(210, 300);

            $this->pdf->AddPage("P", $page_size);
            $this->addWayBill($consignment);

            $new_page_flag = true;
            $filename = time() . "manifestreport.pdf";
            $fileNameNew = "../_assets/manifest/pdf/" . $filename;
            $this->pdf->Output($fileNameNew, 'F');
            $output['file_path'] = $fileNameNew;
            $output['FILE'] = $filename;
            $output['STATUS'] = true;
            $output['MESSAGE'] = "pad generated successfully";
        } else {
            $output['STATUS'] = false;
            $output['MESSAGE'] = "No consignment found for pdf";
        }
        return $output;
        /* $this->pdf->Output("../_assets/pdf/".date('Y_m_d').'/cttManifest_'.date("Y-m-dH:i:s").".pdf", "I");
          return "SUCCESS||".SETTING_MAIN_URL."_assets/pdf/".date('Y_m_d').'/cttManifest_'.date("Y-m-dH:i:s").".pdf||".$licence_plate_array[0]; */
    }

    private function addWayBill($consignment_list) {

        $shipment_count = count($consignment_list);
        $first_awb = $consignment_list[0]->getAwb();
        $last_awb = $consignment_list[$shipment_count - 1]->getAwb();
        $this->pdf->SetFont('Times', '', 9);
        $user = Sessionmanager::getUser();

        $this->pdf->Image('../images/ctt_logo.JPG', 3, 10, 53, 20);
        $this->pdf->SetFont('Times', '', 6);
        $this->pdf->SetTextColor(0, 0, 0);

        $this->pdf->Text(57, 6, "CTT Expresso");
        $this->pdf->Text(57, 9, "SERVIÇOS POSTAIS E DE LOGÍSTICA, S.A.");
        $this->pdf->Text(57, 12, "LICENCE No. 4940/2000");
        $this->pdf->Text(57, 15, "Tax ID No.: 504 520 296");
        $this->pdf->Text(57, 18, "SHARE CAPITAL: 5,000,000 EURO");
        $this->pdf->Text(57, 21, "Edifício CTT EXPRESSO - MARL");
        $this->pdf->Text(57, 24, "Lugar do Quintanilho");
        $this->pdf->Text(57, 27, "2664-500 SÃO JULIÃO TOJAL");
        $this->pdf->Text(57, 30, "www.cttexpresso.pt");

        $this->pdf->Image('../images/ctt_quality.png', 95, 12, 40);
        $this->pdf->SetFont('Times', 'I', 12);
        $this->pdf->Text(140, 12, "Certificate of Acceptance");
        $this->pdf->Text(160, 20, "48");
        $this->pdf->Image('../images/ctt_header.png', 3, 30, 200, 20);

        $this->pdf->Line(3, 42, 203, 42);
        $this->pdf->Line(105, 42, 105, 90);
        $this->pdf->Line(3, 70, 203, 70);
        $this->pdf->Line(3, 90, 203, 90);
        $this->pdf->Line(52, 70, 52, 90);
        $this->pdf->Line(157, 70, 157, 90);
        $this->pdf->Line(3, 96, 203, 96);


        $this->pdf->SetFont('Times', '', 9);
        $this->pdf->Text(3, 44, "Identificação do Cliente");
        $this->pdf->SetFont('Times', '', 11);
        $this->pdf->Text(5, 47, "OWE - CTTEXPRESSO");
        $this->pdf->Text(5, 51, "CTR OPERACIONAL SUL CO S EDF CTTEXP");
        $this->pdf->Text(5, 60, "2664-500 S JULIAO TOJAL");
        $this->pdf->Text(5, 64, "PORTUGAL");

        $this->pdf->SetFont('Times', '', 9);
        $this->pdf->MultiCell(100, 1, "Sempre que o cliente solicitar prova da aceitação, este certificado será
										preenchido em duplicado.
										O original destina-se ao Aceitante.
									    O duplicado constituirá a prova da aceitação. ", 0, 'C', 0, 1, 105, 47);

        $this->pdf->SetFont('Times', '', 10);
        $this->pdf->Text(5, 71, "Cliente No. 11706560");
        $this->pdf->write1DBarcode("11706560", 'C39', 20, 76, '30', 10, 1);
        $this->pdf->SetFont('Times', '', 9);
        $this->pdf->Text(22, 86, "*1 1 7 0 6 5 6 0*");
        $this->pdf->SetFont('Arial', 'B', 9);


        $this->pdf->SetFont('Times', '', 10);
        $this->pdf->Text(55, 71, "Contrato No. 300234701");
        $this->pdf->write1DBarcode("300234701", 'C39', 65, 76, '30', 10, 1);
        $this->pdf->SetFont('Times', '', 9);
        $this->pdf->Text(67, 86, "*3 0 0 2 3 4 7 0 1 *");
        $this->pdf->SetFont('Arial', 'B', 9);

        $this->pdf->SetFont('Times', '', 8);
        $this->pdf->Text(105, 70, "Primeiro Objecto");
        $this->pdf->Text(105, 72.5, "*" . $first_awb . "*");
        $this->pdf->write1DBarcode($first_awb, 'C39', 110, 76, '40', 10, 2);
        $this->pdf->SetFont('Times', '', 7);
        $this->pdf->Text(118, 86, "*" . $first_awb . "*");
        $this->pdf->SetFont('Arial', 'B', 9);

        $this->pdf->SetFont('Times', '', 8);
        $this->pdf->Text(157, 70, "Ultimo Objecto");
        $this->pdf->Text(157, 72.5, "" . $last_awb . "");
        $this->pdf->write1DBarcode($last_awb, 'C39', 160, 76, '40', 10, 2);
        $this->pdf->SetFont('Times', '', 7);
        $this->pdf->Text(167, 86, "*" . $last_awb . "*");
        $this->pdf->SetFont('Arial', 'B', 11);

        $this->pdf->Text(5, 91, "Expedição Nº. 1");
        $this->pdf->Text(35, 91, "Date: " . date("d/m/Y"));

        $this->pdf->Text(10, 98, "Nº do Objecto");
        $this->pdf->Text(43, 98, "Referência");
        $this->pdf->Text(64, 98, "Nome do Destinatário");
        $this->pdf->Text(105, 98, "Qtd.");
        $this->pdf->Text(123, 98, "País");
        $this->pdf->Text(140, 96, "Serv.");
        $this->pdf->Text(140, 100, "Esp");
        $this->pdf->Text(150, 96, "Importância");
        $this->pdf->Text(150, 100, " a Cobrar");
        $this->pdf->Text(172, 96, "Peso");
        $this->pdf->Text(172, 100, "(Kg)");
        $this->pdf->Text(184, 98, "Observ.");

        $this->pdf->Line(43, 96, 43, 107);
        $this->pdf->Line(63, 96, 63, 107);
        $this->pdf->Line(105, 96, 105, 107);
        $this->pdf->Line(115, 96, 115, 107);
        $this->pdf->Line(140, 96, 140, 107);
        $this->pdf->Line(150, 96, 150, 107);
        $this->pdf->Line(172, 96, 172, 107);
        $this->pdf->Line(183, 96, 183, 107);
        $this->pdf->Line(3, 107, 203, 107);

        $this->pdf->SetFont('Arial', '', 9);
        $y = 108;
        $total_weight = 0;
        $total_pieces = 0;
        $count = 0;
        foreach ($consignment_list as $consignment) {
            $count++;

            if ($count == 10) {
                $this->pdf->AddPage();
                $count = 0;
                $y = 10;
            }

            $this->pdf->write1DBarcode($consignment->getAwb(), 'C39', 1, $y, '40', 10, 2);
            $this->pdf->Text(5, $y + 10, "*" . $consignment->getAwb() . "*");
            $this->pdf->Text(43, $y, "Ref 1");
            $city = str_pad($consignment->getCity(), 20);

            $this->pdf->SetFont('Arial', '', 8);
            $this->pdf->MultiCell(40, 9, strtolower($consignment->getContact() . "\r\n" . $consignment->getAddressLine1() . "\r\n" . $consignment->getPostcode() . $city), 0, 'L', 0, 1, 64, $y, true, 1);

            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->Text(105, $y, $consignment->getTotalParcel());
            $this->pdf->Text(116, $y, $consignment->getCountry());
            $this->pdf->Text(150, $y, '');
            $this->pdf->Text(172, $y, $consignment->getParcelWeight());


            $y1 = $y;
            $y = $y + 18;
            $this->pdf->Line(43, $y1, 43, $y);
            $this->pdf->Line(63, $y1, 63, $y);
            $this->pdf->Line(105, $y1, 105, $y);
            $this->pdf->Line(115, $y1, 115, $y);
            $this->pdf->Line(140, $y1, 140, $y);
            $this->pdf->Line(150, $y1, 150, $y);
            $this->pdf->Line(150, $y1 + 8, 172, $y1 + 8);
            $this->pdf->Line(172, $y1, 172, $y);
            $this->pdf->Line(183, $y1, 183, $y);

            $this->pdf->Line(3, $y - 1, 203, $y - 1);
            $total_weight += $consignment->getWeight();
            $total_pieces += $consignment->getNumberPieces();
        }

        $this->pdf->Image('../images/ctt_footer.png', 3, 250, 205);
        $this->pdf->Text(40, 251, "0");
        $this->pdf->Text(55, 257, $total_pieces);
        $this->pdf->Text(55, 261, $total_pieces);
        $this->pdf->Text(172, 257, number_format($total_weight, 2) . " Kg");
        $this->pdf->SetFont('Arial', '', 8);
        $this->pdf->Text(5, 294, date("d-m-Y H:i:s"));
    }

}
