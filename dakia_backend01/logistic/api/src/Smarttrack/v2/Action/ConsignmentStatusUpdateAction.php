<?php
namespace Smarttrack\V2\Action;

use Monolog\Logger;
use RKA\ContentTypeRenderer\HalRenderer;
use Smarttrack\V2\ApiFunctions;
use SmarttrackTransformer\V2\ConsignmentStatusUpdateTransformer;
use Smarttrack\V2\SmartAction;

class ConsignmentStatusUpdateAction extends SmartAction {

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
            $tarnsform = new ConsignmentStatusUpdateTransformer($request, $response);
            return $tarnsform->transform($errorsArray);
            die;
        }
        $paramData = $request->getParams();
        $validateData = ApiFunctions::validateConStatusUpdateParams($paramData);
        if(!empty($validateData)){
            $errors = $validateData;
            $labelResponse['STATUS'] = "ERROR";
            $labelResponse['ERROR'] = $errors;
            $labelResponse['MESSAGE'] = "Please fix below error(s)";

            $tarnsform = new ConsignmentStatusUpdateTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
        }
        $updateStatusResp = ApiFunctions::consignmentsStatusUpdate($paramData);
        $tarnsform = new ConsignmentStatusUpdateTransformer($request, $response);
        return $tarnsform->transform($updateStatusResp);
    }
}
