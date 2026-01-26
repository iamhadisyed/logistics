<?php

class MarketPlaceOrder extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {

        $fieldList = array(
            'id' => 'number',
            'marketplace_id' => 'number',
            'marketplace_order_number' => 'string',
            'create_time' => 'date',
            'order_status' => 'string',
            'receiver_name' => 'string',
            'receiver_phone' => 'string',
            'receiver_state' => 'string',
            'receiver_city' => 'string',
            'receiver_country_id' => 'number',			
            'receiver_addressline1' => 'string',
            'receiver_addressline2'	=> 'string',
            'receiver_postcode' => 'string',
            'payment_method'	=> 'string',
            'order_total' => 'string',
            'tracking_number'	=> 'string',
            'receiver_email'	=> 'string',
            'sender_email' => 'string',
            'Ack' => 'string',
            'error_code' => 'string',
            'error_message' => 'string',
	    	'shipped_date' => 'date',
            'tracking_number' => 'undefined',
            'item_price' => 'undefined',
            'marketplace_order_id' => 'undefined',
            'currency' => 'undefined',
            'consignment_id' => 'number',
            'user_id' => 'number',
            'shipped_date' => 'date',
            'title' => 'undefined', 
            'plugin_key' => 'undefined',
            'marketplace_item_id' =>'undefined',
            'quantity_purchased' => 'undefined',
            'item_price' => 'undefined',
            'sku' => 'undefined',
            'asin' => 'undefined',
            'currency' => 'undefined',
            'send_wms' => 'bit'
        );
        //
        parent::__construct("marketplace_order", 'id', $fieldList, $mixedCreator);
    }

    /**
     * Get list of marketplace_order objects, using sql given
     *
     * @param string $sql
     */
   public static function getMarketPlaceOrderListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
    
      public static function getTotalNumberOfMarketPlaceOrderFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
    
    public static function exportMarketPlaceOrderHeader()
    {
        // output header row
        $header = "";
        $header_row = array(
            "Account",
            "Tracking Number",
            "HAWB",
            "Service",
            "ServiceCode",
          //  "REF",
            "Date Submitted",
            "Date Label Created",
            "Company",
            "Contact",
            "Address Line 1",
            "Address Line 2",
            "City",
            "Country",
            "Postcode",
            "Telephone",
            "Number of Pieces",
            "Item Id",
            "Quantity Purchased",
            "Order Weight",
            "Description",
            "Item Price",
            "Total Price",
            "Currency",
            //"Notes",
            "Parcel Number",
            "Status",
            "Courier Status",
            "Label",
            "Dimensions(L*W*H)");
        foreach ($header_row as $field) {
            $header .= $field . ",";
        }
        $header .= "\r";
        return $header;
    }
    
     public function exportRow($shipment)
    {
        $csv = "";
        $conId = $shipment->getConsignmentId();
        $consignmentObject = new Consignment($conId);
        
        $date_label_created = date("Y-m-d", $consignmentObject->getDateLabelCreated());
        if ($date_label_created == '1970-01-01')
            $date_label_created = "";
        
        $date_created = date("Y-m-d", $consignmentObject->getDateCreated());
        if ($date_created == '1970-01-01')
            $date_created = "";        
        
        $trackingNumber = $consignmentObject->getAwb();
        $userId = $consignmentObject->getUserId();
        $user = new User($userId);
        $userAccountId = $user->getUserAccountId();
        $userAccount = new CustomerAccount($userAccountId);
        $userAccountName = $userAccount->getUserAccount();
        
        $serviceId = $consignmentObject->getServiceId();
        $service = new Services($serviceId);
        $serviceName = $service->getName();
        $serviceCode = $service->getCode();
        
        //$marketplaceOrderDetailFilter = new MarketPlaceOrderDetailsFilter();
       // $marketplaceOrderDetailFilter->addFieldFilter('marketplace_order_id',$shipment->getId());
        //$resultQuantity = $marketplaceOrderDetailFilter->getColumnList('*');
       // $itemDetail =  $resultQuantity[0];
        //foreach($resultQuantity as $itemDetail)
        {
            $itemId = $shipment->getmarketplaceItemId();
            $quantity = $shipment->getQuantityPurchased();
            $description = $shipment->getTitle();
            $itemPrice = $shipment->getItemPrice();
        //}
        
            $csv .= cleanCsvCall($userAccountName) . ",";
            $csv .= cleanCsvCall($trackingNumber) . ",";
            $csv .= cleanCsvCall($shipment->getMarketPlaceOrderNumber()) . ",";
            if($consignmentObject->getProductName()!= '')
            {
                $csv .= cleanCsvCall($consignmentObject->getProductName()) . ",";
                $csv .= cleanCsvCall($consignmentObject->getProductCode()) . ",";
            }
            else {
                $csv .= cleanCsvCall($serviceName) . ",";
                $csv .= cleanCsvCall($serviceCode) . ",";
            }
            //$csv .= preg_replace('/[\$,]/', '', trim(cleanCsvCall($consignmentObject->getReference()))) . ",";
            $csv .= $date_created . ",";
            $csv .= $date_label_created . ",";
            $csv .= cleanCsvCall($consignmentObject->getCompany()) . ",";
            $csv .= cleanCsvCall($consignmentObject->getContact()) . ",";
            $csv .= cleanCsvCall($consignmentObject->getAddressLine1()) . ",";
            $csv .= cleanCsvCall($consignmentObject->getAddressLine2()) . ",";
            $csv .= cleanCsvCall($consignmentObject->getCity()) . ",";

            $country = new Country($consignmentObject->getCountryId());
            $countryName = $country->getName();

            $csv .= cleanCsvCall($countryName) . ",";
            $csv .= cleanCsvCall($consignmentObject->getPostcode()) . ",";
            $csv .= cleanCsvCall($consignmentObject->getTelephone()) . ",";
            $csv .= cleanCsvCall($consignmentObject->getNumberPieces()) . ",";


            $csv .= cleanCsvCall($itemId) . ",";
            $csv .= cleanCsvCall($quantity) . ",";
            $csv .= cleanCsvCall($consignmentObject->getWeight()) . ",";
            $csv .= cleanCsvCall($description) . ",";
            $csv .= cleanCsvCall($itemPrice) . ",";
            $csv .= cleanCsvCall($consignmentObject->getValue()) . ",";;
            $csv .= cleanCsvCall($consignmentObject->getCurrency()) . ",";
            //$csv .= cleanCsvCall($consignmentObject->getNotes()) . ",";
            $csv .= cleanCsvCall($consignmentObject->getAwb(),'int') . ",";
            $csv .= cleanCsvCall($shipment->getOrderStatus()). ",";

            if ($consignmentObject->getCourierStatus() != '')
                $csv .= cleanCsvCall($consignmentObject->getCourierStatus()) . ",";
            else
                $csv .= '' . ",";
            if (trim($consignmentObject->getLabelFile()) != '')
                $csv .= SETTING_URL_LABEL . str_replace("../", "", $consignmentObject->getLabelFile()) . ",";
            else
                $csv .= '' . ",";
            $csv .= cleanCsvCall($consignmentObject->getparcellength()) . '*' . cleanCsvCall($consignmentObject->getparcelwidth()) . '*' . cleanCsvCall($consignmentObject->getparcelheight());
            $csv .= "\r";
        }
            return $csv;       
    }
}

// class
