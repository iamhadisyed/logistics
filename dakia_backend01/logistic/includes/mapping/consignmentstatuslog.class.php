<?php 
class ConsignmentStatusLog extends DbAccess3 {

    /**
     * Constructor. Returns the object.
     * @param int $id
     * @return obj
     */
    public function __construct($mixedCreator = null) {

        $fieldList = array(
            'id' => 'number',
            'parcel_id' => 'number',
            'old_status' => 'string',
            'new_status' => 'string',
            'message' => 'string',
            'added_by' => 'number',
            'date_added' => 'datetime',
        );

        parent::__construct("consignment_status_log", 'id', $fieldList, $mixedCreator);
    }

    public static function getConsignmentStatusLogListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfConsignmentStatusLogFromSql($sql) {
        $rs = self::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    public static function deleteConsignmentStatusLogFromSql($sql) {
        self::runQuery($sql);
    }
    
     public static function bulkDataInsert($rows) {

        $sql = array();

        foreach ($rows as $row) {
            $sql[] = "('',"
                    . "'','"
                    . DbAccess3::escape($row['parcel_id']) . "','"
                    . DbAccess3::escape($row['old_status']) . "','"
                    . DbAccess3::escape($row['new_status']) . "','"
                    . DbAccess3::escape($row['message']) . "','"
                    . DbAccess3::escape($row['added_by']) . "','"
                    . DbAccess3::escape($row['date_added']) . "')";
        }
        $sqlQuery = 'INSERT INTO `consignment_status_log`
                        (
                        `parcel_id`,
                        `old_status`,
                        `new_status`,
                        `message`,
                        `added_by`,
                        `date_added`)
                        VALUES ' . implode(',', $sql);

      		
        return DbAccess3::runQuery($sqlQuery);
    }
    
}

?>
