<?php
namespace Smarttrack\V2\Action;

use Error\ApiProblem;
use Error\Exception\ProblemException;
use Monolog\Logger;
use RKA\ContentTypeRenderer\HalRenderer;
use Smarttrack\V2\ApiFunctions;
use SmarttrackTransformer\V2\TrackingDataTransformer;
use Smarttrack\V2\SmartAction;

class GetTrackingAction  extends SmartAction {
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
            $errors = ['Please add Tracking Number.'];
            $labelResponse['STATUS'] = "ERROR";
            $labelResponse['ERROR'] = $errors;
            $labelResponse['MESSAGE'] = "Please fix below error(s)";

            $tarnsform = new TrackingDataTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
        }
	    if(strlen($trackingId) > 32) {
            $message = 'tracking number length is greater than maximum character limit. Maximum character limit is  32';
            $labelResponse['STATUS'] = "ERROR";
            $labelResponse['ERROR'] = [$message];
            $labelResponse['MESSAGE'] = $message;

            $tarnsform = new TrackingDataTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
        }
	    $trackingData = ApiFunctions::getTracking($trackingId);
	    if(isset($trackingData['status']) && $trackingData['status'] == 'error'){
            $trackingData['STATUS'] = 'ERROR';
            $trackingData['MESSAGE'] = $trackingData['message'];

        }else if(isset($trackingData['status']) && $trackingData['status'] == 'success'){
            $trackingData['STATUS'] = 'SUCCESS';
            $trackingData['MESSAGE'] = $trackingData['message'];
        }
        $tarnsform = new TrackingDataTransformer($request, $response);
        return $tarnsform->transform($trackingData);
    }
}
