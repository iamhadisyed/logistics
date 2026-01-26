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
class CacesaExpressTrackingStatus {
    
    public function __construct() {
        
    }
    
    public static $cacesaexpress_status_code = array(        
        
        "10374" => "CELERITAS- ENTREGADO A AGENTE", //DELIVERED TO AGENT
        "10375" => "DEPARTURE TO DESTINATION COUNTRY",
        "10376" => "ARRIVAL IN THE DESTINATION COUNTRY",
        "10377" => "DEPARTURE FROM SUBSIDIARY",
        "10378" => "ARRIVAL IN THE SERVICE CENTRE LOGISTICS",
        "10379" => "SHIPMENT RECEIVED IN MADRID",
        "10390" => "(SPAIN)MAILED",
        "10391" => "(SPAIN)DEPARTURE FROM BORDER POINT OF ORIGIN COUNTRY",
        "10394" => "RECEIVED IN TRANSIT",
        "10395" => "ITEM DEPARTED TRANSIT",
        "10396" => "ITEM RECEIVED AT DESTINATION OE",
        "10398" => "START CUSTOM CLEAR PROCESS",
        "10399" => "END CUSTOM CLEAR PROCESS(GREENCHANNEL)",
        "10400" => "EN CUSTOM CLEAR PROCESS(REDCHANNEL)",
        "10412" => "AUSENTE",   //absent
        "10413" => "FALTA DE TIEMPO", //LACK OF TIME
        "10414" => "PARTIDA INCOMPLETA", //incomplete 
        "10415" => "FALTA DOCUM.ADUANA/CABILDO", //something related to customs
        "10416" => "DIRECCION INCORRECTA", //incorrect direction
        "10417" => "NO ACEPTA PORTE DEBIDO/REEMBOLSO",  // don't accept
        "10418" => "ENVIO REHUSADO", //REFUSED SHIPPING
        "10419" => "EXTRAVIADO EN GESTION",  //MISSED IN MANAGEMENT
        "10420" => "NO RECIBIDO EN PLAZA DE DESTINO",   //NOT RECEIVED IN 
        "10421" => "ESTACIONADO",  //PARKING
        "10422" => "EXTRAVIADO DEFINITIVO",  //DEFINITIVE EXTRACTED
        "10423" => "ROTURA",  //break
        "10424" => "DISPONIBLE PUNTO DE RECOGIDA/AGENCIA",  //AVAILABLE COLLECTION POINT
        "10425" => "ENVIO ENTREGADO AL DESTINATARIO EN PUDO",  //DELIVERY DELIVERED TO THE RECIPIENT IN PUDO
        "10426" => "DEVOLUCION A ORIGEN",  //RETURN TO ORIGIN
        "10427" => "CADUCADO EN PUDO",  //EXPIRED IN POLE
        "10428" => "DISPONIBLE PARA RECOGER EN DELEGACION",  //AVAILABLE TO COLLECT IN DELEGATION
        "10429" => "DEVOLUCION EN REPARTO",  //DISTRIBUTION IN CAST
        "10430" => "EN REPARTO",  //CASt IN
        "10431" => "ENTRADA EN ALMACEN POR DEVOLUCION",   //ENTRY IN STORE BY RETURN
        "10432" => "LLEGADA AGENCIA ORIGEN",  //ARRIVAL AGENCY ORIGIN
        "10433" => "ENVIO DOCUMENTADO NO RECIBIDO",  //DOCUMENTED SHIPPING NOT RECEIVED
        "10434" => "MAILED",
        "10435" => "ARRIVAL AT BORDER POINT IN THE DESTINATION COUNTRY",
        "10436" => "DEPARTED TRANSIT COUNTRY",
        "10437" => "HANDED TO CUSTOMS",
        "10438" => "POSTAL CUSTOMS CLEARENCE IS COMPLETE",
        "10439" => "ARRIVAL AT ORIGIN BORDER POINT",
        "10440" => "HANDOVER TO DOMESTIC SORTING",
        "10441" => "SORTING",
        "10442" => "ARRIVAL AT DELIVERY OFFICE",
        "10443" => "DISPATCH FROM DELIVERY OFFICE",
        "10444" => "CONSIGNMENT CAN BE COLLECTED",
        "10445" => "DELIVERY ATTEMPT,RECIPIENT IS UNKNOWN",
        "10446" => "DELIVERY ATTEMPT,ADDRESSEE ABSENT",
        "10447" => "THE RECIPIENT HAS REFUSED DELIVERY",
        "10448" => "LATER DELIVERY AS PERRECIPIENTâ€™S INSTRUCTIONS",
        "10449" => "REGISTERED FOR COLLECTION",
        "10450" => "DELIVERY FAILED",
        "10451" => "MISROUTING",
        "10452" => "DELIVERY ATTEMPT",
        "10453" => "DELIVERY ATTEMPT,UNDELIVERABLE CONSIGMENT",
        "10454" => "RETURNED ITEM",
        "10455" => "THE CONSIGNMENT HAS LEFT THE ORIGIN BORDER POINT",
        "10456" => "DEPARTED TRANSIT COUNTRY",
        "10457" => "ARRIVED IN TRANSIT COUNTRY",
        "10458" => "POSTAL CUSTOMS CLEARENCE PROCESS UNDERWAY",
        "10459" => "DELIVERED",
        "10460" => "IMPORT PROCESS CANCELLED IN COUNTRY DESTINATION",
        "10461" => "DELIVERY ATTEMPT,ADDRESS WAS INVALID",            
        "10366" => "THE ITEM IS READY TO BE SHIPPED",
        "10366" => "ELECTRONIC NOTIFICATION RECEIVED",
        "10481" => "ENTREGADO A AGENTE CORREOS ES", //DELIVERED TO AGENT CORREOS IS
        "10480" => "CUSTOMER CONFIRMATION", 
        "10479"	=> "IN RETURNED WAREHOUSE", 
        "10474" =>  "RETURNED",
        "CEL001999" => "EL PEDIDO HA SALIDO DE LAS INSTALACIONES DEL VENDEDOR",  //THE ORDER HAS COME OUT OF THE SELLER'S FACILITIES
        "CEL35" => "ENVÃ?O PENDIENTE DE RECIBIR",  //PENDING SHIPPING TO RECEIVE
        "10374" => "CELERITAS- ENTREGADO A AGENTE", //DELIVERED TO AGENT
        "CEL0"  => "ENVIO ENTREGADO",  //SHIPPING DELIVERED
        "CEL98" => "ENVÃ?O PENDIENTE DE RECIBIR",  //PENDING SHIPPING TO RECEIVE
        "CEL90" => "ENVÃ?O RECIBIDO EN LA CENTRAL DEL TRANSPORTISTA",  //DELIVERY RECEIVED AT THE TRANSPORTATION CENTRAL
        "CEL91" => "PEDIDO EN AGENCIA DE TRANSPORTE", //REQUESTED BY TRANSPORT AGENCY
        "CEL80" => "EN REPARTO",      //Cast in    
        "CEL70" => "DISPONIBLE PARA RECOGER EN AGENCIA DE TRANSPORTE", // Available to pick up from transportation agency",
        "CEL22" => "FALTAN DATOS PARA LA ENTREGA", // DETAILS FOR DELIVERY
        "CEL10" => "NO HEMOS PODIDO REALIZAR LA ENTREGA", //WE HAVE NOT BEEN ABLE TO DELIVER
        "CEL3" => "PEDIENTE DE LLEGAR A LA AGENCIA DE TRANSPORTE" //PENDING TO REACH THE TRANSPORT AGENCY
    );
 
    public static $consignment_status_code = array(
        "10366" => Consignment::STATUS_LABEL_CREATED,
        "10375" => Consignment::STATUS_INTRANSIT,
        "10376" => Consignment::STATUS_INTRANSIT,
        "10377" => Consignment::STATUS_INTRANSIT,
        "10378" => Consignment::STATUS_INTRANSIT,
        "10379" => Consignment::STATUS_INTRANSIT,
        "10390" => Consignment::STATUS_INTRANSIT,
        "10391" => Consignment::STATUS_INTRANSIT,
        "10394" => Consignment::STATUS_INTRANSIT,
        "10395" => Consignment::STATUS_INTRANSIT,
        "10396" => Consignment::STATUS_INTRANSIT,
        "10398" => Consignment::STATUS_INTRANSIT,
        "10399" => Consignment::STATUS_INTRANSIT,
        "10400" => Consignment::STATUS_INTRANSIT,
        "10412" => Consignment::STATUS_INTRANSIT,
        "10413" => Consignment::STATUS_PROBLEM,
        "10414" => Consignment::STATUS_PROBLEM,
        "10415" => Consignment::STATUS_PROBLEM,
        "10416" => Consignment::STATUS_PROBLEM,
        "10417" => Consignment::STATUS_PROBLEM,
        "10418" => Consignment::STATUS_PROBLEM,
        "10419" => Consignment::STATUS_PROBLEM,
        "10420" => Consignment::STATUS_PROBLEM,
        "10421" => Consignment::STATUS_INTRANSIT,
        "10422" => Consignment::STATUS_INTRANSIT,
        "10423" => Consignment::STATUS_INTRANSIT,
        "10424" => Consignment::STATUS_INTRANSIT,
        "10425" => Consignment::STATUS_INTRANSIT,
        "10426" => Consignment::STATUS_RETURNED,
        "10427" => Consignment::STATUS_PROBLEM,
        "10428" => Consignment::STATUS_INTRANSIT,
        "10429" => Consignment::STATUS_INTRANSIT,
        "10430" => Consignment::STATUS_INTRANSIT,
        "10431" => Consignment::STATUS_INTRANSIT,
        "10432" => Consignment::STATUS_INTRANSIT,
        "10433" => Consignment::STATUS_PROBLEM,
        "10434" => Consignment::STATUS_INTRANSIT,
        "10435" => Consignment::STATUS_INTRANSIT,
        "10436" => Consignment::STATUS_INTRANSIT,
        "10437" => Consignment::STATUS_INTRANSIT,
        "10438" => Consignment::STATUS_INTRANSIT,
        "10439" => Consignment::STATUS_INTRANSIT,
        "10440" => Consignment::STATUS_INTRANSIT,
        "10441" => Consignment::STATUS_INTRANSIT,
        "10442" => Consignment::STATUS_INTRANSIT,
        "10443" => Consignment::STATUS_INTRANSIT,
        "10444" => Consignment::STATUS_INTRANSIT,
        "10445" => Consignment::STATUS_DELIVERED,
        "10446" => Consignment::STATUS_DELIVERED,
        "10447" => Consignment::STATUS_PROBLEM,
        "10448" => Consignment::STATUS_INTRANSIT,
        "10449" => Consignment::STATUS_INTRANSIT,
        "10450" => Consignment::STATUS_PROBLEM,
        "10451" => Consignment::STATUS_PROBLEM,
        "10452" => Consignment::STATUS_DELIVERED,
        "10453" => Consignment::STATUS_DELIVERED,
        "10454" => Consignment::STATUS_RETURNED,
        "10455" => Consignment::STATUS_INTRANSIT,
        "10456" => Consignment::STATUS_INTRANSIT,
        "10457" => Consignment::STATUS_INTRANSIT,
        "10458" => Consignment::STATUS_INTRANSIT,
        "10459" => Consignment::STATUS_DELIVERED,
        "10460" => Consignment::STATUS_PROBLEM,
        "10461" => Consignment::STATUS_PROBLEM,
        "10366" => Consignment::STATUS_LABEL_CREATED,
        "10366" => Consignment::STATUS_LABEL_CREATED,
        "10480" => Consignment::STATUS_INTRANSIT,
        "10479"	=> Consignment::STATUS_INTRANSIT,
        "10474" => Consignment::STATUS_RETURNED,
        //"10379" => Consignment::STATUS_INTRANSIT,
        "10481" => Consignment::STATUS_INTRANSIT,
        "CEL001999" => Consignment::STATUS_INTRANSIT,
        "CEL35" => Consignment::STATUS_INTRANSIT,
        "10374" => Consignment::STATUS_INTRANSIT,
        "CEL0" => Consignment::STATUS_DELIVERED,
        "CEL98" => Consignment::STATUS_INTRANSIT,
        "CEL90" => Consignment::STATUS_INTRANSIT,
        "CEL91" => Consignment::STATUS_INTRANSIT,
        "CEL80" => Consignment::STATUS_INTRANSIT,
        "CEL70" => Consignment::STATUS_INTRANSIT, // Available to pick up from transportation agency",
        "CEL22" => Consignment::STATUS_INTRANSIT, // DETAILS FOR DELIVERY
        "CEL10" => Consignment::STATUS_NOT_DELIVERED, //WE HAVE NOT BEEN ABLE TO DELIVER
        "CEL3" => Consignment::STATUS_INTRANSIT //PENDING TO REACH THE TRANSPORT AGENCY
    );

    public static $oneworld_cacesaexpress = array(
        "10374" => 137,
        "10375" => 145,
        "10376" => 146,
        "10377" => 145,
        "10378" => 146,
        "10379" => 148,
        "10390" => 137,
        "10391" => 145,
        "10394" => 137,
        "10395" => 137,
        "10396" => 137,
        "10398" => 117,
        "10399" => 117,
        "10400" => 117,
        "10412" => 115,
        "10413" => 115,
        "10414" => 115,
        "10415" => 117,
        "10416" => 115,
        "10417" => 115,
        "10418" => 115,
        "10419" => 115,
        "10420" => 115,
        "10421" => 137,
        "10422" => 137,
        "10423" => 137,
        "10424" => 114,
        "10425" => 137,
        "10426" => 138,
        "10427" => 115,
        "10428" => 137,
        "10429" => 137,
        "10430" => 137,
        "10431" => 137,
        "10432" => 146,
        "10433" => 115,
        "10434" => 137,
        "10435" => 146,
        "10436" => 145,
        "10437" => 117,
        "10438" => 117,
        "10439" => 146,
        "10440" => 137,
        "10441" => 137,
        "10442" => 146,
        "10443" => 137,
        "10444" => 137,
        "10445" => 123,
        "10446" => 123,
        "10447" => 137,
        "10448" => 137,
        "10449" => 114,
        "10450" => 125,
        "10451" => 115,
        "10452" => 123,
        "10453" => 123,
        "10454" => 143,
        "10455" => 137,
        "10456" => 145,
        "10457" => 146,
        "10458" => 117,
        "10459" => 121,
        "10460" => 115,
        "10461" => 123,
        "10366" => 137,
        "10366" => 144,
        "10480" => 137, 
        "10479"	=> 137, 
        "10474" => 138,
       // "10379" => 137,
        "10481" => 137,
        "CEL001999" => 137,
        "CEL35" => 137,
        "10374" => 137,
        "CEL0" => 121,
        "CEL98" => 137,
        "CEL90" => 137,
        "CEL91" => 111,
        "CEL80" => 137,
        "CEL70" => 137, // Available to pick up from transportation agency",
           "CEL22" => 137, // DETAILS FOR DELIVERY
           "CEL10" => 125, //WE HAVE NOT BEEN ABLE TO DELIVER
           "CEL3" => 124 //PENDING TO REACH THE TRANSPORT AGENCY
        
    );
    public static function getOweStatusCode($cacesaexpressStatus){
        $cacesaexpressTrackingStatus = self::$oneworld_cacesaexpress;
        return isset($cacesaexpressTrackingStatus[$cacesaexpressStatus]) ? $cacesaexpressTrackingStatus[$cacesaexpressStatus] : 0;
    }
    public static function getConsignmentStatus($cacesaexpressStatus){
        $consignmentStatusCodes = Tracking::$oneworld_consignment_code_mapping;
        return isset($consignmentStatusCodes[$cacesaexpressStatus]) ? $consignmentStatusCodes[$cacesaexpressStatus] : 0;
    }
}
