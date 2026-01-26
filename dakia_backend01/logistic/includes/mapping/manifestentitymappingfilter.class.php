<?php

// get settings
class ManifestEntityMappingFilter {

    private $filter = "";
    private $order_by = "";
    private $groupBy = "";
    private $limit = 100;
    private $rowsPerPage = 0;
    private $pageOffset = 0;
    private $join = "";

    /**
     * Get list of ManifestEntityMapping items based on filter conditions
     *
     * @return array[ManifestEntityMapping]
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

        $sql = "SELECT count(mem.id) as total FROM  manifest_entity_mapping mem  $where $sort ";
        t($sql, __METHOD__);
        return ManifestEntityMapping::getTotalNumberOfManifestEntityMappingFromSql($sql);
    }

    public function setOffset($offset) {
        $this->pageOffset = $offset;
    }

    public function setRowsPerPage($rowsPP) {
        $this->rowsPerPage = $rowsPP;
    }

    public function getPagingList($columns = '*') {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;
        
       $sql = "SELECT " . $columns . "  FROM manifest_entity_mapping mem   $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
        t($sql, __METHOD__);
        return ManifestEntityMapping::getManifestEntityMappingListFromSql($sql);
    }

    /**
     * Get list of ManifestEntityMapping items based on filter conditions
     *
     * @return array[ManifestEntityMapping]
     */
    public function getList($columnName = "*") {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;
        $checkJoin = "";
        if ($this->join != "") {
            $checkJoin = $this->join;
        }

       $sql = "SELECT $columnName
                FROM manifest_entity_mapping mem
                $checkJoin
                $where
                $sort
                ";
        /*echo "<pre>";
        print_r($sql);
        echo "</pre>";
        die;*/
        t($sql, __METHOD__);
        return ManifestEntityMapping::getManifestEntityMappingListFromSql($sql);
    }
    public function getColumnList($fields, $recordLimit = 5000, $debug=false) {

        $fields = rtrim($fields, ",");
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;
        
        $groupBy = "";
        if ($this->groupBy != "")
            $groupBy = $this->groupBy;
        
        if ($recordLimit == '')
            $recordLimit = 5000;
        
        $checkJoin = "";
        if ($this->join != "") {
                $checkJoin = $this->join;
        }
        
        $sql = "SELECT " . $fields . ", mem.id 
				FROM manifest_entity_mapping mem
                                $checkJoin
				$where
                                $groupBy
				$sort
				LIMIT " . $recordLimit . "
				";
        if($debug)
        {
            echo $sql;
            die;
        }
        return ManifestEntityMapping::getManifestEntityMappingListFromSql($sql);
    }

  
    /**
     * Get count of ManifestEntityMapping items based on filter conditions
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

        $this->filter .= $filterVal . "";
    }
    
    public function addGroupBy($field) {
        $this->groupBy .= " GROUP BY $field";
    }

    public function addFieldFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " " . $colm . " = '" . DbAccess3::escape($value) . "'";
    }
    /***
    * Limit number of rows returned
    */
    public function setLimit ($lim)
    {
            $this->limit = $lim;
    }
    
    public function getConsignmentNo($manifestId='') {
        $where = '';
        if(!empty($manifestId)){
            $where .= " AND mem.`manifest_id` ='$manifestId'";
        }

        $sql = "SELECT 
                    mem.`entity_id`,
                    mem.`manifest_id`,
                    p.`consignment_id`
                  FROM
                    `manifest_entity_mapping` mem 
                    JOIN `parcel` p 
                      ON p.id = mem.`entity_id` 
                  WHERE mem.`manifest_entity_type` = 'p'  $where";
//      echo $sql;
        return ManifestEntityMapping::getManifestEntityMappingListFromSql($sql);
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
    public function addManifestJoin() {
        $this->join.= " JOIN `manifest` m  ON mem.manifest_id = m.id ";
    }
    public function addManifestServiceJoin() {
        $this->join.= " JOIN `manifest_service_mapping` msm  ON mem.manifest_id = m.id ";
    }
    public static function getServiceData($manifestId){
        $return = "";
        if($manifestId !="" && $manifestId > 0){
            $sql = "SELECT 
                        s.`name` AS id,
                        s.`id` AS service_id,
                        s.`carrier_id` AS manifest_id
                      FROM
                        `manifest_service_mapping` msm 
                        JOIN `services` s 
                          ON msm.`service_id` = s.`id` 
                      WHERE msm.`manifest_id` = '" . DbAccess3::escape($manifestId) . "' ";
            $return = ManifestEntityMapping::getManifestEntityMappingListFromSql($sql);
        }
        return $return;
    }
    public function addJoin($table,$joinField,$fromJoinField,$type="LEFT JOIN") {
        $this->join .= " " . $type ." " . $table . " " . "  ON " . $joinField . "  =  " . $fromJoinField;
    }
}

// class
?>