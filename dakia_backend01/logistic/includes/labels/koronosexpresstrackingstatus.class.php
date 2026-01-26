<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of koronosTrackingStatus
 *
 * @author kiran.iftikhar
 */
class koronosExpressTrackingStatus {
    
    public function __construct() {        
    }
    
    public static $koronexpress_status_code = array( 
        "1001" => "Reciepient Absent",
        "1002" => "Wrong delivery Details",
        "9110" => "Unloaded to Location",
        "9109" => "Loaded for Transfer",
        "9805" => "Issue Submission From System",
        "1003" => "Change delivery Address",
        "1004" => "Parcel Delay",
        "1111" => "ON HOLD",
        "2001" => "Delivery Refusal",
        "2002" => "Refusal to Pay Shipping fees",
        "2003" => "Refusal to Pay COD",
        "2004" => "Delivery Refusal Due To Parcel Status",
        "3001" => "Cannot contact recipient",
        "3002" => "Cannot contact sender",
        "8001" => "Issue A/R",
        "8002" => "A/R Shipping Voucher",
        "9106" => "Not Done Reception",
        "9830" => "Driver Check",
        "9850" => "Charge procedure",
        "9860" => "Received by  PDA",
        "9910" => "Charge for Delivery to Recipient",
        "9920" => "Return Undelivered",
        "9950" => "Submission Delivered",
        "9960" => "Delivery Information",
        "9999" => "Canceled from Dispatcher",
        "5555" => "end Cash on delivery",
        "4444" => "Issue Cash on Delivery voucher",
        "2121" => "Return to LR",
        "6636" => "Change Delivery Date From Recipient",
        "2323" => "Return to sender",
        "1023" => "Customer request to pick up from station",
        "2525" => "Sender Requested Return of the Consignment",
        "2351" => "Duplicated Order",
        "2022" => "Call Reminder For Pickup",
        "2191" => "Invalid Order",
        "2424" => "Change location for return",
        "2626" => "Change location",
        "2125" => "Note on receiver's door",
        "35" => "Inserted into system for machine preparation",
        "32" => "Placed in the machine",
        "36" => "Receiver informed via SMS",
        "37" => "Parcel collected by Receiver"
        );

    public static $consignment_status_code = array(
        "1001" => Consignment::STATUS_PROBLEM,
        "1002" => Consignment::STATUS_PROBLEM,
        "9110" => Consignment::STATUS_INTRANSIT,
        "9109" => Consignment::STATUS_INTRANSIT,
        "9805" => Consignment::STATUS_INTRANSIT,
        "1003" => Consignment::STATUS_INTRANSIT,
        "1004" => Consignment::STATUS_INTRANSIT,
        "1111" => Consignment::STATUS_HOLD,
        "2001" => Consignment::STATUS_NOT_DELIVERED,
        "2002" => Consignment::STATUS_PROBLEM,
        "2003" => Consignment::STATUS_PROBLEM,
        "2004" => Consignment::STATUS_NOT_DELIVERED,
        "3001" => Consignment::STATUS_PROBLEM,
        "3002" => Consignment::STATUS_PROBLEM,
        "8001" => Consignment::STATUS_INTRANSIT,
        "8002" => Consignment::STATUS_INTRANSIT,
        "9106" => Consignment::STATUS_PROBLEM,
        "9830" => Consignment::STATUS_INTRANSIT,
        "9850" => Consignment::STATUS_INTRANSIT,
        "9860" => Consignment::STATUS_INTRANSIT,
        "9910" => Consignment::STATUS_INTRANSIT,
        "9920" => Consignment::STATUS_RETURNED,
        "9950" => Consignment::STATUS_INTRANSIT,
        "9960" => Consignment::STATUS_INTRANSIT,
        "9999" => Consignment::STATUS_CANCELLED,
        "5555" => Consignment::STATUS_INTRANSIT,
        "4444" => Consignment::STATUS_INTRANSIT,
        "2121" => Consignment::STATUS_RETURNED,
        "6636" => Consignment::STATUS_INTRANSIT,
        "2323" => Consignment::STATUS_RETURNED,
        "1023" => Consignment::STATUS_INTRANSIT,
        "2525" => Consignment::STATUS_INTRANSIT,
        "2351" => Consignment::STATUS_PROBLEM,
        "2022" => Consignment::STATUS_INTRANSIT,
        "2191" => Consignment::STATUS_PROBLEM,
        "2424" => Consignment::STATUS_INTRANSIT,
        "2626" => Consignment::STATUS_INTRANSIT,
        "2125" => Consignment::STATUS_INTRANSIT,
        "35"   => Consignment::STATUS_INTRANSIT,
        "32"   => Consignment::STATUS_INTRANSIT,
        "36"   => Consignment::STATUS_INTRANSIT,
        "37"   => Consignment::STATUS_INTRANSIT        
        );

    public static $oneworld_koronexpress = array(
        "1001" => 116,
        "1002" => 115,
        "9110" => 137,
        "9109" => 137,
        "9805" => 144,
        "1003" => 137,
        "1004" => 120,
        "1111" => 136,
        "2001" => 115,
        "2002" => 115,
        "2003" => 115,
        "2004" => 115,
        "3001" => 116,
        "3002" => 115,
        "8001" => 137,
        "8002" => 137,
        "9106" => 115,
        "9830" => 137,
        "9850" => 137,
        "9860" => 137,
        "9910" => 137,
        "9920" => 138,
        "9950" => 121,
        "9960" => 137,
        "9999" => 137,
        "5555" => 137,
        "4444" => 137,
        "2121" => 138,
        "6636" => 137,
        "2323" => 138,
        "1023" => 137,
        "2525" => 137,
        "2351" => 115,
        "2022" => 137,
        "2191" => 115,
        "2424" => 137,
        "2626" => 137,
        "2125" => 137,
        "35"   => 137,
        "32"   => 137,
        "36"   => 137,
        "37"   => 140
    );
    public static function getOweStatusCode($koronexpressStatus){
        $koronexpressStatus = trim($koronexpressStatus);
        $koronexpressTrackingStatus = self::$oneworld_koronexpress;
        return isset($koronexpressTrackingStatus[$koronexpressStatus]) ? $koronexpressTrackingStatus[$koronexpressStatus] : 0;
    }
    public static function getConsignmentStatus($koronexpressStatus){
        $consignmentStatusCodes = Tracking::$oneworld_consignment_code_mapping;
        return isset($consignmentStatusCodes[$koronexpressStatus]) ? $consignmentStatusCodes[$koronexpressStatus] : 0;
    }
    public static function getDescription($koronexpressStatus)
    {
        $koronexpressTrackingStatus = self::$koronexpress_status_code;
        return isset($koronexpressTrackingStatus[$koronexpressStatus]) ? $koronexpressTrackingStatus[$koronexpressStatus] : 0;
    }
}
