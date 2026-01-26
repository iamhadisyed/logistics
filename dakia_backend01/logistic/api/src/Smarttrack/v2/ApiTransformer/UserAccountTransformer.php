<?php
namespace SmarttrackTransformer\V2;

use Smarttrack\V2\SmartTransformer;

/**
 * Transform an Author (or collection of Authors) into Hal resource
 */
class UserAccountTransformer extends SmartTransformer {
    public function transform($userAccounts,$servicesData=[],$selectedShoppingPlatform=[],$userShoppingPlatformsArr=[], $apiUserData = []) {
        $count = 0;
        $apiResp = [];
        if(isset($userAccounts['STATUS']) && $userAccounts['STATUS'] == "ERROR"){
            $apiResp["status"] =   "error";
            $apiResp["status_code"] =  400;
            return $this->sr->error(400, $userAccounts['MESSAGE'],$userAccounts['ERROR']);
        }
        if(count($userAccounts) > 0){
            $count = count($userAccounts);
            foreach ($userAccounts as $userAccount) {
                $isActive = ($userAccount->getActiveFlag()==1) ? "YES" : "NO";
                $companyName = (!is_null($userAccount->getCompany())) ? $userAccount->getCompany() : "Not Available";
                $contactName = (!is_null($userAccount->getFullName())) ? $userAccount->getFullName() : "Not Available";
                $userType = (!is_null($apiUserData->getUserType())) ? $apiUserData->getUserType() : "Not Available";
                $contactNumber = (!is_null($userAccount->getTelephone())) ? $userAccount->getTelephone() : "Not Available";
                $returnAddress = (!is_null($userAccount->getReturnAddress())) ? $userAccount->getReturnAddress() : "Not Available";
                $billingCurrency = (!is_null($userAccount->getBillingCurrency())) ? $userAccount->getBillingCurrency() : "Not Available";
                $invoiceNumber = (!is_null($userAccount->getInvoicePeriod())) ? $userAccount->getInvoicePeriod() : "Not Available";
                $vatNumber     = (!is_null($userAccount->getVatNumber())) ? $userAccount->getVatNumber() : "Not Available";
                $countryObj = new \Country($userAccount->getCountryId());
                $countryName     = (!is_null($countryObj->getName())) ? $countryObj->getName() : "Not Available";
                $postCode     = (!is_null($apiUserData->getPostCode())) ? $apiUserData->getPostCode() : "Not Available";
                $city     = (!is_null($apiUserData->getCity())) ? $apiUserData->getCity() : "Not Available";
                $addressLine1     = (!is_null($apiUserData->getAddress())) ? $apiUserData->getAddress() : "Not Available";
                $addressLine2    = (!is_null($apiUserData->getAddress2())) ? $apiUserData->getAddress2() : "Not Available";
                $addressLine3    = (!is_null($apiUserData->getAddress3())) ? $apiUserData->getAddress3() : "Not Available";
                $data[] =[
                    'account_number'          =>  $userAccount->getUserAccount(),
                    'is_active'               =>  $isActive,
                    'company_name'            =>  $companyName,
                    'contact_name'            =>  $contactName,
                    'user_type'               =>  $userType,
                    'contact_number'          =>  $contactNumber,
                    'return_address'          =>  $returnAddress,
                    'postcode'                =>  $postCode,
                    'city'                =>  $city,
                    'address_line_1'                =>  $addressLine1,
                    'address_line_2'                =>  $addressLine2,
                    'address_line_3'                =>  $addressLine3,
                    'country_name'            =>  $countryName,
                    'billing_currency'        =>  $billingCurrency,
                    'invoice_period'          => $invoiceNumber,
                    'vat_number'              => $vatNumber,
                    'services'              => $servicesData,
                    'integrations'              => $selectedShoppingPlatform,
                    'integration_site'              => $userShoppingPlatformsArr
                ];
            }
            $apiResp["status"] =   "success";
            $apiResp["count"] = $count;
            $apiResp["message"] = "Records Found";
            $apiResp["data"] = $data;
            $warnings = "";
            return $this->sr->success(200,$apiResp["message"],$apiResp["data"],$warnings);
        }else{
            $apiResp["status"] =   "error";
            $apiResp["message"] =   "No Record Found!";
            $apiResp["status_code"] =  400;
            return $this->sr->error(400, "Please contact system administrator",["Whoops, looks like something went wrong."]);
        }
    }
}
