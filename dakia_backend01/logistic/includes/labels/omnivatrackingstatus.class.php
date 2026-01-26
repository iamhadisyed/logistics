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
class OmnivaTrackingStatus {
    
    public function __construct() {
        
    }
    
    public static $omniva_status_code = array( 
        'PACKET_EVENT_SAVED' => 'Parcel data saved to Omnivas system',
        'PACKET_EVENT_PICKED_UP_QUANTITATIVELY' => 'Parcel picked up by courier from sender or Parcel machine',
        'PACKET_EVENT_PICKED_UP_WITH_SCAN' => 'Parcel picked up by courier from sender or Parcel machine',
        'PACKET_EVENT_ARRIVED_EXCESS' => 'Parcel arrived to hub',
        'PACKET_EVENT_IN_POSTOFFICE' => 'Parcel arrived to hub',
        'PACKET_EVENT_FROM_CONTAINER' => 'Parcel arrived to hub',
        'PACKET_EVENT_FROM_WAYBILL_LIST' => 'Parcel arrived to hub',
        'PACKET_EVENT_FROM_DELIVERY_LIST' => 'Parcel arrived to hub',
        'PACKET_EVENT_OPENING_CONTAINER' => 'Parcel arrived to hub',
        'PACKET_EVENT_IN_DEST_POSTOFFICE' => 'Parcel arrived to final hub',
        'PACKET_EVENT_ON_ROUTE_LIST' => 'Parcel sent out from hub',
        'PACKET_EVENT_IN_CONTAINER' => 'Parcel sent out from hub',
        'PACKET_EVENT_ON_DELIVERY_LIST' => 'Parcel handed over to courier for delivery',
        'PACKET_EVENT_DELIVERY_CALL' => 'Receiver was contacted to make agreement',
        'PACKET_EVENT_DELIVERING_TRY' => 'Physical delivery attempt was made',
        'PACKET_EVENT_SEND_REC_EMAIL_NOTIF' => 'Receiver was notified of parcel arrival by e-mail',
        'PACKET_EVENT_SEND_REC_SMS_NOTIF' => 'Receiver was notified of parcel arrival by SMS',
        'PACKET_EVENT_STORING' => 'Parcel was storead and is waiting for receiver to pick it up',
        'PACKET_EVENT_DELIVERED' => 'Parcel was delivered',
        'PACKET_EVENT_WRITING_OFF' => 'Parcel written off when neither sender nor receiver refuses to accept it. Rarely used',
        'PACKET_EVENT_REDIRECTION' => 'Parcel has been redirected',
        'PACKET_EVENT_RETURN' => 'Parcel is starting to move back to initial sender',
        'PACKET_EVENT_DELIVERY_CANCELLED' => 'Delivery event was marked by mistake and it is cancelled. Rarely used',
        'PACKET_EVENT_IPS_C' => 'Shipment from country of departure',
        'PACKET_EVENT_IPS_EMC' => 'Shipment from country of departure',
        'PACKET_EVENT_IPS_D' => 'Arrival to destination country',
        'PACKET_EVENT_IPS_EMD' => 'Arrival to destination country',
        'PACKET_EVENT_IPS_E' => 'Customs clearance (parcel in customs)',
        'PACKET_EVENT_IPS_EME' => 'Customs clearance (parcel in customs)',
        'PACKET_EVENT_IPS_EDD' => 'Parcel arrived to hub',
        'PACKET_EVENT_IPS_EDC' => 'Item returned from customs',
        'PACKET_EVENT_IPS_EDB' => 'Item presented to customs',
        'PACKET_EVENT_IPS_EDA' => 'Held at inward OE',
        'PACKET_EVENT_IPS_EMF' => 'Departure from inward sorting centre',
    );
 
    public static $consignment_status_code = array(
        'PACKET_EVENT_SAVED' => Consignment::STATUS_LABEL_CREATED,
        'PACKET_EVENT_PICKED_UP_QUANTITATIVELY' => Consignment::STATUS_INTRANSIT,
        'PACKET_EVENT_PICKED_UP_WITH_SCAN' => Consignment::STATUS_INTRANSIT,
        'PACKET_EVENT_ARRIVED_EXCESS' => Consignment::STATUS_INTRANSIT,
        'PACKET_EVENT_IN_POSTOFFICE' => Consignment::STATUS_INTRANSIT,
        'PACKET_EVENT_FROM_CONTAINER' => Consignment::STATUS_INTRANSIT,
        'PACKET_EVENT_FROM_WAYBILL_LIST' => Consignment::STATUS_INTRANSIT,
        'PACKET_EVENT_FROM_DELIVERY_LIST' => Consignment::STATUS_INTRANSIT,
        'PACKET_EVENT_OPENING_CONTAINER' => Consignment::STATUS_INTRANSIT,
        'PACKET_EVENT_IN_DEST_POSTOFFICE' => Consignment::STATUS_INTRANSIT,
        'PACKET_EVENT_ON_ROUTE_LIST' => Consignment::STATUS_INTRANSIT,
        'PACKET_EVENT_IN_CONTAINER' => Consignment::STATUS_INTRANSIT,
        'PACKET_EVENT_ON_DELIVERY_LIST' => Consignment::STATUS_INTRANSIT,
        'PACKET_EVENT_DELIVERY_CALL' => Consignment::STATUS_INTRANSIT,
        'PACKET_EVENT_DELIVERING_TRY' => Consignment::STATUS_INTRANSIT,
        'PACKET_EVENT_SEND_REC_EMAIL_NOTIF' => Consignment::STATUS_INTRANSIT,
        'PACKET_EVENT_SEND_REC_SMS_NOTIF' => Consignment::STATUS_INTRANSIT,
        'PACKET_EVENT_STORING' => Consignment::STATUS_INTRANSIT,
        'PACKET_EVENT_DELIVERED' => Consignment::STATUS_DELIVERED,
        'PACKET_EVENT_WRITING_OFF' => Consignment::STATUS_CANCELLED,
        'PACKET_EVENT_REDIRECTION' => Consignment::STATUS_INTRANSIT,
        'PACKET_EVENT_RETURN' => Consignment::STATUS_INTRANSIT,
        'PACKET_EVENT_DELIVERY_CANCELLED' => Consignment::STATUS_CANCELLED,
        'PACKET_EVENT_IPS_C' => Consignment::STATUS_INTRANSIT,
        'PACKET_EVENT_IPS_EMC' => Consignment::STATUS_INTRANSIT,
        'PACKET_EVENT_IPS_D' => Consignment::STATUS_INTRANSIT,
        'PACKET_EVENT_IPS_EMD' => Consignment::STATUS_INTRANSIT,
        'PACKET_EVENT_IPS_E' => Consignment::STATUS_INTRANSIT,
        'PACKET_EVENT_IPS_EME' => Consignment::STATUS_INTRANSIT,
        'PACKET_EVENT_IPS_EDD' => Consignment::STATUS_INTRANSIT,
        'PACKET_EVENT_IPS_EDC' => Consignment::STATUS_INTRANSIT,
        'PACKET_EVENT_IPS_EDB' => Consignment::STATUS_INTRANSIT,
        'PACKET_EVENT_IPS_EDA' => Consignment::STATUS_HOLD,
        'PACKET_EVENT_IPS_EMF' => Consignment::STATUS_INTRANSIT
    );

    public static $oneworld_omniva = array(        
        'PACKET_EVENT_SAVED' => 144,
        'PACKET_EVENT_PICKED_UP_QUANTITATIVELY' => 133,
        'PACKET_EVENT_PICKED_UP_WITH_SCAN' => 133,
        'PACKET_EVENT_ARRIVED_EXCESS' => 137,
        'PACKET_EVENT_IN_POSTOFFICE' => 137,
        'PACKET_EVENT_FROM_CONTAINER' => 137,
        'PACKET_EVENT_FROM_WAYBILL_LIST' => 137,
        'PACKET_EVENT_FROM_DELIVERY_LIST' => 137,
        'PACKET_EVENT_OPENING_CONTAINER' => 137,
        'PACKET_EVENT_IN_DEST_POSTOFFICE' => 137,
        'PACKET_EVENT_ON_ROUTE_LIST' => 137,
        'PACKET_EVENT_IN_CONTAINER' => 137,
        'PACKET_EVENT_ON_DELIVERY_LIST' => 111,
        'PACKET_EVENT_DELIVERY_CALL' => 137,
        'PACKET_EVENT_DELIVERING_TRY' => 123,
        'PACKET_EVENT_SEND_REC_EMAIL_NOTIF' => 137,
        'PACKET_EVENT_SEND_REC_SMS_NOTIF' => 137,
        'PACKET_EVENT_STORING' => 137,
        'PACKET_EVENT_DELIVERED' => 121,
        'PACKET_EVENT_WRITING_OFF' => 152,
        'PACKET_EVENT_REDIRECTION' => 137,
        'PACKET_EVENT_RETURN' => 137,
        'PACKET_EVENT_DELIVERY_CANCELLED' => 155,
        'PACKET_EVENT_IPS_C' => 137,
        'PACKET_EVENT_IPS_EMC' => 137,
        'PACKET_EVENT_IPS_D' => 149,
        'PACKET_EVENT_IPS_EMD' => 149,
        'PACKET_EVENT_IPS_E' => 117,
        'PACKET_EVENT_IPS_EME' => 117,
        'PACKET_EVENT_IPS_EDD' => 137,
        'PACKET_EVENT_IPS_EDC' => 138,
        'PACKET_EVENT_IPS_EDB' => 117,
        'PACKET_EVENT_IPS_EDA' => 128,
        'PACKET_EVENT_IPS_EMF' => 137,           
    );
    public static function getOweStatusCode($omnivaStatus){
        $omnivaTrackingStatus = self::$oneworld_omniva;
        return isset($omnivaTrackingStatus[$omnivaStatus]) ? $omnivaTrackingStatus[$omnivaStatus] : 0;
    }
    public static function getConsignmentStatus($omnivaStatus){
        $consignmentStatusCodes = Tracking::$oneworld_consignment_code_mapping;
        return isset($consignmentStatusCodes[$omnivaStatus]) ? $consignmentStatusCodes[$omnivaStatus] : 0;
    }
}
