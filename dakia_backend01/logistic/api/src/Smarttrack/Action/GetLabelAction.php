<?php
namespace Smarttrack\Action;

use Error\ApiProblem;
use Error\Exception\ProblemException;
use Monolog\Logger;
use RKA\ContentTypeRenderer\ApiProblemRenderer;
use RKA\ContentTypeRenderer\HalRenderer;
use Smarttrack\ApiFunctions;
use SmarttrackTransformer\GetLabelTransformer;
use SmarttrackTransformer\TrackingDataTransformer;
use Zend\InputFilter\Factory as InputFilterFactory;

class GetLabelAction
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
		$getLabelFormData = $request->getParsedBody();
		$data = $this->validate($getLabelFormData, ['order_reference', 'label_type']);
		$this->logger->info("Getting label for shipment ", ['order_reference' => $data['order_reference']]);
		$labelData = ApiFunctions::getLabel($data['order_reference'],$data['label_type'],$getLabelFormData['label_size']);
		$transformer = new GetLabelTransformer();
		$hal = $transformer->transformCollection($labelData);
        $resultData = $this->renderer->render($request, $response, $hal);
        ApiFunctions::AddApiLog("get-label", $getLabelFormData,$resultData ,$userId);
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
