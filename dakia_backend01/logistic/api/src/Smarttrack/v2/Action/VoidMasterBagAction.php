<?php
namespace Smarttrack\V2\Action;

use Error\ApiProblem;
use Error\Exception\ProblemException;
use Monolog\Logger;
use RKA\ContentTypeRenderer\HalRenderer;
use Smarttrack\V2\ApiFunctions;
use SmarttrackTransformer\V2\VoidMasterBagTransformer;
use Smarttrack\V2\SmartAction;

class VoidMasterBagAction extends SmartAction {
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

            $tarnsform = new VoidMasterBagTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
        }
        if(!is_array($data) && empty($data)){
            $errors = ['Api data is invalid.'];
            $labelResponse['STATUS'] = "ERROR";
            $labelResponse['ERROR'] = $errors;
            $labelResponse['MESSAGE'] = "Please fix below error(s)";

            $tarnsform = new VoidMasterBagTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
        }
        $validateData = ApiFunctions::validateVoidMasterBagParams($data);
        if(!empty($validateData)){
            $errors = $validateData;
            $labelResponse['STATUS'] = "ERROR";
            $labelResponse['ERROR'] = $errors;
            $labelResponse['MESSAGE'] = "Please fix below error(s)";

            $tarnsform = new VoidMasterBagTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
        }
        $baggingErrors = ApiFunctions::checkMawbForVoidBag($data, $apiUserData);
        if(!empty($baggingErrors)){
            $errors = $baggingErrors;
            $labelResponse['STATUS'] = "ERROR";
            $labelResponse['ERROR'] = $errors;
            $labelResponse['MESSAGE'] = "Please fix below error(s)";

            $tarnsform = new VoidMasterBagTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
        }
	    $baggingErrors = ApiFunctions::checkBagNumbersForVoidBag($data, $apiUserData);
        if(!empty($baggingErrors['errors']) && count($data['bags_numbers']) == count($baggingErrors['errors'])){
            $labelResponse['STATUS'] = "ERROR";
            $labelResponse['ERROR'] = $baggingErrors['errors'];
            $labelResponse['MESSAGE'] = "Please fix below error(s)";

            $tarnsform = new VoidMasterBagTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
        }
        $voidMawbBag = ApiFunctions::voidMawbBagging($data, $baggingErrors['bag_ids']);
        $voidMawbBag['ERROR'] = $baggingErrors['errors'];
        $tarnsform = new VoidMasterBagTransformer($request, $response);
        return $tarnsform->transform($voidMawbBag);
    }
}
