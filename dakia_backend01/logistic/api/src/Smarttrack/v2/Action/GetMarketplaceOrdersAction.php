<?php
namespace Smarttrack\V2\Action;

use Monolog\Logger;
use RKA\ContentTypeRenderer\HalRenderer;
use Smarttrack\V2\ApiFunctions;
use Smarttrack\V2\SmartAction;
use SmarttrackTransformer\V2\GetMarketplaceOrdersTransformer;

class GetMarketplaceOrdersAction extends SmartAction
{
    protected $logger;
    protected $renderer;
    protected $authorMapper;

    public function __construct(Logger $logger, HalRenderer $renderer)
    {

        $this->logger = $logger;
        $this->renderer = $renderer;
    }

    public function __invoke($request, $response)
    {
        
        $userId = $request->getAttribute('api_user_id');
        $shipmentReqData = $request->getParams();

        $marketplaceKey = (isset($shipmentReqData['marketplace_key']) ? $shipmentReqData['marketplace_key'] : '');
		$trackingNumber = (isset($shipmentReqData['tracking_number']) ? $shipmentReqData['tracking_number'] : '');
		$countryIso = (isset($shipmentReqData['country_iso']) ? $shipmentReqData['country_iso'] : '');
		$city = (isset($shipmentReqData['city']) ? $shipmentReqData['city'] : '');
		$orderDate = (isset($shipmentReqData['order_date']) ? $shipmentReqData['order_date'] : '');
		$orderNumber = (isset($shipmentReqData['order_number']) ? $shipmentReqData['order_number'] : '');
		$contact = (isset($shipmentReqData['contact']) ? $shipmentReqData['contact'] : '');
		$description = (isset($shipmentReqData['description']) ? $shipmentReqData['description'] : '');
		$price = (isset($shipmentReqData['price']) ? $shipmentReqData['price'] : '');

        $limit = (isset($shipmentReqData['per_page']) && ($shipmentReqData['per_page'] <= 50) && ($shipmentReqData['per_page']  > 0)) ? $shipmentReqData['per_page'] :  50;
        $offset = 0;
        $page = (isset($shipmentReqData['page']) && $shipmentReqData['page'] > 0) ? $shipmentReqData['page'] : 1;
        if(!empty($page) && $page > 1) {
            $offset = ((((int) $page) - 1) * $limit);
        }
        $this->logger->info("Getting marketplace key Info - ", ['user_account_id' => $userId]);
        $apiUserData = ApiFunctions::getUserById($userId);
        if ($apiUserData->getId() < 1) {
            $errors = ['User does not exist.'];
            $responseData['STATUS'] = "ERROR";
            $responseData['ERROR'] = $errors;
            $responseData['MESSAGE'] = "Please fix below error(s)";

            $tarnsform = new GetMarketplaceOrdersTransformer($request, $response);
            return $tarnsform->transform($responseData);
        }

		$trackingNumber = (isset($shipmentReqData['tracking_number']) ? $shipmentReqData['tracking_number'] : '');
		$receiverCountryIso = (isset($shipmentReqData['receiver_country_iso']) ? $shipmentReqData['receiver_country_iso'] : '');
		$receiverCity = (isset($shipmentReqData['receiver_city']) ? $shipmentReqData['receiver_city'] : '');
		$orderDateFrom = (isset($shipmentReqData['order_date_from']) ? $shipmentReqData['order_date_from'] : '');
		$orderDateTo = (isset($shipmentReqData['order_date_to']) ? $shipmentReqData['order_date_to'] : date('Y-m-d'));
		$orderNumber = (isset($shipmentReqData['order_number']) ? $shipmentReqData['order_number'] : '');
		$receiverName = (isset($shipmentReqData['receiver_name']) ? $shipmentReqData['receiver_name'] : '');
		$shippedDateFrom = (isset($shipmentReqData['shipped_date_from']) ? $shipmentReqData['shipped_date_from'] : '');
		$shippedDateTo = (isset($shipmentReqData['shipped_date_to']) ? $shipmentReqData['shipped_date_to'] : date('Y-m-d'));
		$orderStatus = (isset($shipmentReqData['order_status']) ? $shipmentReqData['order_status'] : '');

        $paramsValidation = ApiFunctions::validateGetMarketplaceOrdersParams($shipmentReqData);
        if(!empty($paramsValidation)) {
            $errors = $paramsValidation;
            $responseData['STATUS'] = "ERROR";
            $responseData['ERROR'] = $errors;
            $responseData['MESSAGE'] = "Please fix below error(s)";
            $tarnsform = new GetMarketplaceOrdersTransformer($request, $response);
            return $tarnsform->transform($responseData);
        }
        // Get Latest Data from Api insert into DB
        //$marketplaceData = ApiFunctions::getInsertMarketplaceOrders($apiUserData);
        // Get Data from table to show
        $marketPlaceOrderFilter = new \MarketPlaceOrderFilter();
		$marketPlaceOrderFilter->addFieldFilter("    ord.user_id",$apiUserData->getUserAccountId());
        $marketPlaceOrderFilter->addJoin("  country `c`","  c.id = ord.receiver_country_id ", "LEFT");
        $marketPlaceOrderFilter->addJoin("  market_places `mp`","  mp.id = ord.marketplace_id ");
        //$marketPlaceOrderFilter->addFieldFilter("mp.is_active",'1');
        //$marketPlaceOrderFilter->addFieldFilter("mp.is_delete",'0');
		if(!empty($marketplaceKey))
        	$marketPlaceOrderFilter->addFieldFilter("mp.plugin_key",$marketplaceKey);
		if(!empty($orderNumber))
        	$marketPlaceOrderFilter->addFieldFilter("ord.marketplace_order_number",$orderNumber);
		if(!empty($orderStatus))
			$marketPlaceOrderFilter->addFieldFilter("ord.order_status",$orderStatus);
		if(!empty($trackingNumber))
			$marketPlaceOrderFilter->addFieldFilter("ord.tracking_number",$trackingNumber);
		if(!empty($receiverVountryIso))
			$marketPlaceOrderFilter->addFieldFilter("c.iso",$receiverCountryIso);
		if(!empty($receiverCity))
			$marketPlaceOrderFilter->addFieldLikeFilter("ord.receiver_city",$receiverCity);
		if(!empty($orderDateFrom) && !empty($orderDateTo))
			$marketPlaceOrderFilter->addDateFilter($orderDateFrom,$orderDateTo,'create_time');
		if(!empty($receiverName))
			$marketPlaceOrderFilter->addFieldLikeFilter("ord.receiver_name",$receiverName);
		if (!empty($shippedDateFrom) && !empty($shippedDateTo))
            $marketPlaceOrderFilter->addDateFilter($shippedDateFrom, $shippedDateTo, 'shipped_date');

        $marketPlaceOrderFilter->setRowsPerPage($limit);
        $marketPlaceOrderFilter->setOffset($offset);
        $orderDataCount = $marketPlaceOrderFilter->getPagingCount();
        $marketPlaceOrder = $marketPlaceOrderFilter->getColumnList("ord.*,c.name AS receiver_country_id, mp.title, mp.plugin_key");
        $lastPage = (ceil($orderDataCount / $limit) == 0 ? 1 : ceil($orderDataCount / $limit));
        $warnings = [];
//        $foundHawb = [];
//        $foundAwb = [];

        $marketplaceOrderDetailsDataCollection = [];
        $marketplaceDataCollection = [];
        if (count($marketPlaceOrder) > 0) {
            foreach ($marketPlaceOrder as $marketPlaceOrderArr) {
                $marketplaceOrderDetailsDataCollection;
                $marketplaceorderDetailsFilter = new \MarketPlaceOrderDetailsFilter();
                $marketplaceorderDetailsFilter->addFieldFilter("    marketplace_order_id",$marketPlaceOrderArr->getId());
                $marketplaceorderDetailsData = $marketplaceorderDetailsFilter->getList();
                if(count($marketplaceorderDetailsData) > 0){
                    foreach ($marketplaceorderDetailsData as $marketplaceorderDetailsDataArr) {
                        $marketplaceOrderDetailsDataCollection[] = [
                            'marketplace_item_id'=>$marketplaceorderDetailsDataArr->getMarketplaceItemId(),
                            'sku'=>$marketplaceorderDetailsDataArr->getSku(),
                            'title'=>$marketplaceorderDetailsDataArr->getTitle(),
                            'quantity_purchased'=>$marketplaceorderDetailsDataArr->getQuantityPurchased(),
                            'asin'=>$marketplaceorderDetailsDataArr->getAsin(),
                            'item_price'=>$marketplaceorderDetailsDataArr->getItemPrice(),
                            'currency'=>$marketplaceorderDetailsDataArr->getCurrency()
                        ];
                    }
                }
                $marketplaceDataCollection [] = [
                	'marketplace' => $marketPlaceOrderArr->getTitle(),
                	'marketplace_key' => $marketPlaceOrderArr->getPluginKey(),
                    'marketplace_order_number' => $marketPlaceOrderArr->getMarketplaceOrderNumber(),
                    'order_date' => $marketPlaceOrderArr->getCreateTime(),
                    'order_status' => $marketPlaceOrderArr->getOrderStatus(),
                    'receiver_name' => $marketPlaceOrderArr->getReceiverName(),
                    'receiver_phone' => $marketPlaceOrderArr->getReceiverPhone(),
                    'receiver_state' => $marketPlaceOrderArr->getReceiverState(),
                    'receiver_city' => $marketPlaceOrderArr->getReceiverCity(),
                    'receiver_country_id' => $marketPlaceOrderArr->getReceiverCountryId(),
                    'receiver_addressline1' => $marketPlaceOrderArr->getReceiverAddressline1(),
                    'receiver_addressline2' => $marketPlaceOrderArr->getReceiverAddressline2(),
                    'receiver_addressline2' => $marketPlaceOrderArr->getReceiverAddressline2(),
                    'receiver_postcode' => $marketPlaceOrderArr->getReceiverPostcode(),
                    'payment_method' => $marketPlaceOrderArr->getPaymentMethod(),
                    'order_total' => $marketPlaceOrderArr->getOrderTotal(),
                    'tracking_number' => $marketPlaceOrderArr->getTrackingNumber(),
                    'receiver_email' => $marketPlaceOrderArr->getReceiverEmail(),
                    'sender_email' => $marketPlaceOrderArr->getSenderEmail(),
//                    'ack' => $marketPlaceOrderArr->getAck(),
                    'error_code' => $marketPlaceOrderArr->getErrorCode(),
                    'error_message' => $marketPlaceOrderArr->getErrorMessage(),
                    'consignment_id' => $marketPlaceOrderArr->getConsignmentId(),
                    'shipped_date' => $marketPlaceOrderArr->getShippedDate(),
                    'order_detail' => $marketplaceOrderDetailsDataCollection
                ];
                $marketplaceOrderDetailsDataCollection = [];
            }
        }
        $shipmentResponse = [];
        if(!empty($marketplaceDataCollection)) {
            $shipmentResponse['STATUS'] = 'SUCCESS';
            $shipmentResponse['MESSAGE'] = 'Records Found';
            $shipmentResponse['ERROR'] = $warnings;
            $shipmentResponse['TOTAL'] = $orderDataCount;
            $shipmentResponse['PER_PAGE'] = $limit;
            $shipmentResponse['CURRENT_PAGE'] = $page;
            $shipmentResponse['LAST_PAGE'] = $lastPage;
            $shipmentResponse['data'] = $marketplaceDataCollection;
        } else {
            $shipmentResponse['STATUS'] = 'ERROR';
            $shipmentResponse['MESSAGE'] = 'No Records Found.';
            $shipmentResponse['ERROR'] = ['No Records Found.'];
        }
        $tarnsform = new GetMarketplaceOrdersTransformer($request, $response);
        return $tarnsform->transform($shipmentResponse);
    }
}
