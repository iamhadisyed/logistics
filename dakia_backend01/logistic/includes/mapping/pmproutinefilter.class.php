<?php

/*
 * Department Filter
 *
 */

class PmpRoutineFilter {

    private $filter = "";
    private $order_by = "";

    /**
     * Get list of consignment items based on filter conditions
     *
     * @return array[Department]
     */
    public function getList($debug = false) {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

       $sql = "SELECT * FROM pmp_routine	$where $sort LIMIT 5000";
		
		//mail("mruga@oneworldexpress.com","sql", $sql);
        t($sql, __METHOD__);
        return PmpRoutine::getPmpRoutineListFromSql($sql);
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
				FROM pmp_routine
				$where
				$sort
				LIMIT " . $recordLimit . "
				";
        return PmpRoutine::getPmpRoutineListFromSql($sql);
    }

	 public function addFieldFilter($fieldName,$operator, $fieldValue) {
        $this->filter .= " AND " . $fieldName ." ". $operator . " '" . DbAccess3::escape($fieldValue) . "'";
    }
		
		public function addFilter($fieldName) {
        // if(trim($this->filter) != '')
        $this->filter .= " AND " . $fieldName . "";
        //	e//lse
        //	$this->filter .= " " . $fieldName . "";
    }   
}
