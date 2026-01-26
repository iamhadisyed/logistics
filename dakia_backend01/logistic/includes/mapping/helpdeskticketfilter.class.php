<?php

/*
 * HelpDeskTicket Filter
 *
 */

class HelpDeskTicketFilter {

    private $filter = "";
    private $order_by = "";
    private $rowsPerPage = 25;
    private $pageOffset = 0;

    /**
     * Get list of consignment items based on filter conditions
     *
     * @return array[HelpDeskTicket]
     */
    public function getList() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        $sql = "SELECT * FROM helpdesk_ticket $where $sort ";

        t($sql, __METHOD__);
        return HelpDeskTicket::getTicketListFromSql($sql);
    }

    public function getPagingList() {
         // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;


        $sql = "SELECT * FROM helpdesk_ticket $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";



        t($sql, __METHOD__);

        return HelpDeskTicket::getTicketListFromSql($sql);
    }

    public function getColumnList($fields, $recordLimit = 5000) {

        $fields = rtrim($fields, ",");
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        if ($recordLimit == '')
            $recordLimit = 5000;

        $sql = "SELECT " . $fields . ", id 
				FROM helpdesk_ticket
				$where
				$sort
				LIMIT " . $recordLimit . "
				";
        return HelpDeskTicket::getTicketListFromSql($sql);
    }

    
    public function getUnReadMessageForAdmin($department_str) {
        $sql = "SELECT 
                    t.*,
                    u.`full_name` as addedby,
                    (SELECT 
                      tm.added_date 
                    FROM
                      helpdesk_ticket_message tm 
                    WHERE tm.`ticketid` = t.id 
                    ORDER BY id DESC 
                    LIMIT 1) AS ticket_message_added_by 
                FROM
                    `helpdesk_ticket` t JOIN `user` u ON u.`id` = t.`addedby`
                WHERE t.`department_id` IN (" . $department_str . ") 
                    AND t.`addedby` = 
                        (SELECT 
                          tm.addedby 
                        FROM
                          helpdesk_ticket_message tm 
                        WHERE tm.`ticketid` = t.id 
                        ORDER BY id DESC 
                        LIMIT 1) 
                ORDER BY ticket_message_added_by DESC";
        t($sql, __METHOD__);
        return HelpDeskTicket::getTicketListFromSql($sql);
    }

    public function getUnReadMessageForClient($user_id) {
       $sql = "SELECT 
                    t.*,
                    (SELECT 
                      tm.added_date 
                    FROM
                      helpdesk_ticket_message tm 
                    WHERE tm.`ticketid` = t.id 
                    ORDER BY id DESC 
                    LIMIT 1) AS ticket_message_added_by 
                FROM
                    `helpdesk_ticket` t
                WHERE t.`addedby` != 
                        (SELECT 
                          tm.addedby 
                        FROM
                          helpdesk_ticket_message tm 
                        WHERE tm.`ticketid` = t.id 
                        ORDER BY id DESC 
                        LIMIT 1) 
               AND t.addedby = '".$user_id."' ORDER BY ticket_message_added_by DESC";
        t($sql, __METHOD__);
        return HelpDeskTicket::getTicketListFromSql($sql);
    }

    /**
     * Get count of HelpDesk items based on filter conditions
     *
     * @return int
     */
    public function getCount() {
        $result = $this->getList();
        return sizeof($result);
    }

    public function setOffset($offset) {
        $this->pageOffset = $offset;
    }

    public function setRowsPerPage($rowsPP) {
        $this->rowsPerPage = $rowsPP;
    }

  

    /*     * *
     * Oder by AddedDate
     */

    public function AddOrderByAddedDate($ascending = true) {
        //if ($this->order_by != "") $this->order_by .= ", ";
        //
		$this->order_by = " added_date" . ($ascending ? "" : " DESC");
    }

   

    public function addTicketIdFilter($date_value) {
        $this->filter .= " AND ";
        $this->filter .= " id = '" . DbAccess3::escape($date_value) . "'";
    }

    public function addTicketIdMd5Filter($date_value) {
        $this->filter .= " AND ";
        $this->filter .= " MD5(id) = '" . DbAccess3::escape($date_value) . "'";
    }

    public function addAddedByFilter($date_value) {
        $this->filter .= " AND addedby = '" . DbAccess3::escape($date_value) . "'";
    }

    public function addDepartmentInByFilter($date_value) {
        $this->filter .= " AND department_id IN (" . $date_value . ")";
    }

   

}
