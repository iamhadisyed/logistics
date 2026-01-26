<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of YodelTrackingStatus
 *
 * @author kiran.iftikhar
 */
include_classes([
    'tracking.class',
    ]);

class AnpostTrackingStatus {
    
    public function __construct() {
        
    }
    
    public static $anpost_status_code = array(
        "ARRIVED" => "Item has arrived at inward office of destination",
        "ARRIVED TRANSIT HUB" => "Item has arrived at the transit hub for onward despatch",
        "ATTEMPTED DELIVERY" => "We have attempted delivery of the item but there was no one present to take delivery of the item",
        "ATTEMPTED DELIVERY" => "We have attempted delivery of the item but the addressee has refused it",
        "ATTEMPTED DELIVERY" => "We have attempted delivery of the item but the addressee is no longer at the address specified",
        "ATTEMPTED DELIVERY" => "We have attempted delivery of the item but the addressee is not known at the address specified",
        "ATTEMPTED DELIVERY" => "We have attempted delivery of the item but have failed as the address specified is incomplete or insufficient to do so sucessfully",
        "ATTEMPTED DELIVERY" => "We have attempted delivery of the item but have failed for unspecified reasons",
        "COLLECTED" => "The item has been collected (usually triggered by a driver scanning the item onto the van/truck)",
        "DELIVERED" => "Item has been sucessfully delivered",
        "DEPART TRANSIT HUB" => "Item has departed from the transit hub",
        "EXPORTED" => "Item has been exported (overseas)",
        "HELD BY CUSTOMS" => "Item is being held by customs",
        "INTO CUSTOMS" => "Item has been scanned into customs",
        "ITEM ACCEPTED" => "Item has been accepted into our network",
        "ITEM DELIVERED TO SENDER" => "Item has been sucessfully returned to the sender",
        "ITEM HELD" => "Item is being held (usually awaiting collection by the addressee)",
        "ITEM IN TRANSIT TO SENDER" => "Item is on in transit on return to the sender",
        "ITEM ON HAND" => "Item is on hand in a depot - usually an indication that it has missed it's onward despatch",
        "OUT FOR DELIVERY" => "Item is out (in a van) for delivery shortly",
        "OUT OF CUSTOMS" => "Item has been scanned out of customs",
        "POD IMAGED" => "The image of the receiver's signature has been sucessfully uploaded onto our imaging software",
        "POD PROCESSED" => "This is a manual delivery by POD (Proof Of Delivery) card (not scanner).  Receiver's signature has been obtained on a card and the barcode is later scanned as Delivered back at the office.  This is rare and tends only to occur during periods of very high volume (Christmas) when there's a shorttage of scanners relative to the numbers of operatives delivering items",
        "PRE-ADVICE" => "Item has been received into the An Post netword.",
        "RETURN ATTEMPT TO SENDER" => "Attempt to return an item to the sender has been made",
        "RETURNED FROM ABROAD" => "Item has been put in course for return to sender from abroad",
        "SORTED" => "Item has been processed/sorted for onward despatch"       
    );
 
    public static $consignment_status_code = array(
        "ARRIVED" => Consignment::STATUS_INTRANSIT,
        "ARRIVED TRANSIT HUB" => Consignment::STATUS_INTRANSIT,
        "ATTEMPTED DELIVERY" => Consignment::STATUS_DELIVERED,
        "COLLECTED" => Consignment::STATUS_INTRANSIT,
        "DELIVERED" => Consignment::STATUS_DELIVERED,
        "DEPART TRANSIT HUB" => Consignment::STATUS_INTRANSIT,
        "EXPORTED" => Consignment::STATUS_INTRANSIT,
        "HELD BY CUSTOMS" => Consignment::STATUS_HOLD,
        "INTO CUSTOMS" => Consignment::STATUS_INTRANSIT,
        "ITEM ACCEPTED" => Consignment::STATUS_INTRANSIT,
        "ITEM DELIVERED TO SENDER" => Consignment::STATUS_RETURNED,
        "ITEM HELD" => Consignment::STATUS_HOLD,
        "ITEM IN TRANSIT TO SENDER" => Consignment::STATUS_RETURNED,
        "ITEM ON HAND" => Consignment::STATUS_INTRANSIT,
        "OUT FOR DELIVERY" => Consignment::STATUS_INTRANSIT,
        "OUT OF CUSTOMS" => Consignment::STATUS_INTRANSIT,
        "POD IMAGED" => Consignment::STATUS_DELIVERED,
        "POD PROCESSED" => Consignment::STATUS_DELIVERED, 
        "PRE-ADVICE" => Consignment::STATUS_INTRANSIT,
        "RETURN ATTEMPT TO SENDER" => Consignment::STATUS_RETURNED,
        "RETURNED FROM ABROAD" => Consignment::STATUS_RETURNED,
        "SORTED" => Consignment::STATUS_INTRANSIT,
    );

    public static $oneworld_anpost = array(
        "ITEM ON HAND" => 137,
        "ARRIVED" => "Item has arrived at inward office of destination",
        "ARRIVED TRANSIT HUB" => "Item has arrived at the transit hub for onward despatch",
        "ATTEMPTED DELIVERY" => "We have attempted delivery of the item but there was no one present to take delivery of the item",
        "ATTEMPTED DELIVERY" => "We have attempted delivery of the item but the addressee has refused it",
        "ATTEMPTED DELIVERY" => "We have attempted delivery of the item but the addressee is no longer at the address specified",
        "ATTEMPTED DELIVERY" => "We have attempted delivery of the item but the addressee is not known at the address specified",
        "ATTEMPTED DELIVERY" => "We have attempted delivery of the item but have failed as the address specified is incomplete or insufficient to do so sucessfully",
        "ATTEMPTED DELIVERY" => "We have attempted delivery of the item but have failed for unspecified reasons",
        "COLLECTED" => "The item has been collected (usually triggered by a driver scanning the item onto the van/truck)",
        "DELIVERED" => "Item has been sucessfully delivered",
        "DEPART TRANSIT HUB" => "Item has departed from the transit hub",
        "EXPORTED" => "Item has been exported (overseas)",
        "HELD BY CUSTOMS" => "Item is being held by customs",
        "INTO CUSTOMS" => "Item has been scanned into customs",
        "ITEM ACCEPTED" => "Item has been accepted into our network",
        "ITEM DELIVERED TO SENDER" => "Item has been sucessfully returned to the sender",
        "ITEM HELD" => "Item is being held (usually awaiting collection by the addressee)",
        "ITEM IN TRANSIT TO SENDER" => "Item is on in transit on return to the sender",
        "ITEM ON HAND" => "Item is on hand in a depot - usually an indication that it has missed it's onward despatch",
        "OUT FOR DELIVERY" => "Item is out (in a van) for delivery shortly",
        "OUT OF CUSTOMS" => "Item has been scanned out of customs",
        "POD IMAGED" => "The image of the receiver's signature has been sucessfully uploaded onto our imaging software",
        "POD PROCESSED" => "This is a manual delivery by POD (Proof Of Delivery) card (not scanner).  Receiver's signature has been obtained on a card and the barcode is later scanned as Delivered back at the office.  This is rare and tends only to occur during periods of very high volume (Christmas) when there's a shorttage of scanners relative to the numbers of operatives delivering items",
        "PRE-ADVICE" => "Item has been received into the An Post netword.",
        "RETURN ATTEMPT TO SENDER" => "Attempt to return an item to the sender has been made",
        "RETURNED FROM ABROAD" => "Item has been put in course for return to sender from abroad",
        "SORTED" => "Item has been processed/sorted for onward despatch" 
    );
    public static function getOweStatusCode($anpostStatus){
        $anpostTrackingStatus = self::$oneworld_anpost;
        return isset($anpostTrackingStatus[$anpostStatus]) ? $anpostTrackingStatus[$anpostStatus] : 0;
    }
    public static function getConsignmentStatus($anpostStatus){
        $consignmentStatusCodes = Tracking::$oneworld_consignment_code_mapping;
        return isset($consignmentStatusCodes[$anpostStatus]) ? $consignmentStatusCodes[$anpostStatus] : 0;
    }
}
