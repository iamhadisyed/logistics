<?php
namespace Smarttrack\V2\Action;

use Monolog\Logger;
use RKA\ContentTypeRenderer\HalRenderer;
use Smarttrack\V2\ApiFunctions;
use SmarttrackTransformer\V2\UserAccountTransformer;
use Smarttrack\V2\SmartAction;
class CreateAccountAction extends SmartAction {
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
	    $this->logger->info("Creating Account For User - ", ['user_account_id' => $userId]);
	    $apiUserData = ApiFunctions::getUserById($userId);
        if($apiUserData->getId() < 1){
            $errors = ['User does not exist.'];
            $labelResponse['STATUS'] = "ERROR";
            $labelResponse['ERROR'] = $errors;
            $labelResponse['MESSAGE'] = "Please fix below error(s)";

            $tarnsform = new UserAccountTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
        }
	    //validating quotations data
	    $accountData = $request->getParams();
        $validateScannigData = ApiFunctions::validateCreateUserParams($accountData);
        if ($validateScannigData) {
            $errors = $validateScannigData;
            $labelResponse['STATUS'] = "ERROR";
            $labelResponse['ERROR'] = $errors;
            $labelResponse['MESSAGE'] = "Please fix below error(s)";

            $tarnsform = new UserAccountTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
        }
	    $quotations = ApiFunctions::createUserAccount($accountData,$apiUserData);
        $transformer = new UserAccountTransformer($request, $response);
        $hal = $transformer->transform($quotations);
        $resultData = $this->renderer->render($request, $response, $hal);
        ApiFunctions::AddApiLog("create-account", $accountData,$resultData ,$userId);
        return $resultData;
    }
}
