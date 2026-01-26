<?php
namespace Smarttrack\V2\Action;

use Error\ApiProblem;
use Error\Exception\ProblemException;
use Monolog\Logger;
use RKA\ContentTypeRenderer\HalRenderer;
use Smarttrack\V2\ApiFunctions;
use SmarttrackTransformer\V2\RestoreLabelTransformer;
use Zend\InputFilter\Factory as InputFilterFactory;
use Smarttrack\V2\SmartAction;

class RestoreLabelAction extends SmartAction {

    protected $logger;
    protected $renderer;

    public function __construct(Logger $logger, HalRenderer $renderer) {
        $this->logger = $logger;
        $this->renderer = $renderer;
    }

    public function __invoke($request, $response) {
        $userId = $request->getAttribute('api_user_id');
        $this->logger->info("Restore Label for User - ", ['user_account_id' => $userId]);
        $apiUserData = ApiFunctions::getUserById($userId);
        if ($apiUserData->getId() < 1) {
            $errorsArray['STATUS'] = "ERROR";
            $errorsArray['MESSAGE'] = "Could not find user";
            $errorsArray['ERROR'][] = "Could not find user";
            $tarnsform = new RestoreLabelTransformer($request, $response);
            return $tarnsform->transform($errorsArray);
            die;
        }
        $paramData = $request->getParams();
        if(!is_array($paramData['order_reference']) && count($paramData['order_reference']) == 0){
            $errorsArray['STATUS'] = "ERROR";
            $errorsArray['MESSAGE'] = "Please enter valid order_reference";
            $errorsArray['ERROR'][] = "Please enter valid order_reference";
            $tarnsform = new RestoreLabelTransformer($request, $response);
            return $tarnsform->transform($errorsArray);
            die;
        }
        
        $paramsValidation = ApiFunctions::validateRestoreLabelParams($paramData);
        if(!empty($paramsValidation)) {
            $errors = $paramsValidation;
            $responseData['STATUS'] = "ERROR";
            $responseData['ERROR'] = $errors;
            $responseData['MESSAGE'] = "Please fix below error(s)";
            $tarnsform = new RestoreLabelTransformer($request, $response);
            return $tarnsform->transform($responseData);
        }
        
        $scanningResp = ApiFunctions::restoreLabel($paramData);
        $tarnsform = new RestoreLabelTransformer($request, $response);
        return $tarnsform->transform($scanningResp);
    }
}
