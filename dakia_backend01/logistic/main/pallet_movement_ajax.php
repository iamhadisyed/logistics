<?php

require_once("../includes/settings/config.inc.php");

if ($_POST['action'] == 'movepallet') {


    $pallet_number = trim($_POST['pallet_number']);
    $vehicle_number = trim($_POST['vehicle_number']);
    $locationid = $_POST['locationid'];
    $moveType = $_POST['moveType'];
    $date = $_POST['date'];
    $hours = $_POST['hours'];
    $mins = $_POST['mins'];

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

    $con_list = '';
    $pallet = '';
    $palletFilter = new PalletFilter();
    $palletFilter->addPalletNumberFilter($pallet_number);
    $list = $palletFilter->getColumnList("id, palletno, date_dispatch");

    if (count($list) == 0) {
        $pallet_number = str_replace('JJD', 'JD', $pallet_number);
        /////////// YODEL SCANNING WE NEED TO REPLACE JJD WITH JD

        $track_arr = str_split($pallet_number);

        if ($track_arr[0] == '%') { //DPD germany ignore first 8 and last 7 characters
            $pallet_number = substr($pallet_number, 8, 22 - 8);
        }



        $conFilter = new ConsignmentFilter();
        $conFilter->AddAwbFilter($pallet_number);
        $con_list = $conFilter->getColumnList("id, awb");
    }

    $user = SessionManager::getUser();

    if ($user->getId() == 0) {
        $arr = array('result' => 'error',
            'message' => 'Your session has expired. Please login again.');
        echo json_encode($arr);
        return;
    } elseif (count($list) == 0 && count($con_list) == 0) {
        $arr = array('result' => 'error',
            'message' => $pallet_number . ' does not exist.');
        echo json_encode($arr);
        return;
    }



    if ($moveType == 'Movement') { ////////// MOVEMENT //////////////////

        $location = new Location($locationid);
        $locationName = $location->getName();

        $palletLocation = new PalletLocation();

        if (count($list) > 0) {
            $pallet = $list[0];
            $palletId = $pallet->getId();
            $palletLocation->setPalletId($palletId);
        } else {
            $consignment = $con_list[0];
            $conid = $consignment->getId();
            $palletLocation->setConsignmentId($conid);
        }


        $palletLocation->setLocationId($locationid);

        $palletLocation->setCreatedBy($user->getId());

        $palletLocation->setDateCreated($dateTime);
        //$palletLocation->setComments($vehicle_number);
        $palletLocation->save();

        $arr = array('result' => 'success',
            'message' => $pallet_number . ' moved to ' . $locationName . ".");
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


if ($_POST['action'] == 'loadlocationdropdown') {
    $locationType = $_POST['locationtype'];

    //echo "location type " . $locationType;

    $data = locationDropdown($locationType);


    //$arr = array($data);


    echo json_encode($data);
}

function locationDropdown($action = '') {


    $user = Sessionmanager::getUser();

    $locationFilter = new LocationFilter();
    $locationFilter->AddActiveFilter("Y");
    $locationFilter->AddWarehouseIdFilter($user->getWarehouseId());
    if ($action == 'Returns') {
        $locationFilter->addReturnsLocationNameLikeFilter();
    } else {
        $locationFilter->addReturnsLocationNameLikeFilter("not");
    }

    $locationListData = $locationFilter->getColumnList("id, name, active");

    //echo "count : " . count($locationListData);


    $locationArray = array();

    if (count($locationListData) > 0) {
        $count = 0;

        //echo "location name entering";

        foreach ($locationListData as $locationItem) {
            //echo "location name " . $locationItem->getName();
            $locationArray[$count]['name'] = $locationItem->getName();
            $locationArray[$count++]['id'] = $locationItem->getId();
        }
    }

    //echo "<pre>";
    //print_r($locationArray);

    return $locationArray;
}

?>