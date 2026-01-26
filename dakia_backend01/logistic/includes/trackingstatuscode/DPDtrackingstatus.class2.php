<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DPDTrackingStatus
 *
 */
class dpdTrackingStatus {
    
    public function __construct() {
        
    }
    
 public static $dpd_status_code = array(
 
       OT  => "ORDER INFORMATION HAS BEEN TRANSMITTED TO DPD"  
       RC  => "RECEIVED BY DPD FROM CONSIGNOR"               ,
       IN  => "IN TRANSIT"                ,                           ,
       DC  => "PARCEL AT DELIVERY CENTRE"                      ,
       DU  => "BACK AT PARCEL DELIVERY CENTRE AFTER AN UNSUCCESSFUL DELIVERY ATTEMPT"	 ,
       OD  => "OUT FOR DELIVERY"	 ,
       DE  => "DELIVERED"         ,
       
    );    
//}
 
 public static $consignment_status_code = array(
 
       OT  => 35     ,  
       RC  => 16     ,
       IN  => 42     ,
       DC  => 42     ,
       DU  => 46     ,
       OD  => 44     ,
       DE  => 21     ,
);

public static $oneworld_dpd = array(
 
       OT  => 147     ,  
       RC  => 146     ,
       IN  => 137     ,
       DC  => 137     ,
       DU  => 125     ,
       OD  => 111     ,
       DE  => 122     ,
    
);


}
