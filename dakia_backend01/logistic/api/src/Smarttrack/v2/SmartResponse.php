<?php
namespace Smarttrack\V2;
use Nocarrier\Hal;
use RKA\ContentTypeRenderer\HalRenderer;

class SmartResponse {
    private $renderer;
    private $request;
    private $response;
    private $apiName;
    private $conId = NULL;

    public function __construct($request, $response)
    {
        $this->renderer = new HalRenderer();
        $this->request = $request;
        $this->response = $response;
        $this->response = $this->response->withHeader('Content-type', "application/json");
    }
    public function success($code, $message="", $data, $warnings=[]){
        if(!is_array($data) || empty($data)) {
            $errors[] = "Whoops, looks like something went wrong.";
            return $this->error(400,"Please contact system administrator",$errors);
        }

        $response = [
            "success" => true,
            "code" => $code,
            "message" => $message,
            "warnings" => $warnings,
            "data" => $data
        ];

        $hal = new Hal(null, $response);
        $responseObj = $this->response;
        $parsedBody = $this->request->getParsedBody();
        $responseObj = $this->renderer->render($this->request, $responseObj, $hal)->withStatus($code);
        $body = $responseObj->getBody()->__toString();
        ApiFunctions::AddApiLog($this->apiName, $parsedBody, $body, $this->request->getAttribute('api_user_id'),$this->conId);
        return $responseObj;
    }

    public function error($code, $message="", $errors = []){
        $response = null;
        if(is_array($errors) && !empty($errors)){
            $response = [
                'success'   => false,
                'code'      => $code,
                'data'      => [],
                'message'      => $message,
                'errors'      => $errors
            ];
        }else{
            $errors[] = "Whoops, looks like something went wrong.";
            self::error(400,"Please contact system administrator",$errors);
        }
        $hal = new Hal(null, $response);
        $responseObj = $this->response;
        $parsedBody = $this->request->getParsedBody();
        $responseObj = $this->renderer->render($this->request, $responseObj, $hal)->withStatus($code);
        $body = $responseObj->getBody()->__toString();
        ApiFunctions::AddApiLog($this->apiName, $parsedBody, $body, $this->request->getAttribute('api_user_id'),$this->conId);
        return $responseObj;
    }

    public function paginate($code, $message="", $data, $warnings=[]){
        if(!is_array($data) || empty($data)) {
            $errors[] = "Whoops, looks like something went wrong.";
            return $this->error(400,"Please contact system administrator",$errors);
        }
        $response = [
            "success" => true,
            "code" => $code,
            "message" => $message,
            "warnings" => $warnings,
            "total" => $data['total'],
            "per_page" => $data['per_page'],
            "current_page" => $data['current_page'],
            "last_page" => $data['last_page'],
            "data" => $data['data']
        ];

        $hal = new Hal(null, $response);
        $responseObj = $this->response;
        $parsedBody = $this->request->getParsedBody();
        $responseObj = $this->renderer->render($this->request, $responseObj, $hal)->withStatus($code);
        $body = $responseObj->getBody()->__toString();

        ApiFunctions::AddApiLog($this->apiName, $parsedBody, $body, $this->request->getAttribute('api_user_id'));
        return $responseObj;
    }
    public function setApiName($apiName){
        $this->apiName = $apiName;
    }
    public function setConsignmentId($conId){
        $this->conId = $conId;
    }
}
?>