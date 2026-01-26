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
use UserServicesRouting;
use Zend\InputFilter\Factory as InputFilterFactory;

class GetUserAccessabilitiesAction
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
        $paramData = $request->getParams();
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
	    $userAccessabilities = ApiFunctions::getUserAccessabilities($apiUserData);
        $transformer = new UserAccessabilitiesTransformer();
        $hal = $transformer->transformCollection($userAccessabilities);
        $resultData = $this->renderer->render($request, $response, $hal);
        ApiFunctions::AddApiLog("get-accessabilities", $paramData,$resultData ,$userId);
        return $resultData;
    }
}
