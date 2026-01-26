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
class DhlTrackingStatus {
    
    public function __construct() {
        
    }
    
 public static $dhl_status_code = array(
 
       BA => "Bad Address"	,
       CA => "Closed on Arrival",
       CD => "Clearance Delay"	,
       CM => "Consignee Moved"	,
       HP => "Held for Payment"	,
       MC => "Miscode"	,
       MS => "Missort"	,
       NH => "Not Home"	,
       OH => "On Hold"	,
       RD => "Refused Delivery"	,
       UD => "Uncontrollable Clearance Delay"	,
       BR => "Cleared and Delivered by Broker"	,
       CS => "Closed Shipments"	,
       DD => "Delivered Damaged"	,
       DM => "Damaged"	,
       OK => "Delivery"	,
       RT => "Retuned to Consignor"	,
       AF => "Arrived Facility"	,
       AR => "Arrival at delivery facility"	,
       CC => "Awaiting Consignee Collection"	,
       CI => "Facility CheckIn"	,
       CR => "Clearance Release"	,
       CU => "Confirm Uplift"	,
       DF => "Depart Facility"	,
       FD => "Forwarded to Third Party Delivery Agent"	,
       PL => "Processed at Location"	,
       PU => "Shipment Pickup"	,
       SD => "Shipment Detail",
       TR => "Record of Transit"	,
       WC => "With Delivering Courier"	,
       
    );    
//}
 
 public static $consignment_status_code = array(
 
        BA => 47	,
        CA => 40	,
        CD => 42	,
        CM => 22	,
        HP => 17	,
        MC => 22	,
        MS => 22	,
        NH => 42	,
        OH => 33	,
        RD => 22	,
        UD => 42	,
        
        BR => 21	,
        CS => 42	,
        DD => 22	,
        DM => 22	,
        OK => 21	,
        RT => 24	,
        
        AF => 42	,
        AR => 42	,
        CC => 17	,
        CI => 42	,
        CR => 42	,
        CU => 42	,
        DF => 42	,
        FD => 42	,
        PL => 42	,
        PU => 42	,	
        SD => 42,
        TR => 42	,
        WC => 42	,
 
);

public static $oneworld_dhl = array(
 
       
        BA => 112	,
        CA => 116	,
        CD => 120	,
        CM => 116	,
        HP => 128	,
        MC => 115	,
        MS => 115	,
        NH => 137	,
        OH => 128	,
        RD => 115	,
        UD => 120	,
        
        BR => 122	,
        CS => 127	,
        DD => 119	,
        DM => 119	,
        OK => 137	,
        RT => 138	,


        AF => 137	,
        AR => 137	,
        CC => 137	,
        CI => 137	,
        CR => 137	,
        CU => 137	,
        DF => 137	,
        FD => 137	,
        PL => 137	,
        PU => 137	,
        SD => 137       ,
        TR => 137	,
        WC => 137	,
    
);


}
