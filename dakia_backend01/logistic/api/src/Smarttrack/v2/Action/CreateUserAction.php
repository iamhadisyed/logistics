<?php
namespace Smarttrack\V2\Action;

use Error\ApiProblem;
use Error\Exception\ProblemException;
use Monolog\Logger;
use RKA\ContentTypeRenderer\HalRenderer;
use Smarttrack\V2\ApiFunctions;
use SmarttrackTransformer\V2\AddUserTransformer;
use Zend\InputFilter\Factory as InputFilterFactory;
use Smarttrack\V2\SmartAction;

class CreateUserAction extends SmartAction {
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

            $tarnsform = new AddUserTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
        }
//	    echo "<pre>"; print_r($apiUserData); echo "</pre>"; die();
	    //validating user date
	    $newUserData = $request->getParams();
//	    echo "<pre>"; print_r($newUserData); echo "</pre>"; die();
	    $newUserData = $this->validate($newUserData, ['userName','userType','password','firstName','lastName','email','phoneNumber','countryIso','warehouseCode','dashboard','address','accessabilities']);
	    $userCreationResp = ApiFunctions::addUser($newUserData,$apiUserData);
        $transformer = new AddUserTransformer();
        $hal = $transformer->transformCollection($userCreationResp);
        $resultData = $this->renderer->render($request, $response, $hal);
        ApiFunctions::AddApiLog("add-user", $newUserData,$resultData ,$userId);
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
			'userName' => [
				'required' => true,
				'filters' => [
					['name' => 'StringTrim']
				],
				'validators' => [
					[
						'name' => 'Regex',
						'options'=>[
							'pattern'=>'/^[A-Za-z0-9_-]+$/',
							'messages' => array('regexNotMatch' => 'Invalid Username Format')
						],
					]
				]
			],
			'password' => [
				'required' => true,
				'validators' => [
					[
						'name' => 'Regex',
						'options'=>[
							'pattern'=>'/^(?=.*?[A-Z])(?=(.*[a-z]){1,})(?=(.*[\d]){1,})(?=(.*[\W]){1,})(?!.*\s).{8,}$/',
							'messages' => array('regexNotMatch' =>trim(preg_replace('/\s\s+/', ' ', formatMessages(ERROR_PASSWORD_VERIFY,false))))
						],
					]
				]
			],
			'userType' => [
				'required' => true,
				'filters' => [
					['name' => 'StringTrim']
				],
				'validators' => [
					[
						'name' => 'InArray',
						'options'=>[
							'haystack'=>['corporate','client'],
							'messages' => array('notInArray' => 'Value must be in (corporate ,client)')
						],

					]
				]
			],
			'firstName' => [
				'required' => true,
				'filters' => [
					['name' => 'StringTrim'],
					['name' => 'StripTags'],
				]
			],
			'lastName' => [
				'required' => true,
				'filters' => [
					['name' => 'StringTrim'],
					['name' => 'StripTags'],
				]
			],
			'email' => [
				'required' => true,
				'filters' => [
					['name' => 'StringTrim']
				],
				'validators' => [
					['name' => 'emailaddress' ]
				]
			],
			'phoneNumber' => [
				'filters' => [
					['name' => 'StringTrim'],
					['name' => 'StripTags']
				]
			],
			'countryIso' => [
//				'required' => true,
				'filters' => [
					['name' => 'StringTrim']
				]
			],
			'warehouseCode' => [
//				'required' => true,
				'filters' => [
					['name' => 'StringTrim']
				]
			],
			'address' => [
				'filters' => [
					['name' => 'StringTrim'],
					['name' => 'StripTags'],
				]
			],
			'dashboard' => [
//				'required' => true,
				'filters' => [
					['name' => 'StringTrim']
				],
				'validators' => [
					[
						'name' => 'InArray',
						'options'=>[
							'haystack'=>['corporate','operation'],
							'messages' => array('notInArray' => 'Value must be in (corporate , operation)')
						],
					]
				]
			],
			'accessabilities' => [
//				'required' => true,
				'filters' => [
					['name' => 'StringTrim'],
					['name' => 'StripTags'],
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
