<?php

/*
 * Consignment Log Filter
 *
 */

class ConsignmentChargesLogFilter {

    private $filter = "";
    private $order_by = "";
    private $limit = 100;
    private $rowsPerPage = 0;
    private $pageOffset = 0;

    /**
     * Get list of consignment Logs based on filter conditions
     *
     * @return array[Consignment]
     */
    public function getLogList() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . $this->filter;
        }

        $sort = "ORDER BY ccl.logdate desc";


        $sql = "SELECT ccl.*,CONCAT(u.first_name,' ',u.last_name) as userid, u.user_name
                FROM consignment_charges_log ccl 
                JOIN user u ON u.id = ccl.userid
                $where
                $sort
                ";
        t($sql, __METHOD__);
        return ConsignmentChargesLog::getConsignmentChargesLogListFromSql($sql);
    }
    
    public function addFieldFilter($colm, $value) {
        if ($this->filter != "") {
            $this->filter .= " AND ";
        }
        $this->filter .= " " . $colm . " = '" . DbAccess3::escape($value) . "'";
    }


}
