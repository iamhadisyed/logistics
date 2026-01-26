<?php

/*
 * Service Log Filter
 *
 */

class ParcelLogFilter {

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
    public function getLogList() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }

        $sort = "ORDER BY logdate desc";


        $sql = "SELECT *
                FROM parcel_log 
                $where
                $sort
                ";
//echo $sql;die;
        t($sql, __METHOD__);
         return ParcelLog::getParcelLogListFromSql($sql);
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

    public function getAuditLog($ServiceLogid, $limit=10) {
        $sql = "SELECT sl.id, sl.userid, sl.logdate, sl.log_id, sl.log_type, sl.message, sl.previous_data, sl.current_data, u.user_name AS ipaddress FROM parcel_log AS sl ,`user` AS u WHERE sl.userid = u.id AND sl.log_id = '" . DbAccess3::escape($ServiceLogid) . "' ORDER BY sl.logdate desc limit $limit";
        t($sql, __METHOD__);
        return ParcelLog::getParcelLogListFromSql($sql);
    }
    

}
