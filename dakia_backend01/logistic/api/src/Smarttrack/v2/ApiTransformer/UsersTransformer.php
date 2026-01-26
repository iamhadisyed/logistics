<?php
namespace SmarttrackTransformer\V2;

use Smarttrack\V2\SmartTransformer;

/**
 * Transform an Author (or collection of Authors) into Hal resource
 */
class UsersTransformer extends SmartTransformer {
    public function transform($userAccounts) {
        $count = 0;
        $apiResp = [];
        if(count($userAccounts) > 0){
            $count = count($userAccounts);
            foreach ($userAccounts as $userAccount) {
                $isActive = ($userAccount->getActiveFlag()==1) ? "YES" : "NO";
                $companyName = (!is_null($userAccount->getCompany())) ? $userAccount->getCompany() : "Not Available";
                $contactName = (!is_null($userAccount->getFullName())) ? $userAccount->getFullName() : "Not Available";
                $contactNumber = (!is_null($userAccount->getTelephone())) ? $userAccount->getTelephone() : "Not Available";
                $returnAddress = (!is_null($userAccount->getReturnAddress())) ? $userAccount->getReturnAddress() : "Not Available";
                $billingCurrency = (!is_null($userAccount->getBillingCurrency())) ? $userAccount->getBillingCurrency() : "Not Available";
                $invoiceNumber = (!is_null($userAccount->getInvoicePeriod())) ? $userAccount->getInvoicePeriod() : "Not Available";
                $vatNumber     = (!is_null($userAccount->getVatNumber())) ? $userAccount->getVatNumber() : "Not Available";
                $data[] =[
                    'accountNumber'          =>  $userAccount->getUserAccount(),
                    'isActive'               =>  $isActive,
                    'companyName'            =>  $companyName,
                    'contactName'            =>  $contactName,
                    'contactNumber'          =>  $contactNumber,
                    'returnAddress'          =>  $returnAddress,
                    'billingCurrency'        =>  $billingCurrency,
                    'invoicePeriod'          => $invoiceNumber,
                    'vatNumber'              => $vatNumber
                ];
            }
            $apiResp["status"] =   "success";
            $apiResp["count"] = $count;
            $apiResp["message"] = "Records Found";
            $apiResp["data"] = $data;
            $warnings = "";
            return $this->sr->success(201,$apiResp["message"],$apiResp["data"],$warnings);
        }else{
            $apiResp["status"] =   "error";
            $apiResp["message"] =   "No Record Found!";
            $apiResp["status_code"] =  400;
            return $this->sr->error(400, "Please contact system administrator",["Whoops, looks like something went wrong."]);
        }
    }
}
