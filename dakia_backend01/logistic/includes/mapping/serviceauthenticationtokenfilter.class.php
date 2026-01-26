<?php
class serviceAuthenticationTokenFilter
{
	private $filter_str = "";
	private $limit = 200;
	//
	public function getList($debug = false)
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "")
		{
			$where = "WHERE " . substr($this->filter, 4);
		}
		$sort = "";
		if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;
		
		$sql = "SELECT * FROM service_authentication_token s	$where $sort LIMIT 5000";

		if($debug)
                {
                    echo $sql;
                    die;
                }
		return serviceAuthenticationToken::getServiceAuthenticationTokenListFromSql($sql);
	}
        
        public function addFieldFilter($colm, $value)
	{
		if($this->filter != "")
			$this->filter .= " AND ";
			
		$this->filter .= " AND ".$colm." = '" . DbAccess3::escape($value) . "'";
	}
        
        public function addFilter($filterVal) {
            if ($this->filter != "")
                $this->filter .= " AND ";

            $this->filter .= "    " . $filterVal . " ";
        }
        
        public function getAuthenticationToken()
	{
		$date = date("Y-m-d H:i:s");
		$sql = "SELECT * FROM service_authentication_token s where s.token_expire_time > '" . DbAccess3::escape($date) . "' order by id desc limit 1";
                return serviceAuthenticationToken::getServiceAuthenticationTokenListFromSql($sql);
	}
        
   
}