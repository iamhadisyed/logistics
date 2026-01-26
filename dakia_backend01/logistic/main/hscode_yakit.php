<?php

ini_set('display_errors', 1);
echo "<pre>";
$sku_array = array (array( "0.056", "GBP", "2.56", "Handmade beads", "1", "1", "0.1"),
 array( "0.008", "GBP", "1.71", "Eartips Silicone in", "1", "1", "0.1"),
 array( "0.011", "GBP", "1.84", "Mini Pineapple Pack", "1", "1", "0.1"),
 array( "0.026", "GBP", "0.39", "PVC chalk", "1", "1", "1"),
 array( "0.028", "GBP", "0.39", "flower mould", "1", "1", "1"),
 array( "0.027", "GBP", "0.39", "cockroaches", "1", "1", "1"),
 array( "0.016", "GBP", "2.34", "bead(10mm Star-PDZ 03)", "1.5", "1.5", "1.5"),
 array( "0.035", "GBP", "0.39", "Silicone anti crease paste", "1", "1", "1"),
 array( "0.027", "GBP", "2.35", "Bead chain_1", "0.5", "0.5", "0.5"),
 array( "0.087", "GBP", "4.23", "EAMX BLheli Brushless ESC", "1.2", "1.2", "0.1"),
 array( "0.112", "GBP", "2.12", "Washer Belt #1", "0.1", "0.1", "0.1"),
 array( "0.235", "GBP", "3.14", "tablet cover(007-pd10-gjzj-bk+sxb)", "1.5", "1.5", "1.5"),
 array( "0.022", "GBP", "2.2", "DIY Cutting Dies(DE0440)", "1.5", "1.5", "1.5"),
 array( "0.048", "GBP", "1.56", "blb2237cdx1", "1", "0.8", "0.2"),
 array( "0.196", "GBP", "7.84", "T-Shirt(CT-ST176-6-M)", "1.5", "1.5", "1.5"),
 array( "0.172", "GBP", "7.84", "T-Shirt(BC-LX145-4-L)", "1.5", "1.5", "1.5"),
 array( "0.031", "GBP", "1.57", "Strap (IWT2-10Dark Blue-42/44)", "1.5", "1.5", "1.5"),
 array( "0.026", "GBP", "0.78", "hsz2hgBblk", "0.2", "0.2", "0.2"),
 array( "0.076", "GBP", "3.92", "t-shirt,akey000027-45x45cm-Q01", "0.3", "0.5", "0.3"),
 array( "0.018", "GBP", "2.35", "Acrylic decoration(D31-10-C01)", "1.5", "1.5", "1.5"),
 array( "0.035", "GBP", "1.71", "SPDT micro switch", "0.3", "0.2", "0.16"),
 array( "0.023", "GBP", "1.44", "A clamp", "0.63", "0.13", "0.15"),
 array( "0.016", "GBP", "4.04", "Car Interior Dome Light Switch", "0.3", "0.2", "0.2"),
 array( "0.053", "GBP", "4.31", "NGFF to SATA Adapter Card", "1.7", "0.8", "0.1"),
 array( "0.041", "GBP", "5.25", "USB-C male to male extension cable", "1.1", "0.9", "0.2"),
 array( "0.03", "GBP", "2.6", "Automotive LED fog lights", "0.85", "0.4", "0.25"),
 array( "0.02", "GBP", "0.82", "Positioning tube", "0.57", "0.5", "0.1"),
 array( "0.03", "GBP", "1.57", "DIY electric,E887", "0.3", "0.5", "0.3"),
 array( "0.025", "GBP", "1.57", "Eyelashes Fake", "1.2", "0.7", "0.2"),
 array( "0.021", "GBP", "1.94", "natural quartz necklace", "1.3", "1.1", "0.1"),
 array( "0.03", "GBP", "3.25", "Fishing line straightener", "0.6", "0.6", "0.1"),
 array( "0.034", "GBP", "0.98", "pink feathers handmade dreamcatcher craft dre", "4", "1.1", "0.1"),
 array( "0.067", "GBP", "3.06", "Fish diamond painting", "0.2", "0.2", "0.2"),
 array( "0.03", "GBP", "0.75", "silver bead dream catcher with feather wall h", "1.9", "1.6", "0.2"),
 array( "0.178", "GBP", "7.84", "T-Shirt(BC-ST353-5-L)", "1.5", "1.5", "1.5"),
 array( "0.173", "GBP", "7.84", "T-Shirt(BC-SH231-1-L)", "1.5", "1.5", "1.5"),
 array( "0.218", "GBP", "3.14", "tablet cover(003-a.TB-X705F-gjzj-bk+sxb)", "1.5", "1.5", "1.5"),
 array( "0.174", "GBP", "7.84", "T-Shirt(BC-LX884-4-L)", "1.5", "1.5", "1.5"),
 array( "0.172", "GBP", "7.84", "T-Shirt(BC-ST1037-2-L)", "1.5", "1.5", "1.5"),
 array( "0.179", "GBP", "7.84", "T-Shirt(BC-ST1132-4-XL)", "1.5", "1.5", "1.5"),
 array( "0.045", "GBP", "2.36", "Acrylic decoration(C31-200-C05)", "1.5", "1.5", "1.5"),
 array( "0.042", "GBP", "3.06", "Bag Carrying Clip", "1", "1.5", "1"),
 array( "0.062", "GBP", "1.57", "pink heart phone sticker(fr0000059_5)", "1.5", "1.5", "1.5"),
 array( "0.038", "GBP", "1.65", "hematite bracelet", "1.3", "1.1", "0.1"),
 array( "0.015", "GBP", "3.5", "crown ring", "1.3", "1.1", "0.1"),
 array( "0.014", "GBP", "1.57", "Acne tweezers", "1", "0.01", "0.01"),
 array( "0.019", "GBP", "4.42", "control knobs for bmw", "0.6", "0.5", "0.2"),
 array( "0.034", "GBP", "0.78", "Nylon rod", "1", "1", "0.5"),
 array( "0.012", "GBP", "0.82", "Alice gui tar picks", "0.4", "0.35", "0.02"),
 array( "0.287", "GBP", "3.14", "tablet cover(003-pd10-kp-szch-dyME+sxb)", "1.5", "1.5", "1.5"));



foreach ($sku_array as $skunumber) {
    $time_start = microtime(true);
   
  //  $homepage = file_get_contents('https://www.ebay.com/itm/' . $skunumber);
//    $findme = '<meta name="twitter:title" content="';
//    $second = '<meta Property="og:title" Content=';

//    if (strpos($homepage, $findme) !== false) 
    {
//        $pos1 = strpos($homepage, $findme);
//        $pos2 = strpos($homepage, $second);

//        $data = "";
//        $result = $pos2 - $pos1;
//        $last_result = substr($homepage, $pos1, $result - 4);
//        $last_result = str_replace('<meta name="twitter:title" content="', "", $last_result);
//        $last_result = str_replace('&apos;&apos;', "", $last_result);
//        $description1 = $last_result;
//        $description = $description1 . $data;
//        $description = substr($description, 0, 255);
//        $value = "2";
$description = $skunumber[3];
        $data = '{
				"currencyUnit":'.$skunumber[1].',
				"countryList":["GB"],
				"shipmentWeight":'.$skunumber[0].',
				"shipmentWeightUnit":"kg",
				"length":'.$skunumber[4].',
				"width":'.$skunumber[5].',
				"height":'.$skunumber[6].',
				"dimUnit":"cm",
				"items":[
                                    {
                                    "productId":"123-1234A",
                                    "displayName":"'.$skunumber[3].'",
                                    "description":"'.$skunumber[3].'",
                                    "countryOfOrigin":"CN",
                                    "itemWeight":'.$skunumber[0].',
                                    "itemWeightUnit":"kg",
                                    "value": '.$skunumber[2].',
                                    "quantity":1
                                    }
				]
				}';

        $data_string = $data;
        $ch = curl_init('https://shipping.yakit.com/api/yrate/shipmentQuote');

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Basic c2hhYmJpckBvbmV3b3JsZGV4cHJlc3MuY29tOjQ0NmM0ZWYwLTAwOTctNDA3MC1hNjhjLThiNmMyNWE4YzNhMA==',
            'Cache-Control: no-cache',
            'Version: 2',
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string))
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

        //execute post
        $results = curl_exec($ch);

        //	print_r($results );
        //close connection
        curl_close($ch);

        if (@$err) {
            return $err;
        } else {
            $response = json_decode($results);
            if (!empty($response)) {
                $shipment_id = $response->ttdList[0]->shipment;
            }
        }
    }
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://shipping.yakit.com/api/yrate/getClassifications?shipmentId=" . $shipment_id,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "Authorization: Basic c2hhYmJpckBvbmV3b3JsZGV4cHJlc3MuY29tOjQ0NmM0ZWYwLTAwOTctNDA3MC1hNjhjLThiNmMyNWE4YzNhMA==",
            "Cache-Control: no-cache",
            "Version: 2"),
    ));

    $results = curl_exec($curl);
    //print_r(  $results );
    $err = curl_error($curl);
    curl_close($curl);
    $result1 = json_decode($results);
    $hsCode = $result1->classifications[0]->hsCode;
    $confidence = $result1->classifications[0]->confidence;
    
    echo "<pre>";
    print_r($result1);
    
    $time_end = microtime(true);
   $time = $time_end - $time_start;
   
    echo number_format($time,2). ", ".$hsCode . ", ";
    echo str_replace(",", "", $description) . ",";
    echo $confidence . "\r\n";
    die;
}
?>