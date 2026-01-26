<?php
/**
 * A consignment contains a number of parcels.
 * Each parcel will need to be assigned a item number used on label.
 *
 */
 
class LicencePlateFilter
{
	private $filter = "";
	private $order_by = "";
	private $limit = 100;
	private $groupBy = ''; 
        private $rowsPerPage = 0;
        private $pageOffset = 0;
        const PAGE_SIZE = 20;
        


        /**
	 * Get list of consignment items based on filter conditions
	 *
	 * @return array[Consignment]
	 */
    public function getList($debug = false)
    {
        // has filter been configured?
        $where = "";
        if ($this->filter != "")
        {
                $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by ;
        $groupby = "";
        if($this->groupBy != "") $groupby =  $this->groupBy;

        $sql = "SELECT *
                        FROM licence_plate l
                        $where
                        $sort
                        $groupby
                        ";
        if($debug)
        {
            echo $sql;
        }
        t($sql, __METHOD__);
        return LicencePlate::getLicencePlateListFromSql($sql);
    }


            /**
     * Get list of consignment items based on filter conditions
     *
     * @return array[Consignment]
     */
    public function getColumnList($fields)
    {
        // has filter been configured?
        $where = "";
        if ($this->filter != "")
        {
                $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by ;

        $groupby = "";
        if($this->groupBy != "") $groupby =  $this->groupBy;


        $sql = "SELECT id, ".$fields."
                        FROM licence_plate l
                        $where
                        $sort
                        $groupby
                        ";

        t($sql, __METHOD__);
        return LicencePlate::getLicencePlateListFromSql($sql);
    }
    public function getCount() {
        $result = $this->getList();
        return sizeof($result);
    }
     public function getPagingCount() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        $sql = "SELECT count(*) as total FROM licence_plate l  $where $sort ";

        t($sql, __METHOD__);

        return LicencePlate::getTotalNumberOfRangesFromSql($sql);
    }
    
    public function getPagingList($fields, $debug=false) {
        
        
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $groupBy = $this->groupBy;
        if($this->order_by != '')
            $sort = " order by " . $this->order_by;
        
        if ($this->join != "")
            $checkJoin = $this->join;

       $sql = "SELECT " . $fields . " , l.id FROM  licence_plate l $checkJoin  $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
       if($debug)
       {
           echo $sql;
           die;
       }
         return LicencePlate::getLicencePlateListFromSql($sql);
    }
    
    public function setRowsPerPage($rowsPP) {
        $this->rowsPerPage = $rowsPP;
    }
    
    public function setOffset($offset) {
        $this->pageOffset = $offset;
    }
        
    public function addFieldLikeFilter($colm, $value) {
        $this->filter .= " AND ";
        $this->filter .= " " . $colm . " LIKE '" . DbAccess3::escape($value) . "%'";
    }
    public function addFieldFilter($colm, $value) {

        $this->filter .= " AND ";
        $this->filter .= " " . $colm . " = '" . DbAccess3::escape($value) . "'";
    }
    public function addFilter($code) {
        $this->filter .= " AND ";
        $this->filter .= " " . $code . " ";
    }
    
    
    public function addServiceRangeMappingJoin() {
        $this->join.= " JOIN service_range_mapping s ON l.id = s.licence_plate_id ";
    }
     public function AddOrderBy($name, $ascending = true) {
        //if ($this->order_by != "") $this->order_by = " ";
        //
		if (trim($name) != '')
            $this->order_by = $name . " " . ($ascending ? "" : " DESC");
    }
    public function addGroupBy($fieldname) {
        $this->groupBy = " GROUP BY " . $fieldname;
    }
}
