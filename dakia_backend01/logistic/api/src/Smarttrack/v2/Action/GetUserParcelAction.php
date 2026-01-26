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
use SmarttrackTransformer\UserParcelTransformer;

class GetUserParcelAction
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
        $trackingId = $trackingReqData['tracking_number'];
        $offset = 0;
        if (!empty($trackingReqData['page_number']) && $trackingReqData['page_number'] > 1) {
            $offset = ((int)$trackingReqData['page_number']) - 1;
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

        if (empty($trackingId)) {
            $problem = new ApiProblem(
                'Could not find Tacking',
                'http://zenddesk.smarttrack.com',
                404
            );
            throw new ProblemException($problem);
        }
//	    echo "<pre>"; print_r($apiUserData); echo "</pre>"; die();
        $trackingId = "'" . implode("','", $trackingId) . "'";
        $userAccounts = ApiFunctions::getAccountUsers($apiUserData->getUserAccountId());
        $userParcelData = new \ParcelFilter();
        $userParcelData->addConsignmentTableJoin('INNER');
//        $userParcelData->addFieldFilter('join_con.user_id', $apiUserData->getId());
        $userParcelData->addLicensePlateFilterIn($trackingId);
        $userParcelData->addUserIdsInFilter($userAccounts);
        $userParcelData = $userParcelData->getColumnList('*');
        $consignmentParcels = [];
        if (!empty($userParcelData)) {
            foreach ($userParcelData as $tackingData) {
                $consignmentParcels['data'][] = [
                    'tracking_number' => $tackingData->getTrackingNumber(),
                    'length' => $tackingData->getLength(),
                    'width' => $tackingData->getWidth(),
                    'height' => $tackingData->getHeight(),
                    'weight' => $tackingData->getWeight(),
                ];
            }
        }
        $transformer = new UserParcelTransformer();
        $hal = $transformer->transformParcelCollection($consignmentParcels);
        $resultData = $this->renderer->render($request, $response, $hal);
        ApiFunctions::AddApiLog("get-user-parcel", $trackingReqData,$resultData ,$userId);
        return $resultData;
    }
}
