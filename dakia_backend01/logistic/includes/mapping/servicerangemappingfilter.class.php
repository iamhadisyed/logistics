<?php
/**
 * A consignment contains a number of parcels.
 * Each parcel will need to be assigned a item number used on label.
 *
 */
 
class ServiceRangeMappingFilter
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
	public function getList($debug  =   false)
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
                            FROM service_range_mapping s
                            $where
                            $sort
                            $groupby
                            ";
            if($debug)
            {
               // echo $sql;
              //  die;
            }
        return ServiceRangeMapping::getServiceRangeMappingListFromSql($sql);
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
                            FROM service_range_mapping srm
                            $where
                            $sort
                            $groupby
                            ";

            t($sql, __METHOD__);
            return ServiceRangeMapping::getServiceRangeMappingListFromSql($sql);
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
    
    public function getServicesList($licence_plate_id)
    {
        $sql = "SELECT 
            s.name AS service_name, a.agent_name, c.logo
               from 
            ( services s INNER JOIN service_range_mapping srm
               ON s.id = srm.service_id
               )
               INNER JOIN agent_data a 
               ON a.id = srm.agent_id
               left join carrier c ON 
               c.id = s.carrier_id

           WHERE
            licence_plate_id = '".$licence_plate_id."'";
         return ServiceRangeMapping::getServiceRangeMappingListFromSql($sql);
    }

     public function getAgentServicesList($service_id='0') {
        $sql = "SELECT 
                    a.`agent_name`,
                    s.`name`
                    FROM
                    `service_range_mapping` srm 
                    JOIN agent_data a 
                      ON a.`id` = srm.`agent_id` 
                    JOIN `services` s ON s.`id` = srm.`service_id` 
                    WHERE srm.`service_id` = '".$service_id."' ";
        return ServiceRangeMapping::getServiceRangeMappingListFromSql($sql);
    }
}
