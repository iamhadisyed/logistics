<?php

/*
 * Consignment Filter
 *
 */

class LicencePlateCountryFilter {

    private $filter = "";
    private $order_by = "";
    private $group_by = "";

    public function getList() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->group_by != "")
            $sort = "GROUP BY " . $this->group_by;
        if ($this->order_by != "")
            $sort .= "ORDER BY " . $this->order_by;

        $sql = "SELECT *
				FROM licence_plate_country l inner join country c
                                ON l.country_id = c.id
				$where
				$sort limit 5000
				";
        return LicencePlateCountry::getLicencePlateCountryListFromSql($sql);
    }

    public function addOrderByFilter($field, $desc = 'desc') {
        $this->order_by = $field . " " . $desc;
    }

    

    public function getColumnList($fields) {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->group_by != "")
            $sort = "GROUP BY " . $this->group_by;
        if ($this->order_by != "")
            $sort .= "ORDER BY " . $this->order_by;

        $sql = "SELECT l.id, " . $fields . "
				FROM licence_plate_country l inner join country c
                                ON l.country_id = c.id
				$where
				$sort
				";
        return LicencePlateCountry::getLicencePlateCountryListFromSql($sql);
    }
     public function addFilter($filterVal) {
        if ($this->filter != "")
            $this->filter .= " AND ";

        $this->filter .= "    " . $filterVal . " ";
    }

}
