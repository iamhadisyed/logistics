<?php

namespace Smarttrack\V2\Action;

use Error\ApiProblem;
use Error\Exception\ProblemException;
use Monolog\Logger;
use RKA\ContentTypeRenderer\ApiProblemRenderer;
use RKA\ContentTypeRenderer\HalRenderer;
use Smarttrack\V2\ApiFunctions;
use SmarttrackTransformer\V2\AddScanningTransformer;
use Zend\InputFilter\Factory as InputFilterFactory;
use Smarttrack\V2\SmartAction;

class AddScanningAction extends SmartAction  {

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
            $errors = ['User does not exist.'];
            $labelResponse['STATUS'] = "ERROR";
            $labelResponse['ERROR'] = $errors;
            $labelResponse['MESSAGE'] = "Please fix below error(s)";

            $tarnsform = new AddScanningTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
        }
//	    echo "<pre>"; print_r($apiUserData); echo "</pre>"; die();
        //validating user date
        $scanParcelData = $request->getParams();
        $validateScannigData = ApiFunctions::validateScanParcelParams($scanParcelData);
        if ($validateScannigData) {
            $errors = $validateScannigData;
            $labelResponse['STATUS'] = "ERROR";
            $labelResponse['ERROR'] = $errors;
            $labelResponse['MESSAGE'] = "Please fix below error(s)";

            $tarnsform = new AddScanningTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
        }

        if(!in_array($scanParcelData['status_code'], array_keys(\Tracking::$oneworld_status_code))) {
            $errors = ['status code ['.$scanParcelData['status_code'].'] does not exist.'];
            $labelResponse['STATUS'] = "ERROR";
            $labelResponse['ERROR'] = $errors;
            $labelResponse['MESSAGE'] = "Please fix below error(s)";

            $tarnsform = new AddScanningTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
        }
        $scanningResp = ApiFunctions::addScanning($scanParcelData, $apiUserData);
        $tarnsform = new AddScanningTransformer($request, $response);
        return $tarnsform->transform($scanningResp);
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
