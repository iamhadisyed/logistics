<?php

/*
 * Sample Filter
 *
 */

class UserMarketPlacesMappingFilter {

    private $filter = "";
    private $order_by = "";

    /**
     * Get list of Sample items based on filter conditions
     *
     * @return array[Sample]
     */
    public function getAllList() {
        if ($this->join != "") {
            $marketPlaceJoin = $this->join;
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;
        $sql = "SELECT ump.*, mp.title,mp.description, mp.translation_key, mp.is_active, mp.is_delete, mp.page_link, mp.integration_logo, mp.plugin_key FROM user_market_places_mapping ump $marketPlaceJoin $sort ";
        return UserMarketPlacesMapping::getUserMarketPlacesformMappingListFromSql($sql);
    }

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

      $sql = "SELECT * FROM user_market_places_mapping ump $marketPlaceJoin $where  $sort LIMIT 5000"; 
//      echo $sql; die();
      return UserMarketPlacesMapping::getUserMarketPlacesformMappingListFromSql($sql);
    }
    public function getColumnList($col="*",$debug=false) {
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

        $sql = "SELECT $col FROM user_market_places_mapping ump $marketPlaceJoin $where  $sort LIMIT 5000";
        if($debug){
            echo "<pre>";
            print_r($sql);
            echo "</pre>";
            die;
        }
        return UserMarketPlacesMapping::getUserMarketPlacesformMappingListFromSql($sql);
    }

    public function AddOrderBy($field, $ascending = true)
    {
        if ($this->order_by != "")
            $this->order_by .= ", ";
        $this->order_by .= "$field" . ($ascending ? "" : " DESC");
    }

    public function getCount()
    {
        $result = $this->getList();
        return sizeof($result);
    }
    public function setRowsPerPage($rowsPP) {
        $this->rowsPerPage = $rowsPP;
    }

    public function setOffset($offset) {
        $this->pageOffset = $offset;
    }
    public function getPagingCount($debug=false) {
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

        $sql = "SELECT count(*) as total FROM user_market_places_mapping ump $checkJoin $where $sort ";
        if($debug){
            echo "<pre>";
            print_r($sql);
            echo "</pre>";
            die;
        }
        t($sql, __METHOD__);

        return UserMarketPlacesMapping::getTotalNumberOfMarketPlacesFromSql($sql);
    }
    public function getPagingList($debug = false) {
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

        $sql = "SELECT ump.* FROM user_market_places_mapping ump $checkJoin $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
        if($debug){
            echo "<pre>";
            print_r($sql);
            echo "</pre>";
            die;
        }
        return UserMarketPlacesMapping::getUserMarketPlacesformMappingListFromSql($sql);
    }
    public function insertUserMarketPlacesSubscribe($uaid)
    {
        $sql = "SELECT parentid FROM user_account WHERE id = $uaid";
        $result = DbAccess3::runQuery($sql);
        $row = mysqli_fetch_assoc($result);
        $parentid = $row['parentid'];
        if(is_null($parentid)){
            $parentid = $uaid;
        }
        $SQL = "INSERT INTO user_market_places_subscribe(market_places_mapping_id, market_places_id, user_account_id, parent_id, status)
                SELECT ump.id, ump.market_places_id, ump.user_account_id, $parentid, ump.active FROM user_market_places_mapping ump
                LEFT OUTER JOIN user_market_places_subscribe umps
                ON ump.id= umps.`market_places_mapping_id` AND ump.`market_places_id` = umps.`market_places_id`
                WHERE ump.`user_account_id` = $uaid AND ump.active = '1' AND
                umps.id IS NULL";
        DbAccess3::runQueryNew($SQL);
    }
    public function updateUserMarketPlacesSubscribeStatus($uaid)
    {
        $SQL = "UPDATE user_market_places_subscribe umps INNER JOIN 
                user_market_places_mapping mp
                ON mp.id = umps.`market_places_mapping_id` AND mp.`market_places_id` = umps.`market_places_id` AND 
                mp.`user_account_id` = umps.`user_account_id` 
                SET umps.status = '1', umps.is_delete = '0', umps.pending_req = '0', umps.is_reject = '0'
                WHERE mp.active = '1' AND mp.`user_account_id` = '$uaid' AND umps.status = '0'";

        DbAccess3::runQueryNew($SQL);
    }
    
    public function addJoin($table,$where, $type = "INNER")
    {
        $this->join .= $type . " JOIN ".$table."  ON " . $where;
    }

    public function addUserIdFilter($data_value)
    {
        $this->filter .= " AND ump.user_account_id = '" . DbAccess3::escape($data_value) . "'";
    }

    public function isActive()
    {
        $this->filter .= " AND ump.active = '1'";
    }

    public function addParentIdFilter($data_value)
    {
        $this->filter .= " And parentid = '" . DbAccess3::escape($data_value) . "'";
    }
    
    public function addMarketPlacesMappingIdFilter($data_value)
    {
        $this->filter .= " AND ump.market_places_id = '" . DbAccess3::escape($data_value) . "'";
    }

    public function addMarketPlacesMappingIdMd5Filter($data_value)
    {
        $this->filter .= " AND MD5(ump.market_places_id) = '" . DbAccess3::escape($data_value) . "'";
    }
   
	  public function addFieldFilter($colm, $value)
	  {
	      if(!empty($this->filter))
		    $this->filter .= " AND ";

		$this->filter .= " ".$colm." = '" . DbAccess3::escape($value) . "'";
	  }

	   public function addFilter($account_number)
       {
        //echo $account_number;
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= $account_number;
        //echo $this->filter;
        //exit;
        }
    public static function pendingRequests($parentId) {
        $sql = "SELECT COUNT(id) AS pending_req FROM user_market_places_mapping ump WHERE user_parent_id = '" . DbAccess3::escape($parentId) . "' AND `status` = 'pending'";
//        AND expiry_date >= NOW()
        $result = DbAccess3::runQuery($sql);
        $row = mysqli_fetch_assoc($result);
        return $row['pending_req'];
    }
    public function subscribe_request($mpid, $uaid, $paid)
    {
/*        $sql = "UPDATE user_market_places_subscribe SET is_delete = '1' , is_reject = '0' where market_places_mapping_id = '$rowid' and is_reject = '1'";
        DbAccess3::runQuery($sql);
*/
        $sql = "SELECT id from user_market_places_mapping where user_account_id = '$uaid' and market_places_id = '$mpid'";
        $result = DbAccess3::runQuery($sql);
        $row = mysqli_fetch_object($result);
        $rowid = $row->id;
        $sql = "insert into user_market_places_subscribe(market_places_id, user_account_id, parent_id, status, market_places_mapping_id,                   pending_req) values('$mpid', '$uaid', '$paid', '2','$rowid', '2')";
        DbAccess3::runQueryNew($sql);
    }

    public function resubscribe_request($market_places_mapping_id, $mpid, $uaid)
    {
        $sql = "UPDATE user_market_places_subscribe SET is_delete = '0' , is_reject = '0', pending_req = '2', status = '2' where
                user_account_id = '$uaid' and market_places_id = '$mpid' and market_places_mapping_id ='$market_places_mapping_id'";
        DbAccess3::runQuery($sql);
    }

    public function addRow($mpid, $uaid){
        $sql = "insert into user_market_places_mapping(market_places_id, user_account_id, active) values('$mpid', '$uaid', '0')";
        DbAccess3::runQueryNew($sql);
    }
    
    public function getInsertedRow($market_places_id, $user_account_id){
        $sql = "SELECT count(id) as no_of_rows FROM user_market_places_mapping 
        WHERE market_places_id = '$market_places_id' AND user_account_id = '$user_account_id'";
        $result = DbAccess3::runQuery($sql);
        $row = mysqli_fetch_assoc($result);
        return $row['no_of_rows'];
    }

    public function getUserSubscriberInsertRow($mpid, $uaid){
        $sql = "SELECT count(id) as no_of_rows FROM user_market_places_subscribe 
        WHERE market_places_id = '$mpid' and user_account_id = '$uaid'";
        $result = DbAccess3::runQuery($sql);
        $row = mysqli_fetch_assoc($result);
        return $row['no_of_rows'];
    }

    public function subscribeStatus($market_places_id, $uaid, $market_places_mapping_id=""){
      $sql = "SELECT id, status, is_delete, pending_req, is_reject FROM
              user_market_places_subscribe WHERE market_places_id =
              '$market_places_id'AND user_account_id = '$uaid' and market_places_mapping_id = '$market_places_mapping_id'";
      $result = DbAccess3::runQuery($sql);
      $row = mysqli_fetch_assoc($result);
      $subscribe_status = $row['status'] . "," . $row['id'];
      $delete = $row['is_delete'] . "," . $row['id'];
      $pendingreq = $row['pending_req'] . "," . $row['id'];
      $reject = $row['is_reject'] . "," . $row['id'];
      $inactive = $row['is_reject'] . "," . $row['id'];
      if($row['status'] == 1){
          return "status,". $subscribe_status;
      }elseif($row['is_delete'] == 1){
          return "delete,". $delete;
      }elseif($row['pending_req'] == 2){
          return "pendingrequest,". $pendingreq;
      }elseif($row['is_reject'] == 3){
          return "reject,". $reject;
      }elseif($row['is_reject'] == 1){
          return "inactive,". $inactive;
      }
    }

    public function unSubscribe($rowid, $umps_id){
        $sql = "UPDATE user_market_places_mapping SET active = '0'  where id = '$rowid'";
        DbAccess3::runQuery($sql);
        $sql = "UPDATE user_market_places_subscribe SET status = '0' , is_delete = '1', is_reject = '0'  where id = '$umps_id'";
        DbAccess3::runQuery($sql);
    }
    public function getActiveExpiryDateStatus($id, $uaid){
        $sql = "SELECT id, active_date, expiry_date, is_delete FROM user_market_places_subscribe
                WHERE id = '$id' AND user_account_id = '$uaid' ORDER BY id DESC LIMIT 1";
        $result = DbAccess3::runQuery($sql);
        $row = mysqli_fetch_assoc($result);
        $id = $row['id'];
        $active_date = $row['active_date'];
        $expiry_date = $row['expiry_date'];
        $is_delete = $row['is_delete'];
        $columns = $id . "," . $active_date . ",". $expiry_date . ",". $is_delete;
        return $columns;
    }

    public function autoInactive($mpmid,$uaid){
        $sql = "UPDATE `user_market_places_mapping` SET `status` = 'e' WHERE id = ".DbAccess3::escape($mpmid)." AND  user_account_id = ".DbAccess3::escape($uaid);
        DbAccess3::runQuery($sql);
    }

    public function autoActive($id, $mpmid, $uaid, $mpid){
        $sql = "UPDATE user_market_places_subscribe SET STATUS = '1', pending_req = '0', is_reject = '0'  
                WHERE market_places_mapping_id = '$mpmid' AND user_account_id = '$uaid' 
                and market_places_id = '$mpid' AND id = '$id'";
        DbAccess3::runQuery($sql);
        $sql = "UPDATE user_market_places_mapping SET active = '1' where id = '$mpmid'";
        DbAccess3::runQuery($sql);
    }

    public function update_match_values($uaid, $mpid){
        $sql = "UPDATE user_market_places_mapping SET active = 0 
                        WHERE id IN (SELECT market_places_mapping_id FROM user_market_places_subscribe WHERE market_places_id = $mpid
                        AND user_account_id = $uaid AND STATUS != 1)";
        DbAccess3::runQuery($sql);

        $sql = "UPDATE user_market_places_mapping SET active = 1
                        WHERE id IN (SELECT market_places_mapping_id FROM user_market_places_subscribe WHERE market_places_id = $mpid
                        AND user_account_id = $uaid AND STATUS = 1)";
        DbAccess3::runQuery($sql);
    }
    public function getUserMarketsList() {
        $user = SessionManager::getUser();
        $user_account_id = $user->getUserAccountId();
        $sql = "SELECT mp.id, mp.title FROM user_market_places_mapping ump  INNER JOIN market_places mp 
ON mp.id= ump.market_places_id WHERE ump.active=1 AND ump.user_account_id= 2301 LIMIT 5000";
//      echo $sql; die();
        return UserMarketPlacesMapping::getUserMarketPlacesformMappingListFromSql($sql);
    }
}