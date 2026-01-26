<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ParcelforYouTrackingStatus
 *
 * @author kiran.iftikhar
 */

class ParcelforYouTrackingStatus {

    public function __construct() {        
    }
    
    public static $parcelforyou_status_code = array(
        '1' => "We have received the packet data. Freshly created packet.",
        '2' => "Packet has been accepted at our branch.",
        '3' => "Packet is waiting to be dispatched from branch branchId to branch destinationBranchId.",
        '4' => "Packet is on the way from branch branchId to branch destinationBranchId.",
        '5' => "Packet has been delivered to its destination (branchId), the customer has been informed via SMS.",
        '6' => "Packet has been handed over to an external carrier for delivery. It can be traced via carrier's tracking application under externalTrackingCode.",
        '7' => "Packet was picked up by the customer at the branch branchId.",
        '9' => "Packet is on the way back to the sender.",
       '10' => "Packet has been returned to the sender.",
       '11' => "Packet has been cancelled.",
      '999' => "Unknown packet status."
    );
    
    public static $consignment_status_code = array(
        '1' => Consignment::STATUS_RECEIVED,
        '2' => Consignment::STATUS_INTRANSIT,
        '3' => Consignment::STATUS_INTRANSIT,
        '4' => Consignment::STATUS_INTRANSIT,
        '5' => Consignment::STATUS_DELIVERED,
        '6' => Consignment::STATUS_INTRANSIT,
        '7' => Consignment::STATUS_INTRANSIT,
        '9' => Consignment::STATUS_INTRANSIT,
       '10' => Consignment::STATUS_RETURNED,
       '11' => Consignment::STATUS_CANCELLED,
      '999' => Consignment::STATUS_INVALID
    );
    public static $oneworld_parcelforyou = array(
        '1' => 144,
        '2' => 137,
        '3' => 113,
        '4' => 137,
        '5' => 121,
        '6' => 156,
        '7' => 133,
        '9' => 137,
       '10' => 138,
       '11' => 152,
      '999' => 115
    );

    public static function getOweStatusCode($parcelforyouStatus) {
        $parcelforyouTrackingStatus = self::$oneworld_parcelforyou;
        return isset($parcelforyouTrackingStatus[$parcelforyouStatus]) ? $parcelforyouTrackingStatus[$parcelforyouStatus] : 0;
    }

    public static function getConsignmentStatus($parcelforyouStatus) {
        $consignmentStatusCodes = Tracking::$oneworld_consignment_code_mapping;
        return isset($consignmentStatusCodes[$parcelforyouStatus]) ? $consignmentStatusCodes[$parcelforyouStatus] : 0;
    }

}
