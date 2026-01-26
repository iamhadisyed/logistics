<?php

class UserMarketPlacesSubscribeFilter {


    private $filter = "";
    private $order_by = "";
    private $limit = 100;
    private $rowsPerPage = 0;
    private $pageOffset = 0;
    private $join = "";

    
    /**
     * Get list of Sample items based on filter conditions
     *
     * @return array[Sample]
     */
    public function getList() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        
        if ($this->join != "") {
            $marketPlaceJoin = $this->join;
        }
        
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

      $sql = "SELECT * FROM user_market_places_subscribe umps $marketPlaceJoin $where $sort LIMIT 5000";
      return UserMarketPlacesSubscribe::getUserMarketPlacesSubscribeListFromSql($sql);
    }
   /**
     * Get count of Sample items based on filter conditions
     *
     * @return int
     */
    public function getCount() {
        $result = $this->getList();
        return sizeof($result);
    }

    /*     * *
     * Oder by  id
     */
    
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
        
        $sql = "SELECT  umps.ids, " . $fields . "  FROM user_market_places_subscribe umps $checkJoin $where  $sort ";
        if($debug)
        {
            echo $sql;
            die;
        }
        return UserMarketPlacesSubscribe::getUserListFromSql($sql);
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
        
        $sql = "SELECT  " . $fields . " FROM user_market_places_subscribe umps $checkJoin $where  $sort ";
		//mail("mruga@oneworldexpress.com","", $sql);
        return UserMarketPlacesSubscribe::getUserListFromSql($sql);
    }

    public function addFieldLikeFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";

	$this->filter .= " " . $colm . " LIKE '" . DbAccess3::escape($value) . "%'";
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
        
        $sql = "SELECT count(*) as total FROM user_market_places_subscribe umps $checkJoin $where $sort ";
        
        t($sql, __METHOD__);

        return UserMarketPlacesSubscribe::getTotalNumberOfUserMarketPlacesSubscribeFromSql($sql);
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

        $sql = "SELECT umps.* , market_places.title, user_account.user_account FROM user_market_places_subscribe umps $checkJoin $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
        return UserMarketPlacesSubscribe::getUserMarketPlacesSubscribeListFromSql($sql);
    }

    public function addJoin($table,$where, $type = "INNER") {
        $this->join .= $type . " JOIN ".$table."  ON " . $where;
    }

    public function AddOrderBy($fieldName,  $ascending = true) {
        if ($this->order_by != "")
            $this->order_by .= ", ";
        $this->order_by = " ".$fieldName. ($ascending ? "" : " DESC");
    }


    public function addUserIdFilter($data_value) {
        if(!empty($this->filter))
            $this->filter .= " AND ";

        $this->filter .= " umps.user_account_id = '" . DbAccess3::escape($data_value) . "'";
    }

        public function addIsDeleteFilter($data_value) {
        if(!empty($this->filter))
            $this->filter .= " AND ";

        $this->filter .= " umps.is_delete = '" . DbAccess3::escape($data_value) . "'";
    }

    
    
    public function addMarketPlacesSubscribeIdFilter($data_value) {
        if(!empty($this->filter))
            $this->filter .= " AND ";

        $this->filter .= " umps.market_places_id = '" . DbAccess3::escape($data_value) . "'";
    }

    public function addParentIdFilter($data_value) {
        if(!empty($this->filter))
            $this->filter .= " AND ";
        
        $this->filter .= " umps.parent_id = '" . DbAccess3::escape($data_value) . "'";
    }

       
	  public function addFieldFilter($colm, $value)
	{
		$this->filter .= " AND ";
		$this->filter .= " ".$colm." = '" . DbAccess3::escape($value) . "'";
	}	
	   public function addFilter($account_number) {
        //echo $account_number;
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= $account_number;
        //echo $this->filter;
        //exit;
    }
    
    public static function updateUserMarketPlacesSubscribeListid($status, $id)
    {
        $sql = "update user_market_places_subscribe set status = '" . DbAccess3::escape($status) . "' where ids = '" . DbAccess3::escape($id) . "'";
        if (!empty($id) && $id >= 0) {
            return DbAccess3::runQuery($sql);
        }
    }
    
    public static function deleteUserMarketPlacesSubscribeListid($id)
    {
        $sql = "DELETE FROM user_market_places_subscribe WHERE ids = '" . DbAccess3::escape($id) . "'";
        if (!empty($id) && $id > 0) {
            return DbAccess3::runQuery($sql);
        }
    }
    
    public static function deleteUserMarketPlacesSubscribeList($user_id)
    {
        $sql = "DELETE FROM user_market_places_subscribe WHERE user_account_id = '" . DbAccess3::escape($user_id) . "'";
        if (!empty($user_id) && $user_id > 0) {
            return DbAccess3::runQuery($sql);
        }
    }

    public static function deleteSpecificUserMarketPlacesSubscribeList($user_id, $platfor_id)
    {
        $sql = "DELETE FROM user_market_places_subscribe WHERE user_account_id = '" . DbAccess3::escape($user_id) . "' AND market_places_id = '" . DbAccess3::escape($platfor_id) . "'";
        if (!empty($user_id) && $user_id > 0) {
            return DbAccess3::runQuery($sql);
        }
    }

    /**
     * Get count of user_market_places_mapping objects, using sql given
     *
     * @param string $sql
     */
    public static function getUserMarketPlacesSubscribeCountFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public function setRowsPerPage($rowsPP) {
        $this->rowsPerPage = $rowsPP;
    }
    
    public function setOffset($offset) {
        $this->pageOffset = $offset;
    }
    
    public function update_subscriber($status, $market_place_id, $market_place_mapping_id)
    {
        if($status == 1){
            $status_update = 0;
            $is_reject = 1;
        }else {
            $status_update = 1;
            $is_reject = 0;
        }
        $SQL = "Update user_market_places_subscribe set status = '$status_update',
                pending_req = '0', is_reject = '$is_reject' WHERE market_places_id = '$market_place_id' AND  market_places_mapping_id =                   '$market_place_mapping_id' and id in 
                (SELECT id FROM user_market_places_subscribe where market_places_mapping_id = '$market_place_mapping_id' ORDER BY id ASC) 
                LIMIT 1";
        DbAccess3::runQueryNew($SQL);

        //Find if the request is forwaded by a sub-parent by counting number of rows base upon market_place_ids and mapping_ids
        $SQL = "SELECT COUNT(id) as nos from user_market_places_subscribe WHERE
                market_places_id = '$market_place_id' AND  market_places_mapping_id = '$market_place_mapping_id'";
        $result = DbAccess3::runQuery($SQL);
        $row = mysqli_fetch_assoc($result);
        $rows = $row["nos"];
        if($rows > 1){
        $SQL = "SELECT id FROM user_market_places_subscribe where market_places_mapping_id = '$market_place_mapping_id' ORDER BY id ASC                    limit 1";
        $result = DbAccess3::runQuery($SQL);
        $row = mysqli_fetch_assoc($result);
        $rowid = $row["id"];
        $SQL = "Update user_market_places_subscribe set status = '0',
                pending_req = '0', is_reject = '0', is_delete  = '1' WHERE
                market_places_id = '$market_place_id' AND  market_places_mapping_id = '$market_place_mapping_id' AND id != '$rowid'";
        DbAccess3::runQueryNew($SQL);
        }

        $SQL = "Update user_market_places_mapping set active = '$status_update' where id = '$market_place_mapping_id'";
        DbAccess3::runQueryNew($SQL);
    }
    
    public function is_detele($market_places_id, $market_place_mapping_id)
    {
        $SQL = "Update user_market_places_subscribe set is_delete = '1', pending_req = '0',
                status='0', is_reject = '0' where market_places_id = '$market_places_id' and
                market_places_mapping_id='$market_place_mapping_id'";
         DbAccess3::runQueryNew($SQL);
        $SQL = "Update user_market_places_mapping set active = '0' where market_places_id = '$market_places_id' 
                and id ='$market_place_mapping_id'";
        DbAccess3::runQueryNew($SQL);
    }

    public function is_reject($market_places_id, $market_place_mapping_id)
    {
        $SQL = "Update user_market_places_subscribe set is_reject = '3',  pending_req = '0',
                status='3', is_reject = '0' where market_places_id = '$market_places_id' and
                market_places_mapping_id='$market_place_mapping_id'";
        DbAccess3::runQueryNew($SQL);
        $SQL = "Update user_market_places_mapping set active = '0' where market_places_id = '$market_places_id' 
                and id ='$market_place_mapping_id'";
        DbAccess3::runQueryNew($SQL);
    }

    public function pendingRequests($parentid)
    {
        $sql = "SELECT COUNT(pending_req) as pending_req FROM user_market_places_subscribe WHERE pending_req = '2' AND parent_id =          '$parentid'";
        $result = DbAccess3::runQuery($sql);
        $row = mysqli_fetch_assoc($result);
        return $row['pending_req'];
    }

    public function set_ActiveDate($ActiveDatePicker_Id, $ActiveDate)
    {
        $sql = "UPDATE user_market_places_subscribe SET active_date = '$ActiveDate' WHERE id =
               '$ActiveDatePicker_Id'";
        DbAccess3::runQuery($sql);
    }

    public function set_ExpiryDate($ExpiryDatePicker_Id, $ExpiryDate)
    {
        $sql = "UPDATE user_market_places_subscribe SET expiry_date = '$ExpiryDate' WHERE id =
               '$ExpiryDatePicker_Id'";
        DbAccess3::runQuery($sql);
    }

    public function check_authrozied($uaid, $mpid)
    {
        $sql = "Select count(id) as id from user_market_places_mapping where user_account_id  = '$uaid' and market_places_id = '$mpid' and                   active = 1";
        $result = DbAccess3::runQuery($sql);
        $row = mysqli_fetch_assoc($result);
        return $row['id'];
    }

    public function request_forward($id, $row_parent_id, $user_account_id, $mkt_place_id, $mkt_place_mapping_id){
        $sql = "SELECT parentid FROM user_account WHERE id = $row_parent_id";
        $result = DbAccess3::runQuery($sql);
        $row = mysqli_fetch_assoc($result);
        $parentid = $row['parentid'];
        $sql = "SELECT COUNT(id) as row_exist FROM user_market_places_subscribe WHERE user_account_id = '$row_parent_id' and                             market_places_id = '$mkt_place_id' and market_places_mapping_id = '$mkt_place_mapping_id' and parent_id = '$parentid'";
        $result = DbAccess3::runQuery($sql);
        $row = mysqli_fetch_assoc($result);
        $row_exists = $row['row_exist'];

        if($row_exists == 0 || $row_exists == NULL || $row_exists == ""){
        $sql = "INSERT INTO user_market_places_subscribe(market_places_mapping_id, market_places_id, user_account_id, parent_id, status,                   pending_req)
				SELECT market_places_mapping_id, market_places_id, parent_id, '$parentid', '2', '2'  FROM user_market_places_subscribe
				WHERE id = $id";
        DbAccess3::runQuery($sql);
        }else{
            $sql = "UPDATE user_market_places_subscribe SET status = '2', pending_req = '2', is_delete = '0' WHERE user_account_id =                          '$row_parent_id' and market_places_id = '$mkt_place_id' and market_places_mapping_id = '$mkt_place_mapping_id' and                      parent_id = '$parentid'";
            DbAccess3::runQuery($sql);
        }
        $update = "Update user_market_places_subscribe SET status = '4', pending_req = '0' WHERE id = $id";
        DbAccess3::runQuery($update);
    }

    public function request_forwad_subscribers($market_places_mapping_id){
        $sql = "select count(market_places_mapping_id) as no_of_requests  from user_market_places_subscribe
                where market_places_mapping_id = '$market_places_mapping_id'";
        $result = DbAccess3::runQuery($sql);
        $row = mysqli_fetch_assoc($result);
        return $row['no_of_requests'];
    }

    public function user_account($market_places_mapping_id, $user_account_id){
        $sql = "SELECT umps.id, user_account.user_account FROM user_market_places_subscribe umps INNER JOIN user_account ON user_account.id                = umps.user_account_id WHERE umps.market_places_mapping_id = '$market_places_mapping_id' AND user_account_id <>                            '$user_account_id'   ORDER BY umps.id DESC";
        $result = DbAccess3::runQuery($sql);
        $subscribers = '';
        while($row = mysqli_fetch_assoc($result)){
            $subscribers .= $row['user_account']."<br>";
        }
        return $subscribers;
    }

}
