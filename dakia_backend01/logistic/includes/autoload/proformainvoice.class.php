<?php

class ProformaInvoice {

    private $pdf;
    private $invoice_folder = "proforma_invoice";
    private $return_url = true;
    private $invoiceName = "";
    const FONT_SMALL = 8;
    const FONT_MEDIUM = 11;
    const FONT_LARGE = 14;
    const FONT_EXTRA_LARGE = 16;
    CONST LINE_VERY_NARROW = 1.5;
    CONST LINE_NARROW = 2.5;
    CONST LINE_MEDIUM = 3.5;
    CONST LINE_WIDE = 5;
    const FONT_FAMILY = "helvetica";
    const MARGIN_LEFT = 0.75;
    const MARGIN_TOP = 0.75;
    const MARGIN_LEFT_WIDE = 8;
    const BARCODE_HEIGHT = 31;
    const WIDTH = 120;

    /*
     * Create instance
     */

    public function __construct(TCPDF $pdf) {
        $this->pdf = $pdf;
    }

    public function AddHTML(Consignment $consignment,$invoiceTitle= 'PROFORMA', $companyLogo = true,$packingList=false, $declaration=false) {

        $user = new User($consignment->getUserId());
        $parcels = $consignment->getParcels();
        $userAccountId = $user->getUserAccountId();
        $userAccoutnData = new CustomerAccount($userAccountId);
        $countryDetails = new Country($consignment->getCountryId());
        
        $senderinfo = $userAccoutnData->getReturnAddress();
        //$senderinfo = explode(',', $senderinfo);
        $senderinformation = '';
        $senderinformation .= $consignment->getSenderCompany() . '<br />';
        // foreach ($senderinfo as $sender) {
//                $senderinformation .= substr(trim($sender), 0, 30) . '<br />';
        //          }
        $senderCountryDeats = new Country($consignment->getSenderCountryId());
        $senderinformation .= $consignment->getSenderAddressLine1() . '<br />';
        $senderinformation .= $consignment->getSenderAddressLine2() . '<br />';
        $senderinformation .= $consignment->getSenderAddressLine3() . '<br />';
        $senderinformation .= $consignment->getSenderCity() . '<br />';
        $senderinformation .= $consignment->getSenderPostcode() . '<br />';$senderinformation .= $senderCountryDeats->getName() . '<br />';
        $senderinformation .= 'Phone: ' . $userAccoutnData->getTelephone() . '<br />';
        $senderinformation .= 'Email: ' . $userAccoutnData->getAlternativeEmail() . '<br />';
        $senderinformation .= 'GST: ' . $consignment->getVatNumber() . '<br />';
        
        $date = date("d M Y", $consignment->getDateLabelCreated());
        // set default monospaced font
        $this->pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $this->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        $this->pdf->SetFont('dejavusans', '', 8);

        $packingListTitle = '';
        $invoiceTitleHeading = '<p align="center"><br /><font size="16"><strong>'.$invoiceTitle.' INVOICE</strong></font></p>';
        PackingListLabel:
        // add a page
        $this->pdf->AddPage();
        
        $comapnyLogoHtml = '';
        if($companyLogo)
        {
            $logo =    User::getUserCompanyImages(false,$consignment->getUserId());
            $comapnyLogoHtml = ' <img src="'.$logo.'"  /> ';
        }
        else if (trim($packingListTitle)!= '')
        {
            $comapnyLogoHtml = '<div align="center"><font size="16"><strong>' . $packingListTitle . '</strong></font><br /></div>';
        }
        
        
        $description = $consignment->getDescription();
        $descriptionExplode = explode(",", $description);
        $displayDescription = implode("<br>",$descriptionExplode);//[0];
        
        $html = $invoiceTitleHeading . 
                                '<table width="100%" height="719" border="1" cellpadding="3">
						  <tr>
							<td colspan="4">Sender:<br />' . $senderinformation . '
						 </td>
							<td colspan="4" align="center" valign="middle" ><p>'.$comapnyLogoHtml.' </p></td>
						  </tr>
						  <tr>
							<td colspan="4" rowspan="3">Receiver:<br />
                                                ' . $consignment->getCompany() . '<br />						
                                                ' . $consignment->getContact() . '<br />
						' . $consignment->getAddressLine1() . '<br />
						' . $consignment->getAddressLine2() . '<br />
						 ' . $consignment->getAddressLine3() . '<br />
						 ' . $consignment->getCity() . '<br />
						  ' . $countryDetails->getName() . '<br />
						' . $cpostcode . '
						Phone: ' . $consignment->getTelephone() . '</td>
						
							<td colspan="2">Date: ' . $date . '</td><td colspan="2"> Inv. No:'. $consignment->getId(). '</td>
						  </tr>
						  <tr>
							<td colspan="4">HAWB Number: ' . $consignment->getHawb() . '</td>
						  </tr>
						 
						  <tr>
							<td colspan="4">Air Waybill number:<br />
							 <div align="center"><font size="12"><strong>' . $consignment->getAwb() . '</strong></font><br />
							</div></td>
						  </tr>
						  <tr>
							<td width="10%" >Issue</td>
							<td width="45%" colspan="4">Description - HSCODE</td>
							<td width="15%">Unit Quantity</td>
							<td width="15%">Unit Price</td>
							<td width="15%">Total</td>
						  </tr>';
                                        $totalQuantity = 0;
                                        $totalPrice = 0;
                                        if(count($parcels) > 0){
                                            foreach($parcels as $parcelList){
                                                $itemDescription = json_decode($parcelList->getDescription());
                                                $itemQuantity = json_decode($parcelList->getQty());
                                                $itemPrice = json_decode($parcelList->getItemValue());
                                                $itemHscode = json_decode($parcelList->getHscode());
                                                $itemWeight = json_decode($parcelList->getPweight());
                                                if(count($itemDescription) > 0){
                                                    $i = 1;
                                                    foreach($itemDescription as $key => $item){
                                                        $itemhtml = "";
                                                        $itemhtml =    ' 
                                                                    <tr >
                                                                     <td valign="top">&nbsp;&nbsp;'.$i.'
                                                                     </td>
                                                                     <td valign="top"  colspan="4">' . $item . ' - ' . $itemHscode[$key] . '</td>
                                                                     <td valign="top">' . $itemQuantity[$key] . ' X ' .$itemWeight[$key] .'
                                                                     </td>
                                                                     <td valign="top">' . number_format($itemPrice[$key], 2) . '
                                                                     </td>
                                                                     <td valign="top">' . number_format($itemPrice[$key] * $itemQuantity[$key] , 2) . '
                                                                     </td>
                                                                     </tr>
                                                               '; 
                                                        $totalQuantity += $itemQuantity[$key];
                                                        $totalPrice += $itemPrice[$key];
                                                        $html .= $itemhtml;
                                                        $i++;
                                                    }

                                                }
                                                
					

                                            }
                                            
                                        }
                                        
                                            if($itemhtml == "")
                                                {
                                                    $html .=    ' <tr>
                                                                <td height="100" valign="top">1<br />
                                                                </td>
                                                                <td valign="top"  colspan="4">' . $displayDescription . 
                                                                '</td>
                                                                <td valign="top">' . $consignment->getNumberPieces() . ' X ' . $consignment->getWeight() . '<br />
                                                                </td>
                                                                <td valign="top">' . number_format($consignment->getValue() / $consignment->getNumberPieces(), 2) . '<br />
                                                                </td>
                                                                <td valign="top">' . $consignment->getValue() . '<br />
                                                                </td>
                                                          </tr> ';

                                                    $totalQuantity += $consignment->getNumberPieces();
                                                    $totalPrice += $consignment->getValue();
                                                }
                                        
                                                                                $html .=
						  '<tr>
							<td valign="top"><br />
							</td>
							<td valign="top" colspan="4">Total
							</td>
							<td valign="top">' .$totalQuantity . '<br />
							</td>
							<td valign="top">' . $consignment->getCurrency() . '<br />
							</td>
							<td valign="top">' . number_format($totalPrice, 2) . '<br />
							</td>
						  </tr>
						 
						</table>';
                                                if($declaration){
                                                    $html .= '<p><br /> The exporter of the products covered by this document declares that, except where otherwise clearly indicated, these products are of preferential origin and the country of origin of the goods are GB.  <br /></p>';
                                                }
                                                $html .= '<p>I/We hereby certify that the information of this invoice is true and correct and represent true value of goods.</p>';
        // output the HTML content
        $this->pdf->writeHTML($html, true, false, true, false, '');
        if($packingList === true)
        {
           // $packingListTitle = '<br><br>Packing List';
            $invoiceTitleHeading = '<p align="center"><br /><font size="16"><strong>PACKING LIST</strong></font></p>';
            //$this->pdf->AddPage();
            $packingList = false;
            goto PackingListLabel;
        }
        
        $this->pdf->lastPage();
        if($this->invoiceName == "")
            $pdfpath = $consignment->getId() . '.pdf';
        else
            $pdfpath = $this->invoiceName . '.pdf';
        chdir('..');
        chdir('_assets/');
        $currentDirecotryPath = str_replace('\\', '/', getcwd()) . "/";
        $path = $currentDirecotryPath . "".$this->invoice_folder."/";
        if (!file_exists($path))
            @mkdir($path, 0777);
        chdir('../main');
        $this->pdf->Output("../_assets/".$this->invoice_folder."/" . $pdfpath, "F");
        if($this->return_url){
            return SETTING_URL . "_assets/".$this->invoice_folder."/" . $pdfpath;
        }else{
            return "../_assets/".$this->invoice_folder."/" . $pdfpath;
        }
    }
    public function setInvoiceFolder($folder) {
        $this->invoice_folder = $folder;
    }
    public function setIsReturnUrl($url = true) {
        $this->return_url = $url;
    }
    public function setInvoiceName($invoiceName = ""){
        $this->invoiceName = $invoiceName;
    }
}
