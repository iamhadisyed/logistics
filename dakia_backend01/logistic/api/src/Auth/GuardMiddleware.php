<?php
namespace Auth;

class GuardMiddleware
{
    /**
     * @var \OAuth2\OAuth2Sever
     */
    protected $server;

    public function __construct($server)
    {
        $this->server = $server;
    }

    public function __invoke($request, $response, $next)
    {
        $postHeadersArr = [];
        $token = [];
        if (function_exists('getallheaders')){
            $postHeadersArr = getallheaders();
        }else{
            foreach ($_SERVER as $name => $value)
            {
                if (substr($name, 0, 5) == 'HTTP_')
                {
                    $postHeadersArr[str_replace(' ', '-', str_replace('_', ' ', substr($name, 5)))] = $value; //ucwords(strtolower(
                }
            }
        }
    	$ShipStationSellerIDKey = strtoupper('ShipStation-SellerID');
    	$ShipStationSellerProviderIDKey = strtoupper('ShipStation-SellerProviderID');
//    	$ShipStationSellerIDKey = 'ShipStation-SellerID';
//    	$ShipStationSellerProviderIDKey = 'ShipStation-SellerProviderID';
        if(!empty($postHeadersArr[$ShipStationSellerIDKey]) && !empty($postHeadersArr[$ShipStationSellerProviderIDKey])){
            if(array_key_exists($ShipStationSellerIDKey, $postHeadersArr) && array_key_exists($ShipStationSellerProviderIDKey, $postHeadersArr) ){
                // Check if user exsit in system
                $validUserData = \UserShoppingPlatformsFilter::varifyAPIKeyAPISecrete($postHeadersArr[$ShipStationSellerIDKey], $postHeadersArr[$ShipStationSellerProviderIDKey]);
                if(count($validUserData) > 0){
                    $token['user_id'] = $validUserData[0]->getUserId();
                }else{
                    $returnJson = [];
                    $returnJson['error'] = "invalid_credential";
                    $returnJson['error_description'] = "The API Key or API Secret provided is invalid";
                    echo json_encode($returnJson);
                    exit;
                }
            }else{
                    $returnJson = [];
                    $returnJson['error'] = "invalid_credential";
                    $returnJson['error_description'] = "The API Key or API Secret provided is invalid";
                    echo json_encode($returnJson);
                    exit;
                }
        }else{
            $server = $this->server;
            $req = \OAuth2\Request::createFromGlobals();
            if (!$server->verifyResourceRequest($req)) {
                $server->getResponse()->send();
                exit;
            }
            // store the username into the request's attributes
            $token = $server->getAccessTokenData($req);
       }
        $request = $request->withAttribute('username', $token['user_id']);
        $request = $request->withAttribute('api_user_id', $token['user_id']);
        return $next($request, $response);
    }
}
