<?php

/*
 * Consignment Filter
 *
 */

class VehicleDriverFilter
{

    private $filter = [];
    private $orFilter = [];
    private $orderBy = [];
    private $pageOffset = 0;
    private $rowsPerPage = 0;
    private $Join = [];

    public function getList($columns = '*', $debug = false)
    {
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
                vehicle_driver dds $join                
                $where $orderBy";
        if (!empty($this->rowsPerPage) && $this->rowsPerPage > 0) {
            $sql .= " LIMIT " . ((!empty($this->pageOffset) && $this->pageOffset > 0) ? $this->pageOffset : 0) . "," . $this->rowsPerPage;
        }
        if ($debug) {
            echo $sql;
            die();
        }
        return VehicleDriver::getDriversListFromSql($sql);
    }

    public function getCount($soft = true, $debug = false)
    {
        if ($soft) {
            $sql = "SELECT FOUND_ROWS() as total";
        } else {
            $join = "";
            if (!empty($this->Join)) {
                $join = implode(" ", $this->Join);
            }
            $where = $this->getWhere();
            $sql = "SELECT COUNT(dds.id) AS total FROM vehicle_driver dds $join $where";
            if ($debug) {
                echo $sql;
                die();
            }
        }
        return VehicleDriver::getTotalNumberOfDriversFromSql($sql);
    }

    public function delete($debug = false)
    {
        $join = "";
        if (!empty($this->Join)) {
            $join = implode(" ", $this->Join);
        }
        $where = $this->getWhere();
        $sql = "DELETE FROM vehicle_driver  $join $where";
        if ($debug) {
            echo $sql;
            die();
        }
        return VehicleDriver::delete($sql);
    }

    public function update($data, $debug = false)
    {
        $set = '';
        foreach ($data as $key => $value) {
            $set .= "{$key}={$value}, ";
        }
        $set = rtrim($set, ", ");
        $where = $this->getWhere();
        $sql = "UPDATE vehicle_driver SET $set {$where}";
        if ($debug) {
            echo $sql;
            die();
        }
        DbAccess3::runQuery($sql);
    }

    public function getWhere()
    {
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

    public function setOffset($offset)
    {
        $this->pageOffset = $offset;
    }

    public function setRowsPerPage($rowsPP)
    {
        $this->rowsPerPage = $rowsPP;
    }

    public function where($where, $operand = "=")
    {
        if (is_array($where)) {
            foreach ($where as $col => $val) {
                $this->filter[] = $col . " " . $operand . " '" . DbAccess3::escape($val) . "'";
            }
        } else if (is_string($where)) {
            $this->filter[] = $where;
        }
    }

    public function orWhere($where, $operand = "=")
    {
        if (is_array($where)) {
            foreach ($where as $col => $val) {
                $this->orFilter[] = $col . " " . $operand . " '" . DbAccess3::escape($val) . "'";
            }
        } else if (is_string($where)) {
            $this->orFilter[] = $where;
        }
    }

    public function whereIn($field, $values, $not = false)
    {
        if (is_array($values)) {
            $this->filter[] = $field . ($not === true ? ' NOT' : '') . " IN ('" . implode("','", $values) . "')";
        } else {
            $this->filter[] = $field . ($not === true ? ' NOT' : '') . " IN (" . $values . ")";
        }
    }

    public function join($joinTable, $where, $type = "")
    {
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

    public function innerJoin($joinTable, $where)
    {
        $this->join($joinTable, $where, "INNER");
    }

    public function orderBy($order, $ascdesc = "ASC")
    {
        $this->orderBy[] = $order . " " . $ascdesc;
    }


    public function addFilter($where, $operand = '=')
    {
        if (is_array($where)) {
            foreach ($where as $col => $val) {
                $this->filter[] = $col . " " . $operand . " '" . DbAccess3::escape($val) . "'";
            }
        } else if (is_string($where)) {
            $this->filter[] = $where;
        }
    }
}
