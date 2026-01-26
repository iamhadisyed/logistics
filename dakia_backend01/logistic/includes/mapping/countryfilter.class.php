<?php

// get settings
class CountryFilter {

    private $filter = "";

    /**
     * Get list of user items based on filter conditions
     *
     * @return array[User]
     */
    public function getList() {
        // has filter been configured?
        $where = "";
        if (trim($this->filter) != "")
            $where = "WHERE " . $this->filter;

         $sql = "SELECT *
				FROM country cn
				$where
				Order by name";
        //if($_SERVER['REMOTE_ADDR'] == '188.66.86.88')
//        	echo $sql;
        return Country::getCountryListFromSql($sql);
    }

    public function getColumnList($fields) {
        $where = "";
        if ($this->filter != "")
            $where = "WHERE " . $this->filter;
        $sql = "SELECT id, " . $fields . " FROM country cn $where Order by name";
//        echo $sql; die;
        return Country::getCountryListFromSql($sql);
    }
    
    public function addFieldFilter($fieldName, $fieldValue) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= "  " . $fieldName . " = '" . DbAccess3::escape($fieldValue) . "'";
    }

    public function addRegionFilter($region) {
        if (trim($this->filter) != '')
            $this->filter .= " AND cn.region IN (" . $region . ")";
        else
            $this->filter .= " cn.region IN (" . $region . ")";
    }

    public function addTypeFilter($type) {
        if (trim($this->filter) != '')
            $this->filter .= " AND cn.type IN ('" . $type . "')";
        else
            $this->filter .= " cn.type IN ('" . $type . "')";
    }

    public function addNameFilter($name) {
        if (trim($this->filter) != '')
            $this->filter .= " AND cn.name = '" . DbAccess3::escape($name) . "'";
        else
            $this->filter .= " cn.name = '" . DbAccess3::escape($name) . "'";
    }
    
    public function addFilter($name) {
        if (trim($this->filter) != '')
            $this->filter .= " AND $name";
        else
            $this->filter .= " $name ";
    }

    public function addIsoFilter($iso) {
        if (trim($this->filter) != '')
            $this->filter .= " AND cn.iso IN ('" . $iso . "')";
        else
            $this->filter .= " cn.iso IN ('" . $iso . "')";
    }

    public function addNameArrayFilter($name) {
        if (trim($this->filter) != '')
            $this->filter .= " AND cn.name IN ('" . $name . "')";
        else
            $this->filter .= " cn.name IN ('" . $name . "')";
    }

    public function addCountryCodeArrayFilter($code) {
        if (trim($this->filter) != '')
            $this->filter .= " AND cn.iso IN ('" . $code . "')";
        else
            $this->filter .= " cn.iso IN ('" . $code . "')";
//			return $code;
    }

    public function addCountryCodeFilter($code) {
        if (trim($this->filter) != '')
            $this->filter .= " AND cn.iso = '" . DbAccess3::escape($code) . "'";
        else
            $this->filter .= " cn.iso = '" . DbAccess3::escape($code) . "'";
//			return $code;
    }

    public function addIsVatableFilter($Vatable) {
        if (trim($this->filter) != '')
            $this->filter .= " AND cn.is_vatable = '" . DbAccess3::escape($Vatable) . "'";
        else
            $this->filter .= " cn.is_vatable = '" . DbAccess3::escape($Vatable) . "'";
//			return $code;
    }

    public function addPostcodeRequiredFilter($code) {
        if (trim($this->filter) != '')
            $this->filter .= " AND cn.postcode_required = '" . DbAccess3::escape($code) . "'";
        else
            $this->filter .= " cn.postcode_required = '" . DbAccess3::escape($code) . "'";
//			return $code;
    }

    public function addCountryIdByFilter($date_value) {
        $this->filter .= "  id = '" . DbAccess3::escape($date_value) . "'";
    }
    
    public function addCountryLike($country)
    {
       $this->filter .= " name LIKE '" . DbAccess3::escape($country) . "%'";
    }

    public function addFilterIn($field, $values, $not = false)
    {
        if (is_array($values)) {
            $this->filter .= " " . $field . ($not === true ? ' NOT' : '') . " IN ('" . implode("','", $values) . "') ";
        } else {
            $this->filter .= " " . $field . ($not === true ? ' NOT' : '') . " IN (" . $values . ") ";
        }
    }

    public function addFilterNotIn($field, $values)
    {
        $this->addFilterIn($field, $values, true);
    }

    /**
     * Get count of user items based on filter conditions
     *
     * @return int
     */
    public function getCount() {
        $result = $this->getList();
        return sizeof($result);
    }

    public function addRegionFil($region) {
        if (trim($this->filter) != '')
            $this->filter .= " AND cn.region = '" . DbAccess3::escape($region) . "'";
        else
            $this->filter .= " cn.region = '" . DbAccess3::escape($region) . "'";
    }

    public function addRegionCollection($region) {
        if (count($region) > 1) {
            if (trim($this->filter) != '')
                $this->filter .= " AND cn.region_collection IN( '" . $region[0] . "', '" . $region[1] . "')";
            else
                $this->filter .= " cn.region_collection IN( '" . $region[0] . "', '" . $region[1] . "')";
        }
        else {
            if (trim($this->filter) != '')
                $this->filter .= " AND cn.region_collection = '" . DbAccess3::escape($region) . "'";
            else
                $this->filter .= " cn.region_collection = '" . DbAccess3::escape($region) . "'";
        }
    }

    public function getCSTopCountries($userAccount = 0,$warehouseId=0) {
        
        $sql = "SELECT 
                COUNT(c.`id`) AS id,
                coun.`name` 
              FROM
                `consignment` c 
                JOIN country coun 
                  ON coun.`id` = c.`country_id` 
                JOIN `user` u ON u.id = c.`user_id`
                  WHERE u.`user_account_id` IN (".implode(',', $userAccount).") AND DATE(c.`date_created`) >= '".date("Y-m-d", strtotime("-14 day"))."' AND DATE(c.`date_created`) <= '".date('Y-m-d')."' AND c.shipment_status IN ('10','12','13','14','15','16','17','18','19','20','21','23','24','25','26','27','28','29','30')
              GROUP BY coun.`id` 
              ORDER BY c.id DESC";
        //echo $sql;exit;
        return Country::getCountryListFromSql($sql);
    }
    
    public function getAccountAmountTopCountries($userAccount = 0) {
        
        $sql = "SELECT 
                SUM(cc.cost) AS id,
                cn.`name`
              FROM
                `consignment` c 
                JOIN `consignment_charges` cc 
                  ON cc.`consignment_id` = c.`id` 
                JOIN country cn 
                  ON cn.`id` = c.`country_id` 
                JOIN `user` u 
                  ON u.id = c.`user_id` 
              WHERE u.`user_account_id` IN (".implode(",",$userAccount).") 
                AND cc.`cost_type` = 'customer' 
                AND DATE(c.`date_created`) >= '".date("Y-m-d", strtotime("-14 day"))."' AND DATE(c.`date_created`) <= '".date('Y-m-d')."'
                AND (cc.`invoice_id` = 0 
                OR cc.`invoice_id` IS NULL)
                GROUP BY cn.`id`";
        
        //echo $sql;exit;
        return Country::getCountryListFromSql($sql);
    }
    
    public static function getAccountSummaryCountryReport($userAccount = 0,$query='') {

        
        $sql = "SELECT 
                COUNT(c.id) as id,
                cn.`name`
              FROM
                `consignment` c 
                JOIN country cn 
                  ON cn.`id` = c.`country_id` 
                JOIN `user` u 
                  ON u.`id` = c.`user_id`
                WHERE u.`user_account_id` IN (".$userAccount.") AND  c.`shipment_status` NOT IN ('11', '12', '22', '23') ".$query ." 
                GROUP BY cn.id";
        //echo $sql;exit;
        return Country::getCountryListFromSql($sql);
    }
    
    public static function getAccountSummarySerivceReport($userAccount = 0,$query='') {
        $sql = "SELECT 
                COUNT(c.id) as id,
                cn.`name`
              FROM
                `consignment` c 
                JOIN services cn 
                  ON cn.`id` = c.`service_id` 
                JOIN `user` u 
                  ON u.`id` = c.`user_id`
                WHERE u.`user_account_id` IN (".$userAccount.") AND  c.`shipment_status` NOT IN ('11', '12', '22', '23') ".$query ." 
                GROUP BY cn.id";
        //echo $sql;exit;
        return Country::getCountryListFromSql($sql);
    }
    
    public static function getAccountSummarySerivceExcel($userAccount = 0,$query='') {
        $sql = "SELECT 
                COUNT(c.id) as id,
                cn.`name`,
                ua.`user_account` AS iso
              FROM
                `consignment` c 
                JOIN services cn 
                  ON cn.`id` = c.`service_id` 
                JOIN `user` u 
                  ON u.`id` = c.`user_id`
                JOIN customer_account ua 
                    ON ua.`id` = u.`user_account_id`
                WHERE u.`user_account_id` IN (".$userAccount.") AND  c.`shipment_status` NOT IN ('11', '12', '22', '23') ".$query ." 
                GROUP BY cn.id";
        //echo $sql;exit;
        return Country::getCountryListFromSql($sql);
    }
    
    public static function getAccountGraph($userAccount = 0,$month='') {
        $sql = "SELECT 
                SUM(cc.`cost`) AS id,
                DATE(c.`date_created`) AS name
              FROM
                `consignment` c 
                JOIN `consignment_charges` cc 
                  ON cc.`consignment_id` = c.`id` 
                  AND cc.`cost_type` = 'customer' 
                JOIN `user` u 
                  ON u.`id` = c.`user_id` 
              WHERE MONTH(c.`date_created`) = '$month' 
                AND u.`user_account_id` IN ($userAccount) 
              GROUP BY DATE(c.`date_created`)";
        return Country::getCountryListFromSql($sql);
    }
    public static function getCountryBagWeightLimit($countryId) {
        $returnData = DEFAULT_BAG_WEIGHT_LIMIT;
        if ($countryId > 0) {
            $country = new Country($countryId);
            if ($country->getBagWeightLimit() > 0) {
                $returnData = $country->getBagWeightLimit();
            }
        }
        return $returnData;
    }
}

// class
?>