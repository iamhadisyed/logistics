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
 
       RSC => "United Kingdom, Received, Ealing Sorting Centre"	,
       DSC => "United Kingdom, Dispatched, Ealing Sorting Centre",
       PDR => "Manifest Generate, Parcel Data received, Parcel Electronically notified to OWE - awaiting to be received"	,
       SLS => "The shipment item has left the country of the sender"	,
       SAD => "The shipment item has arrived at the country of destination."	,
       UDA => "Unsuccessful delivery attempt"	,
       DEL => "The shipment item has been delivered"	,
       SCC => "The shipment item is being customs cleared by us"	,
       ADT => "The shipment item has arrived at the distribution terminal"	,
       SUT => "The shipment item is under transportation"	,
      
    );    
//}
 
 public static $consignment_status_code = array(
 
        RSC => 16	,
        DSC => 42	,
        PDR => 43	,
        SLS => 42	,
        SAD => 42	,
        UDA => 44	,
        DEL => 21	,
        SCC => 42	,
        ADT => 42	,
        SUT => 42	,
        
 
);

public static $oneworld_dhl = array(
 
       
        RSC => 146 ,
        DSC => 126  ,
        PDR => 144  ,
        SLS => 137  ,
        SAD => 137  ,
        UDA => 111  ,
        DEL => 122  ,
        SCC => 117  ,
        ADT => 137  ,
        SUT => 137  ,
    
);


}
