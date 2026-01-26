<?php
/**
 * A consignment contains a number of parcels.
 * Each parcel will need to be assigned a item number used on label.
 *
 */
 
class ServiceConstantFilter
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
	public function getList($debug=false)
	{
            // has filter been configured?
            $where = "";
          
            if (trim($this->filter) != "")
            {
                    $where = "WHERE " . $this->filter;
            }
            $sort = "";
            if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by ;
            $groupby = "";
            if($this->groupBy != "") $groupby =  $this->groupBy;

          $sql = "SELECT *
                            FROM service_constant s
                            $where
                            $sort
                            $groupby
                            ";
            if($debug)
                echo $sql;
            return ServiceConstant::getServiceConstantListFromSql($sql);
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
                            FROM service_constant s
                            $where
                            $sort
                            $groupby
                            ";

            t($sql, __METHOD__);
            return ServiceConstant::getServiceConstantListFromSql($sql);
	}
        
              
    public function addFieldLikeFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " " . $colm . " LIKE '" . DbAccess3::escape($value) . "%'";
    }
    public function addFieldFilter($colm, $value) {
         if ($this->filter != "")
            $this->filter .= " AND ";
            
        $this->filter .= " " . $colm . " = '" . DbAccess3::escape($value) . "'";
    }
    public function addFilter($code) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " " . $code . " ";
    }
    public function addGroupBy($fieldname) {
        $this->groupBy = " GROUP BY " . $fieldname;
    }
    public function AddOrderBy($columnNamem, $ascending = true) {
        if ($this->order_by != "")
            $this->order_by .= ", ";
        //
        $this->order_by .= $columnNamem." " . ($ascending ? "" : " DESC");
    }
    
    public function getConstantValue($agentid, $carrierId, $serviceId)
    {
        $sql = "SELECT 
                    sc.id, constant, scv.constant_value
                FROM
                    service_constant sc
                        INNER JOIN
                    service_constant_value scv ON scv.constant_id = sc.id
                WHERE  scv.agent_id = '".$agentid."' and (sc.carrier_id = '".$carrierId."' or sc.carrier_id = 0) and scv.service_id = '".$serviceId."'";
          return ServiceConstant::getServiceConstantListFromSql($sql);
    }
   
}
