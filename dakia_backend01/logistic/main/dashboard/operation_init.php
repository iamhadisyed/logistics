<?php

$this->user = SessionManager::getUser();
$week = "";
$today = "";
$month = "";
$date = "";
$tempWeekDate = "";
$tempMonthDate = "";

$date = date('Y-m-d');
$today = $date;
$date = strtotime($date);
$tempDate = strtotime("-7 day", $date);
$tempMonthDate = strtotime("-30 day", $date);
$week = date('Y-m-d', $tempDate);
$month = date('Y-m-d', $tempMonthDate);

$todayData = "";
$weekData = "";
$monthData = "";

$todayUserData = "";
$weekUserData = "";
$monthUserData = "";
//Get data for SERVICE SCANNED REPORT
$trackingDataFilter = new TrackingDataFilter();
$todayData = $trackingDataFilter->getReportScannedGroupByService($this->user->getWarehouseId(),$this->user->getUserAccountId(), $today, "=");
$weekData = $trackingDataFilter->getReportScannedGroupByService($this->user->getWarehouseId(),$this->user->getUserAccountId(), $week, ">=");
$monthData = $trackingDataFilter->getReportScannedGroupByService($this->user->getWarehouseId(),$this->user->getUserAccountId(), $month, ">=");
$this->totalServicesScanned = count($trackingDataFilter->getReportScannedGroupByService($this->user->getWarehouseId(),$this->user->getUserAccountId()));

foreach ($monthData as $monthArr) {
    $this->month .= '{
                                service: "' . $monthArr->getCarrierDesc() . '",
                                scanTotal: ' . $monthArr->getEntityId() . '
                            },';
}
$this->month = rtrim($this->month, ',');

foreach ($weekData as $weekArr) {
    $this->week .= '{
                                service: "' . $weekArr->getCarrierDesc() . '",
                                scanTotal: ' . $weekArr->getEntityId() . '
                            },';
}
$this->week = rtrim($this->week, ',');

foreach ($todayData as $todayArr) {
    $this->today .= '{
                                service: "' . $todayArr->getCarrierDesc() . '",
                                scanTotal: ' . $todayArr->getEntityId() . '
                            },';
}
$this->today = rtrim($this->today, ',');
//End Get data for SERVICE SCANNED REPORT
//

//Get logged in user account then get that user accounts users then get their scanned parcels and then list down each user's total scanned parcels list
//Get data for USER SCANNED REPORT
$userFilter = new UserFilter();
$todayUserData = $userFilter->getTotalScannedUserParcel($this->user->getUserAccountId(), $today, "=");

foreach ($todayUserData as $todayUserArr) {
    $this->userToday .= '{
                                user: "' . $todayUserArr->getUserName() . '",
                                scannedTotal: ' . $todayUserArr->getId() . '
                            },';
}
$this->userToday = rtrim($this->userToday, ',');

$weekUserData = $userFilter->getTotalScannedUserParcel($this->user->getUserAccountId(), $week, ">=");
foreach ($weekUserData as $weekUserArr) {
    $this->userWeek .= '{
                                user: "' . $weekUserArr->getUserName() . '",
                                scannedTotal: ' . $weekUserArr->getId() . '
                            },';
}
$this->userWeek = rtrim($this->userWeek, ',');

$monthUserData = $userFilter->getTotalScannedUserParcel($this->user->getUserAccountId(), $month, ">=");
foreach ($monthUserData as $monthUserArr) {
    $this->userMonth .= '{
                                user: "' . $monthUserArr->getUserName() . '",
                                scannedTotal: ' . $monthUserArr->getId() . '
                            },';
}
$this->userMonth = rtrim($this->userMonth, ',');
//End data for USER SCANNED REPORT
//$colorMArr = ['0'=>'"#b7e021"','1'=>'"#fbd51a"','2'=>'"#2498d2"','3'=>'"#EEE9E9"','4'=>'"#E9D2D2"','5'=>'"#AFFD71"','6'=>'"#FADEE0"','7'=>'"#B1C5FF"','8'=>'"#FFFFC3"','9'=>'"#FFE9CD"'];
//Get data for UPCOMING MANIFEST REPORT
$trackingDatObj = new TrackingData();
$scannedMenifestToday = $trackingDatObj->upcommingManifests($this->user->getUserAccountId(), $today, "=");
foreach ($scannedMenifestToday as $menifest) {
    $this->totalUpcommingMenifest++;
    $this->graphUpcommingMenifestToday .= '{
                                                "weight": "' . $menifest->getrMenifestWeight() . '",
                                                "count": ' . $menifest->getrMenifestId() . '
                                            },';
}
$this->graphUpcommingMenifestToday = rtrim($this->graphUpcommingMenifestToday, ',');

$scannedMenifestWeek = $trackingDatObj->upcommingManifests($this->user->getUserAccountId(), $week, ">=");
foreach ($scannedMenifestWeek as $menifest) {
    $this->totalUpcommingMenifest++;
    $this->graphUpcommingMenifestWeek .= '{
                                                "weight": "' . $menifest->getrMenifestWeight() . '",
                                                "count": ' . $menifest->getrMenifestId() . '
                                            },';
}
$this->graphUpcommingMenifestWeek = rtrim($this->graphUpcommingMenifestWeek, ',');

$scannedMenifestMonth = $trackingDatObj->upcommingManifests($this->user->getUserAccountId(), $month, ">=",true);
foreach ($scannedMenifestMonth as $menifest) {
    $this->totalUpcommingMenifest++;
    $this->graphUpcommingMenifestMonth .= '{
                                                "weight": "' . $menifest->getrMenifestWeight() . '",
                                                "count": ' . $menifest->getrMenifestId() . '
                                            },';
}
$this->graphUpcommingMenifestMonth = rtrim($this->graphUpcommingMenifestMonth, ',');
//End data for UPCOMING MANIFEST REPORT
//Get data for DISPATCHED MANIFESTS REPORT
$manifest = new Manifest();
$scannedMenifestToday = $manifest->getScannedManifestByDate($this->user->getUserAccountId(), $today, "=");
foreach ($scannedMenifestToday as $scannedMenifest) {
    $this->graphDispatchedMenifestToday .= '{
                                "lineColor":"#b7e021",
                                "date": "' . date('Y-m-d', $scannedMenifest->getDateCreated()) . '",
                                "duration": ' . $scannedMenifest->getId() . '
                            },';
}
$this->graphDispatchedMenifestToday = rtrim($this->graphDispatchedMenifestToday, ',');

$scannedMenifestWeek = $manifest->getScannedManifestByDate($this->user->getUserAccountId(), $week, ">=");
foreach ($scannedMenifestWeek as $scannedMenifestW) {
    $this->graphDispatchedMenifestWeek .= '{
                                "lineColor":"#b7e021",
                                "date": "' . date('Y-m-d', $scannedMenifestW->getDateCreated()) . '",
                                "duration": ' . $scannedMenifestW->getId() . '
                            },';
}
$this->graphDispatchedMenifestWeek = rtrim($this->graphDispatchedMenifestWeek, ',');

$scannedMenifestMonth = $manifest->getScannedManifestByDate($this->user->getUserAccountId(), $month, ">=");
foreach ($scannedMenifestMonth as $scannedMenifestM) {
    $this->graphDispatchedMenifestMonth .= '{
                                "lineColor":"#b7e021",
                                "date": "' . date('Y-m-d', $scannedMenifestM->getDateCreated()) . '",
                                "duration": ' . $scannedMenifestM->getId() . '
                            },';
}
$this->graphDispatchedMenifestMonth = rtrim($this->graphDispatchedMenifestMonth, ',');
//End data for DISPATCHED MANIFESTS REPORT
