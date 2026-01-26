<?php

// get settings
//require_once("includes/settings/common.inc.php");

class AgentDataFilter {

    private $filter = "";
    private $groupBy = "";
    private $rowsPerPage = 0;
    private $pageOffset = 0;
    private $join = "";

    /**
     * Get list of user items based on filter conditions
     *
     * @return array[User]
     */
    public function getList($debug=false) {


        // has filter been configured?
        $where = "";
        if (trim($this->filter) != '') {
            $where = "WHERE " . substr($this->filter,4);
        }
        if (trim($this->order_by) != '') {
            $groupBy = 'Order by ' . $this->order_by;
        }
        
        $agentDocumentjoin = "";
        if ($this->join != "")
            $agentDocumentjoin = $this->join;
        
        $agentJoinColumn = "";
        if (strpos($agentDocumentjoin, 'JOIN `agent_document`') !== false) {
            $agentJoinColumn = ",`ad`.*";
        }
        $sql = "SELECT a.* ".$agentJoinColumn." FROM agent_data a $agentDocumentjoin  $where " . $groupBy . " Order by agent_code asc";

        if($debug)
            echo $sql;
        return AgentData::getAgentListFromSql($sql);
    }

    /**
     * Get list of user items based on filter conditions
     *
     * @return array[User]
     */
    public function getColumnList($fields, $debug=false) {


        // has filter been configured?
        $where = "WHERE a.agent_code <> '' ";
        $where .= $this->filter;
        $groupBy = $this->groupBy;
        if (@$this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;
        else
            $sort = "ORDER BY agent_code asc";
        $sql = "SELECT " . $fields . ", id FROM agent_data a $where " . $groupBy . " " . $sort;
        if($debug)
            echo $sql;
        return AgentData::getAgentListFromSql($sql);
    }

    public function AddOrderBy($name, $ascending = true) {
        //if ($this->order_by != "") $this->order_by = " ";
        //
		if (trim($name) != '')
            $this->order_by = $name . " " . ($ascending ? "" : " DESC");
    }

    public function getPagingCount() {
        // has filter been configured?
        $where = "WHERE a.agent_code <> '' ";
        $where .= $this->filter;
        $groupBy = $this->groupBy;
        $sort = $this->order_by;
        $sql = "SELECT count(id) as total FROM agent_data a $where " . $groupBy . " Order by agent_code asc";
        t($sql, __METHOD__);
        return AgentData::getTotalNumberOfAgentFromSql($sql);
    }

    public function getPagingList($fields, $debug = false) {
        $where = "WHERE a.agent_code <> '' ";
        $where .= $this->filter;
        $groupBy = $this->groupBy;
        if($this->order_by != '')
            $sort = " order by " . $this->order_by;

       $sql = "SELECT " . $fields . " , id FROM  agent_data a  $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
        
       if($debug)
       {
           echo $sql;
           die;
       }

        return AgentData::getAgentListFromSql($sql);
    }

    public function setOffset($offset) {
        $this->pageOffset = $offset;
    }

    public function setRowsPerPage($rowsPP) {
        $this->rowsPerPage = $rowsPP;
    }

    public function addAgentIdInFilter($agent_id) {

        $this->filter .= " AND ";
        $this->filter .= "a.id  in ('" . implode("','", $agent_id) . "')";
    }


    public function addFieldFilter($colm, $value) {

        $this->filter .= " AND ";
        $this->filter .= " " . $colm . " = '" . DbAccess3::escape($value) . "'";
    }

    public function addAgentCodeFilter($agent_code) {

        $this->filter .= " AND ";
        $this->filter .= "a.agent_code  = '" . DbAccess3::escape($agent_code) . "'";
    }

    public function addCountryFilter($country) {

        $this->filter .= " AND ";
        $this->filter .= "a.country = '" . DbAccess3::escape($country) . "'";
    }

    public function addActiveFilter($active) {

        $this->filter .= " AND ";
        $this->filter .= "a.active = '" . $active . "'";
    }

    public function addCityFilter($city) {

        $this->filter .= " AND ";
        $this->filter .= "a.city = '" . DbAccess3::escape($city) . "'";
    }

    public function addFilter($filterVal) {
        if ($this->filter != "")
            $this->filter .= "    AND ";

        $this->filter .= "    " . $filterVal . " ";
    }

    public function addFieldLikeFilter($colm, $value) {
        $this->filter .= " AND ";
        $this->filter .= " " . $colm . " LIKE '" . DbAccess3::escape($value) . "%'";
    }

    public function addIdFilter($code) {
        $this->filter .= " AND ";
        $this->filter .= "a.id = '" . DbAccess3::escape($code) . "'";
    }
    public static function checkServicesWeightLimit($agentsId, $serviceId,$fromWeight,$toWeight) {
       $sql = "SELECT COUNT(id) AS total FROM service_agent_mapping WHERE agentid = '". DbAccess3::escape($agentsId)."' AND  serviceid = '". DbAccess3::escape($serviceId)."' AND from_weight  <= '".DbAccess3::escape($fromWeight)."' AND to_weight >= '".DbAccess3::escape($toWeight)."'";
       //$sql = "SELECT COUNT(id) AS total FROM `services` s WHERE  s.id = '". DbAccess3::escape($serviceId)."' AND s.from_weight <= '".DbAccess3::escape($fromWeight)."' AND s.to_weight >= '".DbAccess3::escape($toWeight)."'";
       $rs = DbAccess3::runQuery($sql);
       $data = mysqli_fetch_assoc($rs);
       return $data['total'];
    }
    public static function checkServiceAgent($serviceId){
        $return = "";
        if($serviceId > 0){
            $sql = "SELECT agentid AS id FROM service_agent_mapping WHERE serviceid = '". DbAccess3::escape($serviceId)."' ";
            $return =  AgentData::getAgentListFromSql($sql);
        }
        return $return;
    }

    
    public function myContractList($user_id, $debug=false)
    {
            $sql = "SELECT 
    a.id,
    a.agent_code,
    a.date_created,
    a.active,
	scv.constant_value AS 'contract_name',
	a.active as status,
    cr.carrier,
    cr.logo,
    cr.id AS carrier_id,
    s.id AS service_id
FROM
    agent_data a
        INNER JOIN
    service_agent_mapping sam ON sam.agentid = a.id AND a.user_id = '".$user_id."'
        INNER JOIN
    services s ON sam.serviceid = s.id
        INNER JOIN
    carrier cr ON s.carrier_id = cr.id
        INNER JOIN
    carrier_service_customize_rules csc ON csc.agentid = a.id
		INNER JOIN 
	user_services_routing usr ON usr.service_id = s.id AND usr.user_account_id = '".$user_id."'
    INNER JOIN 
		service_constant_value scv ON a.id = scv.agent_id AND scv.service_id = sam.serviceid AND scv.constant_id IN (SELECT 
                    id
                FROM
                    service_constant
                WHERE
                    carrier_id = 0)
GROUP BY agent_code";

         if($debug)
             echo $sql;
         return AgentData::getAgentListFromSql($sql);
    }
    public function getDispatchAgent() {
        $sql = "SELECT 
                    id
                  FROM
                    agent_data a 
                  WHERE a.agent_code <> '' 
                    AND agent_type = 'dispatch' 
                    OR agent_type = 'both' 
                  ORDER BY agent_code ASC";
        return AgentData::getAgentListFromSql($sql);
    }
    public function getAgentData($agentType="outbound") {
        $sql = "SELECT 
                    id
                  FROM
                    agent_data a 
                  WHERE a.agent_code <> '' 
                    AND agent_type = '".DbAccess3::escape($agentType)."'
                  ORDER BY agent_code ASC";
        return AgentData::getAgentListFromSql($sql);
    }
    public static function getUniqueAgent($agentCode) {
        if(!empty($agentCode)){
            $sql = "SELECT 
                    id
                  FROM
                    agent_data a 
                  WHERE a.agent_code <> '' 
                    AND agent_code = '".DbAccess3::escape($agentCode)."' ";
            return AgentData::getAgentListFromSql($sql);
        }
    }
}

// class
?>