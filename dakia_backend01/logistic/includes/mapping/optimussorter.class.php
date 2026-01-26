<?php

/*
 * Class dealing with sending data to sorter for sorter shipment processing 
 *
 */

include_classes([
    'hermes.class'
        ], 'labels');
require_once(SETTING_DIR_REMOTE . "includes/3rdparty/Net/SFTP.php");
require_once(SETTING_DIR_REMOTE . "includes/3rdparty/ftpimplicitssl.class.php");

class OptimusSorter {

    private $record_array = array();
    private $record_array1 = array();
    private $record_idx = 0;
    private $link_file = null;
    private $consignmentId = array();

    public function addConsignment($list) {
        $this->record_array[] = $this->getShipmentRecord($list);
        //print_r($this->record_array); die;
        return true;
    }

    private function getShipmentRecord($consignment) {
        if (count($consignment) > 0) {

            $record = "";
            $separator = ";";
            $count = 0;
            foreach ($consignment as $c) {
                //    echo $c->getAccount();
                $this->consignmentId[] = $c->getId();
                $number_pieces = $c->getNumberPieces();
                $service_name = $c->getServiceName();
                if ($number_pieces == 1) {
                    if ($c->getAwb() != "") {

                        //H;JD0002255030374718;Yodel Mini Pack;3HPA;PCZ;CC;CHUTE;SER;
                        //SERVICE TYPE SORT
                        $record .= "H" . $separator;

                        if (in_array(trim($c->getCarrierId()), array("16"))) {

                            $awb = "J" . $c->getAwb();
                        } else if (in_array($c->getServiceCode(), array('STRYMFP24'))) {
                            //,'STRYMRM1P', 'STRYMRM1L', 'STRYMRM2P', 'STRYMRM2L'
                            $awb = $c->getHawb();
                        } else {
                            $awb = $c->getAwb();
                        }

                        //$record .=  $count . $separator;
                        $record .= str_replace(";", "", $awb) . $separator;

                        if ($c->getServiceCode() == 'STDPDDE19' && $c->getWeight() > 3) {
                            $record .= "E-EURO DPD DE - 3KG" . $separator;
                            $record .= str_replace(";", "", "20EURDPDDE") . $separator;
                        } else if ($c->getServiceCode() == 'STDPDNL19' && $c->getWeight() > 3) {
                            $record .= "E-EURO DPD NL - 3KG" . $separator;
                            $record .= str_replace(";", "", "20EURDPD") . $separator;
                        } else {
                            $record .= str_replace(";", "", $service_name) . $separator;
                            $record .= str_replace(";", "", $c->getServiceCode()) . $separator;
                        }
                        $record .= $separator;
                        $record .= $separator;
                        $record .= $separator;
                        $record .= "SER" . $separator;
                        $record .= "\r\n";

                        //SORT TYPE COUNTRY
                        $record .= "H" . $separator;
                        //$record .=  $count . $separator;
                        $record .= str_replace(";", "", $awb) . $separator;
                        if ($c->getServiceCode() == 'STDPDDE19' && $c->getWeight() > 3) {
                            $record .= "E-EURO DPD DE - 3KG" . $separator;
                            $record .= str_replace(";", "", "20EURDPDDE") . $separator;
                        } else if ($c->getServiceCode() == 'STDPDNL19' && $c->getWeight() > 3) {
                            $record .= "E-EURO DPD NL - 3KG" . $separator;
                            $record .= str_replace(";", "", "20EURDPD") . $separator;
                        } else {
                            $record .= str_replace(";", "", $service_name) . $separator;
                            $record .= str_replace(";", "", $c->getServiceCode()) . $separator;
                        }

                        $record .= $separator;
                        $record .= $c->getCountryIsoCode() . $separator;
                        $record .= $separator;
                        $record .= "CC" . $separator;
                        $record .= "\r\n";
                        $count = $count + 2;

                        if ($c->getCountryIsoCode() == "GB" && in_array(trim($c->getCarrierId()), array("16", "147"))) {

                            $count = $count + 1;
                            $record .= "H" . $separator;
                            $record .= str_replace(";", "", $awb) . $separator;
                            $record .= str_replace(";", "", $service_name) . $separator;
                            $record .= str_replace(";", "", $c->getServiceCode()) . $separator;
                            if ($c->getCarrierId() == "147") {
                                $routingCode = $c->getRoutingCodeEur();
                                $hermes = new Hermes();
                                $depotCode = $hermes->getPrimeDepotCode($routingCode);
                                $record .= $depotCode . $separator;
                            } else
                                $record .= $c->getOtherRoutingCode() . $separator;
                            $record .= $separator;
                            $record .= $separator;
                            $record .= "PCZ" . $separator;
                            $record .= "\r\n";
                        }
                        else if(in_array(trim($c->getCarrierId()), array("185"))){
                            if($c->getDestinationWarehouseId() > 0){
                                $warehouse = new Warehouse($c->getDestinationWarehouseId());
                                if($warehouse->getWarehouseCode() != ""){
                                    $count = $count + 1;
                                    $record .= "H" . $separator;
                                    $record .= str_replace(";", "", $awb) . $separator;
                                    $record .= str_replace(";", "", $service_name) . $separator;
                                    $record .= str_replace(";", "", $c->getServiceCode()) . $separator;
                                    $record .= $warehouse->getWarehouseCode() . $separator;
                                    $record .= $separator;
                                    $record .= $separator;
                                    $record .= "PCZ" . $separator;
                                    $record .= "\r\n"; 
                                }
                            }
                        }
                    }
                } else {
                    $parcel = new ParcelFilter();
                    $parcel->addConsignmentIdFilter($c->getId());
                    $parcelList = $parcel->getColumnList('tracking_number');
                    if (count($parcelList) > 0) {

                        foreach ($parcelList as $p) {
                            $count = $count + 1;
                            if ($p->getTrackingNumber() != "") {
                                if ($p->getTrackingNumber() == $c->getAwb() || in_array($c->getCarrierId(), array("102", "16", "185"))) {
                                    $record .= "H" . $separator;
                                    //$record .=  $count . $separator;
                                    if (in_array($c->getCarrierId(), array("102", "16")))
                                        $record .= str_replace(";", "", "J" . $p->getTrackingNumber()) . $separator;
                                    else {
                                        $record .= str_replace(";", "", $p->getTrackingNumber()) . $separator;
                                    }


                                    if ($c->getServiceCode() == 'STDPDDE19' && $c->getWeight() > 3) {
                                        $record .= "E-EURO DPD DE - 3KG" . $separator;
                                        $record .= str_replace(";", "", "20EURDPDDE") . $separator;
                                    } else if ($c->getServiceCode() == 'STDPDNL19' && $c->getWeight() > 3) {
                                        $record .= "E-EURO DPD NL - 3KG" . $separator;
                                        $record .= str_replace(";", "", "20EURDPD") . $separator;
                                    } else {
                                        $record .= str_replace(";", "", $service_name) . $separator;
                                        $record .= str_replace(";", "", $c->getServiceCode()) . $separator;
                                    }
                                    $record .= $separator;
                                    $record .= $separator;
                                    $record .= $separator;
                                    $record .= "SER" . $separator;
                                    $record .= "\r\n";


                                    //SORT TYPE COUNTRY
                                    $record .= "H" . $separator;
                                    //$record .=  $count . $separator;
                                    if (in_array($c->getCarrierId(), array("102")))
                                        $record .= str_replace(";", "", "J" . $p->getTrackingNumber()) . $separator;
                                    else
                                        $record .= str_replace(";", "", $p->getTrackingNumber()) . $separator;
                                    //$record .= str_replace(";", "", $p->getTrackingNumber()) . $separator;
                                    if ($c->getServiceCode() == 'STDPDDE19' && $c->getWeight() > 3) {
                                        $record .= "E-EURO DPD DE - 3KG" . $separator;
                                        $record .= str_replace(";", "", "20EURDPDDE") . $separator;
                                    } else if ($c->getServiceCode() == 'STDPDNL19' && $c->getWeight() > 3) {
                                        $record .= "E-EURO DPD NL - 3KG" . $separator;
                                        $record .= str_replace(";", "", "20EURDPD") . $separator;
                                    } else {
                                        $record .= str_replace(";", "", $service_name) . $separator;
                                        $record .= str_replace(";", "", $c->getServiceCode()) . $separator;
                                    }

                                    $record .= $separator;
                                    $record .= $c->getCountryIsoCode() . $separator;
                                    $record .= $separator;
                                    $record .= "CC" . $separator;
                                    $record .= "\r\n";
                                    $count = $count + 2;

                                    if ($c->getCountryIsoCode() == "GB" && in_array(trim($c->getCarrierId()), array("16", "147"))) {
                                        $count = $count + 1;
                                        $record .= "H" . $separator;
                                        $record .= str_replace(";", "", $p->getTrackingNumber()) . $separator;
                                        $record .= str_replace(";", "", $service_name) . $separator;
                                        $record .= str_replace(";", "", $c->getServiceCode()) . $separator;
                                        if ($c->getCarrierId() == "147") {
                                            $routingCode = $c->getRoutingCodeEur();
                                            $hermes = new Hermes();
                                            $depotCode = $hermes->getPrimeDepotCode($routingCode);
                                            $record .= $depotCode . $separator;
                                        } else
                                            $record .= $c->getOtherRoutingCode() . $separator;
                                        $record .= $separator;
                                        $record .= $separator;
                                        $record .= "PCZ" . $separator;
                                        $record .= "\r\n";
                                    }
                                    else if(in_array(trim($c->getCarrierId()), array("185"))){
                                        if($c->getDestinationWarehouseId() > 0){
                                            $warehouse = new Warehouse($c->getDestinationWarehouseId());
                                            if($warehouse->getWarehouseCode() != ""){
                                                $count = $count + 1;
                                                $record .= "H" . $separator;
                                                $record .= str_replace(";", "", $awb) . $separator;
                                                $record .= str_replace(";", "", $service_name) . $separator;
                                                $record .= str_replace(";", "", $c->getServiceCode()) . $separator;
                                                $record .= $warehouse->getWarehouseCode() . $separator;
                                                $record .= $separator;
                                                $record .= $separator;
                                                $record .= "PCZ" . $separator;
                                                $record .= "\r\n"; 
                                            }
                                        }
                                    }
                                } else {
                                    $record .= "D" . $separator;

                                    if (in_array($c->getCarrierId(), array("102", "16"))) {
                                        $record .= "J" . $p->getTrackingNumber() . $separator;
                                    } else {
                                        $record .= $p->getTrackingNumber() . $separator;
                                    }
                                    $count = $count + 1;
                                    $record .= "\r\n";
                                }
                            }
                        }
                    }
                }
            }

            $record .= "F" . $separator;
            $record .= $count . $separator;

            $record .= "\r\n";
            return $record;
        }
    }

    /**
     * Send consignment bookings to DHL.
     *
     * @return bool - success.
     */
    public function sendBookings() {

        if (count($this->record_array) > 0) {
            $run_number = CarrierDataFileLog::generateRunNumber(10000, 10000, false);
            $linkFile = "ESPN" . sprintf("%09d", $run_number) . ".csv";
            $carrierDataFileLog = new CarrierDataFileLog();
            $carrierDataFileLog->setCarrierId(10000);
            $carrierDataFileLog->setAgentId(10000);
            $carrierDataFileLog->setFileName($linkFile);
            $carrierDataFileLog->setRunNumber($run_number);
            $carrierDataFileLog->save();


            $file_path = SETTING_DIR_ASSETS . "optimus_sorter/Inbox/Processed/";
            if (!file_exists($file_path))
                @mkdir($file_path, 0777, true);

            $file_path1 = $file_path . $linkFile;
            $file_handle1 = fopen($file_path1, 'w');

            foreach ($this->record_array as $record) {
                //t($record, __CLASS__);
                //$newrecord = preg_replace("/[\r\n]+/","\r\n",$record) ;
                fwrite($file_handle1, $record);
            }

            // close file
            fclose($file_handle1);

            if (count($this->consignmentId) > 0) {
                $consignment = new Consignment();
                $consignment->bulkUpdate("optimus_sorter = '1'", " id in ('".implode("','", $this->consignmentId)."')");
            }

            $username = "smarttrack";
            $password = "D4AcYbfpFzzT>5JZ%zS";
            $server = "164.39.218.212";

            $ftp_conn = new FTPImplicitSSL($username, $password, $server, $port = 990, $initial_path = '/Inbox/');
//        echo "<pre>";
//        var_dump($ftp_conn);
//        print_r($ftp_conn);
            $ftp_file = fopen($file_path1, "r") or die("Unable to open file!");
            $file_contents = fread($ftp_file, filesize($file_path1));
            fclose($ftp_file);
//        echo "<br>" . $linkFile;
//        echo "<br />";
//        print_r($file_contents);
          $ftp_conn->upload($linkFile, $file_contents);
            return true;
        }


    }

    public function getSorterImage($sorterImage) {
        if ($sorterImage != '') {
            $file_path = SETTING_DIR_ASSETS . "optimus_sorter/Image/";
            if (!file_exists($file_path))
                @mkdir($file_path, 0777, true);

            $username = "smarttrack";
            $password = "D4AcYbfpFzzT>5JZ%zS";
            $server = "164.39.218.212";

            $testFtp = new FTPImplicitSSL($username, $password, $server, $port = 990, $initial_path = '', $passive_mode = true);
            $fp = fopen(SETTING_DIR_ASSETS . '/optimus_sorter/Image/' . $sorterImage, 'w');
            $testFtp->download("/Image/" . $sorterImage, $fp);
            return true;
        }
    }

}
