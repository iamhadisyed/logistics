<?php

// get settings
//require_once("includes/settings/common.inc.php");

class PalletCarrierFilter 
{

    private $filter = "";
    private $order_by = "";
    private $limit = 100;
    private $rowsPerPage = 0;
    private $pageOffset = 0;

    
    
    public function getList() 
    {
        // has filter been configured?
        
        $where = "";
        if ($this->filter != "") 
        {
            $where = "WHERE " . substr($this->filter, 4);
        }

        $sort = "ORDER BY name asc";


        $sql = "SELECT *
                FROM pallet_carrier 
                $where
                $sort
                ";
        //echo $sql;die;
        //t($sql, __METHOD__);
        
         return PalletCarrier::getPalletCarrierListFromSql($sql);
         
    }
  
}

// class
