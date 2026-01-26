<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AsendiaTrackingStatus
 *
 */
class AsendiaTrackingStatus {
    
    public function __construct() {
        
    }
    
 public static $asendia_status_code = array(
 
       PD  => "Parcel Departure"	,
       PP  => "Parcel Leaving Port",
       RE  => "Received"	,
       DI  => "Dispatched"	,
       OW  => "OnTheWay"	,
       FO  => "Forwarding"	,
       CL  => "Closed",
       CP  => "CarrierPickup", 
       PR  => "Processed" 	,
       DE  => "Delivered"	,
       DL  => "Delayed" ,
       DA  => "DeliveredToOtherAddress" ,
       CR  => "Created" ,
       WA  => "WaitingNextAttempt" ,
       
    );    
//}
 
 public static $consignment_status_code = array(
 
       PD  => 30        ,
       PP  => 31        ,
       RE  => 15        ,
       DI  => 42  	,
       OW  => 44        ,
       FO  => 42	,
       CL  => 40        ,
       CP  => 42        , 
       PR  => 42     	,
       DE  => 21	,
       DL  => 33        ,
       DA  => 21        ,
       CR  => 43        ,
       WA  => 46        ,
);

public static $oneworld_asendia = array(
 
       PD  => 126        ,
       PP  => 137        ,
       RE  => 146        ,
       DI  => 127  	,
       OW  => 124       ,
       FO  => 137	,
       CL  => 127        ,
       CP  => 133        , 
       PR  => 137     	,
       DE  => 122	,
       DL  => 120        ,
       DA  => 122        ,
       CR  => 147        ,
       WA  => 111        ,
    
);


}
