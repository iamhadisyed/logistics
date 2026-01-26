<?php
namespace Smarttrack\V2\Action;

use Error\ApiProblem;
use Error\Exception\ProblemException;
use Monolog\Logger;
use RKA\ContentTypeRenderer\HalRenderer;
use Smarttrack\V2\ApiFunctions;
use SmarttrackTransformer\V2\UserAccountTransformer;
use Smarttrack\V2\SmartAction;
use UserAccount;
use Services;
use UserMarketPlacesMappingFilter;
use MarketPlacesAuthenticateFieldFilter;
use UserShoppingPlatformsFilter;

class GetUserAccountsAction extends SmartAction {
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
        $accountNumber = trim($request->getAttribute('account_number'));
        $this->logger->info("Get Account From Account Number - ", ['user_account' => $accountNumber]);
        $apiUserData = ApiFunctions::getUserById($userId);
        if ($apiUserData->getId() < 1) {
            $errors = ['User does not exist.'];
            $labelResponse['STATUS'] = "ERROR";
            $labelResponse['ERROR'] = $errors;
            $labelResponse['MESSAGE'] = "Please fix below error(s)";

            $tarnsform = new UserAccountTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
        }
        $servicesData = [];
        $selectedShoppingPlatform = [];
        // Get user's account and its child accounts
        if($apiUserData->getUserAccountId() > 0){
            $userAccount = new CustomerAccount();
            if(empty($_GET['current_account'])) {
                $userAccountsArr = $userAccount->getImmediateSubaccount($apiUserData->getUserAccountId(), true);
            }
            $userAccountNew = new \CustomerAccount($apiUserData->getUserAccountId());
            if(!empty($_GET['current_account'])) {
                $accountNumber = $userAccountNew->getUserAccount();
            }
            $submmitedUserAccountObj = new \UserAccountFilter();
            $submmitedUserAccountObj->addFieldFilter("    User_account",$accountNumber);
            $userAccounts = $submmitedUserAccountObj->getList();

            array_push($userAccountsArr,$userAccountNew->getUserAccount());
//            check if user submitted account is allowed user account to get data
            $accountSubmitted = false;
            if(!empty($accountNumber)){
                if((in_array($accountNumber,$userAccountsArr)) || !empty($_GET['current_account'])){
                    // Yes its allowed account
                    $accountSubmitted = true;
                    $servicesLists = Services::getServiceMappingWithCarrier($apiUserData->getUserAccountId());
                    foreach ($servicesLists as $service) {
                        $carrierName = $service->getcarrierId();
                        $servicesData[$carrierName][] = [
                            "code"=>$service->getCode(),
                            "name"=>$service->getName()
                        ];
                    }
                } else {
                    $errors = ['Please enter a valid account number'];
                    $labelResponse['STATUS'] = "ERROR";
                    $labelResponse['ERROR'] = $errors;
                    $labelResponse['MESSAGE'] = "Please fix below error(s)";

                    $tarnsform = new UserAccountTransformer($request, $response);
                    return $tarnsform->transform($labelResponse);
                }
            }
            if($accountSubmitted) {
                //GET user platforms
                $userMarketPlacesMappingFilter = new UserMarketPlacesMappingFilter();
                $userAccount = $apiUserData->getUserAccountId();
                $userMarketPlacesMappingFilter->addUserIdFilter($userAccount);
                $userMarketPlacesMappingFilter->addJoin(' market_places', 'market_places.id = ump.market_places_id ');
                $userData = $userMarketPlacesMappingFilter->getList();
                $marketPlaces = new MarketPlacesAuthenticateFieldFilter();
                $marketPlaces = $marketPlaces->getList();
                $marketPlacesFieldValue = [];
                foreach ($marketPlaces as $marketPlace) {
                    $marketPlacesFieldValue[$marketPlace->getFieldValue()] = $marketPlace->getFieldName();
                }
                if (count($userData) > 0) {
                    foreach ($userData as $U) {
                        $authData = json_decode($U->getAuthData(), true);
                        $authDataArr = [];
                        foreach ($authData as $key => $value) {
                            $authDataArr[$marketPlacesFieldValue[$key]] = $value;
                        }
                        $selectedShoppingPlatform[$U->getTitle()] = $authDataArr;
                        $selectedShoppingPlatform[$U->getTitle()]['store_key'] = $U->getStoreKey();
                    }
                }
            }else{
                $userAccounts = ApiFunctions::getUserAccounts($apiUserData);
            }
            $transformer = new UserAccountTransformer($request, $response);
            if($accountSubmitted) {
                return $transformer->transform($userAccounts, $servicesData, $selectedShoppingPlatform, [], $apiUserData);
            } else {
                return $transformer->transform($userAccounts, $servicesData, [], [], $apiUserData);
            }
        }else{
            $errors = ['Please enter a valid account number'];
            $labelResponse['STATUS'] = "ERROR";
            $labelResponse['ERROR'] = $errors;
            $labelResponse['MESSAGE'] = "Please fix below error(s)";

            $tarnsform = new UserAccountTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
        }
    }
}
