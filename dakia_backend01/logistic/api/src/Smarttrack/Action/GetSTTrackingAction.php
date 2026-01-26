<?php
namespace Smarttrack\Action;

use Error\ApiProblem;
use Error\Exception\ProblemException;
use Monolog\Logger;
use RKA\ContentTypeRenderer\ApiProblemRenderer;
use RKA\ContentTypeRenderer\HalRenderer;
use Smarttrack\ApiFunctions;
use SmarttrackTransformer\STTrackingTransformer;
use UserServicesRouting;
use Zend\InputFilter\Factory as InputFilterFactory;

class GetSTTrackingAction
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
        $trackingReqData = $request->getParams();
        $trackingId = $trackingReqData['tracking_number'];
	    $this->logger->info("Getting tracking for", ['tracking_id' => $trackingId]);
	    $userData = ApiFunctions::getUserById($userId);
            if ($userData->getId() < 1) {
                $problem = new ApiProblem(
                        'Could not find user', 'http://zenddesk.smarttrack.com', 404
                );
                throw new ProblemException($problem);
            }
            $validateTrackingData = $this->validate($trackingReqData, ['transaction_id', 'metadata', 'tracking_number', 'is_return']);
            $trackingData = ApiFunctions::getTracking($trackingId);
            $transformer = new STTrackingTransformer();

            $hal = $transformer->transformCollection($trackingData);
            $resultData = $this->renderer->render($request, $response, $hal);
            ApiFunctions::AddApiLog("Track", $trackingReqData,$resultData ,$userId);
            return $resultData;
    }
    public function validate($data, $elements = []) {
        $inputFilter = $this->createInputFilter($elements);
        $inputFilter->setData($data);

        if ($inputFilter->isValid()) {
            return $inputFilter->getValues();
        }

        $problem = new ApiProblem(
                'Validation failed', 'about:blank', 400
        );
        $problem['errors'] = $inputFilter->getMessages();

        throw new ProblemException($problem);
    }

    protected function createInputFilter($elements = []) {
        $specification = [
            'transaction_id' => [
                'required' => false,
                'filters' => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags'],
                ]
            ],
            'tracking_number' => [
                'required' => true,
                'filters' => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags'],
                ]
            ]
        ];
        if ($elements) {
            $specification = array_filter(
                    $specification, function ($key) use ($elements) {
                return in_array($key, $elements);
            }, ARRAY_FILTER_USE_KEY
            );
        }
        $factory = new InputFilterFactory();
        $inputFilter = $factory->createInputFilter($specification);

        return $inputFilter;
    }
}
