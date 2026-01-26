<?php
include_classes([
    'carrierdatafilelog.class',
    'carrierdatafilelogfilter.class' 
    ]);
class Correos implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $userAccount = null;
    private $country = null;
    private $constants = null;
    private $chartable = array('T', 'R', 'W', 'A', 'G', 'M', 'Y', 'F', 'P', 'D', 'X', 'B', 'N', 'J', 'Z', 'S', 'Q', 'V', 'H', 'L', 'C', 'K', 'E');
    private $record_array = null;
    private $booking_file = null;
    private $correos_record_idx = 0;

    public function __construct() {
        
    }

    public function validation(Consignment $consignment, Services $service, Country $country) {
        $returnOutput = array();
        $numberPiecesError = '';
        if ($consignment->getNumberPieces() > 1) {
            $numberPiecesError = "This service can not be use for more than one piece shipment. Please check number of pieces and try again.";
            $returnOutput[] = $numberPiecesError;
            return $returnOutput;
        }

        $postcode = $consignment->getPostcode();
        if (strlen($postcode) != 5 || !is_numeric($postcode)) {
            $returnOutput[] = "Postcode should be five digit only.";
            return $returnOutput;
        }
    }

    public function remoteareas($consignment, $carrierObject, $country) {
        
    }

    public function label($consignment, $labelType = 'pdf', $size = '') {

        $output = array();
        $this->serviceValues = new Services($consignment->getServiceId());
        $this->country = new Country($consignment->getCountryId());
        $user = new User($consignment->getUserId());
        $this->userAccount = new CustomerAccount($user->getUserAccountId());

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
        if (trim(@$this->constants['SENDER_CONTACT']) == '' || trim(@$this->constants['SENDER_ADDRESSLINE1']) == '') {
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
        $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf = $pdf;
        // Generate label for each parecel
        foreach ($parcel_list as $parcel) {
            if ($parcel->getTrackingNumber() == '') {
                $resultArray = LicencePlate::getLicencePlateNumber($licence_plate_id);
                if (trim($resultArray['STATUS']) == 'ERROR')
                    return $resultArray;
                else {
                    $range = $resultArray["RANGE"];
                    $barcode = $resultArray["PREFIX"] . $range . $resultArray["SUFIX"] . $consignment->getPostcode();
                    $checkdigit = $this->chartable[$this->getControlCharacter($barcode)];
                    $licence_plate = $barcode . $checkdigit;
                }
                $parcel->setTrackingNumber($licence_plate);
                $parcel->save();
            } else {
                $licence_plate = $parcel->getTrackingNumber();
            }
            $licence_plate_array[$parcel_idx] = $licence_plate;

            $this->pdf->SetPrintFooter(false);
            $this->pdf->SetFooterMargin(0);
            $this->pdf->SetAutoPageBreak(false, 0);
            $page_size = array(154.5, 105.5);
            $this->pdf->AddPage("L", $page_size);
            $this->addWayBill($consignment, $parcel_idx, $parcel_count, $licence_plate);

            $new_page_flag = true;
            ++$parcel_idx;
        }



        $this->pdf->IncludeJS("print();");
        $this->pdf->Output("../_assets/pdf/" . date('Y_m_d') . '/' . $consignment->getId() . ".pdf", "F");
        $output['STATUS'] = 'SUCCESS';
        $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
        $output['TRACKING_NUMBER'] = $licence_plate_array;
        return $output;
    }

    public function tracking($trackingNumber, $trackBy = 'parcel', $EDI = false) 
    {	
        $tracking_url = 'http://aplicacionesweb.correos.es/localizadorenvios/track.asp?accion=LocalizaUno&numero='.$trackingNumber.'&ecorreo=&numeros=&idiomaCorreos=en_GB'; //1ZA66E640496231819 //1Z12345E1512345676
        $response = file_get_contents($tracking_url);
        print_r($response);
        die;
        if ($response != '')
        {
            $postable = strpos($response, '<table id="Table2"');
            $table_start_part =  substr ($response, $postable, strlen($response) );
            $pos2 = strpos($table_start_part, '</table>');
            $table_end_part =  substr($table_start_part, 0 , $pos2 ) . '</table>';		

            $DOM = new DOMDocument();
            @$DOM->loadHTML($table_end_part);
            @$trs = $DOM->getElementsByTagName('tr');
            if($trs->length >0)
            {
                for ($i = 0; $i <= $trs->length - 1; $i++ ) 
                {
                    $td = $trs->item($i); // get the columns in this row							
                    $tds = $td->getElementsByTagName('td');

                    if(trim($tds->item(1)->nodeValue) !== '' && trim($tds->item(1)->nodeValue) !== 'Statuses')
                    {
                        $tracking_date = $tds->item(0)->nodeValue;
                        $tracking_date = str_replace('/', '-', $tracking_date);
                        $tracking_date = date('Y-m-d', strtotime($tracking_date));
                        $innerHtmlNew           .= "<tr>";
                        $innerHtmlNew           .= "<td>";
                        $innerHtmlNew .= $tracking_date;					
                        $innerHtmlNew           .= "</td>";
                        $innerHtmlNew           .= "<td>";	
                        $innerHtmlNew           .= "SPAIN";							
                        $innerHtmlNew           .= "</td>";
                        $innerHtmlNew           .= "<td>";	
                        $innerHtmlNew .= $tds->item(1)->nodeValue;							
                        $innerHtmlNew           .= "</td>";			
                        $innerHtmlNew           .= "<td>";	
                        $innerHtmlNew           .= "</td>";
                        $innerHtmlNew           .= "</tr>";		

                        $status = 	trim($tds->item(1)->nodeValue);	
                        if(strtolower($status) == 'delivered')
                        {
                            $newStatus = 'Delivered';
                            break;
                        }
                        else
                        {
                            $newStatus = 'Intransportation';
                        }

                    }
                }
            } 
            else 
            {
            }	
        }
        else
        {
            //$html  = $this->Get_RM_REG_HUN_Tracking($trackingNumber); need to ask from kazim 
        }
    }

    public function sendData($tracking_numbers = array()) {
        $output = [];
        $agentServiceArray = [];
        $consignmentData = new ConsignmentFilter();
        $consignmentData->addFilter("     s.carrier_id= '192'", "servicefilter");
        $consignmentData->addFilter("     c.send_courier_data= '0'", "consignmentfilter");
        if (!empty($tracking_numbers)) {
            $consignmentData->addFilter("AND pc.tracking_number in ('" . implode("','", $tracking_numbers) . "') and pc.tracking_number <> ''", "parcelJoinFilter");
        }
        $consignmentData->addGroupBy(" c.service_id, c.agent_id ");

        $consignmentServiceAgent = $consignmentData->getColumnList("c.service_id, c.agent_id");
        if (count($consignmentServiceAgent) > 0) {
            foreach ($consignmentServiceAgent as $agentData) {
                $agentServiceArray['services'][] = $agentData->getServiceId();
                $agentServiceArray['agent'][] = $agentData->getAgentId();
            }
        } else {
            $output["STATUS"] = "ERROR";
            $output["MESSAGE"] = "Please check we did not find any agent and services for data to send.";
        }

        if (!empty($agentServiceArray['services'])) {

            foreach ($agentServiceArray['services'] as $key => $serviceid) {

                $serviceid = trim($serviceid);
                $agentid = trim($agentServiceArray['agent'][$key]);

                $this->serviceValues = new Services($serviceid);

                // GET CONSTANTS AS PER SERVICE AND AGENT
                $serviceAgentConstantFilter = new ServiceConstantValueFilter();
                $serviceAgentConstantFilter->addFilter("service_id = '" . $serviceid . "' AND agent_id = '" . $agentid . "' ");
                $serviceAgentConstant = $serviceAgentConstantFilter->getConstantList("c.constant 'constant_name', scv.constant_value 'constant_value'");
                if (count($serviceAgentConstant) > 0) {
                    foreach ($serviceAgentConstant as $serviceAgentConstantData) {
                        $this->constants[$serviceid][$serviceAgentConstantData->getConstantName()] = $serviceAgentConstantData->getConstantValue();
                    }
                }
                if (!empty($this->constants[$serviceid])) {
                    // GET ALL THE CONSIGNMENT WITH THE PARCEL FOR PREPARE  FILE
                    $consignmentShipmentDataFilter = new ConsignmentFilter();
                    $consignmentShipmentDataFilter->addFilter("     c.service_id = '" . $serviceid . "' AND c.agent_id = '" . $agentid . "' ", "consignmentfilter");
                    $consignmentShipmentDataFilter->addFilter("     c.send_courier_data= '0'", "consignmentfilter");
                    if (!empty($tracking_numbers))
                        $consignmentShipmentDataFilter->addFilter("     AND pc.tracking_number in ('" . implode("','", $tracking_numbers) . "') and pc.tracking_number <> ''", "parcelJoinFilter");
                    $consignmentShipmentData = $consignmentShipmentDataFilter->getColumnList(" c.id 'consignment_id', s.carrier_id ,s.code 'service_code',
                     con.region 'country_region', c.service_id, c.weight, c.hawb, c.description, c.company, c.country_id,c.awb,c.date_created,c.value,c.number_pieces,
                      c.contact, c.address_line_1, c.address_line_2, c.city, c.postcode, c.telephone, c.other_routing_code, routing_code_eur,pc.tracking_number, c.currency ");

                    if (count($consignmentShipmentData) > 0) {
                        $consignmentIdArray = [];
                        $run_number = CarrierDataFileLog::generateRunNumber($carrierId, $agentid);
                        $this->booking_file = "FD41SL" . date("YmdHis", time()) . ".TXT";

                        $carrierDataFileLog = new CarrierDataFileLog();
                        $carrierDataFileLog->setCarrierId($carrierId);
                        $carrierDataFileLog->setAgentId($agentid);
                        $carrierDataFileLog->setFileName($this->booking_file);
                        $carrierDataFileLog->setRunNumber($run_number);
                        $carrierDataFileLog->save();

                        foreach ($consignmentShipmentData as $consignmentItemData) {

                            $consignmentId = $consignmentItemData->getConsignmentId();
                            $consignmentIdArray[] = $consignmentId;
                            $carrierId = $consignmentItemData->getCarrierId();
                            $this->record_array[] = $this->getShipmentRecord($consignmentItemData, $this->constants[$serviceid], $this->correos_record_idx);
                            $this->correos_record_idx++;
                        }
                        $carrierId = $this->serviceValues->getCarrierId();
                        $agentid = trim($agentServiceArray['agent'][$key]);

                        if ($this->sendBookings($this->constants[$serviceid])) {
                            if (!empty($consignmentIdArray)) {

                                $sql = "UPDATE consignment SET booked_file_id = '" . $this->booking_file . "' 
                                        WHERE id IN (" . implode("','", $consignmentIdArray) . ") AND id <> '0' 
                                        AND  shipment_status not in ('" . Consignment::STATUS_RECYCLED . "','" . Consignment::STATUS_READY_TO_PRINT . "','" . Consignment::STATUS_INVALID . "')";
                                DbAccess3::runQuery($sql);
                                $output["STATUS"] = "SUCCESS";
                                $output["MESSAGE"] = "System has successfully send data.";
                            } else {
                                $output["STATUS"] = "ERROR";
                                $output["MESSAGE"] = "No consignment found to send data to carrier.";
                            }
                        } else {
                            $output["STATUS"] = "ERROR";
                            $output["MESSAGE"] = "Please check constants, System not able to find FTP details to send data to fastway. Please fix it ASAP";
                        }
                    } else {
                        
                    }
                }
            }
        }
    }

    public function manifest($consignment) {
        
    }

    public function preAdvice($consignment) {
        
    }

    private function addWayBill(Consignment $consignment, $parcel_idx, $parcel_count, $licence_plate) {

        $handling = $this->serviceValues->getCode();

        $service_image = "../images/domicilio.png";
     
        //logo top left image.
        $this->pdf->Image('../images/correos.jpg', 6, 2, 25, 10);

        //set the font to size 9 Bold
        $this->pdf->SetFont('arial', 'B', 9);

        //set the image in this place.
        $this->pdf->Image('../images/paq72.jpg', 60, 5, 18, 18);



        //get the X,Y
        $y = $this->pdf->GetY();
        $x = $this->pdf->getX();
        $this->pdf->SetXY($x - 6, $y + 22);
        //rotate the text starting.
        $this->pdf->StartTransform();
        $this->pdf->Rotate(+90);

        $this->pdf->Cell(20, 0, 'Remitente', 0, 1, 'L', 0, '');

        //rotate the text finish.
        $this->pdf->StopTransform();



        $this->pdf->Rect(120, 7, 17, 17);
        if ($consignment->getValue() < 15)
            $this->pdf->Text(110, 7, "L", false, false, true, 0, 0, 'C');
        else if ($consignment->getValue() >= 15 && $consignment->getValue() < 135)
            $this->pdf->Text(110, 7, "M", false, false, true, 0, 0, 'C');
        else if ($consignment->getValue() >= 135)
            $this->pdf->Text(110, 7, "H", false, false, true, 0, 0, 'C');

        $this->pdf->Line(120, 12, 137, 12);

        $this->pdf->Text(110, 13, $this->userAccount->getUserAccount(), false, false, true, 0, 0, 'C');

        $this->pdf->Line(120, 18, 137, 18);

        $this->pdf->Text(111, 20, $this->country->getRegion(), false, false, true, 0, 0, 'C');

        //start of sender details.
        $this->pdf->SetFont('', $style = 'L', $size = 8);
        $this->pdf->Text(7, 15, $this->constants["SENDER_CONTACT"]); //"ONE WORLD"
        $this->pdf->Text(7, 17.5, $this->constants["SENDER_COMPANY"]); // "CORREOS CAM 2"
        $this->pdf->Text(7, 20, $this->constants["SENDER_ADDRESSLINE1"]); //"CTRA. VILLAVERDE-VALLECAS KM 3,5"
        $this->pdf->Text(7, 22.5, $this->constants["SENDER_POSTCODE"] . " " . $this->constants["SENDER_CITY"]); //"28070 MADRID"
        $this->pdf->Text(7, 25, "REF: " . $consignment->getReference());
        $this->pdf->Text(7, 27.5, "HAWB: " . $consignment->getHawb());

        $this->pdf->SetXY(4, 53);
        //rotate the text starting.
        $this->pdf->StartTransform();
        $this->pdf->Rotate(+90);
        $this->pdf->Cell(20, 0, 'Destinatario', 0, 1, 'C', 0, '');
        $this->pdf->StopTransform();

        //dashed line break.
        $this->pdf->Line(7, 32, 75, 32, array('dash' => '1'));
        //clear the line effect otherwise it will effect all borders afterwards.
        $this->pdf->Line(0, 0, 0, 0, array('dash' => '0'));

        $this->pdf->SetFont('', '', $size = 8);
        if ($consignment->getCompany() != '')
            $this->pdf->Text(7, 34, $consignment->getCompany());
        if ($consignment->getContact() != '')
            $this->pdf->Text(7, 37, $consignment->getContact());

        $address = $consignment->getAddressLine1() . ', ' . $consignment->getAddressLine2() . ', ' . $consignment->getAddressLine3();

        $this->pdf->MultiCell(80, 3, $address, $border = '0', $align = 'L', $fill = 0, $ln = 1, 7, 40);

        $this->pdf->Text(7, 47, $consignment->getPostcode() . " " . $consignment->getCity());


        //dashed line break.
        $this->pdf->Line(7, 50, 75, 50, array('dash' => '1'));
        $this->pdf->Line(0, 0, 0, 0, array('dash' => '0'));

        $this->pdf->SetFont('', '', $size = 6);
        $this->pdf->Text(7, 50, 'Telefone:' . $consignment->getTelephone());
        $this->pdf->Text(7, 52, 'Observations:' . $consignment->getNotes());
        $this->pdf->SetFont('', $style = 'L', $size = 9);
        $this->pdf->Text(7, 56, 'Codigo Bulto: ' . $licence_plate);

        $y = $this->pdf->GetY();
        $x = $this->pdf->getX();

        $this->pdf->SetXY($x - 33, $y - 1);

        $this->pdf->SetFont('', $style = 'L', $size = 9);
        $this->pdf->StartTransform();
        $this->pdf->Rotate(+90);
        $this->pdf->MultiCell(20, 0, 'Valores<br />Anadidos', $border = '0', $align = 'C', $fill = 0, $ln = 1, $x = '', $y = '', $reseth = true, $reseth = 0, $ishtml = true, $autopadding = false, $maxh = 0);
        $this->pdf->StopTransform();

        $this->pdf->Image($service_image, 120, 38, 15, 15);

        $this->pdf->write1DBarcode($licence_plate, 'C128', 7, 78, 95, 25, 5, '', 'Y');


        $this->pdf->SetFont('', $style = 'L', $size = 6);
        $this->pdf->MultiCell(20, 30, 'Bulto:<br />' . ($parcel_idx + 1) . '/' . $parcel_count . '<br /><br/>Peso:<br />' . $consignment->getWeight() . ' Kg<br /><br/>Peso Vol.:<br />' . $consignment->getWeight() . ' Kg<br /><br/>Fecha etiquetado:<br />' . date("Y-m-d H:i:s", $consignment->getDateLabelCreated()), $border = '0', $align = 'L', $fill = 0, $ln = 0, 125, 70, $reseth = true, $reseth = 0, $ishtml = true, $autopadding = false, $maxh = 0);
    }

    private function getShipmentRecord(Consignment $consignment, $constants, $correos_record_idx) {

        $vol_weight = number_format($consignment->getVolWeight(), 2);

        $parcel_weight = number_format($consignment->getWeight(), 2);

        if ($parcel_weight > $vol_weight) {
            $weight = $parcel_weight;
        } else {
            $weight = $vol_weight;
        }

        $barcode = $consignment->getAwb();


        $tab = "\t";
        if ($correos_record_idx == 0)
            $record = 'C' . $tab;
        else if ($correos_record_idx > 0)
            $record = 'R' . $tab;



        $company = utf8_encode($consignment->getCompany());

        $record .= '2014v001' . $tab;
        //product code
        if ($this->serviceValues->getCode() == "CORRNPO")
            $record .= 'S0133' . $tab;
        else
            $record .= 'S0132' . $tab;
        //FRANKING TYPE
        $record .= 'FP' . $tab;
        //LABELING CODE
        $record .= '41SL' . $tab;
        //contract number
        $record .= $constants["CORREOS_CUSTOMERNO"] . $tab; //54013300
        //Customer Number
        $record .= $constants["CORREOS_FRANKINGNO"] . $tab; //'80439182' 
        //FRANKING NUMBER
        $record .= '' . $tab;
        //amount franked
        $record .= '' . $tab;
        //Parcel Number
        $record .= $barcode . $tab;
        //Dispatch Number / unsure right now.
        $record .= '' . $tab;
        //Referencia de la expedici�n ?? refrence of som kind im leaving it blank.
        $record .= '' . $tab;
        //Autorizaci�n entregas parciales ?? not sure leaving it blank..
        $record .= '' . $tab;
        //number of pieces
        $record .= $consignment->getNumberPieces() . $tab;
        //Number of bulk items (always set to 1?)
        $record .= '1' . $tab;
        //Manifest Number??? not sure what to set this as so im setting 0
        $record .= "MD41SL04" . date("YmdHis", time()) . "01" . $tab;
        //promo code??????
        $record .= '' . $tab;
        //case of return
        $record .= '' . $tab;
        //contact
        $contact = substr($consignment->getContact(), 0, 20);
        if ($contact == "")
            $contact = substr($consignment->getCompany(), 0, 20);
        $record .= utf8_encode($contact) . $tab;
        //surname 1
        $record .= '' . $tab;
        //surname 2
        $record .= '' . $tab;
        //vat number
        $record .= '' . $tab;
        //company
        //company
        $record .= '' . $tab;

        //contact again?
        $record .= '' . $tab;
        $record .= '' . $tab;


        //addres line1
        $record .= utf8_encode(substr($consignment->getAddressLine1() . " " . $consignment->getAddressLine2(), 0, 50)) . $tab;
        //number
        $record .= filter_var(utf8_encode(substr($consignment->getAddressLine1(), 0, 5)), FILTER_SANITIZE_NUMBER_INT) . $tab;
        //portal???
        $record .= '' . $tab;
        //Bloque
        $record .= '' . $tab;
        //Escalera
        $record .= '' . $tab;
        //Floor?
        $record .= '' . $tab;
        //Door???
        $record .= '' . $tab;
        //City
        $city = substr($consignment->getCity(), 0, 20);
        if ($city == "")
            $city = substr($consignment->getAddressLine3(), 0, 20);
        $record .= utf8_encode($city) . $tab;
        //County
        $record .= utf8_encode($city) . $tab;
        //PostCode
        $record .= utf8_encode($consignment->getPostCode()) . $tab;
        //zip???
        $record .= '' . $tab;
        //pais?
        $record .= '' . $tab;
        //Elected Office??
        $record .= '' . $tab;
        //postcode international???
        $record .= '' . $tab;
        //Apartado Postal destino???
        $record .= '' . $tab;
        //Mobile number
        $record .= '' . $tab;
        //Email
        $record .= '' . $tab;
        //Customer Reference
        $record .= utf8_encode($consignment->getHAWB()) . $tab;
        //Type of delivery		
        if ($this->serviceValues->getCode() == "CORRNPO")
            $record .= 'OR' . $tab;
        else
            $record .= 'ST' . $tab;
        //weight in grams
        $record .= ($weight * 1000) . $tab;
        //Largo
        $record .= '' . $tab;
        //Alto
        $record .= '' . $tab;
        //Ancho
        $record .= '' . $tab;
        //Seguro
        $record .= '' . $tab;
        //Importe Seguro
        $record .= '' . $tab;
        //Reembolso
        $record .= '' . $tab;
        //Importe Reembolso
        $record .= '' . $tab;
        //Tipo Reembolso
        $record .= '' . $tab;
        //N�mero de cuenta
        $record .= '' . $tab;
        //Entrega Exclusiva Destinatario
        $record .= '' . $tab;
        //Formato de Prueba entrega
        $record .= '' . $tab;
        //Referencia eAR � P.E.E
        $record .= '' . $tab;
        //Informaci�n del remitente para eAr � P.E.E.
        $record .= '' . $tab;
        //Entrega con Recogida
        $record .= '' . $tab;
        //Descripci�n envio a recoger
        $record .= '' . $tab;
        //Imprimir etiqueta
        $record .= '' . $tab;
        //C�digo del env�o de ida asociado
        $record .= '' . $tab;
        //Generar env�o de vuelta
        $record .= '' . $tab;
        //C�digo del env�o de vuelta
        $record .= '' . $tab;
        //Fecha de caducidad del env�o de vuelta
        $record .= '' . $tab;
        //Env�o de vuelta permite embalaje
        $record .= '' . $tab;
        //C�digo etiquetador del env�o de vuelta
        $record .= '' . $tab;
        //N�mero SMS destinatario
        $record .= '' . $tab;
        //N�mero SMS remitente
        $record .= '' . $tab;
        //Idioma SMS remitente
        $record .= '' . $tab;
        //Idioma SMS destinatario
        $record .= '' . $tab;
        //Recogida a domicilio
        $record .= '' . $tab;
        //Devoluci�n Albaran
        $record .= '' . $tab;
        //Reparto en S�bado
        $record .= '' . $tab;
        //Entrega en fecha determinada
        $record .= '' . $tab;
        //Entrega en franja horaria
        $record .= '' . $tab;
        //Embalaje Prepagado
        $record .= '' . $tab;
        //C�digo embalaje prepago
        $record .= '' . $tab;
        //Admission Point Code
        $record .= '2812796' . $tab;
        //Frase promocional
        $record .= '' . $tab;
        //Fecha de dep�sito prevista
        $record .= '' . $tab;
        //Observaciones 1
        $record .= '' . $tab;
        //Observaciones 2
        $record .= '' . $tab;
        //Instrucciones de devolucion en caso de no entrega para paquetes internacionales
        $record .= '' . $tab;
        //Type of shipment
        $record .= '2' . $tab;
        //Env�o comercial
        $record .= '' . $tab;
        //Factura superior a 500 euros
        $record .= '' . $tab;
        //DUA de exportaci�n con Correos
        $record .= '' . $tab;
        //Cantidad-1
        $record .= '' . $tab;
        //Descripci�n de mercanc�a-1
        $record .= '' . $tab;
        //Peso neto-1
        $record .= '' . $tab;
        //Valor neto-1
        $record .= '' . $tab;
        //N Tarifario-1
        $record .= '' . $tab;
        //Pa�s de Origen-1
        $record .= '' . $tab;
        //Cantidad-2
        $record .= '' . $tab;
        //Descripci�n de mercanc�a-2
        $record .= '' . $tab;
        //Peso neto-2
        $record .= '' . $tab;
        //Valor neto-2
        $record .= '' . $tab;
        //N Tarifario-2
        $record .= '' . $tab;
        //Pa�s de Origen-2
        $record .= '' . $tab;
        //Cantidad-3
        $record .= '' . $tab;
        //Descripci�n de mercanc�a-3
        $record .= '' . $tab;
        //Peso neto-3
        $record .= '' . $tab;
        //Valor neto-3
        $record .= '' . $tab;
        //N Tarifario-3
        $record .= '' . $tab;
        //Pa�s de Origen-3
        $record .= '' . $tab;
        //Se adjunta factura
        $record .= '' . $tab;
        //Se adjunta licencia
        $record .= '' . $tab;
        //Se adjunta certificado
        $record .= '' . $tab;
        //Name of the customer
        $record .= $constants["SENDER_CONTACT"] . $tab;
        //Apellido 1
        $record .= '' . $tab;
        //Apellido 2
        $record .= '' . $tab;
        //Nif
        $record .= '' . $tab;
        //Sender Name
        $record .= $constants["SENDER_COMPANY"] . $tab;
        //Persona contacto
        $record .= '' . $tab;
        //Tipo direcci�n
        $record .= '' . $tab;
        //Sender Address
        $record .= $constants["SENDER_ADDRESSLINE1"] . $tab;
        //N�mero
        $record .= '' . $tab;
        //portal
        $record .= '' . $tab;
        //Bloque
        $record .= '' . $tab;
        //Escalera
        $record .= '' . $tab;
        //Piso
        $record .= '' . $tab;
        //Puerta
        $record .= '' . $tab;
        //City
        $record .= $constants["SENDER_CITY"] . $tab;
        //Provincia
        $record .= '' . $tab;
        //Postal Code
        $record .= $constants["SENDER_POSTCODE"] . $tab;
        //Tel�fono contacto remitente
        $record .= '' . $tab;
        //Email
        $record .= '' . $tab;
        //Numero Apartado reembolso
        $record .= '' . $tab;
        //Codigo de red asociado a giro pago oficina
        $record .= '' . $tab;
        //END.
        $record .= 'E' . "\r\n";

        return $record;
    }

    private function getControlCharacter($code) {
        $total = 0;
        //loop through each character and set the value of each letter.
        for ($i = 0; $i < strlen($code); $i++) {
            $total = ord($code[$i]) + $total;
        }
        //remainder the total by 23
        $total = fmod($total, 23);

        return $total;
    }

    private function sendBookings($ftpConstants) {
        require_once(SETTING_DIR_REMOTE . "includes/3rdparty/Net/SFTP.php");
        require_once(SETTING_DIR_REMOTE . "includes/3rdparty/Crypt/RSA.php");
        if (sizeof($this->record_array) > 0) {
            $path = SETTING_DIR_ASSETS . "data_send/correos_booking/" . date("Y_m_d") . "/";
            if (!file_exists($path))
                @mkdir($path, 0777, true);

            $file_path = $path . $this->booking_file;
            chmod($path, 0777);

            // create file
            $file_handle = @fopen($file_path, 'w');
            $i = 0;
            foreach ($this->record_array as $record) {
                $i++;
                fwrite($file_handle, $record);
            }
            if (count($this->record_array) == 1)
                $headerrecord = preg_replace('/C/', 'U', $this->record_array[0], 1);
            else
                $headerrecord = preg_replace('/R/', 'F', $this->record_array[$i], 1);
            fwrite($file_handle, $headerrecord);
            // close file
            fclose($file_handle);


            if (isset($ftpConstants['CORREOS_FTP_SITE']) && trim($ftpConstants['CORREOS_FTP_SITE']) != '') {
                $SETTING_FTP_USER = $ftpConstants['CORREOS_FTP_USER'];
                $SETTING_FTP_SITE = $ftpConstants['CORREOS_FTP_SITE'];

                $ssh = new Net_SFTP($SETTING_FTP_SITE);
                $private_key = SETTING_DIR_REMOTE . 'includes/3rdparty/credentials/correos-private-rsa.ppk';

                $key = new Crypt_RSA();

                if ($key->loadKey(file_get_contents($private_key)) === false) {
                    exit("private key loading failed!");
                }

                if (!$ssh->login($SETTING_FTP_USER, $key)) {
                    mail("itsupport@oneworldexpress.com", "CORREOS LOGIN FAILED", "CORREOS LOGIN FAILED");
                    return false;
                }


                // puts a three-byte file named filename.remote on the SFTP server
                $ssh->put($private_key, '777');
                // puts an x-byte file named filename.remote on the SFTP server,
                // where x is the size of filename.local
                if ($ssh->put("/entrada/" . $this->booking_file, $file_path, NET_SFTP_LOCAL_FILE) === false) {
                    mail("itsupport@oneworldexpress.com", "CORREOS UNABLE TO UPLOAD FILE", $this->booking_file);
                }


                $this->link_file = NULL;
                $this->record_array = NULL;
                return true;
            } else {
                return false;
            }
        }
    }

    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }

}
