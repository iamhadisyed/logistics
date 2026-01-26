<?php
namespace Smarttrack\V2\Action;

use Error\ApiProblem;
use Error\Exception\ProblemException;
use Monolog\Logger;
use RKA\ContentTypeRenderer\HalRenderer;
use Smarttrack\V2\ApiFunctions;
use SmarttrackTransformer\V2\ParcelWeightUpdateTransformer;
use Zend\InputFilter\Factory as InputFilterFactory;
use Smarttrack\V2\SmartAction;

class ParcelWeightUpdateAction extends SmartAction {

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
            $tarnsform = new ParcelWeightUpdateTransformer($request, $response);
            return $tarnsform->transform($errorsArray);
            die;
        }
        $paramData = $request->getParams();
        if(!isset($paramData['tracking_number'])){
            $errorsArray['STATUS'] = "ERROR";
            $errorsArray['MESSAGE'] = "Please enter valid tracking_number";
            $errorsArray['ERROR'][] = "Please enter valid tracking_number";
            $tarnsform = new ParcelWeightUpdateTransformer($request, $response);
            return $tarnsform->transform($errorsArray);
            die;
        }
        if(!isset($paramData['weight'])) {
            $errorsArray['STATUS'] = "ERROR";
            $errorsArray['MESSAGE'] = "Please enter valid weight";
            $errorsArray['ERROR'][] = "Please enter valid weight";
            $tarnsform = new ParcelWeightUpdateTransformer($request, $response);
            return $tarnsform->transform($errorsArray);
            die;
        }
        $paramKeys = array_keys($paramData);
        $req = array ('tracking_number','weight');
        $result = array_diff($paramKeys, $req);
        if(count($result) > 0) {
            $errorsArray['STATUS'] = "ERROR";
            $errorsArray['MESSAGE'] = "Please fix below error(s)";
            foreach ($result as $item) {
                $errorsArray['ERROR'][] = $item. '  is invalid parameter ';
            }
            $tarnsform = new ParcelWeightUpdateTransformer($request, $response);
            return $tarnsform->transform($errorsArray);
            die;
        }
        $retutnResp = ApiFunctions::updateParcelWeight($paramData);
        $tarnsform = new ParcelWeightUpdateTransformer($request, $response);
        return $tarnsform->transform($retutnResp);
    }
}
