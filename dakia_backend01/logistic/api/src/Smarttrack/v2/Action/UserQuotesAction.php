<?php
namespace Smarttrack\V2\Action;

use Error\ApiProblem;
use Error\Exception\ProblemException;
use Monolog\Logger;
use RKA\ContentTypeRenderer\HalRenderer;
use Smarttrack\V2\ApiFunctions;
use SmarttrackTransformer\V2\QuotationsTransformer;
use Zend\InputFilter\Factory as InputFilterFactory;
use Smarttrack\V2\SmartAction;

class UserQuotesAction extends SmartAction {
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
	    $this->logger->info("Getting Quotes", ['user_account_id' => $userId]);
	    $userData = ApiFunctions::getUserById($userId);
        if($userData->getId() < 1){
            $errors = ['User does not exist.'];
            $labelResponse['STATUS'] = "ERROR";
            $labelResponse['ERROR'] = $errors;
            $labelResponse['MESSAGE'] = "Please fix below error(s)";

            $tarnsform = new QuotationsTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
        }
	    //validating quotations data
	    $quoteData = $request->getParams();
        $invalidParams = self::validateQuotesParams($quoteData);
        $errorArr = [];
        if(count($invalidParams) > 0 && isset($invalidParams['missing'])){
            foreach ($invalidParams['missing'] as $missingParam) {
                if(!is_array($missingParam)){
                    $errorArr[] = $missingParam . " is required";
                }
            }
        }
        if(count($invalidParams) > 0 && isset($invalidParams['parcel_weight'])){
            foreach ($invalidParams['parcel_weight'] as $key =>  $parcelWeightParam) {
                if(!is_array($parcelWeightParam)){
                    $errorArr[] = "Weight of parcel ".$key." is required.";
                }
            }
        }
        if(count($invalidParams) > 0 && isset($invalidParams['empty'])){
            foreach ($invalidParams['empty'] as $missingParam) {
                if(!is_array($missingParam)){
                    $errorArr[] = "Please enter valid value for ".$missingParam;
                }
            }
        }
        if(count($invalidParams) > 0 && isset($invalidParams['validation'])){
            foreach ($invalidParams['validation'] as $validationParam) {
                if(!is_array($missingParam)){
                    $errorArr[] = $validationParam;
                }
            }
        }

//        $invalidParamsLength = ApiFunctions::validateQuotesParams($quoteData);
//        if(count($invalidParamsLength) > 0){
//            $labelResponse['STATUS'] = "ERROR";
//            $labelResponse['ERROR'] = $invalidParamsLength;
//            $labelResponse['MESSAGE'] = "Please fix below error(s)";
//
//            $tarnsform = new QuotationsTransformer($request, $response);
//            return $tarnsform->transform($labelResponse);
//        }

        if(count($invalidParams) > 0){
            foreach ($invalidParams as $invalidParam) {
                if(!is_array($invalidParam)){
                    $errorArr[] = $invalidParam." is invalid parameter.";
                }else if(is_array($invalidParam)){
                    foreach ($invalidParam as $key => $parcelParms){
                        foreach ($parcelParms as $parcelParm) {
                            $errorArr[] = "Parcel ".$key." has ".$parcelParm." invalid parameter.";
                        }
                    }
                }
            }
            $labelResponse['STATUS'] = "ERROR";
            $labelResponse['ERROR'] = $errorArr;
            $labelResponse['MESSAGE'] = "Please fix below error(s)";

            $tarnsform = new QuotationsTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
        }
//	    $data = $this->validate($quoteData, ['origin_country_iso', 'delivery_country_iso','weight','number_of_pieces','parcel']);

//	    validate pieces items
//	    $requiredIndexes =['qty','weight','length','width','height'];
//	    $vErrors = [];
//	    if(is_array($quoteData['parcel'])){
//		    foreach($quoteData['parcel'] as $key=>$pieceData){
//			    if(count($pieceData) < count($requiredIndexes)){
//				    $vErrors[] ="parcel $key must have (".implode(",",$requiredIndexes).') details.';
//			    }
//			    else{
//				    $hasRequiredIndexes = UserQuotesAction::array_keys_exists($requiredIndexes,$pieceData);
//				    if(!$hasRequiredIndexes){
//					    $vErrors[] ="parcel $key must have (".implode(",",$requiredIndexes).') details.';
//				    }
//			    }
//		    }
//	    }else{
//		    $vErrors[] ="parcel field type must be array.";
//	    }
//	    if(!empty($vErrors)){
//		    $quotations['STATUS'] = "ERROR";
//		    $quotations['MESSAGE'] = "Pieces field has invalid data";
//		    $quotations['ERROR'][] = $vErrors;
//	    }else{
        $quotations = ApiFunctions::UserQuotes($quoteData,$userData->getUserAccountId());
        if(isset($quotations["ERROR"]) && count($quotations["ERROR"]) > 0){
            $quotations['STATUS'] = "ERROR";
		    $quotations['MESSAGE'] = "Pieces field has invalid data";
        }

//	    }
        $tarnsform = new QuotationsTransformer($request, $response);
        return $tarnsform->transform($quotations);
    }
	/**
	 * Validate data to be applied to this entity
	 *
	 * @param  array $data
	 * @return array
	 */
//	public function validate($data, $elements = [])
//	{
//		$inputFilter = $this->createInputFilter($elements);
//		$inputFilter->setData($data);
//
//		if ($inputFilter->isValid()) {
//			return $inputFilter->getValues();
//		}
//
//		$problem = new ApiProblem(
//			'Validation failed',
//			'about:blank',
//			400
//		);
//		$problem['errors'] = $inputFilter->getMessages();
//
//		throw new ProblemException($problem);
//	}
//	protected function createInputFilter($elements = [])
//	{
//		$specification = [
//			'origin_country_iso' => [
//				'required' => true,
//				'filters' => [
//					['name' => 'StringTrim'],
//					['name' => 'StripTags'],
//				]
//			],
//			'delivery_country_iso' => [
//				'required' => true,
//				'filters' => [
//					['name' => 'StringTrim'],
//					['name' => 'StripTags'],
//				]
//			],
//			'weight' => [
//				'required' => true,
//				'validators' => [
//					['name' => 'Float']
//				]
//			],
//			'number_of_pieces' => [
//				'required' => true,
//				'validators' => [
//					['name' => 'Float']
//				]
//			],
//			'parcel' => [
//				'required' => true
//			]
//		];
//
//		if ($elements) {
//			$specification = array_filter(
//				$specification,
//				function ($key) use ($elements) {
//					return in_array($key, $elements);
//				},
//				ARRAY_FILTER_USE_KEY
//			);
//		}
//
//		$factory = new InputFilterFactory();
//		$inputFilter = $factory->createInputFilter($specification);
//
//		return $inputFilter;
//	}
	public static  function array_keys_exists(array $keys, array $arr) {
			return !array_diff_key(array_flip($keys), $arr);
	}
    public static function validateQuotesParams($params, $extraApiParams=[]){
        $invalidParams = [];
        foreach($params as $paramName => $paramVal){
            if($paramName == 'parcel')
                continue;
            if(empty($paramVal)){
                $invalidParams['empty'][] = $paramName;
            }
        }
        $paramKeys = array_keys($params);
        $defaultParams = [
                'origin_country_iso' => 2,
                'delivery_country_iso' => 2,
                'origin_city' => 25,
                'delivery_city' => 25,
                'origin_postcode' => 10,
                'delivery_postcode' => 10,
                'parcel' => ''
        ];
        foreach ($defaultParams as $key => $value) {
            if($key == 'parcel')
                continue;
            if(is_integer($value) && strlen($params[$key]) > $value) {
                $invalidParams['validation'][] = $key. ' length is greater than maximum character limit ['.$value.']';
            }
        }
        $defaultParams = array_merge($extraApiParams,array_keys($defaultParams));

        foreach($paramKeys as $param){
            if($param == 'parcel')
                continue;
            if(!in_array(trim($param),$defaultParams)){
                $invalidParams[] = $param;
            }
        }
        $required_params = [
            'origin_country_iso',
            'delivery_country_iso',
            'parcel'
        ];
        $checkDefault = array_diff($required_params,$paramKeys);
        if(!empty($checkDefault)){
            foreach ($checkDefault as $checkDefaultItem) {
                $invalidParams['missing'][] = $checkDefaultItem;
            }
        }
        $parcelDefault = ['weight','height','width','length'];
        if(isset($params['parcel'])){
            foreach($params['parcel'] as $index => $parcel) {
                if(array_key_exists("weight",$parcel)){
                    if(empty($parcel['weight'])){
                        $invalidParams['parcel_weight'][$index] = "weight";
                    }
                }else{
                    $invalidParams['parcel_weight'][$index] = "weight";
                }
                $arrayKeys = [];
                $arrayKeys = array_keys($parcel);
                foreach ($arrayKeys as $arrayKey) {
                    if(!in_array($arrayKey,$parcelDefault)){
                        $invalidParams['parcel'][$index][] = $arrayKey;
                    }
                }
            }
        }
        return $invalidParams;
    }
}
