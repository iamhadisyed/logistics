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
class BrtItalyTrackingStatus {
    
    public function __construct() {
        
    }
    
    public static $brt_status_code = array( 
        '701' => "RITIRATA",            // WITHDRAWAL
        '702'  => "PARTITA",            // MATCH
        '703' => "ARRIVATA IN FILIALE",  // Arrived in branch
        'MIC' => "IN CONSEGNA",     // Delivery
        '704' => "CONSEGNATA",      // DELIVERED
        'RIC' => "Destin.Assente:LASCIATO AVVISO",
        'AVV' => "Destin.Assente:LASCIATO AVVISO",
        '700' => "DATI SPEDIZ. TRASMESSI A BRT",
        '713' => "TELEFONARE ALLA FILIALE", //CALL THE BRANCH
        'T' => "CHIUSO PER TURNO", //CLOSED FOR SHIFT
        'A16' => "RIMANDA LA CONSEGNA", //DELAY DELIVERY
        'DIR' => "INOLTRO ALTRA FILIALE", //ALSO OTHER BRANCH
        '023' => "DESTINATARIO CHIUSO", //CLOSED RECIPIENT
        'PAT' => "FESTIVITA' PATRONALE", //PATRONAL FESTIVITY
        '021' => "DESTINATAR.SCONOSC./INCOMPLETO", //Receiver.UNKNOWN.INCOMPLETE
        '022' => "INDIRIZ.INESISTENTE/INCOMPLETO", //ADDRESS.INEXISTENT/INCOMPLETE
        '700' => "DATI SPEDIZ. TRASMESSI A BRT", //SHIPPING DATA.TRANSMITTED TO BRT
        'ZMP' => "MANIFESTAZIONE PUBBLICA", //PUBLIC EVENT
        '711' => "TELEFONARE ALLA FILIALE", //CALL THE BRANCH
        'IDD' => "CONTATTARE FILIALE", // CONTACT BRANCH
        'N' => "DA CONSEGNARE",//TO BE DELIVERED
        'AVV' => "Destin.Assente:LASCIATO AVVISO" //Left Notice
    );
 
    public static $consignment_status_code = array(
        '701' => Consignment::STATUS_INTRANSIT,
        '702' => Consignment::STATUS_INTRANSIT,
        '703' => Consignment::STATUS_INTRANSIT,
        'MIC' => Consignment::STATUS_DISPATCHED,
        '704' => Consignment::STATUS_DELIVERED,
        'RIC' => Consignment::STATUS_INTRANSIT,
        'AVV' => Consignment::STATUS_INTRANSIT,
        '700' => Consignment::STATUS_INTRANSIT,
        '713' => Consignment::STATUS_INTRANSIT,
        'T' => Consignment::STATUS_INTRANSIT,
        'A16' => Consignment::STATUS_INTRANSIT,
        'DIR' => Consignment::STATUS_INTRANSIT,
        '023' => Consignment::STATUS_PROBLEM,
        'PAT' => Consignment::STATUS_INTRANSIT,
        '021' => Consignment::STATUS_PROBLEM,
        '022' => Consignment::STATUS_PROBLEM,
        '700' => Consignment::STATUS_INTRANSIT,
        'ZMP' => Consignment::STATUS_INTRANSIT,
        '711' => Consignment::STATUS_INTRANSIT,
        'IDD' => Consignment::STATUS_INTRANSIT,
        'N' => Consignment::STATUS_INTRANSIT,
        'AVV' => Consignment::STATUS_INTRANSIT
    );

    public static $oneworld_brt = array(
        '701' => 144,
        '702' => 148,
        '703' => 146,
        'MIC' => 126,
        '704' => 121,
        'RIC' => 123,
        'AVV' => 123, 
        '700' => 137,
        '713' => 137,
        'T' => 137,
        'A16' => 120,
        'DIR' => 137,
        '023' => 115,
        'PAT' => 137,
        '021' => 115,
        '022' => 112,
        '700' => 137,
        'ZMP' => 137,
        '711' => 137,
        'IDD' => 137,
        'N' => 137,
        'AVV' => 137
    );
    public static function getOweStatusCode($brtStatus){
        $brtTrackingStatus = self::$oneworld_brt;
        return isset($brtTrackingStatus[$brtStatus]) ? $brtTrackingStatus[$brtStatus] : 0;
    }
    public static function getConsignmentStatus($brtStatus){
        $consignmentStatusCodes = Tracking::$oneworld_consignment_code_mapping;
        return isset($consignmentStatusCodes[$brtStatus]) ? $consignmentStatusCodes[$brtStatus] : 0;
    }
}
