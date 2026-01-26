<?php
namespace Smarttrack\V2\Action;

use Error\ApiProblem;
use Error\Exception\ProblemException;
use Monolog\Logger;
use RKA\ContentTypeRenderer\HalRenderer;
use Smarttrack\V2\ApiFunctions;
use SmarttrackTransformer\V2\MultiTrackingDataTransformer;
use Smarttrack\V2\SmartAction;

class MultiTrackingAction  extends SmartAction {
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
        $validateScannigData = ApiFunctions::validateMultiTrackingParams($data);
        
        if ($validateScannigData) {
            $errors = $validateScannigData;
            $labelResponse['STATUS'] = "ERROR";
            $labelResponse['ERROR'] = $errors;
            $labelResponse['MESSAGE'] = "Please fix below error(s)";

            $tarnsform = new MultiTrackingDataTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
        }
	    $trackingNumbers = $data['tracking_numbers'];
	    $this->logger->info("Getting tracking for", ['tracking_numbers' => $trackingNumbers]);
	    if(!isset($trackingNumbers) || is_null($trackingNumbers) || empty($trackingNumbers)){
            $errors = ['Please Enter Tracking Number.'];
            $labelResponse['STATUS'] = "ERROR";
            $labelResponse['ERROR'] = $errors;
            $labelResponse['MESSAGE'] = "Please fix below error(s)";

            $tarnsform = new MultiTrackingDataTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
	    }
        if(count($trackingNumbers) > 50){
            $errors = ['Maximum Tracking Numbers limit is 50.'];
            $labelResponse['STATUS'] = "ERROR";
            $labelResponse['ERROR'] = $errors;
            $labelResponse['MESSAGE'] = "Please fix below error(s)";

            $tarnsform = new MultiTrackingDataTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
        }
	    $trackingData = ApiFunctions::getMultiTracking($trackingNumbers);
        $tarnsform = new MultiTrackingDataTransformer($request, $response);
        return $tarnsform->transform($trackingData);
    }
}
