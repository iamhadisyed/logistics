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
class GlsNLTrackingStatus {
    
    public function __construct() {
        
    }
    

    public static $glsnl_status_code = array(
        "0.0" => "Pakket ontvangen door GLS",// Package received by GLS
        "2.0" => "Aangekomen op GLS depot", // Arrived at GLS depot
        "1.0" => "Doorgestuurd naar GLS depot", // Forwarded to GLS depot
        "11.0" => "Onderweg - geladen voor aflevering", // On the way - loaded for delivery
        "4.37" => "Niet afgeleverd - bedrijf gesloten", // Not delivered - company closed
        "35.37" => "Terug op GLS depot - bedrijf gesloten",   // Back at GLS depot - company closed
        "3.0" => "Afgeleverd",  // Delivered
        "2.29" => "Aangekomen op GLS depot", // Arrived at GLS depot
        "0.100" => "Aangekondigd bij GLS",
        "12.8" => "Niet in levering - te laat binnengekomen",// not in delivery arrived too late
        "4.80" => "Niet afgeleverd - wordt opnieuw aangeboden", //Not delivered - will be offered again
        "8.97" => "In bewaring - foutmelding", //In custody - error message
        "9.0" => "Foutmelding - wel gegevens maar geen pakket", //Error message - data but no package
        "46.183" => "Registratie - bevestiging schade", // Registration - confirmation of damage
        "4.31" => "Niet afgeleverd - adres incompleet/onjuist", //Not delivered - address incomplete / incorrect
        "12.38" => "Niet in levering  - geen ontvangstmogelijkheid", //Not in delivery - no reception option
        "12.41" => "Niet in levering - bedrijf gesloten, vakantie", //Not in delivery - company closed, holiday
        "12.80" => "Niet in levering - op verzoek later leveren", //Not in delivery - delivery later on request
        "46.161" => "Registratie - beschadigd tijdens transport", //Registration - damaged during transport
        "12.20" => "Niet in levering - (verpakkings)schade", //Not in delivery - (packaging) damage
        "12.10" => "Niet in levering - onvoldoende ruimte", //Not in delivery - insufficient space
        "67.0" => "Geladen - voertuig", //Loaded - vehicle
        "49.0" => "Controleren", //To Check
        "95.0" => "Instructie - label geprint", //Instruction label printed
        "14.60" => "GLS Customs - blokkade door ontbrekende factuur", //GLS Customs - blockage due to missing invoice
        "26.396" => "Systeem - LOCK/RETURN", //Systeem - LOCK/RETURN
        "12.83" => "Niet in levering - op verzoek zelf afhalen", //Not in delivery - collect on request
        "46.104" => "Registratie - controlescan", //Not in delivery - collect on request
        "6.13" => "Doorgestuurd - afleveradres volgens instructie", //Forwarded - delivery address according to instruction
        "4.49" => "Niet afgeleverd - bijzondere omstandigheden", //Not delivered - special circumstances
        "12.0" => "Niet in levering", //Not in delivery
        "2.106" => "Aangekomen op GLS depot - handmatige sortering", //Arrived at GLS depot - manual sorting
        "35.129" => "Terug op GLS depot - niet afgehaald bij ParcelShop", //Back at GLS depot - not picked up at ParcelShop
        "1.124" => "Collectie bij ParcelShop", //Collection at ParcelShop
        "4.129" => "Niet afgeleverd - niet afgehaald bij ParcelShop", //Not delivered - not picked up at ParcelShop
        "2.101" => "Aangekomen op GLS depot", //Arrived at GLS depot
        "25.91" => "Wijziging - adresgegevens", //Change - address details
        "12.32" => "Niet in levering - adressering niet juist", //Not in delivery - address not correct
        "0.258" => "Aangekondigd bij GLS", //Announced at GLS
        "46.287" => "Registratie - herverpakt",//Registration - repackaged
        "2.107" => "Aangekomen op GLS depot - verzendetiket onjuist", //Arrived at GLS depot - shipping label incorrect
        "89.456" => "Bericht - Email naar verzender", //Message - Email to sender
        "33.91" => "Instructie ontvangen - leveren op ander adres", //Receive instruction - deliver to a different address
        "27.194" => "Systeem - UNLOCK/RTS",//System - UNLOCK / RTS
        "96.0" => "Verzoek - meer informatie noodzakelijk", //Request - more information necessary
        "2.124" => "Aangekomen bij ParcelShop", //Arrived at ParcelShop
        "2.79" => "Aangekomen op GLS depot", //Arrived at GLS depot
        "8.32" => "In bewaring - adresinformatie noodzakelijk", //In custody - address information required
        "4.55" => "Niet afgeleverd - tijdvenster niet gehaald", //Not delivered - time window not reached
        "81.0" => "Collectie - pakket is meegegeven aan GLS", //Collection package was passed on to GLS
        "12.52" => "Niet in levering - pakket onvindbaar", //Not in delivery - package cannot be found
        "2.20" => "Aangekomen op GLS depot - (verpakkings)schade", //Arrived at GLS depot - (packaging) damage
        "72.0" => "Relabeled",
        "4.319" => "Niet afgeleverd - toegangscode noodzakelijk", //Not delivered - access code required
        "12.99" => "Niet in levering - bijzondere omstandigheden", //Not in delivery - special circumstances
        "33.153" => "Wijziging - gewicht", //Change - weight
        "46.0" => "Registratie - controlescan", //Registration - control scan
        "5.0" => "Geretourneerd - aan afzender", //Returned - to sender
        "35.49" => "Terug op GLS depot - bijzondere omstandigheden", //Back at GLS depot - special circumstances
        "35.36" => "Terug op GLS depot - interne sorteerfout", //Back on GLS depot - internal sorting error
        "4.36" => "Niet afgeleverd - interne sorteerfout GLS depot", //Not delivered - internal sorting error GLS depot
        "3.120" => "Afgeleverd volgens instructie", //Delivered according to instruction
        "46.143" => "Registratie - controlescan", //Registration - control scan
        "8.10" => "In bewaring - onvoldoende ruimte", //In custody - insufficient space
        "74.0" => "Correctie", //Correction
        "6.2" => "Doorgestuurd - naar juiste GLS depot", //Forwarded - to correct GLS depot
        "72.257" => "Relabeled- extra labels noodzakelijk", //Relabeled- additional labels required
        "4.260" => "Niet afgeleverd - bijzondere omstandigheden", //Not delivered - special circumstances
        "3.124" => "Overgedragen aan de GLS ParcelShop", //Transferred to the GLS ParcelShop
        "0.100" => "Aangekondigd bij GLS", //Announced at GLS
        "12.2" => "Niet in levering - verkeerd depot", //Not in delivery - wrong depot
        "12.12" => "Niet in levering - foute codering etiket", //Not in delivery - incorrect coding label
        "6.12" => "Doorgestuurd - sorteerinformatie niet juist", //Forwarded - sort information incorrect
        "6.0" => "Doorgestuurd", //Forwarded
        "12.36" => "Niet in levering - interne sorteerfout", //Not in delivery - internal sorting error
        "35.40" => "Terug op GLS depot - Niet Thuis Bericht", //Back on GLS depot - Not Home Message
        "4.40" => "Niet afgeleverd - Niet Thuis Bericht", //Not delivered - Not Home Message
        "8.0" => "In bewaring - opgeslagen", //For safekeeping - stored
        "35.32" => "Terug op GLS depot - adressering niet juist", //Back on GLS depot - address not correct
        "4.32" => "Niet afgeleverd - adressering niet juist", //Not delivered - address not correct
        "3.121" => "Afgeleverd - bij buren Getekend door", // Delivered to neighbours
        "46.160" => "Registratie - beschadigd", // Registration - damaged
        "4.30" => "Niet afgeleverd - geadresseerde is verhuisd", // Not delivered - addressee has moved
        "30.40" => "Niet afgeleverd - geadresseerde 2 maal niet thuis", //Not delivered - consignee not home 2 times
        "4.42" => "Niet afgeleverd - receptie gesloten"
    );
 
    public static $consignment_status_code = array(
        "0.0" => Consignment::STATUS_INTRANSIT,
        "2.0" => Consignment::STATUS_INTRANSIT,
        "1.0" => Consignment::STATUS_INTRANSIT,
        "11.0" => Consignment::STATUS_INTRANSIT,
        "4.37" => Consignment::STATUS_PROBLEM,
        "35.37" => Consignment::STATUS_INTRANSIT,
        "3.0" => Consignment::STATUS_DELIVERED,
        "2.29" => Consignment::STATUS_INTRANSIT,
        "0.100" => Consignment::STATUS_INTRANSIT,
        "12.8" => Consignment::STATUS_INTRANSIT,
        "4.80" => Consignment::STATUS_INTRANSIT,
        "8.97" => Consignment::STATUS_PROBLEM,
        "9.0" => Consignment::STATUS_PROBLEM,
        "46.183" => Consignment::STATUS_PROBLEM,
        "4.31" => Consignment::STATUS_PROBLEM,
        "12.38" => Consignment::STATUS_PROBLEM,
        "12.41" => Consignment::STATUS_INTRANSIT,
        "12.80" => Consignment::STATUS_INTRANSIT,
        "46.161" => Consignment::STATUS_PROBLEM,
        "12.20" => Consignment::STATUS_PROBLEM,
        "12.10" => Consignment::STATUS_PROBLEM,
        "67.0" => Consignment::STATUS_INTRANSIT,
        "49.0" => Consignment::STATUS_INTRANSIT,
        "95.0" => Consignment::STATUS_INTRANSIT,
        "14.60" => Consignment::STATUS_HOLD,
        "26.396" => Consignment::STATUS_RETURNED,
        "12.83" => Consignment::STATUS_INTRANSIT,
        "46.104" => Consignment::STATUS_INTRANSIT,
        "6.13" => Consignment::STATUS_INTRANSIT,
        "4.49" => Consignment::STATUS_NOT_DELIVERED,
        "12.0" => Consignment::STATUS_INTRANSIT,
        "2.106" => Consignment::STATUS_INTRANSIT,
        "35.129" => Consignment::STATUS_INTRANSIT,
        "1.124" => Consignment::STATUS_INTRANSIT,
        "4.129" => Consignment::STATUS_NOT_DELIVERED,
        "2.101" => Consignment::STATUS_INTRANSIT,
        "25.91" => Consignment::STATUS_INTRANSIT,
        "12.32" => Consignment::STATUS_PROBLEM,
        "0.258" => Consignment::STATUS_INTRANSIT,
        "46.287" => Consignment::STATUS_INTRANSIT,
        "2.107" => Consignment::STATUS_PROBLEM,
        "89.456" => Consignment::STATUS_INTRANSIT,
        "33.91" => Consignment::STATUS_DELIVERED,
        "27.194" => Consignment::STATUS_RETURNED,
        "96.0" => Consignment::STATUS_INTRANSIT,
        "2.124" => Consignment::STATUS_INTRANSIT,
        "2.79" => Consignment::STATUS_INTRANSIT,
        "8.32" => Consignment::STATUS_INTRANSIT,
        "4.55" => Consignment::STATUS_NOT_DELIVERED,
        "81.0" => Consignment::STATUS_INTRANSIT,
        "12.52" => Consignment::STATUS_PROBLEM,
        "2.20" => Consignment::STATUS_PROBLEM,
        "72.0" => Consignment::STATUS_RELABLED,
        "4.319" => Consignment::STATUS_NOT_DELIVERED,
        "12.99" => Consignment::STATUS_PROBLEM,
        "33.153" => Consignment::STATUS_INTRANSIT,
        "46.0" => Consignment::STATUS_INTRANSIT,
        "5.0" => Consignment::STATUS_RETURNED,
        "35.49" => Consignment::STATUS_INTRANSIT,
        "35.36" => Consignment::STATUS_PROBLEM,
        "4.36" => Consignment::STATUS_NOT_DELIVERED,
        "3.120" => Consignment::STATUS_DELIVERED,
        "46.143" => Consignment::STATUS_INTRANSIT,
        "8.10" => Consignment::STATUS_INTRANSIT,
        "74.0" => Consignment::STATUS_INTRANSIT,
        "6.2" => Consignment::STATUS_INTRANSIT,
        "72.257" => Consignment::STATUS_INTRANSIT,
        "4.260" => Consignment::STATUS_NOT_DELIVERED,
        "3.124" => Consignment::STATUS_INTRANSIT,
        "0.100" => Consignment::STATUS_INTRANSIT,
        "12.2" => Consignment::STATUS_PROBLEM,
        "12.12" => Consignment::STATUS_PROBLEM,
        "6.12" => Consignment::STATUS_PROBLEM,
        "6.0" => Consignment::STATUS_INTRANSIT,
        "12.36" => Consignment::STATUS_PROBLEM,
        "35.40" => Consignment::STATUS_INTRANSIT,
        "4.40" => Consignment::STATUS_NOT_DELIVERED,
        "8.0" => Consignment::STATUS_INTRANSIT,
        "35.32" => Consignment::STATUS_PROBLEM,
        "4.32" => Consignment::STATUS_NOT_DELIVERED,
        "3.121" => Consignment::STATUS_DELIVERED,
        "46.160" => Consignment::STATUS_PROBLEM,
        "4.30" => Consignment::STATUS_NOT_DELIVERED,
        "30.40" => Consignment::STATUS_NOT_DELIVERED,
        "4.42" => Consignment::STATUS_NOT_DELIVERED
    );

    public static $oneworld_glsnl = array(
        "0.0" => 137,
        "2.0" => 146,
        "1.0" => 137,
        "11.0" => 111,
        "4.37" => 116,
        "35.37" => 137,
        "3.0" => 121,
        "2.29" => 146,
        "0.100" => 137,
        "12.8" => 137,
        "4.80" => 137,
        "8.97" => 115,
        "9.0" => 115,
        "46.183" => 118,
        "4.31" => 112,
        "12.38" => 115,
        "12.41" => 137,
        "12.80" => 137,
        "46.161" => 118,
        "12.20" => 119,
        "12.10" => 137,
        "67.0" => 137,
        "49.0" => 137,
        "95.0" => 144,
        "14.60" => 136,
        "26.396" => 138,
        "12.83" => 137,
        "46.104" => 137,
        "6.13" => 137,
        "4.49" => 125,
        "12.0" => 137,
        "2.106" => 146,
        "35.129" => 137,
        "1.124" => 137,
        "4.129" => 125,
        "2.101" => 146,
        "25.91" => 137,
        "12.32" => 112,
        "0.258" => 137,
        "46.287" => 137,
        "2.107" => 130,
        "89.456" => 137,
        "33.91" => 121,
        "27.194" => 138,
        "96.0" => 137,
        "2.124" => 146,
        "2.79" => 146,
        "8.32" => 112,
        "4.55" => 125,
        "81.0" => 137,
        "12.52" => 157,
        "2.20" => 118,
        "72.0" => 137,
        "4.319" => 125,
        "12.99" => 137,
        "33.153" => 141,
        "46.0" => 137,
        "5.0" => 138,
        "35.49" => 137,
        "35.36" => 115,
        "4.36" => 125,
        "3.120" => 121,
        "46.143" => 137,
        "8.10" => 128,
        "74.0" => 137,
        "6.2" => 156,
        "72.257" => 137,
        "4.260" => 125,
        "3.124" => 137,
        "0.100" => 137,
        "12.2" => 150,
        "12.12" => 130,
        "6.12" => 115,
        "6.0" => 156,
        "12.36" => 115,
        "35.40" => 137,
        "4.40" => 125,
        "8.0" => 137,
        "35.32" => 112,
        "4.32" => 112,
        "3.121" => 121,
        "46.160" => 119,
        "4.30" => 125,
        "30.40" => 125,
        "4.42" => 125
    );
    public static function getOweStatusCode($glsnlStatus){
        $glsnlTrackingStatus = self::$oneworld_glsnl;
        return isset($glsnlTrackingStatus[$glsnlStatus]) ? $glsnlTrackingStatus[$glsnlStatus] : 0;
    }
    public static function getConsignmentStatus($glsnlStatus){
        $consignmentStatusCodes = Tracking::$oneworld_consignment_code_mapping;
        return isset($consignmentStatusCodes[$glsnlStatus]) ? $consignmentStatusCodes[$glsnlStatus] : 0;
    }
}
