<?php
require_once(__DIR__ . "/../includes/settings/config.inc.php");

include_classes([
    'services.class',
    'servicesfilter.class',
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'parcel.class',
    'parcelfilter.class',
    'country.class',
    'countryfilter.class',
    'notfoundrecord.class',
    'notfoundrecordfilter.class',
]);


$date =  strtotime(date("Y-m-d")); //


$consignmentFilter = new ConsignmentFilter();
$consignmentFilter->addFilter("     u.id in (select id from user where user_account_id in (select id from user_account where parentid = 2319))", "userfilter");
//$consignmentFilter->addFilter("     s.id = '310'", "servicefilter");
$consignmentFilter->addFilter("     c.date_label_created >= '". $date ."'", "consignmentfilter");
//$consignmentFilter->addFilter("     c.awb in ('3375304011365182')", "consignmentfilter");
$consignmentFilter->addFilter("    c.shipment_status not in ('" . Consignment::STATUS_RECYCLED . "','" . Consignment::STATUS_READY_TO_PRINT . "','" . Consignment::STATUS_INVALID . "')", "consignmentfilter");
$consignmentList = $consignmentFilter->getColumnList("c.hawb, c.awb, c.reference, ua.user_account, s.code as service_code, c.contact, c.currency, c.value, con.iso as country_iso_code, c.postcode, c.sender_name, c.sender_company, "
        . "c.notes, c.city, c.address_line_1, c.address_line_2, c.address_line_3, c.telephone, c.weight,c.vol_weight,c.number_pieces, c.description, c.mawb, c.sender_address_line_1, c.sender_address_line_2, c.sender_address_line_3,"
        . "c.sender_email, c.sender_telephone, c.sender_city, c.sender_postcode, c.sender_country_id,c.other_routing_code, pc.length, pc.height, pc.width, c.customized_service_id", 1000);


$xml .= '<?xml version="1.0" encoding="iso-8859-1"?> <WQXS-manifest>';
$arr = array();
echo count($consignmentList) . "<br />";
if (count($consignmentList) > 0) {
    foreach ($consignmentList as $consignResult) {
        array_push($arr, $consignResult->getId());
        if($consignResult->getCustomizedServiceId() > 0){
            $customizedServiceObj = new Services($consignResult->getCustomizedServiceId());
            $customizeservice = $customizedServiceObj->getCode() ;
        }
        $service = $consignResult->getServiceCode();
        if($customizeservice == "STVIVSPEC"){
            $service = "CEAD30";
        }else if ($service == 'STYDL3HPA') {
            $service = "CEAD26";
        } elseif ($service == 'STYDL01CN') {
            $service = "CEAD24";
        } elseif ($service == 'STYDL003H') {
            $service = "CEAD25";
        } elseif ($service == 'STYDL001H' || $service == 'STYDL01VP') {
            $service = "CEAD23";
        } elseif ($service == 'STYDL01HS') {
            $service = "CEAD28";
        } elseif ($service == 'STUKM0012') {
            $service = "CEAD27";
        }elseif ($service == 'STYDL2CXN') {
            $service = "CEAD31";
        }elseif ($service == 'STVIVSPEC') {
            $service = "CEAD30";
        }elseif ($service == 'STYDL02CP') {
            $service = "CEAD32";
        }elseif ($service == 'STYDL02VP' ) {
            $service = "CEAD34";
        }elseif ($service == 'STYDL01CP') {
            $service = "CEAD35";
        }elseif ($service == 'STRYM0TP2') {
            $service = "CEAD36";
        }elseif ($service == 'STRYMTP2S') {
            $service = "CEAD37";
        }             
        elseif ($service == 'SPDHLEXHW' || $service == 'STHWVIVAD' || $service == 'STHWVIVAN') {
            $service = "CEAD20";
        }   
        elseif($service == 'STVEEAD00' || $service == 'STVIVECOM' || $service == 'STVIVAEXP'){
            $service = "EEAD00";
        }
        elseif($service == 'STVEEAD03'){
            $service = "EEAD03";
        }
        elseif($service == 'STVEEAD04'){
            $service = "EEAD04";
        }
        elseif($service == 'STVEEAD02'){
            $service = "EEAD02";
        }
        elseif($service == 'STVEEAD05'){
            $service = "EEAD05";
        }
        elseif($service == 'STVEEAD01'){
            $service = "EEAD01";
        }
        elseif($service == 'STVEEAD00'){
            $service = "EEAD00";
        }
        elseif($service == 'STECOMDDU'){
            $service = "CEAD48";
        }
        elseif($service == 'STECOMDDP'){
            $service = "CEAD49";
        }
        else
        {
            $service = "CEAD30";
        }


        if ($consignResult->getSenderCompany() != '')
            $senderCompany = $consignResult->getSenderCompany();
        else
            $senderCompany = $consignResult->getSenderName();
        $senderCountry = new Country($consignResult->getSenderCountryId());

        $xml .= '
	<Shipment>
		<HAWB>' . cleanCsvCall(trim($consignResult->getHawb())) . '</HAWB>
		<HawbId></HawbId>
		<ExportDate>' . date('d-m-Y') . '</ExportDate>
		<CustomerReference>' . cleanCsvCall(trim($consignResult->getReference())) . '</CustomerReference>';
        if($consignResult->getServiceCode() == 'STVIVAEXP'){
            $xml .= '<AgentHawb></AgentHawb>';
        }
        else
        {
            $xml .= '<AgentHawb>' . cleanCsvCall(trim($consignResult->getAwb())) . '</AgentHawb>';
        }
        $xml .= '
		<ConsigneeName>' . cleanCsvCall(trim($consignResult->getContact())) . '</ConsigneeName>
		<ConsigneeCountry>' . cleanCsvCall(trim($consignResult->getCountryIsoCode())) . '</ConsigneeCountry>
		<ConsigneeZipCode>' . cleanCsvCall(trim($consignResult->getPostCode())) . '</ConsigneeZipCode>
		<ConsigneeTown>' . cleanCsvCall(trim($consignResult->getCity())) . '</ConsigneeTown>
		<ConsigneeAddress1>' . cleanCsvCall(trim($consignResult->getAddressLine1() )). '</ConsigneeAddress1>
		<ConsigneeAddress2>' . cleanCsvCall(trim($consignResult->getAddressLine2())) . '</ConsigneeAddress2>
		<ConsigneeAddress3>' . cleanCsvCall(trim($consignResult->getAddressLine3())) . '</ConsigneeAddress3>
		<ConsigneeContact>' . cleanCsvCall(trim($consignResult->getContact())) . '</ConsigneeContact>
		<ConsigneePhone>' . cleanCsvCall(trim($consignResult->getTelephone())) . '</ConsigneePhone>
		<ShipperName>' . cleanCsvCall(trim($senderCompany)) . '</ShipperName>
		<ShipperCountry>' . cleanCsvCall(trim($senderCountry->getIso())) . '</ShipperCountry>
		<ShipperZipCode>' . cleanCsvCall(trim($consignResult->getSenderPostCode())) . '</ShipperZipCode>
		<ShipperTown></ShipperTown>
		<ShipperAddress1>' . cleanCsvCall(trim($consignResult->getSenderAddressLine1())) . '</ShipperAddress1>
		<ShipperAddress2>' . cleanCsvCall(trim($consignResult->getSenderAddressLine1())) . '</ShipperAddress2>
		<ShipperAddress3>' . cleanCsvCall(trim($consignResult->getSenderAddressLine1())) . '</ShipperAddress3>
		<ShipperEmail>' . cleanCsvCall(trim($consignResult->getSenderEmail())) . '</ShipperEmail>
		<ShipperEmailTracking>1</ShipperEmailTracking>
		<Customer>' . cleanCsvCall(trim($consignResult->getUserAccount())) . '</Customer>
		<Agent></Agent>
		<Weight>' . cleanCsvCall(trim($consignResult->getWeight())) . '</Weight>
		<VolumetricWeight>' . cleanCsvCall(trim($consignResult->getVolWeight())) . '</VolumetricWeight>
		<Pieces>' . cleanCsvCall(trim($consignResult->getNumberPieces())) . '</Pieces>
		<Distance></Distance>
                <CollOrder>
                <CollItem>
                        <X>' . $consignResult->getLength() . '</X>
                        <Y>' . $consignResult->getWidth() . '</Y>
                        <Z>' . $consignResult->getHeight() . '</Z>
                </CollItem>
                </CollOrder>
		<ShipmentType>N</ShipmentType>
		<Service>' . $service . '</Service>
		<Description>' . cleanCsvCall(trim($consignResult->getDescription())) . '</Description>
		<SpecialInstruction>' . cleanCsvCall(trim($consignResult->getNotes())) . '</SpecialInstruction>
		<ShipmentReturn></ShipmentReturn>
		<Value>' . cleanCsvCall(trim($consignResult->getValue())) . '</Value>
		<ValueCurrency>' . cleanCsvCall(trim($consignResult->getCurrency())) . '</ValueCurrency>
		<CollectCharge></CollectCharge>
		<CollectChargeCurrency></CollectChargeCurrency>
		<SpecialService></SpecialService>
		<MAWB>' . $consignResult->getMawb() . '</MAWB>
		<Flight></Flight>
		<Manifest>0</Manifest>
		<PickupDate></PickupDate>
		<PickupTime></PickupTime>
		<DeliveryNote1></DeliveryNote1>
		<DeliveryNote2></DeliveryNote2>
		<DeliveryNote3></DeliveryNote3>
		<DeliveryNote4></DeliveryNote4>
		<DeliveryNote5></DeliveryNote5>
		<PickupNote1></PickupNote1>
		<PickupNote2></PickupNote2>
		<PickupNote3></PickupNote3>
		<PickupNote4></PickupNote4>
		<PickupNote5></PickupNote5>
		<Depot>67</Depot>
		<Gateway>MLA</Gateway>
		<Station></Station>
		<SiteFrom>OW</SiteFrom>
		<SiteTo>67</SiteTo>
	</Shipment>';
    }
    $xml .= '</WQXS-manifest>';




    if ($xml != '') {

        $path = SETTING_DIR_ASSETS . "data_send/viva_booking/" . date("Y_m_d") . "/";
        if (!file_exists($path))
            @mkdir($path, 0777, true);

        $filename = "viva" . date("Y_m_d_H_i_s") . ".xml";
        $file_path = $path . $filename;
        chmod($path, 0777);
        $file_handle = fopen($file_path, 'w');
        fwrite($file_handle, $xml);
        fclose($file_handle);

        echo $remote_file_path = "./ShipmentsIn/" . $filename;
        $ftp_conn = ftp_connect('213.246.110.102') or die("Could not connect to $SETTING_FTP_SITE_YODEL");
        $login = ftp_login($ftp_conn, 'viva_dev1', '2,y-]~ba>!~4*gH%');
        ftp_pasv($ftp_conn, true) or die("Unable switch to passive mode");
        // upload file
        $isYodelUpload = ftp_put($ftp_conn, $remote_file_path, $file_path, FTP_ASCII);
        if ($isYodelUpload) {
            echo "Successfully uploaded file.";
        } else {
            $message = 'There is an error while uploading the file to Yodel FTP.';
        }
        ftp_close($ftp_conn);
        die;
    }
}
?>