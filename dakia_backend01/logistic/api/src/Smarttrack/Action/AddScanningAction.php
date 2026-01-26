<?php

namespace Smarttrack\Action;

use Error\ApiProblem;
use Error\Exception\ProblemException;
use Monolog\Logger;
use RKA\ContentTypeRenderer\ApiProblemRenderer;
use RKA\ContentTypeRenderer\HalRenderer;
use Smarttrack\ApiFunctions;
use SmarttrackTransformer\AddScanningTransformer;
use Zend\InputFilter\Factory as InputFilterFactory;

class AddScanningAction {

    protected $logger;
    protected $renderer;

    public function __construct(Logger $logger, HalRenderer $renderer) {
        $this->logger = $logger;
        $this->renderer = $renderer;
    }

    public function __invoke($request, $response) {
        $userId = $request->getAttribute('api_user_id');
        $this->logger->info("Scan Parcel For User - ", ['user_account_id' => $userId]);
        $apiUserData = ApiFunctions::getUserById($userId);
        if ($apiUserData->getId() < 1) {
            $problem = new ApiProblem(
                    'Could not find user', 'http://zenddesk.smarttrack.com', 404
            );
            throw new ProblemException($problem);
        }
//	    echo "<pre>"; print_r($apiUserData); echo "</pre>"; die();
        //validating user date
        $scanParcelData = $request->getParams();
        $validateScannigData = $this->validate($scanParcelData, ['tracking_number', 'scan_time', 'status_code', 'ip_address']);
        $scanningResp = ApiFunctions::addScanning($scanParcelData, $apiUserData);
        $transformer = new AddScanningTransformer();
        $hal = $transformer->transformCollection($scanningResp);
        $resultData = $this->renderer->render($request, $response, $hal);
        ApiFunctions::AddApiLog("scan-parcel", $scanParcelData,$resultData ,$userId);
        return $resultData;
    }

    /**
     * Validate data to be applied to this entity
     *
     * @param  array $data
     * @return array
     */
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
            'tracking_number' => [
                'required' => true,
                'filters' => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags'],
                ]
            ],
            'scan_time' => [
				'required' => true,
				'filters' => [
					['name' => 'StringTrim']
				],
				'validators' => [
					[
						'name' => 'Regex',
						'options'=>[
							'pattern'=>'/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/',
							'messages' => array('regexNotMatch' => 'Invalid Date Format')
						],
					]
				]
			],
            'status_code' => [
                'required' => true,
                'filters' => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags'],
                ]
            ],
            'ip_address' => [
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
