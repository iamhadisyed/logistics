<?php
namespace Smarttrack\Action;

use Error\ApiProblem;
use Error\Exception\ProblemException;
use Monolog\Logger;
use RKA\ContentTypeRenderer\ApiProblemRenderer;
use RKA\ContentTypeRenderer\HalRenderer;
use Smarttrack\ApiFunctions;
use SmarttrackTransformer\QuotationsTransformer;
use UserServicesRouting;
use Zend\InputFilter\Factory as InputFilterFactory;

class UserQuotesAction
{
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
	        $problem = new ApiProblem(
                'Could not find user',
                'http://zenddesk.smarttrack.com',
                404
            );
            throw new ProblemException($problem);
        }
	    //validating quotations data
	    $quoteData = $request->getParams();
//	    echo "<pre>"; print_r($quoteData); echo "</pre>"; die();
//	    echo "<pre>"; print_r($quoteData); echo "</pre>"; die();
	    $data = $this->validate($quoteData, ['origin_country_iso', 'delivery_country_iso','weight','number_of_pieces','parcel']);

//	    validate pieces items
	    $requiredIndexes =['qty','weight','length','width','height'];
	    $vErrors = [];
	    if(is_array($quoteData['parcel'])){
		    foreach($quoteData['parcel'] as $key=>$pieceData){
			    if(count($pieceData) < count($requiredIndexes)){
				    $vErrors[] ="parcel $key must have (".implode(",",$requiredIndexes).') details.';
			    }
			    else{
				    $hasRequiredIndexes = UserQuotesAction::array_keys_exists($requiredIndexes,$pieceData);
				    if(!$hasRequiredIndexes){
					    $vErrors[] ="parcel $key must have (".implode(",",$requiredIndexes).') details.';
				    }
			    }
		    }
	    }else{
		    $vErrors[] ="parcel field type must be array.";
	    }
	    if(!empty($vErrors)){
		    $quotations['STATUS'] = "ERROR";
		    $quotations['MESSAGE'] = "Pieces field has invalid data";
		    $quotations['ERROR'][] = $vErrors;
	    }else{
		    $quotations = ApiFunctions::UserQuotes($quoteData,$userData->getUserAccountId());
	    }
        $transformer = new QuotationsTransformer();
        $hal = $transformer->transformCollection($quotations);
        $resultData = $this->renderer->render($request, $response, $hal);
        ApiFunctions::AddApiLog("get-quotes", $quoteData,$resultData ,$userId);
        return $resultData;
    }
	/**
	 * Validate data to be applied to this entity
	 *
	 * @param  array $data
	 * @return array
	 */
	public function validate($data, $elements = [])
	{
		$inputFilter = $this->createInputFilter($elements);
		$inputFilter->setData($data);

		if ($inputFilter->isValid()) {
			return $inputFilter->getValues();
		}

		$problem = new ApiProblem(
			'Validation failed',
			'about:blank',
			400
		);
		$problem['errors'] = $inputFilter->getMessages();

		throw new ProblemException($problem);
	}
	protected function createInputFilter($elements = [])
	{
		$specification = [
			'origin_country_iso' => [
				'required' => true,
				'filters' => [
					['name' => 'StringTrim'],
					['name' => 'StripTags'],
				]
			],
			'delivery_country_iso' => [
				'required' => true,
				'filters' => [
					['name' => 'StringTrim'],
					['name' => 'StripTags'],
				]
			],
			'weight' => [
				'required' => true,
				'validators' => [
					['name' => 'Float']
				]
			],
			'number_of_pieces' => [
				'required' => true,
				'validators' => [
					['name' => 'Float']
				]
			],
			'parcel' => [
				'required' => true
			]
		];

		if ($elements) {
			$specification = array_filter(
				$specification,
				function ($key) use ($elements) {
					return in_array($key, $elements);
				},
				ARRAY_FILTER_USE_KEY
			);
		}

		$factory = new InputFilterFactory();
		$inputFilter = $factory->createInputFilter($specification);

		return $inputFilter;
	}
	public static  function array_keys_exists(array $keys, array $arr) {
			return !array_diff_key(array_flip($keys), $arr);
	}
}
