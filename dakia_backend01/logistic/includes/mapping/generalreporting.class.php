<?php

////////////////////////////////////////////////////
//
// Class for reporting
//
////////////////////////////////////////////////////


/**
 * Description of reporting
 *
 * @author Irshad Ali
 */
class GeneralReporting extends DbAccess3 {
    public function __construct(){
        
    }
    public static function getReportFromSql($sql){
//        return DbAccess3::getListFromSql(__CLASS__, $sql);
        $rs = self::runQuery($sql);
        $data = array();
        while($d = mysqli_fetch_assoc($rs)){
            $data[] = $d; 
        }
        return $data;
    }
    public static function getTotalRecordsFromSql($sql) {
        $rs = self::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
}
