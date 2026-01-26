<?php
require_once("../includes/labels/googledistancematrix.class.php");
class PassMyParcel implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $constants = null;
    private $country = null;
    private $user = null;
    private $routineList = null;

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
        $this->serviceValues = new Services($consignment->getServiceId());
        $this->country = new Country($consignment->getCountryId());
        $this->user = SessionManager::getUser();
        /*
         *  Get Service  constants 
         */
        $serviceAgentConstantFilter = new ServiceConstantValueFilter();
        $serviceAgentConstantFilter->addFilter("service_id = '" . $consignment->getServiceId() . "' AND agent_id = '" . $consignment->getAgentId() . "' ");
        $serviceAgentConstant = $serviceAgentConstantFilter->getConstantList("c.constant 'constant_name', scv.constant_value 'constant_value'");
        if (count($serviceAgentConstant) > 0) {
            foreach ($serviceAgentConstant as $serviceAgentConstantData) {
                $this->constants[$serviceAgentConstantData->getConstantName()] = $serviceAgentConstantData->getConstantValue();
            }
        }
        if (trim(@$this->constants['PMP_IDENTIFICATION']) == '' || trim(@$this->constants['PMP_SHIPPER_CONTACT']) == '' || trim(@$this->constants['PMP_SHIPPER_ADDRESS_LINE_1']) == '' || trim(@$this->constants['PMP_SHIPPER_CITY']) == '' || trim(@$this->constants['PMP_SHIPPER_POSTCODE']) == '') {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }
        /*
         *  Get Tracking Number ranges
         */
        $serviceRangeMappingFilter = new ServiceRangeMappingFilter();
        $serviceRangeMappingFilter->addFilter("service_id = '" . $consignment->getServiceId() . "' AND agent_id = '" . $consignment->getAgentId() . "' ");
        $serviceRange = $serviceRangeMappingFilter->getList(" licence_plate_id ");
        if (count($serviceRange) > 0) {
            $licence_plate_id = (int) $serviceRange[0]->getLicencePlateId();
        }
        if ($licence_plate_id < 0) {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "This service does not have tracking number range. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }
        $parcel_list = $consignment->getParcels();
        $parcel_count = sizeof($parcel_list);
        $parcel_idx = 0;
        // Generate label for each parecel
        foreach ($parcel_list as $parcel) {
            if ($parcel->getTrackingNumber() == '') {
                $resultArray = LicencePlate::getLicencePlateNumber($licence_plate_id);
                if (trim($resultArray['STATUS']) == 'ERROR')
                    return $resultArray;
                else
                    $licence_plate = $resultArray["RANGE"];
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
            $page_size = array(150, 100);
            $this->pdf->AddPage("P", $page_size);
            $response = "";
            $response = $this->addWayBill($consignment, $parcel_idx,  $licence_plate);
            $new_page_flag = true;
            ++$parcel_idx;
        }
        $tracking_number[] = $response["MESSAGE"];
       
        if($response["STATUS"] == "SUCCESS")
        {
            $this->pdf->IncludeJS("print();");
            $this->pdf->Output("../_assets/pdf/" . date('Y_m_d') . '/' . $consignment->getId() . ".pdf", "F");
            $output['STATUS'] = 'SUCCESS';
            $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
            $output['TRACKING_NUMBER'] = $tracking_number;
            return $output;
        }
        else
        {
            return $response;
        }
    }

    public function tracking($trackingNumber) {
        
    }

    public function sendData($tracking_numbers = array()) {
        
    }

    public function manifest($consignment) {
        
    }

    public function preAdvice($consignment) {
        
    }

    private function addWayBill(Consignment $consignment, $parcel_idx, $licence_plate) {
        $output = array();
        $this->routineList = $this->getRoutineDetails($consignment->getPostcode());

        if (count($this->routineList) > 0) {
            $destinationpostcode = array();
            foreach ($this->routineList as $key => $routine) {
                $destinationpostcode[$key] = str_replace(" ", "", $routine->getPostcode());
            }
            // print_r($destinationpostcode);
            if (sizeof($destinationpostcode) > 0) {
                $distanceArray = array();
                $debug = false;

                $gmapdisance = GoogleDistanceMatrix::getGoogleDistanceMatrix(str_replace(" ", "", $consignment->getPostcode()), $destinationpostcode, $debug);
                $gmpresult = json_decode($gmapdisance);
                if (count($gmpresult) > 0) {
                    $i = 0;
                    foreach ($gmpresult as $distance) {
                        if ($distance->status == "OK") {
                            $distanceArray[$destinationpostcode[$i]] = $distance->distance->text;
                            $i++;
                        }
                    }

                    $lower_distance = '';
                    $near_postcode = '';
                    if (sizeof($distanceArray) > 0) {
                        $j = 0;
                        foreach ($distanceArray as $postcode => $dis) {
                            $distance_km = (float) trim(str_replace("km", "", $dis));
                            if ($distance_km <= '1.6') {
                                if ($j == 0) {
                                    $lower_distance = trim($distance_km);
                                    $near_postcode = $postcode;
                                    //echo $lower_distance . "----" . $distance_km . "----------------".$near_postcode. "1<br />";
                                    $j++;
                                }
                                if (trim($lower_distance) > trim($distance_km)) {
                                    $lower_distance = $distance_km;
                                    $near_postcode = $postcode;
                                }
                            } else {
                                $output['STATUS'] = 'ERROR';
                                $output['MESSAGE'] = "Delivery shop too far from customer address " . $consignment->getPostcode();
                                return $output;
                            }
                            //  echo $lower_distance . "----" . $distance_km . "----------------".$near_postcode. "1<br />";
                        }
                        // echo $lower_distance;    
                        // exit;       

                        $findmatchRoutine = array_search($near_postcode, $destinationpostcode);

                        if ($findmatchRoutine >= 0) {
                            $routinedetail = $this->routineList[$findmatchRoutine];
                            $storeid = $routinedetail->getStoreid();
                            /*
                              for parcel shop address
                             */
                            $storeName = $routinedetail->getAddressLine1();
                            $storeAddress = $routinedetail->getAddressLine2();
                            $storeCity = $routinedetail->getCity();
                            $storeCity = $routinedetail->getCountry();
                            $storePostcode = $routinedetail->getPostcode();


                            $depotno = $routinedetail->getDepotNo();
                            $depotdesc = $routinedetail->getDepotDescription();
                            $round1 = $routinedetail->getRound1();
                            $round2 = $routinedetail->getRound2();
                            $drop1 = $routinedetail->getDrop1();
                            $drop2 = $routinedetail->getDrop2();
                            $other_routing_code = $storeid . "||" . $depotno;
                            $consignment->setOtherRoutingCode($other_routing_code);
                            $consignment->save();
                        }
                    } else {
                        $output['STATUS'] = 'ERROR';
                        $output['MESSAGE'] = "The service is not available for this postcode " . $consignment->getPostcode();
                        return $output;
                    }
                } else {
                    $output['STATUS'] = 'ERROR';
                    $output['MESSAGE'] = "The service is not available for this postcode " . $consignment->getPostcode()."TAHIR";
                    return $output;
                }
            }
        } else {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "The service is not available for this postcode " . $consignment->getPostcode();
            return $output;
        }
        $x = 5;

        $this->pdf->setFont("helvetica", "B", 9);

        $this->pdf->Text($x, 5, "If undelivered please return to:");
        $this->pdf->setFont("helvetica", "", 8);
        $this->pdf->Text($x, 9, trim(@$this->constants['PMP_SHIPPER_CONTACT']));//"One World Express Inc"
        $this->pdf->Text($x, 13, trim(@$this->constants['PMP_SHIPPER_ADDRESS_LINE_1']));
        $this->pdf->Text($x, 17, trim(@$this->constants['PMP_SHIPPER_ADDRESS_LINE_2']) . trim(@$this->constants['PMP_SHIPPER_ADDRESS_LINE_3']));
        $this->pdf->Text($x, 21, trim(@$this->constants['PMP_SHIPPER_CITY']));
        $this->pdf->Text($x, 25, trim(@$this->constants['PMP_SHIPPER_POSTCODE']));

        $image = "../images/passmyparcel.png";

        $x1 = 74;
        $y1 = 2;
        $w = 20;
        $h = 25;
        $fitbox = 'C';
        $fitbox[1] = 'M';
        $this->pdf->image($image, $x1, $y1, $w, $h, 'PNG', '', '', false, 700, '', false, false, 0, $fitbox, false, false);



        $this->pdf->setFont("helvetica", "B", 10);


        /*
          for parcel shop address
         */
        $this->pdf->Text($x, 35, $storeName);
        $this->pdf->Text($x, 40, $storeAddress);
        $this->pdf->Text($x, 45, $storeCity . " " . $storePostcode);
        $this->pdf->Text($x, 50, 'United Kingdom');


        $this->pdf->Text($x, 57, "Customer: " . $consignment->getContact());
        $this->pdf->setFont("helvetica", "", 8);

        $x2 = $x + 70;
        $this->pdf->setFont("helvetica", "B", 13);
        $this->pdf->Text($x2, 29, $depotno); // 4 Digit SN Depot No. (Routing file)
        $this->pdf->Text($x2, 35, $depotdesc); //6 Char SN Depot Name. (Routing file
        $this->pdf->Text($x2, 41, $storeid); //			6 Digit Smiths News Retailer Number (Store ID from Store feed file) and key definition from Routing file)
        $this->pdf->setFont("helvetica", "B", 13);
        $this->pdf->Text($x2, 49, $round1 . "/" . $drop1); //(Routing file) Wave 1 Round/Drop
        $this->pdf->Text($x2, 57, $round2 . "/" . $drop2); //(Routing file) Wave 2 Round/Drop		



        $this->pdf->setFont("helvetica", "", 6);
        $this->pdf->multicell(50, 15, ucfirst(strtolower($consignment->getNotes())), 0, 'L', '', '', $x, 80);
        $x3 = $x + 55;
        $this->pdf->Text($x3, 80, "Dispatch Date : " . date("d/m/Y"));
        $this->pdf->Text($x3, 83, "Delivery Date : " . date("d/m/Y"));
        $this->pdf->Text($x3, 86, "Weight :            " . number_format($consignment->getWeight(), 2) . "Kg");


        $image1 = "../images/passbyparcel_image1.png";

        $x1 = 5;
        $y1 = 83;
        $w = 90;
        $h = 20;
        $this->pdf->image($image1, $x1, $y1, $w, $h, 'PNG', '', '', false, 700, '', false, false, 0, $fitbox, false, false);

        $this->pdf->Line(0, 100, 100, 100);

        $image2 = "../images/passbyparcel_image2.png";

        $x1 = 5;
        $y1 = 97;
        $w = 90;
        $h = 20;
        $this->pdf->image($image2, $x1, $y1, $w, $h, 'PNG', '', '', false, 700, '', false, false, 0, $fitbox, false, false);
        $awb = $this->barcode2D($licence_plate, $depotno, $storeid);

        $this->pdf->write1DBarcode($awb, 'C128', 5, 65, 55, 7, 0.5, '', 'Y');

        $this->sendEmail($routinedetail, $consignment, $awb);
        $output['STATUS'] = 'SUCCESS';
        $output['MESSAGE'] = $awb;
        return $output;
    }

    private function barcode2D($barcode, $depotno, $storeid = '', $return = false) {
        $clientIdentifier = trim(@$this->constants['PMP_IDENTIFICATION']);//'OW';
        $snDepotNumber = substr($depotno, 1);
        if ($return) {
            $serviceCode = '4';
            $retail_number = "000000";
            $seprator = "|";
            $datamatrix_data = $clientIdentifier . $serviceCode . $barcode . $snDepotNumber . $retail_number . $seprator . "ONE WORLD HOUSE" . $seprator . "BELL HEATH WAY" . $seprator . "" . $seprator . "BIRMINGHAM" . $seprator . "B32 3BZ" . $seprator . "UK" . $seprator . $depotno;
        } else {
            $serviceCode = '1';
            $retail_number = $storeid;
            $datamatrix_data = $clientIdentifier . $serviceCode . $barcode . $snDepotNumber . $retail_number;
        }





        // create new PDF document
        $style = array(
            'position' => '',
            'align' => 'C',
            'stretch' => true,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => false,
            'hpadding' => '1',
            'vpadding' => '1',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255),
            'text' => false,
            'font' => 'helvetica',
            'fontsize' => 8,
            'stretchtext' => 1
        );

        $this->pdf->write2DBarcode($datamatrix_data, 'DATAMATRIX', 40, 120, 20, 20, $style, '', 'Y');
        $this->pdf->setFont("helvetica", "B", 10);
        $this->pdf->Text(30, 143, $clientIdentifier . $serviceCode . " " . $barcode);
        $this->pdf->setFont("helvetica", "", 10);
        $this->pdf->Text(55, 143, " " . $snDepotNumber . $retail_number);

        return $clientIdentifier . $serviceCode . $barcode . $snDepotNumber . $retail_number;
    }

    public function sendEmail($routineList, $consignment, $awb) {
        $email = $consignment->getEmail();
        $to = $email;
        //define the subject of the email
        $subject = "Your Order Number " . $consignment->getHawb() . " is processed and ready for dispatch.";
        $headers = "MIME-Version: 1.0 \r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
        $headers .= "From: One World Express <cs@oneworldexpress.com> \r\n";
        $headers .= "Reply-To: cs@oneworldexpress.com \r\n";


        $message = "Hello, " . $consignment->getContact() . "<br /><br />" . "Good news!";             //add boundary string and mime type specification
        $message .= "<br /><br />";
        $message .= 'Your parcel from <b>' . ucfirst($this->user->getCompany()) . ' </b> with order number <b>' . $consignment->getHawb() . '</b> has been processed and '
                . 'will be dispatched to your local parcel shop where you will be able to collect your parcel. <br/ ><br /> Further details will be sent to you in due course'
                . ' to notify you when your parcel arrives at our sorting hub and also when it will  be ready for pickup.<br /><br /> Your parcel details are below. ';
        $message .= "<br /><br />";
        $message .= '<b>Parcel Tracking ID: </b> ' . $awb . '<br /> You can follow your parcel, by tracking on  www.oneworldexpress.com or www.passmyparcel.com/track';
        $message .= "<br /><br />";
        $message .= "Your local Parcel shop collection address : <br /><br />" . $routineList->getAddressLine1() . ",<br /> " . $routineList->getAddressLine2() . ",<br /> " . $routineList->getCity() . ",<br /> " . $routineList->getPostcode() . "<br /><br />  Tel: " . $routineList->getTelePhone();
        //$message .= "<br /><br />";
        //$message .= 'Please note, your parcel will be kept at this location for 7 days, after which it will be returned to ' . $routineList->getAddressLine1();
        $message .= "<br /><br />";
        $message .= 'Thank you,';
        $message .= "<br /><br/ ><br /><br />";
        $message .= "One World Express";
        $message .= "<br />";
        $message .= "Customer Service Team";
       
        //send the email
        $mail_sent = mail($to, $subject, $message, $headers);
    }

    //Get Routine Details
    public function getRoutineDetails($postcode) {
        $postcode = strtoupper($postcode);

        $postCodeLength = 2;
        $postCode = trim(str_replace(' ', '', $postcode));
        if (strlen($postCode) == 5) {
            $postCodeLength = 2;
        } elseif (strlen($postCode) == 6) {
            $postCodeLength = 3;
        } elseif (strlen($postCode) == 7) {
            $postCodeLength = 4;
        }

        $postCodeA = trim(substr($postCode, 0, $postCodeLength));

        $PmpRoutineFilter = new PmpRoutineFilter();
        $PmpRoutineFilter->addFilter(" SUBSTRING(LOWER(postcode),1," . $postCodeLength . ") = '" . strtolower(DbAccess3::escape($postCodeA)) . "'
							AND char_length(REPLACE(postcode,' ','')) = " . DbAccess3::escape(strlen($postCode)));
        $routineList = $PmpRoutineFilter->getList();
        return $routineList;
    }

    public function recycledShipment($consignment) {
            $output["STATUS"]   =   "SUCCESS";
            return $output;
        }
}
