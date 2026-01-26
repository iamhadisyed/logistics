<?php
namespace Smarttrack\V2;

use Smarttrack\V2\SmartResponse;

class SmartTransformer {
var $request;
var $response;
var $apiName;
var $sr;

    /**
    * @return mixed
    */
    public function __construct($request, $response){
        $this->request = $request;
        $this->response = $response;
        $this->apiName = rtrim(basename(get_class($this)),'Transformer');
        $this->sr = new SmartResponse( $request, $response );
        $this->sr->setApiName($this->apiName);
    }
    protected function mappingData($data,$mapping){
        $return = [];
        if(is_array($mapping) && !empty($mapping) && is_array($data) && !empty($data)){
            foreach ($mapping as $key => $map) {
                if(isset($data[$map]))
                    $return[$key] = $data[$map];
            }
        }
        return $return;
    }
    protected function array_change_key_case_recursive($arr, $case = CASE_LOWER){
        $case = ($case == CASE_UPPER) ? CASE_UPPER : CASE_LOWER;
        return array_map(function($item) use($case) {
            if(is_array($item))
                $item = $this->array_change_key_case_recursive($item, $case);
            return $item;
        },array_change_key_case($arr, $case));
    }
}