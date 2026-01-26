<?php

/*
 * to change this license header, choose License headers in project properties.
 * to change this template file, choose tools | templates
 * and open the template in the editor.
 */

/**
 * description of deutschepost
 *
 * @author kiran.iftikhar
 */

/*
 * to change this license header, choose License headers in project properties.
 * to change this template file, choose tools | templates
 * and open the template in the editor.
 */

/**
 * description of Yodeltrackingstatus
 *
 * @author kiran.iftikhar
 */
class deutscheposttrackingstatus {
    
    public function __construct() {
        
    }

    public static $deutschepost_status_code = array(
        'data submitted' => 'Data Submitted',
        'processed sort facility' => 'Processed Sort Facility',
        'departed terminal origin' => 'Departed Terminal Origin',
        'arrived at destination country' => 'Arrived at Destination Country',
        'arrived at delivering terminal' => 'Arrived at Delivering Terminal',
        'return to origin terminal' => 'Return to Origin Terminal',
        'departed terminal at destination' => 'Departed Terminal at Destination',
        'delivered' => 'Delivered',
        'arrived at sort facility hayes - gbr' => 'Arrived at Sort Facility Hayes - GBR',
        'the transport of the shipment item has started in the country of the sender' => 'The transport of the Shipment item has Started in the Country of the Sender',
        'received & processed at deutsche post mail terminal' => 'Received & Processed at Deutsche Post Mail Terminal',
        'departed deutsche post mail terminal' => 'Departed Deutsche Post Mail Terminal',
        'z94' => 'The transport of the Shipment Item has Started in the country of the Sender',
        '31'  => 'The Shipment Item is Under Transportation',
        '1' => 'The Shipment Item has been Delivered to a Service Point',
        '109' => 'Delivery was not possible. The Recipient will get a notification with information on where the shipment item will be available for collection.',
        'z07' => 'A notification has been sent to the recipient',
        '74' => 'The transport of the shipment item has started',
        '355' => 'The shipment item is under transportation',
        '6' => 'Recipient has booked delivery via rural post',
        'z28' => 'Item has been received by rural mail carrier for delivery the next work day',
        '44' => 'The shipment item has been incorrectly sorted',
        '270' => 'The delivery of the shipment item has been delayed with one workday', 
        'shipment information uploaded to deutsche post' => 'Shipment Information uploaded to Deutsche Post',
        'arrival at destination country' => 'Arrival at Destination Country',
        'arrived at local delivering terminal' => 'Arrived at Local Delivering Terminal',
        'attempted delivery' => 'Attempted Delivery',
        'held at customs' => 'Held at Customs',
        'return to deutsche post return center' => 'Return to Deutsche Post Return Center',
        'departed receivers terminal at destination' => 'Departed Receivers Terminal at Destination',
        'departed destination terminal' => 'Departed Destination Terminal',
        'held at customs' => 'Held at Customs',
        'departed from delivering terminal at destination' => 'Departed from Delivering Terminal at Destination',
        'departed terminal' => 'Departed Terminal',
        'departure from deutsche post mailterminal' => 'Departure from Deutsche Post Mailterminal',
        'item received and processed at deutsche post mailterminal' => 'Item Received and Processed at Deutsche Post Mailterminal',
        'arrival at inward office of exchange'  => 'Arrival at Inward Office of Exchange',
        'departure from inward office of exchange' => 'Departure from Inward Office of Exchange',
        'item out for physical delivery' => 'Item Out for Physical Delivery',
        'final delivery' => 'Final Delivery',
        'arrival at delivery office' => 'Arrival at Delivery Office',
     
        'item received at deutsche post mailterminal' => 'item received at deutsche post mailterminal',
        'processed at deutsche post mailterminal' => 'processed at deutsche post mailterminal',
        'arrival at importing country' => 'arrival at importing country',
        'item in delivery' => 'item in delivery',
        'attempted/unsuccessful (physical) delivery' =>  'attempted/unsuccessful (physical) delivery',
        'unsuccessful delivery attempt' =>  'unsuccessful delivery attempt',
        '700' => 'ImpairmentOfPerformance - Label Issues',
        '2004' => 'SystemInformation - Email Sent',
        'final delivery' => 'Final delivery',
        'item out for physical delivery' => 'Item out for physical delivery',
        'arrival at inward office of exchange' => 'Arrival at inward office of exchange',
        'item exported' => 'Item exported',
        'item received and processed at deutsche post mailterminal' => 'Item received and processed at Deutsche Post Mailterminal',
        'departure from deutsche post mailterminal' => 'Departure from Deutsche Post Mailterminal',
        'Attempted Delivery' => 'Attempted Delivery',
        'Arrived at local delivering Terminal' => 'Arrived at local delivering Terminal'
    );
    public static $oneworld_status_code = array(
        'data submitted' => 140,
        'processed sort facility' => 137,
        'departed terminal origin' => 145,
        'arrived at destination country' => 146,
        'arrived at delivering terminal' => 146,
        'return to origin terminal' => 138,
        'departed terminal at destination' => 145,
        'delivered' => 121,
        'arrived at sort facility hayes - gbr' => 137,
        'the transport of the shipment item has started in the country of the sender' => 137,
        'received & processed at deutsche post mail terminal' => 137,
        'departed deutsche post mail terminal' => 145,
        'z94' => 137,
        '31'  => 137,
        '1' => 137,
        '109' => 115,
        'z07' => 137,
        '74' => 137,
        '355' => 137,
        '6' => 137,
        'z28' => 137,
        '44' => 115,
        '270' => 129, 
        'shipment information uploaded to deutsche post' => 144,
        'arrival at destination country' => 137,
        'arrived at local delivering terminal' => 146,
        'attempted delivery' => 123,
        'held at customs' => 128,
        'return to deutsche post return center' => 138,
        'departed receivers terminal at destination' => 137,
        'departed destination terminal' => 145,
        'held at customs' => 128,
        'departed from delivering terminal at destination' => 137,
        'departed terminal' => 145,
        'departed terminal' => 137,
        'departure from deutsche post mailterminal' => 137,
        'item received and processed at deutsche post mailterminal' => 137,
        'arrival at inward office of exchange'  => 137,
        'departure from inward office of exchange' => 137,
        'item out for physical delivery' => 111,
        'final delivery' => 121,
        'arrival at delivery office' => 137,
        'item received at deutsche post mailterminal' => 137,
        'processed at deutsche post mailterminal' => 137,
        'arrival at importing country' => 137,
        'item in delivery' => 137,
        'attempted/unsuccessful (physical) delivery' => 123,
        'unsuccessful delivery attempt' => 123,
        '700' => 115,
        '2004' => 137,
        'final delivery' => 121,
        'item out for physical delivery' => 111,
        'arrival at inward office of exchange' => 137,
        'item exported' => 137,
        'item received and processed at deutsche post mailterminal' => 137,
        'departure from deutsche post mailterminal' => 137,
        'Attempted Delivery' => 123,
        'Arrived at local delivering Terminal' => 137
    );
    public static $consignment_status_code = array(
        'data submitted' => Consignment::STATUS_INTRANSIT,
        'processed sort facility' => Consignment::STATUS_INTRANSIT,
        'departed terminal origin' => consignment::STATUS_DISPATCHED,
        'arrived at destination country' => Consignment::STATUS_INTRANSIT,
        'arrived at delivering terminal' => Consignment::STATUS_INTRANSIT,
        'return to origin terminal' => consignment::STATUS_RETURNED,
        'departed terminal at destination' => consignment::STATUS_DISPATCHED,
        'delivered' => consignment::STATUS_DELIVERED,
        'arrived at sort facility hayes - gbr' => Consignment::STATUS_INTRANSIT,
        'the transport of the shipment item has started in the country of the sender' => Consignment::STATUS_INTRANSIT,
        'z94' => Consignment::STATUS_INTRANSIT,
        '31'  => Consignment::STATUS_INTRANSIT,
        '1' => Consignment::STATUS_INTRANSIT,
        '109' => consignment::STATUS_DELIVERED,
        'z07' => Consignment::STATUS_INTRANSIT,
        '74' => Consignment::STATUS_INTRANSIT,
        '355' => Consignment::STATUS_INTRANSIT,
        '6' => Consignment::STATUS_INTRANSIT,
        'z28' => Consignment::STATUS_INTRANSIT,
        '44' => consignment::STATUS_DELIVERED,
        '270' => Consignment::STATUS_INTRANSIT,
        'received & processed at deutsche post mail terminal' => Consignment::STATUS_INTRANSIT,
        'departed deutsche post mail terminal' => consignment::STATUS_DISPATCHED, 
        'shipment information uploaded to deutsche post' => consignment::STATUS_RECEIVED,
        'arrival at destination country' => Consignment::STATUS_INTRANSIT,
        'arrived at local delivering terminal' => Consignment::STATUS_INTRANSIT,
        'attempted delivery' => Consignment::STATUS_INTRANSIT,
        'held at customs' => Consignment::STATUS_INTRANSIT,
        'return to deutsche post return center' => consignment::STATUS_RETURNED,
        'departed receivers terminal at destination' => Consignment::STATUS_INTRANSIT,
        'departed destination terminal' => Consignment::STATUS_INTRANSIT,
        'held at customs' => consignment::STATUS_HOLD,
        'departed from delivering terminal at destination' => Consignment::STATUS_INTRANSIT,
        'departed terminal' => Consignment::STATUS_INTRANSIT,
        'departed terminal' => Consignment::STATUS_INTRANSIT,
        'departure from deutsche post mailterminal' => Consignment::STATUS_INTRANSIT,
        'item received and processed at deutsche post mailterminal' => Consignment::STATUS_INTRANSIT,
        'arrival at inward office of exchange'  => Consignment::STATUS_INTRANSIT,
        'departure from inward office of exchange' => Consignment::STATUS_INTRANSIT,
        'item out for physical delivery' => Consignment::STATUS_INTRANSIT,
        'final delivery' => Consignment::STATUS_DELIVERED,
        'arrival at delivery office' => Consignment::STATUS_INTRANSIT,
        'item received at deutsche post mailterminal' => Consignment::STATUS_INTRANSIT,
        'processed at deutsche post mailterminal' => Consignment::STATUS_INTRANSIT,
        'arrival at importing country' => Consignment::STATUS_INTRANSIT,
        'item in delivery' => Consignment::STATUS_INTRANSIT,
        'attempted/unsuccessful (physical) delivery' =>  Consignment::STATUS_INTRANSIT,
        'unsuccessful delivery attempt' =>  Consignment::STATUS_INTRANSIT,
        '700' => Consignment::STATUS_PROBLEM,
        '2004' => Consignment::STATUS_INTRANSIT,
        'final delivery' => Consignment::STATUS_DELIVERED,
        'item out for physical delivery' => Consignment::STATUS_INTRANSIT,
        'arrival at inward office of exchange' => Consignment::STATUS_INTRANSIT,
        'item exported' => Consignment::STATUS_INTRANSIT,
        'item received and processed at deutsche post mailterminal' => Consignment::STATUS_INTRANSIT,
        'departure from deutsche post mailterminal' => Consignment::STATUS_INTRANSIT,
        'Attempted Delivery' => Consignment::STATUS_INTRANSIT,
        'Arrived at local delivering Terminal' => Consignment::STATUS_INTRANSIT
    );
    public static function getOwestatuscode($deutschepoststatus){
        $deutscheposttrackingstatus = self::$oneworld_status_code;
        return isset($deutscheposttrackingstatus[$deutschepoststatus]) ? $deutscheposttrackingstatus[$deutschepoststatus] : 0;
    }
    public static function getconsignmentstatus($deutschepoststatus){
        $consignmentstatuscodes = tracking::$oneworld_consignment_code_mapping;
        return isset($consignmentstatuscodes[$deutschepoststatus]) ? $consignmentstatuscodes[$deutschepoststatus] : 0;
    }
}

