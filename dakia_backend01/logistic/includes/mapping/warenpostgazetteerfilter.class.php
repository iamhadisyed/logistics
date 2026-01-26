<?php

class warenpostGazetteerFilter {

    private $filter_str = "";
    private $limit = 200;

    //
    public function getList($debug = false) {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        $sql = "SELECT * FROM warenpost_gazetteer w	$where $sort LIMIT 5000";

        if ($debug) {
            echo $sql;
            die;
        }
        return warenpostGazetteer::getWarenpostGazetteerListFromSql($sql);
    }

    public function addFieldFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";

        $this->filter .= " AND " . $colm . " = '" . DbAccess3::escape($value) . "'";
    }

    public function checkStreetNumber($doornumber, $id) {
        
        if($doornumber % 2 == 0){
            $numberType = 'G';
        }
        else{
            $numberType = 'U';
        }
         $sql = "SELECT *
                    FROM warenpost_gazetteer w WHERE id in ('" . implode("','",$id) . "') AND  " . $doornumber . " BETWEEN hnrvon AND hnrbis
                    AND house_number_type = '".$numberType."'";
                    //house_number_type IN (IF(" . $doornumber . " % 2 <> 0 , 'U', 'G'), 'N')";
        $list = warenpostGazetteer::getWarenpostGazetteerListFromSql($sql);
        if (count($list) > 0) {
                return $list;
        } else {
            $sql = "SELECT *
                FROM warenpost_gazetteer w WHERE id in ('" . implode("','",$id) . "') and  house_number_type = 'N'";
                return warenpostGazetteer::getWarenpostGazetteerListFromSql($sql);
        }
        
    }

    /*     * *
     * Filter by sent date.
     */
}
