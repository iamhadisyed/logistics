<?php
/**
 * A consignment contains a number of parcels.
 * Each parcel will need to be assigned a item number used on label.
 *
 */

class UserServicesRoutingFilter
{
    private $filter = "";
    private $order_by = "";
    private $limit = 100;
    private $groupBy = '';

    /**
     * Get list of consignment items based on filter conditions
     *
     * @return array[Consignment]
     */
    public function getColumnList($fields,$limit=true,$distinct=false,$debug = false)
    {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;

        $groupBy = "";
        if ($this->groupBy != "") {
            $groupBy = $this->groupBy;
        }
        if($distinct){
            $sql = "SELECT DISTINCT " . $fields;
        }else{
            $sql = "SELECT id, " . $fields;
        }
            $sql .= "  FROM user_services_routing psr
				$where
                                $groupBy
				$sort
				";
        if($limit){
            if ($this->limit > 0){
                $sql .= " Limit " . $this->limit;
            }
        }
        if($debug)
            echo $sql;
        return UserServicesRouting::getPartnerServicesListFromSql($sql);
    }

    /**
     * Get list of consignment items based on filter conditions
     *
     * @return array[Consignment]
     */
    public function getStandardCustomerServiceList($selectedcolumn, $userAccountId, $service_id = '', $countryIsoCode = '')
    {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        $parentAccoutnt = new CustomerAccount($userAccountId);
        $parentAccoutntId = $parentAccoutnt->getParentId();


        $sessionUser = SessionManager::getUser();
        $joinType = " JOIN ";
        if ($sessionUser->getUserType() == User::USER_TYPE_ADMIN && $parentAccoutntId <=0) {
            $parentAccoutntId = $sessionUser->getUserAccountId();
            $joinType = " LEFT JOIN ";
            }

        $sql = "SELECT
                        " . $selectedcolumn . "
                    FROM
                        (services sr
                        INNER JOIN carrier cr 
                            ON cr.id = sr.carrier_id 
                            " . (((int)($service_id) > 0) ? " AND cr.id = '" . (int)($service_id) . "'" : "") . "
                            " . ((trim($countryIsoCode) != '') ? " AND sr.service_country LIKE '%" . (trim($countryIsoCode)) . "%'" : "") . "
                        INNER JOIN country con on con.id = cr.country_id
                        )
                           " . $joinType . "
                        (SELECT 
                           service_id, from_weight, to_weight, user_account_id, is_remotearea
                        FROM
                            user_services_routing) psr ON sr.id = psr.service_id
                            AND psr.user_account_id = '" . $parentAccoutntId . "'
                            
                            group by sr.id
                            order by cr.carrier
                            ";
//            echo $sql; die;
        t($sql, __METHOD__);
        return UserServicesRouting::getPartnerServicesListFromSql($sql);
    }

    /**
     * Get list of consignment items based on filter conditions
     *
     * @return array[Consignment]
     */
    
    public function getCustomColumnList($fields)
    {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;


        $sql = "SELECT " . $fields . "
				FROM user_services_routing psr
				$where
				$sort
				";

        t($sql, __METHOD__);


        return UserServicesRouting::getPartnerServicesListFromSql($sql);
    }

    /**
     * Get list of consignment items based on filter conditions
     *
     * @return array[Consignment]
     */
    public function getServiceColumnList($fields)
    {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;


        $sql = "SELECT " . $fields . "
				FROM user_services_routing psr INNER JOIN services ser ON psr.service_id = ser.id
				$where
				$sort
				";

        t($sql, __METHOD__);


        return UserServicesRouting::getPartnerServicesListFromSql($sql);
    }

    
    public function deleteList()
    {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }

        $sql = "DELETE FROM user_services_routing $where";

        $sql = str_replace('psr.', '', $sql);

        t($sql, __METHOD__);


        return UserServicesRouting::getPartnerServicesListFromSql($sql);
    }

    /****
     * Get distinct list of flight numbers, using current filter conditions.
     */
    public function getCountrytList()
    {
        // has filter been configured?
        $where = "WHERE country_iso <> '' ";
        $where .= $this->filter;
        //
        echo $sql = "SELECT c.name FROM user_services_routing p 
                        LEFT JOIN country c
                        on c.iso = p.country_iso"
            . " $where ORDER BY country";

        //if ($this->limit > 0) $sql .= " Limit " . $this->limit;

        t($sql, __METHOD__);
        return UserServicesRouting::getPartnerServicesListFromSql($sql, "country");
    }

    
    public function getListByAttribute($selectString, $debug=false)
    {
        // has filter been configured?
        $where = "WHERE trim(country_id) <> '' ";
        $where .= $this->filter;
        $groupBy = $this->groupBy;

        //
        $sql = "SELECT " . $selectString . " FROM user_services_routing psr $where " . $groupBy . " ";

        if ($this->limit > 0) $sql .= " Limit " . $this->limit;

        if($debug)
            echo $sql;
        return UserServicesRouting::getPartnerServicesListFromSql($sql);
    }

   

    public function addNotSpecialServiceTypeFilter()
    {

        $this->filter .= " AND ";
        $this->filter .= " psr.service_type <> 'S'";
    }

    public function addFieldFilter($fieldName, $fieldValue, $tableprefix = 'psr.')
    {

        $this->filter .= " AND ";
        $this->filter .= $tableprefix . $fieldName . " = '" . DbAccess3::escape($fieldValue) . "'";
    }

    public function addFieldsFilter($fieldName, $fieldValue)
    {
        $this->filter .= " AND ";
        $this->filter .= $fieldName . " = '" . DbAccess3::escape($fieldValue) . "'";
    }

    public function addOrderBy($orderBy)
    {
        $this->order_by = $orderBy;
    }

   
    public function getUserAllowServiceList()
    {
        // has filter been configured?
        $where = "WHERE country <> '' ";
        $where .= $this->filter;
        //
//		$sql = "SELECT DISTINCT(ser.name) as service_name, type FROM user_services_routing psr INNER JOIN services as ser ON ser.code = psr.service_name $where group by ser.code order by ser.name";
        $sql = "SELECT DISTINCT(ser.name) as service_name, ser.code AS carrier FROM user_services_routing psr INNER JOIN services as ser ON ser.code = psr.service_name $where group by ser.code order by ser.name";

        //if ($this->limit > 0) $sql .= " Limit " . $this->limit;

        t($sql, __METHOD__);
        return UserServicesRouting::getPartnerServicesListFromSql($sql);
    }

    
    public function getAvailableRoutingNameList()
    {

        $sql = "SELECT DISTINCT(routing_name) FROM user_services_routing psr WHERE routing_name <> '' AND service_type in ('OR','PR') group by routing_name ";
        t($sql, __METHOD__);
        return UserServicesRouting::getPartnerServicesListFromSql($sql);
    }

    /**
     * Get count of consignment items based on filter conditions
     *
     * @return int
     */
    public function getCount()
    {
        $result = $this->getList();
        return sizeof($result);
    }

    /**
     * Get list of consignment items based on filter conditions
     *
     * @return array[Consignment]
     */
    public function getList($debug = false)
    {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $groupBy = "";
        if ($this->groupBy != "") {
            $groupBy = $this->groupBy;
        }
        $sort = "";
        if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;


        $sql = "SELECT *
                        FROM user_services_routing psr
                        $where
                        $groupBy
                        $sort
                        ";
        if ($this->limit > 0) $sql .= " Limit " . $this->limit;

        if ($debug)
            echo $sql;
        return UserServicesRouting::getPartnerServicesListFromSql($sql);
    }

    /**
     * Limit list to consigments with given Postcode value
     * @param $postcode_value
     */
    public function addUserAccountIdFilter($user_id)
    {
        $this->filter .= " AND ";
        $this->filter .= " psr.user_account_id = '" . DbAccess3::escape($user_id) . "'";
    }


    public function addFilter($name)
    {
        $this->filter .= " AND ";
        $this->filter .= " " . $name . "";
    }


    public function addWeightRangeFilter($fromweight_value, $toweight_value)
    {
        $this->filter .= " AND ";
        $this->filter .= " ( psr.from_weight >= " . DbAccess3::escape($fromweight_value) . " AND psr.to_weight <= " . DbAccess3::escape($toweight_value) . " )";
    }

   
    public function addCountryFilter($country)
    {
        $this->filter .= " AND ";
        $this->filter .= " psr.country_iso = '" . DbAccess3::escape($country) . "'";
    }



    public function addORPRServicesFilter()
    {
        $this->filter .= " AND ";
        $this->filter .= " psr.service_type IN ('OR', 'PR')";
    }

    /***
     * Limit number of rows returned
     */
    public function setLimit($lim)
    {
        $this->limit = $lim;
    }
    
    public function setGroup($groupBy)
    {
        $this->groupBy = " group by psr." . $groupBy;
    }
    
    public function addFilterIn($field, $values) {
        if (trim($this->filter) != "") {
          $this->filter .= " AND ";
        }
        if (is_array($values)) { 
         $this->filter .= $field ." IN (" . implode(",", $values) . ")";
        } else {
         $this->filter .= $field . " IN (" . $values . ")";
        }
    }
    public static function isOwnUserContract($serviceId,$accountId) {
         $sql = "SELECT
                usr.`user_account_id`,
                u.`user_account_id` AS added_by,
                usr.`service_id`
              FROM
                `user_services_routing` usr
                JOIN `user` u
                  ON u.id = usr.`added_by`
                JOIN `agent_data` a
                 ON a.user_id = usr.added_by
              WHERE usr.`user_account_id` = '" . DbAccess3::escape($accountId) . "'
                AND usr.`service_id` = '" . DbAccess3::escape($serviceId) . "'
                AND a.active = 1
              GROUP BY usr.`service_id` ";
        $returnData = UserServicesRouting::getPartnerServicesListFromSql($sql);
        $returnOwnService = false;
        if(count($returnData)){
            if($returnData[0]->getAddedBy() == $returnData[0]->getUserAccountId()){
                $returnOwnService = true;
            }
        }
        return $returnOwnService;
    }
    public static function getUserCountryServices($userAccount,$formCountry,$toCountry=[],$includeServices=[],$mailParcel="",$trackedUntracked="",$transitDays="",$mailType="",$mailOption="")
    {
        if ($userAccount > 0) {
            $scttWhere = "";
            $servicesWhereStr = "";
            $servicesWhere = [];
            if (!empty($transitDays)) {
                $scttWhere = " AND sctt.`transit_time` <= '" . DbAccess3::escape($transitDays) . "'";
            }
            if (!empty($includeServices)) {
                $servicesWhere[] = "usr.`service_id` IN (" . implode(",", $includeServices) . ")";
            }
            if (!empty($toCountry)) {
                $servicesWhere[] = "usr.`country_id` IN (" . implode(",", $toCountry) . ")";
            }
            if (!empty($mailParcel)) {
                $servicesWhere[] = "s.`validation_type` = '" . DbAccess3::escape($mailParcel) . "'";
                if ($mailParcel == "mail" && !empty($mailType)) {
                    $servicesWhere[] = "s.`mail_type` = '" . DbAccess3::escape($mailType) . "'";
                }
                if ($mailParcel == "mail" && !empty($mailOption)) {
                    $servicesWhere[] = "s.`mail_option` = '" . DbAccess3::escape($mailOption) . "'";
                }
            }
            if (trim($trackedUntracked) != "") {
                $servicesWhere[] = "s.`is_untrack` = '" . DbAccess3::escape($trackedUntracked) . "'";
            }
            if (count($servicesWhere) > 0) {
                $servicesWhereStr = " AND " . implode(" AND ", $servicesWhere);
            }
            $sql = "SELECT
                      usr.`country_id`,
                      usr.`service_id`
                    FROM
                      user_services_routing usr
                      JOIN services s
                        ON s.id = usr.`service_id`
                      JOIN service_country_ttime sctt
                        ON sctt.`id_service` = usr.`service_id`".$scttWhere."
                      JOIN carrier c
                        ON c.`id` = s.`carrier_id`
                    WHERE usr.`user_account_id` = '".DbAccess3::escape($userAccount)."'
                      AND
                      CASE
                        WHEN s.service_type = 'C'
                        THEN '".DbAccess3::escape($formCountry)."' IN
                        (SELECT
                          country_id
                        FROM
                          `service_collection_county`
                        WHERE service_id = s.`id`)
                        ELSE 1 = 1
                      END
                      AND usr.`status` = 1
                      AND usr.`is_agreed` = 1                      
                      AND s.`active` = 1
                      AND s.`deletedq` = 0
                      AND c.`status` = 1
                      ".$servicesWhereStr."
                    GROUP BY usr.`service_id`,usr.`country_id`";
            return UserServicesRouting::getPartnerServicesListFromSql($sql);
        }
    }
}
