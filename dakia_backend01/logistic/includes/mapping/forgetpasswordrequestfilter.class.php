<?php

/*
 * ForgetPasswordRequest Filter
 *
 */

class ForgetPasswordRequestFilter {

    private $filter = "";
    private $order_by = "";

    /**
     * Get list of ForgetPasswordRequest items based on filter conditions
     *
     * @return array[ForgetPasswordRequest]
     */
    public function getList($debug=false) {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        $sql = "SELECT * FROM forget_password_request $where $sort LIMIT 5000";

        //if($debug)
            //echo $sql;
        return ForgetPasswordRequest::getForgetPasswordRequestListFromSql($sql, $debug);
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
				FROM forget_password_request
				$where
				$sort
				LIMIT " . $recordLimit . "
				";
        return ForgetPasswordRequest::getForgetPasswordRequestListFromSql($sql);
    }

    /**
     * Get count of ForgetPasswordRequest items based on filter conditions
     *
     * @return int
     */
    public function getCount() {
        $result = $this->getList();
        return sizeof($result);
    }

 

    public function addTokenFilter($data_value) {
        $this->filter .= " AND token = '" . DbAccess3::escape($data_value) . "'";
    }

    public function addIsDateExpireFilter() {
        $this->filter .= " AND date_expire > NOW()";
    }


    public function addIsNotExpireFilter() {
        $this->filter .= " AND is_expire = '0'";
    }
}
