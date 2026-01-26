<?php
namespace Smarttrack\V2\Action;

use Error\ApiProblem;
use Error\Exception\ProblemException;
use Monolog\Logger;
use RKA\ContentTypeRenderer\ApiProblemRenderer;
use RKA\ContentTypeRenderer\HalRenderer;
use Smarttrack\V2\ApiFunctions;
use Smarttrack\V2\SmartAction;
use SmarttrackTransformer\V2\DispatchMarketplaceOrderTransformer;
use Zend\InputFilter\Factory as InputFilterFactory;

class DispatchMarketplaceOrderAction extends SmartAction {
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
        $getFormData = $request->getParsedBody();
        $apiUserData = ApiFunctions::getUserById($userId);
        if ($apiUserData->getId() < 1) {
            $errors = ['User does not exist.'];
            $labelResponse['STATUS'] = "ERROR";
            $labelResponse['ERROR'] = $errors;
            $labelResponse['MESSAGE'] = "Please fix below error(s)";

            $tarnsform = new DispatchMarketplaceOrderTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
        }
        $validateLabelData = ApiFunctions::validateDispatchMarketplaceOrderParams($getFormData);
        if ($validateLabelData) {
            $errors = $validateLabelData;
            $labelResponse['STATUS'] = "ERROR";
            $labelResponse['ERROR'] = $errors;
            $labelResponse['MESSAGE'] = "Please fix below error(s)";

            $tarnsform = new DispatchMarketplaceOrderTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
        }
        $marketplaceOrderNumber = $getFormData['marketplace_order_number'];
        $dispatchData = ApiFunctions::dispatchMarketplaceOrder($marketplaceOrderNumber);
        $tarnsform = new DispatchMarketplaceOrderTransformer($request, $response);
        return $tarnsform->transform($dispatchData);
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
