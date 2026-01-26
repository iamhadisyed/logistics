<?php
include_classes([
    'marketplaceorderdetails.class',
    'marketplaceorderdetails.class',
    'marketplaceorder.class',
    'marketplacesfilter.class',
    'marketplacesauthenticatefield.class',
    'marketalacesauthenticatefieldfilter.class',
    'marketplaceorderfilter.class',
    'usermarketplacesmappingfilter.class',
    'usermarketplacesmapping.class',
    'marketplaces.class',
    'consignmentfilter.class',
    'consignment.class',
]);

class Api2cart
{
    public $userMarketPlaceMappingObj;
    public $storeKey;
    public $marketplace_order_id;

    public function __construct($userMarketPlaceMappingObj = null)
    {
        if(!empty($userMarketPlaceMappingObj)){
            $this->userMarketPlaceMappingObj = $userMarketPlaceMappingObj;
            $this->storeKey = $this->userMarketPlaceMappingObj->getStoreKey();
        }else{
            $this->storeKey = "";
        }
    }

    public $shipmentStatus = [
        'WooCommerce' => 'Processing',
        'OpenCart' => 'Shipped',
        'PrestaShop' => 'Shipped',
        'Magento' => 'Processing',
        'Shopify' => 'Open',
    ];

    public function fetchOrders()
    {
        $storeKey = $this->storeKey;
        if (empty($storeKey)) {
            $output["STATUS"] = "ERROR";
            $output["MESSAGE"] = "You have entered invalid Store Credentials";
            return $output;
        }
        $user = SessionManager::getUser();
        $marketPlaceOrderFilter = new MarketPlaceOrderFilter();
        $marketPlaceOrderFilter->addFieldFilter("    marketplace_id", $this->userMarketPlaceMappingObj->getMarketPlacesId());
        $marketPlaceOrderFilter->addFieldFilter("    user_id", $user->getUserAccountId());
        $marketPlaceOrderFilter->AddOrderById("desc");
        $result = $marketPlaceOrderFilter->getColumnList("create_time");
        $dateFilter = '';
        if (!empty($result)) {
            $dateFilter = '&created_from=' . $result[0]->getCreateTime();
        }
        $getOrderApiUrl = API2CART_URL . 'order.list.json?api_key=' . API2CART_API_KEY . '&store_key=' . $storeKey . $dateFilter . '&params=force_all';//&order_status=Unshipped
        $resultData = json_decode(file_get_contents($getOrderApiUrl), true);
        $recordsFound = false;
        if ($resultData['return_code'] == 0) {
            if (!empty($resultData['result']['order'])) {
                $data = $resultData['result']['order'];
                if (count($data) > 0) {
                    foreach ($data as $datum) {
                        $marketPlaceOrderObj = new MarketPlaceOrderFilter();
                        $marketPlaceOrderObj->addFieldFilter('      marketplace_id', $this->userMarketPlaceMappingObj->getMarketPlacesId());
                        $marketPlaceOrderObj->addFieldFilter('      marketplace_order_number', $datum['order_id']);
                        $marketPlaceOrderObj->addFieldFilter('      user_id', $this->userMarketPlaceMappingObj->getUserAccountId());
                        $marketPlaceOrderObj = $marketPlaceOrderObj->getList();
                        if (empty($marketPlaceOrderObj)) {
                            $recordsFound = true;
                            $curreancyDetails = $datum['currency']['iso3'];
                            $marketPlaceOrder = new MarketPlaceOrder();
                            if (!empty($datum['customer']['first_name']) && !empty($datum['customer']['last_name'])) {
                                $marketPlaceOrder->setReceiverName($datum['customer']['first_name'] . ' ' . $datum['customer']['last_name']);
                            }

                            if (!empty($datum['shipping_address']['phone'])) {
                                $marketPlaceOrder->setReceiverPhone($datum['shipping_address']['phone']);
                            }

                            if (!empty($datum['shipping_address']['state'])) {
                                $marketPlaceOrder->setReceiverState($datum['shipping_address']['state']);
                            }

                            if (!empty($datum['shipping_address']['city'])) {
                                $marketPlaceOrder->setReceiverCity($datum['shipping_address']['city']);
                            }

                            if (!empty($datum['shipping_address']['country']['code2'])) {
                                $countryFilter = new CountryFilter();
                                $countryFilter->addFieldFilter("iso", trim($datum['shipping_address']['country']['code2']));
                                $countryId = $countryFilter->getColumnList("id");
                                $marketPlaceOrder->setReceiverCountryId($countryId[0]->getId());

                                $marketPlaceOrder->setUserId($_SESSION['userId']);
                                $marketPlaceOrder->setMarketPlaceId($this->userMarketPlaceMappingObj->getMarketPlacesId());
                            }

                            if (!empty($datum['shipping_address']['address1'])) {
                                $addressLine1 = trim($datum['shipping_address']['address1']);
                                if (strlen($addressLine1) > 30) {
                                    $addressLine1substr = substr($addressLine1, 0, 29);
                                    $addressLine1substr1 = substr($addressLine1, 30, strlen($addressLine1));
                                    $marketPlaceOrder->setReceiverAddressLine1($addressLine1substr);
                                    //die;
                                } else {
                                    $marketPlaceOrder->setReceiverAddressLine1($addressLine1);
                                }
                            }

                            if (!empty($datum['shipping_address']['address2'])) {
                                $marketPlaceOrder->setReceiverAddressLine2(substr($addressLine1substr1 . $datum['shipping_address']['address2'], 0, 29));
                                //.$combineArray['ship-address-3'];
                            }

                            if (!empty($datum['shipping_address']['postcode'])) {
                                $marketPlaceOrder->setReceiverPostCode($datum['shipping_address']['postcode']);
                            }

                            if (!empty($datum['order_products']['totals']['total'])) {
                                $marketPlaceOrder->setOrderTotal($datum['order_products']['totals']['total']);
                            }

                            if (!empty($datum['customer']['email'])) {
                                $marketPlaceOrder->setReceiverEmail($datum['customer']['email']);
                            }

                            if (!empty($datum['order_id'])) {
                                $marketPlaceOrder->setMarketPlaceOrderNumber($datum['order_id']);

                                $date = date("Y-m-d H:i:s");
                                $startDate = date('Y-m-d H:i:s', strtotime('-1 hours', strtotime($date)));
                                $currentDate = new DateTime($startDate);
                                $currentDateTime = $currentDate->format('Y-m-d H:i:s');
                                $marketPlaceOrder->setCreateTime($currentDateTime);

                                // $marketPlaceOrder->setOrderStatus(''); // need to ask bcz we don't get it in response
                                $marketPlaceOrder->setOrderStatus('Unshipped');
                                $marketPlaceOrder->setUserId($this->userMarketPlaceMappingObj->getUserAccountId());
                                $marketPlaceOrder->save();
                            }

                            $marketPlaceOrderId = $marketPlaceOrder->getId();
                            foreach ($datum['order_products'] as $productDetails) {
                                $marketPlaceOrderDetails = new MarketPlaceOrderDetails();
                                $marketPlaceOrderDetails->setMarketPlaceOrderId($marketPlaceOrderId);
                                $marketPlaceOrderDetails->setMarketPlaceItemId($productDetails['order_product_id']);
                                $marketPlaceOrderDetails->setSku($productDetails['model']);
                                $marketPlaceOrderDetails->setTitle($productDetails['name']);
                                $marketPlaceOrderDetails->setQuantityPurchased($productDetails['quantity']);
                                $marketPlaceOrderDetails->setItemPrice($productDetails['total_price']); //need to ask bcz we are not getting it in API call
                                $marketPlaceOrderDetails->setCurrency($curreancyDetails);
                                if ($marketPlaceOrderId != '') {
                                    $marketPlaceOrderDetails->save();
                                } //break;
                            }
                        }
                    }
                    $output["STATUS"] = "SUCCESS";
                    $output["MESSAGE"] = 'Records Fetched Successfully.';
                } else {
                    $output["STATUS"] = "ERROR";
                    $output["MESSAGE"] = 'No Records Found.';
                }
            } else {
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = 'No Records Found.';
            }
        } else {
            $output["STATUS"] = "ERROR";
            $output["MESSAGE"] = $resultData['return_message'];
        }
        if(!$recordsFound) {
            $output["STATUS"] = "ERROR";
            $output["MESSAGE"] = 'No Records Found.';
        }
        return $output;
    }

    public function fetchApi2cartFields()
    {
        $sessionUser = SessionManager::getUser();
        $getOrderApiUrl = API2CART_URL . 'cart.list.json?api_key=' . API2CART_API_KEY;
        $resultData = json_decode(file_get_contents($getOrderApiUrl), true);
        if (!empty($resultData['result']['supported_carts'][0]['cart'])) {
            $data = $resultData['result']['supported_carts'][0]['cart'];
            if (count($data) > 0) {
                foreach ($data as $datum) {
                    $MarketPlaces = new MarketPlaces();
                    $MarketPlaces->setAddedBy($sessionUser->getId());
                    $MarketPlaces->setAddedDate(date("Y-m-d H:i:s"));
                    $MarketPlaces->setIsDelete("0");
                    $MarketPlaces->setTitle($datum['cart_name']);
                    $MarketPlaces->setDescription('');
                    $MarketPlaces->setPageLink('');
                    $MarketPlaces->setIsActive(1);
                    $MarketPlaces->setTranslationKey('');
                    $MarketPlaces->setPluginKey($datum['cart_id']);
                    $MarketPlaces->setIsFeatured(1);
                    $MarketPlaces->setIsApi2cart(1);
                    $MarketPlaces->save();
                    $lastId = $MarketPlaces->getId();
                    if (!empty($lastId)) {
                        $marketPlaceAuthKeys = new MarketPlacesAuthenticateField();
                        $marketPlaceAuthKeys->setFieldName('Store Url');
                        $marketPlaceAuthKeys->setFieldValue('store_url');
                        $marketPlaceAuthKeys->setAutoGenerateValue(0);
                        $marketPlaceAuthKeys->setMarketPlacesId($lastId);
                        $marketPlaceAuthKeys->setAddedBy($sessionUser->getId());
                        $marketPlaceAuthKeys->setAddedDate(date("Y-m-d H:i:s", time()));
                        $marketPlaceAuthKeys->setIsDelete('0');
                        $marketPlaceAuthKeys->save();
                        foreach ($datum['params'][0] as $key => $param) {
                            $marketPlaceAuthKeys = new MarketPlacesAuthenticateField();
                            $marketPlaceAuthKeys->setFieldName(str_replace('_', ' ', ucwords(strtolower($key))));
                            $marketPlaceAuthKeys->setFieldValue($key);
                            $marketPlaceAuthKeys->setAutoGenerateValue(0);
                            $marketPlaceAuthKeys->setMarketPlacesId($lastId);
                            $marketPlaceAuthKeys->setAddedBy($sessionUser->getId());
                            $marketPlaceAuthKeys->setAddedDate("Y-m-d H:i:s", time());
                            $marketPlaceAuthKeys->setIsDelete('0');
                            $marketPlaceAuthKeys->save();
                        }
                    }
                }
            }
        }
    }

    public function addCartInApi2Cart($data)
    {
        $storeKey = '';
        $data['api_key'] = API2CART_API_KEY;
        $apiUrl = API2CART_URL . 'cart.create.json?';
        foreach ($data as $key => $value) {
            if (!empty($value)) {
                $apiUrl .= $key . '=' . $value . '&';
            }
        }
        $apiUrl = rtrim($apiUrl, '&');
        $responseData = json_decode(file_get_contents($apiUrl), true);
        //status code return_code = 2 for already exist
        //status code return_code = 0 for created successfully
        if ($responseData['return_code'] == 8 || $responseData['return_code'] == 0) {
            $storeKey = $responseData['result']['store_key'];
        }
        if (!empty($storeKey)) {
            return ['status' => true, 'store_key' => $storeKey];
        } else {
            return ['status' => false, 'result' => $responseData['return_message']];
        }
    }

    function callAPI($method, $url, $data = [])
    {
        $curl = curl_init();
        switch ($method) {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);
                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }

        // OPTIONS:
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        // EXECUTE:
        $result = curl_exec($curl);
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
        }
        if (empty($error_msg)) {
            curl_close($curl);
            return $result;
        } else {
            return false;
        }
    }

    public function dispatchLabel($hawb)
    {
        $marketPlaceOrderId = $this->marketplace_order_id;
        $storeKey = $this->userMarketPlaceMappingObj->getStoreKey();
        $consignmentCheck = new ConsignmentFilter();
        $consignmentCheck->addHawbFilter($hawb);
        if ($consignmentCheck->getCount() > 0) {
            $rowlist = $consignmentCheck->getColumnList('*');
            $rowlistData = $rowlist[0];
        } else {
            $output["STATUS"] = "ERROR";
            $output["MESSAGE"] = "Shipment not found";
            return $output;
        }
        $consignmentId = $rowlistData->getId();
        $output["STATUS"] = "SUCCESS";
        $output["MESSAGE"] = "Your order has been dispatched successfully";

        $consignmentObj = new Consignment($consignmentId);

        $consignmentObj->setShipmentStatus(Consignment::STATUS_DISPATCHED);
        $consignmentObj->save();

        // set status in marketplace order table
        $marketPlaceOrderObj = new MarketPlaceOrder($marketPlaceOrderId);
        $marketPlacesObj = new MarketPlaces($marketPlaceOrderObj->getMarketplaceId());
        $marketPlaceOrderObj->setOrderStatus('Shipped');
        $marketPlaceOrderObj->setShippedDate(date('Y-m-d H:i:s'));
        $marketPlaceOrderObj->save();
        $getOrderApiUrl = API2CART_URL . 'order.update.json?api_key=' . API2CART_API_KEY . '&store_key=' . $storeKey . '&date_finished=' . date("Y-m-d H:i:s", time()) . '&order_id=' . $marketPlaceOrderObj->getMarketPlaceOrderNumber() . '&order_status=' . $this->shipmentStatus[$marketPlacesObj->getTitle()] . '&comment=Order Has been Shipped.';
        $response = json_decode(file_get_contents(str_replace(" ", '%20', $getOrderApiUrl)));
        return $output;
    }
}
