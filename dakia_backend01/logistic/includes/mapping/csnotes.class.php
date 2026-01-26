<?php 
/**
 * CS Notes class
 * @package News Releases
 */
class CSNotes extends DbAccess3 {

    /**
     * Constructor. Returns the object.
     * @param int $id
     * @return obj
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array
            (
            'id' => 'number',
            'notes' => 'string',
            'date_created' => 'datetime',
            'created_by' => 'number',
            
            'user_account' => 'undefined'
        );
        parent::__construct("cs_notes", 'id', $fieldList, $mixedCreator);
    }

    public static function getCsNotesListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfCsNotesFromSql($sql) {
        $rs = self::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    public static function deleteCsNotesFromSql($sql) {
        self::runQuery($sql);
    }

}
?>