<?php
require_once(__DIR__ . "/../includes/settings/config.inc.php");

include_classes([
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'parcel.class',
    'parcelfilter.class',
    'country.class',
    'countryfilter.class',
    'invoices.class',
    'invoicesfilter.class',
    'invoices.class',
    'invoicesfilter.class',
    'licenceplate.class',
    'licenceplatefilter.class',
    'notfoundrecord.class',
    'notfoundrecordfilter.class',
]);
$date = date("Y-m-d");

echo $sql = "SELECT 
   c.id, c.hawb, c.awb, c.weight, c.notes, c.other_routing_code, c.number_pieces, c.vol_weight, c.vol_demonimator
FROM
    consignment c
        INNER JOIN
    consignment_relabel cr ON c.id = cr.consignment_id
        AND c.user_id IN (SELECT 
            id
        FROM
            user
        WHERE
            user_account_id IN( select id from user_account where parentid = '2319'))
        AND cr.date_created >= '".$date."'";
$consignmentList = Consignment::getConsignmentListFromSql($sql);

$xml .= '<?xml version="1.0" encoding="iso-8859-1"?> <WQXS-manifest>';
$arr = array();

if (count($consignmentList) > 0) {
    foreach ($consignmentList as $consignResult) {

        $service = $consignResult->getOtherRoutingcode();
        
        if ($consignResult->getSenderCompany() != '')
            $senderCompany = $consignResult->getSenderCompany();
        else
            $senderCompany = $consignResult->getSenderName();
        $senderCountry = new Country($consignResult->getSenderCountryId());
        $parcel = new ParcelFilter();
        $parcel->addFieldFilter("consignment_id", $consignResult->getId());
        $parcelList = $parcel->getList();

        $xml .= '
	<Shipment>
		<HAWB>' . $consignResult->getHawb() . '</HAWB>
		<HawbId>' . $consignResult->getAwb() . '</HawbId>
		<AgentHawb>' . $consignResult->getAwb() . '</AgentHawb>
		<Weight>' . $consignResult->getWeight() . '</Weight>
        <VolumetricWeight>' . $consignResult->getVolWeight() . '</VolumetricWeight>
		<Denominator>'.$consignResult->getVolDemonimator().'</Denominator>		
		<Pieces>' . $consignResult->getNumberPieces() . '</Pieces>
                <CollOrder>
                <CollItem>
                        <X>' . $parcelList[0]->getLength() . '</X>
                        <Y>' . $parcelList[0]->getWidth() . '</Y>
                        <Z>' . $parcelList[0]->getHeight() . '</Z>
                </CollItem>
                </CollOrder>
	</Shipment>';
    }
    $xml .= '</WQXS-manifest>';




    if ($xml != '') {

        $path = SETTING_DIR_ASSETS . "data_send/viva_booking/" . date("Y_m_d") . "/";
        if (!file_exists($path))
            @mkdir($path, 0777, true);
        
        $filename = "st_relabel_" . date("Y_m_d_H_i_s") . ".xml";
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