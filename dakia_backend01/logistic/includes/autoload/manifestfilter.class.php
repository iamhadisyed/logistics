<?php
/*
 * Consignment Filter
 *
 */
class ManifestFilter
{
    private $filter = "";
    private $order_by = "";
    private $limit = 100;
    private $join = "";

    /**
     * Get list of consignment items based on filter conditions
     *
     * @return array[Consignment]
     */
    public function getList()
    {
        // has filter been configured?
        $where = "";
        if ($this->filter != "")
        {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;

        $limit = "";
        if($this->rowsPerPage > 0) {
            $limit = " LIMIT $this->pageOffset , $this->rowsPerPage";
        }
        $checkJoin = "";
        if ($this->join != "")
            $checkJoin = $this->join;

        $sql = "SELECT *
                FROM manifest m
                $checkJoin
                $where
                $sort
                $limit
                ";
        t($sql, __METHOD__);
        return Manifest::getManifestListFromSql($sql);
    }
    public function addJoin($table,$joinField,$fromJoinField,$type="LEFT JOIN") {
        $this->join .= " " . $type ." " . $table . " " . "  ON " . $joinField . "  =  " . $fromJoinField;
    }
    /****
     * Get distinct list of flight numbers, using current filter conditions.
     */


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
     * Limit list to consignments with given states.
     *
     */
    public function addStatusFilter($mixed_status)
    {
        // list of states?
        if (is_array($mixed_status))
        {
            $where = "";
            foreach ($mixed_status as $status)
            {
                if ($where != "") $where .= " OR ";
                $where .= "m.consignment_status='" . $status . "'";
            }
            if ($where != "")
            {
                $this->filter .= "AND ($where)";
            }
        }

        // specific status
        else
        {
            $this->filter .= "AND m.consignment_status='" . $mixed_status . "'";
        }
    }

    
    public function addDateFilter($date_value1, $date_value2,$upcomming=false) 
    {
        if($date_value1 != '') {
            $this->filter .= " AND ";
            if($upcomming){
              $this->filter .= "(date_created>='" . date('Y-m-d 00:00:00', strtotime($date_value1)) . "'";
            }else{
                $this->filter .= "(m.date_created>='" . date('Y-m-d 00:00:00', strtotime($date_value1)) . "'";
            }
        }
        if($date_value2 != '') {
            $this->filter .= " AND ";
            if($upcomming){
              $this->filter .= "date_created<='" . date('Y-m-d 23:59:59',strtotime($date_value2)) . "')";
            }else{
                $this->filter .= "m.date_created<='" . date('Y-m-d 23:59:59',strtotime($date_value2)) . "')";
            }
        }
    }

    /***
     * Limit number of rows returned
     */
    public function setLimit ($lim)
    {
        $this->limit = $lim;
    }


    /***
     * Filter on flight number
     */
    public function addFlightNumberFilter ($flight_number)
    {
        $this->filter .= " AND m.flight_number Like '" . $flight_number . "%'";
    }
    
    
    //Pagaing filters
    
     public function getPagingCount() {
         // has filter been configured?
         $where = "";
         if ($this->filter != "") {
             $where = "WHERE " . substr($this->filter, 4);
         }
         $sort = "";
         if ($this->order_by != "")
             $sort = "ORDER BY " . $this->order_by;

         $sql = "SELECT COUNT(id) AS total FROM (SELECT
                    m.id,
                    m.date_created,
                    m.service_id,
                    m.weight,
                    m.is_dispatched,
                    m.is_send_email,
                    m.file_name,
                    m.pdf_file
                  FROM
                    manifest m
                    JOIN `user` u
                      ON u.`id` = m.`user_id`
                    INNER JOIN manifest_entity_mapping mem
                      ON m.`id` = mem.`manifest_id`
                    INNER JOIN parcel p
                      ON p.`id` = mem.`entity_id`
                      AND mem.`manifest_entity_type` = 'p'
                    INNER JOIN tracking_data td
                      ON p.`tracking_number` = td.`tracking_number`
                    $where
                    GROUP BY m.id
                    $sort) AS manifest_count";
        t($sql, __METHOD__);
        return Manifest::getTotalNumberOfManifestFromSql($sql);
    }
    public function getOpPagingList($debug =false) {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;
        $limit = "";
        if($this->rowsPerPage > 0) {
            $limit = " LIMIT $this->pageOffset , $this->rowsPerPage";
        }
        
        $sql = "SELECT
                    m.id,
                    GROUP_CONCAT( DISTINCT p.`tracking_number`) AS pieces,
                    m.date_created,
                    m.service_id,
                    m.weight,
                    m.is_dispatched,
                    m.is_send_email,
                    m.file_name,
                    m.pdf_file
                  FROM
                    manifest m
                    JOIN `user` u
                      ON u.`id` = m.`user_id`
                    INNER JOIN manifest_entity_mapping mem
                      ON m.`id` = mem.`manifest_id`
                    INNER JOIN parcel p
                      ON p.`id` = mem.`entity_id`
                      AND mem.`manifest_entity_type` = 'p'
                    INNER JOIN tracking_data td
                      ON p.`tracking_number` = td.`tracking_number`
                    $where
                    GROUP BY m.id
                    $sort
                    $limit";
        if($debug)
        {
            echo $sql;
            die;
        }
        t($sql, __METHOD__);
        return Manifest::getManifestListFromSql($sql);
    }
    public function getRypPagingCount() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        $sql = "SELECT COUNT(id) AS total FROM (SELECT
                    m.id
                  FROM
                    manifest m
                    JOIN `user` u
                      ON u.`id` = m.`user_id`
                    INNER JOIN manifest_entity_mapping mem
                      ON m.`id` = mem.`manifest_id`
                     INNER JOIN manifest_service_mapping msm
                      ON msm.`manifest_id` = m.`id`
                    INNER JOIN parcel p
                      ON p.`id` = mem.`entity_id`
                      AND mem.`manifest_entity_type` = 'p'
                    $where
                    GROUP BY m.id
                    $sort) AS manifest_count";
        t($sql, __METHOD__);
        return Manifest::getTotalNumberOfManifestFromSql($sql);
    }
    public function setOffset($offset) {
        $this->pageOffset = $offset;
    }

    public function setRowsPerPage($rowsPP) {
        $this->rowsPerPage = $rowsPP;
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
        $limit = "";
        if($this->rowsPerPage > 0) {
            $limit = " LIMIT $this->pageOffset , $this->rowsPerPage";
        }
        
        $sql = "SELECT m.* FROM manifest m 
                    JOIN `user` u 
                        ON u.`id` = m.`user_id`
                    $where
                    $sort
                    $limit";
        t($sql, __METHOD__);
        return Manifest::getManifestListFromSql($sql);
    }
    public function getPagingRypList() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;
        $limit = "";
        if($this->rowsPerPage > 0) {
            $limit = " LIMIT $this->pageOffset , $this->rowsPerPage";
        }

        $sql = "SELECT
                      m.id,
                      m.`pdf_file`,
                      m.`weight`
                    FROM
                      manifest m
                      JOIN `user` u
                        ON u.`id` = m.`user_id`
                      JOIN `manifest_entity_mapping` mem
                        ON mem.`manifest_id` = m.`id`
                    $where
                    $sort
                    $limit";
        t($sql, __METHOD__);
        return Manifest::getManifestListFromSql($sql);
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
    public function AddOrderBy($columnName = "id", $ascending = true) {
        if ($this->order_by != "")
            $this->order_by .= ", ";
        $this->order_by .= $columnName . ($ascending ? "" : " DESC");
    }
    public function addFieldFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " " . $colm . " = '" . DbAccess3::escape($value) . "'";
    }
    
    public function getUpCommingPagingList($userAccount, $debug =false) {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;
        $limit = "";
        if($this->rowsPerPage > 0) {
            $limit = " LIMIT $this->pageOffset , $this->rowsPerPage";
        }
        if(empty($where)){
            
            $where = "WHERE ";
        }else{
            $where .= " AND ";
        }
        $sql = "SELECT
                    *,
                    GROUP_CONCAT(DISTINCT `tracking_number`) AS pieces
                    FROM
                    (SELECT
                    m.id,
                    m.id AS manifest_id,
                     p.`tracking_number`,
                    td.id AS tracking_data_id,
                    m.`user_id`,
                    m.date_created,
                    m.service_id,
                    m.weight,
                    m.is_dispatched,
                    m.is_send_email,
                    m.file_name,
                    m.pdf_file
                    FROM
                    manifest m
                    JOIN `user` u
                      ON u.`id` = m.`user_id`
                    INNER JOIN manifest_entity_mapping mem
                      ON m.`id` = mem.`manifest_id`
                    INNER JOIN parcel p
                      ON p.`id` = mem.`entity_id`
                      AND mem.`manifest_entity_type` = 'p'
                    INNER JOIN tracking_data td
                      ON p.`tracking_number` = td.`tracking_number`
                    $where u.user_account_id = '" . DbAccess3::escape($userAccount) . "'
                    AND td.`tracking_number` IN (SELECT tracking_number FROM tracking_data GROUP BY tracking_number HAVING COUNT(tracking_number) = 1)
                    AND m.manifest_by = 'client'
                    AND td.`status_code_id` = 144) AS upcommingMenifest
                    GROUP BY id
                    $sort
                    $limit";
        if($debug)
        {
            echo $sql;
            die;
        }
        t($sql, __METHOD__);
        return Manifest::getManifestListFromSql($sql);
    }
    
     public function getUpCommingPagingCount($userAccountId) {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;
        if(empty($where)){
            $where = "WHERE ";
        }else{
            $where .= " AND ";
        }
        $sql = "SELECT
                    COUNT(*)
                    FROM
                    (SELECT
                    m.id,
                    m.id AS manifest_id,
                     p.`tracking_number`,
                    td.id AS tracking_data_id,
                    m.`user_id`,
                    m.date_created,
                    m.service_id,
                    m.weight,
                    m.is_dispatched,
                    m.is_send_email,
                    m.file_name,
                    m.pdf_file
                    FROM
                    manifest m
                    JOIN `user` u
                      ON u.`id` = m.`user_id`
                    INNER JOIN manifest_entity_mapping mem
                      ON m.`id` = mem.`manifest_id`
                    INNER JOIN parcel p
                      ON p.`id` = mem.`entity_id`
                      AND mem.`manifest_entity_type` = 'p'
                    INNER JOIN tracking_data td
                      ON p.`tracking_number` = td.`tracking_number`
                    $where u.user_account_id = '" . DbAccess3::escape($userAccountId) . "'
                    AND td.`tracking_number` IN (SELECT tracking_number FROM tracking_data GROUP BY tracking_number HAVING COUNT(tracking_number) = 1)
                    AND m.manifest_by = 'client'
                    AND td.`status_code_id` = 144) AS upcommingMenifest
                    GROUP BY id
                    $sort";
        t($sql, __METHOD__);
        return Manifest::getManifestListFromSql($sql);
    }
    public function addFilterIn($field, $values) {
        $finalArr = [];
        if (trim($this->filter) != "") {
          $this->filter .= " AND ";
        }
        if (is_array($values)) {
            foreach ($values as $trackinNumber) {
                $finalArr[] = ParseTrackingNumber::Parse($trackinNumber);
            }
         $this->filter .= $field ." IN ('" . implode("','", $finalArr) . "')";
        }
    }

    public function updateManifestWeight($manifestId) {
        $sql = "UPDATE
                  manifest m
                  INNER JOIN
                    (SELECT
                      mem.`manifest_id`,
                      SUM(p.`weight`) AS total_weight
                    FROM
                      `manifest_entity_mapping` mem
                      JOIN parcel p
                        ON p.id = mem.`entity_id`
                        AND mem.`manifest_entity_type` = 'p'
                    WHERE mem.`manifest_id` = '" . DbAccess3::escape($manifestId) . "') ps
                    ON m.`id` = ps.manifest_id SET m.`weight` = ps.total_weight
                WHERE m.`id` = '" . DbAccess3::escape($manifestId) . "'";
        DbAccess3::runQuery($sql);
    }
}