<?php
namespace Smarttrack\V2\Action;

use Error\ApiProblem;
use Error\Exception\ProblemException;
use Monolog\Logger;
use RKA\ContentTypeRenderer\HalRenderer;
use Smarttrack\V2\ApiFunctions;
use SmarttrackTransformer\V2\CreateManifestTransformer;
use Zend\InputFilter\Factory as InputFilterFactory;
use Smarttrack\V2\SmartAction;

class CreateManifestAction extends SmartAction {

    protected $logger;
    protected $renderer;
    protected $authorMapper;
    protected $userServiceRoutingMapper;
    
    public function __construct(Logger $logger, HalRenderer $renderer) {
        $this->logger = $logger;
        $this->renderer = $renderer;

    }

    public function __invoke($request, $response) {
       $userId = $request->getAttribute('api_user_id');
        $this->logger->info("Add Shipment For User ", ['api_user_id' => $userId]);
        $userData = ApiFunctions::getUserById($userId);
        $menifestResponse = [];
        // Show error here
        if ($userData->getId() < 1) {
            $errors = ['User does not exist.'];
            $menifestResponse['STATUS'] = "ERROR";
            $menifestResponse['ERROR'] = $errors;
            $menifestResponse['MESSAGE'] = "Please fix below error(s)";

            $tarnsform = new CreateManifestTransformer($request, $response);
            return $tarnsform->transform($menifestResponse);
        }
        
        $paramData = $request->getParams();
       
        //validating user date
        $validateManifestData = $this->validate($paramData, ['tracking_number']);
        
        $manifestResp = ApiFunctions::CreateManifest($validateManifestData, $userData);
       
        $transformer = new CreateManifestTransformer($request, $response);
        $hal = $transformer->transform($manifestResp);
     
       // $resultData = $this->renderer->render($request, $response, $hal);
    //    ApiFunctions::AddApiLog("CreateManifest", $paramData,$manifestResp ,$userId);
        return $hal;
    }

    /**
     * Validate data to be applied to this entity
     *
     * @param  array $data
     * @return array
     */
    public function validate($data, $elements = []) {
        $inputFilter = $this->createInputFilter($elements);
        $inputFilter->setData($data);

        if ($inputFilter->isValid()) {
            return $inputFilter->getValues();
        }

        $problem = new ApiProblem(
                'Validation failed', 'about:blank', 400
        );
        $problem['errors'] = $inputFilter->getMessages();

        throw new ProblemException($problem);
    }

    protected function createInputFilter($elements = []) {
        $specification = [
            'tracking_number' => [
                'required' => true,
                'filters' => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags'],
                ]
            ]
        ];
        if ($elements) {
            $specification = array_filter(
                    $specification, function ($key) use ($elements) {
                return in_array($key, $elements);
            }, ARRAY_FILTER_USE_KEY
            );
        }
        $factory = new InputFilterFactory();
        $inputFilter = $factory->createInputFilter($specification);

        return $inputFilter;
    }

}
