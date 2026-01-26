<?php

/*
 * ServiceCountryTime Filter
 *
 */

class ServiceCountryTimeFilter {

    private $filter = "";
    private $order_by = "";
    private $limit = 100;
    private $rowsPerPage = 10;
    private $pageOffset = 0;

    const PAGE_SIZE = 20;

    /**
     * Get list of Service Country Time items based on filter conditions
     *
     */
    public function getList($limit=true, $debug = false) {
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        $manifestjoin = "";
        if ($this->join != "")
            $manifestjoin = $this->join;

        $serviceJoinColumn = "";
        if (strpos($manifestjoin, 'JOIN `services`') !== false) {
            $serviceJoinColumn = ",`services`.code, `services`.carrier_id";
        }
        $limitStr = "";
        if($limit)
            $limitStr = " LIMIT $this->pageOffset , $this->rowsPerPage";
        
        $sql = "SELECT service_country_ttime.* ".$serviceJoinColumn." from service_country_ttime $manifestjoin $where $sort ".$limitStr;
        if($debug){
            echo $sql;
            die;
        }
        
        return ServiceCountryTime::getServiceCountryTimeListFromSql($sql);
    }
    


    public function getOrderByList() {
        $sql = "SELECT * FROM service_country_ttime ORDER BY id DESC";

        $service_countrytime = new ServiceCountryTime();

        return ServiceCountryTime::getServiceCountryTimeListFromSql($sql);
    }

    public function getColumnList($fields) {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        $sql = "SELECT id, " . $fields . "FROM service_country_ttime $where $sort ";

        t($sql, __METHOD__);

        return ServiceCountryTime::getServiceCountryTimeListFromSql($sql);
    }

    
    public function getCountryList($fields) {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

         $sql = "SELECT sct.id, " . $fields . " FROM service_country_ttime sct INNER JOIN country c on sct.id_country = c.id $where $sort ";

        return ServiceCountryTime::getServiceCountryTimeListFromSql($sql);
    }
    /**
     * Get count of items based on filter conditions
     *
     * @return int
     */
    public function getCount() {
        $result = $this->getList();
        return sizeof($result);
    }

    public function getPagingCount() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        $sql = "SELECT count(*) as total FROM service_country_ttime $where $sort ";

        t($sql, __METHOD__);

        return ServiceCountryTime::getTotalServiceCountryTimeListFromSql($sql);
    }

    public function getPagingList() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        echo $sql = "SELECT * FROM service_country_ttime $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";

        t($sql, __METHOD__);

        return ServiceCountryTime::getServiceCountryTimeListFromSql($sql);
    }

    public function setOffset($offset) {
        $this->pageOffset = $offset;
    }

    public function setRowsPerPage($rowsPP) {
        $this->rowsPerPage = $rowsPP;
    }

    public function addFieldLikeFilter($colm, $value) {
        $this->filter .= " AND ";
        $this->filter .= " " . $colm . " LIKE '" . DbAccess3::escape($value) . "%'";
    }
    public function addFieldFilter($colm, $value) {
        $this->filter .= " AND ";
         $this->filter .= " " . $colm . " = '" . DbAccess3::escape($value) ."'";
    }

    public function AddOrderBy($field, $ascending = true) {
        $this->order_by = $field . "" . ($ascending ? "" : " DESC");
    }

    public function addFilter($filter) {
        if (!empty($filter))
            $this->filter .= " AND " . $filter;
    }
    public function addServiceTableJoin() {
        $this->join.= " JOIN `services`  ON service_country_ttime.id_service = `services`.id ";
    }
}
