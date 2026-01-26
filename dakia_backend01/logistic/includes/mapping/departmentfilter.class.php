<?php

/*
 * Department Filter
 *
 */

class DepartmentFilter {

    private $filter = "";
    private $order_by = "";

    /**
     * Get list of consignment items based on filter conditions
     *
     * @return array[Department]
     */
    public function getList() {
        // has filter been configured?
        $where = "";
        if (trim($this->filter) != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        $sql = "SELECT * FROM department $where $sort LIMIT 5000";
        t($sql, __METHOD__);
        return Department::getDepartmentListFromSql($sql);        
    }
    
    public function setOffset($offset)
    {
            $this->pageOffset = $offset;
    }

    public function setRowsPerPage($rowsPP)
    {
            $this->rowsPerPage = $rowsPP;
    }
        
    public function getPagingCount()
        {
                // has filter been configured?
                $where = "";
                if ($this->filter != "")
                {
                        $where = "WHERE " . substr($this->filter, 4);
                }
                $sort = "";
                if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;
                  $sql = "SELECT count(a.id) as total FROM department a inner join user u on a.department_head = u.id $where $sort";

                t($sql, __METHOD__);

                return Department::getTotalNumberOfDepartmentFromSql($sql);

        }
        
    public function getPagingList($columns = '*', $debug = false)
        {
            // has filter been configured?
            $where = "";
            if ($this->filter != "")
            {
                    $where = "WHERE " . substr($this->filter, 4);
            }
            $sort = "";
            if ($this->order_by != "") 
                $sort = "ORDER BY " . $this->order_by;
            else
                $sort = "ORDER BY a.id desc" ;
            $sql = "SELECT ".$columns." FROM department a inner join user u on a.department_head = u.id $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
            if($debug)
            {
                echo $sql;
                die;
            }
            return Department::getDepartmentListFromSql($sql);
        }

        public function addFieldFilter($colm, $value) {
            $this->filter .= " AND ";
            $this->filter .= " " . $colm . " = '" . DbAccess3::escape($value) . "'";
        }
        
        public function addFieldLikeFilter($colm, $value) {
            $this->filter .= " AND ";
            $this->filter .= " " . $colm . " LIKE '" . DbAccess3::escape($value) . "%'";
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
				FROM department
				$where
				$sort
				LIMIT " . $recordLimit . "
				";
        return Department::getDepartmentListFromSql($sql);
    }

   public function getDeparmentHead($department_id) {
       $sql = "SELECT 
  user.full_name AS title,
  user.email AS description
FROM
  `user` 
  JOIN `user_department` 
    ON `user`.id = `user_department`.user_id 
WHERE `user_department`.department_id = '".$department_id."'";
        t($sql, __METHOD__);

        return Department::getDepartmentListFromSql($sql);
   } 
   public function getTicketGenerator($ticket_id) {
      $sql = "SELECT 
  user.full_name AS title,
  user.email AS description
FROM
  `user` 
  JOIN `helpdesk_ticket` 
    ON `user`.id = `helpdesk_ticket`.addedby 
WHERE `helpdesk_ticket`.id ='".$ticket_id."'";
        t($sql, __METHOD__);

        return Department::getDepartmentListFromSql($sql);
   } 

    /**
     * Get count of Department items based on filter conditions
     *
     * @return int
     */
    public function getCount() {
        $result = $this->getList();
        return sizeof($result);
    }


    public function addIsActiveFilter() {
        $this->filter .= " AND isactive = '1'";
    }

    public function addIsNotDeletedFilter() {
        $this->filter .= " AND isdeleted = '0'";
    }
        
    public function addFilter($name) {
        if (trim($this->filter) != '')
            $this->filter .= " AND $name ";
        else
            $this->filter .= "    $name ";
    }

    public function AddOrderByTitle ($ascending = true)
    {
        if ($this->order_by != "") $this->order_by .= ", ";
        //
        $this->order_by .= "a.title" . ($ascending ? "" : " DESC");
    }
        
}
