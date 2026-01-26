<?php

class Commercialinvoice {

    private $pdf;

    const FONT_SMALL = 8;
    const FONT_MEDIUM = 11;
    const FONT_LARGE = 14;
    const FONT_EXTRA_LARGE = 16;
    CONST LINE_VERY_NARROW = 1.5;
    CONST LINE_NARROW = 2.5;
    CONST LINE_MEDIUM = 3.5;
    CONST LINE_WIDE = 5;
    const FONT_FAMILY = "freesans";
    const MARGIN_LEFT = 0.75;
    const MARGIN_TOP = 0.75;
    const MARGIN_LEFT_WIDE = 8;
    const BARCODE_HEIGHT = 31;
    const WIDTH = 120;

    public $hermes_barcode_display = "";

    /*     * *
     * Create instance
     */

    public function __construct() {
        
    }

    private function getCIPdf(Consignment $consignment, $trackingNumber, $formatType = 'pdf', PdfBase $pdf = null, $page_size = null) {
        $this->page_size = $page_size;
        $this->pdf = $pdf;
        $this->pdf->AddPage("P", $page_size);
        $this->pdf->SetAutoPageBreak(false, 0);

        $this->commercialinvoiceTemplate();

        $this->pdf->setFont("freesans", "L", 8);
        $this->pdf->Text(67, 22, date("d-m-Y"));
        $this->pdf->Text(67, 31, $trackingNumber);
        $addressForShipment = $consignment->getAddressLine1() . " " .
                $consignment->getAddressLine2() . " " .
                $consignment->getAddressLine3() . "\r\n" .
                $consignment->getCity() . "\r\n" .
                $consignment->getPostcode() . " ";

        $this->pdf->MultiCell(60, 20, $addressForShipment, 0, 'L', 0, 1, 5, 22, true);

        $this->pdf->setFont("freesans", "L", 6);
        $pDesc = array();
        $pQty = array();
        $pWeight = array();
        $pValue = array();
        $parcels = $consignment->getParcels();

        $totalWeight = 0;
        $totalPrice = 0;
        if (count($parcels) > 0) {
            $hscode = "";
            $mcountry = "";
            $y = 43;
            foreach ($parcels as $parcelList) {
                $itemDescription = json_decode($parcelList->getDescription());
                $itemQuantity = json_decode($parcelList->getQty());
                $itemWeight = json_decode($parcelList->getPweight());
                $itemPrice = json_decode($parcelList->getItemValue());
                $itemHscode = json_decode($parcelList->getHscode());
                $itemManufactureCountry = json_decode($parcelList->getCommodityCode());
                if (count($itemDescription) > 0) {
                    $i = 1;

                    foreach ($itemDescription as $key => $item) {
                        $totalWeight += $itemWeight[$key];
                        $totalPrice += $itemPrice[$key];

                        $this->pdf->Text(4, $y, $itemQuantity[$key]);
                        $this->pdf->Text(9, $y, $itemHscode[$key] . "-" . $item);
                        $this->pdf->Text(67, $y, number_format($itemWeight[$key], 2));
                        $this->pdf->Text(82, $y, number_format($itemPrice[$key], 2));
                        $this->pdf->Text(90, $y, $consignment->getCurrency());
                        $hscode .= $itemHscode[$key] . ", ";
                        $manCountry = $itemManufactureCountry[$key];

                        if (is_numeric($manCountry)) {
                            $country = new Country($manCountry);
                            $mcountry .= $country->getIso() . ", ";
                        } else {
                            $mcountry .= $manCountry;
                        }
                        $y += 3;
                        $i++;
                    }
                } else {
                    $this->pdf->Text(4, $y, $consignment->getNumberPieces());
                    $this->pdf->Text(9, $y, $consignment->getDescription());
                    $this->pdf->Text(67, $y, number_format($consignment->getWeight(), 2));
                    $this->pdf->Text(82, $y, number_format($consignment->getValue(), 2));
                    $this->pdf->Text(90, $y, $consignment->getCurrency());
                    $totalPrice = number_format($consignment->getValue(), 2);
                    $totalWeight = number_format($consignment->getWeight(), 2);
                    break;
                }
            }

            $this->pdf->Text(67, 75, number_format(($totalWeight > 0 ? $totalWeight : $consignment->getWeight()), 2));
            $this->pdf->Text(82, 75, number_format(($totalPrice > 0 ? $totalPrice : $consignment->getValue()), 2));
            $this->pdf->Text(90, 75, $consignment->getCurrency());
            $this->pdf->setFont("freesans", "B", 6);
            if (trim($consignment->getEoriNumber()) != "")
                @$this->pdf->Text(5, 75, "EORI NO:" . (trim($consignment->getEoriNumber()) == "" ? " N/A " : $consignment->getEoriNumber()));
            if (trim($consignment->getVatNumber()) != "")
                @$this->pdf->Text(35, 75, "VAT NO:" . (trim($consignment->getVatNumber()) == "" ? " N/A " : $consignment->getVatNumber()));
        }
    }

    private function getCIZpl(Consignment $consignment, $trackingNumber) {

        $pDesc = array();
        $pQty = array();
        $pWeight = array();
        $pValue = array();
        $parcels = $consignment->getParcels();

        $totalWeight = 0;
        $totalPrice = 0;

        $zplComercial = " ";

        if (count($parcels) > 0) {
            $hscode = "";
            $mcountry = "";
            $zplComercial .= "^XA" . "\r";
            $zplComercial .= "^FX borders" . "\r";
            $zplComercial .= "^FO5,5^GB800,800,2^FS" . "\r";
            $zplComercial .= "^FO5,70^GB800,1,2^FS" . "\r";
            $zplComercial .= "^CF0,45" . "\r";
            $zplComercial .= "^FO200,20^FDCOMMERCIAL INVOICE^FS" . "\r";
            $zplComercial .= "^FO5,270^GB800,1,1^FS" . "\r";
            $zplComercial .= "^FO550,70^GB1,200,1^FS" . "\r";
            $zplComercial .= "^FO550,170^GB255,1,1^FS" . "\r";
            $zplComercial .= "^FX Top section with  name and address." . "\r";
            $zplComercial .= "^CF0,25" . "\r";
            $zplComercial .= "^FO40,80^FDAddress^FS" . "\r";
            $zplComercial .= "^FO560,80^FDDate:^FS" . "\r";
            $zplComercial .= "^FO560,180^FDInvoice Ref.^FS" . "\r";
            $zplComercial .= "^CF1,20" . "\r";
            $zplComercial .= "^FO50,110^FD" . $consignment->getCompany() . "^FS" . "\r";
            $zplComercial .= "^FO50,140^FD" . $consignment->getContact() . "^FS" . "\r";
            $zplComercial .= "^FO50,170^FD" . $consignment->getAddressLine1() . ", " . $consignment->getAddressLine2() . "^FS" . "\r";
            $zplComercial .= "^FO50,200^FD" . $consignment->getAddressLine3() . "^FS" . "\r";
            $zplComercial .= "^FO50,230^FD" . $consignment->getCity() . " " . $consignment->getPostcode() . " ^FS" . "\r";
            $zplComercial .= "^FO570,130^FD" .  date("d-m-Y"). "^FS" . "\r";
            $zplComercial .= "^FO570,230^FD" . $trackingNumber . "^FS" . "\r";
            $zplComercial .= "^CF0,20" . "\r";
            $zplComercial .= "^FO40,280^FDQuantity and detailed description of contents^FS" . "\r";
            $zplComercial .= "^FO40,310^FD".mb_substr( 'Description Détaillée et du contenu',"utf-8")."^FS" . "\r";
            $zplComercial .= "^FO570,280^FDWeight^FS" . "\r";
            $zplComercial .= "^FO570,310^FDPoids (kg)^FS" . "\r";
            $zplComercial .= "^FO700,280^FDValue^FS" . "\r";
            $zplComercial .= "^FO700,310^FDValeur^FS" . "\r";
            $zplComercial .= "^FO5,340^GB800,1,1^FS" . "\r";
            $zplComercial .= "^FO550,270^GB1,380,1^FS" . "\r";
            $zplComercial .= "^FO680,270^GB1,380,1^FS" . "\r";
            $zplComercial .= "^FO5,470^GB800,1,1^FS" . "\r";
            $zplComercial .= "^FO5,600^GB800,1,1^FS" . "\r";
            $zplComercial .= "^FO5,650^GB800,1,1^FS" . "\r";
            $zplComercial .= "^CF1,15" . "\r";
            $y = 350;
            foreach ($parcels as $parcelList) {
                $itemDescription = json_decode($parcelList->getDescription());
                $itemQuantity = json_decode($parcelList->getQty());
                $itemWeight = json_decode($parcelList->getPweight());
                $itemPrice = json_decode($parcelList->getItemValue());
                $itemHscode = json_decode($parcelList->getHscode());
                $itemManufactureCountry = json_decode($parcelList->getCommodityCode());
                if (count($itemDescription) > 0) {
                    $i = 1;
                    foreach ($itemDescription as $key => $item) {
                        $totalWeight += $itemWeight[$key];
                        $totalPrice += $itemPrice[$key];
                        $zplComercial .= "^FO40,".$y."^FD" . $itemQuantity[$key] . " " . $itemHscode[$key] . " " . $item . "^FS" . "\r";
                        $zplComercial .= "^FO570,".$y."^FD" . number_format($itemWeight[$key], 2) . "^FS" . "\r";
                        $zplComercial .= "^FO700,".$y."^FD" . number_format($itemPrice[$key], 2) . " " . $consignment->getCurrency() . "^FS" . "\r";
                        $hscode .= $itemHscode[$key] . ", ";
                        $manCountry = $itemManufactureCountry[$key];
                        if (is_numeric($manCountry)) {
                            $country = new Country($manCountry);
                            $mcountry .= $country->getIso() . ", ";
                        } else {
                            $mcountry .= $manCountry;
                        }
                        $y += 30;
                        $i++;
                    }
                } else {
                    $zplComercial .= "^FO40,350^FD" . $consignment->getNumberPieces() . " " . $consignment->getDescription() . "^FS" . "\r";
                    $zplComercial .= "^FO570,350^FD" . number_format($consignment->getWeight(), 2) . "^FS" . "\r";
                    $zplComercial .= "^FO700,350^FD" . number_format($consignment->getValue(), 2) . " " . $consignment->getCurrency() . "^FS" . "\r";
                    $totalPrice = number_format($consignment->getValue(), 2);
                    $totalWeight = number_format($consignment->getWeight(), 2);
                    break;
                }
            }

            $zplComercial .= "^CF0,20" . "\r";
            $zplComercial .= "^FO30,480^FDFor commercial Items Only^FS" . "\r";
            $zplComercial .= "^FO570,480^FDTotal Weight^FS" . "\r";
            $zplComercial .= "^FO700,480^FDTotal Value^FS" . "\r";

            $zplComercial .= "^CF1,20" . "\r";
            $zplComercial .= "^FO35,510^FDIf known, HS tariff number and country of^FS" . "\r";
            $zplComercial .= "^FO35,535^FDorigin of goods. N* tarifaire du SH et pays ^FS" . "\r";
            $zplComercial .= "^FO35,565^FDd\'origine des marchandises (siconnus)^FS" . "\r";

            $zplComercial .= "^FO570,510^FDPoids ^FS" . "\r";
            $zplComercial .= "^FO570,535^FDtotal^FS" . "\r";
            $zplComercial .= "^FO570,565^FD(Kg)^FS" . "\r";

            $zplComercial .= "^FO700,510^FDValeur ^FS" . "\r";
            $zplComercial .= "^FO700,535^FDtotale^FS" . "\r";

            $zplComercial .= "^FO570,620^FD" . number_format(($totalWeight > 0 ? $totalWeight : $consignment->getWeight()), 2) . "^FS" . "\r";
            $zplComercial .= "^FO700,620^FD" . number_format(($totalPrice > 0 ? $totalPrice : $consignment->getValue()), 2) . " " . $consignment->getCurrency() . "^FS" . "\r";

            $zplComercial .= "^CF0,20" . "\r";
            $zplComercial .= "^FO30,620^FDEORI:^FS" . "\r";
            $zplComercial .= "^FO300,620^FDVAT:^FS" . "\r";
            $zplComercial .= "^CF1,20" . "\r";
            if (trim($consignment->getEoriNumber()) != "")
                $zplComercial .= "^FO85,620^FD" . (trim($consignment->getEoriNumber()) == "" ? " N/A " : $consignment->getEoriNumber()) . "^FS" . "\r";
            if (trim($consignment->getVatNumber()) != "")
                $zplComercial .= "^FO350,620^FD" . (trim($consignment->getVatNumber()) == "" ? " N/A " : $consignment->getVatNumber()) . "^FS" . "\r";

            $zplComercial .= "^CF1,20" . "\r";
            $zplComercial .= "^FO30,660^FDI, the undersigned whose name and address are given on the item,^FS" . "\r";
            $zplComercial .= "^FO30,690^FDcertify that the particulars given in this Declaration are ^FS" . "\r";
            $zplComercial .= "^FO30,720^FDcorrect and that this item does not contain any dangerous^FS" . "\r";
            $zplComercial .= "^FO30,750^FDarticle or articles prohibited by legislation or by postal or ^FS" . "\r";
            $zplComercial .= "^FO30,780^FDcustomes regulations.^FS" . "\r";
            $zplComercial .= "^XZ" . "\r";
        }
        return $zplComercial;
    }

    public function getCommercialinvoice(Consignment $consignment, $trackingNumber, $formatType = 'pdf', PdfBase $pdf = null, $page_size = null) {
        
        if ($formatType == 'zpl') {
            return $zplData = $this->getCIZpl($consignment, $trackingNumber);
        } else {
            $this->getCIPdf($consignment, $trackingNumber, $formatType, $pdf, $page_size);
        }
    }

    private function commercialinvoiceTemplate() {
        $this->pdf->line(3, 3, 98, 3);
        $this->pdf->line(3, 3, 3, 97);
        $this->pdf->line(98, 3, 98, 97);
        $this->pdf->line(3, 97, 98, 97);


        $this->pdf->setFont("freesans", "B", 20);
        $this->pdf->Text(8, 6, "COMMERCIAL INVOICE");

        $this->pdf->line(3, 18, 98, 18);

        $this->pdf->setFont("freesans", "B", 8);
        //$this->pdf->Text(34, 21, "HUNGARY");

        $this->pdf->Text(4, 19, "Address:");

        $this->pdf->setFont("freesans", "L", 7);


        $this->pdf->line(65, 18, 65, 35);
        $this->pdf->line(65, 26, 98, 26);
        $this->pdf->line(3, 35, 98, 35);
        $this->pdf->setFont("freesans", "B", 9);
        $this->pdf->Text(65, 18, "Date:");
        $this->pdf->Text(65, 26, "Invoice Ref:");



        $this->pdf->line(3, 42, 98, 42);
        $this->pdf->setFont("freesans", "B", 6);
        $this->pdf->Text(5, 36, "Quantity and detailed description of contents");
        $this->pdf->Text(5, 39, "Description Détaillée et du contenu");

        $this->pdf->line(65, 35, 65, 80);
        $this->pdf->line(82, 35, 82, 80);

        $this->pdf->Text(68, 36, "Weight");
        $this->pdf->Text(67, 39, "Poids (kg)");

        $this->pdf->Text(85.5, 36, "Value");
        $this->pdf->Text(85, 39, "Valeur");
        $this->pdf->setFont("freesans", "L", 7);
        $style = array('width' => 0.2, 'dash' => '2,2,2,2', 'phase' => 0, 'color' => array(0, 0, 0));

        $this->pdf->line(3, 49, 98, 49, $style);
        $style = array('width' => 0.1, 'dash' => '0', 'phase' => 0, 'color' => array(0, 0, 0));
        $this->pdf->line(3, 58, 98, 58, $style);
        $this->pdf->MultiCell(60, 10, "If known, HS tariff number and country of origin of goods N* tarifaire du SH et pays d'origine des marchandises (si connus)", 0, 'L', 0, 1, 5, 62, true);

        $this->pdf->line(3, 73, 98, 73);
        $this->pdf->line(3, 80, 98, 80);
        $this->pdf->MultiCell(95, 5, "I, the undersigned whose name and address are given on the item, certify that the particulars given in this Declaration are correct and that this item does not contain any dangerous article or articles prohibited by legislation or by postal or customs regulations.  ", 0, '', 0, 1, 5, 82, true);
        $this->pdf->setFont("freesans", "B", 7);
        $this->pdf->Text(5, 59, "For commercial Items Only");

        $this->pdf->Text(65, 59, "Total Weight");
        $this->pdf->Text(67, 62, "Poids total");
        $this->pdf->Text(70, 65, "(kg)");

        $this->pdf->Text(82, 59, "Value Total");
        $this->pdf->Text(85, 62, "Valeur");


//		$this->pdf->line(3, 50, 98, 50);
        /*
          $this->pdf->line(3, 50, 3, 120);

          $this->pdf->line(3, 50, 3, 147);
         */
    }

    private function commercialinvoiceTemplateAdditionalPage() {
        $this->pdf->setFont("freesans", "B", 22);
        $this->pdf->Text(73, 8, "CN 22");
        $this->pdf->setFont("freesans", "B", 12);
        $this->pdf->Text(3, 5, "CUSTOMS DECLARATION");
        $this->pdf->Text(3, 10, "VÁMÁRU-NYILATKOZAT");
        $this->pdf->setFont("freesans", "L", 8);
        $this->pdf->Text(57, 5, "May be opened");
        $this->pdf->Text(57, 8, "officially");
        $this->pdf->Text(57, 11, "Hivatalból");
        $this->pdf->Text(57, 14, "felnyitható");

        $this->pdf->line(3, 3, 98, 3);
        $this->pdf->line(3, 18, 98, 18);
        $this->pdf->line(3, 3, 3, 147);
        $this->pdf->line(3, 147, 98, 147);
        //$this->pdf->line(3, 50, 3, 147);
        $this->pdf->line(98, 3, 98, 147);



        $this->pdf->line(3, 30, 98, 30);
        $this->pdf->setFont("freesans", "B", 10);
        $this->pdf->setFont("freesans", "L", 7.5);
        $this->pdf->Text(3, 18, "Quantity and detailed description of contents(1)");
        $this->pdf->Text(3, 22, "Tartalom részletes leírása és mennyisége(1)");
        $this->pdf->Text(66, 18, "Weight");
        $this->pdf->Text(65, 22, "(in kg)(2)");
        $this->pdf->Text(65, 26, "Suly(kg)(2)");

        $this->pdf->Text(82, 18, "Customs");
        $this->pdf->Text(82, 22, "Value (3)");
        $this->pdf->Text(82, 26, "Vámérték(3)");

        $this->pdf->line(65, 18, 65, 147);

        $this->pdf->line(80, 18, 80, 147);
    }

}
