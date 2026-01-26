<?php

/*
 * ShoppingPlatform Filter
 *
 */

class UserShoppingPlatformsFilter {

    private $filter = "";
    private $order_by = "";

    /**
     * Get list of ShoppingPlatform items based on filter conditions
     *
     * @return array[ShoppingPlatform]
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

         $sql = "SELECT * FROM user_shopping_platforms	$where $sort LIMIT 5000";

        t($sql, __METHOD__);
        return UserShoppingPlatforms::getUserShoppingPlatformsListFromSql($sql);
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
				FROM user_shopping_platforms
				$where
				$sort
				LIMIT " . $recordLimit . "
				";
        return UserShoppingPlatforms::getUserShoppingPlatformsListFromSql($sql);
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

        $sql = "SELECT count(*) as total FROM user_shopping_platforms u $where $sort ";

        t($sql, __METHOD__);

        return User::getTotalNumberOfUsersFromSql($sql);
    }
	
	  public function getPagingList() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . $this->filter; //substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = " ORDER BY " . $this->order_by;


        $sql = "SELECT * FROM user_shopping_platforms u $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";



        t($sql, __METHOD__);

        return User::getUserListFromSql($sql);
    }
	
	 public function getPagingColList($fields) {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . $this->filter; //substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = " ORDER BY " . $this->order_by;


        $sql = "SELECT $fields FROM user_shopping_platforms u $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";



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
	
	
	
public function getUserPlatformList($user_id) {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

      $sql = "SELECT m.* FROM user_shopping_platform_mapping pm JOIN shopping_platform m ON m.id = pm.shopping_platform_id WHERE pm.user_id = '".DbAccess3::escape($user_id)."'";

       // t($sql, __METHOD__);
        return UserShoppingPlatforms::getUserShoppingPlatformsListFromSql($sql);
    }
   
    

    
    public function addFieldFilter($named,$data_value) {
        $this->filter .= " AND ".$named." = '" . DbAccess3::escape($data_value) . "'";
    }
	
    public function addFilter($named) {
        $this->filter .= " AND " . $named. "";
    }

    public function addShoppingPlatformIdFilter($data_value) {
        $this->filter .= " AND shopping_platform_id = '" . DbAccess3::escape($data_value) . "'";
    }
    public function addApiKeyFilter($data_value) {
        $this->filter .= " AND api_key = '" . DbAccess3::escape($data_value) . "'";
    }
    public function addApiSecreteFilter($data_value) {
        $this->filter .= " AND api_secrete = '" . DbAccess3::escape($data_value) . "'";
    }
    public function addStatusFilter($data_value) {
        $this->filter .= " AND status = '" . DbAccess3::escape($data_value) . "'";
    }
    public function addUserIdFilter($data_value) {
        $this->filter .= " AND user_id = '" . DbAccess3::escape($data_value) . "'";
    }
    public static function varifyAPIKeyAPISecrete($userName,$password) {
        $return = "";
        if(!empty($userName) && !empty($password)){
            $sql = "SELECT
                            usp.`user_id`
                          FROM
                            `shopping_platform` sp
                            JOIN `user_shopping_platforms` usp
                              ON usp.`shopping_platform_id` = sp.`id`
                          WHERE sp.`page_key` = 'smarttrack'
                            AND usp.`api_key` = '".DbAccess3::escape($userName)."'
                            AND usp.`api_secrete` = '".DbAccess3::escape($password)."'  AND sp.active='1' ";
            $return =  UserShoppingPlatforms::getUserShoppingPlatformsListFromSql($sql);
        }
        return $return;
    }
    public function getUserIntegration($userId) {
        $sql = "SELECT
                  sp.`title` AS shopping_platform_id,
                  usp.id,
                  usp.reference,
                  usp.site_url,
                  usp.user_id,
                  usp.status,
                  usp.api_key,
                  usp.api_secrete,
                  usp.date_created
                FROM
                  user_shopping_platforms usp
                  JOIN shopping_platform sp
                    ON sp.`id` = usp.`shopping_platform_id`
                WHERE STATUS <> 0
                  AND usp.user_id = '".DbAccess3::escape($userId)."'
                  AND usp.`id` IS NOT NULL
                  ORDER BY sp.`id`
                 LIMIT 5000";
        return UserShoppingPlatforms::getUserShoppingPlatformsListFromSql($sql);
    }
}
