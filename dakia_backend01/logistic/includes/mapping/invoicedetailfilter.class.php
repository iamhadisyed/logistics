<?php

/*
 * Consignment Filter
 *
 */

class InvoiceDetailFilter {

    private $filter = "";
    private $order_by = "";

    public function getList() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . $this->filter;
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;


        $sql = "SELECT *
				FROM invoice_detail
				$where
				$sort
				";

        //echo $sql;
        return InvoiceDetail::getListSql($sql);
    }


    public function getColumnList($fields) {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . $this->filter; //substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;


        $sql = "SELECT id, " . $fields . "
				FROM invoice_detail
				$where
				$sort
				";

        return InvoiceDetail::getListSql($sql);

        //	return highVolume::getHighVolumeListFromSql($sql);
    }

    public function getCount() {
        $result = $this->getList();
        return sizeof($result);
    }

    public function addFieldFilter($fieldName, $fieldValue) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " " . $fieldName . "='" . DbAccess3::escape($fieldValue) . "' ";
    }

    public function addFieldIsNullFilter($fieldName) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " ( " . $fieldName . " IS NULL OR " . $fieldName . " = '' )  ";
    }

    public function addQueryFilter($queryFilter) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " " . $queryFilter . " ";
    }

    public function orderBySort($sorter) {
        $this->order_by .= $sorter;
    }

    public function getPagingCount() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . $this->filter; //substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        $sql = "SELECT count(*) as total FROM invoice_detail $where $sort ";


        //return User::getTotalNumberOfUsersFromSql($sql);
        return Invoices::getTotalNumberOfInvoiceFromSql($sql);
    }

    public function getPagingList() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . $this->filter; //substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = " ORDER BY " . $this->order_by;


        $sql = "SELECT * FROM invoice_detail $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";




        //	return User::getUserListFromSql($sql);
        return InvoiceDetail::getListSql($sql);
    }


    public function setOffset($offset) {
        $this->pageOffset = $offset;
    }

    public function setRowsPerPage($rowsPP) {
        $this->rowsPerPage = $rowsPP;
    }

    public function setFilter($filter) {
        $this->filter = $filter;
    }

    public function expunge() {
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . $this->filter;
        }
        if (trim($where) != '') {
            $sql = "DELETE FROM  invoice_detail $where ";

            t($sql, __METHOD__);
            return InvoiceDetail::runQuery($sql);
        }
    }

    public function insertData($query) {
        if (trim($query) != '') {
          
            return InvoiceDetail::runQuery($query);
        }
    }
    public function updateData($query) {
        if (trim($query) != '') {
             
            return InvoiceDetail::runQuery($query);
        }
    }
    public function delete($debug = false) {
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . $this->filter;
        }
        $sql = "DELETE FROM invoice_detail $where";
        if ($debug) {
            echo $sql;
            die();
        }
        return InvoiceDetail::deleteInvoiceDetailsFromSql($sql);
    }

}
