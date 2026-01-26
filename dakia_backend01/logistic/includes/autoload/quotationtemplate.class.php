<?php 
ob_start();

class QuotationTemplate extends TCPDF {

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

    public function addHTML(QuotationDetails $quotatonDetail, $data = array()) {


        $quoteId = $quotatonDetail->getId();
        $currencyObj = new Currency($quotatonDetail->getCurrencyId());
        $calc_currency = $currencyObj->getCurrencyName();
        $fromCountryObj = new Country($quotatonDetail->getShippingFrom());
        $calc_shipping_from = $fromCountryObj->getName();
        $toCountryObj = new Country($quotatonDetail->getShippingTo());
        $calc_shipping_to = $toCountryObj->getName();
        $carrierObj = new Carrier($quotatonDetail->getCarrierId());
        $service_type = $carrierObj->getCarrier();
        $userAccountObj = new CustomerAccount($quotatonDetail->getAccountId());
        $account = $userAccountObj->getUserAccount();
        $pieces = $quotatonDetail->getPieces();
        $city = $quotatonDetail->getToCity();
        $postcode = $quotatonDetail->getToPostcode();
        $calc_remark = $quotatonDetail->getRemark();
        $calc_weight = $quotatonDetail->getWeight();

        $calculate = unserialize($quotatonDetail->getDimensions());
        $pieces = count($calculate);
        $calc_vol = $quotatonDetail->getVolumnWeight();
        $conversion = $quotatonDetail->getConversionrate();
        $calc_length = 0;
        $calc_width = 0;
        $calc_height = 0;
        $piecesDetails = "length x width x height / Convesrion = Vol Weight \n";
        if(count($calculate) > 0) { 
            foreach($calculate as $value) {
                if(!empty($value['length'])) {
                    $calc_length += $value['length'];
                }
                if(!empty($value['width'])) {
                    $calc_width += $value['width'];
                }
                if(!empty($value['height'])) {
                    $calc_height += $value['height'];
                }
                $piecesDetails .= $value['length'] . ' x ' . $value['width'] . ' x ' . $value['height'] . ' / ' . $conversion . ' = ' . $value['vol_weight'] . " \n";
            }
        }
        $dateCreated = $quotatonDetail->getDateCreated();

        $userEmail = $quotatonDetail->getUserEmail();
        $basicChage = $quotatonDetail->getBasicCharge();
        $vatCharge = $quotatonDetail->getVatCharge();
        $extraCharge = $quotatonDetail->getExtraCharge();
        if (trim($quotatonDetail->getDiscountType()) == 'PERCENTAGE') {
            $Discount = $quotatonDetail->getDiscount() . '%';
        } else {
            $Discount = $quotatonDetail->getDiscount() . '';
        }
        $totalCharge = $quotatonDetail->getTotalCharge();

        $summary = '';
        $summary .= 'Basic Charges:		' . $basicChage . "\n";
        $summary .= 'Vat  Charges:		' . $vatCharge . "\n";
        $summary .= 'Extra Charges:		' . $extraCharge . "\n";
        $summary .= 'Subtotal  :		' . ($basicChage + $vatCharge + $extraCharge) . "\n";
        $summary .= 'Discount  :		' . $Discount . "\n";
        $summary .= 'Total :		' . $totalCharge . "\n";

        $countriesList = new CountryFilter();
        $countriesList->addCountryCodeArrayFilter($calc_shipping_from . "','" . $calc_shipping_to . "','" . $data['country']);
        $countryListData = $countriesList->getColumnList('iso, name, iso3');
        $countrArray = array();
        if (count($countryListData) > 0) {
            foreach ($countryListData as $countryData) {
                $countrArray[$countryData->getIso()] = $countryData->getName();
            }
        }


        $page_format = array(210, 300);
        $PdfObj = new QuotationTemplate('P', 'mm', $page_format);
        //$PdfObj->SetPrintHeader(false);
        $PdfObj->AddPage();
        $PdfObj->SetAutoPageBreak(TRUE, 0);
        // instantiate PDF creation class
        $PdfObj->setFont("helvetica", "", 9);
        $PdfObj->Text(25, 52, "  For : " . $data['account']);
        if (trim($data['billing_address']) == '')
            $PdfObj->MultiCell(60, 40, strtoupper($data['return_address'] . $data['country']), 0, 'L', 0, 1, 34.5, 56.5, true);
        else
            $PdfObj->MultiCell(60, 40, strtoupper($data['billing_address'] . $data['country']), 0, 'L', 0, 1, 34.5, 56.5, true);

        /*
         * 	Data for the box on right top side
         */
        $PdfObj->Text(150, 50, "Quote Ref : " . $quoteId);
        $PdfObj->Text(150, 57, "Date: " . date('d-m-Y', $dateCreated));
        //$PdfObj->Text(162, 64,$data['account']);
        //$PdfObj->Text(162, 71,$calc_currency);
        $PdfObj->SetLineWidth('1.0');
        $PdfObj->line(13, 95, 194, 95);
        /*
         * 	quotation details 
         */
        $PdfObj->setFont("helvetica", "B", 8);
        //$this->setFont("helvetica", "B", 7);
        //$PdfObj->MultiCell(25,40,$data['account'],0,'L',0,1,13, 105, true);
        //$PdfObj->MultiCell(25,40,$countrArray[$calc_shipping_from],0,'L',0,1,45, 105, true);
        $addressDest = '';
        if (trim($city) != '')
            $addressDest .= $city . "\n";
        if (trim($postcode) != '')
            $addressDest .= $postcode . "\n";
        if (trim($countrArray[$calc_shipping_to]) != '')
            $addressDest .= $countrArray[$calc_shipping_to];
        $PdfObj->MultiCell(60, 40, "Destination : " . $countrArray[$calc_shipping_to], 0, 'L', 0, 1, 15.5, 97, true);
        $PdfObj->MultiCell(60, 40, "Country      : " . $city, 0, 'L', 0, 1, 15.5, 102, true);
        $PdfObj->MultiCell(60, 40, "Special : ", 0, 'L', 0, 1, 90, 102, true);
        //$PdfObj->MultiCell(25,40,$service_type,0,'L',0,1,125, 105, true);
        $PdfObj->MultiCell(60, 40, "Weight       : " . $calc_weight . " ", 0, 'L', 0, 1, 15.5, 107, true);
        $PdfObj->MultiCell(60, 40, "Pieces  : " . $pieces, 0, 'L', 0, 1, 90, 107, true);
        $PdfObj->MultiCell(60, 40, "VolWeight   : ", 0, 'L', 0, 1, 15.5, 112, true);

        $PdfObj->Text(13, 118, "RATES QUOTED");
        $PdfObj->SetLineWidth('0.6');
        $PdfObj->line(13, 122, 194, 122);
        $PdfObj->setFont("helvetica", "L", 9);
        $PdfObj->MultiCell(60, 40, "Basic  \n" . $basicChage, 0, 'L', 0, 1, 33.5, 125, true);
        $PdfObj->MultiCell(60, 40, "Vat   \n" . $vatCharge, 0, 'L', 0, 1, 73.5, 125, true);
        $PdfObj->MultiCell(60, 40, "Extra   \n" . $extraCharge, 0, 'L', 0, 1, 113.5, 125, true);
        $PdfObj->MultiCell(60, 40, "Discount   \n" . $Discount, 0, 'L', 0, 1, 150.5, 125, true);
        $PdfObj->SetLineWidth('0.6');
        $PdfObj->line(13, 135, 194, 135);
        $PdfObj->setFont("helvetica", "B", 9);
        $PdfObj->MultiCell(60, 40, "Total For   : " . $calc_weight . " = " . $totalCharge . " " . $calc_currency, 0, 'L', 0, 1, 15.5, 140, true);
        //$PdfObj->MultiCell(25,40,$totalCharge. " ". $calc_currency,0,'L',0,1,180, 105, true);
        //$PdfObj->MultiCell(170,50,"REMARKS:   ".$calc_remark,0,'L',0,1,15, 120, true);
        //	$PdfObj->MultiCell(130,50,$piecesDetails,0,'L',0,1,15, 200, true);
        //	$PdfObj->MultiCell(130,50,$summary,0,'L',0,1,140, 200, true);




        $mergePDFArray = array();
        $PDFpage_size = 20;

        $PDFfileLocation = SETTING_DIR_REMOTE . '_assets/quotation_files/';
        if (!file_exists($PDFfileLocation))
            @mkdir($PDFfileLocation, 0777);
//		$PDFfileLocation= str_replace('includes/autoload/', '', $PDFfileLocation);
        //$PDFfilePrefix="quotation_";
        $pdfName = "quotation_" . $quoteId . '.pdf';
        $PDF_Filename = $PDFfileLocation . $pdfName;
        $PdfObj->Output($PDF_Filename, 'F');
        //$_SESSION['counter'] = $counter;

        return $pdfName;
    }

}
