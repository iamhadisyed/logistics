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
 

       DR => "Manifest Generate, Parcel Data received, Parcel Electronically notified to OWE - awaiting to be received"  ,
       RC => "United Kingdom, Received, Ealing Sorting Centre" ,
       DC => "United Kingdom, Dispatched, Ealing Sorting Centre",
       DS => "Data Submitted" ,
       PS => "Processed Sort Facility",
       DT => "Departed Terminal origin"  ,
       AC => "Arrived at destination country"  ,
       HC => "Held at Customs"  ,
       DD => "Departed Terminal at destination" ,
       AT => "Arrived at Delivering Terminal"  ,
       AD => "Attempted Delivery"  ,
       DE => "Delivered"  ,
       UT => "The shipment item is under transportation" ,
       RT => "Return to origin terminal" ,


      
    );    
//}
 
 public static $consignment_status_code = array(
 
        DR => 43	,
        RC => 16	,
        DC => 42	,
        DS => 42	,
        PS => 42	,
        DT => 42	,
        AC => 42	,
        HC => 17	,
        DD => 42	,
        AT => 42	,
        AD => 42  ,
        DE => 21  ,
        UT => 42  ,
        RT => 45  ,
        
 
);

public static $oneworld_dhl = array(
 
       
        DR => 144  ,
        RC => 146  ,
        DC => 126  ,
        DS => 137  ,
        PS => 137  ,
        DT => 137  ,
        AC => 137  ,
        HC => 128  ,
        DD => 137  ,
        AT => 137  ,
        AD => 137  ,
        DE => 122  ,
        UT => 137  ,
        RT => 138  ,
    
);


}
