<?php

/*
 * SalesPotComission Filter
 *
 */

class SalesPotComissionFilter {

    private $filter = "";
    private $order_by = "";
    private $join = "";

    /**
     * Get list of SalesPotComission items based on filter conditions
     *
     * @return array[SalesPotComission]
     */
    public function getList() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        $sql = "SELECT * FROM sales_pot_comission $where $sort LIMIT 5000";

        t($sql, __METHOD__);
        return SalesPotComission::getSalesPotComissionListFromSql($sql);
    }

    public function getColumnList($fields, $recordLimit = 5000,$debug=false) {

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

        $sql = "SELECT " . $fields . ", id 
				FROM sales_pot_comission
				$where
				$sort
				LIMIT " . $recordLimit . "
				";
        if($debug){
            echo "<pre>";
            print_r($sql);
            echo "</pre>";
            die;
        }
        return SalesPotComission::getSalesPotComissionListFromSql($sql);
    }


    /**
     * Get count of SalesPotComission items based on filter conditions
     *
     * @return int
     */
    public function getCount() {
        $result = $this->getList();
        return sizeof($result);
    }

    /*     * *
     * Oder by  id
     */

    public function AddOrderById($ascending = true) {
        //if ($this->order_by != "") $this->order_by = "";
        //
		$this->order_by = "id" . ($ascending ? "" : " DESC");
    }

    public function addSalesPotComissionMd5IdFilter($data_value) {
        $this->filter .= " AND MD5(id) = '" . DbAccess3::escape($data_value) . "'";
    }

    public function addSalesPotComissionDateAddedFilter($date_value) {
        $this->filter .= " AND date(date_added) = '$date_value'";
    }
    public function addSalesPotComissionPaidFromToFilter($date_value_from, $date_value_to) {
        $this->filter .= " AND date(paid_date) >= '" . $date_value_from . "' and date(paid_date) <= '" . $date_value_to . "'";
    }

    public function addSalesPotComissionFromToAddedDateFilter($date_value_from, $date_value_to) {
        $this->filter .= " AND date(date_added) >= '" . $date_value_from . "' and date(date_added) <= '" . $date_value_to . "'";
    }

    public function addSalesPotComissionPaidAfterToDateFilter($date_value) {
        $this->filter .= " AND date(paid_date) >= '" . $date_value . "'";
    }

    public function addSalesPotComissionPaidBeforeDateFilter($date_value) {
        $this->filter .= " AND date(paid_date) <= '" . $date_value . "'";
    }
    public function addSalesPotComissionAfterToDateFilter($date_value) {
        $this->filter .= " AND date(date_added) >= '" . $date_value . "'";
    }

    public function addSalesPotComissionBeforeDateFilter($date_value) {
        $this->filter .= " AND date(date_added) <= '" . $date_value . "'";
    }

    public function addIsPaidFilter() {
        $this->filter .= " AND is_paid = '1'";
    }

    public function addIsNotPaidFilter() {
        $this->filter .= " AND is_paid = '0'";
    }
    public function addFieldFilter($fieldName, $fieldValue) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= "  " . $fieldName . " = '" . DbAccess3::escape($fieldValue) . "'";
    }
    public function getUserSaleData($userId,$userAccountId){
        $rtn = '';
        if(!empty($userId) && $userId > 0 && !empty($userAccountId) && $userAccountId > 0){
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
            $sql = 'SELECT
                          spc.*,
                          i.`invoice_no`,
                          i.`date_created` AS invoice_date_created,
                          i.`net_amount`,
                          ua.`user_account`
                        FROM
                          sales_pot_comission spc
                          '. $checkJoin .'
                        '.$where.'
                        LIMIT 5000';
            $rtn =  SalesPotComission::getSalesPotComissionListFromSql($sql);
        }
        return $rtn;
    }
    public function getPagingCount($debug=false) {
        // has filter been configured?
        $where = "WHERE ";
        $where .= $this->filter;
        $groupBy = $this->groupBy;
        $sql = "SELECT count(id) as total FROM sales_pot_comission spc $where " . $groupBy . " ";

        if($debug)
            echo $sql;
        return SalesPotComission::getTotalNumberOfSalePotComissionFromSql($sql);
    }
    public function setOffset($offset) {
        $this->pageOffset = $offset;
    }
    public function addJoin($table,$joinField,$fromJoinField,$type="LEFT JOIN") {
        $this->join .= " " . $type ." " . $table . " " . "  ON " . $joinField . "  =  " . $fromJoinField;
    }
    public function setRowsPerPage($rowsPP) {
        $this->rowsPerPage = $rowsPP;
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
        $checkJoin = "";
        if ($this->join != "")
            $checkJoin = $this->join;

        $sql = "SELECT $selectColumns FROM  sales_pot_comission spc ". $checkJoin . "  $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
        if($debug)
            echo $sql;
        return SalesPotComission::getSalesPotComissionListFromSql($sql);
    }
    public function addFieldLikeFilter($colm, $value) {
        $colm = trim($colm);
        $value = trim($value);
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " " . $colm . " LIKE '%" . DbAccess3::escape($value) . "%'";
    }
    public function AddOrderBy($name, $ascending = true) {
        //if ($this->order_by != "") $this->order_by = " ";
        //
        if (trim($name) != '')
            $this->order_by = $name . " " . ($ascending ? "" : " DESC");
    }
}
