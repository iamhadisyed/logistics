<?php
/*
 * Consignment Log Filter
 *
 */
class UserLogFilter
{
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
	  public function getLogList()
    {
        // has filter been configured?
        $where = "";
        if ($this->filter != "")
        {
            $where = "WHERE " . substr($this->filter, 4);
        }
       
         $sort = "ORDER BY ul.logdate desc";


     $sql = "SELECT ul.*
                FROM user_log ul 
                $where
                $sort
                ";

        t($sql, __METHOD__);
        return ConsignmentLog::getConsignmentLogListFromSql($sql);
    }
	 
    public function getAuditLog($consignmentid,$limit=15)
    {
        $sql = "select c.id, c.message, c.previous_data, c.userid,c.logdate,c.log_id, ipaddress from user_log c, user u 
                        where c.userid = u.id and c.log_id = '".DbAccess3::escape($consignmentid)."' order by c.logdate desc limit ".$limit;
        t($sql, __METHOD__);

        return UserLog::getUserLogListFromSql($sql);
    }

	
}