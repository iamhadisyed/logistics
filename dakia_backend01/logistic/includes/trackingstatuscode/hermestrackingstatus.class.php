<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of HermesTrackingStatus
 *
 */
class HermesTrackingStatus {
    
    public function __construct() {
        
    }
    
 public static $hermes_status_code = array(
 
       000  => "Pre-adviced parcel has not yet arrived at Hermes depot #NDL# "	,
       010  => "Parcel has left the client’s warehouse",
       012  => "Parcel collected from Hermes parcelshop"	,
       030 => "Parcel processed at HUB "	,
       037 => "Parcel received at Hermes depot #NDL# "	,
       045 => "Parcel received"	,
       050 => "Out for delivery",
       055 => "Delivered to Hermes ParcelShop - ready for collection", 
              "Out for delivery (to islands)" 	,
       060 => "Customer refused to accept the parcel"	,
              "Address provided could not be found" ,
              "Delivery attempt not successful" ,
              "Sorry we missed you at the fourth visit. The parcel is being returned to the sender." ,
              "Parcel is being forwarded to Hermes depot #NDL#" ,
              "Not delivered - damaged parcel" ,
              "Parcel delayed at Hermes depot" ,
              "Not delivered - COD amount not paid" ,
              "Return to sender - Parcel has not been collected at the Hermes parcelshop within the time limit" ,   
       065 => "Parcel collected - processed by depot #NDL# "	,
              "Return - processed at #NDL#",
              "Return - picked up at Hermes parcelshop" ,
              "Return - received at Hermes parcelshop " ,   
       080 => "Parcel kept at Hermes depot #NDL#"	,
       090 => "Delivered"	,
       100 => "Returned to sender "	,
              "Parcel sent to HUB " ,
              "Parcel is being forwarded to the relevant Hermes depot. ",
       115 => "Return - processed at HUB "	,
       
    );    
//}
 
 public static $consignment_status_code = array(
 
        000 => 48	,
        010 => 30	,
        012 => 21	,
        030 => 42       ,
        037 => 16       ,
        045 => 15       ,
        050 => 44	,
        055 => 41	,
        060 => 22	,
        065 => 21	,
        080 => 17	,
        090 => 21	,
        100 => 24	,
        115 => 26	,
);

public static $oneworld_hermes = array(
 
        000 => 113	,
        010 => 137	,
        012 => 134	,
        030 => 137       ,
        037 => 137       ,
        045 => 146      ,
        050 => 111	,
        055 => 114	,
        060 => 115	,
        065 => 138      ,
        080 => 128	,
        090 => 122	,
        100 => 138	,
        115 => 143	,
    
);


}
