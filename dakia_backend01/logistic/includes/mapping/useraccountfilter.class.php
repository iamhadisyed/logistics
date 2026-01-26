<?php

// get settings
//require_once("includes/settings/common.inc.php");

class UserAccountFilter {

    private $filter = "";
    private $order_by = "";
    private $limit = 100;
    private $rowsPerPage = 0;
    private $pageOffset = 0;

    /**
     * Get list of user items based on filter conditions
     *
     * @return array[UserAccount]
     */
    public function getList($debug = '') {
        // has filter been configured?
        $where = "";
        if ($this->filter != "")
            $where = "WHERE " . $this->filter;
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;
         $sql = "SELECT *
                        FROM customer_account ua
                        $where
                         $sort ";
         if($debug){
             echo $sql;
             die;
         }
        return CustomerAccount::getUserAccountListFromSql($sql);
    }

    public function getColumnList($fields,$debug=false) {
        // has filter been configured?
        $where = "";
        if ($this->filter != "")
            $where = "WHERE " . $this->filter;
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;
        $sql = "SELECT  id, " . $fields . "  FROM customer_account ua  $where  $sort ";
        if($debug){
            echo '<pre>';
            print_r($sql);
            echo '</pre>';
            die;
        }
        return CustomerAccount::getUserAccountListFromSql($sql);
    }

    public function getColumnDistinctList($fields) {
        // has filter been configured?
        $where = "";
        if ($this->filter != "")
            $where = "WHERE " . $this->filter;
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;
        $sql = "SELECT  " . $fields . " FROM customer_account ua  $where  $sort ";
		//mail("mruga@oneworldexpress.com","", $sql);
        return CustomerAccount::getUserAccountListFromSql($sql);
    }

    public function getDistinctColumnList($fields) {
        // has filter been configured?
        if ($fields == "")
            $fields = "id";
        $where = "";
        if ($this->filter != "")
            $where = "WHERE " . $this->filter;
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;
        $sql = "SELECT  " . $fields . "  FROM customer_account ua  $where  $sort ";


        return CustomerAccount::getUserAccountListFromSql($sql);
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

        $sql = "SELECT count(*) as total FROM customer_account ua $where $sort ";

        t($sql, __METHOD__);

        return CustomerAccount::getTotalNumberOfUserAccountFromSql($sql);
    }
    
    
    public function getPagingList($debug =false) {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE  " . $this->filter; //substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = " ORDER BY " . $this->order_by;


        $sql = "SELECT *, (SELECT count(id) FROM user u WHERE u.user_account_id = ua.id and u.active_flag = 1) as active_users FROM customer_account ua $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
        if($debug)
            echo $sql;
        return CustomerAccount::getUserAccountListFromSql($sql);
    }

    public function getPagingColumnList($fields) {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = " ORDER BY " . $this->order_by;


        $sql = "SELECT " . $fields . " FROM customer_account ua $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";



        t($sql, __METHOD__);

        return CustomerAccount::getUserAccountListFromSql($sql);
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
    public function getCount($debug = false) {
        $result = $this->getList($debug);
        return sizeof($result);
    }

    /**
     * Filter on given user/password combo
     *
     * @param string or array giving $service_type
     */
    public function addUserNameFilter($username) {
        if ($this->filter != "")
            $this->filter .= " AND ";
			
        $this->filter .= "ua.user_name='" . DbAccess3::escape($username) . "'";
		
    }

    public function addUserAccountFilter($accountname) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= "ua.user_account='" . DbAccess3::escape($accountname) . "'";
    }

    public function addAccountNumberFilter($account_number) {
        //echo $account_number;
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= "ua.user_account='" . DbAccess3::escape($account_number) . "'";
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
    
    public function addOrFilter($where) {
        //echo $account_number;
        if ($this->filter != "")
            $this->filter .= " OR ";
        $this->filter .= $where;
        //echo $this->filter;
        //exit;
    }

    public function addUserTypeFilter($user_type) {
        //echo $account_number;
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= "ua.user_type='" . DbAccess3::escape($user_type) . "'";
        //echo $this->filter;
        //exit;
    }

    /**
     * Filter on given user/password combo
     *
     * @param string or array giving $service_type
     */
    public function addIdFilter($userId) {
        //echo $account_number;
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= "ua.id='" . DbAccess3::escape($userId) . "'";
        //echo $this->filter;
        //exit;
    }

    /**
     * Filter on given user/password combo
     *
     * @param string or array giving $service_type
     */
    public function addParentidFilter($parentId) {
        //echo $account_number;
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= "  ua.parentid = '" . DbAccess3::escape($parentId) . "' ";
        //echo $this->filter;
        //exit;
    }

   
    /**
     * Filter on given user/password combo
     *
     * @param string or array giving $service_type
     */
    public function addAccoutnIdOrFilter($parentId) {
        //echo $account_number;
        if ($this->filter != "")
            $this->filter .= " OR ";
        $this->filter .= "  ua.id = '" . DbAccess3::escape($parentId) . "' ";
        //echo $this->filter;
        //exit;
    }

    /**
     * Filter on given user/password combo
     *
     * @param string or array giving $service_type
     */
    public function addActiveFlagFilter() {
        //echo $account_number;
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= "  active_flag = '1' ";
        //echo $this->filter;
        //exit;
    }

    /**
     * Filter on given user/password combo
     *
     * @param string or array giving $service_type
     */
    public function addIsDeletedFilter($delted) {
        //echo $account_number;
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= "  ua.is_deleted = '" . DbAccess3::escape($delted) . "' ";
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
        return CustomerAccount::getUserAccountListFromSql($sql);
    }
   
    public function getParentChildAccount($parent_id, $sub_account=array()) {
		$where = '';
		if(sizeof($sub_account) > 0)
		$where = " AND `user`.id in ('".implode("','", $sub_account)."')";
        $sql = "select  `user`.id,`user`.full_name,`user`.user_account
                  from
                    `user` 
                      WHERE MD5(`user`.parentid) = '" . DbAccess3::escape($parent_id) . "' $where GROUP BY `user`.user_account";
					  
	
        return CustomerAccount::getUserAccountListFromSql($sql);
    }
    public function getParentChildAccountCount($parent_id) {
        $sql = "select COUNT(*) AS id
                  from
                    `user` 
                      WHERE MD5(`user`.parentid) = '" . DbAccess3::escape($parent_id) . "' GROUP BY `user`.user_account";
        return CustomerAccount::getUserAccountListFromSql($sql);
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
    public function addFieldOrFilter($fieldName, $fieldValue) {
        if ($this->filter != "")
            $this->filter .= " Or ";
	$this->filter .= "  " . $fieldName . " = '" . DbAccess3::escape($fieldValue) . "'";
    }
    public static function updateAccountCode($accountNumber){
        $randomAccountCode = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTVWXYZ"), 0, 4);
        $sqlCheck = "SELECT id FROM `user_account` WHERE account_code = '" . DbAccess3::escape($randomAccountCode) . "'";
        $total = CustomerAccount::getUserAccountListFromSql($sqlCheck);
        if(!empty($total)){
         return UserAccountFilter::updateAccountCode($accountNumber);
        }else{
            $sql = "UPDATE  user_account
                        SET account_code = IF(account_code IS NULL OR account_code = '','" . DbAccess3::escape($randomAccountCode) . "', account_code)
                        WHERE   id = '" . DbAccess3::escape($accountNumber) . "' ";
                DbAccess3::runQuery($sql);
        }
    }
    public static function getImmediateChildAccount($parent_id) {
        $sql = "select  *
                  from
                    customer_account ua
                      WHERE ua.parentid = '" . DbAccess3::escape($parent_id) . "' ";
        return CustomerAccount::getUserAccountListFromSql($sql);
    }
}

// class
?>