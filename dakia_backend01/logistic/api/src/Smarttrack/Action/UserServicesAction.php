<?php
namespace Smarttrack\Action;

use Error\ApiProblem;
use Error\Exception\ProblemException;
use Monolog\Logger;
use RKA\ContentTypeRenderer\ApiProblemRenderer;
use RKA\ContentTypeRenderer\HalRenderer;
use Smarttrack\ApiFunctions;
use SmarttrackTransformer\ServicesTransformer;
use UserServicesRouting;

class UserServicesAction
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
	    $this->logger->info("Getting tariff for user", ['user_account_id' => $userId]);
	    $userData = ApiFunctions::getUserById($userId);
        if($userData->getId() < 1){
	        $problem = new ApiProblem(
                'Could not find user',
                'http://zenddesk.smarttrack.com',
                404
            );
            throw new ProblemException($problem);
        }
        $param = $request->getParams();
        $errosArray = [];
        $serviceId = '';
        if(isset($param['service_code'])){
            $serviecCode = $param['service_code'];
            $serviceObj = new \Services();
            $serviceObj = $serviceObj->getServiceByCode($serviecCode);            
            if(!empty($serviceObj)){
                $serviceId = $serviceObj->getId();
                $shipData['service_type']= $serviceId;                
            }else{
                $errosArray['STATUS'] = "ERROR";
                $errosArray['MESSAGE'] = "Invalid Service Code";
                $errosArray['ERROR']= "serviceCode $serviceCode is invalid.System cannot find any service for the specified service code";
                $transformer = new ServicesTransformer();
                $hal = $transformer->transformCollection($errosArray);
                $resultData = $this->renderer->render($request, $response, $hal);
                ApiFunctions::AddApiLog("get-services", $param,$resultData ,$userId);
                return $resultData;
            }
        }        
        $services = ApiFunctions::getUserServices($userData->getUserAccountId(),$serviceId);
//	echo "<pre>"; print_r($services); echo "</pre>"; die();
        $transformer = new ServicesTransformer();
        $hal = $transformer->transformCollection($services);
        $resultData = $this->renderer->render($request, $response, $hal);
        ApiFunctions::AddApiLog("get-services", $param,$resultData ,$userId);
        return $resultData;
    }
}
