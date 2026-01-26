<?php
/**
 * A consignment contains a number of parcels.
 * Each parcel will need to be assigned a item number used on label.
 *
 */
 
class ServiceConstantValueFilter
{
	private $filter = "";
	private $order_by = "";
	private $limit = 100;
	private $groupBy = '';

	/**
	 * Get list of consignment items based on filter conditions
	 *
	 * @return array[Consignment]
	 */
    public function getList()
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
                        FROM service_constant_value scv
                        $where
                        $sort
                        $groupby
                        ";

        t($sql, __METHOD__);
        return ServiceConstantValue::getServiceConstantValueListFromSql($sql);
    }
	
    
    public function getConstantList($fields,$debug=false) {
        
        
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $groupBy = $this->groupBy;
        $sort = '';

        if($this->order_by != '')
            $sort = " order by " . $this->order_by;

        $sql = "SELECT " . $fields . " , scv.id from 
            ( services s INNER JOIN service_constant_value scv
               ON s.id = scv.service_id
               )
                INNER JOIN agent_data a 
                ON a.id = scv.agent_id
                INNER JOIN service_constant c ON 
                c.id = scv.constant_id 
                LEFT JOIN carrier ca
                on ca.id = s.carrier_id
                $where $sort ";
      if($debug) {
          echo $sql;
          die;
      }

        return ServiceConstantValue::getServiceConstantValueListFromSql($sql);
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
                        FROM service_constant_value scv
                        $where
                        $sort
                        $groupby
                        ";

        t($sql, __METHOD__);
        return ServiceConstantValue::getServiceConstantValueListFromSql($sql);
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

        $sql = "SELECT count(*) as total from 
            ( services s INNER JOIN service_constant_value scv
               ON s.id = scv.service_id
               )
               INNER JOIN agent_data a 
               ON a.id = scv.agent_id
               INNER JOIN service_constant c ON 
               c.id = scv.constant_id 
               LEFT JOIN carrier ca
               on ca.id = s.carrier_id
               $where $sort ";

        t($sql, __METHOD__);

        return ServiceConstantValue::getTotalNumberOfConstantFromSql($sql);
    }
    
    public function getPagingList($fields) {
        
        
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $groupBy = $this->groupBy;
        if($this->order_by != '')
            $sort = " order by " . $this->order_by;

         $sql = "SELECT " . $fields . " , scv.id from 
            ( services s INNER JOIN service_constant_value scv
               ON s.id = scv.service_id
               )
                INNER JOIN agent_data a 
                ON a.id = scv.agent_id
                INNER JOIN service_constant c ON 
                c.id = scv.constant_id 
                LEFT JOIN carrier ca
                on ca.id = s.carrier_id
                $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
      
        t($sql, __METHOD__);

        return ServiceConstantValue::getServiceConstantValueListFromSql($sql);
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
    public function addGroupBy($fieldname) {
        $this->groupBy = " GROUP BY " . $fieldname;
    }
    public function AddOrderBy($name, $ascending = true) {
        //if ($this->order_by != "") $this->order_by = " ";
        //
		if (trim($name) != '')
            $this->order_by = $name . " " . ($ascending ? "" : " DESC");
    }
   
}
