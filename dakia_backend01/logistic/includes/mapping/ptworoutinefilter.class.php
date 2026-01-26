<?php
class pTwoRoutineFilter
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
		
		$sql = "SELECT * FROM ptwo_routine p	$where $sort LIMIT 5000";

		if($debug)
                {
                    echo $sql;
                    die;
                }
		return ptwoRoutine::getPtwoRoutineListFromSql($sql);
	}
        
        public function addFieldFilter($colm, $value)
	{
		if($this->filter != "")
			$this->filter .= " AND ";
			
		$this->filter .= " AND ".$colm." = '" . DbAccess3::escape($value) . "'";
	}
        
        
        public function checkStreetNumber($doornumber, $id){
             $sql = "SELECT *
                    FROM ptwo_routine p WHERE id = '".$id."' AND  ".$doornumber." BETWEEN house_number_from AND house_number_to
                    AND house_number_filter IN (IF(".$doornumber." % 2 <> 0 , 'O', 'E'), 'A')";
            
            return ptwoRoutine::getPtwoRoutineListFromSql($sql);
        }
	/***
	 * Filter by sent date.
	 */
	
}