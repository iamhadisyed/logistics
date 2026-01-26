<?php

namespace Smarttrack\V2\Action;

use Error\ApiProblem;
use Error\Exception\ProblemException;
use Monolog\Logger;
use RKA\ContentTypeRenderer\HalRenderer;
use Smarttrack\V2\ApiFunctions;
use SmarttrackTransformer\V2\VoidLabelTransformer;
use Zend\InputFilter\Factory as InputFilterFactory;
use Smarttrack\V2\SmartAction;

class VoidLabelAction extends SmartAction {

    protected $logger;
    protected $renderer;

    public function __construct(Logger $logger, HalRenderer $renderer) {
        $this->logger = $logger;
        $this->renderer = $renderer;
    }

    public function __invoke($request, $response) {
        $userId = $request->getAttribute('api_user_id');
        $this->logger->info("Void Label for User - ", ['user_account_id' => $userId]);
        $apiUserData = ApiFunctions::getUserById($userId);


        $saveLog=[];
        $saveLog['api']="VoidLabelAction";
        $fileLogDate = date("d-m-Y");
        $saveLog['time'] = date("d-m-Y H:i:s");
        $filePath = SETTING_DIR_ASSETS.'api_log';
        if (!is_dir($filePath)) {
            mkdir($filePath, 0777, true);
        }
        $fileFullName = $filePath."/".$fileLogDate."_api_log.html";

        $abc = false;
        if ($apiUserData->getId() < 1) {
            $errorsArray['STATUS'] = "ERROR";
            $errorsArray['MESSAGE'] = "Could not find user";
            $errorsArray['ERROR'][] = "Could not find user";

            /*
             * Log System
            */
            $shipData = $request->getParams();
            $saveLog['request'] = $shipData;
            $saveLog['response'] = $errorsArray;
            $allFileData = json_encode($saveLog);

            if (!is_dir($filePath)) {
                mkdir($filePath, 0777, true);
            }
            $current = file_get_contents($fileFullName);
            $current .= $allFileData." \n";
            file_put_contents($fileFullName, $current);
            /*
             * End Log System
             */

            $tarnsform = new VoidLabelTransformer($request, $response);
            return $tarnsform->transform($errorsArray);
            die;
        }
        $paramData = $request->getParams();
        if(!is_array($paramData['order_reference']) || count($paramData['order_reference']) == 0){
            $errorsArray['STATUS'] = "ERROR";
            $errorsArray['MESSAGE'] = "Please enter valid order_reference";
            $errorsArray['ERROR'][] = "Please enter valid order_reference";

            /*
             * Log System
            */
            $shipData = $request->getParams();
            $saveLog['request'] = $shipData;
            $saveLog['response'] = $errorsArray;
            $allFileData = json_encode($saveLog);

            if (!is_dir($filePath)) {
                mkdir($filePath, 0777, true);
            }
            $current = file_get_contents($fileFullName);
            $current .= $allFileData." \n";
            file_put_contents($fileFullName, $current);
            /*
             * End Log System
             */

            $tarnsform = new VoidLabelTransformer($request, $response);
            return $tarnsform->transform($errorsArray);
            die;
        }
        $scanningResp = ApiFunctions::voidLabel($paramData);
        /*
        * Log System
        */
        $shipData = $request->getParams();
        $saveLog['request'] = $shipData;
        $saveLog['response'] = $scanningResp;
        $allFileData = json_encode($saveLog);

        if (!is_dir($filePath)) {
            mkdir($filePath, 0777, true);
        }
        $current = file_get_contents($fileFullName);
        $current .= $allFileData." \n";
        file_put_contents($fileFullName, $current);
        /*
         * End Log System
         */
       
        $tarnsform = new VoidLabelTransformer($request, $response);
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
            'hawb' => [
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
