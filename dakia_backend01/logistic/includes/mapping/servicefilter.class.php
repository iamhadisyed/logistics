<?php

// get settings
//require_once("includes/settings/common.inc.php");

class ServiceFilter {

    private $filter = "";
    private $groupBy = "";
    private $rowsPerPage = 0;
    private $pageOffset = 0;
    private $join = [];

    /**
     * Get list of user items based on filter conditions
     *
     * @return array[User]
     */
    public function getList($debug = false) {

        $where = "";
        if (trim($this->filter) != "")
            $where = "WHERE " . substr($this->filter, 4);

        $join = "";
        if (!empty($this->join)) {
            $join = implode(" ", $this->join);
        }
        
        $groupBy = $this->groupBy;

       $sql = "SELECT * FROM services ser $join  $where " . $groupBy . " Order by name asc";
       
       if($debug) {
           echo $sql;
           }
        return Services::getServicesListFromSql($sql);
    }
    
    /**
     * Get list of user items based on filter conditions
     *
     * @return array[User]
     */
    public function getServiceViewList($columnList, $debug=false) {


        // has filter been configured?
//		$where = "WHERE ser.code <> '' ";
        $where = "";
        if (trim($this->filter) != "")
            $where = "WHERE " . substr($this->filter, 4);
        
        $groupBy = $this->groupBy;

        $sql = "SELECT $columnList FROM ( services ser JOIN carrier ca ON ca.id = ser.carrier_id  ) LEFT JOIN country c on ca.country_id = c.id  $where " . $groupBy . " Order by ca.carrier, name asc";
        if($debug)
            echo $sql;
        return Services::getServicesListFromSql($sql);
    }
    
    
    
    public function getCarrierServicesList($field, $debug = false) {
        $where = "";
        if (trim($this->filter) != "")
            $where = "WHERE " . substr($this->filter, 4);
        $groupBy = $this->groupBy;
        $sql = "SELECT ".$field." FROM services ser INNER JOIN carrier ca on ca.id = ser.carrier_id $where " . $groupBy . " Order by name asc";
        if($debug) {
            echo $sql;
            die;
        }
        return Services::getServicesListFromSql($sql);
    }
  
    /**
     * Get list of user items based on filter conditions
     *
     * @return array[User]
     */
    public function getServiceAndCountryList($fields, $debug = false) {


        // has filter been configured?
       $where = "";
        if (trim($this->filter) != "")
            $where = "WHERE " . substr($this->filter, 4);

        if (@$this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;
        else
            $sort = "ORDER BY name asc";


        $groupBy = $this->groupBy;
        $sql = "SELECT " . $fields . " FROM services ser INNER JOIN service_country_ttime sct ON ser.id = sct.id_service $where " . $groupBy . " " . $sort;

        if ($debug)
            echo $sql;
        return Services::getServicesListFromSql($sql);
    }
    
    /**
     * Get list of user items based on filter conditions
     *
     * @return array[User]
     */
    public function getColumnList($fields, $debug = false) {


        // has filter been configured?
       $where = "";
        if (trim($this->filter) != "")
            $where = "WHERE " . substr($this->filter, 4);

        if (@$this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;
        else
            $sort = "ORDER BY name asc";

        $join = "";
        if (!empty($this->join)) {
            $join = implode(" ", $this->join);
        }


        $groupBy = $this->groupBy;
        $sql = "SELECT " . $fields . ", ser.id FROM services ser $join $where " . $groupBy . " " . $sort;

        
        if ($debug)
        {
            echo $sql;
            die;
        }
        return Services::getServicesListFromSql($sql);
    }

    /**
     * Get list of user items based on filter conditions
     *
     * @return array[User]
     */
    public function AddOrderBy($name, $ascending = true) {
        //if ($this->order_by != "") $this->order_by = " ";
        //
		if (trim($name) != '')
            $this->order_by = $name . " " . ($ascending ? "" : " DESC");
    }

    public function getPagingCount($debug=false) {
        // has filter been configured?
        $where = "WHERE ";
        $where .= $this->filter;
        $groupBy = $this->groupBy;
        $sql = "SELECT count(id) as total FROM services ser $where " . $groupBy . " ";

        if($debug)
            echo $sql;
        return Services::getTotalNumberOfServiceFromSql($sql);
    }

    public function getPagingList($selectColumns="*",$debug=false) {
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . $this->filter; //substr($this->filter, 4);
        }
        if ($this->join != "") {
            $join = $this->join;
        }
        $groupBy = $this->groupBy;

        if ($this->order_by != "")
            $sort = " ORDER BY " . $this->order_by;
        
        $sql = "SELECT $selectColumns FROM  services ser ".
                implode(" ", $join)  
                . "  $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
        if($debug)
            echo $sql;
        return Services::getServicesListFromSql($sql);
    }

    public function setOffset($offset) {
        $this->pageOffset = $offset;
    }

    public function setRowsPerPage($rowsPP) {
        $this->rowsPerPage = $rowsPP;
    }

    public function addFieldFilter($colm, $value) {
        $this->filter .= " AND ";
        $this->filter .= " " . $colm . " = '" . DbAccess3::escape($value) . "'";
    }

    public function addFieldWhereFilter($colm, $value, $where = '=') {
        $this->filter .= " AND ";
        $this->filter .= " " . $colm . " ". $where ." '" . DbAccess3::escape($value) . "'";
    }
    
    public function addFieldNotFilter($colm, $value) {
        $this->filter .= " AND ";
        $this->filter .= " " . $colm . " != '" . DbAccess3::escape($value) . "'";
    }

    public function addJoin($table,$whare,$type='') {
        if(!empty($table) && !empty($whare)){
            $this->join[] = "  ".$type." JOIN $table  ON ".$whare;
        }
    }

//	public function addFilter($value)
//	{
//		$this->filter .= " AND ";
//		$this->filter .= "  " . $value . " ";
//	}
    public function addFilter($name) {
        if (trim($this->filter) != '')
            $this->filter .= " AND $name";
        else
            $this->filter .= " $name ";
    }

  
    public function getServiceType($service_type, $type) {
        // has filter been configured?
        $where = "";
        if ($this->filter != "")
            $where = "WHERE " . $this->filter;

        $sql = "SELECT * FROM services where service_type = '" . DbAccess3::escape($service_type) . "' and type = '" . DbAccess3::escape($type) . "'";

        return Services::getServicesListFromSql($sql);
    }

    public function getInnerJoinList($selectColumnArray, $table1, $table2, $joinstring, $groupBy = '', $orderBy = '', $debug = false) {
        // has filter been configured?
        $where = "WHERE ser.code <> '' ";
        $where .= $this->filter;
        $selectString = implode(',', $selectColumnArray);

          $sql = "SELECT " . $selectString . " FROM " . $table1 . " INNER JOIN " . $table2 . " ON " . $joinstring . " " . $where . " " . $groupBy . " " . $orderBy;
            if($debug)
                echo $sql;
        return Services::getServicesListFromSql($sql);
    }

    /**
     * Get count of user items based on filter conditions
     *
     * @return int
     */
    public function getInnerJoinCount($selectColumnArray, $table1, $table2, $joinstring, $groupBy = '', $orderBy = '') {
        $result = $this->getInnerJoinList($selectColumnArray, $table1, $table2, $joinstring, $groupBy, $orderBy);
        return sizeof($result);
    }

    /**
     * Limit list to consigments with given Postcode value
     * @param $postcode_value
     */
    public function addcoulmnFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " " . $colm . " = '" . DbAccess3::escape($value) . "'";
    }

    /**
     * Limit list to consigments with given Postcode value
     * @param $postcode_value
     */
    public function addcoulmnLikeFilter($colm, $value) {
        $this->filter .= " AND ";
        $this->filter .= " " . $colm . " LIKE '" . DbAccess3::escape($value) . "'";
    }

    public function getRecordFromServiceAndName() {
        // has filter been configured?
        $where = "WHERE ser.code <> '' ";
        $where .= $this->filter;
        $groupBy = $this->groupBy;

        $sql = "SELECT distinct(name) FROM services ser INNER JOIN customizedservicesrouting psr ON ser.carrier = psr.carrier $where  " . $groupBy . " ORDER BY name ASC";
        //  return	$rs = DbAccess3::runQuery($sql);
        return Services::getServicesListFromSql($sql);
    }

    public function addSpecialServicesFilter() {
        $this->filter .= " AND ";
        $this->filter .= " psr.service_type = '" . DbAccess3::escape('S') . "'";
    }

    public function getUniqueCarrierfilter() {
        $this->groupBy = " GROUP BY ser.carrier_id";
    }
    
    public function addGroupByFilter( $fields) {
        $this->groupBy = " GROUP BY ".$fields;
    }

    /**
     * Limit list to value of type
     * @param $postcode_value
     */
    public function addName2Filter($type) {
        $this->filter .= " AND ";
        $this->filter .= " ser.name LIKE '" . DbAccess3::escape($type) . "'";
    }

   
    /**
     * Limit list to value of type
     * @param $postcode_value
     */
    public function addCarrierFilter($carrier) {
        $this->filter .= " AND ";
        $this->filter .= " ser.carrier_id = '" . DbAccess3::escape($carrier) . "'";
    }

    public function addIdFilter($id) {
        $this->filter .= " AND ";
        $this->filter .= " ser.id='" . DbAccess3::escape($id) . "'";
    }

    public function addIdArrayFilter($id) {
        $this->filter .= " AND ";
        $this->filter .= " ser.id in ('" . implode("','", $id) . "')";
    }

    public function addAccountNumberFilter($account_number) {
        $this->filter .= " AND ";
        $this->filter .= " psr.account_number = '" . DbAccess3::escape($account_number) . "'";
    }

    public function addName2SFilter($type) {
        $this->filter .= " AND ";
        $this->filter .= " ser.name ='" . DbAccess3::escape($type) . "'";
    }

    
    /**
     * Limit list to consigments with given Postcode value
     * @param $postcode_value
     */
    public function addCodeFilter($code) {
        $this->filter .= " AND ";
        $this->filter .= " ser.code LIKE '%" . DbAccess3::escape($code) . "%'";
    }

    public function addCodeExactFilter($code) {
        $this->filter .= " AND ";
        $this->filter .= " ser.code ='" . DbAccess3::escape($code) . "'";
    }

    public function addCodeArrayFilter($code) {
        //if(trim($this->filter) != '')
        $this->filter .= " AND ser.code IN ('" . $code . "')";
        //else
        //$this->filter  .= " ser.code IN ('" . $code. "')";
//			return $code;
    }

    public function addCodeTrimFilter($code) {
        $this->filter .= " AND ";
        $this->filter .= " ser.code LIKE ='" . DbAccess3::escape($code) . "'";
    }

    /**
     * Limit list to consigments with given Postcode value
     * @param $postcode_value
     */
    public function addSCodeFilter($code) {
        $this->filter .= " AND ";
        $this->filter .= " ser.code ='" . DbAccess3::escape($code) . "'";
    }

   
    public function addCodeNotEmptyFilter() {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " ser.code <> '' ";
    }

    public function addFieldFilterIn($field, $values, $not = false)
    {
        if (is_array($values)) {
            $this->filter .=  ' ' . $field . ($not === true ? ' NOT' : '') . " IN ('" . implode("','", $values) . "')";
        } else {
            $this->filter .= ' ' . $field . ($not === true ? ' NOT' : '') . " IN (" . $values . ")";
        }
    }

    public function addFieldFilterNotIn($field, $values)
    {
        $this->addFieldFilterIn($field, $values, true);
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

    public function getCarrierFromCode($code) {
        // has filter been configured?
        $sql = "SELECT carrier_id FROM services where code = '" . DbAccess3::escape($code) . "'";

        return Services::getServicesListFromSql($sql);
    }

    

    public function getDistinctService() {


        $sql = "SELECT DISTINCT carrier FROM services where active = '1' order by carrier asc";

        return Services::getServicesListFromSql($sql);
    }

    public function GetServiceNameFromAgent() {
        $sql = "select sam.agentid, s.name from services s, service_agent_mapping sam where sam.serviceid = s.id";
        return Services::getServicesListFromSql($sql);
    }

    public function addFieldLikeFilter($colm, $value) {
        $colm = trim($colm);
        $value = trim($value);
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " " . $colm . " LIKE '%" . DbAccess3::escape($value) . "%'";
    }
    public function getFilterServices($sCountry = "",$dCountry = "",$desServiceType = "",$carrierId = "",$accountId="") {
        $sql = 'SELECT `services`.* FROM `services` ';
        $groupBy = "";
        $where = [];

        if(!empty($dCountry) || !empty($desServiceType))
            $sql .= ' JOIN `service_country_ttime` ON `services`.`id` = `service_country_ttime`.`id_service` ';
        
        if(!empty($desServiceType) && $desServiceType != "all")
            $sql .= '  JOIN `country` ON `service_country_ttime`.`id_country` = `country`.id ';
        
        if(!empty($accountId))
            $sql .= "  JOIN `user_services_routing` ON `user_services_routing`.`service_id` = `services`.id AND `user_services_routing`.user_account_id = '".DbAccess3::escape($accountId)."'";
        
        /*if(!empty($sCountry) || !empty($dCountry) || !empty($accountId) || !empty($carrierId) || (!empty($desServiceType) && $desServiceType != "all") )
            $sql .= ' WHERE ';
        */
        if(!empty($desServiceType) && $desServiceType != "all")
            $where[] = '  `country`.`region` = "'.DbAccess3::escape($desServiceType).'" ';
        
        /*if(!empty($desServiceType) && $desServiceType != "all" && !empty($carrierId))
            $sql .= '  AND ';
        */
        if(!empty($carrierId))
            $where[]  = ' `services`.carrier_id = "'.DbAccess3::escape($carrierId).'" ';
        
        /*if(!empty($carrierId) && !empty($sCountry))
            $sql .= ' AND ';
        */
        if(!empty($sCountry))
            $where[]  = '  `services`.`origin_country` = "'.DbAccess3::escape($sCountry).'" ';
        
        /*if(!empty($dCountry) && !empty($sCountry))
            $sql .= ' AND ';
        */
        if(!empty($dCountry))
            $where[]  = ' `service_country_ttime`.`id_country` = "'.DbAccess3::escape($dCountry).'" ';
        
        /*if( (!empty($accountId)) &&  (!empty($carrierId) || !empty($sCountry) || !empty($dCountry) || (!empty($desServiceType) && $desServiceType != "all")) )
            $sql .= ' AND ';
        
        if(!empty($accountId) && (!empty($desServiceType) && $desServiceType != "all") )
            $sql .= " `user_services_routing`.user_account_id = '".DbAccess3::escape($accountId)."' ";
       */
        $whereStr = "";
        if(count($where) > 0){
            $whereStr = " WHERE ".implode(' AND ',$where);
        }

        if(!empty($dCountry) || !empty($desServiceType))
            $groupBy = ' GROUP BY `service_country_ttime`.id_service';

        $query = $sql.$whereStr.$groupBy;

        //echo $query;

        return Services::getServicesListFromSql($query);
    }
    public function addServiceCountryTTimeTableJoin() {
        $this->join[] = " JOIN `service_country_ttime` sctt  ON ser.id = sctt.id_service ";
    }
    public function addFilterIn($field, $values) {
        if (is_array($values)) { 
            $this->filter .= " AND " . $field ." IN (" . implode(",", $values) . ")";
        } else {
            $this->filter .= " AND " . $field . " IN ('" . $values . "')";
        }
    }
    public static function getPreSortServiceByCarrierId($carrierId, $column = "*",$preSort=false) {
        $checkPresort = " AND pre_sort = 'YES' ";
        if($preSort){
            $checkPresort = "  ";
        }
        if($carrierId > 0){
           $sql = "select ".$column." from services where  active = '1' $checkPresort AND deletedq = '0' AND  carrier_id = '".DbAccess3::escape($carrierId)."'";
           return Services::getServicesListFromSql($sql);
        }
    }
    public static function getServicesToAgent($agentId){
        if($agentId > 0){
            $sql = "SELECT 
                            s.*,
                            ca.logo as carrier_logo
                          FROM
                            services s 
                            JOIN `service_agent_mapping` sam
                            ON sam.`serviceid` = s.`id`
                            INNER JOIN carrier ca on ca.id = s.carrier_id
                          WHERE sam.`agentid` = '".DbAccess3::escape($agentId)."'";
            return Services::getServicesListFromSql($sql);
        }
    }
    public static function getUserAccountServices($userAccount,$formCountry,$toCountry,$serviceType="",$includeServices=[],$mailParcel="",$trackedUntracked="",$transitDays="") {
        if($userAccount > 0){
            $scttWhere = "";
            $servicesWhereStr = "";
            $servicesWhere = [];
            if(!empty($transitDays)){
                $scttWhere = " AND sctt.`transit_time` <= '".DbAccess3::escape($transitDays)."'";
            }
           if(!empty($includeServices)){
               $servicesWhere[] = "s.`id` IN (".implode(",",$includeServices).")";
           }
           if(!empty($mailParcel)){
               $servicesWhere[] = "s.`validation_type` = '".DbAccess3::escape($mailParcel)."'";
           }
           if(trim($trackedUntracked) != ""){
               $servicesWhere[] = "s.`is_untrack` = '".DbAccess3::escape($trackedUntracked)."'";
           }
           if(count($servicesWhere) > 0){
               $servicesWhereStr .= " AND ".implode(" AND ",$servicesWhere);
           }
           if(!empty($serviceType)){
//               $servicesWhereStr .= " AND CASE WHEN '".DbAccess3::escape($serviceType)."' = 'D' THEN   ";
//               $servicesWhereStr .= "  (s.service_type = 'D' OR ( s.drop_off_service_id > 0 AND (SELECT service_type FROM services WHERE id = s.drop_off_service_id) = 'D')  )  ";
//               $servicesWhereStr .= " WHEN '".DbAccess3::escape($serviceType)."' = 'C' THEN  ";
//               $servicesWhereStr .= "  (s.service_type = 'C' OR ( s.drop_off_service_id > 0 AND (SELECT service_type FROM services WHERE id = s.drop_off_service_id) = 'C')  )  ";
//               $servicesWhereStr .= " WHEN '".DbAccess3::escape($serviceType)."' = 'DO' THEN  ";
//               $servicesWhereStr .= "  (s.service_type = 'DO' OR ( s.drop_off_service_id > 0 AND (SELECT service_type FROM services WHERE id = s.drop_off_service_id) = 'DO')  )  ";
//               $servicesWhereStr .= " ELSE 1=2 END ";
////               if($serviceType == "DO"){
////               $servicesWhereStr .= " AND s.service_type = 'D' AND s.drop_off_service_id > 0";
////               }else{
////               }
//                   $servicesWhereStr .= " AND s.service_type = '".DbAccess3::escape($serviceType)."' ";
			   $servicesWhereStr .= "AND CASE WHEN s.drop_off_service_id > 0 AND '".DbAccess3::escape($serviceType)."' != '' THEN 
										(s.service_type = '".DbAccess3::escape($serviceType)."' OR (SELECT service_type	FROM services WHERE id = s.drop_off_service_id) = '".DbAccess3::escape($serviceType)."')
				  					 ELSE
										s.service_type = '".DbAccess3::escape($serviceType)."'
				  					 END";
           }
           $sql = "SELECT 
                      s.`id`,
                      s.`code`,
                      s.`name`,
                      s.`is_customized`,
                      s.`proforma_invoice`,
                      usr.`is_dead_weight`,
                      usr.`is_over_size`,
                      s.`insurance_available`,
                      c.`logo` as carrier_logo,
                      s.delivery_mode
                    FROM
                      user_services_routing usr 
                      JOIN services s 
                        ON s.id = usr.`service_id` 
                      JOIN carrier c 
                        ON c.`id` = s.`carrier_id` 
                      JOIN service_country_ttime sctt 
                        ON sctt.`id_service` = s.id 
                        AND sctt.`id_country` = '".DbAccess3::escape($toCountry)."'
                        ".$scttWhere." 
                      LEFT JOIN `customized_services_routing` csr 
                        ON csr.`customize_service_id` = s.`id` 
                        AND 
                        CASE
                          WHEN s.`is_customized` = 1 
                          THEN csr.`country_id` = '".DbAccess3::escape($toCountry)."' 
                          ELSE 1 = 1 
                        END 
                    WHERE usr.`user_account_id` = '".DbAccess3::escape($userAccount)."' 
                      AND (
                        CASE
                          WHEN s.`is_customized` = 1 
                          THEN usr.country_id = '0' 
                          ELSE usr.country_id = '".DbAccess3::escape($toCountry)."' 
                        END 
                      )
                      AND CASE
                       WHEN s.service_type = 'C'
                       THEN ".$formCountry." IN (SELECT country_id FROM `service_collection_county` WHERE service_id = s.`id`)
                        ELSE 1 = 1      
                        END
                    AND s.`active` = 1 
                    AND s.`deletedq` = 0
                    ".$servicesWhereStr."
                    AND c.`status` = 1 
                    AND usr.`status` = 1
                    AND usr.`is_agreed` = 1
                    GROUP BY s.`id`";
           //echo $sql; die;
           //sAND (CASE WHEN ('".$serviceType."' IS NULL OR '".$serviceType."' = '') THEN 1 = 1 ELSE s.service_type = '".$serviceType."' END)
           //#((s.`origin_country` = '".DbAccess3::escape($formCountry)."' AND s.service_type != 'D') OR s.service_type = 'D')
            return Services::getServicesListFromSql($sql);
        }
    }
}

// class
?>
