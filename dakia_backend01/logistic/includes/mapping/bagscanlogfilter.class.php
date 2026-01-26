<?php

/*
 * Permissions Log Filter
 *
 */

class BagScanLogFilter {

    private $filter = "";
    private $order_by = "";
    private $limit = 100;
    private $rowsPerPage = 0;
    private $pageOffset = 0;

    /**
     * Get list of Permissions Log based on filter conditions
     *
     * @return array[Permissions]
     */
    public function getLogList() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }

        $sort = "ORDER BY logdate desc";


        $sql = "SELECT *
                FROM bag_scan_log 
                $where
                $sort
                ";
//echo $sql;die;
        t($sql, __METHOD__);
        return BagScanLog::getBagScanLogListFromSql($sql);
    }
    public function addIdByFilter($date_value) {
        $this->filter .= " AND id = '" . DbAccess3::escape($date_value) . "'";
    }
    public function addLogIdFilter($date_value) {
        $this->filter .= " AND log_id = '" . DbAccess3::escape($date_value) . "'";
    }
    public function AddOrderById($ascending = true) {
        $this->order_by = "id" . ($ascending ? "" : " DESC");
    }
    public function AddOrderByLogDate($ascending = false) {
        if ($this->order_by != "")
            $this->order_by .= ", ";
        //
        $this->order_by .= "logdate" . ($ascending ? "" : " DESC");
    }

    public function getAuditLog($recordId,$limit = 15) {
        $sql = "SELECT pl.id, pl.userid, pl.logdate, pl.log_id, pl.log_type, pl.message, pl.previous_data, pl.current_data, u.user_name AS ipaddress FROM bag_scan_log AS pl ,`user` AS u WHERE pl.userid = u.id AND pl.log_id = '" . DbAccess3::escape($recordId) . "' ORDER BY pl.logdate DESC limit ".$limit;
        t($sql, __METHOD__);

        return BagScanLog::getBagScanLogListFromSql($sql);
    }
    

}
