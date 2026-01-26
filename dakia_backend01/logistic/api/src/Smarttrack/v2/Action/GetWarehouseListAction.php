<?php
namespace Smarttrack\Action;

use Error\ApiProblem;
use Error\Exception\ProblemException;
use Monolog\Logger;
use RKA\ContentTypeRenderer\ApiProblemRenderer;
use RKA\ContentTypeRenderer\HalRenderer;
use Smarttrack\ApiFunctions;
use SmarttrackTransformer\QuotationsTransformer;
use SmarttrackTransformer\UserAccessabilitiesTransformer;
use SmarttrackTransformer\UserAccountTransformer;
use SmarttrackTransformer\WareHouseListTransformer;
use UserServicesRouting;
use Zend\InputFilter\Factory as InputFilterFactory;

class GetWarehouseListAction
{
    protected $logger;
    protected $renderer;
    protected $authorMapper;
	protected $userServiceRoutingMapper;

    public function __construct(Logger $logger, HalRenderer $renderer)
    {
        $this->logger = $logger;
        $this->renderer = $renderer;
    }

    public function __invoke($request, $response)
    {
	    $userId = $request->getAttribute('api_user_id');
	    $this->logger->info("Creating Account For User - ", ['user_account_id' => $userId]);
	    $apiUserData = ApiFunctions::getUserById($userId);
        if($apiUserData->getId() < 1){
	        $problem = new ApiProblem(
                'Could not find user',
                'http://zenddesk.smarttrack.com',
                404
            );
            throw new ProblemException($problem);
        }
	    $wareHouseList = ApiFunctions::GetWarehouseList($apiUserData);
//echo "<pre>"; print_r($wareHouseList); echo "</pre>"; die();
        $transformer = new WareHouseListTransformer();
        $hal = $transformer->transformCollection($wareHouseList);
        $resultData = $this->renderer->render($request, $response, $hal);
        ApiFunctions::AddApiLog("get-warehouse-list", $apiUserData,$resultData ,$userId);
        return $resultData;
    }
}
