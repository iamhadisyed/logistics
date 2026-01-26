<?php

/*
 * Service Log Filter
 *
 */

class TariffDetailFilter {

    private $filter = "";
    private $order_by = "";
    private $limit = 100;
    private $rowsPerPage = 0;
    private $pageOffset = 0;

    /**
     * Get list of Service Log based on filter conditions
     *
     * @return array[ServiceLog]
     */
    public function getList() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }

        $sort = "ORDER BY tariff_name desc";


       $sql = "SELECT *
                FROM tariff_details
                $where
                $sort
                ";
//echo $sql;die;
        t($sql, __METHOD__);
        return TariffDetail::getTariffDetailsListFromSql($sql);
    }
  
    public function AddOrderById($ascending = true) {
        $this->order_by = "id" . ($ascending ? "" : " DESC");
    }
    public function addFilter($code)
    {
        $this->filter .= " AND ";    
        $this->filter .= " ".$code." ";
    }

}
