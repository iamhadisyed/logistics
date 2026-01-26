<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TourlineTrackingStatus
 *
 * @author kiran.iftikhar
 */
class TourlineTrackingStatus {
    
    public function __construct() {
        
    }
    
    public static $tourline_status_code = array(
        "0"  => "PENDING",
        "1"  => "IN TRANSIT",
        "2"  => "CAST IN",
        "3"  => "DELIVERED",
        "4"  => "INCIDENCE",
        "5"  => "RETURN",
        "6"  => "COLLECT IN AGENCY",
        "7"  => "RECANALIZED",
        "8"  => "UNREALIZED",
        "9"  => "RETURNED",
        "10" => "IN CUSTOMS",
        "11" => "IN AGENCY",
        "12" => "PARTIAL DELIVERY",
        "13" => "POSITIONED IN PER",
        "14" => "MERCHANDISE WITH EXCESS OF MEASURES",
        "15" => "MERCHANDISE WITH EXCESS WEIGHT",
        "16" => "IT DOESN’T MATCH IMEI",
        "17" => "MCIA PENDING TO RECEIVE BY MARITIME",
        "18" => "UNSUITED SHIPPING",
        "19" => "MERCHANDISE NOT PERMITTED BY ROUTE",
        "20" => "TYPE OF SERVICE INCORRECT MANIFESTED",
        "21" => "RETAINED SHIPPING WITHOUT A COBRANÇA DOCUMENT",
        "22" => "LACK OF DOCUMENTATION FOR YOUR PROCESSING",
        "23" => "DELIVERY NOT PRE-DELIVERED TO THE DEPARTMENT. AIR SERVICES",
        "24" => "PRE-ALERTED SHIPPING THAT DOESN’T LINK WITH AERIAL ROUTE",
        "25" => "LACK OF RETURN OR RETURN WITHOUT PREPARING",
        "26" => "NO MEANS FOR DISCHARGE ARE AVAILABLE",
        "27" => "DNI NOT VALID",
        "28" => "NO COVERAGE",
        "29" => "THE HOLDER'S ID ISN’T AVAILABLE",
        "30" => "REQUESTED DATE / DELIVERY TIME IN GREAT SUFFERING",
        "50" => "RETURN FROM PLATFORM",
        "51" => "RETURN OF CASH CLAIMS ALREADY EVALUATED",
        "52" => "MODIFICATION OF DELIVERY",
        "53" => "DIRECTION MODIFICATION",
        "70" => "RECYCLING TO CLAIMS BY PLATFORM",
        "71" => "REINFORCED",
        "80" => "DIFFERENCE IN WEIGHT INTERNATIONAL SHIPPING",
        "90" => "CANCELED",
        "97" => "CANCELLATION MADE",
        "98" => "SERVICE REACTIVATION",
        "99" => "COMPOUND",
        "100" => "DELAY IN DELIVERY",
        "P9" => "DO NOT ACCEPT DEBID CARRIAGES",
        "R9" => "NO REIMBURSEMENTS ACCEPTED"
    );
 
    public static $consignment_status_code = array(
        "0" => Consignment::STATUS_INTRANSIT,
        "1" => Consignment::STATUS_INTRANSIT,
        "10" => Consignment::STATUS_INTRANSIT,
        "11" => Consignment::STATUS_INTRANSIT,
        "12" => Consignment::STATUS_INTRANSIT,
        "13" => Consignment::STATUS_INTRANSIT,
        "2" => Consignment::STATUS_INTRANSIT,
        "3" => Consignment::STATUS_DELIVERED,
        "4" => Consignment::STATUS_PROBLEM,
        "5" => Consignment::STATUS_RETURNED,
        "50" => Consignment::STATUS_RETURNED,
        "51" => Consignment::STATUS_INTRANSIT,
        "6" => Consignment::STATUS_INTRANSIT,
        "7" => Consignment::STATUS_INTRANSIT,
        "70" => Consignment::STATUS_RECYCLED,
        "71" => Consignment::STATUS_INTRANSIT,
        "8" => Consignment::STATUS_PROBLEM,
        "9" => Consignment::STATUS_RETURNED,
        "90" => Consignment::STATUS_CANCELLED,
        "99" => Consignment::STATUS_INTRANSIT,
        "1" => Consignment::STATUS_PROBLEM,
        "10" => Consignment::STATUS_INTRANSIT,
        "100" => Consignment::STATUS_INTRANSIT,
        "11" => Consignment::STATUS_PROBLEM,
        "12" => Consignment::STATUS_PROBLEM,
        "13" => Consignment::STATUS_PROBLEM,
        "14" => Consignment::STATUS_INTRANSIT,
        "15" => Consignment::STATUS_INTRANSIT,
        "16" => Consignment::STATUS_PROBLEM,
        "17" => Consignment::STATUS_INTRANSIT,
        "18" => Consignment::STATUS_PROBLEM,
        "19" => Consignment::STATUS_PROBLEM,
        "2" => Consignment::STATUS_INTRANSIT,
        "20" => Consignment::STATUS_PROBLEM,
        "21" => Consignment::STATUS_INTRANSIT,
        "22" => Consignment::STATUS_PROBLEM,
        "23" => Consignment::STATUS_INTRANSIT,
        "24" => Consignment::STATUS_INTRANSIT,
        "25" => Consignment::STATUS_INTRANSIT,
        "26" => Consignment::STATUS_PROBLEM,
        "27" => Consignment::STATUS_PROBLEM,
        "28" => Consignment::STATUS_PROBLEM,
        "29" => Consignment::STATUS_PROBLEM,
        "3"  => Consignment::STATUS_PROBLEM,
        "30" => Consignment::STATUS_PROBLEM,
        "4"  => Consignment::STATUS_PROBLEM,
        "5"  => Consignment::STATUS_INTRANSIT,
        "51" => Consignment::STATUS_INTRANSIT,
        "52" => Consignment::STATUS_INTRANSIT,
        "53" => Consignment::STATUS_INTRANSIT,
        "6"  => Consignment::STATUS_PROBLEM,
        "7"  => Consignment::STATUS_PROBLEM,
        "8"  => Consignment::STATUS_PROBLEM,
        "80" => Consignment::STATUS_PROBLEM,
        "9"  => Consignment::STATUS_PROBLEM,
        "97" => Consignment::STATUS_PROBLEM,
        "98" => Consignment::STATUS_INTRANSIT,
        "99" => Consignment::STATUS_PROBLEM,
        "P9" => Consignment::STATUS_INTRANSIT,
        "R9" => Consignment::STATUS_INTRANSIT
    );

    public static $oneworld_tourline = array(
        "0"  => 124,
        "1"  => 117,
        "2"  => 137,
        "3"  => 121,
        "4"  => 115,
        "5"  => 138,
        "6"  => 140,
        "7"  => 137,
        "8"  => 115,
        "9"  => 138,
        "10" => 117,
        "11" => 144,
        "12" => 147,
        "13" => 137,
        "14" => 141,
        "15" => 141,
        "16" => 115,
        "17" => 124,
        "18" => 115,
        "19" => 115,
        "20" => 115,
        "21" => 137,
        "22" => 115,
        "23" => 137,
        "24" => 137,
        "25" => 137,
        "26" => 115,
        "27" => 115,
        "28" => 115,
        "29" => 115,
        "30" => 115,
        "50" => 138,
        "51" => 137,
        "52" => 137,
        "53" => 137,
        "70" => 152,
        "71" => 137,
        "80" => 141,
        "90" => 152,
        "97" => 152,
        "98" => 137,
        "99" => 137,
        "100" => 120,
        "P9" => 137,
        "R9" => 137
    );
    public static function getOweStatusCode($tourlineStatus){
        $tourlineTrackingStatus = self::$oneworld_tourline;
        return isset($tourlineTrackingStatus[$tourlineStatus]) ? $tourlineTrackingStatus[$tourlineStatus] : 0;
    }
    public static function getConsignmentStatus($tourlineStatus){
        $consignmentStatusCodes = Tracking::$oneworld_consignment_code_mapping;
        return isset($consignmentStatusCodes[$tourlineStatus]) ? $consignmentStatusCodes[$tourlineStatus] : 0;
    }
    public static function getDescription($tourlineStatus){
        $description = self::$tourline_status_code;
        return isset($description[$tourlineStatus]) ? $description[$tourlineStatus] : 0;
    }
}
