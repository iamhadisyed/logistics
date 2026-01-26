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

class TrackingAction
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
        $data = $request->getParams();
	    $trackingId = $request->getAttribute('tracking_id');
	    $this->logger->info("Getting tracking for", ['tracking_id' => $trackingId]);
	    if(!isset($trackingId) || is_null($trackingId) || empty($trackingId)){
		    $problem = new ApiProblem(
			    'Could not find user',
			    'http://zenddesk.smarttrack.com',
			    404
		    );
		    throw new ProblemException($problem);
	    }
	   $trackingData = ApiFunctions::getTracking($trackingId);
       $transformer = new TrackingDataTransformer();
       $hal = $transformer->transformCollection($trackingData);
        $resultData = $this->renderer->render($request, $response, $hal);
        ApiFunctions::AddApiLog("Track", $data,$resultData ,$userId);
        return $resultData;
    }
}
