<?php

// get settings
class MarketPlacesLogFilter {

    private $filter = "";
    private $order_by = "";
    private $groupby = "";
    private $limit = 100;
    private $rowsPerPage = 0;
    private $pageOffset = 0;

    /**
     * Get list of MarketPlacesLog items based on filter conditions
     *
     * @return array[MarketPlacesLog]
     */
    public function getPagingCount() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;
        $groupBy = "";
        if ($this->groupby != "")
            $groupBy = " GROUP BY " . $this->groupby;
        $checkJoin = "";
        if ($this->join != "")
            $checkJoin = $this->join;

        $sql = "SELECT count(pbm.id) as total FROM market_places_log mpl  $checkJoin $where $groupBy $sort ";
//        echo '<pre>';
//        print_r($sql);
//        echo '</pre>';
//        die;
        t($sql, __METHOD__);
        return MarketPlacesLog::getTotalNumberOfMarketPlacesLogFromSql($sql);
    }

    public function getParcelPagingCount() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;
        $groupBy = "";
        if ($this->groupby != "")
            $groupBy = " GROUP BY " . $this->groupby;
        $checkJoin = "";
        if ($this->join != "")
            $checkJoin = $this->join;

        $sql = "SELECT COUNT(id) AS total FROM (SELECT pbm.id FROM market_places_log mpl  $checkJoin $where $groupBy $sort ) AS fdf";
//        echo '<pre>';
//        print_r($sql);
//        echo '</pre>';
//        die;
        t($sql, __METHOD__);
        return MarketPlacesLog::getTotalNumberOfMarketPlacesLogFromSql($sql);
    }

    public function setOffset($offset) {
        $this->pageOffset = $offset;
    }

    public function setRowsPerPage($rowsPP) {
        $this->rowsPerPage = $rowsPP;
    }

    public function getPagingList($columns = '*',$debug=false) {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;
        $groupBy = "";
        if ($this->groupby != "")
            $groupBy = " GROUP BY " . $this->groupby;
        $checkJoin = "";
        if ($this->join != "")
            $checkJoin = $this->join;

        $sql = "SELECT " . $columns . "  FROM market_places_log mpl $checkJoin  $where $groupBy $sort LIMIT $this->pageOffset , $this->rowsPerPage";
        if($debug) {
            echo '<pre>';
            print_r($sql);
            echo '</pre>';
            die;
        }
        t($sql, __METHOD__);
        return MarketPlacesLog::getMarketPlacesLogListFromSql($sql);
    }

    /**
     * Get list of MarketPlacesLog items based on filter conditions
     *
     * @return array[MarketPlacesLog]
     */
    public function getList($columnName = "*", $debug =false) {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        $limit = "";
        if ($this->limit != "")
            $limit = "LIMIT " . $this->limit;

        $checkJoin = "";
        if ($this->join != "")
            $checkJoin = $this->join;

        $sql = "SELECT $columnName 
                FROM market_places_log mpl 
                $checkJoin
                $where 
                $sort 
                $limit
                ";
        if($debug)
        {       echo $sql; die;
        }
        t($sql, __METHOD__);
        return MarketPlacesLog::getMarketPlacesLogListFromSql($sql);
    }
    public function getColumnList($fields, $recordLimit = 5000) {

        $fields = rtrim($fields, ",");
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        if ($recordLimit == '')
            $recordLimit = 5000;

        $checkJoin = "";
        if ($this->join != "")
            $checkJoin = $this->join;

        $groupBy = "";
        if ($this->groupby != "")
            $groupBy = " GROUP BY " . $this->groupby;

        $sql = "SELECT " . $fields . ", pbm.id 
				FROM market_places_log mpl
				$checkJoin
				$where
				$groupBy
				$sort
				LIMIT " . $recordLimit . "
				";
        return MarketPlacesLog::getMarketPlacesLogListFromSql($sql);
    }

    public function orderBy($order, $ascdesc = "ASC")
    {
        $this->order_by .= $order . " " . $ascdesc;
    }



    function delete_parcel_from_mapping() {
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sql =  "DELETE 
                    FROM parcel_bagging_mapping
                    $where";
        return MarketPlacesLog::deleteBySql($sql);
    }
    /**
     * Get count of MarketPlacesLog items based on filter conditions
     *
     * @return int
     */
    public function getCount() {
        $result = $this->getList();
        return sizeof($result);
    }
    /*     * *
     * Oder by Column Name
     */

    public function AddOrderBy($columnName = "id", $ascending = true) {
        if ($this->order_by != "")
            $this->order_by .= ", ";
        //
        $this->order_by .= $columnName . ($ascending ? "" : " DESC");
    }

    public function addFieldLikeFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";

        $this->filter .= "    " . $colm . " LIKE '" . DbAccess3::escape($value) . "%'";
    }

    public function addFilter($filterVal) {
        if ($this->filter != "")
            $this->filter .= " AND ";

        $this->filter .= "    " . DbAccess3::escape($filterVal) . " ";
    }
    public function addFieldFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " " . $colm . " = '" . DbAccess3::escape($value) . "'";
    }
    public function addJoin($table,$joinField,$fromJoinField,$type="LEFT JOIN") {
        $this->join .= " " . $type ." " . $table . " " . "  ON " . $joinField . "  =  " . $fromJoinField;
    }
    public function addGroupBy($colm) {
        if ($this->groupby != "")
            $this->groupby .= " , ";
        $this->groupby .= "  " . $colm . " ";
    }
    public function addFilterIn($field, $values, $filter='filter') {
        $finalArr = [];
        if (trim($this->$filter) != "") {
            $this->$filter .= " AND ";
        }
        if (is_array($values)) {
            foreach ($values as $trackinNumber) {
                $finalArr[] = trim(trim(ParseTrackingNumber::Parse($trackinNumber),"\n"),"\r");
            }
            $this->$filter .= $field ." IN ('" . implode("','", $finalArr) . "')";
        } else {
            $this->$filter .= $field . " IN (" . $values . ")";
        }
    }
    /***
     * Limit number of rows returned
     */
    public function setLimit ($lim)
    {
        $this->limit = $lim;
    }
    public static function checkPreSortBag($bagId,$carrierHubId) {
        $returnRes = false;
        if($bagId > 0 && $carrierHubId > 0){
            $sql = "SELECT 
                            p.routing_code 
                          FROM
                            market_places_log mpl 
                            JOIN parcel p 
                              ON pbm.parcel_id = p.id 
                            JOIN carrier_hubs ch 
                              ON ch.`routing_code` = p.`routing_code` 
                          WHERE pbm.bag_id = '".$bagId."' 
                            AND ch.`id` = '".$carrierHubId."' 
                          GROUP BY p.`routing_code` ";
            $totalRec = MarketPlacesLog::getMarketPlacesLogListFromSql($sql);
            if(count($totalRec) == 1){
                $returnRes = true;
            }
        }
        return $returnRes;
    }
}

// class
?>