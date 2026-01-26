<?php

/**
 * MAWB CLASS
 *
 */
class Tariffs extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id'          => 'number',
            'user_account_id' => 'number',
            'carrier_id' => 'number',
            'service_id' => 'number',
			'agent_id' => 'number',
            'name' => 'string',
            'status' => 'number',
            'currency_id' => 'number',
            'tariff_template' => ['enum' => ['kg','item','both','default']],
            'tariff_type' => 'string',
            'start_date' => 'date',
            'end_date' => 'date',
            'date_added' => 'datetime',
            'description' => 'string',
            'tariff_file' => 'string',
            'tariffs_pricing_rule_id' => 'number',
            'added_by' => 'number',
            'date_updated' => 'datetime',
            'updated_by' => 'number',
            'zone_base' => 'undefined',
            'weight_from' => 'undefined',
            'weight_to' => 'undefined',
            'weight_cost' => 'undefined',
            'piece_cost' => 'undefined',
            'full_name' =>'undefined',
            'billing_contact' =>'undefined',
            'email' =>'undefined',
            'billing_email' =>'undefined',
            'carrier_name' =>'undefined',
            'service_name' =>'undefined',
            'service_code' =>'undefined',
            'is_customized' =>'undefined',
            'agent_name' =>'undefined',
            'from_zone_id' =>'undefined',
            'to_zone_id' =>'undefined',
            'carrier_zone' =>'undefined',
            'formula' =>'undefined',
            'currency' =>'undefined',
            'assign_date' =>'undefined',
            'user_account' =>'undefined',
            'carrier' =>'undefined',
            'service' =>'undefined',
            'country_id' =>'undefined',
        );

        //
    parent::__construct("tariffs", 'id', $fieldList, $mixedCreator);
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
    public static function getTariffsListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfTariffsFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
    public function deleteById($id) {
        if($id != "" && $id > 0)
            $sql = "UPDATE tariffs SET status = '0' WHERE id=''".DbAccess3::escape($id)."'";
        self::runQuery($sql);
    }
    public static function getDataFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
    /**
     * delete. Updates row as deleted
     * @return void
     */
    public function delete()
    {
        if ($this->getId() > 0) {
            $sql = "UPDATE tariffs SET status = '0' WHERE id='".DbAccess3::escape($this->getId())."'";
            $result = DbAccess3::runQuery($sql);
        }
        return;

    }
    
    public static function getUserQuotationsByAssignedServices($userAccountId,$orgCountryId,$destCountryId,$originPostcode="",$deliveryPostcode="",$originCity="",$deliveryCity="",$weight,$no_of_pieces,$serviceId=0,$return_tariff=0,$maxDim=0, $accountOwnContract = 0,$serviceType="D"){
        $outputArray = array();
        $outputArray["STATUS"] = "ERROR";
        $val = 0;
        $prefix = 0;
        ///////////////////////////////////// PLEASE ROLLBACK THE BELOW CODE IF ANY PROBLEM ////////////////////
        $host = SETTING_DB_SERVER;
        $user = SETTING_DB_USER;
        $password = SETTING_DB_PASSWORD;
        $db = SETTING_DB_DATABASE;
        $mysqli = new mysqli($host, $user, $password, $db);
        if (!$mysqli->connect_errno) {
            $sql = "CALL quotations('" . $userAccountId . "', $orgCountryId, '" . $destCountryId . "','" . $originPostcode . "','" . $deliveryPostcode . "','" . DbAccess3::escape($originCity) . "','" .DbAccess3::escape( $deliveryCity) . "', $weight,$no_of_pieces,$maxDim,'" . $serviceId . "','" . $return_tariff . "','".$accountOwnContract."','".$serviceType."',NULL,NULL,@S_STATUS,@S_MESSAGE)";
            $res = $mysqli->query($sql);
            if (!$res) {
                $dbErrors = DbAccess3::$dbError;
                $outputArray["STATUS"] = "ERROR";
                if (count($dbErrors) > 0) {
                    foreach ($dbErrors as $error) {
                        $outputArray["ERROR"][] = "CALL failed: " . $error;
                    }
                }
            } else if ($res->num_rows > 0) {
                $outputArray["STATUS"] = "SUCCESS";
                $outputArray["MESSAGE"] = "Quotations Found Successfully.";
                $quotationsRows = $res->fetch_all(MYSQLI_ASSOC);
                foreach ($quotationsRows as $quotation) {
                    if (isset($quotation['CARRIERLOGO']) && !empty($quotation['CARRIERLOGO'])) {
                        $quotation['CARRIERLOGO'] = BASE_URL . 'images/carrierlogo/thumbnail/owe_100_' . $quotation['CARRIERLOGO'];
                    }
                    $outputArray["QUOTATIONS"][] = $quotation;
                }
            } else {
                $outputArray["STATUS"] = "ERROR";
                $outputArray["ERROR"][] = "Tariff not available. Please contact to administrator at info@smarttrack.co";
                $outputArray["MESSAGE"] = "Tariff not available. Please contact to administrator at info@smarttrack.co";
            }
        }else{
            $outputArray["STATUS"]  = "ERROR";
            $outputArray["ERROR"][] = "Failed to connect to database: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
            $outputArray["MESSAGE"] = "Failed to connect to database: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        }
        $mysqli->close();
        return $outputArray;
    }

    public static function checkSupplierTariffActive($user_account_id, $serviceId, $startDate, $endDate) {
        $tariffFilter = new TariffsFilter();
        $tariffFilter->addFieldFilter('      user_account_id', $user_account_id);
        $tariffFilter->addFieldFilter('      service_id', $serviceId);
        $tariffFilter->addFieldFilter('      tariff_type', 'supplier');
        $tariffFilter->addFilter("      DATE(start_date) >= '".DbAccess3::escape($startDate)."'");
        $tariffFilter->addFilter("      DATE(end_date) <= '".DbAccess3::escape($endDate)."'");
        $tariffFilterObjs = $tariffFilter->getList('*');
        $return = [];
        $ids = '';
        if(count($tariffFilterObjs)) {
            $idsArr = [];
            foreach($tariffFilterObjs as $tariffFilterObj) {
                $idsArr[] = $tariffFilterObj->getId();
            }
            if(count($idsArr)) {
                $ids = implode(',',$idsArr);
            }
            $return = [
                'tariff_found' => true,
                'ids' => $ids
            ];
        } else {
            $return = [
                'tariff_found' => false,
                'ids' => $ids
            ];
        }
        return $return;
    }
}
