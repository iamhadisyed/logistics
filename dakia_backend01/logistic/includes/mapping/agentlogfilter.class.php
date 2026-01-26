<?php

/*
 * Service Log Filter
 *
 */

class AgentLogFilter {

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
                FROM agent_log 
                $where
                $sort
                ";
//echo $sql;die;
        t($sql, __METHOD__);
         return AgentLog::getAgentLogListFromSql($sql);
    }
  
    public function getAuditLog($ServiceLogid, $limit=10) {
        $sql = "SELECT sl.id, sl.userid, sl.logdate, sl.log_id, sl.log_type, sl.message, sl.previous_data, sl.current_data, u.user_name AS ipaddress FROM agent_log AS sl ,`user` AS u WHERE sl.userid = u.id AND sl.log_id = '" . DbAccess3::escape($ServiceLogid) . "' ORDER BY sl.logdate desc limit $limit";
        t($sql, __METHOD__);
        return AgentLog::getAgentLogListFromSql($sql);
    }
    

}
