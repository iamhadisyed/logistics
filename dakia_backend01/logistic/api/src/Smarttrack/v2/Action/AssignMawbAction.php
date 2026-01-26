<?php
namespace Smarttrack\V2\Action;

use Error\ApiProblem;
use Error\Exception\ProblemException;
use Monolog\Logger;
use RKA\ContentTypeRenderer\HalRenderer;
use Smarttrack\V2\ApiFunctions;
use SmarttrackTransformer\V2\AssignMawbTransformer;
use SmarttrackTransformer\V2\MultiTrackingDataTransformer;
use Smarttrack\V2\SmartAction;

class AssignMawbAction  extends SmartAction {
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
        $apiUserData = ApiFunctions::getUserById($userId);
        if ($apiUserData->getId() < 1) {
            $errors = ['User does not exist.'];
            $labelResponse['STATUS'] = "ERROR";
            $labelResponse['ERROR'] = $errors;
            $labelResponse['MESSAGE'] = "Please fix below error(s)";

            $tarnsform = new AssignMawbTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
        }
        if(!is_array($data) && empty($data)){
            $errors = ['Api data is invalid.'];
            $labelResponse['STATUS'] = "ERROR";
            $labelResponse['ERROR'] = $errors;
            $labelResponse['MESSAGE'] = "Please fix below error(s)";

            $tarnsform = new AssignMawbTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
        }
        $validateData = ApiFunctions::validateAssignMawbParams($data);
        if(!empty($validateData)){
            $errors = $validateData;
            $labelResponse['STATUS'] = "ERROR";
            $labelResponse['ERROR'] = $errors;
            $labelResponse['MESSAGE'] = "Please fix below error(s)";

            $tarnsform = new AssignMawbTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
        }
        $baggingErrors = ApiFunctions::checkMawb($data);
        if(!empty($baggingErrors)){
            $errors = $baggingErrors;
            $labelResponse['STATUS'] = "ERROR";
            $labelResponse['ERROR'] = $errors;
            $labelResponse['MESSAGE'] = "Please fix below error(s)";

            $tarnsform = new AssignMawbTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
        }
	    $baggingErrors = ApiFunctions::checkBagNumbers($data['bags_numbers']);
        if(!empty($baggingErrors)){
            $errors = $baggingErrors;
            $labelResponse['STATUS'] = "ERROR";
            $labelResponse['ERROR'] = $errors;
            $labelResponse['MESSAGE'] = "Please fix below error(s)";

            $tarnsform = new AssignMawbTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
        }
        $bagAssignErrors = ApiFunctions::checkIsBagAssignedToMawb($data['bags_numbers']);
        if(empty($bagAssignErrors['bag_ids'])){
            $labelResponse['STATUS'] = "ERROR";
            $labelResponse['ERROR'] = $bagAssignErrors['warnings'];
            $labelResponse['MESSAGE'] = "Please fix below error(s)";
            $tarnsform = new AssignMawbTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
        }
        $parcelAssignErrors = ApiFunctions::checkIsParcelAssignedToMawb($bagAssignErrors['bag_ids']);
        if(empty($parcelAssignErrors['bagging_data'])){
            $labelResponse['STATUS'] = "ERROR";
            $labelResponse['ERROR'] = array_merge($bagAssignErrors['warnings'], $parcelAssignErrors['warnings']);
            $labelResponse['MESSAGE'] = "Please fix below error(s)";

            $tarnsform = new AssignMawbTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
        }
        $assignParcels = ApiFunctions::assignBagging($data, $parcelAssignErrors['bagging_data'], $apiUserData);
        if(!empty($bagAssignErrors['warnings']) || !empty($parcelAssignErrors['warnings'])) {
            $assignParcels['ERROR'] = array_merge($bagAssignErrors['warnings'], $parcelAssignErrors['warnings']);
        }
        $tarnsform = new AssignMawbTransformer($request, $response);
        return $tarnsform->transform($assignParcels);
    }
}
