<?php 
/**
 * MAWB CLASS
 *
 */
class ConsignmentCharges extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' => 'number',
            'tariff_id' => 'number',
            'account_id' => 'number',
            'consignment_id' => 'number',
            'invoice_id' => 'number',
            'charge_type_id' => 'number',
            'agent_id' => 'number',
            'cost_type' => 'string',
            'cost' => 'string',
            'cost_currency' => 'string',
            'cost_supplier_currency' => 'number',
            'supplier_currency' => 'string',
            'cost_company_currency' => 'number',
            'company_currency' => 'string',
            'description' => 'string',
            'changes_reference' => 'string',
            'added_date' => 'datetime',
            'added_by' => 'number',
            'updated_date' => 'datetime',
            'updated_by' => 'number',
            'title' => 'undefined',
            'is_vat' => 'undefined',
            'charges_key' => 'undefined',
            'name' => 'undefined',
            'is_extra_charge' => 'undefined'
        );

        parent::__construct("consignment_charges", 'id', $fieldList, $mixedCreator);
    }

    /**
     * Get object Id (not provided as magic method) - read only.
     *
     */
    public function getId() {
        return $this->valArray["id"];
    }

    /**
     * Get list of user objects, using sql given
     *
     * @param string $sql
     */
    public static function getConsignmentChargesListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfConsignmentChargesFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    public static function getDataFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getDataByConsignmentId($consignment_id) {
        $sql = "SELECT * FROM consignment_charges WHERE consignment_id = '$consignment_id'";
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function deteteDataByConsignmentId($consignment_id) {
        if (!empty($consignment_id)) {
            $sql = "DELETE FROM consignment_charges WHERE consignment_id =  '$consignment_id' ";
            DbAccess3::runQuery($sql);
        }
        return;
    }

    public static function updateInvoiceId($accountId, $consignmentId, $invoiceId) {
        $sql = "UPDATE 
                    consignment_charges 
                  SET
                    invoice_id=" . $invoiceId . " 
                  WHERE account_id=" . $accountId . " 
                    AND consignment_id=" . $consignmentId;
        return DbAccess3::runQuery($sql);
    }

    public static function deleteRemoteareaCharges($consignmentId, $userAccountId) {
        $sql = "DELETE FROM consignment_charges WHERE consignment_id =  '$consignmentId' AND account_id = '$userAccountId' AND charge_type_id = '8' AND cost_type='customer'";
        DbAccess3::runQuery($sql);
    }

    public static function getTotalNotInvoicedConsignmentChargesByAccountId($accountId) {
        
        $retunObj = "";
        if (!empty($accountId)) {
            $accountIdIn = "";
            if (is_array($accountId)) {
                $accountIdIn = " IN (" . implode(",", $accountId) . ")";
            } else {
                $accountIdIn = " IN ('" . $accountId . "')";
            }
            $sql = " 
                    SELECT 
                        SUM(cc.`cost`) AS `cost` 
                      FROM
                        `consignment_charges` cc 
                        JOIN consignment c 
                          ON c.`id` = cc.`consignment_id` 
                          AND c.`shipment_status` NOT IN ('11', '12', '22', '23') 
                      WHERE cc.`account_id`  $accountIdIn
                            AND (cc.`invoice_id` IS NULL 
                            OR cc.`invoice_id` = '' )
                            AND cc.`cost_type` = 'customer'
                     ";
            $retunObj = DbAccess3::getListFromSql(__CLASS__, $sql);
        }
        return $retunObj;
    }

    public static function getTariffCurrency($service_id=0, $account_id=0, $tariff_type = 'customer'){
        $sqlTariffCurrencyQuery = "SELECT 
                    c.rightsymbol as supplier_currency
                FROM
                    tariffs t INNER join currency c ON t.currency_id = c.id
                WHERE
                    service_id = '".$service_id."'
                        AND start_date <= now()
                        AND end_date >= now()
                        AND user_account_id = '".$account_id."'
                        AND status = 1
                        AND tariff_type = '".$tariff_type."' order by t.id desc limit 1";
                $resultTariffCurrency = DbAccess3::runQuery($sqlTariffCurrencyQuery);
                $objTariffCurrency = mysqli_fetch_assoc($resultTariffCurrency);
                $tariffCurrency = $objTariffCurrency['supplier_currency']; 
                return $tariffCurrency;
    }
    public static function updateChargesByConsignmentIds($consignment_ids,$formData, $replaceAll = false, $csvPricing = false) {
        
       
        $user = SessionManager::getUser();
        // UserAccountCurrency
        $companyAccountCurrencyObj = new CustomerAccount($user->getUserAccountId());
       $companyAccountCurrency = $companyAccountCurrencyObj->getBillingCurrency();
       
       $consignmentChargesName = [];
        $consignmentChargesTypeFilter = new ConsignmentChargesTypesFilter();
        $consignmentChargesTypeFilter->addFilter("    status = 1 and is_delete = 0");
        $consignmentChargesType = $consignmentChargesTypeFilter->getList();
        if(count($consignmentChargesType)>0)
        {
            foreach ($consignmentChargesType as $consignmentChargesTypeItems)
                $consignmentChargesName[$consignmentChargesTypeItems->getId()] = $consignmentChargesTypeItems->getTitle();
        }
        
        $date_added = time();
        $added_by = $user->getId();
        $date_update = time();
        $update_by = $user->getId();
        $ipAddress = getClientIp();

        $apply_per_kg = isset($formData['apply_per_kg']) ? $formData['apply_per_kg'] : '0';
        $per_shipment = isset($formData['per_shipment']) ? $formData['per_shipment'] : '0';
        $reference = isset($formData['reference']) ? $formData['reference'] : '';
        $purchaseReference = isset($formData['purchase_invoice_reference']) ? $formData['purchase_invoice_reference'] : '';
        
        $totalWeight = 1;
        if ($apply_per_kg > 0) {
            $conFilter = new ConsignmentFilter();
            $conFilter->addFilter(" c.id in ('".implode("','", $consignment_ids)."')",'filter');
            $totalWeightObj = $conFilter->getConsignmentTotalWeight();
            $totalWeight = $totalWeightObj[0]->getWeight();
        }
        $invoiceId = '';
        $references = [];
        if($replaceAll) {
            if(isset($formData['invoice_id']) && $formData['invoice_id'] > 0) {
                $invoiceId = $formData['invoice_id'];
            }
        }
        if(trim($formData['reference'])!= '')
        {
            $references = $formData['reference'];
        }
        foreach ($consignment_ids as $id) {
            $logdataArray = [];
            $logdataArray['new'] = '';
            $logdataArray['old'] = '';
            
            
            $output = [];
            $consignmentObj = new Consignment($id);
            
            
            
            $c_weight = $consignmentObj->getWeight();
            $c_vol_weight = $consignmentObj->getVolWeight();
            $numberPieces = $consignmentObj->getNumberPieces();
            if ($c_vol_weight > $c_weight) {
                $c_weight = $c_vol_weight;
            }
            // checked consignment invoiced
            $checkInvoiced = Consignment::checkConsignmentInvoiced($id);
            // user account for pricing
            $user_account_id = Consignment::getConsignmentUserAccountIdForPricing($id);
            //UserAccountObject for Currency
            $userAccountCurrencyObj  = new CustomerAccount($user_account_id);
            $checkVatChargable = $userAccountCurrencyObj->getVatChargable();
            $userBillingCurrency = $userAccountCurrencyObj->getBillingCurrency();

            $billingCurrenctFilter = new CurrencyFilter();
            $billingCurrenctFilter->addFieldFilter('    rightsymbol',$userBillingCurrency);
            $billingCurrenctFilterObj = $billingCurrenctFilter->getList();
            $userBillingCurrencyId = '2';
            if(count($billingCurrenctFilterObj)) {
                $userBillingCurrencyId = $billingCurrenctFilterObj[0]->getId();
            }

            if(trim($formData['currency']) !='')
                $userSelectedCurrency = trim($formData['currency']);
            else
                $userSelectedCurrency = $userBillingCurrency;


            /* Check amount before update */
            $consignmentCharges = ConsignmentCharges::getConsignmentTotalCharges($id,$user_account_id);
            $oldConsignmentChargesTotalBeforeUpdate = $consignmentCharges['cost'];


            
            /*********************Conversion Currency For db***************************************/

            /************************************************************/    
            
            $output['pricing_user_account_id'] = $user_account_id;
            $output['pricing_purchase_status'] = '';
            // get total charges
            $oldChargesDataForAudit = [];
            $newChargesDataForAudit = [];
            $oldTotalCost = 0.00;
            $oldChargesData = [];
            if ($checkInvoiced == 0) {
                $oldConsignmentChargesFilter = new ConsignmentChargesFilter();
                $oldConsignmentChargesFilter->addFieldFilter("    cc.consignment_id", $id);
                $oldConsignmentChargesFilter->addFieldFilter("    cc.account_id", $user_account_id);
                $oldConsignmentChargesFilter->addFieldFilter("    cc.cost_type", "customer");
                $oldConsignmentChargesFilterObj = $oldConsignmentChargesFilter->getColumnList("*");
                $oldConsignmentChargesFilterSumObj = $oldConsignmentChargesFilter->getColumnList("SUM(cost) AS cost");
                if (count($oldConsignmentChargesFilterObj) > 0) {
                    foreach ($oldConsignmentChargesFilterObj as $datum) {
                        $oldChargesDataForAudit[] = [
                            'invoice_id' => $datum->getInvoiceId(),
                            'charge_type_id' => $datum->getChargeTypeId(),
                            'cost_type' => $datum->getCostType(),
                            'cost' => $datum->getCost(),
                            'cost_currency' => $datum->getCostCurrency(),
                            'cost_supplier_currency' => $datum->getCostSupplierCurrency(),
                            'supplier_currency' => $datum->getSupplierCurrency(),
                            'cost_company_currency' => $datum->getCostCompanyCurrency(),
                            'company_currency' => $datum->getCompanyCurrency(),
                            'description' => $datum->getDescription(),
                        ];
                    }
                    $oldTotalCost = $oldConsignmentChargesFilterSumObj[0]->getCost();
                    $logdataArray['old'] = $oldChargesData = serialize($oldConsignmentChargesFilterObj);
                    if($replaceAll) {
                        ConsignmentCharges::deteteDataByConsignmentId($id);
                    }
                }
            }

            if(isset($formData['consignment_price']['customer'])) {
                $tariffCurrency = self::getTariffCurrency($consignmentObj->getServiceId(), $user->getUserAccountId(),'customer' );
                $supplierCurrency = ((trim($tariffCurrency)=='')?$userBillingCurrency:$tariffCurrency);
                $totalPriceForVat = 0;
                if ($checkInvoiced == 0) {
                    foreach ($formData['consignment_price']['customer'] as $charge_type_id => $cost) {
                        $consignmentChargesArray = self::PriceAllCurrencyConversion($userSelectedCurrency,$userBillingCurrency, $companyAccountCurrency, $supplierCurrency, $cost);
                        extract($consignmentChargesArray);
                        //////////////////Cost Converted into supplier Currency
                        //////////////////Cost Converted into User Currency
                        //////////////////Cost Converted into Company Currency
                        
                        if (($cost != '') && ($cost > 0) && (is_numeric($charge_type_id))) {
                            $consignmentChargesType = new ConsignmentChargesTypes($charge_type_id);
                            if($consignmentChargesType->getIsVat() == 1) {
                                $totalPriceForVat = $totalPriceForVat + $customerPrice;
                            }
                            $consignmentChargesObj = new ConsignmentCharges();
                            $consignmentChargesFilter = new ConsignmentChargesFilter();
                            $consignmentChargesFilter->addFilter("     cc.account_id = '" . $user_account_id . "' AND cc.consignment_id = '" . $id . "' AND cc.charge_type_id = '" . $charge_type_id . "' AND cc.cost_type = 'customer'");
                            $consignmentCharges = $consignmentChargesFilter->getColumnList("cc.consignment_id,cc.charge_type_id,cc.invoice_id");
                            $auditDesc = '';
                            if (count($consignmentCharges) > 0) {
                                $chargeId = $consignmentCharges[0]->getId();
                                $consignmentChargesObj = new ConsignmentCharges($chargeId);
                            }
                            $consignmentChargesObj->setDescription("Manual Pricing search");
                            $auditDesc = 'Manual Pricing search';
                            if($replaceAll) {
                                $consignmentChargesObj->setInvoiceId($invoiceId);
                                $consignmentChargesObj->setDescription("Manual Pricing Details");
                                $auditDesc = 'Manual Pricing Details';
                            }
                            if($csvPricing) {
                                $consignmentChargesObj->setDescription("CSV Upload Price");
                                $auditDesc = 'CSV Upload Price';
                            }
                            $logdataArray['new'] .= "Customer ".$consignmentChargesName[$charge_type_id]."=>".$customerPrice . "<br>";
                                    
                            $consignmentChargesObj->setAccountId($user_account_id);
                            $consignmentChargesObj->setConsignmentId($id);
                            $consignmentChargesObj->setChargeTypeId($charge_type_id);
                            $consignmentChargesObj->setCostType('customer');
                            $consignmentChargesObj->setCost($customerPrice);
                            $consignmentChargesObj->setCostCurrency($userBillingCurrency);
                            $consignmentChargesObj->setCostSupplierCurrency($supplierPrice);
                            $consignmentChargesObj->setSupplierCurrency($supplierCurrency);
                            $consignmentChargesObj->setCostCompanyCurrency($companyPrice);
                            $consignmentChargesObj->setCompanyCurrency($companyAccountCurrency);
                            $consignmentChargesObj->setAddedBy($added_by);
                            $consignmentChargesObj->setAddedDate($date_added);
                            $consignmentChargesObj->setUpdatedBy($update_by);
                            $consignmentChargesObj->setUpdatedDate($date_update);
                            $consignmentChargesObj->saveLog = false;
                            $newChargesDataForAudit[] = [
                                'invoice_id' => $invoiceId,
                                'charge_type_id' => $charge_type_id,
                                'cost_type' => 'customer',
                                'cost' => $customerPrice,
                                'cost_currency' => $userBillingCurrency,
                                'cost_supplier_currency' => $supplierPrice,
                                'supplier_currency' => $supplierCurrency,
                                'cost_company_currency' => $companyPrice,
                                'company_currency' => $$companyAccountCurrency,
                                'description' => $auditDesc,
                            ];
                            $consignmentChargesObj->save();
                        }
                        if ( (isset($formData['zero_pricing'])) && ($formData['zero_pricing'] > 0) && (is_numeric($charge_type_id))) {
                            
                            $consignmentChargesObj = new ConsignmentCharges();
                            $consignmentChargesFilter = new ConsignmentChargesFilter();
                            $consignmentChargesFilter->addFilter("      account_id = '" . $user_account_id . "' AND consignment_id = '" . $id . "' AND charge_type_id = '" . $charge_type_id . "' AND cost_type = 'customer'");
                            $consignmentCharges = $consignmentChargesFilter->getColumnList("id, charge_type_id, invoice_id");
                            if (count($consignmentCharges) > 0) {
                                $chargeId = $consignmentCharges[0]->getId();
                                $consignmentChargesObj = new ConsignmentCharges($chargeId);
                            }
                            $logdataArray['new'] .= "Customer ".$consignmentChargesName[$charge_type_id]."=> 0 <br>";
                            $consignmentChargesObj->setAccountId($user_account_id);
                            $consignmentChargesObj->setConsignmentId($id);
                            $consignmentChargesObj->setChargeTypeId($charge_type_id);
                            $consignmentChargesObj->setCostType('customer');
                            $consignmentChargesObj->setCost(0);
                            $consignmentChargesObj->setCostCurrency($userBillingCurrency);
                            $consignmentChargesObj->setCostSupplierCurrency(0);
                            $consignmentChargesObj->setSupplierCurrency($userBillingCurrency);
                            $consignmentChargesObj->setCostCompanyCurrency(0);
                            $consignmentChargesObj->setCompanyCurrency($companyAccountCurrency);
                            $consignmentChargesObj->setAddedBy($added_by);
                            $consignmentChargesObj->setAddedDate($date_added);
                            $consignmentChargesObj->setUpdatedBy($update_by);
                            $consignmentChargesObj->setUpdatedDate($date_update);
                            $consignmentChargesObj->save();
                        }
                    }
                    //////////////////Save Vat charges
                    /// calculate vat 20%
                    $fromCountry = $consignmentObj->getSenderCountryId();
                    $toCountry = $consignmentObj->getCountryId();
                    $vatApplicable = Consignment::checkVatChargable($fromCountry,$toCountry);
                    if($checkVatChargable &&  $vatApplicable == 2) {
                        $cost = ((20 / 100) * $totalPriceForVat);
                        if ((isset($formData['zero_pricing'])) && ($formData['zero_pricing'] > 0)) {
                            $cost = 0;
                        }
                        $consignmentChargesArray = self::PriceAllCurrencyConversion($userSelectedCurrency, $userBillingCurrency, $companyAccountCurrency, $supplierCurrency, $cost);
                        extract($consignmentChargesArray);
                        $chargesType = new ConsignmentChargesTypesFilter();
                        $chargesType->addFieldFilter('      cct.charges_key', 'VAT');
                        $chargesTypeObj = $chargesType->getList();
                        $charge_type_id = 28;
                        if (count($chargesTypeObj)) {
                            $charge_type_id = $chargesTypeObj[0]->getId();
                        }
                        $consignmentChargesObj = new ConsignmentCharges();
                        $consignmentChargesFilter = new ConsignmentChargesFilter();
                        $consignmentChargesFilter->addFilter("     cc.account_id = '" . $user_account_id . "' AND cc.consignment_id = '" . $id . "' AND cc.charge_type_id = '" . $charge_type_id . "' AND cc.cost_type = 'customer'");
                        $consignmentCharges = $consignmentChargesFilter->getColumnList("cc.consignment_id,cc.charge_type_id,cc.invoice_id");
                        if (count($consignmentCharges) > 0) {
                            $chargeId = $consignmentCharges[0]->getId();
                            $consignmentChargesObj = new ConsignmentCharges($chargeId);
                        }
                        $auditDesc = '';
                        $consignmentChargesObj->setDescription("Manual Pricing search");
                        $auditDesc = 'Manual Pricing search';
                        if ($replaceAll) {
                            $consignmentChargesObj->setInvoiceId($invoiceId);
                            $consignmentChargesObj->setDescription("Manual Pricing Details");
                            $auditDesc = 'Manual Pricing Details';
                        }
                        if ($csvPricing) {
                            $consignmentChargesObj->setDescription("CSV Upload Price");
                            $auditDesc = 'CSV Upload Price';
                        }
                        $consignmentChargesObj->setAccountId($user_account_id);
                        $consignmentChargesObj->setConsignmentId($id);
                        $consignmentChargesObj->setChargeTypeId($charge_type_id);
                        $consignmentChargesObj->setCostType('customer');
                        $consignmentChargesObj->setCost($customerPrice);
                        $consignmentChargesObj->setCostCurrency($userBillingCurrency);
                        $consignmentChargesObj->setCostSupplierCurrency($supplierPrice);
                        $consignmentChargesObj->setSupplierCurrency($supplierCurrency);
                        $consignmentChargesObj->setCostCompanyCurrency($companyPrice);
                        $consignmentChargesObj->setCompanyCurrency($companyAccountCurrency);
                        $consignmentChargesObj->setAddedBy($added_by);
                        $consignmentChargesObj->setAddedDate($date_added);
                        $consignmentChargesObj->setUpdatedBy($update_by);
                        $consignmentChargesObj->setUpdatedDate($date_update);
                        $consignmentChargesObj->saveLog = false;
                        $newChargesDataForAudit[] = [
                            'invoice_id' => $invoiceId,
                            'charge_type_id' => $charge_type_id,
                            'cost_type' => 'customer',
                            'cost' => $customerPrice,
                            'cost_currency' => $userBillingCurrency,
                            'cost_supplier_currency' => $supplierPrice,
                            'supplier_currency' => $supplierCurrency,
                            'cost_company_currency' => $companyPrice,
                            'company_currency' => $$companyAccountCurrency,
                            'description' => $auditDesc,
                        ];
                        $consignmentChargesObj->save();
                    }

                    $consignmentChargesAudit = new ConsignmentCharges();
                    $consignmentChargesAudit->tableKey = $id;
                    $consignmentChargesAudit->logMoreDataOld['pricing'] = $oldChargesDataForAudit;
                    $consignmentChargesAudit->logMoreDataNew['pricing'] = $newChargesDataForAudit;
                    $consignmentChargesAudit->auditTableName = 'consignment';
                    $consignmentChargesAudit->custom_message = $user->getFirstName() . ' ' . $user->getLastName() . ' updated consignment pricing';
                    $consignmentChargesAudit->saveAuditData();
                }
                /* Check amount after update */
				if($userAccountCurrencyObj->getIsPrepaid() == 1){
					$consignmentCharges = ConsignmentCharges::getConsignmentTotalCharges($id, $user_account_id);
					$newConsignmentChargesTotalAfterUpdate = $consignmentCharges['cost'];
					if ($newConsignmentChargesTotalAfterUpdate != $oldConsignmentChargesTotalBeforeUpdate) {
						$msg = "Shipment [" . $consignmentObj->getHawb() . "] payment";
						$debitCharges = 0.00;
						$creditCharges = 0.00;
						if ($newConsignmentChargesTotalAfterUpdate > $oldConsignmentChargesTotalBeforeUpdate) {
							$debitCharges = $newConsignmentChargesTotalAfterUpdate - $oldConsignmentChargesTotalBeforeUpdate;
						} else if ($newConsignmentChargesTotalAfterUpdate < $oldConsignmentChargesTotalBeforeUpdate) {
							$creditCharges = $oldConsignmentChargesTotalBeforeUpdate - $newConsignmentChargesTotalAfterUpdate;
							$msg = "Shipment [" . $consignmentObj->getHawb() . "] payment (refund)";
						}
						$chargeNewAmount = $debitCharges;
						if ($creditCharges > 0) {
							$chargeNewAmount = $creditCharges;
						}
						// Add payment transcation into payment history table for invoice charges
						$paymentsHistory = new PaymentsHistory();
						$paymentsHistory->setAccountId($userAccountCurrencyObj->getId());
						$paymentsHistory->setAmount($chargeNewAmount);
						$paymentsHistory->setAmountCurrencyId($userBillingCurrencyId);
						$paymentsHistory->setPaymentDetail($msg);
						$paymentsHistory->setUserCurrencyId($userBillingCurrencyId);
						$paymentsHistory->setDebit($debitCharges);
						$paymentsHistory->setCredit($creditCharges);
						$paymentsHistory->setPaymentStatus("completed");
						$paymentsHistory->setIsCompleted("yes");
						$paymentsHistory->setDateAdded(time());
						$paymentsHistory->setAddedBy($added_by);
						$paymentsHistory->save();
						/* update account balance */
						CustomerAccount::updateBalance($userAccountCurrencyObj->getId());
						/******************************/
					}
				}
            }
            if(isset($formData['consignment_price']['agent'])) {
                $tariffCurrency = self::getTariffCurrency($consignmentObj->getServiceId(), $user->getUserAccountId(),'supplier' );
                $supplierCurrency = ((trim($tariffCurrency)=='')?$userBillingCurrency:$tariffCurrency);
                
                foreach ($formData['consignment_price']['agent'] as $charge_type_id => $cost) {
                    $consignmentChargesArray = self::PriceAllCurrencyConversion($userSelectedCurrency,$userBillingCurrency, $companyAccountCurrency, $supplierCurrency, $cost);
                    extract($consignmentChargesArray);
                    
                    if (($cost != '') && ($cost > 0) && (is_numeric($charge_type_id))) {
                        if (($apply_per_kg > 0) || ($per_shipment > 0)) {
                            $consignmentChargesTypesObj = new ConsignmentChargesTypes($charge_type_id);
                        }
                        if ($apply_per_kg > 0) {
                            if (($consignmentChargesTypesObj->getApplyPerKg() == 1)) {
                                $cost = (($cost / $totalWeight) * $c_weight);
                                if ($per_shipment == 0) {
                                    $cost = $cost * $numberPieces;
                                }
                            }
                        }
                        $cost = number_format((float) $cost, 2, '.', '');
                        $consignmentChargesObj = new ConsignmentCharges();
                        $consignmentChargesFilter = new ConsignmentChargesFilter();
                        $consignmentChargesFilter->addFilter("      account_id = '" . $user_account_id . "' AND consignment_id = '" . $id . "' AND charge_type_id = '" . $charge_type_id . "' AND cost_type = 'agent'");
                        $consignmentCharges = $consignmentChargesFilter->getColumnList("id, charge_type_id");
                        if (count($consignmentCharges) > 0) {
                            $chargeId = $consignmentCharges[0]->getId();
                            $chargeTypeId = $consignmentCharges[0]->getChargeTypeId();
                            $consignmentChargesObj = new ConsignmentCharges($chargeId);
                            $consignmentChargesType = new ConsignmentChargesTypes($chargeTypeId);
                            if(!$replaceAll) {
                                if ($consignmentChargesType->getIsReplaceCharges() == 0) {
                                    $cost = $cost + $consignmentChargesObj->getCost();
                                }
                            }
                        }
                        $consignmentChargesObj->setDescription("Manual Pricing search");
                        if($replaceAll) {
                            $consignmentChargesObj->setChangesReference($references[$charge_type_id]);
                            $consignmentChargesObj->setDescription("Manual Pricing Details");
                        }
                        if($csvPricing) {
                            $consignmentChargesObj->setDescription("CSV Upload Price");
                        }
                        $logdataArray['new'] .= "Agent ".$consignmentChargesName[$charge_type_id]."=>".$customerPrice. "<br>";
                        $consignmentChargesObj->setAccountId($user_account_id);
                        $consignmentChargesObj->setConsignmentId($id);
                        $consignmentChargesObj->setChargeTypeId($charge_type_id);
                        $consignmentChargesObj->setCostType('agent');
                        $consignmentChargesObj->setCost($customerPrice);
                            $consignmentChargesObj->setCostCurrency($userBillingCurrency);
                            $consignmentChargesObj->setCostSupplierCurrency($supplierPrice);
                            $consignmentChargesObj->setSupplierCurrency($supplierCurrency);
                            $consignmentChargesObj->setCostCompanyCurrency($companyPrice);
                            $consignmentChargesObj->setCompanyCurrency($companyAccountCurrency);
                        $consignmentChargesObj->setAddedBy($added_by);
                        $consignmentChargesObj->setAddedDate($date_added);
                        $consignmentChargesObj->setUpdatedBy($update_by);
                        $consignmentChargesObj->setUpdatedDate($date_update);
                        $consignmentChargesObj->save();
                        /* Update reference charges  */
                        if (count($formData['consignment_price']['agent']) > 0 && $reference != "") {
                            $sql = "UPDATE
                                                `consignment_charges`
                                            SET
                                                changes_reference = CONCAT((CASE WHEN (changes_reference = '' OR changes_reference IS NULL) THEN '' ELSE CONCAT(changes_reference,',') END),'" . DbAccess3::escape($reference) . "')
                                            WHERE account_id = '" . DbAccess3::escape($user_account_id) . "'
                                                AND consignment_id = '" . DbAccess3::escape($id) . "'
                                                AND cost_type = 'agent'
                                                AND charge_type_id = '" . DbAccess3::escape($charge_type_id) . "'";
                            DbAccess3::runQuery($sql);
                        }
                    }
                }
            }
            
            if(isset($formData['consignment_price']['purchase_invoice'])) {
                
                $tariffCurrency = self::getTariffCurrency($consignmentObj->getServiceId(), $user->getUserAccountId(),'supplier' );
                $supplierCurrency = ((trim($tariffCurrency)=='')?$userSelectedCurrency:$tariffCurrency);
              
//                $resultSupplierCurrency = DbAccess3::runQuery($sqlSupplierCurrencyQuery);
//                $objSupplierCurrency = mysqli_fetch_assoc($resultSupplierCurrency);
//                $supplierCurrency = $objSupplierCurrency['supplier_currency']; 
                foreach ($formData['consignment_price']['purchase_invoice'] as $charge_type_id => $cost) {
                    $consignmentChargesArray = self::PriceAllCurrencyConversion($userSelectedCurrency,$userBillingCurrency, $companyAccountCurrency, $supplierCurrency, $cost);
                    extract($consignmentChargesArray);
                    if (($cost != '') && ($cost > 0) && (is_numeric($charge_type_id))) {
                        
                        $consignmentChargesTypesObj = new ConsignmentChargesTypes($charge_type_id);
                        if ($apply_per_kg > 0) {
                            if (($consignmentChargesTypesObj->getApplyPerKg() == 1)) {
                                $cost = (($cost / $totalWeight) * $c_weight);
                                if ($per_shipment == 0) {
                                    $cost = $cost * $numberPieces;
                                }
                            }
                        }
                        $cost = number_format((float) $cost, 2, '.', '');
                        $consignmentChargesObj = new ConsignmentCharges();
                        $consignmentChargesFilter = new ConsignmentChargesFilter();
                        $consignmentChargesFilter->addFilter("      account_id = '" . $user_account_id . "' AND consignment_id = '" . $id . "' AND charge_type_id = '" . $charge_type_id . "' AND cost_type = 'purchase_invoice'");
                        $consignmentCharges = $consignmentChargesFilter->getColumnList("id, charge_type_id" );
                        $saveNewPrices  = true;
                        $updateVat      = false;
                        if (count($consignmentCharges) > 0) {
                            $chargeId = $consignmentCharges[0]->getId();
                            $chargeTypeId = $consignmentCharges[0]->getChargeTypeId();
                            $consignmentChargesObj = new ConsignmentCharges($chargeId);
                            $consignmentChargesType = new ConsignmentChargesTypes($chargeTypeId);
                            if(!$replaceAll) {
                                if ($consignmentChargesType->getIsReplaceCharges() == 0) {
                                    $cost = $cost + $consignmentChargesObj->getCost();
                                }
                            }
                            $output['pricing_purchase_status'][$consignmentChargesTypesObj->getTitle()] = [$consignmentChargesObj->getCost(),$consignmentChargesObj->getChangesReference()];
                            $saveNewPrices = false;
                            if($charge_type_id == 28){
                                $updateVat = true;
                            }
                        }
                        $consignmentChargesObj->setDescription("Manual Pricing search");
                        
                        if(trim($consignmentChargesObj->getChangesReference()) != '')
                            $finalReference = $consignmentChargesObj->getChangesReference()." | ".$purchaseReference;
                        else
                            $finalReference = $purchaseReference;
                        if($replaceAll) {
                            $consignmentChargesObj->setChangesReference($finalReference);
                            $consignmentChargesObj->setDescription("Manual Pricing Details");
                        }
                        else
                        {
                            $consignmentChargesObj->setChangesReference($finalReference);
                            $consignmentChargesObj->setDescription("Manual Pricing Details");
                        }
                        if($csvPricing) {
                            if(trim($consignmentChargesObj->getChangesReference())== '')
                                $consignmentChargesObj->setChangesReference($finalReference);
                            else
                                $consignmentChargesObj->setChangesReference($finalReference);
                            $consignmentChargesObj->setDescription("CSV Upload Price");
                        }
                        
                        $logdataArray['new'] .= "Purchase Invoice ".$consignmentChargesName[$charge_type_id]."=>".$customerPrice. "<br>";
                        $consignmentChargesObj->setAccountId($user_account_id);
                        $consignmentChargesObj->setConsignmentId($id);
                        $consignmentChargesObj->setChargeTypeId($charge_type_id);
                        $consignmentChargesObj->setCostType('purchase_invoice');
                        $consignmentChargesObj->setCost($customerPrice);
                        $consignmentChargesObj->setCostCurrency($userBillingCurrency);
                        $consignmentChargesObj->setCostSupplierCurrency($supplierPrice);
                        $consignmentChargesObj->setSupplierCurrency($supplierCurrency);
                        $consignmentChargesObj->setCostCompanyCurrency($companyPrice);
                        $consignmentChargesObj->setCompanyCurrency($companyAccountCurrency);
                        $consignmentChargesObj->setAddedBy($added_by);
                        $consignmentChargesObj->setAddedDate($date_added);
                        $consignmentChargesObj->setUpdatedBy($update_by);
                        $consignmentChargesObj->setUpdatedDate($date_update);
                        if ($saveNewPrices){
                            $consignmentChargesObj->save();
                            /* Update reference charges  */
                            if (count($formData['consignment_price']['purchase_invoice']) > 0 && $purchaseReference != "") {
                                $sql = "UPDATE
                                                    `consignment_charges`
                                                SET
                                                    changes_reference = CONCAT((CASE WHEN (changes_reference = '' OR changes_reference IS NULL) THEN '' ELSE CONCAT(changes_reference,',') END),'" . DbAccess3::escape($purchaseReference) . "')
                                                WHERE account_id = '" . DbAccess3::escape($user_account_id) . "'
                                                    AND consignment_id = '" . DbAccess3::escape($id) . "'
                                                    AND cost_type = 'purchase_invoice'
                                                    AND charge_type_id = '" . DbAccess3::escape($charge_type_id) . "'";
                              //  DbAccess3::runQuery($sql);
                            }
                        }  else if($updateVat){
                             $sqlVatUpdate = "UPDATE
                                        `consignment_charges`
                                    SET
                                        changes_reference = CONCAT((CASE WHEN (changes_reference = '' OR changes_reference IS NULL) THEN '' ELSE CONCAT(changes_reference,',') END),'" . DbAccess3::escape($purchaseReference) . "'),
                                        cost  = (cost + $customerPrice),
                                        cost_supplier_currency = (cost_supplier_currency + $supplierPrice),
                                        cost_company_currency= (cost_company_currency + $companyPrice)
                                    WHERE account_id = '" . DbAccess3::escape($user_account_id) . "'
                                        AND consignment_id = '" . DbAccess3::escape($id) . "'
                                        AND cost_type = 'purchase_invoice'
                                        AND charge_type_id = '28'";
                                DbAccess3::runQuery($sqlVatUpdate);
                        } else {
                            $output['status'] = 'error';
                            $output['message'] = 'Price cannot be updated. Already available in system';
                        }
                        
                    }
                }
            }
            if(isset($formData['customer_agent_id'])) {
                $tariffCurrency = self::getTariffCurrency($consignmentObj->getServiceId(), $user->getUserAccountId(),'customer' );
                $supplierCurrency = ((trim($tariffCurrency)=='')?$userBillingCurrency:$tariffCurrency);
                foreach ($formData['customer_agent_id'] as $key => $agent_id) {
                    if ($checkInvoiced == 0) {
                        $cost = $formData['customer_extra_total'][$key];
                        $charge_type_id = $formData['customer_charge_type_id'][$key];
                        if (($apply_per_kg > 0) || ($per_shipment > 0)) {
                            $consignmentChargesTypesObj = new ConsignmentChargesTypes($charge_type_id);
                        }
                        if ($apply_per_kg > 0) {
                            if (($consignmentChargesTypesObj->getApplyPerKg() == 1)) {
                                $cost = (($cost / $totalWeight) * $c_weight);
                                if ($per_shipment == 0) {
                                    $cost = $cost * $numberPieces;
                                }
                            }
                        }
                        $cost = number_format((float) $cost, 2, '.', '');
                        $consignmentChargesArray = self::PriceAllCurrencyConversion($userSelectedCurrency,$userBillingCurrency, $companyAccountCurrency, $supplierCurrency, $cost);
                        extract($consignmentChargesArray);
                        $consignmentChargesObj = new ConsignmentCharges();
                        $consignmentChargesFilter = new ConsignmentChargesFilter();
                        $consignmentChargesFilter->addFilter("     account_id = '" . $user_account_id . "' AND consignment_id = '" . $id . "' AND charge_type_id = '" . $charge_type_id . "' AND cost_type = 'customer' AND agent_id = '" . $agent_id . "'");
                        $consignmentCharges = $consignmentChargesFilter->getColumnList("id, charge_type_id,invoice_id");
                        if (count($consignmentCharges) > 0) {
                            $chargeId = $consignmentCharges[0]->getId();
                            $consignmentChargesObj = new ConsignmentCharges($chargeId);
                        }
                        
                        $logdataArray['new'] .= "Customer ".$consignmentChargesName[$charge_type_id]."=>".$customerPrice. "<br>";
                        
                        $consignmentChargesObj->setAccountId($user_account_id);
                        $consignmentChargesObj->setConsignmentId($id);
                        $consignmentChargesObj->setAgentId($agent_id);
                        $consignmentChargesObj->setChargeTypeId($charge_type_id);
                        $consignmentChargesObj->setCostType('customer');
                        $consignmentChargesObj->setCost($customerPrice);
                        $consignmentChargesObj->setCostCurrency($userBillingCurrency);
                        $consignmentChargesObj->setCostSupplierCurrency($supplierPrice);
                        $consignmentChargesObj->setSupplierCurrency($supplierCurrency);
                        $consignmentChargesObj->setCostCompanyCurrency($companyPrice);
                        $consignmentChargesObj->setCompanyCurrency($companyAccountCurrency);
                        $consignmentChargesObj->setDescription($formData['customer_description'][$key]);
                        $consignmentChargesObj->setAddedBy($added_by);
                        $consignmentChargesObj->setAddedDate($date_added);
                        $consignmentChargesObj->setUpdatedBy($update_by);
                        $consignmentChargesObj->setUpdatedDate($date_update);
                        $consignmentChargesObj->save();
                    }
                }
            }
            if(isset($formData['agent_agent_id'])) {
                $tariffCurrency = self::getTariffCurrency($consignmentObj->getServiceId(), $user->getUserAccountId(),'supplier' );
                $supplierCurrency = ((trim($tariffCurrency)=='')?$userBillingCurrency:$tariffCurrency);
                foreach ($formData['agent_agent_id'] as $key => $agent_id) {
                    $cost = $formData['agent_extra_total'][$key];
                    $charge_type_id = $formData['agent_charge_type_id'][$key];
                    if (($apply_per_kg > 0) || ($per_shipment > 0)) {
                        $consignmentChargesTypesObj = new ConsignmentChargesTypes($charge_type_id);
                    }
                    if ($apply_per_kg > 0) {
                        if (($consignmentChargesTypesObj->getApplyPerKg() == 1)) {
                            $cost = (($cost / $totalWeight) * $c_weight);
                            if ($per_shipment == 0) {
                                $cost = $cost * $numberPieces;
                            }
                        }
                    }
                    $cost = number_format((float) $cost, 2, '.', '');
                    $consignmentChargesArray = self::PriceAllCurrencyConversion($userSelectedCurrency,$userBillingCurrency, $companyAccountCurrency, $supplierCurrency, $cost);
                    extract($consignmentChargesArray);
                    $consignmentChargesObj = new ConsignmentCharges();
                    $consignmentChargesFilter = new ConsignmentChargesFilter();
                    $consignmentChargesFilter->addFilter("      account_id = '" . $user_account_id . "' AND consignment_id = '" . $id . "' AND charge_type_id = '" . $charge_type_id . "' AND cost_type = 'agent' AND agent_id = '" . $agent_id . "'");
                    $consignmentCharges = $consignmentChargesFilter->getColumnList("id, charge_type_id");
                     if (count($consignmentCharges) > 0) {
                        $chargeId = $consignmentCharges[0]->getId();
                        $chargeTypeId = $consignmentCharges[0]->getChargeTypeId();
                        $consignmentChargesObj = new ConsignmentCharges($chargeId);
                        $consignmentChargesType = new ConsignmentChargesTypes($chargeTypeId);
                        if(!$replaceAll) {
                            if ($consignmentChargesType->getIsReplaceCharges() == 0) {
                                $cost = $cost + $consignmentChargesObj->getCost();
                            }
                        }
                    }
                    
                    $logdataArray['new'] .= "Agent ".$consignmentChargesName[$charge_type_id]."=>".$customerPrice. "<br>";
                    $consignmentChargesObj->setAccountId($user_account_id);
                    $consignmentChargesObj->setConsignmentId($id);
                    $consignmentChargesObj->setAgentId($agent_id);
                    $consignmentChargesObj->setChargeTypeId($charge_type_id);
                    $consignmentChargesObj->setCostType('agent');
                    $consignmentChargesObj->setCost($customerPrice);
                            $consignmentChargesObj->setCostCurrency($userBillingCurrency);
                            $consignmentChargesObj->setCostSupplierCurrency($supplierPrice);
                            $consignmentChargesObj->setSupplierCurrency($supplierCurrency);
                            $consignmentChargesObj->setCostCompanyCurrency($companyPrice);
                            $consignmentChargesObj->setCompanyCurrency($companyAccountCurrency);
                    $consignmentChargesObj->setDescription($formData['agent_description'][$key]);
                    $consignmentChargesObj->setAddedBy($added_by);
                    $consignmentChargesObj->setAddedDate($date_added);
                    $consignmentChargesObj->setUpdatedBy($update_by);
                    $consignmentChargesObj->setUpdatedDate($date_update);
                    $consignmentChargesObj->save();
                }
            }
            if(isset($formData['purchase_invoice_agent_id'])) {
                $tariffCurrency = self::getTariffCurrency($consignmentObj->getServiceId(), $user->getUserAccountId(),'supplier' );
                $supplierCurrency = ((trim($tariffCurrency)=='')?$userSelectedCurrency:$tariffCurrency);
                foreach ($formData['purchase_invoice_agent_id'] as $key => $agent_id) {
                    $cost = $formData['purchase_invoice_extra_total'][$key];
                    $charge_type_id = $formData['purchase_invoice_charge_type_id'][$key];
                    if (($apply_per_kg > 0) || ($per_shipment > 0)) {
                        $consignmentChargesTypesObj = new ConsignmentChargesTypes($charge_type_id);
                    }
                    if ($apply_per_kg > 0) {
                        if (($consignmentChargesTypesObj->getApplyPerKg() == 1)) {
                            $cost = (($cost / $totalWeight) * $c_weight);
                            if ($per_shipment == 0) {
                                $cost = $cost * $numberPieces;
                            }
                        }
                    }
                    $cost = number_format((float) $cost, 2, '.', '');
                    $consignmentChargesArray = self::PriceAllCurrencyConversion($userSelectedCurrency,$userBillingCurrency, $companyAccountCurrency, $supplierCurrency, $cost);
                    extract($consignmentChargesArray);
                    $consignmentChargesObj = new ConsignmentCharges();
                    $consignmentChargesFilter = new ConsignmentChargesFilter();
                    $consignmentChargesFilter->addFilter("      account_id = '" . $user_account_id . "' AND consignment_id = '" . $id . "' AND charge_type_id = '" . $charge_type_id . "' AND cost_type = 'agent' AND agent_id = '" . $agent_id . "'");
                    $consignmentCharges = $consignmentChargesFilter->getColumnList("id, charge_type_id");
                     if (count($consignmentCharges) > 0) {
                        $chargeId = $consignmentCharges[0]->getId();
                        $chargeTypeId = $consignmentCharges[0]->getChargeTypeId();
                        $consignmentChargesObj = new ConsignmentCharges($chargeId);
                        $consignmentChargesType = new ConsignmentChargesTypes($chargeTypeId);
                        if(!$replaceAll) {
                            if ($consignmentChargesType->getIsReplaceCharges() == 0) {
                                $cost = $cost + $consignmentChargesObj->getCost();
                            }
                        }
                    }
                    $logdataArray['new'] .= "Purchase Invoice ".$consignmentChargesName[$charge_type_id]."=>".$customerPrice. "<br>";
                    $consignmentChargesObj->setAccountId($user_account_id);
                    $consignmentChargesObj->setConsignmentId($id);
                    $consignmentChargesObj->setAgentId($agent_id);
                    $consignmentChargesObj->setChargeTypeId($charge_type_id);
                    $consignmentChargesObj->setCostType('purchase_invoice');
                    $consignmentChargesObj->setCost($customerPrice);
                            $consignmentChargesObj->setCostCurrency($userBillingCurrency);
                            $consignmentChargesObj->setCostSupplierCurrency($supplierPrice);
                            $consignmentChargesObj->setSupplierCurrency($supplierCurrency);
                            $consignmentChargesObj->setCostCompanyCurrency($companyPrice);
                            $consignmentChargesObj->setCompanyCurrency($companyAccountCurrency);
                    $consignmentChargesObj->setDescription($formData['agent_description'][$key]);
                    $consignmentChargesObj->setAddedBy($added_by);
                    $consignmentChargesObj->setAddedDate($date_added);
                    $consignmentChargesObj->setUpdatedBy($update_by);
                    $consignmentChargesObj->setUpdatedDate($date_update);
                    $consignmentChargesObj->save();
                }
            }
            
            $newTotalCost = 0.00;
            if ($checkInvoiced == 0) {
                $newChargesData = [];

                $newConsignmentChargesFilter = new ConsignmentChargesFilter();
                $newConsignmentChargesFilter->addFieldFilter("    cc.consignment_id", $id);
                $newConsignmentChargesFilter->addFieldFilter("    cc.account_id", $user_account_id);
                $newConsignmentChargesFilter->addFieldFilter("    cc.cost_type", "customer");
                $newConsignmentChargesFilterObj = $newConsignmentChargesFilter->getColumnList("*");
                $newConsignmentChargesFilterSumObj = $newConsignmentChargesFilter->getColumnList("SUM(cost) AS cost");
                if (count($newConsignmentChargesFilterObj) > 0) {
                    $newTotalCost = $newConsignmentChargesFilterSumObj[0]->getCost();
                    $newChargesData = serialize($newConsignmentChargesFilterObj);
                }
                $ipAddress = getClientIp();
                $message = "Update Price";
                if ($newTotalCost != $oldTotalCost) {
                    $logData = [
                        'userid' => $added_by,
                        'logdate' => date("Y-m-d H:i:s"),
                        'ipaddress' => $ipAddress,
                        'log_id' => $id,
                        'message' => $message,
                        'previous_data' => $oldChargesData,
                        'current_data' => $newChargesData
                    ];
                    $consignmentChargesLog = new ConsignmentChargesLog($logData);
                    $consignmentChargesLog->save();
                }
                $userAccountObj = new CustomerAccount($user_account_id);
                $user_billing_currency = $userAccountObj->getBillingCurrency();
                if ($userAccountObj->getIsPrepaid() == 1) {
                    if ($newTotalCost > $oldTotalCost) {
                        $amount = $newTotalCost - $oldTotalCost;
                        $debit = $amount;
                        $credit = 0.00;
                        
                    } else {
                        $amount = $oldTotalCost - $newTotalCost;
                        $debit = 0.00;
                        $credit = $amount;
                    }
                    // CH sb ay k blunder ay
                    /*$paymentDetail = "Update manual pricing amount";
                    $paymenthistoryObj = new PaymentsHistory();
                    $paymenthistoryObj->setAccountId($user_account_id);
                    $paymenthistoryObj->setAmount($amount);
                    $paymenthistoryObj->setAmountCurrencyId("GBP");
                    $paymenthistoryObj->setPaymentDetail($paymentDetail);
                    $paymenthistoryObj->setUserCurrencyId($user_billing_currency);
                    $paymenthistoryObj->setDebit($debit);
                    $paymenthistoryObj->setCredit($credit);
                    $paymenthistoryObj->setPaymentStatus("pending");
                    $paymenthistoryObj->setIsCompleted("no");
                    $paymenthistoryObj->setDateAdded($date_added);
                    $paymenthistoryObj->setAddedBy($added_by);
                    $paymenthistoryObj->setDateUpdated($date_update);
                    $paymenthistoryObj->setUpdatedBy($update_by);
                    $paymenthistoryObj->save();*/
					/* update account balance */
					//CustomerAccount::updateBalance($user_account_id);
					/******************************/
                }
            }
            /*
             * Insert Pricing logs
             */
            /*$userAudit = new UserAudit();
            $old_data = null ;//json_encode($logdataArray['old']);
            $new_data = json_encode(["Shipment Price"=>$logdataArray['new']]);
            $log_key = $id;
            $log_name = 'shipment_price';
            $userAudit->insertAuditData($log_name, 'update', $user->getFirstName() . ' ' . $user->getLastName(), $user->getId(), $new_data, $log_key, $old_data);*/
        }
        
        
        
        if(trim($output['status'])== '')
        $output['status'] = "success";
        if(trim($output['message'])== '')
            $output['message'] = "Consignment charges is updated successfully";
        return $output;
    }

    public static function PriceAllCurrencyConversion($fromCurrency, $customerCurrency, $companyCurrency, $supplierCurrency, $amount) {
        
        if(trim($customerCurrency)!= ''){            
            $sqlCustomerPriceRecords = "SELECT currency_converter('" . $fromCurrency . "', '" . $customerCurrency . "', '" . $amount . "' ) as customer_price";
            $resultCustomerPriceSql = DbAccess3::runQuery($sqlCustomerPriceRecords);
            $objCustomerPrice = mysqli_fetch_assoc($resultCustomerPriceSql);
            $customerPrice = $objCustomerPrice['customer_price'];
        }else {
            $customerPrice = 0;
        }
        if(trim($supplierCurrency)!= ''){            
            $sqlSupplierPriceRecords = "SELECT currency_converter('" . $fromCurrency . "', '" . $supplierCurrency . "', '" . $amount . "' ) as supplier_price";
            $resultSupplierPriceSql = DbAccess3::runQuery($sqlSupplierPriceRecords);
            $objSupplierPrice = mysqli_fetch_assoc($resultSupplierPriceSql);
            $supplierPrice = $objSupplierPrice['supplier_price'];
        }else {
            $supplierPrice = 0;
        }
        if(trim($companyCurrency)!= ''){            
            $sqlCompanyPriceRecords = "SELECT currency_converter('" . $fromCurrency . "', '" . $companyCurrency . "', '" . $amount . "' ) as company_price";
            $resultCompanyPriceSql = DbAccess3::runQuery($sqlCompanyPriceRecords);
            $objCompanyPrice = mysqli_fetch_assoc($resultCompanyPriceSql);
            $companyPrice = $objCompanyPrice['company_price'];
        }else {
            $companyPrice = 0;
        }

        $outputArray =[];
        $outputArray['customerPrice'] =$customerPrice;
        $outputArray['supplierPrice'] =$supplierPrice;
        $outputArray['companyPrice'] =$companyPrice;
        
        return $outputArray;
    }

    public static function getConsignmentTotalCharges($consignmentId,$userAccountId,$costType = "customer",$vatInclude = true) {
        $vatwhere = '';
        if($vatInclude === false ){
            $vatwhere = " AND cc.charge_type_id <> 28 ";
        }
        $sql = "SELECT
                    IFNULL(SUM(IF(cct.`charges_key` = 'DISCOUNT', cost*-1 , cost)),0) AS cost,
                    cost_currency, 
                    (select invoice_no FROM invoices where invoices.id = cc.invoice_id  ) as invoice_id
                FROM
                    consignment_charges cc
                JOIN `consignment_charges_types` cct
                    ON cct.`id` = cc.`charge_type_id`
                WHERE cc.consignment_id = '".DbAccess3::escape($consignmentId)."'
                    AND cc.cost_type = '".DbAccess3::escape($costType)."'
                    AND cc.account_id = '".DbAccess3::escape($userAccountId)."'"
                . " $vatwhere ";
        $retunObj = DbAccess3::getListFromSql(__CLASS__, $sql);
        $return = [
            'currency_code' =>  $retunObj[0]->getCostCurrency(),
            'cost' => $retunObj[0]->getCost(),
            'invoice_id' => $retunObj[0]->getInvoiceId()
            
        ];
        return $return;
    }

    public static function getPricingColoumn($type) {
        if($type == "customer") {
            $searchFilter = "agent";
        } else if($type == "agent" || $type == "purchase_invoice" ) {
            $searchFilter = "customer";
        }
        $heading = ["Hawb", "Tracking Number","Agent Invoice Number"];
        $consignmentChargesTypeFilter = new ConsignmentChargesTypesFilter();
        $consignmentChargesTypeFilter->addFilter("     status=1 and is_delete = 0");
        $consignmentChargesTypeFilter->addFieldNotFilter("     charge_type", $searchFilter);
        $consignmentChargesTypeFilterObj = $consignmentChargesTypeFilter->getList();
        if(count($consignmentChargesTypeFilterObj) > 0) {
            foreach ($consignmentChargesTypeFilterObj as $chargesTypeObj) {
                $heading[] = $chargesTypeObj->getTitle();
            }
        }
        return $heading;
    }

    public static function csvUpdateChages($file_object, $price_type,$recon=false) {
        $result = array();
        $result['status'] = true;
        $result['error'] = '';
        $formData = [];
        $consignmentIds = [];
        if ((trim($file_object->tracking_number) != '' && trim($file_object->hawb) != '') || trim($file_object->tracking_number) != '' && $recon ) {
            /*********MAKING HEADING AND GET Charges Id *********/
            $priceTypeArray = [];
            SWITCH ($price_type) {
                CASE 'agent':
                CASE 'purchase_invoice':
                    $priceTypeArray = ['agent', 'both'];
                    if($price_type == 'purchase_invoice')
                        $formData['purchase_invoice_reference'] = $file_object->charges_reference;
                    break;
                CASE 'customer':
                    $priceTypeArray = ['customer', 'both'];
                    break;
            }
            $consignmnetChargesTypeArray = [];
            $consignmnetChargesTypeFilter = new ConsignmentChargesTypesFilter();
            $consignmnetChargesTypeFilter->addFilter("     cct.charge_type in ('" . implode("','", $priceTypeArray) . "') and cct.is_delete <> 1 and cct.status = 1 ");
            $consignmnetChargesTypeFilterObj = $consignmnetChargesTypeFilter->getList();
            if (count($consignmnetChargesTypeFilterObj) > 0) {
                foreach ($consignmnetChargesTypeFilterObj as $consignmnetChargesTypeData) {
                    $col = trim(str_replace([' ', '/'], '_', strtolower($consignmnetChargesTypeData->getTitle())));
@                    $formData['consignment_price'][trim($price_type)][$consignmnetChargesTypeData->getId()] = $file_object->$col;
                }
            }
            //$formData['reference'] = $file_object->charges_reference;
            $formData['currency'] = $file_object->currency;
            /*             * **************** */
            $consignmentFilter = new ConsignmentFilter();
            if($recon){
                $whereClause = " and awb = '" . $file_object->tracking_number . "' and shipment_status not in ('" . Consignment::STATUS_RECYCLED . "', '" . Consignment::STATUS_INVALID . "')";

            } else {
                $whereClause = " and awb = '" . $file_object->tracking_number . "' and hawb = '" . $file_object->hawb . "' and shipment_status not in ('" . Consignment::STATUS_RECYCLED . "', '" . Consignment::STATUS_INVALID . "')";
            }
            $consignmentFilter->setFilter($whereClause);
            $consignmentData = $consignmentFilter->getColumnList('c.hawb, c.user_id, c.id, c.service_id ');
            if (count($consignmentData) > 0) {
                $updatePricedShipment = true;
                $consignment = $consignmentData[0];
                $consignnmentId = $consignment->getId();
                // checked consignment invoiced
                if (trim($price_type) == 'cusotmer') {
                    $checkInvoiced = Consignment::checkConsignmentInvoiced($consignnmentId);
                    if ($checkInvoiced != 0) {
                        $updatePricedShipment = false;
                        $result['status'] = false;
                        $result['MESSAGE'] = 'Customer Priced cannot be updated. Shipment already invoiced.';
                    }
                }
                if ($updatePricedShipment) {
                    $consignmentIds[] = $consignnmentId;
                    $returnData = ConsignmentCharges::updateChargesByConsignmentIds($consignmentIds, $formData, false, true);
                    $result['status'] = true;
                    $result['CUATOMER_ACCOUNT'] = $returnData['pricing_user_account_id'];
                    $result['MESSAGE'] = $returnData['message'];
                    if (trim($price_type) == 'purchase_invoice')
                        $result['pricing_purchase_status'] = $returnData['pricing_purchase_status'];
                }
            } else {
                $result['status'] = false;
                $result['MESSAGE'] = 'Order Reference or Tracking Number not found in portal';
            }
        } else {
            $result['status'] = false;
            $result['MESSAGE'] = 'Order Reference or Tracking Number is blank.';
        }
        //exit;
        return $result;
    }

}
