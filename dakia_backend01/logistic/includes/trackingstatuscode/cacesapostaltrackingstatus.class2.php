<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of cacesapostalTrackingStatus
 *
 */
class cacesapostalTrackingStatus {
    
    public function __construct() {
        
    }
    
 public static $cacesapostal_status_code = array(
 
       IRS  => "THE ITEM IS READY TO BE SHIPPED"                 ,
       ENR  => "ELECTRONIC NOTIFICATION RECEIVED"                ,
       SRE  => "SHIPMENT RECEIVED"                               ,
       SCP  => "START CUSTOM CLEAR PROCESS"                      ,
       ECP  => "END CUSTOM CLEAR PROCESS (GREEN CHANNEL)"	 ,
       OLP  => "THE ORDER HAS LEFT THE SELLER'S PREMISES"	 ,
       SLP  => "SHIPPING HAS LEFT THE SELLER'S PREMISES"         ,
       ODE  => "ON DELIVERY"                                     , 
       SPR  => "SHIPPING PENDING TO RECEIVE"                     ,
       OFD  => "OUT FOR DELIVERY"                                ,
       UMD  => "WE WERE UNABLE TO MAKE THE DELIVERY"             ,
       DEL  => "DELIVERED"                                       ,
       
    );    
//}
 
 public static $consignment_status_code = array(
 
       IRS  => 27                 ,
       ENR  => 35                 ,
       SRE  => 15                 ,
       SCP  => 42                 ,
       ECP  => 42              	  ,
       OLP  => 30                 ,
       SLP  => 42                 ,
       ODE  => 44                 , 
       SPR  => 41                 ,
       OFD  => 42                 ,
       UMD  => 46                 ,
       DEL  => 21                 ,
);

public static $oneworld_cacesapostal = array(
 
       IRS  => 126                 ,
       ENR  => 144                 ,
       SRE  => 146                 ,
       SCP  => 117                 ,
       ECP  => 117              	  ,
       OLP  => 137                 ,
       SLP  => 137                 ,
       ODE  => 137                 , 
       SPR  => 124                 ,
       OFD  => 111                 ,
       UMD  => 125                 ,
       DEL  => 122                 ,
    
);


}
