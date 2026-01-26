<?php

require_once("../includes/settings/config.inc.php");
error_reporting(E_ALL);
ini_set('display_errors', '0');

require_once("../includes/settings/config.inc.php");
//error_reporting(E_ALL);
//ini_set('display_errors', 'On');



require_once("../includes/settings/config.inc.php");
//error_reporting(E_ALL);
//ini_set('display_errors', 'On');

include_classes([
    'carrierservice.class'
    ], 'general');
include_classes([
    'tourline.class', 'tourlinetrackingstatus.class','anpost.class','brtitaly.class',
    'yodel.class', 'yodeltrackingstatus.class', 'royalmail.class', 'royalmailtrackingstatus.class','hermes1.class',
    'yodel.class', 'yodeltrackingstatus.class', 'cttexpress.class',
    'huxloehermes.class', 'kaab.class', 'kabbtrackingstatus.class','asendiauk.class', 'asendiauktrackingstatus.class', 'ups.class',
    'kronosexpress.class', 'kronosexpresstrackingstatus.class', 'deutschepost.class', 'deutscheposttrackingstatus.class', 'viva.class',
    'vivatrackingstatus.class', 'parcelforyou.class','parcelforyoutrackingstatus.class', 'dhl.class','dhltrackingstatus.class','wmsfbo.class'
    ], 'labels');
include_classes([    
    'iaddress.class',    
    'sku.class',
    'consignment.class',
    'parcel.class',
    'parcelfilter.class',
    'trackingdata.class',
    'tracking.class',
    'trackingdatafilter.class',
    'consignmentfilter.class', 
    'consignmentrelabelfilter.class',
    'consignmentrelabel.class', 
    'countryfilter.class',
    'country.class',
    'serviceagentmappingfilter.class',
    'serviceagentmapping.class',
    'services.class',
    'carrier.class','servicecountrytimefilter.class', 'servicecountrytime.class', 'warehouse.class', 'marketplaceorder.class',
    'marketplaceorderfilter.class', 'marketplaceorderdetails.class', 'marketplaceorderdetailsfilter.class', 'marketplaces.class',
    'usermarketplacesmapping.class',
    'usermarketplacesmappingfilter.class'
    ]);

    set_time_limit(-1);




    $GLOBALS['url'] = "https://www.smarttrack.co/api/";

    CreateLabelsForMarketPlaces();
  
    /////////////// This function creates label for Brands2you Amazon Marketplace on the basis of SKU /////////////////////////////////
    
    function CreateLabelsForMarketPlaces()
    {
        $marketPlaceOrderFilter = new MarketPlaceOrderFilter();
        $marketPlaceOrderFilter->addFilter("    (consignment_id is null or consignment_id = 0)  and order_status = 'Unshipped' and marketplace_id = 1 and user_id = 4778");
        $marketPlaceOrderResult = $marketPlaceOrderFilter->getList();

        echo "<br> Total Amazon Orders : " . count($marketPlaceOrderResult) . "<br>";

        foreach($marketPlaceOrderResult as $marketPlaceOrder)
        {
            GetLabel($marketPlaceOrder);
        }
    }


    function GetLabel($marketPlaceOrder)
    {
        //print_r($marketPlaceOrder);
        $marketPlaceOrderDetailFilter = new MarketPlaceOrderDetailsFilter();
        $marketPlaceOrderDetailFilter->addFieldFilter("marketplace_order_id", $marketPlaceOrder->getId());
        $marketPlaceOrderDetailFilterResult = $marketPlaceOrderDetailFilter->getList();

        if(count($marketPlaceOrderDetailFilterResult) > 0)
        {
            $curl = curl_init();

            $marketPlaceOrderDetail = $marketPlaceOrderDetailFilterResult[0];
            $skuName =  trim($marketPlaceOrderDetail->getSku());
            $service = "";

            $skuFilter = new SkuFilter();
            $skuFilter->addFilter("sku = '" . $marketPlaceOrderDetail->getSku() . "'");
            $skuFilterResult = $skuFilter->getList();



            if(count($skuFilterResult) > 0)
            {

                $skuObj = $skuFilterResult[0];

                $length = $skuObj->getLength();
                $width = $skuObj->getWidth();
                $height = $skuObj->getHeight();
                $weight = $skuObj->getGrossWeight();



                if($length <= 0)
                {
                    $length = 1;
                }
                if($width <= 0)
                {
                    $width = 1;
                }
                if($height <= 0)
                {
                    $height = 1;
                }
                if($weight <= 0)
                {
                    $weight = 0.5;
                }
                
            }
            else
            {
                $length = 1;
                $width = 1;
                $height = 1;
                $weight = 0.5;

            }



            $quantity = $marketPlaceOrderDetail->getQuantityPurchased();


            $parcelList = array();
            $i = 0;

            



            if($skuName == 'B2Y-HEA-2WB' || 
               $skuName == 'B2Y-HEA-2WE' || 
               $skuName == 'B2Y-HEA-2WD' ||
               $skuName == 'B2Y-HEA-2W')
            {
                if($quantity == 1 || $quantity == 2)
                    $service = "STYDL2CXN";
                elseif($quantity >= 3)
                {
                    $service = "STYDL02CP";
                }

                $hscode = "8516395000";

            }
            elseif($skuName == '0F-P387-10PD')
            {
                $service = "STYDL02CP";
                $hscode = "85162950";
            }
            elseif($skuName == 'ES-GSAU-7XA8')
            {
                if($quantity <= 5)
                {
                    $service = "STYDL2CXN";
                    //$service = "STYDL02CP";
                }
                else
                {
                    $service = "STYDL02CP";
                }

                $hscode = "85392210";
            }
            elseif($skuName == 'QI-YYGF-1EMR')
            {
                if($quantity <= 2)
                {
                    $service = "STYDL2CXN";
                }
                else
                {
                    $service = "STYDL02CP";
                }
            }
            elseif($skuName == '8R-9GRD-ZS6W')
            {
                if($quantity <= 2)
                {
                    $service = "STYDL2CXN";
                }
                else
                {
                    $service = "STYDL02CP";
                }
            }
            elseif($skuName == 'FH-VR2G-17VJ')
            {
                if($quantity <= 3)
                {
                    $service = "STYDL2CXN";
                }
                else
                {
                    $service = "STYDL02CP";
                }
            }
            elseif($skuName == '13-YAA9-I6ZP')
            {
                if($quantity == 1)
                {
                    $service = "STYDL2CXN";
                }
                else
                {
                    $service = "STYDL02CP";
                }
            }
            
            else if($skuName == "0700461659273")
            {
                $service = "STYDL2CXN";
            }
            elseif($skuName == "SU0006813")
            {
                $service  = "STYDL2CXN";
            }
            elseif($skuName == "B2Y-HT-001")
            {
                if($quantity == 1)
                {
                    $service = "STYDL2CXN";
                }
                else
                {
                    $service = "STYDL02CP";
                }
                $hscode = "8516395000";
            }
            else
            {
                $service = "STYDL02CP";
            }

            echo "Order Number || Quantity || Service" . "<br>";
            echo $marketPlaceOrder->getMarketPlaceOrderNumber() . " " . $quantity . " " . $service . "<br>";

            $items = array(

                "hscode" => $hscode
            );

            //for($i=0;$i<$quantity;$i++)
            {
                $parcel = array(

                    "length" => $length,
                    "width" => $width,
                    "height" => $height,
                    "weight" => $weight,
                    "items" => $items

                );

                $parcelList[$i] = $parcel;
            }




            if($service != "")
            {
                $countryId = $marketPlaceOrder->getReceiverCountryId();

                $country = new Country($countryId);

                $receiverCountryIso = $country->getIso();

                $postCode = str_replace("  ", " ", $marketPlaceOrder->getReceiverPostCode());
 
                $request = array(

                    "order_reference" => $marketPlaceOrder->getMarketPlaceOrderNumber(),
                    "reference" => "",
                    "service_code" => $service,

                    "sender_country_iso" => "GB",
                    "sender_contact" =>  "Brands",
                    "sender_address_line_1" =>  "One World House, Pump Ln",
                    "sender_address_line_2" =>  "Hayes",
                    "sender_address_line_3" =>  "",
                    "sender_city" =>  "London",
                    "sender_postcode" =>  "UB3 3NB",

                    "receiver_contact" =>  $marketPlaceOrder->getReceiverName(),
                    "receiver_address_line_1" =>  $marketPlaceOrder->getReceiverAddressLine1(),
                    "receiver_address_line_2" =>  $marketPlaceOrder->getReceiverAddressLine2(),                     
                    "receiver_city" =>  $marketPlaceOrder->getReceiverCity(),
                    "receiver_state" =>  $marketPlaceOrder->getReceiverState(),
                    "receiver_postcode" =>  $postCode,
                    "receiver_country_iso" =>  $receiverCountryIso,
                    "receiver_telephone" =>  $marketPlaceOrder->getReceiverPhone(),
                    "receiver_email" =>  $marketPlaceOrder->getReceiverEmail(),

                    "description" => substr($marketPlaceOrderDetail->getTitle(), 0, 50),
                    "value" => $marketPlaceOrderDetail->getItemPrice(),
                    "currency" =>  $marketPlaceOrderDetail->getCurrency(),
                    "eori_number" => "GB214131562000",

                    "parcel" => $parcelList
                );
                
                curl_setopt($curl, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json',
                                                            'Authorization: Bearer ' . GetToken())
                );  
                
                echo json_encode($request);

                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($request));
                curl_setopt($curl, CURLOPT_URL, $GLOBALS['url'] . "v2/generate-label");
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

                $result = curl_exec($curl);
                echo "<pre>";
                print_r($result);
                if(!$result)
                {
                    die("Connection Failure");
                }
                else
                {
                    $resultArray = json_decode($result, true);
                    print_r($resultArray);
                    if($resultArray["sucess"] == "true")
                    {
                        $id = $resultArray["data"]["id"];
                        $conFilter = new ConsignmentFilter();
                        $conFilter->addFilterNew("    id = '" . $id);
                        $conList = $conFilter->getList();
                        if(count($conList) > 0)
                        {
                            $con = $conList[0];
                            $marketPlaceOrder->setConsignmentId($id);
                            $marketPlaceOrder->save();
                            echo "$id id saved";
                        }
                    }
                    else
                    {
                        $error = $resultArray["errors"][0];
                        $conFilter = new ConsignmentFilter();
                        $conFilter->addFilterNew("    hawb = '" . $marketPlaceOrder->getMarketPlaceOrderNumber() . "'");
                        $conList = $conFilter->getList();
                        if(count($conList) > 0)
                        {
                            $con = $conList[0];
                            $id = pathinfo($con->getLabelFile(), PATHINFO_FILENAME);
                            echo $id;
                            $marketPlaceOrder->setConsignmentId($id);
                            $marketPlaceOrder->save();
                        }

                    }

                }

                
                curl_close($curl);
                    
        
                print_r($request);
            }
            
 
        }
        
    }

    function GetToken()
    {
        $token = "";
        $curl = curl_init();
        $auth_data = array(
            'client_id'         => 'eac4802b06553be613bc4633a745db37',
            'client_secret'     => '3e9e793952e7e484b05d6ba1211a31f5',
            'grant_type'        => 'client_credentials'
        );

        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $auth_data);
        curl_setopt($curl, CURLOPT_URL, $GLOBALS['url'] . "token");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $result = curl_exec($curl);
        print_r($result);
        if(!$result)
        {
            die("Connection Failure");
        }
        else
        {
            $resultArray = json_decode($result, true);
            $token = $resultArray["access_token"];
        }

        curl_close($curl);

        return $token;
    }

    
				

?>
				
			
				
						
									
		
	
	
