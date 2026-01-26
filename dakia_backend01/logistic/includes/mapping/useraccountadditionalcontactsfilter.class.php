<?php

/*
 * Consignment Filter
 *
 */

class UserAccountAdditionalContactsFilter {

    private $filter = "";
    private $order_by = "";
    private $group_by = "";
    private $rowsPerPage = 0;
    private $pageOffset = 0;

    public function getList($debug=false) {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . $this->filter;
        }
        $sort = "";
        if ($this->group_by != "")
            $sort = "GROUP BY " . $this->group_by;
        if ($this->order_by != "")
            $sort .= "ORDER BY " . $this->order_by;

        $sql = "SELECT * FROM user_account_additional_contacts uaac
                        $where
                        $sort
                        ";		
        if($debug)
            echo $sql;
        return UserAccountAdditionalContacts::getUserAccountAdditionalContactsListFromSql($sql);
    }

    public function getColumnList($columns = "*",$debug=false) {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . $this->filter;
        }
        $sort = "";
        if ($this->group_by != "")
            $sort = "GROUP BY " . $this->group_by;
        if ($this->order_by != "")
            $sort .= "ORDER BY " . $this->order_by;

        $sql = "SELECT ".$columns."
                        FROM user_account_additional_contacts uaac
                        $where
                        $sort
                        ";
        if($debug)
            echo $sql;
        return UserAccountAdditionalContacts::getUserAccountAdditionalContactsListFromSql($sql);
    }


    public function addFieldFilter($fieldName, $fieldValue) {
        if(trim($this->filter)!= '')
            $this->filter .= " AND " ;
         $this->filter .= " " . $fieldName . " = '" . DbAccess3::escape($fieldValue) . "'";
    }

    public function addFilter($fieldName) {
        if(trim($this->filter)!= '')
            $this->filter .= " AND " ;
        $this->filter .= " " . $fieldName ;
    }

    public function addFieldNotFilter($fieldName, $fieldValue) {
        if(trim($this->filter)!= '')
            $this->filter .= " AND " ;
        $this->filter .= " " . $fieldName . " != '" . DbAccess3::escape($fieldValue) . "'";
    }

    public function addFieldLikeFilter($fieldName, $fieldValue) {
        if(trim($this->filter)!= '')
            $this->filter .= " AND " ;
        $this->filter .= " " . $fieldName . " LIKE '" . DbAccess3::escape($fieldValue) . "%'";
    }


    public function AddOrderBy($fieldName,  $ascending = true) {
        if ($this->order_by != "")
            $this->order_by .= ", ";
        $this->order_by .=  $fieldName." " . ($ascending ? "" : " DESC");
    }


    public function getCount() {
        $result = $this->getList();
        return sizeof($result);
    }
    public function getPagingCount($debug=false) {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . $this->filter;
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        $sql = "SELECT 
                  count(*) as total 
                FROM 
                  (     user_account_additional_contacts uaac
                    INNER JOIN 
                        customer_account ua ON ua.id = uaac.user_account_id)
                  INNER JOIN user u ON u.id = uaac.added_by
                $where $sort ";
        if($debug)
            echo $sql;
        return UserAccountAdditionalContacts::getTotalNumberOfUserAccountAdditionalContactsFromSql($sql);
    }

    public function getPagingList($fields,$debug=false) {


        if ($this->filter != "") {
            $where = "WHERE " . $this->filter;
        }
        $groupBy = $this->groupBy;
        if($this->order_by != '')
            $sort = " order by " . $this->order_by;

        $sql = "SELECT " . $fields . " , uaac.id FROM 
                  ( 
                    user_account_additional_contacts uaac 
                  INNER JOIN 
                    customer_account ua ON ua.id = uaac.user_account_id)
                  INNER JOIN 
                    user u ON u.id = uaac.added_by
                $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";

        if($debug)
            echo $sql;

        return UserAccountAdditionalContacts::getUserAccountAdditionalContactsListFromSql($sql);
    }

    public function setRowsPerPage($rowsPP) {
        $this->rowsPerPage = $rowsPP;
    }

    public function setOffset($offset) {
        $this->pageOffset = $offset;
    }




}
