<?php

// get settings
//require_once("includes/settings/common.inc.php");

class UserFilter {

    private $filter = "";
    private $order_by = "";
    private $limit = 100;
    private $rowsPerPage = 0;
    private $pageOffset = 0;
    private $join = "";

    /**
     * Get list of user items based on filter conditions
     *
     * @return array[User]
     */
    public function getList($debug = false) {
        // has filter been configured?
        $where = "";
        if ($this->filter != "")
            $where = "WHERE " . $this->filter;
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;
        
        $checkJoin = "";
        if ($this->join != "")
            $checkJoin = $this->join;
        
         $sql = "SELECT *
                        FROM user u
                        $checkJoin
                        $where
                         $sort ";

         if($debug)
             echo $sql ;
        return User::getUserListFromSql($sql);
    }

    public function getColumnList($fields, $debug = false) {
        // has filter been configured?
        $where = "";
        if ($this->filter != "")
            $where = "WHERE " . $this->filter;
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;
        
        $checkJoin = "";
        if ($this->join != "")
            $checkJoin = $this->join;
        
        $sql = "SELECT  u.id, " . $fields . "  FROM user u $checkJoin $where  $sort ";
        if($debug)
        {
            echo $sql;
            die;
        }
        return User::getUserListFromSql($sql);
    }

    public function getColumnDistinctList($fields) {
        // has filter been configured?
        $where = "";
        if ($this->filter != "")
            $where = "WHERE " . $this->filter;
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;
        
        $checkJoin = "";
        if ($this->join != "")
            $checkJoin = $this->join;
        
        $sql = "SELECT  " . $fields . " FROM user u $checkJoin $where  $sort ";
		//mail("mruga@oneworldexpress.com","", $sql);
        return User::getUserListFromSql($sql);
    }

   
    public function getPagingCount() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . $this->filter; //substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;
        
        $checkJoin = "";
        if ($this->join != "")
            $checkJoin = $this->join;
        
        $sql = "SELECT count(*) as total FROM user u $checkJoin $where $sort ";

        t($sql, __METHOD__);

        return User::getTotalNumberOfUsersFromSql($sql);
    }

    public function getPagingList() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE  " . $this->filter; //substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = " ORDER BY " . $this->order_by;
        $checkJoin = "";
        if ($this->join != "")
            $checkJoin = $this->join;

        $sql = "SELECT u.* FROM user u $checkJoin $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
        return User::getUserListFromSql($sql);
    }

    public function getPagingColumnList($fields) {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . $this->filter;
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = " ORDER BY " . $this->order_by;
        $checkJoin = "";
        if ($this->join != "")
            $checkJoin = $this->join;

        $sql = "SELECT " . $fields . " FROM user u $checkJoin $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
        t($sql, __METHOD__);

        return User::getUserListFromSql($sql);
    }

    public function setOffset($offset) {
        $this->pageOffset = $offset;
    }

    public function setRowsPerPage($rowsPP) {
        $this->rowsPerPage = $rowsPP;
    }

    /**
     * Get count of user items based on filter conditions
     *
     * @return int
     */
    public function getCount() {
        $result = $this->getList();
        return sizeof($result);
    }

    /**
     * Filter on given user/password combo
     *
     * @param string or array giving $service_type
     */
    public function addUserPassFilter($username, $password) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= "u.user_name='" . DbAccess3::escape($username) . "' AND u.user_pass='" . DbAccess3::escape($password) . "'";
    }

    /**
     * Filter on given user/password combo
     *
     * @param string or array giving $service_type
     */
    public function addUserNameFilter($username) {
        if ($this->filter != "")
            $this->filter .= " AND ";
			
        $this->filter .= "u.user_name='" . DbAccess3::escape($username) . "'";
		
    }

    public function addUserNameLikeFilter($username) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= "u.user_name Like '%" . DbAccess3::escape($username) . "%'";
    }

    /**
     * Filter on given user/password combo
     *
     * @param string or array giving $service_type
     */
    public function addUserAccountFilter($accountname) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= "u.user_account='" . DbAccess3::escape($accountname) . "'";
    }

    public function addUserAccountInFilter($accountnamearray) {
        $acc_array = "'" . implode("','", $accountnamearray) . "'";
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= "u.id IN (" . $acc_array . ")";
    }

    public function addUserAccountLikeFilter($accountname) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= "u.user_account LIKE '%" . DbAccess3::escape($accountname) . "%'";
    }

    /**
     * Filter on given user/password combo
     *
     * @param string or array giving $service_type
     */
    public function addAccountNumberFilter($account_number) {
        //echo $account_number;
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= "u.user_account='" . DbAccess3::escape($account_number) . "'";
        //echo $this->filter;
        //exit;
    }
    
    public function addAccountIdFilter($account_id) {
        //echo $account_number;
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= "u.user_account_id='" . DbAccess3::escape($account_id) . "'";
        //echo $this->filter;
        //exit;
    }

    
    /**
     * Filter on given user/password combo
     *
     * @param string or array giving $service_type
     */
    public function addFilter($account_number) {
        //echo $account_number;
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= $account_number;
        //echo $this->filter;
        //exit;
    }

    public function addFullNameFilter($fullname) {
        //echo $account_number;
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= "u.full_name='" . DbAccess3::escape($fullname) . "'";
        //echo $this->filter;
        //exit;
    }

    public function addUserTypeFilter($user_type) {
        //echo $account_number;
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= "u.user_type='" . DbAccess3::escape($user_type) . "'";
        //echo $this->filter;
        //exit;
    }

  
    public function addIdFilter($userId) {
        //echo $account_number;
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= "u.id='" . DbAccess3::escape($userId) . "'";
        //echo $this->filter;
        //exit;
    }

    
   public function getParentChild() {
       $sql = "select 
                    id,full_name,user_account,user_type
                  from
                    `user`
                      WHERE user_type='corporateclient' and active_flag = 1
                  GROUP BY user_account ";
        return User::getUserListFromSql($sql);
    }
   
    public function addFieldLikeFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";

	$this->filter .= " " . $colm . " LIKE '" . DbAccess3::escape($value) . "%'";
    }
    
    public function AddOrderBy($fieldName,  $ascending = true) {
        if ($this->order_by != "")
            $this->order_by .= ", ";
        
	$this->order_by = " ".$fieldName. ($ascending ? "" : " DESC");
    }
    public function addFieldFilter($fieldName, $fieldValue) {
        if ($this->filter != "")
            $this->filter .= " AND ";
	$this->filter .= "  " . $fieldName . " = '" . DbAccess3::escape($fieldValue) . "'";
    }
    public function addUserAccountJoin() {
        $this->join.= " JOIN customer_account ua  ON u.user_account_id = ua.id ";
    }
    public function getUserIdsFromAccountIds($ids = '') {
        $sql = "SELECT id
                FROM user
                WHERE user_account_id IN (" . $ids . ")";
        $result =  User::getUserListFromSql($sql);
        $array = array();
        if(count($result) > 0) {
            foreach ($result as  $obj) {
                $array[] = $obj->getId();
            }
        }
        return $array;
    }
    public function addFilterIn($field, $values) {
        if (is_array($values)) { 
            if ($this->filter != "") {
                $this->filter .= " AND ";
            }
            $this->filter .= $field ." IN (" . implode(",", $values) . ")";
        } else {
            if ($this->filter != "") {
                $this->filter .= " AND ";
            }
            $this->filter .= $field . " IN ('" . $values . "')";
        }
    }
    public function addCountryJoin() {
        $this->join.= " LEFT JOIN `country` c  ON u.country_id = c.id ";
    }
    public function checkFeildAlreayExsist($columnName,$feildValue,$curUserId = 0) {
        $where = '';
        $where = $columnName." = '" . DbAccess3::escape($feildValue) . "' ";
        if($curUserId > 0)
            $where .= " AND id != '" . DbAccess3::escape($curUserId) . "' ";
        
        $sql = "SELECT	id FROM `user` WHERE ".$where;
        t($sql, __METHOD__);
        return User::getUserListFromSql($sql);
    }
    public function getTcUser($userId,$debug=false) {
        $now = time();
        $date = '2018/05/27';
        $where = "";
        if (strtotime($date) >= $now) {
            $where .= "  u.is_tc_agreed != 'y' ";
        }else{
            $where .= " u.tc_agreed_date + INTERVAL 90 DAY <= NOW() ";
        }
        $sql = "SELECT   u.is_tc_agreed, u.tc_agreed_date , u.id FROM user u WHERE (u.tc_agreed_date IS NULL OR ".$where." ) AND id = '".DbAccess3::escape($userId)."'";
		if($debug)
		{
			echo $sql;
			die;
			
		}
        return User::getUserListFromSql($sql);
    }
    public function getTotalScannedUserParcel($userAccount,$date,$op,$debug=false){
        $sql = "SELECT 
                    u.`user_name` AS user_name,COUNT(u.id) AS id
                  FROM
                    `user` u 
                    JOIN tracking_data td
                    ON td.`user_id` = u.`id` AND DATE(td.date_created) ".$op." '".$date."' 
                  WHERE u.`user_account_id` = '".DbAccess3::escape($userAccount)."' AND td.`status_code_id` = '146' GROUP BY u.id";
        if($debug){
            echo $sql;die;
        }
        return User::getUserListFromSql($sql);
        
    }
    public function addApiKeysJoin() {
        $this->join.= " INNER JOIN `oauth_access_tokens` at  ON u.id = at.user_id ";
    }

    public function updateUserApiKey($api_access_token, $expiry_date) {
        $sql = "UPDATE oauth_access_tokens
                SET expires='$expiry_date'
                WHERE access_token='$api_access_token'";
        DbAccess3::runQuery($sql);
    }

    public function getListLimit($limit = 0, $debug = false) {
        // has filter been configured?
        $where = "";
        if ($this->filter != "")
            $where = "WHERE " . $this->filter;
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;
        $limitString = '';
        if (!empty($limit)) {
            $limitString = "LIMIt " . $limit;
        }
        $checkJoin = "";
        if ($this->join != "")
            $checkJoin = $this->join;

        $sql = "SELECT *
                        FROM user u
                        $checkJoin
                        $where
                         $sort $limitString ";

        if($debug)
            echo $sql ;
        return User::getUserListFromSql($sql);
    }
}

// class
?>