<?php
namespace Smarttrack\V2\Action;

use Monolog\Logger;
use RKA\ContentTypeRenderer\HalRenderer;
use Smarttrack\V2\ApiFunctions;
use SmarttrackTransformer\V2\CurrencyConverterTransformer;
use Smarttrack\V2\SmartAction;

class CurrencyConverterAction extends SmartAction {
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
	    $this->logger->info("currency converter - ", ['user_account_id' => $userId]);
	    $apiUserData = ApiFunctions::getUserById($userId);
        if($apiUserData->getId() < 1){
            $errors = ['User does not exist.'];
            $labelResponse['STATUS'] = "ERROR";
            $labelResponse['ERROR'] = $errors;
            $labelResponse['MESSAGE'] = "Please fix below error(s)";

            $tarnsform = new CurrencyConverterTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
        }
	    //validating quotations data
	    $paramData = $request->getParams();
        $validateScannigData = ApiFunctions::validateCurrencyConverterParams($paramData);
        if ($validateScannigData) {
            $errors = $validateScannigData;
            $labelResponse['STATUS'] = "ERROR";
            $labelResponse['ERROR'] = $errors;
            $labelResponse['MESSAGE'] = "Please fix below error(s)";

            $tarnsform = new CurrencyConverterTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
        }
        $fromCurrencyCode = $paramData['from_currency_code'];
        $toCurrencyCode = $paramData['to_currency_code'];
        $amount = $paramData['amount'];

	    $quotations = ApiFunctions::currencyConverter($fromCurrencyCode,$toCurrencyCode,$amount);
	    if($quotations > 0){
            $labelResponse['STATUS'] = "SUCCESS";
            $labelResponse['amount'] = $quotations;
            $labelResponse['MESSAGE'] = "Currency converted successfully";
        }else{
            $labelResponse['STATUS'] = "ERROR";
            $labelResponse['ERROR'] = ["Please contact to administrator or try again"];
            $labelResponse['MESSAGE'] = "Please fix below error(s)";
        }
        $transformer = new CurrencyConverterTransformer($request, $response);
        return $transformer->transform($labelResponse);
    }
}
