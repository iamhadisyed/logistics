<?php 
namespace Smarttrack\Action;

use Error\ApiProblem;
use Error\Exception\ProblemException;
use Monolog\Logger;
use RKA\ContentTypeRenderer\ApiProblemRenderer;
use RKA\ContentTypeRenderer\HalRenderer;
use Smarttrack\ApiFunctions;
use SmarttrackTransformer\ServicesTransformer;
use SmarttrackTransformer\TrackingDataTransformer;
use UserServicesRouting;
use SmarttrackTransformer\GetDropOffLocationTransformer;

class GetDropOffLocationAction {

    protected $logger;
    protected $renderer;

    public function __construct(Logger $logger, HalRenderer $renderer) {
        $this->logger = $logger;
        $this->renderer = $renderer;
    }

    public function __invoke($request, $response) {
        $userId = $request->getAttribute('api_user_id');
        $this->logger->info("Add Shipment For User ", ['api_user_id' => $userId]);
        $userData = ApiFunctions::getUserById($userId);
        if($userData->getId() < 1){
                $problem = new ApiProblem(
                        'Could not find user',
                        'http://zenddesk.smarttrack.com',
                        404
                );
                throw new ProblemException($problem);
        }
        $params = $request->getParams();
        $postcode = $params['postcode'];
        $dropOffLocationData = ApiFunctions::getDropOffLocation($postcode);
        $transformer = new GetDropOffLocationTransformer();
        $hal = $transformer->transformCollection($dropOffLocationData);
        $resultData = $this->renderer->render($request, $response, $hal);
        ApiFunctions::AddApiLog("get-drop-off-locations", $params,$resultData ,$userId);
        return $resultData;
        return $this->renderer->render($request, $response, $hal);
    }

}
