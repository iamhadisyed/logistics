<?php
namespace SmarttrackTransformer;

use Nocarrier\Hal;
use Smarttrack\ApiFunctions;

/**
 * Transform an Author (or collection of Authors) into Hal resource
 */
class UserAccountTransformer
{
    public function transformCollection($quotesRes)
    {
	    $apiResp = ['status' => "success",'status_code' => 200];
	    $hal = new Hal();
	    $data =[
//		    'status'          =>  strtolower($quotesRes['STATUS']),
		    'message'         =>  isset($quotesRes['MESSAGE']) ? $quotesRes['MESSAGE'] : ""
	    ];
	    if(isset($quotesRes['ERRORS']) && count($quotesRes['ERRORS'])>0){
		    $apiResp['errors'] = $quotesRes['ERRORS'];
		    $apiResp['status'] = "error";
	    }
	    $resource = new Hal(null, $data);
        $hal->addResource('accountCreationResponse', $resource);
        $hal->setData($apiResp);
        return $hal;
    }
	public function transformAccountsCollection($userAccounts)
	{
       $apiResp = ['status' => "success",'status_code' => 200,"count"=>0];
		$hal = new Hal();
		$count = 0;
//		echo "<pre>"; print_r($userAccounts);echo "</pre>"; die();
		if(count($userAccounts ) > 0){
			foreach ($userAccounts as $userAccount) {
				$count++;
				$hal->addResource('accounts', $this->transformAccount($userAccount));
			}
			$apiResp["count"]   =   $count;
			$apiResp["message"] =   "Records Found";
			$hal->setData($apiResp);
		}else{
			$apiResp["status"] =   "error";
			$apiResp["message"] =   "No Record Found!";
			$apiResp["status_code"] =  400;
			$hal->setData($apiResp);
		}

		return $hal;
	}

	public function transformAccount($userAccount)
	{
		$isActive = ($userAccount->getActiveFlag()==1) ? "YES" : "NO";
		$companyName = (!is_null($userAccount->getCompany())) ? $userAccount->getCompany() : "Not Available";
		$contactName = (!is_null($userAccount->getFullName())) ? $userAccount->getFullName() : "Not Available";
		$contactNumber = (!is_null($userAccount->getTelephone())) ? $userAccount->getTelephone() : "Not Available";
		$returnAddress = (!is_null($userAccount->getReturnAddress())) ? $userAccount->getReturnAddress() : "Not Available";
		$billingCurrency = (!is_null($userAccount->getBillingCurrency())) ? $userAccount->getBillingCurrency() : "Not Available";
		$invoiceNumber = (!is_null($userAccount->getInvoicePeriod())) ? $userAccount->getInvoicePeriod() : "Not Available";
		$vatNumber     = (!is_null($userAccount->getVatNumber())) ? $userAccount->getVatNumber() : "Not Available";

		$data =[
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
		$resource = new Hal(null, $data);
		return $resource;
	}

}
