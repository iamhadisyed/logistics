<?php
namespace Smarttrack\V2\Action;

use Error\ApiProblem;
use Error\Exception\ProblemException;
use Monolog\Logger;
use RKA\ContentTypeRenderer\HalRenderer;
use Smarttrack\V2\ApiFunctions;
use SmarttrackTransformer\V2\ServicesTransformer;
use Smarttrack\V2\SmartAction;
class UserServicesAction extends SmartAction {
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
            $errors = ['User does not exist.'];
            $labelResponse['STATUS'] = "ERROR";
            $labelResponse['ERROR'] = $errors;
            $labelResponse['MESSAGE'] = "Please fix below error(s)";

            $tarnsform = new ServicesTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
        }
        $param = $request->getParams();
        $errosArray = [];
        $serviceId = '';
        if(isset($param['service_code'])){
            $serviecCode = $param['service_code'];
            if(strlen($serviecCode) > 9) {
                $errosArray['STATUS'] = "ERROR";
                $errosArray['MESSAGE'] = "Invalid Service Code";
                $errosArray['ERROR']= "service_code $serviecCode length is greater than maximum character limit. Maximum character limit is 9.";
                $transformer = new ServicesTransformer($request, $response);
                return $transformer->transform($errosArray);
            }
            $serviceObj = new \Services();
            $serviceObj = $serviceObj->getServiceByCode($serviecCode);
            if(!empty($serviceObj)){
                $serviceId = $serviceObj->getId();
            }else{
                $errosArray['STATUS'] = "ERROR";
                $errosArray['MESSAGE'] = "Invalid Service Code";
                $errosArray['ERROR']= "service_code $serviecCode is invalid.System cannot find any service for the specified service code";
                $transformer = new ServicesTransformer($request, $response);
                return $transformer->transform($errosArray);
            }
        }
        $services = ApiFunctions::getUserServices($userData->getUserAccountId(),$serviceId);
        if(empty($services)) {
            $errosArray['STATUS'] = "ERROR";
            $errosArray['MESSAGE'] = "Please fix below error.";
            $errosArray['ERROR']= "This service $serviecCode is not assigned to your account.";
            $transformer = new ServicesTransformer($request, $response);
            return $transformer->transform($errosArray);
        }
        $transformer = new ServicesTransformer($request, $response);
        return $transformer->transform($services);
    }
}
