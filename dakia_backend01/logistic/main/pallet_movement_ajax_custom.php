<?php

require_once("../includes/settings/config.inc.php");

if ($_POST['action'] == 'movepallet') {

    $locDataArr = array();
    $arr_update_scan_numbers = array();
    $NumberofUniqueCode = 0;
    $user = SessionManager::getUser();

    $pallet_number = $_POST['pallet_number'];
    $vehicle_number = trim($_POST['vehicle_number']);
    $locationid = $_POST['locationid'];
    $moveType = $_POST['moveType'];
    $date = $_POST['date'];
    $hours = $_POST['hours'];
    $mins = $_POST['mins'];

    //$arr_trackingnumbers = $pallet_number;
    //echo "tracking numbers" . $_POST['pallet_number'];
    //print_r($pallet_number);	
    //die;

    if ($hours == '')
        $hours = "00";

    if ($mins == '')
        $mins = "00";

    if ($date != '')
        $dateTime = date("Y-m-d G:i", strtotime($date . " " . $hours . ":" . $mins));
    else
        $dateTime = date("Y-m-d G:i:s");

    //if($locationid == '' || $locationid == 0)
    //$locationid = 5; /// DISPATCH			
    //print_r($pallet_number);
    //die;

    if (is_array($pallet_number) && count($pallet_number) > 0) { //////////////////////////// scan tracking numbers list ////////////////
        foreach ($pallet_number as $trackingNumber) {
            $trackingNumber = trim(ParseTrackingNumber::Parse($trackingNumber));
            if ($trackingNumber != '')
                $arr_update_scan_numbers[] = $trackingNumber;
        }

        if (count($arr_update_scan_numbers) > 0) {
            $str_trackingnumbers = "'" . implode("','", $arr_update_scan_numbers) . "'";
            $conFilter = new ConsignmentFilter();
            $conFilter->addawbFilterList($str_trackingnumbers);
            $con_list = $conFilter->getColumnList("id, awb");

            foreach ($con_list as $consignment) {
                $foundArr[] = $consignment->getAwb();
            }

            $diff = array_values(array_diff($arr_update_scan_numbers, $foundArr));  //// IF TRACKING NUMBERS NOT FOUND IN LIST ////
        }
    } elseif (trim($pallet_number) != '') {  /////////////// CHECK IF ITS A PALLET NUMBER OR SINGLE TRACKING NUMBER///////////////////
        $pallet_number = trim($pallet_number);
        $palletFilter = new PalletFilter();
        $palletFilter->addPalletNumberFilter($pallet_number);
        $list = $palletFilter->getColumnList("id, palletno, date_dispatch");

        if (count($list) == 0) {
            $conFilter = new ConsignmentFilter();
            $conFilter->AddAwbFilter($pallet_number);
            $con_list = $conFilter->getColumnList("id, awb");

            //print_r($con_list);
            //die;
        }
    }




    if ($user->getId() == 0) {
        $arr = array('result' => 'error',
            'message' => 'Your session has expired. Please login again.');
        echo json_encode($arr);
        return;
    } elseif (count($list) == 0 && count($con_list) == 0) {  ////////// NOT A TRACKING NUMBER AND NOT A PALLET NUMBER /////////
        if (is_array($pallet_number)) {
            $arr = array('result' => 'error',
                'message' => $str_trackingnumbers . ' does not exist.');
        } else {
            $arr = array('result' => 'error',
                'message' => $pallet_number . ' does not exist.');
        }

        echo json_encode($arr);
        return;
    } elseif (count($diff) > 0) {
        $location = new Location($locationid);
        $locationName = $location->getName();

        $arr = array('result' => 'error',
            'message' => 'Please remove the' . implode(',', $diff) . ' not found tracking numbers and then try again.');
        mail("kazim@oneworldexpress.com", "Not found numbers on location $locationName", "Not found numbers on the system does not move to location $locationName 
					 		 <br><br>" . implode(',', $diff));
    }



    if ($moveType == 'Movement') { ////////// MOVEMENT //////////////////

        $location = new Location($locationid);
        $locationName = $location->getName();

        $palletLocation = new PalletLocation();

        if (count($list) > 0) {
            $pallet = $list[0];
            $palletId = $pallet->getId();
            $palletLocation->setPalletId($palletId);
            $palletLocation->setLocationId($locationid);
            $palletLocation->setCreatedBy($user->getId());
            $palletLocation->setDateCreated($dateTime);
            $palletLocation->save();

            $arr = array('result' => 'success',
                'message' => $pallet_number . ' moved to ' . $locationName . ".");
        } else {
            //print_r($con_list);
            //die;

            foreach ($con_list as $consignment) {
                $locDataArr[$NumberofUniqueCode]['locationid'] = $locationid;

                $locDataArr[$NumberofUniqueCode]['palletid'] = 0;
                $locDataArr[$NumberofUniqueCode]['consignmentid'] = $consignment->getId();
                $locDataArr[$NumberofUniqueCode]['date_created'] = $dateTime;
                $locDataArr[$NumberofUniqueCode]['createdby'] = $user->getId();
                $NumberofUniqueCode++;
            }



            $result = PalletLocation::bulkDataInsert($locDataArr);

            //echo "result " . $result;


            if ($result == 1) {
                $arr = array('result' => 'success',
                    'message' => 'Total: ' . $NumberofUniqueCode . ' <br>' . $str_trackingnumbers . ' moved to ' . $locationName . ".");
            } else {
                $arr = array('result' => 'error',
                    'message' => "Sql query failed due to some error. Please try again.");
            }
        }
    } else {   ///////////////// DISPATCHED ////////////////////


        if (count($list) > 0) {
            $pallet = $list[0];
            $palletId = $pallet->getId();
            //$palletLocation->setPalletId($palletId);

            $dateDispatch = trim($pallet->getDateDispatch());


            if ($dateDispatch == '') {

                //echo "date dispatch ";

                $pallet->setDateDispatch($dateTime);
                $pallet->setDispatchUserId($user->getId());
                $pallet->setComments($vehicle_number);
                $pallet->save();


                $consignmentFilter = new ConsignmentFilter();
                $consignmentFilter->AddPalletNumberFilter($pallet_number);
                $consignmentFilter->addFieldEqualFilter('consignment_status', '<>', 'recycled');
                $consignmentFilter->addFieldEqualFilter('consignment_status', '<>', 'hold');
                $con_list = $consignmentFilter->getColumnList("awb");

                if (count($con_list) > 0) {
                    $arr_update_scan_numbers = array();

                    foreach ($con_list as $con) {
                        $arr_update_scan_numbers[] = $con->getAwb();
                    }

                    TrackingData::AddVirtualTrackingToScanParcels($arr_update_scan_numbers, $user, "Dispatched", $dispatchdate, "", "Dispatched from OWE warehouse");
                }

                $message = "Pallet dispatached from warehouse.";
            } else {
                $message = "Pallet already dispatched from warehouse on " .
                        date("d-m-Y", $pallet->getDateDispatch());
            }


            $palletLabel = new PalletDispatchLabel();
            $pallet_label_link = $palletLabel->buildPDFDocuments($pallet->getId(), $pallet->getPalletNo());
        } else {
            $consignment = $con_list[0];
            $conid = $consignment->getId();
            $awb = $consignment->getAwb();
            $arr_update_scan_numbers = array($awb);
            $message = "$awb dispatached from warehouse.";
            //$palletLocation->setConsignmentId($conid);

            TrackingData::AddVirtualTrackingToScanParcels($arr_update_scan_numbers, $user, "Dispatched", $dispatchdate, "", "Dispatched from OWE warehouse");
        }




        //echo $dateDispatch;
        //$pallet_label_link = PalletLabel::buildPDFDocuments($pallet->getId() , $pallet->getPalletNo());

        $arr = array('result' => 'success',
            'label' => $pallet_label_link,
            'message' => $message);
    }



    echo json_encode($arr);
    exit;
}
?>