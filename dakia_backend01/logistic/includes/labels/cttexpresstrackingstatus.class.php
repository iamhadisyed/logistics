<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of YodelTrackingStatus
 *
 * @author kiran.iftikharEm distribuição
 */
class CttExpressTrackingStatus {
    
    public function __construct() {
        
    }
    
    public static $cttexpress_status_code = array( 
        "EMN" => "Routing error",
        "EDF" => "Object stopped", 
        "EMC" => "International dispatch", 
        "EMA" => "Acceptance by the Post Services", 
        "EMI" => "Delivery success", 
        "EMH" => "Delivery failure", 
        "EMB" => "National reception", 
        "EMD" => "International reception", 
        "EME" => "Held by Customs", 
        "EMF" => "National dispatch",
        "EMG" => "Reception by Delivery Station", 
        "EMJ" => "Arrival to Transit Station", 
        "EMK" => "Departure from Transit Station", 
        "EMX" => "Transit", 
        "EMY" => "Dispatch from Acceptance Station", 
        "EMW" => "Arrival to Depot Station", 
        "EMZ" => "In distribution", 
        "EMT" => "Send", 
        "EMR" => "Label reprint", 
        "EML" => "Departure from Customs", 
        "EMP" => "Collection", 
        "EMV" => "Devolution", 
        "EMM" => "Delivery to dispatcher", 
        "Entregue" => "Entregue", // delivered
        "Em distribuição" => "Em distribuição", // in distribution
        "Receção" => "Receção", // receiving
        "Expedição Nacional" => "Expedição Nacional", //national expidite
        "Aceitação" => "Aceitação", // Acceptance
        "Expedição internacional" => "Expedição internacional", //international expedite
        "Disponível para levantamento" => "Disponível para levantamento", // Available for survey
        "Não Entregue" => "Não Entregue", // Do not Deliver
        "Receção internacional" => "Receção internacional" //International reception
    );
 
    public static $consignment_status_code = array(
        "EMN" => Consignment::STATUS_PROBLEM,
        "EDF" => Consignment::STATUS_PROBLEM, 
        "EMC" => Consignment::STATUS_INTRANSIT, 
        "EMA" => Consignment::STATUS_INTRANSIT,  
        "EMI" => Consignment::STATUS_DELIVERED, 
        "EMH" => Consignment::STATUS_PROBLEM, 
        "EMB" => Consignment::STATUS_INTRANSIT, 
        "EMD" => Consignment::STATUS_INTRANSIT,  
        "EME" => Consignment::STATUS_INTRANSIT,   
        "EMF" => Consignment::STATUS_INTRANSIT,
        "EMG" => Consignment::STATUS_INTRANSIT,   
        "EMJ" => Consignment::STATUS_INTRANSIT,  
        "EMK" => Consignment::STATUS_INTRANSIT,   
        "EMX" => Consignment::STATUS_INTRANSIT,  
        "EMY" => Consignment::STATUS_INTRANSIT,   
        "EMW" => Consignment::STATUS_INTRANSIT,  
        "EMZ" => Consignment::STATUS_INTRANSIT,  
        "EMT" => Consignment::STATUS_INTRANSIT,  
        "EMR" => Consignment::STATUS_RELABLED,
        "EML" => Consignment::STATUS_INTRANSIT,  
        "EMP" => Consignment::STATUS_INTRANSIT,
        "EMV" => Consignment::STATUS_INTRANSIT, 
        "EMM" => Consignment::STATUS_INTRANSIT,
        "Entregue" => Consignment::STATUS_DELIVERED, // delivered
        "Em distribuição" => Consignment::STATUS_INTRANSIT, // in distribution
        "Receção" => Consignment::STATUS_INTRANSIT, // receiving
        "Expedição Nacional" => Consignment::STATUS_INTRANSIT, //national expidite
        "Aceitação" => Consignment::STATUS_INTRANSIT, // Acceptance
        "Expedição internacional" => Consignment::STATUS_INTRANSIT, //international expedite
        "Disponível para levantamento" => Consignment::STATUS_INTRANSIT, // Available for survey
        "Não Entregue" => Consignment::STATUS_PROBLEM, // Do not Deliver
        "Receção internacional" => Consignment::STATUS_INTRANSIT //International reception         
    );

    public static $oneworld_cttexpress = array(
        "EMN" => 115,
        "EDF" => 115, 
        "EMC" => 126,
        "EMA" => 144, 
        "EMI" => 121, 
        "EMH" => 127, 
        "EMB" => 137, 
        "EMD" => 137, 
        "EME" => 128, 
        "EMF" => 126,
        "EMG" => 148, 
        "EMJ" => 137, 
        "EMK" => 137, 
        "EMX" => 137, 
        "EMY" => 126, 
        "EMW" => 146, 
        "EMZ" => 111, 
        "EMT" => 137, 
        "EMR" => 137, 
        "EML" => 137, 
        "EMP" => 114, 
        "EMV" => 137, 
        "EMM" => 137, 
        "Entregue" => 121, // delivered
        "Em distribuição" => 111, // in distribution
        "Receção" => 137, // receiving
        "Expedição Nacional" => 137, //national expidite
        "Aceitação" => 148, // Acceptance
        "Expedição internacional" => 137, //international expedite
        "Disponível para levantamento" => 137, // Available for survey
        "Não Entregue" => 125 , // Do not Deliver
        "Receção internacional" => 137 //International reception
    );
    public static function getOweStatusCode($cttexpressStatus){
        $cttexpressTrackingStatus = self::$oneworld_cttexpress;
        return isset($cttexpressTrackingStatus[$cttexpressStatus]) ? $cttexpressTrackingStatus[$cttexpressStatus] : 0;
    }
    public static function getConsignmentStatus($cttexpressStatus){
        $consignmentStatusCodes = Tracking::$oneworld_consignment_code_mapping;
        return isset($consignmentStatusCodes[$cttexpressStatus]) ? $consignmentStatusCodes[$cttexpressStatus] : 0;
    }
}
