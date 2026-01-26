<?php
for ($i = 1; $i <= 100; $i++) {
    $curl = curl_init();
    $orderRef = 'API_Hadi_'.rand(0,99999).'_'.$i;
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://staging.smarttrack.co/api/v2/generate-label',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array('sender_country_iso' => 'GB', 'service_code' => 'STOWESECO', 'order_reference' => $orderRef, 'sender_email' => 'hadi@stellartech.co', 'sender_company' => 'ASD Tech', 'sender_contact' => 'Hadi hussain naqvi', 'sender_address_line_1' => 'xx defence', 'sender_address_line_2' => 'xx defence', 'sender_address_line_3' => 'Pump Ln Hayes', 'sender_city' => 'London', 'sender_state' => 'London', 'sender_postcode' => 'UB3 3NB', 'sender_telephone' => '3214518055', 'receiver_country_iso' => 'US', 'receiver_contact' => 'hadi final', 'receiver_email' => 'hadi@yahoo.com', 'receiver_company' => 'ASD Tech', 'receiver_telephone' => '3214518055', 'receiver_address_line_1' => 'xx defence', 'receiver_address_line_2' => '', 'receiver_address_line_3' => '', 'receiver_city' => 'Anchorage', 'receiver_state' => 'NewHampshire', 'receiver_postcode' => '03063', 'value' => '1', 'currency' => 'GBP', 'notes' => '', 'description' => 'api generated', 'parcel[0][weight]' => '1', 'parcel[0][length]' => '1', 'parcel[0][width]' => '1', 'parcel[0][height]' => '1', 'shipment_type' => 'DO', 'parcel[0][tracking_number]' => '', 'parcel[0][itemvalue]' => '2', 'parcel[0][items][0][item_description]' => 'books', 'parcel[0][items][0][item_sku]' => 'S2W1622469149', 'parcel[0][items][0][item_url]' => 'http://owe.stellartech.co', 'parcel[0][items][0][no_of_items]' => '1', 'parcel[0][items][0][item_value]' => '1', 'parcel[0][items][0][weight]' => '1', 'parcel[0][items][0][tariff_no]' => '', 'parcel[0][items][0][hscode]' => '', 'parcel[0][items][0][manufacture_country_iso]' => 'GB', 'label_type' => 'zpl', 'label_size' => '100x150'),
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer 87cfa037bf16b0e7ddf682a5fc0da157b73d027a',
            'Cookie: gl__session=bat09bv58ud4b8rsn00jm3dpn6'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    // Always set content-type when sending HTML email
//    $headers = "MIME-Version: 1.0" . "\r\n";
//    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
//
//// More headers
//    $headers .= 'From: <ops@oneworldexpress.com>' . "\r\n";
////    $headers .= 'Cc: myboss@example.com' . "\r\n";
//
//    mail("iamhadisyed@gmail.com","test_api",$response,$headers);
//    sleep(1);
    //echo $response;
}