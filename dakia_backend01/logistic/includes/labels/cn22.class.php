<?php

class Cn22 {

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

    public function getCn22Pdf(Consignment $consignment, $trackingNumber, $formatType = 'pdf', PdfBase $pdf = null, $page_size = null) {
                //$pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->page_size = $page_size;
        $this->pdf = $pdf;
        $this->pdf->AddPage("P", $page_size);
        $this->pdf->SetAutoPageBreak(false, 0);
        $this->cn22Template();
        $this->pdf->setFont("freesans", "L", 6);
//		$filterParcelFilter	= new ParcelFilter();
//		$filterParcelFilter->addLicensePlateFilter($trackingNumber);
//		$filterParcelFilter->addconsignmentIdFilter($consignment->getId());
//		$consignmentfilterParcelFilter = $filterParcelFilter->getList();
//              
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
            foreach ($parcels as $parcelList) {
                $itemDescription = json_decode($parcelList->getDescription());
                $itemQuantity = json_decode($parcelList->getQty());
                $itemWeight = json_decode($parcelList->getPweight());
                $itemPrice = json_decode($parcelList->getItemValue());
                $itemHscode = json_decode($parcelList->getHscode());
                $itemManufactureCountry = json_decode($parcelList->getCommodityCode());
                if (count($itemDescription) > 0) {
                    $i = 1;
                    $y=66;
                    foreach ($itemDescription as $key => $item) {
                        $totalWeight += $itemWeight[$key];
                        $totalPrice += $itemPrice[$key];
                        $this->pdf->Text(4, $y, $itemQuantity[$key]);
                        $this->pdf->Text(9, $y, $item);
                        $this->pdf->Text(67, $y, number_format($itemWeight[$key], 2));
                        $this->pdf->Text(82, $y, number_format($itemPrice[$key], 2));
                        $this->pdf->Text(90, $y, $consignment->getCurrency());
                        $hscode .= $itemHscode[$key] . ", ";
                        $manCountry = $itemManufactureCountry[$key];
                        
                        if(is_numeric($manCountry))
                        {
                            $country = new Country($manCountry);
                            $mcountry .= $country->getIso() . ", ";
                        }
                        else {
                            $mcountry .= $manCountry;
                        }
                        $y+=3;
                        $i++;
                    }
                }
                else
                {
                    $this->pdf->Text(4, 67, $consignment->getNumberPieces());
                    $this->pdf->Text(9, 67, $consignment->getDescription());
                    $this->pdf->Text(67, 67, number_format($consignment->getWeight(),2));
                    $this->pdf->Text(82, 67, number_format($consignment->getValue(),2));
                    $this->pdf->Text(90, 67, $consignment->getCurrency());
                    $totalPrice = number_format($consignment->getValue(),2);
                    $totalWeight = number_format($consignment->getWeight(),2);
                    break;
                }
            }
            $this->pdf->Text(4, 104, $hscode);
            $this->pdf->Text(4, 107, $mcountry);
            $this->pdf->Text(67, 106, number_format($totalWeight,2));
            $this->pdf->Text(82, 106, number_format($totalPrice,2));
            $this->pdf->Text(90, 106, $consignment->getCurrency());
        }
    }
    
    public function getCn22Zpl(Consignment $consignment, $trackingNumber) {
        
        $pDesc = array();
        $pQty = array();
        $pWeight = array();
        $pValue = array();
        $parcels = $consignment->getParcels();

        $totalWeight = 0;
        $totalPrice = 0;
 $zplComercial  =   "";
        if (count($parcels) > 0) {
            
        
        $zplComercial .= "^XA" . "\r";
        $zplComercial .= "^FX borders." . "\r";
        $zplComercial .= "^FO5,150^GB800,655,2^FS" . "\r";
        
        $zplComercial .= "^FO5,210^GB148,1,2^FS" . "\r";
        $zplComercial .= "^FO400,210^GB150,1,2^FS" . "\r";
        $zplComercial .= "^FO150,150^GB1,120,2^FS" . "\r";
        $zplComercial .= "^FO400,150^GB1,120,2^FS" . "\r";
        $zplComercial .= "^FO550,150^GB1,120,2^FS" . "\r";
        $zplComercial .= "^CF0,50" . "\r";
        $zplComercial .= "^FO690,15^FDCN22^FS" . "\r";
        $zplComercial .= "^CF0,30" . "\r";
        $zplComercial .= "^FO5,20^FDCOMMERCIAL INVOICE^FS" . "\r";
        $zplComercial .= "^FO5,60^FDDÉCLARATION EN DOUANE^FS" . "\r";
        $zplComercial .= "^FO608,60^FDMay be opened^FS" . "\r";
        $zplComercial .= "^FO690,95^FDofficially^FS" . "\r";
        
        $zplComercial .= "^CF1,20" . "\r";
        $zplComercial .= "^FO5,100^FDUnited Kingdom^FS" . "\r";
        $zplComercial .= "^FO480,125^FDPeut étre ouvert d'office^FS" . "\r";
        
        $zplComercial .= "^FO5,270^GB800,1,2^FS" . "\r";
        
        $zplComercial .= "^FX Top section with  name and address." . "\r";
        
        $zplComercial .= "^CF0,20" . "\r";
        $zplComercial .= "^FO160,170^FDGift / Cadea^FS" . "\r";
        $zplComercial .= "^FO160,230^FDDocuments^FS" . "\r";
        $zplComercial .= "^FO560,160^FDCommercial sample^FS" . "\r";
        $zplComercial .= "^FO560,190^FD / Échantillon commercial^FS" . "\r";
        $zplComercial .= "^FO560,230^FDOthe / Autrer^FS" . "\r";
        
        $zplComercial .= "^CF0,20" . "\r";
        $zplComercial .= "^FO40,280^FDQuantity and detailed description of contents^FS" . "\r";
        $zplComercial .= "^FO40,310^FDDescription Détaillée et du contenu^FS" . "\r";
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
        $zplComercial .= "^FO35,565^FDd'origine des marchandises (siconnus)^FS" . "\r";
        
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
        }
        return $zplComercial .= "^XZ";
    }
    public function getCn22(Consignment $consignment, $trackingNumber, $formatType = 'pdf', PdfBase $pdf = null, $page_size = null) {
        
        if ($formatType == 'zpl') {
            return $zplData = $this->getCn22Zpl($consignment, $trackingNumber);
        } else {
            $this->getCn22Pdf($consignment, $trackingNumber, $formatType, $pdf, $page_size);
        }
    }
    private function cn22Template() {
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
        $this->pdf->setFont("freesans", "L", 16);
        //$this->pdf->Text(34, 21, "HUNGARY");
        $this->pdf->setFont("freesans", "L", 10);
        $this->pdf->Text(3, 20, "Designated operator");
        $this->pdf->Text(3, 24, "Felvevõ posta");

        $this->pdf->setFont("freesans", "L", 7);
        $this->pdf->Text(63, 18, "Important!");
        $this->pdf->Text(63, 21, "See instructions on the back");
        $this->pdf->Text(63, 24, "Fontos! ");
        $this->pdf->Text(63, 27, "Kitöltési segédlet a hátoldalon");



        $this->pdf->line(3, 30, 98, 30);
        $this->pdf->line(3, 3, 3, 50);
        $this->pdf->line(3, 50, 98, 50);

        $this->pdf->line(3, 50, 3, 120);

        $this->pdf->line(3, 50, 3, 147);
        $this->pdf->line(98, 3, 98, 147);
        $this->pdf->line(65, 50, 65, 110);

        $this->pdf->line(80, 50, 80, 110);

        $this->pdf->line(98, 30, 98, 50);

        $this->pdf->line(13, 30, 13, 50);
        $this->pdf->line(35, 30, 35, 50);
        $this->pdf->line(45, 30, 45, 50);





        $this->pdf->Line(3, 65, 98, 65);
        // $this->pdf->Line(3, 80, 98, 80);
        $this->pdf->Line(3, 80, 98, 80);
        // $this->pdf->Line(3, 100, 98, 100);
        $this->pdf->line(3, 110, 98, 110);
        $this->pdf->Line(3, 103, 65, 103);
        $this->pdf->line(3, 147, 98, 147);




        $this->pdf->line(3, 40, 13, 40);
        $this->pdf->line(35, 40, 45, 40);


        $this->pdf->setFont("freesans", "L", 8);
        $this->pdf->Text(13, 31, "Gift");
        $this->pdf->Text(13, 35, "Ajándék");
        $this->pdf->Text(13, 41, "Documents");
        $this->pdf->Text(13, 45, "Dokumentumok");
        $this->pdf->Text(44.5, 31, "Commercial sample");
        $this->pdf->Text(44.5, 35, "Kereskedelmi áruminta");

        $tickmark = realpath("../images/tickmark.png");
        $this->pdf->image($tickmark, 36, 41, 7, 7, '', '', '', false, 700, '', false, false, 0, '', false, false);
        $this->pdf->Text(44.5, 41, "Other  Tick one or more boxes");
        $this->pdf->setFont("freesans", "L", 7);
        $this->pdf->Text(44.5, 45, "Egyeb  Jelölje meg a megfelelõ négyzete(ke)t");

        $this->pdf->setFont("freesans", "B", 10);
        $this->pdf->Text(4, 83, "For Commercial items only");
        $this->pdf->setFont("freesans", "L", 8);
        $this->pdf->Text(3, 53, "Quantity and detailed description of contents(1)");
        $this->pdf->Text(3, 57, "Tartalom részletes leírása és mennyisége(1)");
        $this->pdf->Text(66, 53, "Weight");
        $this->pdf->Text(65, 57, "(in kg)(2)");
        $this->pdf->Text(65, 61, "Suly(kg)(2)");

        $this->pdf->Text(80, 53, "Customs");
        $this->pdf->Text(80, 57, "Value (3)");
        $this->pdf->Text(80, 61, "Vámérték(3)");

        $this->pdf->Text(4, 88, "If Known, HS tariff number (4) and ");
        $this->pdf->Text(4, 92, "country of origin of goods(5) ");
        $this->pdf->setFont("freesans", "L", 7.5);
        $this->pdf->Text(4, 96, "Kereskedelmi küldeményeknél ha ismert az áruk");
        $this->pdf->Text(4, 99, "HS vámtarifaszáma (4) és származási országa (5)");
        $this->pdf->Text(65, 84, "T. Weight");

        $this->pdf->setFont("freesans", "L", 7.5);
        $this->pdf->Text(65, 92, "Összsúly");
        $this->pdf->Text(65, 88, "(in kg)(6)");
        $this->pdf->Text(65, 96, "(kg) (6)");
        $this->pdf->Text(80, 84, "T. Value (7)");
        $this->pdf->Text(80, 92, "Összérték (7)");


        $this->pdf->setFont("freesans", "L", 7);
        $declarationText = "I, the  undersigned,  whose name  and address are given  on the item, certify  that the  particulars given in this  declaration are correct and that this item does not contain any dangerous article or articles prohibited by legislation or by postal or customs regulations.\r\nDate and sender's signature (8). \r\n " . date("d/m/Y");
        $declarationTextHr = "Alulírott, akinek a neve és címe fel van tüntetve a küldeményen, igazolom , hogy a  jelen nyilatkozatban megadott információk pontosak és hogy ez a küldemény nem tartalmaz semmiféle veszélyes vagy olyan tárgyat,amelyet jogszabály, a postai szabályok, vagy a vámszabályok tiltanak. \r\nKelt és a feladó aláírása (8)";
        $this->pdf->MultiCell(95, 40, $declarationText, 0, 'L', 0, 1, 4, 111, true);
        $this->pdf->MultiCell(95, 40, $declarationTextHr, 0, 'L', 0, 1, 4, 130, true);
        $this->pdf->setFont("freesans", "L", 8);
    }

    private function cn22TemplateAdditionalPage() {
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
