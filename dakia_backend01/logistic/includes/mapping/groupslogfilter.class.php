<?php

/*
 * Groups Log Filter
 *
 */

class GroupsLogFilter {

    private $filter = "";
    private $order_by = "";
    private $limit = 100;
    private $rowsPerPage = 0;
    private $pageOffset = 0;

    /**
     * Get list of Groups Log based on filter conditions
     *
     * @return array[Groups]
     */
    public function getLogList() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }

        $sort = "ORDER BY logdate desc";


        $sql = "SELECT *
                FROM groups_log 
                $where
                $sort
                ";
//echo $sql;die;
        t($sql, __METHOD__);
        return GroupsLog::getGroupsLogListFromSql($sql);
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

    public function getAuditLog($Groupsid,$limit = 15) {
        $sql = "SELECT gl.id, gl.userid, gl.logdate, gl.log_id, gl.log_type, gl.message, gl.previous_data, gl.current_data, u.user_name AS ipaddress FROM groups_log AS gl ,`user` AS u WHERE gl.userid = u.id AND gl.log_id = '" . DbAccess3::escape($Groupsid) . "' ORDER BY gl.logdate DESC limit ".$limit;
        t($sql, __METHOD__);

        return GroupsLog::getGroupsLogListFromSql($sql);
    }
    

}
