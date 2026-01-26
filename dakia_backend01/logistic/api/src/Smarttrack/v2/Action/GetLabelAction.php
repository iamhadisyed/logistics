<?php
namespace Smarttrack\V2\Action;

use Error\ApiProblem;
use Error\Exception\ProblemException;
use Monolog\Logger;
use RKA\ContentTypeRenderer\ApiProblemRenderer;
use RKA\ContentTypeRenderer\HalRenderer;
use Smarttrack\V2\ApiFunctions;
use Smarttrack\V2\SmartAction;
use SmarttrackTransformer\V2\GetLabelTransformer;
use Zend\InputFilter\Factory as InputFilterFactory;

class GetLabelAction extends SmartAction {
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
		$getLabelFormData = $request->getParsedBody();
		$this->logger->info("Getting label for shipment ", ['order_reference' => $getLabelFormData['order_reference']]);
        $apiUserData = ApiFunctions::getUserById($userId);

        $saveLog=[];
        $saveLog['api']="GetLabelAction";
        $fileLogDate = date("d-m-Y");
        $saveLog['time'] = date("d-m-Y H:i:s");
        $filePath = SETTING_DIR_ASSETS.'api_log';
        if (!is_dir($filePath)) {
            mkdir($filePath, 0777, true);
        }
        $fileFullName = $filePath."/".$fileLogDate."_api_log.html";

        if ($apiUserData->getId() < 1) {
            $errors = ['User does not exist.'];
            $labelResponse['STATUS'] = "ERROR";
            $labelResponse['ERROR'] = $errors;
            $labelResponse['MESSAGE'] = "Please fix below error(s)";

            /*
             * Log System
            */
            $shipData = $request->getParams();
            $saveLog['request'] = $shipData;
            $saveLog['response'] = $getLabelFormData;
            $allFileData = json_encode($saveLog);

            if (!is_dir($filePath)) {
                mkdir($filePath, 0777, true);
            }
            $current = file_get_contents($fileFullName);
            $current .= $allFileData." \n";
            file_put_contents($fileFullName, $current);
            /*
             * End Log System
             */

            $tarnsform = new GetLabelTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
        }
        $validateLabelData = ApiFunctions::validateLabelParams($getLabelFormData);
        if ($validateLabelData) {
            $errors = $validateLabelData;
            $labelResponse['STATUS'] = "ERROR";
            $labelResponse['ERROR'] = $errors;
            $labelResponse['MESSAGE'] = "Please fix below error(s)";


            /*
             * Log System
            */
            $shipData = $request->getParams();
            $saveLog['request'] = $shipData;
            $saveLog['response'] = $labelResponse;
            $allFileData = json_encode($saveLog);

            if (!is_dir($filePath)) {
                mkdir($filePath, 0777, true);
            }
            $current = file_get_contents($fileFullName);
            $current .= $allFileData." \n";
            file_put_contents($fileFullName, $current);
            /*
             * End Log System
             */

            $tarnsform = new GetLabelTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
        }
        $labelData = ApiFunctions::getLabel($getLabelFormData['order_reference'],$getLabelFormData['label_type'],$getLabelFormData['label_size']);

        /*
             * Log System
            */
        $shipData = $request->getParams();
        $saveLog['request'] = $shipData;
        $saveLog['response'] = $labelData;
        $allFileData = json_encode($saveLog);

        if (!is_dir($filePath)) {
            mkdir($filePath, 0777, true);
        }
        $current = file_get_contents($fileFullName);
        $current .= $allFileData." \n";
        file_put_contents($fileFullName, $current);
        /*
         * End Log System
         */

        $tarnsform = new GetLabelTransformer($request, $response);
        return $tarnsform->transform($labelData);
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
			'order_reference' => [
				'required' => true,
				'filters' => [
					['name' => 'StringTrim'],
					['name' => 'StripTags'],
				]
			],
			'label_type' => [
				'required' => false,
				'filters' => [
					['name' => 'StringTrim'],
					['name' => 'StripTags'],
				],
			],
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
