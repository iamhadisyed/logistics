<?php
/*
 * Carrier Zones Postcode Filter
 *
 */

class CarrierZonesPostcodeFilter
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
        $groupBy = "";
        if (trim($this->groupBy) != '') {
            $groupBy = $this->groupBy;
        }
        $sql = "SELECT SQL_CALC_FOUND_ROWS " . $columns . " FROM
                carrier_zones_postcode czp $join                
                $where $groupBy $orderBy";
        if (!empty($this->rowsPerPage) && $this->rowsPerPage > 0) {
            $sql .= " LIMIT " . ((!empty($this->pageOffset) && $this->pageOffset > 0) ? $this->pageOffset : 0) . "," . $this->rowsPerPage;
        }
        if ($debug) {
            echo $sql;
            die();
        }
        return CarrierZonesPostcode::getCarrierZonesPostcodeListFromSql($sql);
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
            $sql = "SELECT COUNT(czp.id) AS total FROM carrier_zones_postcode czp $join $where";
            if ($debug) {
                echo $sql;
                die();
            }
        }
        return CarrierZonesPostcode::getTotalNumberOfCarrierZonesPostcodeFromSql($sql);
    }

    public function delete($debug = false)
    {
        $join = "";
        if (!empty($this->Join)) {
            $join = implode(" ", $this->Join);
        }
        $where = $this->getWhere();
        $sql = "DELETE FROM carrier_zones_postcode  $join $where";
        if ($debug) {
            echo $sql;
            die();
        }
        return CarrierZonesPostcode::deleteCarrierZonesPostcodeFromSql($sql);
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

    public function whereBetween($col, $startValue, $endValue, $includeValues = true)
    {
        $this->filter[] = $col . " >" . ($includeValues === true ? '= ' : ' ') . "'" . DbAccess3::escape($startValue) . "' AND " . $col . " <" . ($includeValues === true ? '= ' : ' ') . "'" . DbAccess3::escape($endValue) . "'";
    }

    public function orWhereBetween($col, $startValue, $endValue, $includeValues = true)
    {
        $this->orFilter[] = $col . " >" . ($includeValues === true ? '= ' : ' ') . "'" . DbAccess3::escape($startValue) . "' AND " . $col . " <" . ($includeValues === true ? '= ' : ' ') . "'" . DbAccess3::escape($endValue) . "'";
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

    public function whereLike($where = [])
    {
        $this->where($where, "LIKE");
    }

    public function orWhereLike($where = [])
    {
        $this->orWhere($where, "LIKE");
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

    public function whereNotIn($field, $values)
    {
        $this->whereIn($field, $values, true);
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

    public function leftJoin($joinTable, $where)
    {
        $this->join($joinTable, $where, "LEFT");
    }

    public function rightJoin($joinTable, $where)
    {
        $this->join($joinTable, $where, "RIGHT");
    }

    public function innerJoin($joinTable, $where)
    {
        $this->join($joinTable, $where, "INNER");
    }

    public function orderBy($order, $ascdesc = "ASC")
    {
        $this->orderBy[] = $order . " " . $ascdesc;
    }

    public function groupBy($columnName)
    {
        if (is_array($columnName))
            $this->groupBy = " group by " . implode(", ", $columnName);
        else if (trim($columnName) != '')
            $this->groupBy = " group by " . $columnName;
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