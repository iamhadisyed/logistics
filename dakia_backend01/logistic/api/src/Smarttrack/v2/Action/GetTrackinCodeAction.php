<?php
namespace Smarttrack\Action;

use Error\ApiProblem;
use Error\Exception\ProblemException;
use Monolog\Logger;
use RKA\ContentTypeRenderer\ApiProblemRenderer;
use RKA\ContentTypeRenderer\HalRenderer;
use Smarttrack\ApiFunctions;
use SmarttrackTransformer\TrackinCodeTransformer;
use UserServicesRouting;
use Zend\InputFilter\Factory as InputFilterFactory;

class GetTrackinCodeAction
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
        $trackingCodeList = ApiFunctions::getTrackingStatusCode();
        $userId = $request->getAttribute('api_user_id');
        $data = $request->getParams();
        $transformer = new TrackinCodeTransformer();
        $hal = $transformer->transformCollection($trackingCodeList);
        $resultData = $this->renderer->render($request, $response, $hal);
        ApiFunctions::AddApiLog("get-tracking-code", $data,$resultData ,$userId);
        return $resultData;
    }
}
