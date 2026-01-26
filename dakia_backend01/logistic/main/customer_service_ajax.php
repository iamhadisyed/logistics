<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'country.class',
    'countryfilter.class',
    'trackingdata.class',
    'trackingdatafilter.class',
    
]);
class Page extends BasePage {
    /*     * *
     * Controller logic
     */
    protected function init() {
        $this->user = SessionManager::getUser();
        $allouedAcccounts = CustomerAccount::accountSubAccount($this->user->getUserAccountId(), 0, true);
        if(!empty($_GET['top']) && $_GET['top'] == 'top-countires'){
            $country = [];
            $countryObj = new CountryFilter();
            $dataCountry = $countryObj->getCSTopCountries($allouedAcccounts, 0);
            $value = [];
            if(!empty($dataCountry)){
                foreach($dataCountry as $key => $data){
                   
                    $value[$data->getName()] = $data->getId();
                }
                arsort($value);
                $count = 0;
                foreach($value as $key => $dataCountry){
                    if($count <= '19'){
                        $country[] = array(
                            'country' => $key,
                            'value' => $dataCountry
                        );
                    }
                    $count++;
                }
            }
            echo json_encode($country);
            exit;
        }
        
        if(!empty($_GET['serialchart']) && $_GET['serialchart'] == 'serial-worse' && !empty($_GET['param'])){
            $topWorseGoodService =[];
            $trackindDataFilter = new TrackingDataFilter();
            if($_GET['param'] == 'today'){
                $trackingObj = $trackindDataFilter->getDashboardCSDelivered($allouedAcccounts, 0,0,date('Y-m-d'),'','INNER JOIN `services` s ON s.id = c.`service_id`',',s.`name` AS track_point',',s.id');
            }else if($_GET['param'] == 'week'){
                $trackingObj = $trackindDataFilter->getDashboardCSDelivered($allouedAcccounts, 0,0,date("Y-m-d",  strtotime("-7 day")),date("Y-m-d"),'INNER JOIN `services` s ON s.id = c.`service_id`',',s.`name` AS track_point',',s.id');
            }else if($_GET['param'] == 'month'){
                $trackingObj = $trackindDataFilter->getDashboardCSDelivered($allouedAcccounts, 0,0,date('Y-m-d',  strtotime("-30 day")),date('Y-m-d'),'INNER JOIN `services` s ON s.id = c.`service_id`',',s.`name` AS track_point',',s.id');
            }
            if(!empty($trackingObj)){
                $serviceGood = [];
                $totalDelivered = [];
                $deliveryOnTime = [];
                $deliveryOutTime = [];
                foreach ($trackingObj as $key => $data) {
                    $trackingStatusCodes = explode(',', $data->getGroupStatusCode());
                    $deleivery_ex_hub = explode(',', $data->getGroupDateCreated());
                    $receivedHub = array_search(146, $trackingStatusCodes);
                    $transitTime = (!empty($data->getTransitTime()) ? $data->getTransitTime() : '');
                    if (in_array('121', $trackingStatusCodes)) {
                        $deliveredStatusCount = array_search(121, $trackingStatusCodes);
                        $receivedParcel = 121;
                        $totalDelivered[] = 1;
                    } else if (in_array('122', $trackingStatusCodes)) {
                        $deliveredStatusCount = array_search(122, $trackingStatusCodes);
                        $receivedParcel = 122;
                        $totalDelivered[] = 1;
                    }
                    if(!in_array('146',$trackingStatusCodes)){
                        $parcelId = 0;
                        $parcelId = $data->getId();
                        $dataHubReceivedobj = TrackingDataFilter::getCSStatusCOde($parcelId);
                        if(!empty($dataHubReceivedobj))
                            $dataDeliveryHub  = (!empty($dataHubReceivedobj[0]->getDateCreateTrack()) ? $dataHubReceivedobj[0]->getDateCreateTrack():'0');
                        else
                            $dataDeliveryHub = 0;
                    }else{
                        $receivedHub = array_search(146, $trackingStatusCodes);
                        $dataDeliveryHub = $deleivery_ex_hub[$receivedHub];
                    }
                    if ($deliveredStatusCount !== FALSE && in_array($receivedParcel, $trackingStatusCodes)) {
                        $firstIndexCarrierReceived = $dataDeliveryHub;
                        $LastIndexDelivery = $deleivery_ex_hub[$deliveredStatusCount];

                        $date1 = date_create($firstIndexCarrierReceived);
                        $date2 = date_create($LastIndexDelivery);
                        $diff = date_diff($date2, $date1);
                        $daysOfDelivered = 0;
                        if (!empty($LastIndexDelivery) && !empty($firstIndexCarrierReceived)) {
                            $date1 = date_create($firstIndexCarrierReceived);
                            $date2 = date_create($LastIndexDelivery);
                            $diff = date_diff($date2, $date1);
                            $daysOfDelivered = $diff->d;
                        }
                        if ($daysOfDelivered >= $transitTime) {
                           $serviceGood[$data->getTrackPoint()][] = array(
                                'totalShipment' => count($data->getId()),
                                'tranitTime' => $transitTime,
                                'daysOfDelivery' => $daysOfDelivered,
                            );
                        }
                    }
                }
                if(!empty($serviceGood)){
                    foreach($serviceGood as $key => $dataServiceOfTime){
                        $countParcel = 0;
                        $days = 0;
                        $avgTime = 0;
                        foreach($dataServiceOfTime as $ind => $data){
                            $countParcel += count($data['totalShipment']);
                            $days += $data['daysOfDelivery'];
                        }
                        $avgTime = $days / $countParcel;
                        $topWorseGoodService[] = array(
                            'service' => $key,
                            'value' => number_format($avgTime,2),
                            'value_label' => number_format($avgTime,2).' averge days'
                        );

                    }
                }
            }
            echo json_encode($topWorseGoodService);
            exit;
        }
        
        if(!empty($_GET['serialchart']) && $_GET['serialchart'] == 'serial' && !empty($_GET['param'])){
            $topFiveGoodService =[];
            $trackindDataFilter = new TrackingDataFilter();
            if($_GET['param'] == 'today'){
                $trackingObj = $trackindDataFilter->getDashboardCSDelivered($allouedAcccounts, 0,0,date('Y-m-d'),'','INNER JOIN `services` s ON s.id = c.`service_id`',',s.`name` AS track_point',',s.id');
            }else if($_GET['param'] == 'week'){
                $trackingObj = $trackindDataFilter->getDashboardCSDelivered($allouedAcccounts, 0,0,date("Y-m-d",  strtotime("-7 day")),date("Y-m-d"),'INNER JOIN `services` s ON s.id = c.`service_id`',',s.`name` AS track_point',',s.id');
            }else if($_GET['param'] == 'month'){
                $trackingObj = $trackindDataFilter->getDashboardCSDelivered($allouedAcccounts, 0,0,date('Y-m-d',  strtotime("-30 day")),date('Y-m-d'),'INNER JOIN `services` s ON s.id = c.`service_id`',',s.`name` AS track_point',',s.id');
            }
            if(!empty($trackingObj)){
                $serviceGood = [];
                $totalDelivered = [];
                $deliveryOnTime = [];
                $deliveryOutTime = [];
                foreach ($trackingObj as $key => $data) {
                    $trackingStatusCodes = explode(',', $data->getGroupStatusCode());
                    $deleivery_ex_hub = explode(',', $data->getGroupDateCreated());
                    $receivedHub = array_search(146, $trackingStatusCodes);
                    $transitTime = (!empty($data->getTransitTime()) ? $data->getTransitTime() : '');
                    if (in_array('121', $trackingStatusCodes)) {
                        $deliveredStatusCount = array_search(121, $trackingStatusCodes);
                        $receivedParcel = 121;
                        $totalDelivered[] = 1;
                    } else if (in_array('122', $trackingStatusCodes)) {
                        $deliveredStatusCount = array_search(122, $trackingStatusCodes);
                        $receivedParcel = 122;
                        $totalDelivered[] = 1;
                    }
                    if(!in_array('146',$trackingStatusCodes)){
                        $parcelId = 0;
                        $parcelId = $data->getId();
                        $dataHubReceivedobj = TrackingDataFilter::getCSStatusCOde($parcelId);
                        if(!empty($dataHubReceivedobj))
                            $dataDeliveryHub  = (!empty($dataHubReceivedobj[0]->getDateCreateTrack())?$dataHubReceivedobj[0]->getDateCreateTrack():'0');
                        else
                            $dataDeliveryHub = 0;

                    }else{
                        $receivedHub = array_search(146, $trackingStatusCodes);
                        $dataDeliveryHub = $deleivery_ex_hub[$receivedHub];
                    }
                    if ($deliveredStatusCount !== FALSE && in_array($receivedParcel, $trackingStatusCodes)) {
                        $firstIndexCarrierReceived = $dataDeliveryHub;
                        $LastIndexDelivery = $deleivery_ex_hub[$deliveredStatusCount];

                        $date1 = date_create($firstIndexCarrierReceived);
                        $date2 = date_create($LastIndexDelivery);
                        $diff = date_diff($date2, $date1);
                        $daysOfDelivered = 0;
                        if (!empty($LastIndexDelivery) && !empty($firstIndexCarrierReceived)) {
                            $date1 = date_create($firstIndexCarrierReceived);
                            $date2 = date_create($LastIndexDelivery);
                            $diff = date_diff($date2, $date1);
                            $daysOfDelivered = $diff->d;
                        }
                        if (!empty($daysOfDelivered) && $daysOfDelivered <= $transitTime) {
                            $serviceGood[$data->getTrackPoint()][] = array(
                                'totalShipment' => count($data->getId()),
                                'tranitTime' => $transitTime,
                                'daysOfDelivery' => $daysOfDelivered,
                            );
                        } else if ($daysOfDelivered === $transitTime) {
                            $serviceGood[$data->getTrackPoint()][] = array(
                                'totalShipment' => count($data->getId()),
                                'tranitTime' => $transitTime,
                                'daysOfDelivery' => $daysOfDelivered,
                            );
                        }
                    }
                }
                if(!empty($serviceGood)){
                    foreach($serviceGood as $key => $dataServiceOfTime){
                        $countParcel = 0;
                        $days = 0;
                        $avgTime = 0;
                        foreach($dataServiceOfTime as $ind => $data){
                            $countParcel += count($data['totalShipment']);
                            $days += $data['daysOfDelivery'];
                        }
                        $avgTime = $days / $countParcel;
                        $topFiveGoodService[] = array(
                            'service' => $key,
                            'value' => number_format($avgTime,2),
                            'value_label' => number_format($avgTime,2).' averge days'
                        );

                    }
                }
            }
            echo json_encode($topFiveGoodService);
            exit;
        }
        
        if(!empty($_GET['piechart']) && $_GET['piechart'] == 'pie' && !empty($_GET['param'])){
            $trackindDataFilter = new TrackingDataFilter();
            if($_GET['param'] == 'today'){
                $trackingObj = $trackindDataFilter->getDashboardCSDelivered($allouedAcccounts,0,0,date('Y-m-d'),'');
                if(!empty($trackingObj)){
                    $totalDelivered = [];
                    $deliveryOnTime = [];
                    $deliveryOutTime = [];
                    foreach ($trackingObj as $key => $data) {
                        $trackingStatusCodes = explode(',', $data->getGroupStatusCode());
                        $deleivery_ex_hub = explode(',', $data->getGroupDateCreated());
                        $receivedHub = array_search(146, $trackingStatusCodes);
                        $transitTime = (!empty($data->getTransitTime()) ? $data->getTransitTime() : '');
                        if (in_array('121', $trackingStatusCodes)) {
                            $deliveredStatusCount = array_search(121, $trackingStatusCodes);
                            $receivedParcel = 121;
                            $totalDelivered[] = 1;
                        } else if (in_array('122', $trackingStatusCodes)) {
                            $deliveredStatusCount = array_search(122, $trackingStatusCodes);
                            $receivedParcel = 122;
                            $totalDelivered[] = 1;
                        }
                        if(!in_array('146',$trackingStatusCodes)){
                            $parcelId = 0;
                            $parcelId = $data->getId();
                            $dataHubReceivedobj = TrackingDataFilter::getCSStatusCOde($parcelId);
                            if(!empty($dataHubReceivedobj))
                                $dataDeliveryHub  = (!empty($dataHubReceivedobj[0]->getDateCreateTrack()) ? $dataHubReceivedobj[0]->getDateCreateTrack():'0');
                            else
                                $dataDeliveryHub = 0;
                        }else{
                            $receivedHub = array_search(146, $trackingStatusCodes);
                            $dataDeliveryHub = $deleivery_ex_hub[$receivedHub];
                        }
                        if ($deliveredStatusCount !== FALSE && in_array($receivedParcel, $trackingStatusCodes)) {
                            $firstIndexCarrierReceived = $dataDeliveryHub;
                            $LastIndexDelivery = $deleivery_ex_hub[$deliveredStatusCount];

                            $date1 = date_create($firstIndexCarrierReceived);
                            $date2 = date_create($LastIndexDelivery);
                            $diff = date_diff($date2, $date1);
                            $daysOfDelivered = 0;
                            if (!empty($LastIndexDelivery) && !empty($firstIndexCarrierReceived)) {
                                $date1 = date_create($firstIndexCarrierReceived);
                                $date2 = date_create($LastIndexDelivery);
                                $diff = date_diff($date2, $date1);
                                $daysOfDelivered = $diff->d;
                            }

                            if (!empty($daysOfDelivered) && $daysOfDelivered <= $transitTime) {
                                $deliveryOnTime[] = 1;
                            } else if ($daysOfDelivered === $transitTime) {
                                $deliveryOnTime[] = 1;
                            } else if ($daysOfDelivered >= $transitTime) {
                                $deliveryOutTime[] = 1;
                            }
                        }
                    }
                }
                $totalNoOfDelivered = (!empty($totalDelivered)?array_sum($totalDelivered):'0');
                $totalNoOfOnTime = (!empty($deliveryOnTime)?array_sum($deliveryOnTime):'0');
                $totalNoOfOutTime = (!empty($deliveryOutTime)?array_sum($deliveryOutTime):'0');
                $this->pieChartDeliveredShipmentToday = [];
                //$this->pieChartDeliveredShipmentToday[0] = array('caption' => 'Total Delivered', 'value' => $totalNoOfDelivered);
                $this->pieChartDeliveredShipmentToday[0] = array('caption' => 'Delivered Ontime', 'value' => $totalNoOfOnTime);
                $this->pieChartDeliveredShipmentToday[1] = array('caption' => 'Delivered Outtime', 'value' => $totalNoOfOutTime);                
                echo json_encode($this->pieChartDeliveredShipmentToday);
            }else if($_GET['param'] == 'week'){
                    $trackingWeekObj = $trackindDataFilter->getDashboardCSDelivered($allouedAcccounts, 0,0,date("Y-m-d",  strtotime("-7 day")),date("Y-m-d"));
                    if(!empty($trackingWeekObj)){
                        $totalDelivered = [];
                        $deliveryOnTime = [];
                        $deliveryOutTime = [];
                        foreach ($trackingWeekObj as $key => $data) {
                            $trackingStatusCodes = explode(',', $data->getGroupStatusCode());
                            $deleivery_ex_hub = explode(',', $data->getGroupDateCreated());
                            $transitTime = (!empty($data->getTransitTime()) ? $data->getTransitTime() : '');
                            if(!in_array('146',$trackingStatusCodes)){
                                $parcelId = 0;
                                $parcelId = $data->getId();
                                $dataHubReceivedobj = TrackingDataFilter::getCSStatusCOde($parcelId);
                                if(!empty($dataHubReceivedobj))
                                    $dataDeliveryHub  = (!empty($dataHubReceivedobj[0]->getDateCreateTrack())?$dataHubReceivedobj[0]->getDateCreateTrack():'0');
                                else
                                    $dataDeliveryHub = 0;
                            }else{
                                $receivedHub = array_search(146, $trackingStatusCodes);
                                $dataDeliveryHub = $deleivery_ex_hub[$receivedHub];
                                
                           }
                            if (in_array('121', $trackingStatusCodes)) {
                                $deliveredStatusCount = array_search(121, $trackingStatusCodes);
                                $receivedParcel = 121;
                                $totalDelivered[] = 1;
                            } else if (in_array('122', $trackingStatusCodes)) {
                                $deliveredStatusCount = array_search(122, $trackingStatusCodes);
                                $receivedParcel = 122;
                                $totalDelivered[] = 1;
                            }
                            if ($deliveredStatusCount !== FALSE && in_array($receivedParcel, $trackingStatusCodes)) {
                                $firstIndexCarrierReceived = $dataDeliveryHub;
                                $LastIndexDelivery = $deleivery_ex_hub[$deliveredStatusCount];
                                $daysOfDelivered = 0;
                                if (!empty($LastIndexDelivery) && !empty($firstIndexCarrierReceived)) {
                                    $date1 = date_create($firstIndexCarrierReceived);
                                    $date2 = date_create($LastIndexDelivery);
                                    $diff = date_diff($date2, $date1);
                                    $daysOfDelivered = $diff->d;
                                }
                                if (!empty($daysOfDelivered) && $daysOfDelivered <= $transitTime) {
                                    $deliveryOnTime[] = 1;
                                } else if ($daysOfDelivered === $transitTime) {
                                    $deliveryOnTime[] = 1;
                                } else if ($daysOfDelivered >= $transitTime) {
                                    $deliveryOutTime[] = 1;
                                }
                            }
                        }
                    }
                      //die();
                    $totalNoOfDelivered = (!empty($totalDelivered)?array_sum($totalDelivered):'0');
                    $totalNoOfOnTime = (!empty($deliveryOnTime)?array_sum($deliveryOnTime):'0');
                    $totalNoOfOutTime = (!empty($deliveryOutTime)?array_sum($deliveryOutTime):'0');
                    $this->pieChartDeliveredShipmentToday = [];
                    //$this->pieChartDeliveredShipmentToday[0] = array('caption' => 'Total Delivered', 'value' => $totalNoOfDelivered);
                    $this->pieChartDeliveredShipmentToday[0] = array('caption' => 'Delivered Ontime', 'value' => $totalNoOfOnTime);
                    $this->pieChartDeliveredShipmentToday[1] = array('caption' => 'Delivered Outtime', 'value' => $totalNoOfOutTime);                  
                    echo json_encode($this->pieChartDeliveredShipmentToday);
            }else if($_GET['param'] == 'month'){
                $trackingMonthObj = $trackindDataFilter->getDashboardCSDelivered($allouedAcccounts,0,0,date('Y-m-d',  strtotime("-30 day")),date('Y-m-d'));
                if(!empty($trackingMonthObj)){
                    $totalDelivered = [];
                    $deliveryOnTime = [];
                    $deliveryOutTime = [];
                    foreach ($trackingMonthObj as $key => $data) {
                        $trackingStatusCodes = explode(',', $data->getGroupStatusCode());
                        $deleivery_ex_hub = explode(',', $data->getGroupDateCreated());
                        $receivedHub = array_search(146, $trackingStatusCodes);
                        $transitTime = (!empty($data->getTransitTime()) ? $data->getTransitTime() : '');
                        if (in_array('121', $trackingStatusCodes)) {
                            $deliveredStatusCount = array_search(121, $trackingStatusCodes);
                            $receivedParcel = 121;
                            $totalDelivered[] = 1;
                        } else if (in_array('122', $trackingStatusCodes)) {
                            $deliveredStatusCount = array_search(122, $trackingStatusCodes);
                            $receivedParcel = 122;
                            $totalDelivered[] = 1;
                        }
                        if(!in_array('146',$trackingStatusCodes)){
                                $parcelId = 0;
                                $parcelId = $data->getId();
                                $dataHubReceivedobj = TrackingDataFilter::getCSStatusCOde($parcelId);
                                if(!empty($dataHubReceivedobj))
                                    $dataDeliveryHub  = (!empty($dataHubReceivedobj[0]->getDateCreateTrack())?$dataHubReceivedobj[0]->getDateCreateTrack():'0');
                                else
                                    $dataDeliveryHub = 0;
                        }else{
                                $receivedHub = array_search(146, $trackingStatusCodes);
                                $dataDeliveryHub = $deleivery_ex_hub[$receivedHub];
                                
                        }
                        if ($deliveredStatusCount !== FALSE && in_array($receivedParcel, $trackingStatusCodes)) {
                            $firstIndexCarrierReceived = $dataDeliveryHub;
                            $LastIndexDelivery = $deleivery_ex_hub[$deliveredStatusCount];

                            $date1 = date_create($firstIndexCarrierReceived);
                            $date2 = date_create($LastIndexDelivery);
                            $diff = date_diff($date2, $date1);
                            $daysOfDelivered = 0;
                            if (!empty($LastIndexDelivery) && !empty($firstIndexCarrierReceived)) {
                                $date1 = date_create($firstIndexCarrierReceived);
                                $date2 = date_create($LastIndexDelivery);
                                $diff = date_diff($date2, $date1);
                                $daysOfDelivered = $diff->d;
                            }

                            if (!empty($daysOfDelivered) && $daysOfDelivered <= $transitTime) {
                                $deliveryOnTime[] = 1;
                            } else if ($daysOfDelivered === $transitTime) {
                                $deliveryOnTime[] = 1;
                            } else if ($daysOfDelivered >= $transitTime) {
                                $deliveryOutTime[] = 1;
                            }
                        }
                    }
                }
                $totalNoOfDelivered = (!empty($totalDelivered)?array_sum($totalDelivered):'0');
                $totalNoOfOnTime = (!empty($deliveryOnTime)?array_sum($deliveryOnTime):'0');
                $totalNoOfOutTime = (!empty($deliveryOutTime)?array_sum($deliveryOutTime):'0');
                $this->pieChartDeliveredShipmentToday = [];
                //$this->pieChartDeliveredShipmentToday[0] = array('caption' => 'Total Delivered', 'value' => $totalNoOfDelivered);
                $this->pieChartDeliveredShipmentToday[0] = array('caption' => 'Delivered Ontime', 'value' => $totalNoOfOnTime);
                $this->pieChartDeliveredShipmentToday[1] = array('caption' => 'Delivered Outtime', 'value' => $totalNoOfOutTime);                  
                echo json_encode($this->pieChartDeliveredShipmentToday);
            }
            exit;
        }
    }
    /**
     * Page-specific buttons
     */
    protected function renderFooter() {
       
    }

    protected function addPagelavelCss() {
     
    }

    public function addPagelavelJs() {
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
    }

    /**
     * Override to show the menu
     *
     */
    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::CUSTOMERS);
        $menu->render();
    }

    public function renderHead() {
        
    }

}

// class

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
?>