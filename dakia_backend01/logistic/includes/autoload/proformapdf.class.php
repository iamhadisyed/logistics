 <?php

//require_once('tcpdf_include.php');
class ProformaPDF 
{

	private $pdf;
	
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

		/***
		 * Create instance
		 */
		public function __construct()
		{

			$this->pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		}

		 
		public function AddHTML(Consignment $consignment)
		{
			
			Sessionmanager::checkUserAccess(USER::PRIVILEGE_CLIENT);
			$user = SessionManager::getUser();

			$parcel_list = $consignment->getParcels(true);
		
			$parcel_idx = 0;
			$parcelDescription = '';
			$parcelQty = '';
			$parcelCommodityCode = '';
			$parcelUnitValue = '';
			$parcelTotalValue = '';
			$parcelUnitWeight = '';
			$parcelTotalWeight = '';
			$parcelCountry = '';
			$parcelTotalGrossValue = '';
			$parcelTotalGrossWeight = '';
			$parcelTotalNetWeight = '';
			$sendchecked =  $consignment->getSenderChecked();
			$sendercompany = "";
			$sendercontact = "";
			$senderaddressline1 = "";
			$senderaddressline2 = "";
			$senderaddressline3 = "";
			$sendercity = "";
			$sendercountry = "";
			$senderpostcode = "";
			
			$usercompany = "";
			$userfullname = "";
			$useraddress1 = "";
			$useraddress2 = "";
			$useraddress3 = "";
			$usercity = "";
			$usercountry = "";
			$userpostcode = "";
			$userphone = "";
			$sendertelephone = "";
			
			
			$cpostcode = "";
			
		
		
			$senderinfo =  $user->getReturnAddress();
			
			$senderinfo = explode(',',$senderinfo);
			
			$senderinformation = '';
			
			foreach($senderinfo as $sender)
			{
		
			$senderinformation .= substr(trim($sender),0,30). '<br />';		
			}
			
			$senderinformation .= 'Phone: ' .$user->getPhone();
			
			$parcelUValue = ($consignment->getValue()) / ($consignment->getNumberPieces());
			
			foreach ($parcel_list as $parcel)
			{
			
			
			$itemdesc = explode("||",$parcel->getDescription());
			foreach($itemdesc as $desc)
			{
				$parcelDescription =  $parcelDescription. $desc. '<br />';
			}
			
			$itemcoun = explode("||",$parcel->getCommoditycode());
			foreach($itemcoun as $country)
			{
				$parcelCountry = $parcelCountry . $country. '<br />';
			}
			
			$itemtarrif = explode("||",$parcel->getTarrifNo());
			foreach($itemtarrif as $tarrif)
			{
				$parcelCommodityCode = $parcelCommodityCode . $tarrif. '<br />';
			}
			
			$itemvalue = explode("||",$parcel->getItemValue());
			
			foreach($itemvalue as $value)
			{
				if($value != "" && $value != "0")
					$parcelUnitValue = $parcelUnitValue . round($value,2).'<br />';
				
			}
			
			$itemqty= explode("||",$parcel->getQty());
			foreach($itemqty as $qty)
			{
				$parcelQty =  $parcelQty. $qty . '<br/>';
				//$parcelUnitWeight = $parcelUnitWeight. number_format((float)$consignment->getWeight() / sizeof($itemqty), 2, '.', ''). '<br />';
				//$parcelUnitWeight = $parcelUnitWeight. number_format((float)$consignment->getWeight() / $qty, 2, '.', ''). '<br />';
			}
			
			$itemweight = explode("||",$parcel->getPweight());
			foreach($itemweight as $weight)
			{
				$parcelUnitWeight = $parcelUnitWeight . $weight.'<br />';
				$parcelTotalNetWeight = $parcelTotalNetWeight. number_format((float)$parcelUnitWeight, 2, '.', ''). '<br />';
				
				
			}
			
			
			$parcelTotalValue = $parcelTotalValue. number_format((float)$consignment->getValue(), 2, '.', ''). '<br />';
			$parcelTotalGrossValue = $consignment->getValue();//$parcelTotalGrossValue + number_format((float)$parcel->getUnitvalue() * $parcel->getQty(), 2, '.', '');
			//$parcelTotalGrossWeight = $parcelTotalGrossWeight + number_format(((float)$consignment->getWeight() / $consignment->getNumberPieces()) * $parcel->getQty(), 2, '.', '');
				$parcelTotalGrossWeight = number_format((float)$consignment->getWeight(), 2, '.', '');
			
			}
			$proformaInvoice = new ProformaInvoiceBillingFilter();
			$proformaInvoice->addConsignmentIdFilter($consignment->getId());
			$profoma_list = $proformaInvoice->getList();
			if(count($profoma_list) > 0)
			{
				$profoma_list = $profoma_list[0];
				$billing_company= $profoma_list->getBillingCompany();
				$billing_contact = $profoma_list->getBillingContact();
				$billing_add_1 = $profoma_list->getBillingAddressLine1();
				$billing_add_2 = $profoma_list->getBillingAddressLine2();
				$billing_add_3 = $profoma_list->getBillingAddressLine3();
				$billing_city = $profoma_list->getBillingCity();
				$billing_country = $profoma_list->getBillingCountry();
				$billing_postcode = $profoma_list->getBillingPostCode();
				$billing_telephone =$profoma_list->getBillingTelephone();
 				$billing_payervat = $profoma_list->getPayerVAT();
				$billing_harmcode = $profoma_list->getHarmCommCode();
				$billing_export = $profoma_list->getExportType();
				$billing_payment = $profoma_list->getPaymentTerms();
				$billing_comments =	$profoma_list->getComments();
				$billing_delivery = $profoma_list->getDeliveryTerms();
				$billing_exporttype = $profoma_list->getExport();
				$billing_invoice = $profoma_list->getInvoiceType();
				
			}
			$date =  date("d.m.Y", strtotime($consignment->getDateSubmitted()));
		
			// set default monospaced font
			$this->pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
			
			// set margins
			$this->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
			$this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
			
			$this->pdf->SetFont('dejavusans', '', 8);

			// add a page
			$this->pdf->AddPage();
		
				$html = '<table width="100%" height="719" border="1" cellpadding="3">
						  <tr>
							<td colspan="4">Sender:<br />' .$senderinformation. '
						 </td>
							<td colspan="4" align="center" bgcolor="#666666"><p><br /><font size="16"><strong>'.strtoupper($billing_invoice).' <br />
							</strong></font><font size="16"><strong>INVOICE</strong></font></p></td>
						  </tr>
						  <tr>
							<td colspan="4" rowspan="3">Receiver:<br />
						'.$consignment->getContact().', '.$consignment->getCompany().'<br />
						' .$consignment->getAddressLine1(). '<br />
						' .$consignment->getAddressLine2(). '<br />
						 ' .$consignment->getAddressLine3(). '<br />
						 ' .$consignment->getCity(). '<br />
						  ' .$consignment->getCountry(). '<br />
						' .$cpostcode.'
						Phone: ' .$consignment->getTelephone(). '</td>
						
							<td colspan="4">Date: ' .$date. '</td>
						  </tr>
						  <tr>
							<td colspan="4">Invoice Number: '.$consignment->getHawb(). '</td>
						  </tr>
						  <tr>
							<td colspan="4">Shipment Reference: '.$consignment->getReference().'</td>
						  </tr>
						  <tr>
							<td colspan="4" rowspan="2"><p>Bill to Third Party:</p>
							<p>'.$billing_company.'<br />
							'.$billing_contact.'<br />
							' .$billing_add_1. '<br />
							' .$billing_add_2. '<br />
							' .$billing_add_3. '<br />
							' .$billing_city. '<br />
							' .$billing_country. '<br />
							' .$billing_postcode.' <br />
							Phone: ' .$billing_telephone. '</p></td>
							<td height="60" colspan="4">Comments: '. $billing_comments .'</td> 
						  </tr>
						  <tr>
							<td colspan="4">Air Waybill number:<br />
							 <div align="center"><font size="16"><strong>' .$consignment->getAwb(). '</strong></font><br />
							</div></td>
						  </tr>
						  <tr>
							<td width="31%" >Full Description of Goods</td>
							<td width="4%">Qty</td>
							<td width="15%">Commodity Code</td>
							<td width="8%">Unit Value</td>
							<td width="12%">Subtotal Value</td>
							<td width="12%">Unit Net Weight</td>
						  
							<td width="18%">Country of Manufacture</td>
						  </tr>
						  <tr>
							<td height="130" valign="top">'.$parcelDescription. '<br />
							</td>
							<td valign="top">'.$parcelQty.'<br />
							</td>
							<td valign="top">'.$parcelCommodityCode.'<br />
							</td>
							<td valign="top">'.$parcelUnitValue.'<br />
							</td>
							<td valign="top">' . $parcelsubTotal . '<br />
							</td>
							<td valign="top">'.$parcelUnitWeight.'<br />
							</td>
							<td valign="top">'.$parcelCountry. '<br />
							</td>
						  </tr>
						  <tr>
							<td colspan="4">Total Declared Value: ' .number_format((float)$parcelTotalGrossValue, 2, '.', ''). '           '.$consignment->getCurrency().'</td>
							<td colspan="3">Total Pieces: '.count($parcel_list). '</td>
						  </tr>
						  <tr>
							<td colspan="4">Total Net Weight:      ' .number_format((float)$parcelTotalNetWeight, 2, '.', '').'     KGS</td>
							<td colspan="3">Total Gross Weight:    ' .number_format((float)$parcelTotalGrossWeight, 2, '.', '').'     KGS</td>
						  </tr>
						</table><table width="100%" border="0">
						  <tr>
							<td width="49%" valign="top"><p>Payer of GST/VAT:  '.$billing_payervat.'<br />
							Harm.Comm.Code:  '.$billing_harmcode.'<br />
							Type Of Export: '.$billing_exporttype.'<br />
							Terms of Payment: '.$billing_payment.'<br />
							Reason For Export: '.$billing_export.'<br />
							Delivery Terms: '.$billing_delivery.'
							
							</p></td>
							<td width="51%" valign="top">Currency Code: '.$consignment->getCurrency().'</td>
						  </tr>
						</table>
						
						<p>I//We hereby certify that the information of this invoice is true and correct and that the contents of this shipment are as stated above.</p>
						<p>Signature:  ____________________________________________<br />
						  Position In Company: <br />
						  Shipping Consultant: ____________________________________Company Stamp:';


			// output the HTML content
			$this->pdf->writeHTML($html, true, false, true, false, '');
			$this->pdf->lastPage();
			$profoma_list->setLinkFile("../_assets/ProformaInvoice/ProformaInvoice_" .$consignment->getId(). ".pdf");
			$pdfpath = SETTING_DIR_ASSETS.'ProformaInvoice/ProformaInvoice_' .$consignment->getId(). '.pdf';
			$this->pdf->Output($pdfpath, 'F');
			$profoma_list->save();
			 return $pdfpath;
		
		}


}