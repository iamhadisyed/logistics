<?php
class RoyalMailUntracked implements CarrierService {
    private $pdf;
    private $serviceValues  = null;
    private $agentValues    = null;
    private $constants      = null;
    private $user           = null;
    
    
    public function __construct() {
        $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf = $pdf;
    }
    
    public function validation(Consignment $consignment, Services $service, Country $country) {
        
    }

    public function remoteareas($consignment, $carrierObject, $sender) {
        
    }
    
    public function label($consignment) {
        
        $output = array();
        $this->serviceValues    =   new Services($consignment->getServiceId());
        $this->user             =   SessionManager::getUser();
        
        
        /*
        *  Get Tracking Number ranges
        */
        
        $serviceRangeMappingFilter      =   new ServiceRangeMappingFilter();
        $serviceRangeMappingFilter->addFilter("service_id = '".$consignment->getServiceId()."' AND agent_id = '".$consignment->getAgentId()."' ");
        $serviceRange   =    $serviceRangeMappingFilter->getList(" licence_plate_id ");
        if(count($serviceRange)>0)
        {
            $licence_plate_id    =   (int)$serviceRange[0]->getLicencePlateId();
        }
        if($licence_plate_id < 0)
        {
            $output['STATUS']       =   'ERROR';
            $output['MESSAGE']      =   "This service does not have tracking number range. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }
        
        
        $parcel_list = $consignment->getParcels();
        $parcel_count = sizeof($parcel_list);
        $parcel_idx = 0;
        // Generate label for each parecel
        foreach ($parcel_list as $parcel) {
            if ($parcel->getTrackingNumber() == '') {
                $resultArray        = LicencePlate::getLicencePlateNumber($licence_plate_id);
                if(trim($resultArray['STATUS']) == 'ERROR')
                    return $resultArray;
                else
                {
                    $post_code = str_replace(" ", "", $consignment->getPostcode());
                    $post_code = str_pad($post_code, 8, "0", STR_PAD_RIGHT);
                    $licence_plate  =   $resultArray["PREFIX"].$resultArray["RANGE"].$post_code;
                }
                $parcel->setTrackingNumber($licence_plate);
                $parcel->save();
            }
            else {
                $licence_plate = $parcel->getTrackingNumber();
            }
            $licence_plate_array[$parcel_idx] = $licence_plate;
            $this->pdf->SetPrintFooter(false);
            $this->pdf->SetFooterMargin(0);
            $this->pdf->SetAutoPageBreak(false, 0);
            $page_size = array(100, 100);
            $this->pdf->AddPage("P", $page_size);
            $this->addWayBill($consignment, $parcel_idx, $licence_plate);
           
            $new_page_flag = true;
            ++$parcel_idx;
        }
        
        $this->pdf->IncludeJS("print();");
        $this->pdf->Output("../_assets/pdf/" . date('Y_m_d') . '/' . $consignment->getId() . ".pdf", "F");
        $output['STATUS']   = 'SUCCESS';
        $output['LABEL']    = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
        $output['TRACKING_NUMBER'] = $licence_plate_array;
        return $output;
    }

    public function tracking($trackingNumber) {
        
    }

    public function sendData($tracking_numbers = array()) {
        
    }

    public function manifest($consignment) {
        
    }

    public function preAdvice($consignment) {
        
    }

   private function addWayBill(Consignment $consignment, $parcel_idx,$licence_plate)
	{
            // BORDER LINES
            $this->pdf->line(2, 2, 98, 2);
            $this->pdf->line(2, 98, 98, 98);
            $this->pdf->line(2, 2, 2, 98);
            $this->pdf->line(98, 2, 98, 98);
            //TOP LINE UNDER IMAGE
            $this->pdf->line(2, 20, 98, 20);
            //TOP TO MIDDLE LINE BETWEEN IMAGES
            $this->pdf->line(50, 2, 50, 20);
            // BOTTOM LINE UNDER BARCODE
            $this->pdf->line(2, 35, 98, 35);
            // BOTTOM HORIZONTALE LINE
            $this->pdf->line(2, 85, 98, 85);
            //RIGHT LINE NEXT TO RETURN ADDRESS
            $this->pdf->line(10, 35, 10, 85);
            // RIGHT LINE NEXT TO ADDRESS
            $this->pdf->line(85, 35, 85, 98);
            //LINE ABOVE PARCEL OR LATER
            $this->pdf->line(85, 75, 98, 75);
            
            
            $style = array(
                'position' => '',
                'align' => 'C',
                'stretch' => true,
                'fitwidth' => true,
                'cellfitalign' => '',
                'border' => false,
                'hpadding' => 'auto',
                'vpadding' => 'auto',
                'fgcolor' => array(0,0,0),
                'bgcolor' => false, //array(255,255,255),
                'text' => true,
                'font' => 'helvetica',
                'fontsize' => 8,
                'stretchtext' => 4

                );

            $this->pdf->write1DBarcode($licence_plate, 'C128', 15, 21, '', 14, 0.28, $style, 'N');

            $this->pdf->setFont("helvetica", "B",6);
            
          
            if(trim($this->user->getLogo()) != '')
                $image1=realpath("../images/userlogo/".$this->user->getLogo());
            else
                $image1=realpath("../images/logo.jpg");

          
            if(file_exists($image1))
            {
                $fitbox = 'C';
                $fitbox[1] = 'M';
                $this->pdf->image($image1, 10, 3, 30, 15, '', '', '', false, 700, '', false, false, 0, $fitbox, false, false);
            }
            $this->pdf->Text(10, 17, "www.oneworldexpress.com");


            if (in_array($this->serviceValues->getCode(),array('RM1','RM1L')))
            {
                $image = realpath ("../images/1st Class.jpg");
                $serviceLetterOnLabel   =   'P';
            }
            else if (in_array($this->serviceValues->getCode(),array('RM2','RM2L')))
            {
                $image = realpath ("../images/2nd Class.jpg");
                $serviceLetterOnLabel   =   'L';
            }
            
            if(file_exists($image))
            {
                 $this->pdf->image($image, 52, 3, 45, 15);
            }
            
            $this->pdf->setFont("helvetica", "B", 18);
            $this->pdf->Text(88, 76, $serviceLetterOnLabel);

            $this->pdf->StartTransform();
            $this->pdf->Rotate(90, 70,70);

            
            
            $this->pdf->setFont("helvetica", "B", 10);
            
            $this->pdf->Text(70,88 , $this->serviceValues->getCode() . " Untracked");
            // Stop Transformation
            $this->pdf->setFont("helvetica", "B", 7.5);
            $companyaddress = "One World Express";
            $useraddress = "One World Express, Pump Lane, Hayes, UK, UB3 3NB";
           
            $this->pdf->setFont("helvetica", "", 6);
            $this->pdf->Text(55, 3, "If Undelivered Return To: (" .  $companyaddress . ")");
            $this->pdf->setFont("helvetica", "", 5);
            $this->pdf->Text(55, 6, $useraddress  ); 
            $this->pdf->StopTransform();
           
            $this->pdf->setFont("helvetica", '', 10);
            $address = "";
            $company = "";
            if ($consignment->getCompany() != "") $company = $consignment->getCompany();
            if ($consignment->getAddressLine1() != "") $address1  = $consignment->getAddressLine1();
            if ($consignment->getAddressLine2() != "") $address2  = $consignment->getAddressLine2();
            if ($consignment->getAddressLine3() != "") $address3  = $consignment->getAddressLine3();

            
            $this->pdf->Text(12, 40, ucwords($company));
            $this->pdf->Text(12, 44, ucwords($consignment->getContact()));
            $this->pdf->Text(12, 48, ucwords($address1));
            $this->pdf->Text(12, 52, ucwords($address2));
            $this->pdf->Text(12, 56, ucwords($address3));
            $this->pdf->Text(12, 60, ucwords($consignment->getCity()));
            $this->pdf->Text(12, 64, ucwords("United Kingdom"));

            $this->pdf->setFont("helvetica", "B", 12);
            $this->pdf->Text(12, 70, $consignment->getPostcode());
            
            $fontname = $this->pdf->addTTFfont('../assets/fonts/simhei.ttf', 'TrueTypeUnicode', '', 32);
            $this->pdf->SetFont($fontname, '', 8);
            $this->pdf->Text(12,78 , "Notes: " . $consignment->getNotes() );
            
            
            $style1 = array(
                    'position' => '',
                    'align' => 'C',
                    'stretch' => true,
                    'fitwidth' => true,
                    'cellfitalign' => '',
                    'border' => false,
                    'hpadding' => 'auto',
                    'vpadding' => 'auto',
                    'fgcolor' => array(0,0,0),
                    'bgcolor' => false, //array(255,255,255),
                    'text' => true,
                    'font' => 'helvetica',
                    'fontsize' => 8,
                    'stretchtext' => 1

            );

  
            $this->pdf->write1DBarcode($consignment->getHawb(), 'C128', 15, 85, '', 15, 0.5, $style1, 'Y');
        
            

            $zone = $this->getZone($consignment->getPostcode());
            if(isset($zone))
            {

                if (strlen($zone) == 1)
                {
                     $this->pdf->setFont("helvetica", "B", 33);
                }
                else
                {
                     $this->pdf->setFont("helvetica", "B", 33);
                } 
                $this->pdf->Text(87, 84, $zone);
            }


	}

    public function getZone($postcode)
    {
            $one_level_postcode  = substr(trim($postcode),0,1);
            $two_level_postcode  = substr(trim($postcode),0,2);
            $three_level_postcode  = substr(trim($postcode),0,3);
            $four_level_postcode  = substr(trim($postcode),0,4);
            $zone= "";
            if($four_level_postcode == "BFPO")
            $zone= "0";
            else if($four_level_postcode == "HWDC" || $four_level_postcode == "PRDC")
            $zone= "3";					
            else if($three_level_postcode == "W10" || $three_level_postcode == "W11" || $three_level_postcode == "W12" || $three_level_postcode == "W13" )
            $zone= "0";
            else if($two_level_postcode == "CV" ||
                $two_level_postcode == "MK" ||					
                $two_level_postcode == "DE" ||					
                $two_level_postcode == "WS" ||
                $two_level_postcode == "WV" ||
                $two_level_postcode == "ST" ||
                $two_level_postcode == "DY" ||
                $two_level_postcode == "NN" ||
                $two_level_postcode == "LE" ||
                $two_level_postcode == "WR" ||
                $two_level_postcode == "HR" ||
                $two_level_postcode == "PR" ||
                $two_level_postcode == "FY" ||
                $two_level_postcode == "BB" ||
                $two_level_postcode == "LA" ||
                $two_level_postcode == "BL" ||
                $two_level_postcode == "OL" ||
                $two_level_postcode == "SK" ||
                $two_level_postcode == "TF" )
            $zone= "1";
            else if($two_level_postcode == "LS" ||
                $two_level_postcode == "WF" ||
                $two_level_postcode == "HG" ||
                $two_level_postcode == "HU" ||
                $two_level_postcode == "DN" ||
                $two_level_postcode == "LN" ||
                $two_level_postcode == "NG" ||
                $two_level_postcode == "YO" ||
                $two_level_postcode == "BD" ||
                $two_level_postcode == "HX" ||
                $two_level_postcode == "HD" ||
                $two_level_postcode == "CH" ||
                $two_level_postcode == "LL" ||
                $two_level_postcode == "CW" ||
                $two_level_postcode == "WA" ||
                $two_level_postcode == "WN" ||
                $two_level_postcode == "SY" )
        $zone= "2";	
            else if($two_level_postcode == "EC" ||
                            $two_level_postcode == "W1" ||
                            $two_level_postcode == "WC" ||
                            $two_level_postcode == "CM" ||
                            $two_level_postcode == "CO" ||
                            $two_level_postcode == "CB" ||
                            $two_level_postcode == "SS" ||
                            $two_level_postcode == "PE" )
            $zone= "3";	
            else if($two_level_postcode == "EH" ||
                            $two_level_postcode == "FK" ||
                            $two_level_postcode == "KY" ||
                            $two_level_postcode == "TD" ||
                            $two_level_postcode == "DD" ||
                            $two_level_postcode == "PH" ||
                            $two_level_postcode == "PA" ||
                            $two_level_postcode == "ML" ||
                            $two_level_postcode == "KA" ||
                            $two_level_postcode == "BT" ||
                            $two_level_postcode == "GY" ||
                            $two_level_postcode == "JE" ||
                            $two_level_postcode == "IM" ||
                            $two_level_postcode == "CA" ||
                            $two_level_postcode == "DG" ||
                            $two_level_postcode == "IV" ||
                            $two_level_postcode == "KW" ||
                            $two_level_postcode == "HS" ||
                            $two_level_postcode == "PL" ||
                            $two_level_postcode == "TR" ||
                             $two_level_postcode == "AB" ||
                            $two_level_postcode == "ZE" )
            $zone= "4";			
            else if($two_level_postcode == "NR" ||
                            $two_level_postcode == "IP" ||
                            $two_level_postcode == "RG" ||
                            $two_level_postcode == "OX" ||
                            $two_level_postcode == "SN" ||
                            $two_level_postcode == "SW" ||
                            $two_level_postcode == "KT" ||
                            $two_level_postcode == "TW" ||
                            $two_level_postcode == "GU" ||
                            $two_level_postcode == "DL" ||
                            $two_level_postcode == "DH" ||
                            $two_level_postcode == "NE" ||
                            $two_level_postcode == "SR" ||
                            $two_level_postcode == "TS"  )
                    $zone= "5";				
            else if($two_level_postcode == "BA" ||
                            $two_level_postcode == "TA" ||
                            $two_level_postcode == "BS" ||
                            $two_level_postcode == "CF" ||
                            $two_level_postcode == "NP" ||
                            $two_level_postcode == "BH" ||
                            $two_level_postcode == "DT" ||
                            $two_level_postcode == "PO" ||
                            $two_level_postcode == "SO" ||
                            $two_level_postcode == "SP" ||
                            $two_level_postcode == "SA" ||
                            $two_level_postcode == "GL" ||
                            $two_level_postcode == "LD" ||
                            $two_level_postcode == "EX" ||
                            $two_level_postcode == "TQ" )
                    $zone= "6";					
            else if($two_level_postcode == "CT" ||	
                            $two_level_postcode == "CR" ||	
                            $two_level_postcode == "BR" ||	
                            $two_level_postcode == "SM" ||	
                            $two_level_postcode == "BN" ||	
                            $two_level_postcode == "RH" ||	
                            $two_level_postcode == "ME" ||	
                            $two_level_postcode == "TN" ||	
                            $two_level_postcode == "DA" ||	
                            $two_level_postcode == "RM" ||	
                            $two_level_postcode == "IG" ||	
                            $two_level_postcode == "SE" )
                    $zone= "7";						
            else if($two_level_postcode == "HA" ||
                            $two_level_postcode == "NW" ||			
                            $two_level_postcode == "SL" ||			
                            $two_level_postcode == "UB" ||			
                            $two_level_postcode == "W2" ||
                            $two_level_postcode == "W3" ||
                            $two_level_postcode == "W4" ||
                            $two_level_postcode == "W5" ||
                            $two_level_postcode == "W6" ||
                            $two_level_postcode == "W7" ||
                            $two_level_postcode == "W8" ||
                            $two_level_postcode == "W9" )
                    $zone= "0";	
            else if($two_level_postcode == "AL" ||
                            $two_level_postcode == "LU" ||
                            $two_level_postcode == "SP" ||
                            $two_level_postcode == "SG" ||
                            $two_level_postcode == "EN" ||
                            $two_level_postcode == "WD" )							
                    $zone= "8";								
			
            else if ($one_level_postcode == "B" ||  $one_level_postcode == "M")
            $zone= "1";
            else if ($one_level_postcode == "S" || $one_level_postcode == "L")
            $zone= "2";
                                else if ($one_level_postcode == "N")
            $zone= "3";
            else if ($one_level_postcode == "G")
            $zone= "4";
            else if ($one_level_postcode == "E")
            $zone= "7";
            return $zone;
    }
    
   public function recycledShipment($consignment) {
            $output["STATUS"]   =   "SUCCESS";
            return $output;
        }
}
