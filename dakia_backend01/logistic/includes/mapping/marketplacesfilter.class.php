<?php

/*
 * MarketPlaces
 *
 */

class MarketPlacesFilter
{

    private $filter = "";
    private $order_by = "";
    private $rowsPerPage = 0;
    private $pageOffset = 0;
    private $join = 0;
    private $groupby = 0;

    /**
     * Get list of MarketPlaces items based on filter conditions
     *
     * @return array[Sample]
     */
    public function getList()
    {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        $sql = "SELECT * FROM market_places	mp $where order by title asc LIMIT 5000";

        t($sql, __METHOD__);
        return MarketPlaces::getMarketPlacesPlatformListFromSql($sql);
    }

    public function orderBy($order, $ascdesc = "ASC")
    {
        $this->order_by .= $order . " " . $ascdesc;
    }

    public function setOffset($offset)
    {
        $this->pageOffset = $offset;
    }

    public function setRowsPerPage($rowsPP)
    {
        $this->rowsPerPage = $rowsPP;
    }

    public function getPagingList($columns = '*', $debug = false)
    {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;
        $groupBy = "";
        if ($this->groupby != "")
            $groupBy = " GROUP BY " . $this->groupby;
        $checkJoin = "";
        if ($this->join != "")
            $checkJoin = $this->join;

        $sql = "SELECT " . $columns . "  FROM market_places mp $checkJoin  $where $groupBy $sort LIMIT $this->pageOffset , $this->rowsPerPage";
        if ($debug) {
            echo '<pre>';
            print_r($sql);
            echo '</pre>';
            die;
        }
        t($sql, __METHOD__);
        return MarketPlaces::getMarketPlacesPlatformListFromSql($sql);
    }

    public function addJoin($table,$joinField,$fromJoinField,$type="LEFT JOIN") {
        $this->join .= " " . $type ." " . $table . " " . "  ON " . $joinField . "  =  " . $fromJoinField;
    }
    public function addGroupBy($colm) {
        if ($this->groupby != "")
            $this->groupby .= " , ";
        $this->groupby .= "  " . $colm . " ";
    }

    public function getColumnList($fields, $recordLimit = 5000)
    {

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
				FROM market_places
				$where
				order by title asc
				LIMIT " . $recordLimit . "
				";

        return MarketPlaces::getMarketPlacesPlatformListFromSql($sql);
    }

    public function getUserPlatformList($user_id)
    {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        $sql = "SELECT m.* FROM user_market_places_mapping pm JOIN market_places m ON m.id = pm.market_places_id WHERE pm.user_id = '" . DbAccess3::escape($user_id) . "'";

        t($sql, __METHOD__);
        return MarketPlaces::getMarketPlacesPlatformListFromSql($sql);
    }

    public function getMarketPlacesListWithAuthFields($marketPlaceId)
    {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        $sql = "SELECT * FROM market_places_authenticate_field pmaf JOIN market_places m ON m.id = pmaf.market_places_id WHERE market_places_id = " . $marketPlaceId;
        t($sql, __METHOD__);
        return MarketPlaces::getMarketPlacesPlatformListFromSql($sql);
    }

    /**
     * Get count of MarketPlaces items based on filter conditions
     *
     * @return int
     */
    public function getCount()
    {
        $result = $this->getList();
        return sizeof($result);
    }
    public function getAccountShoppingAndAuthenticateData($user_account_id){
    	$sql = "SELECT
				  market_places.id,
				  market_places.title,
				  market_places.help_doc,
				  market_places.integration_logo,
				  market_places.plugin_key,
				  market_places.is_featured,
				  market_places_authenticate_field.id AS description,
				  market_places_authenticate_field.field_name AS is_active,
				  market_places_authenticate_field.field_value AS added_by,
				  market_places_authenticate_field.auto_generate_value AS translation_key
				FROM
				  market_places
				  LEFT JOIN market_places_authenticate_field
					ON market_places.id = market_places_authenticate_field.market_places_id
				JOIN `user_market_places_mapping` umpm ON umpm.`market_places_id` = market_places.id
				WHERE market_places.is_delete = '0'
				  AND market_places.parent_id = '0'
				  AND umpm.`user_account_id` = '".DbAccess3::escape($user_account_id)."'";
		return MarketPlaces::getMarketPlacesPlatformListFromSql($sql);
	}
    public function getShoppingAndAuthenticateData() {
       $sql = "SELECT market_places.id, market_places.title, market_places.class_name, market_places.help_doc, market_places.integration_logo, market_places.plugin_key, market_places.is_featured, market_places_authenticate_field.id AS description, market_places_authenticate_field.field_name AS is_active, market_places_authenticate_field.field_value AS added_by, market_places_authenticate_field.auto_generate_value AS translation_key FROM market_places LEFT JOIN market_places_authenticate_field ON market_places.id = market_places_authenticate_field.market_places_id WHERE market_places.is_delete = '0' and market_places.parent_id = '0'";
	
      return MarketPlaces::getMarketPlacesPlatformListFromSql($sql);  
    }

    public function getAllowedShoppingAndAuthenticateData($user_id)
    {
        $sql = "SELECT market_places.id, market_places.title, market_places_authenticate_field.id AS description, market_places_authenticate_field.field_name AS is_active, market_places_authenticate_field.field_value AS added_by, market_places_authenticate_field.auto_generate_value AS translation_key FROM market_places INNER JOIN market_places_authenticate_field ON market_places.id = market_places_authenticate_field.market_places_id INNER JOIN user_market_places_mapping ON user_market_places_mapping.market_places_id = market_places.id WHERE market_places.is_delete = '0' AND market_places_authenticate_field.is_delete = '0' AND user_market_places_mapping.user_id='" . DbAccess3::escape($user_id) . "'";
        return MarketPlaces::getMarketPlacesPlatformListFromSql($sql);
    }

    public function getAllowedShoppingPlatformData($user_id, $paltform_id)
    {
        $sql = "SELECT market_places.id, market_places.title, market_places_authenticate_field.id AS description, market_places_authenticate_field.field_name AS is_active, market_places_authenticate_field.field_value AS added_by, market_places_authenticate_field.auto_generate_value AS translation_key FROM market_places INNER JOIN market_places_authenticate_field ON market_places.id = market_places_authenticate_field.market_places_id INNER JOIN user_market_places_mapping ON user_market_places_mapping.market_places_id = market_places.id WHERE market_places.is_delete = '0' AND market_places_authenticate_field.is_delete = '0' AND user_market_places_mapping.user_id='" . DbAccess3::escape($user_id) . "' AND md5(user_market_places_mapping.market_places_id) ='" . DbAccess3::escape($paltform_id) . "'";
        return MarketPlaces::getMarketPlacesPlatformListFromSql($sql);
    }


    public function addMd5IdByFilter($date_value)
    {
        $this->filter .= " AND Md5(id) = '" . DbAccess3::escape($date_value) . "'";
    }

    public function addIsActiveFilter()
    {
        $this->filter .= " AND is_active = '1'";
    }

    public function addIsDeleteFilter()
    {
        $this->filter .= " AND is_delete = '1'";
    }

    public function addIsDeleteNotFilter()
    {
        $this->filter .= " AND is_delete = '0'";
    }


    public function addFieldFilter($colm, $value)
    {
        if ($this->filter != "") {
            $this->filter .= " AND ";
        }
        $this->filter .= " AND ";
        $this->filter .= " " . $colm . " = '" . DbAccess3::escape($value) . "'";
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

}
