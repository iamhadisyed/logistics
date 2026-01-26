<?php

namespace Smarttrack\V2\Action;


use Monolog\Logger;
use RKA\ContentTypeRenderer\HalRenderer;
use Smarttrack\V2\ApiFunctions;
use SmarttrackTransformer\V2\ImportBoxTransformer;
use Smarttrack\V2\SmartAction;

class ImportBoxAction extends SmartAction {

    protected $logger;
    protected $renderer;

    public function __construct(Logger $logger, HalRenderer $renderer) {
        $this->logger = $logger;
        $this->renderer = $renderer;
    }

    public function __invoke($request, $response) {
        $userId = $request->getAttribute('api_user_id');
        $this->logger->info("Import Data for Box - ", ['user_account_id' => $userId]);
        $apiUserData = ApiFunctions::getUserById($userId);
        $abc = false;
        if ($apiUserData->getId() < 1) {
            $errorsArray['STATUS'] = "ERROR";
            $errorsArray['MESSAGE'] = "Could not find user";
            $errorsArray['ERROR'][] = "Could not find user";
            $tarnsform = new ImportBoxTransformer($request, $response);
            return $tarnsform->transform($errorsArray);
            die;
        }
        $paramData = $request->getParams();
        // Validate here
        $validateImportData = ApiFunctions::validateImportBoxParams($paramData);
        
        if ($validateImportData) {
            $errors = $validateImportData;
            $labelResponse['STATUS'] = "ERROR";
            $labelResponse['ERROR'] = $errors;
            $labelResponse['MESSAGE'] = "Please fix below error(s)";

            $tarnsform = new ImportBoxTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
        }
        
        $scanningResp = ApiFunctions::importBox($paramData,$apiUserData);
//print_r($validateImportData);
//print_r($scanningResp);
  //      die;
        $tarnsform = new ImportBoxTransformer($request, $response);
        
        return $tarnsform->transform($scanningResp);
    }

}
