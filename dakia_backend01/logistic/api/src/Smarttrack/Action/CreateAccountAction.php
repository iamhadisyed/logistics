<?php
namespace Smarttrack\Action;

use Error\ApiProblem;
use Error\Exception\ProblemException;
use Monolog\Logger;
use RKA\ContentTypeRenderer\ApiProblemRenderer;
use RKA\ContentTypeRenderer\HalRenderer;
use Smarttrack\ApiFunctions;
use SmarttrackTransformer\QuotationsTransformer;
use SmarttrackTransformer\UserAccountTransformer;
use UserServicesRouting;
use Zend\InputFilter\Factory as InputFilterFactory;

class CreateAccountAction
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
	    $this->logger->info("Creating Account For User - ", ['user_account_id' => $userId]);
	    $apiUserData = ApiFunctions::getUserById($userId);
        if($apiUserData->getId() < 1){
	        $problem = new ApiProblem(
                'Could not find user',
                'http://zenddesk.smarttrack.com',
                404
            );
            throw new ProblemException($problem);
        }
//	    echo "<pre>"; print_r($userData); echo "</pre>"; die();
	    //validating quotations data
	    $accountData = $request->getParams();
//	    echo "<pre>"; print_r($quoteData); echo "</pre>"; die();
	    $data = $this->validate($accountData, ['accountNumber', 'accountEmail','companyName','contactName','contactNumber','countryIso','returnAddress','companyRegNumber','companyRegAddress','companyRegPostcode','companyRegCountryIso','invoicePeriod']);
// echo "<pre>"; print_r($accountData); echo "</pre>"; die();
	    $quotations = ApiFunctions::createUserAccount($accountData,$apiUserData);
//	    echo "<pre>"; print_r($quotations); echo "</pre>"; die();
        $transformer = new UserAccountTransformer();
        $hal = $transformer->transformCollection($quotations);
        $resultData = $this->renderer->render($request, $response, $hal);
        ApiFunctions::AddApiLog("create-account", $accountData,$resultData ,$userId);
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
			'accountNumber' => [
				'required' => true,
				'filters' => [
					['name' => 'StringTrim'],
					['name' => 'StripTags'],
				]
			],
			'accountEmail' => [
				'required' => true,
				'filters' => [
					['name' => 'StringTrim'],
					['name' => 'StripTags'],
				]
			],
			'companyName' => [
				'required' => true,
				'filters' => [
					['name' => 'StringTrim'],
					['name' => 'StripTags'],
				]
			],
			'contactName' => [
				'required' => true,
				'filters' => [
					['name' => 'StringTrim'],
					['name' => 'StripTags'],
				]
			],
			'contactNumber' => [
				'required' => true,
				'filters' => [
					['name' => 'StringTrim'],
					['name' => 'StripTags'],
				]
			],
			'countryIso' => [
				'required' => true,
				'filters' => [
					['name' => 'StringTrim'],
					['name' => 'StripTags'],
				]
			],
			'returnAddress' => [
				'required' => true,
				'filters' => [
					['name' => 'StringTrim'],
					['name' => 'StripTags'],
				]
			],
			'companyRegNumber' => [
				'required' => true,
				'filters' => [
					['name' => 'StringTrim'],
					['name' => 'StripTags'],
				]
			],

			'companyRegAddress' => [
				'required' => true,
				'filters' => [
					['name' => 'StringTrim'],
					['name' => 'StripTags'],
				]
			],
			'companyRegPostcode' => [
				'required' => true,
				'filters' => [
					['name' => 'StringTrim'],
					['name' => 'StripTags'],
				]
			],
			'companyRegCountryIso' => [
				'required' => true,
				'filters' => [
					['name' => 'StringTrim'],
					['name' => 'StripTags'],
				]
			],
			'invoicePeriod' => [
				'required' => true,
				'filters' => [
					['name' => 'StringTrim'],
					['name' => 'StripTags'],
				],
				'validators' => [
					[
						'name' => 'InArray',
						'options'=>[
							'haystack'=>['daily','weekly','bi-monthly','monthly'],
							'messages' => array('notInArray' => 'Value must be in (daily, weekly ,bi-monthly, monthly)')
						],

					]
				]
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
}
