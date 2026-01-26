<?php

/**
 * Parcelgroupconsignment - Parcel group consignment class
 * - deals with Parcel group consignments
 *
 */
class NotFoundRecord extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'mawb' => 'string',
            'bag_number' => 'string',
            'tracking_number' => 'string',
            'length' => 'string',
            'width' => 'string',
            'height' => 'string',
            'weight' => 'string',
            'scanned_by' => 'string',
            'date_created' => 'string',
            'reason' => 'string',
            'image' => 'string'
        );
        parent::__construct("not_found_record", 'id', $fieldList, $mixedCreator);
    }

    public static function getNotFountRecordListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfNotFountRecordFromSql($sql) {
        $rs = self::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    public static function deleteNotFountRecordFromSql($sql) {
        self::runQuery($sql);
    }

    public static function bulkDataInsert($rows) {

        $sql = array();

        foreach ($rows as $row) {
            $sql[] = "('',"
                    . "'','"
                    . DbAccess3::escape($row['tracking_number']) . "','"
                    . DbAccess3::escape($row['scanned_by']) . "','"
                    . DbAccess3::escape($row['length']) . "','"
                    . DbAccess3::escape($row['width']) . "','"
                    . DbAccess3::escape($row['height']) . "','"
                    . DbAccess3::escape($row['weight']) . "','"
                    . DbAccess3::escape($row['date_created']) . "','"
                    . DbAccess3::escape($row['reason']) . "','"
                    . DbAccess3::escape($row['image']) . "')";
        }
        $sqlQuery = 'INSERT INTO `not_found_record`
                        (
                        `mawb`,
                        `bag_number`,
                        `tracking_number`,
                        `scanned_by`,
                        `length`,
                        `width`,
                        `height`,
                        `weight`,
                        `date_created`,
                        `reason`,
                        `image`)
                        VALUES ' . implode(',', $sql);


        return DbAccess3::runQuery($sqlQuery);
    }

}
