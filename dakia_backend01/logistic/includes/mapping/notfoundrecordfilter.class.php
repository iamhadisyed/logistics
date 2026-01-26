<?php 
/*
 * Consignment Filter
 *
 */

class NotFoundRecordFilter {

    private $filter = [];
    private $orFilter = [];
    private $orderBy = [];
    private $pageOffset = 0;
    private $rowsPerPage = 0;
    private $Join = [];
    private $myFilter = "";

    public function getList($columns = '*', $debug = false) {
        $join = "";
        $orderBy = "";
        if (!empty($this->Join)) {
            $join = implode(" ", $this->Join);
        }
        $where = $this->getWhere();
        if (!empty($this->orderBy)) {
            $orderBy = "ORDER BY " . implode(",", $this->orderBy);
        }
        $sql = "SELECT SQL_CALC_FOUND_ROWS " . $columns . " FROM
                not_found_record nfr $join                
                $where $orderBy";
        if (!empty($this->rowsPerPage) && $this->rowsPerPage > 0) {
            $sql .= " LIMIT " . ((!empty($this->pageOffset) && $this->pageOffset > 0) ? $this->pageOffset : 0) . "," . $this->rowsPerPage;
        }
        if ($debug) {
            echo $sql;
            die();
        }
        return NotFoundRecord::getNotFountRecordListFromSql($sql);
    }

    public function getCount($soft = true, $debug = false) {
        if ($soft) {
            $sql = "SELECT FOUND_ROWS() as total";
        } else {
            $join = "";
            if (!empty($this->Join)) {
                $join = implode(" ", $this->Join);
            }
            $where = $this->getWhere();
            $sql = "SELECT COUNT(nfr.id) AS total FROM not_found_record nfr $join $where";
            if ($debug) {
                echo $sql;
                die();
            }
        }
        return NotFoundRecord::getTotalNumberOfNotFountRecordFromSql($sql);
    }

    public function delete($debug = false) {
        $join = "";
        if (!empty($this->Join)) {
            $join = implode(" ", $this->Join);
        }
        $where = $this->getWhere();
        $sql = "DELETE FROM not_found_record  $join $where";
        if ($debug) {
            echo $sql;
            die();
        }
        return NotFountRecord::delete($sql);
    }

    public function getWhere() {
        $where = "";
        if (!empty($this->filter)) {
            $whereAnd = implode(" AND ", $this->filter);
        }
        if (!empty($this->orFilter)) {
            $whereOr = implode(" OR ", $this->orFilter);
        }
        $where .= (!empty($whereAnd) ? $whereAnd : '');
        $where .= (!empty($whereOr) ? (!empty($where) ? " OR " : '') . $whereOr : '');
        if (!empty($where)) {
            $where = " WHERE " . $where;
        }
        return $where;
    }

    public function setOffset($offset) {
        $this->pageOffset = $offset;
    }

    public function setRowsPerPage($rowsPP) {
        $this->rowsPerPage = $rowsPP;
    }

    public function whereBetween($col, $startValue, $endValue, $includeValues = true) {
        $this->filter[] = $col . " >" . ($includeValues === true ? '= ' : ' ') . "'" . DbAccess3::escape($startValue) . "' AND " . $col . " <" . ($includeValues === true ? '= ' : ' ') . "'" . DbAccess3::escape($endValue) . "'";
    }

    public function orWhereBetween($col, $startValue, $endValue, $includeValues = true) {
        $this->orFilter[] = $col . " >" . ($includeValues === true ? '= ' : ' ') . "'" . DbAccess3::escape($startValue) . "' AND " . $col . " <" . ($includeValues === true ? '= ' : ' ') . "'" . DbAccess3::escape($endValue) . "'";
    }

    public function where($where, $operand = "=") {
        if (is_array($where)) {
            foreach ($where as $col => $val) {
                $this->filter[] = $col . " " . $operand . " '" . DbAccess3::escape($val) . "'";
            }
        } else if (is_string($where)) {
            $this->filter[] = $where;
        }
    }

    public function whereLike($where = []) {
        $this->where($where, "LIKE");
    }

    public function orWhereLike($where = []) {
        $this->orWhere($where, "LIKE");
    }

    public function orWhere($where, $operand = "=") {
        if (is_array($where)) {
            foreach ($where as $col => $val) {
                $this->orFilter[] = $col . " " . $operand . " '" . DbAccess3::escape($val) . "'";
            }
        } else if (is_string($where)) {
            $this->orFilter[] = $where;
        }
    }

    public function whereIn($field, $values, $not = false) {
        if (is_array($values)) {
            $this->filter[] = $field . ($not === true ? ' NOT' : '') . " IN ('" . implode("','", $values) . "')";
        } else {
            $this->filter[] = $field . ($not === true ? ' NOT' : '') . " IN (" . $values . ")";
        }
    }

    public function whereNotIn($field, $values) {
        $this->whereIn($field, $values, true);
    }

    public function join($joinTable, $where, $type = "") {
        $_where = [];
        $operand = '=';
        if (is_array($where)) {
            foreach ($where as $col => $val) {
                $_where[] = $col . " " . $operand . " " . DbAccess3::escape($val);
            }
            $_where = implode(" AND ", $_where);
        } else if (is_string($where)) {
            $_where = $where;
        }
        $this->Join[] = $type . " JOIN " . $joinTable . " ON " . $_where;
    }

    public function leftJoin($joinTable, $where) {
        $this->join($joinTable, $where, "LEFT");
    }

    public function rightJoin($joinTable, $where) {
        $this->join($joinTable, $where, "RIGHT");
    }

    public function innerJoin($joinTable, $where) {
        $this->join($joinTable, $where, "INNER");
    }

    public function orderBy($order, $ascdesc = "ASC") {
        $this->orderBy[] = $order . " " . $ascdesc;
    }
    public function addFieldFilter($colm, $value) {
        if ($this->myFilter != "")
            $this->myFilter .= " AND ";
        $this->myFilter .= " " . $colm . " = '" . DbAccess3::escape($value) . "'";
    }
    public function getColumnList($fields, $recordLimit = 5000) {
        $fields = rtrim($fields, ",");
        // has filter been configured?
        $where = "";
        if ($this->myFilter != "") {
            $where = "WHERE " . substr($this->myFilter, 4);
        }
        
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        if ($recordLimit == '')
            $recordLimit = 5000;

        $sql = "SELECT " . $fields . ", id 
				FROM not_found_record nfr
				$where
				$sort
				LIMIT " . $recordLimit . "
				";
        return NotFoundRecord::getNotFountRecordListFromSql($sql);
    }
}
