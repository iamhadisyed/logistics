<?php 
////////////////////////////////////////////////////
//
// Class for dealing with Relabel Consignment
//
////////////////////////////////////////////////////

/**
 * Relabel Consignment class
 * @package News Releases
 */
class RelabelConsignment extends DbAccess3 {

    /**
     * Constructor. Returns the object.
     * @param int $id
     * @return obj
     */
    public function __construct($mixedCreator = null) {

        $fieldList = array(
            'id' => 'number',
            'consignment_id' => 'number',
            'old_tracking_no' => 'string',
            'new_tracking_no' => 'string',
            'date_created' => 'datetime',
            'userid' => 'number',
            'old_consignment_data' => 'string',
            'old_new_tracking_mapping' => 'string',
            
            'user_account' => 'undefined',
            'user_name' => 'undefined',
        );

        parent::__construct("consignment_relabel", 'id', $fieldList, $mixedCreator);
    }

    public static function getRelabelConsignmentListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfRelabelConsignmentFromSql($sql) {
        $rs = self::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    public static function deleteRelabelConsignmentFromSql($sql) {
        self::runQuery($sql);
    }

}

?>
