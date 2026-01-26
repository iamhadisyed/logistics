<?php

/*
 * ShoppingPlatform Filter
 *
 */

class ShoppingPlatformFilter {

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

        $sql = "SELECT * FROM shopping_platform	$where $sort LIMIT 5000";

        t($sql, __METHOD__);
        return ShoppingPlatform::getShoppingPlatformListFromSql($sql);
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
				FROM shopping_platform
				$where
				$sort
				LIMIT " . $recordLimit . "
				";
        return ShoppingPlatform::getShoppingPlatformListFromSql($sql);
    }

 
     public function DeleteRecord($id) {
        $sql = "DELETE FROM shopping_platform WHERE id = '" . DbAccess3::escape($id) . "'";
        return ShoppingPlatform::getShoppingPlatformListFromSql($sql);
     }
    /**
     * Get count of ShoppingPlatform items based on filter conditions
     *
     * @return int
     */
    public function getCount() {
        $result = $this->getList();
        return sizeof($result);
    }

   
	  public function AddOrderBy($field, $ascending = true) {
        //if ($this->order_by != "") $this->order_by = "";
        //
		$this->order_by = $field." " . ($ascending ? "" : " DESC");
    }

    public function addPageKeyFilter($data_value) {
        $this->filter .= " AND page_key = '" . DbAccess3::escape($data_value) . "'";
    }
    public function addPluginKeyFilter($data_value) {
        $this->filter .= " AND plugin_key = '" . DbAccess3::escape($data_value) . "'";
    }
	  public function addFieldEqualFilter($columnName, $operator, $columnValue) {
        $this->filter .= " AND ";
        $this->filter .=  $columnName . " " . $operator . " '" . DbAccess3::escape($columnValue) . "'";
    }

}
