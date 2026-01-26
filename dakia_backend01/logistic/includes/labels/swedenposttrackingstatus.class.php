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
class SwedenpostTrackingStatus {

    public function __construct() {

    }

    public static $swedenpost_status_code = array(
        '35' => "Dispatched",
        '21' => "DELIVERED",
        '126' => "Shipment Stopped",
        '210' => "DELIVERY_IMPOSSIBLE",
        'z95'=> "In Transit",
        'z114'=> "In Transit",
        'z76' => 'DELIVERED',
        'z30' => 'In Transit',
        'z96' => 'DELIVERED',
        '270' => 'The delivery of the shipment item has been delayed with one workday',
        '109' => 'Delivery was not possible. The recipient will get a notification with information on where the shipment item will be available for collection.',
        'z94' => 'The transport of the shipment item has started in the country of the sender',
        '1'   => 'The shipment item has been delivered to a service point',
        '31'  => 'The shipment item is under transportation',
        'z07' => 'A notification has been sent to the recipient',
        '74'  => 'The transport of the shipment item has started',
        'z28' => 'Item has been received by rural mail carrier for delivery the next work day',
        '6'   => 'Recipient has booked delivery via rural post',
        '44'  => 'The shipment item has been incorrectly sorted',
        "Return to Deutsche Post Return Center" => "Return to Deutsche Post Return Center",
        "Possible end of tracking due to limited tracking availability in destination country." =>  "Possible end of tracking due to limited tracking availability in destination country.",
        "Attempted Delivery" =>	"Attempted Delivery",
        "Arrived at local delivering Terminal" => "Arrived at local delivering Terminal",
        "Departed Destination Terminal" => "Departed Destination Terminal",
        "Departed Receivers Terminal at destination" =>	"Departed Receivers Terminal at destination",
        "Held at Customs" => "Held at Customs",
        "Arrival at destination country" => "Arrival at destination country",
        "Departed Deutsche Post Mail Terminal" => "Departed Deutsche Post Mail Terminal",
        "Received & Processed at Deutsche Post Mail Terminal" => "Received & Processed at Deutsche Post Mail Terminal",
        "Shipment information uploaded to Deutsche Post" => "Shipment information uploaded to Deutsche Post"
        
    );

    public static $consignment_status_code = array(
        '35' => Consignment::STATUS_DISPATCHED,
        '21' => Consignment::STATUS_DELIVERED,
        '126' => Consignment::STATUS_INTRANSIT,
        '210' => Consignment::STATUS_PROBLEM,
        'z95' => Consignment::STATUS_INTRANSIT,
        'z114' => Consignment::STATUS_INTRANSIT,
        'z76' => Consignment::STATUS_DELIVERED,
        'z30' => Consignment::STATUS_INTRANSIT,
        'z96' => Consignment::STATUS_DELIVERED,
        '270' => Consignment::STATUS_INTRANSIT,
        '109' => Consignment::STATUS_INTRANSIT,
        'z94' => Consignment::STATUS_INTRANSIT,
        '1'   => Consignment::STATUS_INTRANSIT,
        '31'  => Consignment::STATUS_INTRANSIT,
        'z07' => Consignment::STATUS_INTRANSIT,
        '74'  => Consignment::STATUS_INTRANSIT,
        'z28' => Consignment::STATUS_INTRANSIT,
        '6'   => Consignment::STATUS_INTRANSIT,
        '44'  => Consignment::STATUS_PROBLEM,
        "Return to Deutsche Post Return Center" => Consignment::STATUS_INTRANSIT,
        "Possible end of tracking due to limited tracking availability in destination country." =>  Consignment::STATUS_INTRANSIT,
        "Attempted Delivery" =>	Consignment::STATUS_INTRANSIT,
        "Arrived at local delivering Terminal" => Consignment::STATUS_INTRANSIT,
        "Departed Destination Terminal" => Consignment::STATUS_INTRANSIT,
        "Departed Receivers Terminal at destination" =>	Consignment::STATUS_INTRANSIT,
        "Held at Customs" => Consignment::STATUS_HOLD,
        "Arrival at destination country" => Consignment::STATUS_INTRANSIT,
        "Departed Deutsche Post Mail Terminal" => Consignment::STATUS_INTRANSIT,
        "Received & Processed at Deutsche Post Mail Terminal" => Consignment::STATUS_INTRANSIT,
        "Shipment information uploaded to Deutsche Post" => Consignment::STATUS_INTRANSIT
    );

    public static $oneworld_swedenpost = array(
        '35' => 148,
        '21' => 121,
        '126' => 117,
        '210' => 123,
        'z95' => 137,
        'z114' => 137,
        'z76' => 121,
        'z30' => 137,
        'z96' => 121,
        '270' => 126,
        '109' => 137,
        'z94' => 137,
        '1'   => 137,
        '31'  => 137,
        'z07' => 137,
        '74'  => 137,
        'z28' => 137,
        '6'   => 137,
        '44'  => 115,
        "Return to Deutsche Post Return Center" => 138,
        "Possible end of tracking due to limited tracking availability in destination country." =>  137,
        "Attempted Delivery" =>	123,
        "Arrived at local delivering Terminal" => 137,
        "Departed Destination Terminal" => 137,
        "Departed Receivers Terminal at destination" =>	137,
        "Held at Customs" => 128,
        "Arrival at destination country" => 146,
        "Departed Deutsche Post Mail Terminal" => 137,
        "Received & Processed at Deutsche Post Mail Terminal" => 137,
        "Shipment information uploaded to Deutsche Post" => 137
    );

    public static function getOweStatusCode($swedenpostStatus){
        $swedenpostTrackingStatus = self::$oneworld_swedenpost;
        return isset($swedenpostTrackingStatus[$swedenpostStatus]) ? $swedenpostTrackingStatus[$swedenpostStatus] : 0;
    }
    public static function getConsignmentStatus($swedenpostStatus){
        $consignmentStatusCodes = Tracking::$oneworld_consignment_code_mapping;
        return isset($consignmentStatusCodes[$swedenpostStatus]) ? $consignmentStatusCodes[$swedenpostStatus] : 0;
    }
}
