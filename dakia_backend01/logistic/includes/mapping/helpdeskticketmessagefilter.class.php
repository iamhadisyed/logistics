<?php

/*
 * HelpDeskTicketMessage Filter
 *
 */

class HelpDeskTicketMessageFilter {

    private $filter = "";
    private $order_by = "";

    /**
     * Get list of HelpDeskTicketMessage items based on filter conditions
     *
     * @return array[HelpDeskTicketMessage]
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

        $sql = "SELECT * FROM helpdesk_ticket_message	$where $sort LIMIT 5000";

        t($sql, __METHOD__);
        return HelpDeskTicketMessage::getTicketListFromSql($sql);
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
				FROM helpdesk_ticket_message
				$where
				$sort
				LIMIT " . $recordLimit . "
				";
        return HelpDeskTicketMessage::getTicketListFromSql($sql);
    }

 

    /**
     * Get count of consignment items based on filter conditions
     *
     * @return int
     */
    public function getCount() {
        $result = $this->getList();
        return sizeof($result);
    }

   
    /*     * *
     * Oder by AddedDate
     */

    public function AddOrderByAddedDate($ascending = true) {
        //if ($this->order_by != "") $this->order_by .= ", ";
        //
		$this->order_by = "added_date" . ($ascending ? "" : " DESC");
    }

    public function addTicketIdFilter($date_value) {
        $this->filter .= " AND ";
        $this->filter .= " ticketid='" . DbAccess3::escape($date_value) . "'";
    }

  
}
