<?php

/*
 * Service Log Filter
 *
 */

class CustomizedServicesRoutingLogFilter {

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
                FROM customized_services_routing_log 
                $where
                $sort
                ";
//echo $sql;die;
        t($sql, __METHOD__);
        return CustomizedServicesRoutingLog::getCustomizedServicesRoutingLogListFromSql($sql);
    }
 
    public function getAuditLog($productid, $limit=15) {
       echo $sql = "SELECT prl.id, prl.userid, prl.logdate, prl.log_id,  prl.message,  u.user_name AS ipaddress FROM customized_services_routing_log AS prl ,`user` AS u WHERE prl.userid = u.id AND prl.log_id = '" . DbAccess3::escape($productid) . "' ORDER BY prl.logdate desc limit $limit";
        t($sql, __METHOD__);

        return CustomizedServicesRoutingLog::getCustomizedServicesRoutingLogListFromSql($sql);
    }
    

}
