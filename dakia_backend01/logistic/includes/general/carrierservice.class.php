<?php

// copied from smart system
////////////////////////////////////////////////////
//
//	Session Management
//
//
////////////////////////////////////////////////////

/**
 * Session Manager
 * @package Ecommerce
 */
define('CarrierService', '');
if(class_exists(CarrierService) === false)
{
interface CarrierService {

    public function validation(Consignment $consignment, Services $service, Country $country);

    public function remoteareas($consignment, $carrierObject, $sender);

    public function label($consignment, $labelType = '', $size = '');

    public function tracking($trackingNumber, $trackBy, $EDI);

    public function sendData($tracking_numbers = array());

    public function manifest($consignment);

    public function preAdvice($consignment);

    public function recycledShipment($consignment);
}
}
?>