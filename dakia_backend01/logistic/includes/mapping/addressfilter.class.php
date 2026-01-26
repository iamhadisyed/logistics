<?php
/*
 * Address Filter
 *
 */
class AddressFilter
{
	/**
	 * Get list of address items based on filter conditions
	 *
	 */
	public function getList($columns = '*', $debug = false)
	{
		$where = "";
		if ($this->filter != "")
		{
			$where = "WHERE " . substr($this->filter, 4);
		}
		$sort = "";
		if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;

        $sql = "SELECT ".$columns." FROM address a inner join country c on a.country = c.id $where $sort ";

        if($debug)
            echo $sql;
		return Address::getAddressListFromSql($sql);
        }
	

	public function getColumnList($fields)
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "")
		{
			$where = "WHERE " . substr($this->filter, 4);
		}
		$sort = "";
		if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;

		$sql = "SELECT id, ".$fields."FROM Address $where $sort ";


		return Address::getAddressListFromSql($sql);

	}
				
		/**
	 * Get count of consignment items based on filter conditions
	 *
	 * @return int
	 */
	public function getCount()
	{
		$result = $this->getList();
		return sizeof($result);
	}
	
	public function getPagingCount()
        {
                // has filter been configured?
                $where = "";
                if ($this->filter != "")
                {
                        $where = "WHERE " . substr($this->filter, 4);
                }
                $sort = "";
                if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;
                $sql = "SELECT count(a.id) as total FROM address a inner join country c on a.country = c.id $where $sort";
                return Address::getTotalAddressListFromSql($sql);

        }

        public function getPagingList($columns = '*', $debug=false)
        {
            // has filter been configured?
            $where = "";
            if ($this->filter != "")
            {
                    $where = "WHERE " . substr($this->filter, 4);
            }
            $sort = "";
            if ($this->order_by != "") 
                $sort = "ORDER BY " . $this->order_by;
            else
                $sort = "ORDER BY a.id desc" ;
            $sql = "SELECT ".$columns." FROM address a inner join country c on a.country = c.id $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
            if($debug)
                echo $sql;
            return Address::getAddressListFromSql($sql);
        }

        public function setOffset($offset)
        {
                $this->pageOffset = $offset;
        }

        public function setRowsPerPage($rowsPP)
        {
                $this->rowsPerPage = $rowsPP;
        }

        public function addFieldLikeFilter($colm, $value) {
            $this->filter .= " AND ";
            $this->filter .= " " . $colm . " LIKE '" . DbAccess3::escape($value) . "%'";
        }
        
        public function addFieldFilter($colm, $value) {
            $this->filter .= " AND ";
            $this->filter .= " " . $colm . " = '" . DbAccess3::escape($value) . "'";
        }
        public function addFilter($value) {
            $this->filter .= " AND ";
            $this->filter .= " " . $value . " ";
        }

    public function addUserFilter($userId) {
        $this->filter .= " AND ";
        $this->filter .= "user_id='" . DbAccess3::escape($userId) . "'";
    }
			
}