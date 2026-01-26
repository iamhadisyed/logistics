<?php
namespace Smarttrack\V2\Action;

use Error\ApiProblem;
use Error\Exception\ProblemException;
use Monolog\Logger;
use RKA\ContentTypeRenderer\HalRenderer;
use Smarttrack\V2\ApiFunctions;
use SmarttrackTransformer\V2\GetShipmentTransformer;
use Smarttrack\V2\SmartAction;

class GetShipmentAction  extends SmartAction {
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
	    $hawbNumber = $data['order_reference'];
	    $this->logger->info("Getting shipment info of ", ['order_reference' => $hawbNumber]);
	    if(!isset($hawbNumber) || is_null($hawbNumber) || empty($hawbNumber)){
            $errors = ['Order Reference is missing.'];
            $labelResponse['STATUS'] = "ERROR";
            $labelResponse['ERROR'] = $errors;
            $labelResponse['MESSAGE'] = "Please fix below error(s)";

            $tarnsform = new GetShipmentTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
	    }
        $validateScannigData = ApiFunctions::validateGetShipmentParams($data);
        if ($validateScannigData) {
            $errors = $validateScannigData;
            $labelResponse['STATUS'] = "ERROR";
            $labelResponse['ERROR'] = $errors;
            $labelResponse['MESSAGE'] = "Please fix below error(s)";

            $tarnsform = new GetShipmentTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
        }
	    $trackingData = ApiFunctions::getShipmentInfo($hawbNumber);
        $tarnsform = new GetShipmentTransformer($request, $response);
        return $tarnsform->transform($trackingData);
    }
}
