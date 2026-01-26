<?php
namespace Smarttrack\V2\Action;

use Monolog\Logger;
use RKA\ContentTypeRenderer\HalRenderer;
use Smarttrack\V2\ApiFunctions;
use Smarttrack\V2\SmartAction;
use SmarttrackTransformer\V2\UserShipmentTransformer;

class GetUserShipmentAction extends SmartAction
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
        $orderReference = $shipmentReqData['order_reference'];
        $trackingNumber = $shipmentReqData['tracking_number'];
        $limit = (isset($shipmentReqData['per_page']) && ($shipmentReqData['per_page'] <= 50) && ($shipmentReqData['per_page']  > 0)) ? $shipmentReqData['per_page'] :  50;
        $offset = 0;
        $page = (isset($shipmentReqData['page']) && $shipmentReqData['page'] > 0) ? $shipmentReqData['page'] : 1;
        if(!empty($page) && $page > 1) {
            $offset = ((((int) $page) - 1) * $limit);
        }

        $this->logger->info("Getting Shipment Info - ", ['user_account_id' => $userId]);
        $apiUserData = ApiFunctions::getUserById($userId);
        if ($apiUserData->getId() < 1) {
            $errors = ['User does not exist.'];
            $responseData['STATUS'] = "ERROR";
            $responseData['ERROR'] = $errors;
            $responseData['MESSAGE'] = "Please fix below error(s)";

            $tarnsform = new UserShipmentTransformer($request, $response);
            return $tarnsform->transform($responseData);
        }

        $paramsValidation = ApiFunctions::validateGetShipmentsParams($shipmentReqData);
        if(!empty($paramsValidation)) {
            $errors = $paramsValidation;
            $responseData['STATUS'] = "ERROR";
            $responseData['ERROR'] = $errors;
            $responseData['MESSAGE'] = "Please fix below error(s)";
            $tarnsform = new UserShipmentTransformer($request, $response);
            return $tarnsform->transform($responseData);
        }
        $warnings = [];
        $foundHawb = [];
        $foundAwb = [];
        $userAccounts = ApiFunctions::getAccountUsers($apiUserData->getUserAccountId());
        $shipmentData = new \ConsignmentFilter();
        if(!empty($orderReference)){
            if(is_array($orderReference))
            {
                $orderReferenceString = [];
                foreach ($orderReference as $key => $hawb) {
                    if(!empty($hawb)) {
                        $orderReferenceString[] .= "'" . trim($hawb) . "'";
                    } else {
                        $warnings[] = 'order_reference[' . $key . '] is invalid';
                    }
                }
                $shipmentData->addFilterNew(' (c.hawb IN (' . implode(',', $orderReferenceString) . '))');
            }
        }
        if(!empty($trackingNumber)){
            if(is_array($trackingNumber))
            {
                $trackingString = [];
                foreach ($trackingNumber as $key => $tracking) {
                    if(!empty($tracking)) {
                        $trackingString[] .= "'" . trim($tracking) . "'";
                    } else {
                        $warnings[] = 'tracking_number[' . $key . '] is invalid';
                    }
                }
                $shipmentData->addFilterNew(' (c.awb IN (' . implode(',', $trackingString) . '))');
            }
        }
        /* Add filter */
        $createdDateFrom = isset($shipmentReqData['date_from']) ? $shipmentReqData['date_from'] . ' 00:00:00': "";
        $createdDateTo = isset($shipmentReqData['date_to']) ? $shipmentReqData['date_to'] . ' 23:59:59' : date('Y-m-d H:i:s');
        if(!empty($createdDateFrom) && !empty($createdDateTo)) {
            $createdDateFrom = date_format(date_create_from_format('Y-m-d H:i:s', $createdDateFrom), 'Y-m-d H:i:s');
            $createdDateTo = date_format(date_create_from_format('Y-m-d H:i:s', $createdDateTo), 'Y-m-d H:i:s');
            $shipmentData->addFilterNew('    (DATE(c.date_created) BETWEEN "' . $createdDateFrom . '" AND "' . $createdDateTo .'") ');
        }
        $city = isset($shipmentReqData['city']) ? $shipmentReqData['city'] : "";
        if(!empty($city)){
            $shipmentData->addFilterNew('    c.city like "%' . $city . '%"');
        }
        $country = isset($shipmentReqData['country']) ? $shipmentReqData['country'] : "";
        if(!empty($country)){
            $shipmentData->addFilterNew('   co.iso = "' . $country . '"');
        }
        $consignmentStatusCode = isset($shipmentReqData['consignment_status_code']) ? $shipmentReqData['consignment_status_code'] : "";
        if(!empty($consignmentStatusCode)){
            $shipmentData->addFilterNew('    c.shipment_status = ' . $consignmentStatusCode );
        }
        $shipmentData->addFilterNew('c.user_id IN ( ' . $userAccounts . ')');
        $shipmentData->setRowsPerPage($limit);
        $shipmentData->setOffset($offset);
        $shipmentData->addJoin('    country co ','co.id = c.country_id', 'left');
        $shipmentData->addJoin(' user u', 'c.user_id = u.id');
        $shipmentData->addJoin(' services s', 'c.service_id = s.id');
        $shipmentData = $shipmentData->getListDynamic('*, c.id as c_id, s.name as service_name, s.code as service_code, co.name as country');
        $shipmentDataCount = new \ConsignmentFilter();
        $shipmentDataCount = $shipmentDataCount->getShipmentPagingCountNew();
        $lastPage = (ceil($shipmentDataCount / $limit) == 0 ? 1 : ceil($shipmentDataCount / $limit));

        $shipmentDataCollection = [];
        if (!empty($shipmentData)) {
            foreach ($shipmentData as $shipment) {
                $foundHawb[] = $shipment->getHawb();
                $foundAwb[] = $shipment->getAwb();
                $shippemntParcels = new \ParcelFilter();
                $shippemntParcels->addFieldFilter('consignment_id', $shipment->getCId());
                $shippemntParcels = $shippemntParcels->getList();
                $consignmentParcels = [];
                if (!empty($shippemntParcels)) {
                    foreach ($shippemntParcels as $shippemntParcel) {
                        $consignmentParcels[] = [
                            'tracking_number' => $shippemntParcel->getTrackingNumber(),
                            'length' => $shippemntParcel->getLength(),
                            'width' => $shippemntParcel->getWidth(),
                            'height' => $shippemntParcel->getHeight(),
                            'weight' => $shippemntParcel->getWeight(),
                        ];
                    }
                }
                $shipmentDataCollection[] = [
                    "created_date" => $shipment->getDateCreated(),
                    "created_by" => $shipment->getFirstName() . " " . $shipment->getLastName(),
                    "order_reference" => $shipment->getHawb(),
                    "tracking_number" => $shipment->getAwb(),
                    "receiver_company" => $shipment->getCompany(),
                    "receiver_contact" => $shipment->getContact(),
                    "receiver_address_line1" => $shipment->getAddressLine1(),
                    "receiver_address_line2" => $shipment->getAddressLine2(),
                    "receiver_address_line3" => $shipment->getAddressLine3(),
                    "receiver_city" => $shipment->getCity(),
                    "receiver_country" => $shipment->getCountry(),
                    "receiver_postcode" => $shipment->getPostcode(),
                    "receiver_telephone" => $shipment->getTelephone(),
                    "sender_company" => $shipment->getSenderCompany(),
                    "sender_contact" => $shipment->getSenderEmail(),
                    "sender_address_line1" => $shipment->getSenderAddressLine1(),
                    "sender_address_line2" => $shipment->getSenderAddressLine2(),
                    "sender_address_line3" => $shipment->getSenderAddressLine3(),
                    "sender_city" => $shipment->getSenderCity(),
//                    "sender_country" => $shipment->getSenderCountry(),
                    "sender_postcode" => $shipment->getSenderPostcode(),
                    "sender_telephone" => $shipment->getSenderTelephone(),
//                    "sender_country_iso" => $shipment->GB,
//                    "sender_contact" => $shipment->getNajam,
//                    "sender_email" => $shipment->gethadi,
//                    "sender_company" => $shipment->getASD,
//                    "sender_address_line_1" => $shipment->getxx,
//                    "sender_address_line_2" => $shipment->get,
//                    "sender_address_line_3" => $shipment->get,
//                    "sender_city" => $shipment->getlondon,
//                    "sender_state" => $shipment->getLondon,
//                    "sender_postcode" => $shipment->get,
//                    "sender_telephone" => $shipment->get,
//                    "receiver_country_iso" => $shipment->get,
//                    "receiver_contact" => $shipment->getNajam,
//                    "receiver_email" => $shipment->get,
//                    "receiver_company" => $shipment->get,
//                    "receiver_address_line_1" => $shipment->get,
//                    "receiver_address_line_2" => $shipment->get,
//                    "receiver_address_line_3" => $shipment->get,
//                    "receiver_city" => $shipment->get,
//                    "receiver_state" => $shipment->get,
//                    "receiver_postcode" => $shipment->get,
//                    "receiver_telephone" => $shipment->get3214518055,
                    "weight" => $shipment->getWeight(),
//                    "DateImported" => null,
                    "consignment_status" => $shipment->getConsignmentStatus(),
//                    "ServiceType" => null,
                    "label_url" => SETTING_URL_LABEL . $shipment->getLabelFile(),
                    "service_code" => $shipment->getServiceCode(),
                    "service_name" => $shipment->getServiceName(),
                    "parcels" => $consignmentParcels
                ];
            }
        }
        $shipmentResponse = [];
        if(!empty($shipmentDataCollection)) {
            foreach ($orderReference as $hawbId) {
                if(!empty($hawbId)) {
                    if(!in_array($hawbId, $foundHawb)) {
                        $warnings[] = 'order_reference ' . $hawbId . ' not found.';
                    }
                }
            }
            foreach ($trackingNumber as $trackingFoundId) {
                if(!empty($trackingFoundId)) {
                    if(!in_array($trackingFoundId, $foundAwb)) {
                        $warnings[] = 'tracking_number ' . $trackingFoundId . ' not found.';
                    }
                }
            }
            $shipmentResponse['STATUS'] = 'SUCCESS';
            $shipmentResponse['MESSAGE'] = 'Records Found';
            $shipmentResponse['ERROR'] = $warnings;
            $shipmentResponse['TOTAL'] = $shipmentDataCount;
            $shipmentResponse['PER_PAGE'] = $limit;
            $shipmentResponse['CURRENT_PAGE'] = $page;
            $shipmentResponse['LAST_PAGE'] = $lastPage;
            $shipmentResponse['ERROR'] = $warnings;
            $shipmentResponse['data'] = $shipmentDataCollection;
        } else {
            $shipmentResponse['STATUS'] = 'ERROR';
            $shipmentResponse['MESSAGE'] = 'No Records Found.';
            $shipmentResponse['ERROR'] = ['No Records Found.'];
        }
        $tarnsform = new UserShipmentTransformer($request, $response);
        return $tarnsform->transform($shipmentResponse);
    }
}
