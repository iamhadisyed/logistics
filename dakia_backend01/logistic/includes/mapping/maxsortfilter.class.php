<?php
/*
 * MaxSort Filter
 *
 */
class MaxSortFilter
{
    private $filter = "";
    public function getList()
    {
            // has filter been configured
            $where = "";
            if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
             $sql = "SELECT *
                            FROM max_sort ms
                            $where";
            return MaxSort::getMaxSortListFromSql($sql);
    }
    /**
     * Get list of parcels based on filter conditions
     *
     * @return array[parcel]
     */
    public function getColumnList($fields, $debug= false)
    {
        $where = "";
        
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        
        $sql = "SELECT id, ".$fields."
                        FROM max_sort ms
                        $where";
        if($debug)
            echo $sql;
        return MaxSort::getMaxSortListFromSql($sql);
    }

    public function addFieldFilter($fieldName, $fieldValue) 
    {
        $this->filter .= " AND ms." . $fieldName . " = '" . DbAccess3::escape($fieldValue) . "'";
    }
    
    public function addFilter($fieldName) 
    {
        $this->filter .= " AND " . $fieldName . "";
    }
}