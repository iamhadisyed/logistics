<?php

namespace Smarttrack\Action;

use Error\ApiProblem;
use Error\Exception\ProblemException;
use Monolog\Logger;
use RKA\ContentTypeRenderer\ApiProblemRenderer;
use RKA\ContentTypeRenderer\HalRenderer;
use Smarttrack\ApiFunctions;
use SmarttrackTransformer\QuotationsTransformer;
//use SmarttrackTransformer\UserAccountTransformer;
use Zend\InputFilter\Factory as InputFilterFactory;
use SmarttrackTransformer\UserShipmentTransformer;

class GetUserShipmentAction
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
        $trackingReqData = $request->getParams();
      
        $trackingId = $trackingReqData['shipment'];
        $offset = 0;
        if(!empty($trackingReqData['page_number']) && $trackingReqData['page_number'] > 1) {
            $offset = ((int) $trackingReqData['page_number']) - 1;
            $offset = $offset * 50;
        }
        $this->logger->info("Creating Account For User - ", ['user_account_id' => $userId]);
        $apiUserData = ApiFunctions::getUserById($userId);
        if ($apiUserData->getId() < 1) {
            $problem = new ApiProblem(
                'Could not find user',
                'http://zenddesk.smarttrack.com',
                404
            );
            throw new ProblemException($problem);
        }
//	    echo "<pre>"; print_r($apiUserData); echo "</pre>"; die();
        $userAccounts = ApiFunctions::getAccountUsers($apiUserData->getUserAccountId());
        $userTrackingData = new \ConsignmentFilter();
        if(!empty($trackingId)){
            if(is_array($trackingId))
            {
                $trackingId = "'" . array_map('trim',implode("','", $trackingId)) . "'";
                $userTrackingData->addFilterNew(' (c.awb IN (' . $trackingId . ') OR c.hawb IN (' . $trackingId . '))');
            }
            else
            {
                $trackingId =  trim($trackingId) ;
                $userTrackingData->addFilterNew(" (c.awb IN ('" . $trackingId . "') OR c.hawb IN ('" . $trackingId . "'))");    
            }
        }
        $userTrackingData->addFilterNew('c.user_id IN ( ' . $userAccounts . ')');
        $userTrackingData->setRowsPerPage('50');
        $userTrackingData->setOffset($offset);
//        $userTrackingData->addJoin(' parcel p', 'c.awb = p.tracking_number');
        $userTrackingData->addJoin(' user u', 'c.user_id = u.id');
        $userTrackingData->addJoin(' services s', 'c.service_id = s.id');
        $userTrackingData = $userTrackingData->getListDynamic('*, c.id as c_id, s.name as service_name, s.code as service_code');
        $userTrackingDataCount = new \ConsignmentFilter();
        $userTrackingDataCount = $userTrackingDataCount->getShipmentPagingCountNew();
        $pagesCount = 1;
        if($userTrackingDataCount > 50) {
            $pagesCount = ceil($userTrackingDataCount/50);
        }
        $trackingDataCollection = [];
        if (!empty($userTrackingData)) {
            foreach ($userTrackingData as $tackingData) {
//                echo "<pre>" . $tackingData->getCId();
                $shippemntParcels = new \ParcelFilter();
                $shippemntParcels->addFieldFilter('consignment_id', $tackingData->getCId());
                $shippemntParcels = $shippemntParcels->getList();
                $consignmentParcels = [];
                if (!empty($shippemntParcels)) {
                    foreach ($shippemntParcels as $shippemntParcel) {
                        $consignmentParcels[] = [
//                            'parcel_status' => $shippemntParcel->getParcelStatusCode(),
                            'tracking_number' => $shippemntParcel->getTrackingNumber(),
                            'length' => $shippemntParcel->getLength(),
                            'width' => $shippemntParcel->getWidth(),
                            'height' => $shippemntParcel->getHeight(),
                            'weight' => $shippemntParcel->getWeight(),
                        ];
                    }
                }
                $trackingDataCollection['total_pages'] = $pagesCount;
                $trackingDataCollection['data'][] = [
                    "created_by" => $tackingData->getFirstName() . " " . $tackingData->getLastName(),
                    "order_reference" => $tackingData->getHawb(),
                    "tracking_number" => $tackingData->getAwb(),
                    "receiver_company" => $tackingData->getCompany(),
                    "receiver_contact" => $tackingData->getContact(),
                    "receiver_address_line1" => $tackingData->getAddressLine1(),
                    "receiver_address_line2" => $tackingData->getAddressLine2(),
                    "receiver_address_line3" => $tackingData->getAddressLine3(),
                    "receiver_city" => $tackingData->getCity(),
                    "receiver_country" => $tackingData->getCountry(),
                    "receiver_postcode" => $tackingData->getPostcode(),
                    "receiver_telephone" => $tackingData->getTelephone(),
                    "sender_company" => $tackingData->getSenderCompany(),
                    "sender_contact" => $tackingData->getSenderEmail(),
                    "sender_address_line1" => $tackingData->getSenderAddressLine1(),
                    "sender_address_line2" => $tackingData->getSenderAddressLine2(),
                    "sender_address_line3" => $tackingData->getSenderAddressLine3(),
                    "sender_city" => $tackingData->getSenderCity(),
//                    "sender_country" => $tackingData->getSenderCountry(),
                    "sender_postcode" => $tackingData->getSenderPostcode(),
                    "sender_telephone" => $tackingData->getSenderTelephone(),
//                    "sender_country_iso" => $tackingData->GB,
//                    "sender_contact" => $tackingData->getNajam,
//                    "sender_email" => $tackingData->gethadi,
//                    "sender_company" => $tackingData->getASD,
//                    "sender_address_line_1" => $tackingData->getxx,
//                    "sender_address_line_2" => $tackingData->get,
//                    "sender_address_line_3" => $tackingData->get,
//                    "sender_city" => $tackingData->getlondon,
//                    "sender_state" => $tackingData->getLondon,
//                    "sender_postcode" => $tackingData->get,
//                    "sender_telephone" => $tackingData->get,
//                    "receiver_country_iso" => $tackingData->get,
//                    "receiver_contact" => $tackingData->getNajam,
//                    "receiver_email" => $tackingData->get,
//                    "receiver_company" => $tackingData->get,
//                    "receiver_address_line_1" => $tackingData->get,
//                    "receiver_address_line_2" => $tackingData->get,
//                    "receiver_address_line_3" => $tackingData->get,
//                    "receiver_city" => $tackingData->get,
//                    "receiver_state" => $tackingData->get,
//                    "receiver_postcode" => $tackingData->get,
//                    "receiver_telephone" => $tackingData->get3214518055,
                    "weight" => $tackingData->getWeight(),
//                    "DateImported" => null,
                    "consignment_status" => $tackingData->getConsignmentStatus(),
//                    "ServiceType" => null,
                    "label_url" => SETTING_URL_LABEL . $tackingData->getLabelFile(),
                    "service_code" => $tackingData->getServiceCode(),
                    "service_name" => $tackingData->getServiceName(),
                    "parcels" => $consignmentParcels
                ];
            }
        }

        $transformer = new UserShipmentTransformer();
        $hal = $transformer->transformShipmentCollection($trackingDataCollection);
        $resultData = $this->renderer->render($request, $response, $hal);
        ApiFunctions::AddApiLog("get-shipments", $trackingReqData,$resultData ,$userId);
        return $resultData;
    }
}
